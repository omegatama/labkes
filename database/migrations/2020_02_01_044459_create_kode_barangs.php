<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKodeBarangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kode_barangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('kode_rekenings');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kode_barangs');
    }
}
