<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class JadwalController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        
        if (!$periodik) {
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
            ]);
        }
        
        $tahunAktif = $periodik->tahun_pelajaran;
        $semesterJadwal = strtolower($periodik->semester);
        
        // Find siswa's rombel (iterate semester 1-6)
        $namaRombel = null;
        $agamaSiswa = $siswa->agama ?? null;
        
        for ($i = 1; $i <= 6; $i++) {
            $kolomRombel = "rombel_semester_{$i}";
            if (!empty($siswa->$kolomRombel)) {
                $namaRombel = $siswa->$kolomRombel;
                break;
            }
        }
        
        if (!$namaRombel) {
            return view('siswa.jadwal', [
                'siswa' => $siswa,
                'periodik' => $periodik,
                'jadwalPerHari' => [],
                'namaRombel' => null,
                'totalMapel' => 0,
                'hariList' => [],
            ]);
        }
        
        // Find rombel ID
        $rombel = DB::table('rombel')
            ->where('nama_rombel', $namaRombel)
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterJadwal)
            ->first();
        
        if (!$rombel) {
            $rombel = DB::table('rombel')
                ->where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $tahunAktif)
                ->first();
        }
        
        if (!$rombel) {
            $rombel = DB::table('rombel')
                ->where('nama_rombel', $namaRombel)
                ->first();
        }
        
        $idRombel = $rombel ? $rombel->id : null;
        
        // Get jadwal per hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];
        $totalMapelBerbeda = [];
        
        if ($idRombel) {
            foreach ($hariList as $hari) {
                $jadwalHari = array_fill(1, 11, null);
                
                $jadwals = DB::table('jadwal_pelajaran as jp')
                    ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
                    ->where('jp.id_rombel', $idRombel)
                    ->where('jp.tahun_pelajaran', $tahunAktif)
                    ->where('jp.semester', $semesterJadwal)
                    ->where('jp.hari', $hari)
                    ->where(function($query) use ($agamaSiswa) {
                        $query->where('mp.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%')
                              ->orWhere(function($q) use ($agamaSiswa) {
                                  $q->where('mp.nama_mapel', 'LIKE', 'Pendidikan Agama%')
                                    ->where('mp.nama_mapel', $this->getAgamaMapelName($agamaSiswa));
                              });
                    })
                    ->select('jp.jam_ke', 'jp.nama_guru', 'mp.nama_mapel', 'jp.hari')
                    ->orderBy('jp.jam_ke', 'asc')
                    ->get();
                
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
        }
        
        return view('siswa.jadwal', [
            'siswa' => $siswa,
            'periodik' => $periodik,
            'jadwalPerHari' => $jadwalPerHari,
            'namaRombel' => $namaRombel,
            'totalMapel' => count($totalMapelBerbeda),
            'hariList' => $hariList,
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
