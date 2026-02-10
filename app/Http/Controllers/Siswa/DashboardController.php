<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Penilaian;
use App\Models\PresensiSiswa;
use App\Models\AdminSekolah;
use App\Models\Rombel;
use App\Services\EffectiveDateService;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();

        // Check if impersonating
        $isImpersonating = session('impersonating', false);

        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        // Get effective date (supports testing mode)
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $tanggalHariIni = $effectiveDate['date'];
        $hariIni = $effectiveDate['hari'];
        $isTesting = $effectiveDate['is_testing'];
        $tanggalFormatted = $effectiveDate['formatted'];
        
        // Get siswa's current rombel
        $namaRombel = null;
        $idRombel = null;
        $agamaSiswa = $siswa->agama ?? null;
        
        // Search for rombel from semester 6 to 1
        for ($i = 6; $i >= 1; $i--) {
            $kolomRombel = "rombel_semester_{$i}";
            if (!empty($siswa->$kolomRombel)) {
                $namaRombel = $siswa->$kolomRombel;
                break;
            }
        }
        
        // Get rombel ID
        if ($namaRombel && $tahunAktif && $semesterAktif) {
            $rombel = Rombel::where('nama_rombel', $namaRombel)
                ->where('tahun_pelajaran', $tahunAktif)
                ->whereRaw('LOWER(semester) = LOWER(?)', [$semesterAktif])
                ->first();
            if ($rombel) {
                $idRombel = $rombel->id;
            }
        }

        
        // Get jadwal hari ini
        $jadwalPerMapel = [];
        if ($idRombel && $hariIni !== 'Minggu' && !empty($tahunAktif) && !empty($semesterAktif)) {
            // Query jadwal
            $jadwalQuery = DB::table('jadwal_pelajaran as jp')
                ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
                ->select(
                    'jp.jam_ke', 'jp.nama_guru', 'mp.nama_mapel', 'jp.id_mapel'
                )
                ->where('jp.id_rombel', $idRombel)
                ->where('jp.tahun_pelajaran', $tahunAktif)
                ->whereRaw("LOWER(jp.semester) = LOWER(?)", [$semesterAktif])
                ->where('jp.hari', $hariIni);
            
            // Filter by religion if applicable
            if ($agamaSiswa) {
                $agamaMapelMap = [
                    'Islam' => 'Pendidikan Agama Islam',
                    'Kristen' => 'Pendidikan Agama Kristen',
                    'Katholik' => 'Pendidikan Agama Katholik',
                    'Hindu' => 'Pendidikan Agama Hindu',
                    'Buddha' => 'Pendidikan Agama Buddha',
                    'Konghucu' => 'Pendidikan Agama Konghucu',
                ];
                $matchedAgama = $agamaMapelMap[$agamaSiswa] ?? null;
                
                $jadwalQuery->where(function ($q) use ($matchedAgama) {
                    $q->where('mp.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%');
                    if ($matchedAgama) {
                        $q->orWhere('mp.nama_mapel', $matchedAgama);
                    }
                });
            }
            
            $jadwalRaw = $jadwalQuery->orderByRaw('CAST(jp.jam_ke AS UNSIGNED)')->get();
            
            // Group by mapel and guru, then split by consecutive jam ranges
            $tempJadwal = [];
            foreach ($jadwalRaw as $jadwal) {
                $key = $jadwal->id_mapel . '-' . $jadwal->nama_guru;
                if (!isset($tempJadwal[$key])) {
                    $tempJadwal[$key] = [
                        'nama_mapel' => $jadwal->nama_mapel,
                        'nama_guru' => $jadwal->nama_guru,
                        'jam_list' => [],
                    ];
                }
                $tempJadwal[$key]['jam_list'][] = (int) $jadwal->jam_ke;
            }
            
            // Split by consecutive jam ranges
            foreach ($tempJadwal as $baseKey => $data) {
                $jamList = $data['jam_list'];
                sort($jamList, SORT_NUMERIC);
                
                $ranges = [];
                $currentRange = [$jamList[0]];
                
                for ($i = 1; $i < count($jamList); $i++) {
                    if ($jamList[$i] === $jamList[$i-1] + 1) {
                        $currentRange[] = $jamList[$i];
                    } else {
                        $ranges[] = $currentRange;
                        $currentRange = [$jamList[$i]];
                    }
                }
                $ranges[] = $currentRange;
                
                // Create separate entries for each range
                foreach ($ranges as $rangeIndex => $range) {
                    $uniqueKey = $baseKey . '-range' . $rangeIndex;
                    $jamKeParam = implode(',', $range);
                    
                    // Check presensi for this specific jam range
                    $firstJam = min($range);
                    $jamColumn = "jam_ke_{$firstJam}";
                    
                    $presensiRecord = DB::table('presensi_siswa')
                        ->where('nisn', $siswa->nisn)
                        ->where('tanggal_presensi', $tanggalHariIni)
                        ->where('id_rombel', $idRombel)
                        ->where('mata_pelajaran', $data['nama_mapel'])
                        ->where('tahun_pelajaran', $tahunAktif)
                        ->whereRaw("LOWER(semester) = LOWER(?)", [$semesterAktif])
                        ->whereNotNull($jamColumn)
                        ->where($jamColumn, '!=', '')
                        ->first();
                    
                    $presensiStatus = null;
                    $kehadiranGuruStatus = null;
                    $presensiRecordId = null;
                    if ($presensiRecord) {
                        $presensiStatus = $presensiRecord->$jamColumn;
                        $presensiRecordId = $presensiRecord->id;
                        // Get kehadiran guru for this jam
                        $kehadiranGuruColumn = "kehadiran_guru_{$firstJam}";
                        $kehadiranGuruStatus = $presensiRecord->$kehadiranGuruColumn ?? null;
                    }
                    
                    // Check penilaian for this specific jam range
                    $nilaiRecord = DB::table('penilaian')
                        ->where('nisn', $siswa->nisn)
                        ->where('tanggal_penilaian', $tanggalHariIni)
                        ->where('nama_rombel', $namaRombel)
                        ->where('mapel', $data['nama_mapel'])
                        ->where('jam_ke', $jamKeParam)
                        ->first();
                    
                    // Check izin guru for this specific jam range
                    $izinRecord = null;
                    if (DB::getSchemaBuilder()->hasTable('izin_guru')) {
                        $izinRecord = DB::table('izin_guru')
                            ->where('id_rombel', $idRombel)
                            ->where('tanggal_izin', $tanggalHariIni)
                            ->where('mapel', $data['nama_mapel'])
                            ->where('jam_ke', $jamKeParam)
                            ->first();
                    }
                    
                    $jadwalPerMapel[$uniqueKey] = [
                        'nama_mapel' => $data['nama_mapel'],
                        'nama_guru' => $data['nama_guru'],
                        'jam_list' => $range,
                        'presensi_status' => $presensiStatus,
                        'presensi_record_id' => $presensiRecordId,
                        'kehadiran_guru' => $kehadiranGuruStatus,
                        'nilai' => $nilaiRecord,
                        'izin_guru' => $izinRecord,
                    ];
                }
            }
        }

        // Get recent grades
        $nilaiTerbaru = Penilaian::where('nisn', $siswa->nisn)
            ->orderBy('tanggal_penilaian', 'desc')
            ->limit(5)
            ->get();

        // Get attendance summary for TODAY
        $presensiSummary = null;
        if ($periodik) {
            $presensiSummary = PresensiSiswa::selectRaw("
                SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa
            ")
            ->where('nisn', $siswa->nisn)
            ->where('tanggal_presensi', $tanggalHariIni)
            ->where('tahun_pelajaran', $periodik->tahun_pelajaran)
            ->where('semester', $periodik->semester)
            ->first();
        }

        return view('siswa.dashboard', compact(
            'siswa',
            'periodik',
            'nilaiTerbaru',
            'presensiSummary',
            'isImpersonating',
            'namaRombel',
            'hariIni',
            'tanggalHariIni',
            'tanggalFormatted',
            'isTesting',
            'jadwalPerMapel'
        ));
    }
    
    /**
     * Format jam range for display
     */
    public static function formatJamRange($jamList)
    {
        if (empty($jamList)) return '-';
        
        sort($jamList, SORT_NUMERIC);
        $jamList = array_values(array_unique($jamList));
        
        $count = count($jamList);
        
        if ($count === 1) {
            return $jamList[0];
        }
        
        $start = $jamList[0];
        $end = $jamList[$count - 1];
        
        return $start . '-' . $end;
    }

    /**
     * Save kehadiran guru confirmation from student
     */
    public function saveKehadiranGuru(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        
        $presensiId = $request->input('presensi_id');
        $jamKe = intval($request->input('jam_ke'));
        $status = $request->input('status'); // Tepat Waktu, Terlambat, Tidak Hadir
        
        if (!$presensiId || !$jamKe || !$status) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }
        
        if ($jamKe < 1 || $jamKe > 11) {
            return response()->json(['success' => false, 'message' => 'Jam ke tidak valid']);
        }
        
        $allowedStatuses = ['Tepat Waktu', 'Terlambat', 'Tidak Hadir'];
        if (!in_array($status, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid']);
        }
        
        // Find presensi record belonging to this student
        $record = PresensiSiswa::where('id', $presensiId)
            ->where('nisn', $siswa->nisn)
            ->first();
        
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Data presensi tidak ditemukan']);
        }
        
        $column = "kehadiran_guru_{$jamKe}";
        $record->$column = $status;
        $record->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi kehadiran guru berhasil disimpan',
            'status' => $status
        ]);
    }

    /**
     * Stop impersonating and return to admin account
     */
    public function stopImpersonate()
    {
        if (!session('impersonating')) {
            return redirect()->route('siswa.dashboard');
        }

        $adminId = session('original_admin_id');
        
        // Logout siswa
        Auth::guard('siswa')->logout();
        
        // Clear impersonation session
        session()->forget(['impersonating', 'original_admin_id', 'original_admin_username']);
        
        // Login back as admin
        $admin = AdminSekolah::find($adminId);
        if ($admin) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.dashboard')->with('success', 'Kembali ke akun admin');
        }

        return redirect()->route('login');
    }
}
