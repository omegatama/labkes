<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaldoAwal extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['periode', 'created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['ta', 'npsn', 'periode', 'saldo_tunai', 'saldo_bank'];
}
