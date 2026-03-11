<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureLock;
use Illuminate\Http\Request;

class FeatureLockController extends Controller
{
    private function getDefaultFeatures()
    {
        return [
            // Guru features
            ['role' => 'guru', 'feature_key' => 'guru.dashboard', 'feature_name' => 'Dashboard'],
            ['role' => 'guru', 'feature_key' => 'guru.presensi', 'feature_name' => 'Presensi Siswa'],
            ['role' => 'guru', 'feature_key' => 'guru.input-penilaian', 'feature_name' => 'Input Penilaian'],
            ['role' => 'guru', 'feature_key' => 'guru.lihat-nilai', 'feature_name' => 'Lihat Nilai'],
            ['role' => 'guru', 'feature_key' => 'guru.anggota-rombel', 'feature_name' => 'Anggota Rombel'],
            ['role' => 'guru', 'feature_key' => 'guru.jadwal', 'feature_name' => 'Jadwal Pelajaran'],
            ['role' => 'guru', 'feature_key' => 'guru.tugas-mengajar', 'feature_name' => 'Tugas Mengajar'],
            ['role' => 'guru', 'feature_key' => 'guru.tugas-tambahan', 'feature_name' => 'Tugas Tambahan'],
            ['role' => 'guru', 'feature_key' => 'guru.cetak-raport', 'feature_name' => 'Cetak Raport'],
            ['role' => 'guru', 'feature_key' => 'guru.prestasi', 'feature_name' => 'Prestasi'],
            ['role' => 'guru', 'feature_key' => 'guru.pengaduan', 'feature_name' => 'Pengaduan Siswa'],
            ['role' => 'guru', 'feature_key' => 'guru.profil', 'feature_name' => 'Profil Saya'],
            // Siswa features
            ['role' => 'siswa', 'feature_key' => 'siswa.dashboard', 'feature_name' => 'Dashboard'],
            ['role' => 'siswa', 'feature_key' => 'siswa.nilai', 'feature_name' => 'Lihat Nilai'],
            ['role' => 'siswa', 'feature_key' => 'siswa.presensi', 'feature_name' => 'Lihat Presensi'],
            ['role' => 'siswa', 'feature_key' => 'siswa.jadwal', 'feature_name' => 'Jadwal Pelajaran'],
            ['role' => 'siswa', 'feature_key' => 'siswa.mapel', 'feature_name' => 'Mata Pelajaran'],
            ['role' => 'siswa', 'feature_key' => 'siswa.ekstrakurikuler', 'feature_name' => 'Ekstrakurikuler'],
            ['role' => 'siswa', 'feature_key' => 'siswa.prestasi', 'feature_name' => 'Prestasi'],
            ['role' => 'siswa', 'feature_key' => 'siswa.catatan-bk', 'feature_name' => 'Catatan BK'],
            ['role' => 'siswa', 'feature_key' => 'siswa.catatan-guru-wali', 'feature_name' => 'Catatan Guru Wali'],
            ['role' => 'siswa', 'feature_key' => 'siswa.pelanggaran', 'feature_name' => 'Pelanggaran'],
            ['role' => 'siswa', 'feature_key' => 'siswa.pengaduan', 'feature_name' => 'Pengaduan'],
            ['role' => 'siswa', 'feature_key' => 'siswa.riwayat-akademik', 'feature_name' => 'Riwayat Akademik'],
            ['role' => 'siswa', 'feature_key' => 'siswa.profil', 'feature_name' => 'Profil Saya'],
        ];
    }

    public function index()
    {
        // Seed defaults if empty
        if (FeatureLock::count() === 0) {
            foreach ($this->getDefaultFeatures() as $f) {
                FeatureLock::create($f);
            }
        }

        $guruFeatures = FeatureLock::where('role', 'guru')->orderBy('id')->get();
        $siswaFeatures = FeatureLock::where('role', 'siswa')->orderBy('id')->get();

        return view('admin.feature-lock.index', compact('guruFeatures', 'siswaFeatures'));
    }

    public function toggle(Request $request)
    {
        $feature = FeatureLock::findOrFail($request->id);
        $feature->is_locked = !$feature->is_locked;
        $feature->save();

        return response()->json([
            'success' => true,
            'is_locked' => $feature->is_locked,
            'message' => $feature->is_locked
                ? "Fitur '{$feature->feature_name}' berhasil dikunci."
                : "Fitur '{$feature->feature_name}' berhasil dibuka.",
        ]);
    }
}
