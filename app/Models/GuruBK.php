<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class GuruBK extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'guru_bk';

    protected $fillable = [
        'nama',
        'nip',
        'email',
        'no_hp',
        'password',
        'jenis_kelamin',
        'alamat',
        'foto',
        'status',
        'status_kepegawaian',
        'golongan',
        'jabatan',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function catatanBimbingan()
    {
        return $this->hasMany(CatatanBimbingan::class, 'guru_bk_id', 'id');
    }

    public function konseling()
    {
        return $this->hasMany(Konseling::class, 'guru_bk_id', 'id');
    }
}
