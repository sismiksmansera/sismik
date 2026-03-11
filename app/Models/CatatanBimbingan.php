<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanBimbingan extends Model
{
    use HasFactory;

    protected $table = 'catatan_bimbingan';

    protected $fillable = [
        'nisn',
        'guru_bk_id',
        'pencatat_id',
        'pencatat_nama',
        'pencatat_role',
        'tanggal',
        'jenis_bimbingan',
        'masalah',
        'penyelesaian',
        'tindak_lanjut',
        'keterangan',
        'status',
        'tahun_pelajaran',
        'semester',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }

    public function guruBK()
    {
        return $this->belongsTo(GuruBK::class, 'guru_bk_id', 'id');
    }
}
