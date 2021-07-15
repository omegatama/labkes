<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Metode extends Model
{
    protected $table = 'metodes';
    protected $fillable = ['kode','nama_metode','badan_sertifikasi','expiredate','status'];
}
