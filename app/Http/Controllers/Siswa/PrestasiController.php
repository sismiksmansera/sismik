<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use Carbon\Carbon;

class PrestasiController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        // Get prestasi siswa
        $prestasiList = DB::table('prestasi_siswa as ps')
            ->leftJoin('ekstrakurikuler as e', function($join) {
                $join->on('ps.sumber_prestasi', '=', DB::raw("'ekstrakurikuler'"))
                     ->on('ps.sumber_id', '=', 'e.id');
            })
            ->leftJoin('rombel as r', function($join) {
                $join->on('ps.sumber_prestasi', '=', DB::raw("'rombel'"))
                     ->on('ps.sumber_id', '=', 'r.id');
            })
            ->select(
                'ps.*',
                DB::raw("CASE 
                    WHEN ps.sumber_prestasi = 'ekstrakurikuler' THEN e.nama_ekstrakurikuler
                    WHEN ps.sumber_prestasi = 'rombel' THEN r.nama_rombel
                    ELSE 'Lainnya'
                END as sumber_nama")
            )
            ->where('ps.siswa_id', $siswa->id)
            ->where('ps.tahun_pelajaran', $tahunAktif)
            ->where('ps.semester', $semesterAktif)
            ->orderBy('ps.tanggal_pelaksanaan', 'desc')
            ->get()
            ->map(function($item) use ($siswa, $periodik) {
                $item->jenjang_colors = $this->getJenjangColor($item->jenjang);
                $item->juara_info = $this->getJuaraIcon($item->juara);
                
                // Get team members if Tim
                if (($item->tipe_peserta ?? 'Single') === 'Tim') {
                    $item->team_members = $this->getTeamMembers($item, $periodik);
                }
                
                return $item;
            });
        
        // Group by year
        $prestasiByYear = $prestasiList->groupBy(function($item) {
            return Carbon::parse($item->tanggal_pelaksanaan)->format('Y');
        })->sortKeysDesc();
        
        $totalPrestasi = $prestasiList->count();
        
        return view('siswa.prestasi', compact(
            'siswa',
            'periodik',
            'prestasiByYear',
            'totalPrestasi',
            'tahunAktif',
            'semesterAktif'
        ));
    }
    
    private function getTeamMembers($prestasi, $periodik)
    {
        return DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select('s.nama', 's.nis', 's.angkatan_masuk',
                's.rombel_semester_1', 's.rombel_semester_2',
                's.rombel_semester_3', 's.rombel_semester_4',
                's.rombel_semester_5', 's.rombel_semester_6')
            ->where('ps.nama_kompetisi', $prestasi->nama_kompetisi)
            ->where('ps.juara', $prestasi->juara)
            ->where('ps.jenjang', $prestasi->jenjang)
            ->where('ps.tanggal_pelaksanaan', $prestasi->tanggal_pelaksanaan)
            ->where('ps.sumber_prestasi', $prestasi->sumber_prestasi)
            ->where('ps.sumber_id', $prestasi->sumber_id)
            ->orderBy('s.nama')
            ->get()
            ->map(function($member) use ($periodik) {
                // Determine rombel aktif
                $tahunAjaran = explode('/', $periodik->tahun_pelajaran ?? '2025/2026');
                $tahunAwal = intval($tahunAjaran[0] ?? 2025);
                $angkatan = intval($member->angkatan_masuk ?? 2023);
                $semesterAktif = $periodik->semester ?? 'Ganjil';
                
                $tahunSelisih = $tahunAwal - $angkatan;
                if ($semesterAktif === 'Ganjil') {
                    $semesterKe = ($tahunSelisih * 2) + 1;
                } else {
                    $semesterKe = ($tahunSelisih * 2) + 2;
                }
                
                $rombelField = "rombel_semester_{$semesterKe}";
                $member->rombel_aktif = $member->$rombelField ?? '-';
                
                return $member;
            });
    }
    
    private function getJenjangColor($jenjang)
    {
        $colors = [
            'Kelas' => ['bg' => '#f3f4f6', 'text' => '#6b7280', 'gradient' => '#6b7280'],
            'Sekolah' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'gradient' => '#3b82f6'],
            'Kecamatan' => ['bg' => '#dcfce7', 'text' => '#166534', 'gradient' => '#10b981'],
            'Kabupaten' => ['bg' => '#ede9fe', 'text' => '#5b21b6', 'gradient' => '#8b5cf6'],
            'Provinsi' => ['bg' => '#fef3c7', 'text' => '#92400e', 'gradient' => '#f59e0b'],
            'Nasional' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'gradient' => '#ef4444'],
            'Internasional' => ['bg' => '#fce7f3', 'text' => '#9d174d', 'gradient' => '#ec4899'],
        ];
        return $colors[$jenjang] ?? ['bg' => '#e0f2fe', 'text' => '#0369a1', 'gradient' => '#0ea5e9'];
    }
    
    private function getJuaraIcon($juara)
    {
        $juaraLower = strtolower($juara);
        if (strpos($juaraLower, '1') !== false) return ['icon' => 'fa-trophy', 'color' => '#f59e0b'];
        if (strpos($juaraLower, '2') !== false) return ['icon' => 'fa-medal', 'color' => '#94a3b8'];
        if (strpos($juaraLower, '3') !== false) return ['icon' => 'fa-award', 'color' => '#d97706'];
        return ['icon' => 'fa-star', 'color' => '#3b82f6'];
    }
}
