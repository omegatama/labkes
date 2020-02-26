<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKasTrxDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kas_trx_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->string('npsn', 15);
            $table->enum('tipe', ['Pindah Buku', 'Setor Kembali', 'Bunga']);
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
        Schema::dropIfExists('kas_trx_details');
    }
}
