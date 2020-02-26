<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KodeRekening extends Model
{
	use SoftDeletes;

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'kode_rekening', 'nama_rekening', 'parent_id', 'jenis'
    ];

    public function parent(){
        return $this->belongsTo('App\KodeRekening', 'parent_id')->select('id','kode_rekening');
    }

    public function subrekening(){
        return $this->hasMany('App\KodeRekening', 'parent_id');
    }


}
