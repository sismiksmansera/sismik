<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginSettings extends Model
{
    use HasFactory;

    protected $table = 'login_settings';

    public $timestamps = false;

    protected $fillable = [
        'background_image',
        'logo_image',
        'overlay_color',
        'overlay_color_end',
        'testing_date',
        'testing_active',
    ];

    protected $casts = [
        'testing_date' => 'date',
    ];
}
