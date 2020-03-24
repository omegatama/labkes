<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBelanjaPersediaans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('belanja_persediaans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('belanja_id');
            $table->unsignedBigInteger('barang_persediaan_id');
            $table->integer('qty');
            $table->float('total', 12, 2)->nullable();
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
        Schema::dropIfExists('belanja_persediaans');
    }
}
