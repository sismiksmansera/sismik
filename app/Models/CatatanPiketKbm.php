<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanPiketKbm extends Model
{
    protected $table = 'catatan_piket_kbm';

    protected $fillable = [
        'piket_kbm_id',
        'tanggal',
        'jam_ke',
        'nama_guru',
        'nama_mapel',
        'nama_rombel',
        'status_kehadiran',
        'keterangan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function piketKbm()
    {
        return $this->belongsTo(PiketKbm::class, 'piket_kbm_id');
    }
}
