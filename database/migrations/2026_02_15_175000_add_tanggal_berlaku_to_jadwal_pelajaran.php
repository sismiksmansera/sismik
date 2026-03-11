<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_akhir')->nullable()->after('tanggal_mulai');
        });

        // Backfill existing rows with a default start date
        DB::table('jadwal_pelajaran')
            ->whereNull('tanggal_mulai')
            ->update(['tanggal_mulai' => '2024-07-15']);
        
        // Now make tanggal_mulai NOT NULL
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_akhir']);
        });
    }
};
