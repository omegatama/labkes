<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Pendidikan;
use DataTables;

class PendidikanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = Pendidikan::select(['id','kode','pendidikan']);
            return DataTables::eloquent($model)
            ->addColumn('action', function(Pendidikan $pendidikan) {
                $urledit= route('admin.pendidikan.edit', ['id' => $pendidikan->id]);
                $urlhapus= route('admin.pendidikan.destroy', ['id' => $pendidikan->id]);
                               
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlhapus, "Non Aktif");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.pendidikan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aksi= "tambah";
        return view('admin.pendidikan.tambah', compact("aksi"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pendidikan = new Pendidikan;
        $pendidikan->kode = $request->kode;
        $pendidikan->pendidikan = $request->pendidikan;

        if($pendidikan->save()){
            return redirect()->route("admin.pendidikan.index");
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
        $pendidikan = Pendidikan::findOrFail($id);
        $aksi= "tambah";
        return view('admin.pendidikan.tambah', compact("aksi","pendidikan"));
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
        $pendidikan = Pendidikan::findOrFail($id);
        $pendidikan->kode = $request->kode;
        $pendidikan->pendidikan = $request->pendidikan;
        
        if($pendidikan->save()){
            return redirect()->route("admin.pendidikan.index");
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
        $pendidikan = Pendidikan::findOrFail($id);
        // $tenagamedis->status = 0;
        if($pendidikan->save()){
            return redirect()->route("admin.pendidikan.index");
        }
    }
}
