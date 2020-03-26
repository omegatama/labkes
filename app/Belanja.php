<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Belanja extends Model
{
    use SoftDeletes;

    protected $dates = ['tanggal', 'created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['triwulan', 'npsn', 'rka_id', 'nama', 'nilai', 'kas', 'tanggal', 'nomor', 'penerima', 'ppn', 'pph21', 'pph23'];

    protected $appends = ['jenis_belanja', 'keterangan'];

    public function sekolah()
	{
	    return $this->belongsTo('App\Sekolah', 'npsn', 'npsn');
	}

    public function transaksi()
    {
        return $this->belongsTo('App\KasTrx', 'id', 'reference_id')
        ->where(
            [
                'io' => 'o'
            ]
        );
    }

    public function rka()
    {
        return $this->belongsTo('App\Rka', 'rka_id', 'id');
    }

    public function scopeNpsn($query, $npsn)
    {
        return $query->whereHas('rka', function ($qrka) use ($npsn) {
            $qrka->whereHas('sekolah', function ($q) use ($npsn) {
                $q->where('npsn','=', $npsn);   
            });
        });
    }

    public function scopeTa($query, $ta)
    {
        return $query->whereHas('rka', function ($qrka) use ($ta) {
			$qrka->where('ta','=', $ta);   
            
        });
    }

    public function scopeModal($query)
    {
        return $query->whereHas('rka', function ($qrka) {
            $qrka->whereHas('rekening', function ($q) {
                $q->where('jenis','=', 1);   
            });
        });
    }

    public function scopePersediaan($query)
    {
        return $query->whereHas('rka', function ($qrka) {
            $qrka->whereHas('rekening', function ($q) {
                $q->where('jenis','=', 2);   
            });
        });
    }

    public function scopeSampaiTriwulan($query, $tw)
    {
        // if (!empty($tw)) {
            return $query->where('triwulan','<=', $tw);
        // }
    }

    public function scopeTriwulan($query, $tw)
    {
        // if (!empty($tw)) {
            return $query->where('triwulan','=', $tw);
        // }
    }

    public function scopeParentRekening($query, $rekening_id)
    {
        return $query->whereHas('rka', function ($qrka) use ($rekening_id) {
            $qrka->whereHas('rekening', function ($q) use ($rekening_id) {
                $q->where('parent_id', $rekening_id);
            });
        });
    }

    public function scopeRekening($query, $rekening_id)
    {
        return $query->whereHas('rka', function ($qrka) use ($rekening_id) {
            $qrka->whereHas('rekening', function ($q) use ($rekening_id) {
                $q->where('id', $rekening_id);
            });
        });
    }

    /*public function modals()
    {
        return $this->hasMany('App\BelanjaModal', 'belanja_id', 'id');
    }

    public function persediaans()
    {
        return $this->hasMany('App\BelanjaPersediaan', 'belanja_id', 'id');
    }*/

    public function modals()
    {
        return $this->hasMany('App\BelanjaModal', 'belanja_id', 'id');
    }

    public function persediaans()
    {
        return $this->hasMany('App\BelanjaPersediaan', 'belanja_id', 'id');
    }

    public function getJenisBelanjaAttribute()
    {
        // return "{$this->first_name} {$this->last_name}";
        if($this->rka->rekening->jenis){
            return $this->rka->rekening->jenis;
        }
        // ;
    }

    public function getKeteranganAttribute()
    {
        switch ($this->jenis_belanja) {
            case 1:
                // Modal...
                $this->load('modals');
                if ($this->nilai==$this->modals()->sum('total')) {
                    # code...
                    return 1;
                }
                else{
                    return -1;
                }
                break;

            case 2:
                // Persediaan
                $this->load('persediaans');
                if ($this->nilai==$this->persediaans()->sum('total')) {
                    # code...
                    return 1;
                }
                else{
                    return -1;
                }
                break;

            default:
                // Something else
                # code...
                break;
        }
    }
}
