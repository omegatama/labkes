<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rka extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['ta', 'npsn', 'kode_program_id', 'kegiatan_id', 'komponen_pembiayaan_id', 'kode_rekening_id', 'uraian', 'volume', 'satuan', 'harga_satuan', 'jumlah', 'alokasi_tw1', 'alokasi_tw2', 'alokasi_tw3', 'alokasi_tw4'];

    public function sekolah()
	{
	    return $this->belongsTo('App\Sekolah', 'npsn', 'npsn');
	}

	public function program()
	{
	    return $this->belongsTo('App\KodeProgram','kode_program_id');
	}

	public function kegiatan()
	{
	    return $this->belongsTo('App\Kegiatan');
	}

	public function kp()
	{
	    return $this->belongsTo('App\KomponenPembiayaan','komponen_pembiayaan_id');
	}

	public function rekening()
	{
	    return $this->belongsTo('App\KodeRekening','kode_rekening_id');
	}
}
