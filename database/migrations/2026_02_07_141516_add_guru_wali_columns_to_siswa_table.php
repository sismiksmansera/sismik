<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('guru_wali_sem_1', 100)->nullable()->after('bk_semester_6');
            $table->string('guru_wali_sem_2', 100)->nullable()->after('guru_wali_sem_1');
            $table->string('guru_wali_sem_3', 100)->nullable()->after('guru_wali_sem_2');
            $table->string('guru_wali_sem_4', 100)->nullable()->after('guru_wali_sem_3');
            $table->string('guru_wali_sem_5', 100)->nullable()->after('guru_wali_sem_4');
            $table->string('guru_wali_sem_6', 100)->nullable()->after('guru_wali_sem_5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn([
                'guru_wali_sem_1',
                'guru_wali_sem_2',
                'guru_wali_sem_3',
                'guru_wali_sem_4',
                'guru_wali_sem_5',
                'guru_wali_sem_6',
            ]);
        });
    }
};
