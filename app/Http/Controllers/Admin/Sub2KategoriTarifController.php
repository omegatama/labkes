<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Sub2KategoriTarif;
use App\Sub1KategoriTarif;
use App\KategoriTarif;
use DataTables;


class Sub2KategoriTarifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // echo "tes";
        $idsub1 = $request->query('idsub1');
    

        if(request()->ajax()) {
            $model = Sub2KategoriTarif::select(['id','kodesub2kategori','namasub2kategori'])->where('idsub1',$idsub1);
            return DataTables::eloquent($model)
            ->addColumn('action', function(Sub2KategoriTarif $sub2kategoritarif) use ($idsub1) {
                $urledit= route('admin.sub2kategoritarif.edit', [
                    'id' => $sub2kategoritarif->id,
                    'idsub1' => $idsub1
                ]);
                // $urlsub3= route('admin.sub2kategoritarif.edit', ['id' => $sub2kategoritarif->id]);//route('admin.sub2kategoritarif.index', ['idsub1kategori' => $sub1kategoritarif->id]);
                                
                return RenderTombol("success", $urledit, "Edit");
                // RenderTombol("warning confirmation", $urlsub3, "Sub3");
            })
            ->addIndexColumn()
            ->make(true);
             
        }
        $sub1kategori = Sub1KategoriTarif::with('kategori')->findOrFail($idsub1);
        // return $sub1kategori;
        // return $sub1kategori->kategori;
        return view('admin.sub2kategoritarif.index',compact('sub1kategori'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $aksi= "tambah";
        $idsub1 = $request->query('idsub1');
        $sub1kategori = Sub1KategoriTarif::with('kategori')->findOrFail($idsub1);
        return view('admin.sub2kategoritarif.tambah', compact("aksi","sub1kategori"));
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
        $sub2kategoritarif = new Sub2KategoriTarif;
        $sub2kategoritarif->idsub1 = $request->idsub1;
        $sub2kategoritarif->kodesub2kategori = $request->kodesub2kategori;
        $sub2kategoritarif->namasub2kategori = $request->namasub2kategori;
        

        if($sub2kategoritarif->save()){
            return redirect()->route("admin.sub2kategoritarif.index",['idsub1' => $request->idsub1]);
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

        $sub2kategori = Sub2KategoriTarif::findOrFail($id);
        // return $sub1kategori;
        $aksi= "edit";
        return view('admin.sub2kategoritarif.tambah', compact("aksi","sub2kategori","kategori"));
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
        //
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
