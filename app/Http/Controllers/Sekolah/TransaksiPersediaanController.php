<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function penggunaan_index()
    {
        if(request()->ajax()) {
            $ta= Cookie::get('ta');
            $npsn= Auth::user()->npsn;
            $model = PersediaanTrx::with('persediaan')->npsn($npsn);
            return DataTables::eloquent($model)
            ->filter(function ($query) use ($ta, $npsn) {
                $query->where([
                    ['ta', '=', $ta],
                    ['jenis', '=', 'Usage']
                ]);
            },true)
            ->editColumn('tanggal', function ($trx) {
                return $trx->tanggal->format('d/m/Y');
            })
            ->addColumn('action', function(PersediaanTrx $trx) {
                $urledit= route('sekolah.trxpersediaan.edit', ['id' => $trx->id]);
                $urlhapus= route('sekolah.trxpersediaan.destroy', ['id' => $trx->id]);                
                return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger confirmation", $urlhapus, "Hapus");
            })
            ->editColumn('persediaan.harga_satuan', function ($trx) {
                return FormatMataUang($trx->persediaan->harga_satuan);
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('sekolah.persediaan.penggunaan');
    }

    public function penyesuaian_index()
    {
        if(request()->ajax()) {
            $ta= Cookie::get('ta');
            $npsn= Auth::user()->npsn;
            $model= PersediaanTrx::with('persediaan')->npsn($npsn);
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
            ->addColumn('action', function(PersediaanTrx $trx) {
                $urledit= route('sekolah.trxpersediaan.edit', ['id' => $trx->id]);
                $urlhapus= route('sekolah.trxpersediaan.destroy', ['id' => $trx->id]); 
                return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger confirmation", $urlhapus, "Hapus");
            })
            ->editColumn('persediaan.harga_satuan', function ($trx) {
                return FormatMataUang($trx->persediaan->harga_satuan);
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('sekolah.persediaan.penyesuaian');
    }

    public function stok_index()
    {
        if(request()->ajax()) {
            $model= Auth::user()->persediaans();
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
        return view('sekolah.persediaan.stok');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $jenis= $request->get('jenis', 'usage');
        $aksi= 'tambah';
        return view('sekolah.persediaan.tambah', compact('jenis','aksi'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $persediaan= Auth::user()->persediaans()
            ->findOrFail($request->barang_persediaan_id);
            // return json_encode($persediaan);

            $ta = $request->cookie('ta');
            $npsn = Auth::user()->npsn;

            if ($request->io=='o') {
                if ($request->qty > $persediaan->stok) {
                    return redirect()->back()->withErrors(['msg'=>'Stok Barang Persediaan Tidak Mencukupi!']);
                }
            }

            DB::beginTransaction();

            // Step 1: Buat Transaksi Persediaan
            try {
                $trx = new PersediaanTrx;
                $trx->ta = $ta;
                $trx->barang_persediaan_id = $request->barang_persediaan_id;
                $trx->io = $request->io;
                $trx->jenis = $request->jenis;
                $trx->qty = $request->qty;
                $trx->keterangan = $request->keterangan;
                $trx->tanggal = $request->tanggal;
                $trx->save();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                ->withErrors(['msg' => 'Oops, ada yang salah! (Tp-1)']);

            }

            // Step 2: Potong Stok
            try {
                if ($request->io=='o') {
                    $persediaan->stok -= $trx->qty;
                }
                else if ($request->io=='i') {
                    $persediaan->stok += $trx->qty;
                }

                $persediaan->save();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                ->withErrors(['msg' => 'Oops, ada yang salah! (Tp-2)']);
            }

            // Step 3: Buat atau Update Stok Awal
            try {
                try {
                    $stokawal = StokAwal::where(
                        [
                            'ta' => $ta,
                            'npsn' => $npsn,
                            'barang_persediaan_id' => $trx->barang_persediaan_id,
                            'periode' => $trx->tanggal->addMonth()->startOfMonth()
                        ]
                    )->firstOrFail();

                    if ($request->io=='o') {
                        $stokawal->stok -= $trx->qty;
                    }
                    else if ($request->io=='i') {
                        $stokawal->stok += $trx->qty;
                    }

                    $stokawal->save();

                } catch (\Exception $e) {
                    $stokawal = new StokAwal;
                    $stokawal->ta = $ta;
                    $stokawal->npsn = $npsn;
                    $stokawal->barang_persediaan_id = $trx->barang_persediaan_id;
                    $stokawal->periode = $trx->tanggal->addMonth()->startOfMonth();
                    $stokawal->stok = $persediaan->stok;
                    $stokawal->save();
                }

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                ->withErrors(['msg' => 'Oops, ada yang salah! (Tp-3)']);

            }

            DB::commit(); // All transaction will commit if statement reach on this
            if ($trx->jenis=='Adjustment') {
                # code...
                return redirect()->route('sekolah.persediaan.penyesuaian')->with('success','Data Trx berhasil disimpan!');
            }
            else if ($trx->jenis=='Usage') {
                # code...
                return redirect()->route('sekolah.persediaan.penggunaan')->with('success','Data Trx berhasil disimpan!');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=> $e->getMessage()]);
        }

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
        $aksi= 'edit';

        try {
            $trx = PersediaanTrx::npsn(Auth::user()->npsn)->with('persediaan')->where('id',$id)->firstOrFail();
            $jenis = strtolower($trx->jenis);
            // return json_encode($trx);
            return view('sekolah.persediaan.tambah', compact('jenis','aksi','trx'));

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=> 'Data Trx tidak ditemukan!']);
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
            $trx = PersediaanTrx::npsn(Auth::user()->npsn)->with('persediaan')->where('id',$id)->firstOrFail();
            $qty_lama = $trx->qty;
            $qty_baru = $request->qty;
            $selisih = $qty_baru - $qty_lama;

            if ($selisih > $trx->persediaan->stok) {
                return redirect()->back()->withErrors(['msg'=> 'Maaf stok barang persediaan terkait tidak cukup!']);
            }
            else{
                DB::beginTransaction();

                // Step 1: Update Trx
                try {
                    $trx->qty += $selisih;
                    $trx->keterangan = $request->keterangan;
                    
                    if ($trx->io=='o') {
                        $trx->persediaan->stok -= $selisih;
                    }
                    else if ($request->io=='i') {
                        $trx->persediaan->stok += $selisih;
                    }

                    $trx->push();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TpE-1)']);
                }

                // Step 2: Update Stok Awal
                try {
                    $stokawal = StokAwal::where(
                        [
                            'ta' => $trx->ta,
                            'npsn' => $trx->persediaan->npsn,
                            'barang_persediaan_id' => $trx->barang_persediaan_id,
                            'periode' => $trx->tanggal->addMonth()->startOfMonth()
                        ]
                    )->firstOrFail();

                    if ($trx->io=='o') {
                        $stokawal->stok -= $selisih;
                    }
                    else if ($trx->io=='i') {
                        $stokawal->stok += $selisih;
                    }

                    $stokawal->save();

                } catch (\Exception $e) {
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TpE-2)']);
                }

                DB::commit();
                if ($trx->jenis=='Adjustment') {
                    return redirect()->route('sekolah.persediaan.penyesuaian')->with('success','Data Trx berhasil diperbarui!');
                }
                else if ($trx->jenis=='Usage') {
                    return redirect()->route('sekolah.persediaan.penggunaan')->with('success','Data Trx berhasil diperbarui!');
                }

            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=> $e->getMessage()]);
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
            $trx = PersediaanTrx::npsn(Auth::user()->npsn)->with('persediaan')->where('id',$id)->firstOrFail();
            $qty = $trx->qty;
            $io = $trx->io;
            $ta = $trx->ta;
            $npsn = $trx->persediaan->npsn;
            $jenis = $trx->jenis;
            $tanggal = $trx->tanggal;
            $barang_persediaan_id = $trx->barang_persediaan_id;
            // return json_encode($trx);

            if ($qty > $trx->persediaan->stok) {
                return redirect()->back()->withErrors(['msg'=> 'Maaf stok barang persediaan terkait tidak cukup!']);
            }
            else{

                DB::beginTransaction();

                // Step 1: Update Persediaan
                try {
                
                    if ($io=='o') {
                        // $persediaan = BarangPersediaan::find($barang_persediaan_id);
                        // $persediaan->stok += $qty;
                        // $persediaan->save();
                        $trx->persediaan->stok += $qty;
                    }
                    else if ($io=='i') {
                        // $persediaan = BarangPersediaan::find($barang_persediaan_id);
                        // $persediaan->stok -= $qty;
                        // $persediaan->save();
                        $trx->persediaan->stok -= $qty;
                    }
                    $trx->push();
                    $trx->delete();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TpD-1)']);
                }

                // return json_encode($npsn);

                // Step 2: Update Stok Awal
                try {
                    $stokawal = StokAwal::where(
                        [
                            'ta' => $ta,
                            'npsn' => $npsn,
                            'barang_persediaan_id' => $barang_persediaan_id,
                            'periode' => $tanggal->addMonth()->startOfMonth()->format('Y-m-d')
                        ]
                    )->firstOrFail();
                    
                    if ($io=='o') {
                        $stokawal->stok += $qty;
                    }
                    else if ($io=='i') {
                        $stokawal->stok -= $qty;
                    }

                    $stokawal->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (TpD-2)']);
                }

                DB::commit();
                if ($jenis=='Adjustment') {
                    return redirect()->route('sekolah.persediaan.penyesuaian')->with('success','Data Trx berhasil dihapus!');
                }
                else if ($jenis=='Usage') {
                    return redirect()->route('sekolah.persediaan.penggunaan')->with('success','Data Trx berhasil dihapus!');
                }

            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=> $e->getMessage()]);
        }
    }
}
