<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckGuruBK
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('guru_bk')->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai guru BK untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
