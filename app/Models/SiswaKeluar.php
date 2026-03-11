<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaKeluar extends Model
{
    protected $table = 'siswa_keluar';

    public $timestamps = false;

    protected $fillable = [
        'siswa_id', 'nisn', 'nis', 'nama', 'jk', 'agama', 'tempat_lahir', 'tgl_lahir', 'nohp_siswa', 'email',
        'provinsi', 'kota', 'kecamatan', 'kelurahan',
        'nama_bapak', 'pekerjaan_bapak', 'nohp_bapak',
        'nama_ibu', 'pekerjaan_ibu', 'nohp_ibu',
        'jml_saudara', 'anak_ke', 'asal_sekolah', 'nilai_skl', 'cita_cita', 'mapel_fav1', 'mapel_fav2', 'harapan', 'angkatan_masuk',
        'rombel_semester_1', 'rombel_semester_2', 'rombel_semester_3', 'rombel_semester_4', 'rombel_semester_5', 'rombel_semester_6',
        'bk_semester_1', 'bk_semester_2', 'bk_semester_3', 'bk_semester_4', 'bk_semester_5', 'bk_semester_6',
        'tanggal_keluar', 'jenis_keluar', 'keterangan',
    ];
}
