<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class OsnRegistrationController extends Controller
{
    public function index()
    {
        return view('pendaftaran-osn');
    }

    public function search(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|min:4',
        ]);

        $siswa = Siswa::where('nisn', $request->nisn)->first();

        if (!$siswa) {
            return response()->json([
                'found' => false,
                'message' => 'Data siswa dengan NISN tersebut tidak ditemukan.'
            ]);
        }

        return response()->json([
            'found' => true,
            'message' => 'Data siswa berhasil ditemukan!',
            'siswa' => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'jk' => $siswa->jk,
                'agama' => $siswa->agama,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tgl_lahir' => $siswa->tgl_lahir ? $siswa->tgl_lahir->format('d M Y') : '-',
                'provinsi' => $siswa->provinsi,
                'kota' => $siswa->kota,
                'kecamatan' => $siswa->kecamatan,
                'kelurahan' => $siswa->kelurahan,
                'dusun' => $siswa->dusun,
                'rt_rw' => $siswa->rt_rw,
                'email' => $siswa->email,
                'nohp_siswa' => $siswa->nohp_siswa,
                'mapel_osn_2026' => $siswa->mapel_osn_2026,
                'ikut_osn_2025' => $siswa->ikut_osn_2025,
                'foto' => $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto)
                    ? asset('storage/siswa/' . $siswa->foto)
                    : null,
                'initials' => collect(explode(' ', $siswa->nama))
                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                    ->take(2)
                    ->join(''),
                'rombel_aktif' => $siswa->nama_rombel,
                'angkatan' => $siswa->angkatan_masuk,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'email' => 'required|email',
            'nohp_siswa' => 'required|string|min:8',
            'dusun' => 'nullable|string|max:100',
            'rt_rw' => 'nullable|string|max:20',
            'mapel_osn_2026' => 'required|in:Matematika,Fisika,Kimia,Biologi,Geografi,Astronomi,Informatika,Ekonomi,Kebumian',
            'ikut_osn_2025' => 'required|in:Ya,Tidak',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nohp_siswa.required' => 'Nomor HP wajib diisi.',
            'nohp_siswa.min' => 'Nomor HP minimal 8 digit.',
            'mapel_osn_2026.required' => 'Pilih Mapel OSN 2026.',
            'mapel_osn_2026.in' => 'Mapel OSN 2026 tidak valid.',
            'ikut_osn_2025.required' => 'Pilih apakah ikut OSN 2025.',
            'ikut_osn_2025.in' => 'Pilihan OSN 2025 tidak valid.',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $siswa->update([
            'email' => $request->email,
            'nohp_siswa' => $request->nohp_siswa,
            'dusun' => $request->dusun,
            'rt_rw' => $request->rt_rw,
            'mapel_osn_2026' => $request->mapel_osn_2026,
            'ikut_osn_2025' => $request->ikut_osn_2025,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran OSN 2026 berhasil disimpan!'
        ]);
    }
}
