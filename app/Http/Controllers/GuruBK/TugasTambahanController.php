<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TugasTambahanController extends Controller
{
    /**
     * Display tugas tambahan for Guru BK
     */
    public function index()
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_nama = $guruBK->nama;

        // Get active period
        $periodeAktif = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();

        if (!$periodeAktif) {
            $periodeAktif = (object) [
                'tahun_pelajaran' => date('Y') . '/' . (date('Y') + 1),
                'semester' => 'Ganjil'
            ];
        }

        // 1. PEMBINA EKSTRAKURIKULER
        $tugasPembina = $this->getTugasPembina($guru_nama, $periodeAktif);

        // 2. WALI KELAS
        $tugasWaliKelas = $this->getTugasWaliKelas($guru_nama, $periodeAktif);

        // 3. GURU WALI - Siswa yang dibimbing oleh guru ini
        $guruWaliData = $this->getGuruWaliData($guru_nama, $periodeAktif);
        $totalSiswaBimbinganWali = array_sum(array_column($guruWaliData, 'jumlah'));

        // 4. TUGAS TAMBAHAN LAINNYA
        $tugasTambahanLain = DB::table('tugas_tambahan_guru as t')
            ->join('jenis_tugas_tambahan_lain as j', 't.jenis_tugas_id', '=', 'j.id')
            ->where('t.tipe_guru', 'guru_bk')
            ->where('t.guru_id', $guruBK->id)
            ->select('t.*', 'j.nama_tugas as jenis_nama', 'j.deskripsi as jenis_deskripsi')
            ->orderBy('j.nama_tugas', 'ASC')
            ->get();

        // Enrich tugas tambahan with extra data
        foreach ($tugasTambahanLain as $item) {
            $item->extra_count = null;
            $item->extra_route = null;
            // If this is "Koordinator Ekstrakurikuler" (or variant spelling), count ekstra in active period
            if (stripos($item->jenis_nama, 'ekstrakurikuler') !== false) {
                $item->extra_count = DB::table('ekstrakurikuler')
                    ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                    ->where('semester', $periodeAktif->semester)
                    ->count();
                $item->extra_route = 'guru_bk.koordinator-ekstra.index';
                $item->extra_label = 'Ekstrakurikuler';
                $item->extra_icon = 'fa-futbol';
            }
        }

        // Total tugas
        $totalTugas = count($tugasPembina) + count($tugasWaliKelas) + ($totalSiswaBimbinganWali > 0 ? 1 : 0) + count($tugasTambahanLain);

        return view('guru-bk.tugas-tambahan', compact(
            'tugasPembina',
            'tugasWaliKelas',
            'guruWaliData',
            'totalSiswaBimbinganWali',
            'totalTugas',
            'periodeAktif',
            'tugasTambahanLain'
        ));
    }

    /**
     * Get pembina ekstrakurikuler assignments
     */
    private function getTugasPembina($guru_nama, $periodeAktif)
    {
        $tugasPembina = [];

        $ekstrakurikulers = DB::table('ekstrakurikuler')
            ->where(function ($query) use ($guru_nama) {
                $query->where('pembina_1', $guru_nama)
                    ->orWhere('pembina_2', $guru_nama)
                    ->orWhere('pembina_3', $guru_nama);
            })
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('nama_ekstrakurikuler', 'ASC')
            ->get();

        foreach ($ekstrakurikulers as $ekstra) {
            // Count anggota
            $jumlahAnggota = DB::table('anggota_ekstrakurikuler')
                ->where('ekstrakurikuler_id', $ekstra->id)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->count();

            // Count prestasi
            $jumlahPrestasi = DB::table('prestasi_siswa')
                ->where('sumber_prestasi', 'ekstrakurikuler')
                ->where('sumber_id', $ekstra->id)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->distinct()
                ->count(DB::raw("CONCAT(nama_kompetisi, juara, jenjang, tanggal_pelaksanaan)"));

            // Determine position
            $posisi = '';
            if ($ekstra->pembina_1 == $guru_nama) {
                $posisi = 'Pembina Utama';
            } elseif ($ekstra->pembina_2 == $guru_nama) {
                $posisi = 'Pembina Kedua';
            } elseif ($ekstra->pembina_3 == $guru_nama) {
                $posisi = 'Pembina Ketiga';
            }

            $ekstra->jumlah_anggota = $jumlahAnggota;
            $ekstra->jumlah_prestasi = $jumlahPrestasi;
            $ekstra->posisi_pembina = $posisi;

            $tugasPembina[] = $ekstra;
        }

        return $tugasPembina;
    }

    /**
     * Get wali kelas assignments
     */
    private function getTugasWaliKelas($guru_nama, $periodeAktif)
    {
        $tugasWaliKelas = [];

        $rombels = DB::table('rombel')
            ->where('wali_kelas', $guru_nama)
            ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
            ->where('semester', $periodeAktif->semester)
            ->orderBy('nama_rombel', 'ASC')
            ->get();

        foreach ($rombels as $rombel) {
            // Calculate jumlah siswa based on semester
            $tahun_ajaran = explode('/', $periodeAktif->tahun_pelajaran);
            $tahun_awal = intval($tahun_ajaran[0]);

            if ($periodeAktif->semester == 'Ganjil') {
                $jumlahSiswa = DB::table('siswa')
                    ->where(function ($query) use ($tahun_awal, $rombel) {
                        $query->where([['angkatan_masuk', $tahun_awal], ['rombel_semester_1', $rombel->nama_rombel]])
                            ->orWhere([['angkatan_masuk', $tahun_awal - 1], ['rombel_semester_3', $rombel->nama_rombel]])
                            ->orWhere([['angkatan_masuk', $tahun_awal - 2], ['rombel_semester_5', $rombel->nama_rombel]]);
                    })
                    ->count();
            } else {
                $jumlahSiswa = DB::table('siswa')
                    ->where(function ($query) use ($tahun_awal, $rombel) {
                        $query->where([['angkatan_masuk', $tahun_awal], ['rombel_semester_2', $rombel->nama_rombel]])
                            ->orWhere([['angkatan_masuk', $tahun_awal - 1], ['rombel_semester_4', $rombel->nama_rombel]])
                            ->orWhere([['angkatan_masuk', $tahun_awal - 2], ['rombel_semester_6', $rombel->nama_rombel]]);
                    })
                    ->count();
            }

            // Count prestasi for rombel
            $jumlahPrestasi = DB::table('prestasi_siswa')
                ->where('sumber_prestasi', 'rombel')
                ->where('sumber_id', $rombel->id)
                ->where('tahun_pelajaran', $periodeAktif->tahun_pelajaran)
                ->where('semester', $periodeAktif->semester)
                ->distinct()
                ->count(DB::raw("CONCAT(nama_kompetisi, juara, jenjang, tanggal_pelaksanaan)"));

            $rombel->jumlah_siswa = $jumlahSiswa;
            $rombel->jumlah_prestasi = $jumlahPrestasi;

            $tugasWaliKelas[] = $rombel;
        }

        return $tugasWaliKelas;
    }

    /**
     * Get guru wali data - students assigned to this guru as guru wali
     */
    private function getGuruWaliData($guru_nama, $periodeAktif)
    {
        $guruWaliData = [];

        $tahun_ajaran = explode('/', $periodeAktif->tahun_pelajaran);
        $tahunAwal = intval($tahun_ajaran[0]);
        $tahunAwalMinus1 = $tahunAwal - 1;
        $tahunAwalMinus2 = $tahunAwal - 2;

        if (strtolower($periodeAktif->semester) == 'ganjil') {
            // Kelas X - Semester 1
            $kelas10 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwal)
                ->where('guru_wali_sem_1', $guru_nama)
                ->count();
            if ($kelas10 > 0) {
                $guruWaliData[] = ['tingkat' => 'X', 'semester' => 1, 'jumlah' => $kelas10];
            }

            // Kelas XI - Semester 3
            $kelas11 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus1)
                ->where('guru_wali_sem_3', $guru_nama)
                ->count();
            if ($kelas11 > 0) {
                $guruWaliData[] = ['tingkat' => 'XI', 'semester' => 3, 'jumlah' => $kelas11];
            }

            // Kelas XII - Semester 5
            $kelas12 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus2)
                ->where('guru_wali_sem_5', $guru_nama)
                ->count();
            if ($kelas12 > 0) {
                $guruWaliData[] = ['tingkat' => 'XII', 'semester' => 5, 'jumlah' => $kelas12];
            }
        } else {
            // Kelas X - Semester 2
            $kelas10 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwal)
                ->where('guru_wali_sem_2', $guru_nama)
                ->count();
            if ($kelas10 > 0) {
                $guruWaliData[] = ['tingkat' => 'X', 'semester' => 2, 'jumlah' => $kelas10];
            }

            // Kelas XI - Semester 4
            $kelas11 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus1)
                ->where('guru_wali_sem_4', $guru_nama)
                ->count();
            if ($kelas11 > 0) {
                $guruWaliData[] = ['tingkat' => 'XI', 'semester' => 4, 'jumlah' => $kelas11];
            }

            // Kelas XII - Semester 6
            $kelas12 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus2)
                ->where('guru_wali_sem_6', $guru_nama)
                ->count();
            if ($kelas12 > 0) {
                $guruWaliData[] = ['tingkat' => 'XII', 'semester' => 6, 'jumlah' => $kelas12];
            }
        }

        return $guruWaliData;
    }
}
