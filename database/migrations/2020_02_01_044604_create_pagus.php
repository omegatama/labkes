<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->string('npsn', 15);
            $table->float('pagu', 12, 2);
            $table->float('penggunaan_tw1', 12, 2);
            $table->float('penggunaan_tw2', 12, 2);
            $table->float('penggunaan_tw3', 12, 2);
            $table->float('penggunaan_tw4', 12, 2);
            $table->float('sisa', 12, 2);
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
        Schema::dropIfExists('pagus');
    }
}
