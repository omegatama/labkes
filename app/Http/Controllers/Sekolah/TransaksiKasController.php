<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KasTrxDetail;
use App\KasTrx;
use App\SaldoAwal;
use Auth;
use Cookie;
use Response;
use DataTables;

class TransaksiKasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $query = Auth::user()->kas_trx_details()->with(['sekolah','kas_trx'])->select('kas_trx_details.*');
            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                    ]
                );
            },true)
            ->addColumn('action', function($trx) {
                $urledit= route('sekolah.trxkas.edit', ['id' => $trx->id]);
                $urlhapus= route('sekolah.trxkas.destroy', ['id' => $trx->id]);

                return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger confirmation", $urlhapus, "Hapus");
            })
            ->editColumn('tanggal', function ($trx) {
                return $trx->tanggal->format('d/m/Y');
            })
            ->editColumn('nominal', function ($trx) {
                return FormatMataUang($trx->nominal);
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('sekolah.kas.transaksi.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sekolah.kas.transaksi.tambah',['aksi'=>'tambah']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ta= $request->cookie('ta');
        $npsn= Auth::user()->npsn;
        $saldo= Auth::user()->saldos()->where('ta',$ta)->first();
        $nominal= floatval(str_replace(',', '.', $request->nominal));
        
        if ($request->tipe=='Pindah Buku') {
            $source = "saldo_bank";
            $target = "saldo_tunai";
            $msg = "Saldo Bank Tidak Cukup!";

        }
        else if ($request->tipe=='Setor Kembali') {
            $source = "saldo_tunai";
            $target = "saldo_bank";
            $msg = "Saldo Tunai Tidak Cukup!";

        }
        else {
            $target = NULL;
            $source = NULL;
        }
        if (!empty($target)) {
            if ($nominal > $saldo->$source) {
                return redirect()->back()->withErrors(['msg' => $msg]);
            }
        }

        DB::beginTransaction();
                
        // Step 1: Buat Transaksi Kas Detail
        try {
            $trx = new KasTrxDetail;
            $trx->ta = $ta;
            $trx->npsn = $npsn;
            $trx->tipe = $request->tipe;
            $trx->nominal = $nominal;
            $trx->keterangan = $request->keterangan;
            $trx->tanggal = $request->tanggal;
            $trx->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
            ->withErrors(['msg' => 'Oops, ada yang salah! (Tk-1)']);
        
        }

        // Step 2 : Create Kas
        try {
            $transaksi = new KasTrx;
            $transaksi->ta = $ta;
            $transaksi->npsn = $npsn;
            
            if ($target==NULL) {
                # code...
                $transaksi->kas = 'B';
            }

            $transaksi->nominal = $nominal;
            $transaksi->reference_id = $trx->id;
            $transaksi->tanggal = $request->tanggal;
            $transaksi->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
            ->withErrors(['msg' => 'Oops, ada yang salah! (Tk-2)']);
        
        }

        if ($target!=NULL) {

            // Step 3: Pindahkan Saldo bank ke Saldo tunai pada Data Saldo
            try {
                $saldo->$source -= $nominal;
                $saldo->$target += $nominal;
                $saldo->save();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                ->withErrors(['msg' => 'Oops, ada yang salah! (Tk-3)']);
            
            }

            // Step 4: Update Saldo Awal
            try {
                try {
                    $saldoawal = SaldoAwal::where(
                        [
                            'ta' => $ta,
                            'npsn' => $npsn,
                            'periode' => $trx->tanggal->addMonth()->startOfMonth()
                        ]
                    )->firstOrFail();

                    $saldoawal->$source -= $nominal;
                    $saldoawal->$target += $nominal;
                    $saldoawal->save();

                } catch (\Exception $e) {
                    $saldoawal = new SaldoAwal; 
                    $saldoawal->ta = $ta;
                    $saldoawal->npsn = $npsn;
                    $saldoawal->periode = $trx->tanggal->addMonth()->startOfMonth();
                    $saldoawal->saldo_bank = $saldo->saldo_bank;
                    $saldoawal->saldo_tunai = $saldo->saldo_tunai;
                    $saldoawal->save();
                }
                

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                ->withErrors(['msg' => 'Oops, ada yang salah! (Tk-4)']);
            
            }

        }

        DB::commit(); // All transaction will commit if statement reach on this
        return redirect()->route('sekolah.trxkas.index')->with('success','Data Trx berhasil disimpan!');

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
        $npsn= Auth::user()->npsn;
        
        try {
            $trx = KasTrxDetail::where([
                'id' => $id,
                'npsn' => $npsn
            ])
            ->firstOrFail();
            return view('sekolah.kas.transaksi.tambah',['aksi'=>'edit', 'trx'=>$trx]);
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=>'Maaf Trx tidak ditemukan!']);
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
        $ta= $request->cookie('ta');
        $npsn= Auth::user()->npsn;

        try {
            $trx = KasTrxDetail::where([
                'id' => $id,
                'npsn' => $npsn
            ])
            ->with('kas_trx')
            ->firstOrFail();
            // return json_encode($trx);
            
            $saldo = Auth::user()->saldos()->where('ta',$ta)->first();

            $nominal_lama = $trx->nominal;
            $nominal_baru = floatval(str_replace(',', '.', $request->nominal));
            $selisih = $nominal_baru - $nominal_lama;

            if ($request->tipe=='Pindah Buku') {
                $source = "saldo_bank";
                $target = "saldo_tunai";
                $msg = "Saldo Bank Tidak Cukup!";

            }
            else if ($request->tipe=='Setor Kembali') {
                $source = "saldo_tunai";
                $target = "saldo_bank";
                $msg = "Saldo Tunai Tidak Cukup!";

            }
            else {
                $target = NULL;
                $source = NULL;
            }
            
            if (!empty($target)) {
                if ($selisih > $saldo->$source) {
                    return redirect()->back()->withErrors(['msg' => $msg]);
                }
            }

            DB::beginTransaction();
                
            // Step 1: Update Transaksi Kas Detail dan Kas
            try {

                $trx->nominal += $selisih;
                $trx->keterangan = $request->keterangan;
                $trx->kas_trx->nominal = $trx->nominal;
                $trx->push();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                ->withErrors(['msg' => 'Oops, ada yang salah! (TkE-1)']);
            
            }

            if ($target!=NULL) {

                // Step 2: Pindahkan Saldo bank ke Saldo tunai pada Data Saldo
                try {
                    $saldo->$source -= $selisih;
                    $saldo->$target += $selisih;
                    $saldo->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TkE-2)']);
                
                }

                // Step 3: Update Saldo Awal
                try {
                    try {
                        $saldoawal = SaldoAwal::where(
                            [
                                'ta' => $ta,
                                'npsn' => $npsn,
                                'periode' => $trx->tanggal->addMonth()->startOfMonth()
                            ]
                        )->firstOrFail();

                        $saldoawal->$source -= $selisih;
                        $saldoawal->$target += $selisih;
                        $saldoawal->save();

                    } catch (\Exception $e) {
                        $saldoawal = new SaldoAwal; 
                        $saldoawal->ta = $ta;
                        $saldoawal->npsn = $npsn;
                        $saldoawal->periode = $trx->tanggal->addMonth()->startOfMonth();
                        $saldoawal->saldo_bank = $saldo->saldo_bank;
                        $saldoawal->saldo_tunai = $saldo->saldo_tunai;
                        $saldoawal->save();
                    }
                    

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TkE-3)']);
                
                }

            }

            DB::commit(); // All transaction will commit if statement reach on this
            return redirect()->route('sekolah.trxkas.index')->with('success','Data Trx berhasil diperbarui!');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=>'Maaf Trx tidak ditemukan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $ta = $request->cookie('ta');
        try {
            $trx = Auth::user()->kas_trx_details()
            ->with('kas_trx')
            ->findOrFail($id);

            $saldo = Auth::user()->saldos()->where('ta',$ta)->first();

            $saldoawal = Auth::user()->saldo_awals()->where(
                [
                    'ta' => $ta,
                    'periode' => $trx->tanggal->addMonth()->startOfMonth()
                ]
            )->first();
            
            $nominal = $trx->nominal;
            $tanggal = $trx->tanggal;
            $tipe = $trx->tipe;

            if ($tipe=='Pindah Buku') {
                $source = "saldo_bank";
                $target = "saldo_tunai";
                $msg = "Saldo Bank Tidak Cukup!";

            }
            else if ($tipe=='Setor Kembali') {
                $source = "saldo_tunai";
                $target = "saldo_bank";
                $msg = "Saldo Tunai Tidak Cukup!";

            }
            else {
                $target = NULL;
                $source = NULL;
            }

            if (!empty($target)) {
                if ($nominal > $saldo->$source) {
                    return redirect()->back()->withErrors(['msg' => $msg]);
                }
            }

            DB::beginTransaction();

            // Step 1: Hapus Trx dan Trx Detail
            try {
                $trx->kas_trx()->delete();
                $trx->delete();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('sekolah.trxkas.index')
                ->withErrors(['msg' => 'Oops, ada yang salah! (TkD-1)']);
            }

            if ($target!=NULL) {
                // Step 2: Update Saldo
                try {
                    $saldo->$source += $nominal;
                    $saldo->$target -= $nominal;
                    $saldo->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('sekolah.trxkas.index')
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TkD-2)']);
                
                }

                // Step 3: Update Saldo Awal
                try {
                    $saldoawal->$source += $nominal;
                    $saldoawal->$target -= $nominal;
                    $saldoawal->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('sekolah.trxkas.index')
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TkD-3)']);
                
                }
            }

            // Commit
            DB::commit(); // All transaction will commit if statement reach on this
            return redirect()->route('sekolah.trxkas.index')->with('success','Data Trx berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('sekolah.trxkas.index')->withErrors(['msg'=>$e->getMessage()]);
        }
    }
}
