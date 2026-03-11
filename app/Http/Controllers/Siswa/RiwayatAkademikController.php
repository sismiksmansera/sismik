<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use Carbon\Carbon;

class RiwayatAkademikController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        $semesterJadwal = strtolower($semesterAktif);
        
        // Find siswa's rombel aktif based on semester
        $namaRombel = $this->getRombelAktif($siswa, $periodik);
        
        // Find rombel ID
        $idRombel = null;
        if ($namaRombel) {
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
            
            $idRombel = $rombel->id ?? null;
        }
        
        // Get mapel list from jadwal
        $mapelList = $this->getMapelList($idRombel, $tahunAktif, $semesterJadwal, $siswa->agama);
        
        // Get rekap nilai per mapel
        $rekapNilai = [];
        $totalRataRata = 0;
        $totalMapelDinilai = 0;
        
        foreach ($mapelList as $mapel) {
            $nilaiData = $this->getDataNilai($siswa->nisn, $mapel->nama_mapel);
            $presensiData = $this->getPresensiPerMapel($siswa->nisn, $mapel->nama_mapel, $tahunAktif, $semesterAktif);
            
            $totalNilai = count($nilaiData);
            $rataRata = 0;
            $tertinggi = 0;
            $terendah = 0;
            
            if ($totalNilai > 0) {
                $jumlah = array_sum(array_column($nilaiData, 'nilai'));
                $tertinggi = max(array_column($nilaiData, 'nilai'));
                $terendah = min(array_column($nilaiData, 'nilai'));
                $rataRata = round($jumlah / $totalNilai, 2);
                $totalRataRata += $rataRata;
                $totalMapelDinilai++;
            }
            
            $rekapNilai[] = [
                'nama_mapel' => $mapel->nama_mapel,
                'nama_guru' => $mapel->nama_guru,
                'total_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'tertinggi' => $tertinggi,
                'terendah' => $terendah,
                'presensi' => $presensiData,
                'predikat' => $this->getPredikat($rataRata),
            ];
        }
        
        $rataRataKeseluruhan = $totalMapelDinilai > 0 ? round($totalRataRata / $totalMapelDinilai, 2) : 0;
        
        // Get total presensi keseluruhan
        $totalPresensi = $this->getTotalPresensi($siswa->nisn, $tahunAktif, $semesterAktif);
        $persentaseKehadiran = $totalPresensi['total'] > 0 
            ? round(($totalPresensi['hadir'] / $totalPresensi['total']) * 100, 1) 
            : 0;
        
        // Get ekstrakurikuler
        $ekskulList = $this->getEkstrakurikuler($siswa->id, $tahunAktif, $semesterAktif);
        
        // Get catatan BK
        $catatanBkList = $this->getCatatanBk($siswa->nisn, $tahunAktif, $semesterAktif);
        $totalCatatanBk = $this->getTotalCatatanBk($siswa->nisn, $tahunAktif, $semesterAktif);
        
        // Get prestasi
        $prestasiList = $this->getPrestasi($siswa->id, $tahunAktif, $semesterAktif);
        $totalPrestasi = $this->getTotalPrestasi($siswa->id, $tahunAktif, $semesterAktif);
        
        // Grand total presensi
        $grandTotalPresensi = 0;
        $grandTotalHadir = 0;
        foreach ($rekapNilai as $nilai) {
            $grandTotalPresensi += $nilai['presensi']['total'];
            $grandTotalHadir += $nilai['presensi']['hadir'];
        }
        $grandPersentase = $grandTotalPresensi > 0 ? round(($grandTotalHadir / $grandTotalPresensi) * 100, 1) : 0;
        
        return view('siswa.riwayat-akademik', compact(
            'siswa',
            'periodik',
            'namaRombel',
            'tahunAktif',
            'semesterAktif',
            'rekapNilai',
            'rataRataKeseluruhan',
            'totalPresensi',
            'persentaseKehadiran',
            'ekskulList',
            'catatanBkList',
            'totalCatatanBk',
            'prestasiList',
            'totalPrestasi',
            'grandPersentase'
        ));
    }
    
    private function getRombelAktif($siswa, $periodik)
    {
        if (!$periodik) return null;
        
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
        return $siswa->$kolomRombel ?? null;
    }
    
    private function getMapelList($idRombel, $tahunAktif, $semesterJadwal, $agamaSiswa)
    {
        if (!$idRombel) return collect();
        
        return DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->select('mp.id as id_mapel', 'mp.nama_mapel', 'jp.nama_guru')
            ->where('jp.id_rombel', $idRombel)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->where('jp.semester', $semesterJadwal)
            ->where(function($query) use ($agamaSiswa) {
                $query->where('mp.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%')
                    ->orWhere('mp.nama_mapel', $this->getAgamaMapelName($agamaSiswa));
            })
            ->groupBy('mp.id', 'mp.nama_mapel', 'jp.nama_guru')
            ->orderBy('mp.nama_mapel')
            ->get();
    }
    
    private function getAgamaMapelName($agama)
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
    
    private function getDataNilai($nisn, $mapel)
    {
        return DB::table('penilaian')
            ->where('nisn', $nisn)
            ->where('mapel', $mapel)
            ->pluck('nilai')
            ->map(fn($v) => ['nilai' => floatval($v)])
            ->toArray();
    }
    
    private function getPresensiPerMapel($nisn, $mapel, $tahun, $semester)
    {
        $result = DB::table('presensi_siswa')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos
            ")
            ->where('nisn', $nisn)
            ->where('mata_pelajaran', $mapel)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->first();
        
        $data = [
            'total' => intval($result->total ?? 0),
            'hadir' => intval($result->hadir ?? 0),
            'dispen' => intval($result->dispen ?? 0),
            'izin' => intval($result->izin ?? 0),
            'sakit' => intval($result->sakit ?? 0),
            'alfa' => intval($result->alfa ?? 0),
            'bolos' => intval($result->bolos ?? 0),
        ];
        $data['persentase'] = $data['total'] > 0 ? round(($data['hadir'] / $data['total']) * 100, 1) : 0;
        
        return $data;
    }
    
    private function getTotalPresensi($nisn, $tahun, $semester)
    {
        $result = DB::table('presensi_siswa')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa,
                SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos
            ")
            ->where('nisn', $nisn)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->first();
        
        return [
            'total' => intval($result->total ?? 0),
            'hadir' => intval($result->hadir ?? 0),
            'dispen' => intval($result->dispen ?? 0),
            'izin' => intval($result->izin ?? 0),
            'sakit' => intval($result->sakit ?? 0),
            'alfa' => intval($result->alfa ?? 0),
            'bolos' => intval($result->bolos ?? 0),
        ];
    }
    
    private function getPredikat($nilai)
    {
        if ($nilai >= 85) return ['predikat' => 'A', 'keterangan' => 'Sangat Baik', 'color' => '#10b981'];
        if ($nilai >= 75) return ['predikat' => 'B', 'keterangan' => 'Baik', 'color' => '#3b82f6'];
        if ($nilai >= 65) return ['predikat' => 'C', 'keterangan' => 'Cukup', 'color' => '#f59e0b'];
        return ['predikat' => 'D', 'keterangan' => 'Perlu Improvement', 'color' => '#ef4444'];
    }
    
    private function getEkstrakurikuler($siswaId, $tahun, $semester)
    {
        return DB::table('anggota_ekstrakurikuler as ae')
            ->join('ekstrakurikuler as e', 'ae.ekstrakurikuler_id', '=', 'e.id')
            ->select('ae.nilai', 'e.nama_ekstrakurikuler', 'e.pembina_1')
            ->where('ae.siswa_id', $siswaId)
            ->where('ae.tahun_pelajaran', $tahun)
            ->where('ae.semester', $semester)
            ->orderBy('e.nama_ekstrakurikuler')
            ->get()
            ->map(function($item) {
                $item->nilai_info = $this->getNilaiEkstraColor($item->nilai);
                return $item;
            });
    }
    
    private function getNilaiEkstraColor($nilai)
    {
        $colors = [
            'A' => ['bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Sangat Baik'],
            'B' => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'Baik'],
            'C' => ['bg' => '#fef9c3', 'text' => '#a16207', 'label' => 'Cukup'],
            'D' => ['bg' => '#fee2e2', 'text' => '#dc2626', 'label' => 'Kurang'],
        ];
        return $colors[$nilai] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280', 'label' => 'Belum Dinilai'];
    }
    
    private function getCatatanBk($nisn, $tahun, $semester)
    {
        return DB::table('catatan_bimbingan')
            ->select('id', 'tanggal', 'jenis_bimbingan', 'masalah', 'status')
            ->where('nisn', $nisn)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();
    }
    
    private function getTotalCatatanBk($nisn, $tahun, $semester)
    {
        return DB::table('catatan_bimbingan')
            ->where('nisn', $nisn)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->count();
    }
    
    private function getPrestasi($siswaId, $tahun, $semester)
    {
        return DB::table('prestasi_siswa')
            ->select('nama_kompetisi', 'juara', 'jenjang', 'tanggal_pelaksanaan', 'penyelenggara')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->jenjang_color = $this->getJenjangColor($item->jenjang);
                return $item;
            });
    }
    
    private function getTotalPrestasi($siswaId, $tahun, $semester)
    {
        return DB::table('prestasi_siswa')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->count();
    }
    
    private function getJenjangColor($jenjang)
    {
        $colors = [
            'Kelas' => '#6b7280',
            'Sekolah' => '#3b82f6',
            'Kecamatan' => '#10b981',
            'Kabupaten' => '#8b5cf6',
            'Provinsi' => '#f59e0b',
            'Nasional' => '#ef4444',
            'Internasional' => '#ec4899',
        ];
        return $colors[$jenjang] ?? '#0ea5e9';
    }
    
    public function print()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        $semesterJadwal = strtolower($semesterAktif);
        
        // Kepala Sekolah info
        $namaKepala = $periodik->nama_kepala ?? '';
        $nipKepala = $periodik->nip_kepala ?? '';
        
        // Find siswa's rombel aktif
        $namaRombel = $this->getRombelAktif($siswa, $periodik);
        
        // Find rombel ID and Wali Kelas
        $idRombel = null;
        $namaWaliKelas = '';
        $nipWaliKelas = '';
        
        if ($namaRombel) {
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
            
            if ($rombel) {
                $idRombel = $rombel->id;
                $namaWaliKelas = $rombel->wali_kelas ?? '';
                
                // Get NIP from guru table
                if ($namaWaliKelas) {
                    $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
                    $nipWaliKelas = $guru->nip ?? '';
                }
            }
        }
        
        // Get mapel list
        $mapelList = $this->getMapelList($idRombel, $tahunAktif, $semesterJadwal, $siswa->agama);
        
        // Get rekap nilai per mapel
        $rekapNilai = [];
        $totalRataRata = 0;
        $totalMapelDinilai = 0;
        
        foreach ($mapelList as $mapel) {
            $nilaiData = $this->getDataNilai($siswa->nisn, $mapel->nama_mapel);
            $presensiData = $this->getPresensiPerMapel($siswa->nisn, $mapel->nama_mapel, $tahunAktif, $semesterAktif);
            
            $totalNilai = count($nilaiData);
            $rataRata = 0;
            
            if ($totalNilai > 0) {
                $jumlah = array_sum(array_column($nilaiData, 'nilai'));
                $rataRata = round($jumlah / $totalNilai, 2);
                $totalRataRata += $rataRata;
                $totalMapelDinilai++;
            }
            
            // Predikat
            $predikat = '-';
            if ($rataRata >= 85) $predikat = 'A';
            elseif ($rataRata >= 75) $predikat = 'B';
            elseif ($rataRata >= 65) $predikat = 'C';
            elseif ($rataRata > 0) $predikat = 'D';
            
            $rekapNilai[] = [
                'nama_mapel' => $mapel->nama_mapel,
                'nama_guru' => $mapel->nama_guru,
                'total_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'predikat' => $predikat,
                'presensi' => $presensiData,
            ];
        }
        
        $rataRataKeseluruhan = $totalMapelDinilai > 0 ? round($totalRataRata / $totalMapelDinilai, 2) : 0;
        
        // Get total presensi
        $totalPresensi = $this->getTotalPresensi($siswa->nisn, $tahunAktif, $semesterAktif);
        $persentaseKehadiran = $totalPresensi['total'] > 0 
            ? round(($totalPresensi['hadir'] / $totalPresensi['total']) * 100, 1) 
            : 0;
        
        // Get all ekstrakurikuler (no limit for print)
        $ekskulList = DB::table('anggota_ekstrakurikuler as ae')
            ->join('ekstrakurikuler as e', 'ae.ekstrakurikuler_id', '=', 'e.id')
            ->select('ae.nilai', 'e.nama_ekstrakurikuler')
            ->where('ae.siswa_id', $siswa->id)
            ->where('ae.tahun_pelajaran', $tahunAktif)
            ->where('ae.semester', $semesterAktif)
            ->orderBy('e.nama_ekstrakurikuler')
            ->get();
        
        // Get all catatan BK (no limit for print)
        $catatanBkList = DB::table('catatan_bimbingan')
            ->select('tanggal', 'jenis_bimbingan', 'masalah', 'status')
            ->where('nisn', $siswa->nisn)
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // Get all prestasi (no limit for print)
        $prestasiList = DB::table('prestasi_siswa')
            ->select('nama_kompetisi', 'juara', 'jenjang', 'tanggal_pelaksanaan')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get();
        
        return view('siswa.print-riwayat-akademik', compact(
            'siswa',
            'periodik',
            'namaRombel',
            'tahunAktif',
            'semesterAktif',
            'namaKepala',
            'nipKepala',
            'namaWaliKelas',
            'nipWaliKelas',
            'rekapNilai',
            'rataRataKeseluruhan',
            'totalPresensi',
            'persentaseKehadiran',
            'ekskulList',
            'catatanBkList',
            'prestasiList'
        ));
    }
}

