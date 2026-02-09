<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use App\Services\EffectiveDateService;

class JadwalController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
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
        $semesterAktif = strtolower($periodik->semester);
        
        // Find siswa's active rombel based on semester calculation
        $namaRombel = $this->getRombelAktif($siswa, $periodik);
        
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
        $rombel = Rombel::where('nama_rombel', $namaRombel)
            ->where('tahun_pelajaran', $tahunAktif)
            ->first();
        
        if (!$rombel) {
            // Try without semester filter
            $rombel = Rombel::where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $tahunAktif)
                ->first();
        }
        
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];
        $totalMapelBerbeda = [];
        
        if ($rombel) {
            $agamaSiswa = $siswa->agama ?? null;
            
            foreach ($hariList as $hari) {
                $jadwalHari = array_fill(1, 11, null);
                
                $jadwals = JadwalPelajaran::where('jadwal_pelajaran.id_rombel', $rombel->id)
                    ->where('jadwal_pelajaran.tahun_pelajaran', $tahunAktif)
                    ->where('jadwal_pelajaran.semester', $semesterAktif)
                    ->where('jadwal_pelajaran.hari', $hari)
                    ->join('mata_pelajaran', 'jadwal_pelajaran.id_mapel', '=', 'mata_pelajaran.id')
                    ->select('jadwal_pelajaran.*', 'mata_pelajaran.nama_mapel')
                    ->orderBy('jadwal_pelajaran.jam_ke', 'asc')
                    ->get();
                
                foreach ($jadwals as $jadwal) {
                    $namaMapel = $jadwal->nama_mapel;
                    
                    // Filter agama - skip non-matching religion subjects
                    if (str_contains($namaMapel, 'Pendidikan Agama')) {
                        $mapelAgama = $this->getAgamaMapelName($agamaSiswa);
                        if ($namaMapel !== $mapelAgama) {
                            continue;
                        }
                    }
                    
                    $jamKe = $jadwal->jam_ke;
                    if ($jamKe >= 1 && $jamKe <= 11) {
                        $jadwalHari[$jamKe] = [
                            'mapel' => $namaMapel,
                            'guru' => $jadwal->nama_guru,
                        ];
                        
                        if (!in_array($namaMapel, $totalMapelBerbeda)) {
                            $totalMapelBerbeda[] = $namaMapel;
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
    
    private function getRombelAktif($siswa, $periodik)
    {
        if (!$periodik) return null;
        
        // Method 1: Calculate based on angkatan (preferred for accurate semester tracking)
        $tahunAjaran = explode('/', $periodik->tahun_pelajaran ?? '2025/2026');
        $tahunAwal = intval($tahunAjaran[0] ?? 2025);
        $angkatan = intval($siswa->angkatan_masuk ?? 2023);
        $semesterNama = $periodik->semester ?? 'Ganjil';
        
        $tahunSelisih = $tahunAwal - $angkatan;
        if ($semesterNama === 'Ganjil') {
            $semesterKe = ($tahunSelisih * 2) + 1;
        } else {
            $semesterKe = ($tahunSelisih * 2) + 2;
        }
        
        // Clamp to valid range
        $semesterKe = max(1, min(6, $semesterKe));
        
        $kolomRombel = "rombel_semester_{$semesterKe}";
        $rombelCalculated = $siswa->$kolomRombel ?? null;
        
        if (!empty($rombelCalculated)) {
            return $rombelCalculated;
        }
        
        // Method 2: Fallback - iterate through all semesters to find any assigned rombel
        // This matches the legacy PHP approach that iterates through semester 1-6
        for ($i = 1; $i <= 6; $i++) {
            $kolomRombel = "rombel_semester_{$i}";
            if (!empty($siswa->$kolomRombel)) {
                return $siswa->$kolomRombel;
            }
        }
        
        return null;
    }
}

