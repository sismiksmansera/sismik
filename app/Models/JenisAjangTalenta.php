<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAjangTalenta extends Model
{
    use HasFactory;

    protected $table = 'jenis_ajang_talenta';

    protected $fillable = [
        'nama_jenis',
    ];
}
