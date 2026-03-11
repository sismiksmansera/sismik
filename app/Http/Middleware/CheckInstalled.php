<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     * Redirect to setup page if database is not accessible.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip if already on setup routes
        if ($request->is('setup') || $request->is('setup/*')) {
            return $next($request);
        }

        // Skip for asset files
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*') || $request->is('storage/*')) {
            return $next($request);
        }

        // Check if database is accessible
        try {
            \DB::connection()->getPdo();
            $dbName = \DB::connection()->getDatabaseName();
            
            // Check if database actually has tables (not just an empty database)
            $tables = \DB::select('SHOW TABLES');
            if (empty($tables)) {
                return redirect('/setup?reason=empty');
            }
            
        } catch (\Exception $e) {
            return redirect('/setup');
        }

        return $next($request);
    }
}
