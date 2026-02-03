<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    /**
     * Display list of pengaduan forwarded to this Guru BK
     */
    public function index()
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $nama_guru = $guruBK->nama;

        // Get active period
        $periode = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();
        
        $tahun_pelajaran = $periode->tahun_pelajaran ?? '';
        $semester = $periode->semester ?? '';

        // Get pengaduan forwarded to this guru
        $pengaduan_list = DB::table('pengaduan as p')
            ->select('p.*', DB::raw("(SELECT COUNT(*) FROM notifikasi n WHERE n.referensi_id = p.id AND n.penerima_nama LIKE '%{$nama_guru}%' AND n.dibaca = 0) as is_new"))
            ->where('p.diteruskan_ke', 'LIKE', "%{$nama_guru}%")
            ->orderBy('p.created_at', 'DESC')
            ->get();

        // Calculate stats
        $stats = [
            'total' => count($pengaduan_list),
            'menunggu' => $pengaduan_list->where('status', 'Menunggu')->count(),
            'diproses' => $pengaduan_list->where('status', 'Diproses')->count(),
            'ditangani' => $pengaduan_list->where('status', 'Ditangani')->count(),
            'ditutup' => $pengaduan_list->where('status', 'Ditutup')->count(),
        ];

        return view('guru-bk.pengaduan', compact(
            'pengaduan_list',
            'stats',
            'tahun_pelajaran',
            'semester'
        ));
    }

    /**
     * Update pengaduan (status + tanggapan) via AJAX
     */
    public function update(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = intval($request->input('id', 0));
        $status = $request->input('status', '');
        $tanggapan = $request->input('tanggapan', '');

        if ($id <= 0 || empty($status)) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.']);
        }

        $updated = DB::table('pengaduan')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'tanggapan' => $tanggapan,
                'ditanggapi_oleh' => $guruBK->nama,
                'updated_at' => now()
            ]);

        if ($updated) {
            // Mark notification as read
            DB::table('notifikasi')
                ->where('referensi_id', $id)
                ->where('penerima_nama', 'LIKE', "%{$guruBK->nama}%")
                ->update(['dibaca' => 1]);

            return response()->json(['success' => true, 'message' => 'Data pengaduan berhasil diperbarui.']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }

    /**
     * Mark notification as read
     */
    public function markRead(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return response()->json(['success' => false], 401);
        }

        $id = intval($request->input('id', 0));

        DB::table('notifikasi')
            ->where('referensi_id', $id)
            ->where('penerima_nama', 'LIKE', "%{$guruBK->nama}%")
            ->update(['dibaca' => 1]);

        return response()->json(['success' => true]);
    }

    /**
     * Get status color for display
     */
    public static function getStatusColor($status)
    {
        switch ($status) {
            case 'Menunggu':
                return ['color' => '#f59e0b', 'bg' => '#fef3c7'];
            case 'Diproses':
                return ['color' => '#3b82f6', 'bg' => '#dbeafe'];
            case 'Ditangani':
                return ['color' => '#10b981', 'bg' => '#d1fae5'];
            case 'Ditutup':
                return ['color' => '#6b7280', 'bg' => '#e5e7eb'];
            default:
                return ['color' => '#6b7280', 'bg' => '#f3f4f6'];
        }
    }
}
