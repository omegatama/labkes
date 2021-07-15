<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditKodeKategoriMenjadiIdkategori extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub1kategori_tarifs', function (Blueprint $table) {
            $table->dropColumn('kodekategori');
            $table->string('idkategori',25)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub1kategori_tarifs', function (Blueprint $table) {
            $table->dropColumn('idkategori');
            $table->string('kodekategori',25)->after('id');
        });
    }
}
