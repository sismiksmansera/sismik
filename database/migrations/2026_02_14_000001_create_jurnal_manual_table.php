<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_manual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guru_bk_id');
            $table->date('tanggal');
            $table->time('waktu')->nullable();
            $table->string('jenis_aktivitas');
            $table->enum('tipe_subyek', ['Siswa', 'Lainnya'])->default('Lainnya');
            $table->string('nisn')->nullable(); // if tipe_subyek = Siswa
            $table->string('subyek_manual')->nullable(); // if tipe_subyek = Lainnya
            $table->text('deskripsi');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('guru_bk_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_manual');
    }
};
