<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PelanggaranController extends Controller
{
    /**
     * Display all pelanggaran list
     */
    public function index(Request $request)
    {
        $guruBk = Auth::guard('guru_bk')->user();

        $pelanggaranList = Pelanggaran::with('siswa')
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru-bk.pelanggaran.index', compact('guruBk', 'pelanggaranList'));
    }

    /**
     * Store new pelanggaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'integer|exists:siswa,id',
        ]);

        try {
            $guruBk = Auth::guard('guru_bk')->user();

            $pelanggaran = Pelanggaran::create([
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'jenis_pelanggaran' => $request->jenis_pelanggaran,
                'jenis_lainnya' => $request->jenis_pelanggaran === 'Lainnya' ? $request->jenis_lainnya : null,
                'deskripsi' => $request->deskripsi,
                'sanksi' => $request->sanksi,
                'guru_bk_id' => $guruBk->id,
            ]);

            // Attach students
            $pelanggaran->siswa()->attach($request->siswa_ids);

            return redirect()->route('guru_bk.pelanggaran', ['tanggal' => $request->tanggal])
                ->with('success', 'Pelanggaran berhasil dicatat untuk ' . count($request->siswa_ids) . ' siswa.');
        } catch (\Exception $e) {
            Log::error("Error storing pelanggaran: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan pelanggaran: ' . $e->getMessage());
        }
    }

    /**
     * Delete pelanggaran
     */
    public function destroy($id)
    {
        try {
            $pelanggaran = Pelanggaran::findOrFail($id);
            $tanggal = $pelanggaran->tanggal->format('Y-m-d');
            $pelanggaran->delete(); // cascade deletes pivot

            return redirect()->route('guru_bk.pelanggaran', ['tanggal' => $tanggal])
                ->with('success', 'Data pelanggaran berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Error deleting pelanggaran: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data pelanggaran.');
        }
    }

    /**
     * Update pelanggaran
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string',
        ]);

        try {
            $pelanggaran = Pelanggaran::findOrFail($id);
            $pelanggaran->update([
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'jenis_pelanggaran' => $request->jenis_pelanggaran,
                'jenis_lainnya' => $request->jenis_pelanggaran === 'Lainnya' ? $request->jenis_lainnya : null,
                'deskripsi' => $request->deskripsi,
                'sanksi' => $request->sanksi,
            ]);

            return redirect()->route('guru_bk.pelanggaran', ['tanggal' => $request->tanggal])
                ->with('success', 'Data pelanggaran berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Error updating pelanggaran: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data pelanggaran.');
        }
    }

    /**
     * AJAX: Search students by name/nisn/nis
     */
    public function searchSiswa(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $siswa = Siswa::where(function($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%")
                  ->orWhere('nisn', 'like', "%{$query}%")
                  ->orWhere('nis', 'like', "%{$query}%");
            })
            ->select('id', 'nama', 'nisn', 'nis', 'jk', 'nama_rombel')
            ->orderBy('nama')
            ->limit(50)
            ->get();

        // Map nama_rombel to rombel_aktif for frontend consistency
        $result = $siswa->map(function($s) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nisn' => $s->nisn,
                'nis' => $s->nis,
                'jk' => $s->jk,
                'rombel_aktif' => $s->nama_rombel ?: '-',
            ];
        });

        return response()->json($result);
    }
}
