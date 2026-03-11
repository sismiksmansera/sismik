<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SetupController extends Controller
{
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

        return view('setup', compact('reason', 'defaults'));
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

            return response()->json([
                'success' => true,
                'message' => "Koneksi berhasil! MySQL v{$version}",
                'databases' => $databases,
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
        // Increase time limit for large SQL imports
        set_time_limit(300);
        ini_set('max_execution_time', '300');

        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_username' => 'required|string',
            'db_database' => 'required|string|regex:/^[a-zA-Z0-9_]+$/',
            'sql_file' => 'required|file|max:51200', // max 50MB
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

        // Step 3: Import SQL file
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
                $cmd = "\"{$mysqlBin}\" -h {$host} -P {$port} -u {$username} {$passwordArg} {$database} < \"{$tempPath}\" 2>&1";
                
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

        // Step 4: Update .env file
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

        // Step 5: Clear config cache manually (avoid Artisan::call which can kill the process)
        try {
            $cachedConfigPath = base_path('bootstrap/cache/config.php');
            if (file_exists($cachedConfigPath)) {
                @unlink($cachedConfigPath);
            }
            // Clear file-based cache
            $cachePath = storage_path('framework/cache/data');
            if (is_dir($cachePath)) {
                array_map('unlink', glob($cachePath . '/*') ?: []);
            }
        } catch (\Exception $e) {
            // Non-critical, continue
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

    /**
     * Find mysql binary path
     */
    private function findMysqlBinary()
    {
        $paths = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Scan Laragon mysql directories dynamically
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

            // Handle escape characters inside strings
            if ($escaped) {
                $statement .= $char;
                $escaped = false;
                continue;
            }

            // Handle line comments (-- or #)
            if (!$inString && !$inBlockComment) {
                if ($char === '-' && $nextChar === '-') {
                    $inLineComment = true;
                    $i++; // skip next char
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

            // Handle block comments /* */
            if (!$inString && $char === '/' && $nextChar === '*') {
                // Keep /*!40101 ... */ conditional comments as they are MySQL-specific
                if (($i + 2 < $length) && $sqlContent[$i + 2] === '!') {
                    // This is a conditional comment, include it
                    $statement .= $char;
                    continue;
                }
                $inBlockComment = true;
                $i++; // skip *
                continue;
            }

            if ($inBlockComment) {
                if ($char === '*' && $nextChar === '/') {
                    $inBlockComment = false;
                    $i++; // skip /
                }
                continue;
            }

            // Handle string literals
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
                    // Check for escaped quote (double quote like '')
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

            // Statement terminator
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

        // Execute any remaining statement
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
            // Escape value if it contains spaces
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
