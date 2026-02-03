<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\DataPeriodik;

class RekapStatusBimbinganController extends Controller
{
    /**
     * Display rekap based on status
     */
    public function index(Request $request, $status)
    {
        // Get logged-in Guru BK
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_bk_id = $guruBK->id;
        $nama_guru_bk = $guruBK->nama;

        // Validate status
        $allowed_statuses = ['Belum Ditangani', 'Dalam Proses', 'Selesai', 'Belum Ada Catatan'];
        if (!in_array($status, $allowed_statuses)) {
            return redirect()->route('guru_bk.siswa-bimbingan')->with('error', 'Status tidak valid.');
        }

        // Get parameters
        $tahun = $request->get('tahun', '');
        $semester = $request->get('semester', '');

        // Get active period if not provided
        if (empty($tahun) || empty($semester)) {
            $periodik = DataPeriodik::where('aktif', 'Ya')->first();
            if ($periodik) {
                $tahun = $periodik->tahun_pelajaran;
                $semester = $periodik->semester;
            }
        }

        // Status icon and color mapping
        $status_config = [
            'Belum Ditangani' => ['icon' => 'fa-clock', 'color' => '#ef4444', 'bg' => 'danger'],
            'Dalam Proses' => ['icon' => 'fa-spinner', 'color' => '#f59e0b', 'bg' => 'warning'],
            'Selesai' => ['icon' => 'fa-check-circle', 'color' => '#10b981', 'bg' => 'success'],
            'Belum Ada Catatan' => ['icon' => 'fa-file-circle-exclamation', 'color' => '#6b7280', 'bg' => 'secondary'],
        ];

        $status_icon = $status_config[$status]['icon'];
        $status_color = $status_config[$status]['color'];
        $status_bg = $status_config[$status]['bg'];

        $data_rekap = [];
        $total_data = 0;
        $total_catatan = 0;

        if ($status === 'Belum Ada Catatan') {
            // Get students without any notes in this period
            $students = DB::table('siswa')
                ->where(function($q) use ($nama_guru_bk) {
                    $q->where('bk_semester_1', $nama_guru_bk)
                      ->orWhere('bk_semester_2', $nama_guru_bk)
                      ->orWhere('bk_semester_3', $nama_guru_bk)
                      ->orWhere('bk_semester_4', $nama_guru_bk)
                      ->orWhere('bk_semester_5', $nama_guru_bk)
                      ->orWhere('bk_semester_6', $nama_guru_bk);
                })
                ->whereNotIn('nisn', function($q) use ($tahun, $semester) {
                    $q->select('nisn')
                      ->from('catatan_bimbingan')
                      ->where('tahun_pelajaran', $tahun)
                      ->where('semester', $semester);
                })
                ->orderBy('nama', 'ASC')
                ->get();

            foreach ($students as $siswa) {
                $semester_aktif = $this->calculateActiveSemester($siswa->angkatan_masuk, $tahun, $semester);
                $rombel_field = 'rombel_semester_' . $semester_aktif;
                $rombel_aktif = $siswa->$rombel_field ?? '';
                
                $kelas = '10';
                if (in_array($semester_aktif, [3, 4])) $kelas = '11';
                elseif (in_array($semester_aktif, [5, 6])) $kelas = '12';

                $data_rekap[] = [
                    'type' => 'siswa',
                    'data' => $siswa,
                    'semester_aktif' => $semester_aktif,
                    'rombel_aktif' => $rombel_aktif,
                    'kelas' => $kelas,
                    'catatan' => []
                ];
            }
            $total_data = count($data_rekap);

        } else {
            // Map status for database query
            $status_db = $status;
            if ($status === 'Belum Ditangani') $status_db = 'Belum';
            elseif ($status === 'Dalam Proses') $status_db = 'Proses';

            // Get catatan with status
            $catatan_rows = DB::table('catatan_bimbingan as cb')
                ->join('siswa as s', 'cb.nisn', '=', 's.nisn')
                ->leftJoin('guru_bk as gb', 'cb.guru_bk_id', '=', 'gb.id')
                ->select(
                    'cb.*',
                    's.id as siswa_id', 's.nis', 's.nama as nama_siswa', 's.jk', 's.agama',
                    's.tempat_lahir', 's.tgl_lahir', 's.nama_rombel', 's.angkatan_masuk',
                    's.asal_sekolah', 's.foto',
                    's.rombel_semester_1', 's.rombel_semester_2', 's.rombel_semester_3',
                    's.rombel_semester_4', 's.rombel_semester_5', 's.rombel_semester_6',
                    'gb.nama as nama_guru'
                )
                ->where('cb.tahun_pelajaran', $tahun)
                ->where('cb.semester', $semester)
                ->where('cb.status', $status_db)
                ->where(function($q) use ($nama_guru_bk) {
                    $q->where('s.bk_semester_1', $nama_guru_bk)
                      ->orWhere('s.bk_semester_2', $nama_guru_bk)
                      ->orWhere('s.bk_semester_3', $nama_guru_bk)
                      ->orWhere('s.bk_semester_4', $nama_guru_bk)
                      ->orWhere('s.bk_semester_5', $nama_guru_bk)
                      ->orWhere('s.bk_semester_6', $nama_guru_bk);
                })
                ->orderBy('s.nama', 'ASC')
                ->orderBy('cb.tanggal', 'DESC')
                ->get();

            // Group by student
            $catatan_per_siswa = [];
            foreach ($catatan_rows as $catatan) {
                $nisn = $catatan->nisn;
                
                if (!isset($catatan_per_siswa[$nisn])) {
                    $semester_aktif = $this->calculateActiveSemester($catatan->angkatan_masuk, $tahun, $semester);
                    $rombel_field = 'rombel_semester_' . $semester_aktif;
                    $rombel_aktif = $catatan->$rombel_field ?? '';
                    
                    $kelas = '10';
                    if (in_array($semester_aktif, [3, 4])) $kelas = '11';
                    elseif (in_array($semester_aktif, [5, 6])) $kelas = '12';

                    $catatan_per_siswa[$nisn] = [
                        'siswa' => [
                            'id' => $catatan->siswa_id,
                            'nis' => $catatan->nis,
                            'nisn' => $catatan->nisn,
                            'nama' => $catatan->nama_siswa,
                            'jk' => $catatan->jk,
                            'agama' => $catatan->agama,
                            'foto' => $catatan->foto
                        ],
                        'semester_aktif' => $semester_aktif,
                        'rombel_aktif' => $rombel_aktif,
                        'kelas' => $kelas,
                        'catatan' => []
                    ];
                }

                // Normalize status for display
                $status_display = $this->normalizeStatus($catatan->status);

                $catatan_per_siswa[$nisn]['catatan'][] = [
                    'id' => $catatan->id,
                    'tanggal' => $catatan->tanggal,
                    'jenis_bimbingan' => $catatan->jenis_bimbingan,
                    'masalah' => $catatan->masalah,
                    'penyelesaian' => $catatan->penyelesaian,
                    'tindak_lanjut' => $catatan->tindak_lanjut,
                    'keterangan' => $catatan->keterangan,
                    'status' => $status_display,
                    'status_db' => $catatan->status,
                    'nama_guru' => $catatan->nama_guru
                ];
            }

            $data_rekap = array_values($catatan_per_siswa);
            $total_data = count($data_rekap);
            
            foreach ($data_rekap as $item) {
                $total_catatan += count($item['catatan']);
            }
        }

        return view('guru-bk.rekap-status-bimbingan', compact(
            'status',
            'status_icon',
            'status_color',
            'status_bg',
            'tahun',
            'semester',
            'nama_guru_bk',
            'data_rekap',
            'total_data',
            'total_catatan'
        ));
    }

    /**
     * Delete catatan (AJAX)
     */
    public function destroy(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $catatan = DB::table('catatan_bimbingan')
                ->where('id', $id)
                ->where('guru_bk_id', $guruBK->id)
                ->first();

            if (!$catatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan tidak ditemukan atau bukan milik Anda.'
                ]);
            }

            DB::table('catatan_bimbingan')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dihapus.',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate active semester for student
     */
    private function calculateActiveSemester($angkatan, $tahun_pelajaran, $semester)
    {
        if (empty($angkatan) || empty($tahun_pelajaran) || empty($semester)) {
            return 1;
        }

        $tahun_parts = explode('/', $tahun_pelajaran);
        $tahun_mulai = intval($tahun_parts[0]);
        $angkatan_int = intval($angkatan);
        $selisih_tahun = $tahun_mulai - $angkatan_int;

        if ($selisih_tahun == 0) {
            return ($semester == 'Ganjil') ? 1 : 2;
        } elseif ($selisih_tahun == 1) {
            return ($semester == 'Ganjil') ? 3 : 4;
        } elseif ($selisih_tahun == 2) {
            return ($semester == 'Ganjil') ? 5 : 6;
        }

        return 1;
    }

    /**
     * Normalize status for display
     */
    private function normalizeStatus($status)
    {
        if (empty($status)) {
            return 'Belum Ditangani';
        }

        $status_lower = strtolower(trim($status));

        if ($status_lower === 'proses' || $status_lower === 'dalam proses') {
            return 'Dalam Proses';
        } elseif ($status_lower === 'belum' || $status_lower === 'belum ditangani') {
            return 'Belum Ditangani';
        } elseif ($status_lower === 'selesai') {
            return 'Selesai';
        }

        return $status;
    }
}
