<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Rombel;

class LegerController extends Controller
{
    /**
     * Display leger page with filters
     */
    public function index()
    {
        // Get unique tahun pelajaran
        $tahunList = DB::table('data_periodik')
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        return view('admin.leger.index', compact('tahunList'));
    }
    
    /**
     * Get semesters for selected tahun pelajaran (AJAX)
     */
    public function getSemesters(Request $request)
    {
        $tahun = $request->tahun_pelajaran;
        $semesters = DB::table('data_periodik')
            ->where('tahun_pelajaran', $tahun)
            ->pluck('semester')
            ->unique()
            ->values();
        
        return response()->json($semesters);
    }
    
    /**
     * Get rombels for selected tahun and semester (AJAX)
     */
    public function getRombels(Request $request)
    {
        $tahun = $request->tahun_pelajaran;
        $semester = $request->semester;
        
        $rombels = Rombel::where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->select('id', 'nama_rombel')
            ->get()
            ->sortBy('nama_rombel', SORT_NATURAL)
            ->values();
        return response()->json($rombels);
    }
    
    /**
     * Get leger data for display (AJAX)
     */
    public function getLegerData(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        // Get all katrol data for this rombel
        $katrolData = DB::table('katrol_nilai_leger')
            ->where('rombel_id', $rombelId)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->orderBy('ranking')
            ->get();
        
        if ($katrolData->isEmpty()) {
            return response()->json([
                'students' => [],
                'mapels' => []
            ]);
        }
        
        // Define all possible mapel columns (24 mapel + IPA/IPS)
        $allMapelColumns = [
            'pendidikan_agama_dan_budi_pekerti', 'pendidikan_pancasila', 'bahasa_indonesia',
            'matematika', 'sejarah_indonesia', 'bahasa_inggris', 'seni_budaya',
            'pendidikan_jasmani_olahraga_dan_kesehatan', 'prakarya_dan_kewirausahaan',
            'matematika_peminatan', 'biologi', 'fisika', 'kimia', 'ekonomi',
            'geografi', 'sosiologi', 'sejarah_peminatan', 'bahasa_dan_sastra_indonesia',
            'bahasa_dan_sastra_inggris', 'antropologi', 'bahasa_dan_sastra_asing_lainnya',
            'informatika', 'bimbingan_konseling', 'mulok', 'ipa_average', 'ips_average'
        ];
        
        // Find which mapels have data (at least one student has non-null value)
        $activeMapels = [];
        foreach ($allMapelColumns as $col) {
            foreach ($katrolData as $row) {
                if (isset($row->$col) && $row->$col !== null && $row->$col !== '') {
                    $activeMapels[] = $col;
                    break;
                }
            }
        }
        
        // Convert column names to display names
        $mapelDisplayNames = [];
        foreach ($activeMapels as $col) {
            $displayName = ucwords(str_replace('_', ' ', $col));
            $mapelDisplayNames[] = $displayName;
        }
        
        // Format student data
        $students = [];
        foreach ($katrolData as $row) {
            $nilai = [];
            foreach ($activeMapels as $col) {
                $displayName = ucwords(str_replace('_', ' ', $col));
                $nilai[$displayName] = $row->$col ?? '-';
            }
            
            $students[] = [
                'nisn' => $row->nisn,
                'nama_siswa' => $row->nama_siswa,
                'nilai' => $nilai,
                'rata_rata' => number_format($row->rata_rata ?? 0, 2),
                'ranking' => $row->ranking ?? '-'
            ];
        }
        
        return response()->json([
            'students' => $students,
            'mapels' => $mapelDisplayNames
        ]);
    }
    /**
     * Print Leger Nilai (Original grades)
     */
    public function printNilai(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahunPelajaran = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (!$rombelId || !$tahunPelajaran || !$semester) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Data rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get wali kelas NIP
        $namaWaliKelas = $rombel->wali_kelas ?? '';
        $nipWaliKelas = '';
        if ($namaWaliKelas) {
            $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
            $nipWaliKelas = $guru->nip ?? '';
        }
        
        // Get active period & kepala sekolah
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? $tahunPelajaran;
        $semesterAktif = $periodeAktif->semester ?? $semester;
        $namaKepala = $periodeAktif->nama_kepala ?? '';
        $nipKepala = $periodeAktif->nip_kepala ?? '';
        
        // Build student query based on semester
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $rombelNama = $rombel->nama_rombel;
        
        if (strtolower($semester) == 'ganjil') {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        } else {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        }
        
        if ($siswaList->isEmpty()) {
            return response('<script>alert("Tidak ada siswa dalam rombel ini!"); window.close();</script>');
        }
        
        // Get mapel list from jadwal (only PAI for agama, skip other agama subjects)
        $semesterJadwal = strtolower($semesterAktif);
        $mapelList = DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $rombelId)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->where('jp.semester', $semesterJadwal)
            ->select('mp.id', 'mp.nama_mapel')
            ->distinct()
            ->orderBy('mp.id')
            ->get()
            ->filter(function($mapel) {
                if (str_contains($mapel->nama_mapel, 'Pendidikan Agama')) {
                    return $mapel->nama_mapel === 'Pendidikan Agama Islam';
                }
                return true;
            })->values();
        
        // Calculate grades for each student
        $legerData = [];
        foreach ($siswaList as $siswa) {
            $nilaiMapel = [];
            $total = 0;
            $count = 0;
            $allNilai = [];
            
            foreach ($mapelList as $mapel) {
                $mapelNama = $mapel->nama_mapel;
                
                // For agama subjects, check student's religion
                if (str_contains($mapelNama, 'Pendidikan Agama')) {
                    $agamaMapel = 'Pendidikan Agama ' . $siswa->agama;
                    $nilaiResult = DB::table('penilaian')
                        ->where('nisn', $siswa->nisn)
                        ->where('mapel', $agamaMapel)
                        ->avg('nilai');
                } else {
                    $nilaiResult = DB::table('penilaian')
                        ->where('nisn', $siswa->nisn)
                        ->where('mapel', $mapelNama)
                        ->avg('nilai');
                }
                
                $nilai = $nilaiResult ? round($nilaiResult, 1) : null;
                $nilaiMapel[$mapel->id] = $nilai;
                $allNilai[] = $nilai;
                
                if ($nilai !== null) {
                    $total += $nilai;
                    $count++;
                }
            }
            
            // Get attendance
            $presensi = DB::table('presensi_siswa')
                ->where('nisn', $siswa->nisn)
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', strtolower($semesterAktif))
                ->selectRaw("COUNT(*) as total, SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir")
                ->first();
            $kehadiran = $presensi && $presensi->total > 0 
                ? round(($presensi->hadir / $presensi->total) * 100, 1) : 0;
            
            // Calculate priority score (sum of top half grades)
            $validNilai = array_filter($allNilai, fn($v) => $v !== null);
            sort($validNilai);
            $half = ceil(count($validNilai) / 2);
            $topHalf = array_slice($validNilai, -$half);
            $skorPrioritas = array_sum($topHalf);
            
            $legerData[] = [
                'id' => $siswa->id,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'jk' => $siswa->jk,
                'agama' => $siswa->agama,
                'nilai_mapel' => $nilaiMapel,
                'total' => round($total, 1),
                'rata_rata' => $count > 0 ? round($total / $count, 1) : 0,
                'kehadiran' => $kehadiran,
                'skor_prioritas' => $skorPrioritas
            ];
        }
        
        // Sort for ranking
        usort($legerData, function($a, $b) {
            if ($a['rata_rata'] != $b['rata_rata']) return $b['rata_rata'] <=> $a['rata_rata'];
            if ($a['kehadiran'] != $b['kehadiran']) return $b['kehadiran'] <=> $a['kehadiran'];
            return $b['skor_prioritas'] <=> $a['skor_prioritas'];
        });
        
        foreach ($legerData as $i => &$data) {
            $data['ranking'] = $i + 1;
        }
        unset($data);
        
        // Sort by name for display
        usort($legerData, fn($a, $b) => strcmp($a['nama'], $b['nama']));
        
        return view('admin.leger.print-nilai', compact(
            'rombel', 'rombelNama', 'mapelList', 'legerData',
            'tahunAktif', 'semesterAktif', 'namaWaliKelas', 'nipWaliKelas',
            'namaKepala', 'nipKepala'
        ));
    }
    
    /**
     * Print Leger Katrol (Adjusted grades from nilai_katrol table)
     */
    public function printKatrol(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahunPelajaran = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (!$rombelId || !$tahunPelajaran || !$semester) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Data rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? $tahunPelajaran;
        $semesterAktif = $periodeAktif->semester ?? $semester;
        
        // Check if katrol data exists in new leger table
        $katrolCount = DB::table('katrol_nilai_leger')
            ->where('rombel_id', $rombelId)
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->count();
        
        if ($katrolCount == 0) {
            return response('<script>alert("Belum ada data nilai katrol untuk rombel ini!"); window.close();</script>');
        }
        
        // Get wali kelas NIP
        $namaWaliKelas = $rombel->wali_kelas ?? '';
        $nipWaliKelas = '';
        if ($namaWaliKelas) {
            $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
            $nipWaliKelas = $guru->nip ?? '';
        }
        
        $namaKepala = $periodeAktif->nama_kepala ?? '';
        $nipKepala = $periodeAktif->nip_kepala ?? '';
        $tingkatRombel = $rombel->tingkat ?? '';
        
        // Build student query
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $rombelNama = $rombel->nama_rombel;
        
        if (strtolower($semester) == 'ganjil') {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        } else {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        }
        
        if ($siswaList->isEmpty()) {
            return response('<script>alert("Tidak ada siswa dalam rombel ini!"); window.close();</script>');
        }
        
        // Get mapel list from jadwal
        $semesterJadwal = strtolower($semesterAktif);
        $mapelList = DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $rombelId)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->where('jp.semester', $semesterJadwal)
            ->select('mp.id', 'mp.nama_mapel')
            ->distinct()
            ->orderBy('mp.id')
            ->get()
            ->filter(function($mapel) {
                if (str_contains($mapel->nama_mapel, 'Pendidikan Agama')) {
                    return $mapel->nama_mapel === 'Pendidikan Agama Islam';
                }
                return true;
            })->values();
        
        // For Kelas X: add IPA and IPS columns
        $ipaMapel = ['Biologi', 'Fisika', 'Kimia'];
        $ipsMapel = ['Sejarah', 'Ekonomi', 'Sosiologi', 'Geografi'];
        $isKelasX = ($tingkatRombel == 'X');
        
        if ($isKelasX) {
            $newMapelList = collect();
            foreach ($mapelList as $mapel) {
                $newMapelList->push($mapel);
                if ($mapel->nama_mapel == 'Kimia') {
                    $newMapelList->push((object)['id' => 'IPA', 'nama_mapel' => 'IPA', 'is_grouped' => true]);
                }
                if ($mapel->nama_mapel == 'Geografi') {
                    $newMapelList->push((object)['id' => 'IPS', 'nama_mapel' => 'IPS', 'is_grouped' => true]);
                }
            }
            $mapelList = $newMapelList;
        }
        
        // Get katrol grades from leger table (wide format)
        $legerData = [];
        foreach ($siswaList as $siswa) {
            // Fetch this student's row from katrol_nilai_leger
            $katrolRow = DB::table('katrol_nilai_leger')
                ->where('rombel_id', $rombelId)
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', $semesterAktif)
                ->where('nisn', $siswa->nisn)
                ->first();
            
            $nilaiMapel = [];
            $total = 0;
            $count = 0;
            
            // Extract nilai from columns
            foreach ($mapelList as $mapel) {
                $mapelNama = $mapel->nama_mapel;
                
                // Convert mapel name to column name (lowercase, replace space with underscore)
                $columnName = strtolower(str_replace(' ', '_', $mapelNama));
                
                // Get value from the column
                $nilai = null;
                if ($katrolRow && isset($katrolRow->$columnName)) {
                    $nilai = floatval($katrolRow->$columnName);
                }
                
                $nilaiMapel[$mapelNama] = $nilai;
                
                // Skip IPA/IPS component subjects for total (Kelas X)
                if ($isKelasX && in_array($mapelNama, $ipaMapel)) continue;
                if ($isKelasX && in_array($mapelNama, $ipsMapel)) continue;
                
                if ($nilai !== null) {
                    $total += $nilai;
                    $count++;
                }
            }
            
            // Get attendance
            $presensi = DB::table('presensi_siswa')
                ->where('nisn', $siswa->nisn)
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', strtolower($semesterAktif))
                ->selectRaw("COUNT(*) as total, SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir")
                ->first();
            $kehadiran = $presensi && $presensi->total > 0 
                ? round(($presensi->hadir / $presensi->total) * 100, 1) : 0;
            
            $legerData[] = [
                'id' => $siswa->id,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'jk' => $siswa->jk,
                'agama' => $siswa->agama,
                'nilai_mapel' => $nilaiMapel,
                'total' => round($total, 1),
                'rata_rata' => $count > 0 ? round($total / $count, 1) : 0,
                'kehadiran' => $kehadiran
            ];
        }
        
        // Sort for ranking
        usort($legerData, function($a, $b) {
            if ($a['rata_rata'] != $b['rata_rata']) return $b['rata_rata'] <=> $a['rata_rata'];
            return $b['kehadiran'] <=> $a['kehadiran'];
        });
        
        foreach ($legerData as $i => &$data) {
            $data['ranking'] = $i + 1;
        }
        unset($data);
        
        // Sort by name for display
        usort($legerData, fn($a, $b) => strcmp($a['nama'], $b['nama']));
        
        return view('admin.leger.print-katrol', compact(
            'rombel', 'rombelNama', 'mapelList', 'legerData',
            'tahunAktif', 'semesterAktif', 'namaWaliKelas', 'nipWaliKelas',
            'namaKepala', 'nipKepala', 'isKelasX', 'ipaMapel', 'ipsMapel'
        ));
    }
}
