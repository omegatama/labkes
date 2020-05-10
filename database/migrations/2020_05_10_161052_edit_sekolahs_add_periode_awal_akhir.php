<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSekolahsAddPeriodeAwalAkhir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->date('periode_awal')->nullable()->after('kunci_rka');
            $table->date('periode_akhir')->nullable()->after('periode_awal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('periode_awal');
            $table->dropColumn('periode_akhir');
        });
    }
}
