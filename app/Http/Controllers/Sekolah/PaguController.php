<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pagu;
use Auth;
use Cookie;
use Response;
use DataTables;

class PaguController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
            $model = Pagu::query();
            // $query = Pagu::with('sekolah')->select('pagus.*');

            $ta = Cookie::get('ta');
            $npsn= Auth::user()->npsn;
            return DataTables::eloquent($model)
            ->editColumn('pagu', function ($pagu) {
                return FormatMataUang($pagu->pagu);
            })
            ->editColumn('penggunaan_tw1', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw1);
            })
            ->editColumn('penggunaan_tw2', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw2);
            })
            ->editColumn('penggunaan_tw3', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw3);
            })
            ->editColumn('penggunaan_tw4', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw4);
            })
            ->editColumn('sisa', function ($pagu) {
                return FormatMataUang($pagu->sisa);
            })
            ->filter(function ($query) use ($ta, $npsn) {
                $query->where(
                	[
                		'ta' => $ta,
                		'npsn' => $npsn
                	]
                );
            },true)
            ->addIndexColumn()
            ->make(true);
        }

    	return view('sekolah.pagu');
    }
}
