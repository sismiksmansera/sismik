<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tamu extends Model
{
    use HasFactory;

    protected $table = 'tamu';

    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'datang_sebagai',
        'bertemu_dengan',
        'keperluan',
        'memberikan_dokumen',
        'jenis_dokumen_diberikan',
        'deskripsi_dokumen_diberikan',
        'meminta_dokumen',
        'jenis_dokumen_diminta',
        'deskripsi_dokumen_diminta',
    ];

    protected $casts = [
        'memberikan_dokumen' => 'boolean',
        'meminta_dokumen' => 'boolean',
    ];

    // Options for dropdowns
    public static function getKategoriOptions(): array
    {
        return [
            'Wali Murid',
            'Jurnalis',
            'Perguruan Tinggi',
            'Tamu Khusus',
            'Tamu Umum',
        ];
    }

    public static function getJenisDokumenOptions(): array
    {
        return [
            'Surat Undangan',
            'Proposal',
            'Barang/Berkas Lain',
        ];
    }

    // Helper to get formatted date
    public function getHariAttribute(): string
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        return $days[$this->created_at->format('l')] ?? $this->created_at->format('l');
    }

    public function getTanggalFormattedAttribute(): string
    {
        return $this->created_at->format('d F Y');
    }

    public function getWaktuAttribute(): string
    {
        return $this->created_at->format('H:i');
    }
}
