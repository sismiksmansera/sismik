<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeamananController extends Controller
{
    // Lockout settings
    protected $lockoutMinutes = 15;
    protected $lockoutThreshold = 5;
    
    /**
     * Display security dashboard
     */
    public function index()
    {
        // Ensure tables exist
        $this->ensureTablesExist();
        
        $lockoutTime = now()->subMinutes($this->lockoutMinutes)->format('Y-m-d H:i:s');
        
        // Get locked users (5+ failed attempts in last 15 minutes)
        $lockedUsers = DB::table('login_attempts')
            ->select(
                'username',
                'ip_address',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('MAX(attempt_time) as last_attempt'),
                DB::raw('MIN(attempt_time) as first_attempt')
            )
            ->where('success', 0)
            ->where('attempt_time', '>', $lockoutTime)
            ->groupBy('username', 'ip_address')
            ->havingRaw('COUNT(*) >= ?', [$this->lockoutThreshold])
            ->orderByDesc('attempts')
            ->orderByDesc('last_attempt')
            ->get();
        
        // Get recent login attempts
        $recentAttempts = DB::table('login_attempts')
            ->select('username', 'ip_address', 'attempt_time', 'success')
            ->orderByDesc('attempt_time')
            ->limit(50)
            ->get();
        
        // Get security logs
        $securityLogs = DB::table('security_logs')
            ->select('action', 'user_type', 'username', 'details', 'ip_address', 'created_at')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
        
        // Statistics
        $totalAttemptsToday = DB::table('login_attempts')
            ->whereDate('attempt_time', today())
            ->count();
        
        $failedAttemptsToday = DB::table('login_attempts')
            ->whereDate('attempt_time', today())
            ->where('success', 0)
            ->count();
        
        $successfulLoginsToday = DB::table('security_logs')
            ->whereDate('created_at', today())
            ->where('action', 'login_success')
            ->count();
        
        return view('admin.keamanan.index', compact(
            'lockedUsers', 'recentAttempts', 'securityLogs',
            'totalAttemptsToday', 'failedAttemptsToday', 'successfulLoginsToday'
        ));
    }
    
    /**
     * Unlock a user
     */
    public function unlock(Request $request)
    {
        $username = $request->input('username', '');
        $ip = $request->input('ip', '');
        $affected = 0;
        
        if (!empty($username)) {
            $affected += DB::table('login_attempts')
                ->where('username', $username)
                ->where('success', 0)
                ->delete();
        }
        
        if (!empty($ip)) {
            $affected += DB::table('login_attempts')
                ->where('ip_address', $ip)
                ->where('success', 0)
                ->delete();
        }
        
        // Log the action
        $this->logSecurityEvent('admin_unlock', 'admin', auth()->id() ?? 0, 
            "Admin unlock user: $username, IP: $ip");
        
        return redirect()->route('admin.keamanan.index')
            ->with('success', "Lockout berhasil direset! ($affected record dihapus)");
    }
    
    /**
     * Clear all lockouts
     */
    public function clearAll()
    {
        $affected = DB::table('login_attempts')
            ->where('success', 0)
            ->delete();
        
        $this->logSecurityEvent('admin_clear_all_lockouts', 'admin', auth()->id() ?? 0, 
            "Admin cleared all lockouts ($affected records)");
        
        return redirect()->route('admin.keamanan.index')
            ->with('success', "Semua lockout berhasil dihapus! ($affected record)");
    }
    
    /**
     * Log security event
     */
    protected function logSecurityEvent($action, $userType, $userId, $details)
    {
        DB::table('security_logs')->insert([
            'action' => $action,
            'user_type' => $userType,
            'user_id' => $userId,
            'username' => session('username', 'admin'),
            'details' => $details,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
    
    /**
     * Ensure required tables exist
     */
    protected function ensureTablesExist()
    {
        // Check login_attempts table
        if (!DB::getSchemaBuilder()->hasTable('login_attempts')) {
            DB::statement("
                CREATE TABLE login_attempts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(100) NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                    success TINYINT(1) DEFAULT 0,
                    INDEX idx_username (username),
                    INDEX idx_ip (ip_address),
                    INDEX idx_time (attempt_time)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
        
        // Check security_logs table
        if (!DB::getSchemaBuilder()->hasTable('security_logs')) {
            DB::statement("
                CREATE TABLE security_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    action VARCHAR(50) NOT NULL,
                    user_type VARCHAR(20) DEFAULT NULL,
                    user_id INT DEFAULT NULL,
                    username VARCHAR(100) DEFAULT NULL,
                    details TEXT,
                    ip_address VARCHAR(45) DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_action (action),
                    INDEX idx_created (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }
    
    /**
     * Check if user is locked out (for login controller)
     */
    public static function isLockedOut($username, $ip)
    {
        $lockoutMinutes = 15;
        $lockoutThreshold = 5;
        $lockoutTime = now()->subMinutes($lockoutMinutes)->format('Y-m-d H:i:s');
        
        $attempts = DB::table('login_attempts')
            ->where(function($q) use ($username, $ip) {
                $q->where('username', $username)
                  ->orWhere('ip_address', $ip);
            })
            ->where('success', 0)
            ->where('attempt_time', '>', $lockoutTime)
            ->count();
        
        return $attempts >= $lockoutThreshold;
    }
    
    /**
     * Record login attempt (for login controller)
     */
    public static function recordLoginAttempt($username, $ip, $success = false)
    {
        DB::table('login_attempts')->insert([
            'username' => $username,
            'ip_address' => $ip,
            'attempt_time' => now(),
            'success' => $success ? 1 : 0,
        ]);
    }
}
