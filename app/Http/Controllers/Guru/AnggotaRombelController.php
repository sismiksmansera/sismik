<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class AnggotaRombelController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }
        $guruNama = $guru->nama;

        $idRombel = intval($request->get('id'));
        $tahunPelajaran = $request->get('tahun');
        $semester = $request->get('semester');

        if (!$idRombel || !$tahunPelajaran || !$semester) {
            return redirect()->route('guru.tugas-tambahan')->with('error', 'Parameter tidak lengkap!');
        }

        // Get rombel data
        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        if (!$rombel) {
            return redirect()->route('guru.tugas-tambahan')->with('error', 'Data rombel tidak ditemukan!');
        }

        $rombelNama = $rombel->nama_rombel;
        $waliKelas = $rombel->wali_kelas ?: "-";

        // Verify guru is wali kelas
        if ($waliKelas != $guruNama) {
            return redirect()->route('guru.tugas-tambahan')->with('error', 'Anda tidak memiliki akses ke rombel ini!');
        }

        // Dynamic logic for angkatan -> rombel semester mapping
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
                ->select('id', 'nis', 'nisn', 'nama', 'jk', 'agama', 'angkatan_masuk', 'foto')
                ->orderBy('nama', 'asc')
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
                ->select('id', 'nis', 'nisn', 'nama', 'jk', 'agama', 'angkatan_masuk', 'foto')
                ->orderBy('nama', 'asc')
                ->get();
        }

        // Calculate recap
        $totalLaki = $siswaList->where('jk', 'Laki-laki')->count();
        $totalPerempuan = $siswaList->where('jk', 'Perempuan')->count();
        $totalSiswa = $siswaList->count();

        return view('guru.anggota-rombel', compact(
            'guru', 'rombel', 'siswaList', 'tahunPelajaran', 'semester',
            'totalLaki', 'totalPerempuan', 'totalSiswa', 'idRombel'
        ));
    }
}
