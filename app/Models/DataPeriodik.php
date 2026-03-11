<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPeriodik extends Model
{
    use HasFactory;

    protected $table = 'data_periodik';
    
    // Database hanya punya created_at, tidak punya updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'tahun_pelajaran',
        'semester',
        'nama_kepala',
        'nip_kepala',
        'aktif',
        'waka_kurikulum',
        'waka_kesiswaan',
        'waka_sarpras',
        'waka_humas',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function jamPelajaranSetting()
    {
        return $this->hasOne(JamPelajaranSetting::class, 'periodik_id', 'id');
    }

    public function raportSettings()
    {
        return $this->hasOne(RaportSettings::class, 'periodik_id', 'id');
    }

    // Scope for active period
    public function scopeAktif($query)
    {
        return $query->where('aktif', 'Ya');
    }
}
