<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ManajemenSekolahController;
use App\Http\Controllers\Admin\RombelController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\GuruBKController;
use App\Http\Controllers\Admin\PrestasiController;
use App\Http\Controllers\Admin\JadwalPelajaranController;
use App\Http\Controllers\Admin\PanggilanOrtuController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\GuruBK\DashboardController as GuruBKDashboardController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - SISMIK
|--------------------------------------------------------------------------
*/

// Home redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Tamu (Guest) Routes - Public
Route::get('/tamu', [\App\Http\Controllers\TamuController::class, 'create'])->name('tamu.form');
Route::post('/tamu', [\App\Http\Controllers\TamuController::class, 'store'])->name('tamu.store');
Route::get('/tamu/print/{id}', [\App\Http\Controllers\TamuController::class, 'print'])->name('tamu.print');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('check.admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal/detail', [AdminDashboardController::class, 'detailJadwal'])->name('jadwal.detail');
    
    // Manajemen Sekolah
    Route::get('/manajemen-sekolah', [ManajemenSekolahController::class, 'index'])->name('manajemen-sekolah');
    
    // Periodik CRUD
    Route::post('/periodik', [ManajemenSekolahController::class, 'storePeriodik'])->name('periodik.store');
    Route::put('/periodik/{id}', [ManajemenSekolahController::class, 'updatePeriodik'])->name('periodik.update');
    Route::delete('/periodik/{id}', [ManajemenSekolahController::class, 'destroyPeriodik'])->name('periodik.destroy');
    Route::post('/periodik/toggle-aktif', [ManajemenSekolahController::class, 'toggleAktif'])->name('periodik.toggle');
    
    // Admin CRUD
    Route::post('/admin-sekolah', [ManajemenSekolahController::class, 'storeAdmin'])->name('admin-sekolah.store');
    Route::put('/admin-sekolah/{id}', [ManajemenSekolahController::class, 'updateAdmin'])->name('admin-sekolah.update');
    
    // Raport & Jam Pelajaran Settings (AJAX)
    Route::post('/raport-settings', [ManajemenSekolahController::class, 'saveRaportSettings'])->name('raport-settings.save');
    Route::get('/jam-pelajaran', [ManajemenSekolahController::class, 'getJamPelajaran'])->name('jam-pelajaran.get');
    Route::post('/jam-pelajaran', [ManajemenSekolahController::class, 'saveJamPelajaran'])->name('jam-pelajaran.save');
    
    // Login Settings
    Route::post('/login-settings/background', [ManajemenSekolahController::class, 'uploadBackground'])->name('login.upload-bg');
    Route::delete('/login-settings/background', [ManajemenSekolahController::class, 'deleteBackground'])->name('login.delete-bg');
    Route::post('/login-settings/logo', [ManajemenSekolahController::class, 'uploadLogo'])->name('login.upload-logo');
    Route::delete('/login-settings/logo', [ManajemenSekolahController::class, 'deleteLogo'])->name('login.delete-logo');
    Route::post('/login-settings/overlay', [ManajemenSekolahController::class, 'saveOverlaySettings'])->name('login.save-overlay');
    Route::get('/testing-date', [ManajemenSekolahController::class, 'getTestingDate'])->name('testing-date.get');
    Route::post('/testing-date', [ManajemenSekolahController::class, 'saveTestingDate'])->name('testing-date.save');
    
    // Rombel (Rombongan Belajar) Management
    Route::get('/rombel', [RombelController::class, 'index'])->name('rombel.index');
    Route::get('/rombel/create', [RombelController::class, 'create'])->name('rombel.create');
    Route::post('/rombel', [RombelController::class, 'store'])->name('rombel.store');
    Route::get('/rombel/{id}/edit', [RombelController::class, 'edit'])->name('rombel.edit');
    Route::put('/rombel/{id}', [RombelController::class, 'update'])->name('rombel.update');
    Route::delete('/rombel/{id}', [RombelController::class, 'destroy'])->name('rombel.destroy');
    Route::post('/rombel/copy', [RombelController::class, 'copyRombel'])->name('rombel.copy');
    Route::get('/rombel/{id}/members', [RombelController::class, 'members'])->name('rombel.members');
    
    // Mata Pelajaran & Jadwal Management
    Route::get('/rombel/{id}/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mapel.index');
    Route::get('/mapel/jadwal', [MataPelajaranController::class, 'getJadwal'])->name('mapel.jadwal.get');
    Route::post('/mapel/jadwal', [MataPelajaranController::class, 'saveJadwal'])->name('mapel.jadwal.save');
    Route::get('/mapel/agama-islam-id', [MataPelajaranController::class, 'getAgamaIslamId'])->name('mapel.agama-islam-id');
    
    // Jadwal Pelajaran (Jadwal Harian)
    Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran.index');
    
    // Prestasi Management
    Route::get('/prestasi/lihat', [PrestasiController::class, 'lihat'])->name('prestasi.lihat');
    
    // Siswa Management
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::post('/siswa/{id}/photo', [SiswaController::class, 'uploadPhoto'])->name('siswa.photo');
    
    // Siswa AJAX endpoints
    Route::post('/siswa/delete-multiple', [SiswaController::class, 'deleteMultiple'])->name('siswa.delete-multiple');
    Route::post('/siswa/update-angkatan', [SiswaController::class, 'updateAngkatan'])->name('siswa.update-angkatan');
    Route::post('/siswa/update-rombel-semester', [SiswaController::class, 'updateRombelSemester'])->name('siswa.update-rombel');
    Route::post('/siswa/update-bk-semester', [SiswaController::class, 'updateBKSemester'])->name('siswa.update-bk');
    Route::post('/siswa/update-wali-semester', [SiswaController::class, 'updateWaliSemester'])->name('siswa.update-wali');
    Route::post('/siswa/get-rombel', [SiswaController::class, 'getRombelBySemester'])->name('siswa.get-rombel');
    
    // Siswa Import
    Route::get('/siswa/import', [SiswaController::class, 'showImport'])->name('siswa.import');
    Route::post('/siswa/import', [SiswaController::class, 'processImport'])->name('siswa.import.process');
    Route::get('/siswa/import/periodic-template', [SiswaController::class, 'downloadPeriodicTemplate'])->name('siswa.import.periodic-template');
    Route::post('/siswa/import/periodic', [SiswaController::class, 'importPeriodicData'])->name('siswa.import.periodic');
    
    // Login as Siswa (Impersonate)
    Route::get('/impersonate/siswa/{nisn}', [SiswaController::class, 'impersonate'])->name('siswa.impersonate');
    
    // Guru Management
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/create', [GuruController::class, 'create'])->name('guru.create');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::get('/guru/{id}/edit', [GuruController::class, 'edit'])->name('guru.edit');
    Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy');
    Route::post('/guru/{id}/reset-password', [GuruController::class, 'resetPassword'])->name('guru.reset-password');
    Route::get('/impersonate/guru/{id}', [GuruController::class, 'impersonate'])->name('guru.impersonate');
    Route::post('/guru/delete-ajax', [GuruController::class, 'deleteAjax'])->name('guru.delete-ajax');
    Route::get('/guru/{id}/tugas-mengajar', [GuruController::class, 'tugasMengajar'])->name('guru.tugas-mengajar');
    Route::get('/guru/{id}/aktivitas', [GuruController::class, 'aktivitas'])->name('guru.aktivitas');
    Route::get('/guru/{id}/penugasan/check-jadwal', [GuruController::class, 'checkJadwalKonflik'])->name('guru.penugasan.check-jadwal');
    Route::post('/guru/{id}/penugasan', [GuruController::class, 'savePenugasan'])->name('guru.penugasan.save');
    Route::post('/guru/{id}/penugasan/delete', [GuruController::class, 'deletePenugasan'])->name('guru.penugasan.delete');
    Route::get('/guru/import', [GuruController::class, 'showImport'])->name('guru.import.show');
    Route::get('/guru/export', [GuruController::class, 'downloadGuruData'])->name('guru.export');
    Route::post('/guru/import', [GuruController::class, 'importGuruData'])->name('guru.import');
    
    // Guru BK Management
    Route::get('/guru-bk', [GuruBKController::class, 'index'])->name('guru-bk.index');
    Route::get('/guru-bk/create', [GuruBKController::class, 'create'])->name('guru-bk.create');
    Route::post('/guru-bk', [GuruBKController::class, 'store'])->name('guru-bk.store');
    Route::get('/guru-bk/{id}/edit', [GuruBKController::class, 'edit'])->name('guru-bk.edit');
    Route::put('/guru-bk/{id}', [GuruBKController::class, 'update'])->name('guru-bk.update');
    Route::delete('/guru-bk/{id}', [GuruBKController::class, 'destroy'])->name('guru-bk.destroy');
    Route::post('/guru-bk/{id}/reset-password', [GuruBKController::class, 'resetPassword'])->name('guru-bk.reset-password');
    Route::get('/impersonate/guru-bk/{id}', [GuruBKController::class, 'impersonate'])->name('guru-bk.impersonate');
    Route::get('/guru-bk/{id}/siswa-bimbingan', [GuruBKController::class, 'siswaBimbingan'])->name('guru-bk.siswa-bimbingan');
    Route::get('/guru-bk/{id}/aktivitas', [GuruBKController::class, 'aktivitas'])->name('guru-bk.aktivitas');
    
    // Panggilan Ortu Management
    Route::get('/panggilan-ortu', [PanggilanOrtuController::class, 'index'])->name('panggilan-ortu.index');
    Route::get('/panggilan-ortu/create', [PanggilanOrtuController::class, 'create'])->name('panggilan-ortu.create');
    Route::post('/panggilan-ortu', [PanggilanOrtuController::class, 'store'])->name('panggilan-ortu.store');
    Route::get('/panggilan-ortu/{id}/edit', [PanggilanOrtuController::class, 'edit'])->name('panggilan-ortu.edit');
    Route::put('/panggilan-ortu/{id}', [PanggilanOrtuController::class, 'update'])->name('panggilan-ortu.update');
    Route::get('/panggilan-ortu/{id}/print', [PanggilanOrtuController::class, 'print'])->name('panggilan-ortu.print');
    Route::delete('/panggilan-ortu/{id}', [PanggilanOrtuController::class, 'destroy'])->name('panggilan-ortu.destroy');
    
    // Tamu (Guest) Management
    Route::get('/tamu', [\App\Http\Controllers\Admin\TamuController::class, 'index'])->name('tamu.index');
    Route::delete('/tamu/{id}', [\App\Http\Controllers\Admin\TamuController::class, 'destroy'])->name('tamu.destroy');
    
    // Ekstrakurikuler Management
    Route::get('/ekstrakurikuler', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'index'])->name('ekstrakurikuler.index');
    Route::get('/ekstrakurikuler/create', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'create'])->name('ekstrakurikuler.create');
    Route::post('/ekstrakurikuler', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'store'])->name('ekstrakurikuler.store');
    Route::get('/ekstrakurikuler/{id}/edit', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'edit'])->name('ekstrakurikuler.edit');
    Route::put('/ekstrakurikuler/{id}', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'update'])->name('ekstrakurikuler.update');
    Route::delete('/ekstrakurikuler/{id}', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'destroy'])->name('ekstrakurikuler.destroy');
    Route::post('/ekstrakurikuler/copy', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'copy'])->name('ekstrakurikuler.copy');
    Route::get('/ekstrakurikuler/preview-copy', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'previewCopy'])->name('ekstrakurikuler.preview-copy');
    Route::post('/ekstrakurikuler/get-siswa', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'getSiswa'])->name('ekstrakurikuler.get-siswa');
    Route::get('/ekstrakurikuler/get-siswa-by-ids', [\App\Http\Controllers\Admin\EkstrakurikulerController::class, 'getSiswaByIds'])->name('ekstrakurikuler.get-siswa-by-ids');

    
    // Keamanan (Security) Management
    Route::get('/keamanan', [\App\Http\Controllers\Admin\KeamananController::class, 'index'])->name('keamanan.index');
    Route::post('/keamanan/unlock', [\App\Http\Controllers\Admin\KeamananController::class, 'unlock'])->name('keamanan.unlock');
    Route::post('/keamanan/clear-all', [\App\Http\Controllers\Admin\KeamananController::class, 'clearAll'])->name('keamanan.clear-all');
    
    // Raport (Print Raport)
    Route::get('/raport/print', [\App\Http\Controllers\Admin\RaportController::class, 'print'])->name('raport.print');
    Route::get('/raport/print-all', [\App\Http\Controllers\Admin\RaportController::class, 'printAll'])->name('raport.print-all');
    
    // Riwayat Akademik
    Route::get('/riwayat-akademik', [\App\Http\Controllers\Admin\RiwayatAkademikController::class, 'show'])->name('riwayat-akademik');
    Route::get('/riwayat-akademik/print', [\App\Http\Controllers\Admin\RiwayatAkademikController::class, 'print'])->name('riwayat-akademik.print');
    Route::get('/riwayat-akademik/print-all', [\App\Http\Controllers\Admin\RiwayatAkademikController::class, 'printAll'])->name('riwayat-akademik.print-all');
    
    // Leger (Cetak Leger Nilai & Katrol)
    Route::get('/leger/print-nilai', [\App\Http\Controllers\Admin\LegerController::class, 'printNilai'])->name('leger.print-nilai');
    Route::get('/leger/print-katrol', [\App\Http\Controllers\Admin\LegerController::class, 'printKatrol'])->name('leger.print-katrol');
    
    // Pengaduan Management
    Route::get('/pengaduan', [\App\Http\Controllers\Admin\PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::post('/pengaduan/detail', [\App\Http\Controllers\Admin\PengaduanController::class, 'getDetail'])->name('pengaduan.detail');
    Route::post('/pengaduan/update', [\App\Http\Controllers\Admin\PengaduanController::class, 'update'])->name('pengaduan.update');
    Route::post('/pengaduan/teruskan', [\App\Http\Controllers\Admin\PengaduanController::class, 'teruskan'])->name('pengaduan.teruskan');
    Route::post('/pengaduan/destroy', [\App\Http\Controllers\Admin\PengaduanController::class, 'destroy'])->name('pengaduan.destroy');
});


// Guru Routes
Route::prefix('guru')->name('guru.')->middleware('check.guru')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stop-impersonate', [GuruDashboardController::class, 'stopImpersonate'])->name('stop-impersonate');
    
    // Profile routes
    Route::get('/profil', [\App\Http\Controllers\Guru\ProfileController::class, 'index'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\Guru\ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [\App\Http\Controllers\Guru\ProfileController::class, 'uploadPhoto'])->name('profil.upload-foto');
    
    // Presensi routes
    Route::get('/presensi', [\App\Http\Controllers\Guru\PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [\App\Http\Controllers\Guru\PresensiController::class, 'store'])->name('presensi.store');
    
    // Penilaian routes
    Route::get('/penilaian', [\App\Http\Controllers\Guru\PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian', [\App\Http\Controllers\Guru\PenilaianController::class, 'store'])->name('penilaian.store');
    
    // Input Penilaian via Sidebar routes
    Route::get('/input-penilaian', [\App\Http\Controllers\Guru\InputPenilaianController::class, 'index'])->name('input-penilaian');
    Route::get('/input-penilaian/mapel-options', [\App\Http\Controllers\Guru\InputPenilaianController::class, 'getMapelOptions'])->name('input-penilaian.mapel');
    Route::get('/input-penilaian/rombel-options', [\App\Http\Controllers\Guru\InputPenilaianController::class, 'getRombelOptions'])->name('input-penilaian.rombel');
    Route::get('/input-penilaian/siswa-list', [\App\Http\Controllers\Guru\InputPenilaianController::class, 'getSiswaList'])->name('input-penilaian.siswa');
    Route::post('/input-penilaian', [\App\Http\Controllers\Guru\InputPenilaianController::class, 'store'])->name('input-penilaian.store');
    
    // Izin Guru routes
    Route::get('/izin-guru', [\App\Http\Controllers\Guru\IzinGuruController::class, 'index'])->name('izin-guru.index');
    Route::post('/izin-guru', [\App\Http\Controllers\Guru\IzinGuruController::class, 'store'])->name('izin-guru.store');
    
    // Jadwal Pelajaran route
    Route::get('/jadwal', [\App\Http\Controllers\Guru\JadwalController::class, 'index'])->name('jadwal');
    
    // Tugas Mengajar route
    Route::get('/tugas-mengajar', [\App\Http\Controllers\Guru\TugasMengajarController::class, 'index'])->name('tugas-mengajar');
    
    // Rekap Presensi route
    Route::get('/rekap-presensi', [\App\Http\Controllers\Guru\RekapPresensiController::class, 'index'])->name('rekap-presensi');
    
    // Presensi Siswa via Sidebar routes (selector)
    Route::get('/presensi-selector', [\App\Http\Controllers\Guru\RekapPresensiController::class, 'selector'])->name('presensi-selector');
    Route::get('/presensi-selector/rekap-data', [\App\Http\Controllers\Guru\RekapPresensiController::class, 'getRekapData'])->name('presensi-selector.rekap-data');
    
    // Lihat Nilai route
    Route::get('/lihat-nilai', [\App\Http\Controllers\Guru\LihatNilaiController::class, 'index'])->name('lihat-nilai');
    
    // Lihat Nilai via Sidebar routes (selector)
    Route::get('/lihat-nilai-selector', [\App\Http\Controllers\Guru\LihatNilaiController::class, 'selector'])->name('lihat-nilai-selector');
    Route::get('/lihat-nilai-selector/nilai-data', [\App\Http\Controllers\Guru\LihatNilaiController::class, 'getNilaiData'])->name('lihat-nilai-selector.nilai-data');
    
    // Tambah Nilai Siswa routes
    Route::get('/tambah-nilai', [\App\Http\Controllers\Guru\TambahNilaiController::class, 'index'])->name('tambah-nilai');
    Route::post('/tambah-nilai', [\App\Http\Controllers\Guru\TambahNilaiController::class, 'store'])->name('tambah-nilai.store');
    
    // Edit Nilai Siswa routes
    Route::get('/edit-nilai-siswa', [\App\Http\Controllers\Guru\EditNilaiSiswaController::class, 'index'])->name('edit-nilai-siswa');
    Route::post('/edit-nilai-siswa', [\App\Http\Controllers\Guru\EditNilaiSiswaController::class, 'update'])->name('edit-nilai-siswa.update');
    
    // Tugas Tambahan route
    Route::get('/tugas-tambahan', [\App\Http\Controllers\Guru\TugasTambahanController::class, 'index'])->name('tugas-tambahan');
    
    // Siswa Bimbingan (Guru Wali) route
    Route::get('/siswa-bimbingan', [\App\Http\Controllers\Guru\SiswaBimbinganController::class, 'index'])->name('siswa-bimbingan');
    
    // Catatan Guru Wali routes
    Route::get('/catatan-guru-wali/{siswa_id}', [\App\Http\Controllers\Guru\CatatanGuruWaliController::class, 'index'])->name('catatan-guru-wali.index');
    Route::get('/catatan-guru-wali/{siswa_id}/create', [\App\Http\Controllers\Guru\CatatanGuruWaliController::class, 'create'])->name('catatan-guru-wali.create');
    Route::post('/catatan-guru-wali/{siswa_id}', [\App\Http\Controllers\Guru\CatatanGuruWaliController::class, 'store'])->name('catatan-guru-wali.store');
    Route::get('/catatan-guru-wali/edit/{id}', [\App\Http\Controllers\Guru\CatatanGuruWaliController::class, 'edit'])->name('catatan-guru-wali.edit');
    Route::put('/catatan-guru-wali/{id}', [\App\Http\Controllers\Guru\CatatanGuruWaliController::class, 'update'])->name('catatan-guru-wali.update');
    Route::delete('/catatan-guru-wali/{id}', [\App\Http\Controllers\Guru\CatatanGuruWaliController::class, 'destroy'])->name('catatan-guru-wali.destroy');
    
    // Anggota Ekstrakurikuler routes
    Route::get('/anggota-ekstrakurikuler', [\App\Http\Controllers\Guru\AnggotaEkstrakurikulerController::class, 'index'])->name('anggota-ekstrakurikuler');
    Route::post('/anggota-ekstrakurikuler/tambah', [\App\Http\Controllers\Guru\AnggotaEkstrakurikulerController::class, 'tambahAnggota'])->name('anggota-ekstrakurikuler.tambah');
    Route::post('/anggota-ekstrakurikuler/hapus', [\App\Http\Controllers\Guru\AnggotaEkstrakurikulerController::class, 'hapusAnggota'])->name('anggota-ekstrakurikuler.hapus');
    Route::post('/anggota-ekstrakurikuler/update-nilai', [\App\Http\Controllers\Guru\AnggotaEkstrakurikulerController::class, 'updateNilai'])->name('anggota-ekstrakurikuler.update-nilai');
    Route::get('/anggota-ekstrakurikuler/cari-siswa', [\App\Http\Controllers\Guru\AnggotaEkstrakurikulerController::class, 'cariSiswa'])->name('anggota-ekstrakurikuler.cari-siswa');
    
    // Lihat Prestasi routes
    Route::get('/lihat-prestasi', [\App\Http\Controllers\Guru\LihatPrestasiController::class, 'index'])->name('lihat-prestasi');
    Route::post('/lihat-prestasi/hapus', [\App\Http\Controllers\Guru\LihatPrestasiController::class, 'hapus'])->name('lihat-prestasi.hapus');
    
    // Anggota Rombel route
    Route::get('/anggota-rombel', [\App\Http\Controllers\Guru\AnggotaRombelController::class, 'index'])->name('anggota-rombel');
    
    // Catatan Wali Kelas routes
    Route::get('/catatan-wali-kelas', [\App\Http\Controllers\Guru\CatatanWaliKelasController::class, 'index'])->name('catatan-wali-kelas');
    Route::post('/catatan-wali-kelas/simpan', [\App\Http\Controllers\Guru\CatatanWaliKelasController::class, 'simpan'])->name('catatan-wali-kelas.simpan');
    Route::post('/catatan-wali-kelas/hapus', [\App\Http\Controllers\Guru\CatatanWaliKelasController::class, 'hapus'])->name('catatan-wali-kelas.hapus');
    
    // Print Raport route
    Route::get('/raport/print', [\App\Http\Controllers\Admin\RaportController::class, 'print'])->name('raport.print');
    Route::get('/raport/print-all', [\App\Http\Controllers\Admin\RaportController::class, 'printAll'])->name('raport.print-all');
    
    // Riwayat Akademik route
    Route::get('/riwayat-akademik', [\App\Http\Controllers\Admin\RiwayatAkademikController::class, 'show'])->name('riwayat-akademik');
    Route::get('/riwayat-akademik/print', [\App\Http\Controllers\Admin\RiwayatAkademikController::class, 'print'])->name('riwayat-akademik.print');
    Route::get('/riwayat-akademik/print-all', [\App\Http\Controllers\Admin\RiwayatAkademikController::class, 'printAll'])->name('riwayat-akademik.print-all');
    
    // Leger routes
    Route::get('/leger/print-nilai', [\App\Http\Controllers\Admin\LegerController::class, 'printNilai'])->name('leger.print-nilai');
    Route::get('/leger/print-katrol', [\App\Http\Controllers\Admin\LegerController::class, 'printKatrol'])->name('leger.print-katrol');
    
    // Pengaduan routes
    Route::get('/pengaduan', [\App\Http\Controllers\Guru\PengaduanController::class, 'index'])->name('pengaduan');
    Route::post('/pengaduan/detail', [\App\Http\Controllers\Guru\PengaduanController::class, 'getDetail'])->name('pengaduan.detail');
    Route::post('/pengaduan/update', [\App\Http\Controllers\Guru\PengaduanController::class, 'update'])->name('pengaduan.update');
});



// Guru BK Routes
Route::prefix('guru-bk')->name('guru_bk.')->middleware('check.guru_bk')->group(function () {
    Route::get('/dashboard', [GuruBKDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stop-impersonate', [GuruBKDashboardController::class, 'stopImpersonate'])->name('stop-impersonate');
    Route::post('/presensi-detail', [GuruBKDashboardController::class, 'getPresensiDetail'])->name('presensi-detail');
    
    // Profile routes
    Route::get('/profil', [\App\Http\Controllers\GuruBK\ProfileController::class, 'index'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\GuruBK\ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [\App\Http\Controllers\GuruBK\ProfileController::class, 'uploadPhoto'])->name('profil.upload-foto');
    
    // Semua Catatan routes
    Route::get('/semua-catatan', [\App\Http\Controllers\GuruBK\SemuaCatatanController::class, 'index'])->name('semua-catatan');
    Route::post('/semua-catatan', [\App\Http\Controllers\GuruBK\SemuaCatatanController::class, 'index'])->name('semua-catatan.filter');
    Route::post('/semua-catatan/search-students', [\App\Http\Controllers\GuruBK\SemuaCatatanController::class, 'searchStudents'])->name('semua-catatan.search-students');
    
    // Siswa Bimbingan routes
    Route::get('/siswa-bimbingan', [\App\Http\Controllers\GuruBK\SiswaBimbinganController::class, 'index'])->name('siswa-bimbingan');
    Route::post('/siswa-bimbingan', [\App\Http\Controllers\GuruBK\SiswaBimbinganController::class, 'index'])->name('siswa-bimbingan.filter');
    
    // Rekap Status Bimbingan routes
    Route::get('/rekap-status/{status}', [\App\Http\Controllers\GuruBK\RekapStatusBimbinganController::class, 'index'])->name('rekap-status');
    Route::delete('/rekap-catatan/{id}', [\App\Http\Controllers\GuruBK\RekapStatusBimbinganController::class, 'destroy'])->name('rekap-catatan.destroy');
    
    // Catatan Bimbingan routes
    Route::get('/catatan-bimbingan/{nisn}', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'index'])->name('catatan-bimbingan');
    Route::post('/catatan-bimbingan/delete', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'delete'])->name('catatan-bimbingan.delete');
    Route::get('/catatan-bimbingan/edit/{id}', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'edit'])->name('catatan-bimbingan.edit');
    Route::put('/catatan-bimbingan/update/{id}', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'update'])->name('catatan-bimbingan.update');
    Route::get('/catatan-bimbingan/create/{nisn}', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'create'])->name('catatan-bimbingan.create');
    Route::post('/catatan-bimbingan/store/{nisn}', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'store'])->name('catatan-bimbingan.store');
    Route::get('/catatan-bimbingan/print/{id}', [\App\Http\Controllers\GuruBK\CatatanBimbinganController::class, 'print'])->name('catatan-bimbingan.print');
    
    // Panggilan Ortu routes
    Route::get('/panggilan-ortu/{nisn}', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'index'])->name('panggilan-ortu');
    Route::get('/panggilan-ortu/create/{nisn}', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'create'])->name('panggilan-ortu.create');
    Route::post('/panggilan-ortu/store/{nisn}', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'store'])->name('panggilan-ortu.store');
    Route::get('/panggilan-ortu/edit/{id}', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'edit'])->name('panggilan-ortu.edit');
    Route::put('/panggilan-ortu/update/{id}', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'update'])->name('panggilan-ortu.update');
    Route::post('/panggilan-ortu/delete', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'delete'])->name('panggilan-ortu.delete');
    Route::get('/panggilan-ortu/print/{id}', [\App\Http\Controllers\GuruBK\PanggilanOrtuController::class, 'print'])->name('panggilan-ortu.print');
    
    // Tugas Tambahan route
    Route::get('/tugas-tambahan', [\App\Http\Controllers\GuruBK\TugasTambahanController::class, 'index'])->name('tugas-tambahan');
    
    // Siswa Wali (guru wali) route
    Route::get('/siswa-wali', [\App\Http\Controllers\GuruBK\SiswaWaliController::class, 'index'])->name('siswa-wali');
    
    // Catatan Guru Wali routes
    Route::get('/catatan-guru-wali/{siswa_id}', [\App\Http\Controllers\GuruBK\CatatanGuruWaliController::class, 'index'])->name('catatan-guru-wali.index');
    Route::get('/catatan-guru-wali/{siswa_id}/create', [\App\Http\Controllers\GuruBK\CatatanGuruWaliController::class, 'create'])->name('catatan-guru-wali.create');
    Route::post('/catatan-guru-wali/{siswa_id}', [\App\Http\Controllers\GuruBK\CatatanGuruWaliController::class, 'store'])->name('catatan-guru-wali.store');
    Route::get('/catatan-guru-wali/edit/{id}', [\App\Http\Controllers\GuruBK\CatatanGuruWaliController::class, 'edit'])->name('catatan-guru-wali.edit');
    Route::put('/catatan-guru-wali/{id}', [\App\Http\Controllers\GuruBK\CatatanGuruWaliController::class, 'update'])->name('catatan-guru-wali.update');
    Route::delete('/catatan-guru-wali/{id}', [\App\Http\Controllers\GuruBK\CatatanGuruWaliController::class, 'destroy'])->name('catatan-guru-wali.destroy');
    
    // Anggota Ekstrakurikuler routes
    Route::get('/anggota-ekstrakurikuler/{id}', [\App\Http\Controllers\GuruBK\AnggotaEkstrakurikulerController::class, 'index'])->name('anggota-ekstra');
    Route::post('/anggota-ekstrakurikuler/{id}/tambah', [\App\Http\Controllers\GuruBK\AnggotaEkstrakurikulerController::class, 'tambahAnggota'])->name('anggota-ekstra.tambah');
    Route::post('/anggota-ekstrakurikuler/{id}/hapus', [\App\Http\Controllers\GuruBK\AnggotaEkstrakurikulerController::class, 'hapusAnggota'])->name('anggota-ekstra.hapus');
    Route::post('/anggota-ekstrakurikuler/{id}/update-nilai', [\App\Http\Controllers\GuruBK\AnggotaEkstrakurikulerController::class, 'updateNilai'])->name('anggota-ekstra.update-nilai');
    
    // Prestasi routes
    Route::get('/prestasi', [\App\Http\Controllers\GuruBK\PrestasiController::class, 'index'])->name('prestasi');
    
    // Pengaduan routes
    Route::get('/pengaduan', [\App\Http\Controllers\GuruBK\PengaduanController::class, 'index'])->name('pengaduan');
    Route::post('/pengaduan/update', [\App\Http\Controllers\GuruBK\PengaduanController::class, 'update'])->name('pengaduan.update');
    Route::post('/pengaduan/mark-read', [\App\Http\Controllers\GuruBK\PengaduanController::class, 'markRead'])->name('pengaduan.mark-read');
});

// Siswa Routes
Route::prefix('siswa')->name('siswa.')->middleware('check.siswa')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stop-impersonate', [SiswaDashboardController::class, 'stopImpersonate'])->name('stop-impersonate');
    
    // Profile routes
    Route::get('/profil', [\App\Http\Controllers\Siswa\ProfileController::class, 'index'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\Siswa\ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [\App\Http\Controllers\Siswa\ProfileController::class, 'uploadPhoto'])->name('profil.upload-foto');
    
    // Fase 1: Jadwal, Presensi, Nilai
    Route::get('/jadwal', [\App\Http\Controllers\Siswa\JadwalController::class, 'index'])->name('jadwal');
    Route::get('/presensi', [\App\Http\Controllers\Siswa\PresensiController::class, 'index'])->name('presensi');
    Route::get('/presensi/detail', [\App\Http\Controllers\Siswa\PresensiController::class, 'detail'])->name('presensi.detail');
    Route::get('/nilai', [\App\Http\Controllers\Siswa\NilaiController::class, 'index'])->name('nilai');
    
    // Fase 2: Catatan BK, Pengaduan
    Route::get('/catatan-bk', [\App\Http\Controllers\Siswa\CatatanBkController::class, 'index'])->name('catatan-bk');
    Route::get('/pengaduan', [\App\Http\Controllers\Siswa\PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/create', [\App\Http\Controllers\Siswa\PengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan', [\App\Http\Controllers\Siswa\PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::delete('/pengaduan/{id}', [\App\Http\Controllers\Siswa\PengaduanController::class, 'destroy'])->name('pengaduan.destroy');
    
    // Fase 3: Ekskul, Prestasi, Mapel
    Route::get('/ekstrakurikuler', [\App\Http\Controllers\Siswa\EkstrakurikulerController::class, 'index'])->name('ekstrakurikuler');
    Route::get('/prestasi', [\App\Http\Controllers\Siswa\PrestasiController::class, 'index'])->name('prestasi');
    Route::get('/mapel', [\App\Http\Controllers\Siswa\MapelController::class, 'index'])->name('mapel');
    
    // Riwayat Akademik
    Route::get('/riwayat-akademik', [\App\Http\Controllers\Siswa\RiwayatAkademikController::class, 'index'])->name('riwayat-akademik');
    Route::get('/riwayat-akademik/print', [\App\Http\Controllers\Siswa\RiwayatAkademikController::class, 'print'])->name('riwayat-akademik.print');
});
