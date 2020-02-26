<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kecamatan extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'nama_kecamatan'
    ];
}
