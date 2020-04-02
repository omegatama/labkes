<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BelanjaPersediaan extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['belanja_id', 'barang_persediaan_id', 'qty'];

    public function barang_persediaan()
	{
	    return $this->belongsTo('App\BarangPersediaan');
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

    public function scopeBulan($query, $bulan)
    {
        return $query->whereHas('belanja', function ($q) use ($bulan) {
            $q->whereMonth('tanggal', $bulan);   
        });
    }

    public function scopePersediaanId($query, $barang_id)
    {
        // if (!empty($barang_id)) {
            return $query->where('barang_persediaan_id','=', $barang_id);
        // }
    }
}
