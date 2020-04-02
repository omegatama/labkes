<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
	use SoftDeletes;

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'npsn', 'uraian'
    ];

    public function rkas()
    {
        return $this->hasMany('App\Rka');
    }
}
