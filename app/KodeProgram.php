<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KodeProgram extends Model
{
    use SoftDeletes;

	protected $dates = ['deleted_at'];

    protected $fillable = [
        'kode_program', 'nama_program'
    ];
}
