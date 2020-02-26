<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KomponenPembiayaan;
use Auth;
use Response;
use DataTables;

class KomponenPembiayaanController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
    		$model = KomponenPembiayaan::query();

	        return DataTables::eloquent($model)
	        ->addColumn('action', 'layouts.edithapus')
	        ->rawColumns(['action'])
	        ->addIndexColumn()
	        ->make(true);
	    }
        return view('admin.master.komponenpembiayaan');
    }

    public function store(Request $request)
	{  
	    $komponenpembiayaan   = new KomponenPembiayaan;
		$komponenpembiayaan->kode_komponen 	= $request->kode_komponen;
		$komponenpembiayaan->nama_komponen 	= $request->nama_komponen;
		$saved 		  = $komponenpembiayaan->save();        
	    return Response::json($komponenpembiayaan);
	}

	public function edit($id)
	{   
	    $komponenpembiayaan  = KomponenPembiayaan::find($id);
	 
	    return Response::json($komponenpembiayaan);
	}

	public function update(Request $request, $id)
    {
        $komponenpembiayaan = KomponenPembiayaan::find($id);
        if ($komponenpembiayaan) {
        	$komponenpembiayaan->kode_komponen= $request->kode_komponen;
        	$komponenpembiayaan->nama_komponen= $request->nama_komponen;
        	$updated= $komponenpembiayaan->save();
        	return Response::json($komponenpembiayaan);
        }
    }

    public function destroy($id)
	{
	    $komponenpembiayaan = KomponenPembiayaan::find($id)->delete();
	 
	    return Response::json($komponenpembiayaan);
	}
}
