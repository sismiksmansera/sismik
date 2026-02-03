<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class SemuaCatatanController extends Controller
{
    /**
     * Display all counseling notes with filters
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

        // Get active period
        $periodik = DataPeriodik::where('aktif', 'Ya')->first();
        $tahun_pelajaran_aktif = $periodik->tahun_pelajaran ?? '';
        $semester_aktif = $periodik->semester ?? '';

        // Get filter parameters
        $tahun_filter = $request->get('tahun', '');
        $semester_filter = $request->get('semester', '');
        $status_filter = $request->get('status', '');
        $search = $request->get('search', '');

        // Build query for catatan bimbingan
        $query = DB::table('catatan_bimbingan as cb')
            ->leftJoin('guru_bk as gb', 'cb.guru_bk_id', '=', 'gb.id')
            ->leftJoin('siswa as s', 'cb.nisn', '=', 's.nisn')
            ->select(
                'cb.id',
                'cb.nisn',
                'cb.tanggal',
                'cb.jenis_bimbingan',
                'cb.masalah',
                'cb.penyelesaian',
                'cb.tindak_lanjut',
                'cb.keterangan',
                'cb.tahun_pelajaran',
                'cb.semester',
                'cb.status',
                'cb.created_at',
                'cb.updated_at',
                'cb.guru_bk_id',
                'cb.pencatat_id',
                'cb.pencatat_nama',
                'cb.pencatat_role',
                'gb.nama as nama_guru',
                's.nama as nama_siswa',
                's.nis',
                's.jk',
                's.angkatan_masuk',
                's.bk_semester_1',
                's.bk_semester_2',
                's.bk_semester_3',
                's.bk_semester_4',
                's.bk_semester_5',
                's.bk_semester_6'
            );

        // Apply filters
        if (!empty($tahun_filter)) {
            $query->where('cb.tahun_pelajaran', $tahun_filter);
        }

        if (!empty($semester_filter)) {
            $query->where('cb.semester', $semester_filter);
        }

        if (!empty($status_filter)) {
            // Map filter value to database value
            $status_db = $status_filter;
            if ($status_filter === 'Belum Ditangani') $status_db = 'Belum';
            elseif ($status_filter === 'Dalam Proses') $status_db = 'Proses';
            
            $query->where('cb.status', $status_db);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('s.nama', 'LIKE', "%{$search}%")
                  ->orWhere('s.nisn', 'LIKE', "%{$search}%")
                  ->orWhere('cb.masalah', 'LIKE', "%{$search}%");
            });
        }

        $catatan_list = $query->orderBy('cb.tanggal', 'DESC')
                              ->orderBy('cb.created_at', 'DESC')
                              ->get();

        // Calculate statistics
        $total_catatan = $catatan_list->count();
        $status_stats = [
            'Belum' => 0,
            'Proses' => 0,
            'Selesai' => 0
        ];

        foreach ($catatan_list as $catatan) {
            $status = $catatan->status ?? 'Belum';
            if (isset($status_stats[$status])) {
                $status_stats[$status]++;
            }
        }

        // Get unique tahun pelajaran for filter
        $tahun_list = DB::table('catatan_bimbingan')
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'DESC')
            ->pluck('tahun_pelajaran')
            ->toArray();

        return view('guru-bk.semua-catatan', compact(
            'catatan_list',
            'total_catatan',
            'status_stats',
            'tahun_list',
            'tahun_filter',
            'semester_filter',
            'status_filter',
            'search',
            'tahun_pelajaran_aktif',
            'semester_aktif',
            'nama_guru_bk'
        ));
    }

    /**
     * AJAX: Search all students
     */
    public function searchStudents(Request $request)
    {
        $search = $request->input('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 2 karakter untuk pencarian.'
            ]);
        }

        // Get active period
        $periodik = DataPeriodik::where('aktif', 'Ya')->first();
        $tahun = $periodik->tahun_pelajaran ?? '';
        $semester = $periodik->semester ?? '';

        try {
            $students = DB::table('siswa as s')
                ->select(
                    's.id',
                    's.nis',
                    's.nisn',
                    's.nama',
                    's.jk',
                    's.angkatan_masuk',
                    's.rombel_semester_1',
                    's.rombel_semester_2',
                    's.rombel_semester_3',
                    's.rombel_semester_4',
                    's.rombel_semester_5',
                    's.rombel_semester_6'
                )
                ->where(function($q) use ($search) {
                    $q->where('s.nama', 'LIKE', "%{$search}%")
                      ->orWhere('s.nisn', 'LIKE', "%{$search}%")
                      ->orWhere('s.nis', 'LIKE', "%{$search}%");
                })
                ->orderBy('s.nama', 'ASC')
                ->limit(20)
                ->get();

            $result = [];
            foreach ($students as $row) {
                // Calculate active semester
                $semester_aktif_siswa = $this->calculateActiveSemester(
                    $row->angkatan_masuk,
                    $tahun,
                    $semester
                );

                // Get rombel for active semester
                $rombel_field = 'rombel_semester_' . $semester_aktif_siswa;
                $rombel_aktif = $row->$rombel_field ?? '-';

                // Determine kelas
                $kelas = '10';
                if (in_array($semester_aktif_siswa, [3, 4])) $kelas = '11';
                elseif (in_array($semester_aktif_siswa, [5, 6])) $kelas = '12';

                $result[] = [
                    'id' => $row->id,
                    'nis' => $row->nis,
                    'nisn' => $row->nisn,
                    'nama' => $row->nama,
                    'jk' => $row->jk,
                    'kelas' => $kelas,
                    'rombel' => !empty($rombel_aktif) ? $rombel_aktif : '-'
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper: Calculate active semester for student
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
     * Helper: Get correct BK teacher for catatan
     */
    public static function getGuruBKForCatatan($catatan)
    {
        // If no angkatan data, use nama_guru from join
        if (empty($catatan->angkatan_masuk) || empty($catatan->tahun_pelajaran)) {
            return $catatan->nama_guru ?? '-';
        }

        // Calculate active semester when note was created
        $tahun_parts = explode('/', $catatan->tahun_pelajaran);
        $tahun_mulai = intval($tahun_parts[0]);
        $angkatan_int = intval($catatan->angkatan_masuk);
        $selisih_tahun = $tahun_mulai - $angkatan_int;

        $semester_siswa = 1;
        if ($selisih_tahun == 0) {
            $semester_siswa = ($catatan->semester == 'Ganjil') ? 1 : 2;
        } elseif ($selisih_tahun == 1) {
            $semester_siswa = ($catatan->semester == 'Ganjil') ? 3 : 4;
        } elseif ($selisih_tahun == 2) {
            $semester_siswa = ($catatan->semester == 'Ganjil') ? 5 : 6;
        }

        // Get BK name from appropriate column
        $bk_field = 'bk_semester_' . $semester_siswa;
        $nama_bk = $catatan->$bk_field ?? '';

        // Fallback to nama_guru from join
        return !empty($nama_bk) ? $nama_bk : ($catatan->nama_guru ?? '-');
    }
}
