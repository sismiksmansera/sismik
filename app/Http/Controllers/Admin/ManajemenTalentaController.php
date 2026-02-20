<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\DataPeriodik;
use App\Models\AjangTalenta;
use App\Models\JenisAjangTalenta;
use App\Models\Guru;
use App\Models\GuruBK;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
        $admin = Auth::guard('admin')->user();

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

        // Get ajang talenta list
        $ajangList = AjangTalenta::orderBy('created_at', 'DESC')->get();

        // Get jenis ajang talenta list
        $jenisAjangList = JenisAjangTalenta::orderBy('nama_jenis', 'ASC')->get();

        // Get guru + guru BK lists for pembina dropdown
        $guruList = Guru::orderBy('nama', 'ASC')->pluck('nama')->toArray();
        $guruBkList = GuruBK::orderBy('nama', 'ASC')->pluck('nama')->toArray();

        return view('admin.manajemen-talenta.index', compact(
            'admin',
            'siswaList',
            'mapelStats',
            'mapelList',
            'angkatanList',
            'filterMapel',
            'filterAngkatan',
            'tahunAktif',
            'semesterAktif',
            'ajangList',
            'jenisAjangList',
            'guruList',
            'guruBkList'
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

    /**
     * Store a new Ajang Talenta (AJAX)
     */
    public function storeAjang(Request $request)
    {
        $request->validate([
            'jenis_ajang' => 'required|string|max:200',
            'nama_ajang' => 'required|string|max:200',
            'tahun' => 'nullable|string|max:10',
            'penyelenggara' => 'nullable|string|max:200',
            'pembina' => 'nullable|string|max:200',
        ], [
            'jenis_ajang.required' => 'Jenis ajang talenta wajib dipilih.',
            'nama_ajang.required' => 'Nama ajang talenta wajib diisi.',
        ]);

        $ajang = AjangTalenta::create([
            'jenis_ajang' => $request->jenis_ajang,
            'nama_ajang' => $request->nama_ajang,
            'tahun' => $request->tahun,
            'penyelenggara' => $request->penyelenggara,
            'pembina' => $request->pembina,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ajang Talenta berhasil ditambahkan!',
            'ajang' => $ajang,
        ]);
    }

    /**
     * Delete an Ajang Talenta (AJAX)
     */
    public function deleteAjang(Request $request)
    {
        $request->validate([
            'ajang_id' => 'required|exists:ajang_talenta,id',
        ]);

        $ajang = AjangTalenta::findOrFail($request->ajang_id);
        $nama = $ajang->nama_ajang;
        $ajang->delete();

        return response()->json([
            'success' => true,
            'message' => "Ajang Talenta '{$nama}' berhasil dihapus.",
        ]);
    }

    /**
     * Store a new Jenis Ajang Talenta (AJAX)
     */
    public function storeJenisAjang(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:200|unique:jenis_ajang_talenta,nama_jenis',
        ], [
            'nama_jenis.required' => 'Nama jenis ajang talenta wajib diisi.',
            'nama_jenis.unique' => 'Jenis ajang talenta sudah ada.',
        ]);

        $jenis = JenisAjangTalenta::create([
            'nama_jenis' => $request->nama_jenis,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jenis Ajang Talenta berhasil ditambahkan!',
            'jenis' => $jenis,
        ]);
    }

    /**
     * Delete a Jenis Ajang Talenta (AJAX)
     */
    public function deleteJenisAjang(Request $request)
    {
        $request->validate([
            'jenis_id' => 'required|exists:jenis_ajang_talenta,id',
        ]);

        $jenis = JenisAjangTalenta::findOrFail($request->jenis_id);
        $nama = $jenis->nama_jenis;
        $jenis->delete();

        return response()->json([
            'success' => true,
            'message' => "Jenis Ajang Talenta '{$nama}' berhasil dihapus.",
        ]);
    }
}
