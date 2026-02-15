<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class TugasMengajarController extends Controller
{
    public function index(Request $request)
    {
        // Get logged in guru using guru guard
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        $namaGuru = $guru->nama;

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaranAktif = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'ganjil';

        // Filters
        $tahunFilter = $request->get('tahun', $tahunPelajaranAktif);
        $semesterFilter = $request->get('semester', $semesterAktif);

        // Check if filter matches active period
        $isPeriodeAktif = ($tahunFilter == $tahunPelajaranAktif && $semesterFilter == $semesterAktif);

        // Get unique tahun pelajaran from database
        $tahunList = DB::table('jadwal_pelajaran')
            ->select('tahun_pelajaran')
            ->distinct()
            ->where('nama_guru', $namaGuru)
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');

        // Build query conditions
        $queryConditions = "WHERE j.nama_guru = '$namaGuru'";
        $countConditions = "WHERE nama_guru = '$namaGuru'";

        if (!empty($tahunFilter)) {
            $queryConditions .= " AND j.tahun_pelajaran = '$tahunFilter'";
            $countConditions .= " AND tahun_pelajaran = '$tahunFilter'";
        }

        if (!empty($semesterFilter)) {
            $queryConditions .= " AND j.semester = '$semesterFilter'";
            $countConditions .= " AND semester = '$semesterFilter'";
        }

        // Get teaching assignments (distinct rombel & mapel)
        $assignments = DB::select("
            SELECT DISTINCT j.id_rombel, j.id_mapel, r.nama_rombel, m.nama_mapel, j.tahun_pelajaran, j.semester
            FROM jadwal_pelajaran j
            JOIN rombel r ON j.id_rombel = r.id
            JOIN mata_pelajaran m ON j.id_mapel = m.id
            $queryConditions
            ORDER BY j.tahun_pelajaran DESC, j.semester DESC, r.nama_rombel ASC, m.nama_mapel ASC
        ");

        // Calculate statistics
        $rombelSet = [];
        $mapelSet = [];
        foreach ($assignments as $row) {
            $rombelSet[$row->id_rombel] = true;
            $mapelSet[$row->id_mapel] = true;
        }
        $totalRombel = count($rombelSet);
        $totalMapel = count($mapelSet);

        $totalJam = DB::selectOne("SELECT COUNT(*) as total FROM jadwal_pelajaran $countConditions")->total ?? 0;

        // Process assignments with jadwal details
        $assignmentData = [];
        $totalJamTable = 0;

        foreach ($assignments as $row) {
            // Build jadwal conditions
            $jadwalConditions = "WHERE id_mapel = '{$row->id_mapel}' AND id_rombel = '{$row->id_rombel}' AND nama_guru = '$namaGuru'";
            
            if (!empty($tahunFilter)) {
                $jadwalConditions .= " AND tahun_pelajaran = '$tahunFilter'";
            }
            if (!empty($semesterFilter)) {
                $jadwalConditions .= " AND semester = '$semesterFilter'";
            }

            // Get jadwal for this assignment
            $jadwalItems = DB::select("
                SELECT hari, jam_ke, tahun_pelajaran, semester, kode_jadwal
                FROM jadwal_pelajaran
                $jadwalConditions
                ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jam_ke ASC
            ");

            // Group by hari
            $jadwalPerHari = [];
            $kodeJadwal = null;
            foreach ($jadwalItems as $j) {
                $jadwalPerHari[$j->hari][] = (int) $j->jam_ke;
                if ($kodeJadwal === null) {
                    $kodeJadwal = $j->kode_jadwal ?? null;
                }
            }

            // Get periode info from kode_jadwal
            $periodeInfo = null;
            if ($kodeJadwal) {
                $periodeInfo = \App\Models\PeriodeJadwal::where('kode', $kodeJadwal)->first();
            }

            // Count jam
            $jamCount = 0;
            foreach ($jadwalPerHari as $hari => $jamArr) {
                $jamCount += count($jamArr);
            }
            $totalJamTable += $jamCount;

            // Format jadwal per hari with ranges
            $jadwalFormatted = [];
            foreach ($jadwalPerHari as $hari => $jamArr) {
                sort($jamArr, SORT_NUMERIC);
                $jamArr = array_values(array_unique($jamArr, SORT_NUMERIC));
                $ranges = $this->formatJamRanges($jamArr);
                $jadwalFormatted[] = [
                    'hari' => $hari,
                    'jam_text' => 'Jam ke ' . implode(', ', $ranges)
                ];
            }

            $assignmentData[] = [
                'id_rombel' => $row->id_rombel,
                'id_mapel' => $row->id_mapel,
                'nama_rombel' => $row->nama_rombel,
                'nama_mapel' => $row->nama_mapel,
                'tahun_pelajaran' => $row->tahun_pelajaran,
                'semester' => $row->semester,
                'jadwal' => $jadwalFormatted,
                'jam_count' => $jamCount,
                'kode_jadwal' => $kodeJadwal,
                'tanggal_mulai' => $periodeInfo ? $periodeInfo->tanggal_mulai->format('Y-m-d') : null,
                'tanggal_akhir' => $periodeInfo && $periodeInfo->tanggal_akhir ? $periodeInfo->tanggal_akhir->format('Y-m-d') : null,
            ];
        }

        return view('guru.tugas-mengajar', compact(
            'guru',
            'tahunFilter',
            'semesterFilter',
            'tahunList',
            'isPeriodeAktif',
            'totalRombel',
            'totalMapel',
            'totalJam',
            'totalJamTable',
            'assignmentData'
        ));
    }

    /**
     * Format jam array into ranges (e.g., [1,2,3,5,6] -> ["1-3", "5-6"])
     */
    private function formatJamRanges(array $jamArr): array
    {
        if (empty($jamArr)) {
            return [];
        }

        if (count($jamArr) === 1) {
            return [(string) $jamArr[0]];
        }

        $ranges = [];
        $start = $jamArr[0];
        $end = $start;

        for ($i = 1; $i < count($jamArr); $i++) {
            $current = $jamArr[$i];
            if ($current === $end + 1) {
                $end = $current;
            } else {
                $ranges[] = $start === $end ? (string) $start : $start . '-' . $end;
                $start = $current;
                $end = $current;
            }
        }

        $ranges[] = $start === $end ? (string) $start : $start . '-' . $end;

        return $ranges;
    }
}
