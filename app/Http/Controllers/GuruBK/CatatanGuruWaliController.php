<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CatatanGuruWali;
use App\Models\Siswa;

class CatatanGuruWaliController extends Controller
{
    /**
     * Display list of catatan for a student
     */
    public function index($siswa_id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $siswa = Siswa::findOrFail($siswa_id);

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

        // Get rombel for this student
        $rombel = $this->getRombelSiswa($siswa, $periodeAktif);

        // Get catatan list
        $catatanList = CatatanGuruWali::where('siswa_id', $siswa_id)
            ->where('guru_nama', $guruBK->nama)
            ->orderBy('tanggal_pencatatan', 'desc')
            ->get();

        return view('guru-bk.catatan-guru-wali.index', compact(
            'siswa',
            'catatanList',
            'rombel',
            'periodeAktif'
        ));
    }

    /**
     * Show create form
     */
    public function create($siswa_id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $siswa = Siswa::findOrFail($siswa_id);

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

        $rombel = $this->getRombelSiswa($siswa, $periodeAktif);

        return view('guru-bk.catatan-guru-wali.form', [
            'siswa' => $siswa,
            'rombel' => $rombel,
            'periodeAktif' => $periodeAktif,
            'catatan' => null,
            'jenisBimbinganOptions' => CatatanGuruWali::jenisBimbinganOptions(),
            'nilaiPraktikIbadahOptions' => CatatanGuruWali::nilaiPraktikIbadahOptions(),
            'perkembanganOptions' => CatatanGuruWali::perkembanganOptions(),
        ]);
    }

    /**
     * Store new catatan
     */
    public function store(Request $request, $siswa_id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $request->validate([
            'tanggal_pencatatan' => 'required|date',
            'jenis_bimbingan' => 'required|in:' . implode(',', CatatanGuruWali::jenisBimbinganOptions()),
            'catatan' => 'required|string',
            'nilai_praktik_ibadah' => 'nullable|in:A,B,C',
            'perkembangan' => 'nullable|in:' . implode(',', CatatanGuruWali::perkembanganOptions()),
        ]);

        // Get active period
        $periodeAktif = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();

        CatatanGuruWali::create([
            'siswa_id' => $siswa_id,
            'guru_nama' => $guruBK->nama,
            'tahun_pelajaran' => $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1),
            'semester' => $periodeAktif->semester ?? 'Ganjil',
            'tanggal_pencatatan' => $request->tanggal_pencatatan,
            'jenis_bimbingan' => $request->jenis_bimbingan,
            'catatan' => $request->catatan,
            'nilai_praktik_ibadah' => $request->jenis_bimbingan === 'Bimbingan Ibadah' ? $request->nilai_praktik_ibadah : null,
            'perkembangan' => $request->perkembangan,
        ]);

        return redirect()
            ->route('guru_bk.catatan-guru-wali.index', $siswa_id)
            ->with('success', 'Catatan berhasil disimpan.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $catatan = CatatanGuruWali::where('id', $id)
            ->where('guru_nama', $guruBK->nama)
            ->firstOrFail();

        $siswa = Siswa::findOrFail($catatan->siswa_id);

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

        $rombel = $this->getRombelSiswa($siswa, $periodeAktif);

        return view('guru-bk.catatan-guru-wali.form', [
            'siswa' => $siswa,
            'rombel' => $rombel,
            'periodeAktif' => $periodeAktif,
            'catatan' => $catatan,
            'jenisBimbinganOptions' => CatatanGuruWali::jenisBimbinganOptions(),
            'nilaiPraktikIbadahOptions' => CatatanGuruWali::nilaiPraktikIbadahOptions(),
            'perkembanganOptions' => CatatanGuruWali::perkembanganOptions(),
        ]);
    }

    /**
     * Update catatan
     */
    public function update(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $catatan = CatatanGuruWali::where('id', $id)
            ->where('guru_nama', $guruBK->nama)
            ->firstOrFail();

        $request->validate([
            'tanggal_pencatatan' => 'required|date',
            'jenis_bimbingan' => 'required|in:' . implode(',', CatatanGuruWali::jenisBimbinganOptions()),
            'catatan' => 'required|string',
            'nilai_praktik_ibadah' => 'nullable|in:A,B,C',
            'perkembangan' => 'nullable|in:' . implode(',', CatatanGuruWali::perkembanganOptions()),
        ]);

        $catatan->update([
            'tanggal_pencatatan' => $request->tanggal_pencatatan,
            'jenis_bimbingan' => $request->jenis_bimbingan,
            'catatan' => $request->catatan,
            'nilai_praktik_ibadah' => $request->jenis_bimbingan === 'Bimbingan Ibadah' ? $request->nilai_praktik_ibadah : null,
            'perkembangan' => $request->perkembangan,
        ]);

        return redirect()
            ->route('guru_bk.catatan-guru-wali.index', $catatan->siswa_id)
            ->with('success', 'Catatan berhasil diperbarui.');
    }

    /**
     * Delete catatan
     */
    public function destroy($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $catatan = CatatanGuruWali::where('id', $id)
            ->where('guru_nama', $guruBK->nama)
            ->firstOrFail();

        $siswa_id = $catatan->siswa_id;
        $catatan->delete();

        return redirect()
            ->route('guru_bk.catatan-guru-wali.index', $siswa_id)
            ->with('success', 'Catatan berhasil dihapus.');
    }

    /**
     * Get rombel for student based on active period
     */
    private function getRombelSiswa($siswa, $periodeAktif)
    {
        $tahunAjaran = explode('/', $periodeAktif->tahun_pelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $angkatan = $siswa->angkatan_masuk;

        $tingkat = $tahunAwal - $angkatan + 1;

        if (strtolower($periodeAktif->semester) == 'ganjil') {
            $semesterCol = ($tingkat * 2) - 1;
        } else {
            $semesterCol = $tingkat * 2;
        }

        $rombelCol = 'rombel_semester_' . $semesterCol;
        
        return $siswa->$rombelCol ?? '-';
    }
}
