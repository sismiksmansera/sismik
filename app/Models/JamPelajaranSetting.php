<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelajaranSetting extends Model
{
    use HasFactory;

    protected $table = 'jam_pelajaran_setting';

    protected $fillable = [
        'periodik_id',
        'jp_1_mulai', 'jp_1_selesai',
        'jp_2_mulai', 'jp_2_selesai',
        'jp_3_mulai', 'jp_3_selesai',
        'jp_4_mulai', 'jp_4_selesai',
        'jp_5_mulai', 'jp_5_selesai',
        'jp_6_mulai', 'jp_6_selesai',
        'jp_7_mulai', 'jp_7_selesai',
        'jp_8_mulai', 'jp_8_selesai',
        'jp_9_mulai', 'jp_9_selesai',
        'jp_10_mulai', 'jp_10_selesai',
        'jp_11_mulai', 'jp_11_selesai',
    ];

    public function periodik()
    {
        return $this->belongsTo(DataPeriodik::class, 'periodik_id');
    }
}
