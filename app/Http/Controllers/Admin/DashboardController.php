<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\GuruBK;
use App\Models\Rombel;
use App\Models\DataPeriodik;
use App\Services\EffectiveDateService;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        // Statistics
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::where('status', 'Aktif')->count();
        $totalGuruBK = GuruBK::where('status', 'Aktif')->count();
        $totalRombel = $periodik ? Rombel::where('tahun_pelajaran', $periodik->tahun_pelajaran)
            ->where('semester', strtolower($periodik->semester))
            ->count() : 0;

        // Siswa by gender
        $siswaLaki = Siswa::where('jk', 'Laki-laki')->count();
        $siswaPerempuan = Siswa::where('jk', 'Perempuan')->count();

        // Siswa by tingkat
        $siswaTingkat = Siswa::selectRaw("
            SUBSTRING(nama_rombel, 1, 2) as tingkat,
            COUNT(*) as jumlah
        ")->whereNotNull('nama_rombel')->groupBy('tingkat')->get();
        
        // Get effective date (supports testing mode)
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $tanggalHariIni = $effectiveDate['date'];
        $hariIni = $effectiveDate['hari'];
        $isTesting = $effectiveDate['is_testing'];
        $tanggalFormatted = $effectiveDate['formatted'];
        
        // Get kelas kosong count
        $kelasKosong = 0;
        if ($periodik) {
            $kelasKosong = DB::select("
                SELECT COUNT(*) as kosong FROM rombel r
                LEFT JOIN (
                    SELECT rombel_semester_1, COUNT(*) as jumlah_siswa 
                    FROM siswa 
                    GROUP BY rombel_semester_1
                ) s ON r.nama_rombel COLLATE utf8mb4_general_ci = s.rombel_semester_1 COLLATE utf8mb4_general_ci
                WHERE r.tahun_pelajaran = ?
                AND LOWER(r.semester) = LOWER(?)
                AND (s.jumlah_siswa IS NULL OR s.jumlah_siswa = 0)
            ", [$tahunAktif, $semesterAktif])[0]->kosong ?? 0;
        }
        
        // Query jadwal hari ini grouped by rombel
        $jadwalPerRombel = [];
        if (!empty($tahunAktif) && !empty($semesterAktif) && $hariIni !== 'Minggu') {
            $jadwalRaw = DB::table('jadwal_pelajaran as jp')
                ->join('rombel as r', 'jp.id_rombel', '=', 'r.id')
                ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
                ->select(
                    'jp.id_rombel', 'r.nama_rombel', 'r.tingkat', 'jp.jam_ke', 
                    'jp.nama_guru', 'mp.nama_mapel', 'jp.id_mapel'
                )
                ->where('jp.hari', $hariIni)
                ->where('jp.tahun_pelajaran', $tahunAktif)
                ->whereRaw("LOWER(jp.semester) = LOWER(?)", [$semesterAktif])
                ->whereNotNull('jp.nama_guru')
                ->orderBy('r.tingkat')
                ->orderBy('r.nama_rombel')
                ->orderByRaw('CAST(jp.jam_ke AS UNSIGNED)')
                ->get();
            
            // Group jadwal per rombel first
            $tempPerRombel = [];
            foreach ($jadwalRaw as $jadwal) {
                $rombelName = $jadwal->nama_rombel;
                if (!isset($tempPerRombel[$rombelName])) {
                    $tempPerRombel[$rombelName] = [
                        'id_rombel' => $jadwal->id_rombel,
                        'nama_rombel' => $rombelName,
                        'tingkat' => $jadwal->tingkat,
                        'jadwal_list' => [],
                    ];
                }
                $tempPerRombel[$rombelName]['jadwal_list'][] = $jadwal;
            }
            
            // For each rombel, group consecutive jam ranges per mapel+guru
            foreach ($tempPerRombel as $rombelName => $rombelData) {
                $groupedByMapelGuru = [];
                
                foreach ($rombelData['jadwal_list'] as $jadwal) {
                    $key = $jadwal->id_mapel . '-' . $jadwal->nama_guru;
                    if (!isset($groupedByMapelGuru[$key])) {
                        $groupedByMapelGuru[$key] = [
                            'id_rombel' => $jadwal->id_rombel,
                            'nama_mapel' => $jadwal->nama_mapel,
                            'nama_guru' => $jadwal->nama_guru,
                            'jam_list' => [],
                        ];
                    }
                    $groupedByMapelGuru[$key]['jam_list'][] = (int) $jadwal->jam_ke;
                }
                
                // Split by consecutive jam ranges
                $jadwalItems = [];
                foreach ($groupedByMapelGuru as $baseKey => $data) {
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
                        $jamKeParam = implode(',', $range);
                        $firstJam = min($range);
                        $jamColumn = "jam_ke_{$firstJam}";
                        
                        // Check kehadiran guru (presensi exists for this jam slot)
                        $sudahPresensi = DB::table('presensi_siswa')
                            ->where('tanggal_presensi', $tanggalHariIni)
                            ->where('id_rombel', $data['id_rombel'])
                            ->where('mata_pelajaran', $data['nama_mapel'])
                            ->where('guru_pengajar', $data['nama_guru'])
                            ->where('tahun_pelajaran', $tahunAktif)
                            ->whereRaw("LOWER(semester) = LOWER(?)", [$semesterAktif])
                            ->whereNotNull($jamColumn)
                            ->where($jamColumn, '!=', '')
                            ->exists();
                        
                        // Check izin guru for this specific jam range
                        $hasIzin = false;
                        if (DB::getSchemaBuilder()->hasTable('izin_guru')) {
                            $hasIzin = DB::table('izin_guru')
                                ->where('tanggal_izin', $tanggalHariIni)
                                ->where('mapel', $data['nama_mapel'])
                                ->where('id_rombel', $data['id_rombel'])
                                ->where('guru', $data['nama_guru'])
                                ->where('jam_ke', $jamKeParam)
                                ->exists();
                        }
                        
                        // Determine kehadiran status based on student confirmations
                        $kehadiranStatus = 'belum'; // Belum Hadir
                        $kehadiranGuruData = null;
                        if ($hasIzin) {
                            $kehadiranStatus = 'izin';
                        } elseif ($sudahPresensi) {
                            $kehadiranGuruColumn = "kehadiran_guru_{$firstJam}";
                            
                            // Count students who have presensi 'H' (Hadir) for this jam
                            $totalSiswaHadir = DB::table('presensi_siswa')
                                ->where('tanggal_presensi', $tanggalHariIni)
                                ->where('id_rombel', $data['id_rombel'])
                                ->where('mata_pelajaran', $data['nama_mapel'])
                                ->where($jamColumn, 'H')
                                ->count();
                            
                            if ($totalSiswaHadir > 0) {
                                // Count each kehadiran guru status
                                $tepatWaktu = DB::table('presensi_siswa')
                                    ->where('tanggal_presensi', $tanggalHariIni)
                                    ->where('id_rombel', $data['id_rombel'])
                                    ->where('mata_pelajaran', $data['nama_mapel'])
                                    ->where($jamColumn, 'H')
                                    ->where($kehadiranGuruColumn, 'Tepat Waktu')
                                    ->count();
                                
                                $terlambat = DB::table('presensi_siswa')
                                    ->where('tanggal_presensi', $tanggalHariIni)
                                    ->where('id_rombel', $data['id_rombel'])
                                    ->where('mata_pelajaran', $data['nama_mapel'])
                                    ->where($jamColumn, 'H')
                                    ->where($kehadiranGuruColumn, 'Terlambat')
                                    ->count();
                                
                                $tidakHadir = DB::table('presensi_siswa')
                                    ->where('tanggal_presensi', $tanggalHariIni)
                                    ->where('id_rombel', $data['id_rombel'])
                                    ->where('mata_pelajaran', $data['nama_mapel'])
                                    ->where($jamColumn, 'H')
                                    ->where($kehadiranGuruColumn, 'Tidak Hadir')
                                    ->count();
                                
                                $belumKonfirmasi = $totalSiswaHadir - $tepatWaktu - $terlambat - $tidakHadir;
                                
                                $totalConfirmed = $tepatWaktu + $terlambat + $tidakHadir;
                                
                                if ($totalConfirmed > 0) {
                                    $kehadiranStatus = 'terkonfirmasi';
                                    $kehadiranGuruData = [
                                        'total' => $totalConfirmed,
                                        'tepat_waktu' => round(($tepatWaktu / $totalConfirmed) * 100),
                                        'terlambat' => round(($terlambat / $totalConfirmed) * 100),
                                        'tidak_hadir' => round(($tidakHadir / $totalConfirmed) * 100),
                                    ];
                                } else {
                                    $kehadiranStatus = 'belum_terkonfirmasi';
                                }
                            } else {
                                $kehadiranStatus = 'belum_terkonfirmasi';
                            }
                        }
                        
                        // Calculate presensi siswa percentage for this jam slot
                        $presensiPersen = null;
                        if ($sudahPresensi) {
                            $totalSiswaQuery = DB::table('presensi_siswa')
                                ->where('tanggal_presensi', $tanggalHariIni)
                                ->where('id_rombel', $data['id_rombel'])
                                ->where('mata_pelajaran', $data['nama_mapel'])
                                ->whereNotNull($jamColumn)
                                ->where($jamColumn, '!=', '')
                                ->count();
                            
                            $siswaHadir = DB::table('presensi_siswa')
                                ->where('tanggal_presensi', $tanggalHariIni)
                                ->where('id_rombel', $data['id_rombel'])
                                ->where('mata_pelajaran', $data['nama_mapel'])
                                ->where($jamColumn, 'H')
                                ->count();
                            
                            $presensiPersen = $totalSiswaQuery > 0 ? round(($siswaHadir / $totalSiswaQuery) * 100) : 0;
                        }
                        
                        // Check penilaian for this specific jam range
                        $hasPenilaian = DB::table('penilaian')
                            ->where('tanggal_penilaian', $tanggalHariIni)
                            ->where('mapel', $data['nama_mapel'])
                            ->where('nama_rombel', $rombelName)
                            ->where('guru', $data['nama_guru'])
                            ->where('jam_ke', $jamKeParam)
                            ->exists();
                        
                        $jadwalItems[] = [
                            'id_rombel' => $data['id_rombel'],
                            'nama_mapel' => $data['nama_mapel'],
                            'nama_guru' => $data['nama_guru'],
                            'jam_list' => $range,
                            'kehadiran_status' => $kehadiranStatus,
                            'kehadiran_guru_data' => $kehadiranGuruData,
                            'presensi_persen' => $presensiPersen,
                            'has_penilaian' => $hasPenilaian,
                        ];
                    }
                }
                
                // Sort jadwal items by first jam
                usort($jadwalItems, function($a, $b) {
                    return min($a['jam_list']) - min($b['jam_list']);
                });
                
                $jadwalPerRombel[$rombelName] = [
                    'id_rombel' => $rombelData['id_rombel'],
                    'nama_rombel' => $rombelName,
                    'tingkat' => $rombelData['tingkat'],
                    'jadwal_items' => $jadwalItems,
                ];
            }
        }

        return view('admin.dashboard', compact(
            'admin',
            'periodik',
            'totalSiswa',
            'totalGuru',
            'totalGuruBK',
            'totalRombel',
            'siswaLaki',
            'siswaPerempuan',
            'siswaTingkat',
            'hariIni',
            'tanggalHariIni',
            'tanggalFormatted',
            'isTesting',
            'jadwalPerRombel',
            'kelasKosong'
        ));
    }
    
    /**
     * Show detail jadwal - student list with presensi and nilai
     */
    public function detailJadwal(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');
        $tanggal = $request->query('tanggal');
        $guru = $request->query('guru');
        $jamKe = $request->query('jam_ke');
        
        if (empty($idRombel) || empty($mapel) || empty($tanggal)) {
            return redirect()->route('admin.dashboard')->with('error', 'Parameter tidak lengkap');
        }
        
        // Get rombel info
        $rombel = Rombel::find($idRombel);
        $namaRombel = $rombel->nama_rombel ?? '';
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semester = $periodik->semester ?? '';
        
        // Calculate semester column for students based on angkatan
        $tahunAwal = explode('/', $tahunPelajaran)[0] ?? date('Y');
        $tahunAktif = (int) $tahunAwal;
        
        $whereConditions = [];
        $tahunAngkatanMin = $tahunAktif - 2;
        $tahunAngkatanMax = $tahunAktif;
        
        for ($tahunAngkatan = $tahunAngkatanMax; $tahunAngkatan >= $tahunAngkatanMin; $tahunAngkatan--) {
            $selisihTahun = $tahunAktif - $tahunAngkatan;
            
            if (strtolower($semester) === 'ganjil') {
                $semesterKe = ($selisihTahun * 2) + 1;
            } else {
                $semesterKe = ($selisihTahun * 2) + 2;
            }
            
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = {$tahunAngkatan} AND rombel_semester_{$semesterKe} = ?)";
            }
        }
        
        // Query students
        $siswaList = [];
        if (!empty($whereConditions)) {
            $whereClause = implode(' OR ', $whereConditions);
            $bindings = array_fill(0, count($whereConditions), $namaRombel);
            
            $siswaList = DB::select("
                SELECT id, nis, nisn, nama, foto
                FROM siswa 
                WHERE ({$whereClause})
                ORDER BY nama ASC
            ", $bindings);
        }
        
        // Parse jam_ke to get first jam for column lookup
        $jamList = !empty($jamKe) ? explode(',', $jamKe) : [];
        $firstJam = !empty($jamList) ? min(array_map('intval', $jamList)) : 1;
        $jamColumn = "jam_ke_{$firstJam}";
        
        // Get presensi data for this date/mapel/rombel with specific jam
        $presensiData = [];
        $presensiRecords = DB::table('presensi_siswa')
            ->where('tanggal_presensi', $tanggal)
            ->where('mata_pelajaran', $mapel)
            ->where('id_rombel', $idRombel)
            ->get();
        
        foreach ($presensiRecords as $p) {
            $kehadiranGuruColumn = "kehadiran_guru_{$firstJam}";
            // Use the specific jam column value
            $presensiData[$p->nisn] = [
                'presensi' => $p->$jamColumn ?? null,
                'kehadiran_guru' => $p->$kehadiranGuruColumn ?? null,
                'record' => $p
            ];
        }
        
        // Get penilaian data for this date/mapel/rombel with specific jam_ke
        $penilaianData = [];
        $penilaianRecords = DB::table('penilaian')
            ->where('tanggal_penilaian', $tanggal)
            ->where('mapel', $mapel)
            ->where('nama_rombel', $namaRombel)
            ->where('jam_ke', $jamKe)
            ->get();
        
        foreach ($penilaianRecords as $n) {
            $penilaianData[$n->nisn] = $n;
        }
        
        // Status map for presensi
        $statusMap = [
            'H' => ['text' => 'Hadir', 'class' => 'success', 'icon' => 'fa-check'],
            'S' => ['text' => 'Sakit', 'class' => 'warning', 'icon' => 'fa-bed'],
            'I' => ['text' => 'Izin', 'class' => 'info', 'icon' => 'fa-envelope'],
            'A' => ['text' => 'Alpha', 'class' => 'danger', 'icon' => 'fa-times'],
            'D' => ['text' => 'Dispensasi', 'class' => 'primary', 'icon' => 'fa-certificate'],
            'B' => ['text' => 'Bolos', 'class' => 'danger', 'icon' => 'fa-exclamation-triangle']
        ];
        
        // Format jam text
        $jamText = count($jamList) === 1 ? $jamList[0] : min($jamList) . '-' . max($jamList);
        
        return view('admin.detail-jadwal', compact(
            'namaRombel',
            'mapel',
            'tanggal',
            'guru',
            'jamKe',
            'jamText',
            'siswaList',
            'presensiData',
            'penilaianData',
            'statusMap'
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
}
