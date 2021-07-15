<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KategoriTarif extends Model
{
    protected $table = 'kategori_tarifs';
    protected $fillable = ['kodekategori','namakategori'];
}
