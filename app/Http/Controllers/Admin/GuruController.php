<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\NameCascadeService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruController extends Controller
{
    /**
     * Display a listing of guru
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 25);
        
        $query = Guru::query();
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }
        
        $guruList = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->query());
        
        // Statistics
        $totalGuru = Guru::count();
        $totalAktif = Guru::where('status', 'Aktif')->count();
        $totalNonaktif = $totalGuru - $totalAktif;
        
        return view('admin.guru.index', compact(
            'guruList',
            'search',
            'totalGuru',
            'totalAktif',
            'totalNonaktif'
        ));
    }

    /**
     * Show the import form for guru
     */
    public function showImport()
    {
        return view('admin.guru.import');
    }

    /**
     * Show the form for creating a new guru
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Store a newly created guru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'username' => 'required|string|max:100|unique:guru,username',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'nullable|in:L,P',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status_kepegawaian' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'mapel_diampu' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'foto']);
        $data['password'] = Hash::make($request->password);
        $data['status'] = 'Aktif';

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $filename = 'guru_' . time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->storeAs('guru', $filename, 'public');
            $data['foto'] = $filename;
        }

        Guru::create($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified guru
     */
    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Update the specified guru
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $oldName = $guru->nama; // Store old name for cascade update

        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'username' => 'required|string|max:100|unique:guru,username,' . $id,
            'jenis_kelamin' => 'nullable|in:L,P',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status_kepegawaian' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'mapel_diampu' => 'nullable|string',
            'status' => 'nullable|in:Aktif,Nonaktif',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'foto', '_token', '_method']);

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle foto upload with compression
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($guru->foto && Storage::disk('public')->exists('guru/' . $guru->foto)) {
                Storage::disk('public')->delete('guru/' . $guru->foto);
            }
            
            $file = $request->file('foto');
            $filename = 'guru_' . $id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/guru/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/guru'))) {
                mkdir(storage_path('app/public/guru'), 0755, true);
            }
            
            // Compress image if larger than 250KB
            $maxSizeKB = 250;
            $this->compressImage($file->getPathname(), $uploadPath, $maxSizeKB);
            
            $data['foto'] = $filename;
        }

        $guru->update($data);

        // Cascade name update if name changed
        if ($oldName !== $request->nama) {
            NameCascadeService::updateGuruName($oldName, $request->nama);
        }

        // Redirect back to edit page if only uploading foto
        if ($request->hasFile('foto') && !$request->filled('password') && !$request->has('full_update')) {
            return redirect()->route('admin.guru.edit', $id)->with('success', 'Foto berhasil diperbarui!');
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui!');
    }

    /**
     * Remove the specified guru
     */
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);

        // Delete foto if exists
        if ($guru->foto && Storage::disk('public')->exists('guru/' . $guru->foto)) {
            Storage::disk('public')->delete('guru/' . $guru->foto);
        }

        $guru->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus!');
    }

    /**
     * Reset password for a guru
     */
    public function resetPassword($id)
    {
        $guru = Guru::findOrFail($id);
        
        // Reset password to default (NIP or username)
        $defaultPassword = $guru->nip ?: $guru->username;
        $guru->update(['password' => Hash::make($defaultPassword)]);

        return redirect()->route('admin.guru.index')->with('success', 'Password guru berhasil direset ke NIP/Username!');
    }

    /**
     * Impersonate a guru (Login as Guru)
     */
    public function impersonate($id)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        $guru = Guru::findOrFail($id);

        // Store admin session for returning later
        session([
            'impersonating' => true,
            'impersonate_type' => 'guru',
            'original_admin_id' => $admin->id,
            'original_admin_username' => $admin->username,
        ]);

        // Login as guru
        Auth::guard('guru')->login($guru);

        return redirect()->route('guru.dashboard')->with('success', 'Anda sekarang login sebagai ' . $guru->nama);
    }

    /**
     * Show tugas mengajar (teaching assignments)
     */
    public function tugasMengajar(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $namaGuru = $guru->nama;
        
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';
        
        // Filter values (default to active period)
        $tahunFilter = $request->get('tahun', $tahunAktif);
        $semesterFilter = $request->get('semester', strtolower($semesterAktif));
        
        // Get unique tahun_pelajaran the guru has jadwal
        $tahunList = \DB::table('jadwal_pelajaran')
            ->where('nama_guru', $namaGuru)
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        // Build query for penugasan
        $query = \DB::table('jadwal_pelajaran as j')
            ->join('rombel as r', 'j.id_rombel', '=', 'r.id')
            ->join('mata_pelajaran as m', 'j.id_mapel', '=', 'm.id')
            ->select('j.id_rombel', 'j.id_mapel', 'r.nama_rombel', 'm.nama_mapel', 'j.tahun_pelajaran', 'j.semester')
            ->where('j.nama_guru', $namaGuru)
            ->distinct();
        
        if (!empty($tahunFilter)) {
            $query->where('j.tahun_pelajaran', $tahunFilter);
        }
        if (!empty($semesterFilter)) {
            $query->whereRaw('LOWER(j.semester) = ?', [$semesterFilter]);
        }
        
        $penugasanList = $query->orderBy('j.tahun_pelajaran', 'desc')
            ->orderBy('j.semester', 'desc')
            ->orderBy('r.nama_rombel', 'asc')
            ->orderBy('m.nama_mapel', 'asc')
            ->get();
        
        // Calculate stats
        $rombelSet = [];
        $mapelSet = [];
        foreach ($penugasanList as $p) {
            $rombelSet[$p->id_rombel] = true;
            $mapelSet[$p->id_mapel] = true;
        }
        $totalRombel = count($rombelSet);
        $totalMapel = count($mapelSet);
        
        // Total jam query
        $totalJamQuery = \DB::table('jadwal_pelajaran')
            ->where('nama_guru', $namaGuru);
        if (!empty($tahunFilter)) {
            $totalJamQuery->where('tahun_pelajaran', $tahunFilter);
        }
        if (!empty($semesterFilter)) {
            $totalJamQuery->whereRaw('LOWER(semester) = ?', [$semesterFilter]);
        }
        $totalJam = $totalJamQuery->count();
        
        // Get jadwal per hari for each penugasan
        $penugasanWithJadwal = [];
        foreach ($penugasanList as $p) {
            $jadwalQuery = \DB::table('jadwal_pelajaran')
                ->where('id_mapel', $p->id_mapel)
                ->where('id_rombel', $p->id_rombel)
                ->where('nama_guru', $namaGuru);
            
            if (!empty($tahunFilter)) {
                $jadwalQuery->where('tahun_pelajaran', $tahunFilter);
            }
            if (!empty($semesterFilter)) {
                $jadwalQuery->whereRaw('LOWER(semester) = ?', [$semesterFilter]);
            }
            
            $jadwalData = $jadwalQuery
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam_ke', 'asc')
                ->get();
            
            // Group by hari
            $jadwalPerHari = [];
            foreach ($jadwalData as $j) {
                $jadwalPerHari[$j->hari][] = (int) $j->jam_ke;
            }
            
            // Count jam
            $jamCount = 0;
            foreach ($jadwalPerHari as $hari => $jamArr) {
                $jamCount += count($jamArr);
            }
            
            // Format jam ranges
            $jadwalFormatted = [];
            foreach ($jadwalPerHari as $hari => $jamArr) {
                sort($jamArr, SORT_NUMERIC);
                $jamArr = array_unique($jamArr, SORT_NUMERIC);
                $ranges = $this->formatJamRanges(array_values($jamArr));
                $jadwalFormatted[$hari] = $ranges;
            }
            
            $penugasanWithJadwal[] = [
                'id_rombel' => $p->id_rombel,
                'id_mapel' => $p->id_mapel,
                'nama_rombel' => $p->nama_rombel,
                'nama_mapel' => $p->nama_mapel,
                'tahun_pelajaran' => $p->tahun_pelajaran,
                'semester' => $p->semester,
                'jadwal' => $jadwalFormatted,
                'jam_count' => $jamCount,
            ];
        }
        
        // Get rombel list for active period (for modal)
        $rombelList = \App\Models\Rombel::where('tahun_pelajaran', $tahunAktif)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semesterAktif)])
            ->orderBy('nama_rombel', 'asc')
            ->get();
        
        // Get mapel list (for modal)
        $mapelList = \App\Models\MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        
        return view('admin.guru.tugas-mengajar', compact(
            'guru', 'penugasanWithJadwal', 'tahunList', 'tahunFilter', 'semesterFilter',
            'totalRombel', 'totalMapel', 'totalJam', 'tahunAktif', 'semesterAktif',
            'rombelList', 'mapelList'
        ));
    }
    
    /**
     * Check jadwal konflik for a specific rombel (AJAX)
     */
    public function checkJadwalKonflik(Request $request, $id)
    {
        $idRombel = $request->input('id_rombel');
        $idMapel = $request->input('id_mapel');
        
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodeAktif->semester ?? '');
        
        // Get jadwal terisi for this rombel (all mapel)
        $jadwalTerisi = \DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $idRombel)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->whereRaw('LOWER(jp.semester) = ?', [$semesterAktif])
            ->select('jp.hari', 'jp.jam_ke', 'jp.id_mapel', 'mp.nama_mapel', 'jp.nama_guru')
            ->get();
        
        // Group by hari
        $jadwalByHari = [];
        foreach ($jadwalTerisi as $j) {
            $jadwalByHari[$j->hari][] = [
                'jam' => $j->jam_ke,
                'mapel' => $j->nama_mapel,
                'guru' => $j->nama_guru,
                'id_mapel' => $j->id_mapel
            ];
        }
        
        return response()->json([
            'success' => true,
            'jadwal_terisi' => $jadwalByHari,
            'id_mapel_selected' => $idMapel
        ]);
    }
    
    /**
     * Save penugasan mengajar (AJAX)
     */
    public function savePenugasan(Request $request, $id)
    {
        try {
            $guru = Guru::findOrFail($id);
            $namaGuru = $guru->nama;
            
            $idRombel = $request->input('id_rombel');
            $idMapel = $request->input('id_mapel');
            $jadwal = $request->input('jadwal', []);
            
            // Get active period
            $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
            $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
            $semesterAktif = strtolower($periodeAktif->semester ?? '');
            
            // Delete existing jadwal for this guru + rombel + mapel
            \App\Models\JadwalPelajaran::where('id_mapel', $idMapel)
                ->where('id_rombel', $idRombel)
                ->where('nama_guru', $namaGuru)
                ->where('tahun_pelajaran', $tahunAktif)
                ->whereRaw('LOWER(semester) = ?', [$semesterAktif])
                ->delete();
            
            // Insert new jadwal
            foreach ($jadwal as $hari => $jamArray) {
                foreach ($jamArray as $jam) {
                    \App\Models\JadwalPelajaran::create([
                        'id_mapel' => $idMapel,
                        'id_rombel' => $idRombel,
                        'hari' => $hari,
                        'jam_ke' => $jam,
                        'nama_guru' => $namaGuru,
                        'tahun_pelajaran' => $tahunAktif,
                        'semester' => $semesterAktif
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Penugasan berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete penugasan mengajar (AJAX)
     */
    public function deletePenugasan(Request $request, $id)
    {
        try {
            $guru = Guru::findOrFail($id);
            $namaGuru = $guru->nama;
            
            $idRombel = $request->input('id_rombel');
            $idMapel = $request->input('id_mapel');
            
            // Get active period
            $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
            $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
            $semesterAktif = strtolower($periodeAktif->semester ?? '');
            
            // Delete jadwal for this guru + rombel + mapel
            $deleted = \App\Models\JadwalPelajaran::where('id_mapel', $idMapel)
                ->where('id_rombel', $idRombel)
                ->where('nama_guru', $namaGuru)
                ->where('tahun_pelajaran', $tahunAktif)
                ->whereRaw('LOWER(semester) = ?', [$semesterAktif])
                ->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Penugasan berhasil dihapus! (' . $deleted . ' jadwal dihapus)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Format jam array to ranges (e.g., 1,2,3 => "1-3", 1,3,4 => "1, 3-4")
     */
    private function formatJamRanges($jamArr)
    {
        if (empty($jamArr)) return '';
        
        $count = count($jamArr);
        if ($count === 1) {
            return (string) $jamArr[0];
        }
        
        $ranges = [];
        $start = $jamArr[0];
        $end = $start;
        
        for ($i = 1; $i < $count; $i++) {
            $current = $jamArr[$i];
            if ($current === $end + 1) {
                $end = $current;
            } else {
                $ranges[] = ($start === $end) ? (string) $start : "{$start}–{$end}";
                $start = $current;
                $end = $current;
            }
        }
        $ranges[] = ($start === $end) ? (string) $start : "{$start}–{$end}";
        
        return implode(', ', $ranges);
    }

    /**
     * Show aktivitas guru - monitoring presensi, penilaian, jadwal
     */
    public function aktivitas(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $guruNama = $guru->nama;
        
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        // Filter values
        $filterTahun = $request->get('tahun', $tahunAktif);
        $filterSemester = $request->get('semester', $semesterAktif);
        
        // Calculate date range based on semester
        $tahunParts = explode('/', $filterTahun);
        $tahunAwal = intval($tahunParts[0] ?? date('Y'));
        $tahunAkhir = $tahunAwal + 1;
        
        if ($filterSemester == 'Ganjil') {
            $minDate = $tahunAwal . '-07-01';
            $maxDate = $tahunAwal . '-12-31';
        } else {
            $minDate = $tahunAkhir . '-01-01';
            $maxDate = $tahunAkhir . '-06-30';
        }
        
        $tanggalMulai = $request->get('tanggal_mulai', $minDate);
        $tanggalSelesai = $request->get('tanggal_selesai', date('Y-m-d'));
        
        // Validate date ranges
        if ($tanggalMulai < $minDate) $tanggalMulai = $minDate;
        if ($tanggalMulai > $maxDate) $tanggalMulai = $maxDate;
        if ($tanggalSelesai < $minDate) $tanggalSelesai = $minDate;
        if ($tanggalSelesai > $maxDate) $tanggalSelesai = $maxDate;
        
        // ========== PRESENSI STATS ==========
        $statPresensi = \DB::table('presensi_siswa')
            ->where('guru_pengajar', $guruNama)
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->whereBetween('tanggal_presensi', [$tanggalMulai, $tanggalSelesai])
            ->selectRaw('COUNT(DISTINCT tanggal_presensi) as total_hari, COUNT(*) as total_record')
            ->first();
        
        // Detail presensi with jam_ke columns
        $listPresensiRaw = \DB::table('presensi_siswa as ps')
            ->leftJoin('rombel as r', 'ps.id_rombel', '=', 'r.id')
            ->where('ps.guru_pengajar', $guruNama)
            ->where('ps.tahun_pelajaran', $filterTahun)
            ->where('ps.semester', $filterSemester)
            ->whereBetween('ps.tanggal_presensi', [$tanggalMulai, $tanggalSelesai])
            ->select(
                'ps.tanggal_presensi', 'ps.mata_pelajaran', 'ps.tanggal_waktu_record',
                'r.nama_rombel', 'ps.id_rombel',
                \DB::raw('COUNT(DISTINCT ps.nisn) as jumlah_siswa'),
                \DB::raw("SUM(CASE WHEN ps.presensi = 'H' THEN 1 ELSE 0 END) as hadir"),
                \DB::raw("SUM(CASE WHEN ps.presensi = 'S' THEN 1 ELSE 0 END) as sakit"),
                \DB::raw("SUM(CASE WHEN ps.presensi = 'I' THEN 1 ELSE 0 END) as izin"),
                \DB::raw("SUM(CASE WHEN ps.presensi = 'A' THEN 1 ELSE 0 END) as alfa"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_1 IS NOT NULL AND ps.jam_ke_1 != '' THEN 1 ELSE 0 END) as has_jam_1"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_2 IS NOT NULL AND ps.jam_ke_2 != '' THEN 1 ELSE 0 END) as has_jam_2"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_3 IS NOT NULL AND ps.jam_ke_3 != '' THEN 1 ELSE 0 END) as has_jam_3"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_4 IS NOT NULL AND ps.jam_ke_4 != '' THEN 1 ELSE 0 END) as has_jam_4"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_5 IS NOT NULL AND ps.jam_ke_5 != '' THEN 1 ELSE 0 END) as has_jam_5"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_6 IS NOT NULL AND ps.jam_ke_6 != '' THEN 1 ELSE 0 END) as has_jam_6"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_7 IS NOT NULL AND ps.jam_ke_7 != '' THEN 1 ELSE 0 END) as has_jam_7"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_8 IS NOT NULL AND ps.jam_ke_8 != '' THEN 1 ELSE 0 END) as has_jam_8"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_9 IS NOT NULL AND ps.jam_ke_9 != '' THEN 1 ELSE 0 END) as has_jam_9"),
                \DB::raw("MAX(CASE WHEN ps.jam_ke_10 IS NOT NULL AND ps.jam_ke_10 != '' THEN 1 ELSE 0 END) as has_jam_10")
            )
            ->groupBy('ps.tanggal_presensi', 'ps.mata_pelajaran', 'ps.tanggal_waktu_record', 'r.nama_rombel', 'ps.id_rombel')
            ->orderBy('ps.tanggal_presensi', 'desc')
            ->limit(50)
            ->get();
        
        // ========== PENILAIAN STATS ==========
        $statPenilaian = \DB::table('penilaian')
            ->where('guru', $guruNama)
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->whereBetween('tanggal_penilaian', [$tanggalMulai, $tanggalSelesai])
            ->selectRaw('COUNT(DISTINCT tanggal_penilaian) as total_hari, COUNT(*) as total_record')
            ->first();
        
        $listPenilaian = \DB::table('penilaian')
            ->where('guru', $guruNama)
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->whereBetween('tanggal_penilaian', [$tanggalMulai, $tanggalSelesai])
            ->select('tanggal_penilaian', 'mapel', 'nama_rombel', 'materi', \DB::raw('COUNT(DISTINCT nisn) as jumlah_siswa'))
            ->groupBy('tanggal_penilaian', 'mapel', 'nama_rombel', 'materi')
            ->orderBy('tanggal_penilaian', 'desc')
            ->limit(50)
            ->get();
        
        // ========== JADWAL MENGAJAR ==========
        $semesterJadwal = strtolower($filterSemester);
        $listJadwal = \DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->join('rombel as r', 'jp.id_rombel', '=', 'r.id')
            ->where('jp.nama_guru', $guruNama)
            ->where('jp.tahun_pelajaran', $filterTahun)
            ->where('jp.semester', $semesterJadwal)
            ->select('jp.*', 'mp.nama_mapel', 'r.nama_rombel')
            ->orderByRaw("FIELD(jp.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jp.jam_ke')
            ->get();
        
        $totalJam = $listJadwal->count();
        
        // Build jadwal lookup: hari|id_rombel|mapel => [jam_ke list]
        $jadwalLookup = [];
        foreach ($listJadwal as $j) {
            $key = $j->hari . '|' . $j->id_rombel . '|' . strtolower($j->nama_mapel);
            if (!isset($jadwalLookup[$key])) {
                $jadwalLookup[$key] = [];
            }
            $jadwalLookup[$key][] = $j->jam_ke;
        }
        
        // Get jam pelajaran settings
        $periodikAktif = \DB::table('data_periodik')
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->first();
        
        $jpSetting = null;
        if ($periodikAktif) {
            $jpSetting = \DB::table('jam_pelajaran_setting')
                ->where('periodik_id', $periodikAktif->id)
                ->first();
        }
        
        // Process presensi with jadwal matching
        $listPresensi = [];
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        foreach ($listPresensiRaw as $p) {
            $item = (array) $p;
            
            // Get jam presensi from has_jam_X columns
            $jamPresensi = [];
            for ($j = 1; $j <= 10; $j++) {
                if ($p->{'has_jam_' . $j} == 1) {
                    $jamPresensi[] = $j;
                }
            }
            $item['jam_presensi'] = $jamPresensi;
            $item['jam_presensi_str'] = count($jamPresensi) > 0 ? implode(',', $jamPresensi) : '-';
            
            // Get hari from tanggal
            $hari = $days[date('w', strtotime($p->tanggal_presensi))];
            $item['hari'] = $hari;
            
            // Lookup jadwal
            $lookupKey = $hari . '|' . $p->id_rombel . '|' . strtolower($p->mata_pelajaran);
            $jamJadwal = $jadwalLookup[$lookupKey] ?? [];
            $item['jam_jadwal'] = $jamJadwal;
            $item['jam_jadwal_str'] = count($jamJadwal) > 0 ? implode(',', $jamJadwal) : '-';
            
            // Calculate waktu jadwal
            $item['waktu_jadwal'] = $this->getWaktuJadwal($jamJadwal, $jpSetting);
            
            // Calculate waktu presensi
            $item['waktu_presensi'] = $p->tanggal_waktu_record ? date('H:i', strtotime($p->tanggal_waktu_record)) : '-';
            
            // Check jadwal match
            $jadwalMatch = $this->checkJadwalMatch($jamPresensi, $jamJadwal);
            $item['jadwal_match'] = $jadwalMatch;
            
            // Check time match
            $timeMatch = $this->checkTimeMatch($p->tanggal_waktu_record, $jamJadwal, $jpSetting);
            $item['time_match'] = $timeMatch;
            
            $listPresensi[] = $item;
        }
        
        // ========== KEAKTIFAN PERCENTAGE ==========
        $expectedDays = 20;
        $actualPresensi = $statPresensi->total_hari ?? 0;
        $persentaseKeaktifan = $expectedDays > 0 ? min(100, round(($actualPresensi / $expectedDays) * 100)) : 0;
        
        if ($persentaseKeaktifan >= 80) {
            $warnaIndikator = '#10b981';
            $labelIndikator = 'Sangat Aktif';
        } elseif ($persentaseKeaktifan >= 50) {
            $warnaIndikator = '#f59e0b';
            $labelIndikator = 'Cukup Aktif';
        } else {
            $warnaIndikator = '#ef4444';
            $labelIndikator = 'Kurang Aktif';
        }
        
        // Generate years for filter
        $currentYear = intval(date('Y'));
        $years = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $years[] = $i . '/' . ($i + 1);
        }
        
        return view('admin.guru.aktivitas', compact(
            'guru', 'filterTahun', 'filterSemester', 'tanggalMulai', 'tanggalSelesai',
            'minDate', 'maxDate', 'years',
            'statPresensi', 'listPresensi', 'statPenilaian', 'listPenilaian',
            'listJadwal', 'totalJam',
            'persentaseKeaktifan', 'warnaIndikator', 'labelIndikator'
        ));
    }
    
    /**
     * Get waktu jadwal from jam list and jp settings
     */
    private function getWaktuJadwal($jamList, $jpSetting)
    {
        if (empty($jamList) || !$jpSetting) return '-';
        
        $minJam = min($jamList);
        $maxJam = max($jamList);
        
        $mulai = $jpSetting->{'jp_' . $minJam . '_mulai'} ?? null;
        $selesai = $jpSetting->{'jp_' . $maxJam . '_selesai'} ?? null;
        
        if ($mulai && $selesai) {
            return substr($mulai, 0, 5) . '-' . substr($selesai, 0, 5);
        }
        return '-';
    }
    
    /**
     * Check if presensi jam matches jadwal jam
     */
    private function checkJadwalMatch($jamPresensi, $jamJadwal)
    {
        if (empty($jamJadwal)) {
            return ['status' => 'unknown', 'label' => 'Tidak ada jadwal', 'color' => '#9ca3af'];
        }
        
        if (empty($jamPresensi)) {
            return ['status' => 'unknown', 'label' => 'Tidak ada data', 'color' => '#9ca3af'];
        }
        
        $matching = array_intersect($jamPresensi, $jamJadwal);
        
        if (count($matching) == count($jamPresensi) && count($matching) == count($jamJadwal)) {
            return ['status' => 'match', 'label' => 'JP Sesuai', 'color' => '#10b981'];
        } elseif (count($matching) > 0) {
            return ['status' => 'partial', 'label' => 'JP Sebagian', 'color' => '#f59e0b'];
        } else {
            return ['status' => 'mismatch', 'label' => 'JP Berbeda', 'color' => '#ef4444'];
        }
    }
    
    /**
     * Check if presensi time matches jadwal time
     */
    private function checkTimeMatch($waktuPresensi, $jamJadwal, $jpSetting)
    {
        if (!$waktuPresensi || empty($jamJadwal) || !$jpSetting) {
            return ['status' => 'unknown', 'text' => ''];
        }
        
        $minJam = min($jamJadwal);
        $maxJam = max($jamJadwal);
        
        $mulai = $jpSetting->{'jp_' . $minJam . '_mulai'} ?? null;
        $selesai = $jpSetting->{'jp_' . $maxJam . '_selesai'} ?? null;
        
        if (!$mulai || !$selesai) {
            return ['status' => 'unknown', 'text' => ''];
        }
        
        $presensiTime = strtotime(date('H:i', strtotime($waktuPresensi)));
        $mulaiTime = strtotime($mulai);
        $selesaiTime = strtotime($selesai);
        $tolerance = 15 * 60; // 15 minutes
        
        if ($presensiTime >= ($mulaiTime - $tolerance) && $presensiTime <= ($selesaiTime + $tolerance)) {
            return ['status' => 'match', 'text' => 'Presensi sesuai jadwal'];
        } else {
            return ['status' => 'mismatch', 'text' => 'Presensi tidak dilakukan pada waktunya'];
        }
    }

    /**
     * Delete via AJAX
     */
    public function deleteAjax(Request $request)
    {
        $id = $request->input('id');
        $guru = Guru::find($id);
        
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan']);
        }

        // Delete foto if exists
        if ($guru->foto && Storage::disk('public')->exists('guru/' . $guru->foto)) {
            Storage::disk('public')->delete('guru/' . $guru->foto);
        }

        $guru->delete();

        return response()->json(['success' => true, 'message' => 'Guru berhasil dihapus']);
    }

    /**
     * Compress image to target size (max KB)
     */
    private function compressImage($source, $destination, $maxSizeKB = 250)
    {
        $maxSizeBytes = $maxSizeKB * 1024;
        
        // Get image info
        $info = getimagesize($source);
        if (!$info) {
            copy($source, $destination);
            return false;
        }
        
        // Load image based on type
        $image = null;
        switch ($info['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                copy($source, $destination);
                return false;
        }
        
        if (!$image) {
            copy($source, $destination);
            return false;
        }
        
        // Resize if width > 800px
        $width = imagesx($image);
        $height = imagesy($image);
        $maxWidth = 800;
        
        if ($width > $maxWidth) {
            $newHeight = intval(($maxWidth / $width) * $height);
            $newImage = imagecreatetruecolor($maxWidth, $newHeight);
            
            if ($info['mime'] === 'image/png' || $info['mime'] === 'image/gif') {
                imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }
            
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
        }
        
        // Compress with decreasing quality until <= maxSizeKB
        $quality = 85;
        $tempPath = sys_get_temp_dir() . '/compressed_guru_' . time() . '.jpg';
        
        do {
            imagejpeg($image, $tempPath, $quality);
            $currentSize = filesize($tempPath);
            
            if ($currentSize <= $maxSizeBytes || $quality <= 20) {
                break;
            }
            
            $quality -= 10;
        } while ($quality > 20);
        
        if (copy($tempPath, $destination)) {
            imagedestroy($image);
            @unlink($tempPath);
            return true;
        }
        
        imagedestroy($image);
        @unlink($tempPath);
        return false;
    }

    /**
     * Download Guru data as XLSX
     */
    public function downloadGuruData()
    {
        $guruList = Guru::orderBy('nama')->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');
        
        // Headers
        $headers = ['ID', 'NIP', 'Nama', 'Jenis Kelamin', 'No HP', 'Email', 'Alamat', 'Status Kepegawaian', 'Golongan', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Data rows
        $row = 2;
        foreach ($guruList as $guru) {
            $sheet->setCellValue('A' . $row, $guru->id);
            $sheet->setCellValue('B' . $row, $guru->nip ?? '');
            $sheet->setCellValue('C' . $row, $guru->nama);
            $sheet->setCellValue('D' . $row, $guru->jenis_kelamin ?? '');
            $sheet->setCellValue('E' . $row, $guru->no_hp ?? '');
            $sheet->setCellValue('F' . $row, $guru->email ?? '');
            $sheet->setCellValue('G' . $row, $guru->alamat ?? '');
            $sheet->setCellValue('H' . $row, $guru->status_kepegawaian ?? '');
            $sheet->setCellValue('I' . $row, $guru->golongan ?? '');
            $sheet->setCellValue('J' . $row, $guru->status ?? 'Aktif');
            $row++;
        }
        
        $filename = 'data_guru_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Import Guru data from XLSX (update existing only)
     */
    public function importGuruData(Request $request)
    {
        $request->validate([
            'file_guru' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $file = $request->file('file_guru');
        $path = $file->getRealPath();
        
        $updated = 0;
        $skipped = 0;
        $errors = [];

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            foreach ($rows as $index => $data) {
                if (count($data) < 10) {
                    $skipped++;
                    continue;
                }
                
                try {
                    $id = intval(trim($data[0] ?? 0));
                    
                    if (empty($id)) {
                        $skipped++;
                        continue;
                    }
                    
                    $guru = Guru::find($id);
                    if (!$guru) {
                        $skipped++;
                        continue;
                    }
                    
                    // Store old name before updating
                    $oldName = $guru->nama;
                    $newName = trim($data[2] ?? $guru->nama);
                    
                    // Update fields (excluding ID and NIP)
                    $guru->nama = $newName;
                    $guru->jenis_kelamin = trim($data[3] ?? $guru->jenis_kelamin);
                    $guru->no_hp = trim($data[4] ?? $guru->no_hp);
                    $guru->email = trim($data[5] ?? $guru->email);
                    $guru->alamat = trim($data[6] ?? $guru->alamat);
                    $guru->status_kepegawaian = trim($data[7] ?? $guru->status_kepegawaian);
                    $guru->golongan = trim($data[8] ?? $guru->golongan);
                    $guru->status = trim($data[9] ?? $guru->status);
                    $guru->save();
                    
                    // Cascade name update if name changed
                    if ($oldName !== $newName && !empty($newName)) {
                        NameCascadeService::updateGuruName($oldName, $newName);
                    }
                    
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.guru.index')
                ->withErrors(['file_guru' => 'Gagal membaca file: ' . $e->getMessage()]);
        }

        $message = "Import data guru selesai: $updated data berhasil diupdate, $skipped data dilewati.";
        if (!empty($errors)) {
            $message .= " (" . count($errors) . " error)";
        }

        return redirect()->route('admin.guru.index')
            ->with('success', $message);
    }

    /**
     * Show the import jadwal form
     */
    public function showImportJadwal()
    {
        $periodik = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        return view('admin.guru.import-jadwal', compact('tahunAktif', 'semesterAktif'));
    }

    /**
     * Download blangko/template XLSX for jadwal import
     */
    public function downloadBlangkoJadwal(Request $request)
    {
        $periodik = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? (date('Y') . '/' . (date('Y') + 1));
        $semesterAktif = strtolower($periodik->semester ?? 'Ganjil');

        $spreadsheet = new Spreadsheet();

        // ===== Sheet 1: Data Penugasan =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Penugasan');

        $headers = ['Nama Guru', 'Mata Pelajaran', 'Rombel', 'Hari', 'Jam Ke'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4CAF50');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Info row
        $sheet->setCellValue('A2', "Tahun Pelajaran: {$tahunAktif} | Semester: " . ucfirst($semesterAktif));
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->getColor()->setRGB('666666');

        // Example data row
        $sheet->setCellValue('A3', 'CONTOH GURU');
        $sheet->setCellValue('B3', 'Bahasa Indonesia');
        $sheet->setCellValue('C3', 'X.1');
        $sheet->setCellValue('D3', 'Senin');
        $sheet->setCellValue('E3', '1');
        $sheet->getStyle('A3:E3')->getFont()->getColor()->setRGB('999999');

        // Note
        $sheet->setCellValue('A4', '--- Hapus baris contoh di atas, lalu isi data mulai baris ini ---');
        $sheet->mergeCells('A4:E4');
        $sheet->getStyle('A4')->getFont()->setItalic(true)->getColor()->setRGB('FF0000');

        // ===== Sheet 2: Referensi =====
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi');

        // Guru list
        $refSheet->setCellValue('A1', 'Daftar Guru');
        $refSheet->getStyle('A1')->getFont()->setBold(true);
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->pluck('nama');
        $row = 2;
        foreach ($guruList as $nama) {
            $refSheet->setCellValue('A' . $row, $nama);
            $row++;
        }

        // Mapel list
        $refSheet->setCellValue('C1', 'Daftar Mata Pelajaran');
        $refSheet->getStyle('C1')->getFont()->setBold(true);
        $mapelList = \DB::table('mata_pelajaran')->orderBy('nama_mapel')->pluck('nama_mapel');
        $row = 2;
        foreach ($mapelList as $mapel) {
            $refSheet->setCellValue('C' . $row, $mapel);
            $row++;
        }

        // Rombel list (semester aktif)
        $refSheet->setCellValue('E1', 'Daftar Rombel (' . ucfirst($semesterAktif) . ')');
        $refSheet->getStyle('E1')->getFont()->setBold(true);
        $rombelList = \DB::table('rombel')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');
        $row = 2;
        foreach ($rombelList as $rombel) {
            $refSheet->setCellValue('E' . $row, $rombel);
            $row++;
        }

        // Hari list
        $refSheet->setCellValue('G1', 'Daftar Hari');
        $refSheet->getStyle('G1')->getFont()->setBold(true);
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $row = 2;
        foreach ($hariList as $hari) {
            $refSheet->setCellValue('G' . $row, $hari);
            $row++;
        }

        // Auto size columns
        foreach (['A', 'C', 'E', 'G'] as $c) {
            $refSheet->getColumnDimension($c)->setAutoSize(true);
        }

        // Set active sheet back to first
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'blangko_penugasan_guru_' . str_replace('/', '-', $tahunAktif) . '_' . $semesterAktif . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import jadwal data from XLSX (insert new or update existing)
     */
    public function importJadwalData(Request $request)
    {
        $request->validate([
            'file_jadwal' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $periodik = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = strtolower($periodik->semester ?? 'ganjil');

        $file = $request->file('file_jadwal');
        $path = $file->getRealPath();

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row (row 1), info row (row 2), example row (row 3), note row (row 4)
            // Data starts at row 5 (index 4)
            $dataStartIndex = 4;

            // Preload lookup maps
            $mapelMap = [];
            $mapelRecords = \DB::table('mata_pelajaran')->get();
            foreach ($mapelRecords as $m) {
                $mapelMap[strtolower(trim($m->nama_mapel))] = $m->id;
            }

            $rombelMap = [];
            $rombelRecords = \DB::table('rombel')
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', $semesterAktif)
                ->get();
            foreach ($rombelRecords as $r) {
                $rombelMap[strtolower(trim($r->nama_rombel))] = $r->id;
            }

            $validHari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $data = $rows[$i];

                // Skip empty rows
                if (empty(trim($data[0] ?? '')) && empty(trim($data[1] ?? ''))) {
                    continue;
                }

                // Skip rows starting with "---"
                if (str_starts_with(trim($data[0] ?? ''), '---')) {
                    continue;
                }

                try {
                    $namaGuru = strtoupper(trim($data[0] ?? ''));
                    $namaMapel = trim($data[1] ?? '');
                    $namaRombel = trim($data[2] ?? '');
                    $hari = ucfirst(strtolower(trim($data[3] ?? '')));
                    $jamKe = trim($data[4] ?? '');

                    // Validate required fields
                    if (empty($namaGuru) || empty($namaMapel) || empty($namaRombel) || empty($hari) || empty($jamKe)) {
                        $errors[] = "Baris " . ($i + 1) . ": Data tidak lengkap";
                        $skipped++;
                        continue;
                    }

                    // Lookup mapel
                    $idMapel = $mapelMap[strtolower($namaMapel)] ?? null;
                    if (!$idMapel) {
                        $errors[] = "Baris " . ($i + 1) . ": Mata pelajaran '$namaMapel' tidak ditemukan";
                        $skipped++;
                        continue;
                    }

                    // Lookup rombel
                    $idRombel = $rombelMap[strtolower($namaRombel)] ?? null;
                    if (!$idRombel) {
                        $errors[] = "Baris " . ($i + 1) . ": Rombel '$namaRombel' tidak ditemukan (semester $semesterAktif)";
                        $skipped++;
                        continue;
                    }

                    // Validate hari
                    if (!in_array(strtolower($hari), $validHari)) {
                        $errors[] = "Baris " . ($i + 1) . ": Hari '$hari' tidak valid";
                        $skipped++;
                        continue;
                    }

                    // Validate jam_ke
                    if (!is_numeric($jamKe) || intval($jamKe) < 1 || intval($jamKe) > 10) {
                        $errors[] = "Baris " . ($i + 1) . ": Jam ke '$jamKe' tidak valid (harus 1-10)";
                        $skipped++;
                        continue;
                    }

                    // Check existing record
                    $existing = \DB::table('jadwal_pelajaran')
                        ->where('id_mapel', $idMapel)
                        ->where('id_rombel', $idRombel)
                        ->where('hari', $hari)
                        ->where('jam_ke', intval($jamKe))
                        ->where('tahun_pelajaran', $tahunAktif)
                        ->where('semester', $semesterAktif)
                        ->first();

                    if ($existing) {
                        // Update nama_guru
                        \DB::table('jadwal_pelajaran')
                            ->where('id', $existing->id)
                            ->update(['nama_guru' => $namaGuru]);
                        $updated++;
                    } else {
                        // Insert new
                        \DB::table('jadwal_pelajaran')->insert([
                            'id_mapel' => $idMapel,
                            'nama_guru' => $namaGuru,
                            'hari' => $hari,
                            'jam_ke' => intval($jamKe),
                            'id_rombel' => $idRombel,
                            'tahun_pelajaran' => $tahunAktif,
                            'semester' => $semesterAktif,
                            'created_at' => now(),
                        ]);
                        $inserted++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.guru.import-jadwal.show')
                ->withErrors(['file_jadwal' => 'Gagal membaca file: ' . $e->getMessage()]);
        }

        $message = "Import selesai: $inserted data baru ditambahkan, $updated data diupdate, $skipped data dilewati.";
        if (!empty($errors)) {
            $message .= " (" . count($errors) . " error)";
        }

        return redirect()->route('admin.guru.import-jadwal.show')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
