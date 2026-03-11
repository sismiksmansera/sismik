<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use App\Models\DataPeriodik;

class MataPelajaranController extends Controller
{
    /**
     * Display the mata pelajaran page for a rombel
     */
    public function index(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';
        
        // Get rombel data
        $rombel = Rombel::findOrFail($id);
        
        // Use parameters or defaults to rombel's period
        $tahunPelajaran = $request->get('tahun', $rombel->tahun_pelajaran);
        $semester = $request->get('semester', $rombel->semester);
        
        // Get all mata pelajaran
        $mapelList = MataPelajaran::orderBy('id')->get();
        
        // Build mapel info array - query each mapel individually like legacy code
        $mapelInfo = [];
        foreach ($mapelList as $mapel) {
            // Query for this specific mapel's jadwal (like legacy code)
            $jadwalQuery = DB::table('jadwal_pelajaran')
                ->select('nama_guru', DB::raw('COUNT(DISTINCT CONCAT(hari, jam_ke)) as total_jam'))
                ->where('id_mapel', $mapel->id)
                ->where('id_rombel', $id)
                ->where('tahun_pelajaran', $tahunPelajaran)
                ->where('semester', $semester)
                ->groupBy('nama_guru')
                ->orderByDesc('total_jam')
                ->first();
            
            $guruPengampu = $jadwalQuery && $jadwalQuery->nama_guru ? $jadwalQuery->nama_guru : '-';
            $jumlahJam = $jadwalQuery ? (int) $jadwalQuery->total_jam : 0;
            
            $mapelInfo[$mapel->id] = [
                'guru' => $guruPengampu,
                'jam' => $jumlahJam,
                'aktif' => $jumlahJam > 0
            ];
        }
        
        // Get jadwal for this rombel with this period (for conflict checking)
        $jadwalData = JadwalPelajaran::where('id_rombel', $id)
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semester)
            ->get();
        
        // Count total hours (excluding non-Islam agama for main count)
        $totalJam = JadwalPelajaran::where('jadwal_pelajaran.id_rombel', $id)
            ->where('jadwal_pelajaran.tahun_pelajaran', $tahunPelajaran)
            ->where('jadwal_pelajaran.semester', $semester)
            ->join('mata_pelajaran', 'jadwal_pelajaran.id_mapel', '=', 'mata_pelajaran.id')
            ->where(function($q) {
                $q->where('mata_pelajaran.nama_mapel', 'Pendidikan Agama Islam')
                  ->orWhere('mata_pelajaran.nama_mapel', 'NOT LIKE', 'Pendidikan Agama%');
            })
            ->count();
        
        // Count active mapel
        $mapelAktif = count(array_filter($mapelInfo, fn($info) => $info['aktif']));
        
        // Get guru list for dropdown
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->get();
        
        // Get all jadwal for this rombel (for conflict checking)
        $jadwalTerisi = [];
        foreach ($jadwalData as $jd) {
            $jadwalTerisi[$jd->hari][$jd->jam_ke] = [
                'mapel' => $jd->mapel->nama_mapel ?? '',
                'guru' => $jd->nama_guru,
                'id_mapel' => $jd->id_mapel
            ];
        }
        
        return view('admin.mata-pelajaran.index', compact(
            'admin', 'rombel', 'mapelList', 'mapelInfo', 
            'totalJam', 'mapelAktif', 'guruList', 'jadwalTerisi',
            'tahunPelajaran', 'semester', 'tahunAktif', 'semesterAktif'
        ));
    }
    
    /**
     * Get jadwal for a mapel via AJAX
     */
    public function getJadwal(Request $request)
    {
        $idMapel = $request->get('id_mapel');
        $idRombel = $request->get('id_rombel');
        $tahun = $request->get('tahun_pelajaran');
        $semester = $request->get('semester');
        
        // Get jadwal for this mapel
        $jadwal = JadwalPelajaran::where('id_mapel', $idMapel)
            ->where('id_rombel', $idRombel)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->get();
        
        $namaGuru = $jadwal->first()->nama_guru ?? '';
        
        $jadwalArray = [];
        foreach ($jadwal as $jd) {
            $jadwalArray[$jd->hari][] = $jd->jam_ke;
        }
        
        // Get all jadwal for this rombel (for conflict checking)
        $allJadwal = JadwalPelajaran::where('id_rombel', $idRombel)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->where('id_mapel', '!=', $idMapel)
            ->with('mapel')
            ->get();
        
        $jadwalTerisi = [];
        foreach ($allJadwal as $jd) {
            $jadwalTerisi[$jd->hari][] = [
                'jam' => $jd->jam_ke,
                'mapel' => $jd->mapel->nama_mapel ?? '',
                'guru' => $jd->nama_guru
            ];
        }
        
        return response()->json([
            'success' => true,
            'nama_guru' => $namaGuru,
            'jadwal' => $jadwalArray,
            'jadwal_terisi' => $jadwalTerisi
        ]);
    }
    
    /**
     * Save jadwal via AJAX
     */
    public function saveJadwal(Request $request)
    {
        try {
            $idMapel = $request->input('id_mapel');
            $idRombel = $request->input('id_rombel');
            $tahun = $request->input('tahun_pelajaran');
            $semester = $request->input('semester');
            $namaGuru = $request->input('nama_guru');
            $jadwal = $request->input('jadwal', []);
            
            // Check if this is a non-Islam agama subject
            $mapel = MataPelajaran::find($idMapel);
            $isAgamaNonIslam = str_contains($mapel->nama_mapel ?? '', 'Pendidikan Agama') 
                && !str_contains($mapel->nama_mapel ?? '', 'Islam');
            
            // Delete old jadwal
            JadwalPelajaran::where('id_mapel', $idMapel)
                ->where('id_rombel', $idRombel)
                ->where('tahun_pelajaran', $tahun)
                ->where('semester', $semester)
                ->delete();
            
            if ($isAgamaNonIslam) {
                // For non-Islam agama, copy jadwal from Agama Islam
                $agamaIslam = MataPelajaran::where('nama_mapel', 'Pendidikan Agama Islam')->first();
                if ($agamaIslam) {
                    $jadwalIslam = JadwalPelajaran::where('id_mapel', $agamaIslam->id)
                        ->where('id_rombel', $idRombel)
                        ->where('tahun_pelajaran', $tahun)
                        ->where('semester', $semester)
                        ->get();
                    
                    foreach ($jadwalIslam as $jd) {
                        JadwalPelajaran::create([
                            'id_mapel' => $idMapel,
                            'id_rombel' => $idRombel,
                            'hari' => $jd->hari,
                            'jam_ke' => $jd->jam_ke,
                            'nama_guru' => $namaGuru,
                            'tahun_pelajaran' => $tahun,
                            'semester' => $semester
                        ]);
                    }
                }
            } else {
                // Normal jadwal save
                foreach ($jadwal as $hari => $jamArray) {
                    foreach ($jamArray as $jam) {
                        JadwalPelajaran::create([
                            'id_mapel' => $idMapel,
                            'id_rombel' => $idRombel,
                            'hari' => $hari,
                            'jam_ke' => $jam,
                            'nama_guru' => $namaGuru,
                            'tahun_pelajaran' => $tahun,
                            'semester' => $semester
                        ]);
                    }
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get agama Islam ID
     */
    public function getAgamaIslamId()
    {
        $agamaIslam = MataPelajaran::where('nama_mapel', 'Pendidikan Agama Islam')->first();
        return response()->json([
            'id_agama_islam' => $agamaIslam->id ?? null
        ]);
    }
}
