<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPeriodik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DownloadPresensiController extends Controller
{
    /**
     * Show the download presensi selector page
     */
    public function index()
    {
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        $rombelList = DB::table('rombel')
            ->selectRaw('MIN(id) as id, nama_rombel, MIN(tingkat) as tingkat')
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semesterAktif)
            ->groupBy('nama_rombel')
            ->orderByRaw('MIN(tingkat)')
            ->orderByRaw("CAST(REGEXP_SUBSTR(nama_rombel, '[0-9]+$') AS UNSIGNED)")
            ->orderBy('nama_rombel')
            ->get();

        return view('admin.download-presensi', compact('tahunPelajaran', 'semesterAktif', 'rombelList'))
            ->with('routePrefix', 'admin');
    }

    /**
     * Download Blangko Presensi as XLSX
     * Each rombel gets its own sheet — blank attendance template
     */
    public function downloadBlangko(Request $request)
    {
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        // Get all rombel
        $rombelList = DB::table('rombel')
            ->selectRaw('MIN(id) as id, nama_rombel, MIN(tingkat) as tingkat')
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semesterAktif)
            ->groupBy('nama_rombel')
            ->orderByRaw('MIN(tingkat)')
            ->orderByRaw("CAST(REGEXP_SUBSTR(nama_rombel, '[0-9]+$') AS UNSIGNED)")
            ->orderBy('nama_rombel')
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $tahunAwal = explode('/', $tahunPelajaran)[0];
        $tahunAktif = (int)$tahunAwal;
        $maxJp = 10;
        $lastDataCol = $this->getColumnLetter(3 + $maxJp + 1); // N

        foreach ($rombelList as $rIdx => $rombel) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, substr($rombel->nama_rombel, 0, 31));
            $spreadsheet->addSheet($sheet);

            // Get students
            $whereConditions = [];
            for ($tahunAngkatan = $tahunAktif; $tahunAngkatan >= $tahunAktif - 2; $tahunAngkatan--) {
                $selisih = $tahunAktif - $tahunAngkatan;
                $semesterKe = $semesterAktif == 'Ganjil' ? ($selisih * 2) + 1 : ($selisih * 2) + 2;
                if ($semesterKe <= 6) {
                    $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = ?)";
                }
            }
            if (empty($whereConditions)) {
                $semesters = $semesterAktif == 'Ganjil' ? [1, 3, 5] : [2, 4, 6];
                foreach ($semesters as $sem) {
                    $whereConditions[] = "rombel_semester_$sem = ?";
                }
            }
            $whereClause = implode(' OR ', $whereConditions);
            $bindings = array_fill(0, count($whereConditions), $rombel->nama_rombel);
            $siswaList = \App\Models\Siswa::whereRaw("($whereClause)", $bindings)
                ->orderBy('nama')
                ->get();

            // =================== BUILD SHEET ===================
            $row = 1;

            // Row 1: Title
            $sheet->setCellValue('A' . $row, 'DAFTAR HADIR SISWA');
            $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;

            // Row 2: Subtitle
            $sheet->setCellValue('A' . $row, 'Tahun Pelajaran ' . $tahunPelajaran . ' — Semester ' . $semesterAktif);
            $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->setSize(11);
            $row += 2;

            // Row 4: Rombel info
            $sheet->setCellValue('A' . $row, 'Rombel');
            $sheet->setCellValue('C' . $row, ': ' . $rombel->nama_rombel);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('C' . $row)->getFont()->setSize(10);
            $row++;

            // Row 5: Tanggal (blank — to be filled manually)
            $sheet->setCellValue('A' . $row, 'Tanggal');
            $sheet->setCellValue('C' . $row, ': .......................................');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('C' . $row)->getFont()->setSize(10);
            $row += 2;

            // === TABLE HEADER (2 rows) ===
            // Row 7: Header row 1
            $headerRow1 = $row;
            $sheet->setCellValue('A' . $row, 'No');
            $sheet->setCellValue('B' . $row, 'NISN');
            $sheet->setCellValue('C' . $row, 'Nama Siswa');

            // Merge "Jam Pelajaran" across D-M (columns 4 to 13)
            $jpStartCol = $this->getColumnLetter(4); // D
            $jpEndCol = $this->getColumnLetter(13);  // M
            $sheet->setCellValue($jpStartCol . $row, 'Jam Pelajaran');
            $sheet->mergeCells($jpStartCol . $row . ':' . $jpEndCol . $row);

            $sheet->setCellValue($lastDataCol . $row, 'Ket.');
            $row++;

            // Row 8: Header row 2 — numbers 1 to 10
            $headerRow2 = $row;
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $col = $this->getColumnLetter(3 + $jp);
                $sheet->setCellValue($col . $row, $jp);
            }
            $sheet->setCellValue($lastDataCol . $row, '');

            // Merge header cells vertically (No, NISN, Nama, Ket.)
            $sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2);
            $sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2);
            $sheet->mergeCells('C' . $headerRow1 . ':C' . $headerRow2);
            $sheet->mergeCells($lastDataCol . $headerRow1 . ':' . $lastDataCol . $headerRow2);

            // Style headers
            $headerRange = 'A' . $headerRow1 . ':' . $lastDataCol . $headerRow2;
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle($headerRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E5E7EB');
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $row++;

            // === STUDENT ROWS ===
            $startDataRow = $row;
            foreach ($siswaList as $sIdx => $siswa) {
                $sheet->setCellValue('A' . $row, $sIdx + 1);
                $sheet->setCellValueExplicit('B' . $row, $siswa->nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $row, $siswa->nama);
                for ($jp = 1; $jp <= $maxJp; $jp++) {
                    $sheet->setCellValue($this->getColumnLetter(3 + $jp) . $row, '');
                }
                $sheet->setCellValue($lastDataCol . $row, '');
                $row++;
            }

            // Style data rows
            if (count($siswaList) > 0) {
                $dataRange = 'A' . $startDataRow . ':' . $lastDataCol . ($row - 1);
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($dataRange)->getFont()->setSize(9);
                $sheet->getStyle('A' . $startDataRow . ':A' . ($row - 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $startDataRow . ':B' . ($row - 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                for ($jp = 1; $jp <= $maxJp; $jp++) {
                    $col = $this->getColumnLetter(3 + $jp);
                    $sheet->getStyle($col . $startDataRow . ':' . $col . ($row - 1))->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }

            // === KETERANGAN ===
            $sheet->setCellValue('A' . $row, 'Keterangan: H = Hadir, S = Sakit, I = Izin, A = Alpa, D = Dispen, B = Bolos');
            $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(8);
            $row += 2;

            // === GURU SIGNATURE TABLE ===
            $guruTableStart = $row;
            // Header
            $sheet->setCellValue('B' . $row, 'Jam Ke');
            $sheet->mergeCells('C' . $row . ':' . $this->getColumnLetter(8) . $row); // C-H
            $sheet->setCellValue('C' . $row, 'Nama Guru');
            $sheet->mergeCells($this->getColumnLetter(9) . $row . ':' . $lastDataCol . $row); // I-N
            $sheet->setCellValue($this->getColumnLetter(9) . $row, 'Tanda Tangan');

            $sheet->getStyle('B' . $row . ':' . $lastDataCol . $row)->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle('B' . $row . ':' . $lastDataCol . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B' . $row . ':' . $lastDataCol . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $row++;

            // Rows 1-10
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $sheet->setCellValue('B' . $row, $jp);
                $sheet->mergeCells('C' . $row . ':' . $this->getColumnLetter(8) . $row);
                $sheet->setCellValue('C' . $row, '');
                $sheet->mergeCells($this->getColumnLetter(9) . $row . ':' . $lastDataCol . $row);
                $sheet->setCellValue($this->getColumnLetter(9) . $row, '');
                $row++;
            }

            // Style guru table data rows
            $guruDataRange = 'B' . ($guruTableStart + 1) . ':' . $lastDataCol . ($row - 1);
            $sheet->getStyle($guruDataRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($guruDataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B' . ($guruTableStart + 1) . ':B' . ($row - 1))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($guruDataRange)->getFont()->setSize(9);
            $row++;

            // === WALI KELAS & KETUA KELAS SIGNATURE ===
            $sheet->setCellValue('B' . $row, 'Wali Kelas');
            $sheet->mergeCells('B' . $row . ':' . $this->getColumnLetter(6) . $row);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue($this->getColumnLetter(9) . $row, 'Ketua Kelas');
            $sheet->mergeCells($this->getColumnLetter(9) . $row . ':' . $lastDataCol . $row);
            $sheet->getStyle($this->getColumnLetter(9) . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle($this->getColumnLetter(9) . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row += 4;

            // Signature lines
            $sheet->setCellValue('B' . $row, '.........................................');
            $sheet->mergeCells('B' . $row . ':' . $this->getColumnLetter(6) . $row);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue($this->getColumnLetter(9) . $row, '.........................................');
            $sheet->mergeCells($this->getColumnLetter(9) . $row . ':' . $lastDataCol . $row);
            $sheet->getStyle($this->getColumnLetter(9) . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row += 2;

            // === PRINT TIMESTAMP ===
            $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $now = now();
            $printDate = $now->format('j') . ' ' . $bulanNames[(int)$now->format('m')] . ' ' . $now->format('Y') . ' ' . $now->format('H:i');
            $sheet->setCellValue('A' . $row, 'dicetak pada : ' . $printDate);
            $sheet->mergeCells('A' . $row . ':' . $this->getColumnLetter(6) . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(8);

            // === ROW HEIGHTS === (row 9 onwards = 12.75)
            for ($r = 9; $r <= $row; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(12.75);
            }

            // === COLUMN WIDTHS ===
            $sheet->getColumnDimension('A')->setWidth(4);    // No
            $sheet->getColumnDimension('B')->setWidth(13);   // NISN
            $sheet->getColumnDimension('C')->setWidth(38);   // Nama Siswa
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $sheet->getColumnDimension($this->getColumnLetter(3 + $jp))->setWidth(3.5); // JP
            }
            $sheet->getColumnDimension($lastDataCol)->setWidth(8); // Ket.

            // === PRINT SETUP ===
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setPrintArea('A1:N66');
            $sheet->getPageSetup()->setFitToHeight(0);
            $sheet->getPageMargins()->setTop(0.3);
            $sheet->getPageMargins()->setBottom(0.3);
            $sheet->getPageMargins()->setLeft(0.6);
            $sheet->getPageMargins()->setRight(0.4);
        }

        if ($spreadsheet->getSheetCount() > 0) {
            $spreadsheet->setActiveSheetIndex(0);
        }

        $fileName = 'Blangko_Presensi_' . str_replace('/', '-', $tahunPelajaran) . '_Sem_' . $semesterAktif . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Download Rincian Presensi per Rombel as XLSX
     * Detailed attendance data per date per jam pelajaran
     */
    public function downloadRincianRombel(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $startDate = $request->query('start');
        $endDate = $request->query('end');

        if (!$idRombel || !$startDate || !$endDate) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 400);
        }

        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        if (!$rombel) {
            return response()->json(['error' => 'Rombel tidak ditemukan'], 404);
        }

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        $semesterLower = strtolower($semesterAktif);

        $tahunAwal = explode('/', $tahunPelajaran)[0];
        $tahunAktif = (int)$tahunAwal;

        // Get all rombel IDs with same nama_rombel
        $allRombelIds = DB::table('rombel')
            ->where('nama_rombel', $rombel->nama_rombel)
            ->pluck('id')
            ->toArray();

        // Get students in rombel
        $whereConditions = [];
        for ($tahunAngkatan = $tahunAktif; $tahunAngkatan >= $tahunAktif - 2; $tahunAngkatan--) {
            $selisih = $tahunAktif - $tahunAngkatan;
            $semesterKe = $semesterAktif == 'Ganjil' ? ($selisih * 2) + 1 : ($selisih * 2) + 2;
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = ?)";
            }
        }
        if (empty($whereConditions)) {
            $semesters = $semesterAktif == 'Ganjil' ? [1, 3, 5] : [2, 4, 6];
            foreach ($semesters as $sem) {
                $whereConditions[] = "rombel_semester_$sem = ?";
            }
        }
        $whereClause = implode(' OR ', $whereConditions);
        $bindings = array_fill(0, count($whereConditions), $rombel->nama_rombel);
        $siswaList = \App\Models\Siswa::whereRaw("($whereClause)", $bindings)
            ->orderBy('nama')
            ->get();

        // Get all presensi records in date range
        $presensiRecords = DB::table('presensi_siswa')
            ->whereIn('id_rombel', $allRombelIds)
            ->where('tanggal_presensi', '>=', $startDate)
            ->where('tanggal_presensi', '<=', $endDate)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(semester) = ?', [$semesterLower])
            ->orderBy('tanggal_presensi')
            ->get();

        // Build presensi map: tanggal => nisn => [jp_1..jp_10]
        $presensiMap = [];
        $datesWithData = [];
        foreach ($presensiRecords as $p) {
            $tgl = $p->tanggal_presensi;
            $nisn = $p->nisn;
            if (!isset($presensiMap[$tgl])) {
                $presensiMap[$tgl] = [];
                $datesWithData[] = $tgl;
            }
            if (!isset($presensiMap[$tgl][$nisn])) {
                $presensiMap[$tgl][$nisn] = array_fill(1, 10, null);
            }
            for ($jp = 1; $jp <= 10; $jp++) {
                $field = "jam_ke_$jp";
                $val = $p->$field ?? null;
                if ($val !== null && $val !== '' && $val !== '-') {
                    $presensiMap[$tgl][$nisn][$jp] = $val;
                }
            }
        }

        // Sort dates
        $datesWithData = array_unique($datesWithData);
        sort($datesWithData);

        // Indonesian day & month names
        $dayMap = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                   'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($rombel->nama_rombel, 0, 31));

        $maxJp = 10;
        $lastDataCol = $this->getColumnLetter(3 + $maxJp + 1); // N

        $row = 1;

        // Title
        $sheet->setCellValue('A' . $row, 'RINCIAN PRESENSI SISWA');
        $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Subtitle
        $sheet->setCellValue('A' . $row, 'Tahun Pelajaran ' . $tahunPelajaran . ' — Semester ' . $semesterAktif);
        $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setSize(11);
        $row += 2;

        // Info
        $sheet->setCellValue('A' . $row, 'Rombel');
        $sheet->setCellValue('C' . $row, ': ' . $rombel->nama_rombel);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
        $row++;

        // Date range
        $startFormatted = date('j', strtotime($startDate)) . ' ' . $bulanNames[(int)date('m', strtotime($startDate))] . ' ' . date('Y', strtotime($startDate));
        $endFormatted = date('j', strtotime($endDate)) . ' ' . $bulanNames[(int)date('m', strtotime($endDate))] . ' ' . date('Y', strtotime($endDate));
        $sheet->setCellValue('A' . $row, 'Periode');
        $sheet->setCellValue('C' . $row, ': ' . $startFormatted . '  s.d.  ' . $endFormatted);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
        $row += 2;

        // For each date with data, create a section
        foreach ($datesWithData as $tgl) {
            $dayName = $dayMap[date('l', strtotime($tgl))] ?? '';
            $d = date('j', strtotime($tgl));
            $m = (int)date('m', strtotime($tgl));
            $y = date('Y', strtotime($tgl));
            $tanggalFormatted = "$dayName, $d " . $bulanNames[$m] . " $y";

            // Date header
            $sheet->setCellValue('A' . $row, 'Tanggal: ' . $tanggalFormatted);
            $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('A' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D1E7DD');
            $row++;

            // Table header row 1
            $headerRow1 = $row;
            $sheet->setCellValue('A' . $row, 'No');
            $sheet->setCellValue('B' . $row, 'NISN');
            $sheet->setCellValue('C' . $row, 'Nama Siswa');

            $jpStartCol = $this->getColumnLetter(4);
            $jpEndCol = $this->getColumnLetter(13);
            $sheet->setCellValue($jpStartCol . $row, 'Jam Pelajaran');
            $sheet->mergeCells($jpStartCol . $row . ':' . $jpEndCol . $row);
            $sheet->setCellValue($lastDataCol . $row, 'Ket.');
            $row++;

            // Table header row 2 — numbers 1-10
            $headerRow2 = $row;
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $sheet->setCellValue($this->getColumnLetter(3 + $jp) . $row, $jp);
            }

            // Merge header cells vertically
            $sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2);
            $sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2);
            $sheet->mergeCells('C' . $headerRow1 . ':C' . $headerRow2);
            $sheet->mergeCells($lastDataCol . $headerRow1 . ':' . $lastDataCol . $headerRow2);

            // Style headers
            $headerRange = 'A' . $headerRow1 . ':' . $lastDataCol . $headerRow2;
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(8);
            $sheet->getStyle($headerRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E5E7EB');
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $row++;

            // Student rows
            $startDataRow = $row;
            foreach ($siswaList as $sIdx => $siswa) {
                $sheet->setCellValue('A' . $row, $sIdx + 1);
                $sheet->setCellValueExplicit('B' . $row, $siswa->nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $row, $siswa->nama);

                $statusData = $presensiMap[$tgl][$siswa->nisn] ?? array_fill(1, 10, null);
                $ketParts = [];
                for ($jp = 1; $jp <= $maxJp; $jp++) {
                    $status = $statusData[$jp] ?? '';
                    $col = $this->getColumnLetter(3 + $jp);
                    $sheet->setCellValue($col . $row, $status);

                    // Color cells based on status
                    if ($status) {
                        $color = match(strtoupper($status)) {
                            'H' => 'C6EFCE',
                            'S' => 'FCE4EC',
                            'I' => 'FFF3CD',
                            'A' => 'F8D7DA',
                            'D' => 'D1ECF1',
                            'B' => 'F5C6CB',
                            default => null,
                        };
                        if ($color) {
                            $sheet->getStyle($col . $row)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB($color);
                        }
                    }
                }

                // Build Ket: count non-H statuses
                $counts = ['S' => 0, 'I' => 0, 'A' => 0, 'D' => 0, 'B' => 0];
                for ($jp = 1; $jp <= $maxJp; $jp++) {
                    $s = strtoupper($statusData[$jp] ?? '');
                    if (isset($counts[$s])) {
                        $counts[$s]++;
                    }
                }
                $ketArr = [];
                foreach ($counts as $k => $v) {
                    if ($v > 0) $ketArr[] = "$k:$v";
                }
                $sheet->setCellValue($lastDataCol . $row, implode(' ', $ketArr));
                $row++;
            }

            // Style data rows
            if (count($siswaList) > 0) {
                $dataRange = 'A' . $startDataRow . ':' . $lastDataCol . ($row - 1);
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($dataRange)->getFont()->setSize(8);
                $sheet->getStyle('A' . $startDataRow . ':A' . ($row - 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $startDataRow . ':B' . ($row - 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                for ($jp = 1; $jp <= $maxJp; $jp++) {
                    $col = $this->getColumnLetter(3 + $jp);
                    $sheet->getStyle($col . $startDataRow . ':' . $col . ($row - 1))->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }

            $row++; // blank row between date sections
        }

        // Keterangan
        $sheet->setCellValue('A' . $row, 'Keterangan: H = Hadir, S = Sakit, I = Izin, A = Alpa, D = Dispen, B = Bolos');
        $sheet->mergeCells('A' . $row . ':' . $lastDataCol . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(8);
        $row += 2;

        // Print timestamp
        $now = now();
        $printDate = $now->format('j') . ' ' . $bulanNames[(int)$now->format('m')] . ' ' . $now->format('Y') . ' ' . $now->format('H:i');
        $sheet->setCellValue('A' . $row, 'dicetak pada : ' . $printDate);
        $sheet->mergeCells('A' . $row . ':' . $this->getColumnLetter(6) . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(8);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(13);
        $sheet->getColumnDimension('C')->setWidth(38);
        for ($jp = 1; $jp <= $maxJp; $jp++) {
            $sheet->getColumnDimension($this->getColumnLetter(3 + $jp))->setWidth(3.5);
        }
        $sheet->getColumnDimension($lastDataCol)->setWidth(8);

        // Row heights from row 9 onwards
        for ($r = 9; $r <= $row; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(12.75);
        }

        // Print setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.3);
        $sheet->getPageMargins()->setBottom(0.3);
        $sheet->getPageMargins()->setLeft(0.6);
        $sheet->getPageMargins()->setRight(0.4);

        $fileName = 'Rincian_Presensi_' . str_replace(' ', '_', $rombel->nama_rombel) . '_' . $startDate . '_' . $endDate . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Convert column index (1-based) to Excel letter (1=A, 2=B, ..., 27=AA)
     */
    private function getColumnLetter(int $index): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }
}
