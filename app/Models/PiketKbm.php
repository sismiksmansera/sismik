<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiketKbm extends Model
{
    protected $table = 'piket_kbm';

    protected $fillable = [
        'hari',
        'nama_guru',
        'nip',
        'tipe_guru',
        'guru_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function guruBk()
    {
        return $this->belongsTo(GuruBK::class, 'guru_id');
    }
}
