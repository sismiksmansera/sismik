<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\DataPeriodik;
use App\Models\AdminSekolah;
use App\Services\EffectiveDateService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $periodik = DataPeriodik::aktif()->first();
        $isImpersonating = session('impersonating', false);
        
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        $namaGuru = $guru->nama ?? '';
        
        // Get effective date (supports testing mode)
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $tanggalHariIni = $effectiveDate['date'];
        $hariIni = $effectiveDate['hari'];
        $isTesting = $effectiveDate['is_testing'];
        
        // Get periodik id for jam pelajaran setting
        $periodikId = $periodik->id ?? 0;
        
        // Get jam pelajaran settings
        $jamSetting = [];
        $jamRow = DB::table('jam_pelajaran_setting')
            ->where('periodik_id', $periodikId)
            ->first();
        
        if ($jamRow) {
            for ($i = 1; $i <= 11; $i++) {
                $jamSetting[$i] = [
                    'mulai' => $jamRow->{"jp_{$i}_mulai"} ?? null,
                    'selesai' => $jamRow->{"jp_{$i}_selesai"} ?? null
                ];
            }
        }
        
        // Query jadwal hari ini
        $jadwalHariIni = [];
        if (!empty($namaGuru) && !empty($tahunAktif) && !empty($semesterAktif)) {
            $jadwalHariIni = DB::table('jadwal_pelajaran as jp')
                ->join('rombel as r', 'jp.id_rombel', '=', 'r.id')
                ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
                ->select(
                    'jp.id_rombel', 'r.nama_rombel', 'jp.jam_ke', 'mp.nama_mapel', 'jp.id_mapel'
                )
                ->where('jp.hari', $hariIni)
                ->where('jp.nama_guru', $namaGuru)
                ->where('jp.tahun_pelajaran', $tahunAktif)
                ->whereRaw("LOWER(jp.semester) = LOWER(?)", [$semesterAktif])
                ->orderBy('r.nama_rombel')
                ->orderByRaw('CAST(jp.jam_ke AS UNSIGNED)')
                ->get();
        }
        
        // Group jadwal per mapel dan rombel, then split by consecutive jam ranges
        $jadwalPerMapelRombel = [];
        $tempJadwal = [];
        
        // First, collect all jams per rombel+mapel
        foreach ($jadwalHariIni as $jadwal) {
            $key = $jadwal->id_rombel . '-' . $jadwal->id_mapel;
            if (!isset($tempJadwal[$key])) {
                $tempJadwal[$key] = [
                    'id_rombel' => $jadwal->id_rombel,
                    'nama_rombel' => $jadwal->nama_rombel,
                    'nama_mapel' => $jadwal->nama_mapel,
                    'jam_list' => [],
                ];
            }
            $tempJadwal[$key]['jam_list'][] = (int) $jadwal->jam_ke;
        }
        
        // Then split by consecutive jam ranges
        foreach ($tempJadwal as $baseKey => $data) {
            $jamList = $data['jam_list'];
            sort($jamList, SORT_NUMERIC);
            
            // Split into consecutive ranges
            $ranges = [];
            $currentRange = [$jamList[0]];
            
            for ($i = 1; $i < count($jamList); $i++) {
                if ($jamList[$i] === $jamList[$i-1] + 1) {
                    // Consecutive, add to current range
                    $currentRange[] = $jamList[$i];
                } else {
                    // Not consecutive, save current range and start new one
                    $ranges[] = $currentRange;
                    $currentRange = [$jamList[$i]];
                }
            }
            $ranges[] = $currentRange; // Don't forget the last range
            
            // Create separate entries for each range
            foreach ($ranges as $rangeIndex => $range) {
                $uniqueKey = $baseKey . '-range' . $rangeIndex;
                $jamKeParam = implode(',', $range);
                
                // Check presensi for this specific jam range
                // The presensi_siswa table has columns jam_ke_1, jam_ke_2, etc.
                // We need to check if ALL jam columns in this range have values
                $firstJam = min($range);
                $jamColumn = "jam_ke_{$firstJam}";
                
                $sudahPresensi = DB::table('presensi_siswa')
                    ->where('tanggal_presensi', $tanggalHariIni)
                    ->where('id_rombel', $data['id_rombel'])
                    ->where('mata_pelajaran', $data['nama_mapel'])
                    ->where('guru_pengajar', $namaGuru)
                    ->where('tahun_pelajaran', $tahunAktif)
                    ->whereRaw("LOWER(semester) = LOWER(?)", [$semesterAktif])
                    ->whereNotNull($jamColumn)
                    ->where($jamColumn, '!=', '')
                    ->exists();
                
                // Check penilaian for this specific jam range
                $sudahPenilaian = DB::table('penilaian')
                    ->where('tanggal_penilaian', $tanggalHariIni)
                    ->where('mapel', $data['nama_mapel'])
                    ->where('nama_rombel', $data['nama_rombel'])
                    ->where('guru', $namaGuru)
                    ->where('jam_ke', $jamKeParam)
                    ->exists();
                
                // Check izin for this specific jam range
                $sudahIzin = false;
                if (DB::getSchemaBuilder()->hasTable('izin_guru')) {
                    $sudahIzin = DB::table('izin_guru')
                        ->where('tanggal_izin', $tanggalHariIni)
                        ->where('mapel', $data['nama_mapel'])
                        ->where('id_rombel', $data['id_rombel'])
                        ->where('guru', $namaGuru)
                        ->where('jam_ke', $jamKeParam)
                        ->exists();
                }
                
                $jadwalPerMapelRombel[$uniqueKey] = [
                    'id_rombel' => $data['id_rombel'],
                    'nama_rombel' => $data['nama_rombel'],
                    'nama_mapel' => $data['nama_mapel'],
                    'jam_list' => $range,
                    'sudah_presensi' => $sudahPresensi,
                    'sudah_penilaian' => $sudahPenilaian,
                    'sudah_izin' => $sudahIzin
                ];
            }
        }
        
        // Calculate stats
        $jumlahKelasHariIni = count($jadwalPerMapelRombel);
        $totalJamHariIni = 0;
        $sudahPresensiCount = 0;
        
        foreach ($jadwalPerMapelRombel as $j) {
            $totalJamHariIni += count($j['jam_list']);
            if ($j['sudah_presensi']) {
                $sudahPresensiCount++;
            }
        }
        
        $persentasePresensi = $jumlahKelasHariIni > 0 
            ? round(($sudahPresensiCount / $jumlahKelasHariIni) * 100) 
            : 0;
        
        // Get rombel where this guru is wali kelas
        $rombelWali = null;
        if ($periodik) {
            $rombelWali = Rombel::where('wali_kelas', $guru->nama)
                ->where('tahun_pelajaran', $periodik->tahun_pelajaran)
                ->where('semester', strtolower($periodik->semester))
                ->first();
        }

        return view('guru.dashboard', compact(
            'guru', 'periodik', 'rombelWali', 'isImpersonating',
            'hariIni', 'tanggalHariIni', 'jadwalPerMapelRombel', 'jamSetting',
            'jumlahKelasHariIni', 'totalJamHariIni', 'persentasePresensi', 'isTesting'
        ));
    }
    
    /**
     * Get Indonesian day name
     */
    private function getHariIndonesia($dayOfWeek)
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
        return $days[$dayOfWeek] ?? 'Senin';
    }
    
    /**
     * Format jam range
     */
    public static function formatJamRange($jamList)
    {
        if (empty($jamList)) return '-';
        
        sort($jamList, SORT_NUMERIC);
        $jamList = array_values(array_unique($jamList));
        
        $ranges = [];
        $count = count($jamList);
        
        if ($count === 1) {
            return "Jam ke-" . $jamList[0];
        }
        
        $start = $jamList[0];
        $end = $start;
        
        for ($i = 1; $i < $count; $i++) {
            $current = $jamList[$i];
            if ($current === $end + 1) {
                $end = $current;
            } else {
                $ranges[] = ($start === $end) ? (string) $start : $start . '-' . $end;
                $start = $current;
                $end = $current;
            }
        }
        
        $ranges[] = ($start === $end) ? (string) $start : $start . '-' . $end;
        
        return "Jam ke-" . implode(', ', $ranges);
    }

    /**
     * Stop impersonating and return to admin account
     */
    public function stopImpersonate()
    {
        if (!session('impersonating')) {
            return redirect()->route('guru.dashboard');
        }

        $adminId = session('original_admin_id');
        
        Auth::guard('guru')->logout();
        session()->forget(['impersonating', 'impersonate_type', 'original_admin_id', 'original_admin_username']);
        
        $admin = AdminSekolah::find($adminId);
        if ($admin) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.guru.index')->with('success', 'Kembali ke akun admin');
        }

        return redirect()->route('login');
    }
}

