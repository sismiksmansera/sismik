<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAsesmenSekolah extends Model
{
    use HasFactory;

    protected $table = 'nilai_asesmen_sekolah';

    protected $fillable = [
        'jenis_asesmen',
        'semester',
        'tahun_pelajaran',
        'nama_rombel',
        'mata_pelajaran',
        'nama_siswa',
        'nisn',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
}
