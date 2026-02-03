<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaportSettings extends Model
{
    use HasFactory;

    protected $table = 'raport_settings';

    protected $fillable = [
        'periodik_id',
        'tanggal_bagi_raport',
        'lock_print_raport',
        'lock_print_raport_all',
        'lock_print_riwayat_guru',
        'lock_print_riwayat_all',
        'lock_print_leger_nilai',
        'lock_print_leger_katrol',
        'lock_nilai_minmax',
        'lock_katrol_nilai',
    ];

    protected $casts = [
        'tanggal_bagi_raport' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function periodik()
    {
        return $this->belongsTo(DataPeriodik::class, 'periodik_id');
    }
}
