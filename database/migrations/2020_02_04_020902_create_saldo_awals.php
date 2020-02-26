<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaldoAwals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldo_awals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->string('npsn', 15);
            $table->date('periode');
            $table->float('saldo_tunai', 12, 2);
            $table->float('saldo_bank', 12, 2);
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
        Schema::dropIfExists('saldo_awals');
    }
}
