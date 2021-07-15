<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\NamaTarif;
use DataTables;

class NamaTarifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = NamaTarif::select(['id','kodenamatarif','namatarif','status'])->where('status',1);
            return DataTables::eloquent($model)
            ->addColumn('action', function(NamaTarif $namatarif) {
                $urledit= route('admin.namatarif.edit', ['id' => $namatarif->id]);
                $urlhapus= route('admin.namatarif.destroy', ['id' => $namatarif->id]);
                               
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlhapus, "Non Aktif");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.namatarif.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aksi= "tambah";
        return view('admin.namatarif.tambah', compact("aksi"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $namatarif = new NamaTarif;
        $namatarif->kodenamatarif = $request->kodenamatarif;
        $namatarif->namatarif = $request->namatarif;
        $namatarif->status = 1;

        if($namatarif->save()){
            return redirect()->route("admin.namatarif.index");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $namatarif = NamaTarif::findOrFail($id);
        $aksi= "tambah";
        return view('admin.namatarif.tambah', compact("aksi","namatarif"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $namatarif = TenagaMedis::findOrFail($id);
        $namatarif->kodenamatarif = $request->kodenamatarif;
        $namatarif->namatarif = $request->namatarif;
        // $namatarif->status = $request->status;

        if($tenagamedis->save()){
            return redirect()->route("admin.namatarif.index");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $namatarif = NamaTarif::findOrFail($id);
        $namatarif->status = 0;
        if($namatarif->save()){
            return redirect()->route("admin.namatarif.index");
        }
    }
}
