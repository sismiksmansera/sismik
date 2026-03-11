<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiKatrol extends Model
{
    use HasFactory;

    protected $table = 'nilai_katrol';

    protected $fillable = [
        'rombel_id',
        'tahun_pelajaran',
        'semester',
        'nisn',
        'mapel',
        'nilai_asli',
        'nilai_katrol',
    ];

    protected $casts = [
        'rombel_id' => 'integer',
        'nilai_asli' => 'decimal:2',
        'nilai_katrol' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id', 'id');
    }
}
