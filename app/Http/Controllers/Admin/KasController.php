<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Saldo;
use Auth;
use Cookie;
use Response;
use DataTables;

class KasController extends Controller
{
    public function saldo_index()
    {
    	if(request()->ajax()) {
            $model = Saldo::with('sekolah');
            $ta = Cookie::get('ta');
            return DataTables::eloquent($model)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                        // 'npsn' => $npsn
                    ]
                );
            },true)
            ->editColumn('saldo_tunai', function ($saldo) {
                return FormatMataUang($saldo->saldo_tunai);
            })
            ->editColumn('saldo_bank', function ($saldo) {
                return FormatMataUang($saldo->saldo_bank);
            })
            ->filter(function ($query) use ($ta) {
                $query->where('ta', '=', $ta);
            },true)
            ->addIndexColumn()
            ->make(true);
        }
    	return view('admin.kas.saldo');
    }

    public function saldolalu_index()
    {
    	if(request()->ajax()) {
            $model = Saldo::with('sekolah');
            $ta = Cookie::get('ta') - 1;
            return DataTables::eloquent($model)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                        // 'npsn' => $npsn
                    ]
                );
            },true)
            ->editColumn('saldo_tunai', function ($saldo) {
                return FormatMataUang($saldo->saldo_tunai);
            })
            ->editColumn('saldo_bank', function ($saldo) {
                return FormatMataUang($saldo->saldo_bank);
            })
            ->filter(function ($query) use ($ta) {
                $query->where('ta', '=', $ta);
            },true)
            ->addIndexColumn()
            ->make(true);
        }
    	return view('admin.kas.saldolalu');
    }
}
