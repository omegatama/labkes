<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Saldo;
use App\SaldoAwal;
use App\Pendapatan;
use App\KasTrx;
use Auth;
use Cookie;
use Response;
use DataTables;

class PenerimaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            // $model = Pagu::query();
            $query = Pendapatan::with('sekolah')->select('pendapatans.*');

            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->addColumn('action', function(Pendapatan $pendapatan) {
                $urledit= route('admin.penerimaan.edit', ['id' => $pendapatan->id]);
                $urlhapus= route('admin.penerimaan.destroy', ['id' => $pendapatan->id]);
                
                return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger confirmation", $urlhapus, "Hapus");
            })
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

        return view('admin.penerimaan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.penerimaan.tambah', ['aksi' => "tambah"]);
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
        $npsn= $request->npsn;
        DB::beginTransaction();
        try {
            // Step 1 : Create Pendapatan
            $penerimaan= new Pendapatan;
            $penerimaan->ta = $request->cookie('ta');
            $penerimaan->npsn = $request->npsn;
            if(!empty($request->sumber))
            $penerimaan->sumber = $request->sumber;
            $penerimaan->nominal = floatval(str_replace(",",".",$request->nominal));
            $penerimaan->keterangan = $request->keterangan;
            $penerimaan->tanggal = $request->tanggal;
            $penerimaan->save();

        } catch (\Exception $e){
            DB::rollback();
            return redirect()->route('admin.penerimaan.create')
            ->withErrors(['msg' => 'Oops, ada yang salah! (P1)']);
        }
        
        try {
            // Step 2 : Create Kas
            $transaksi = new KasTrx;
            $transaksi->ta = $penerimaan->ta;
            $transaksi->npsn = $penerimaan->npsn;
            $transaksi->kas = 'B';
            $transaksi->io = 'i';
            $transaksi->nominal = $penerimaan->nominal;
            $transaksi->reference_id = $penerimaan->id;
            $transaksi->tanggal = $penerimaan->tanggal;
            $transaksi->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.penerimaan.create')
            ->withErrors(['msg' => 'Oops, ada yang salah! (P2)']);
        }

        try {
            // Step 3 : Create or Update Saldo
            $saldo = Saldo::firstOrNew(
                [
                    'ta' => $penerimaan->ta,
                    'npsn' => $penerimaan->npsn
                ]
            );

            if ($transaksi->kas=='B')
                $saldo->saldo_bank += $penerimaan->nominal;
            else if ($transaksi->kas=='T')
                $saldo->saldo_tunai += $penerimaan->nominal;

            $saldo->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.penerimaan.create')
            ->withErrors(['msg' => 'Oops, ada yang salah! (P3)']);
        }

        try {
            // Step 4 : Create or Update Saldo AWal
            try {
                $saldoawal = SaldoAwal::where(
                    [
                        'ta' => $penerimaan->ta,
                        'npsn' => $penerimaan->npsn,
                        'periode' => $penerimaan->tanggal->addMonth()->startOfMonth()
                    ]
                )->firstOrFail();

                if ($transaksi->kas=='B')
                    $saldoawal->saldo_bank += $penerimaan->nominal;
                else if ($transaksi->kas=='T')
                    $saldoawal->saldo_tunai += $penerimaan->nominal;
                
                $saldoawal->save();

            } catch (\Exception $e) {
                $saldoawal = new SaldoAwal; 
                $saldoawal->ta = $ta;
                $saldoawal->npsn = $npsn;
                $saldoawal->periode = $penerimaan->tanggal->addMonth()->startOfMonth();
                $saldoawal->saldo_bank = $saldo->saldo_bank;
                $saldoawal->saldo_tunai = $saldo->saldo_tunai;
                $saldoawal->save();
            }


        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.penerimaan.create')
            ->withErrors(['msg' => $e->getMessage().' (P4)']);
        }

        DB::commit(); // All transaction will commit if statement reach on this
        return redirect()->route('admin.penerimaan.index')->with('success','Data Penerimaan berhasil disimpan!');
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
        try {
            $penerimaan = Pendapatan::with('sekolah')->findOrFail($id);

            return view('admin.penerimaan.tambah', [
                'aksi' => "tambah",
                'penerimaan' => $penerimaan
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.penerimaan.index')->withErrors(['msg'=>'Penerimaan tidak ditemukan!']);
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
        try {
            $penerimaan = Pendapatan::with(['sekolah','transaksi'])->findOrFail($id);

            $nominal_lama = $penerimaan->nominal;
            $nominal_baru = floatval(str_replace(',', '.', $request->nominal));
            $selisih = $nominal_baru - $nominal_lama;

            $saldo = Saldo::where(
                [
                    'ta' => $penerimaan->ta,
                    'npsn' => $penerimaan->npsn
                ]
            )->firstOrFail();

            $kas= $penerimaan->transaksi->kas;
            if ($kas=='B') {
                $target= 'saldo_bank';
                $msg= 'Saldo Bank tidak Cukup';
            }

            else if ($kas=='T') {
                $target= 'saldo_tunai';
                $msg= 'Saldo Tunai tidak Cukup';
            }

            // if ($selisih > $saldo->$target) {
            //     return redirect()->route('admin.penerimaan.index')->withErrors(['msg'=> $msg]);
            // }

            // else{

                DB::beginTransaction();
                // Step 1: Update Penerimaan dan Transaksi
                try {
                    $penerimaan->keterangan = $request->keterangan;
                    $penerimaan->nominal += $selisih;
                    $penerimaan->transaksi->nominal += $selisih;
                    $penerimaan->push();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg'=> 'Oops, ada yang salah! (PE-1)']);
                
                }

                // Step 2: Update Saldo
                try {
                    $saldo->$target += $selisih;
                    $saldo->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg'=> 'Oops, ada yang salah! (PE-2)']);
                
                }

                // Step 3: Update SaldoAWal
                try {
                    $saldoawal = SaldoAwal::where(
                        [
                            'ta' => $penerimaan->ta,
                            'npsn' => $penerimaan->npsn,
                            'periode' => $penerimaan->tanggal->addMonth()->startOfMonth()
                        ]
                    )->firstOrFail();
                    $saldoawal->$target += $selisih;
                    $saldoawal->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg'=> 'Oops, ada yang salah! (PE-3)']);
                
                }

                DB::commit();
                return redirect()->route('admin.penerimaan.index')
                    ->with(['success'=> 'Data Penerimaan berhasil diperbarui!']);

            // }
            // return json_encode($penerimaan);

        } catch (\Exception $e) {
            return redirect()->route('admin.penerimaan.index')->withErrors(['msg'=> $e->getMessage()]);
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
        try {
            $penerimaan = Pendapatan::with(['sekolah','transaksi'])->findOrFail($id);
            $saldo = Saldo::where(
                [
                    'ta' => $penerimaan->ta,
                    'npsn' => $penerimaan->npsn
                ]
            )->firstOrFail();

            $ta= $penerimaan->ta;
            $tanggal = $penerimaan->tanggal;
            $kas = $penerimaan->transaksi->kas;
            $npsn = $penerimaan->npsn;
            $nominal = $penerimaan->nominal;

            if ($kas=='B') {
                $target= 'saldo_bank';
                $msg= 'Saldo Bank tidak Cukup';
            }

            else if ($kas=='T') {
                $target= 'saldo_tunai';
                $msg= 'Saldo Tunai tidak Cukup';
            }

            if ($nominal > $saldo->$target) {
                return redirect()->route('admin.penerimaan.index')->withErrors(['msg'=> $msg]);
            }
            else{

                DB::beginTransaction();

                // Step 1: Delete Penerimaan dan Transaksi
                try {
                    $penerimaan->transaksi()->delete();
                    $penerimaan->delete();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg'=> 'Oops, ada yang salah! (PD-1)']);
                
                }

                // Step 2: Update Saldo
                try {
                    $saldo->$target -= $nominal;
                    $saldo->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg'=> 'Oops, ada yang salah! (PD-2)']);
                
                }

                // Step 3: Update Saldo Awal
                try {
                    $saldoawal = SaldoAwal::where(
                        [
                            'ta' => $ta,
                            'npsn' => $npsn,
                            'periode' => $tanggal->addMonth()->startOfMonth()
                        ]
                    )->firstOrFail();
                    $saldoawal->$target -= $nominal;
                    $saldoawal->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg'=> 'Oops, ada yang salah! (PD-3)']);
                
                }

                DB::commit();
                return redirect()->route('admin.penerimaan.index')->with('success','Data Penerimaan berhasil dihapus!');

            }

        } catch (\Exception $e) {
            return redirect()->route('admin.penerimaan.index')->withErrors(['msg'=> $e->getMessage()]);
        }
    }
}
