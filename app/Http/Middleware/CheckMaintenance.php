<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        // Skip maintenance check for these routes
        $excludedRoutes = [
            'setup',
            'setup/*',
            'logout',
            'admin/*',
            'admin-access/*',
        ];

        foreach ($excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Skip if logged in as admin
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Allow login page access only with valid admin access token
        if ($request->is('login') && session('admin_maintenance_access')) {
            return $next($request);
        }

        // Allow POST login if admin access token is in session
        if ($request->is('login') && $request->isMethod('post') && session('admin_maintenance_access')) {
            return $next($request);
        }

        // Check maintenance mode
        try {
            if (Schema::hasTable('login_settings')) {
                $settings = DB::table('login_settings')->first();
                if ($settings && !empty($settings->maintenance_mode) && $settings->maintenance_mode == 1) {
                    $message = $settings->maintenance_message ?? 'Sistem sedang dalam pemeliharaan. Silakan kembali beberapa saat lagi.';
                    return response()->view('maintenance', [
                        'message' => $message,
                        'loginSettings' => $settings,
                    ], 503);
                }
            }
        } catch (\Exception $e) {
            // If DB not available, skip check
        }

        return $next($request);
    }
}
