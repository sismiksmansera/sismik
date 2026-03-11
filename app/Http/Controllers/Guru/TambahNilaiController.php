<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class TambahNilaiController extends Controller
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
        $nisn = $request->get('nisn');
        $namaSiswaParam = $request->get('nama_siswa');

        if (empty($idRombel) || empty($mapel) || empty($nisn)) {
            return redirect()->route('guru.lihat-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel])
                ->with('error', 'Parameter tidak lengkap.');
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

        // Parse tahun pelajaran for date range
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

        // Get student data
        $siswa = DB::table('siswa')->where('nisn', $nisn)->first();
        if (!$siswa) {
            return redirect()->route('guru.lihat-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel])
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('guru.tambah-nilai', compact(
            'guru',
            'namaGuru',
            'idRombel',
            'mapel',
            'namaRombel',
            'tahunPelajaran',
            'semesterAktif',
            'minDate',
            'maxDate',
            'siswa'
        ));
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'id_rombel' => 'required',
            'mapel' => 'required',
            'nama_rombel' => 'required',
            'guru' => 'required',
            'tahun_pelajaran' => 'required',
            'semester' => 'required',
            'tanggal_penilaian' => 'required|date',
            'materi' => 'required',
            'nisn' => 'required',
            'nama_siswa' => 'required',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $idRombel = $request->id_rombel;
        $mapel = $request->mapel;
        $namaRombel = $request->nama_rombel;
        $guru = $request->guru;
        $tahunPelajaran = $request->tahun_pelajaran;
        $semester = $request->semester;
        $tanggalPenilaian = $request->tanggal_penilaian;
        $materi = $request->materi;
        $nisn = $request->nisn;
        $nis = $request->nis;
        $namaSiswa = $request->nama_siswa;
        $nilai = $request->nilai;
        $keterangan = $request->keterangan ?? '';

        // Insert to penilaian table
        DB::table('penilaian')->insert([
            'nama_rombel' => $namaRombel,
            'mapel' => $mapel,
            'guru' => $guru,
            'tanggal_penilaian' => $tanggalPenilaian,
            'nis' => $nis,
            'nisn' => $nisn,
            'nama_siswa' => $namaSiswa,
            'materi' => $materi,
            'nilai' => $nilai,
            'keterangan' => $keterangan,
            'tahun_pelajaran' => $tahunPelajaran,
            'semester' => $semester,
        ]);

        return redirect()->route('guru.lihat-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel])
            ->with('success', 'Nilai berhasil ditambahkan untuk ' . $namaSiswa);
    }
}
