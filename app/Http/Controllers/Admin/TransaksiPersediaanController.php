<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BarangPersediaan;
use App\PersediaanTrx;
use App\StokAwal;
use Auth;
use Cookie;
use Response;
use DataTables;

class TransaksiPersediaanController extends Controller
{
    public function penggunaan_index()
    {
        if(request()->ajax()) {
            $ta= Cookie::get('ta');
            $model = PersediaanTrx::with('persediaan.sekolah');
            return DataTables::eloquent($model)
            ->filter(function ($query) use ($ta) {
                $query->where([
                    ['ta', '=', $ta],
                    ['jenis', '=', 'Usage']
                ]);
            },true)
            ->editColumn('tanggal', function ($trx) {
                return $trx->tanggal->format('d/m/Y');
            })
            
            ->editColumn('persediaan.harga_satuan', function ($trx) {
                return FormatMataUang($trx->persediaan->harga_satuan);
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('admin.persediaan.penggunaan');
    }

    public function penyesuaian_index()
    {
        if(request()->ajax()) {
            $ta= Cookie::get('ta');
            $model= PersediaanTrx::with('persediaan.sekolah');
            return DataTables::eloquent($model)
            ->filter(function ($query) use ($ta) {
                $query->where([
                    ['ta', '=', $ta],
                    ['jenis', '=', 'Adjustment']
                ]);
            },true)
            ->editColumn('tanggal', function ($trx) {
                return $trx->tanggal->format('d/m/Y');
            })
            ->editColumn('io', function ($trx) {
                switch ($trx->io) {
                    case 'i':
                        return "In";
                    case 'o':
                        return "Out";
                    default:
                        return "-";
                }
            })
            
            ->editColumn('persediaan.harga_satuan', function ($trx) {
                return FormatMataUang($trx->persediaan->harga_satuan);
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('admin.persediaan.penyesuaian');
    }

    public function stok_index()
    {
        if(request()->ajax()) {
            $model= BarangPersediaan::with('sekolah');
            return DataTables::eloquent($model)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->editColumn('harga_satuan', function ($trx) {
                return FormatMataUang($trx->harga_satuan);
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('admin.persediaan.stok');
    }
}
