<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnggotaEkstrakurikulerController extends Controller
{
    /**
     * Display members of an ekstrakurikuler
     */
    public function index($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_nama = $guruBK->nama;

        // Get ekstrakurikuler data
        $ekstra = DB::table('ekstrakurikuler')
            ->where('id', $id)
            ->first();

        if (!$ekstra) {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', 'Data ekstrakurikuler tidak ditemukan!');
        }

        // Verify that this guru is a pembina
        $is_pembina = ($ekstra->pembina_1 == $guru_nama || 
                       $ekstra->pembina_2 == $guru_nama || 
                       $ekstra->pembina_3 == $guru_nama);
        
        if (!$is_pembina) {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', 'Anda tidak memiliki akses ke ekstrakurikuler ini!');
        }

        // Get member list
        $anggota_list = $this->getAnggotaList($id, $ekstra);

        // Get available students (not yet registered)
        $anggota_ids = collect($anggota_list)->pluck('siswa_id')->toArray();
        $siswa_available = $this->getSiswaAvailable($anggota_ids, $ekstra);

        return view('guru-bk.anggota-ekstrakurikuler', compact(
            'ekstra',
            'anggota_list',
            'siswa_available'
        ));
    }

    /**
     * Add members to ekstrakurikuler
     */
    public function tambahAnggota(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $ekstra = DB::table('ekstrakurikuler')->where('id', $id)->first();
        
        if (!$ekstra) {
            return back()->with('error', 'Data ekstrakurikuler tidak ditemukan!');
        }

        $siswa_ids = $request->input('siswa_ids', []);

        if (empty($siswa_ids)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih!');
        }

        $success_count = 0;
        $error_count = 0;
        $tanggal_bergabung = now();

        foreach ($siswa_ids as $siswa_id) {
            $siswa_id = intval($siswa_id);

            // Check if already registered
            $exists = DB::table('anggota_ekstrakurikuler')
                ->where('ekstrakurikuler_id', $id)
                ->where('siswa_id', $siswa_id)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->exists();

            if (!$exists) {
                $inserted = DB::table('anggota_ekstrakurikuler')->insert([
                    'ekstrakurikuler_id' => $id,
                    'siswa_id' => $siswa_id,
                    'tahun_pelajaran' => $ekstra->tahun_pelajaran,
                    'semester' => $ekstra->semester,
                    'tanggal_bergabung' => $tanggal_bergabung,
                    'status' => 'Aktif'
                ]);

                if ($inserted) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            } else {
                $error_count++;
            }
        }

        if ($success_count > 0) {
            $message = "Berhasil menambahkan $success_count anggota.";
            if ($error_count > 0) {
                $message .= " $error_count anggota gagal (mungkin sudah terdaftar).";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', 'Gagal menambahkan anggota. Semua siswa sudah terdaftar.');
        }
    }

    /**
     * Remove a member from ekstrakurikuler
     */
    public function hapusAnggota(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $anggota_id = $request->input('anggota_id');

        // Verify the member belongs to this ekstrakurikuler
        $exists = DB::table('anggota_ekstrakurikuler')
            ->where('id', $anggota_id)
            ->where('ekstrakurikuler_id', $id)
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Data anggota tidak ditemukan!');
        }

        $deleted = DB::table('anggota_ekstrakurikuler')
            ->where('id', $anggota_id)
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Anggota berhasil dihapus!');
        } else {
            return back()->with('error', 'Gagal menghapus anggota!');
        }
    }

    /**
     * Update nilai for a member (AJAX)
     */
    public function updateNilai(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $anggota_id = $request->input('anggota_id');
        $nilai = $request->input('nilai');

        // Verify the member belongs to this ekstrakurikuler
        $exists = DB::table('anggota_ekstrakurikuler')
            ->where('id', $anggota_id)
            ->where('ekstrakurikuler_id', $id)
            ->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Data anggota tidak ditemukan!']);
        }

        $updated = DB::table('anggota_ekstrakurikuler')
            ->where('id', $anggota_id)
            ->update(['nilai' => $nilai]);

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan!'
        ]);
    }

    /**
     * Get member list with calculated rombel
     */
    private function getAnggotaList($ekstra_id, $ekstra)
    {
        $anggota_raw = DB::table('anggota_ekstrakurikuler as ae')
            ->join('siswa as s', 'ae.siswa_id', '=', 's.id')
            ->select(
                'ae.*',
                's.id as siswa_id',
                's.nama as nama_siswa',
                's.nis',
                's.nisn',
                's.angkatan_masuk as angkatan',
                's.jk',
                's.foto',
                's.email',
                's.rombel_semester_1', 's.rombel_semester_2', 's.rombel_semester_3',
                's.rombel_semester_4', 's.rombel_semester_5', 's.rombel_semester_6'
            )
            ->where('ae.ekstrakurikuler_id', $ekstra_id)
            ->where('ae.tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('ae.semester', $ekstra->semester)
            ->orderBy('s.nama', 'ASC')
            ->get();

        $anggota_list = [];
        foreach ($anggota_raw as $row) {
            $row->rombel_aktif = $this->getBestRombelForDisplay($row, $ekstra->tahun_pelajaran, $ekstra->semester);
            $anggota_list[] = $row;
        }

        return $anggota_list;
    }

    /**
     * Get available students not yet registered
     */
    private function getSiswaAvailable($anggota_ids, $ekstra)
    {
        $query = DB::table('siswa')
            ->select(
                'id', 'nama', 'nisn', 'nis', 'jk', 'angkatan_masuk', 'foto',
                'rombel_semester_1', 'rombel_semester_2', 'rombel_semester_3',
                'rombel_semester_4', 'rombel_semester_5', 'rombel_semester_6'
            )
            ->orderBy('nama', 'ASC');

        if (!empty($anggota_ids)) {
            $query->whereNotIn('id', $anggota_ids);
        }

        $siswa_raw = $query->get();

        $siswa_available = [];
        foreach ($siswa_raw as $row) {
            $row->rombel_aktif = $this->getBestRombelForDisplay($row, $ekstra->tahun_pelajaran, $ekstra->semester);
            $siswa_available[] = $row;
        }

        return $siswa_available;
    }

    /**
     * Get the best rombel for display
     */
    private function getBestRombelForDisplay($student_data, $tahun_pelajaran, $semester)
    {
        $angkatan = $student_data->angkatan ?? $student_data->angkatan_masuk ?? null;
        
        if (!empty($angkatan)) {
            $calculated_rombel = $this->calculateRombelFromAngkatan($angkatan, $tahun_pelajaran, $semester, $student_data);
            if ($calculated_rombel) {
                return $calculated_rombel;
            }
        }

        // Fallback to any available rombel
        for ($i = 6; $i >= 1; $i--) {
            $col = "rombel_semester_$i";
            if (!empty($student_data->$col)) {
                return $student_data->$col;
            }
        }

        return "-";
    }

    /**
     * Calculate rombel from angkatan
     */
    private function calculateRombelFromAngkatan($angkatan, $tahun_pelajaran, $semester, $student_data)
    {
        if (empty($angkatan)) {
            return null;
        }

        $tahun_parts = explode('/', $tahun_pelajaran);
        $tahun_awal = intval($tahun_parts[0]);
        $angkatan = intval($angkatan);

        if ($angkatan > $tahun_awal) {
            return null;
        }

        $tahun_ke = $tahun_awal - $angkatan + 1;
        if ($tahun_ke > 3) {
            $tahun_ke = 3;
        }

        $semester_num = (strtolower($semester) == 'ganjil') ? 1 : 2;
        $semester_total = (($tahun_ke - 1) * 2) + $semester_num;

        if ($semester_total < 1) $semester_total = 1;
        if ($semester_total > 6) $semester_total = 6;

        $column = "rombel_semester_" . $semester_total;
        return !empty($student_data->$column) ? $student_data->$column : null;
    }

    /**
     * Helper: Get color for ekstrakurikuler
     */
    public static function getColorForEkstra($nama)
    {
        $colors = [
            'Pramuka' => '#3b82f6',
            'Paskibra' => '#ef4444',
            'PMR' => '#dc2626',
            'OSIS' => '#8b5cf6',
            'Basket' => '#f59e0b',
            'Futsal' => '#10b981',
            'Voli' => '#ec4899',
            'Seni Musik' => '#06b6d4',
            'Seni Tari' => '#f97316',
            'English Club' => '#6366f1',
            'IT Club' => '#0ea5e9',
            'KIR' => '#84cc16'
        ];
        return $colors[$nama] ?? '#6b7280';
    }

    /**
     * Helper: Get icon for ekstrakurikuler
     */
    public static function getIconForEkstra($nama)
    {
        $icons = [
            'Pramuka' => 'fa-campground',
            'Paskibra' => 'fa-flag',
            'PMR' => 'fa-heartbeat',
            'OSIS' => 'fa-users-cog',
            'Basket' => 'fa-basketball-ball',
            'Futsal' => 'fa-futbol',
            'Voli' => 'fa-volleyball-ball',
            'Seni Musik' => 'fa-music',
            'Seni Tari' => 'fa-gem',
            'English Club' => 'fa-language',
            'IT Club' => 'fa-laptop-code',
            'KIR' => 'fa-flask'
        ];
        return $icons[$nama] ?? 'fa-star';
    }
}
