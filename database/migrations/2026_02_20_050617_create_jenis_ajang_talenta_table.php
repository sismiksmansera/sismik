<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jenis_ajang_talenta', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 200);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jenis_ajang_talenta');
    }
};
