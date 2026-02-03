<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Services\EffectiveDateService;

class PresensiController extends Controller
{
    /**
     * Display presensi form
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama ?? '';
        
        // Get parameters
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');
        $lockedJamKe = $request->query('jam_ke', '');
        $lockedTanggal = $request->query('tanggal', '');
        $fromPage = $request->query('from', 'tugas_mengajar');
        
        // Parse locked jam list
        $lockedJamList = [];
        if (!empty($lockedJamKe)) {
            $lockedJamList = array_map('intval', explode(',', $lockedJamKe));
        }
        $isLockedMode = !empty($lockedJamKe) && !empty($lockedTanggal);
        
        // Validate required params
        if (empty($idRombel) || empty($mapel)) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Parameter tidak lengkap (id_rombel / mapel).');
        }
        
        // Get rombel data
        $rombel = Rombel::find($idRombel);
        if (!$rombel) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Data rombel tidak ditemukan.');
        }
        $namaRombel = $rombel->nama_rombel;
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        if (!$periodik) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Periode aktif tidak ditemukan.');
        }
        
        $tahunPelajaranAktif = $periodik->tahun_pelajaran;
        $semesterAktif = $periodik->semester;
        
        // Parse tahun pelajaran
        $tahunAwal = explode('/', $tahunPelajaranAktif)[0];
        $tahunAktif = (int) $tahunAwal;
        $tahunAkhir = $tahunAktif + 1;
        
        // Calculate allowed date range
        if ($semesterAktif == 'Ganjil') {
            $minDate = $tahunAktif . '-07-01';
            $maxDate = $tahunAktif . '-12-31';
        } else {
            $minDate = $tahunAkhir . '-01-01';
            $maxDate = $tahunAkhir . '-06-30';
        }
        
        // Check if mapel is agama
        $isMapelAgama = false;
        $agamaMapel = '';
        $mapelLower = strtolower($mapel);
        
        if (strpos($mapelLower, 'islam') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Islam';
        } elseif (strpos($mapelLower, 'kristen') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Kristen';
        } elseif (strpos($mapelLower, 'katholik') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Katholik';
        } elseif (strpos($mapelLower, 'hindu') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Hindu';
        } elseif (strpos($mapelLower, 'buddha') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Buddha';
        } elseif (strpos($mapelLower, 'agama') !== false) {
            $isMapelAgama = true;
            $agamaMapel = 'Islam';
        }
        
        // Build dynamic where conditions for rombel_semester
        $siswaList = $this->getSiswaByRombel($namaRombel, $tahunAktif, $semesterAktif, $isMapelAgama, $agamaMapel);
        
        // Check existing presensi (view mode)
        $existingPresensi = [];
        $isViewMode = false;
        if ($isLockedMode && !empty($lockedTanggal) && !empty($lockedJamList)) {
            // Check if presensi exists with specific jam_ke columns filled
            $firstJam = min($lockedJamList);
            $jamColumn = "jam_ke_{$firstJam}";
            
            $presensiRecords = DB::table('presensi_siswa')
                ->where('tanggal_presensi', $lockedTanggal)
                ->where('mata_pelajaran', $mapel)
                ->where('id_rombel', $idRombel)
                ->whereNotNull($jamColumn)
                ->where($jamColumn, '!=', '')
                ->get();
            
            if ($presensiRecords->count() > 0) {
                $isViewMode = true;
                foreach ($presensiRecords as $row) {
                    $existingPresensi[$row->nisn] = (array) $row;
                }
            }
        }
        
        return view('guru.presensi', compact(
            'guru', 'namaGuru', 'rombel', 'namaRombel', 'mapel',
            'periodik', 'tahunPelajaranAktif', 'semesterAktif',
            'minDate', 'maxDate', 'isLockedMode', 'lockedTanggal', 
            'lockedJamKe', 'lockedJamList', 'fromPage',
            'isMapelAgama', 'agamaMapel', 'siswaList',
            'existingPresensi', 'isViewMode', 'idRombel'
        ));
    }
    
    /**
     * Store presensi data
     */
    public function store(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama ?? '';
        
        $idRombel = $request->input('id_rombel');
        $mapel = $request->input('mapel');
        $tanggalPresensi = $request->input('tanggal_presensi');
        $jamPelajaran = $request->input('jam_pelajaran', []);
        $presensiData = $request->input('presensi', []);
        $koordinat = $request->input('hidden_koordinat', '');
        $fromPage = $request->input('from', 'tugas_mengajar');
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semester = $periodik->semester ?? '';
        
        // Validate
        if (empty($jamPelajaran)) {
            return back()->with('error', 'Pilih minimal satu jam pelajaran!');
        }
        if (empty($tanggalPresensi)) {
            return back()->with('error', 'Pilih tanggal presensi!');
        }
        if (empty($presensiData)) {
            return back()->with('error', 'Tidak ada data presensi yang dikirim!');
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($presensiData as $idSiswa => $statusPresensi) {
            $siswa = Siswa::find($idSiswa);
            if (!$siswa) continue;
            
            // Build jam columns
            $jamColumns = [];
            foreach ($jamPelajaran as $jamKe) {
                $jamKe = intval($jamKe);
                if ($jamKe >= 1 && $jamKe <= 11) {
                    $jamColumns["jam_ke_$jamKe"] = $statusPresensi;
                }
            }
            
            if (empty($jamColumns)) continue;
            
            // Check if presensi exists
            $existing = DB::table('presensi_siswa')
                ->where('nisn', $siswa->nisn)
                ->where('mata_pelajaran', $mapel)
                ->where('tanggal_presensi', $tanggalPresensi)
                ->first();
            
            if ($existing) {
                // Update existing
                $updateData = array_merge($jamColumns, [
                    'presensi' => $statusPresensi,
                    'tanggal_waktu_record' => now(),
                    'koordinat_melakukan_presensi' => $koordinat,
                ]);
                
                $updated = DB::table('presensi_siswa')
                    ->where('id', $existing->id)
                    ->update($updateData);
                
                if ($updated) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                // Insert new
                $insertData = array_merge($jamColumns, [
                    'nama_siswa' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'presensi' => $statusPresensi,
                    'mata_pelajaran' => $mapel,
                    'tanggal_presensi' => $tanggalPresensi,
                    'koordinat_melakukan_presensi' => $koordinat,
                    'id_rombel' => $idRombel,
                    'tahun_pelajaran' => $tahunPelajaran,
                    'semester' => $semester,
                    'guru_pengajar' => $namaGuru,
                    'tanggal_waktu_record' => now(),
                ]);
                
                $inserted = DB::table('presensi_siswa')->insert($insertData);
                
                if ($inserted) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }
        
        if ($successCount > 0) {
            $message = "Berhasil menyimpan presensi untuk $successCount siswa.";
            if ($errorCount > 0) {
                $message .= " ($errorCount gagal)";
            }
            
            if ($fromPage === 'dashboard') {
                return redirect()->route('guru.dashboard')
                    ->with('success', $message);
            }
            
            return back()->with('success', $message);
        }
        
        return back()->with('error', 'Gagal menyimpan presensi. Silakan coba lagi.');
    }
    
    /**
     * Get students by rombel with dynamic semester logic
     */
    private function getSiswaByRombel($namaRombel, $tahunAktif, $semesterAktif, $isMapelAgama = false, $agamaMapel = '')
    {
        $tahunAngkatanMin = $tahunAktif - 2;
        $tahunAngkatanMax = $tahunAktif;
        
        $whereConditions = [];
        
        for ($tahunAngkatan = $tahunAngkatanMax; $tahunAngkatan >= $tahunAngkatanMin; $tahunAngkatan--) {
            $selisihTahun = $tahunAktif - $tahunAngkatan;
            
            if ($semesterAktif == 'Ganjil') {
                $semesterKe = ($selisihTahun * 2) + 1;
            } else {
                $semesterKe = ($selisihTahun * 2) + 2;
            }
            
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = ?)";
            }
        }
        
        if (empty($whereConditions)) {
            $semesterMap = [
                'Ganjil' => [1, 3, 5],
                'Genap' => [2, 4, 6]
            ];
            $semesters = $semesterMap[$semesterAktif] ?? [1, 3, 5];
            foreach ($semesters as $sem) {
                $whereConditions[] = "rombel_semester_$sem = ?";
            }
        }
        
        $whereClause = implode(' OR ', $whereConditions);
        $bindings = array_fill(0, count($whereConditions), $namaRombel);
        
        $query = Siswa::whereRaw("($whereClause)", $bindings);
        
        if ($isMapelAgama && !empty($agamaMapel)) {
            $query->where('agama', $agamaMapel);
        }
        
        return $query->orderBy('nama')->get();
    }
}
