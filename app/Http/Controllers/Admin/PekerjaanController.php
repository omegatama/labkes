<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Pekerjaan;
use DataTables;

class PekerjaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = Pekerjaan::select(['id','kode','nama_pekerjaan']);
            return DataTables::eloquent($model)
            ->addColumn('action', function(Pekerjaan $pekerjaan) {
                $urledit= route('admin.pekerjaan.edit', ['id' => $pekerjaan->id]);
                $urlhapus= route('admin.pekerjaan.destroy', ['id' => $pekerjaan->id]);
                               
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlhapus, "Non Aktif");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.pekerjaan.index');
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aksi= "tambah";
        return view('admin.pekerjaan.tambah', compact("aksi"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pekerjaan = new Pekerjaan;
        // $pekerjaan->kode = $request->kode;
        $pekerjaan->nama_pekerjaan = $request->nama_pekerjaan;

        if($pekerjaan->save()){
            return redirect()->route("admin.pekerjaan.index");
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
        $pekerjaan = Pekerjaan::findOrFail($id);
        $aksi= "tambah";
        return view('admin.pekerjaan.tambah', compact("aksi","pekerjaan"));
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
        $pekerjaan = Pekerjaan::findOrFail($id);
        $pekerjaan->kode = $request->kode;
        $pekerjaan->nama_pekerjaan = $request->nama_pekerjaan;
        
        if($pekerjaan->save()){
            return redirect()->route("admin.pekerjaan.index");
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
        $pekerjaan = Pekerjaan::findOrFail($id);

        // $tenagamedis->status = 0;
        if($pekerjaan->delete()){
            return redirect()->route("admin.pekerjaan.index");
        }
    }
}
