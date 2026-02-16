<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MigrasiNilaiController extends Controller
{
    /**
     * Display migrasi nilai page
     */
    public function index()
    {
        // Get unique tahun pelajaran
        $tahunList = DataPeriodik::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        return view('admin.migrasi-nilai.index', compact('tahunList'));
    }
    
    /**
     * Get semesters for selected tahun pelajaran
     */
    public function getSemesters(Request $request)
    {
        $tahun = $request->tahun_pelajaran;
        $semesters = DataPeriodik::where('tahun_pelajaran', $tahun)
            ->pluck('semester')
            ->unique()
            ->values();
        
        return response()->json($semesters);
    }
    
    /**
     * Get rombels for selected tahun and semester
     */
    public function getRombels(Request $request)
    {
        $tahun = $request->tahun_pelajaran;
        $semester = $request->semester;
        
        // Get rombels based on tahun and semester
        $rombels = Rombel::where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->select('id', 'nama_rombel')
            ->get();
        
        // Natural sort by rombel name (so X.10 comes after X.9, not after X.1)
        $rombels = $rombels->sortBy('nama_rombel', SORT_NATURAL)->values();
        
        return response()->json($rombels);
    }
    
    /**
     * Download Excel template
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'tahun_pelajaran' => 'required',
            'semester' => 'required',
            'rombel_id' => 'required|exists:rombel,id'
        ]);
        
        $tahunPelajaran = $request->tahun_pelajaran;
        $semester = $request->semester;
        $rombelId = $request->rombel_id;
        
        $rombel = Rombel::find($rombelId);
        $rombelNama = $rombel->nama_rombel;
        
        // Get students in this rombel
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        
        if (strtolower($semester) == 'ganjil') {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        } else {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        }
        
        if ($siswaList->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa dalam rombel ini!');
        }
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setTitle('Template Import Nilai');
        
        // Headers
        $headers = [
            'No', 'NISN', 'Nama Siswa',
            'Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Inggris Lanjut', 'Bahasa Lampung',
            'Matematika', 'Matematika Lanjut',
            'Biologi', 'Fisika', 'Kimia',
            'Sejarah', 'Ekonomi', 'Sosiologi', 'Geografi',
            'Pendidikan Agama Islam', 'Pendidikan Kewarganegaraan',
            'Informatika', 'KKA', 'PJOK', 'Seni Budaya', 'Prakarya dan Kewirausahaan',
            'IPA', 'IPS'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $col++;
        }
        
        // Add student data
        $row = 2;
        $no = 1;
        foreach ($siswaList as $siswa) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $siswa->nisn);
            $sheet->setCellValue('C' . $row, $siswa->nama);
            $row++;
        }
        
        // Auto width
        foreach(range('A','Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Freeze first row
        $sheet->freezePane('A2');
        
        // Add borders
        $lastRow = $row - 1;
        $sheet->getStyle('A1:Z' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);
        
        // Output
        $fileName = 'Template_Migrasi_Nilai_' . $rombelNama . '_' . $tahunPelajaran . '_' . $semester . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Import Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'tahun_pelajaran' => 'required',
            'semester' => 'required',
            'rombel_id' => 'required|exists:rombel,id',
            'file' => 'required|file|mimes:xlsx,xls|max:5120'
        ]);
        
        try {
            $tahunPelajaran = $request->tahun_pelajaran;
            $semester = $request->semester;
            $rombelId = $request->rombel_id;
            
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Skip header
            array_shift($rows);
            
            $imported = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; // +2 because we skip header and array is 0-indexed
                
                // Skip empty rows
                if (empty($row[1])) continue;
                
                $nisn = $row[1];
                $namaSiswa = $row[2];
                
                // Validate NISN exists
                $siswa = DB::table('siswa')->where('nisn', $nisn)->first();
                if (!$siswa) {
                    $errors[] = "Baris $rowNum: NISN $nisn tidak ditemukan";
                    continue;
                }
                
                // Map nilai to columns (convert column name to lowercase with underscores)
                $data = [
                    'rombel_id' => $rombelId,
                    'tahun_pelajaran' => $tahunPelajaran,
                    'semester' => $semester,
                    'nisn' => $nisn,
                    'nama_siswa' => $namaSiswa,
                    'bahasa_indonesia' => $row[3] ?: null,
                    'bahasa_inggris' => $row[4] ?: null,
                    'bahasa_inggris_lanjut' => $row[5] ?: null,
                    'bahasa_lampung' => $row[6] ?: null,
                    'matematika' => $row[7] ?: null,
                    'matematika_lanjut' => $row[8] ?: null,
                    'biologi' => $row[9] ?: null,
                    'fisika' => $row[10] ?: null,
                    'kimia' => $row[11] ?: null,
                    'sejarah' => $row[12] ?: null,
                    'ekonomi' => $row[13] ?: null,
                    'sosiologi' => $row[14] ?: null,
                    'geografi' => $row[15] ?: null,
                    'pendidikan_agama_islam' => $row[16] ?: null,
                    'pendidikan_kewarganegaraan' => $row[17] ?: null,
                    'informatika' => $row[18] ?: null,
                    'kka' => $row[19] ?: null,
                    'pjok' => $row[20] ?: null,
                    'seni_budaya' => $row[21] ?: null,
                    'prakarya_dan_kewirausahaan' => $row[22] ?: null,
                    'ipa' => $row[23] ?: null,
                    'ips' => $row[24] ?: null,
                    'nilai_min_baru' => 0,
                    'nilai_max_baru' => 100,
                    'generated_by' => auth()->user()->name ?? 'Manual Import'
                ];
                
                // Update or insert
                DB::table('katrol_nilai_leger')->updateOrInsert(
                    [
                        'rombel_id' => $rombelId,
                        'tahun_pelajaran' => $tahunPelajaran,
                        'semester' => $semester,
                        'nisn' => $nisn
                    ],
                    $data
                );
                
                $imported++;
            }
            
            DB::commit();
            
            if (!empty($errors)) {
                return back()->with('warning', "Import selesai dengan $imported data berhasil, tetapi ada " . count($errors) . " error: " . implode(', ', $errors));
            }
            
            return back()->with('success', "Berhasil import $imported data nilai ke tabel katrol_nilai_leger!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
