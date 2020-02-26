<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KodeBarang;
use Auth;
use Response;
use DataTables;

class KodeBarangController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
    		$model = KodeBarang::query();

	        return DataTables::eloquent($model)
	        ->addColumn('action', 'layouts.edithapus')
	        ->rawColumns(['action'])
	        ->addIndexColumn()
	        ->make(true);
	    }
        return view('admin.master.kodebarang');
    }

    public function store(Request $request)
	{  
	    $kodebarang   = new KodeBarang;
		$kodebarang->kode_barang 	= $request->kode_barang;
		$kodebarang->nama_barang 	= $request->nama_barang;
		$kodebarang->parent_id 		= $request->parent_id;
		$saved 		  = $kodebarang->save();        
	    return Response::json($kodebarang);
	}

	public function edit($id)
	{   

	    $kodebarang  = KodeBarang::find($id);
	 
	    return Response::json($kodebarang);
	}

	public function update(Request $request, $id)
    {
        $kodebarang = KodeBarang::find($id);
        if ($kodebarang) {
        	$kodebarang->kode_barang= $request->kode_barang;
        	$kodebarang->nama_barang= $request->nama_barang;
        	$kodebarang->parent_id= $request->parent_id;
        	$updated= $kodebarang->save();
        	return Response::json($kodebarang);
        }
    }

	public function destroy($id)
	{
	    $kodebarang = KodeBarang::find($id)->delete();
	 
	    return Response::json($kodebarang);
	}
}
