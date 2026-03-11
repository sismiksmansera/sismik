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

            $headerRow2 = $row;
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $col = $this->getColumnLetter(3 + $jp);
                $sheet->setCellValue($col . $row, $jp);
            }
            $sheet->setCellValue($lastDataCol . $row, '');

            $sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2);
            $sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2);
            $sheet->mergeCells('C' . $headerRow1 . ':C' . $headerRow2);
            $sheet->mergeCells($lastDataCol . $headerRow1 . ':' . $lastDataCol . $headerRow2);

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
            $sheet->setCellValue('B' . $row, 'Jam Ke');
            $sheet->mergeCells('C' . $row . ':' . $this->getColumnLetter(8) . $row);
            $sheet->setCellValue('C' . $row, 'Nama Guru');
            $sheet->mergeCells($this->getColumnLetter(9) . $row . ':' . $lastDataCol . $row);
            $sheet->setCellValue($this->getColumnLetter(9) . $row, 'Tanda Tangan');

            $sheet->getStyle('B' . $row . ':' . $lastDataCol . $row)->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle('B' . $row . ':' . $lastDataCol . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B' . $row . ':' . $lastDataCol . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $row++;

            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $sheet->setCellValue('B' . $row, $jp);
                $sheet->mergeCells('C' . $row . ':' . $this->getColumnLetter(8) . $row);
                $sheet->setCellValue('C' . $row, '');
                $sheet->mergeCells($this->getColumnLetter(9) . $row . ':' . $lastDataCol . $row);
                $sheet->setCellValue($this->getColumnLetter(9) . $row, '');
                $row++;
            }

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

            // === ROW HEIGHTS ===
            for ($r = 9; $r <= $row; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(12.75);
            }

            // === COLUMN WIDTHS ===
            $sheet->getColumnDimension('A')->setWidth(4);
            $sheet->getColumnDimension('B')->setWidth(13);
            $sheet->getColumnDimension('C')->setWidth(38);
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $sheet->getColumnDimension($this->getColumnLetter(3 + $jp))->setWidth(3.5);
            }
            $sheet->getColumnDimension($lastDataCol)->setWidth(8);

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
     * Horizontal layout: students vertically, dates expand horizontally with JP 1-10
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

        $allRombelIds = DB::table('rombel')->where('nama_rombel', $rombel->nama_rombel)->pluck('id')->toArray();

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
        $siswaList = \App\Models\Siswa::whereRaw("(" . implode(' OR ', $whereConditions) . ")",
            array_fill(0, count($whereConditions), $rombel->nama_rombel))->orderBy('nama')->get();

        // Get presensi records
        $presensiRecords = DB::table('presensi_siswa')
            ->whereIn('id_rombel', $allRombelIds)
            ->where('tanggal_presensi', '>=', $startDate)
            ->where('tanggal_presensi', '<=', $endDate)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(semester) = ?', [$semesterLower])
            ->orderBy('tanggal_presensi')
            ->get();

        // Build map: tanggal => nisn => [1..10]
        $presensiMap = [];
        $datesWithData = [];
        foreach ($presensiRecords as $p) {
            $tgl = $p->tanggal_presensi;
            if (!isset($presensiMap[$tgl])) {
                $presensiMap[$tgl] = [];
                $datesWithData[] = $tgl;
            }
            if (!isset($presensiMap[$tgl][$p->nisn])) {
                $presensiMap[$tgl][$p->nisn] = array_fill(1, 10, null);
            }
            for ($jp = 1; $jp <= 10; $jp++) {
                $field = "jam_ke_$jp";
                $val = $p->$field ?? null;
                if ($val !== null && $val !== '' && $val !== '-') {
                    $presensiMap[$tgl][$p->nisn][$jp] = $val;
                }
            }
        }
        $datesWithData = array_values(array_unique($datesWithData));
        sort($datesWithData);

        $dayMap = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                   'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $maxJp = 10;
        $dateCount = count($datesWithData);
        $jpCols = $dateCount * $maxJp;
        $totalCols = 3 + $jpCols + 2; // +2 for % Hadir and Rekap
        $lastCol = $this->getColumnLetter(max($totalCols, 3));
        $colPersen = $this->getColumnLetter(3 + $jpCols + 1); // % Hadir
        $colRekap = $this->getColumnLetter(3 + $jpCols + 2);  // Rekap

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($rombel->nama_rombel, 0, 31));
        $row = 1;

        // Row 1: Title
        $sheet->setCellValue('A' . $row, 'RINCIAN PRESENSI SISWA');
        $sheet->mergeCells('A' . $row . ':' . $lastCol . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Row 2: Subtitle
        $sheet->setCellValue('A' . $row, 'Tahun Pelajaran ' . $tahunPelajaran . ' — Semester ' . $semesterAktif);
        $sheet->mergeCells('A' . $row . ':' . $lastCol . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setSize(11);
        $row += 2;

        // Row 4: Rombel
        $sheet->setCellValue('A' . $row, 'Rombel');
        $sheet->setCellValue('C' . $row, ': ' . $rombel->nama_rombel);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
        $row++;

        // Row 5: Periode
        $sf = date('j', strtotime($startDate)) . ' ' . $bulanNames[(int)date('m', strtotime($startDate))] . ' ' . date('Y', strtotime($startDate));
        $ef = date('j', strtotime($endDate)) . ' ' . $bulanNames[(int)date('m', strtotime($endDate))] . ' ' . date('Y', strtotime($endDate));
        $sheet->setCellValue('A' . $row, 'Periode');
        $sheet->setCellValue('C' . $row, ': ' . $sf . '  s.d.  ' . $ef);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
        $row += 2;

        // === HEADER ROW 1: Date headers merged across 10 JP columns ===
        $headerRow1 = $row;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'NISN');
        $sheet->setCellValue('C' . $row, 'Nama Siswa');

        foreach ($datesWithData as $dIdx => $tgl) {
            $cs = 4 + ($dIdx * $maxJp);
            $ce = $cs + $maxJp - 1;
            $dayName = $dayMap[date('l', strtotime($tgl))] ?? '';
            $sheet->setCellValue($this->getColumnLetter($cs) . $row, $dayName . ', ' . date('d/m', strtotime($tgl)));
            $sheet->mergeCells($this->getColumnLetter($cs) . $row . ':' . $this->getColumnLetter($ce) . $row);
        }
        $row++;

        // === HEADER ROW 2: JP 1-10 per date ===
        $headerRow2 = $row;
        foreach ($datesWithData as $dIdx => $tgl) {
            for ($jp = 1; $jp <= $maxJp; $jp++) {
                $sheet->setCellValue($this->getColumnLetter(4 + ($dIdx * $maxJp) + ($jp - 1)) . $row, $jp);
            }
        }

        // Summary column headers
        $sheet->setCellValue($colPersen . $headerRow1, '% Hadir');
        $sheet->setCellValue($colRekap . $headerRow1, 'Rekap');

        // Merge No/NISN/Nama/Summary vertically
        $sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2);
        $sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2);
        $sheet->mergeCells('C' . $headerRow1 . ':C' . $headerRow2);
        $sheet->mergeCells($colPersen . $headerRow1 . ':' . $colPersen . $headerRow2);
        $sheet->mergeCells($colRekap . $headerRow1 . ':' . $colRekap . $headerRow2);

        // Style headers
        $hr = 'A' . $headerRow1 . ':' . $lastCol . $headerRow2;
        $sheet->getStyle($hr)->getFont()->setBold(true)->setSize(8);
        $sheet->getStyle($hr)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle($hr)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($hr)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Alternating date header colors
        foreach ($datesWithData as $dIdx => $tgl) {
            $csLetter = $this->getColumnLetter(4 + ($dIdx * $maxJp));
            $ceLetter = $this->getColumnLetter(4 + ($dIdx * $maxJp) + $maxJp - 1);
            $clr = ($dIdx % 2 === 0) ? 'D6EAF8' : 'FADBD8';
            $sheet->getStyle($csLetter . $headerRow1 . ':' . $ceLetter . $headerRow1)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($clr);
        }
        $row++;

        // === STUDENT DATA ===
        $startDataRow = $row;
        foreach ($siswaList as $sIdx => $siswa) {
            $sheet->setCellValue('A' . $row, $sIdx + 1);
            $sheet->setCellValueExplicit('B' . $row, $siswa->nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $siswa->nama);

            // Counters for summary columns
            $totalJpFilled = 0;
            $counts = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'D' => 0, 'B' => 0];

            foreach ($datesWithData as $dIdx => $tgl) {
                $sd = $presensiMap[$tgl][$siswa->nisn] ?? array_fill(1, 10, null);
                for ($jp = 1; $jp <= $maxJp; $jp++) {
                    $ci = 4 + ($dIdx * $maxJp) + ($jp - 1);
                    $col = $this->getColumnLetter($ci);
                    $status = $sd[$jp] ?? '';
                    $sheet->setCellValue($col . $row, $status);
                    if ($status) {
                        $totalJpFilled++;
                        $upper = strtoupper($status);
                        if (isset($counts[$upper])) {
                            $counts[$upper]++;
                        }
                        $color = match ($upper) {
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
                                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                        }
                    }
                }
            }

            // % Hadir
            $persen = $totalJpFilled > 0 ? round(($counts['H'] / $totalJpFilled) * 100, 1) : 0;
            $sheet->setCellValue($colPersen . $row, $persen . '%');

            // Rekap
            $rekapParts = [];
            foreach ($counts as $k => $v) {
                if ($v > 0) $rekapParts[] = "$k:$v";
            }
            $sheet->setCellValue($colRekap . $row, implode(' ', $rekapParts));
            $row++;
        }

        // Style data rows
        if (count($siswaList) > 0) {
            $dr = 'A' . $startDataRow . ':' . $lastCol . ($row - 1);
            $sheet->getStyle($dr)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($dr)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($dr)->getFont()->setSize(8);
            $sheet->getStyle('A' . $startDataRow . ':A' . ($row - 1))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $startDataRow . ':B' . ($row - 1))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            for ($c = 4; $c <= $totalCols; $c++) {
                $sheet->getStyle($this->getColumnLetter($c) . $startDataRow . ':' . $this->getColumnLetter($c) . ($row - 1))
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // Keterangan
        $row++;
        $sheet->setCellValue('A' . $row, 'Keterangan: H = Hadir, S = Sakit, I = Izin, A = Alpa, D = Dispen, B = Bolos');
        $sheet->mergeCells('A' . $row . ':' . $this->getColumnLetter(min($totalCols, 14)) . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(8);
        $row += 2;

        // Print timestamp
        $now = now();
        $pd = $now->format('j') . ' ' . $bulanNames[(int)$now->format('m')] . ' ' . $now->format('Y') . ' ' . $now->format('H:i');
        $sheet->setCellValue('A' . $row, 'dicetak pada : ' . $pd);
        $sheet->mergeCells('A' . $row . ':' . $this->getColumnLetter(min($totalCols, 6)) . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(8);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(13);
        $sheet->getColumnDimension('C')->setWidth(30);
        for ($c = 4; $c <= (3 + $jpCols); $c++) {
            $sheet->getColumnDimension($this->getColumnLetter($c))->setWidth(3.5);
        }
        $sheet->getColumnDimension($colPersen)->setWidth(7);  // % Hadir
        $sheet->getColumnDimension($colRekap)->setWidth(18);  // Rekap

        // Row heights
        for ($r = $headerRow2; $r <= $row; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(12.75);
        }

        // Freeze panes: freeze columns A-C and header rows
        $sheet->freezePane('D' . ($headerRow2 + 1));

        // Print setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.3);
        $sheet->getPageMargins()->setBottom(0.3);
        $sheet->getPageMargins()->setLeft(0.4);
        $sheet->getPageMargins()->setRight(0.3);

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
