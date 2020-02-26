<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKodeRekening extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kode_rekenings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_rekening');
            $table->string('nama_rekening');
            $table->unsignedInteger('parent_id')->nullable();
            $table->tinyInteger('jenis')->default(0);
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
        Schema::dropIfExists('kode_rekenings');
    }
}
