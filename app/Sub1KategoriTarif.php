<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sub1KategoriTarif extends Model
{
    protected $table = 'sub1kategori_tarifs';
    protected $fillable = ['idkategori','kodesub1kategori','namasub1kategori'];

    public function kategori()
    {
        return $this->hasOne('App\KategoriTarif', 'id', 'idkategori');
    }
}
