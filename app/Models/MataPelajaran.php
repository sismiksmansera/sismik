<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    public $timestamps = false;

    protected $fillable = [
        'nama_mapel',
        'kode_mapel',
        'kelompok',
    ];

    /**
     * Get the jadwal for this mata pelajaran
     */
    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_mapel');
    }
}
