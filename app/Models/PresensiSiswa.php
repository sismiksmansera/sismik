<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiSiswa extends Model
{
    use HasFactory;

    protected $table = 'presensi_siswa';

    public $timestamps = false;

    protected $fillable = [
        'nama_siswa',
        'nisn',
        'presensi',
        'mata_pelajaran',
        'tanggal_presensi',
        'jam_ke_1', 'jam_ke_2', 'jam_ke_3', 'jam_ke_4', 'jam_ke_5',
        'jam_ke_6', 'jam_ke_7', 'jam_ke_8', 'jam_ke_9', 'jam_ke_10', 'jam_ke_11',
        'tanggal_waktu_record',
        'koordinat_melakukan_presensi',
        'id_rombel',
        'tahun_pelajaran',
        'semester',
        'guru_pengajar',
    ];

    protected $casts = [
        'tanggal_presensi' => 'date',
        'tanggal_waktu_record' => 'datetime',
        'id_rombel' => 'integer',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'id_rombel', 'id');
    }
}
