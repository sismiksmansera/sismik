<?php

namespace App\Services;

use App\Models\LoginSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class EffectiveDateService
{
    protected static $hariList = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu'
    ];

    /**
     * Get effective date for schedule display
     * If testing mode is active, returns the testing date
     * Otherwise returns the current date
     *
     * @return array ['date' => 'Y-m-d', 'hari' => 'Senin', 'carbon' => Carbon, 'is_testing' => bool]
     */
    public static function getEffectiveDate(): array
    {
        date_default_timezone_set('Asia/Jakarta');
        
        // Default to today
        $now = Carbon::now();
        $effectiveDate = $now->format('Y-m-d');
        $effectiveHari = self::$hariList[$now->dayOfWeek];
        $isTesting = false;
        
        // Check testing settings with caching (5 minutes)
        try {
            $settings = Cache::remember('login_settings', 300, function() {
                return LoginSettings::first();
            });
            
            if ($settings && $settings->testing_active === 'Ya' && $settings->testing_date) {
                $testingDate = Carbon::parse($settings->testing_date);
                $effectiveDate = $testingDate->format('Y-m-d');
                $effectiveHari = self::$hariList[$testingDate->dayOfWeek];
                $isTesting = true;
            }
        } catch (\Exception $e) {
            // If table/column doesn't exist, use current date
        }
        
        return [
            'date' => $effectiveDate,
            'hari' => $effectiveHari,
            'carbon' => Carbon::parse($effectiveDate),
            'is_testing' => $isTesting,
            'formatted' => Carbon::parse($effectiveDate)->format('d F Y'),
        ];
    }
    
    /**
     * Get the hari in Indonesian
     */
    public static function getHariIndonesia(int $dayOfWeek): string
    {
        return self::$hariList[$dayOfWeek] ?? 'Senin';
    }
    
    /**
     * Clear the login settings cache
     * Call this after updating testing date settings
     */
    public static function clearCache(): void
    {
        Cache::forget('login_settings');
    }
}

