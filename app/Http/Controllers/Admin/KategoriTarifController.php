<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\KategoriTarif;
use DataTables;

class KategoriTarifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = KategoriTarif::select(['id','kodekategori','namakategori']);
            return DataTables::eloquent($model)
            ->addColumn('action', function(KategoriTarif $kategoritarif) {
                $urledit= route('admin.kategoritarif.edit', ['id' => $kategoritarif->id]);
                $urlsub1= route('admin.sub1kategoritarif.index', ['idkategori' => $kategoritarif->id]);
                               
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlsub1, "Sub1");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.kategoritarif.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aksi= "tambah";
        return view('admin.kategoritarif.tambah', compact("aksi"));
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
        $kategoritarif = new KategoriTarif;
        $kategoritarif->kodekategori = $request->kodekategori;
        $kategoritarif->namakategori = $request->namakategori;
        

        if($kategoritarif->save()){
            return redirect()->route("admin.kategoritarif.index");
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
        $kategoritarif = KategoriTarif::findOrFail($id);
        //return $kategoritarif;
        $aksi= "edit";
        return view('admin.kategoritarif.tambah', compact("aksi","kategoritarif"));
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
        $kategoritarif = KategoriTarif::findOrFail($id);
        $kategoritarif->kodekategori = $request->kodekategori;
        $kategoritarif->namakategori = $request->namakategori;
        // $tenagamedis->status = $request->status;

        if($kategoritarif->save()){
            return redirect()->route("admin.kategoritarif.index");
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
        //
    }
}
