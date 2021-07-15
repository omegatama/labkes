<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteIdkategoriFromSub2kategoriTarifs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub2kategori_tarifs', function (Blueprint $table) {
            $table->dropColumn('idkategori');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub2kategori_tarifs', function (Blueprint $table) {
            $table->string('idkategori',25)->after('id');
        });
    }
}
