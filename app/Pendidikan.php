<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class Pendidikan extends Model
{
    use AutoNumberTrait;
    public function getAutoNumberOptions()
    {
        return [
            'kode' => [
                'format' => 'PD?', // Format kode yang akan digunakan.
                'length' => 3 // Jumlah digit yang akan digunakan sebagai nomor urut
            ]
        ];
    }
}
