<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BelanjaModal extends Model
{
    use SoftDeletes;

    protected $dates = ['tanggal_bukti', 'created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['belanja_id', 'nama_barang', 'kode_barang_id', 'warna', 'merek', 'tipe', 'bahan', 'tanggal_bukti', 'nomor_bukti', 'satuan', 'harga_satuan', 'qty', 'total'];

    public function kode_barang()
	{
	    return $this->belongsTo('App\KodeBarang');
	}

}
