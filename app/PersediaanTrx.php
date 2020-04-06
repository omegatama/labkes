<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersediaanTrx extends Model
{
    use SoftDeletes;

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['tanggal','created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'ta', 'barang_persediaan_id', 'io', 'qty', 'keterangan', 'tanggal'
    ];

    public function persediaan()
    {
        return $this->belongsTo('App\BarangPersediaan','barang_persediaan_id');
    }

    public function scopeNpsn($query, $npsn)
    {
        return $query->whereHas('persediaan', function ($qpersediaan) use ($npsn) {
            $qpersediaan->whereHas('sekolah', function ($q) use ($npsn) {
                $q->where('npsn','=', $npsn);   
            });
        });
    }

    public function scopeTa($query, $ta)
    {
        // if (!empty($ta)) {
            return $query->where('ta','=', $ta);
        // }
    }

    public function scopePersediaanId($query, $barang_id)
    {
        // if (!empty($barang_id)) {
            return $query->where('barang_persediaan_id','=', $barang_id);
        // }
    }

    public function scopeIn($query)
    {
        return $query->where('io','=', 'i');
    }

    public function scopeOut($query)
    {
        return $query->where('io','=', 'o');
    }
}
