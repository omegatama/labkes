<?php

namespace App\Http\Controllers\Sekolah\Master;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BarangPersediaan;
use Auth;
use Response;
use DataTables;

class BarangPersediaanController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
    		$npsn= Auth::user()->npsn;
    		$model = BarangPersediaan::query();

	        return DataTables::eloquent($model)
	        ->filter(function ($query) use ($npsn) {
	    		$query->where('npsn', '=', $npsn);
	    	},true)
	        ->addColumn('action', 'layouts.edithapus')
	        ->rawColumns(['action'])
	        ->addIndexColumn()
	        ->make(true);
	    }

    	return view('sekolah.master.barangpersediaan');
    }

    public function store(Request $request)
	{  
	    $barangpersediaan   = new BarangPersediaan;
		$barangpersediaan->nama_persediaan 	= $request->nama_persediaan;
		$barangpersediaan->satuan 			= $request->satuan;
		$barangpersediaan->harga_satuan 	= $request->harga_satuan;
		// $barangpersediaan->stok 			= $request->stok;
		$barangpersediaan->npsn 			= Auth::user()->npsn;
		$saved 		= $barangpersediaan->save();        
	    return Response::json($barangpersediaan);
	}

	public function edit($id)
	{   
	    $barangpersediaan  = BarangPersediaan::find($id);
	 
	    return Response::json($barangpersediaan);
	}

	public function update(Request $request, $id)
    {
        $barangpersediaan = BarangPersediaan::find($id);
        if ($barangpersediaan) {
        	$barangpersediaan->nama_persediaan 	= $request->nama_persediaan;
			$barangpersediaan->satuan 			= $request->satuan;
			$barangpersediaan->harga_satuan 	= $request->harga_satuan;
			// $barangpersediaan->stok 			= $request->stok;
			$updated= $barangpersediaan->save();
        	return Response::json($barangpersediaan);
        }
    }

    public function destroy($id)
	{
		DB::beginTransaction();
		$counttrx = BarangPersediaan::find($id)->trxpersediaans()->count();
		$countbelanja = BarangPersediaan::find($id)->belanja_persediaans()->count();
		if ($counttrx==0 && $countbelanja==0) {
			# code...
	    	$barangpersediaan = BarangPersediaan::find($id)->delete();
			DB::commit();
		}
		else{
	 		DB::rollback();
	    	$barangpersediaan = 0;
		}
	 
	    return Response::json($barangpersediaan);
	}
}
