<?php

namespace App\Http\Controllers;

use App\Models\Tamu;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    /**
     * Show guest registration form
     */
    public function create()
    {
        $kategoriOptions = Tamu::getKategoriOptions();
        $dokumenOptions = Tamu::getJenisDokumenOptions();
        
        // Get current day name in Indonesian
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hari = $days[date('l')] ?? date('l');
        $tanggal = date('d F Y');
        
        return view('tamu.form', compact('kategoriOptions', 'dokumenOptions', 'hari', 'tanggal'));
    }
    
    /**
     * Store guest data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'datang_sebagai' => 'required|in:Wali Murid,Jurnalis,Perguruan Tinggi,Tamu Khusus,Tamu Umum',
            'bertemu_dengan' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'memberikan_dokumen' => 'nullable|boolean',
            'jenis_dokumen_diberikan' => 'nullable|required_if:memberikan_dokumen,1|in:Surat Undangan,Proposal,Barang/Berkas Lain',
            'deskripsi_dokumen_diberikan' => 'nullable|required_if:memberikan_dokumen,1|string',
            'meminta_dokumen' => 'nullable|boolean',
            'jenis_dokumen_diminta' => 'nullable|required_if:meminta_dokumen,1|in:Surat Undangan,Proposal,Barang/Berkas Lain',
            'deskripsi_dokumen_diminta' => 'nullable|required_if:meminta_dokumen,1|string',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'datang_sebagai.required' => 'Pilih kategori tamu',
            'bertemu_dengan.required' => 'Kolom bertemu dengan wajib diisi',
            'keperluan.required' => 'Keperluan wajib diisi',
            'jenis_dokumen_diberikan.required_if' => 'Pilih jenis dokumen yang diberikan',
            'deskripsi_dokumen_diberikan.required_if' => 'Deskripsi dokumen yang diberikan wajib diisi',
            'jenis_dokumen_diminta.required_if' => 'Pilih jenis dokumen yang diminta',
            'deskripsi_dokumen_diminta.required_if' => 'Deskripsi dokumen yang diminta wajib diisi',
        ]);
        
        // Convert checkbox values
        $validated['memberikan_dokumen'] = $request->has('memberikan_dokumen');
        $validated['meminta_dokumen'] = $request->has('meminta_dokumen');
        
        // Clear document fields if not applicable
        if (!$validated['memberikan_dokumen']) {
            $validated['jenis_dokumen_diberikan'] = null;
            $validated['deskripsi_dokumen_diberikan'] = null;
        }
        if (!$validated['meminta_dokumen']) {
            $validated['jenis_dokumen_diminta'] = null;
            $validated['deskripsi_dokumen_diminta'] = null;
        }
        
        $tamu = Tamu::create($validated);
        
        return redirect()->route('tamu.print', $tamu->id)
            ->with('success', 'Data tamu berhasil disimpan!');
    }
    
    /**
     * Print receipt
     */
    public function print($id)
    {
        $tamu = Tamu::findOrFail($id);
        return view('tamu.print', compact('tamu'));
    }
}
