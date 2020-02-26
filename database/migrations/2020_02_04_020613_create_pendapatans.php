<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendapatans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pendapatans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->string('npsn', 15);
            $table->string('sumber')->default('BOS');
            $table->float('nominal', 12, 2);
            $table->string('keterangan')->nullable();
            $table->date('tanggal');
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
        Schema::dropIfExists('pendapatans');
    }
}
