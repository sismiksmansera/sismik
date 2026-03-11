<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CatatanBimbinganController extends Controller
{
    /**
     * Display catatan bimbingan for specific student
     */
    public function index(Request $request, $nisn)
    {
        // Get logged-in Guru BK
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_bk_id = $guruBK->id;
        $nama_guru_bk = $guruBK->nama;

        // Get student data
        $siswa = DB::table('siswa')
            ->select(
                'id', 'nis', 'nisn', 'nama', 'jk', 'agama', 'foto',
                'tempat_lahir', 'tgl_lahir', 'nama_rombel', 'angkatan_masuk',
                'rombel_semester_1', 'rombel_semester_2', 'rombel_semester_3',
                'rombel_semester_4', 'rombel_semester_5', 'rombel_semester_6',
                'bk_semester_1', 'bk_semester_2', 'bk_semester_3',
                'bk_semester_4', 'bk_semester_5', 'bk_semester_6'
            )
            ->where('nisn', $nisn)
            ->first();

        if (!$siswa) {
            return redirect()->route('guru_bk.siswa-bimbingan')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get filter parameters
        $tahun_filter = $request->input('tahun', '');
        $semester_filter = $request->input('semester', '');

        // Build catatan query
        $query = DB::table('catatan_bimbingan as cb')
            ->leftJoin('guru_bk as gb', 'cb.guru_bk_id', '=', 'gb.id')
            ->select(
                'cb.id', 'cb.tanggal', 'cb.jenis_bimbingan', 'cb.masalah',
                'cb.penyelesaian', 'cb.tindak_lanjut', 'cb.keterangan',
                'cb.tahun_pelajaran', 'cb.semester', 'cb.status',
                'cb.created_at', 'cb.updated_at', 'cb.guru_bk_id',
                'cb.pencatat_id', 'cb.pencatat_nama', 'cb.pencatat_role',
                'gb.nama as nama_guru'
            )
            ->where('cb.nisn', $nisn);

        if (!empty($tahun_filter)) {
            $query->where('cb.tahun_pelajaran', $tahun_filter);
        }

        if (!empty($semester_filter)) {
            $query->where('cb.semester', $semester_filter);
        }

        $catatan_list = $query->orderBy('cb.tanggal', 'DESC')
            ->orderBy('cb.created_at', 'DESC')
            ->get();

        // Calculate statistics
        $total_catatan = $catatan_list->count();
        $status_stats = [
            'Belum Ditangani' => 0,
            'Dalam Proses' => 0,
            'Selesai' => 0
        ];
        $jenis_stats = [];

        foreach ($catatan_list as $catatan) {
            // Normalize status
            $status = $this->normalizeStatus($catatan->status);
            if (isset($status_stats[$status])) {
                $status_stats[$status]++;
            }

            // Count by jenis
            $jenis = $catatan->jenis_bimbingan ?? 'Lainnya';
            $jenis_stats[$jenis] = ($jenis_stats[$jenis] ?? 0) + 1;
        }

        // Get unique tahun pelajaran for filter
        $tahun_list = DB::table('catatan_bimbingan')
            ->where('nisn', $nisn)
            ->distinct()
            ->orderBy('tahun_pelajaran', 'DESC')
            ->pluck('tahun_pelajaran')
            ->toArray();

        return view('guru-bk.catatan-bimbingan', compact(
            'siswa',
            'catatan_list',
            'total_catatan',
            'status_stats',
            'jenis_stats',
            'tahun_list',
            'tahun_filter',
            'semester_filter',
            'nama_guru_bk',
            'guru_bk_id',
            'nisn'
        ));
    }

    /**
     * Delete a catatan bimbingan via AJAX
     */
    public function delete(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $id = $request->input('id');
        $guru_bk_id = $guruBK->id;

        if (!$id || $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'ID catatan tidak valid.'
            ]);
        }

        try {
            // Check if catatan belongs to this guru BK
            $catatan = DB::table('catatan_bimbingan')
                ->where('id', $id)
                ->where('guru_bk_id', $guru_bk_id)
                ->first();

            if (!$catatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan tidak ditemukan atau bukan milik Anda.'
                ]);
            }

            // Delete catatan
            DB::table('catatan_bimbingan')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dihapus.',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            Log::error("Error deleting catatan: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem.'
            ]);
        }
    }

    /**
     * Helper: Normalize status string
     */
    private function normalizeStatus($status)
    {
        $status = trim($status ?? '');
        $status_lower = strtolower($status);

        if ($status_lower === 'proses' || $status_lower === 'dalam proses') {
            return 'Dalam Proses';
        } elseif ($status_lower === 'belum' || $status_lower === 'belum ditangani' || empty($status)) {
            return 'Belum Ditangani';
        } elseif ($status_lower === 'selesai') {
            return 'Selesai';
        }

        return $status;
    }

    /**
     * Helper: Get Guru BK name for catatan
     */
    public static function getGuruBKForCatatan($catatan, $siswa)
    {
        if (empty($siswa->angkatan_masuk) || empty($catatan->tahun_pelajaran)) {
            return $catatan->nama_guru ?? '-';
        }

        $tahun_parts = explode('/', $catatan->tahun_pelajaran);
        $tahun_mulai = intval($tahun_parts[0] ?? 0);
        $angkatan_int = intval($siswa->angkatan_masuk);
        $selisih_tahun = $tahun_mulai - $angkatan_int;

        $semester_siswa = 1;
        if ($selisih_tahun == 0) {
            $semester_siswa = ($catatan->semester == 'Ganjil') ? 1 : 2;
        } elseif ($selisih_tahun == 1) {
            $semester_siswa = ($catatan->semester == 'Ganjil') ? 3 : 4;
        } elseif ($selisih_tahun == 2) {
            $semester_siswa = ($catatan->semester == 'Ganjil') ? 5 : 6;
        }

        $bk_field = 'bk_semester_' . $semester_siswa;
        $nama_bk = $siswa->$bk_field ?? '';

        return !empty($nama_bk) ? $nama_bk : ($catatan->nama_guru ?? '-');
    }

    /**
     * Show edit form for catatan bimbingan
     */
    public function edit($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get catatan with student data
        $catatan = DB::table('catatan_bimbingan as cb')
            ->join('siswa as s', 'cb.nisn', '=', 's.nisn')
            ->leftJoin('guru_bk as gb', 'cb.guru_bk_id', '=', 'gb.id')
            ->select(
                'cb.*',
                's.id as siswa_id', 's.nama as nama_siswa', 's.nis', 's.nisn', 
                's.jk', 's.agama', 's.foto', 's.nama_rombel', 's.angkatan_masuk',
                's.rombel_semester_1', 's.rombel_semester_2', 's.rombel_semester_3',
                's.rombel_semester_4', 's.rombel_semester_5', 's.rombel_semester_6',
                's.created_at as siswa_created_at', 's.updated_at as siswa_updated_at',
                'gb.nama as nama_guru_pembuat'
            )
            ->where('cb.id', $id)
            ->first();

        if (!$catatan) {
            return redirect()->route('guru_bk.semua-catatan')
                ->with('error', 'Catatan tidak ditemukan.');
        }

        // Calculate date range based on tahun pelajaran and semester
        $tahun_parts = explode('/', $catatan->tahun_pelajaran);
        $tahun_awal = intval($tahun_parts[0] ?? date('Y'));
        $tahun_akhir = intval($tahun_parts[1] ?? $tahun_awal + 1);

        if ($catatan->semester == 'Ganjil') {
            $min_date = $tahun_awal . '-07-01';
            $max_date = $tahun_awal . '-12-31';
        } else {
            $min_date = $tahun_akhir . '-01-01';
            $max_date = $tahun_akhir . '-06-30';
        }

        // Get rombel based on semester
        $selected_rombel = $this->getRombelBySemester($catatan, $catatan->semester);

        // Normalize status for form display
        $status_form_value = $this->getStatusFormValue($catatan->status);

        $nama_guru_bk = $guruBK->nama;
        $guru_bk_id = $guruBK->id;

        return view('guru-bk.edit-catatan', compact(
            'catatan',
            'min_date',
            'max_date',
            'selected_rombel',
            'status_form_value',
            'nama_guru_bk',
            'guru_bk_id'
        ));
    }

    /**
     * Update catatan bimbingan
     */
    public function update(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Validate request
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_bimbingan' => 'required|string',
            'masalah' => 'required|string',
            'penyelesaian' => 'required|string',
            'tindak_lanjut' => 'required|string',
            'status' => 'required|string',
            'tahun_pelajaran' => 'required|string',
            'semester' => 'required|string',
        ]);

        // Status mapping for database
        $status_mapping = [
            'Belum Ditangani' => 'Belum',
            'Dalam Proses' => 'Proses',
            'Selesai' => 'Selesai'
        ];
        
        $status_database = $status_mapping[$request->status] ?? 'Proses';

        try {
            // Get catatan for NISN before update
            $catatan = DB::table('catatan_bimbingan')->where('id', $id)->first();
            
            if (!$catatan) {
                return back()->with('error', 'Catatan tidak ditemukan.');
            }

            // Update catatan
            DB::table('catatan_bimbingan')
                ->where('id', $id)
                ->update([
                    'tanggal' => $request->tanggal,
                    'jenis_bimbingan' => $request->jenis_bimbingan,
                    'masalah' => $request->masalah,
                    'penyelesaian' => $request->penyelesaian,
                    'tindak_lanjut' => $request->tindak_lanjut,
                    'keterangan' => $request->keterangan ?? '',
                    'tahun_pelajaran' => $request->tahun_pelajaran,
                    'semester' => $request->semester,
                    'status' => $status_database,
                    'updated_at' => now(),
                ]);

            return redirect()->route('guru_bk.catatan-bimbingan', ['nisn' => $catatan->nisn])
                ->with('success', 'Catatan bimbingan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Error updating catatan: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui catatan: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Get rombel based on semester
     */
    private function getRombelBySemester($siswa, $semester)
    {
        $semester_lower = strtolower($semester ?? '');
        
        switch ($semester_lower) {
            case 'ganjil':
            case '1':
                return $siswa->rombel_semester_1 ?? $siswa->nama_rombel ?? '-';
            case 'genap':
            case '2':
                return $siswa->rombel_semester_2 ?? $siswa->nama_rombel ?? '-';
            case '3':
                return $siswa->rombel_semester_3 ?? $siswa->nama_rombel ?? '-';
            case '4':
                return $siswa->rombel_semester_4 ?? $siswa->nama_rombel ?? '-';
            case '5':
                return $siswa->rombel_semester_5 ?? $siswa->nama_rombel ?? '-';
            case '6':
                return $siswa->rombel_semester_6 ?? $siswa->nama_rombel ?? '-';
            default:
                return $siswa->nama_rombel ?? '-';
        }
    }

    /**
     * Helper: Get rombel based on semester number (1-6)
     */
    private function getRombelBySemesterNumber($siswa, $semesterNumber)
    {
        $semesterNumber = intval($semesterNumber);
        
        switch ($semesterNumber) {
            case 1:
                return $siswa->rombel_semester_1 ?? $siswa->nama_rombel ?? '-';
            case 2:
                return $siswa->rombel_semester_2 ?? $siswa->nama_rombel ?? '-';
            case 3:
                return $siswa->rombel_semester_3 ?? $siswa->nama_rombel ?? '-';
            case 4:
                return $siswa->rombel_semester_4 ?? $siswa->nama_rombel ?? '-';
            case 5:
                return $siswa->rombel_semester_5 ?? $siswa->nama_rombel ?? '-';
            case 6:
                return $siswa->rombel_semester_6 ?? $siswa->nama_rombel ?? '-';
            default:
                return $siswa->nama_rombel ?? '-';
        }
    }

    /**
     * Helper: Convert database status to form value
     */
    private function getStatusFormValue($status)
    {
        $status_db = trim($status ?? '');
        
        if ($status_db === 'Belum') {
            return 'Belum Ditangani';
        } elseif ($status_db === 'Proses') {
            return 'Dalam Proses';
        } elseif ($status_db === 'Selesai') {
            return 'Selesai';
        } elseif (in_array($status_db, ['Belum Ditangani', 'Dalam Proses', 'Selesai'])) {
            return $status_db;
        }
        
        return 'Dalam Proses';
    }

    /**
     * Show create form for new catatan bimbingan
     */
    public function create(Request $request, $nisn)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get student data
        $siswa = DB::table('siswa')
            ->select(
                'id', 'nis', 'nisn', 'nama', 'jk', 'agama', 'foto',
                'tempat_lahir', 'tgl_lahir', 'nama_rombel', 'angkatan_masuk',
                'rombel_semester_1', 'rombel_semester_2', 'rombel_semester_3',
                'rombel_semester_4', 'rombel_semester_5', 'rombel_semester_6',
                'bk_semester_1', 'bk_semester_2', 'bk_semester_3',
                'bk_semester_4', 'bk_semester_5', 'bk_semester_6',
                'created_at', 'updated_at'
            )
            ->where('nisn', $nisn)
            ->first();

        if (!$siswa) {
            return redirect()->route('guru_bk.siswa-bimbingan')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get active tahun pelajaran and semester from data_periodik
        $periodik = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();

        if ($periodik) {
            $tahun_pelajaran_aktif = $periodik->tahun_pelajaran;
            $semester_aktif = $periodik->semester;
        } else {
            $tahun_pelajaran_aktif = date('Y') . "/" . (date('Y') + 1);
            $currentMonth = date('n');
            $semester_aktif = ($currentMonth >= 1 && $currentMonth <= 6) ? "Genap" : "Ganjil";
        }

        // Use URL params if provided, else use active
        $selected_tahun = !empty($request->input('tahun')) ? $request->input('tahun') : $tahun_pelajaran_aktif;
        $selected_semester = !empty($request->input('semester')) ? $request->input('semester') : $semester_aktif;

        // Calculate date range based on selected tahun and semester
        $tahun_parts = explode('/', $selected_tahun);
        $tahun_awal = intval($tahun_parts[0] ?? date('Y'));
        $tahun_akhir = intval($tahun_parts[1] ?? $tahun_awal + 1);

        if ($selected_semester == 'Ganjil') {
            $min_date = $tahun_awal . '-07-01';
            $max_date = $tahun_awal . '-12-31';
        } else {
            $min_date = $tahun_akhir . '-01-01';
            $max_date = $tahun_akhir . '-06-30';
        }

        // Calculate active semester number for student (1-6) based on angkatan
        $semester_aktif_siswa = $this->calculateActiveSemester(
            $siswa->angkatan_masuk,
            $selected_tahun,
            $selected_semester
        );

        // Get rombel based on calculated semester number
        $selected_rombel = $this->getRombelBySemesterNumber($siswa, $semester_aktif_siswa);

        $nama_guru_bk = $guruBK->nama;
        $guru_bk_id = $guruBK->id;

        return view('guru-bk.create-catatan', compact(
            'siswa',
            'nisn',
            'selected_tahun',
            'selected_semester',
            'min_date',
            'max_date',
            'selected_rombel',
            'semester_aktif_siswa',
            'nama_guru_bk',
            'guru_bk_id'
        ));
    }

    /**
     * Store new catatan bimbingan
     */
    public function store(Request $request, $nisn)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Validate request
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_bimbingan' => 'required|string',
            'masalah' => 'required|string',
            'penyelesaian' => 'required|string',
            'tindak_lanjut' => 'required|string',
            'status' => 'required|string',
            'tahun_pelajaran' => 'required|string',
            'semester' => 'required|string',
        ]);

        // Status mapping for database
        $status_mapping = [
            'Belum Ditangani' => 'Belum',
            'Dalam Proses' => 'Proses',
            'Selesai' => 'Selesai'
        ];
        
        $status_database = $status_mapping[$request->status] ?? 'Belum';

        // Get Guru BK for this student
        $siswa = DB::table('siswa')->where('nisn', $nisn)->first();
        
        // Calculate active semester for student
        $semester_aktif_siswa = $this->calculateActiveSemester(
            $siswa->angkatan_masuk ?? null,
            $request->tahun_pelajaran,
            $request->semester
        );

        // Get BK name from siswa based on semester
        $bk_field = 'bk_semester_' . $semester_aktif_siswa;
        $bk_nama_dari_db = $siswa->$bk_field ?? '';

        // Find guru_bk_id from name
        $guru_bk_id = $guruBK->id;
        if (!empty($bk_nama_dari_db)) {
            $guru_dari_db = DB::table('guru_bk')
                ->where('nama', $bk_nama_dari_db)
                ->first();
            if ($guru_dari_db) {
                $guru_bk_id = $guru_dari_db->id;
            }
        }

        try {
            DB::table('catatan_bimbingan')->insert([
                'nisn' => $nisn,
                'guru_bk_id' => $guru_bk_id,
                'pencatat_id' => $guruBK->id,
                'pencatat_nama' => $guruBK->nama,
                'pencatat_role' => 'guru_bk',
                'tanggal' => $request->tanggal,
                'jenis_bimbingan' => $request->jenis_bimbingan,
                'masalah' => $request->masalah,
                'penyelesaian' => $request->penyelesaian,
                'tindak_lanjut' => $request->tindak_lanjut,
                'keterangan' => $request->keterangan ?? '',
                'status' => $status_database,
                'tahun_pelajaran' => $request->tahun_pelajaran,
                'semester' => $request->semester,
                'created_at' => now(),
            ]);

            return redirect()->route('guru_bk.catatan-bimbingan', ['nisn' => $nisn])
                ->with('success', 'Catatan bimbingan berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error("Error storing catatan: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan catatan: ' . $e->getMessage())
                ->withInput();
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
        $tahun_mulai = intval($tahun_parts[0] ?? 0);
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
     * Print catatan bimbingan
     */
    public function print($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get catatan with joins
        $catatan = DB::table('catatan_bimbingan as cb')
            ->join('siswa as s', 'cb.nisn', '=', 's.nisn')
            ->leftJoin('guru_bk as gb', 'cb.guru_bk_id', '=', 'gb.id')
            ->select(
                'cb.*',
                's.nama as nama_siswa', 's.nis', 's.nisn as siswa_nisn',
                's.nama_rombel', 's.angkatan_masuk',
                's.rombel_semester_1', 's.rombel_semester_2', 's.rombel_semester_3',
                's.rombel_semester_4', 's.rombel_semester_5', 's.rombel_semester_6',
                's.bk_semester_1', 's.bk_semester_2', 's.bk_semester_3',
                's.bk_semester_4', 's.bk_semester_5', 's.bk_semester_6',
                'gb.nama as nama_guru', 'gb.nip as nip_guru'
            )
            ->where('cb.id', $id)
            ->first();

        if (!$catatan) {
            return back()->with('error', 'Catatan tidak ditemukan.');
        }

        // Get periode aktif for kepala sekolah
        $periodeAktif = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();

        // Calculate guru BK info and rombel based on semester
        $guruBKInfo = $this->getGuruBKInfoForPrint($catatan);

        return view('guru-bk.print-catatan', compact(
            'catatan',
            'guruBKInfo',
            'periodeAktif'
        ));
    }

    /**
     * Helper: Get Guru BK info for print (nama, nip, rombel)
     */
    private function getGuruBKInfoForPrint($catatan)
    {
        if (empty($catatan->angkatan_masuk) || empty($catatan->tahun_pelajaran)) {
            return [
                'nama' => $catatan->nama_guru ?? '-',
                'nip' => $catatan->nip_guru ?? '-',
                'rombel' => $catatan->nama_rombel ?? '-'
            ];
        }

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

        $bk_field = 'bk_semester_' . $semester_siswa;
        $rombel_field = 'rombel_semester_' . $semester_siswa;
        $nama_bk = $catatan->$bk_field ?? '';
        $rombel = $catatan->$rombel_field ?? $catatan->nama_rombel ?? '-';

        return [
            'nama' => !empty($nama_bk) ? $nama_bk : ($catatan->nama_guru ?? '-'),
            'nip' => $catatan->nip_guru ?? '-',
            'rombel' => $rombel
        ];
    }
}

