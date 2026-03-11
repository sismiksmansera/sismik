<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ajang_talenta', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ajang', 200);
            $table->string('tahun', 10)->nullable();
            $table->string('penyelenggara', 200)->nullable();
            $table->string('pembina', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ajang_talenta');
    }
};
