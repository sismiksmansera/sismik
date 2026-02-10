<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi_siswa', function (Blueprint $table) {
            for ($i = 1; $i <= 11; $i++) {
                $table->string("kehadiran_guru_{$i}", 30)->nullable()->after("jam_ke_{$i}");
            }
        });
    }

    public function down(): void
    {
        Schema::table('presensi_siswa', function (Blueprint $table) {
            for ($i = 1; $i <= 11; $i++) {
                $table->dropColumn("kehadiran_guru_{$i}");
            }
        });
    }
};
