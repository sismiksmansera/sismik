<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\GuruBK;
use App\Models\DataPeriodik;

class SiswaController extends Controller
{
    /**
     * Helper: Calculate active semester based on angkatan
     */
    private function calculateActiveSemester($angkatan, $tahunAktif, $semesterAktif)
    {
        if (empty($angkatan) || empty($tahunAktif) || empty($semesterAktif)) {
            return 1;
        }
        
        $tahunParts = explode('/', $tahunAktif);
        $tahunMulai = intval($tahunParts[0] ?? 0);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;
        
        if ($selisihTahun == 0) {
            return (strtolower($semesterAktif) == 'ganjil') ? 1 : 2;
        } elseif ($selisihTahun == 1) {
            return (strtolower($semesterAktif) == 'ganjil') ? 3 : 4;
        } elseif ($selisihTahun == 2) {
            return (strtolower($semesterAktif) == 'ganjil') ? 5 : 6;
        }
        return 1;
    }

    /**
     * Display list of students
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';
        
        // Get all students with search
        $query = Siswa::query();
        
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }
        
        $perPage = $request->get('per_page', 10);
        $siswaList = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->query());
        
        // Get rombel list for dropdowns
        $rombelList = Rombel::select('nama_rombel')
            ->distinct()
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');
        
        // Get guru BK list
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama')->get();
        
        // Get angkatan list
        $angkatanList = range(date('Y') - 3, date('Y') + 1);
        
        return view('admin.siswa.index', compact(
            'admin',
            'siswaList',
            'rombelList',
            'guruBKList',
            'angkatanList',
            'tahunAktif',
            'semesterAktif'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $rombelList = Rombel::select('nama_rombel')->distinct()->orderBy('nama_rombel')->pluck('nama_rombel');
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama')->get();
        $angkatanList = range(date('Y') - 3, date('Y') + 1);
        
        return view('admin.siswa.create', compact('rombelList', 'guruBKList', 'angkatanList'));
    }

    /**
     * Store new student
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn',
            'nama' => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        Siswa::create([
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'jk' => $request->jk,
            'agama' => $request->agama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir ?: null,
            'nohp_siswa' => $request->nohp_siswa,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'nama_bapak' => $request->nama_bapak,
            'pekerjaan_bapak' => $request->pekerjaan_bapak,
            'nohp_bapak' => $request->nohp_bapak,
            'nama_ibu' => $request->nama_ibu,
            'pekerjaan_ibu' => $request->pekerjaan_ibu,
            'nohp_ibu' => $request->nohp_ibu,
            'jml_saudara' => $request->jml_saudara ?: 0,
            'anak_ke' => $request->anak_ke ?: 0,
            'asal_sekolah' => $request->asal_sekolah,
            'nilai_skl' => $request->nilai_skl,
            'angkatan_masuk' => $request->angkatan_masuk,
            'nama_rombel' => $request->nama_rombel,
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $rombelList = Rombel::select('nama_rombel')->distinct()->orderBy('nama_rombel')->pluck('nama_rombel');
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama')->get();
        $angkatanList = range(date('Y') - 3, date('Y') + 1);
        
        return view('admin.siswa.edit', compact('siswa', 'rombelList', 'guruBKList', 'angkatanList'));
    }

    /**
     * Update student
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn,' . $id,
            'nama' => 'required|string|max:100',
        ]);

        $siswa = Siswa::findOrFail($id);
        
        $data = [
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'jk' => $request->jk,
            'agama' => $request->agama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir ?: null,
            'nohp_siswa' => $request->nohp_siswa,
            'email' => $request->email,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'nama_bapak' => $request->nama_bapak,
            'pekerjaan_bapak' => $request->pekerjaan_bapak,
            'nohp_bapak' => $request->nohp_bapak,
            'nama_ibu' => $request->nama_ibu,
            'pekerjaan_ibu' => $request->pekerjaan_ibu,
            'nohp_ibu' => $request->nohp_ibu,
            'jml_saudara' => $request->jml_saudara ?: 0,
            'anak_ke' => $request->anak_ke ?: 0,
            'asal_sekolah' => $request->asal_sekolah,
            'nilai_skl' => $request->nilai_skl,
            'cita_cita' => $request->cita_cita,
            'mapel_fav1' => $request->mapel_fav1,
            'mapel_fav2' => $request->mapel_fav2,
            'harapan' => $request->harapan,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle foto upload with compression
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
                Storage::disk('public')->delete('siswa/' . $siswa->foto);
            }
            
            $file = $request->file('foto');
            $filename = 'siswa_' . $id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/siswa/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/siswa'))) {
                mkdir(storage_path('app/public/siswa'), 0755, true);
            }
            
            // Compress image to max 250KB
            $this->compressImage($file->getPathname(), $uploadPath, 250);
            
            $data['foto'] = $filename;
        }

        $siswa->update($data);

        // Redirect back to edit page if only uploading foto
        if ($request->hasFile('foto') && !$request->filled('password') && !$request->has('full_update')) {
            return redirect()->route('admin.siswa.edit', $id)->with('success', 'Foto berhasil diperbarui!');
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Delete student
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Delete multiple students (AJAX)
     */
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        $deleted = Siswa::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "$deleted data siswa berhasil dihapus",
            'deleted' => $deleted
        ]);
    }

    /**
     * Update angkatan (AJAX)
     */
    public function updateAngkatan(Request $request)
    {
        $nisn = $request->input('nisn');
        $angkatan = $request->input('angkatan');

        if (empty($nisn)) {
            return response()->json(['success' => false, 'msg' => 'NISN kosong']);
        }

        $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update(['angkatan_masuk' => $angkatan ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Update rombel per semester (AJAX)
     */
    public function updateRombelSemester(Request $request)
    {
        $nisn = $request->input('nisn');
        $semester = intval($request->input('semester'));
        $namaRombel = $request->input('nama_rombel');

        if (empty($nisn) || $semester < 1 || $semester > 6) {
            return response()->json(['success' => false, 'msg' => 'Data tidak valid']);
        }

        $column = "rombel_semester_$semester";
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update([$column => $namaRombel ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Update guru BK per semester (AJAX)
     */
    public function updateBKSemester(Request $request)
    {
        $nisn = $request->input('nisn');
        $semester = intval($request->input('semester'));
        $namaGuruBK = $request->input('nama_guru_bk');

        if (empty($nisn) || $semester < 1 || $semester > 6) {
            return response()->json(['success' => false, 'msg' => 'Data tidak valid']);
        }

        $column = "bk_semester_$semester";
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update([$column => $namaGuruBK ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Get rombel by semester (AJAX)
     */
    public function getRombelBySemester(Request $request)
    {
        $tahun = $request->input('tahun_ajaran');
        $semester = $request->input('semester');

        if (empty($tahun) || empty($semester)) {
            return response()->json(['error' => 'Data tidak lengkap']);
        }

        $rombelList = Rombel::where('tahun_pelajaran', $tahun)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semester)])
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');

        return response()->json($rombelList);
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('admin.siswa.import');
    }

    /**
     * Process CSV import
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();
        
        $imported = 0;
        $skipped = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ',');
            
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                if (count($data) < 3) continue; // Minimal: NISN, NIS, Nama
                
                try {
                    // Check if NISN exists
                    $nisn = trim($data[0] ?? '');
                    if (empty($nisn)) {
                        $skipped++;
                        continue;
                    }

                    $exists = Siswa::where('nisn', $nisn)->exists();
                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    Siswa::create([
                        'nisn' => $nisn,
                        'nis' => trim($data[1] ?? ''),
                        'nama' => trim($data[2] ?? ''),
                        'jk' => trim($data[3] ?? ''),
                        'agama' => trim($data[4] ?? ''),
                        'tempat_lahir' => trim($data[5] ?? ''),
                        'tgl_lahir' => !empty($data[6]) ? date('Y-m-d', strtotime($data[6])) : null,
                        'password' => Hash::make($nisn), // Default password = NISN
                        'angkatan_masuk' => trim($data[7] ?? date('Y')),
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris: " . ($imported + $skipped + 2) . " - " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        $message = "Import selesai: $imported data berhasil, $skipped data dilewati.";
        if (!empty($errors)) {
            $message .= " (" . count($errors) . " error)";
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', $message);
    }

    /**
     * Upload photo
     */
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $siswa = Siswa::findOrFail($id);

        // Delete old photo
        if ($siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
            Storage::disk('public')->delete('siswa/' . $siswa->foto);
        }

        // Store new photo
        $filename = 'siswa_' . $id . '_' . time() . '.' . $request->file('foto')->extension();
        $request->file('foto')->storeAs('siswa', $filename, 'public');

        $siswa->update(['foto' => $filename]);

        return back()->with('success', 'Foto berhasil diupload!');
    }

    /**
     * Impersonate a student (Login as Siswa)
     */
    public function impersonate($nisn)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return back()->with('error', 'Siswa tidak ditemukan');
        }

        // Store admin session for returning later
        session([
            'impersonating' => true,
            'original_admin_id' => $admin->id,
            'original_admin_username' => $admin->username,
        ]);

        // Login as siswa
        Auth::guard('siswa')->login($siswa);

        return redirect()->route('siswa.dashboard')->with('success', 'Anda sekarang login sebagai ' . $siswa->nama);
    }

    /**
     * Compress image to target size (max KB)
     */
    private function compressImage($source, $destination, $maxSizeKB = 250)
    {
        $maxSizeBytes = $maxSizeKB * 1024;
        
        $info = getimagesize($source);
        if (!$info) {
            copy($source, $destination);
            return false;
        }
        
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
        
        // Compress with decreasing quality
        $quality = 85;
        $tempPath = sys_get_temp_dir() . '/compressed_siswa_' . time() . '.jpg';
        
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
}
