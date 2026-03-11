<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    /**
     * Display backup & restore page
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $dbName = config('database.connections.mysql.database');
        
        return view('admin.backup-restore', compact('admin', 'dbName'));
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

        $mysqldumpBin = $this->findMysqldumpBinary();

        if (!$mysqldumpBin) {
            return back()->withErrors(['backup' => 'mysqldump tidak ditemukan. Pastikan MySQL sudah terinstall.']);
        }

        $filename = $database . '_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

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
     * Restore storage files from ZIP upload
     */
    public function restoreStorage(Request $request)
    {
        set_time_limit(300);

        if (!$request->hasFile('storage_zip')) {
            return back()->withErrors(['backup' => 'Tidak ada file ZIP yang diupload.']);
        }

        $file = $request->file('storage_zip');

        if ($file->getClientOriginalExtension() !== 'zip') {
            return back()->withErrors(['backup' => 'File harus berformat .zip']);
        }

        if ($file->getSize() > 200 * 1024 * 1024) {
            return back()->withErrors(['backup' => 'Ukuran file terlalu besar. Maksimal 200MB.']);
        }

        $zip = new \ZipArchive();
        $tempPath = $file->getRealPath();

        if ($zip->open($tempPath) !== true) {
            return back()->withErrors(['backup' => 'Gagal membuka file ZIP. File mungkin rusak.']);
        }

        $storagePath = storage_path('app/public');
        $extractedCount = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);

            $relativePath = $entryName;
            if (strpos($entryName, 'storage/') === 0) {
                $relativePath = substr($entryName, strlen('storage/'));
            }

            if (empty($relativePath) || $relativePath === '.gitignore') continue;
            if (strpos($relativePath, '..') !== false) continue;

            $targetPath = $storagePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

            if (substr($entryName, -1) === '/') {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
                continue;
            }

            $parentDir = dirname($targetPath);
            if (!is_dir($parentDir)) {
                mkdir($parentDir, 0755, true);
            }

            $content = $zip->getFromIndex($i);
            if ($content !== false) {
                file_put_contents($targetPath, $content);
                $extractedCount++;
            }
        }

        $zip->close();

        return back()->with('success', "Restore berhasil! {$extractedCount} file telah dipulihkan.");
    }

    /**
     * Find mysqldump binary path
     */
    private function findMysqldumpBinary()
    {
        $paths = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
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
            $paths = ['/usr/bin/mysqldump', '/usr/local/bin/mysqldump', 'mysqldump'];
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
            $relativePath = str_replace('\\', '/', $relativePath);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                if ($file->getFilename() === '.gitignore') continue;
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
