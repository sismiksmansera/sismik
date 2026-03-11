<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CatatanGuruWali;
use App\Models\DataPeriodik;

class CatatanGuruWaliController extends Controller
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

        // Get catatan guru wali for this student
        $query = CatatanGuruWali::where('siswa_id', $siswa->id);

        if ($tahunFilter) {
            $query->where('tahun_pelajaran', $tahunFilter);
        }
        if ($semesterFilter) {
            $query->where('semester', $semesterFilter);
        }

        $catatanList = $query->orderBy('tanggal_pencatatan', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCatatan = $catatanList->count();

        // Stats by jenis bimbingan
        $jenisStats = [];
        foreach ($catatanList as $c) {
            $jenis = $c->jenis_bimbingan ?? 'Lainnya';
            if (!isset($jenisStats[$jenis])) {
                $jenisStats[$jenis] = 0;
            }
            $jenisStats[$jenis]++;
        }

        // Get perkembangan stats
        $perkembanganStats = [
            'Belum Dinilai' => 0,
            'Belum Berkembang' => 0,
            'Berkembang Sesuai Harapan' => 0,
            'Berkembang Sangat Baik' => 0,
        ];
        foreach ($catatanList as $c) {
            $p = $c->perkembangan;
            if (empty($p)) {
                $perkembanganStats['Belum Dinilai']++;
            } elseif (isset($perkembanganStats[$p])) {
                $perkembanganStats[$p]++;
            }
        }

        // Get available tahun pelajaran
        $tahunList = CatatanGuruWali::where('siswa_id', $siswa->id)
            ->distinct()
            ->pluck('tahun_pelajaran')
            ->sort()
            ->reverse()
            ->values();

        return view('siswa.catatan-guru-wali', compact(
            'siswa',
            'periodik',
            'catatanList',
            'totalCatatan',
            'jenisStats',
            'perkembanganStats',
            'tahunFilter',
            'semesterFilter',
            'tahunList',
            'tahunAktif',
            'semesterAktif'
        ));
    }
}
