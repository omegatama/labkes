<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRkas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rkas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->string('npsn', 15);
            $table->unsignedBigInteger('kode_program_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('komponen_pembiayaan_id');
            $table->unsignedBigInteger('kode_rekening_id');
            $table->string('uraian');
            $table->integer('volume');
            $table->string('satuan');
            $table->float('harga_satuan', 12, 2);
            $table->float('jumlah', 12, 2);
            $table->float('alokasi_tw1', 12, 2);
            $table->float('alokasi_tw2', 12, 2);
            $table->float('alokasi_tw3', 12, 2);
            $table->float('alokasi_tw4', 12, 2);
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
        Schema::dropIfExists('rkas');
    }
}
