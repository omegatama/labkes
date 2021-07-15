<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NamaTarif extends Model
{
    protected $table = 'nama_tarifs';
    protected $fillable = ['kodenamatarif','namatarif','status'];
}
