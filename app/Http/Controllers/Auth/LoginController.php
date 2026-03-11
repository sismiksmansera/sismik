<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminSekolah;
use App\Models\Guru;
use App\Models\GuruBK;
use App\Models\Siswa;
use App\Models\LoginSettings;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Optimized: Check guards using array iteration
        $guards = [
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'guru_bk' => 'guru_bk.dashboard',
            'siswa' => 'siswa.dashboard',
        ];

        foreach ($guards as $guard => $route) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route($route);
            }
        }

        // Cache login settings for faster page load
        $loginSettings = \Illuminate\Support\Facades\Cache::remember('login_settings', 3600, function() {
            return LoginSettings::first();
        });
        
        return view('auth.login', compact('loginSettings'));
    }

    /**
     * Handle login request with multi-guard detection
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // 1. Check admin_sekolah
        $admin = AdminSekolah::where('username', $username)->first();
        if ($admin && $this->verifyPassword($password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . $admin->nama);
        }

        // 2. Check guru (by NIP)
        $guru = Guru::where('nip', $username)->first();
        if ($guru && $this->verifyPassword($password, $guru->password)) {
            Auth::guard('guru')->login($guru);
            $request->session()->regenerate();
            return redirect()->intended(route('guru.dashboard'))
                ->with('success', 'Selamat datang, ' . $guru->nama);
        }

        // 3. Check guru_bk (by NIP)
        $guruBK = GuruBK::where('nip', $username)->first();
        if ($guruBK && $this->verifyPassword($password, $guruBK->password)) {
            Auth::guard('guru_bk')->login($guruBK);
            $request->session()->regenerate();
            return redirect()->intended(route('guru_bk.dashboard'))
                ->with('success', 'Selamat datang, ' . $guruBK->nama);
        }

        // 4. Check siswa (by NISN)
        $siswa = Siswa::where('nisn', $username)->first();
        if ($siswa && $this->verifyPassword($password, $siswa->password)) {
            Auth::guard('siswa')->login($siswa);
            $request->session()->regenerate();
            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Selamat datang, ' . $siswa->nama);
        }

        // Login failed
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    /**
     * Verify password supporting bcrypt, MD5, and plaintext (legacy)
     */
    private function verifyPassword(string $password, ?string $storedHash): bool
    {
        if (empty($storedHash)) {
            return false;
        }

        // Try bcrypt first
        if (Hash::check($password, $storedHash)) {
            return true;
        }

        // Try MD5 (legacy)
        if ($storedHash === md5($password)) {
            return true;
        }

        // Try plaintext (development only)
        if ($storedHash === $password && strlen($password) < 60) {
            return true;
        }

        return false;
    }

    /**
     * Logout from all guards
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('guru')->logout();
        Auth::guard('guru_bk')->logout();
        Auth::guard('siswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
