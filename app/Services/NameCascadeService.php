<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NameCascadeService
{
    /**
     * Update nama guru across all related tables
     * 
     * @param string $oldName Old name of the guru
     * @param string $newName New name of the guru
     * @return int Total number of affected rows
     */
    public static function updateGuruName(string $oldName, string $newName): int
    {
        if (empty($oldName) || empty($newName) || $oldName === $newName) {
            return 0;
        }
        
        $affected = 0;
        
        // 1. jadwal_pelajaran - nama_guru
        if (Schema::hasTable('jadwal_pelajaran') && Schema::hasColumn('jadwal_pelajaran', 'nama_guru')) {
            $affected += DB::table('jadwal_pelajaran')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 2. rombel - wali_kelas
        if (Schema::hasTable('rombel') && Schema::hasColumn('rombel', 'wali_kelas')) {
            $affected += DB::table('rombel')
                ->where('wali_kelas', $oldName)
                ->update(['wali_kelas' => $newName]);
        }
        
        // 3. rombel - guru_* columns (guru per mapel)
        if (Schema::hasTable('rombel')) {
            $guruColumns = [
                'guru_pendidikan_agama_islam', 'guru_pendidikan_agama_hindu',
                'guru_pendidikan_agama_buddha', 'guru_pendidikan_agama_kristen',
                'guru_pendidikan_agama_katholik', 'guru_pendidikan_kewarganegaraan',
                'guru_bahasa_indonesia', 'guru_bahasa_inggris', 'guru_bahasa_inggris_lanjut',
                'guru_matematika', 'guru_matematika_lanjut', 'guru_biologi',
                'guru_fisika', 'guru_kimia', 'guru_sejarah', 'guru_ekonomi',
                'guru_sosiologi', 'guru_geografi', 'guru_informatika',
                'guru_kka', 'guru_seni_budaya', 'guru_pjok',
                'guru_bahasa_lampung', 'guru_prakarya_dan_kewirausahaan'
            ];
            
            foreach ($guruColumns as $column) {
                if (Schema::hasColumn('rombel', $column)) {
                    $affected += DB::table('rombel')
                        ->where($column, $oldName)
                        ->update([$column => $newName]);
                }
            }
        }
        
        // 4. ekstrakurikuler - pembina_1, pembina_2, pembina_3
        if (Schema::hasTable('ekstrakurikuler')) {
            if (Schema::hasColumn('ekstrakurikuler', 'pembina_1')) {
                $affected += DB::table('ekstrakurikuler')
                    ->where('pembina_1', $oldName)
                    ->update(['pembina_1' => $newName]);
            }
            if (Schema::hasColumn('ekstrakurikuler', 'pembina_2')) {
                $affected += DB::table('ekstrakurikuler')
                    ->where('pembina_2', $oldName)
                    ->update(['pembina_2' => $newName]);
            }
            if (Schema::hasColumn('ekstrakurikuler', 'pembina_3')) {
                $affected += DB::table('ekstrakurikuler')
                    ->where('pembina_3', $oldName)
                    ->update(['pembina_3' => $newName]);
            }
        }
        
        // 5. catatan_bimbingan - nama_guru (jika ada kolom ini)
        if (Schema::hasTable('catatan_bimbingan') && Schema::hasColumn('catatan_bimbingan', 'nama_guru')) {
            $affected += DB::table('catatan_bimbingan')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 6. presensi_guru - nama_guru
        if (Schema::hasTable('presensi_guru') && Schema::hasColumn('presensi_guru', 'nama_guru')) {
            $affected += DB::table('presensi_guru')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 7. penilaian - nama_guru
        if (Schema::hasTable('penilaian') && Schema::hasColumn('penilaian', 'nama_guru')) {
            $affected += DB::table('penilaian')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 8. izin_guru - nama_guru
        if (Schema::hasTable('izin_guru') && Schema::hasColumn('izin_guru', 'nama_guru')) {
            $affected += DB::table('izin_guru')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 9. tugas_tambahan - nama_guru
        if (Schema::hasTable('tugas_tambahan') && Schema::hasColumn('tugas_tambahan', 'nama_guru')) {
            $affected += DB::table('tugas_tambahan')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 10. catatan_wali_kelas - nama_wali_kelas
        if (Schema::hasTable('catatan_wali_kelas') && Schema::hasColumn('catatan_wali_kelas', 'nama_wali_kelas')) {
            $affected += DB::table('catatan_wali_kelas')
                ->where('nama_wali_kelas', $oldName)
                ->update(['nama_wali_kelas' => $newName]);
        }
        
        // 11. siswa - guru_wali_sem_* columns (Guru bisa jadi Guru Wali)
        if (Schema::hasTable('siswa')) {
            $waliColumns = [
                'guru_wali_sem_1', 'guru_wali_sem_2', 'guru_wali_sem_3',
                'guru_wali_sem_4', 'guru_wali_sem_5', 'guru_wali_sem_6'
            ];
            
            foreach ($waliColumns as $column) {
                if (Schema::hasColumn('siswa', $column)) {
                    $affected += DB::table('siswa')
                        ->where($column, $oldName)
                        ->update([$column => $newName]);
                }
            }
        }
        
        return $affected;
    }
    
    /**
     * Update nama siswa across all related tables
     * 
     * @param string $oldName Old name of the siswa
     * @param string $newName New name of the siswa
     * @param string|null $nisn NISN of the siswa for additional matching
     * @return int Total number of affected rows
     */
    public static function updateSiswaName(string $oldName, string $newName, ?string $nisn = null): int
    {
        if (empty($oldName) || empty($newName) || $oldName === $newName) {
            return 0;
        }
        
        $affected = 0;
        
        // 1. penilaian - nama_siswa (match by NISN for accuracy)
        if (Schema::hasTable('penilaian') && Schema::hasColumn('penilaian', 'nama_siswa')) {
            if ($nisn) {
                $affected += DB::table('penilaian')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('penilaian')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        // 2. presensi_siswa - nama_siswa
        if (Schema::hasTable('presensi_siswa') && Schema::hasColumn('presensi_siswa', 'nama_siswa')) {
            if ($nisn) {
                $affected += DB::table('presensi_siswa')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('presensi_siswa')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        // 3. prestasi_siswa - nama_siswa
        if (Schema::hasTable('prestasi_siswa') && Schema::hasColumn('prestasi_siswa', 'nama_siswa')) {
            if ($nisn) {
                $affected += DB::table('prestasi_siswa')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('prestasi_siswa')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        // 4. catatan_bimbingan - nama_siswa
        if (Schema::hasTable('catatan_bimbingan') && Schema::hasColumn('catatan_bimbingan', 'nama_siswa')) {
            if ($nisn) {
                $affected += DB::table('catatan_bimbingan')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('catatan_bimbingan')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        // 5. panggilan_ortu - nama_siswa
        if (Schema::hasTable('panggilan_ortu') && Schema::hasColumn('panggilan_ortu', 'nama_siswa')) {
            if ($nisn) {
                $affected += DB::table('panggilan_ortu')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('panggilan_ortu')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        // 6. catatan_wali_kelas - nama_siswa
        if (Schema::hasTable('catatan_wali_kelas') && Schema::hasColumn('catatan_wali_kelas', 'nama_siswa')) {
            if ($nisn) {
                $affected += DB::table('catatan_wali_kelas')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('catatan_wali_kelas')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        return $affected;
    }
    
    /**
     * Update nama guru BK across all related tables
     * 
     * @param string $oldName Old name of the guru BK
     * @param string $newName New name of the guru BK
     * @return int Total number of affected rows
     */
    public static function updateGuruBKName(string $oldName, string $newName): int
    {
        if (empty($oldName) || empty($newName) || $oldName === $newName) {
            return 0;
        }
        
        $affected = 0;
        
        // 1. catatan_bimbingan - nama_guru_bk
        if (Schema::hasTable('catatan_bimbingan') && Schema::hasColumn('catatan_bimbingan', 'nama_guru_bk')) {
            $affected += DB::table('catatan_bimbingan')
                ->where('nama_guru_bk', $oldName)
                ->update(['nama_guru_bk' => $newName]);
        }
        
        // 2. panggilan_ortu - nama_guru_bk
        if (Schema::hasTable('panggilan_ortu') && Schema::hasColumn('panggilan_ortu', 'nama_guru_bk')) {
            $affected += DB::table('panggilan_ortu')
                ->where('nama_guru_bk', $oldName)
                ->update(['nama_guru_bk' => $newName]);
        }
        
        // 3. ekstrakurikuler - pembina_1, pembina_2, pembina_3 (guru BK juga bisa jadi pembina)
        if (Schema::hasTable('ekstrakurikuler')) {
            if (Schema::hasColumn('ekstrakurikuler', 'pembina_1')) {
                $affected += DB::table('ekstrakurikuler')
                    ->where('pembina_1', $oldName)
                    ->update(['pembina_1' => $newName]);
            }
            if (Schema::hasColumn('ekstrakurikuler', 'pembina_2')) {
                $affected += DB::table('ekstrakurikuler')
                    ->where('pembina_2', $oldName)
                    ->update(['pembina_2' => $newName]);
            }
            if (Schema::hasColumn('ekstrakurikuler', 'pembina_3')) {
                $affected += DB::table('ekstrakurikuler')
                    ->where('pembina_3', $oldName)
                    ->update(['pembina_3' => $newName]);
            }
        }
        
        // 4. siswa - bk_semester_* columns
        if (Schema::hasTable('siswa')) {
            $bkColumns = [
                'bk_semester_1', 'bk_semester_2', 'bk_semester_3',
                'bk_semester_4', 'bk_semester_5', 'bk_semester_6'
            ];
            
            foreach ($bkColumns as $column) {
                if (Schema::hasColumn('siswa', $column)) {
                    $affected += DB::table('siswa')
                        ->where($column, $oldName)
                        ->update([$column => $newName]);
                }
            }
            
            // Also update guru_wali_sem columns (Guru BK can be Guru Wali)
            $waliColumns = [
                'guru_wali_sem_1', 'guru_wali_sem_2', 'guru_wali_sem_3',
                'guru_wali_sem_4', 'guru_wali_sem_5', 'guru_wali_sem_6'
            ];
            
            foreach ($waliColumns as $column) {
                if (Schema::hasColumn('siswa', $column)) {
                    $affected += DB::table('siswa')
                        ->where($column, $oldName)
                        ->update([$column => $newName]);
                }
            }
        }
        
        return $affected;
    }
}
