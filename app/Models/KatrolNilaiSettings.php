<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KatrolNilaiSettings extends Model
{
    protected $table = 'katrol_nilai_settings';

    protected $fillable = [
        'rombel_id',
        'tahun_pelajaran',
        'semester',
        'nilai_min',
        'nilai_max',
        'is_locked',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'rombel_id' => 'integer',
        'nilai_min' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id', 'id');
    }
}
