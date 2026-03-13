<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SetupController extends Controller
{
    /**
     * Minimum required PHP limits for setup
     */
    private const REQUIRED_LIMITS = [
        'upload_max_filesize' => 100,  // MB
        'post_max_size'       => 100,  // MB
        'memory_limit'        => 512,  // MB
        'max_execution_time'  => 600,  // seconds
    ];

    /**
     * Show the setup page
     */
    public function index(Request $request)
    {
        // If already installed, redirect to home
        try {
            \DB::connection()->getPdo();
            $tables = \DB::select('SHOW TABLES');
            if (!empty($tables)) {
                return redirect('/');
            }
        } catch (\Exception $e) {
            // DB not available, show setup page
        }

        $reason = $request->get('reason', '');
        
        // Read current .env values as defaults
        $defaults = [
            'db_host' => env('DB_HOST', '127.0.0.1'),
            'db_port' => env('DB_PORT', '3306'),
            'db_username' => env('DB_USERNAME', 'root'),
            'db_password' => env('DB_PASSWORD', ''),
            'db_database' => env('DB_DATABASE', 'simas_db'),
        ];

        // Check and auto-fix PHP limits
        $serverChecks = $this->checkServerLimits();
        $fixApplied = false;

        if ($serverChecks['needs_fix']) {
            $fixApplied = $this->applyUserIni();
        }

        return view('setup', compact('reason', 'defaults', 'serverChecks', 'fixApplied'));
    }

    /**
     * Check PHP limits and fix them via .user.ini (AJAX)
     */
    public function fixLimits()
    {
        $applied = $this->applyUserIni();
        $checks = $this->checkServerLimits();

        // Also try to restart PHP-FPM to apply .user.ini immediately
        $this->tryRestartPhpFpm();

        return response()->json([
            'success' => $applied,
            'checks' => $checks,
            'message' => $applied
                ? 'File .user.ini telah dibuat. Silakan refresh halaman untuk menerapkan perubahan.'
                : 'Gagal membuat file .user.ini. Pastikan folder public/ memiliki permission write.',
        ]);
    }

    /**
     * Test MySQL connection (AJAX)
     */
    public function testConnection(Request $request)
    {
        $host = $request->input('db_host', '127.0.0.1');
        $port = $request->input('db_port', '3306');
        $username = $request->input('db_username', 'root');
        $password = $request->input('db_password', '');

        try {
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);

            // Get server version
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);

            // Get existing databases
            $stmt = $pdo->query("SHOW DATABASES");
            $databases = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            // Filter system databases
            $databases = array_values(array_filter($databases, function ($db) {
                return !in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys', 'phpmyadmin']);
            }));

            // Get max_allowed_packet
            $stmt = $pdo->query("SHOW VARIABLES LIKE 'max_allowed_packet'");
            $maxPacket = $stmt->fetch(\PDO::FETCH_ASSOC);
            $maxPacketMB = $maxPacket ? round($maxPacket['Value'] / 1048576) : 0;

            return response()->json([
                'success' => true,
                'message' => "Koneksi berhasil! MySQL v{$version}",
                'databases' => $databases,
                'max_allowed_packet_mb' => $maxPacketMB,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal koneksi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Process installation
     */
    public function install(Request $request)
    {
        // Override PHP limits at runtime (for what we can change)
        set_time_limit(600);
        ini_set('max_execution_time', '600');
        ini_set('memory_limit', '512M');

        // Determine the max file size we can actually accept
        $maxUploadBytes = min(
            $this->parseSize(ini_get('upload_max_filesize')),
            $this->parseSize(ini_get('post_max_size'))
        );
        $maxUploadKB = intval($maxUploadBytes / 1024);

        // Check if the request was rejected by PHP before reaching Laravel
        if (empty($_FILES) && empty($_POST) && $request->server('CONTENT_LENGTH') > 0) {
            $attemptedMB = round($request->server('CONTENT_LENGTH') / 1048576, 1);
            return back()->withErrors([
                'sql_file' => "File terlalu besar ({$attemptedMB} MB). Server hanya mengizinkan upload maksimal " 
                    . ini_get('upload_max_filesize') . ". Klik tombol 'Perbaiki Otomatis' di halaman setup untuk memperbesar batas upload, lalu refresh halaman."
            ])->withInput();
        }

        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_username' => 'required|string',
            'db_database' => 'required|string|regex:/^[a-zA-Z0-9_]+$/',
            'sql_file' => 'required|file|max:' . $maxUploadKB,
        ], [
            'sql_file.max' => 'File SQL terlalu besar. Maksimal: ' . ini_get('upload_max_filesize') . '. Klik "Perbaiki Otomatis" untuk memperbesar batas.',
            'sql_file.required' => 'File SQL wajib diupload.',
        ]);

        $host = $request->input('db_host');
        $port = $request->input('db_port');
        $username = $request->input('db_username');
        $password = $request->input('db_password', '');
        $database = $request->input('db_database');

        // Step 1: Connect to MySQL
        try {
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['connection' => 'Gagal koneksi MySQL: ' . $e->getMessage()])->withInput();
        }

        // Step 2: Create database if not exists
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$database}`");
        } catch (\Exception $e) {
            return back()->withErrors(['database' => 'Gagal membuat database: ' . $e->getMessage()])->withInput();
        }

        // Step 3: Increase MySQL max_allowed_packet for this session
        try {
            $pdo->exec("SET GLOBAL max_allowed_packet = 104857600"); // 100MB
        } catch (\Exception $e) {
            // Not critical — may not have SUPER privilege
            try {
                $pdo->exec("SET SESSION max_allowed_packet = 104857600");
            } catch (\Exception $e2) {
                // Ignore — will use default
            }
        }

        // Step 4: Import SQL file
        $tempPath = null;
        try {
            $sqlFile = $request->file('sql_file');
            $sqlContent = file_get_contents($sqlFile->getRealPath());

            if (empty($sqlContent)) {
                return back()->withErrors(['sql_file' => 'File SQL kosong'])->withInput();
            }

            // Execute SQL using mysql CLI for better compatibility
            $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sismik_import_' . time() . '.sql';
            file_put_contents($tempPath, $sqlContent);

            // Try mysql CLI first (handles large files and complex SQL better)
            $mysqlBin = $this->findMysqlBinary();
            
            if ($mysqlBin) {
                $passwordArg = !empty($password) ? "-p\"{$password}\"" : '';
                $cmd = "\"{$mysqlBin}\" -h {$host} -P {$port} -u {$username} {$passwordArg} --max_allowed_packet=100M {$database} < \"{$tempPath}\" 2>&1";
                
                exec($cmd, $output, $returnCode);
                @unlink($tempPath);

                if ($returnCode !== 0) {
                    // Fallback to PDO execution
                    $this->importViaPdo($pdo, $sqlContent, $database);
                }
            } else {
                @unlink($tempPath);
                // Use PDO execution
                $this->importViaPdo($pdo, $sqlContent, $database);
            }

        } catch (\Exception $e) {
            @unlink($tempPath ?? '');
            return back()->withErrors(['sql_file' => 'Gagal import SQL: ' . $e->getMessage()])->withInput();
        }

        // Step 5: Update .env file
        try {
            $this->updateEnv([
                'DB_HOST' => $host,
                'DB_PORT' => $port,
                'DB_DATABASE' => $database,
                'DB_USERNAME' => $username,
                'DB_PASSWORD' => $password,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['env' => 'Database terinstall tapi gagal update .env: ' . $e->getMessage()])->withInput();
        }

        // Step 6: Clear config cache manually
        try {
            $cachedConfigPath = base_path('bootstrap/cache/config.php');
            if (file_exists($cachedConfigPath)) {
                @unlink($cachedConfigPath);
            }
            $cachePath = storage_path('framework/cache/data');
            if (is_dir($cachePath)) {
                array_map('unlink', glob($cachePath . '/*') ?: []);
            }
        } catch (\Exception $e) {
            // Non-critical
        }

        return redirect('/setup/success');
    }

    /**
     * Show success page
     */
    public function success()
    {
        return view('setup-success');
    }

    // ================================================================
    // PRIVATE HELPERS
    // ================================================================

    /**
     * Check server PHP limits and return status
     */
    private function checkServerLimits(): array
    {
        $checks = [];
        $needsFix = false;

        foreach (self::REQUIRED_LIMITS as $directive => $required) {
            $current = $this->parseSizeMB($directive);
            $ok = $current >= $required;
            if (!$ok) $needsFix = true;
            $checks[$directive] = [
                'current' => $current,
                'required' => $required,
                'ok' => $ok,
                'unit' => in_array($directive, ['max_execution_time']) ? 'detik' : 'MB',
            ];
        }

        // Check if .user.ini already exists
        $userIniExists = file_exists(public_path('.user.ini'));

        return [
            'checks' => $checks,
            'needs_fix' => $needsFix,
            'user_ini_exists' => $userIniExists,
        ];
    }

    /**
     * Parse a PHP size directive into megabytes
     */
    private function parseSizeMB(string $directive): int
    {
        if ($directive === 'max_execution_time') {
            return (int) ini_get($directive);
        }
        $value = ini_get($directive);
        return intval($this->parseSize($value) / 1048576);
    }

    /**
     * Parse PHP size string (e.g., "128M") to bytes
     */
    private function parseSize(string $size): int
    {
        $size = trim($size);
        $last = strtolower(substr($size, -1));
        $value = (int) $size;
        
        switch ($last) {
            case 'g': $value *= 1073741824; break;
            case 'm': $value *= 1048576; break;
            case 'k': $value *= 1024; break;
        }
        
        return $value;
    }

    /**
     * Create/update .user.ini in public/ directory to fix PHP limits.
     * This works with PHP-FPM (Nginx) without needing sudo or editing php.ini.
     */
    private function applyUserIni(): bool
    {
        $iniPath = public_path('.user.ini');
        
        $content = "; Auto-generated by SISMIK Setup\n";
        $content .= "; This file overrides PHP settings for this application only.\n";
        $content .= "; It is read by PHP-FPM automatically.\n\n";
        $content .= "upload_max_filesize = 100M\n";
        $content .= "post_max_size = 105M\n";
        $content .= "memory_limit = 512M\n";
        $content .= "max_execution_time = 600\n";
        $content .= "max_input_time = 600\n";
        $content .= "max_input_vars = 5000\n";

        try {
            file_put_contents($iniPath, $content);
            return file_exists($iniPath);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Try to restart PHP-FPM to apply .user.ini immediately
     */
    private function tryRestartPhpFpm(): void
    {
        // The optimize script has sudo access, try using it for a quick restart
        $script = base_path('scripts/optimize-server.sh');
        if (file_exists($script)) {
            // Only restart PHP-FPM, not full optimization
            @exec("sudo systemctl reload php*-fpm 2>/dev/null");
        }
    }

    /**
     * Find mysql binary path
     */
    private function findMysqlBinary()
    {
        $paths = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $laragonMysqlDir = 'C:\\laragon\\bin\\mysql';
            if (is_dir($laragonMysqlDir)) {
                $dirs = glob($laragonMysqlDir . '\\mysql-*', GLOB_ONLYDIR);
                foreach ($dirs as $dir) {
                    $paths[] = $dir . '\\bin\\mysql.exe';
                }
            }
            $paths[] = 'C:\\xampp\\mysql\\bin\\mysql.exe';
            $paths[] = 'mysql';
        } else {
            $paths = [
                '/usr/bin/mysql',
                '/usr/local/bin/mysql',
                'mysql',
            ];
        }

        foreach ($paths as $path) {
            if ($path === 'mysql') {
                exec('which mysql 2>/dev/null || where mysql 2>nul', $out, $code);
                if ($code === 0) return 'mysql';
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Import SQL via PDO (fallback) - robust parser
     */
    private function importViaPdo($pdo, $sqlContent, $database)
    {
        $pdo->exec("USE `{$database}`");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
        $pdo->exec("SET NAMES utf8mb4");

        // Try to set max_allowed_packet for this session
        try {
            $pdo->exec("SET SESSION max_allowed_packet = 104857600");
        } catch (\Exception $e) {
            // Ignore
        }

        $length = strlen($sqlContent);
        $statement = '';
        $inString = false;
        $stringChar = '';
        $escaped = false;
        $inLineComment = false;
        $inBlockComment = false;
        $importedCount = 0;
        $errorCount = 0;

        for ($i = 0; $i < $length; $i++) {
            $char = $sqlContent[$i];
            $nextChar = ($i + 1 < $length) ? $sqlContent[$i + 1] : '';

            if ($escaped) {
                $statement .= $char;
                $escaped = false;
                continue;
            }

            if (!$inString && !$inBlockComment) {
                if ($char === '-' && $nextChar === '-') {
                    $inLineComment = true;
                    $i++;
                    continue;
                }
                if ($char === '#') {
                    $inLineComment = true;
                    continue;
                }
            }

            if ($inLineComment) {
                if ($char === "\n") {
                    $inLineComment = false;
                }
                continue;
            }

            if (!$inString && $char === '/' && $nextChar === '*') {
                if (($i + 2 < $length) && $sqlContent[$i + 2] === '!') {
                    $statement .= $char;
                    continue;
                }
                $inBlockComment = true;
                $i++;
                continue;
            }

            if ($inBlockComment) {
                if ($char === '*' && $nextChar === '/') {
                    $inBlockComment = false;
                    $i++;
                }
                continue;
            }

            if (!$inString && ($char === "'" || $char === '"')) {
                $inString = true;
                $stringChar = $char;
                $statement .= $char;
                continue;
            }

            if ($inString) {
                if ($char === '\\') {
                    $escaped = true;
                    $statement .= $char;
                    continue;
                }
                if ($char === $stringChar) {
                    if ($nextChar === $stringChar) {
                        $statement .= $char . $nextChar;
                        $i++;
                        continue;
                    }
                    $inString = false;
                }
                $statement .= $char;
                continue;
            }

            if ($char === ';') {
                $trimmed = trim($statement);
                if (!empty($trimmed)) {
                    try {
                        $pdo->exec($trimmed);
                        $importedCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        \Log::warning("SQL import error (statement #{$importedCount}): " . $e->getMessage());
                    }
                }
                $statement = '';
                continue;
            }

            $statement .= $char;
        }

        $trimmed = trim($statement);
        if (!empty($trimmed) && $trimmed !== ';') {
            try {
                $pdo->exec($trimmed);
                $importedCount++;
            } catch (\Exception $e) {
                $errorCount++;
                \Log::warning("SQL import final statement error: " . $e->getMessage());
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        \Log::info("SQL import via PDO completed: {$importedCount} statements, {$errorCount} errors");
    }

    /**
     * Update .env file values
     */
    private function updateEnv(array $data)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $escapedValue = $value;
            if (str_contains($value, ' ') || str_contains($value, '#') || empty($value)) {
                $escapedValue = "\"$value\"";
            }

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$escapedValue}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
