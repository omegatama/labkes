<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBelanjaModals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('belanja_modals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('belanja_id');
            $table->string('nama_barang');
            $table->unsignedBigInteger('kode_barang_id');
            $table->string('warna')->nullable();
            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->string('bahan')->nullable();
            $table->date('tanggal_bukti');
            $table->string('nomor_bukti');
            $table->string('satuan')->nullable();
            $table->float('harga_satuan', 12, 2);
            $table->integer('qty');
            $table->float('total', 12, 2);
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
        Schema::dropIfExists('belanja_modals');
    }
}
