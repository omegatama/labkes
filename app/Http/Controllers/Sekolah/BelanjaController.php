<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Belanja;
use App\BelanjaModal;
use App\BelanjaPersediaan;
use App\PersediaanTrx;
use App\KasTrx;
use App\Saldo;
use App\SaldoAwal;
use App\StokAwal;
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
    		$query = Auth::user()->belanjas()->ta($ta)->with('rka.rekening','rka.program','rka.kp','rka.kegiatan')->select('belanjas.*');

            return DataTables::eloquent($query)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->editColumn('tanggal', function ($belanja) {
                return $belanja->tanggal->locale('id_ID')->isoFormat('LL');
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

                $periode_awal= Auth::user()->periode_awal;
                $periode_akhir= Auth::user()->periode_akhir;
                
                $btndasar = RenderTombol("success", $urledit, "Edit")." ".
                RenderTombol("danger confirmation", $urlhapus, "Hapus")." ";
                
                if (isset($periode_awal) && isset($periode_akhir)) {
                    if (
                        $belanja->tanggal < $periode_awal ||
                        $belanja->tanggal > $periode_akhir
                    ) {
                        $btndasar = "";
                    }
                }

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
                    $btndasar.
	                RenderTombol("info", $urla2, "Download A2")." ";

	            $btnaction .= (!empty($urltambahan)) ? RenderTombol("warning", $urltambahan, $labeltambahan) : "" ;
	            return $btnaction;
            })
            ->setRowClass(function ($belanja) {
                return $belanja->keterangan == -1 ? 'bg-red text-white' : '';
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
        $ppn = floatval(str_replace(',', '.', $request->ppn));
        $pph21 = floatval(str_replace(',', '.', $request->pph21));
        $pph23 = floatval(str_replace(',', '.', $request->pph23));
        

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
            // $alokasi_triwulan= 'alokasi_tw'.$triwulan;
            $realisasi_triwulan= 'realisasi_tw'.$triwulan;
            $total_sisa_sampai_tw= 0;
            for ($i=1; $i <= $triwulan; $i++) { 
    		    $alokasi_tw= 'alokasi_tw'.$i;
                $realisasi_tw= 'realisasi_tw'.$i;
                $sisa_tw= $rka->$alokasi_tw - $rka->$realisasi_tw;
                $total_sisa_sampai_tw += $sisa_tw;
            }

    		/*if ($rka->$realisasi_triwulan + $nominal > $rka->$alokasi_triwulan) {
    			return redirect()->back()->withErrors('Alokasi Triwulan '.$triwulan.' untuk RKA terkait tidak cukup!');
    		}*/
            //cek dulu
            if ($nominal > $total_sisa_sampai_tw) {
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
                    $belanja->ppn = $ppn;
                    $belanja->pph21 = $pph21;
                    $belanja->pph23 = $pph23;
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
		                        'periode' => $transaksi->tanggal->addMonthsNoOverflow()->startOfMonth()
		                    ]
		                )->firstOrFail();
		                $saldoawal->$source -= $nominal;
    					$saldoawal->save();

    				} catch (\Exception $e) {
    					$saldoawal = new SaldoAwal; 
		                $saldoawal->ta = $ta;
		                // $saldoawal->npsn = $belanja->npsn;
		                $saldoawal->periode = $transaksi->tanggal->addMonthsNoOverflow()->startOfMonth();
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

    public function edit(Request $request, $id)
    {
        $belanja= Auth::user()->belanjas()->findOrFail($id);
    	$ta = Cookie::get('ta');
        $rka = Auth::user()->rkas()->where('ta','=',$ta)->get();
        $aksi= 'edit';
        return view('sekolah.belanja.tambah', compact('aksi','rka','belanja'));
    }

    public function update(Request $request, $id)
    {
        $belanja= Auth::user()->belanjas()->with('transaksi')->findOrFail($id);
        $ta = Cookie::get('ta');
        $kas = $belanja->kas;

        $tanggal = $belanja->tanggal;
        $triwulan = GetTriwulan($tanggal);

        $saldo = Auth::user()->saldos()->where('ta', '=', $ta)->firstOrFail();
        $nominal_lama = $belanja->nilai;
        $nominal_baru = floatval(str_replace(',', '.', $request->nominal));
        $selisih = $nominal_baru - $nominal_lama;

        $ppn = floatval(str_replace(',', '.', $request->ppn));
        $pph21 = floatval(str_replace(',', '.', $request->pph21));
        $pph23 = floatval(str_replace(',', '.', $request->pph23));
        
        $rka_id= $belanja->rka_id;
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

        if ($selisih > $saldo->$source) {
            return redirect()->back()->withErrors($msg." saldo:" .$saldo->$source. " selisih nominal ".$selisih);
        }

        else {
            $alokasi_triwulan= 'alokasi_tw'.$triwulan;
            $realisasi_triwulan= 'realisasi_tw'.$triwulan;

            if ($rka->$realisasi_triwulan + $selisih > $rka->$alokasi_triwulan) {
                return redirect()->back()->withErrors('Alokasi Triwulan '.$triwulan.' untuk RKA terkait tidak cukup!');
            }

            else {
                //di sini
                // return json_encode($request->input());
                DB::beginTransaction();

                // Step 1: Simpan Data Belanja
                try {
                    $belanja->nama = $request->nama;
                    $belanja->nilai += $selisih;
                    
                    $belanja->ppn = $ppn;
                    $belanja->pph21 = $pph21;
                    $belanja->pph23 = $pph23;
                    
                    $belanja->nomor = $request->nomor;
                    $belanja->penerima = $request->penerima;
                    Auth::user()->belanjas()->save($belanja);

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Step 1: '.$e->getMessage()); 
                }

                // Step 2: Buat Trx
                try {
                    $transaksi = $belanja->transaksi;
                    // return json_encode($transaksi);
                    $transaksi->nominal += $selisih;
                    $transaksi->save();
                    // Auth::user()->kas_trxs()->save($transaksi);

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Step 2: '.$e->getMessage());
                }

                // Step 3: Update Saldo
                try {
                    $saldo->$source -= $selisih;
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
                                'periode' => $transaksi->tanggal->addMonthsNoOverflow()->startOfMonth()
                            ]
                        )->firstOrFail();
                        $saldoawal->$source -= $selisih;
                        $saldoawal->save();

                    } catch (\Exception $e) {
                        $saldoawal = new SaldoAwal; 
                        $saldoawal->ta = $transaksi->ta;
                        // $saldoawal->npsn = $belanja->npsn;
                        $saldoawal->periode = $transaksi->tanggal->addMonthsNoOverflow()->startOfMonth();
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
                    $rka->$realisasi_triwulan += $selisih;
                    $rka->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Step 5: '.$e->getMessage());
                }

                DB::commit();
                return redirect()->route('sekolah.belanja.index')->with(['success' => 'Belanja Berhasil diperbarui!']);

            }
        }
        
    }

    public function destroy(Request $request, $id)
    {
    	$ta = $request->cookie('ta');
        $belanja = Auth::user()->belanjas()->findOrFail($id);
        // return $belanja;
        $jenis_belanja = $belanja->jenis_belanja;

        DB::beginTransaction();
        switch ($jenis_belanja) {
            case 1:
                // Step 1: Delete Belanja Modal
                try {
                    $belanja->modals()->delete();
                    // 
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Error: '.$e->getMessage());
                    // 
                }

                // Step 2: Delete Belanja
                /*try {
                    $belanja->delete();
                    //
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Error: '.$e->getMessage());
                    //
                }*/
                break;

            case 2:
                $belanjapersediaan = $belanja->persediaans;
                foreach ($belanjapersediaan as $key => $item) {
                    $qty = $item->qty;
                    $persediaan = $item->barang_persediaan;
                    $barang_persediaan_id = $item->barang_persediaan_id;
                    
                    // Step 1: Update stok
                    try {
                        $persediaan->stok -= $qty;
                        $persediaan->save();

                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()
                            ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
                    }

                    // Step 2: Update Stok Awal
                    try {
                        try {
                            $stokawal = StokAwal::where(
                                [
                                    'ta' => $ta,
                                    'npsn' => $belanja->npsn,
                                    'barang_persediaan_id' => $barang_persediaan_id,
                                    'periode' => $belanja->tanggal->addMonthsNoOverflow()->startOfMonth()
                                ]
                            )->firstOrFail();

                            $stokawal->stok -= $qty;
                            $stokawal->save();

                        } catch (\Exception $e) {
                            $stokawal = new StokAwal;
                            $stokawal->ta = $ta;
                            $stokawal->npsn = $belanja->npsn;
                            $stokawal->barang_persediaan_id = $barang_persediaan_id;
                            $stokawal->periode = $belanja->tanggal->addMonthsNoOverflow()->startOfMonth();
                            $stokawal->stok = $persediaan->stok;
                            $stokawal->save();
                        }

                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()
                            ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
                    }

                    // Step 3: Hapus Belanja Persediaan
                    try {
                        $item->delete();
                        
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->withErrors('Error: '.$e->getMessage());
                    }
                    
                }
                // Step 4: Hapus Belanja
                /*try {
                    $belanja->delete();
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Error: '.$e->getMessage());
                }*/
                // return $belanjapersediaan;
                break;
            
            default:
                // return $belanja;
                break;
        }
        
        $kas = $belanja->kas;
        $tanggal = $belanja->tanggal;
        $triwulan = GetTriwulan($tanggal);
        $saldo = Auth::user()->saldos()->where('ta', '=', $ta)->firstOrFail();
        $nominal = $belanja->nilai;
        $realisasi_triwulan= 'realisasi_tw'.$triwulan;
        $transaksi = $belanja->transaksi;
        $rka = $belanja->rka;
        if ($kas=='B') {
            $source = 'saldo_bank';
            
        }
        else if ($kas=='T') {
            $source = 'saldo_tunai';
            
        }

        // Hapus Transaksi
        try {
            $transaksi->delete();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }

        // Hapus Transaksi
        try {
            $belanja->delete();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }

        // Kembalikan Saldo
        try {
            $saldo->$source += $nominal;
            $saldo->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }

        // Update Saldo Awal
        try {
            try {
                $saldoawal = Auth::user()->saldo_awals()
                ->where(
                    [
                        'ta' => $ta,
                        'periode' => $tanggal->addMonthsNoOverflow()->startOfMonth()
                    ]
                )->firstOrFail();
                $saldoawal->$source += $nominal;
                $saldoawal->save();

            } catch (\Exception $e) {
                $saldoawal = new SaldoAwal; 
                $saldoawal->ta = $ta;
                // $saldoawal->npsn = $belanja->npsn;
                $saldoawal->periode = $tanggal->addMonthsNoOverflow()->startOfMonth();
                $saldoawal->saldo_bank = $saldo->saldo_bank;
                $saldoawal->saldo_tunai = $saldo->saldo_tunai;
                // $saldoawal->save();
                Auth::user()->saldo_awals()->save($saldoawal);
            }
            

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }

        // Update RKA
        try {
            $rka->$realisasi_triwulan -= $nominal;
            $rka->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }
        
        // return $belanja;
        // DB::rollback();
        DB::commit();
        return redirect()->back()->with(['success'=>'Data Belanja Berhasil dihapus!']);
        
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
            // return $belanja->rka->rekening->parent->kode_rekening.".".$belanja->rka->rekening->kode_rekening;
    		$tanggal= $belanja->tanggal->locale('id_ID')->isoFormat('LL');
    		$npsn= $belanja->npsn;
    		$namaprogram= $belanja->rka->program->nama_program;
			$namakp= $belanja->rka->kp->nama_komponen;
			$sekolah= Auth::user();
			$namasekolah= $sekolah->name;
			$namakecamatan= $sekolah->kecamatan->nama_kecamatan;
			$ta= $belanja->rka->ta;
			$nomor= $belanja->nomor;	
			$judul= $namasekolah." - ".$namakecamatan;
			$penerima= $belanja->penerima;
			$uang_digit= $belanja->nilai;
			$uang_terbilang= "# ( ".ucwords(Terbilang($uang_digit)." rupiah")." ) #";
			$pembayaran= $belanja->nama;
			$keperluan= $namaprogram." / ".$namakp;
			$kode_rekening= $belanja->rka->rekening->parent->kode_rekening.".".$belanja->rka->rekening->kode_rekening." - ".$belanja->rka->rekening->nama_rekening;
			$jumlah_kotor= $belanja->nilai;
			$ppn= "PPN: ".FormatUang($belanja->ppn);
			$pph_21= "PPH21: ".FormatUang($belanja->pph21);
			$pph_23= "PPH23: ".FormatUang($belanja->pph23);
			$jumlah_bersih= $jumlah_kotor-$belanja->ppn-$belanja->pph21-$belanja->pph23;

			$nama_kepalasekolah= $sekolah->nama_kepsek;
			$nip_kepalasekolah= "NIP. ".$sekolah->nip_kepsek;
			$nama_bendahara= $sekolah->nama_bendahara;
			$nip_bendahara= "NIP. ".$sekolah->nip_bendahara;

			// return $nama_kepalasekolah;
    		// return json_encode($belanja);
    		// Excel
    		$spreadsheet = IOFactory::load('storage/format/bukti_pengeluaran.xlsx');
        	$worksheet = $spreadsheet->getActiveSheet();
        	$worksheet->getCell('nama_kepalasekolah')->setValue($nama_kepalasekolah);
			$worksheet->getCell('nip_kepalasekolah')->setValue($nip_kepalasekolah);
			$worksheet->getCell('nama_bendahara')->setValue($nama_bendahara);
			$worksheet->getCell('nip_bendahara')->setValue($nip_bendahara);
			$worksheet->getCell('jumlah_bersih')->setValue(FormatMataUang($jumlah_bersih));
			$worksheet->getCell('jumlah_kotor')->setValue(FormatMataUang($jumlah_kotor));
			$worksheet->getCell('ppn')->setValue($ppn);
			$worksheet->getCell('pph_21')->setValue($pph_21);
			$worksheet->getCell('pph_23')->setValue($pph_23);
			$worksheet->getCell('keperluan')->setValue($keperluan);
			$worksheet->getCell('kode_rekening')->setValue($kode_rekening);
			$worksheet->getCell('pembayaran')->setValue($pembayaran);
			$worksheet->getCell('uang_digit')->setValue(FormatMataUang($uang_digit));
			$worksheet->getCell('uang_terbilang')->setValue($uang_terbilang);
			$worksheet->getCell('penerima')->setValue($penerima);
			$worksheet->getCell('judul')->setValue($judul);
			$worksheet->getCell('ta')->setValue($ta);
			$worksheet->getCell('nomor')->setValue($nomor);
			$worksheet->getCell('tanggal')->setValue($tanggal);

        	// Cetak
	        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
	        $writer->save($temp_file);
	        $file= 'A2_'.$belanja->id.'.xlsx';
	        $documento = file_get_contents($temp_file);
	        unlink($temp_file);  // delete file tmp
	        header("Content-Disposition: attachment; filename= ".$file."");
	        header('Content-Type: application/excel');
	        return $documento;


    	} catch (\Exception $e) {
    		return redirect()->back()->withErrors('Data Belanja Tidak ditemukan!');
    	}
    }

    public function modal($id)
    {
    	$belanja = Auth::user()->belanjas()->modal()->findOrFail($id);
    	return view('sekolah.belanja.modal.detail', compact('belanja'));
    }

    public function createmodal($id)
    {
    	$aksi="tambah";
    	$belanja = Auth::user()->belanjas()->with('rka.rekening')->modal()->findOrFail($id);
    	$nama= $belanja->nama;
    	$parent= $belanja->rka->rekening->parent_id;
    	return view('sekolah.belanja.modal.tambah', compact('aksi','nama','id','parent'));
    }

    public function storemodal(Request $request, $id)
    {
        $belanja = Auth::user()->belanjas()->findOrFail($id);
        $harga_satuan = floatval(str_replace(',', '.', $request->harga_satuan));
        $total = floatval(str_replace(',', '.', $request->total));

        $belanjamodal                   = new BelanjaModal;
        $belanjamodal->nama_barang      = $request->nama_barang;
        $belanjamodal->kode_barang_id   = $request->kode_barang_id;
        $belanjamodal->warna            = $request->warna;
        $belanjamodal->merek            = $request->merek;
        $belanjamodal->tipe             = $request->tipe;
        $belanjamodal->bahan            = $request->bahan;
        $belanjamodal->tanggal_bukti    = $request->tanggal_bukti;
        $belanjamodal->nomor_bukti      = $request->nomor_bukti;
        $belanjamodal->satuan           = $request->satuan;
        $belanjamodal->harga_satuan     = $harga_satuan;
        $belanjamodal->qty              = $request->qty;
        $belanjamodal->total            = $total;
        
        try {
            $belanja->modals()->save($belanjamodal);
            return redirect()->route('sekolah.belanja.modal',['id'=>$id]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }
        
        // return $request->input();
    }

    public function editmodal($id, $modal_id)
    {
        $aksi="edit";
        $belanja = Auth::user()->belanjas()->with('rka.rekening')->modal()->findOrFail($id);
        $belanjamodal = $belanja->modals()->with('kode_barang')->findOrFail($modal_id);
        $nama= $belanja->nama;
        $parent= $belanja->rka->rekening->parent_id;
        return view('sekolah.belanja.modal.tambah', compact('aksi','nama','id','parent', 'belanjamodal'));
    }

    public function updatemodal(Request $request, $id, $modal_id)
    {
        $belanja = Auth::user()->belanjas()->findOrFail($id);
        $belanjamodal = $belanja->modals()->findOrFail($modal_id);
        
        $harga_satuan = floatval(str_replace(',', '.', $request->harga_satuan));
        $total = floatval(str_replace(',', '.', $request->total));

        $belanjamodal->nama_barang      = $request->nama_barang;
        // $belanjamodal->kode_barang_id   = $request->kode_barang_id;
        $belanjamodal->warna            = $request->warna;
        $belanjamodal->merek            = $request->merek;
        $belanjamodal->tipe             = $request->tipe;
        $belanjamodal->bahan            = $request->bahan;
        // $belanjamodal->tanggal_bukti    = $request->tanggal_bukti;
        $belanjamodal->nomor_bukti      = $request->nomor_bukti;
        $belanjamodal->satuan           = $request->satuan;
        $belanjamodal->harga_satuan     = $harga_satuan;
        $belanjamodal->qty              = $request->qty;
        $belanjamodal->total            = $total;
        // return $belanjamodal;
        try {
            $belanjamodal->save();
            return redirect()->route('sekolah.belanja.modal',['id'=>$id]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }
    }

    public function destroymodal(Request $request, $id, $modal_id)
    {
        $belanja = Auth::user()->belanjas()->findOrFail($id);
        $belanjamodal = $belanja->modals()->findOrFail($modal_id);
        
        try {
            $belanjamodal->delete();
            return redirect()->route('sekolah.belanja.modal',['id'=>$id]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }
    }

    public function getmodal($id)
    {
    	// if(request()->ajax()) {
            $ta = Cookie::get('ta');
            $belanja = Auth::user()->belanjas()->findOrFail($id);
            $query = $belanja->modals()->with('kode_barang');

            return DataTables::eloquent($query)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->withQuery('total', function($filteredQuery) {
                return FormatMataUang($filteredQuery->sum('total'));
            })
            ->editColumn('harga_satuan', function ($belanjamodal) {
                return FormatMataUang($belanjamodal->harga_satuan);
            })
            ->editColumn('total', function ($belanjamodal) {
                return FormatMataUang($belanjamodal->total);
            })
            ->editColumn('tanggal_bukti', function ($belanjamodal
            ) {
                return $belanjamodal
                ->tanggal_bukti->format('d/m/Y');
            })
            ->addColumn('action', function($belanjamodal) {
                $urledit= route('sekolah.belanja.editmodal', ['id' => $belanjamodal->belanja_id, 'modal_id' => $belanjamodal->id]);
                $urlhapus= route('sekolah.belanja.destroymodal', ['id' => $belanjamodal->belanja_id, 'modal_id' => $belanjamodal->id]);
                
                $btnaction =
                    RenderTombol("success", $urledit, "Edit")." ".
                    RenderTombol("danger confirmation", $urlhapus, "Hapus");

                return $btnaction;
            })
            ->addIndexColumn()
            ->make(true);
            // return $belanja;
        // }
    }

    public function persediaan($id)
    {
    	$belanja = Auth::user()->belanjas()->persediaan()->findOrFail($id);
    	return view('sekolah.belanja.persediaan.detail', compact('belanja'));
    }

    public function createpersediaan($id)
    {
        $aksi="tambah";
        $belanja = Auth::user()->belanjas()->with('rka.rekening')->persediaan()->findOrFail($id);
        $jenis= intval($belanja->rka->rekening->kode_rekening);
        $persediaans = Auth::user()->persediaans()
        ->where('jenis', $jenis)
        ->get();
        $nama= $belanja->nama;
        // return $jenis;
        return view('sekolah.belanja.persediaan.tambah', compact('aksi','nama','id', 'persediaans'));
    
    }

    public function storepersediaan(Request $request, $id)
    {
        $sekolah = Auth::user();
        $belanja = $sekolah->belanjas()->findOrFail($id);
        $barang_persediaan_id = $request->barang_persediaan_id;
        $persediaan= $sekolah->persediaans()
        ->findOrFail($barang_persediaan_id);
        $ta = $request->cookie('ta');
        $npsn = $sekolah->npsn;
        $qty = $request->qty;

        DB::beginTransaction();
        // Step 1: Buat Transaksi Persediaan
        /*try {
            $trx = new PersediaanTrx;
            $trx->ta = $ta;
            $trx->barang_persediaan_id = $barang_persediaan_id;
            $trx->io = 'i';
            $trx->jenis = 'Purchase';
            $trx->qty = $qty;
            $trx->keterangan = $belanja->nama;
            $trx->tanggal = $belanja->tanggal;
            $trx->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }*/

        // Step 2: Tambah Stok
        try {
            $persediaan->stok += $qty;
            $persediaan->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // Step 3: Update Stok Awal
        try {
            try {
                $stokawal = StokAwal::where(
                    [
                        'ta' => $ta,
                        'npsn' => $npsn,
                        'barang_persediaan_id' => $barang_persediaan_id,
                        'periode' => $belanja->tanggal->addMonthsNoOverflow()->startOfMonth()
                    ]
                )->firstOrFail();

                $stokawal->stok += $qty;

                $stokawal->save();

            } catch (\Exception $e) {
                $stokawal = new StokAwal;
                $stokawal->ta = $ta;
                $stokawal->npsn = $npsn;
                $stokawal->barang_persediaan_id = $barang_persediaan_id;
                $stokawal->periode = $belanja->tanggal->addMonthsNoOverflow()->startOfMonth();
                $stokawal->stok = $persediaan->stok;
                $stokawal->save();
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // Step 4: Simpan Belanja Persediaan
        try {
            $belanjapersediaan = new BelanjaPersediaan;
            $belanjapersediaan->barang_persediaan_id = $barang_persediaan_id;
            $belanjapersediaan->qty = $request->qty;
            $belanjapersediaan->total = floatval(str_replace(',', '.', $request->total));
            $belanja->persediaans()->save($belanjapersediaan);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        DB::commit();
        return redirect()->route('sekolah.belanja.persediaan',['id'=>$id])
        ->with(['success'=>'Belanja Persediaan berhasil disimpan']);

        // return $belanja;
    }

    public function editpersediaan($id, $modal_id)
    {
        $aksi="edit";
        $belanja = Auth::user()->belanjas()->with('rka.rekening')->persediaan()->findOrFail($id);
        $belanjapersediaan = $belanja->persediaans()->with('barang_persediaan')->findOrFail($modal_id);
        $nama= $belanja->nama;
        return view('sekolah.belanja.persediaan.tambah', compact('aksi','nama','id', 'belanjapersediaan'));
    }

    public function updatepersediaan(Request $request, $id, $persediaan_id)
    {
        $sekolah = Auth::user();
        $belanja = $sekolah->belanjas()->findOrFail($id);
        $belanjapersediaan = $belanja->persediaans()->findOrFail($persediaan_id);
        $persediaan = $belanjapersediaan->barang_persediaan;
        $ta = $request->cookie('ta');
        $npsn = $sekolah->npsn;
        $barang_persediaan_id = $belanjapersediaan->barang_persediaan_id;

        $qty_lama = $belanjapersediaan->qty;
        $qty_baru = $request->qty;
        $selisih = $qty_baru - $qty_lama;

        DB::beginTransaction();

        // Step 1: Update stok
        try {
            $persediaan->stok += $selisih;
            $persediaan->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // Step 2: Update Stok Awal
        try {
            try {
                $stokawal = StokAwal::where(
                    [
                        'ta' => $ta,
                        'npsn' => $npsn,
                        'barang_persediaan_id' => $barang_persediaan_id,
                        'periode' => $belanja->tanggal->addMonthsNoOverflow()->startOfMonth()
                    ]
                )->firstOrFail();

                $stokawal->stok += $selisih;
                $stokawal->save();

            } catch (\Exception $e) {
                $stokawal = new StokAwal;
                $stokawal->ta = $ta;
                $stokawal->npsn = $npsn;
                $stokawal->barang_persediaan_id = $barang_persediaan_id;
                $stokawal->periode = $belanja->tanggal->addMonthsNoOverflow()->startOfMonth();
                $stokawal->stok = $persediaan->stok;
                $stokawal->save();
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // Step 3: Update Belanja Persediaan
        try {
            $belanjapersediaan->qty += $selisih;
            $belanjapersediaan->total = floatval(str_replace(',', '.', $request->total));
            $belanjapersediaan->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // return $belanjapersediaan;
        DB::commit();
        return redirect()->route('sekolah.belanja.persediaan',['id'=>$id])
        ->with(['success'=>'Belanja Persediaan berhasil diperbarui']);

    }

    public function destroypersediaan(Request $request, $id, $persediaan_id)
    {
        $ta = $request->cookie('ta');
        $sekolah = Auth::user();
        $npsn = $sekolah->npsn;
        $belanja = $sekolah->belanjas()->findOrFail($id);
        $belanjapersediaan = $belanja->persediaans()->findOrFail($persediaan_id);
        $persediaan = $belanjapersediaan->barang_persediaan;
        $barang_persediaan_id = $belanjapersediaan->barang_persediaan_id;
        $qty = $belanjapersediaan->qty;

        DB::beginTransaction();

        // Step 1: Update stok
        try {
            $persediaan->stok -= $qty;
            $persediaan->save();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // Step 2: Update Stok Awal
        try {
            try {
                $stokawal = StokAwal::where(
                    [
                        'ta' => $ta,
                        'npsn' => $npsn,
                        'barang_persediaan_id' => $barang_persediaan_id,
                        'periode' => $belanja->tanggal->addMonthsNoOverflow()->startOfMonth()
                    ]
                )->firstOrFail();

                $stokawal->stok -= $qty;
                $stokawal->save();

            } catch (\Exception $e) {
                $stokawal = new StokAwal;
                $stokawal->ta = $ta;
                $stokawal->npsn = $npsn;
                $stokawal->barang_persediaan_id = $barang_persediaan_id;
                $stokawal->periode = $belanja->tanggal->addMonthsNoOverflow()->startOfMonth();
                $stokawal->stok = $persediaan->stok;
                $stokawal->save();
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['msg' => 'Error: '.$e->getMessage()]);
        }

        // Step 3: Hapus Belanja Persediaan
        try {
            $belanjapersediaan->delete();
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: '.$e->getMessage());
        }

        DB::commit();
        return redirect()->route('sekolah.belanja.persediaan',['id'=>$id]);
        
    }

    public function getpersediaan($id)
    {
    	// if(request()->ajax()) {
            $ta = Cookie::get('ta');
            $belanja = Auth::user()->belanjas()->findOrFail($id);
            $query = $belanja->persediaans()->ta($ta)->with('barang_persediaan');
            $bpersediaan = $belanja->persediaans()->ta($ta)->with('barang_persediaan')->get();
            
            return DataTables::eloquent($query)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->with('total', function() use ($bpersediaan) {
                $total=0;
                foreach ($bpersediaan as $key => $item) {
                    $total += $item->total;
                }
                return FormatMataUang($total);
            })
            ->editColumn('barang_persediaan.harga_satuan', function ($belanjapersediaan) {
                return FormatMataUang($belanjapersediaan->barang_persediaan->harga_satuan);
            })
            ->addColumn('total', function ($belanjapersediaan) {
                return FormatMataUang(($belanjapersediaan->total));
            })
            ->addColumn('action', function($belanjapersediaan) {
                $urledit= route('sekolah.belanja.editpersediaan', ['id' => $belanjapersediaan->belanja_id, 'modal_id' => $belanjapersediaan->id]);
                $urlhapus= route('sekolah.belanja.destroypersediaan', ['id' => $belanjapersediaan->belanja_id, 'modal_id' => $belanjapersediaan->id]);
                
                $btnaction =
                    RenderTombol("success", $urledit, "Edit")." ".
                    RenderTombol("danger confirmation", $urlhapus, "Hapus");

                return $btnaction;
            })
            ->addIndexColumn()
            ->make(true);
            // return $belanja;
        // }
    }
}
