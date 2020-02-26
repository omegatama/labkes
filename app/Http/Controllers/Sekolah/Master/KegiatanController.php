<?php

namespace App\Http\Controllers\Sekolah\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Kegiatan;
use Auth;
use Response;
use DataTables;

class KegiatanController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
    		$npsn= Auth::user()->npsn;
    		$model = Kegiatan::query();

	        return DataTables::eloquent($model)
	        ->filter(function ($query) use ($npsn) {
	    		$query->where('npsn', '=', $npsn);
	    	},true)
	        ->addColumn('action', 'layouts.edithapus')
	        ->rawColumns(['action'])
	        ->addIndexColumn()
	        ->make(true);
	    }
        return view('sekolah.master.kegiatan');
    }

    public function store(Request $request)
	{  
	    $kegiatan   = new Kegiatan;
		$kegiatan->uraian 	= $request->uraian;
		$kegiatan->npsn 	= Auth::user()->npsn;
		$saved 		= $kegiatan->save();        
	    return Response::json($kegiatan);
	}

    public function edit($id)
	{   
	    $where = array('id' => $id);
	    $kegiatan  = Kegiatan::where($where)->first();
	 
	    return Response::json($kegiatan);
	}

	public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::find($id);
        if ($kegiatan) {
        	$kegiatan->uraian= $request->uraian;
        	$updated= $kegiatan->save();
        	return Response::json($kegiatan);
        }
    }

	public function destroy($id)
	{
	    $kegiatan = Kegiatan::where('id',$id)->delete();
	 
	    return Response::json($kegiatan);
	}
}
