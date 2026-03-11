<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $table = 'pelanggaran';

    protected $fillable = [
        'tanggal',
        'waktu',
        'jenis_pelanggaran',
        'jenis_lainnya',
        'deskripsi',
        'sanksi',
        'guru_bk_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guruBk()
    {
        return $this->belongsTo(GuruBK::class, 'guru_bk_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'pelanggaran_siswa', 'pelanggaran_id', 'siswa_id');
    }
}
