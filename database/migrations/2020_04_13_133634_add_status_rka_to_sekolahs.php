<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusRkaToSekolahs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->tinyInteger('kunci_rka')->nullable()->after('nip_bendahara');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('kunci_rka');
        });
    }
}
