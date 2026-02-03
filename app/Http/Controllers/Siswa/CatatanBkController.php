<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use Carbon\Carbon;

class CatatanBkController extends Controller
{
    public function index(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        // Filter parameter
        $tahunFilter = $request->get('tahun', $tahunAktif);
        $semesterFilter = $request->get('semester', $semesterAktif);
        
        // Get catatan bimbingan
        $query = DB::table('catatan_bimbingan')
            ->leftJoin('guru_bk', 'catatan_bimbingan.guru_bk_id', '=', 'guru_bk.id')
            ->select('catatan_bimbingan.*', 'guru_bk.nama as nama_guru')
            ->where('catatan_bimbingan.nisn', $siswa->nisn);
        
        if ($tahunFilter) {
            $query->where('catatan_bimbingan.tahun_pelajaran', $tahunFilter);
        }
        if ($semesterFilter) {
            $query->where('catatan_bimbingan.semester', $semesterFilter);
        }
        
        $catatanList = $query->orderBy('catatan_bimbingan.tanggal', 'desc')
            ->orderBy('catatan_bimbingan.created_at', 'desc')
            ->get();
        
        // Calculate stats
        $totalCatatan = $catatanList->count();
        $statusStats = [
            'Belum Ditangani' => 0,
            'Dalam Proses' => 0,
            'Selesai' => 0
        ];
        
        foreach ($catatanList as $catatan) {
            $status = $this->normalizeStatus($catatan->status ?? 'Belum Ditangani');
            if (isset($statusStats[$status])) {
                $statusStats[$status]++;
            }
        }
        
        // Get available tahun pelajaran
        $tahunList = DB::table('catatan_bimbingan')
            ->where('nisn', $siswa->nisn)
            ->distinct()
            ->pluck('tahun_pelajaran')
            ->sort()
            ->reverse()
            ->values();
        
        return view('siswa.catatan-bk', compact(
            'siswa',
            'periodik',
            'catatanList',
            'totalCatatan',
            'statusStats',
            'tahunFilter',
            'semesterFilter',
            'tahunList',
            'tahunAktif',
            'semesterAktif'
        ));
    }
    
    private function normalizeStatus($status)
    {
        $statusLower = strtolower(trim($status));
        
        if ($statusLower === 'proses' || $statusLower === 'dalam proses') {
            return 'Dalam Proses';
        } elseif ($statusLower === 'belum' || $statusLower === 'belum ditangani') {
            return 'Belum Ditangani';
        } elseif ($statusLower === 'selesai') {
            return 'Selesai';
        }
        
        return 'Belum Ditangani';
    }
}
