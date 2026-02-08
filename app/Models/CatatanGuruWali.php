<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanGuruWali extends Model
{
    use HasFactory;

    protected $table = 'catatan_guru_wali';

    protected $fillable = [
        'siswa_id',
        'guru_nama',
        'tahun_pelajaran',
        'semester',
        'tanggal_pencatatan',
        'jenis_bimbingan',
        'catatan',
        'nilai_praktik_ibadah',
        'perkembangan',
    ];

    protected $casts = [
        'tanggal_pencatatan' => 'date',
    ];

    /**
     * Relationship to Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Get jenis bimbingan options
     */
    public static function jenisBimbinganOptions()
    {
        return [
            'Bimbingan Akademik',
            'Bimbingan Karakter',
            'Sosial Emosional',
            'Kedisiplinan',
            'Potensi dan Minat',
            'Bimbingan Ibadah',
        ];
    }

    /**
     * Get nilai praktik ibadah options
     */
    public static function nilaiPraktikIbadahOptions()
    {
        return ['A', 'B', 'C'];
    }

    /**
     * Get perkembangan options
     */
    public static function perkembanganOptions()
    {
        return [
            'Belum Berkembang',
            'Berkembang Sesuai Harapan',
            'Berkembang Sangat Baik',
        ];
    }
}
