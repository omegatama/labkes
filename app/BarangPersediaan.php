<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangPersediaan extends Model
{
    use SoftDeletes;

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'npsn', 'nama_persediaan', 'satuan', 'harga_satuan', 'stok'
    ];

    public function trxpersediaans()
    {
        return $this->hasMany('App\PersediaanTrx');
    }

    public function sekolah()
    {
        return $this->belongsTo('App\Sekolah', 'npsn', 'npsn');
    }
}
