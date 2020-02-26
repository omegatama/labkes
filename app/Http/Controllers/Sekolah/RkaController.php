<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rka;
use App\RkaLimit;
use App\Pagu;
use Auth;
use Cookie;
use Response;
use DataTables;

class RkaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $query = Auth::user()->rkas()->with(['sekolah','program','kegiatan','kp','rekening'])->select('rkas.*');
            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                        // 'npsn' => $npsn
                    ]
                );
            },true)
            ->addColumn('action', function(Rka $rka) {
                $urledit= route('sekolah.rka.edit', ['id' => $rka->id]);
                $urlhapus= route('sekolah.rka.destroy', ['id' => $rka->id]);
                
                return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger confirmation", $urlhapus, "Hapus");
            })
            ->editColumn('harga_satuan', '{{FormatMataUang($harga_satuan)}}')
            ->editColumn('jumlah', '{{FormatMataUang($jumlah)}}')
            
            ->editColumn('alokasi_tw1', '{{FormatMataUang($alokasi_tw1)}}')
            ->editColumn('alokasi_tw2', '{{FormatMataUang($alokasi_tw2)}}')
            ->editColumn('alokasi_tw3', '{{FormatMataUang($alokasi_tw3)}}')
            ->editColumn('alokasi_tw4', '{{FormatMataUang($alokasi_tw4)}}')
            ->editColumn('rekening.nama_rekening', function(Rka $rka) {
                return 
                    $rka->rekening->parent->kode_rekening.".".
                    $rka->rekening->kode_rekening." - ".
                    $rka->rekening->nama_rekening;
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('sekolah.rka.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sekolah.rka.tambah', ['aksi' => "tambah"]);
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
        $npsn = Auth::user()->npsn;
        $pagu = Auth::user()->pagus->where('ta',$ta)->first();

        $harga_satuan = floatval(str_replace(",",".",$request->harga_satuan));
        $jumlah = floatval(str_replace(",",".",$request->jumlah));
        $alokasi_tw1 = floatval(str_replace(",",".",$request->alokasi_tw1));
        $alokasi_tw2 = floatval(str_replace(",",".",$request->alokasi_tw2));
        $alokasi_tw3 = floatval(str_replace(",",".",$request->alokasi_tw3));
        $alokasi_tw4 = floatval(str_replace(",",".",$request->alokasi_tw4));
        
        if ($jumlah == ($alokasi_tw1 + $alokasi_tw2 + $alokasi_tw3 + $alokasi_tw4)) {
            // return json_encode($pagus);
            /*if (
                ($alokasi_tw1 + $pagu->penggunaan_tw1) <= (0.2 * $pagu->pagu) &&
                ($alokasi_tw2 + $pagu->penggunaan_tw2) <= (0.4 * $pagu->pagu) &&
                ($alokasi_tw3 + $pagu->penggunaan_tw3) <= (0.2 * $pagu->pagu) &&
                ($alokasi_tw4 + $pagu->penggunaan_tw4) <= (0.2 * $pagu->pagu)    
            )*/ 
            
            // if( ($jumlah + $pagu->penggunaan_tw1 + $pagu->penggunaan_tw2 + $pagu->penggunaan_tw3 + $pagu->penggunaan_tw4) <= $pagu->pagu )
            
            if($jumlah <= $pagu->sisa)
            {
                # code...
                DB::beginTransaction();
                try {
                    // Step 1: Create RKA
                    $rka= new Rka;
                    $rka->ta= $ta;
                    $rka->npsn= $npsn;
                    $rka->kode_program_id= $request->kode_program_id;
                    $rka->kegiatan_id= $request->kegiatan_id;
                    $rka->komponen_pembiayaan_id= $request->komponen_pembiayaan_id;
                    $rka->kode_rekening_id= $request->kode_rekening_id;
                    $rka->uraian= $request->uraian;
                    $rka->volume= $request->volume;
                    $rka->satuan= $request->satuan;
                    $rka->harga_satuan= $harga_satuan;
                    $rka->jumlah= $jumlah;
                    $rka->alokasi_tw1= $alokasi_tw1;
                    $rka->alokasi_tw2= $alokasi_tw2;
                    $rka->alokasi_tw3= $alokasi_tw3;
                    $rka->alokasi_tw4= $alokasi_tw4;
                    $rka->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('sekolah.rka.create')
                    ->withErrors(['msg' => 'Oops, ada yang salah! (R1)']);
                }

                try {
                    // Step 2: Update Penggunaan Pagu
                    $pagu->penggunaan_tw1 += $rka->alokasi_tw1;
                    $pagu->penggunaan_tw2 += $rka->alokasi_tw2;
                    $pagu->penggunaan_tw3 += $rka->alokasi_tw3;
                    $pagu->penggunaan_tw4 += $rka->alokasi_tw4;
                    $pagu->sisa -= $jumlah;
                    $pagu->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('sekolah.rka.create')
                    ->withErrors(['msg' => 'Oops, ada yang salah! (R2)']);
                }

                DB::commit(); // All transaction will commit if statement reach on this
                return redirect()->route('sekolah.rka.index')->with('success','Data RKA berhasil disimpan!');

            }
            else{
                return redirect()->back()->withErrors(['msg' => 'Jumlah melebihi Pagu']);
            }
        }
        else{
            return redirect()->back()->withErrors(['msg' => 'Jumlah dan Alokasi Triwulan belum sesuai']);
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
        try {
            $rka= Auth::user()->rkas()->findOrFail($id);  
            return view('sekolah.rka.tambah', 
                [
                    'aksi' => "edit",
                    'rka' => $rka,
                ]
            );
        } catch (\Exception $e) {
            return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak ditemukan']);
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
        $pagu = Auth::user()->pagus->where('ta',$ta)->first();
        $rka= Auth::user()->rkas()->find($id);
        
        if (!empty($rka)) {
            $npsn= $rka->npsn;
            $harga_satuan_lama  = $rka->harga_satuan;
            $jumlah_lama        = $rka->jumlah;
            $alokasi_tw1_lama   = $rka->alokasi_tw1;
            $alokasi_tw2_lama   = $rka->alokasi_tw2;
            $alokasi_tw3_lama   = $rka->alokasi_tw3;
            $alokasi_tw4_lama   = $rka->alokasi_tw4;

            $harga_satuan_baru = floatval(str_replace(",",".",$request->harga_satuan));
            $jumlah_baru = floatval(str_replace(",",".",$request->jumlah));
            $alokasi_tw1_baru = floatval(str_replace(",",".",$request->alokasi_tw1));
            $alokasi_tw2_baru = floatval(str_replace(",",".",$request->alokasi_tw2));
            $alokasi_tw3_baru = floatval(str_replace(",",".",$request->alokasi_tw3));
            $alokasi_tw4_baru = floatval(str_replace(",",".",$request->alokasi_tw4));

            $selisih_harga_satuan = $harga_satuan_baru - $harga_satuan_lama;
            $selisih_jumlah = $jumlah_baru - $jumlah_lama; 
            $selisih_alokasi_tw1 = $alokasi_tw1_baru - $alokasi_tw1_lama;
            $selisih_alokasi_tw2 = $alokasi_tw2_baru - $alokasi_tw2_lama;
            $selisih_alokasi_tw3 = $alokasi_tw3_baru - $alokasi_tw3_lama;
            $selisih_alokasi_tw4 = $alokasi_tw4_baru - $alokasi_tw4_lama;

            if (
                ($selisih_alokasi_tw1 + $pagu->penggunaan_tw1) <= (0.2 * $pagu->pagu) &&
                ($selisih_alokasi_tw2 + $pagu->penggunaan_tw2) <= (0.4 * $pagu->pagu) &&
                ($selisih_alokasi_tw3 + $pagu->penggunaan_tw3) <= (0.2 * $pagu->pagu) &&
                ($selisih_alokasi_tw4 + $pagu->penggunaan_tw4) <= (0.2 * $pagu->pagu)    
            ) {
                DB::beginTransaction();
                try {
                    // Step 1: Update RKA
                    $rka->uraian = $request->uraian;
                    $rka->volume = $request->volume;
                    $rka->satuan = $request->satuan;

                    $rka->harga_satuan += $selisih_harga_satuan;
                    $rka->jumlah += $selisih_jumlah;
                    $rka->alokasi_tw1 += $selisih_alokasi_tw1;
                    $rka->alokasi_tw2 += $selisih_alokasi_tw2;
                    $rka->alokasi_tw3 += $selisih_alokasi_tw3;
                    $rka->alokasi_tw4 += $selisih_alokasi_tw4;
                    $rka->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RE-1)']);
                
                }

                try {
                    // Step 2: Update Pagu
                    $pagu->penggunaan_tw1 += $selisih_alokasi_tw1;
                    $pagu->penggunaan_tw2 += $selisih_alokasi_tw2;
                    $pagu->penggunaan_tw3 += $selisih_alokasi_tw3;
                    $pagu->penggunaan_tw4 += $selisih_alokasi_tw4;
                    $pagu->sisa -= $selisih_jumlah;
                    $pagu->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RE-2)']);
                
                }


                DB::commit(); // All transaction will commit if statement reach on this
                return redirect()->route('sekolah.rka.index')->with('success','Data RKA berhasil diperbarui!');
                
            }
            else{
                return redirect()->back()->withErrors(['msg' => 'Alokasi Triwulan melebihi Pagu']);
            }
            
        }
        else{
            return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak ditemukan']);
        }
                
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $rka = Auth::user()->rkas()->findOrFail($id);

            DB::beginTransaction();
            try {
                // Step 1: Get required info then Delete RKA
                $ta = $rka->ta;
                $npsn = $rka->npsn;
                $kode_rekening_id = $rka->kode_rekening_id;
                $pagu = Auth::user()->pagus->where('ta',$ta)->first();

                $alokasi_tw1 = $rka->alokasi_tw1;
                $alokasi_tw2 = $rka->alokasi_tw2;
                $alokasi_tw3 = $rka->alokasi_tw3;
                $alokasi_tw4 = $rka->alokasi_tw4;
                $jumlah      = $rka->jumlah;

                $rka->delete();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RD-1)']);

            }

            try {
                // Step 2: Update Alokasi dan Sisa Pagu
                $pagu->penggunaan_tw1 -= $alokasi_tw1;
                $pagu->penggunaan_tw2 -= $alokasi_tw2;
                $pagu->penggunaan_tw3 -= $alokasi_tw3;
                $pagu->penggunaan_tw4 -= $alokasi_tw4;
                $pagu->sisa += $jumlah;
                $pagu->save();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RD-2)']);

            }

            DB::commit(); // All transaction will commit if statement reach on this
            return redirect()->route('sekolah.rka.index')->with('success','Data RKA berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak ditemukan']);
        }
    }
}
