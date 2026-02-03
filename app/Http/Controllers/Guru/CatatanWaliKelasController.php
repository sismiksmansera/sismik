<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatatanWaliKelasController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }
        $guruNama = $guru->nama;
        $guruId = $guru->id;

        // Get parameters
        $siswaId = intval($request->get('siswa_id'));
        $rombelId = intval($request->get('rombel_id'));
        $tahunPelajaran = $request->get('tahun');
        $semester = $request->get('semester');

        if (!$siswaId || !$rombelId || !$tahunPelajaran || !$semester) {
            return back()->with('error', 'Parameter tidak lengkap!');
        }

        // Get siswa data
        $siswa = DB::table('siswa')
            ->where('id', $siswaId)
            ->first(['id', 'nis', 'nisn', 'nama', 'foto']);

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan!');
        }

        // Get rombel data
        $rombel = DB::table('rombel')
            ->where('id', $rombelId)
            ->first(['id', 'nama_rombel', 'wali_kelas']);

        if (!$rombel || $rombel->wali_kelas != $guruNama) {
            return back()->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }

        // Calculate date range based on period
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $tahunAkhir = $tahunAwal + 1;

        if (strtolower($semester) == 'ganjil') {
            $minDate = $tahunAwal . '-07-01';
            $maxDate = $tahunAwal . '-12-31';
        } else {
            $minDate = $tahunAkhir . '-01-01';
            $maxDate = $tahunAkhir . '-06-30';
        }

        // Get catatan list
        $catatanList = DB::table('catatan_wali_kelas')
            ->where('siswa_id', $siswaId)
            ->where('rombel_id', $rombelId)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semester)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.catatan-wali-kelas', compact(
            'guru', 'guruId', 'siswa', 'rombel', 'tahunPelajaran', 'semester',
            'minDate', 'maxDate', 'catatanList', 'siswaId', 'rombelId'
        ));
    }

    public function simpan(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Tidak terautentikasi']);
        }

        $siswaId = intval($request->siswa_id);
        $rombelId = intval($request->rombel_id);
        $tahunPelajaran = $request->tahun_pelajaran;
        $semester = $request->semester;
        $tanggal = $request->tanggal;
        $catatan = $request->catatan;

        if (empty($tanggal) || empty($catatan)) {
            return response()->json(['success' => false, 'message' => 'Tanggal dan catatan harus diisi']);
        }

        try {
            DB::table('catatan_wali_kelas')->insert([
                'siswa_id' => $siswaId,
                'guru_id' => $guru->id,
                'rombel_id' => $rombelId,
                'tahun_pelajaran' => $tahunPelajaran,
                'semester' => $semester,
                'tanggal' => $tanggal,
                'catatan' => $catatan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function hapus(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Tidak terautentikasi']);
        }

        $catatanId = intval($request->catatan_id);

        // Verify ownership
        $catatan = DB::table('catatan_wali_kelas')
            ->where('id', $catatanId)
            ->where('guru_id', $guru->id)
            ->first();

        if (!$catatan) {
            return response()->json(['success' => false, 'message' => 'Catatan tidak ditemukan']);
        }

        try {
            DB::table('catatan_wali_kelas')->where('id', $catatanId)->delete();
            return response()->json(['success' => true, 'message' => 'Catatan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus']);
        }
    }
}
