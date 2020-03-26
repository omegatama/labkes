<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Belanja;
use Auth;
use Cookie;
use Response;
use DataTables;

class BelanjaPersediaanController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
            $ta = Cookie::get('ta');
    		$query = Belanja::persediaan()->ta($ta)->with('rka.rekening','rka.program','rka.kp','rka.kegiatan','sekolah');

            return DataTables::eloquent($query)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->editColumn('tanggal', function ($belanja) {
                return $belanja->tanggal->format('d/m/Y');
            })
            ->editColumn('nilai', function ($belanja) {
                return FormatMataUang($belanja->nilai);
            })
            ->editColumn('rka.rekening.nama_rekening', function(Belanja $belanja) {
                return 
                    $belanja->rka->rekening->parent->kode_rekening.".".
                    $belanja->rka->rekening->kode_rekening." - ".
                    $belanja->rka->rekening->nama_rekening;
            })
            ->addColumn('action', function($belanja) {
                $urledit= route('admin.belanja.persediaan', ['id' => $belanja->id]);

                $btnaction =
	                RenderTombol("success", $urledit, "Detail")." ";

	            return $btnaction;
            })
            ->addColumn('details_url', function($belanja) {
                return route('admin.belanja.getpersediaan', $belanja->id);
            })
            ->setRowClass(function ($belanja) {
                return $belanja->keterangan == -1 ? 'bg-darken-1 bg-red text-white' : '';
            })
            ->addIndexColumn()
            ->make(true);
    	}
    	return view('admin.belanja.persediaan.index');
    }
}
