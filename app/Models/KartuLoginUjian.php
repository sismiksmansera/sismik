<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuLoginUjian extends Model
{
    protected $table = 'kartu_login_ujian';

    protected $fillable = [
        'nama_siswa',
        'kelas',
        'nisn',
        'password_dsmart',
        'password_bimasoft',
        'password_aksi_jihan',
    ];
}
