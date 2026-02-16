<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class CekPresensiController extends Controller
{
    /**
     * Display the selector page
     */
    public function index()
    {
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        // Get all rombel for active period (unique only)
        $rombelList = DB::table('rombel')
            ->selectRaw('MIN(id) as id, nama_rombel, MIN(tingkat) as tingkat')
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semesterAktif)
            ->groupBy('nama_rombel')
            ->orderByRaw('MIN(tingkat)')
            ->orderByRaw("CAST(REGEXP_SUBSTR(nama_rombel, '[0-9]+$') AS UNSIGNED)")
            ->orderBy('nama_rombel')
            ->get();

        return view('admin.cek-presensi', compact('tahunPelajaran', 'semesterAktif', 'rombelList'))->with('routePrefix', 'admin');
    }

    /**
     * AJAX: Get mapel list for a given rombel (from jadwal_pelajaran)
     */
    public function getMapelList(Request $request)
    {
        $idRombel = $request->query('id_rombel');

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        $mapelList = DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $idRombel)
            ->where('jp.tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(jp.semester) = ?', [$semesterAktif])
            ->select('mp.id', 'mp.nama_mapel')
            ->distinct()
            ->orderBy('mp.nama_mapel')
            ->get();

        // Also get mapel from presensi_siswa that may not be in jadwal
        $mapelPresensi = DB::table('presensi_siswa')
            ->where('id_rombel', $idRombel)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(semester) = ?', [$semesterAktif])
            ->select('mata_pelajaran')
            ->distinct()
            ->pluck('mata_pelajaran');

        // Merge: add presensi mapel not already in jadwal list
        $existingNames = $mapelList->pluck('nama_mapel')->map(fn($n) => strtolower($n))->toArray();
        $extraMapel = [];
        foreach ($mapelPresensi as $mp) {
            if (!in_array(strtolower($mp), $existingNames)) {
                $extraMapel[] = (object)['id' => null, 'nama_mapel' => $mp];
            }
        }

        $merged = $mapelList->toArray();
        foreach ($extraMapel as $e) {
            $merged[] = $e;
        }

        return response()->json([
            'success' => true,
            'data' => $merged,
        ]);
    }

    /**
     * AJAX: Get presensi data grouped by date
     */
    public function getData(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        // Get per-date summary
        $perDateSummary = DB::table('presensi_siswa')
            ->where('id_rombel', $idRombel)
            ->where('mata_pelajaran', $mapel)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semesterAktif)])
            ->select(
                'tanggal_presensi',
                'guru_pengajar',
                DB::raw('COUNT(DISTINCT nisn) as total_siswa'),
                DB::raw("SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa"),
                DB::raw("SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen"),
                DB::raw("SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos")
            )
            ->groupBy('tanggal_presensi', 'guru_pengajar')
            ->orderBy('tanggal_presensi', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $perDateSummary,
        ]);
    }

    /**
     * AJAX: Get detailed presensi for a specific date
     */
    public function getDetail(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');
        $tanggal = $request->query('tanggal');

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        $detail = DB::table('presensi_siswa as ps')
            ->leftJoin('siswa as s', function ($join) {
                $join->on(DB::raw('ps.nisn COLLATE utf8mb4_general_ci'), '=', DB::raw('s.nisn COLLATE utf8mb4_general_ci'));
            })
            ->where('ps.id_rombel', $idRombel)
            ->where('ps.mata_pelajaran', $mapel)
            ->where('ps.tanggal_presensi', $tanggal)
            ->where('ps.tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(ps.semester) = ?', [$semesterAktif])
            ->select(
                'ps.id',
                'ps.nisn',
                's.nama as nama_siswa',
                'ps.presensi',
                'ps.guru_pengajar',
                'ps.mata_pelajaran',
                'ps.tanggal_waktu_record'
            )
            ->orderBy('s.nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $detail,
        ]);
    }

    /**
     * AJAX: Update a presensi record
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $presensi = $request->input('presensi');

        if (!$id || !$presensi) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $valid = ['H', 'S', 'I', 'A', 'D', 'B'];
        if (!in_array($presensi, $valid)) {
            return response()->json(['success' => false, 'message' => 'Status presensi tidak valid']);
        }

        $affected = DB::table('presensi_siswa')
            ->where('id', $id)
            ->update(['presensi' => $presensi]);

        return response()->json([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Presensi berhasil diperbarui' : 'Data tidak ditemukan',
        ]);
    }

    /**
     * AJAX: Update mapel for all presensi records on a given date + rombel
     */
    public function updateMapel(Request $request)
    {
        $idRombel = $request->input('id_rombel');
        $oldMapel = $request->input('old_mapel');
        $newMapel = $request->input('new_mapel');
        $tanggal = $request->input('tanggal');

        if (!$idRombel || !$oldMapel || !$newMapel || !$tanggal) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        $affected = DB::table('presensi_siswa')
            ->where('id_rombel', $idRombel)
            ->where('mata_pelajaran', $oldMapel)
            ->where('tanggal_presensi', $tanggal)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(semester) = ?', [$semesterAktif])
            ->update(['mata_pelajaran' => $newMapel]);

        return response()->json([
            'success' => true,
            'message' => "Mata pelajaran berhasil diubah untuk $affected record",
            'affected' => $affected,
        ]);
    }

    /**
     * AJAX: Get all mata pelajaran for picker
     */
    public function getAllMapel()
    {
        $mapelList = DB::table('mata_pelajaran')
            ->select('id', 'nama_mapel')
            ->orderBy('nama_mapel')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mapelList,
        ]);
    }

    /**
     * AJAX: Get hari libur/non-KBM dates for date picker
     */
    public function getHariLibur()
    {
        $dates = [];
        if (\Schema::hasTable('hari_efektif')) {
            $dates = DB::table('hari_efektif')
                ->pluck('tanggal')
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => $dates,
        ]);
    }

    /**
     * AJAX: Get presensi data per tanggal for a rombel (all students, JP 1-10)
     */
    public function getDataPerTanggal(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $tanggal = $request->query('tanggal');

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        // Get nama_rombel from the selected id
        $namaRombel = DB::table('rombel')->where('id', $idRombel)->value('nama_rombel');

        // Get ALL rombel IDs with same nama_rombel
        $allRombelIds = DB::table('rombel')
            ->where('nama_rombel', $namaRombel)
            ->pluck('id')
            ->toArray();

        // Get presensi records for the rombel and date
        $presensiRecords = DB::table('presensi_siswa as ps')
            ->leftJoin('siswa as s', function ($join) {
                $join->on(DB::raw('ps.nisn COLLATE utf8mb4_general_ci'), '=', DB::raw('s.nisn COLLATE utf8mb4_general_ci'));
            })
            ->whereIn('ps.id_rombel', $allRombelIds)
            ->where('ps.tanggal_presensi', $tanggal)
            ->where('ps.tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(ps.semester) = ?', [$semesterAktif])
            ->select(
                'ps.nisn',
                's.nama as nama_siswa',
                'ps.presensi',
                'ps.jam_ke_1', 'ps.jam_ke_2', 'ps.jam_ke_3', 'ps.jam_ke_4', 'ps.jam_ke_5',
                'ps.jam_ke_6', 'ps.jam_ke_7', 'ps.jam_ke_8', 'ps.jam_ke_9', 'ps.jam_ke_10'
            )
            ->orderBy('s.nama')
            ->get();

        // Merge JP data per student (across multiple mapel records)
        $merged = [];
        foreach ($presensiRecords as $rec) {
            $nisn = $rec->nisn;
            if (!isset($merged[$nisn])) {
                $merged[$nisn] = [
                    'nisn' => $nisn,
                    'nama' => $rec->nama_siswa ?? $rec->nisn,
                ];
                for ($jp = 1; $jp <= 10; $jp++) {
                    $merged[$nisn]["jp_{$jp}"] = null;
                }
            }
            for ($jp = 1; $jp <= 10; $jp++) {
                $field = "jam_ke_{$jp}";
                $val = $rec->$field ?? null;
                if ($val !== null && $val !== '' && $val !== '-') {
                    $merged[$nisn]["jp_{$jp}"] = $val;
                }
            }
        }

        // Build result with numbering and percentage
        $result = [];
        $no = 1;
        foreach ($merged as $row) {
            $totalJpFilled = 0;
            $totalHadir = 0;

            for ($jp = 1; $jp <= 10; $jp++) {
                $val = $row["jp_{$jp}"];
                if ($val !== null && $val !== '' && $val !== '-') {
                    $totalJpFilled++;
                    if ($val === 'H') {
                        $totalHadir++;
                    }
                }
            }

            $prosentase = $totalJpFilled > 0 ? round(($totalHadir / $totalJpFilled) * 100, 1) : null;

            $row['no'] = $no++;
            $row['prosentase'] = $prosentase;
            $result[] = $row;
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'tanggal' => $tanggal,
            'total_siswa' => count($result),
        ]);
    }

    /**
     * AJAX: Get week ranges (Mon-Fri) from semester start to today
     */
    public function getWeekRanges()
    {
        $periodik = DataPeriodik::aktif()->first();
        
        if (!$periodik) {
            return response()->json(['success' => false, 'data' => []]);
        }

        // Get semester start date from data_periodik
        $semesterStart = $periodik->tanggal_mulai ?? null;
        
        // If no tanggal_mulai, estimate based on semester and tahun_pelajaran
        if (!$semesterStart) {
            $tahun = explode('/', $periodik->tahun_pelajaran)[0];
            $semesterStart = $periodik->semester === 'Ganjil' 
                ? "{$tahun}-07-01"  // Ganjil starts ~July
                : ($tahun + 1) . "-01-01"; // Genap starts ~January
        }

        $startDate = new \DateTime($semesterStart);
        $today = new \DateTime();
        
        // Adjust to the first Monday
        if ($startDate->format('N') != 1) {
            $startDate->modify('next monday');
        }

        $weeks = [];
        $current = clone $startDate;

        while ($current <= $today) {
            $monday = clone $current;
            $friday = clone $current;
            $friday->modify('+4 days'); // Monday + 4 = Friday

            // Don't include future weeks beyond today
            if ($monday > $today) {
                break;
            }

            $weeks[] = [
                'start' => $monday->format('Y-m-d'),
                'end' => min($friday, $today)->format('Y-m-d'),
                'label' => sprintf(
                    '%s %s - %s %s',
                    $monday->format('d/m/Y'),
                    'Senin',
                    min($friday, $today)->format('d/m/Y'),
                    min($friday, $today)->format('N') == 5 ? 'Jumat' : $this->getDayName(min($friday, $today)->format('N'))
                ),
            ];

            $current->modify('+7 days');
        }

        return response()->json([
            'success' => true,
            'data' => array_reverse($weeks), // Most recent first
        ]);
    }

    /**
     * AJAX: Get presensi data per minggu for a rombel (all students, all dates in week with JP 1-10)
     */
    public function getDataPerMinggu(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $weekStart = $request->query('week_start');
        $weekEnd = $request->query('week_end');

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        // Get nama_rombel from the selected id
        $namaRombel = DB::table('rombel')->where('id', $idRombel)->value('nama_rombel');
        
        // Get ALL rombel IDs with same nama_rombel
        $allRombelIds = DB::table('rombel')
            ->where('nama_rombel', $namaRombel)
            ->pluck('id')
            ->toArray();

        // Get all dates in the week range (weekdays only)
        $dates = [];
        $current = new \DateTime($weekStart);
        $end = new \DateTime($weekEnd);
        
        while ($current <= $end) {
            $dayOfWeek = (int)$current->format('N'); // 1=Mon, 7=Sun
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Mon-Fri only
                $dates[] = $current->format('Y-m-d');
            }
            $current->modify('+1 day');
        }

        // Get presensi records for all dates in range
        $presensiRecords = DB::table('presensi_siswa as ps')
            ->leftJoin('siswa as s', function ($join) {
                $join->on(DB::raw('ps.nisn COLLATE utf8mb4_general_ci'), '=', DB::raw('s.nisn COLLATE utf8mb4_general_ci'));
            })
            ->whereIn('ps.id_rombel', $allRombelIds)
            ->whereBetween('ps.tanggal_presensi', [$weekStart, $weekEnd])
            ->where('ps.tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(ps.semester) = ?', [$semesterAktif])
            ->select(
                'ps.nisn',
                's.nama as nama_siswa',
                'ps.tanggal_presensi',
                'ps.jam_ke_1', 'ps.jam_ke_2', 'ps.jam_ke_3', 'ps.jam_ke_4', 'ps.jam_ke_5',
                'ps.jam_ke_6', 'ps.jam_ke_7', 'ps.jam_ke_8', 'ps.jam_ke_9', 'ps.jam_ke_10'
            )
            ->orderBy('s.nama')
            ->get();

        // Group by NISN and date
        $grouped = [];
        foreach ($presensiRecords as $rec) {
            $nisn = $rec->nisn;
            $tgl = $rec->tanggal_presensi;
            
            if (!isset($grouped[$nisn])) {
                $grouped[$nisn] = [
                    'nisn' => $nisn,
                    'nama' => $rec->nama_siswa ?? $nisn,
                    'dates' => [],
                ];
            }
            
            if (!isset($grouped[$nisn]['dates'][$tgl])) {
                $grouped[$nisn]['dates'][$tgl] = [];
                for ($jp = 1; $jp <= 10; $jp++) {
                    $grouped[$nisn]['dates'][$tgl]["jp_{$jp}"] = null;
                }
            }
            
            // Merge JP values from multiple records (multiple mapel on same day)
            for ($jp = 1; $jp <= 10; $jp++) {
                $field = "jam_ke_{$jp}";
                $val = $rec->$field ?? null;
                if ($val !== null && $val !== '' && $val !== '-') {
                    $grouped[$nisn]['dates'][$tgl]["jp_{$jp}"] = $val;
                }
            }
        }

        // Build result with all dates and percentage
        $result = [];
        $no = 1;
        foreach ($grouped as $student) {
            $row = [
                'no' => $no++,
                'nisn' => $student['nisn'],
                'nama' => $student['nama'],
            ];

            $totalHadir = 0;
            $totalJpFilled = 0;

            // Add JP data for each date
            foreach ($dates as $date) {
                $jpData = $student['dates'][$date] ?? null;
                
                for ($jp = 1; $jp <= 10; $jp++) {
                    $key = "{$date}_jp_{$jp}";
                    $val = $jpData ? ($jpData["jp_{$jp}"] ?? null) : null;
                    $row[$key] = $val;

                    if ($val !== null && $val !== '' && $val !== '-') {
                        $totalJpFilled++;
                        if ($val === 'H') {
                            $totalHadir++;
                        }
                    }
                }
            }

            $row['prosentase'] = $totalJpFilled > 0 ? round(($totalHadir / $totalJpFilled) * 100, 1) : null;
            $result[] = $row;
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'dates' => $dates,
            'total_siswa' => count($result),
        ]);
    }

    private function getDayName($dayNum)
    {
        $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return $days[(int)$dayNum] ?? '';
    }
    /**
     * AJAX: Search students by name or NISN
     */
    public function searchSiswa(Request $request)
    {
        $query = $request->query('q', '');
        if (strlen($query) < 2) {
            return response()->json(['success' => false, 'message' => 'Minimal 2 karakter']);
        }

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        // Search in siswa table
        $students = DB::table('siswa as s')
            ->join('rombel as r', function ($join) use ($tahunPelajaran, $semesterAktif) {
                $join->on(DB::raw('s.nisn COLLATE utf8mb4_general_ci'), '=', DB::raw('r.nisn COLLATE utf8mb4_general_ci'))
                     ->where('r.tahun_pelajaran', $tahunPelajaran)
                     ->where('r.semester', $semesterAktif);
            })
            ->where(function ($q) use ($query) {
                $q->where('s.nama', 'LIKE', "%{$query}%")
                  ->orWhere('s.nisn', 'LIKE', "%{$query}%");
            })
            ->select('s.nisn', 's.nama', 'r.nama_rombel')
            ->groupBy('s.nisn', 's.nama', 'r.nama_rombel')
            ->orderBy('s.nama')
            ->limit(20)
            ->get();

        return response()->json(['success' => true, 'data' => $students]);
    }

    /**
     * AJAX: Get presensi data per siswa (all dates or a specific date)
     */
    public function getDataPerSiswa(Request $request)
    {
        $nisn = $request->query('nisn');
        $tanggal = $request->query('tanggal'); // optional, for filtering

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        $query = DB::table('presensi_siswa as ps')
            ->leftJoin('siswa as s', function ($join) {
                $join->on(DB::raw('ps.nisn COLLATE utf8mb4_general_ci'), '=', DB::raw('s.nisn COLLATE utf8mb4_general_ci'));
            })
            ->where(DB::raw('ps.nisn COLLATE utf8mb4_general_ci'), '=', $nisn)
            ->where('ps.tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(ps.semester) = ?', [$semesterAktif]);

        if ($tanggal) {
            $query->where('ps.tanggal_presensi', $tanggal);
        }

        $records = $query->select(
                'ps.tanggal_presensi',
                'ps.mata_pelajaran',
                'ps.presensi',
                'ps.jam_ke_1', 'ps.jam_ke_2', 'ps.jam_ke_3', 'ps.jam_ke_4', 'ps.jam_ke_5',
                'ps.jam_ke_6', 'ps.jam_ke_7', 'ps.jam_ke_8', 'ps.jam_ke_9', 'ps.jam_ke_10',
                's.nama as nama_siswa'
            )
            ->orderBy('ps.tanggal_presensi', 'desc')
            ->get();

        // Get student name
        $namaSiswa = $records->first()->nama_siswa ?? $nisn;

        // Get rombel
        $rombel = DB::table('rombel')
            ->where(DB::raw('nisn COLLATE utf8mb4_general_ci'), $nisn)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $periodik->semester ?? 'Ganjil')
            ->value('nama_rombel') ?? '-';

        // Group by date and merge JP data
        $grouped = [];
        foreach ($records as $rec) {
            $tgl = $rec->tanggal_presensi;
            if (!isset($grouped[$tgl])) {
                $grouped[$tgl] = [
                    'tanggal' => $tgl,
                    'mapel_list' => [],
                ];
                for ($jp = 1; $jp <= 10; $jp++) {
                    $grouped[$tgl]["jp_{$jp}"] = null;
                }
            }

            // Collect mapel
            if ($rec->mata_pelajaran && !in_array($rec->mata_pelajaran, $grouped[$tgl]['mapel_list'])) {
                $grouped[$tgl]['mapel_list'][] = $rec->mata_pelajaran;
            }

            // Merge JP values
            for ($jp = 1; $jp <= 10; $jp++) {
                $field = "jam_ke_{$jp}";
                $val = $rec->$field ?? null;
                if ($val !== null && $val !== '' && $val !== '-') {
                    $grouped[$tgl]["jp_{$jp}"] = $val;
                }
            }
        }

        // Calculate percentages
        $result = [];
        foreach ($grouped as $row) {
            $totalJp = 0;
            $totalHadir = 0;
            for ($jp = 1; $jp <= 10; $jp++) {
                $val = $row["jp_{$jp}"];
                if ($val !== null && $val !== '' && $val !== '-') {
                    $totalJp++;
                    if ($val === 'H') $totalHadir++;
                }
            }
            $row['prosentase'] = $totalJp > 0 ? round(($totalHadir / $totalJp) * 100, 1) : null;
            $row['mapel'] = implode(', ', $row['mapel_list']);
            unset($row['mapel_list']);
            $result[] = $row;
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'nama_siswa' => $namaSiswa,
            'nisn' => $nisn,
            'rombel' => $rombel,
            'total_hari' => count($result),
        ]);
    }
}
