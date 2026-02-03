<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rombel;
use App\Models\DataPeriodik;
use App\Models\Guru;
use App\Models\Siswa;

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

        return view('admin.rombel.members', compact(
            'rombel', 
            'siswaList', 
            'tahunPelajaran', 
            'semester',
            'rekap',
            'totalLK',
            'totalPR',
            'totalSemua'
        ));
    }
}
