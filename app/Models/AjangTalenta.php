<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjangTalenta extends Model
{
    use HasFactory;

    protected $table = 'ajang_talenta';

    protected $fillable = [
        'nama_ajang',
        'tahun',
        'penyelenggara',
        'pembina',
    ];
}
