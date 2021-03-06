<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Sekolah extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $guard = 'sekolah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'email', 'npsn', 'password',
        'jenjang', 'status', 'kecamatan_id', 'desa',
        'alamat', 'telepon', 'nama_kepsek', 'nip_kepsek',
        'nama_bendahara', 'nip_bendahara', 'kunci_rka',
        'periode_awal', 'periode_akhir'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function kecamatan()
    {
        return $this->belongsTo('App\Kecamatan');
    }

    public function pagus()
    {
        return $this->hasMany('App\Pagu', 'npsn', 'npsn');
    }

    public function rkas()
    {
        return $this->hasMany('App\Rka', 'npsn', 'npsn');
    }

    public function pendapatans()
    {
        return $this->hasMany('App\Pendapatan', 'npsn', 'npsn');
    }

    public function saldos()
    {
        return $this->hasMany('App\Saldo', 'npsn', 'npsn');
    }

    public function saldo_awals()
    {
        return $this->hasMany('App\SaldoAwal', 'npsn', 'npsn');
    }

    public function persediaans()
    {
        return $this->hasMany('App\BarangPersediaan', 'npsn', 'npsn');
    }

    public function kas_trxs()
    {
        return $this->hasMany('App\KasTrx', 'npsn','npsn'); 
    }

    public function kas_trx_details()
    {
        return $this->hasMany('App\KasTrxDetail', 'npsn','npsn'); 
    }

    public function belanjas()
    {
        return $this->hasMany('App\Belanja', 'npsn','npsn'); 
    }

    public function kegiatans()
    {
        return $this->hasMany('App\Kegiatan', 'npsn', 'npsn');
    }

    public function scopeKecamatanId($query, $kecamatan_id)
    {
        return $query->where('kecamatan_id','=', $kecamatan_id);
    }

    public function scopeJenjang($query, $jenjang)
    {
        return $query->where('jenjang','=', $jenjang);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status','=', $status);
    }

    public function scopeNpsn($query, $npsn)
    {
        return $query->where('npsn','=', $npsn);
    }

}
