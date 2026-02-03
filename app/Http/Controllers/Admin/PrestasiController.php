<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\Ekstrakurikuler;
use App\Models\PrestasiSiswa;

class PrestasiController extends Controller
{
    /**
     * Display prestasi list for a rombel or ekstrakurikuler
     */
    public function lihat(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $type = $request->get('type', '');
        $id = intval($request->get('id', 0));
        
        if (empty($type) || $id <= 0) {
            return redirect()->route('admin.rombel.index')
                ->with('error', 'Parameter tidak valid!');
        }
        
        $sumberInfo = [];
        $prestasiList = [];
        $backUrl = '';
        
        if ($type == 'ekstra') {
            // Get ekstrakurikuler data
            $ekstra = Ekstrakurikuler::find($id);
            if (!$ekstra) {
                return redirect()->route('admin.ekstrakurikuler.index')
                    ->with('error', 'Data tidak ditemukan!');
            }
            
            $sumberInfo = [
                'title' => $ekstra->nama_ekstrakurikuler,
                'tahun_pelajaran' => $ekstra->tahun_pelajaran,
                'semester' => $ekstra->semester,
                'icon' => 'fa-trophy',
                'color' => '#f59e0b',
            ];
            $backUrl = route('admin.ekstrakurikuler.index');
            
            // Query prestasi grouped by competition
            $prestasiList = $this->getPrestasiList('ekstrakurikuler', $id, $ekstra->tahun_pelajaran, $ekstra->semester);
            
        } elseif ($type == 'rombel') {
            // Get rombel data
            $rombel = Rombel::find($id);
            if (!$rombel) {
                return redirect()->route('admin.rombel.index')
                    ->with('error', 'Data tidak ditemukan!');
            }
            
            $sumberInfo = [
                'title' => $rombel->nama_rombel,
                'tahun_pelajaran' => $rombel->tahun_pelajaran,
                'semester' => $rombel->semester,
                'icon' => 'fa-trophy',
                'color' => '#f59e0b',
            ];
            $backUrl = route('admin.rombel.index');
            
            // Query prestasi grouped by competition
            $prestasiList = $this->getPrestasiList('rombel', $id, $rombel->tahun_pelajaran, $rombel->semester);
        }
        
        return view('admin.prestasi.lihat', compact(
            'admin', 'type', 'sumberInfo', 'prestasiList', 'backUrl', 'id'
        ));
    }
    
    /**
     * Get prestasi list grouped by competition
     */
    private function getPrestasiList($sumberPrestasi, $sumberId, $tahunPelajaran, $semester)
    {
        $results = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi',
                'ps.juara',
                'ps.jenjang',
                'ps.tanggal_pelaksanaan',
                'ps.penyelenggara',
                DB::raw('MAX(ps.tipe_peserta) as tipe_peserta'),
                DB::raw("GROUP_CONCAT(DISTINCT s.nama ORDER BY s.nama SEPARATOR '||') as siswa_list"),
                DB::raw("GROUP_CONCAT(DISTINCT s.nis ORDER BY s.nama SEPARATOR '||') as nis_list"),
                DB::raw('COUNT(*) as jumlah_siswa')
            )
            ->where('ps.sumber_prestasi', $sumberPrestasi)
            ->where('ps.sumber_id', $sumberId)
            ->where('ps.tahun_pelajaran', $tahunPelajaran)
            ->where('ps.semester', $semester)
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.tanggal_pelaksanaan', 'ps.penyelenggara')
            ->orderBy('ps.tanggal_pelaksanaan', 'desc')
            ->get();
        
        // Process results to split siswa_list and nis_list
        $prestasiList = [];
        foreach ($results as $row) {
            $item = (array) $row;
            $item['siswa_array'] = explode('||', $row->siswa_list ?? '');
            $item['nis_array'] = explode('||', $row->nis_list ?? '');
            $prestasiList[] = $item;
        }
        
        return $prestasiList;
    }
    
    /**
     * Get jenjang color
     */
    public static function getJenjangColor($jenjang)
    {
        $colors = [
            'Kelas' => '#6b7280',
            'Sekolah' => '#3b82f6',
            'Kecamatan' => '#10b981',
            'Kabupaten' => '#8b5cf6',
            'Provinsi' => '#f59e0b',
            'Nasional' => '#ef4444',
            'Internasional' => '#ec4899',
            'Lainnya' => '#6b7280'
        ];
        return $colors[$jenjang] ?? '#6b7280';
    }
}
