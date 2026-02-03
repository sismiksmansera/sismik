<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class RekapPresensiController extends Controller
{
    public function index(Request $request)
    {
        // Get logged in guru
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        $namaGuru = $guru->nama;

        // Get parameters
        $idRombel = $request->get('id_rombel');
        $mapel = $request->get('mapel');

        if (empty($idRombel) || empty($mapel)) {
            return redirect()->route('guru.tugas-mengajar')->with('error', 'Parameter tidak lengkap.');
        }

        // Get rombel data
        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        if (!$rombel) {
            return redirect()->route('guru.tugas-mengajar')->with('error', 'Data rombel tidak ditemukan.');
        }
        $namaRombel = $rombel->nama_rombel;

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        if (!$periodik) {
            return redirect()->route('guru.tugas-mengajar')->with('error', 'Periode aktif tidak ditemukan.');
        }
        $tahunPelajaran = $periodik->tahun_pelajaran;
        $semesterAktif = $periodik->semester;

        // Parse tahun pelajaran
        $tahunAwal = explode('/', $tahunPelajaran)[0];
        $tahunAktif = (int) $tahunAwal;
        $tahunAkhir = $tahunAktif + 1;

        // Calculate date range based on semester
        if (strtolower($semesterAktif) == 'ganjil') {
            $minDate = $tahunAktif . '-07-01';
            $maxDate = $tahunAktif . '-12-31';
        } else {
            $minDate = $tahunAkhir . '-01-01';
            $maxDate = $tahunAkhir . '-06-30';
        }

        // Get date filters with validation
        $tanggalMulai = $request->get('tanggal_mulai', $minDate);
        $tanggalSelesai = $request->get('tanggal_selesai', date('Y-m-d'));

        // Ensure dates are within allowed range
        if ($tanggalMulai < $minDate) $tanggalMulai = $minDate;
        if ($tanggalMulai > $maxDate) $tanggalMulai = $maxDate;
        if ($tanggalSelesai < $minDate) $tanggalSelesai = $minDate;
        if ($tanggalSelesai > $maxDate) $tanggalSelesai = $maxDate;

        // Detect mapel agama
        $isMapelAgama = false;
        $agamaMapel = '';
        if (stripos($mapel, 'islam') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Islam';
        } elseif (stripos($mapel, 'kristen') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Kristen';
        } elseif (stripos($mapel, 'katholik') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Katholik';
        } elseif (stripos($mapel, 'hindu') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Hindu';
        } elseif (stripos($mapel, 'buddha') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Buddha';
        }

        // Build where conditions for semester-based rombel
        $whereConditions = [];
        $tahunAngkatanMin = $tahunAktif - 2;
        $tahunAngkatanMax = $tahunAktif;

        for ($tahunAngkatan = $tahunAngkatanMax; $tahunAngkatan >= $tahunAngkatanMin; $tahunAngkatan--) {
            $selisihTahun = $tahunAktif - $tahunAngkatan;
            
            if (strtolower($semesterAktif) == 'ganjil') {
                $semesterKe = ($selisihTahun * 2) + 1;
            } else {
                $semesterKe = ($selisihTahun * 2) + 2;
            }
            
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = '$namaRombel')";
            }
        }

        if (empty($whereConditions)) {
            $semesters = strtolower($semesterAktif) == 'ganjil' ? [1, 3, 5] : [2, 4, 6];
            foreach ($semesters as $sem) {
                $whereConditions[] = "rombel_semester_$sem = '$namaRombel'";
            }
        }

        $whereClause = implode(' OR ', $whereConditions);
        if ($isMapelAgama && !empty($agamaMapel)) {
            $whereClause = "($whereClause) AND agama = '$agamaMapel'";
        }

        // Query rekap presensi
        $rekapData = DB::select("
            SELECT 
                s.id,
                s.nis,
                s.nisn,
                s.nama,
                s.agama,
                s.foto,
                COUNT(ps.id) as total_presensi,
                SUM(CASE WHEN ps.presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN ps.presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN ps.presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN ps.presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN ps.presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN ps.presensi = 'B' THEN 1 ELSE 0 END) as bolos
            FROM siswa s
            LEFT JOIN presensi_siswa ps ON s.nisn COLLATE utf8mb4_general_ci = ps.nisn COLLATE utf8mb4_general_ci
                AND ps.mata_pelajaran = '$mapel'
                AND ps.tanggal_presensi BETWEEN '$tanggalMulai' AND '$tanggalSelesai'
                AND ps.id_rombel = '$idRombel'
            WHERE ($whereClause)
            GROUP BY s.id, s.nis, s.nisn, s.nama, s.agama, s.foto
            ORDER BY s.nama ASC
        ");

        // Calculate statistics
        $totalSiswa = count($rekapData);
        $totalHadir = 0;
        $totalPresensi = 0;

        foreach ($rekapData as $row) {
            $totalHadir += $row->hadir;
            $totalPresensi += $row->total_presensi;
        }

        return view('guru.rekap-presensi', compact(
            'guru',
            'idRombel',
            'mapel',
            'namaRombel',
            'tahunPelajaran',
            'semesterAktif',
            'tanggalMulai',
            'tanggalSelesai',
            'minDate',
            'maxDate',
            'rekapData',
            'totalSiswa',
            'totalHadir',
            'totalPresensi'
        ));
    }

    /**
     * Display selector page for Presensi Siswa via Sidebar
     */
    public function selector()
    {
        $guru = Auth::guard('guru')->user();
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        $namaGuru = $guru->nama;
        $mapelList = DB::select("
            SELECT DISTINCT m.id, m.nama_mapel
            FROM jadwal_pelajaran j
            JOIN mata_pelajaran m ON j.id_mapel = m.id
            WHERE j.nama_guru = ?
            AND j.tahun_pelajaran = ?
            AND j.semester = ?
            ORDER BY m.nama_mapel ASC
        ", [$namaGuru, $tahunPelajaran, $semesterAktif]);
        
        return view('guru.presensi-selector', compact('guru', 'tahunPelajaran', 'semesterAktif', 'mapelList'));
    }

    /**
     * AJAX endpoint to get rekap presensi data
     */
    public function getRekapData(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');

        // Get rombel data
        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        if (!$rombel) {
            return response()->json(['success' => false, 'message' => 'Rombel tidak ditemukan']);
        }
        $namaRombel = $rombel->nama_rombel;

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran;
        $semesterAktif = $periodik->semester;

        // Calculate date range
        $tahunAwal = explode('/', $tahunPelajaran)[0];
        $tahunAktif = (int) $tahunAwal;
        $tahunAkhir = $tahunAktif + 1;

        if (strtolower($semesterAktif) == 'ganjil') {
            $minDate = $tahunAktif . '-07-01';
            $maxDate = $tahunAktif . '-12-31';
        } else {
            $minDate = $tahunAkhir . '-01-01';
            $maxDate = $tahunAkhir . '-06-30';
        }

        $tanggalMulai = $minDate;
        $tanggalSelesai = date('Y-m-d');
        if ($tanggalSelesai > $maxDate) $tanggalSelesai = $maxDate;

        // Detect mapel agama
        $isMapelAgama = false;
        $agamaMapel = '';
        if (stripos($mapel, 'islam') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Islam';
        } elseif (stripos($mapel, 'kristen') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Kristen';
        } elseif (stripos($mapel, 'katholik') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Katholik';
        } elseif (stripos($mapel, 'hindu') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Hindu';
        } elseif (stripos($mapel, 'buddha') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Buddha';
        }

        // Build where conditions
        $whereConditions = [];
        $tahunAngkatanMin = $tahunAktif - 2;
        $tahunAngkatanMax = $tahunAktif;

        for ($tahunAngkatan = $tahunAngkatanMax; $tahunAngkatan >= $tahunAngkatanMin; $tahunAngkatan--) {
            $selisihTahun = $tahunAktif - $tahunAngkatan;
            
            if (strtolower($semesterAktif) == 'ganjil') {
                $semesterKe = ($selisihTahun * 2) + 1;
            } else {
                $semesterKe = ($selisihTahun * 2) + 2;
            }
            
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = '$namaRombel')";
            }
        }

        if (empty($whereConditions)) {
            $semesters = strtolower($semesterAktif) == 'ganjil' ? [1, 3, 5] : [2, 4, 6];
            foreach ($semesters as $sem) {
                $whereConditions[] = "rombel_semester_$sem = '$namaRombel'";
            }
        }

        $whereClause = implode(' OR ', $whereConditions);
        if ($isMapelAgama && !empty($agamaMapel)) {
            $whereClause = "($whereClause) AND agama = '$agamaMapel'";
        }

        // Query rekap presensi
        $rekapData = DB::select("
            SELECT 
                s.id,
                s.nis,
                s.nisn,
                s.nama,
                s.agama,
                s.foto,
                COUNT(ps.id) as total_presensi,
                SUM(CASE WHEN ps.presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN ps.presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN ps.presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN ps.presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN ps.presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN ps.presensi = 'B' THEN 1 ELSE 0 END) as bolos
            FROM siswa s
            LEFT JOIN presensi_siswa ps ON s.nisn COLLATE utf8mb4_general_ci = ps.nisn COLLATE utf8mb4_general_ci
                AND ps.mata_pelajaran = '$mapel'
                AND ps.tanggal_presensi BETWEEN '$tanggalMulai' AND '$tanggalSelesai'
                AND ps.id_rombel = '$idRombel'
            WHERE ($whereClause)
            GROUP BY s.id, s.nis, s.nisn, s.nama, s.agama, s.foto
            ORDER BY s.nama ASC
        ");

        // Process data for JSON
        $processedData = [];
        foreach ($rekapData as $siswa) {
            $totalKehadiran = $siswa->hadir + $siswa->dispen;
            $persentase = $siswa->total_presensi > 0 
                ? round(($totalKehadiran / $siswa->total_presensi) * 100, 1) 
                : 0;
            
            $warnaKartu = $persentase >= 90 ? 'success' : ($persentase >= 75 ? 'warning' : 'danger');
            
            $initials = collect(explode(' ', $siswa->nama))
                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                ->take(2)
                ->join('');
            
            $hasFoto = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);

            $processedData[] = [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'initials' => $initials ?: 'S',
                'foto_exists' => $hasFoto,
                'foto_path' => $hasFoto ? asset('storage/siswa/' . $siswa->foto) : null,
                'total_presensi' => $siswa->total_presensi,
                'hadir' => $siswa->hadir,
                'dispen' => $siswa->dispen,
                'izin' => $siswa->izin,
                'sakit' => $siswa->sakit,
                'alfa' => $siswa->alfa,
                'bolos' => $siswa->bolos,
                'persentase' => $persentase,
                'warna_kartu' => $warnaKartu
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $processedData,
            'nama_rombel' => $namaRombel,
            'total_siswa' => count($processedData)
        ]);
    }
}
