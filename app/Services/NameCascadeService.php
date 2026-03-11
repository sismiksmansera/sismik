<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NameCascadeService
{
    /**
     * Update nama guru across all related tables.
     * Uses BOTH guru ID and old name for comprehensive matching.
     * 
     * @param string $oldName Old name of the guru
     * @param string $newName New name of the guru
     * @param int|null $guruId ID of the guru for ID-based matching
     * @return int Total number of affected rows
     */
    public static function updateGuruName(string $oldName, string $newName, ?int $guruId = null): int
    {
        if (empty($oldName) || empty($newName) || $oldName === $newName) {
            return 0;
        }
        
        $affected = 0;
        
        // ── Tables with nama_guru (text) column ──
        
        // 1. jadwal_pelajaran.nama_guru
        if (Schema::hasTable('jadwal_pelajaran') && Schema::hasColumn('jadwal_pelajaran', 'nama_guru')) {
            $affected += DB::table('jadwal_pelajaran')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 2. catatan_piket_kbm.nama_guru
        if (Schema::hasTable('catatan_piket_kbm') && Schema::hasColumn('catatan_piket_kbm', 'nama_guru')) {
            $affected += DB::table('catatan_piket_kbm')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 3. piket_kbm.nama_guru
        if (Schema::hasTable('piket_kbm') && Schema::hasColumn('piket_kbm', 'nama_guru')) {
            $affected += DB::table('piket_kbm')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 4. mata_pelajaran.nama_guru
        if (Schema::hasTable('mata_pelajaran') && Schema::hasColumn('mata_pelajaran', 'nama_guru')) {
            $affected += DB::table('mata_pelajaran')
                ->where('nama_guru', $oldName)
                ->update(['nama_guru' => $newName]);
        }
        
        // 5. catatan_guru_wali.guru_nama
        if (Schema::hasTable('catatan_guru_wali') && Schema::hasColumn('catatan_guru_wali', 'guru_nama')) {
            $affected += DB::table('catatan_guru_wali')
                ->where('guru_nama', $oldName)
                ->update(['guru_nama' => $newName]);
        }
        
        // 6. penilaian.guru (text column storing guru name)
        if (Schema::hasTable('penilaian') && Schema::hasColumn('penilaian', 'guru')) {
            $affected += DB::table('penilaian')
                ->where('guru', $oldName)
                ->update(['guru' => $newName]);
        }
        
        // 7. izin_guru.guru (text column storing guru name)
        if (Schema::hasTable('izin_guru') && Schema::hasColumn('izin_guru', 'guru')) {
            $affected += DB::table('izin_guru')
                ->where('guru', $oldName)
                ->update(['guru' => $newName]);
        }
        
        // 8. presensi_siswa.guru_pengajar
        if (Schema::hasTable('presensi_siswa') && Schema::hasColumn('presensi_siswa', 'guru_pengajar')) {
            $affected += DB::table('presensi_siswa')
                ->where('guru_pengajar', $oldName)
                ->update(['guru_pengajar' => $newName]);
        }
        
        // 9. catatan_bimbingan.pencatat_nama (guru bisa jadi pencatat)
        if (Schema::hasTable('catatan_bimbingan') && Schema::hasColumn('catatan_bimbingan', 'pencatat_nama')) {
            $affected += DB::table('catatan_bimbingan')
                ->where('pencatat_nama', $oldName)
                ->update(['pencatat_nama' => $newName]);
        }
        
        // 10. notifikasi.penerima_nama (guru bisa jadi penerima)
        if (Schema::hasTable('notifikasi') && Schema::hasColumn('notifikasi', 'penerima_nama')) {
            $affected += DB::table('notifikasi')
                ->where('penerima_nama', $oldName)
                ->update(['penerima_nama' => $newName]);
        }
        
        // 11. ajang_talenta.pembina (guru bisa jadi pembina)
        if (Schema::hasTable('ajang_talenta') && Schema::hasColumn('ajang_talenta', 'pembina')) {
            $affected += DB::table('ajang_talenta')
                ->where('pembina', $oldName)
                ->update(['pembina' => $newName]);
        }
        
        // ── rombel.wali_kelas ──
        if (Schema::hasTable('rombel') && Schema::hasColumn('rombel', 'wali_kelas')) {
            $affected += DB::table('rombel')
                ->where('wali_kelas', $oldName)
                ->update(['wali_kelas' => $newName]);
        }
        
        // ── rombel.guru_* columns (guru per mapel) ──
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
        
        // ── rombel.mapel_guru_json (JSON text containing guru names) ──
        if (Schema::hasTable('rombel') && Schema::hasColumn('rombel', 'mapel_guru_json')) {
            $rowsWithJson = DB::table('rombel')
                ->whereNotNull('mapel_guru_json')
                ->where('mapel_guru_json', '!=', '')
                ->where('mapel_guru_json', 'like', '%' . $oldName . '%')
                ->get(['id', 'mapel_guru_json']);
            
            foreach ($rowsWithJson as $row) {
                $updatedJson = str_replace($oldName, $newName, $row->mapel_guru_json);
                if ($updatedJson !== $row->mapel_guru_json) {
                    DB::table('rombel')
                        ->where('id', $row->id)
                        ->update(['mapel_guru_json' => $updatedJson]);
                    $affected++;
                }
            }
        }
        
        // ── ekstrakurikuler.pembina_1, pembina_2, pembina_3 ──
        if (Schema::hasTable('ekstrakurikuler')) {
            foreach (['pembina_1', 'pembina_2', 'pembina_3'] as $col) {
                if (Schema::hasColumn('ekstrakurikuler', $col)) {
                    $affected += DB::table('ekstrakurikuler')
                        ->where($col, $oldName)
                        ->update([$col => $newName]);
                }
            }
        }
        
        // ── siswa.guru_wali_sem_* columns ──
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
        
        // ── piket_kbm with guru_id (update via ID if available) ──
        if ($guruId && Schema::hasTable('piket_kbm') && Schema::hasColumn('piket_kbm', 'guru_id')) {
            $affected += DB::table('piket_kbm')
                ->where('guru_id', $guruId)
                ->where('nama_guru', '!=', $newName)
                ->update(['nama_guru' => $newName]);
        }
        
        return $affected;
    }
    
    /**
     * Update nama siswa across all related tables.
     * Uses NISN for accurate matching when available.
     * 
     * @param string $oldName Old name of the siswa
     * @param string $newName New name of the siswa
     * @param string|null $nisn NISN of the siswa for accurate matching
     * @return int Total number of affected rows
     */
    public static function updateSiswaName(string $oldName, string $newName, ?string $nisn = null): int
    {
        if (empty($oldName) || empty($newName) || $oldName === $newName) {
            return 0;
        }
        
        $affected = 0;
        
        // Helper to update by NISN if available, otherwise by old name
        $updateByNisnOrName = function(string $table, string $nameColumn, string $nisnColumn = 'nisn') use ($oldName, $newName, $nisn, &$affected) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $nameColumn)) {
                return;
            }
            
            if ($nisn && Schema::hasColumn($table, $nisnColumn)) {
                $affected += DB::table($table)
                    ->where($nisnColumn, $nisn)
                    ->update([$nameColumn => $newName]);
            } else {
                $affected += DB::table($table)
                    ->where($nameColumn, $oldName)
                    ->update([$nameColumn => $newName]);
            }
        };
        
        // 1. penilaian.nama_siswa (has nisn column)
        $updateByNisnOrName('penilaian', 'nama_siswa', 'nisn');
        
        // 2. presensi_siswa.nama_siswa (has nisn column)
        $updateByNisnOrName('presensi_siswa', 'nama_siswa', 'nisn');
        
        // 3. katrol_nilai_leger.nama_siswa
        if (Schema::hasTable('katrol_nilai_leger') && Schema::hasColumn('katrol_nilai_leger', 'nama_siswa')) {
            if ($nisn && Schema::hasColumn('katrol_nilai_leger', 'nisn')) {
                $affected += DB::table('katrol_nilai_leger')
                    ->where('nisn', $nisn)
                    ->update(['nama_siswa' => $newName]);
            } else {
                $affected += DB::table('katrol_nilai_leger')
                    ->where('nama_siswa', $oldName)
                    ->update(['nama_siswa' => $newName]);
            }
        }
        
        return $affected;
    }
    
    /**
     * Update nama guru BK across all related tables.
     * Uses BOTH guru BK ID and old name for comprehensive matching.
     * 
     * @param string $oldName Old name of the guru BK
     * @param string $newName New name of the guru BK
     * @param int|null $guruBkId ID of the guru BK for ID-based matching
     * @return int Total number of affected rows
     */
    public static function updateGuruBKName(string $oldName, string $newName, ?int $guruBkId = null): int
    {
        if (empty($oldName) || empty($newName) || $oldName === $newName) {
            return 0;
        }
        
        $affected = 0;
        
        // 1. ekstrakurikuler.pembina_1, pembina_2, pembina_3 (guru BK bisa jadi pembina)
        if (Schema::hasTable('ekstrakurikuler')) {
            foreach (['pembina_1', 'pembina_2', 'pembina_3'] as $col) {
                if (Schema::hasColumn('ekstrakurikuler', $col)) {
                    $affected += DB::table('ekstrakurikuler')
                        ->where($col, $oldName)
                        ->update([$col => $newName]);
                }
            }
        }
        
        // 2. ajang_talenta.pembina (guru BK bisa jadi pembina)
        if (Schema::hasTable('ajang_talenta') && Schema::hasColumn('ajang_talenta', 'pembina')) {
            $affected += DB::table('ajang_talenta')
                ->where('pembina', $oldName)
                ->update(['pembina' => $newName]);
        }
        
        // 3. notifikasi.penerima_nama (guru BK bisa jadi penerima)
        if (Schema::hasTable('notifikasi') && Schema::hasColumn('notifikasi', 'penerima_nama')) {
            $affected += DB::table('notifikasi')
                ->where('penerima_nama', $oldName)
                ->update(['penerima_nama' => $newName]);
        }
        
        // 4. catatan_bimbingan.pencatat_nama (guru BK bisa jadi pencatat)
        if (Schema::hasTable('catatan_bimbingan') && Schema::hasColumn('catatan_bimbingan', 'pencatat_nama')) {
            $affected += DB::table('catatan_bimbingan')
                ->where('pencatat_nama', $oldName)
                ->update(['pencatat_nama' => $newName]);
        }
        
        // 5. siswa.bk_semester_* columns (nama guru BK text)
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
