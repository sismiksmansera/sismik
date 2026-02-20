<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('dusun')->nullable()->after('kelurahan');
            $table->string('rt_rw')->nullable()->after('dusun');
            $table->string('mapel_osn_2026')->nullable()->after('rt_rw');
            $table->string('ikut_osn_2025')->nullable()->after('mapel_osn_2026');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['dusun', 'rt_rw', 'mapel_osn_2026', 'ikut_osn_2025']);
        });
    }
};
