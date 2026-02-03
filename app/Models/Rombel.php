<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    protected $table = 'rombel';

    public $timestamps = false;

    protected $fillable = [
        'nama_rombel',
        'tingkat',
        'wali_kelas',
        'latitude',
        'longitude',
        'alamat',
        'radius_meter',
        'marker_color',
        'mapel_guru_json',
        'tahun_pelajaran',
        'semester',
        // Mata pelajaran flags and guru
        'mapel_pendidikan_agama_islam', 'guru_pendidikan_agama_islam', 'jam_pendidikan_agama_islam',
        'mapel_pendidikan_agama_hindu', 'guru_pendidikan_agama_hindu', 'jam_pendidikan_agama_hindu',
        'mapel_pendidikan_agama_buddha', 'guru_pendidikan_agama_buddha', 'jam_pendidikan_agama_buddha',
        'mapel_pendidikan_agama_kristen', 'guru_pendidikan_agama_kristen', 'jam_pendidikan_agama_kristen',
        'mapel_pendidikan_agama_katholik', 'guru_pendidikan_agama_katholik', 'jam_pendidikan_agama_katholik',
        'mapel_pendidikan_kewarganegaraan', 'guru_pendidikan_kewarganegaraan', 'jam_pendidikan_kewarganegaraan',
        'mapel_bahasa_indonesia', 'guru_bahasa_indonesia', 'jam_bahasa_indonesia',
        'mapel_bahasa_inggris', 'guru_bahasa_inggris', 'jam_bahasa_inggris',
        'mapel_bahasa_inggris_lanjut', 'guru_bahasa_inggris_lanjut', 'jam_bahasa_inggris_lanjut',
        'mapel_matematika', 'guru_matematika', 'jam_matematika',
        'mapel_matematika_lanjut', 'guru_matematika_lanjut', 'jam_matematika_lanjut',
        'mapel_biologi', 'guru_biologi', 'jam_biologi',
        'mapel_fisika', 'guru_fisika', 'jam_fisika',
        'mapel_kimia', 'guru_kimia', 'jam_kimia',
        'mapel_sejarah', 'guru_sejarah', 'jam_sejarah',
        'mapel_ekonomi', 'guru_ekonomi', 'jam_ekonomi',
        'mapel_sosiologi', 'guru_sosiologi', 'jam_sosiologi',
        'mapel_geografi', 'guru_geografi', 'jam_geografi',
        'mapel_informatika', 'guru_informatika', 'jam_informatika',
        'mapel_kka', 'guru_kka', 'jam_kka',
        'mapel_seni_budaya', 'guru_seni_budaya', 'jam_seni_budaya',
        'mapel_pjok', 'guru_pjok', 'jam_pjok',
        'mapel_bahasa_lampung', 'guru_bahasa_lampung', 'jam_bahasa_lampung',
        'mapel_prakarya_dan_kewirausahaan', 'guru_prakarya_dan_kewirausahaan', 'jam_prakarya_dan_kewirausahaan',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meter' => 'integer',
    ];

    // Relationships
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_rombel', 'id');
    }

    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class, 'id_rombel', 'id');
    }

    public function presensiSiswa()
    {
        return $this->hasMany(PresensiSiswa::class, 'id_rombel', 'id');
    }

    public function presensiGuru()
    {
        return $this->hasMany(PresensiGuru::class, 'id_rombel', 'id');
    }

    public function nilaiKatrol()
    {
        return $this->hasMany(NilaiKatrol::class, 'rombel_id', 'id');
    }

    // Get siswa by semester
    public function getSiswaAttribute()
    {
        return Siswa::where('rombel_semester_1', $this->nama_rombel)
            ->orWhere('rombel_semester_2', $this->nama_rombel)
            ->orWhere('rombel_semester_3', $this->nama_rombel)
            ->orWhere('rombel_semester_4', $this->nama_rombel)
            ->orWhere('rombel_semester_5', $this->nama_rombel)
            ->orWhere('rombel_semester_6', $this->nama_rombel)
            ->get();
    }
}
