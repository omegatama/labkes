<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenagaMedisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenaga_medis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nip', 25);
            $table->string('nama', 100);
            $table->string('alamat', 150);
            $table->string('email', 50);
            $table->string('telpon', 25);
            $table->string('jenis_kelamin', 1);
            $table->string('jabatan', 25);
            $table->string('marital', 20);
            $table->tinyInteger('status');
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
        Schema::dropIfExists('tenaga_medis');
    }
}
