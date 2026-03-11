<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ekstrakurikuler;
use App\Models\AnggotaEkstrakurikuler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerController extends Controller
{
    /**
     * Display list of ekstrakurikuler (Koordinator view)
     */
    public function index(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        // Filter values
        $filterTahun = $request->get('tahun', $tahunAktif);
        $filterSemester = $request->get('semester', $semesterAktif);
        
        // Validate tahun format
        if (!empty($filterTahun) && !preg_match('/^\d{4}\/\d{4}$/', $filterTahun)) {
            $filterTahun = $tahunAktif;
        }
        
        // Normalize semester
        $filterSemester = ucfirst(strtolower($filterSemester));
        if (!in_array($filterSemester, ['Ganjil', 'Genap'])) {
            $filterSemester = $semesterAktif;
        }
        
        // Query ekstrakurikuler with counts
        $ekstrakurikulerList = DB::table('ekstrakurikuler as e')
            ->leftJoin('anggota_ekstrakurikuler as ae', 'e.id', '=', 'ae.ekstrakurikuler_id')
            ->select(
                'e.*',
                DB::raw('COUNT(DISTINCT ae.siswa_id) as jumlah_anggota')
            )
            ->where('e.tahun_pelajaran', $filterTahun)
            ->where('e.semester', $filterSemester)
            ->groupBy('e.id')
            ->orderBy('e.nama_ekstrakurikuler', 'asc')
            ->get();
        
        // Get prestasi count for each ekstra
        foreach ($ekstrakurikulerList as $ekstra) {
            $ekstra->jumlah_prestasi = DB::table('prestasi_siswa')
                ->where('sumber_prestasi', 'ekstrakurikuler')
                ->where('sumber_id', $ekstra->id)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->count();
        }
        
        // Get all years for dropdown
        $allYears = DB::table('ekstrakurikuler')
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderByDesc('tahun_pelajaran')
            ->pluck('tahun_pelajaran');
        
        // Count active ekstrakurikuler
        $totalAktif = DB::table('ekstrakurikuler')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->count();
        
        return view('guru-bk.koordinator-ekstra.index', compact(
            'ekstrakurikulerList', 'filterTahun', 'filterSemester',
            'tahunAktif', 'semesterAktif', 'allYears', 'totalAktif'
        ));
    }

    /**
     * Export ekstrakurikuler data to Excel (multi-sheet)
     */
    public function exportExcel(Request $request)
    {
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';

        $filterTahun = $request->get('tahun', $tahunAktif);
        $filterSemester = $request->get('semester', $semesterAktif);

        $ekstraList = DB::table('ekstrakurikuler')
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->orderBy('nama_ekstrakurikuler')
            ->get();

        if ($ekstraList->isEmpty()) {
            return back()->with('error', 'Tidak ada data ekstrakurikuler untuk diekspor.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($ekstraList as $idx => $ekstra) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, mb_substr($ekstra->nama_ekstrakurikuler, 0, 31));
            $spreadsheet->addSheet($sheet, $idx);

            $sheet->setCellValue('A1', $ekstra->nama_ekstrakurikuler);
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            $pembinaList = array_filter([$ekstra->pembina_1, $ekstra->pembina_2, $ekstra->pembina_3]);
            $sheet->setCellValue('A2', 'Pembina: ' . implode(', ', $pembinaList));
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('A3', 'Tahun: ' . $filterTahun . ' | Semester: ' . $filterSemester);
            $sheet->mergeCells('A3:H3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

            // Anggota & Nilai
            $row = 5;
            $sheet->setCellValue('A' . $row, 'DAFTAR ANGGOTA & NILAI');
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('F59E0B');
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $headers = ['No', 'NIS', 'Nama Siswa', 'Rombel', 'Tgl Bergabung', 'Status', 'Nilai', 'Predikat'];
            foreach ($headers as $col => $header) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . $row;
                $sheet->setCellValue($cell, $header);
            }
            $headerRange = 'A' . $row . ':H' . $row;
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()->setFillType('solid')->getStartColor()->setRGB('FEF3C7');
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle('thin');

            $anggota = DB::table('anggota_ekstrakurikuler as ae')
                ->join('siswa as s', 'ae.siswa_id', '=', 's.id')
                ->where('ae.ekstrakurikuler_id', $ekstra->id)
                ->select('s.nis', 's.nama', 's.angkatan_masuk',
                    's.rombel_semester_1', 's.rombel_semester_2',
                    's.rombel_semester_3', 's.rombel_semester_4',
                    's.rombel_semester_5', 's.rombel_semester_6',
                    'ae.tanggal_bergabung', 'ae.status', 'ae.nilai', 'ae.siswa_id')
                ->orderBy('s.nama')
                ->get();

            // Calculate semester column for rombel
            $tahunAjaran = explode('/', $filterTahun);
            $tahunAwal = intval($tahunAjaran[0]);

            $row++;
            $no = 1;
            foreach ($anggota as $a) {
                $predikat = $this->getNilaiPredikat($a->nilai);

                // Determine active rombel based on angkatan and period
                $tingkat = $tahunAwal - intval($a->angkatan_masuk) + 1;
                if (strtolower($filterSemester) == 'ganjil') {
                    $semCol = ($tingkat * 2) - 1;
                } else {
                    $semCol = $tingkat * 2;
                }
                $rombelCol = 'rombel_semester_' . $semCol;
                $rombel = ($semCol >= 1 && $semCol <= 6 && isset($a->$rombelCol)) ? $a->$rombelCol : '-';

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValueExplicit('B' . $row, $a->nis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $row, $a->nama);
                $sheet->setCellValue('D' . $row, $rombel ?: '-');
                $sheet->setCellValue('E' . $row, $a->tanggal_bergabung ? date('d/m/Y', strtotime($a->tanggal_bergabung)) : '-');
                $sheet->setCellValue('F' . $row, $a->status ?? 'Aktif');
                $sheet->setCellValue('G' . $row, $a->nilai ?? '-');
                $sheet->setCellValue('H' . $row, $predikat);
                $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle('thin');
                $row++;
            }

            if ($anggota->isEmpty()) {
                $sheet->setCellValue('A' . $row, 'Belum ada anggota terdaftar');
                $sheet->mergeCells('A' . $row . ':H' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row++;
            }

            // Prestasi
            $row += 2;
            $sheet->setCellValue('A' . $row, 'PRESTASI');
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('10B981');
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $prestasiHeaders = ['No', 'Nama Kompetisi', 'Juara', 'Tingkat', 'Penyelenggara', 'Tanggal', 'Tipe', 'Nama Siswa'];
            foreach ($prestasiHeaders as $col => $header) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . $row;
                $sheet->setCellValue($cell, $header);
            }
            $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('D1FAE5');
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle('thin');

            $prestasi = DB::table('prestasi_siswa as ps')
                ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
                ->where('ps.sumber_prestasi', 'ekstrakurikuler')
                ->where('ps.sumber_id', $ekstra->id)
                ->select('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.penyelenggara',
                         'ps.tanggal_pelaksanaan', 'ps.tipe_peserta', 's.nama as nama_siswa')
                ->orderBy('ps.tanggal_pelaksanaan', 'desc')
                ->get();

            $row++;
            $no = 1;
            foreach ($prestasi as $p) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $p->nama_kompetisi);
                $sheet->setCellValue('C' . $row, $p->juara);
                $sheet->setCellValue('D' . $row, $p->jenjang);
                $sheet->setCellValue('E' . $row, $p->penyelenggara ?? '-');
                $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($p->tanggal_pelaksanaan)));
                $sheet->setCellValue('G' . $row, $p->tipe_peserta == 'Single' ? 'Individu' : ($p->tipe_peserta ?? '-'));
                $sheet->setCellValue('H' . $row, $p->nama_siswa);
                $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle('thin');
                $row++;
            }

            if ($prestasi->isEmpty()) {
                $sheet->setCellValue('A' . $row, 'Belum ada prestasi');
                $sheet->mergeCells('A' . $row . ':H' . $row);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
            }

            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'Rekap_Ekstrakurikuler_' . str_replace('/', '-', $filterTahun) . '_' . $filterSemester . '_' . date('Ymd') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $temp = tempnam(sys_get_temp_dir(), 'ekstra');
        $writer->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function getNilaiPredikat($nilai)
    {
        if ($nilai === null || $nilai === '' || $nilai === '-') return '-';
        $v = strtoupper(trim($nilai));
        if ($v === 'A') return 'Sangat Baik';
        if ($v === 'B') return 'Baik';
        if ($v === 'C') return 'Cukup';
        if ($v === 'D') return 'Kurang';
        // Fallback for numeric values
        if (is_numeric($v)) {
            $n = intval($v);
            if ($n >= 90) return 'Sangat Baik';
            if ($n >= 80) return 'Baik';
            if ($n >= 70) return 'Cukup';
            return 'Kurang';
        }
        return $v;
    }

    /**
     * Delete ekstrakurikuler
     */
    public function destroy($id)
    {
        $ekstra = Ekstrakurikuler::find($id);
        
        if ($ekstra) {
            AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)->delete();
            $ekstra->delete();
        }
        
        return redirect()->route('guru_bk.koordinator-ekstra.index')
            ->with('success', 'Data ekstrakurikuler berhasil dihapus!');
    }
    
    /**
     * Show form to create new ekstrakurikuler
     */
    public function create()
    {
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        // Get pembina list (guru + guru_bk)
        $pembinaList = $this->getPembinaList();
        
        // Get rombel list for modal
        $rombelList = \App\Models\Rombel::where('tahun_pelajaran', $tahunAktif)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semesterAktif)])
            ->orderBy('nama_rombel', 'asc')
            ->pluck('nama_rombel');
        
        // Get angkatan list
        $angkatanList = \App\Models\Siswa::select('angkatan_masuk')
            ->whereNotNull('angkatan_masuk')
            ->distinct()
            ->orderBy('angkatan_masuk', 'desc')
            ->pluck('angkatan_masuk');
        
        return view('guru-bk.koordinator-ekstra.create', compact(
            'tahunAktif', 'semesterAktif', 'pembinaList', 'rombelList', 'angkatanList'
        ));
    }
    
    /**
     * Store new ekstrakurikuler
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ekstrakurikuler' => 'required|min:3'
        ]);
        
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        
        $ekstra = new Ekstrakurikuler();
        $ekstra->nama_ekstrakurikuler = $request->nama_ekstrakurikuler;
        $ekstra->tahun_pelajaran = $periodeAktif->tahun_pelajaran;
        $ekstra->semester = $periodeAktif->semester;
        $ekstra->pembina_1 = $request->pembina_1;
        $ekstra->pembina_2 = $request->pembina_2;
        $ekstra->pembina_3 = $request->pembina_3;
        $ekstra->deskripsi = $request->deskripsi;
        $ekstra->save();
        
        if ($request->has('anggota_ids') && is_array($request->anggota_ids)) {
            foreach ($request->anggota_ids as $siswaId) {
                AnggotaEkstrakurikuler::create([
                    'ekstrakurikuler_id' => $ekstra->id,
                    'siswa_id' => $siswaId,
                    'tahun_pelajaran' => $periodeAktif->tahun_pelajaran,
                    'semester' => $periodeAktif->semester,
                    'tanggal_bergabung' => now()->format('Y-m-d')
                ]);
            }
        }
        
        return redirect()->route('guru_bk.koordinator-ekstra.index')
            ->with('success', 'Ekstrakurikuler berhasil ditambahkan!');
    }
    
    /**
     * Show form to edit ekstrakurikuler
     */
    public function edit($id)
    {
        $ekstra = Ekstrakurikuler::findOrFail($id);
        
        $pembinaList = $this->getPembinaList();
        
        $anggotaTerdaftar = AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)
            ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('semester', $ekstra->semester)
            ->pluck('siswa_id')
            ->toArray();
        
        $rombelList = \App\Models\Rombel::where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->whereRaw('LOWER(semester) = ?', [strtolower($ekstra->semester)])
            ->orderBy('nama_rombel', 'asc')
            ->pluck('nama_rombel');
        
        $angkatanList = \App\Models\Siswa::select('angkatan_masuk')
            ->whereNotNull('angkatan_masuk')
            ->distinct()
            ->orderBy('angkatan_masuk', 'desc')
            ->pluck('angkatan_masuk');
        
        return view('guru-bk.koordinator-ekstra.edit', compact(
            'ekstra', 'pembinaList', 'anggotaTerdaftar', 'rombelList', 'angkatanList'
        ));
    }
    
    /**
     * Update ekstrakurikuler
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekstrakurikuler' => 'required|min:3'
        ]);
        
        $ekstra = Ekstrakurikuler::findOrFail($id);
        $ekstra->nama_ekstrakurikuler = $request->nama_ekstrakurikuler;
        $ekstra->pembina_1 = $request->pembina_1;
        $ekstra->pembina_2 = $request->pembina_2;
        $ekstra->pembina_3 = $request->pembina_3;
        $ekstra->deskripsi = $request->deskripsi;
        $ekstra->save();
        
        // Delete old anggota and save new ones
        AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)
            ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('semester', $ekstra->semester)
            ->delete();
        
        if ($request->has('anggota_ids') && is_array($request->anggota_ids)) {
            foreach ($request->anggota_ids as $siswaId) {
                AnggotaEkstrakurikuler::create([
                    'ekstrakurikuler_id' => $ekstra->id,
                    'siswa_id' => $siswaId,
                    'tahun_pelajaran' => $ekstra->tahun_pelajaran,
                    'semester' => $ekstra->semester
                ]);
            }
        }
        
        return redirect()->route('guru_bk.koordinator-ekstra.index')
            ->with('success', 'Ekstrakurikuler berhasil diupdate!');
    }
    
    /**
     * Get siswa for ekstrakurikuler modal (AJAX)
     */
    public function getSiswa(Request $request)
    {
        $search = $request->input('search', '');
        $rombel = $request->input('rombel', '');
        $angkatan = $request->input('angkatan', '');
        
        $query = \App\Models\Siswa::query();
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }
        
        // Filter by rombel: compute which rombel_semester_X to check
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        if (!empty($rombel) && !empty($angkatan)) {
            $rombelCol = $this->getRombelColumnForFilter($angkatan, $tahunAktif, $semesterAktif);
            if ($rombelCol) {
                $query->where($rombelCol, $rombel);
            }
        } elseif (!empty($rombel)) {
            // Fallback: search across all rombel_semester columns
            $query->where(function($q) use ($rombel) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("rombel_semester_$i", $rombel);
                }
            });
        }
        
        if (!empty($angkatan)) {
            $query->where('angkatan_masuk', $angkatan);
        }
        
        $siswa = $query->orderBy('nama', 'asc')->limit(100)->get();
        
        $data = $siswa->map(function($s) use ($tahunAktif, $semesterAktif) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'rombel_aktif' => $this->computeRombelAktif($s, $tahunAktif, $semesterAktif),
                'angkatan' => $s->angkatan_masuk
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Get siswa by IDs (AJAX)
     */
    public function getSiswaByIds(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return response()->json(['success' => false, 'data' => []]);
        }
        
        $idArray = explode(',', $ids);
        $siswa = \App\Models\Siswa::whereIn('id', $idArray)->get();
        
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        $data = $siswa->map(function($s) use ($tahunAktif, $semesterAktif) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'rombel_aktif' => $this->computeRombelAktif($s, $tahunAktif, $semesterAktif),
                'angkatan' => $s->angkatan_masuk
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Compute rombel_aktif from angkatan_masuk and rombel_semester_* columns
     */
    private function computeRombelAktif($siswa, $tahunPelajaran, $semester)
    {
        $angkatan = $siswa->angkatan_masuk;
        if (!empty($angkatan) && !empty($tahunPelajaran)) {
            $tahunAjaran = explode('/', $tahunPelajaran);
            $tahunAwal = intval($tahunAjaran[0] ?? 0);
            if ($tahunAwal > 0) {
                $tingkat = $tahunAwal - intval($angkatan) + 1;
                if (strtolower($semester) == 'ganjil') {
                    $semCol = ($tingkat * 2) - 1;
                } else {
                    $semCol = $tingkat * 2;
                }
                $rombelCol = 'rombel_semester_' . $semCol;
                if ($semCol >= 1 && $semCol <= 6 && !empty($siswa->$rombelCol)) {
                    return $siswa->$rombelCol;
                }
            }
        }
        
        // Fallback: use latest available rombel
        for ($i = 6; $i >= 1; $i--) {
            $col = "rombel_semester_$i";
            if (!empty($siswa->$col)) {
                return $siswa->$col;
            }
        }
        
        return '-';
    }
    
    /**
     * Get the correct rombel_semester_X column name for DB filtering
     */
    private function getRombelColumnForFilter($angkatan, $tahunPelajaran, $semester)
    {
        if (empty($angkatan) || empty($tahunPelajaran)) return null;
        
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0] ?? 0);
        if ($tahunAwal <= 0) return null;
        
        $tingkat = $tahunAwal - intval($angkatan) + 1;
        if (strtolower($semester ?: '') == 'ganjil') {
            $semCol = ($tingkat * 2) - 1;
        } else {
            $semCol = $tingkat * 2;
        }
        
        if ($semCol >= 1 && $semCol <= 6) {
            return 'rombel_semester_' . $semCol;
        }
        
        return null;
    }
    
    /**
     * Get pembina list (guru + guru_bk)
     */
    private function getPembinaList()
    {
        $guru = \App\Models\Guru::where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->pluck('nama')
            ->toArray();
        
        $guruBK = DB::table('guru_bk')
            ->where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->pluck('nama')
            ->toArray();
        
        $pembinaList = array_merge($guru, $guruBK);
        sort($pembinaList);
        
        return $pembinaList;
    }
}
