<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomponenPembiayaan extends Model
{
    use SoftDeletes;

	protected $dates = ['deleted_at'];

    protected $fillable = [
        'kode_komponen', 'nama_komponen'
    ];
}
