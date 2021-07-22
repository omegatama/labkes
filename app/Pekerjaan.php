<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class Pekerjaan extends Model
{
    use AutoNumberTrait;

    protected $table = 'pekerjaans';
    protected $fillable = ['kode','nama_pekerjaan'];

    public function getAutoNumberOptions()
    {
        return [
            'kode' => [
                'format' => 'PK?', // Format kode yang akan digunakan.
                'length' => 3 // Jumlah digit yang akan digunakan sebagai nomor urut
            ]
        ];
    }
}
