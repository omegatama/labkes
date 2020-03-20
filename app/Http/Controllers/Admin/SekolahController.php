<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Sekolah;
use Auth;
use Cookie;
use Response;
use DataTables;

class SekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $model = Sekolah::with('kecamatan');
            return DataTables::eloquent($model)
            ->addColumn('action', function(Sekolah $sekolah) {
                $urledit= route('admin.sekolah.edit', ['id' => $sekolah->id]);
                $urlreset= route('admin.sekolah.reset', ['id' => $sekolah->id]);                
                return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("warning confirmation", $urlreset, "Reset");
            })
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.sekolah.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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

    public function reset($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->password = Hash::make($sekolah->npsn);

        try {
            $sekolah->save();
            return redirect()->back()->with(['success'=> 'Password di set sesuai NPSN']);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Reset Password Gagal!');
        }
    }

    public function selectSekolah(Request $request)
    {
        $search = $request->get('search');
        $data = Sekolah::select(['id', 'name', 'npsn'])
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('npsn', 'like', '%' . $search . '%')
            ->orderBy('name')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }
}
