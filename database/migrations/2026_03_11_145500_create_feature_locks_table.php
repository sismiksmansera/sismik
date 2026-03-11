<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_locks', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // guru, siswa
            $table->string('feature_key')->unique();
            $table->string('feature_name');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_locks');
    }
};
