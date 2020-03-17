<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\KodeProgram;
use App\KomponenPembiayaan;

use Auth;
use Cookie;

class LaporanController extends Controller
{
    public function realisasi()
    {
    	return view('sekolah.laporan.realisasi');
    }

    public function proses_realisasi(Request $request)
    {
    	$ta = $request->cookie('ta');
    	$sekolah= Auth::user();
    	$tahun_tahun= "TAHUN ".$ta;
    	$triwulan= $request->triwulan;
    	$realisasi_triwulan= 'realisasi_tw'.$triwulan;
    	$realisasi_twlalu  = 'realisasi_tw'.($triwulan-1);

    	switch ($triwulan) {
			case '1':
				# code...
				$twhuruf= "I";
				break;
			case '2':
				# code...
				$twhuruf= "II";
				break;
			case '3':
				# code...
				$twhuruf= "III";
				break;
			case '4':
				# code...
				$twhuruf= "IV";
				break;
			
			default:
				# code...
				$twhuruf="-";
				break;
		}

		$deskripsi= "Bersama ini kami laporkan realisasi atas penggunaan Dana BOS untuk Triwulan ".$twhuruf."  sebagai berikut:";

		$total_rkaberjalan = $sekolah->rkas()->where([
			'ta' => $ta,
		])->sum('jumlah');

		$rka_rek1 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(1)->sum('jumlah');
		$rka_rek2 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(2)->sum('jumlah');
		$rka_rek3 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(3)->sum('jumlah');
		$rka_rek4 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(4)->sum('jumlah');
		$rka_rek5 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(5)->sum('jumlah');

		// $belanjar1_sd_twlalu = ($triwulan-1) ? $sekolah->rkas()->where('ta','=',$ta)->parentRekening(1)->sum($realisasi_twlalu) : 0;
		// $belanjar2_sd_twlalu = ($triwulan-1) ? $sekolah->rkas()->where('ta','=',$ta)->parentRekening(2)->sum($realisasi_twlalu) : 0;
		// $belanjar3_sd_twlalu = ($triwulan-1) ? $sekolah->rkas()->where('ta','=',$ta)->parentRekening(3)->sum($realisasi_twlalu) : 0;
		// $belanjar4_sd_twlalu = ($triwulan-1) ? $sekolah->rkas()->where('ta','=',$ta)->parentRekening(4)->sum($realisasi_twlalu) : 0;
		// $belanjar5_sd_twlalu = ($triwulan-1) ? $sekolah->rkas()->where('ta','=',$ta)->parentRekening(5)->sum($realisasi_twlalu) : 0;
        $belanjar1_sd_twlalu = $sekolah->belanjas()->ta($ta)->parentRekening(1)->sampaiTriwulan($triwulan-1)->sum('nilai');
        $belanjar2_sd_twlalu = $sekolah->belanjas()->ta($ta)->parentRekening(2)->sampaiTriwulan($triwulan-1)->sum('nilai');
        $belanjar3_sd_twlalu = $sekolah->belanjas()->ta($ta)->parentRekening(3)->sampaiTriwulan($triwulan-1)->sum('nilai');
        $belanjar4_sd_twlalu = $sekolah->belanjas()->ta($ta)->parentRekening(4)->sampaiTriwulan($triwulan-1)->sum('nilai');
        $belanjar5_sd_twlalu = $sekolah->belanjas()->ta($ta)->parentRekening(5)->sampaiTriwulan($triwulan-1)->sum('nilai');
        
		$belanjar1 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(1)->sum($realisasi_triwulan);
		$belanjar2 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(2)->sum($realisasi_triwulan);
		$belanjar3 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(3)->sum($realisasi_triwulan);
		$belanjar4 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(4)->sum($realisasi_triwulan);
		$belanjar5 = $sekolah->rkas()->where('ta','=',$ta)->parentRekening(5)->sum($realisasi_triwulan);

		$nama_sekolah= $sekolah->nama_sekolah;
		$nama_kepsek= $sekolah->nama_kepsek;
		$nip_kepsek= $sekolah->nip_kepsek;

		$tanggal= AkhirTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL');
		$tanggal_tempat= "Kab. Semarang, ".$tanggal;

		// return json_encode($tanggal_tempat);
		// Excel
		$spreadsheet = IOFactory::load('storage/format/lap_realisasi.xlsx');
    	$worksheet = $spreadsheet->getActiveSheet();
    	$worksheet->getCell('tahun_tahun')->setValue($tahun_tahun);
		$worksheet->getCell('deskripsi')->setValue($deskripsi);
		$worksheet->getCell('total_rkaberjalan')->setValue($total_rkaberjalan);

		$worksheet->getCell('rka_rek1')->setValue($rka_rek1);
		$worksheet->getCell('rka_rek2')->setValue($rka_rek2);
		$worksheet->getCell('rka_rek3')->setValue($rka_rek3);
		$worksheet->getCell('rka_rek4')->setValue($rka_rek4);
		$worksheet->getCell('rka_rek5')->setValue($rka_rek5);

		$worksheet->getCell('belanjar1_sd_twlalu')->setValue($belanjar1_sd_twlalu);
		$worksheet->getCell('belanjar2_sd_twlalu')->setValue($belanjar2_sd_twlalu);
		$worksheet->getCell('belanjar3_sd_twlalu')->setValue($belanjar3_sd_twlalu);
		$worksheet->getCell('belanjar4_sd_twlalu')->setValue($belanjar4_sd_twlalu);
		$worksheet->getCell('belanjar5_sd_twlalu')->setValue($belanjar5_sd_twlalu);

		$worksheet->getCell('belanjar1')->setValue($belanjar1);
		$worksheet->getCell('belanjar2')->setValue($belanjar2);
		$worksheet->getCell('belanjar3')->setValue($belanjar3);
		$worksheet->getCell('belanjar4')->setValue($belanjar4);
		$worksheet->getCell('belanjar5')->setValue($belanjar5);

		$worksheet->getCell('tanggal_tempat')->setValue($tanggal_tempat);
		$worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
		$worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
		$worksheet->getCell('nip_kepsek')->setValue("NIP.".$nip_kepsek);

    	// Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Realisasi_tw'.$triwulan."_".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function sptj()
    {
    	return view('sekolah.laporan.sptj');
    }

    public function proses_sptj(Request $request)
    {
    	$ta = $request->cookie('ta');
    	$sekolah= Auth::user();
    	$triwulan= $request->triwulan;
    	$nomor_sptj= $request->nomor_sptj;

        $nama_sekolah= $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= $sekolah->nip_kepsek;
        $jenjang= $sekolah->jenjang;

        function twhuruf($triwulan)
        {
            switch ($triwulan) {
                case '1':
                    # code...
                    $twhuruf= "I";
                    break;
                case '2':
                    # code...
                    $twhuruf= "II";
                    break;
                case '3':
                    # code...
                    $twhuruf= "III";
                    break;
                case '4':
                    # code...
                    $twhuruf= "IV";
                    break;
                
                default:
                    # code...
                    $twhuruf="-";
                    break;
            }
            return $twhuruf;
        }
        $twloop="";
        for ($i=1; $i <= $triwulan ; $i++) {
            if ($i==1) {
                # code...
                $twloop.="Triwulan ".twhuruf($i);
            }
            elseif ($i<$triwulan) {
                # code...
                $twloop.=", Triwulan ".twhuruf($i);
            }
            elseif ($i==$triwulan){
                $twloop.=" dan Triwulan ".twhuruf($i);
            }
        }
        $paragraf_terakhir= "penggunaan Dana BOS pada ".$twloop." Tahun Anggaran ".$ta." dengan rincian sebagai berikut:";
        $saldo_thlalu= $sekolah->saldos()->where('ta','=',$ta-1)->sum('saldo_bank') + $sekolah->saldos()->where('ta','=',$ta-1)->sum('saldo_tunai');;
        // return $saldo_thlalu;
        $penerimaan_cw1=0;
        $penerimaan_cw2=0;
        $penerimaan_cw3=0;
        $penerimaanpercw="penerimaan_cw";

        // tw1= 123 //cw1
        // tw2= 456 //cw1
        // tw3= 789 //cw2
        // tw4= 101112 //cw3
        $cw = ($triwulan > 2) ?  ($triwulan > 3) ? 3 : 2 : 1 ;
        for ($i=1; $i <= $cw ; $i++) { 
            ${$penerimaanpercw.$i} = $sekolah->pendapatans()->whereBetween('tanggal', [AwalCaturwulan($cw, $ta), AkhirCaturwulan($cw, $ta)])->sum('nominal');
        }

        $belanjar1_sd_tw_sekarang= $sekolah->belanjas()->ta($ta)->sampaiTriwulan($triwulan)->parentRekening(1)->sum('nilai');
        $belanjar2_sd_tw_sekarang= $sekolah->belanjas()->ta($ta)->sampaiTriwulan($triwulan)->parentRekening(2)->sum('nilai');
        $belanjar3_sd_tw_sekarang= $sekolah->belanjas()->ta($ta)->sampaiTriwulan($triwulan)->parentRekening(3)->sum('nilai');
        $belanjar4_sd_tw_sekarang= $sekolah->belanjas()->ta($ta)->sampaiTriwulan($triwulan)->parentRekening(4)->sum('nilai');
        $belanjar5_sd_tw_sekarang= $sekolah->belanjas()->ta($ta)->sampaiTriwulan($triwulan)->parentRekening(5)->sum('nilai');
        $belanjar345_sd_tw_sekarang= $belanjar3_sd_tw_sekarang+$belanjar4_sd_tw_sekarang+$belanjar5_sd_tw_sekarang;
        
        $tanggal= AkhirTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL');
        $tanggal_tempat= "Kab. Semarang, ".$tanggal;
        
        if($triwulan<4){
            $kas_tunai = $sekolah->saldo_awals()->where('periode','=',AwalTriwulan($triwulan+1,$ta))->sum('saldo_tunai');
        }
        else{
            $kas_tunai = $sekolah->saldo_awals()->where('periode','=',AwalTriwulan(($triwulan+1)-4, $ta+1))->sum('saldo_tunai');
        }
        // return $kas_tunai;

    	// Excel
    	$spreadsheet = IOFactory::load('storage/format/sptj.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->getCell('nomor_sptj')->setValue($nomor_sptj);
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('kode_organisasi')->setValue($sekolah->npsn);
        $worksheet->getCell('jenjang')->setValue($jenjang);
        $worksheet->getCell('paragraf_terakhir')->setValue($paragraf_terakhir);
        $worksheet->getCell('ta')->setValue($ta);
        $worksheet->getCell('saldo_thlalu')->setValue($saldo_thlalu);

        $worksheet->getCell('penerimaan_cw1')->setValue($penerimaan_cw1);
        $worksheet->getCell('penerimaan_cw2')->setValue($penerimaan_cw2);
        $worksheet->getCell('penerimaan_cw3')->setValue($penerimaan_cw3);

        $worksheet->getCell('belanjar1_sd_tw_sekarang')->setValue($belanjar1_sd_tw_sekarang);
        $worksheet->getCell('belanjar2_sd_tw_sekarang')->setValue($belanjar2_sd_tw_sekarang);
        $worksheet->getCell('belanjar345_sd_tw_sekarang')->setValue($belanjar345_sd_tw_sekarang);

        $worksheet->getCell('kas_tunai')->setValue($kas_tunai);

        $worksheet->getCell('tanggal_tempat')->setValue($tanggal_tempat);
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue("NIP.".$nip_kepsek);

		// Cetak
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	    $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
	    $writer->save($temp_file);
	    $file= 'SPTJ_tw_'.$triwulan."-".$sekolah->npsn.'.xlsx';
	    $documento = file_get_contents($temp_file);
	    unlink($temp_file);  // delete file tmp
	    header("Content-Disposition: attachment; filename= ".$file."");
	    header('Content-Type: application/excel');
	    return $documento;
    }

    public function sptmh()
    {
    	return view('sekolah.laporan.sptmh');
    }

    public function proses_sptmh(Request $request)
    {
        $triwulan = $request->triwulan;
        $sekolah = Auth::user();
        $ta = $request->cookie('ta');
        $npsn = $sekolah->npsn;
        $nomor_sptmh= $request->nomor_sptmh;

        $tanggal= AkhirTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL');
        $tanggal_tanggal= strtoupper("Tanggal ".$tanggal);
        $tanggal_tempat= "Kab. Semarang, ".$tanggal;

        $nama_sekolah= $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= $sekolah->nip_kepsek;
        $jenjang= $sekolah->jenjang;
        $nama_kecamatan= $sekolah->kecamatan->nama_kecamatan;

        switch ($triwulan) {
            case '1':
                # code...
                $twhuruf= "I";
                break;
            case '2':
                # code...
                $twhuruf= "II";
                break;
            case '3':
                # code...
                $twhuruf= "III";
                break;
            case '4':
                # code...
                $twhuruf= "IV";
                break;
            
            default:
                # code...
                $twhuruf="-";
                break;
        }

        $deskripsi= "Bertangungjawab penuh atas segala penerima hibah berupa uang yang diterima langsung pada triwulan ".$twhuruf;

        $total_rkaberjalan = $sekolah->rkas()->where([
            'ta' => $ta,
        ])->sum('jumlah');
        $total_rka= $total_rkaberjalan;

        $realisasi_sd_twlalu = $sekolah->belanjas()->ta($ta)->sampaiTriwulan($triwulan-1)->sum('nilai');
        $realisasi_twsekarang= $sekolah->belanjas()->ta($ta)->triwulan($triwulan)->sum('nilai');
        // return $realisasi_twsekarang;
        
    	// Excel
        $spreadsheet = IOFactory::load('storage/format/sptmh.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('nomor_sptmh')->setValue($nomor_sptmh);
        $worksheet->getCell('tanggal_tanggal')->setValue($tanggal_tanggal);
        $worksheet->getCell('jenjang')->setValue($jenjang);
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('nama_kecamatan')->setValue($nama_kecamatan);
        $worksheet->getCell('npsn')->setValue($npsn);
        $worksheet->getCell('deskripsi')->setValue($deskripsi);
        $worksheet->getCell('total_rka')->setValue($total_rka);
        $worksheet->getCell('realisasi_sd_twlalu')->setValue($realisasi_sd_twlalu);
        $worksheet->getCell('realisasi_twsekarang')->setValue($realisasi_twsekarang);
        $worksheet->getCell('tanggal_tempat')->setValue($tanggal_tempat);
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue('NIP.'.$nip_kepsek);

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'SPTMH_tw_'.$triwulan."-".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function k7prov()
    {
    	return view('sekolah.laporan.k7prov');
    }

    public function proses_k7prov(Request $request)
    {
    	$ta = $request->cookie('ta');
        $sekolah = Auth::user();
        $npsn = $sekolah->npsn;
        $triwulan = $request->triwulan;

        $periode="PERIODE TANGGAL : ".AwalTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL')." s/d ".AkhirTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL')." (Triwulan ".$triwulan." Tahun ".$ta.")";
        // return $periode;
        $nama_sekolah= $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= $sekolah->nip_kepsek;
        $nama_bendahara= $sekolah->nama_bendahara;
        $nip_bendahara= $sekolah->nip_bendahara;
        $nama_kecamatan= $sekolah->kecamatan->nama_kecamatan;

        $saldo_tw_lalu=0;
        $penerimaan_tw_sekarang=0;

        $program = KodeProgram::all();
        $komponen = KomponenPembiayaan::all();
        $program_kp= array();
        // return json_encode($komponen);

        foreach ($program as $key => $p) {
            foreach ($komponen as $kpkey => $kp) {
                $program_id=$p->id;
                $pembiayaan_id=$kp->id;
                $program_kp_detail= $sekolah->belanjas()->ta($ta)->triwulan($triwulan)
                    ->whereHas('rka', function ($qrka) use ($program_id) {
                        $qrka->where('kode_program_id', $program_id);
                    })
                    ->whereHas('rka', function ($qrka) use ($pembiayaan_id) {
                        $qrka->where('komponen_pembiayaan_id', $pembiayaan_id);
                    })
                    ->sum('nilai');
                
                $program_kp[$p->id][$kp->id]=$program_kp_detail;
            }
        }

        // return json_encode($program_kp);
        // Excel
        $spreadsheet = IOFactory::load('storage/format/k7_prov1.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('triwulan')->setValue($triwulan);
        $worksheet->getCell('periode')->setValue($periode);
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('nama_kecamatan')->setValue($nama_kecamatan);
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue("NIP.".$nip_kepsek);
        $worksheet->getCell('nama_bendahara')->setValue($nama_bendahara);
        $worksheet->getCell('nip_bendahara')->setValue("NIP.".$nip_bendahara);
        // $worksheet->getCell('teks_saldo_tw')->setValue($teks_saldo_tw);
        $worksheet->getCell('saldo_tw_lalu')->setValue($saldo_tw_lalu);
        // $worksheet->getCell('teks_penerimaan_tw')->setValue($teks_penerimaan_tw);
        $worksheet->getCell('penerimaan_tw_sekarang')->setValue($penerimaan_tw_sekarang);
        $worksheet->fromArray(
            $program_kp,
            null,
            'E16'
        );

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'K7Prov_tw_'.$triwulan."-".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function k7kab()
    {
    	# code...
    }

    public function proses_k7kab(Request $request)
    {
    	# code...
    }

    public function modal()
    {
    	# code...
    }

    public function proses_modal(Request $request)
    {
    	# code...
    }

    public function persediaan()
    {
    	# code...
    }

    public function proses_persediaan(Request $request)
    {
    	# code...
    }

    public function bku()
    {
    	return view('sekolah.laporan.bku');
    }

    public function proses_bku(Request $request)
    {
        $sekolah = Auth::user();
    	$nama_sekolah = $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= "NIP.".$sekolah->nip_kepsek;
        $nama_bendahara= $sekolah->nama_bendahara;
        $nip_bendahara= "NIP.".$sekolah->nip_bendahara;
        $nama_kecamatan= $sekolah->kecamatan->nama_kecamatan;
        $desa= (!empty($sekolah->desa)) ? strtoupper($sekolah->desa) : "-" ;
        $desa_kecamatan=$desa." / ".$nama_kecamatan;
        $ta = $request->cookie('ta');
        $bulan = $request->bulan;
        $tanggal = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->endOfMonth();
        $tempat_tanggal = "Kab. Semarang, ".$tanggal->locale('id_ID')->isoFormat('LL');
        $periode= IntBulan($bulan);

        // return $periode;

        $bku_content= array();

        if ($bulan==1) {
            $saldo_awal = 0;
            // Carbon::parse('first day of january '.$ta)->locale('id_ID')->isoFormat('DD MMM YY');
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Awal';
            $bku_content[0][4]= $saldo_awal;
            $bku_content[0][5]= null;
        }
        else {
            $fromDate = Carbon::createFromFormat("!Y-n-j", $ta."-1-1");
            $tillDate = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->startOfMonth();
            $saldo_awal = Auth::user()->saldo_awals()->where([
                'ta' => $ta,
                'periode' => $tillDate
            ])->first();
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Bulan Lalu';
            $bku_content[0][4]= $saldo_awal->saldo_bank + $saldo_awal->saldo_tunai;
            $bku_content[0][5]= 0;
        }

        $kodebku = $uraian = $nomorbukti = $nominalpendapatan = $nominalbelanja = array();
        $trx = Auth::user()->kas_trxs()->whereMonth('tanggal', $bulan)->orderBy('tanggal')->get();
        $i = 0;
        $a= array();
        foreach ($trx as $key => $item) {
            $kodebku[$i]=null;
            $uraian[$i]=null;
            $nomorbukti[$i]=null;
            $nominalpendapatan[$i]=0;
            $nominalbelanja[$i]=0;

            if ($item->io == 'o') {
                // $a[]= $item->belanja->rka;
                $belanja= $item->belanja;
                $kodebku[$i]  = $belanja->rka->kode_program_id."/";
                $kodebku[$i] .= $belanja->rka->rekening->parent->kode_rekening.".";
                $kodebku[$i] .= $belanja->rka->rekening->kode_rekening."/";
                $kodebku[$i] .= $belanja->rka->kp->kode_komponen;
                $uraian[$i]   = $belanja->nama;
                $nominalbelanja[$i] = $item->belanja->nilai; 
                $nomorbukti[$i] = $belanja->nomor;

                $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                $bku_content[$i+1][1]= $kodebku[$i];
                $bku_content[$i+1][2]= $nomorbukti[$i];
                $bku_content[$i+1][3]= $uraian[$i];
                $bku_content[$i+1][4]= $nominalpendapatan[$i];
                $bku_content[$i+1][5]= $nominalbelanja[$i];
                $i++;

                if (($item->belanja->ppn)!=0) {
                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menerima PPN';
                    $bku_content[$i+1][4]= $item->belanja->ppn;
                    $bku_content[$i+1][5]= 0;
                    $i++;

                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menyetorkan PPN';
                    $bku_content[$i+1][4]= 0;
                    $bku_content[$i+1][5]= $item->belanja->ppn;
                    $i++; 
                }

                if (($item->belanja->pph21)!=0) {
                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menerima PPh 21';
                    $bku_content[$i+1][4]= $item->belanja->pph21;
                    $bku_content[$i+1][5]= 0;
                    $i++;

                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menyetorkan PPh 21';
                    $bku_content[$i+1][4]= 0;
                    $bku_content[$i+1][5]= $item->belanja->pph21;
                    $i++; 
                }

                if (($item->belanja->pph23)!=0) {
                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menerima PPh 23';
                    $bku_content[$i+1][4]= $item->belanja->pph23;
                    $bku_content[$i+1][5]= 0;
                    $i++;

                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menyetorkan PPh 23';
                    $bku_content[$i+1][4]= 0;
                    $bku_content[$i+1][5]= $item->belanja->pph23;
                    $i++; 
                }
            }

            else {

                if ($item->io == 'i') {
                    // $a[]= $item->pendapatan;
                    $kodebku[$i] = "Pendapatan ".$item->pendapatan->sumber;
                    $uraian[$i] = $item->pendapatan->keterangan;
                    $nominalpendapatan[$i] = $item->pendapatan->nominal;
                }

                else if (empty($item->io)) {
                    $a[]= $item->kas_trx_detail;
                    $kodebku[$i] = $item->kas_trx_detail->tipe;

                    switch ($kodebku[$i]) {
                        case 'Pindah Buku':
                            $uraian[$i] = 'Pemindahbukuan';
                            break;

                        case 'Setor Kembali':
                            $uraian[$i] = 'Setor Sisa Kas';
                            break;
                        
                        default:
                            $uraian[$i] = 'Bunga';
                            break;   
                    }

                    $nominalpendapatan[$i] = $item->kas_trx_detail->nominal;
                    $nominalbelanja[$i] = $item->kas_trx_detail->nominal;
                }

                $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                $bku_content[$i+1][1]= $kodebku[$i];
                $bku_content[$i+1][2]= $nomorbukti[$i];
                $bku_content[$i+1][3]= $uraian[$i];
                $bku_content[$i+1][4]= $nominalpendapatan[$i];
                $bku_content[$i+1][5]= $nominalbelanja[$i];

                $i++;
            }
            // $i++;
        }

        // return json_encode($bku_content);
        $spreadsheet = IOFactory::load('storage/format/bku.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('desa_kecamatan')->setValue($desa_kecamatan);
        $worksheet->getCell('periode')->setValue("BULAN ".strtoupper($periode));
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue($nip_kepsek);
        $worksheet->getCell('nama_bendahara')->setValue($nama_bendahara);
        $worksheet->getCell('nip_bendahara')->setValue($nip_bendahara);
        
              
        $worksheet->fromArray(
            $bku_content,
            NULL,
            'B12'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('B11:I211');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('I');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            'A'
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'BKU_bulan_'.$bulan."-".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;

    }

    public function bukubank()
    {
        return view('sekolah.laporan.bukubank');
    }

    public function proses_bukubank(Request $request)
    {
        $sekolah = Auth::user();
        $nama_sekolah = $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= "NIP.".$sekolah->nip_kepsek;
        $nama_bendahara= $sekolah->nama_bendahara;
        $nip_bendahara= "NIP.".$sekolah->nip_bendahara;
        $nama_kecamatan= $sekolah->kecamatan->nama_kecamatan;
        $desa= (!empty($sekolah->desa)) ? strtoupper($sekolah->desa) : "-" ;
        $desa_kecamatan=$desa." / ".$nama_kecamatan;
        $ta = $request->cookie('ta');
        $bulan = $request->bulan;
        $tanggal = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->endOfMonth();
        $tempat_tanggal = "Kab. Semarang, ".$tanggal->locale('id_ID')->isoFormat('LL');
        $periode= IntBulan($bulan);

        // return $periode;

        $bku_content= array();

        if ($bulan==1) {
            $saldo_awal = 0;
            // Carbon::parse('first day of january '.$ta)->locale('id_ID')->isoFormat('DD MMM YY');
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Awal';
            $bku_content[0][4]= $saldo_awal;
            $bku_content[0][5]= null;
        }
        else {
            $fromDate = Carbon::createFromFormat("!Y-n-j", $ta."-1-1");
            $tillDate = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->startOfMonth();
            $saldo_awal = Auth::user()->saldo_awals()->where([
                'ta' => $ta,
                'periode' => $tillDate
            ])->first();
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Bulan Lalu';
            $bku_content[0][4]= $saldo_awal->saldo_bank + $saldo_awal->saldo_tunai;
            $bku_content[0][5]= 0;
        }

        $kodebku = $uraian = $nomorbukti = $nominalpendapatan = $nominalbelanja = array();
        $trx = Auth::user()->kas_trxs()->whereMonth('tanggal', $bulan)->orderBy('tanggal')->get();
        $i = 0;
        $a= array();
        foreach ($trx as $key => $item) {
            $kodebku[$i]=null;
            $uraian[$i]=null;
            $nomorbukti[$i]=null;
            $nominalpendapatan[$i]=0;
            $nominalbelanja[$i]=0;

            if ($item->io == 'o') {
                if ($item->kas =='B') {
                    // $a[]= $item->belanja->rka;
                    $belanja= $item->belanja;
                    $kodebku[$i]  = $belanja->rka->kode_program_id."/";
                    $kodebku[$i] .= $belanja->rka->rekening->parent->kode_rekening.".";
                    $kodebku[$i] .= $belanja->rka->rekening->kode_rekening."/";
                    $kodebku[$i] .= $belanja->rka->kp->kode_komponen;
                    $uraian[$i]   = $belanja->nama;
                    $nominalbelanja[$i] = $item->belanja->nilai; 
                    $nomorbukti[$i] = $belanja->nomor;

                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i];
                    $bku_content[$i+1][2]= $nomorbukti[$i];
                    $bku_content[$i+1][3]= $uraian[$i];
                    $bku_content[$i+1][4]= $nominalpendapatan[$i];
                    $bku_content[$i+1][5]= $nominalbelanja[$i];
                    $i++;

                    if (($item->belanja->ppn)!=0) {
                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menerima PPN';
                        $bku_content[$i+1][4]= $item->belanja->ppn;
                        $bku_content[$i+1][5]= 0;
                        $i++;

                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menyetorkan PPN';
                        $bku_content[$i+1][4]= 0;
                        $bku_content[$i+1][5]= $item->belanja->ppn;
                        $i++; 
                    }

                    if (($item->belanja->pph21)!=0) {
                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menerima PPh 21';
                        $bku_content[$i+1][4]= $item->belanja->pph21;
                        $bku_content[$i+1][5]= 0;
                        $i++;

                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menyetorkan PPh 21';
                        $bku_content[$i+1][4]= 0;
                        $bku_content[$i+1][5]= $item->belanja->pph21;
                        $i++; 
                    }

                    if (($item->belanja->pph23)!=0) {
                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menerima PPh 23';
                        $bku_content[$i+1][4]= $item->belanja->pph23;
                        $bku_content[$i+1][5]= 0;
                        $i++;

                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menyetorkan PPh 23';
                        $bku_content[$i+1][4]= 0;
                        $bku_content[$i+1][5]= $item->belanja->pph23;
                        $i++; 
                    }
                }
            }

            else {

                if ($item->io == 'i') {
                    if ($item->kas =='B') {
                        // $a[]= $item->pendapatan;
                        $kodebku[$i] = "Pendapatan ".$item->pendapatan->sumber;
                        $uraian[$i] = $item->pendapatan->keterangan;
                        $nominalpendapatan[$i] = $item->pendapatan->nominal;
                        
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i];
                        $bku_content[$i+1][2]= $nomorbukti[$i];
                        $bku_content[$i+1][3]= $uraian[$i];
                        $bku_content[$i+1][4]= $nominalpendapatan[$i];
                        $bku_content[$i+1][5]= $nominalbelanja[$i];
                    }
                }

                else if (empty($item->io)) {
                    $a[]= $item->kas_trx_detail;
                    $kodebku[$i] = $item->kas_trx_detail->tipe;

                    switch ($kodebku[$i]) {
                        case 'Pindah Buku':
                            $uraian[$i] = 'Pemindahbukuan';
                            $nominalbelanja[$i] = $item->kas_trx_detail->nominal;
                            break;

                        case 'Setor Kembali':
                            $uraian[$i] = 'Setor Sisa Kas';
                            $nominalpendapatan[$i] = $item->kas_trx_detail->nominal;
                            break;
                        
                        default:
                            $uraian[$i] = 'Bunga';
                            $nominalpendapatan[$i] = $item->kas_trx_detail->nominal;
                            $nominalbelanja[$i] = $item->kas_trx_detail->nominal;
                            break;
                    }

                    /*if ($uraian[$i]== 'Bunga') {
                        continue;
                    }*/

                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i];
                    $bku_content[$i+1][2]= $nomorbukti[$i];
                    $bku_content[$i+1][3]= $uraian[$i];
                    $bku_content[$i+1][4]= $nominalpendapatan[$i];
                    $bku_content[$i+1][5]= $nominalbelanja[$i];
                }

                

                $i++;
            }
            // $i++;
        }

        // return json_encode($bku_content);
        $spreadsheet = IOFactory::load('storage/format/buku_bank.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('desa_kecamatan')->setValue($desa_kecamatan);
        $worksheet->getCell('periode')->setValue("BULAN ".strtoupper($periode));
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue($nip_kepsek);
        $worksheet->getCell('nama_bendahara')->setValue($nama_bendahara);
        $worksheet->getCell('nip_bendahara')->setValue($nip_bendahara);
        
              
        $worksheet->fromArray(
            $bku_content,
            NULL,
            'B12'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('B11:I211');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('I');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            'A'
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Buku_Bank_bulan_'.$bulan."-".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;

    }

    public function bukutunai()
    {
        return view('sekolah.laporan.bukutunai');
    }

    public function proses_bukutunai(Request $request)
    {
        $sekolah = Auth::user();
        $nama_sekolah = $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= "NIP.".$sekolah->nip_kepsek;
        $nama_bendahara= $sekolah->nama_bendahara;
        $nip_bendahara= "NIP.".$sekolah->nip_bendahara;
        $nama_kecamatan= $sekolah->kecamatan->nama_kecamatan;
        $desa= (!empty($sekolah->desa)) ? strtoupper($sekolah->desa) : "-" ;
        $desa_kecamatan=$desa." / ".$nama_kecamatan;
        $ta = $request->cookie('ta');
        $bulan = $request->bulan;
        $tanggal = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->endOfMonth();
        $tempat_tanggal = "Kab. Semarang, ".$tanggal->locale('id_ID')->isoFormat('LL');
        $periode= IntBulan($bulan);

        // return $periode;

        $bku_content= array();

        if ($bulan==1) {
            $saldo_awal = 0;
            // Carbon::parse('first day of january '.$ta)->locale('id_ID')->isoFormat('DD MMM YY');
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Awal';
            $bku_content[0][4]= $saldo_awal;
            $bku_content[0][5]= null;
        }
        else {
            $fromDate = Carbon::createFromFormat("!Y-n-j", $ta."-1-1");
            $tillDate = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->startOfMonth();
            $saldo_awal = Auth::user()->saldo_awals()->where([
                'ta' => $ta,
                'periode' => $tillDate
            ])->first();
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Bulan Lalu';
            $bku_content[0][4]= $saldo_awal->saldo_bank + $saldo_awal->saldo_tunai;
            $bku_content[0][5]= 0;
        }

        $kodebku = $uraian = $nomorbukti = $nominalpendapatan = $nominalbelanja = array();
        $trx = Auth::user()->kas_trxs()->whereMonth('tanggal', $bulan)->orderBy('tanggal')->get();
        $i = 0;
        $a= array();
        foreach ($trx as $key => $item) {
            $kodebku[$i]=null;
            $uraian[$i]=null;
            $nomorbukti[$i]=null;
            $nominalpendapatan[$i]=0;
            $nominalbelanja[$i]=0;

            if ($item->io == 'o') {
                if ($item->kas =='T') {
                    // $a[]= $item->belanja->rka;
                    $belanja= $item->belanja;
                    $kodebku[$i]  = $belanja->rka->kode_program_id."/";
                    $kodebku[$i] .= $belanja->rka->rekening->parent->kode_rekening.".";
                    $kodebku[$i] .= $belanja->rka->rekening->kode_rekening."/";
                    $kodebku[$i] .= $belanja->rka->kp->kode_komponen;
                    $uraian[$i]   = $belanja->nama;
                    $nominalbelanja[$i] = $item->belanja->nilai; 
                    $nomorbukti[$i] = $belanja->nomor;

                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i];
                    $bku_content[$i+1][2]= $nomorbukti[$i];
                    $bku_content[$i+1][3]= $uraian[$i];
                    $bku_content[$i+1][4]= $nominalpendapatan[$i];
                    $bku_content[$i+1][5]= $nominalbelanja[$i];
                    $i++;

                    if (($item->belanja->ppn)!=0) {
                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menerima PPN';
                        $bku_content[$i+1][4]= $item->belanja->ppn;
                        $bku_content[$i+1][5]= 0;
                        $i++;

                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menyetorkan PPN';
                        $bku_content[$i+1][4]= 0;
                        $bku_content[$i+1][5]= $item->belanja->ppn;
                        $i++; 
                    }

                    if (($item->belanja->pph21)!=0) {
                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menerima PPh 21';
                        $bku_content[$i+1][4]= $item->belanja->pph21;
                        $bku_content[$i+1][5]= 0;
                        $i++;

                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menyetorkan PPh 21';
                        $bku_content[$i+1][4]= 0;
                        $bku_content[$i+1][5]= $item->belanja->pph21;
                        $i++; 
                    }

                    if (($item->belanja->pph23)!=0) {
                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menerima PPh 23';
                        $bku_content[$i+1][4]= $item->belanja->pph23;
                        $bku_content[$i+1][5]= 0;
                        $i++;

                        $kodebku[$i] = $kodebku[$i-1];
                        $nomorbukti[$i] = $nomorbukti[$i-1];
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i-1];
                        $bku_content[$i+1][2]= $nomorbukti[$i-1];
                        $bku_content[$i+1][3]= 'Menyetorkan PPh 23';
                        $bku_content[$i+1][4]= 0;
                        $bku_content[$i+1][5]= $item->belanja->pph23;
                        $i++; 
                    }
                }
            }

            else {

                if ($item->io == 'i') {
                    if ($item->kas =='T') {
                        // $a[]= $item->pendapatan;
                        $kodebku[$i] = "Pendapatan ".$item->pendapatan->sumber;
                        $uraian[$i] = $item->pendapatan->keterangan;
                        $nominalpendapatan[$i] = $item->pendapatan->nominal;
                        
                        $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                        $bku_content[$i+1][1]= $kodebku[$i];
                        $bku_content[$i+1][2]= $nomorbukti[$i];
                        $bku_content[$i+1][3]= $uraian[$i];
                        $bku_content[$i+1][4]= $nominalpendapatan[$i];
                        $bku_content[$i+1][5]= $nominalbelanja[$i];
                    }
                }

                else if (empty($item->io)) {
                    $a[]= $item->kas_trx_detail;
                    $kodebku[$i] = $item->kas_trx_detail->tipe;

                    switch ($kodebku[$i]) {
                        case 'Pindah Buku':
                            $uraian[$i] = 'Pemindahbukuan';
                            $nominalpendapatan[$i] = $item->kas_trx_detail->nominal;
                            break;

                        case 'Setor Kembali':
                            $uraian[$i] = 'Setor Sisa Kas';
                            $nominalbelanja[$i] = $item->kas_trx_detail->nominal;
                            break;
                        
                        default:
                            $uraian[$i] = 'Bunga';
                            break;   
                    }

                    if ($uraian[$i]== 'Bunga') {
                        continue;
                    }

                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i];
                    $bku_content[$i+1][2]= $nomorbukti[$i];
                    $bku_content[$i+1][3]= $uraian[$i];
                    $bku_content[$i+1][4]= $nominalpendapatan[$i];
                    $bku_content[$i+1][5]= $nominalbelanja[$i];
                }

                

                $i++;
            }
            // $i++;
        }

        // return json_encode($bku_content);
        $spreadsheet = IOFactory::load('storage/format/buku_tunai.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('desa_kecamatan')->setValue($desa_kecamatan);
        $worksheet->getCell('periode')->setValue("BULAN ".strtoupper($periode));
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue($nip_kepsek);
        $worksheet->getCell('nama_bendahara')->setValue($nama_bendahara);
        $worksheet->getCell('nip_bendahara')->setValue($nip_bendahara);
        
              
        $worksheet->fromArray(
            $bku_content,
            NULL,
            'B12'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('B11:I211');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('I');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            'A'
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Buku_Tunai_bulan_'.$bulan."-".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;

    }

    public function bukupajak()
    {
        return view('sekolah.laporan.bukupajak');
    }

    public function proses_bukupajak(Request $request)
    {
        $sekolah = Auth::user();
        $nama_sekolah = $sekolah->name;
        $nama_kepsek= $sekolah->nama_kepsek;
        $nip_kepsek= "NIP.".$sekolah->nip_kepsek;
        $nama_bendahara= $sekolah->nama_bendahara;
        $nip_bendahara= "NIP.".$sekolah->nip_bendahara;
        $nama_kecamatan= $sekolah->kecamatan->nama_kecamatan;
        $desa= (!empty($sekolah->desa)) ? strtoupper($sekolah->desa) : "-" ;
        $desa_kecamatan=$desa." / ".$nama_kecamatan;
        $ta = $request->cookie('ta');
        $bulan = $request->bulan;
        $tanggal = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->endOfMonth();
        $tempat_tanggal = "Kab. Semarang, ".$tanggal->locale('id_ID')->isoFormat('LL');
        $periode= IntBulan($bulan);

        // return $periode;

        $bku_content= array();

        /*if ($bulan==1) {
            $saldo_awal = 0;
            // Carbon::parse('first day of january '.$ta)->locale('id_ID')->isoFormat('DD MMM YY');
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Awal';
            $bku_content[0][4]= $saldo_awal;
            $bku_content[0][5]= null;
        }
        else {
            $fromDate = Carbon::createFromFormat("!Y-n-j", $ta."-1-1");
            $tillDate = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan)."-1")->startOfMonth();
            $saldo_awal = Auth::user()->saldo_awals()->where([
                'ta' => $ta,
                'periode' => $tillDate
            ])->first();
            $bku_content[0][0]= null;
            $bku_content[0][1]= null;
            $bku_content[0][2]= null;
            $bku_content[0][3]= 'Saldo Bulan Lalu';
            $bku_content[0][4]= $saldo_awal->saldo_bank + $saldo_awal->saldo_tunai;
            $bku_content[0][5]= 0;
        }*/

        $kodebku = $uraian = $nomorbukti = $nominalpendapatan = $nominalbelanja = array();
        $trx = Auth::user()->kas_trxs()->whereMonth('tanggal', $bulan)->orderBy('tanggal')->get();
        $i = 0;
        $a= array();
        foreach ($trx as $key => $item) {
            $kodebku[$i]=null;
            $uraian[$i]=null;
            $nomorbukti[$i]=null;
            $nominalpendapatan[$i]=0;
            $nominalbelanja[$i]=0;

            if ($item->io == 'o') {
                // $a[]= $item->belanja->rka;
                $belanja= $item->belanja;
                $kodebku[$i]  = $belanja->rka->kode_program_id."/";
                $kodebku[$i] .= $belanja->rka->rekening->parent->kode_rekening.".";
                $kodebku[$i] .= $belanja->rka->rekening->kode_rekening."/";
                $kodebku[$i] .= $belanja->rka->kp->kode_komponen;
                $nomorbukti[$i] = $belanja->nomor;
                $i++;

                if (($item->belanja->ppn)!=0) {
                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menerima PPN';
                    $bku_content[$i+1][4]= $item->belanja->ppn;
                    $bku_content[$i+1][5]= 0;
                    $i++;

                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menyetorkan PPN';
                    $bku_content[$i+1][4]= 0;
                    $bku_content[$i+1][5]= $item->belanja->ppn;
                    $i++; 
                }

                if (($item->belanja->pph21)!=0) {
                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menerima PPh 21';
                    $bku_content[$i+1][4]= $item->belanja->pph21;
                    $bku_content[$i+1][5]= 0;
                    $i++;

                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menyetorkan PPh 21';
                    $bku_content[$i+1][4]= 0;
                    $bku_content[$i+1][5]= $item->belanja->pph21;
                    $i++; 
                }

                if (($item->belanja->pph23)!=0) {
                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menerima PPh 23';
                    $bku_content[$i+1][4]= $item->belanja->pph23;
                    $bku_content[$i+1][5]= 0;
                    $i++;

                    $kodebku[$i] = $kodebku[$i-1];
                    $nomorbukti[$i] = $nomorbukti[$i-1];
                    $bku_content[$i+1][0]= $item->tanggal->locale('id_ID')->isoFormat('DD MMM YY');
                    $bku_content[$i+1][1]= $kodebku[$i-1];
                    $bku_content[$i+1][2]= $nomorbukti[$i-1];
                    $bku_content[$i+1][3]= 'Menyetorkan PPh 23';
                    $bku_content[$i+1][4]= 0;
                    $bku_content[$i+1][5]= $item->belanja->pph23;
                    $i++; 
                }
            }
            // $i++;
        }

        // return json_encode($bku_content);
        $spreadsheet = IOFactory::load('storage/format/buku_pajak.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('desa_kecamatan')->setValue($desa_kecamatan);
        $worksheet->getCell('periode')->setValue("BULAN ".strtoupper($periode));
        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue($nip_kepsek);
        $worksheet->getCell('nama_bendahara')->setValue($nama_bendahara);
        $worksheet->getCell('nip_bendahara')->setValue($nip_bendahara);
        
              
        $worksheet->fromArray(
            $bku_content,
            NULL,
            'B12'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('B11:I211');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('I');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            'A'
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Buku_Pajak_bulan_'.$bulan."-".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;

    }
}
