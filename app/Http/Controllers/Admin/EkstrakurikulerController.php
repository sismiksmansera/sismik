<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ekstrakurikuler;
use App\Models\AnggotaEkstrakurikuler;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerController extends Controller
{
    /**
     * Display list of ekstrakurikuler
     */
    public function index(Request $request)
    {
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        // Filter values
        $filterTahun = $request->get('tahun', $tahunAktif);
        $filterSemester = $request->get('semester', $semesterAktif);
        
        // Validate tahun format
        if (!empty($filterTahun) && !preg_match('/^\d{4}\/\d{4}$/', $filterTahun)) {
            $filterTahun = $tahunAktif;
        }
        
        // Normalize semester
        $filterSemester = ucfirst(strtolower($filterSemester));
        if (!in_array($filterSemester, ['Ganjil', 'Genap'])) {
            $filterSemester = $semesterAktif;
        }
        
        // Query ekstrakurikuler with counts
        $ekstrakurikulerList = DB::table('ekstrakurikuler as e')
            ->leftJoin('anggota_ekstrakurikuler as ae', 'e.id', '=', 'ae.ekstrakurikuler_id')
            ->select(
                'e.*',
                DB::raw('COUNT(DISTINCT ae.siswa_id) as jumlah_anggota')
            )
            ->where('e.tahun_pelajaran', $filterTahun)
            ->where('e.semester', $filterSemester)
            ->groupBy('e.id')
            ->orderBy('e.nama_ekstrakurikuler', 'asc')
            ->get();
        
        // Get prestasi count for each ekstra
        foreach ($ekstrakurikulerList as $ekstra) {
            $ekstra->jumlah_prestasi = DB::table('prestasi_siswa')
                ->where('sumber_prestasi', 'ekstrakurikuler')
                ->where('sumber_id', $ekstra->id)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->count();
        }
        
        // Get all years for dropdown
        $allYears = DB::table('ekstrakurikuler')
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderByDesc('tahun_pelajaran')
            ->pluck('tahun_pelajaran');
        
        // Count active ekstrakurikuler
        $totalAktif = DB::table('ekstrakurikuler')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->count();
        
        return view('admin.ekstrakurikuler.index', compact(
            'ekstrakurikulerList', 'filterTahun', 'filterSemester',
            'tahunAktif', 'semesterAktif', 'allYears', 'totalAktif'
        ));
    }
    
    /**
     * Delete ekstrakurikuler
     */
    public function destroy($id)
    {
        $ekstra = Ekstrakurikuler::find($id);
        
        if ($ekstra) {
            // Delete related anggota first
            AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)->delete();
            $ekstra->delete();
        }
        
        return redirect()->route('admin.ekstrakurikuler.index')
            ->with('success', 'Data ekstrakurikuler berhasil dihapus!');
    }
    
    /**
     * Copy ekstrakurikuler from previous semester
     */
    public function copy(Request $request)
    {
        $sourceTahun = $request->input('source_tahun');
        $sourceSemester = $request->input('source_semester');
        $salinPembina = $request->input('salin_pembina', false);
        $salinAnggota = $request->input('salin_anggota', false);
        
        // Get active period as target
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $targetTahun = $periodeAktif->tahun_pelajaran;
        $targetSemester = $periodeAktif->semester;
        
        // Get source ekstrakurikuler
        $sourceList = Ekstrakurikuler::where('tahun_pelajaran', $sourceTahun)
            ->where('semester', $sourceSemester)
            ->get();
        
        $count = 0;
        foreach ($sourceList as $source) {
            // Check if already exists in target
            $exists = Ekstrakurikuler::where('nama_ekstrakurikuler', $source->nama_ekstrakurikuler)
                ->where('tahun_pelajaran', $targetTahun)
                ->where('semester', $targetSemester)
                ->exists();
            
            if (!$exists) {
                $newEkstra = new Ekstrakurikuler();
                $newEkstra->nama_ekstrakurikuler = $source->nama_ekstrakurikuler;
                $newEkstra->tahun_pelajaran = $targetTahun;
                $newEkstra->semester = $targetSemester;
                
                if ($salinPembina) {
                    $newEkstra->pembina_1 = $source->pembina_1;
                    $newEkstra->pembina_2 = $source->pembina_2;
                    $newEkstra->pembina_3 = $source->pembina_3;
                }
                
                $newEkstra->save();
                
                // Copy anggota if requested
                if ($salinAnggota) {
                    $anggotaList = AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $source->id)->get();
                    foreach ($anggotaList as $anggota) {
                        AnggotaEkstrakurikuler::create([
                            'ekstrakurikuler_id' => $newEkstra->id,
                            'siswa_id' => $anggota->siswa_id,
                        ]);
                    }
                }
                
                $count++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Berhasil menyalin $count ekstrakurikuler ke periode aktif."
        ]);
    }
    
    /**
     * Get ekstrakurikuler list for preview copy
     */
    public function previewCopy(Request $request)
    {
        $tahun = $request->get('tahun');
        $semester = $request->get('semester');
        
        $list = Ekstrakurikuler::where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->get(['id', 'nama_ekstrakurikuler', 'pembina_1', 'pembina_2', 'pembina_3']);
        
        return response()->json($list);
    }
}
