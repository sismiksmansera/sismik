<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use App\Models\DataPeriodik;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    /**
     * Display listing of pengaduan forwarded to this guru
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama;
        
        $periodik = DataPeriodik::aktif()->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        // Get pengaduan forwarded to this guru
        $pengaduanList = Pengaduan::where('diteruskan_ke', 'like', "%{$namaGuru}%")
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if has unread notification for each pengaduan
        foreach ($pengaduanList as $item) {
            $unreadCount = 0;
            if (\Schema::hasTable('notifikasi')) {
                $unreadCount = DB::table('notifikasi')
                    ->where('referensi_id', $item->id)
                    ->where('penerima_nama', 'like', "%{$namaGuru}%")
                    ->where('dibaca', 0)
                    ->count();
            }
            $item->is_new = $unreadCount > 0;
        }

        // Calculate stats
        $statusStats = [
            'Menunggu' => 0,
            'Diproses' => 0,
            'Ditangani' => 0,
            'Ditutup' => 0
        ];
        
        foreach ($pengaduanList as $item) {
            if (isset($statusStats[$item->status])) {
                $statusStats[$item->status]++;
            }
        }
        
        $totalPengaduan = count($pengaduanList);

        return view('guru.pengaduan.index', compact(
            'guru',
            'periodik',
            'tahunAktif',
            'semesterAktif',
            'pengaduanList',
            'statusStats',
            'totalPengaduan'
        ));
    }

    /**
     * Get detail pengaduan (AJAX)
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        $pengaduan = Pengaduan::find($id);
        
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Mark notification as read
        $guru = Auth::guard('guru')->user();
        if (\Schema::hasTable('notifikasi')) {
            DB::table('notifikasi')
                ->where('referensi_id', $id)
                ->where('penerima_nama', 'like', "%{$guru->nama}%")
                ->update(['dibaca' => 1]);
        }

        return response()->json([
            'success' => true,
            'data' => $pengaduan
        ]);
    }

    /**
     * Update pengaduan status and tanggapan (AJAX)
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $tanggapan = $request->input('tanggapan', '');
        
        $pengaduan = Pengaduan::find($id);
        
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $guru = Auth::guard('guru')->user();
        
        $pengaduan->update([
            'status' => $status,
            'tanggapan' => $tanggapan,
            'ditanggapi_oleh' => $guru->nama
        ]);

        // Mark notification as read
        if (\Schema::hasTable('notifikasi')) {
            DB::table('notifikasi')
                ->where('referensi_id', $id)
                ->where('penerima_nama', 'like', "%{$guru->nama}%")
                ->update(['dibaca' => 1]);
        }

        return response()->json(['success' => true, 'message' => 'Pengaduan berhasil diperbarui']);
    }
}
