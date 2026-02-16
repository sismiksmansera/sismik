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

        return view('admin.cek-presensi', compact('tahunPelajaran', 'semesterAktif', 'rombelList'));
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

        // Get all students in this rombel
        $siswaList = DB::table('siswa')
            ->where('nama_rombel', function($query) use ($idRombel) {
                $query->select('nama_rombel')
                    ->from('rombel')
                    ->where('id', $idRombel)
                    ->limit(1);
            })
            ->where('status_siswa', 'Aktif')
            ->select('nisn', 'nama')
            ->orderBy('nama')
            ->get();

        // Get presensi records for this rombel and date
        $presensiRecords = DB::table('presensi_siswa')
            ->where('id_rombel', $idRombel)
            ->where('tanggal_presensi', $tanggal)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->whereRaw('LOWER(semester) = ?', [$semesterAktif])
            ->get();

        // Build lookup: nisn => record data
        $presensiByNisn = [];
        foreach ($presensiRecords as $rec) {
            $presensiByNisn[$rec->nisn] = $rec;
        }

        // Build result with JP 1-10
        $result = [];
        foreach ($siswaList as $index => $siswa) {
            $rec = $presensiByNisn[$siswa->nisn] ?? null;
            $jpData = [];
            $totalJpFilled = 0;
            $totalHadir = 0;

            for ($jp = 1; $jp <= 10; $jp++) {
                $field = "jam_ke_{$jp}";
                $val = $rec ? ($rec->$field ?? null) : null;
                $jpData["jp_{$jp}"] = $val;

                if ($val !== null && $val !== '' && $val !== '-') {
                    $totalJpFilled++;
                    if ($val === 'H') {
                        $totalHadir++;
                    }
                }
            }

            $prosentase = $totalJpFilled > 0 ? round(($totalHadir / $totalJpFilled) * 100, 1) : null;

            $result[] = array_merge([
                'no' => $index + 1,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'presensi_utama' => $rec ? ($rec->presensi ?? null) : null,
                'prosentase' => $prosentase,
            ], $jpData);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'tanggal' => $tanggal,
            'total_siswa' => count($siswaList),
        ]);
    }
}
