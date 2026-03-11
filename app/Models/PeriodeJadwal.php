<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeJadwal extends Model
{
    use HasFactory;

    protected $table = 'periode_jadwal';

    protected $fillable = [
        'kode',
        'tanggal_mulai',
        'tanggal_akhir',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    /**
     * Jadwal pelajaran using this kode
     */
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'kode_jadwal', 'kode');
    }

    /**
     * Check if this periode is active on a given date
     */
    public function isActiveOn($date): bool
    {
        if ($this->tanggal_mulai > $date) return false;
        if ($this->tanggal_akhir && $this->tanggal_akhir < $date) return false;
        return true;
    }
}
