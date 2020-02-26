<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRkaLimits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rka_limits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->enum('triwulan', ['1', '2', '3', '4']);
            $table->string('npsn', 15);
            $table->unsignedBigInteger('kode_rekening_id');
            $table->float('limit', 12, 2);
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
        Schema::dropIfExists('rka_limits');
    }
}
