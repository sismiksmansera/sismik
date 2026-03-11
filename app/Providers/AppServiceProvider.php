<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\LoginSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share login settings (logo) with all sidebar partials
        // Using cache to reduce database queries - cache for 1 hour
        // Wrapped in try-catch to handle case when DB is not yet configured (setup mode)
        View::composer('layouts.partials.*', function ($view) {
            try {
                $loginSettings = Cache::remember('login_settings', 3600, function () {
                    return LoginSettings::first();
                });
                $view->with('loginSettings', $loginSettings);
            } catch (\Exception $e) {
                $view->with('loginSettings', null);
            }
        });
    }

    /**
     * Clear login settings cache (call when settings are updated)
     */
    public static function clearLoginSettingsCache(): void
    {
        Cache::forget('login_settings');
    }
}
