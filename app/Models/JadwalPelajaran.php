<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    public $timestamps = false;

    protected $fillable = [
        'id_mapel',
        'id_rombel',
        'hari',
        'jam_ke',
        'nama_guru',
        'tahun_pelajaran',
        'semester',
    ];

    /**
     * Get the mata pelajaran
     */
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel');
    }

    /**
     * Get the rombel
     */
    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'id_rombel');
    }
}
