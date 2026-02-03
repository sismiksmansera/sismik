<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'nisn',
        'nama_pelapor',
        'rombel_pelapor',
        'kategori',
        'subyek_terlapor',
        'tanggal_kejadian',
        'waktu_kejadian',
        'lokasi_kejadian',
        'deskripsi',
        'bukti_pendukung',
        'status',
        'tanggapan',
        'ditangani_oleh',
        'tahun_pelajaran',
        'semester',
        'diteruskan_ke',
        'ditanggapi_oleh',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
}
