<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KodeBarang extends Model
{
    use SoftDeletes;

	protected $dates = ['deleted_at'];

    protected $fillable = [
        'kode_barang', 'nama_barang', 'parent_id'
    ];
}
