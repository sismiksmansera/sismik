<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PiketKbm;
use App\Models\Guru;
use App\Models\GuruBK;
use App\Models\LoginSettings;

class PiketKbmController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $loginSettings = LoginSettings::first();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Get all piket data grouped by day
        $piketData = [];
        foreach ($hariList as $hari) {
            $piketData[$hari] = PiketKbm::where('hari', $hari)
                ->orderBy('created_at')
                ->get();
        }

        // Get guru and guru_bk list for dropdown
        $guruList = Guru::where('status', 'Aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip']);

        $guruBkList = GuruBK::orderBy('nama')
            ->get(['id', 'nama', 'nip']);

        return view('admin.piket-kbm', compact(
            'admin',
            'loginSettings',
            'hariList',
            'piketData',
            'guruList',
            'guruBkList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'guru_id' => 'required|integer',
            'tipe_guru' => 'required|in:guru,guru_bk',
        ]);

        $tipe = $request->tipe_guru;
        $guruId = $request->guru_id;

        if ($tipe === 'guru') {
            $guru = Guru::findOrFail($guruId);
            $nama = $guru->nama;
            $nip = $guru->nip;
        } else {
            $guru = GuruBK::findOrFail($guruId);
            $nama = $guru->nama;
            $nip = $guru->nip;
        }

        // Check duplicate
        $exists = PiketKbm::where('hari', $request->hari)
            ->where('guru_id', $guruId)
            ->where('tipe_guru', $tipe)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Guru ini sudah ditugaskan piket pada hari ' . $request->hari
            ]);
        }

        PiketKbm::create([
            'hari' => $request->hari,
            'nama_guru' => $nama,
            'nip' => $nip,
            'tipe_guru' => $tipe,
            'guru_id' => $guruId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guru piket berhasil ditambahkan'
        ]);
    }

    public function destroy($id)
    {
        $piket = PiketKbm::findOrFail($id);
        $piket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guru piket berhasil dihapus'
        ]);
    }
}
