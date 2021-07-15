<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSub2kategoriTarifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub2kategori_tarifs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('idkategori', 25);
            $table->string('idsub1', 25);
            $table->string('kodesub2kategori', 25);
            $table->string('namasub2kategori', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub2kategori_tarifs');
    }
}
