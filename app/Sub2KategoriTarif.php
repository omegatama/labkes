<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sub2KategoriTarif extends Model
{
    protected $table = 'sub2kategori_tarifs';
    protected $fillable = ['idkategori','idsub1','kodesub2kategori','namasub2kategori'];
}
