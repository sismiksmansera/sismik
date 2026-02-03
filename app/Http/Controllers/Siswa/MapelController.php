<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class MapelController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        $semesterJadwal = strtolower($semesterAktif);
        
        // Find siswa's rombel
        $namaRombel = null;
        for ($i = 1; $i <= 6; $i++) {
            $kolomRombel = "rombel_semester_{$i}";
            if (!empty($siswa->$kolomRombel)) {
                $namaRombel = $siswa->$kolomRombel;
            }
        }
        
        $idRombel = null;
        $mapelList = collect();
        
        if ($namaRombel) {
            // Find rombel ID
            $rombel = DB::table('rombel')
                ->where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $tahunAktif)
                ->first();
            
            if (!$rombel) {
                $rombel = DB::table('rombel')
                    ->where('nama_rombel', $namaRombel)
                    ->first();
            }
            
            if ($rombel) {
                $idRombel = $rombel->id;
                
                // Get student's religion for filtering
                $agamaSiswa = $siswa->agama ?? null;
                $agamaMapelMap = [
                    'Islam' => 'Pendidikan Agama Islam',
                    'Kristen' => 'Pendidikan Agama Kristen',
                    'Katholik' => 'Pendidikan Agama Katholik',
                    'Hindu' => 'Pendidikan Agama Hindu',
                    'Buddha' => 'Pendidikan Agama Buddha',
                    'Konghucu' => 'Pendidikan Agama Konghucu',
                ];
                $matchedAgama = $agamaSiswa ? ($agamaMapelMap[$agamaSiswa] ?? null) : null;
                
                // Get mata pelajaran from jadwal
                $query = DB::table('jadwal_pelajaran as jp')
                    ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
                    ->select(
                        'mp.id as id_mapel',
                        'mp.nama_mapel',
                        'jp.nama_guru',
                        'jp.tahun_pelajaran',
                        'jp.semester',
                        DB::raw('COUNT(DISTINCT jp.hari) as total_hari'),
                        DB::raw('COUNT(jp.jam_ke) as total_jam')
                    )
                    ->where('jp.id_rombel', $idRombel)
                    ->where('jp.tahun_pelajaran', $tahunAktif)
                    ->where('jp.semester', $semesterJadwal);
                
                // Filter religion subjects based on student's religion
                if ($agamaSiswa) {
                    $query->where(function ($q) use ($matchedAgama) {
                        // Include all non-religion subjects
                        $q->where('mp.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%');
                        // Include only the matching religion subject
                        if ($matchedAgama) {
                            $q->orWhere('mp.nama_mapel', $matchedAgama);
                        }
                    });
                }
                
                $mapelList = $query
                    ->groupBy('mp.id', 'mp.nama_mapel', 'jp.nama_guru', 'jp.tahun_pelajaran', 'jp.semester')
                    ->orderBy('mp.nama_mapel')
                    ->get()
                    ->map(function($mapel) use ($idRombel, $tahunAktif, $semesterJadwal) {
                        // Get jadwal detail for this mapel
                        $mapel->jadwal_detail = DB::table('jadwal_pelajaran')
                            ->select('hari', DB::raw('GROUP_CONCAT(DISTINCT jam_ke ORDER BY jam_ke ASC) as jam_list'))
                            ->where('id_rombel', $idRombel)
                            ->where('tahun_pelajaran', $tahunAktif)
                            ->where('semester', $semesterJadwal)
                            ->where('nama_guru', $mapel->nama_guru)
                            ->where('id_mapel', $mapel->id_mapel)
                            ->groupBy('hari')
                            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
                            ->get();
                        
                        return $mapel;
                    });
            }
        }
        
        $totalMapel = $mapelList->count();
        $totalJamMinggu = $mapelList->sum('total_jam');
        
        return view('siswa.mapel', compact(
            'siswa',
            'periodik',
            'mapelList',
            'namaRombel',
            'totalMapel',
            'totalJamMinggu',
            'tahunAktif',
            'semesterAktif',
            'idRombel'
        ));
    }
}
