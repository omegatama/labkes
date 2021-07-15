<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Sub1KategoriTarif;
use App\KategoriTarif;
use DataTables;


class Sub1KategoriTarifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // echo "tes";
        $idkategori = $request->query('idkategori');
    

        if(request()->ajax()) {
            $model = Sub1KategoriTarif::select(['id','kodesub1kategori','namasub1kategori'])->where('idkategori',$idkategori);
            return DataTables::eloquent($model)
            ->addColumn('action', function(Sub1KategoriTarif $sub1kategoritarif) use ($idkategori) {
                $urledit= route('admin.sub1kategoritarif.edit', [
                    'id' => $sub1kategoritarif->id,
                    'idkategori' => $idkategori
                ]);
                $urlsub2= route('admin.sub2kategoritarif.index', ['idsub1' => $sub1kategoritarif->id]);//route('admin.sub2kategoritarif.index', ['idsub1kategori' => $sub1kategoritarif->id]);
                                
                return RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("warning confirmation", $urlsub2, "Sub2");
            })
            ->addIndexColumn()
            ->make(true);
             
        }
        $kategori = KategoriTarif::findOrFail($idkategori);
        // return $kategori;
        return view('admin.sub1kategoritarif.index',compact('kategori'));  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $aksi= "tambah";
        $idkategori = $request->query('idkategori');
        $kategori = KategoriTarif::findOrFail($idkategori);
        return view('admin.sub1kategoritarif.tambah', compact("aksi","kategori"));
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
        $sub1kategoritarif = new Sub1KategoriTarif;
        $sub1kategoritarif->idkategori = $request->idkategori;
        $sub1kategoritarif->kodesub1kategori = $request->kodesub1kategori;
        $sub1kategoritarif->namasub1kategori = $request->namasub1kategori;
        

        if($sub1kategoritarif->save()){
            return redirect()->route("admin.sub1kategoritarif.index",['idkategori' => $request->idkategori]);
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
    public function edit(Request $request, $id)
    {
        $idkategori = $request->query('idkategori');
        $kategori = KategoriTarif::findOrFail($idkategori);

        $sub1kategori = Sub1KategoriTarif::findOrFail($id);
        // return $sub1kategori;
        $aksi= "edit";
        return view('admin.sub1kategoritarif.tambah', compact("aksi","sub1kategori","kategori"));
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
        $sub1kategori = Sub1KategoriTarif::findOrFail($id);
        $sub1kategori->kodesub1kategori = $request->kodesub1kategori;
        $sub1kategori->namasub1kategori = $request->namasub1kategori;
        // $tenagamedis->status = $request->status;

        if($sub1kategori->save()){
            return redirect()->route("admin.sub1kategoritarif.index",['idkategori' => $request->idkategori]);
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
