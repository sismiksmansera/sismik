<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;

class JadwalController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        
        if (!$periodik) {
            // Fallback to latest periodik
            $periodik = DataPeriodik::orderBy('id', 'desc')->first();
        }
        
        if (!$periodik) {
            return view('siswa.jadwal', [
                'siswa' => $siswa,
                'periodik' => null,
                'jadwalPerHari' => [],
                'namaRombel' => null,
                'totalMapel' => 0,
                'hariList' => [],
                'debug' => ['error' => 'Tidak ada periode aktif'],
            ]);
        }
        
        $tahunAktif = $periodik->tahun_pelajaran;
        $semesterJadwal = strtolower($periodik->semester); // 'genap' or 'ganjil'
        
        // Debug collection
        $debug = [
            'tahun_aktif' => $tahunAktif,
            'semester_aktif' => $periodik->semester,
            'semester_jadwal' => $semesterJadwal,
        ];
        
        // CARI ROMBEL SISWA - exactly like PHP legacy (iterate semester 1-6)
        $namaRombel = null;
        $agamaSiswa = $siswa->agama ?? null;
        
        for ($i = 1; $i <= 6; $i++) {
            $kolomRombel = "rombel_semester_{$i}";
            if (!empty($siswa->$kolomRombel)) {
                $namaRombel = $siswa->$kolomRombel;
                $debug['found_in_semester'] = $i;
                break;
            }
        }
        
        $debug['nama_rombel_siswa'] = $namaRombel;
        $debug['agama_siswa'] = $agamaSiswa;
        
        if (!$namaRombel) {
            return view('siswa.jadwal', [
                'siswa' => $siswa,
                'periodik' => $periodik,
                'jadwalPerHari' => [],
                'namaRombel' => null,
                'totalMapel' => 0,
                'hariList' => [],
                'debug' => array_merge($debug, ['error' => 'Rombel tidak ditemukan di data siswa']),
            ]);
        }
        
        // CARI ID ROMBEL - exactly like PHP legacy
        $idRombel = null;
        
        // First try: with tahun_pelajaran and semester
        $rombel = DB::table('rombel')
            ->where('nama_rombel', $namaRombel)
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterJadwal)
            ->first();
        
        $debug['rombel_query_1'] = $rombel ? "Found ID: {$rombel->id}" : "Not found";
        
        if (!$rombel) {
            // Second try: with tahun_pelajaran only
            $rombel = DB::table('rombel')
                ->where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $tahunAktif)
                ->first();
            $debug['rombel_query_2'] = $rombel ? "Found ID: {$rombel->id}" : "Not found";
        }
        
        if (!$rombel) {
            // Third try: by name only
            $rombel = DB::table('rombel')
                ->where('nama_rombel', $namaRombel)
                ->first();
            $debug['rombel_query_3'] = $rombel ? "Found ID: {$rombel->id}" : "Not found";
        }
        
        if ($rombel) {
            $idRombel = $rombel->id;
            $debug['rombel_id'] = $idRombel;
            $debug['rombel_tahun'] = $rombel->tahun_pelajaran ?? 'N/A';
            $debug['rombel_semester'] = $rombel->semester ?? 'N/A';
        }
        
        // AMBIL DATA JADWAL PELAJARAN PER HARI
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];
        $totalMapelBerbeda = [];
        
        if ($idRombel) {
            foreach ($hariList as $hari) {
                // Initialize jadwal for 11 jam
                $jadwalHari = array_fill(1, 11, null);
                
                // Query jadwal - exactly like PHP legacy
                $jadwals = DB::table('jadwal_pelajaran as jp')
                    ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
                    ->where('jp.id_rombel', $idRombel)
                    ->where('jp.tahun_pelajaran', $tahunAktif)
                    ->where('jp.semester', $semesterJadwal)
                    ->where('jp.hari', $hari)
                    ->where(function($query) use ($agamaSiswa) {
                        // Filter agama - show non-agama subjects OR matching agama subject
                        $query->where('mp.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%')
                              ->orWhere(function($q) use ($agamaSiswa) {
                                  $q->where('mp.nama_mapel', 'LIKE', 'Pendidikan Agama%')
                                    ->where('mp.nama_mapel', $this->getAgamaMapelName($agamaSiswa));
                              });
                    })
                    ->select('jp.jam_ke', 'jp.nama_guru', 'mp.nama_mapel', 'jp.hari')
                    ->orderBy('jp.jam_ke', 'asc')
                    ->get();
                
                // Debug first day
                if ($hari === 'Senin') {
                    $debug['jadwal_senin_count'] = $jadwals->count();
                    
                    // Also check without filters to debug
                    $jadwalSeninsAll = DB::table('jadwal_pelajaran')
                        ->where('id_rombel', $idRombel)
                        ->where('hari', 'Senin')
                        ->count();
                    $debug['jadwal_senin_all_count'] = $jadwalSeninsAll;
                    
                    // Check what data is in jadwal_pelajaran for this rombel
                    $sampleJadwal = DB::table('jadwal_pelajaran')
                        ->where('id_rombel', $idRombel)
                        ->limit(3)
                        ->get();
                    $debug['sample_jadwal'] = $sampleJadwal->map(fn($j) => [
                        'hari' => $j->hari,
                        'jam_ke' => $j->jam_ke,
                        'tahun' => $j->tahun_pelajaran ?? 'N/A',
                        'semester' => $j->semester ?? 'N/A',
                    ])->toArray();
                }
                
                foreach ($jadwals as $jadwal) {
                    $jamKe = $jadwal->jam_ke;
                    if ($jamKe >= 1 && $jamKe <= 11) {
                        $jadwalHari[$jamKe] = [
                            'mapel' => $jadwal->nama_mapel,
                            'guru' => $jadwal->nama_guru,
                        ];
                        
                        if (!in_array($jadwal->nama_mapel, $totalMapelBerbeda)) {
                            $totalMapelBerbeda[] = $jadwal->nama_mapel;
                        }
                    }
                }
                
                $jadwalPerHari[$hari] = $jadwalHari;
            }
        } else {
            $debug['error'] = 'Rombel tidak ditemukan di tabel rombel';
        }
        
        return view('siswa.jadwal', [
            'siswa' => $siswa,
            'periodik' => $periodik,
            'jadwalPerHari' => $jadwalPerHari,
            'namaRombel' => $namaRombel,
            'totalMapel' => count($totalMapelBerbeda),
            'hariList' => $hariList,
            'debug' => $debug,
        ]);
    }
    
    private function getAgamaMapelName(?string $agama): string
    {
        $mapping = [
            'Islam' => 'Pendidikan Agama Islam',
            'Kristen' => 'Pendidikan Agama Kristen',
            'Katholik' => 'Pendidikan Agama Katholik',
            'Hindu' => 'Pendidikan Agama Hindu',
            'Buddha' => 'Pendidikan Agama Buddha',
            'Konghucu' => 'Pendidikan Agama Konghucu',
        ];
        
        return $mapping[$agama] ?? 'Pendidikan Agama Islam';
    }
}
