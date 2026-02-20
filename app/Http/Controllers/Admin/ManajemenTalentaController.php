<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\DataPeriodik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManajemenTalentaController extends Controller
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

    /**
     * Display the Manajemen Talenta page
     */
    public function index(Request $request)
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();

        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';

        // Filter
        $filterMapel = $request->get('mapel', '');
        $filterAngkatan = $request->get('angkatan', '');

        // Get OSN registered students
        $query = Siswa::whereNotNull('mapel_osn_2026')
            ->where('mapel_osn_2026', '!=', '')
            ->where('status_siswa', 'Aktif');

        if (!empty($filterMapel)) {
            $query->where('mapel_osn_2026', $filterMapel);
        }

        if (!empty($filterAngkatan)) {
            $query->where('angkatan_masuk', $filterAngkatan);
        }

        $siswaList = $query->orderBy('mapel_osn_2026', 'ASC')
            ->orderBy('nama', 'ASC')
            ->get();

        // Add rombel_aktif to each student
        foreach ($siswaList as $siswa) {
            $semesterNumber = $this->calculateActiveSemester(
                $siswa->angkatan_masuk,
                $tahunAktif,
                $semesterAktif
            );
            $rombelField = "rombel_semester_{$semesterNumber}";
            $siswa->rombel_aktif = $siswa->$rombelField ?? $siswa->nama_rombel ?? '-';

            // Photo URL
            $siswa->foto_url = $siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)
                ? asset('storage/siswa/' . $siswa->foto)
                : null;
        }

        // Group by mapel for statistics
        $mapelStats = $siswaList->groupBy('mapel_osn_2026')->map(function ($group) {
            return $group->count();
        })->sortKeys();

        // Get available mapel list for filter
        $mapelList = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Geografi', 'Astronomi', 'Informatika', 'Ekonomi', 'Kebumian'];

        // Get angkatan list
        $angkatanList = Siswa::whereNotNull('mapel_osn_2026')
            ->where('mapel_osn_2026', '!=', '')
            ->distinct()
            ->orderBy('angkatan_masuk', 'DESC')
            ->pluck('angkatan_masuk')
            ->filter();

        return view('admin.manajemen-talenta.index', compact(
            'admin',
            'siswaList',
            'mapelStats',
            'mapelList',
            'angkatanList',
            'filterMapel',
            'filterAngkatan',
            'tahunAktif',
            'semesterAktif'
        ));
    }

    /**
     * Remove OSN registration for a student (AJAX)
     */
    public function removeOsn(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $mapel = $siswa->mapel_osn_2026;
        $siswa->update([
            'mapel_osn_2026' => null,
            'ikut_osn_2025' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pendaftaran OSN {$siswa->nama} ({$mapel}) berhasil dihapus.",
        ]);
    }
}
