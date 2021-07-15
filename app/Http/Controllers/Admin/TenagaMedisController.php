<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\TenagaMedis;
use DataTables;


class TenagaMedisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = TenagaMedis::select(['id','nip','nama','alamat','jabatan','status'])->where('status',1);
            return DataTables::eloquent($model)
            ->addColumn('action', function(TenagaMedis $tenagamedis) {
                $urledit= route('admin.tenagamedis.edit', ['id' => $tenagamedis->id]);
                $urlhapus= route('admin.tenagamedis.destroy', ['id' => $tenagamedis->id]);
                               
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlhapus, "Non Aktif");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.tenagamedis.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aksi= "tambah";
        return view('admin.tenagamedis.tambah', compact("aksi"));
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
        $tenagamedis = new TenagaMedis;
        $tenagamedis->nip = $request->nip;
        $tenagamedis->nama = $request->nama;
        $tenagamedis->alamat = $request->alamat;
        $tenagamedis->email = $request->email;
        $tenagamedis->telpon = $request->telpon;
        $tenagamedis->jenis_kelamin = $request->jenis_kelamin;
        $tenagamedis->jabatan = $request->jabatan;
        $tenagamedis->marital = $request->marital;
        $tenagamedis->status = 1;

        if($tenagamedis->save()){
            return redirect()->route("admin.tenagamedis.index");
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
        $tenagamedis = TenagaMedis::findOrFail($id);
        $aksi= "tambah";
        return view('admin.tenagamedis.tambah', compact("aksi","tenagamedis"));
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
        $tenagamedis = TenagaMedis::findOrFail($id);
        $tenagamedis->nip = $request->nip;
        $tenagamedis->nama = $request->nama;
        $tenagamedis->alamat = $request->alamat;
        $tenagamedis->email = $request->email;
        $tenagamedis->telpon = $request->telpon;
        $tenagamedis->jenis_kelamin = $request->jenis_kelamin;
        $tenagamedis->jabatan = $request->jabatan;
        $tenagamedis->marital = $request->marital;
        // $tenagamedis->status = $request->status;

        if($tenagamedis->save()){
            return redirect()->route("admin.tenagamedis.index");
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
        $tenagamedis = TenagaMedis::findOrFail($id);
        $tenagamedis->status = 0;
        if($tenagamedis->save()){
            return redirect()->route("admin.tenagamedis.index");
        }
    }
}
