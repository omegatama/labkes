<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pagu;
use Auth;
use Cookie;
use Response;
use DataTables;

class PaguController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
            $model = Pagu::query();
            // $query = Pagu::with('sekolah')->select('pagus.*');

            $ta = Cookie::get('ta');
            $npsn= Auth::user()->npsn;
            return DataTables::eloquent($model)
            ->editColumn('pagu', function ($pagu) {
                return FormatMataUang($pagu->pagu);
            })
            ->editColumn('penggunaan_tw1', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw1);
            })
            ->editColumn('penggunaan_tw2', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw2);
            })
            ->editColumn('penggunaan_tw3', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw3);
            })
            ->editColumn('penggunaan_tw4', function ($pagu) {
                return FormatMataUang($pagu->penggunaan_tw4);
            })
            ->editColumn('sisa', function ($pagu) {
                return FormatMataUang($pagu->sisa);
            })
            ->addColumn('action', function(Pagu $pagu) {
                $urlhitung= route('sekolah.pagu.hitungulang');
                return RenderTombol("success", $urlhitung, "Hitung Ulang");
            })
            ->filter(function ($query) use ($ta, $npsn) {
                $query->where(
                	[
                		'ta' => $ta,
                		'npsn' => $npsn
                	]
                );
            },true)
            ->addIndexColumn()
            ->make(true);
        }

    	return view('sekolah.pagu');
    }

    public function hitungulang(Request $request)
    {
        $sekolah= Auth::user();
        $npsn= $sekolah->npsn;
        // return $npsn;
        // SELECT npsn, sum(jumlah) as jumlah, SUM(alokasi_tw1) , SUM(alokasi_tw2) , SUM(alokasi_tw3) , SUM(alokasi_tw4)
        // FROM rkas WHERE npsn = "20320022" AND deleted_at IS NULL
        $results = DB::select('SELECT 
            SUM(jumlah) as jumlah, 
            SUM(alokasi_tw1) as alokasi_tw1, 
            SUM(alokasi_tw2) as alokasi_tw2, 
            SUM(alokasi_tw3) as alokasi_tw3, 
            SUM(alokasi_tw4) as alokasi_tw4
            FROM rkas 
            WHERE npsn = :npsn 
            AND deleted_at IS NULL', 
            ['npsn' => $npsn]
        );

        // return $results;
        $pagu= $sekolah->pagus->where('ta', $request->cookie('ta'))->first();
        // return $pagu;

        DB::beginTransaction();
        
        try {
            $alokasi_tw1 = $results[0]->alokasi_tw1;
            $alokasi_tw2 = $results[0]->alokasi_tw2;
            $alokasi_tw3 = $results[0]->alokasi_tw3;
            $alokasi_tw4 = $results[0]->alokasi_tw4;
            $jumlah = $alokasi_tw1 + $alokasi_tw2 + $alokasi_tw3 + $alokasi_tw4;

            if ($jumlah == $results[0]->jumlah) {
                $pagu->penggunaan_tw1 = $alokasi_tw1;
                $pagu->penggunaan_tw2 = $alokasi_tw2;
                $pagu->penggunaan_tw3 = $alokasi_tw3;
                $pagu->penggunaan_tw4 = $alokasi_tw4;
                $pagu->sisa = $pagu->pagu - $jumlah;
                $pagu->save();

            }

            else {
                return redirect()->back()
                    ->withErrors(['msg' => 'Alokasi RKA dan Jumlah RKA belum sama']);
            }

            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                    ->withErrors(['msg' => $e->getMessage()]);
        }

        // return $pagu;
        DB::commit();
        return redirect()->route('sekolah.pagu.index')->with(['success' => 'Pagu berhasil dihitung ulang!']);
    }
}
