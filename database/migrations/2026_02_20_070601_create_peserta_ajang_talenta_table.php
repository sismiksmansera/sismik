<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peserta_ajang_talenta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ajang_talenta_id');
            $table->unsignedInteger('siswa_id');
            $table->string('status', 50)->default('Aktif');
            $table->timestamps();

            $table->index('ajang_talenta_id');
            $table->index('siswa_id');
            $table->unique(['ajang_talenta_id', 'siswa_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('peserta_ajang_talenta');
    }
};
