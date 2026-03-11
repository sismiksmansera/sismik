<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\DataPeriodik;

class SiswaBimbinganController extends Controller
{
    /**
     * Display students under BK guidance
     */
    public function index(Request $request)
    {
        // Get logged-in Guru BK
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_bk_id = $guruBK->id;
        $nama_guru_bk = $guruBK->nama;
        $nip_guru = $guruBK->nip;

        // Get active period with caching (5 minutes)
        $periodik = Cache::remember('data_periodik_aktif', 300, function() {
            return DataPeriodik::where('aktif', 'Ya')->first();
        });
        $tahun_pelajaran_aktif = $periodik->tahun_pelajaran ?? '2025/2026';
        $semester_aktif = $periodik->semester ?? 'Ganjil';

        // Get tahun pelajaran list with caching (1 hour)
        $tahun_pelajaran_list = Cache::remember('tahun_pelajaran_list', 3600, function() {
            $list = DB::table('rombel')
                ->select('tahun_pelajaran')
                ->distinct()
                ->orderBy('tahun_pelajaran', 'DESC')
                ->pluck('tahun_pelajaran')
                ->toArray();
            
            if (empty($list)) {
                for ($year = 2020; $year <= 2030; $year++) {
                    $list[] = $year . "/" . ($year + 1);
                }
            }
            return $list;
        });

        // Handle filter
        $selected_tahun = $tahun_pelajaran_aktif;
        $selected_semester = $semester_aktif;

        if ($request->isMethod('post') && $request->has('filter_tahun_semester')) {
            $selected_tahun = $request->input('tahun_pelajaran', $tahun_pelajaran_aktif);
            $selected_semester = $request->input('semester', $semester_aktif);

            if (!in_array($selected_tahun, $tahun_pelajaran_list)) {
                $selected_tahun = $tahun_pelajaran_aktif;
            }

            if (!in_array($selected_semester, ['Ganjil', 'Genap'])) {
                $selected_semester = $semester_aktif;
            }
        }

        // Get students under BK guidance
        $siswa_bimbingan = [];
        $total_siswa = 0;
        $rombel_list = [];
        $rombel_counts = [];
        $kelas_counts = ['10' => 0, '11' => 0, '12' => 0];

        try {
            $students = DB::table('siswa as s')
                ->select(
                    's.id', 's.nis', 's.nisn', 's.nama', 's.jk', 's.agama',
                    's.tempat_lahir', 's.tgl_lahir', 's.nama_rombel', 's.angkatan_masuk',
                    's.asal_sekolah', 's.cita_cita', 's.harapan', 's.email', 's.nohp_siswa',
                    's.nama_bapak', 's.nama_ibu', 's.pekerjaan_bapak', 's.pekerjaan_ibu',
                    's.nohp_bapak', 's.nohp_ibu',
                    's.rombel_semester_1', 's.rombel_semester_2', 's.rombel_semester_3',
                    's.rombel_semester_4', 's.rombel_semester_5', 's.rombel_semester_6',
                    's.bk_semester_1', 's.bk_semester_2', 's.bk_semester_3',
                    's.bk_semester_4', 's.bk_semester_5', 's.bk_semester_6',
                    's.foto'
                )
                ->where(function($q) use ($nama_guru_bk) {
                    $q->where('s.bk_semester_1', $nama_guru_bk)
                      ->orWhere('s.bk_semester_2', $nama_guru_bk)
                      ->orWhere('s.bk_semester_3', $nama_guru_bk)
                      ->orWhere('s.bk_semester_4', $nama_guru_bk)
                      ->orWhere('s.bk_semester_5', $nama_guru_bk)
                      ->orWhere('s.bk_semester_6', $nama_guru_bk);
                })
                ->orderBy('s.nama', 'ASC')
                ->get();

            foreach ($students as $siswa) {
                $semester_siswa_aktif = $this->calculateActiveSemester(
                    $siswa->angkatan_masuk,
                    $selected_tahun,
                    $selected_semester
                );

                // Check if BK teacher matches for active semester
                $bk_field = 'bk_semester_' . $semester_siswa_aktif;
                $bk_aktif = $siswa->$bk_field ?? '';

                if ($bk_aktif === $nama_guru_bk) {
                    // Get active rombel
                    $rombel_field = 'rombel_semester_' . $semester_siswa_aktif;
                    $rombel_aktif = $siswa->$rombel_field ?? '';

                    if (!empty($rombel_aktif) && !in_array($rombel_aktif, $rombel_list)) {
                        $rombel_list[] = $rombel_aktif;
                    }

                    if (!empty($rombel_aktif)) {
                        if (!isset($rombel_counts[$rombel_aktif])) {
                            $rombel_counts[$rombel_aktif] = 0;
                        }
                        $rombel_counts[$rombel_aktif]++;
                    }

                    // Determine kelas
                    if (in_array($semester_siswa_aktif, [1, 2])) {
                        $kelas = '10';
                        $kelas_counts['10']++;
                    } elseif (in_array($semester_siswa_aktif, [3, 4])) {
                        $kelas = '11';
                        $kelas_counts['11']++;
                    } else {
                        $kelas = '12';
                        $kelas_counts['12']++;
                    }

                    $siswa->semester_aktif = $semester_siswa_aktif;
                    $siswa->rombel_aktif = $rombel_aktif;
                    $siswa->bk_aktif = $bk_aktif;
                    $siswa->kelas = $kelas;

                    $siswa_bimbingan[] = $siswa;
                }
            }

            $total_siswa = count($siswa_bimbingan);
            sort($rombel_list);

        } catch (\Exception $e) {
            \Log::error("Error fetching siswa bimbingan: " . $e->getMessage());
        }

        // Calculate status bimbingan statistics
        $status_bimbingan_stats = [
            'Belum Ditangani' => 0,
            'Dalam Proses' => 0,
            'Selesai' => 0,
            'Belum Ada Catatan' => 0
        ];

        try {
            $nisn_list = collect($siswa_bimbingan)->pluck('nisn')->toArray();

            if (!empty($nisn_list)) {
                // Count total status bimbingan
                $status_counts = DB::table('catatan_bimbingan as cb')
                    ->select(
                        DB::raw("CASE 
                            WHEN cb.status = 'Belum' THEN 'Belum Ditangani'
                            WHEN cb.status = 'Proses' THEN 'Dalam Proses'
                            WHEN cb.status = 'Selesai' THEN 'Selesai'
                            ELSE cb.status
                        END as status_normalized"),
                        DB::raw('COUNT(cb.id) as jumlah_catatan')
                    )
                    ->whereIn('cb.nisn', $nisn_list)
                    ->where('cb.tahun_pelajaran', $selected_tahun)
                    ->where('cb.semester', $selected_semester)
                    ->whereIn('cb.status', ['Belum', 'Proses', 'Selesai'])
                    ->groupBy('cb.status')
                    ->get();

                foreach ($status_counts as $row) {
                    $status = $row->status_normalized;
                    $jumlah = $row->jumlah_catatan;
                    if (isset($status_bimbingan_stats[$status])) {
                        $status_bimbingan_stats[$status] += $jumlah;
                    }
                }

                // Count students with no catatan
                $no_catatan_count = DB::table('siswa as s')
                    ->leftJoin('catatan_bimbingan as cb', function($join) use ($selected_tahun, $selected_semester) {
                        $join->on('s.nisn', '=', 'cb.nisn')
                             ->where('cb.tahun_pelajaran', '=', $selected_tahun)
                             ->where('cb.semester', '=', $selected_semester);
                    })
                    ->whereIn('s.nisn', $nisn_list)
                    ->where(function($q) use ($nama_guru_bk) {
                        $q->where('s.bk_semester_1', $nama_guru_bk)
                          ->orWhere('s.bk_semester_2', $nama_guru_bk)
                          ->orWhere('s.bk_semester_3', $nama_guru_bk)
                          ->orWhere('s.bk_semester_4', $nama_guru_bk)
                          ->orWhere('s.bk_semester_5', $nama_guru_bk)
                          ->orWhere('s.bk_semester_6', $nama_guru_bk);
                    })
                    ->whereNull('cb.id')
                    ->count();

                $status_bimbingan_stats['Belum Ada Catatan'] = $no_catatan_count;
            }
        } catch (\Exception $e) {
            \Log::error("Error calculating status stats: " . $e->getMessage());
        }

        // Group students by rombel
        $siswa_per_rombel = [];
        foreach ($siswa_bimbingan as $siswa) {
            $rombel = !empty($siswa->rombel_aktif) ? $siswa->rombel_aktif : 'Belum diatur';
            if (!isset($siswa_per_rombel[$rombel])) {
                $siswa_per_rombel[$rombel] = [];
            }
            $siswa_per_rombel[$rombel][] = $siswa;
        }
        ksort($siswa_per_rombel);

        return view('guru-bk.siswa-bimbingan', compact(
            'siswa_per_rombel',
            'total_siswa',
            'rombel_list',
            'rombel_counts',
            'kelas_counts',
            'status_bimbingan_stats',
            'tahun_pelajaran_list',
            'selected_tahun',
            'selected_semester',
            'tahun_pelajaran_aktif',
            'semester_aktif',
            'nama_guru_bk'
        ));
    }

    /**
     * Helper: Calculate active semester for student
     */
    private function calculateActiveSemester($angkatan, $tahun_pelajaran_aktif, $semester_aktif)
    {
        if (empty($angkatan) || empty($tahun_pelajaran_aktif) || empty($semester_aktif)) {
            return 1;
        }

        $tahun_parts = explode('/', $tahun_pelajaran_aktif);
        $tahun_mulai = intval($tahun_parts[0]);
        $angkatan_int = intval($angkatan);
        $selisih_tahun = $tahun_mulai - $angkatan_int;

        if ($selisih_tahun == 0) {
            return ($semester_aktif == 'Ganjil') ? 1 : 2;
        } elseif ($selisih_tahun == 1) {
            return ($semester_aktif == 'Ganjil') ? 3 : 4;
        } elseif ($selisih_tahun == 2) {
            return ($semester_aktif == 'Ganjil') ? 5 : 6;
        }

        return 1;
    }
}
