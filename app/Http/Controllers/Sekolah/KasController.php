<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Cookie;
use Response;
use DataTables;

class KasController extends Controller
{
    public function penerimaanDana_index()
    {
    	if(request()->ajax()) {
            $model = Auth::user()->pendapatans();

            $ta = Cookie::get('ta');
            return DataTables::eloquent($model)
            ->editColumn('tanggal', function ($pendapatan) {
                return $pendapatan->tanggal->format('d/m/Y');
            })
            ->editColumn('nominal', function ($pendapatan) {
                return FormatMataUang($pendapatan->nominal);
            })
            ->filter(function ($query) use ($ta) {
                $query->where('ta', '=', $ta);
            },true)
            ->addIndexColumn()
            ->make(true);
        }
    	return view('sekolah.kas.penerimaan_dana');
    }

    public function saldo_index()
    {
    	if(request()->ajax()) {
            $model = Auth::user()->saldos();
            $ta = Cookie::get('ta');
            return DataTables::eloquent($model)
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
    	return view('sekolah.kas.saldo');
    }

    public function saldolalu_index()
    {
    	if(request()->ajax()) {
            $model = Auth::user()->saldos();
            $ta = Cookie::get('ta') - 1;
            return DataTables::eloquent($model)
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
    	return view('sekolah.kas.saldolalu');
    }
}
