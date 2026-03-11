<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\FeatureLock;
use Illuminate\Http\Request;

class CheckFeatureLock
{
    public function handle(Request $request, Closure $next, $featureKey)
    {
        if (FeatureLock::isLocked($featureKey)) {
            $role = explode('.', $featureKey)[0] ?? '';
            return response()->view('feature-locked', ['featureName' => $featureKey, 'role' => $role], 403);
        }

        return $next($request);
    }
}
