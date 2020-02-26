<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RkaLimit extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['ta', 'triwulan', 'npsn', 'kode_rekening_id', 'limit', 'sisa'];

    public function rekening()
	{
	    return $this->belongsTo('App\KodeRekening','kode_rekening_id');
	}
}
