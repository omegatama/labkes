<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pagu;
use Auth;
use Cookie;
use Response;
use DataTables;

class PaguController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            // $model = Pagu::query();
            $query = Pagu::with('sekolah')->select('pagus.*');

            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->addColumn('action', function(Pagu $pagu) {
                if ($pagu->pagu==$pagu->sisa) {
                    # code...
                    $urledit= route('admin.pagu.edit', ['id' => $pagu->id]);
                    $fungsidelete= "hapus_pagu($pagu->id)";
                    return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger", "#", "Hapus", $fungsidelete);
                }
                else{
                    return '-';
                }
            })
            ->editColumn('pagu', function ($pagu) {
                return FormatMataUang($pagu->pagu);
            })
            ->filter(function ($query) use ($ta) {
                $query->where('ta', '=', $ta);
            },true)
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.pagu.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pagu.tambah', ['aksi' => "tambah"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check= Pagu::where([
            'ta' =>$request->cookie('ta'),
            'npsn' => $request->npsn
        ])->doesntExist();

        if ($check) {
            # code...
            $pagu= new Pagu;
            $pagu->ta= $request->cookie('ta');
            $pagu->npsn= $request->npsn;
            $pagu->pagu= floatval(str_replace(",",".",$request->nominal));
            $pagu->sisa= $pagu->pagu;
            if($pagu->save()){
                return redirect()->route('admin.pagu.index');
            }
        }
        else{
            return back()->withErrors(['msg' => 'Pagu untuk '.$request->npsn.' sudah ada!']);
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
        $pagu= Pagu::find($id);
        if ($pagu) {
            # code...
            return view('admin.pagu.tambah', 
                [
                    'aksi' => "edit",
                    'pagu' => $pagu
                ]
            );
        }
        else{
            return redirect()->back()->withErrors(['msg' => 'Pagu dengan ID: '.$id.' tidak ditemukan!']);
        }
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
        $pagu= Pagu::find($id);
        $pagu->pagu= floatval(str_replace(",",".",$request->nominal));
        $pagu->sisa= $pagu->pagu;
        if ($pagu->save()) {
            return redirect()->route('admin.pagu.index');
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
        $pagu= Pagu::find($id)->delete();
     
        return Response::json($pagu);

    }
}
