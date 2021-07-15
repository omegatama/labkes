<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenagaMedis extends Model
{
    protected $table = 'tenaga_medis';
    protected $fillable = ['nip','nama','alamat','email','telpon','jenis_kelamin','jabatan','marital','status'];
}
