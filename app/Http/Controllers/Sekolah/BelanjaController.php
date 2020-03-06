<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Belanja;
use App\KasTrx;
use App\Saldo;
use App\SaldoAwal;
use Auth;
use Cookie;
use Response;
use DataTables;

class BelanjaController extends Controller
{
    public function index()
    {
    	if(request()->ajax()) {
            $ta = Cookie::get('ta');
    		$query = Auth::user()->belanjas()->ta($ta)->with('rka.rekening','rka.program','rka.kp','rka.kegiatan');

            return DataTables::eloquent($query)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->editColumn('tanggal', function ($belanja) {
                return $belanja->tanggal->format('d/m/Y');
            })
            ->editColumn('nilai', function ($belanja) {
                return FormatMataUang($belanja->nilai);
            })
            ->editColumn('rka.rekening.nama_rekening', function(Belanja $belanja) {
                return 
                    $belanja->rka->rekening->parent->kode_rekening.".".
                    $belanja->rka->rekening->kode_rekening." - ".
                    $belanja->rka->rekening->nama_rekening;
            })
            ->editColumn('rka.program.nama_program', function(Belanja $belanja) {
                return 
                    $belanja->rka->program->kode_program." - ".
                    $belanja->rka->program->nama_program;
            })
            ->editColumn('rka.kp.nama_komponen', function(Belanja $belanja) {
                return 
                    $belanja->rka->kp->kode_komponen." - ".
                    $belanja->rka->kp->nama_komponen;
            })
            ->addColumn('action', function($belanja) {
                $urledit= route('sekolah.belanja.edit', ['id' => $belanja->id]);
                $urlhapus= route('sekolah.belanja.destroy', ['id' => $belanja->id]);
                $urla2= route('sekolah.belanja.a2', ['id' => $belanja->id]);
                $jenisrekening= $belanja->rka->rekening->jenis; //1: Belanja Modal //2: Belanja Persediaan

                switch ($jenisrekening) {
                	case '1':
                		$urltambahan = route('sekolah.belanja.modal', ['id' => $belanja->id]);
                		$labeltambahan = "Belanja Modal";
                		break;

                	case '2':
                		$urltambahan = route('sekolah.belanja.persediaan', ['id' => $belanja->id]);
                		$labeltambahan = "Belanja Persediaan";
                		break;
                	
                	default:
                		$urltambahan = "";
                		break;
                }

                $btnaction =
	                RenderTombol("success", $urledit, "Edit")." ".
	                RenderTombol("danger confirmation", $urlhapus, "Hapus")." ".
	                RenderTombol("info", $urla2, "Download A2")." ";

	            $btnaction .= (!empty($urltambahan)) ? RenderTombol("warning", $urltambahan, $labeltambahan) : "" ;
	            return $btnaction;
            })
            ->addIndexColumn()
            ->make(true);
    	}

    	return view('sekolah.belanja.index');
    }

    public function create(){
    	$ta = Cookie::get('ta');
    	$rka = Auth::user()->rkas()->where('ta','=',$ta)->get();
    	$aksi= 'tambah';
    	return view('sekolah.belanja.tambah', compact('aksi','rka'));
    }

    public function store(Request $request){
    	// return json_encode($request->input());
    	$ta = $request->cookie('ta');
    	$saldo = Auth::user()->saldos()->where('ta', '=', $ta)->firstOrFail();
    	$kas = $request->kas;
    	$nominal = floatval(str_replace(',', '.', $request->nominal));
    	$tanggal = $request->tanggal;
    	$triwulan = GetTriwulan($tanggal);

    	$rka_id= $request->rka_id;
    	$rka= Auth::user()->rkas()->where(
    		[
    			'id' => $rka_id,
    			'ta' => $ta
    		]
    	)->firstOrFail();

    	if ($kas=='B') {
    		$source = 'saldo_bank';
    		$msg = 'Maaf Saldo Bank tidak cukup';
    	}
    	else if ($kas=='T') {
    		$source = 'saldo_tunai';
    		$msg = 'Maaf Saldo Tunai tidak cukup';
    	}

    	if ($nominal > $saldo->$source) {
    		return redirect()->back()->withErrors($msg." saldo:" .$saldo->$source. " nominal ".$nominal);
    	}
    	else{
    		$alokasi_triwulan= 'alokasi_tw'.$triwulan;
    		$realisasi_triwulan= 'realisasi_tw'.$triwulan;

    		if ($rka->$realisasi_triwulan + $nominal > $rka->$alokasi_triwulan) {
    			return redirect()->back()->withErrors('Alokasi Triwulan '.$triwulan.' untuk RKA terkait tidak cukup!');
    		}

    		else{
    			//di sini
    			// return json_encode($request->input());
    			DB::beginTransaction();

    			// Step 1: Simpan Data Belanja
    			try {
    				$belanja= new Belanja;
    				$belanja->triwulan = $triwulan;
    				$belanja->rka_id = $rka_id;
    				$belanja->nama = $request->nama;
    				$belanja->nilai = $nominal;
    				$belanja->kas = $kas;
    				$belanja->nomor = $request->nomor;
    				$belanja->penerima = $request->penerima;
    				$belanja->tanggal = $tanggal;
    				Auth::user()->belanjas()->save($belanja);

    			} catch (\Exception $e) {
    				DB::rollback();
    				return redirect()->back()->withErrors('Step 1: '.$e->getMessage());	
    			}

    			// Step 2: Buat Trx
    			try {
		            $transaksi = new KasTrx;
		            $transaksi->ta = $ta;
		            // $transaksi->npsn = $belanja->npsn;
		            $transaksi->kas = $kas;
		            $transaksi->io = 'o';
		            $transaksi->nominal = $belanja->nilai;
		            $transaksi->reference_id = $belanja->id;
		            $transaksi->tanggal = $belanja->tanggal;
		            // $transaksi->save();
		            Auth::user()->kas_trxs()->save($transaksi);

    			} catch (\Exception $e) {
    				DB::rollback();
    				return redirect()->back()->withErrors('Step 2: '.$e->getMessage());
    			}

    			// Step 3: Update Saldo
    			try {
    				$saldo->$source -= $nominal;
    				$saldo->save();

    			} catch (\Exception $e) {
    				DB::rollback();
    				return redirect()->back()->withErrors('Step 3: '.$e->getMessage());
    			}

    			// Step 4: Update Saldo Awal
    			try {
    				try {
    					$saldoawal = Auth::user()->saldo_awals()
    					->where(
		                    [
		                        'ta' => $transaksi->ta,
		                        'periode' => $transaksi->tanggal->addMonth()->startOfMonth()
		                    ]
		                )->firstOrFail();
		                $saldoawal->$source -= $nominal;
    					$saldoawal->save();

    				} catch (\Exception $e) {
    					$saldoawal = new SaldoAwal; 
		                $saldoawal->ta = $ta;
		                // $saldoawal->npsn = $belanja->npsn;
		                $saldoawal->periode = $transaksi->tanggal->addMonth()->startOfMonth();
		                $saldoawal->saldo_bank = $saldo->saldo_bank;
		                $saldoawal->saldo_tunai = $saldo->saldo_tunai;
		                // $saldoawal->save();
		                Auth::user()->saldo_awals()->save($saldoawal);
    				}
    				

    			} catch (\Exception $e) {
    				DB::rollback();
    				return redirect()->back()->withErrors('Step 4: '.$e->getMessage());
    			}

    			// Step 5:
    			try {
    				$rka->$realisasi_triwulan += $nominal;
    				$rka->save();

    			} catch (\Exception $e) {
    				DB::rollback();
    				return redirect()->back()->withErrors('Step 5: '.$e->getMessage());
    			}

    			DB::commit();
    			return redirect()->route('sekolah.belanja.index')->with(['success' => 'Belanja Berhasil disimpan!']);
    		}
    	}
    }

    public function edit()
    {
    	# code...
    }

    public function destroy()
    {
    	# code...
    }

    public function a2(Request $request, $id)
    {
    	try {
    		$ta = $request->cookie('ta');
    		$belanja= Auth::user()->belanjas()->ta($ta)
    		/*->where(
    			[
    				'' => ''
    			]
    		)
    		->firstOrFail();*/
    		->findOrFail($id);

    	} catch (\Exception $e) {
    		return redirect()->back()->withErrors('Data Belanja Tidak ditemukan!');
    	}
    }

    public function modal($id)
    {
    	$belanja = Auth::user()->belanjas()->modal()->findOrFail($id);
    	return view('sekolah.belanja.modal.detail', compact('belanja'));
    }

    public function createmodal()
    {
    	$aksi="tambah";
    	return view('sekolah.belanja.modal.tambah', compact('aksi'));
    }

    public function getmodal($id)
    {
    	# code...
    }

    public function persediaan($id)
    {
    	$belanja = Auth::user()->belanjas()->persediaan()->findOrFail($id);
    	return view('sekolah.belanja.persediaan.detail', compact('belanja'));
    }

    public function getpersediaan($id)
    {
    	# code...
    }
}