<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class LihatNilaiController extends Controller
{
    public function index(Request $request)
    {
        // Get logged in guru
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        // Get parameters
        $idRombel = $request->get('id_rombel');
        $mapel = $request->get('mapel');

        if (empty($idRombel) || empty($mapel)) {
            return redirect()->route('guru.tugas-mengajar')->with('error', 'Parameter tidak lengkap.');
        }

        // Get rombel data
        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        $namaRombel = $rombel->nama_rombel ?? '';

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        // Parse tahun pelajaran
        $tahunAwal = explode('/', $tahunPelajaran)[0] ?? date('Y');
        $tahunAktif = (int) $tahunAwal;

        // Build where conditions for semester-based rombel
        $whereConditions = [];
        $tahunAngkatanMin = $tahunAktif - 2;
        $tahunAngkatanMax = $tahunAktif;

        for ($tahunAngkatan = $tahunAngkatanMax; $tahunAngkatan >= $tahunAngkatanMin; $tahunAngkatan--) {
            $selisihTahun = $tahunAktif - $tahunAngkatan;
            
            if (strtolower($semesterAktif) == 'ganjil') {
                $semesterKe = ($selisihTahun * 2) + 1;
            } else {
                $semesterKe = ($selisihTahun * 2) + 2;
            }
            
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = '$namaRombel')";
            }
        }

        if (empty($whereConditions)) {
            $semesters = strtolower($semesterAktif) == 'ganjil' ? [1, 3, 5] : [2, 4, 6];
            foreach ($semesters as $sem) {
                $whereConditions[] = "rombel_semester_$sem = '$namaRombel'";
            }
        }

        $whereClause = implode(' OR ', $whereConditions);

        // Get all students in rombel
        $siswaList = DB::select("
            SELECT id, nis, nisn, nama, angkatan_masuk, foto
            FROM siswa 
            WHERE ($whereClause)
            ORDER BY nama ASC
        ");

        // Get nilai data
        $nilaiResult = DB::select("
            SELECT nis, nisn, nama_siswa, tanggal_penilaian, materi, nilai, keterangan
            FROM penilaian
            WHERE nama_rombel = '$namaRombel' AND mapel = '$mapel'
            ORDER BY tanggal_penilaian ASC, nama_siswa ASC
        ");

        // Initialize nilai data for all students
        $nilaiData = [];
        foreach ($siswaList as $siswa) {
            $key = $siswa->nisn . '_' . md5($siswa->nama);
            $nilaiData[$key] = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'angkatan' => $siswa->angkatan_masuk,
                'foto' => $siswa->foto,
                'nilai' => []
            ];
        }

        // Fill nilai data
        foreach ($nilaiResult as $row) {
            $key = $row->nisn . '_' . md5($row->nama_siswa);
            
            if (!isset($nilaiData[$key])) {
                $nilaiData[$key] = [
                    'id' => '',
                    'nama' => $row->nama_siswa,
                    'nis' => $row->nis,
                    'nisn' => $row->nisn,
                    'angkatan' => '',
                    'foto' => '',
                    'nilai' => []
                ];
            }
            
            $nilaiData[$key]['nilai'][$row->tanggal_penilaian] = [
                'nilai' => $row->nilai,
                'keterangan' => $row->keterangan,
                'materi' => $row->materi
            ];
        }

        // Sort by name
        uasort($nilaiData, function($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        // Calculate statistics
        $totalSiswa = count($nilaiData);
        $siswaSudahDinilai = 0;
        $siswaBelumDinilai = 0;

        foreach ($nilaiData as $data) {
            if (!empty($data['nilai'])) {
                $siswaSudahDinilai++;
            } else {
                $siswaBelumDinilai++;
            }
        }

        // Search filter
        $searchQuery = $request->get('search', '');
        $filteredData = $nilaiData;

        if (!empty($searchQuery)) {
            $searchLower = strtolower($searchQuery);
            $filteredData = array_filter($nilaiData, function($siswa) use ($searchLower) {
                return strpos(strtolower($siswa['nama']), $searchLower) !== false ||
                       strpos(strtolower($siswa['nis']), $searchLower) !== false ||
                       strpos(strtolower($siswa['nisn']), $searchLower) !== false;
            });
        }

        $filteredTotal = count($filteredData);

        return view('guru.lihat-nilai', compact(
            'guru',
            'idRombel',
            'mapel',
            'namaRombel',
            'tahunPelajaran',
            'semesterAktif',
            'filteredData',
            'totalSiswa',
            'siswaSudahDinilai',
            'siswaBelumDinilai',
            'searchQuery',
            'filteredTotal'
        ));
    }
    
    /**
     * Display selector page for sidebar access
     */
    public function selector()
    {
        $guru = Auth::guard('guru')->user();
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
        // Get distinct mapel for this teacher in active period
        $namaGuru = $guru->nama;
        $mapelList = DB::select("
            SELECT DISTINCT m.id, m.nama_mapel
            FROM jadwal_pelajaran j
            JOIN mata_pelajaran m ON j.id_mapel = m.id
            WHERE j.nama_guru = ?
            AND j.tahun_pelajaran = ?
            AND j.semester = ?
            ORDER BY m.nama_mapel ASC
        ", [$namaGuru, $tahunPelajaran, $semesterAktif]);
        
        return view('guru.lihat-nilai-selector', compact(
            'guru',
            'tahunPelajaran',
            'semesterAktif',
            'mapelList'
        ));
    }
    
    /**
     * Get nilai data for selected mapel and rombel via AJAX
     */
    public function getNilaiData(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');
        
        if (empty($idRombel) || empty($mapel)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }
        
        // Get rombel data
        $rombel = DB::table('rombel')->where('id', $idRombel)->first();
        $namaRombel = $rombel->nama_rombel ?? '';
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        // Parse tahun pelajaran
        $tahunAwal = explode('/', $tahunPelajaran)[0] ?? date('Y');
        $tahunAktif = (int) $tahunAwal;
        
        // Build where conditions for semester-based rombel
        $whereConditions = [];
        $tahunAngkatanMin = $tahunAktif - 2;
        $tahunAngkatanMax = $tahunAktif;
        
        for ($tahunAngkatan = $tahunAngkatanMax; $tahunAngkatan >= $tahunAngkatanMin; $tahunAngkatan--) {
            $selisihTahun = $tahunAktif - $tahunAngkatan;
            
            if (strtolower($semesterAktif) == 'ganjil') {
                $semesterKe = ($selisihTahun * 2) + 1;
            } else {
                $semesterKe = ($selisihTahun * 2) + 2;
            }
            
            if ($semesterKe <= 6) {
                $whereConditions[] = "(angkatan_masuk = $tahunAngkatan AND rombel_semester_$semesterKe = '$namaRombel')";
            }
        }
        
        if (empty($whereConditions)) {
            $semesters = strtolower($semesterAktif) == 'ganjil' ? [1, 3, 5] : [2, 4, 6];
            foreach ($semesters as $sem) {
                $whereConditions[] = "rombel_semester_$sem = '$namaRombel'";
            }
        }
        
        $whereClause = implode(' OR ', $whereConditions);
        
        // Get all students in rombel
        $siswaList = DB::select("
            SELECT id, nis, nisn, nama, angkatan_masuk, foto
            FROM siswa 
            WHERE ($whereClause)
            ORDER BY nama ASC
        ");
        
        // Get nilai data
        $nilaiResult = DB::select("
            SELECT nis, nisn, nama_siswa, tanggal_penilaian, materi, nilai, keterangan
            FROM penilaian
            WHERE nama_rombel = '$namaRombel' AND mapel = '$mapel'
            ORDER BY tanggal_penilaian ASC, nama_siswa ASC
        ");
        
        // Initialize nilai data for all students
        $nilaiData = [];
        foreach ($siswaList as $siswa) {
            $key = $siswa->nisn . '_' . md5($siswa->nama);
            
            // Calculate initials
            $namaParts = explode(' ', $siswa->nama);
            $initials = '';
            foreach ($namaParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
            }
            $initials = $initials ?: strtoupper(substr($siswa->nama, 0, 1));
            
            // Check foto
            $fotoExists = !empty($siswa->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
            $fotoPath = $fotoExists ? asset('storage/siswa/' . $siswa->foto) : '';
            
            $nilaiData[$key] = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'angkatan' => $siswa->angkatan_masuk,
                'foto_path' => $fotoPath,
                'foto_exists' => $fotoExists,
                'initials' => $initials,
                'nilai' => []
            ];
        }
        
        // Fill nilai data
        foreach ($nilaiResult as $row) {
            $key = $row->nisn . '_' . md5($row->nama_siswa);
            
            if (!isset($nilaiData[$key])) {
                $initials = strtoupper(substr($row->nama_siswa, 0, 1));
                $nilaiData[$key] = [
                    'id' => '',
                    'nama' => $row->nama_siswa,
                    'nis' => $row->nis,
                    'nisn' => $row->nisn,
                    'angkatan' => '',
                    'foto_path' => '',
                    'foto_exists' => false,
                    'initials' => $initials,
                    'nilai' => []
                ];
            }
            
            $nilaiData[$key]['nilai'][] = [
                'tanggal' => $row->tanggal_penilaian,
                'tanggal_formatted' => \Carbon\Carbon::parse($row->tanggal_penilaian)->format('d M Y'),
                'nilai' => $row->nilai,
                'keterangan' => $row->keterangan,
                'materi' => $row->materi
            ];
        }
        
        // Sort by name
        uasort($nilaiData, function($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });
        
        // Calculate statistics
        $totalSiswa = count($nilaiData);
        $siswaSudahDinilai = 0;
        $siswaBelumDinilai = 0;
        
        foreach ($nilaiData as &$data) {
            if (!empty($data['nilai'])) {
                $siswaSudahDinilai++;
                
                // Calculate min, max, avg
                $nilaiValues = array_map(function($n) { return floatval($n['nilai']); }, $data['nilai']);
                $data['min_nilai'] = min($nilaiValues);
                $data['max_nilai'] = max($nilaiValues);
                $data['avg_nilai'] = round(array_sum($nilaiValues) / count($nilaiValues), 1);
                $data['sudah_dinilai'] = true;
            } else {
                $siswaBelumDinilai++;
                $data['min_nilai'] = 0;
                $data['max_nilai'] = 0;
                $data['avg_nilai'] = 0;
                $data['sudah_dinilai'] = false;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => array_values($nilaiData),
            'nama_rombel' => $namaRombel,
            'total_siswa' => $totalSiswa,
            'siswa_sudah_dinilai' => $siswaSudahDinilai,
            'siswa_belum_dinilai' => $siswaBelumDinilai
        ]);
    }
}
