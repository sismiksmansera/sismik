<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_tambahan_guru', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_tugas_id');
            $table->string('tipe_guru'); // 'guru' or 'guru_bk'
            $table->unsignedBigInteger('guru_id');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('jenis_tugas_id')
                ->references('id')
                ->on('jenis_tugas_tambahan_lain')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_tambahan_guru');
    }
};
