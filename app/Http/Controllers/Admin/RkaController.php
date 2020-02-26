<?php

namespace App\Http\Controllers\Admin;

// use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rka;
use App\Pagu;
use Auth;
use Cookie;
use Response;
use DataTables;

class RkaController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
            $query = Rka::with(['sekolah','program','kegiatan','kp','rekening'])->select('rkas.*');
            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                        // 'npsn' => $npsn
                    ]
                );
            },true)
            ->editColumn('harga_satuan', '{{FormatMataUang($harga_satuan)}}')
            ->editColumn('jumlah', '{{FormatMataUang($jumlah)}}')
            
            ->editColumn('alokasi_tw1', '{{FormatMataUang($alokasi_tw1)}}')
            ->editColumn('alokasi_tw2', '{{FormatMataUang($alokasi_tw2)}}')
            ->editColumn('alokasi_tw3', '{{FormatMataUang($alokasi_tw3)}}')
            ->editColumn('alokasi_tw4', '{{FormatMataUang($alokasi_tw4)}}')
            ->editColumn('rekening.nama_rekening', function(Rka $rka) {
                return 
                    $rka->rekening->parent->kode_rekening.".".
                    $rka->rekening->kode_rekening." - ".
                    $rka->rekening->nama_rekening;
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('admin.rka');
    }
}
