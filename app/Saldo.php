<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saldo extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['ta', 'npsn', 'saldo_tunai', 'saldo_bank'];

    public function sekolah()
    {
        return $this->belongsTo('App\Sekolah', 'npsn', 'npsn');
    }

    public function scopeTa($query, $ta)
    {
        $query->where('ta','=', $ta);   
    }
}
