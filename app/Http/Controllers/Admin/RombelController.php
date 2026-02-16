<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\DataPeriodik;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\KatrolNilaiSettings;
use App\Models\NilaiKatrol;

class RombelController extends Controller
{
    /**
     * Display list of rombel
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';
        
        // Get filter values
        $tahunFilter = $request->get('tahun', $tahunAktif);
        $semesterFilter = strtolower($request->get('semester', $semesterAktif));
        
        // Build query
        $query = Rombel::query();
        
        if (!empty($tahunFilter)) {
            $query->where('tahun_pelajaran', $tahunFilter);
        }
        
        if (!empty($semesterFilter)) {
            $query->whereRaw('LOWER(semester) = ?', [$semesterFilter]);
        }
        
        $rombelList = $query->orderBy('tahun_pelajaran', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->sort(function($a, $b) {
                // Natural sort by nama_rombel (X.10 comes after X.9)
                return strnatcmp($a->nama_rombel, $b->nama_rombel);
            })
            ->values();
        
        // Get all years for dropdown
        $tahunList = Rombel::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        // Get guru list for wali kelas selection
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->get();
        
        return view('admin.rombel.index', compact(
            'admin',
            'rombelList',
            'tahunList',
            'guruList',
            'tahunFilter',
            'semesterFilter',
            'tahunAktif',
            'semesterAktif'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->get();
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunList = DataPeriodik::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        return view('admin.rombel.create', compact('guruList', 'periodeAktif', 'tahunList'));
    }

    /**
     * Store new rombel
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_pelajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap,ganjil,genap',
            'nama_rombel' => 'required|string|max:50',
            'tingkat' => 'required|in:X,XI,XII',
            'wali_kelas' => 'required|string|max:100',
        ]);

        // Check duplicate
        $exists = Rombel::where('tahun_pelajaran', $request->tahun_pelajaran)
            ->whereRaw('LOWER(semester) = ?', [strtolower($request->semester)])
            ->where('nama_rombel', $request->nama_rombel)
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['nama_rombel' => 'Rombel dengan nama yang sama sudah ada di periode ini.'])->withInput();
        }

        Rombel::create([
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'semester' => ucfirst(strtolower($request->semester)),
            'nama_rombel' => $request->nama_rombel,
            'tingkat' => $request->tingkat,
            'wali_kelas' => $request->wali_kelas,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.rombel.index', [
            'tahun' => $request->tahun_pelajaran,
            'semester' => strtolower($request->semester)
        ])->with('success', 'Rombel baru berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $rombel = Rombel::findOrFail($id);
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->get();
        $tahunList = DataPeriodik::select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        return view('admin.rombel.edit', compact('rombel', 'guruList', 'tahunList'));
    }

    /**
     * Update rombel
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_pelajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap,ganjil,genap',
            'nama_rombel' => 'required|string|max:50',
            'tingkat' => 'required|in:X,XI,XII',
            'wali_kelas' => 'required|string|max:100',
        ]);

        $rombel = Rombel::findOrFail($id);
        
        // Check duplicate (exclude current)
        $exists = Rombel::where('tahun_pelajaran', $request->tahun_pelajaran)
            ->whereRaw('LOWER(semester) = ?', [strtolower($request->semester)])
            ->where('nama_rombel', $request->nama_rombel)
            ->where('id', '!=', $id)
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['nama_rombel' => 'Rombel dengan nama yang sama sudah ada di periode ini.'])->withInput();
        }

        $rombel->update([
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'semester' => ucfirst(strtolower($request->semester)),
            'nama_rombel' => $request->nama_rombel,
            'tingkat' => $request->tingkat,
            'wali_kelas' => $request->wali_kelas,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.rombel.index', [
            'tahun' => $request->tahun_pelajaran,
            'semester' => strtolower($request->semester)
        ])->with('success', 'Data rombel berhasil diperbarui!');
    }

    /**
     * Delete rombel
     */
    public function destroy($id)
    {
        $rombel = Rombel::findOrFail($id);
        $tahun = $rombel->tahun_pelajaran;
        $semester = strtolower($rombel->semester);
        $rombel->delete();

        return redirect()->route('admin.rombel.index', [
            'tahun' => $tahun,
            'semester' => $semester
        ])->with('success', 'Data rombel berhasil dihapus!');
    }

    /**
     * Copy rombel from one period to another (AJAX)
     */
    public function copyRombel(Request $request)
    {
        $tahunAsal = $request->input('tahun_asal');
        $semesterAsal = $request->input('semester_asal');
        $tahunTujuan = $request->input('tahun_tujuan');
        $semesterTujuan = $request->input('semester_tujuan');
        $salinAnggota = $request->input('salin_anggota') == '1';

        // Get source rombels
        $sourceRombels = Rombel::where('tahun_pelajaran', $tahunAsal)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semesterAsal)])
            ->get();

        if ($sourceRombels->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada rombel di periode sumber.']);
        }

        $copied = 0;
        foreach ($sourceRombels as $source) {
            // Check if already exists
            $exists = Rombel::where('tahun_pelajaran', $tahunTujuan)
                ->whereRaw('LOWER(semester) = ?', [strtolower($semesterTujuan)])
                ->where('nama_rombel', $source->nama_rombel)
                ->exists();

            if (!$exists) {
                $newRombel = Rombel::create([
                    'tahun_pelajaran' => $tahunTujuan,
                    'semester' => ucfirst($semesterTujuan),
                    'nama_rombel' => $source->nama_rombel,
                    'tingkat' => $source->tingkat,
                    'wali_kelas' => $source->wali_kelas,
                    'latitude' => $source->latitude,
                    'longitude' => $source->longitude,
                ]);
                
                // Copy students if requested
                if ($salinAnggota && $newRombel) {
                    $siswaList = Siswa::where('nama_rombel', $source->nama_rombel)->get();
                    // Note: In real implementation, you might need to update siswa's rombel reference
                }
                
                $copied++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyalin $copied rombel.",
            'rombel_dicopy' => $copied
        ]);
    }

    /**
     * Get members of a rombel with semester-based query
     */
    public function members(Request $request, $id)
    {
        $rombel = Rombel::findOrFail($id);
        $rombelNama = $rombel->nama_rombel;
        
        // Get tahun and semester from query params or use rombel's values
        $tahunPelajaran = $request->get('tahun', $rombel->tahun_pelajaran);
        $semester = $request->get('semester', $rombel->semester);
        
        // Parse tahun pelajaran
        $tahunParts = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunParts[0] ?? date('Y'));
        
        // Build dynamic query conditions based on semester mapping
        $query = Siswa::query();
        
        $query->where(function($q) use ($tahunAwal, $semester, $rombelNama) {
            if ($semester == 'Ganjil') {
                // Semester Ganjil: 1, 3, 5
                $q->orWhere(function($sub) use ($tahunAwal, $rombelNama) {
                    $sub->where('angkatan_masuk', $tahunAwal)
                        ->where('rombel_semester_1', $rombelNama);
                });
                $q->orWhere(function($sub) use ($tahunAwal, $rombelNama) {
                    $sub->where('angkatan_masuk', $tahunAwal - 1)
                        ->where('rombel_semester_3', $rombelNama);
                });
                $q->orWhere(function($sub) use ($tahunAwal, $rombelNama) {
                    $sub->where('angkatan_masuk', $tahunAwal - 2)
                        ->where('rombel_semester_5', $rombelNama);
                });
            } else {
                // Semester Genap: 2, 4, 6
                $q->orWhere(function($sub) use ($tahunAwal, $rombelNama) {
                    $sub->where('angkatan_masuk', $tahunAwal)
                        ->where('rombel_semester_2', $rombelNama);
                });
                $q->orWhere(function($sub) use ($tahunAwal, $rombelNama) {
                    $sub->where('angkatan_masuk', $tahunAwal - 1)
                        ->where('rombel_semester_4', $rombelNama);
                });
                $q->orWhere(function($sub) use ($tahunAwal, $rombelNama) {
                    $sub->where('angkatan_masuk', $tahunAwal - 2)
                        ->where('rombel_semester_6', $rombelNama);
                });
            }
        });
        
        $siswaList = $query->orderBy('nama')->get();
        
        // Calculate rekapitulasi by agama and gender
        $rekap = [];
        $totalLK = 0;
        $totalPR = 0;
        
        foreach ($siswaList as $siswa) {
            $agama = $siswa->agama ?: 'Tidak Diketahui';
            $jk = $siswa->jk;
            
            if (!isset($rekap[$agama])) {
                $rekap[$agama] = ['Laki-laki' => 0, 'Perempuan' => 0, 'total' => 0];
            }
            
            if ($jk == 'Laki-laki') {
                $rekap[$agama]['Laki-laki']++;
                $totalLK++;
            } else {
                $rekap[$agama]['Perempuan']++;
                $totalPR++;
            }
            $rekap[$agama]['total']++;
        }
        
        $totalSemua = $totalLK + $totalPR;

        // Get katrol settings
        $katrolSettings = KatrolNilaiSettings::where('rombel_id', $id)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semester)
            ->first();
        
        $isLocked = $katrolSettings->is_locked ?? false;
        $lockedNilaiMin = $katrolSettings->nilai_min ?? 65;
        $lockedNilaiMax = $katrolSettings->nilai_max ?? 95;

        return view('admin.rombel.members', compact(
            'rombel', 
            'siswaList', 
            'tahunPelajaran', 
            'semester',
            'rekap',
            'totalLK',
            'totalPR',
            'totalSemua',
            'isLocked',
            'lockedNilaiMin',
            'lockedNilaiMax'
        ));
    }

    /**
     * AJAX: Preview katrol nilai (grade adjustment)
     */
    public function katrolPreview(Request $request, $id)
    {
        try {
            $rombel = Rombel::findOrFail($id);
            $rombelNama = $rombel->nama_rombel;
            
            $tahunPelajaran = $request->input('tahun', $rombel->tahun_pelajaran);
            $semester = $request->input('semester', $rombel->semester);
            $minBaru = floatval($request->input('min_baru', 65));
            $maxBaru = floatval($request->input('max_baru', 95));

            // Get active period
            $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
            $tahunAktif = $periodeAktif->tahun_pelajaran ?? $tahunPelajaran;
            $semesterAktif = $periodeAktif->semester ?? $semester;

            // Build where clause for siswa in this rombel
            $tahunParts = explode('/', $tahunPelajaran);
            $tahunAwal = intval($tahunParts[0] ?? date('Y'));
            $semesterLower = strtolower($semester);

            $whereConditions = [];
            if ($semesterLower == 'ganjil') {
                $whereConditions[] = "(angkatan_masuk = {$tahunAwal} AND rombel_semester_1 = '{$rombelNama}')";
                $whereConditions[] = "(angkatan_masuk = " . ($tahunAwal - 1) . " AND rombel_semester_3 = '{$rombelNama}')";
                $whereConditions[] = "(angkatan_masuk = " . ($tahunAwal - 2) . " AND rombel_semester_5 = '{$rombelNama}')";
            } else {
                $whereConditions[] = "(angkatan_masuk = {$tahunAwal} AND rombel_semester_2 = '{$rombelNama}')";
                $whereConditions[] = "(angkatan_masuk = " . ($tahunAwal - 1) . " AND rombel_semester_4 = '{$rombelNama}')";
                $whereConditions[] = "(angkatan_masuk = " . ($tahunAwal - 2) . " AND rombel_semester_6 = '{$rombelNama}')";
            }
            $whereClause = implode(' OR ', $whereConditions);

            // Get siswa NISNs
            $siswaRows = DB::select("SELECT nisn, nama FROM siswa WHERE {$whereClause} ORDER BY nama");
            $siswaList = [];
            foreach ($siswaRows as $row) {
                $siswaList[$row->nisn] = $row->nama;
            }

            if (empty($siswaList)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada siswa dalam rombel ini']);
            }

            $nisnIn = "'" . implode("','", array_keys($siswaList)) . "'";

            // Get all grades
            $nilaiRows = DB::select("
                SELECT p.nisn, p.mapel, AVG(p.nilai) as rata_nilai
                FROM penilaian p
                WHERE p.nisn COLLATE utf8mb4_general_ci IN ({$nisnIn})
                  AND p.tahun_pelajaran COLLATE utf8mb4_general_ci = ?
                  AND p.semester COLLATE utf8mb4_general_ci = ?
                GROUP BY p.nisn, p.mapel
                ORDER BY p.mapel, p.nisn
            ", [$tahunAktif, $semesterAktif]);

            if (empty($nilaiRows)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data nilai untuk periode ini']);
            }

            // Collect grades per mapel for min/max calculation
            $nilaiPerMapel = [];
            $dataRaw = [];

            foreach ($nilaiRows as $row) {
                $mapel = $row->mapel;
                $nilai = floatval($row->rata_nilai);
                $nilaiPerMapel[$mapel][] = $nilai;
                $dataRaw[] = [
                    'nisn' => $row->nisn,
                    'nama_siswa' => $siswaList[$row->nisn] ?? 'Unknown',
                    'mapel' => $mapel,
                    'nilai_lama' => round($nilai, 1)
                ];
            }

            // Calculate min/max per mapel
            $mapelRanges = [];
            foreach ($nilaiPerMapel as $mapel => $nilaiArr) {
                $mapelRanges[$mapel] = ['min' => min($nilaiArr), 'max' => max($nilaiArr)];
            }

            // Transform grades with linear normalization per mapel
            $resultData = [];
            foreach ($dataRaw as $row) {
                $mapel = $row['mapel'];
                $nilaiLama = $row['nilai_lama'];
                $minLama = $mapelRanges[$mapel]['min'];
                $maxLama = $mapelRanges[$mapel]['max'];

                if ($maxLama == $minLama) {
                    $nilaiBaru = ($minBaru + $maxBaru) / 2;
                } else {
                    $nilaiBaru = (($nilaiLama - $minLama) / ($maxLama - $minLama)) * ($maxBaru - $minBaru) + $minBaru;
                }

                $resultData[] = [
                    'nisn' => $row['nisn'],
                    'nama_siswa' => $row['nama_siswa'],
                    'mapel' => $mapel,
                    'nilai_lama' => $nilaiLama,
                    'nilai_baru' => round($nilaiBaru, 1)
                ];

                // Save to nilai_katrol
                DB::statement("
                    INSERT INTO nilai_katrol (rombel_id, tahun_pelajaran, semester, nisn, mapel, nilai_asli, nilai_katrol)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE nilai_asli = VALUES(nilai_asli), nilai_katrol = VALUES(nilai_katrol)
                ", [$id, $tahunAktif, $semesterAktif, $row['nisn'], $mapel, $nilaiLama, round($nilaiBaru, 1)]);
            }

            // Generate IPA and IPS averages
            $ipaMapels = ['Biologi', 'Fisika', 'Kimia'];
            $ipsMapels = ['Sejarah', 'Ekonomi', 'Sosiologi', 'Geografi'];
            $nilaiPerSiswa = [];

            foreach ($resultData as $row) {
                $nilaiPerSiswa[$row['nisn']]['nama'] = $row['nama_siswa'];
                $nilaiPerSiswa[$row['nisn']]['nilai'][$row['mapel']] = $row['nilai_baru'];
            }

            foreach ($nilaiPerSiswa as $nisn => $siswaData) {
                // IPA
                $ipaValues = [];
                foreach ($ipaMapels as $m) {
                    if (isset($siswaData['nilai'][$m])) $ipaValues[] = $siswaData['nilai'][$m];
                }
                if (!empty($ipaValues)) {
                    $ipaAvg = round(array_sum($ipaValues) / count($ipaValues), 1);
                    DB::statement("
                        INSERT INTO nilai_katrol (rombel_id, tahun_pelajaran, semester, nisn, mapel, nilai_asli, nilai_katrol)
                        VALUES (?, ?, ?, ?, 'IPA', ?, ?)
                        ON DUPLICATE KEY UPDATE nilai_asli = VALUES(nilai_asli), nilai_katrol = VALUES(nilai_katrol)
                    ", [$id, $tahunAktif, $semesterAktif, $nisn, $ipaAvg, $ipaAvg]);
                }

                // IPS
                $ipsValues = [];
                foreach ($ipsMapels as $m) {
                    if (isset($siswaData['nilai'][$m])) $ipsValues[] = $siswaData['nilai'][$m];
                }
                if (!empty($ipsValues)) {
                    $ipsAvg = round(array_sum($ipsValues) / count($ipsValues), 1);
                    DB::statement("
                        INSERT INTO nilai_katrol (rombel_id, tahun_pelajaran, semester, nisn, mapel, nilai_asli, nilai_katrol)
                        VALUES (?, ?, ?, ?, 'IPS', ?, ?)
                        ON DUPLICATE KEY UPDATE nilai_asli = VALUES(nilai_asli), nilai_katrol = VALUES(nilai_katrol)
                    ", [$id, $tahunAktif, $semesterAktif, $nisn, $ipsAvg, $ipsAvg]);
                }
            }

            // === SAVE TO LEGER TABLE (WIDE FORMAT) ===
            // Group data by student (nisn) and prepare mapel values
            $studentDataLeger = [];
            foreach ($resultData as $row) {
                $nisn = $row['nisn'];
                if (!isset($studentDataLeger[$nisn])) {
                    $studentDataLeger[$nisn] = [
                        'nama_siswa' => $row['nama_siswa'],
                        'values' => []
                    ];
                }
                // Convert mapel name to column name
                $columnName = strtolower(str_replace(' ', '_', $row['mapel']));
                $studentDataLeger[$nisn]['values'][$columnName] = $row['nilai_baru'];
            }

            // Calculate and add IPA/IPS for each student
            $nilaiPerSiswaLeger = [];
            foreach ($resultData as $row) {
                $nilaiPerSiswaLeger[$row['nisn']]['nilai'][$row['mapel']] = $row['nilai_baru'];
            }

            foreach ($nilaiPerSiswaLeger as $nisn => $siswaData) {
                $ipaMapels = ['Biologi', 'Fisika', 'Kimia'];
                $ipsMapels = ['Sejarah', 'Ekonomi', 'Sosiologi', 'Geografi'];
                
                // Calculate IPA average
                $ipaValues = [];
                foreach ($ipaMapels as $m) {
                    if (isset($siswaData['nilai'][$m])) $ipaValues[] = $siswaData['nilai'][$m];
                }
                if (!empty($ipaValues)) {
                    $ipaAvg = round(array_sum($ipaValues) / count($ipaValues), 1);
                    $studentDataLeger[$nisn]['values']['ipa'] = $ipaAvg;
                }

                // Calculate IPS average
                $ipsValues = [];
                foreach ($ipsMapels as $m) {
                    if (isset($siswaData['nilai'][$m])) $ipsValues[] = $siswaData['nilai'][$m];
                }
                if (!empty($ipsValues)) {
                    $ipsAvg = round(array_sum($ipsValues) / count($ipsValues), 1);
                    $studentDataLeger[$nisn]['values']['ips'] = $ipsAvg;
                }
            }

            // Insert/Update each student's row in leger table
            foreach ($studentDataLeger as $nisn => $data) {
                // Build the data array for this student
                $legerRow = array_merge(
                    [
                        'rombel_id' => $id,
                        'tahun_pelajaran' => $tahunAktif,
                        'semester' => $semesterAktif,
                        'nisn' => $nisn,
                        'nama_siswa' => $data['nama_siswa'],
                        'nilai_min_baru' => $minBaru,
                        'nilai_max_baru' => $maxBaru,
                        'generated_by' => auth()->user()->name ?? 'System',
                    ],
                    $data['values'] // Add all mapel column values
                );
                
                // Use updateOrInsert for clean upsert
                DB::table('katrol_nilai_leger')->updateOrInsert(
                    [
                        'rombel_id' => $id,
                        'tahun_pelajaran' => $tahunAktif,
                        'semester' => $semesterAktif,
                        'nisn' => $nisn
                    ],
                    $legerRow
                );
            }

            // Sort by mapel then nama
            usort($resultData, function($a, $b) {
                $cmp = strcmp($a['mapel'], $b['mapel']);
                return $cmp == 0 ? strcmp($a['nama_siswa'], $b['nama_siswa']) : $cmp;
            });

            return response()->json([
                'success' => true,
                'data' => $resultData,
                'total' => count($resultData),
                'saved' => true,
                'ipa_ips_generated' => true,
                'history_id' => $historyId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Toggle lock/unlock katrol nilai settings
     */
    public function katrolToggleLock(Request $request, $id)
    {
        $tahunPelajaran = $request->input('tahun');
        $semester = $request->input('semester');
        $nilaiMin = floatval($request->input('nilai_min', 65));
        $nilaiMax = floatval($request->input('nilai_max', 95));
        $adminName = Auth::guard('admin')->user()->nama ?? 'Admin';

        $existing = KatrolNilaiSettings::where('rombel_id', $id)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semester)
            ->first();

        if ($existing) {
            $newStatus = !$existing->is_locked;
            $existing->update([
                'is_locked' => $newStatus,
                'nilai_min' => $nilaiMin,
                'nilai_max' => $nilaiMax,
                'locked_by' => $adminName,
                'locked_at' => now(),
            ]);
            $message = $newStatus
                ? 'Pengaturan katrol nilai berhasil dikunci. Guru hanya bisa melihat tanpa mengubah.'
                : 'Pengaturan katrol nilai berhasil dibuka. Guru bisa mengubah settingan.';
        } else {
            KatrolNilaiSettings::create([
                'rombel_id' => $id,
                'tahun_pelajaran' => $tahunPelajaran,
                'semester' => $semester,
                'nilai_min' => $nilaiMin,
                'nilai_max' => $nilaiMax,
                'is_locked' => true,
                'locked_by' => $adminName,
                'locked_at' => now(),
            ]);
            $message = 'Pengaturan katrol nilai berhasil dikunci. Guru hanya bisa melihat tanpa mengubah.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}
