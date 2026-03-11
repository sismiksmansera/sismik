<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ajang_talenta', function (Blueprint $table) {
            $table->string('jenis_ajang', 200)->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('ajang_talenta', function (Blueprint $table) {
            $table->dropColumn('jenis_ajang');
        });
    }
};
