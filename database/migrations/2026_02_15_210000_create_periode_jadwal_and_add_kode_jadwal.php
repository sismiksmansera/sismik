<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create periode_jadwal table
        Schema::create('periode_jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->timestamps();
        });

        // 2. Add kode_jadwal column to jadwal_pelajaran
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->string('kode_jadwal', 50)->nullable()->after('semester');
        });

        // 3. Backfill: create periode_jadwal records from existing tanggal_mulai/akhir combos
        $combos = DB::table('jadwal_pelajaran')
            ->select('tanggal_mulai', 'tanggal_akhir')
            ->distinct()
            ->whereNotNull('tanggal_mulai')
            ->get();

        $counter = 1;
        foreach ($combos as $combo) {
            $kode = 'JDW-' . str_pad($counter, 3, '0', STR_PAD_LEFT);

            DB::table('periode_jadwal')->insert([
                'kode' => $kode,
                'tanggal_mulai' => $combo->tanggal_mulai,
                'tanggal_akhir' => $combo->tanggal_akhir,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update jadwal_pelajaran rows matching this combo
            $query = DB::table('jadwal_pelajaran')
                ->where('tanggal_mulai', $combo->tanggal_mulai);

            if ($combo->tanggal_akhir) {
                $query->where('tanggal_akhir', $combo->tanggal_akhir);
            } else {
                $query->whereNull('tanggal_akhir');
            }

            $query->update(['kode_jadwal' => $kode]);

            $counter++;
        }

        // 4. Drop old columns
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_akhir']);
        });
    }

    public function down(): void
    {
        // Re-add columns
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_akhir')->nullable()->after('tanggal_mulai');
        });

        // Restore data from periode_jadwal
        $periodeList = DB::table('periode_jadwal')->get();
        foreach ($periodeList as $p) {
            DB::table('jadwal_pelajaran')
                ->where('kode_jadwal', $p->kode)
                ->update([
                    'tanggal_mulai' => $p->tanggal_mulai,
                    'tanggal_akhir' => $p->tanggal_akhir,
                ]);
        }

        // Drop kode_jadwal column
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropColumn('kode_jadwal');
        });

        Schema::dropIfExists('periode_jadwal');
    }
};
