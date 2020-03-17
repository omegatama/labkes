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

	public function belanja()
	{
	    return $this->belongsTo('App\Belanja');
	}

	public function scopeNpsn($query, $npsn)
    {
        return $query->whereHas('belanja', function ($qbelanja) use ($npsn) {
            $qbelanja->whereHas('sekolah', function ($q) use ($npsn) {
                $q->where('npsn','=', $npsn);   
            });
        });
    }

    public function scopeTa($query, $ta)
    {
        return $query->whereHas('belanja', function ($qbelanja) use ($ta) {
            $qbelanja->whereHas('rka', function ($q) use ($ta) {
                $q->where('ta','=', $ta);   
            });
        });
    }

    public function scopeTriwulan($query, $tw)
    {
        return $query->whereHas('belanja', function ($q) use ($tw) {
            $q->where('triwulan','=', $tw);   
        });
    }

}
