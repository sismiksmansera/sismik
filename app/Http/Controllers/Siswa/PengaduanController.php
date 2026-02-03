<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\DataPeriodik;
use Carbon\Carbon;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        // Get riwayat pengaduan
        $pengaduanList = DB::table('pengaduan')
            ->where('nisn', $siswa->nisn)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate stats
        $totalPengaduan = $pengaduanList->count();
        $statusStats = [
            'Menunggu' => $pengaduanList->where('status', 'Menunggu')->count(),
            'Diproses' => $pengaduanList->where('status', 'Diproses')->count(),
            'Ditangani' => $pengaduanList->where('status', 'Ditangani')->count(),
            'Ditutup' => $pengaduanList->where('status', 'Ditutup')->count(),
        ];
        
        return view('siswa.pengaduan.index', compact(
            'siswa',
            'periodik',
            'pengaduanList',
            'totalPengaduan',
            'statusStats',
            'tahunAktif',
            'semesterAktif'
        ));
    }
    
    public function create()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        return view('siswa.pengaduan.create', compact(
            'siswa',
            'periodik',
            'tahunAktif',
            'semesterAktif'
        ));
    }
    
    public function store(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $request->validate([
            'kategori' => 'required|in:Sarana Prasarana,Kekerasan,Bullying,Pelanggaran Aturan,Kegiatan Pembelajaran,Pelayanan Sekolah,Lainnya',
            'subyek_terlapor' => 'required|string|max:255',
            'tanggal_kejadian' => 'required|date|before_or_equal:today',
            'deskripsi' => 'required|string',
            'bukti_pendukung' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,pdf'
        ]);
        
        $buktiPendukung = null;
        if ($request->hasFile('bukti_pendukung')) {
            $file = $request->file('bukti_pendukung');
            $filename = 'bukti_' . $siswa->nisn . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/pengaduan', $filename);
            $buktiPendukung = $filename;
        }
        
        DB::table('pengaduan')->insert([
            'nisn' => $siswa->nisn,
            'nama_pelapor' => $siswa->nama,
            'rombel_pelapor' => $siswa->nama_rombel,
            'kategori' => $request->kategori,
            'subyek_terlapor' => $request->subyek_terlapor,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'waktu_kejadian' => $request->waktu_kejadian,
            'lokasi_kejadian' => $request->lokasi_kejadian,
            'deskripsi' => $request->deskripsi,
            'bukti_pendukung' => $buktiPendukung,
            'status' => 'Menunggu',
            'tahun_pelajaran' => $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1),
            'semester' => $periodik->semester ?? 'Ganjil',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return redirect()->route('siswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dikirim. Laporan Anda akan segera ditindaklanjuti oleh pihak sekolah.');
    }
    
    public function destroy($id)
    {
        $siswa = Auth::guard('siswa')->user();
        
        $pengaduan = DB::table('pengaduan')
            ->where('id', $id)
            ->where('nisn', $siswa->nisn)
            ->first();
        
        if (!$pengaduan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan tidak ditemukan atau bukan milik Anda.'
            ]);
        }
        
        if ($pengaduan->status !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan yang sudah diproses tidak dapat dihapus.'
            ]);
        }
        
        // Delete file if exists
        if ($pengaduan->bukti_pendukung) {
            Storage::delete('public/pengaduan/' . $pengaduan->bukti_pendukung);
        }
        
        DB::table('pengaduan')->where('id', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dihapus.'
        ]);
    }
}
