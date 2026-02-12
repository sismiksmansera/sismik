<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\DataPeriodik;
use App\Models\AdminSekolah;
use App\Models\RaportSettings;
use App\Models\JamPelajaranSetting;
use App\Models\LoginSettings;
use App\Models\Guru;

class ManajemenSekolahController extends Controller
{
    /**
     * Display the school management page
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get all periodik data with raport settings
        $periodikList = DataPeriodik::leftJoin('raport_settings', 'data_periodik.id', '=', 'raport_settings.periodik_id')
            ->select('data_periodik.*', 
                'raport_settings.tanggal_bagi_raport',
                'raport_settings.lock_print_raport',
                'raport_settings.lock_print_raport_all',
                'raport_settings.lock_print_riwayat_guru',
                'raport_settings.lock_print_riwayat_all',
                'raport_settings.lock_print_leger_nilai',
                'raport_settings.lock_print_leger_katrol',
                'raport_settings.lock_nilai_minmax',
                'raport_settings.lock_katrol_nilai')
            ->orderBy('data_periodik.id', 'desc')
            ->get();
        
        // Get all admins
        $adminList = AdminSekolah::orderBy('id', 'desc')->get();
        
        // Get active guru for waka selection
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->get();
        
        // Get login settings
        $loginSettings = LoginSettings::first();
        
        return view('admin.manajemen-sekolah', compact(
            'admin',
            'periodikList',
            'adminList',
            'guruList',
            'loginSettings'
        ));
    }

    /**
     * Store new periodik data
     */
    public function storePeriodik(Request $request)
    {
        $request->validate([
            'tahun_pelajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'nama_kepala' => 'required|string|max:100',
            'nip_kepala' => 'required|string|max:50',
        ]);

        DataPeriodik::create([
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'semester' => $request->semester,
            'nama_kepala' => $request->nama_kepala,
            'nip_kepala' => $request->nip_kepala,
            'aktif' => 'Tidak',
        ]);

        return redirect()->route('admin.manajemen-sekolah')
            ->with('success', 'Data periodik berhasil ditambahkan.');
    }

    /**
     * Update periodik data
     */
    public function updatePeriodik(Request $request, $id)
    {
        $request->validate([
            'tahun_pelajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'nama_kepala' => 'required|string|max:100',
            'nip_kepala' => 'required|string|max:50',
        ]);

        $periodik = DataPeriodik::findOrFail($id);
        $periodik->update([
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'semester' => $request->semester,
            'nama_kepala' => $request->nama_kepala,
            'nip_kepala' => $request->nip_kepala,
            'waka_kurikulum' => $request->waka_kurikulum,
            'waka_kesiswaan' => $request->waka_kesiswaan,
            'waka_sarpras' => $request->waka_sarpras,
            'waka_humas' => $request->waka_humas,
        ]);

        return redirect()->route('admin.manajemen-sekolah')
            ->with('success', 'Data periodik berhasil diperbarui.');
    }

    /**
     * Delete periodik data
     */
    public function destroyPeriodik($id)
    {
        $periodik = DataPeriodik::findOrFail($id);
        $periodik->delete();

        return redirect()->route('admin.manajemen-sekolah')
            ->with('success', 'Data periodik berhasil dihapus.');
    }

    /**
     * Toggle active status (AJAX)
     */
    public function toggleAktif(Request $request)
    {
        $id = $request->input('id');
        $aktif = $request->input('aktif');

        // If setting to active, deactivate all others first
        if ($aktif === 'Ya') {
            DataPeriodik::where('id', '!=', $id)->update(['aktif' => 'Tidak']);
        }

        DataPeriodik::where('id', $id)->update(['aktif' => $aktif]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }

    /**
     * Store new admin
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:admin_sekolah,username',
            'password' => 'required|string|min:6',
        ]);

        AdminSekolah::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.manajemen-sekolah')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Update admin
     */
    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:admin_sekolah,username,' . $id,
        ]);

        $admin = AdminSekolah::findOrFail($id);
        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.manajemen-sekolah')
            ->with('success', 'Admin berhasil diperbarui.');
    }

    /**
     * Save raport settings (AJAX)
     */
    public function saveRaportSettings(Request $request)
    {
        $periodikId = $request->input('periodik_id');

        RaportSettings::updateOrCreate(
            ['periodik_id' => $periodikId],
            [
                'tanggal_bagi_raport' => $request->input('tanggal_bagi_raport') ?: null,
                'lock_print_raport' => $request->input('lock_print_raport', 'Tidak'),
                'lock_print_raport_all' => $request->input('lock_print_raport_all', 'Tidak'),
                'lock_print_riwayat_guru' => $request->input('lock_print_riwayat_guru', 'Tidak'),
                'lock_print_riwayat_all' => $request->input('lock_print_riwayat_all', 'Tidak'),
                'lock_print_leger_nilai' => $request->input('lock_print_leger_nilai', 'Tidak'),
                'lock_print_leger_katrol' => $request->input('lock_print_leger_katrol', 'Tidak'),
                'lock_nilai_minmax' => $request->input('lock_nilai_minmax', 'Tidak'),
                'lock_katrol_nilai' => $request->input('lock_katrol_nilai', 'Tidak'),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Pengaturan raport berhasil disimpan.']);
    }

    /**
     * Get jam pelajaran settings (AJAX)
     */
    public function getJamPelajaran(Request $request)
    {
        $periodikId = $request->input('periodik_id');
        $data = JamPelajaranSetting::where('periodik_id', $periodikId)->first();

        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Data belum ada']);
    }

    /**
     * Save jam pelajaran settings (AJAX)
     */
    public function saveJamPelajaran(Request $request)
    {
        $periodikId = $request->input('periodik_id');
        $data = ['periodik_id' => $periodikId];

        for ($i = 1; $i <= 11; $i++) {
            $data["jp_{$i}_mulai"] = $request->input("jp_{$i}_mulai") ?: null;
            $data["jp_{$i}_selesai"] = $request->input("jp_{$i}_selesai") ?: null;
        }

        JamPelajaranSetting::updateOrCreate(
            ['periodik_id' => $periodikId],
            $data
        );

        return response()->json(['success' => true, 'message' => 'Pengaturan jam pelajaran berhasil disimpan.']);
    }

    /**
     * Upload login background
     */
    public function uploadBackground(Request $request)
    {
        try {
            // Manual validation with JSON response
            if (!$request->hasFile('background_image')) {
                return response()->json(['success' => false, 'message' => 'Tidak ada file yang diupload.']);
            }

            $file = $request->file('background_image');
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return response()->json(['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau WebP.']);
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json(['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB.']);
            }

            // Delete old background
            $settings = LoginSettings::first();
            if ($settings && $settings->background_image) {
                Storage::disk('public')->delete($settings->background_image);
            }

            // Store new file
            $path = $file->store('login-bg', 'public');

            // Update or create settings
            if ($settings) {
                $settings->update(['background_image' => $path]);
            } else {
                LoginSettings::create(['background_image' => $path]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Background berhasil diupload!',
                'path' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete login background
     */
    public function deleteBackground()
    {
        try {
            $settings = LoginSettings::first();
            if ($settings && $settings->background_image) {
                Storage::disk('public')->delete($settings->background_image);
                $settings->update(['background_image' => null]);
            }

            return response()->json(['success' => true, 'message' => 'Background berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload login logo
     */
    public function uploadLogo(Request $request)
    {
        try {
            // Manual validation with JSON response
            if (!$request->hasFile('logo_image')) {
                return response()->json(['success' => false, 'message' => 'Tidak ada file yang diupload.']);
            }

            $file = $request->file('logo_image');
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'];
            
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return response()->json(['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.']);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return response()->json(['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 2MB.']);
            }

            // Delete old logo
            $settings = LoginSettings::first();
            if ($settings && $settings->logo_image) {
                Storage::disk('public')->delete($settings->logo_image);
            }

            // Store new file
            $path = $file->store('login-bg', 'public');

            // Update or create settings
            if ($settings) {
                $settings->update(['logo_image' => $path]);
            } else {
                LoginSettings::create(['logo_image' => $path]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Logo berhasil diupload!',
                'path' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete login logo
     */
    public function deleteLogo()
    {
        try {
            $settings = LoginSettings::first();
            if ($settings && $settings->logo_image) {
                Storage::disk('public')->delete($settings->logo_image);
                $settings->update(['logo_image' => null]);
            }

            return response()->json(['success' => true, 'message' => 'Logo berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Save overlay settings
     */
    public function saveOverlaySettings(Request $request)
    {
        LoginSettings::updateOrCreate(
            ['id' => 1],
            [
                'overlay_color' => $request->input('overlay_color', 'rgba(0, 100, 0, 0.7)'),
                'overlay_color_end' => $request->input('overlay_color_end', 'rgba(0, 150, 0, 0.5)'),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Pengaturan overlay berhasil disimpan!']);
    }

    /**
     * Get testing date settings (AJAX)
     */
    public function getTestingDate()
    {
        $settings = LoginSettings::first();
        
        if ($settings) {
            return response()->json([
                'success' => true,
                'data' => [
                    'testing_date' => $settings->testing_date?->format('Y-m-d'),
                    'testing_active' => $settings->testing_active ?? 'Tidak',
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Data belum ada',
            'data' => ['testing_date' => null, 'testing_active' => 'Tidak']
        ]);
    }

    /**
     * Save testing date settings (AJAX)
     */
    public function saveTestingDate(Request $request)
    {
        LoginSettings::updateOrCreate(
            ['id' => 1],
            [
                'testing_date' => $request->input('testing_date') ?: null,
                'testing_active' => $request->input('testing_active', 'Tidak'),
            ]
        );

        // Clear cache to apply changes immediately
        Cache::forget('login_settings');

        return response()->json(['success' => true, 'message' => 'Pengaturan testing date berhasil disimpan!']);
    }

    /**
     * Backup database as .sql file download
     */
    public function backupDatabase()
    {
        set_time_limit(300);

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Find mysqldump binary
        $mysqldumpBin = $this->findMysqldumpBinary();

        if (!$mysqldumpBin) {
            return back()->withErrors(['backup' => 'mysqldump tidak ditemukan. Pastikan MySQL sudah terinstall.']);
        }

        $filename = $database . '_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        // Build mysqldump command
        $passwordArg = !empty($password) ? "-p\"{$password}\"" : '';
        $cmd = "\"{$mysqldumpBin}\" -h {$host} -P {$port} -u {$username} {$passwordArg} --single-transaction --routines --triggers {$database} > \"{$tempPath}\" 2>&1";

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($tempPath) || filesize($tempPath) === 0) {
            @unlink($tempPath);
            $errorMsg = implode("\n", $output);
            return back()->withErrors(['backup' => 'Gagal backup database: ' . $errorMsg]);
        }

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Find mysqldump binary path
     */
    private function findMysqldumpBinary()
    {
        $paths = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Scan Laragon mysql directories dynamically
            $laragonMysqlDir = 'C:\\laragon\\bin\\mysql';
            if (is_dir($laragonMysqlDir)) {
                $dirs = glob($laragonMysqlDir . '\\mysql-*', GLOB_ONLYDIR);
                foreach ($dirs as $dir) {
                    $paths[] = $dir . '\\bin\\mysqldump.exe';
                }
            }
            $paths[] = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
            $paths[] = 'mysqldump';
        } else {
            $paths = [
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
                'mysqldump',
            ];
        }

        foreach ($paths as $path) {
            if ($path === 'mysqldump') {
                exec('which mysqldump 2>/dev/null || where mysqldump 2>nul', $out, $code);
                if ($code === 0) return 'mysqldump';
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Backup storage/upload files as ZIP download
     */
    public function backupStorage()
    {
        set_time_limit(300);

        $storagePath = storage_path('app/public');

        if (!is_dir($storagePath)) {
            return back()->withErrors(['backup' => 'Folder storage/app/public tidak ditemukan.']);
        }

        $filename = 'sismik_storage_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        $zip = new \ZipArchive();
        if ($zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['backup' => 'Gagal membuat file ZIP.']);
        }

        $this->addDirectoryToZip($zip, $storagePath, 'storage');

        $zip->close();

        if (!file_exists($tempPath) || filesize($tempPath) === 0) {
            @unlink($tempPath);
            return back()->withErrors(['backup' => 'File ZIP kosong atau gagal dibuat.']);
        }

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Recursively add directory contents to ZIP
     */
    private function addDirectoryToZip(\ZipArchive $zip, $dirPath, $zipBasePath)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $zipBasePath . '/' . substr($filePath, strlen($dirPath) + 1);
            // Normalize path separators for ZIP
            $relativePath = str_replace('\\', '/', $relativePath);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                // Skip .gitignore
                if ($file->getFilename() === '.gitignore') continue;
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
