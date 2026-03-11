<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureLock extends Model
{
    protected $fillable = ['role', 'feature_key', 'feature_name', 'is_locked'];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public static function isLocked($featureKey)
    {
        $feature = self::where('feature_key', $featureKey)->first();
        return $feature ? $feature->is_locked : false;
    }
}
