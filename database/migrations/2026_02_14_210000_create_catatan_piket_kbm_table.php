<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_piket_kbm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('piket_kbm_id');
            $table->date('tanggal');
            $table->integer('jam_ke');
            $table->string('nama_guru');
            $table->string('nama_mapel')->nullable();
            $table->string('nama_rombel')->nullable();
            $table->enum('status_kehadiran', ['Hadir', 'Tidak Hadir', 'Izin', 'Terlambat'])->default('Hadir');
            $table->text('keterangan')->nullable();
            $table->text('penugasan')->nullable();
            $table->string('dicatat_oleh');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_piket_kbm');
    }
};
