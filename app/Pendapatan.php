<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendapatan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['tanggal', 'created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['ta', 'npsn', 'sumber', 'nominal', 'keterangan', 'tanggal'];

    public function sekolah()
	{
	    return $this->belongsTo('App\Sekolah', 'npsn', 'npsn');
	}

    public function transaksi()
    {
        return $this->belongsTo('App\KasTrx', 'id', 'reference_id');
    }
}
