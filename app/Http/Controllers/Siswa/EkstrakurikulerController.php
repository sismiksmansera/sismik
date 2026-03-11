<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class EkstrakurikulerController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        // Get ekstrakurikuler yang diikuti siswa
        $ekskulList = DB::table('anggota_ekstrakurikuler as ae')
            ->join('ekstrakurikuler as e', 'ae.ekstrakurikuler_id', '=', 'e.id')
            ->select(
                'ae.id as anggota_id',
                'ae.nilai',
                'e.id as ekstra_id',
                'e.nama_ekstrakurikuler',
                'e.deskripsi',
                'e.pembina_1',
                'e.pembina_2',
                'e.pembina_3',
                'e.tahun_pelajaran',
                'e.semester'
            )
            ->where('ae.siswa_id', $siswa->id)
            ->where('ae.tahun_pelajaran', $tahunAktif)
            ->where('ae.semester', $semesterAktif)
            ->orderBy('e.nama_ekstrakurikuler')
            ->get()
            ->map(function($item) {
                $item->pembina_list = array_filter([
                    $item->pembina_1,
                    $item->pembina_2,
                    $item->pembina_3
                ]);
                $item->color = $this->getColorForEkstra($item->nama_ekstrakurikuler);
                $item->icon = $this->getIconForEkstra($item->nama_ekstrakurikuler);
                $item->nilai_info = $this->getNilaiInfo($item->nilai);
                return $item;
            });
        
        return view('siswa.ekstrakurikuler', compact(
            'siswa',
            'periodik',
            'ekskulList',
            'tahunAktif',
            'semesterAktif'
        ));
    }
    
    private function getColorForEkstra($nama)
    {
        $colors = [
            'Pramuka' => '#3b82f6',
            'Paskibra' => '#ef4444',
            'PMR' => '#dc2626',
            'OSIS' => '#8b5cf6',
            'Basket' => '#f59e0b',
            'Futsal' => '#10b981',
            'Voli' => '#ec4899',
            'Seni Musik' => '#06b6d4',
            'Seni Tari' => '#f97316',
            'English Club' => '#6366f1',
            'Japanese Club' => '#8b5cf6',
            'IT Club' => '#0ea5e9',
            'KIR' => '#84cc16',
            'Paduan Suara' => '#d946ef'
        ];
        return $colors[$nama] ?? '#6b7280';
    }
    
    private function getIconForEkstra($nama)
    {
        $icons = [
            'Pramuka' => 'fa-campground',
            'Paskibra' => 'fa-flag',
            'PMR' => 'fa-heartbeat',
            'OSIS' => 'fa-users-cog',
            'Basket' => 'fa-basketball-ball',
            'Futsal' => 'fa-futbol',
            'Voli' => 'fa-volleyball-ball',
            'Seni Musik' => 'fa-music',
            'Seni Tari' => 'fa-gem',
            'English Club' => 'fa-language',
            'Japanese Club' => 'fa-language',
            'IT Club' => 'fa-laptop-code',
            'KIR' => 'fa-flask',
            'Paduan Suara' => 'fa-microphone-alt'
        ];
        return $icons[$nama] ?? 'fa-star';
    }
    
    private function getNilaiInfo($nilai)
    {
        $info = [
            'A' => ['bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Sangat Baik'],
            'B' => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'Baik'],
            'C' => ['bg' => '#fef9c3', 'text' => '#a16207', 'label' => 'Cukup'],
            'D' => ['bg' => '#fee2e2', 'text' => '#dc2626', 'label' => 'Kurang'],
        ];
        return $info[$nilai] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280', 'label' => 'Belum Dinilai'];
    }
}
