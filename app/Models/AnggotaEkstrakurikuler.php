<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaEkstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'anggota_ekstrakurikuler';

    public $timestamps = false;

    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
        'tahun_pelajaran',
        'semester',
        'tanggal_bergabung',
        'status',
        'nilai',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id', 'id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}
