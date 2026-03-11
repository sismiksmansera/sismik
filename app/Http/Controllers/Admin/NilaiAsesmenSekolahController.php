<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NilaiAsesmenSekolah;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class NilaiAsesmenSekolahController extends Controller
{
    /**
     * Display Nilai Asesmen Sekolah table page
     */
    public function index(Request $request)
    {
        $query = NilaiAsesmenSekolah::query();

        // Apply filters
        if ($request->filled('tahun_pelajaran')) {
            $query->where('tahun_pelajaran', $request->tahun_pelajaran);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->filled('nama_rombel')) {
            $query->where('nama_rombel', $request->nama_rombel);
        }
        if ($request->filled('mata_pelajaran')) {
            $query->where('mata_pelajaran', $request->mata_pelajaran);
        }
        if ($request->filled('jenis_asesmen')) {
            $query->where('jenis_asesmen', $request->jenis_asesmen);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('nama_rombel')
                      ->orderBy('mata_pelajaran')
                      ->orderBy('nama_siswa')
                      ->paginate(25)
                      ->withQueryString();

        // Get filter options
        $tahunList = DataPeriodik::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');

        $semesterList = ['Ganjil', 'Genap'];

        $rombelList = NilaiAsesmenSekolah::select('nama_rombel')
            ->distinct()
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');

        $mapelList = NilaiAsesmenSekolah::select('mata_pelajaran')
            ->distinct()
            ->orderBy('mata_pelajaran')
            ->pluck('mata_pelajaran');

        $jenisAsesmenList = NilaiAsesmenSekolah::select('jenis_asesmen')
            ->distinct()
            ->orderBy('jenis_asesmen')
            ->pluck('jenis_asesmen');

        return view('admin.nilai-asesmen.index', compact(
            'data', 'tahunList', 'semesterList', 'rombelList', 'mapelList', 'jenisAsesmenList'
        ));
    }

    /**
     * Show download format page
     */
    public function downloadFormat()
    {
        $tahunList = DataPeriodik::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');

        $mapelList = MataPelajaran::select('nama_mapel')
            ->distinct()
            ->orderBy('nama_mapel')
            ->pluck('nama_mapel');

        $rombelList = Rombel::select('nama_rombel', 'tahun_pelajaran', 'semester')
            ->orderBy('nama_rombel')
            ->get();

        return view('admin.nilai-asesmen.download', compact('tahunList', 'mapelList', 'rombelList'));
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'jenis_asesmen' => 'required|string',
            'tahun_pelajaran' => 'required|string',
            'semester' => 'required|string',
        ]);

        $jenisAsesmen = $request->jenis_asesmen;
        $tahunPelajaran = $request->tahun_pelajaran;
        $semester = $request->semester;

        // Get rombels for this period
        $rombels = Rombel::where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semester)
            ->orderBy('nama_rombel')
            ->get();

        // Get all mata pelajaran
        $mapelList = MataPelajaran::select('nama_mapel')
            ->distinct()
            ->orderBy('nama_mapel')
            ->pluck('nama_mapel');

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();

        // ====== Sheet 1: Template Import ======
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Headers
        $headers = ['No', 'Jenis Asesmen', 'Semester', 'Tahun Pelajaran', 'Rombel', 'Mata Pelajaran', 'Nama Siswa', 'NISN', 'Nilai'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E86AB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $col++;
        }

        // Pre-fill some data for rows (50 rows ready)
        $templateRows = 51;
        for ($i = 2; $i <= $templateRows; $i++) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $jenisAsesmen);
            $sheet->setCellValue('C' . $i, $semester);
            $sheet->setCellValue('D' . $i, $tahunPelajaran);
        }

        // Add dropdown data validation for Rombel (column E) from Ref Rombel sheet
        $rombelCount = $rombels->count();
        if ($rombelCount > 0) {
            $rombelFormula = "'Ref Rombel'!\$B\$2:\$B\$" . ($rombelCount + 1);
            for ($i = 2; $i <= $templateRows; $i++) {
                $validation = $sheet->getCell('E' . $i)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowDropDown(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Rombel tidak valid');
                $validation->setError('Silakan pilih rombel dari daftar.');
                $validation->setPromptTitle('Pilih Rombel');
                $validation->setPrompt('Klik untuk memilih rombel dari daftar referensi.');
                $validation->setFormula1($rombelFormula);
            }
        }

        // Add dropdown data validation for Mata Pelajaran (column F) from Ref Mata Pelajaran sheet
        $mapelCount = $mapelList->count();
        if ($mapelCount > 0) {
            $mapelFormula = "'Ref Mata Pelajaran'!\$B\$2:\$B\$" . ($mapelCount + 1);
            for ($i = 2; $i <= $templateRows; $i++) {
                $validation = $sheet->getCell('F' . $i)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowDropDown(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Mata Pelajaran tidak valid');
                $validation->setError('Silakan pilih mata pelajaran dari daftar.');
                $validation->setPromptTitle('Pilih Mata Pelajaran');
                $validation->setPrompt('Klik untuk memilih mata pelajaran dari daftar referensi.');
                $validation->setFormula1($mapelFormula);
            }
        }

        // Auto width
        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // Add borders for header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Freeze first row
        $sheet->freezePane('A2');

        // ====== Sheet 2: Referensi Mata Pelajaran ======
        $refMapelSheet = $spreadsheet->createSheet();
        $refMapelSheet->setTitle('Ref Mata Pelajaran');

        $refMapelSheet->setCellValue('A1', 'No');
        $refMapelSheet->setCellValue('B1', 'Nama Mata Pelajaran');
        $refMapelSheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($mapelList as $mapel) {
            $refMapelSheet->setCellValue('A' . $row, $no++);
            $refMapelSheet->setCellValue('B' . $row, $mapel);
            $row++;
        }

        $refMapelSheet->getColumnDimension('A')->setAutoSize(true);
        $refMapelSheet->getColumnDimension('B')->setAutoSize(true);

        // Add borders
        $lastRowMapel = $row - 1;
        if ($lastRowMapel >= 1) {
            $refMapelSheet->getStyle('A1:B' . $lastRowMapel)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
            ]);
        }

        // ====== Sheet 3: Referensi Rombel ======
        $refRombelSheet = $spreadsheet->createSheet();
        $refRombelSheet->setTitle('Ref Rombel');

        $refRombelSheet->setCellValue('A1', 'No');
        $refRombelSheet->setCellValue('B1', 'Nama Rombel');
        $refRombelSheet->setCellValue('C1', 'Tahun Pelajaran');
        $refRombelSheet->setCellValue('D1', 'Semester');
        $refRombelSheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($rombels as $rombel) {
            $refRombelSheet->setCellValue('A' . $row, $no++);
            $refRombelSheet->setCellValue('B' . $row, $rombel->nama_rombel);
            $refRombelSheet->setCellValue('C' . $row, $rombel->tahun_pelajaran);
            $refRombelSheet->setCellValue('D' . $row, $rombel->semester);
            $row++;
        }

        foreach (range('A', 'D') as $c) {
            $refRombelSheet->getColumnDimension($c)->setAutoSize(true);
        }

        // Add borders
        $lastRowRombel = $row - 1;
        if ($lastRowRombel >= 1) {
            $refRombelSheet->getStyle('A1:D' . $lastRowRombel)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
            ]);
        }

        // Set active sheet back to first
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $fileName = 'Format_Nilai_Asesmen_' . str_replace(' ', '_', $jenisAsesmen) . '_' . $semester . '_' . str_replace('/', '-', $tahunPelajaran) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Show import page
     */
    public function importPage()
    {
        $tahunList = DataPeriodik::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');

        return view('admin.nilai-asesmen.import', compact('tahunList'));
    }

    /**
     * Import Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header
            array_shift($rows);

            $imported = 0;
            $skipped = 0;
            $skippedNisns = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                // Skip empty rows (check NISN column - index 7)
                if (empty($row[7])) continue;

                $jenisAsesmen = trim($row[1] ?? '');
                $semester = trim($row[2] ?? '');
                $tahunPelajaran = trim($row[3] ?? '');
                $namaRombel = trim($row[4] ?? '');
                $mataPelajaran = trim($row[5] ?? '');
                $namaSiswa = trim($row[6] ?? '');
                $nisn = trim($row[7] ?? '');
                $nilai = $row[8] ?? null;

                // Skip if required fields are empty
                if (empty($jenisAsesmen) || empty($semester) || empty($tahunPelajaran) || empty($namaRombel) || empty($mataPelajaran) || empty($nisn)) {
                    continue;
                }

                // Validate NISN exists in siswa table
                $siswa = DB::table('siswa')->where('nisn', $nisn)->first();
                if (!$siswa) {
                    $skipped++;
                    $skippedNisns[] = $nisn;
                    continue;
                }

                // Use nama from siswa table if not provided
                if (empty($namaSiswa)) {
                    $namaSiswa = $siswa->nama;
                }

                // Insert or update
                NilaiAsesmenSekolah::updateOrCreate(
                    [
                        'jenis_asesmen' => $jenisAsesmen,
                        'semester' => $semester,
                        'tahun_pelajaran' => $tahunPelajaran,
                        'nama_rombel' => $namaRombel,
                        'mata_pelajaran' => $mataPelajaran,
                        'nisn' => $nisn,
                    ],
                    [
                        'nama_siswa' => $namaSiswa,
                        'nilai' => is_numeric($nilai) ? $nilai : null,
                    ]
                );

                $imported++;
            }

            DB::commit();

            $message = "Berhasil import {$imported} data nilai asesmen.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati karena NISN tidak valid: " . implode(', ', array_unique($skippedNisns));
            }

            $type = $skipped > 0 ? 'warning' : 'success';

            return redirect()->route('admin.nilai-asesmen.index')->with($type, $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    /**
     * Delete a nilai asesmen record
     */
    public function destroy($id)
    {
        try {
            $data = NilaiAsesmenSekolah::findOrFail($id);
            $data->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete nilai asesmen records
     */
    public function destroyBulk(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
            }

            $deleted = NilaiAsesmenSekolah::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => "Berhasil menghapus {$deleted} data"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
