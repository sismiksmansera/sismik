<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamu', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_hp');
            $table->enum('datang_sebagai', ['Wali Murid', 'Jurnalis', 'Perguruan Tinggi', 'Tamu Khusus', 'Tamu Umum']);
            $table->string('bertemu_dengan');
            $table->text('keperluan');
            
            // Memberikan dokumen
            $table->boolean('memberikan_dokumen')->default(false);
            $table->enum('jenis_dokumen_diberikan', ['Surat Undangan', 'Proposal', 'Barang/Berkas Lain'])->nullable();
            $table->text('deskripsi_dokumen_diberikan')->nullable();
            
            // Meminta dokumen
            $table->boolean('meminta_dokumen')->default(false);
            $table->enum('jenis_dokumen_diminta', ['Surat Undangan', 'Proposal', 'Barang/Berkas Lain'])->nullable();
            $table->text('deskripsi_dokumen_diminta')->nullable();
            
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('datang_sebagai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamu');
    }
};
