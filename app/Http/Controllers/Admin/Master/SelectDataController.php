<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Kecamatan;
use App\KategoriTarif;
use App\Sub1KategoriTarif;
use App\Sub2KategoriTarif;

class SelectDataController extends Controller
{
    public function selectKecamatan(Request $request)
    {
        $search = $request->get('search');
        $data = Kecamatan::select(['id', 'nama_kecamatan'])
            ->where('nama_kecamatan', 'like', '%' . $search . '%')
            ->orderBy('id')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }

    public function selectKategori(Request $request)
    {
        $kategori = KategoriTarif::all();
        return $kategori;
    }

    public function selectSub1(Request $request)
    {
        if ($request->has('idkategori')) {
            $idkategori= $request->idkategori;
            $sub1kategori = Sub1KategoriTarif::where('idkategori', $idkategori)->get();
        }  
        else{
            $sub1kategori = Sub1KategoriTarif::all();
        }

        return $sub1kategori;
    }

    public function selectSub2(Request $request)
    {
        if ($request->has('idsub1')) {
            $idsub1= $request->idsub1;
            $sub2kategori = Sub2KategoriTarif::where('idsub1', $idsub1)->get();
        }  
        else{
            $sub2kategori = Sub2KategoriTarif::all();
        }

        return $sub2kategori;
    }
}
