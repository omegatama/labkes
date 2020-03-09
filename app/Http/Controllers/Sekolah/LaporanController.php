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
    	# code...
    }

    public function proses_bku(Request $request)
    {
    	# code...
    }
}
