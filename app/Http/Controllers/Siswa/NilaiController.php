<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use Carbon\Carbon;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        // Get mapel parameter for detail view
        $mapelParam = $request->get('mapel');
        
        if ($mapelParam) {
            return $this->showDetailMapel($siswa, $periodik, $mapelParam);
        }
        
        // Get all subjects with grades for this student
        $mapelList = DB::table('penilaian')
            ->select(
                'mapel',
                DB::raw('COUNT(*) as total_nilai'),
                DB::raw('AVG(nilai) as rata_rata'),
                DB::raw('MAX(nilai) as tertinggi'),
                DB::raw('MIN(nilai) as terendah'),
                DB::raw('MAX(tanggal_penilaian) as terakhir_dinilai')
            )
            ->where('nisn', $siswa->nisn)
            ->groupBy('mapel')
            ->orderBy('mapel')
            ->get()
            ->map(function($item) {
                $item->rata_rata = round($item->rata_rata, 1);
                $item->status = $item->rata_rata >= 75 ? 'kompeten' : 'perlu_perbaikan';
                return $item;
            });
        
        // Overall stats
        $totalNilai = $mapelList->sum('total_nilai');
        $rataRataKeseluruhan = $mapelList->count() > 0 ? round($mapelList->avg('rata_rata'), 1) : 0;
        $mapelKompeten = $mapelList->where('status', 'kompeten')->count();
        
        return view('siswa.nilai', compact(
            'siswa',
            'periodik',
            'mapelList',
            'totalNilai',
            'rataRataKeseluruhan',
            'mapelKompeten'
        ));
    }
    
    private function showDetailMapel($siswa, $periodik, $mapelParam)
    {
        $mapel = str_replace('_', ' ', ucwords(strtolower($mapelParam)));
        
        $nilaiData = DB::table('penilaian')
            ->where('nisn', $siswa->nisn)
            ->where('mapel', $mapel)
            ->orderBy('tanggal_penilaian', 'desc')
            ->get();
        
        // Calculate stats
        $totalNilai = $nilaiData->count();
        $rataRata = $totalNilai > 0 ? round($nilaiData->avg('nilai'), 1) : 0;
        $tertinggi = $nilaiData->max('nilai') ?? 0;
        $terendah = $nilaiData->min('nilai') ?? 100;
        
        $status = $rataRata >= 75 ? 'kompeten' : 'perlu_perbaikan';
        
        return view('siswa.nilai-detail', compact(
            'siswa',
            'periodik',
            'mapel',
            'nilaiData',
            'totalNilai',
            'rataRata',
            'tertinggi',
            'terendah',
            'status'
        ));
    }
}
