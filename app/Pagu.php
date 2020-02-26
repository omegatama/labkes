<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagu extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['ta', 'npsn', 'pagu', 'penggunaan_tw1', 'penggunaan_tw2', 'penggunaan_tw3', 'penggunaan_tw4', 'sisa'];

    /**
	 * Get data sekolah yang memiliki pagu
	 */
	public function sekolah()
	{
	    return $this->belongsTo('App\Sekolah', 'npsn', 'npsn');
	}
}
