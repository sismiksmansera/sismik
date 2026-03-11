<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestasiSiswa extends Model
{
    protected $table = 'prestasi_siswa';
    public $timestamps = false;
    
    protected $fillable = [
        'siswa_id',
        'nama_kompetisi',
        'juara',
        'jenjang',
        'tanggal_pelaksanaan',
        'penyelenggara',
        'tipe_peserta',
        'sumber_prestasi',
        'sumber_id',
        'tahun_pelajaran',
        'semester',
    ];
    
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
