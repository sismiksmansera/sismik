<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\DataPeriodik;
use App\Models\Penilaian;
use App\Models\PresensiSiswa;
use App\Models\AnggotaEkstrakurikuler;
use App\Models\CatatanBimbingan;
use App\Models\PrestasiSiswa;
use App\Models\RaportSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatAkademikController extends Controller
{
    /**
     * Display riwayat akademik for a student
     */
    public function show(Request $request)
    {
        $nisn = $request->query('nisn');
        
        if (empty($nisn)) {
            return response('<div class="alert alert-danger">NISN tidak ditemukan.</div>');
        }
        
        // Get student data
        $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response('<div class="alert alert-danger">Data siswa tidak ditemukan.</div>');
        }
        
        // Find active rombel from semester 1-6
        $namaRombel = '';
        for ($i = 1; $i <= 6; $i++) {
            $kolomRombel = "rombel_semester_" . $i;
            if (!empty($siswa->$kolomRombel)) {
                $namaRombel = $siswa->$kolomRombel;
            }
        }
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        if (!$periodeAktif) {
            $periodeAktif = (object)[
                'tahun_pelajaran' => '2024/2025',
                'semester' => 'Ganjil'
            ];
        }
        
        $semesterJadwal = strtolower($periodeAktif->semester);
        
        // Get rombel
        $rombel = null;
        if ($namaRombel) {
            $rombel = Rombel::where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                ->where('semester', $semesterJadwal)
                ->first();
        }
        
        // Get mapel from jadwal
        $mapelList = [];
        $agamaSiswa = $siswa->agama ?? '';
        
        if ($rombel) {
            $mapelList = $this->getMapelList($rombel->id, $periodeAktif->tahun_pelajaran, $semesterJadwal, $agamaSiswa);
        }
        
        // Calculate nilai per mapel
        $rekapNilai = [];
        $totalRataRata = 0;
        $totalMapelDinilai = 0;
        
        foreach ($mapelList as $mapel) {
            $nilaiData = Penilaian::where('nisn', $nisn)
                ->where('mapel', $mapel->nama_mapel)
                ->pluck('nilai')
                ->toArray();
            
            $totalNilai = count($nilaiData);
            $rataRata = 0;
            $tertinggi = 0;
            $terendah = 0;
            
            if ($totalNilai > 0) {
                $rataRata = round(array_sum($nilaiData) / $totalNilai, 2);
                $tertinggi = max($nilaiData);
                $terendah = min($nilaiData);
                $totalRataRata += $rataRata;
                $totalMapelDinilai++;
            }
            
            // Get presensi per mapel
            $presensiMapel = $this->getPresensiPerMapel($nisn, $mapel->nama_mapel, $periodeAktif->tahun_pelajaran, $periodeAktif->semester);
            
            $rekapNilai[] = [
                'nama_mapel' => $mapel->nama_mapel,
                'nama_guru' => $mapel->nama_guru,
                'total_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'tertinggi' => $tertinggi,
                'terendah' => $terendah,
                'presensi' => $presensiMapel
            ];
        }
        
        $rataRataKeseluruhan = $totalMapelDinilai > 0 ? round($totalRataRata / $totalMapelDinilai, 2) : 0;
        
        // Get total presensi
        $presensiTotal = PresensiSiswa::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos
            ")
            ->first();
        
        $persentaseKehadiran = $presensiTotal->total > 0 
            ? round(($presensiTotal->hadir / $presensiTotal->total) * 100, 1) 
            : 0;
        
        // Get ekstrakurikuler
        $ekstraList = AnggotaEkstrakurikuler::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->with('ekstrakurikuler')
            ->get()
            ->map(function($item) {
                return [
                    'nama_ekstrakurikuler' => $item->ekstrakurikuler->nama_ekstrakurikuler ?? '',
                    'pembina' => $item->ekstrakurikuler->pembina_1 ?? '',
                    'nilai' => $item->nilai
                ];
            })
            ->toArray();
        
        // Get catatan BK
        $catatanBkList = CatatanBimbingan::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();
        
        $totalCatatanBk = CatatanBimbingan::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->count();
        
        // Get prestasi
        $prestasiList = PrestasiSiswa::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->take(5)
            ->get();
        
        $totalPrestasi = PrestasiSiswa::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->count();
        
        return view('admin.riwayat-akademik.show', compact(
            'siswa',
            'namaRombel',
            'periodeAktif',
            'rekapNilai',
            'rataRataKeseluruhan',
            'presensiTotal',
            'persentaseKehadiran',
            'ekstraList',
            'catatanBkList',
            'totalCatatanBk',
            'prestasiList',
            'totalPrestasi'
        ));
    }
    
    /**
     * Print riwayat akademik for a student
     */
    public function print(Request $request)
    {
        $nisn = $request->query('nisn');
        
        if (empty($nisn)) {
            return response('<script>alert("NISN tidak ditemukan!"); window.close();</script>');
        }
        
        $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response('<script>alert("Data siswa tidak ditemukan!"); window.close();</script>');
        }
        
        // Find active rombel from semester 1-6
        $namaRombel = '';
        for ($i = 1; $i <= 6; $i++) {
            $kolomRombel = "rombel_semester_" . $i;
            if (!empty($siswa->$kolomRombel)) {
                $namaRombel = $siswa->$kolomRombel;
            }
        }
        
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        if (!$periodeAktif) {
            $periodeAktif = (object)[
                'tahun_pelajaran' => '2024/2025',
                'semester' => 'Ganjil',
                'nama_kepala' => '',
                'nip_kepala' => ''
            ];
        }
        
        $semesterJadwal = strtolower($periodeAktif->semester);
        
        // Get rombel and wali kelas
        $rombel = null;
        $namaWaliKelas = '';
        $nipWaliKelas = '';
        if ($namaRombel) {
            $rombel = Rombel::where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                ->where('semester', $semesterJadwal)
                ->first();
            
            if ($rombel && $rombel->wali_kelas) {
                $namaWaliKelas = $rombel->wali_kelas;
                $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
                $nipWaliKelas = $guru->nip ?? '';
            }
        }
        
        // Get mapel from jadwal
        $mapelList = [];
        $agamaSiswa = $siswa->agama ?? '';
        
        if ($rombel) {
            $mapelList = $this->getMapelList($rombel->id, $periodeAktif->tahun_pelajaran, $semesterJadwal, $agamaSiswa);
        }
        
        // Calculate nilai per mapel
        $rekapNilai = [];
        $totalRataRata = 0;
        $totalMapelDinilai = 0;
        
        foreach ($mapelList as $mapel) {
            $nilaiData = Penilaian::where('nisn', $nisn)
                ->where('mapel', $mapel->nama_mapel)
                ->pluck('nilai')
                ->toArray();
            
            $totalNilai = count($nilaiData);
            $rataRata = 0;
            $predikat = '-';
            
            if ($totalNilai > 0) {
                $rataRata = round(array_sum($nilaiData) / $totalNilai, 2);
                $totalRataRata += $rataRata;
                $totalMapelDinilai++;
                
                if ($rataRata >= 85) $predikat = 'A';
                elseif ($rataRata >= 75) $predikat = 'B';
                elseif ($rataRata >= 65) $predikat = 'C';
                elseif ($rataRata > 0) $predikat = 'D';
            }
            
            $presensiMapel = $this->getPresensiPerMapel($nisn, $mapel->nama_mapel, $periodeAktif->tahun_pelajaran, $periodeAktif->semester);
            
            $rekapNilai[] = [
                'nama_mapel' => $mapel->nama_mapel,
                'nama_guru' => $mapel->nama_guru,
                'total_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'predikat' => $predikat,
                'presensi' => $presensiMapel
            ];
        }
        
        $rataRataKeseluruhan = $totalMapelDinilai > 0 ? round($totalRataRata / $totalMapelDinilai, 2) : 0;
        
        // Get total presensi
        $presensiTotal = PresensiSiswa::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos
            ")
            ->first();
        
        $persentaseKehadiran = $presensiTotal->total > 0 
            ? round(($presensiTotal->hadir / $presensiTotal->total) * 100, 1) 
            : 0;
        
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
        
        // Get catatan BK
        $catatanBkList = CatatanBimbingan::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // Get prestasi
        $prestasiList = PrestasiSiswa::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get();
        
        // Get tanggal bagi raport from settings
        $tanggalBagiRaport = now()->format('d F Y');
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
        
        return view('admin.riwayat-akademik.print', compact(
            'siswa',
            'namaRombel',
            'periodeAktif',
            'rekapNilai',
            'rataRataKeseluruhan',
            'presensiTotal',
            'persentaseKehadiran',
            'ekstraList',
            'catatanBkList',
            'prestasiList',
            'namaWaliKelas',
            'nipWaliKelas',
            'tanggalBagiRaport'
        ));
    }
    
    /**
     * Print riwayat akademik for all students in a rombel
     */
    public function printAll(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (empty($rombelId) || empty($tahun) || empty($semester)) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Data rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get wali kelas info
        $namaWaliKelas = $rombel->wali_kelas ?? '';
        $nipWaliKelas = '';
        if ($namaWaliKelas) {
            $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
            $nipWaliKelas = $guru->nip ?? '';
        }
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        if (!$periodeAktif) {
            $periodeAktif = (object)[
                'tahun_pelajaran' => $tahun,
                'semester' => $semester,
                'nama_kepala' => '',
                'nip_kepala' => ''
            ];
        }
        
        // Get tanggal bagi raport
        $tanggalBagiRaport = now()->format('d F Y');
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
        
        // Get all students in rombel
        $tahunAjaran = explode('/', $tahun);
        $tahunAwal = intval($tahunAjaran[0]);
        $semesterLower = strtolower($semester);
        
        $siswaList = Siswa::where(function($query) use ($rombel, $tahunAwal, $semesterLower) {
            if ($semesterLower === 'ganjil') {
                $query->where(function($q) use ($rombel, $tahunAwal) {
                    $q->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombel->nama_rombel);
                })->orWhere(function($q) use ($rombel, $tahunAwal) {
                    $q->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombel->nama_rombel);
                })->orWhere(function($q) use ($rombel, $tahunAwal) {
                    $q->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombel->nama_rombel);
                });
            } else {
                $query->where(function($q) use ($rombel, $tahunAwal) {
                    $q->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombel->nama_rombel);
                })->orWhere(function($q) use ($rombel, $tahunAwal) {
                    $q->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombel->nama_rombel);
                })->orWhere(function($q) use ($rombel, $tahunAwal) {
                    $q->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombel->nama_rombel);
                });
            }
        })->orderBy('nama')->get();
        
        if ($siswaList->isEmpty()) {
            return response('<script>alert("Tidak ada siswa dalam rombel ini!"); window.close();</script>');
        }
        
        // Collect all student data
        $allStudentsData = [];
        foreach ($siswaList as $siswa) {
            $studentData = $this->getSiswaAkademikData($siswa, $rombelId, $periodeAktif);
            $allStudentsData[] = $studentData;
        }
        
        return view('admin.riwayat-akademik.print-all', compact(
            'rombel',
            'periodeAktif',
            'namaWaliKelas',
            'nipWaliKelas',
            'tanggalBagiRaport',
            'allStudentsData'
        ));
    }
    
    /**
     * Get full akademik data for a student
     */
    private function getSiswaAkademikData($siswa, $rombelId, $periodeAktif)
    {
        $nisn = $siswa->nisn;
        $semesterJadwal = strtolower($periodeAktif->semester);
        
        // Get mapel list
        $mapelList = $this->getMapelList($rombelId, $periodeAktif->tahun_pelajaran, $semesterJadwal, $siswa->agama ?? '');
        
        // Calculate nilai per mapel
        $rekapNilai = [];
        $totalRataRata = 0;
        $totalMapelDinilai = 0;
        
        foreach ($mapelList as $mapel) {
            $nilaiData = Penilaian::where('nisn', $nisn)
                ->where('mapel', $mapel->nama_mapel)
                ->pluck('nilai')->toArray();
            
            $totalNilai = count($nilaiData);
            $rataRata = 0;
            $predikat = '-';
            
            if ($totalNilai > 0) {
                $rataRata = round(array_sum($nilaiData) / $totalNilai, 2);
                $totalRataRata += $rataRata;
                $totalMapelDinilai++;
                
                if ($rataRata >= 85) $predikat = 'A';
                elseif ($rataRata >= 75) $predikat = 'B';
                elseif ($rataRata >= 65) $predikat = 'C';
                elseif ($rataRata > 0) $predikat = 'D';
            }
            
            $presensiMapel = $this->getPresensiPerMapel($nisn, $mapel->nama_mapel, $periodeAktif->tahun_pelajaran, $periodeAktif->semester);
            
            $rekapNilai[] = [
                'nama_mapel' => $mapel->nama_mapel,
                'nama_guru' => $mapel->nama_guru,
                'total_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'predikat' => $predikat,
                'presensi' => $presensiMapel
            ];
        }
        
        $rataRataKeseluruhan = $totalMapelDinilai > 0 ? round($totalRataRata / $totalMapelDinilai, 2) : 0;
        
        // Get presensi total
        $presensiTotal = PresensiSiswa::where('nisn', $nisn)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos
            ")->first();
        
        $persentaseKehadiran = $presensiTotal->total > 0 
            ? round(($presensiTotal->hadir / $presensiTotal->total) * 100, 1) : 0;
        
        // Get ekstrakurikuler
        $ekstraList = AnggotaEkstrakurikuler::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->with('ekstrakurikuler')->get()
            ->map(fn($item) => [
                'nama_ekstrakurikuler' => $item->ekstrakurikuler->nama_ekstrakurikuler ?? '',
                'nilai' => $item->nilai
            ])->toArray();
        
        // Get prestasi
        $prestasiList = PrestasiSiswa::where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('tanggal_pelaksanaan', 'desc')->get();
        
        return [
            'siswa' => $siswa,
            'rekapNilai' => $rekapNilai,
            'rataRataKeseluruhan' => $rataRataKeseluruhan,
            'presensiTotal' => $presensiTotal,
            'persentaseKehadiran' => $persentaseKehadiran,
            'ekstraList' => $ekstraList,
            'prestasiList' => $prestasiList
        ];
    }
    
    /**
     * Get mapel list from jadwal
     */
    private function getMapelList($rombelId, $tahunPelajaran, $semester, $agamaSiswa)
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
            ->select('mp.id', 'mp.nama_mapel', 'jp.nama_guru')
            ->distinct()
            ->orderBy('mp.nama_mapel')
            ->get();
    }
    
    /**
     * Get presensi per mapel
     */
    private function getPresensiPerMapel($nisn, $mapel, $tahun, $semester)
    {
        $result = PresensiSiswa::where('nisn', $nisn)
            ->where('mata_pelajaran', $mapel)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos
            ")
            ->first();
        
        $total = $result->total ?? 0;
        $hadir = $result->hadir ?? 0;
        
        return [
            'total' => $total,
            'hadir' => $hadir,
            'dispen' => $result->dispen ?? 0,
            'izin' => $result->izin ?? 0,
            'sakit' => $result->sakit ?? 0,
            'alfa' => $result->alfa ?? 0,
            'bolos' => $result->bolos ?? 0,
            'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0
        ];
    }
    
    /**
     * Get predikat for nilai
     */
    public static function getPredikat($nilai)
    {
        if ($nilai >= 85) return ['predikat' => 'A', 'keterangan' => 'Sangat Baik', 'color' => '#10b981'];
        if ($nilai >= 75) return ['predikat' => 'B', 'keterangan' => 'Baik', 'color' => '#3b82f6'];
        if ($nilai >= 65) return ['predikat' => 'C', 'keterangan' => 'Cukup', 'color' => '#f59e0b'];
        return ['predikat' => 'D', 'keterangan' => 'Perlu Improvement', 'color' => '#ef4444'];
    }
    
    /**
     * Get nilai ekstra color
     */
    public static function getNilaiEkstraColor($nilai)
    {
        switch ($nilai) {
            case 'A': return ['bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Sangat Baik'];
            case 'B': return ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'Baik'];
            case 'C': return ['bg' => '#fef9c3', 'text' => '#a16207', 'label' => 'Cukup'];
            case 'D': return ['bg' => '#fee2e2', 'text' => '#dc2626', 'label' => 'Kurang'];
            default: return ['bg' => '#f3f4f6', 'text' => '#6b7280', 'label' => 'Belum Dinilai'];
        }
    }
}
