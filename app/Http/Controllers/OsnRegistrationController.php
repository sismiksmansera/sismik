<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DataPeriodik;
use App\Models\AjangTalenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OsnRegistrationController extends Controller
{
    /**
     * Helper: Calculate active semester based on angkatan
     */
    private function calculateActiveSemester($angkatan, $tahunAktif, $semesterAktif)
    {
        if (empty($angkatan) || empty($tahunAktif) || empty($semesterAktif)) {
            return 1;
        }

        $tahunParts = explode('/', $tahunAktif);
        $tahunMulai = intval($tahunParts[0] ?? 0);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;

        if ($selisihTahun == 0) {
            return (strtolower($semesterAktif) == 'ganjil') ? 1 : 2;
        } elseif ($selisihTahun == 1) {
            return (strtolower($semesterAktif) == 'ganjil') ? 3 : 4;
        } elseif ($selisihTahun == 2) {
            return (strtolower($semesterAktif) == 'ganjil') ? 5 : 6;
        }
        return 1;
    }

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

        // Get active period and calculate rombel
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';

        $semesterNumber = $this->calculateActiveSemester(
            $siswa->angkatan_masuk,
            $tahunAktif,
            $semesterAktif
        );

        $rombelField = "rombel_semester_{$semesterNumber}";
        $rombelAktif = $siswa->$rombelField ?? $siswa->nama_rombel ?? '-';

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
                'tgl_lahir_raw' => $siswa->tgl_lahir ? $siswa->tgl_lahir->format('Y-m-d') : '',
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
                'foto' => $siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)
                    ? asset('storage/siswa/' . $siswa->foto)
                    : null,
                'initials' => collect(explode(' ', $siswa->nama))
                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                    ->take(2)
                    ->join(''),
                'rombel_aktif' => $rombelAktif,
                'angkatan' => $siswa->angkatan_masuk,
                'already_registered' => !empty($siswa->mapel_osn_2026),
                'registered_mapel' => $siswa->mapel_osn_2026,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'email' => 'required|email',
            'nohp_siswa' => 'required|string|min:8',
            'tempat_lahir' => 'required|string|max:100',
            'tgl_lahir' => 'required|date',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kelurahan' => 'required|string|max:100',
            'dusun' => 'nullable|string|max:100',
            'rt_rw' => 'nullable|string|max:20',
            'mapel_osn_2026' => 'required|in:Matematika,Fisika,Kimia,Biologi,Geografi,Astronomi,Informatika,Ekonomi,Kebumian',
            'ikut_osn_2025' => 'required|in:Ya,Tidak',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nohp_siswa.required' => 'Nomor HP wajib diisi.',
            'nohp_siswa.min' => 'Nomor HP minimal 8 digit.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
            'kota.required' => 'Kota/Kabupaten wajib diisi.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'kelurahan.required' => 'Kampung wajib diisi.',
            'mapel_osn_2026.required' => 'Pilih Mapel OSN 2026.',
            'mapel_osn_2026.in' => 'Mapel OSN 2026 tidak valid.',
            'ikut_osn_2025.required' => 'Pilih apakah ikut OSN 2025.',
            'ikut_osn_2025.in' => 'Pilihan OSN 2025 tidak valid.',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);

        // Require photo
        if (empty($siswa->foto) || !Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
            return response()->json([
                'success' => false,
                'message' => 'Foto profil wajib diupload sebelum mendaftar.',
            ], 422);
        }

        $siswa->update([
            'email' => $request->email,
            'nohp_siswa' => $request->nohp_siswa,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'dusun' => $request->dusun,
            'rt_rw' => $request->rt_rw,
            'mapel_osn_2026' => $request->mapel_osn_2026,
            'ikut_osn_2025' => $request->ikut_osn_2025,
        ]);

        // Auto-register siswa as peserta ajang talenta based on mapel
        $mapel = $request->mapel_osn_2026;
        if ($mapel) {
            // Find ajang talenta matching "OSN {mapel}" pattern
            $ajang = AjangTalenta::where('nama_ajang', 'LIKE', "%OSN%{$mapel}%")->first();

            if ($ajang) {
                // Insert if not already registered
                $exists = DB::table('peserta_ajang_talenta')
                    ->where('ajang_talenta_id', $ajang->id)
                    ->where('siswa_id', $siswa->id)
                    ->exists();

                if (!$exists) {
                    DB::table('peserta_ajang_talenta')->insert([
                        'ajang_talenta_id' => $ajang->id,
                        'siswa_id' => $siswa->id,
                        'status' => 'Aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran OSN 2026 berhasil disimpan!'
        ]);
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'foto' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);

        // Delete old photo if exists
        if ($siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
            Storage::disk('public')->delete('siswa/' . $siswa->foto);
        }

        // Store new photo
        $file = $request->file('foto');
        $filename = 'siswa_' . $siswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('siswa', $filename, 'public');

        $siswa->update([
            'foto' => $filename,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui!',
            'foto_url' => asset('storage/siswa/' . $filename),
        ]);
    }

    public function pesertaList()
    {
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';

        // Get all siswa registered for OSN
        $pesertaAll = Siswa::whereNotNull('mapel_osn_2026')
            ->where('mapel_osn_2026', '!=', '')
            ->orderBy('mapel_osn_2026', 'ASC')
            ->orderBy('nama', 'ASC')
            ->get();

        // Calculate rombel_aktif for each
        foreach ($pesertaAll as $siswa) {
            $semesterNumber = $this->calculateActiveSemester(
                $siswa->angkatan_masuk, $tahunAktif, $semesterAktif
            );
            $rombelField = "rombel_semester_{$semesterNumber}";
            $siswa->rombel_aktif = $siswa->$rombelField ?? $siswa->nama_rombel ?? '-';

            $siswa->foto_url = $siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)
                ? asset('storage/siswa/' . $siswa->foto)
                : null;

            $siswa->initials = collect(explode(' ', $siswa->nama))
                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                ->take(2)->join('');
        }

        // Group by mapel
        $grouped = $pesertaAll->groupBy('mapel_osn_2026');

        return view('peserta-osn', compact('pesertaAll', 'grouped'));
    }

    public function hapusPeserta(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);

        // Clear mapel_osn_2026 from siswa table
        $siswa->update([
            'mapel_osn_2026' => null,
            'ikut_osn_2025' => null,
        ]);

        // Delete all peserta_ajang_talenta entries for this siswa
        DB::table('peserta_ajang_talenta')
            ->where('siswa_id', $siswa->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Pendaftaran OSN untuk {$siswa->nama} berhasil dihapus.",
        ]);
    }
}
