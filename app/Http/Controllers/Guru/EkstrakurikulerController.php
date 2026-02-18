<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ekstrakurikuler;
use App\Models\AnggotaEkstrakurikuler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerController extends Controller
{
    /**
     * Display list of ekstrakurikuler (Koordinator view)
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru.');
        }

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
        
        return view('guru.koordinator-ekstra.index', compact(
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
            AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)->delete();
            $ekstra->delete();
        }
        
        return redirect()->route('guru.koordinator-ekstra.index')
            ->with('success', 'Data ekstrakurikuler berhasil dihapus!');
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
        
        return view('guru.koordinator-ekstra.create', compact(
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
        
        return redirect()->route('guru.koordinator-ekstra.index')
            ->with('success', 'Ekstrakurikuler berhasil ditambahkan!');
    }
    
    /**
     * Show form to edit ekstrakurikuler
     */
    public function edit($id)
    {
        $ekstra = Ekstrakurikuler::findOrFail($id);
        
        $pembinaList = $this->getPembinaList();
        
        $anggotaTerdaftar = AnggotaEkstrakurikuler::where('ekstrakurikuler_id', $id)
            ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('semester', $ekstra->semester)
            ->pluck('siswa_id')
            ->toArray();
        
        $rombelList = \App\Models\Rombel::where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->whereRaw('LOWER(semester) = ?', [strtolower($ekstra->semester)])
            ->orderBy('nama_rombel', 'asc')
            ->pluck('nama_rombel');
        
        $angkatanList = \App\Models\Siswa::select('angkatan_masuk')
            ->whereNotNull('angkatan_masuk')
            ->distinct()
            ->orderBy('angkatan_masuk', 'desc')
            ->pluck('angkatan_masuk');
        
        return view('guru.koordinator-ekstra.edit', compact(
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
        
        return redirect()->route('guru.koordinator-ekstra.index')
            ->with('success', 'Ekstrakurikuler berhasil diupdate!');
    }
    
    /**
     * Get siswa for ekstrakurikuler modal (AJAX)
     */
    public function getSiswa(Request $request)
    {
        $search = $request->input('search', '');
        $rombel = $request->input('rombel', '');
        $angkatan = $request->input('angkatan', '');
        
        $query = \App\Models\Siswa::query();
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }
        
        if (!empty($rombel)) {
            $query->where('rombel_aktif', $rombel);
        }
        
        if (!empty($angkatan)) {
            $query->where('angkatan_masuk', $angkatan);
        }
        
        $siswa = $query->orderBy('nama', 'asc')->limit(100)->get();
        
        $data = $siswa->map(function($s) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'rombel_aktif' => $s->rombel_aktif,
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
        
        $data = $siswa->map(function($s) {
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'rombel_aktif' => $s->rombel_aktif,
                'angkatan' => $s->angkatan_masuk
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
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
