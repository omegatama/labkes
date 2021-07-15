<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSub1kategoriTarifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub1kategori_tarifs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigIncrements('kodekategori', 25);
            $table->string('kodesub1kategori', 25);
            $table->string('namasub1kategori', 100);
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
        Schema::dropIfExists('sub1kategori_tarifs');
    }
}
