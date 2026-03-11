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
    
    /**
     * Show form to create new ekstrakurikuler
     */
    public function create()
    {
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        // Get pembina list (guru + guru_bk)
        $pembinaList = $this->getPembinaList();
        
        // Get rombel list for modal
        $rombelList = \App\Models\Rombel::where('tahun_pelajaran', $tahunAktif)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semesterAktif)])
            ->orderBy('nama_rombel', 'asc')
            ->pluck('nama_rombel');
        
        // Get angkatan list
        $angkatanList = \App\Models\Siswa::select('angkatan_masuk')
            ->whereNotNull('angkatan_masuk')
            ->distinct()
            ->orderBy('angkatan_masuk', 'desc')
            ->pluck('angkatan_masuk');
        
        return view('admin.ekstrakurikuler.create', compact(
            'tahunAktif', 'semesterAktif', 'pembinaList', 'rombelList', 'angkatanList'
        ));
    }
    
    /**
     * Store new ekstrakurikuler
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ekstrakurikuler' => 'required|min:3'
        ]);
        
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        
        $ekstra = new Ekstrakurikuler();
        $ekstra->nama_ekstrakurikuler = $request->nama_ekstrakurikuler;
        $ekstra->tahun_pelajaran = $periodeAktif->tahun_pelajaran;
        $ekstra->semester = $periodeAktif->semester;
        $ekstra->pembina_1 = $request->pembina_1;
        $ekstra->pembina_2 = $request->pembina_2;
        $ekstra->pembina_3 = $request->pembina_3;
        $ekstra->deskripsi = $request->deskripsi;
        $ekstra->save();
        
        // Save anggota if any
        if ($request->has('anggota_ids') && is_array($request->anggota_ids)) {
            foreach ($request->anggota_ids as $siswaId) {
                AnggotaEkstrakurikuler::create([
                    'ekstrakurikuler_id' => $ekstra->id,
                    'siswa_id' => $siswaId,
                    'tahun_pelajaran' => $periodeAktif->tahun_pelajaran,
                    'semester' => $periodeAktif->semester,
                    'tanggal_bergabung' => now()->format('Y-m-d')
                ]);
            }
        }
        
        return redirect()->route('admin.ekstrakurikuler.index')
            ->with('success', 'Ekstrakurikuler berhasil ditambahkan!');
    }
    
    /**
     * Show form to edit ekstrakurikuler
     */
    public function edit($id)
    {
        $ekstra = Ekstrakurikuler::findOrFail($id);
        
        // Get pembina list
        $pembinaList = $this->getPembinaList();
        
        // Get anggota terdaftar
        $anggotaTerdaftar = AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)
            ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('semester', $ekstra->semester)
            ->pluck('siswa_id')
            ->toArray();
        
        // Get rombel list for modal
        $rombelList = \App\Models\Rombel::where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->whereRaw('LOWER(semester) = ?', [strtolower($ekstra->semester)])
            ->orderBy('nama_rombel', 'asc')
            ->pluck('nama_rombel');
        
        // Get angkatan list
        $angkatanList = \App\Models\Siswa::select('angkatan_masuk')
            ->whereNotNull('angkatan_masuk')
            ->distinct()
            ->orderBy('angkatan_masuk', 'desc')
            ->pluck('angkatan_masuk');
        
        return view('admin.ekstrakurikuler.edit', compact(
            'ekstra', 'pembinaList', 'anggotaTerdaftar', 'rombelList', 'angkatanList'
        ));
    }
    
    /**
     * Update ekstrakurikuler
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekstrakurikuler' => 'required|min:3'
        ]);
        
        $ekstra = Ekstrakurikuler::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $ekstra->nama_ekstrakurikuler = $request->nama_ekstrakurikuler;
            $ekstra->pembina_1 = $request->pembina_1;
            $ekstra->pembina_2 = $request->pembina_2;
            $ekstra->pembina_3 = $request->pembina_3;
            $ekstra->deskripsi = $request->deskripsi;
            $ekstra->save();
            
            // Delete old anggota and save new ones
            AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->delete();
            
            if ($request->has('anggota_ids') && is_array($request->anggota_ids)) {
                foreach ($request->anggota_ids as $siswaId) {
                    AnggotaEkstrakurikuler::create([
                        'ekstrakurikuler_id' => $ekstra->id,
                        'siswa_id' => $siswaId,
                        'tahun_pelajaran' => $ekstra->tahun_pelajaran,
                        'semester' => $ekstra->semester
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.ekstrakurikuler.index')
                ->with('success', 'Ekstrakurikuler berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.ekstrakurikuler.index')
                ->with('error', 'Gagal mengupdate ekstrakurikuler: ' . $e->getMessage());
        }
    }
    
    /**
     * Get siswa for ekstrakurikuler modal (AJAX)
     */
    public function getSiswa(Request $request)
    {
        $search = $request->input('search', '');
        $rombel = $request->input('rombel', '');
        $angkatan = $request->input('angkatan', '');
        $tahun = $request->input('tahun_aktif', '');
        $semester = $request->input('semester_aktif', '');
        
        $query = \App\Models\Siswa::query();
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }
        
        // Filter by rombel: compute which rombel_semester_X to check
        if (!empty($rombel) && !empty($angkatan)) {
            $rombelCol = $this->getRombelColumnForFilter($angkatan, $tahun, $semester);
            if ($rombelCol) {
                $query->where($rombelCol, $rombel);
            }
        } elseif (!empty($rombel)) {
            // Fallback: search across all rombel_semester columns
            $query->where(function($q) use ($rombel) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("rombel_semester_$i", $rombel);
                }
            });
        }
        
        if (!empty($angkatan)) {
            $query->where('angkatan_masuk', $angkatan);
        }
        
        $siswa = $query->orderBy('nama', 'asc')->limit(100)->get();
        
        // Compute rombel_aktif in PHP
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $tahun ?: ($periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $semester ?: ($periodeAktif->semester ?? 'Ganjil');
        
        $data = $siswa->map(function($s) use ($tahunAktif, $semesterAktif) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'rombel_aktif' => $this->computeRombelAktif($s, $tahunAktif, $semesterAktif),
                'angkatan' => $s->angkatan_masuk
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Get siswa by IDs (AJAX)
     */
    public function getSiswaByIds(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return response()->json(['success' => false, 'data' => []]);
        }
        
        $idArray = explode(',', $ids);
        $siswa = \App\Models\Siswa::whereIn('id', $idArray)->get();
        
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        $data = $siswa->map(function($s) use ($tahunAktif, $semesterAktif) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'rombel_aktif' => $this->computeRombelAktif($s, $tahunAktif, $semesterAktif),
                'angkatan' => $s->angkatan_masuk
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Compute rombel_aktif from angkatan_masuk and rombel_semester_* columns
     */
    private function computeRombelAktif($siswa, $tahunPelajaran, $semester)
    {
        $angkatan = $siswa->angkatan_masuk;
        if (!empty($angkatan) && !empty($tahunPelajaran)) {
            $tahunAjaran = explode('/', $tahunPelajaran);
            $tahunAwal = intval($tahunAjaran[0] ?? 0);
            if ($tahunAwal > 0) {
                $tingkat = $tahunAwal - intval($angkatan) + 1;
                if (strtolower($semester) == 'ganjil') {
                    $semCol = ($tingkat * 2) - 1;
                } else {
                    $semCol = $tingkat * 2;
                }
                $rombelCol = 'rombel_semester_' . $semCol;
                if ($semCol >= 1 && $semCol <= 6 && !empty($siswa->$rombelCol)) {
                    return $siswa->$rombelCol;
                }
            }
        }
        
        // Fallback: use latest available rombel
        for ($i = 6; $i >= 1; $i--) {
            $col = "rombel_semester_$i";
            if (!empty($siswa->$col)) {
                return $siswa->$col;
            }
        }
        
        return '-';
    }
    
    /**
     * Get the correct rombel_semester_X column name for DB filtering
     */
    private function getRombelColumnForFilter($angkatan, $tahunPelajaran, $semester)
    {
        if (empty($angkatan) || empty($tahunPelajaran)) return null;
        
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0] ?? 0);
        if ($tahunAwal <= 0) return null;
        
        $tingkat = $tahunAwal - intval($angkatan) + 1;
        if (strtolower($semester ?: '') == 'ganjil') {
            $semCol = ($tingkat * 2) - 1;
        } else {
            $semCol = $tingkat * 2;
        }
        
        if ($semCol >= 1 && $semCol <= 6) {
            return 'rombel_semester_' . $semCol;
        }
        
        return null;
    }
    
    /**
     * Get pembina list (guru + guru_bk)
     */
    private function getPembinaList()
    {
        $guru = \App\Models\Guru::where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->pluck('nama')
            ->toArray();
        
        $guruBK = DB::table('guru_bk')
            ->where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->pluck('nama')
            ->toArray();
        
        $pembinaList = array_merge($guru, $guruBK);
        sort($pembinaList);
        
        return $pembinaList;
    }
}

