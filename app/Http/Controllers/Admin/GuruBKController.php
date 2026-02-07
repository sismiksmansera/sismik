<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuruBK;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\NameCascadeService;

class GuruBKController extends Controller
{
    /**
     * Display a listing of guru BK
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 25);
        
        $query = GuruBK::query();
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }
        
        $guruBKList = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->query());
        
        // Statistics
        $totalGuruBK = GuruBK::count();
        $totalAktif = GuruBK::where('status', 'Aktif')->count();
        $totalNonaktif = $totalGuruBK - $totalAktif;
        
        return view('admin.guru-bk.index', compact(
            'guruBKList',
            'search',
            'totalGuruBK',
            'totalAktif',
            'totalNonaktif'
        ));
    }

    /**
     * Show the form for creating a new guru BK
     */
    public function create()
    {
        return view('admin.guru-bk.create');
    }

    /**
     * Store a newly created guru BK
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'nullable|in:L,P',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status_kepegawaian' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:50',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'foto', '_token']);
        $data['password'] = Hash::make($request->password);
        $data['status'] = 'Aktif';

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $filename = 'guru_bk_' . time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->storeAs('guru_bk', $filename, 'public');
            $data['foto'] = $filename;
        }

        GuruBK::create($data);

        return redirect()->route('admin.guru-bk.index')->with('success', 'Data guru BK berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified guru BK
     */
    public function edit($id)
    {
        $guruBK = GuruBK::findOrFail($id);
        return view('admin.guru-bk.edit', compact('guruBK'));
    }

    /**
     * Update the specified guru BK
     */
    public function update(Request $request, $id)
    {
        $guruBK = GuruBK::findOrFail($id);
        $oldName = $guruBK->nama; // Store old name for cascade update

        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|in:L,P',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status_kepegawaian' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:50',
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
            if ($guruBK->foto && Storage::disk('public')->exists('guru_bk/' . $guruBK->foto)) {
                Storage::disk('public')->delete('guru_bk/' . $guruBK->foto);
            }
            
            $file = $request->file('foto');
            $filename = 'guru_bk_' . $id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/guru_bk/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/guru_bk'))) {
                mkdir(storage_path('app/public/guru_bk'), 0755, true);
            }
            
            // Compress image if larger than 250KB
            $maxSizeKB = 250;
            $this->compressImage($file->getPathname(), $uploadPath, $maxSizeKB);
            
            $data['foto'] = $filename;
        }

        $guruBK->update($data);

        // Cascade name update if name changed
        if ($oldName !== $request->nama) {
            NameCascadeService::updateGuruBKName($oldName, $request->nama);
        }

        // Redirect back to edit page if only uploading foto
        if ($request->hasFile('foto') && !$request->filled('password') && !$request->has('full_update')) {
            return redirect()->route('admin.guru-bk.edit', $id)->with('success', 'Foto berhasil diperbarui!');
        }

        return redirect()->route('admin.guru-bk.index')->with('success', 'Data guru BK berhasil diperbarui!');
    }

    /**
     * Remove the specified guru BK
     */
    public function destroy($id)
    {
        $guruBK = GuruBK::findOrFail($id);

        // Delete foto if exists
        if ($guruBK->foto && Storage::disk('public')->exists('guru_bk/' . $guruBK->foto)) {
            Storage::disk('public')->delete('guru_bk/' . $guruBK->foto);
        }

        $guruBK->delete();

        return redirect()->route('admin.guru-bk.index')->with('success', 'Data guru BK berhasil dihapus!');
    }

    /**
     * Reset password for a guru BK
     */
    public function resetPassword($id)
    {
        $guruBK = GuruBK::findOrFail($id);
        
        // Reset password to default (NIP or username)
        $defaultPassword = $guruBK->nip ?: $guruBK->username;
        $guruBK->update(['password' => Hash::make($defaultPassword)]);

        return redirect()->route('admin.guru-bk.index')->with('success', 'Password guru BK berhasil direset ke NIP/Username!');
    }

    /**
     * Impersonate a guru BK (Login as Guru BK)
     */
    public function impersonate($id)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        $guruBK = GuruBK::findOrFail($id);

        // Store admin session for returning later
        session([
            'impersonating' => true,
            'impersonate_type' => 'guru_bk',
            'original_admin_id' => $admin->id,
            'original_admin_username' => $admin->username,
        ]);

        // Login as guru BK
        Auth::guard('guru_bk')->login($guruBK);

        return redirect()->route('guru_bk.dashboard')->with('success', 'Anda sekarang login sebagai ' . $guruBK->nama);
    }

    /**
     * Show siswa bimbingan (counseling students)
     */
    public function siswaBimbingan(Request $request, $id)
    {
        $guruBK = GuruBK::findOrFail($id);
        $namaGuruBK = $guruBK->nama;
        
        // Get active period
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodeAktif->semester ?? 'Ganjil';
        
        // Filter values
        $selectedTahun = $request->get('tahun', $tahunAktif);
        $selectedSemester = $request->get('semester', $semesterAktif);
        
        // Get all siswa that have this guru BK in any semester
        $siswaAll = \DB::table('siswa')
            ->where(function($q) use ($namaGuruBK) {
                $q->where('bk_semester_1', $namaGuruBK)
                  ->orWhere('bk_semester_2', $namaGuruBK)
                  ->orWhere('bk_semester_3', $namaGuruBK)
                  ->orWhere('bk_semester_4', $namaGuruBK)
                  ->orWhere('bk_semester_5', $namaGuruBK)
                  ->orWhere('bk_semester_6', $namaGuruBK);
            })
            ->orderBy('nama', 'asc')
            ->get();
        
        // Filter and process siswa for selected period
        $siswaBimbingan = [];
        $rombelList = [];
        
        foreach ($siswaAll as $siswa) {
            // Calculate active semester for this siswa
            $semesterAktifSiswa = $this->calculateActiveSemester(
                $siswa->angkatan_masuk,
                $selectedTahun,
                $selectedSemester
            );
            
            // Check BK for this semester
            $bkAktif = $siswa->{'bk_semester_' . $semesterAktifSiswa} ?? null;
            
            if ($bkAktif === $namaGuruBK) {
                $item = (array) $siswa;
                $item['semester_aktif'] = $semesterAktifSiswa;
                
                // Get rombel for this semester
                $rombelAktif = $siswa->{'rombel_semester_' . $semesterAktifSiswa} ?? '';
                $item['rombel_aktif'] = $rombelAktif;
                
                // Determine kelas
                if (in_array($semesterAktifSiswa, [1, 2])) {
                    $item['kelas'] = '10';
                } elseif (in_array($semesterAktifSiswa, [3, 4])) {
                    $item['kelas'] = '11';
                } else {
                    $item['kelas'] = '12';
                }
                
                if (!empty($rombelAktif) && !in_array($rombelAktif, $rombelList)) {
                    $rombelList[] = $rombelAktif;
                }
                
                $siswaBimbingan[] = $item;
            }
        }
        
        // Calculate stats
        $totalSiswa = count($siswaBimbingan);
        $totalRombel = count($rombelList);
        $kelasCounts = ['10' => 0, '11' => 0, '12' => 0];
        $jkCounts = ['L' => 0, 'P' => 0];
        
        foreach ($siswaBimbingan as $siswa) {
            $kelasCounts[$siswa['kelas']]++;
            if ($siswa['jk'] == 'Laki-laki') {
                $jkCounts['L']++;
            } else {
                $jkCounts['P']++;
            }
        }
        
        // Group siswa per rombel
        $siswaPerRombel = [];
        foreach ($siswaBimbingan as $siswa) {
            $rombelName = !empty($siswa['rombel_aktif']) ? $siswa['rombel_aktif'] : 'Belum diatur';
            if (!isset($siswaPerRombel[$rombelName])) {
                $siswaPerRombel[$rombelName] = [];
            }
            $siswaPerRombel[$rombelName][] = $siswa;
        }
        ksort($siswaPerRombel);
        
        // Generate years for filter
        $currentYear = intval(date('Y'));
        $years = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $years[] = $i . '/' . ($i + 1);
        }
        
        return view('admin.guru-bk.siswa-bimbingan', compact(
            'guruBK', 'selectedTahun', 'selectedSemester', 'years',
            'siswaBimbingan', 'siswaPerRombel', 'totalSiswa', 'totalRombel',
            'kelasCounts', 'jkCounts'
        ));
    }
    
    /**
     * Calculate active semester for siswa based on angkatan
     */
    private function calculateActiveSemester($angkatan, $tahunPelajaran, $semester)
    {
        if (empty($angkatan) || empty($tahunPelajaran) || empty($semester)) {
            return 1;
        }
        
        $tahunParts = explode('/', $tahunPelajaran);
        $tahunMulai = intval($tahunParts[0] ?? 0);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;
        
        if ($selisihTahun == 0) {
            return ($semester == 'Ganjil') ? 1 : 2;
        } elseif ($selisihTahun == 1) {
            return ($semester == 'Ganjil') ? 3 : 4;
        } elseif ($selisihTahun == 2) {
            return ($semester == 'Ganjil') ? 5 : 6;
        }
        
        return 1;
    }

    /**
     * Show aktivitas guru BK
     */
    public function aktivitas(Request $request, $id)
    {
        $guruBK = GuruBK::findOrFail($id);
        
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
        
        // Validate date range
        if ($tanggalMulai < $minDate) $tanggalMulai = $minDate;
        if ($tanggalMulai > $maxDate) $tanggalMulai = $maxDate;
        if ($tanggalSelesai < $minDate) $tanggalSelesai = $minDate;
        if ($tanggalSelesai > $maxDate) $tanggalSelesai = $maxDate;
        
        // Stats query
        $stats = \DB::table('catatan_bimbingan')
            ->selectRaw('COUNT(*) as total_catatan')
            ->selectRaw('COUNT(DISTINCT nisn) as total_siswa')
            ->selectRaw("SUM(CASE WHEN status = 'Belum Ditangani' THEN 1 ELSE 0 END) as belum_ditangani")
            ->selectRaw("SUM(CASE WHEN status = 'Dalam Proses' THEN 1 ELSE 0 END) as dalam_proses")
            ->selectRaw("SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai")
            ->where('guru_bk_id', $id)
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->first();
        
        // Stats per jenis bimbingan
        $listJenis = \DB::table('catatan_bimbingan')
            ->select('jenis_bimbingan', \DB::raw('COUNT(*) as total'))
            ->where('guru_bk_id', $id)
            ->where('tahun_pelajaran', $filterTahun)
            ->where('semester', $filterSemester)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->groupBy('jenis_bimbingan')
            ->orderByDesc('total')
            ->get();
        
        // Riwayat bimbingan
        $listRiwayat = \DB::table('catatan_bimbingan as cb')
            ->leftJoin('siswa as s', 'cb.nisn', '=', 's.nisn')
            ->select('cb.*', 's.nama as nama_siswa')
            ->where('cb.guru_bk_id', $id)
            ->where('cb.tahun_pelajaran', $filterTahun)
            ->where('cb.semester', $filterSemester)
            ->whereBetween('cb.tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderByDesc('cb.tanggal')
            ->orderByDesc('cb.created_at')
            ->limit(50)
            ->get();
        
        // Calculate percentage
        $totalCatatan = $stats->total_catatan ?? 0;
        $selesai = $stats->selesai ?? 0;
        $persentaseSelesai = $totalCatatan > 0 ? round(($selesai / $totalCatatan) * 100) : 0;
        
        // Indicator color
        if ($persentaseSelesai >= 70) {
            $warnaIndikator = '#10b981';
            $labelIndikator = 'Sangat Baik';
        } elseif ($persentaseSelesai >= 40) {
            $warnaIndikator = '#f59e0b';
            $labelIndikator = 'Cukup';
        } else {
            $warnaIndikator = '#ef4444';
            $labelIndikator = 'Perlu Perhatian';
        }
        
        // Generate years for filter
        $currentYear = intval(date('Y'));
        $years = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $years[] = $i . '/' . ($i + 1);
        }
        
        return view('admin.guru-bk.aktivitas', compact(
            'guruBK', 'filterTahun', 'filterSemester', 'tanggalMulai', 'tanggalSelesai',
            'minDate', 'maxDate', 'years', 'stats', 'listJenis', 'listRiwayat',
            'totalCatatan', 'persentaseSelesai', 'warnaIndikator', 'labelIndikator'
        ));
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
            // Fallback: just copy the file
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
                // Unsupported format, just copy
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
            
            // Preserve transparency
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
        $tempPath = sys_get_temp_dir() . '/compressed_' . time() . '.jpg';
        
        do {
            imagejpeg($image, $tempPath, $quality);
            $currentSize = filesize($tempPath);
            
            if ($currentSize <= $maxSizeBytes || $quality <= 20) {
                break;
            }
            
            $quality -= 10;
        } while ($quality > 20);
        
        // Move to destination
        if (copy($tempPath, $destination)) {
            imagedestroy($image);
            @unlink($tempPath);
            return true;
        }
        
        imagedestroy($image);
        @unlink($tempPath);
        return false;
    }
}
