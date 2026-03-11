<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_asesmen_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_asesmen', 100);
            $table->string('semester', 20);
            $table->string('tahun_pelajaran', 20);
            $table->string('nama_rombel', 50);
            $table->string('mata_pelajaran', 100);
            $table->string('nama_siswa', 150);
            $table->string('nisn', 20);
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->index('nisn');
            $table->index(['tahun_pelajaran', 'semester']);
            $table->index('nama_rombel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_asesmen_sekolah');
    }
};
