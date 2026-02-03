<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanggilanOrtu extends Model
{
    protected $table = 'panggilan_ortu';
    
    protected $fillable = [
        'nisn',
        'guru_bk_id',
        'tanggal_surat',
        'no_surat',
        'perihal',
        'alasan',
        'menghadap_ke',
        'tanggal_panggilan',
        'jam_panggilan',
        'tempat',
        'status',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_panggilan' => 'date',
    ];
    
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
    
    public function guruBK()
    {
        return $this->belongsTo(GuruBK::class, 'guru_bk_id');
    }
}
