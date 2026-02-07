<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CatatanBimbingan;
use App\Models\DataPeriodik;
use App\Models\AdminSekolah;
use App\Services\EffectiveDateService;

class DashboardController extends Controller
{
    public function index()
    {
        $guruBK = Auth::guard('guru_bk')->user();
        $periodik = DataPeriodik::aktif()->first();

        // Check if impersonating
        $isImpersonating = session('impersonating', false);
        
        // Get effective date (supports testing mode)
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $isTesting = $effectiveDate['is_testing'];
        $tanggalFormatted = $effectiveDate['formatted'];
        $tanggalHariIni = $effectiveDate['date'];
        $hariIni = $effectiveDate['hari'];

        // Statistics for catatan bimbingan
        $totalCatatan = CatatanBimbingan::where('guru_bk_id', $guruBK->id)->count();
        $catatanBelum = CatatanBimbingan::where('guru_bk_id', $guruBK->id)
            ->where('status', 'Belum')->count();
        $catatanProses = CatatanBimbingan::where('guru_bk_id', $guruBK->id)
            ->where('status', 'Proses')->count();
        $catatanSelesai = CatatanBimbingan::where('guru_bk_id', $guruBK->id)
            ->where('status', 'Selesai')->count();

        // Presensi stats for today - count occurrences of each status across all jam_ke columns
        $presensiStats = [
            'A' => 0,  // Alpha
            'S' => 0,  // Sakit
            'I' => 0,  // Izin
            'D' => 0,  // Dispensasi
            'B' => 0,  // Bolos
        ];
        
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        if ($hariIni !== 'Minggu' && !empty($tahunAktif) && !empty($semesterAktif)) {
            $jamColumns = ['jam_ke_1', 'jam_ke_2', 'jam_ke_3', 'jam_ke_4', 'jam_ke_5', 
                           'jam_ke_6', 'jam_ke_7', 'jam_ke_8', 'jam_ke_9', 'jam_ke_10', 'jam_ke_11'];
            
            foreach ($jamColumns as $jamCol) {
                foreach (array_keys($presensiStats) as $status) {
                    $count = \DB::table('presensi_siswa')
                        ->where('tanggal_presensi', $tanggalHariIni)
                        ->where('tahun_pelajaran', $tahunAktif)
                        ->whereRaw('LOWER(semester) = LOWER(?)', [$semesterAktif])
                        ->where($jamCol, $status)
                        ->count();
                    $presensiStats[$status] += $count;
                }
            }
        }
        
        $totalPresensiIssues = array_sum($presensiStats);

        return view('guru_bk.dashboard', compact(
            'guruBK',
            'periodik',
            'totalCatatan',
            'catatanBelum',
            'catatanProses',
            'catatanSelesai',
            'isImpersonating',
            'isTesting',
            'tanggalFormatted',
            'tanggalHariIni',
            'hariIni',
            'presensiStats',
            'totalPresensiIssues'
        ));
    }

    /**
     * Stop impersonating and return to admin account
     */
    public function stopImpersonate()
    {
        if (!session('impersonating')) {
            return redirect()->route('guru_bk.dashboard');
        }

        $adminId = session('original_admin_id');
        
        // Logout guru BK
        Auth::guard('guru_bk')->logout();
        
        // Clear impersonation session
        session()->forget(['impersonating', 'impersonate_type', 'original_admin_id', 'original_admin_username']);
        
        // Login back as admin
        $admin = AdminSekolah::find($adminId);
        if ($admin) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.guru-bk.index')->with('success', 'Kembali ke akun admin');
        }

        return redirect()->route('login');
    }

    /**
     * Get presensi detail for modal (AJAX)
     */
    public function getPresensiDetail(Request $request)
    {
        $status = $request->input('status'); // A, S, I, D, B
        $periodik = DataPeriodik::aktif()->first();
        
        if (!$periodik) {
            return response()->json(['success' => false, 'message' => 'Tidak ada periode aktif']);
        }
        
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $tanggalHariIni = $effectiveDate['date'];
        $tahunAktif = $periodik->tahun_pelajaran;
        $semesterAktif = $periodik->semester;
        
        $jamColumns = ['jam_ke_1', 'jam_ke_2', 'jam_ke_3', 'jam_ke_4', 'jam_ke_5', 
                       'jam_ke_6', 'jam_ke_7', 'jam_ke_8', 'jam_ke_9', 'jam_ke_10', 'jam_ke_11'];
        
        $results = [];
        
        // Get all presensi records for today
        $presensiRecords = \DB::table('presensi_siswa as ps')
            ->leftJoin('rombel as r', 'ps.id_rombel', '=', 'r.id')
            ->select(
                'ps.nama_siswa',
                'ps.nisn',
                'ps.mata_pelajaran',
                'ps.guru_pengajar',
                'r.nama_rombel',
                'ps.jam_ke_1', 'ps.jam_ke_2', 'ps.jam_ke_3', 'ps.jam_ke_4', 'ps.jam_ke_5',
                'ps.jam_ke_6', 'ps.jam_ke_7', 'ps.jam_ke_8', 'ps.jam_ke_9', 'ps.jam_ke_10', 'ps.jam_ke_11'
            )
            ->where('ps.tanggal_presensi', $tanggalHariIni)
            ->where('ps.tahun_pelajaran', $tahunAktif)
            ->whereRaw('LOWER(ps.semester) = LOWER(?)', [$semesterAktif])
            ->get();
        
        foreach ($presensiRecords as $record) {
            foreach ($jamColumns as $index => $jamCol) {
                if ($record->$jamCol === $status) {
                    $jamKe = $index + 1;
                    $results[] = [
                        'nama_siswa' => $record->nama_siswa,
                        'nisn' => $record->nisn,
                        'rombel' => $record->nama_rombel ?? '-',
                        'mapel' => $record->mata_pelajaran ?? '-',
                        'guru' => $record->guru_pengajar ?? '-',
                        'jam_ke' => $jamKe,
                    ];
                }
            }
        }
        
        // Sort by rombel, then by nama_siswa, then by jam_ke
        usort($results, function($a, $b) {
            $cmp = strcmp($a['rombel'], $b['rombel']);
            if ($cmp !== 0) return $cmp;
            $cmp = strcmp($a['nama_siswa'], $b['nama_siswa']);
            if ($cmp !== 0) return $cmp;
            return $a['jam_ke'] - $b['jam_ke'];
        });
        
        // Status labels
        $statusLabels = [
            'A' => 'Alpha',
            'S' => 'Sakit',
            'I' => 'Izin',
            'D' => 'Dispensasi',
            'B' => 'Bolos'
        ];
        
        return response()->json([
            'success' => true,
            'data' => $results,
            'status' => $status,
            'statusLabel' => $statusLabels[$status] ?? $status,
            'count' => count($results)
        ]);
    }
}
