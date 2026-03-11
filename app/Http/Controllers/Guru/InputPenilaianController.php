<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use App\Models\Siswa;

class InputPenilaianController extends Controller
{
    /**
     * Display input penilaian page with selector
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaranAktif = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';
        
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
        
        return view('guru.input-penilaian', compact(
            'guru',
            'tahunPelajaranAktif',
            'semesterAktif',
            'minDate',
            'maxDate'
        ));
    }
    
    /**
     * Get mapel options for current teacher
     */
    public function getMapelOptions()
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama;
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaranAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        // Get distinct mapel for this teacher in active period
        $mapelList = DB::select("
            SELECT DISTINCT m.id, m.nama_mapel
            FROM jadwal_pelajaran j
            JOIN mata_pelajaran m ON j.id_mapel = m.id
            WHERE j.nama_guru = ?
            AND j.tahun_pelajaran = ?
            AND j.semester = ?
            ORDER BY m.nama_mapel ASC
        ", [$namaGuru, $tahunPelajaranAktif, $semesterAktif]);
        
        return response()->json([
            'success' => true,
            'data' => $mapelList
        ]);
    }
    
    /**
     * Get rombel options for selected mapel
     */
    public function getRombelOptions(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama;
        $idMapel = $request->query('id_mapel');
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaranAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        // Get distinct rombel for this teacher and mapel
        $rombelList = DB::select("
            SELECT DISTINCT r.id, r.nama_rombel
            FROM jadwal_pelajaran j
            JOIN rombel r ON j.id_rombel = r.id
            WHERE j.nama_guru = ?
            AND j.id_mapel = ?
            AND j.tahun_pelajaran = ?
            AND j.semester = ?
            ORDER BY r.nama_rombel ASC
        ", [$namaGuru, $idMapel, $tahunPelajaranAktif, $semesterAktif]);
        
        return response()->json([
            'success' => true,
            'data' => $rombelList
        ]);
    }
    
    /**
     * Get student list for selected rombel
     */
    public function getSiswaList(Request $request)
    {
        $idRombel = $request->query('id_rombel');
        $namaMapel = $request->query('nama_mapel', '');
        
        // Get rombel data
        $rombel = Rombel::find($idRombel);
        if (!$rombel) {
            return response()->json([
                'success' => false,
                'message' => 'Rombel tidak ditemukan'
            ]);
        }
        $namaRombel = $rombel->nama_rombel;
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaranAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';
        
        // Parse tahun pelajaran
        $tahunAwal = explode('/', $tahunPelajaranAktif)[0];
        $tahunAktif = (int) $tahunAwal;
        
        // Check if mapel is agama
        $isMapelAgama = false;
        $agamaMapel = '';
        $mapelLower = strtolower($namaMapel);
        
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
        
        // Get students by rombel
        $siswaList = $this->getSiswaByRombel($namaRombel, $tahunAktif, $semesterAktif, $isMapelAgama, $agamaMapel);
        
        // Add foto path info
        $siswaData = [];
        foreach ($siswaList as $s) {
            $fotoExists = !empty($s->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $s->foto);
            $fotoPath = $fotoExists ? asset('storage/siswa/' . $s->foto) : '';
            $namaParts = explode(' ', $s->nama);
            $initials = '';
            foreach ($namaParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
            }
            $initials = $initials ?: strtoupper(substr($s->nama, 0, 1));
            
            $siswaData[] = [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis ?? '',
                'nisn' => $s->nisn ?? '',
                'agama' => $s->agama ?? '',
                'foto_path' => $fotoPath,
                'initials' => $initials,
                'foto_exists' => $fotoExists
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $siswaData,
            'is_mapel_agama' => $isMapelAgama,
            'agama_mapel' => $agamaMapel,
            'nama_rombel' => $namaRombel
        ]);
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
