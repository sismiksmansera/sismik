<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    public $timestamps = false;

    protected $fillable = [
        'nama_rombel',
        'mapel',
        'nama_siswa',
        'nis',
        'nisn',
        'tanggal_penilaian',
        'jam_ke',
        'materi',
        'nilai',
        'keterangan',
        'guru',
        'tahun_pelajaran',
        'semester',
    ];

    protected $casts = [
        'tanggal_penilaian' => 'date',
        'nilai' => 'decimal:2',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
}
