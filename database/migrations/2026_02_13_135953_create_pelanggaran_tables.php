<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main pelanggaran table (one record = one violation session)
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('waktu')->nullable();
            $table->string('jenis_pelanggaran');
            $table->string('jenis_lainnya')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('sanksi')->nullable();
            $table->integer('guru_bk_id');
            $table->timestamps();

            $table->index('guru_bk_id');
            $table->index('tanggal');
        });

        // Pivot table for multiple students per pelanggaran
        Schema::create('pelanggaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelanggaran_id');
            $table->integer('siswa_id');

            $table->foreign('pelanggaran_id')->references('id')->on('pelanggaran')->onDelete('cascade');
            $table->index('siswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswa');
        Schema::dropIfExists('pelanggaran');
    }
};
