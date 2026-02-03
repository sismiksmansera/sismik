<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guru extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'guru';

    protected $fillable = [
        'nama',
        'nip',
        'password',
        'email',
        'no_hp',
        'username',
        'jenis_kelamin',
        'alamat',
        'status_kepegawaian',
        'golongan',
        'jabatan',
        'mapel_diampu',
        'foto',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'nama_guru', 'nama');
    }

    public function rombelWali()
    {
        return $this->hasMany(Rombel::class, 'wali_kelas', 'nama');
    }

    public function presensiGuru()
    {
        return $this->hasMany(PresensiGuru::class, 'id_guru', 'id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'guru', 'nama');
    }

    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'guru_id', 'id');
    }
}
