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
        'tanggal_mulai',
        'tanggal_akhir',
    ];

    /**
     * Scope: only jadwal active on the given date
     */
    public function scopeActiveOn($query, $date)
    {
        return $query->where('tanggal_mulai', '<=', $date)
                     ->where(function ($q) use ($date) {
                         $q->whereNull('tanggal_akhir')
                           ->orWhere('tanggal_akhir', '>=', $date);
                     });
    }

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
