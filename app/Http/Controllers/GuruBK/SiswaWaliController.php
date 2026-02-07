<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiswaWaliController extends Controller
{
    /**
     * Display students under Guru Wali guidance for Guru BK
     */
    public function index()
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guruNama = $guruBK->nama;

        // Get active period
        $periodeAktif = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();

        if (!$periodeAktif) {
            $periodeAktif = (object) [
                'tahun_pelajaran' => date('Y') . '/' . (date('Y') + 1),
                'semester' => 'Ganjil'
            ];
        }

        $tahunPelajaran = $periodeAktif->tahun_pelajaran;
        $semesterAktif = $periodeAktif->semester;

        // Calculate tahun values
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $tahunAwalMinus1 = $tahunAwal - 1;
        $tahunAwalMinus2 = $tahunAwal - 2;

        // Get siswa wali based on semester
        $siswaList = collect();

        if (strtolower($semesterAktif) == 'ganjil') {
            // Kelas X - Semester 1
            $kelas10 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwal)
                ->where('guru_wali_sem_1', $guruNama)
                ->selectRaw("siswa.*, 'X' as tingkat, 1 as semester_siswa, rombel_semester_1 as rombel")
                ->get();

            // Kelas XI - Semester 3
            $kelas11 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus1)
                ->where('guru_wali_sem_3', $guruNama)
                ->selectRaw("siswa.*, 'XI' as tingkat, 3 as semester_siswa, rombel_semester_3 as rombel")
                ->get();

            // Kelas XII - Semester 5
            $kelas12 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus2)
                ->where('guru_wali_sem_5', $guruNama)
                ->selectRaw("siswa.*, 'XII' as tingkat, 5 as semester_siswa, rombel_semester_5 as rombel")
                ->get();

            $siswaList = $kelas10->concat($kelas11)->concat($kelas12);
        } else {
            // Kelas X - Semester 2
            $kelas10 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwal)
                ->where('guru_wali_sem_2', $guruNama)
                ->selectRaw("siswa.*, 'X' as tingkat, 2 as semester_siswa, rombel_semester_2 as rombel")
                ->get();

            // Kelas XI - Semester 4
            $kelas11 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus1)
                ->where('guru_wali_sem_4', $guruNama)
                ->selectRaw("siswa.*, 'XI' as tingkat, 4 as semester_siswa, rombel_semester_4 as rombel")
                ->get();

            // Kelas XII - Semester 6
            $kelas12 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus2)
                ->where('guru_wali_sem_6', $guruNama)
                ->selectRaw("siswa.*, 'XII' as tingkat, 6 as semester_siswa, rombel_semester_6 as rombel")
                ->get();

            $siswaList = $kelas10->concat($kelas11)->concat($kelas12);
        }

        // Sort by tingkat then nama
        $siswaList = $siswaList->sortBy([
            fn($a, $b) => $a->tingkat <=> $b->tingkat,
            fn($a, $b) => $a->nama <=> $b->nama,
        ])->values();

        // Group by tingkat for display
        $siswaByTingkat = $siswaList->groupBy('tingkat');

        // Stats
        $totalSiswa = $siswaList->count();
        $totalLaki = $siswaList->where('jk', 'Laki-laki')->count();
        $totalPerempuan = $siswaList->where('jk', 'Perempuan')->count();

        return view('guru-bk.siswa-wali', compact(
            'guruBK',
            'tahunPelajaran',
            'semesterAktif',
            'siswaList',
            'siswaByTingkat',
            'totalSiswa',
            'totalLaki',
            'totalPerempuan'
        ));
    }
}
