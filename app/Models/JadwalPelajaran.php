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
        'kode_jadwal',
    ];

    /**
     * Scope: only jadwal active on the given date (via periode_jadwal)
     */
    public function scopeActiveOn($query, $date)
    {
        return $query->whereHas('periodeJadwal', function ($q) use ($date) {
            $q->where('tanggal_mulai', '<=', $date)
              ->where(function ($q2) use ($date) {
                  $q2->whereNull('tanggal_akhir')
                     ->orWhere('tanggal_akhir', '>=', $date);
              });
        });
    }

    /**
     * Get the periode jadwal
     */
    public function periodeJadwal()
    {
        return $this->belongsTo(PeriodeJadwal::class, 'kode_jadwal', 'kode');
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

