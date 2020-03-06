<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBelanjas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('belanjas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('triwulan', ['1', '2', '3', '4']);
            $table->string('npsn', 15);
            $table->unsignedBigInteger('rka_id');
            $table->string('nama');
            $table->float('nilai', 12, 2);
            $table->enum('kas', ['B', 'T'])->nullable();
            $table->date('tanggal');
            $table->integer('nomor')->nullable();
            $table->string('penerima');
            $table->float('ppn', 12, 2)->nullable();
            $table->float('pph21', 12, 2)->nullable();
            $table->float('pph23', 12, 2)->nullable();
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
        Schema::dropIfExists('belanjas');
    }
}
