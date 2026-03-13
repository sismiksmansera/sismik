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
            'Pendidikan Agama Islam', 'Pendidikan Agama Hindu', 'Pendidikan Agama Buddha',
            'Pendidikan Agama Katholik', 'Pendidikan Agama Kristen',
            'Pendidikan Kewarganegaraan',
            'Bahasa Indonesia', 'Bahasa Inggris',
            'Matematika', 'Matematika Lanjut', 'Bahasa Inggris Lanjut',
            'Biologi', 'Fisika', 'Kimia', 'IPA',
            'Geografi', 'Sejarah', 'Sosiologi', 'Ekonomi', 'IPS',
            'Seni Budaya', 'Informatika', 'PJOK', 'Bahasa Lampung',
            'KKA', 'Prakarya dan Kewirausahaan', 'Pendidikan Anti Korupsi'
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
        
        // Auto width (A through AD = 30 columns)
        $autoCols = array_merge(range('A','Z'), ['AA','AB','AC','AD']);
        foreach($autoCols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Freeze first row
        $sheet->freezePane('A2');
        
        // Add borders
        $lastRow = $row - 1;
        $sheet->getStyle('A1:AD' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);
        
        // Add Metadata sheet with tahun, semester, rombel info
        $metaSheet = $spreadsheet->createSheet();
        $metaSheet->setTitle('Metadata');
        $metaSheet->setCellValue('A1', 'Key');
        $metaSheet->setCellValue('B1', 'Value');
        $metaSheet->setCellValue('A2', 'tahun_pelajaran');
        $metaSheet->setCellValue('B2', $tahunPelajaran);
        $metaSheet->setCellValue('A3', 'semester');
        $metaSheet->setCellValue('B3', $semester);
        $metaSheet->setCellValue('A4', 'rombel_id');
        $metaSheet->setCellValue('B4', $rombelId);
        $metaSheet->setCellValue('A5', 'rombel_nama');
        $metaSheet->setCellValue('B5', $rombelNama);
        
        // Style metadata header
        $metaSheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        $metaSheet->getColumnDimension('A')->setAutoSize(true);
        $metaSheet->getColumnDimension('B')->setAutoSize(true);
        
        // Protect metadata sheet so users don't accidentally edit it
        $metaSheet->getProtection()->setSheet(true);
        
        // Set active sheet back to data sheet
        $spreadsheet->setActiveSheetIndex(0);
        
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
            'file' => 'required|file|mimes:xlsx,xls|max:5120'
        ]);
        
        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            
            // Read metadata from the Metadata sheet
            $metaSheet = $spreadsheet->getSheetByName('Metadata');
            if (!$metaSheet) {
                return back()->with('error', 'File tidak valid! Sheet "Metadata" tidak ditemukan. Gunakan template yang didownload dari sistem.');
            }
            
            $tahunPelajaran = $metaSheet->getCell('B2')->getValue();
            $semester = $metaSheet->getCell('B3')->getValue();
            $rombelId = $metaSheet->getCell('B4')->getValue();
            
            if (!$tahunPelajaran || !$semester || !$rombelId) {
                return back()->with('error', 'Metadata tidak lengkap! Pastikan file template tidak diubah.');
            }
            
            // Validate rombel exists
            $rombel = Rombel::find($rombelId);
            if (!$rombel) {
                return back()->with('error', 'Rombel ID ' . $rombelId . ' tidak ditemukan di database!');
            }
            
            $sheet = $spreadsheet->getSheet(0);
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
                    'pendidikan_agama_islam' => $row[3] ?: null,
                    'pendidikan_agama_hindu' => $row[4] ?: null,
                    'pendidikan_agama_buddha' => $row[5] ?: null,
                    'pendidikan_agama_katholik' => $row[6] ?: null,
                    'pendidikan_agama_kristen' => $row[7] ?: null,
                    'pendidikan_kewarganegaraan' => $row[8] ?: null,
                    'bahasa_indonesia' => $row[9] ?: null,
                    'bahasa_inggris' => $row[10] ?: null,
                    'matematika' => $row[11] ?: null,
                    'matematika_lanjut' => $row[12] ?: null,
                    'bahasa_inggris_lanjut' => $row[13] ?: null,
                    'biologi' => $row[14] ?: null,
                    'fisika' => $row[15] ?: null,
                    'kimia' => $row[16] ?: null,
                    'ipa' => $row[17] ?: null,
                    'geografi' => $row[18] ?: null,
                    'sejarah' => $row[19] ?: null,
                    'sosiologi' => $row[20] ?: null,
                    'ekonomi' => $row[21] ?: null,
                    'ips' => $row[22] ?: null,
                    'seni_budaya' => $row[23] ?: null,
                    'informatika' => $row[24] ?: null,
                    'pjok' => $row[25] ?: null,
                    'bahasa_lampung' => $row[26] ?: null,
                    'kka' => $row[27] ?: null,
                    'prakarya_dan_kewirausahaan' => $row[28] ?: null,
                    'pendidikan_anti_korupsi' => $row[29] ?: null,
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
