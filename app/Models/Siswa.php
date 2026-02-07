<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Siswa extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'siswa';

    protected $fillable = [
        'nama_rombel',
        'angkatan_masuk',
        'nis',
        'nisn',
        'nama',
        'jk',
        'agama',
        'tempat_lahir',
        'tgl_lahir',
        'nohp_siswa',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'nama_bapak',
        'pekerjaan_bapak',
        'nohp_bapak',
        'nama_ibu',
        'pekerjaan_ibu',
        'nohp_ibu',
        'jml_saudara',
        'anak_ke',
        'asal_sekolah',
        'nilai_skl',
        'cita_cita',
        'mapel_fav1',
        'mapel_fav2',
        'harapan',
        'email',
        'password',
        'rombel_semester_1',
        'rombel_semester_2',
        'rombel_semester_3',
        'rombel_semester_4',
        'rombel_semester_5',
        'rombel_semester_6',
        'bk_semester_1',
        'bk_semester_2',
        'bk_semester_3',
        'bk_semester_4',
        'bk_semester_5',
        'bk_semester_6',
        'guru_wali_sem_1',
        'guru_wali_sem_2',
        'guru_wali_sem_3',
        'guru_wali_sem_4',
        'guru_wali_sem_5',
        'guru_wali_sem_6',
        'foto',
        'foto_drive_id',
        'foto_url',
        'foto_thumbnail',
        'foto_size',
        'foto_original_name',
        'status_siswa',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'angkatan_masuk' => 'integer',
        'jml_saudara' => 'integer',
        'anak_ke' => 'integer',
        'nilai_skl' => 'decimal:2',
        'foto_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'nisn', 'nisn');
    }

    public function presensi()
    {
        return $this->hasMany(PresensiSiswa::class, 'nisn', 'nisn');
    }

    public function catatanBimbingan()
    {
        return $this->hasMany(CatatanBimbingan::class, 'nisn', 'nisn');
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'nisn', 'nisn');
    }

    public function anggotaEkstrakurikuler()
    {
        return $this->hasMany(AnggotaEkstrakurikuler::class, 'siswa_id', 'id');
    }

    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'siswa_id', 'id');
    }
}
