<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasTrx extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = 'kas_trxes';

    protected $fillable = ['ta', 'npsn', 'kas', 'io', 'nominal', 'saldo_tunai', 'saldo_bank', 'reference_id'];

    public function kas_trx_detail()
    {
        if (empty($this->io)) {
            # code...
            return $this->hasOne('App\KasTrxDetail', 'reference_id');
        }
    }

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
}
