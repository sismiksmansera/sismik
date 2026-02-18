<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\PiketKbm;
use App\Services\EffectiveDateService;

class TugasTambahanController extends Controller
{
    public function index(Request $request)
    {
        // Get logged in guru
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }
        $guruNama = $guru->nama;

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '2024/2025';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        // Parse tahun pelajaran
        $tahunAwal = (int) explode('/', $tahunPelajaran)[0];
        $tahunAwalMinus1 = $tahunAwal - 1;
        $tahunAwalMinus2 = $tahunAwal - 2;

        // 1. PEMBINA EKSTRAKURIKULER
        $tugasPembina = [];
        $ekstrakurikuler = DB::table('ekstrakurikuler')
            ->where(function($q) use ($guruNama) {
                $q->where('pembina_1', $guruNama)
                  ->orWhere('pembina_2', $guruNama)
                  ->orWhere('pembina_3', $guruNama);
            })
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semesterAktif)
            ->orderBy('nama_ekstrakurikuler', 'asc')
            ->get();

        foreach ($ekstrakurikuler as $ekstra) {
            // Count members
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
                ->count();

            // Determine position
            $posisi = '';
            if ($ekstra->pembina_1 == $guruNama) $posisi = 'Pembina Utama';
            elseif ($ekstra->pembina_2 == $guruNama) $posisi = 'Pembina Kedua';
            elseif ($ekstra->pembina_3 == $guruNama) $posisi = 'Pembina Ketiga';

            $tugasPembina[] = [
                'id' => $ekstra->id,
                'nama' => $ekstra->nama_ekstrakurikuler,
                'posisi' => $posisi,
                'jumlah_anggota' => $jumlahAnggota,
                'jumlah_prestasi' => $jumlahPrestasi,
                'semester' => $ekstra->semester,
            ];
        }

        // 2. WALI KELAS
        $tugasWaliKelas = [];
        $rombelList = DB::table('rombel')
            ->where('wali_kelas', $guruNama)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semesterAktif)
            ->orderBy('nama_rombel', 'asc')
            ->get();

        foreach ($rombelList as $rombel) {
            $namaRombel = $rombel->nama_rombel;

            // Count siswa dynamically
            if (strtolower($semesterAktif) == 'ganjil') {
                $jumlahSiswa = DB::table('siswa')
                    ->where(function($q) use ($tahunAwal, $tahunAwalMinus1, $tahunAwalMinus2, $namaRombel) {
                        $q->where(function($q2) use ($tahunAwal, $namaRombel) {
                            $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $namaRombel);
                        })
                        ->orWhere(function($q2) use ($tahunAwalMinus1, $namaRombel) {
                            $q2->where('angkatan_masuk', $tahunAwalMinus1)->where('rombel_semester_3', $namaRombel);
                        })
                        ->orWhere(function($q2) use ($tahunAwalMinus2, $namaRombel) {
                            $q2->where('angkatan_masuk', $tahunAwalMinus2)->where('rombel_semester_5', $namaRombel);
                        });
                    })
                    ->count();
            } else {
                $jumlahSiswa = DB::table('siswa')
                    ->where(function($q) use ($tahunAwal, $tahunAwalMinus1, $tahunAwalMinus2, $namaRombel) {
                        $q->where(function($q2) use ($tahunAwal, $namaRombel) {
                            $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $namaRombel);
                        })
                        ->orWhere(function($q2) use ($tahunAwalMinus1, $namaRombel) {
                            $q2->where('angkatan_masuk', $tahunAwalMinus1)->where('rombel_semester_4', $namaRombel);
                        })
                        ->orWhere(function($q2) use ($tahunAwalMinus2, $namaRombel) {
                            $q2->where('angkatan_masuk', $tahunAwalMinus2)->where('rombel_semester_6', $namaRombel);
                        });
                    })
                    ->count();
            }

            // Count prestasi
            $jumlahPrestasi = DB::table('prestasi_siswa')
                ->where('sumber_prestasi', 'rombel')
                ->where('sumber_id', $rombel->id)
                ->where('tahun_pelajaran', $tahunPelajaran)
                ->where('semester', $semesterAktif)
                ->count();

            $tugasWaliKelas[] = [
                'id' => $rombel->id,
                'nama' => $rombel->nama_rombel,
                'tingkat' => $rombel->tingkat ?? '',
                'jumlah_siswa' => $jumlahSiswa,
                'jumlah_prestasi' => $jumlahPrestasi,
            ];
        }

        // 3. GURU WALI - Siswa yang dibimbing oleh guru ini
        $tugasGuruWali = [];
        
        // Determine which guru_wali_sem column to check based on semester
        // Semester 1,3,5 = Ganjil, Semester 2,4,6 = Genap
        $guruWaliData = [];
        
        if (strtolower($semesterAktif) == 'ganjil') {
            // Ganjil: check semester 1, 3, 5
            $kelas10 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwal)
                ->where('guru_wali_sem_1', $guruNama)
                ->count();
            if ($kelas10 > 0) {
                $guruWaliData[] = ['tingkat' => 'X', 'semester' => 1, 'jumlah' => $kelas10];
            }
            
            $kelas11 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus1)
                ->where('guru_wali_sem_3', $guruNama)
                ->count();
            if ($kelas11 > 0) {
                $guruWaliData[] = ['tingkat' => 'XI', 'semester' => 3, 'jumlah' => $kelas11];
            }
            
            $kelas12 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus2)
                ->where('guru_wali_sem_5', $guruNama)
                ->count();
            if ($kelas12 > 0) {
                $guruWaliData[] = ['tingkat' => 'XII', 'semester' => 5, 'jumlah' => $kelas12];
            }
        } else {
            // Genap: check semester 2, 4, 6
            $kelas10 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwal)
                ->where('guru_wali_sem_2', $guruNama)
                ->count();
            if ($kelas10 > 0) {
                $guruWaliData[] = ['tingkat' => 'X', 'semester' => 2, 'jumlah' => $kelas10];
            }
            
            $kelas11 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus1)
                ->where('guru_wali_sem_4', $guruNama)
                ->count();
            if ($kelas11 > 0) {
                $guruWaliData[] = ['tingkat' => 'XI', 'semester' => 4, 'jumlah' => $kelas11];
            }
            
            $kelas12 = DB::table('siswa')
                ->where('angkatan_masuk', $tahunAwalMinus2)
                ->where('guru_wali_sem_6', $guruNama)
                ->count();
            if ($kelas12 > 0) {
                $guruWaliData[] = ['tingkat' => 'XII', 'semester' => 6, 'jumlah' => $kelas12];
            }
        }
        
        // Total siswa bimbingan
        $totalSiswaBimbingan = array_sum(array_column($guruWaliData, 'jumlah'));

        // 4. PIKET KBM - Check if guru is on duty today (respects Testing Date setting)
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $hariIni = $effectiveDate['hari'];
        $isTesting = $effectiveDate['is_testing'];
        $piketHariIni = PiketKbm::where('hari', $hariIni)
            ->where('guru_id', $guru->id)
            ->where('tipe_guru', 'guru')
            ->first();

        // Get all guru piket for today (to show colleagues)
        $semuaPiketHariIni = [];
        if ($piketHariIni) {
            $semuaPiketHariIni = PiketKbm::where('hari', $hariIni)
                ->orderBy('created_at')
                ->get();
        }
        
        // 5. TUGAS TAMBAHAN LAINNYA
        $tugasTambahanLain = DB::table('tugas_tambahan_guru as t')
            ->join('jenis_tugas_tambahan_lain as j', 't.jenis_tugas_id', '=', 'j.id')
            ->where('t.tipe_guru', 'guru')
            ->where('t.guru_id', $guru->id)
            ->select('t.*', 'j.nama_tugas as jenis_nama', 'j.deskripsi as jenis_deskripsi')
            ->orderBy('j.nama_tugas', 'ASC')
            ->get();

        // Enrich tugas tambahan with extra data
        foreach ($tugasTambahanLain as $item) {
            $item->extra_count = null;
            $item->extra_route = null;
            // If this is "Koordinator Ekstrakurikuler", count ekstra in active period
            if (stripos($item->jenis_nama, 'koordinator ekstrakurikuler') !== false) {
                $item->extra_count = DB::table('ekstrakurikuler')
                    ->where('tahun_pelajaran', $tahunPelajaran)
                    ->where('semester', $semesterAktif)
                    ->count();
                $item->extra_route = 'guru.koordinator-ekstra.index';
                $item->extra_label = 'Ekstrakurikuler';
                $item->extra_icon = 'fa-futbol';
            }
        }

        $totalTugas = count($tugasPembina) + count($tugasWaliKelas) + ($totalSiswaBimbingan > 0 ? 1 : 0) + ($piketHariIni ? 1 : 0) + count($tugasTambahanLain);

        return view('guru.tugas-tambahan', compact(
            'guru',
            'tahunPelajaran',
            'semesterAktif',
            'tugasPembina',
            'tugasWaliKelas',
            'guruWaliData',
            'totalSiswaBimbingan',
            'totalTugas',
            'piketHariIni',
            'hariIni',
            'semuaPiketHariIni',
            'tugasTambahanLain'
        ));
    }
}
