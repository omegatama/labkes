<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersediaanTrxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persediaan_trxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->unsignedBigInteger('barang_persediaan_id');
            $table->enum('io', ['i', 'o'])->nullable();
            $table->integer('qty');
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
        Schema::dropIfExists('persediaan_trxes');
    }
}
