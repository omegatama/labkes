<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Metode;
use DataTables;

class MetodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = Metode::select(['id','kode','nama_metode','badan_sertifikasi','expiredate']);
            return DataTables::eloquent($model)
            ->addColumn('action', function(Metode $metode) {
                $urledit= route('admin.metode.edit', ['id' => $metode->id]);
                $urlhapus= route('admin.metode.destroy', ['id' => $metode->id]);
                               
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlhapus, "Non Aktif");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.metode.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aksi= "tambah";
        return view('admin.metode.tambah', compact("aksi"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $metode = new Metode;
        $metode->kode = $request->kode;
        $metode->nama_metode = $request->nama_metode;
        $metode->badan_sertifikasi = $request->nama_bs;
        $metode->expiredate = $request->ed;
        $metode->status = $request->status;

        if($metode->save()){
            return redirect()->route("admin.metode.index");
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
        $metode = Metode::findOrFail($id);
        $aksi= "tambah";
        return view('admin.metode.tambah', compact("aksi","metode"));
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
        $metode = Metode::findOrFail($id);
        $metode->kode = $request->kode;
        $metode->nama_metode = $request->nama_metode;
        $metode->badan_sertifikasi = $request->nama_bs;
        $metode->expiredate = $request->ed;
        $metode->status = $request->status;

        if($metode->save()){
            return redirect()->route("admin.metode.index");
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
        $metode = Metode::findOrFail($id);
        // $tenagamedis->status = 0;
        if($metode->save()){
            return redirect()->route("admin.metode.index");
        }
    }
}
