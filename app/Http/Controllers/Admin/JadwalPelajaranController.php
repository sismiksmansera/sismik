<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\DataPeriodik;

class JadwalPelajaranController extends Controller
{
    /**
     * Display jadwal pelajaran harian
     */
    public function index(Request $request)
    {
        // Daftar hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodeAktif->semester ?? '');
        
        // Filter values (default to active period)
        $tahunFilter = $request->get('tahun', $tahunAktif);
        $semesterFilter = $request->get('semester', $semesterAktif);
        $filterHari = $request->get('hari', '');
        
        // Validate hari filter
        if (!empty($filterHari) && !in_array($filterHari, $hariList)) {
            $filterHari = '';
        }
        
        // Get unique tahun_pelajaran
        $tahunList = JadwalPelajaran::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        // Build base query for stats
        $statsQuery = JadwalPelajaran::query();
        if (!empty($tahunFilter)) {
            $statsQuery->where('tahun_pelajaran', $tahunFilter);
        }
        if (!empty($semesterFilter)) {
            $statsQuery->whereRaw('LOWER(semester) = ?', [$semesterFilter]);
        }
        
        // Calculate statistics
        $totalJadwal = (clone $statsQuery)->count();
        $totalRombel = (clone $statsQuery)->distinct('id_rombel')->count('id_rombel');
        $hariAktifCount = (clone $statsQuery)->distinct('hari')->count('hari');
        
        // Get rombels with jadwal for active period
        $rombelList = Rombel::where('tahun_pelajaran', $tahunFilter ?: $tahunAktif)
            ->whereRaw('LOWER(semester) = ?', [$semesterFilter ?: $semesterAktif])
            ->orderBy('nama_rombel', 'asc')
            ->get();
        
        // Determine which days to show
        $loopHari = $filterHari ? [$filterHari] : $hariList;
        
        // Build jadwal data structure
        $jadwalData = [];
        
        foreach ($loopHari as $hari) {
            $jadwalData[$hari] = [
                'count' => 0,
                'rombels' => []
            ];
            
            // Count jadwal for this day
            $countQuery = JadwalPelajaran::where('hari', $hari);
            if (!empty($tahunFilter)) {
                $countQuery->where('tahun_pelajaran', $tahunFilter);
            }
            if (!empty($semesterFilter)) {
                $countQuery->whereRaw('LOWER(semester) = ?', [$semesterFilter]);
            }
            $jadwalData[$hari]['count'] = $countQuery->count();
            
            // Get jadwal per rombel
            foreach ($rombelList as $rombel) {
                $jadwalQuery = JadwalPelajaran::select(
                        'jadwal_pelajaran.jam_ke',
                        'jadwal_pelajaran.nama_guru',
                        'mata_pelajaran.nama_mapel',
                        'jadwal_pelajaran.tahun_pelajaran',
                        'jadwal_pelajaran.semester'
                    )
                    ->join('mata_pelajaran', 'jadwal_pelajaran.id_mapel', '=', 'mata_pelajaran.id')
                    ->where('jadwal_pelajaran.id_rombel', $rombel->id)
                    ->where('jadwal_pelajaran.hari', $hari);
                
                if (!empty($tahunFilter)) {
                    $jadwalQuery->where('jadwal_pelajaran.tahun_pelajaran', $tahunFilter);
                }
                if (!empty($semesterFilter)) {
                    $jadwalQuery->whereRaw('LOWER(jadwal_pelajaran.semester) = ?', [$semesterFilter]);
                }
                
                $jadwal = $jadwalQuery->orderByRaw('CAST(jadwal_pelajaran.jam_ke AS UNSIGNED) ASC')->get();
                
                if ($jadwal->count() > 0) {
                    $jadwalData[$hari]['rombels'][] = [
                        'id' => $rombel->id,
                        'nama_rombel' => $rombel->nama_rombel,
                        'mapel_count' => $jadwal->count(),
                        'jadwal' => $jadwal
                    ];
                }
            }
        }
        
        return view('admin.jadwal-pelajaran.index', compact(
            'hariList', 'loopHari', 'tahunList', 'tahunFilter', 'semesterFilter', 
            'filterHari', 'totalJadwal', 'totalRombel', 'hariAktifCount', 
            'jadwalData', 'tahunAktif', 'semesterAktif'
        ));
    }
}
