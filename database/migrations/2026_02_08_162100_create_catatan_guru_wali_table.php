<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catatan_guru_wali', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->string('guru_nama');
            $table->string('tahun_pelajaran');
            $table->string('semester');
            $table->date('tanggal_pencatatan');
            $table->enum('jenis_bimbingan', [
                'Bimbingan Akademik',
                'Bimbingan Karakter',
                'Sosial Emosional',
                'Kedisiplinan',
                'Potensi dan Minat',
                'Bimbingan Ibadah'
            ]);
            $table->text('catatan');
            $table->enum('nilai_praktik_ibadah', ['A', 'B', 'C'])->nullable();
            $table->enum('perkembangan', [
                'Belum Berkembang',
                'Berkembang Sesuai Harapan',
                'Berkembang Sangat Baik'
            ])->nullable();
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('guru_nama');
            $table->index(['tahun_pelajaran', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_guru_wali');
    }
};
