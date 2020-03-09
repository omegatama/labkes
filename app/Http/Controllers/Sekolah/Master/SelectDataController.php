<?php

namespace App\Http\Controllers\Sekolah\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KodeProgram;
use App\Kegiatan;
use App\Kecamatan;
use App\KomponenPembiayaan;
use App\KodeRekening;
use App\KodeBarang;
use Auth;

class SelectDataController extends Controller
{
    public function selectProgram(Request $request)
    {
        $search = $request->get('search');
        $data = KodeProgram::select(['id', 'kode_program', 'nama_program'])
            ->where('kode_program', 'like', '%' . $search . '%')
            ->orWhere('nama_program', 'like', '%' . $search . '%')
            ->orderBy('kode_program')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }

    public function selectKecamatan(Request $request)
    {
        $search = $request->get('search');
        $data = Kecamatan::select(['id', 'nama_kecamatan'])
            ->where('nama_kecamatan', 'like', '%' . $search . '%')
            ->orderBy('id')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }

    public function selectKegiatan(Request $request)
    {
        $search = $request->get('search');
        $npsn = Auth::user()->npsn;
        $data = Kegiatan::select(['id', 'uraian'])
        	->where([
        		['npsn', '=', $npsn],
    			['uraian', 'like', '%' . $search . '%'],
        	])
            ->orderBy('id')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }

    public function selectKp(Request $request)
    {
        $search = $request->get('search');
        $data = KomponenPembiayaan::select(['id', 'kode_komponen', 'nama_komponen'])
            ->where('kode_komponen', 'like', '%' . $search . '%')
            ->orWhere('nama_komponen', 'like', '%' . $search . '%')
            ->orderBy('kode_komponen')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }

	public function selectRekening(Request $request)
	{
		$search = $request->get('search');
        $data = KodeRekening::whereNotNull('parent_id')
        ->with('parent')
        // $data = KodeRekening::select(['id', 'kode_komponen', 'nama_komponen'])
            ->where('nama_rekening', 'like', '%' . $search . '%')
        //     ->orWhere('nama_komponen', 'like', '%' . $search . '%')
            ->orderBy('parent_id')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);	
	}

    public function selectBarangPersediaan(Request $request)
    {
        $search = $request->get('search');
        // $npsn = Auth::user()->npsn;
        $data = Auth::user()->persediaans()->select(['id', 'nama_persediaan'])
            ->where([
                ['nama_persediaan', 'like', '%' . $search . '%'],
                // ['npsn', '=', $npsn],
            ])
            ->orderBy('id')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }

    public function selectKodeBarang(Request $request, $parent)
    {
        $search = $request->get('search');
        $data = KodeBarang::select(['id', 'kode_barang', 'nama_barang'])
            ->where('parent_id', '=', $parent)
            ->where(function ($query) use ($search) {
                $query
                    ->where('kode_barang', 'like', '%' . $search . '%')
                    ->orWhere('nama_barang', 'like', '%' . $search . '%');
            })
            
            ->orderBy('kode_barang')
            ->paginate(5);
        
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }
}
