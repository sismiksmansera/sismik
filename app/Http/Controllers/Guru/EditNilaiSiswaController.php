<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class EditNilaiSiswaController extends Controller
{
    /**
     * Display edit form for specific siswa nilai
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        // Get parameters
        $idRombel = $request->get('id_rombel');
        $mapel = $request->get('mapel');
        $tanggal = $request->get('tanggal');
        $nisn = $request->get('nisn');

        if (empty($idRombel) || empty($mapel) || empty($tanggal) || empty($nisn)) {
            return redirect()->route('guru.lihat-nilai', [
                'id_rombel' => $idRombel,
                'mapel' => $mapel
            ])->with('error', 'Parameter tidak lengkap.');
        }

        // Get rombel data
        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        if (!$rombel) {
            return redirect()->route('guru.lihat-nilai', [
                'id_rombel' => $idRombel,
                'mapel' => $mapel
            ])->with('error', 'Data rombel tidak ditemukan.');
        }
        $namaRombel = $rombel->nama_rombel;

        // Get siswa data
        $siswa = DB::table('siswa')->where('nisn', $nisn)->first();
        if (!$siswa) {
            return redirect()->route('guru.lihat-nilai', [
                'id_rombel' => $idRombel,
                'mapel' => $mapel
            ])->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        // Get existing nilai data
        $nilaiData = DB::table('penilaian')
            ->where('nisn', $nisn)
            ->where('mapel', $mapel)
            ->where('tanggal_penilaian', $tanggal)
            ->where('nama_rombel', $namaRombel)
            ->first();

        if (!$nilaiData) {
            return redirect()->route('guru.lihat-nilai', [
                'id_rombel' => $idRombel,
                'mapel' => $mapel
            ])->with('error', 'Data nilai tidak ditemukan untuk siswa ini pada tanggal tersebut.');
        }

        return view('guru.edit-nilai-siswa', compact(
            'guru',
            'idRombel',
            'mapel',
            'tanggal',
            'namaRombel',
            'siswa',
            'nilaiData',
            'tahunPelajaran',
            'semesterAktif'
        ));
    }

    /**
     * Update nilai siswa
     */
    public function update(Request $request)
    {
        $request->validate([
            'nisn' => 'required',
            'mapel' => 'required',
            'tanggal_penilaian' => 'required|date',
            'nama_rombel' => 'required',
            'nilai' => 'required|numeric|min:0|max:100',
            'materi' => 'required|string',
        ]);

        $updated = DB::table('penilaian')
            ->where('nisn', $request->nisn)
            ->where('mapel', $request->mapel)
            ->where('tanggal_penilaian', $request->tanggal_penilaian)
            ->where('nama_rombel', $request->nama_rombel)
            ->update([
                'nilai' => $request->nilai,
                'materi' => $request->materi,
                'keterangan' => $request->keterangan ?? ''
            ]);

        if ($updated) {
            return redirect()->route('guru.lihat-nilai', [
                'id_rombel' => $request->id_rombel,
                'mapel' => $request->mapel
            ])->with('success', 'Nilai berhasil diupdate untuk ' . $request->nama_siswa);
        }

        return redirect()->back()->with('error', 'Gagal mengupdate nilai.');
    }
}
