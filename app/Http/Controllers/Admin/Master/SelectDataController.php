<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Kecamatan;

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
}
