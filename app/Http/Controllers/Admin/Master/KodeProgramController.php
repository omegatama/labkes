<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KodeProgram;
use Auth;
use Response;
use DataTables;

class KodeProgramController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
    		$model = KodeProgram::query();

	        return DataTables::eloquent($model)
	        ->addColumn('action', 'layouts.edithapus')
	        ->rawColumns(['action'])
	        ->addIndexColumn()
	        ->make(true);
	    }
        return view('admin.master.kodeprogram');
    }

    public function store(Request $request)
	{  
	    $kodeprogram   = new KodeProgram;
		$kodeprogram->kode_program 	= $request->kode_program;
		$kodeprogram->nama_program 	= $request->nama_program;
		$saved 		  = $kodeprogram->save();        
	    return Response::json($kodeprogram);
	}

	public function edit($id)
	{   
	    $kodeprogram  = KodeProgram::find($id);
	 
	    return Response::json($kodeprogram);
	}

	public function update(Request $request, $id)
    {
        $kodeprogram = KodeProgram::find($id);
        if ($kodeprogram) {
        	$kodeprogram->kode_program= $request->kode_program;
        	$kodeprogram->nama_program= $request->nama_program;
        	$updated= $kodeprogram->save();
        	return Response::json($kodeprogram);
        }
    }

    public function destroy($id)
	{
	    $kodeprogram = KodeProgram::find($id)->delete();
	 
	    return Response::json($kodeprogram);
	}
}
