<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrestasiController extends Controller
{
    /**
     * Display prestasi list for ekstrakurikuler or rombel
     */
    public function index(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_nama = $guruBK->nama;
        $type = $request->query('type', '');
        $id = intval($request->query('id', 0));

        if (empty($type) || $id <= 0) {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', 'Parameter tidak valid!');
        }

        $sumber_info = null;
        $prestasi_list = [];

        if ($type == 'ekstra') {
            $result = $this->getPrestasiEkstra($id, $guru_nama);
        } elseif ($type == 'rombel') {
            $result = $this->getPrestasiRombel($id, $guru_nama);
        } else {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', 'Tipe tidak valid!');
        }

        if (isset($result['error'])) {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', $result['error']);
        }

        $sumber_info = $result['sumber_info'];
        $prestasi_list = $result['prestasi_list'];

        return view('guru-bk.prestasi', compact(
            'type',
            'id',
            'sumber_info',
            'prestasi_list'
        ));
    }

    /**
     * Get prestasi for ekstrakurikuler
     */
    private function getPrestasiEkstra($id, $guru_nama)
    {
        // Get ekstrakurikuler data
        $ekstra = DB::table('ekstrakurikuler')
            ->where('id', $id)
            ->first();

        if (!$ekstra) {
            return ['error' => 'Data ekstrakurikuler tidak ditemukan!'];
        }

        // Verify access
        $is_pembina = ($ekstra->pembina_1 == $guru_nama || 
                       $ekstra->pembina_2 == $guru_nama || 
                       $ekstra->pembina_3 == $guru_nama);
        
        if (!$is_pembina) {
            return ['error' => 'Anda tidak memiliki akses ke ekstrakurikuler ini!'];
        }

        $sumber_info = (object) [
            'id' => $ekstra->id,
            'title' => $ekstra->nama_ekstrakurikuler,
            'tahun_pelajaran' => $ekstra->tahun_pelajaran,
            'semester' => $ekstra->semester,
            'icon' => 'fa-trophy',
            'color' => '#f59e0b'
        ];

        // Query prestasi
        $prestasi_list = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi',
                'ps.juara',
                'ps.jenjang',
                'ps.tanggal_pelaksanaan',
                DB::raw("GROUP_CONCAT(DISTINCT s.nama SEPARATOR ', ') as peserta")
            )
            ->where('ps.sumber_prestasi', 'ekstrakurikuler')
            ->where('ps.sumber_id', $id)
            ->where('ps.tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('ps.semester', $ekstra->semester)
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.tanggal_pelaksanaan')
            ->orderBy('ps.tanggal_pelaksanaan', 'DESC')
            ->get();

        return [
            'sumber_info' => $sumber_info,
            'prestasi_list' => $prestasi_list
        ];
    }

    /**
     * Get prestasi for rombel
     */
    private function getPrestasiRombel($id, $guru_nama)
    {
        // Get rombel data
        $rombel = DB::table('rombel')
            ->where('id', $id)
            ->first();

        if (!$rombel) {
            return ['error' => 'Data rombel tidak ditemukan!'];
        }

        // Verify access
        if ($rombel->wali_kelas != $guru_nama) {
            return ['error' => 'Anda tidak memiliki akses ke kelas ini!'];
        }

        $sumber_info = (object) [
            'id' => $rombel->id,
            'title' => $rombel->nama_rombel,
            'tahun_pelajaran' => $rombel->tahun_pelajaran,
            'semester' => $rombel->semester,
            'icon' => 'fa-trophy',
            'color' => '#f59e0b'
        ];

        // Query prestasi
        $prestasi_list = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi',
                'ps.juara',
                'ps.jenjang',
                'ps.tanggal_pelaksanaan',
                DB::raw("GROUP_CONCAT(DISTINCT s.nama SEPARATOR ', ') as peserta")
            )
            ->where('ps.sumber_prestasi', 'rombel')
            ->where('ps.sumber_id', $id)
            ->where('ps.tahun_pelajaran', $rombel->tahun_pelajaran)
            ->where('ps.semester', $rombel->semester)
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.tanggal_pelaksanaan')
            ->orderBy('ps.tanggal_pelaksanaan', 'DESC')
            ->get();

        return [
            'sumber_info' => $sumber_info,
            'prestasi_list' => $prestasi_list
        ];
    }

    /**
     * Get medal color based on juara ranking
     */
    public static function getMedalColor($juara)
    {
        $juara_lower = strtolower($juara);
        
        if (strpos($juara_lower, '1') !== false || strpos($juara_lower, 'pertama') !== false) {
            return '#f59e0b'; // Gold
        } elseif (strpos($juara_lower, '2') !== false || strpos($juara_lower, 'kedua') !== false) {
            return '#9ca3af'; // Silver
        } elseif (strpos($juara_lower, '3') !== false || strpos($juara_lower, 'ketiga') !== false) {
            return '#cd7f32'; // Bronze
        }
        
        return '#6b7280'; // Default gray
    }
}
