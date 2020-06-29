<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Belanja;
use App\SaldoAwal;
use Carbon\Carbon;
use Auth;
use Cookie;
use Response;
use DataTables;

class SaldoAwalController extends Controller
{
    public function index()
    {
    	/*if(request()->ajax()) {
    		$query = Auth::user()->saldo_awals();
            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                    ]
                );
            },true)
            ->editColumn('saldo_bank', function ($sa) {
                return FormatMataUang($sa->saldo_bank);
            })
            ->editColumn('saldo_tunai', function ($sa) {
                return FormatMataUang($sa->saldo_tunai);
            })
            ->editColumn('periode', function ($sa) {
                return $sa->periode->format('d-m-Y');
            })
            ->addColumn('action', function($sa) {
                $urlhitung= route('sekolah.saldoawal.hitung', ['id' => $sa->id]);

                return RenderTombol("success", $urlhitung, "Hitung Ulang");
                // return "-";
            })
            ->addIndexColumn()
            ->make(true);
    	}*/
        $ta = Cookie::get('ta');
        $saldoawal= Auth::user()->saldo_awals()->where('ta',$ta)->get();
    	return view('sekolah.kas.saldoawalv2', compact('saldoawal'));
    }

    public function hitung(Request $request, $id)
    {
    	$sekolah = Auth::user();
        $sa= $sekolah->saldo_awals()->findOrFail($id);
        $ta= $request->cookie('ta');
        // return json_encode($sa);

        $bulan = $sa->periode->format('n');
        $fromDate = Carbon::createFromDate($ta, $bulan-1, 1)->format('Y-m-d');
        $tillDate = Carbon::createFromDate($ta, $bulan-1, 1)->endOfMonth()->format('Y-m-d');

        if (($bulan-1)==1) {
            $saldo_awal = $sekolah->pendapatans()
            ->where('sumber', 'SILPA BOS')
            ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
            $saldoawal_bank = $saldo_awal->sum('nominal');
            $saldoawal_tunai = 0;

            $pendapatan_sebulan = $sekolah->pendapatans()
            ->with('transaksi')
            ->where('sumber','!=', 'SILPA BOS')
            ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
            // return $pendapatan_sebulan;
        }

        else{
            $saldo_awal = $sekolah->saldo_awals()
            ->where('periode', $fromDate)->get();
            // return $saldo_awal;
            $sa_bulan_ini = $sekolah->saldo_awals()
            ->where('periode', $sa->periode)->get();
            // return $sa_bulan_ini;
            if ($sa_bulan_ini->count()>1) {
                $sekolah->saldo_awals()
                ->where('periode', $sa->periode)
                ->where('id', '!=', $sa->id)
                ->first()->delete();
            }

            if ($saldo_awal->count()>1) {
                $sekolah->saldo_awals()
                ->where('periode', $fromDate)
                ->first()->delete();
            }

            $saldoawal_bank = $saldo_awal->sum('saldo_bank');
            $saldoawal_tunai = $saldo_awal->sum('saldo_tunai');

            $pendapatan_sebulan = $sekolah->pendapatans()
            ->with('transaksi')
            ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
        }

        $belanja_sebulan = Belanja::with('transaksi')->npsn($sekolah->npsn)->whereBetween('tanggal',[$fromDate, $tillDate])->get();
        // return $belanja_sebulan;

        $pendapatan_bank = $pendapatan_sebulan
        ->where('transaksi.kas','B')->sum('nominal');
        $pendapatan_tunai = $pendapatan_sebulan
        ->where('transaksi.kas','T')->sum('nominal');

        $belanja_bank = $belanja_sebulan
        ->where('transaksi.kas','B')->sum('nilai');
        $belanja_tunai = $belanja_sebulan
        ->where('transaksi.kas','T')->sum('nilai');
        // return $belanja_tunai;

        $transaksi_sebulan = $sekolah->kas_trx_details()
        ->with('kas_trx')
        ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
        // return $transaksi_sebulan;
        $setor= $transaksi_sebulan->where('tipe','Setor Kembali')->sum('nominal');
        // Pindah Buku
        $tarik= $transaksi_sebulan->where('tipe','Pindah Buku')->sum('nominal');


        $saldoakhir_bank= ($saldoawal_bank + $pendapatan_bank) - $belanja_bank - $tarik + $setor;
        $saldoakhir_tunai= ($saldoawal_tunai + $pendapatan_tunai) - $belanja_tunai - $setor + $tarik;
        // return $tarik;
        try {
            $sa->saldo_bank = $saldoakhir_bank;
            $sa->saldo_tunai = $saldoakhir_tunai;
            $sa->save();
            // return $sa;
            return redirect()->route('sekolah.saldoawal.index')->with(['success' => 'Berhasil Update Saldo Awal']);
            
        } catch (\Exception $e) {
            return redirect()->route('sekolah.saldoawal.index')->withErrors('Error :'. $e->getMessage());
        }
    }

    public function kalkulasi(Request $request, $periode)
    {
        $sekolah = Auth::user();
        $ta= $request->cookie('ta');
        // return json_encode($sa);

        $bulan = Carbon::createFromFormat('Y-m-d', $periode)->format('n');
        $fromDate = Carbon::createFromDate($ta, $bulan-1, 1)->format('Y-m-d');
        $tillDate = Carbon::createFromDate($ta, $bulan-1, 1)->endOfMonth()->format('Y-m-d');

        if (($bulan-1)==1) {
            $saldo_awal = $sekolah->pendapatans()
            ->where('sumber', 'SILPA BOS')
            ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
            $saldoawal_bank = $saldo_awal->sum('nominal');
            $saldoawal_tunai = 0;

            $pendapatan_sebulan = $sekolah->pendapatans()
            ->with('transaksi')
            ->where('sumber','!=', 'SILPA BOS')
            ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
            // return $pendapatan_sebulan;
        }

        else{
            $saldo_awal = $sekolah->saldo_awals()
            ->where('periode', $fromDate)->get();
            $saldoawal_bank = $saldo_awal->sum('saldo_bank');
            $saldoawal_tunai = $saldo_awal->sum('saldo_tunai');

            $pendapatan_sebulan = $sekolah->pendapatans()
            ->with('transaksi')
            ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
        }

        $belanja_sebulan = Belanja::with('transaksi')->npsn($sekolah->npsn)->whereBetween('tanggal',[$fromDate, $tillDate])->get();
        // return $belanja_sebulan;

        $pendapatan_bank = $pendapatan_sebulan
        ->where('transaksi.kas','B')->sum('nominal');
        $pendapatan_tunai = $pendapatan_sebulan
        ->where('transaksi.kas','T')->sum('nominal');

        $belanja_bank = $belanja_sebulan
        ->where('transaksi.kas','B')->sum('nilai');
        $belanja_tunai = $belanja_sebulan
        ->where('transaksi.kas','T')->sum('nilai');
        // return $belanja_tunai;

        $transaksi_sebulan = $sekolah->kas_trx_details()
        ->with('kas_trx')
        ->whereBetween('tanggal',[$fromDate, $tillDate])->get();
        // return $transaksi_sebulan;
        $setor= $transaksi_sebulan->where('tipe','Setor Kembali')->sum('nominal');
        // Pindah Buku
        $tarik= $transaksi_sebulan->where('tipe','Pindah Buku')->sum('nominal');


        $saldoakhir_bank= ($saldoawal_bank + $pendapatan_bank) - $belanja_bank - $tarik + $setor;
        $saldoakhir_tunai= ($saldoawal_tunai + $pendapatan_tunai) - $belanja_tunai - $setor + $tarik;
        // return $tarik;
        try {
            $sa = new SaldoAwal();
            $sa->npsn = $sekolah->npsn;
            $sa->ta = $ta;
            $sa->periode = $periode;
            $sa->saldo_bank = $saldoakhir_bank;
            $sa->saldo_tunai = $saldoakhir_tunai;
            $sa->save();
            return redirect()->route('sekolah.saldoawal.index')->with(['success' => 'Berhasil Update Saldo Awal']);
            
        } catch (\Exception $e) {
            return redirect()->route('sekolah.saldoawal.index')->withErrors('Error :'. $e->getMessage());
        }
    }
}
