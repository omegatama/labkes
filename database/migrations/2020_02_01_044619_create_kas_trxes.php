<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKasTrxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kas_trxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('ta');
            $table->string('npsn', 15);
            $table->enum('kas', ['B', 'T'])->nullable();
            $table->enum('io', ['i', 'o'])->nullable();
            $table->float('nominal', 12, 2);
            $table->float('saldo_tunai', 12, 2);
            $table->float('saldo_bank', 12, 2);
            $table->unsignedInteger('reference_id');
            $table->date('tanggal');
            $table->timestamps();
            $table->softDeletes();
            // ['ta', 'npsn', 'kas', 'io', 'nominal', 'saldo_tunai', 'saldo_bank', 'reference_id']

            // Kas berisi B, T atau Null
            // B= Bank
            // T= Tunai
            // Null= Kedua nya

            // IO berisi i, o atau Null
            // i= input; Pendapatan
            // o= output; Pengeluaran
            // Null= Kedua nya

            // Reference ID
            // i= Tabel Pendapatan
            // o= Tabel Belanja
            // Null= Tabel KasTrx_details

            ////////////////////////////////////
            // Setor Kembali (-) [-]
            // Setoran tunai ke bank

            // Pemindah Bukuan (-) [-]
            // Tarikan bank ke tunai

            // Bunga (-) [B]
            // Entah ke mana

            // Belanja (o) [B/T]
            // Bisa tunai, bisa bank

            // Pendapatan (i) [B/T]
            // Bisa dana bos, blud, jkn
            ////////////////////////////////////
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kas_trxes');
    }
}
