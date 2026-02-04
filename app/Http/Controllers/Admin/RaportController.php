<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\DataPeriodik;
use App\Models\RaportSettings;
use App\Models\NilaiKatrol;
use App\Models\PresensiSiswa;
use App\Models\AnggotaEkstrakurikuler;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaportController extends Controller
{
    /**
     * Print raport for a specific student
     */
    public function print(Request $request)
    {
        $nisn = $request->query('nisn');
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (!$nisn || !$rombelId || !$tahun || !$semester) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get student data
        $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response('<script>alert("Siswa tidak ditemukan!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get wali kelas NIP
        $nipWaliKelas = '';
        if (!empty($rombel->wali_kelas)) {
            $guru = Guru::where('nama', $rombel->wali_kelas)->first();
            if ($guru) {
                $nipWaliKelas = $guru->nip ?? '';
            }
        }
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        if (!$periodeAktif) {
            $periodeAktif = (object)[
                'tahun_pelajaran' => '2024/2025',
                'semester' => 'Ganjil',
                'nama_kepala' => '',
                'nip_kepala' => ''
            ];
        }
        
        // Get tanggal bagi raport from settings
        $tanggalBagiRaport = '';
        $raportSettings = RaportSettings::whereHas('periodik', function($q) {
            $q->where('aktif', 'Ya');
        })->first();
        
        if ($raportSettings && $raportSettings->tanggal_bagi_raport) {
            $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $tanggalObj = $raportSettings->tanggal_bagi_raport;
            $tanggalBagiRaport = $tanggalObj->format('d') . ' ' . 
                                 $bulanIndo[(int)$tanggalObj->format('n')] . ' ' . 
                                 $tanggalObj->format('Y');
        }
        
        $semesterJadwal = strtolower($periodeAktif->semester);
        $agamaSiswa = $siswa->agama ?? '';
        
        // Get all mata pelajaran from jadwal_pelajaran
        $mapelList = $this->getMapelForRaport($rombelId, $periodeAktif->tahun_pelajaran, $semesterJadwal, $agamaSiswa);
        
        // Get nilai katrol for each mapel
        $nilaiMapel = [];
        $totalNilai = 0;
        $jumlahMapelValid = 0;
        
        foreach ($mapelList as $mapel) {
            $nilaiKatrol = NilaiKatrol::where('rombel_id', $rombelId)
                ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                ->where('semester', $periodeAktif->semester)
                ->where('nisn', $nisn)
                ->where('mapel', $mapel->nama_mapel)
                ->first();
            
            $nilai = $nilaiKatrol ? floatval($nilaiKatrol->nilai_katrol) : null;
            
            $nilaiMapel[] = [
                'mapel' => $mapel->nama_mapel,
                'nilai' => $nilai
            ];
            
            if ($nilai !== null) {
                $totalNilai += $nilai;
                $jumlahMapelValid++;
            }
        }
        
        // Grouping IPA/IPS for class X
        if ($rombel->tingkat == 'X') {
            $nilaiMapel = $this->groupIpaIps($nilaiMapel);
            
            // Recalculate totals
            $totalNilai = 0;
            $jumlahMapelValid = 0;
            foreach ($nilaiMapel as $item) {
                if ($item['nilai'] !== null) {
                    $totalNilai += $item['nilai'];
                    $jumlahMapelValid++;
                }
            }
        }
        
        $rataRata = $jumlahMapelValid > 0 ? round($totalNilai / $jumlahMapelValid, 1) : 0;
        
        // Get attendance data
        $presensi = PresensiSiswa::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa
            ")
            ->first();
        
        // Get ekstrakurikuler
        $ekstraList = AnggotaEkstrakurikuler::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->with('ekstrakurikuler')
            ->get()
            ->map(function($item) {
                return [
                    'nama_ekstrakurikuler' => $item->ekstrakurikuler->nama_ekstrakurikuler ?? '',
                    'nilai' => $item->nilai
                ];
            })
            ->toArray();
        
        return view('admin.raport.print', compact(
            'siswa',
            'rombel',
            'nipWaliKelas',
            'periodeAktif',
            'tanggalBagiRaport',
            'nilaiMapel',
            'totalNilai',
            'rataRata',
            'presensi',
            'ekstraList'
        ));
    }
    
    /**
     * Get mata pelajaran for raport based on student's religion
     */
    private function getMapelForRaport($rombelId, $tahunPelajaran, $semester, $agamaSiswa)
    {
        $agamaMapel = [
            'Islam' => 'Pendidikan Agama Islam',
            'Kristen' => 'Pendidikan Agama Kristen',
            'Katholik' => 'Pendidikan Agama Katholik',
            'Hindu' => 'Pendidikan Agama Hindu',
            'Buddha' => 'Pendidikan Agama Buddha',
            'Konghucu' => 'Pendidikan Agama Konghucu',
        ];
        
        $agamaMapelSiswa = $agamaMapel[$agamaSiswa] ?? null;
        
        return DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $rombelId)
            ->where('jp.tahun_pelajaran', $tahunPelajaran)
            ->where('jp.semester', $semester)
            ->where(function($query) use ($agamaMapelSiswa) {
                $query->where('mp.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%');
                if ($agamaMapelSiswa) {
                    $query->orWhere('mp.nama_mapel', $agamaMapelSiswa);
                }
            })
            ->select('mp.id', 'mp.nama_mapel')
            ->distinct()
            ->orderBy('mp.id')
            ->get();
    }
    
    /**
     * Group IPA/IPS subjects for class X
     */
    private function groupIpaIps($nilaiMapel)
    {
        $ipaMapel = ['Biologi', 'Fisika', 'Kimia'];
        $ipsMapel = ['Sejarah', 'Ekonomi', 'Sosiologi', 'Geografi'];
        
        $ipaValues = [];
        $ipsValues = [];
        $ipaExists = false;
        $ipsExists = false;
        $tempNilaiMapel = [];
        
        foreach ($nilaiMapel as $item) {
            if (in_array($item['mapel'], $ipaMapel)) {
                $ipaExists = true;
                if ($item['nilai'] !== null) {
                    $ipaValues[] = $item['nilai'];
                }
            } elseif (in_array($item['mapel'], $ipsMapel)) {
                $ipsExists = true;
                if ($item['nilai'] !== null) {
                    $ipsValues[] = $item['nilai'];
                }
            } else {
                $tempNilaiMapel[] = $item;
            }
        }
        
        // Calculate averages
        $ipaRata = ($ipaExists && !empty($ipaValues)) ? round(array_sum($ipaValues) / count($ipaValues), 1) : null;
        $ipsRata = ($ipsExists && !empty($ipsValues)) ? round(array_sum($ipsValues) / count($ipsValues), 1) : null;
        
        // Insert IPA/IPS after Matematika
        $newNilaiMapel = [];
        foreach ($tempNilaiMapel as $item) {
            $newNilaiMapel[] = $item;
            if ($item['mapel'] == 'Matematika') {
                if ($ipaExists) {
                    $newNilaiMapel[] = ['mapel' => 'Ilmu Pengetahuan Alam', 'nilai' => $ipaRata];
                }
                if ($ipsExists) {
                    $newNilaiMapel[] = ['mapel' => 'Ilmu Pengetahuan Sosial', 'nilai' => $ipsRata];
                }
            }
        }
        
        return $newNilaiMapel;
    }
    
    /**
     * Get predikat for nilai
     */
    public static function getPredikat($nilai)
    {
        if ($nilai === null) return '-';
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 65) return 'C';
        return 'D';
    }
    
    /**
     * Get deskripsi predikat
     */
    public static function getDeskripsiPredikat($predikat)
    {
        switch ($predikat) {
            case 'A': return 'Sangat Baik';
            case 'B': return 'Baik';
            case 'C': return 'Cukup';
            default: return 'Kurang';
        }
    }
    
    /**
     * Print raport for all students in a rombel
     */
    public function printAll(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (!$rombelId || !$tahun || !$semester) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get wali kelas NIP
        $nipWaliKelas = '';
        if (!empty($rombel->wali_kelas)) {
            $guru = Guru::where('nama', $rombel->wali_kelas)->first();
            if ($guru) {
                $nipWaliKelas = $guru->nip ?? '';
            }
        }
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        if (!$periodeAktif) {
            $periodeAktif = (object)[
                'tahun_pelajaran' => '2024/2025',
                'semester' => 'Ganjil',
                'nama_kepala' => '',
                'nip_kepala' => ''
            ];
        }
        
        // Get tanggal bagi raport from settings
        $tanggalBagiRaport = '';
        $raportSettings = RaportSettings::whereHas('periodik', function($q) {
            $q->where('aktif', 'Ya');
        })->first();
        
        if ($raportSettings && $raportSettings->tanggal_bagi_raport) {
            $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $tanggalObj = $raportSettings->tanggal_bagi_raport;
            $tanggalBagiRaport = $tanggalObj->format('d') . ' ' . 
                                 $bulanIndo[(int)$tanggalObj->format('n')] . ' ' . 
                                 $tanggalObj->format('Y');
        }
        
        $semesterJadwal = strtolower($periodeAktif->semester);
        
        // Get all students in rombel
        $siswaList = $this->getSiswaByRombel($rombel->nama_rombel, $tahun, $semester);
        
        if ($siswaList->isEmpty()) {
            return response('<script>alert("Tidak ada siswa dalam rombel ini!"); window.close();</script>');
        }
        
        // Prepare data for each student
        $studentsData = [];
        foreach ($siswaList as $siswa) {
            $agamaSiswa = $siswa->agama ?? '';
            
            // Get all mata pelajaran
            $mapelList = $this->getMapelForRaport($rombelId, $periodeAktif->tahun_pelajaran, $semesterJadwal, $agamaSiswa);
            
            // Get nilai katrol for each mapel
            $nilaiMapel = [];
            $totalNilai = 0;
            $jumlahMapelValid = 0;
            
            foreach ($mapelList as $mapel) {
                $nilaiKatrol = NilaiKatrol::where('rombel_id', $rombelId)
                    ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                    ->where('semester', $periodeAktif->semester)
                    ->where('nisn', $siswa->nisn)
                    ->where('mapel', $mapel->nama_mapel)
                    ->first();
                
                $nilai = $nilaiKatrol ? floatval($nilaiKatrol->nilai_katrol) : null;
                
                $nilaiMapel[] = [
                    'mapel' => $mapel->nama_mapel,
                    'nilai' => $nilai
                ];
                
                if ($nilai !== null) {
                    $totalNilai += $nilai;
                    $jumlahMapelValid++;
                }
            }
            
            // Grouping IPA/IPS for class X
            if ($rombel->tingkat == 'X') {
                $nilaiMapel = $this->groupIpaIps($nilaiMapel);
                
                // Recalculate totals
                $totalNilai = 0;
                $jumlahMapelValid = 0;
                foreach ($nilaiMapel as $item) {
                    if ($item['nilai'] !== null) {
                        $totalNilai += $item['nilai'];
                        $jumlahMapelValid++;
                    }
                }
            }
            
            $rataRata = $jumlahMapelValid > 0 ? round($totalNilai / $jumlahMapelValid, 1) : 0;
            
            // Get attendance data
            $presensi = PresensiSiswa::where('nisn', $siswa->nisn)
                ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                ->where('semester', $periodeAktif->semester)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa
                ")
                ->first();
            
            // Get ekstrakurikuler
            $ekstraList = AnggotaEkstrakurikuler::where('siswa_id', $siswa->id)
                ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                ->where('semester', $periodeAktif->semester)
                ->with('ekstrakurikuler')
                ->get()
                ->map(function($item) {
                    return [
                        'nama_ekstrakurikuler' => $item->ekstrakurikuler->nama_ekstrakurikuler ?? '',
                        'nilai' => $item->nilai
                    ];
                })
                ->toArray();
            
            $studentsData[] = [
                'siswa' => $siswa,
                'nilaiMapel' => $nilaiMapel,
                'totalNilai' => $totalNilai,
                'rataRata' => $rataRata,
                'presensi' => $presensi,
                'ekstraList' => $ekstraList
            ];
        }
        
        return view('admin.raport.print-all', compact(
            'rombel',
            'nipWaliKelas',
            'periodeAktif',
            'tanggalBagiRaport',
            'studentsData'
        ));
    }
    
    /**
     * Get students by rombel name for specific period
     */
    private function getSiswaByRombel($rombelNama, $tahun, $semester)
    {
        $tahunAjaran = explode('/', $tahun);
        $tahunAwal = intval($tahunAjaran[0]);
        
        $query = Siswa::query();
        
        if ($semester == 'Ganjil') {
            $query->where(function($q) use ($rombelNama, $tahunAwal) {
                $q->where(function($sq) use ($rombelNama, $tahunAwal) {
                    $sq->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombelNama);
                })->orWhere(function($sq) use ($rombelNama, $tahunAwal) {
                    $sq->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombelNama);
                })->orWhere(function($sq) use ($rombelNama, $tahunAwal) {
                    $sq->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombelNama);
                });
            });
        } else { // Genap
            $query->where(function($q) use ($rombelNama, $tahunAwal) {
                $q->where(function($sq) use ($rombelNama, $tahunAwal) {
                    $sq->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombelNama);
                })->orWhere(function($sq) use ($rombelNama, $tahunAwal) {
                    $sq->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombelNama);
                })->orWhere(function($sq) use ($rombelNama, $tahunAwal) {
                    $sq->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombelNama);
                });
            });
        }
        
        return $query->orderBy('nama')->get();
    }
}
