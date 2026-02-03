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

class PenilaianController extends Controller
{
    /**
     * Display penilaian form
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama ?? '';
        
        // Get parameters
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');
        $jamKe = $request->query('jam_ke', '');
        $lockedTanggal = $request->query('tanggal', '');
        $fromPage = $request->query('from', 'tugas_mengajar');
        
        $isLockedMode = !empty($lockedTanggal);
        
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
        
        // Check existing penilaian (view mode)
        $existingPenilaian = [];
        $isViewMode = false;
        $savedMateri = '';
        
        if ($isLockedMode && !empty($lockedTanggal)) {
            // Filter by jam_ke to only show data for this specific pertemuan
            $query = DB::table('penilaian')
                ->where('tanggal_penilaian', $lockedTanggal)
                ->where('mapel', $mapel)
                ->where('nama_rombel', $namaRombel);
            
            // Add jam_ke filter if available
            if (!empty($jamKe)) {
                $query->where('jam_ke', $jamKe);
            }
            
            $penilaianRecords = $query->get();
            
            if ($penilaianRecords->count() > 0) {
                $isViewMode = true;
                foreach ($penilaianRecords as $row) {
                    $existingPenilaian[$row->nisn] = (array) $row;
                    if (empty($savedMateri) && !empty($row->materi)) {
                        $savedMateri = $row->materi;
                    }
                }
            }
        }
        
        return view('guru.penilaian', compact(
            'guru', 'namaGuru', 'rombel', 'namaRombel', 'mapel', 'jamKe',
            'periodik', 'tahunPelajaranAktif', 'semesterAktif',
            'minDate', 'maxDate', 'isLockedMode', 'lockedTanggal', 'fromPage',
            'isMapelAgama', 'agamaMapel', 'siswaList',
            'existingPenilaian', 'isViewMode', 'savedMateri', 'idRombel'
        ));
    }
    
    /**
     * Store penilaian data
     */
    public function store(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama ?? '';
        
        $idRombel = $request->input('id_rombel');
        $mapel = $request->input('mapel');
        $namaRombel = $request->input('nama_rombel');
        $tanggalPenilaian = $request->input('tanggal_penilaian');
        $materi = $request->input('materi', '');
        $nilaiData = $request->input('nilai', []);
        $keteranganData = $request->input('keterangan', []);
        $nisData = $request->input('nis', []);
        $nisnData = $request->input('nisn', []);
        $namaSiswaData = $request->input('nama_siswa', []);
        $jamKe = $request->input('jam_ke', '');
        $fromPage = $request->input('from', 'tugas_mengajar');
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semester = $periodik->semester ?? '';
        
        // Validate
        if (empty($tanggalPenilaian)) {
            return back()->with('error', 'Pilih tanggal penilaian!');
        }
        
        $savedCount = 0;
        
        foreach ($nilaiData as $idSiswa => $nilai) {
            // Only save if nilai is not empty and > 0
            if (!empty($nilai) && floatval($nilai) > 0) {
                $nis = $nisData[$idSiswa] ?? '';
                $nisn = $nisnData[$idSiswa] ?? '';
                $namaSiswa = $namaSiswaData[$idSiswa] ?? '';
                $keterangan = $keteranganData[$idSiswa] ?? '';
                
                // Check if penilaian exists for this student on this date/mapel/rombel
                $existing = DB::table('penilaian')
                    ->where('nisn', $nisn)
                    ->where('mapel', $mapel)
                    ->where('nama_rombel', $namaRombel)
                    ->where('tanggal_penilaian', $tanggalPenilaian)
                    ->first();
                
                if ($existing) {
                    // Update existing
                    DB::table('penilaian')
                        ->where('id', $existing->id)
                        ->update([
                            'nilai' => floatval($nilai),
                            'keterangan' => $keterangan,
                            'materi' => $materi,
                        ]);
                    $savedCount++;
                } else {
                    // Insert new
                    $insertData = [
                        'nama_rombel' => $namaRombel,
                        'mapel' => $mapel,
                        'nama_siswa' => $namaSiswa,
                        'nis' => $nis,
                        'nisn' => $nisn,
                        'tanggal_penilaian' => $tanggalPenilaian,
                        'jam_ke' => $jamKe,
                        'materi' => $materi,
                        'nilai' => floatval($nilai),
                        'keterangan' => $keterangan,
                        'guru' => $namaGuru,
                    ];
                    
                    // Add tahun_pelajaran and semester if columns exist
                    if (DB::getSchemaBuilder()->hasColumn('penilaian', 'tahun_pelajaran')) {
                        $insertData['tahun_pelajaran'] = $tahunPelajaran;
                    }
                    if (DB::getSchemaBuilder()->hasColumn('penilaian', 'semester')) {
                        $insertData['semester'] = $semester;
                    }
                    
                    DB::table('penilaian')->insert($insertData);
                    $savedCount++;
                }
            }
        }
        
        if ($savedCount > 0) {
            $message = "Berhasil menyimpan penilaian untuk $savedCount siswa.";
            
            if ($fromPage === 'dashboard') {
                return redirect()->route('guru.dashboard')
                    ->with('success', $message);
            }
            
            return back()->with('success', $message);
        }
        
        return back()->with('error', 'Tidak ada data nilai yang disimpan. Pastikan nilai tidak kosong.');
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
