<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Sekolah;
use App\Kecamatan;
use App\KodeRekening;
use App\KodeProgram;
use App\KomponenPembiayaan;
use App\BelanjaModal;
use App\BelanjaPersediaan;
use App\PersediaanTrx;

class LaporanController extends Controller
{
    public function rkaall()
    {
    	return view('admin.laporan.rkaall');
    }

    public function proses_rkaall(Request $request)
    {
        set_time_limit(1800);
    	$sekolah = Sekolah::where('id', '>', '2');
    	$ta = $request->cookie('ta');

    	if ($request->filled('kecamatan_id')) {
    		// return $request->kecamatan_id;
    		$sekolah->kecamatanId($request->kecamatan_id);
    	}

    	if ($request->filled('status')) {
    		// return $request->status;
    		$sekolah->status($request->status);
    	}

    	if ($request->filled('jenjang')) {
    		// return $request->jenjang;
    		$sekolah->jenjang($request->jenjang);
    	}

		$filteredSekolah = $sekolah->has('rkas')->get();
		// return $filteredSekolah;
		$data = array();
		$rekening = KodeRekening::whereNotNull('parent_id')->orderBy('parent_id')->get();

		foreach ($filteredSekolah as $key => $item) {
			$data[$key]['npsn'] = $item->npsn;
			$data[$key]['nama_sekolah'] = $item->name;
			$data[$key]['kecamatan'] = $item->kecamatan->nama_kecamatan;

			for ($tw=0; $tw < 4 ; $tw++) { 
				foreach ($rekening as $key_rek => $rek) {
					$alokasi_per_tw= 'alokasi_tw'.($tw+1);
					$rka_per_tw_per_rek = $item->rkas()->ta($ta)->rekeningId($rek->id)->sum($alokasi_per_tw);
					$data[$key]['tw'.($tw+1).'_'.$rek->parent->kode_rekening.".".$rek->kode_rekening] = $rka_per_tw_per_rek;//0;
				}
			}
		}

		// return $data;
		// Excel
        $spreadsheet = IOFactory::load('storage/format/rkaall.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->fromArray(
            $data,
            null,
            'B3'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('A2:HE605');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('HE');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            "A"
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'RKA_all_TA_'.$ta.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function rka()
    {
    	return view('admin.laporan.rka');
    }
	
	public function proses_rka(Request $request)
    {
    	$sekolah= Sekolah::npsn($request->npsn)->firstOrFail();
        $ta = $request->cookie('ta');
        $programs = KodeProgram::all();
        $kegiatans = $sekolah->kegiatans()->get();
        $nama_sekolah = $sekolah->name;
        $desa_kecamatan = $sekolah->desa." / ".$sekolah->kecamatan->nama_kecamatan;
        $nama_kepsek = $sekolah->nama_kepsek;
        $nip_kepsek = $sekolah->nip_kepsek;

        $rkas = $sekolah->rkas()->where('ta','=',$request->cookie('ta'))->get()->sortBy('parent');
        // return json_encode($rkas->count());
        if ($rkas->count()==0) {
        	return redirect()->back()->withErrors('RKAS '.$sekolah->name.' belum dibuat');
        }

        $sorted = $rkas->sort(function($a, $b) {
            if ($a->parent == $b->parent)
            {
                if ($a->kode_program_id == $b->kode_program_id) {
                    if ($a->kegiatan_id > $b->kegiatan_id) return 1;
                }
                else {
                    if ($a->kode_program_id > $b->kode_program_id) return 1;
                }

            }

            return $a->parent > $b->parent ? 1 : -1;
            
        });

        $rkas_sorted = $sorted->values()->all();

        $parents = [
            [
                'kode' => '5.2.1.05.01',
                'nama' => 'Belanja Pegawai'
            ],
            [
                'kode' => '5.2.2.25.01',
                'nama' => 'Belanja Barang dan Jasa'
            ],
            [
                'kode' => '5.2.3.35.01',
                'nama' => 'Belanja Modal Peralatan dan Mesin'
            ],
            [
                'kode' => '5.2.3.35.02',
                'nama' => 'Belanja Modal Aset Tetap Lainnya'
            ],
            [
                'kode' => '5.2.3.35.03',
                'nama' => 'Belanja Modal Gedung dan Bangunan'
            ]
        ];

        $hasil= array();

        foreach($rkas_sorted as $i => $rka)
        {
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['snp'] = $rka->rekening->parent_id.".".$rka->rekening->kode_rekening;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['uraian'] = $rka->uraian;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['kp'] = $rka->kp->kode_komponen;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['vol'] = $rka->volume;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['sat'] = $rka->satuan;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['harga'] = $rka->harga_satuan;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['jumlah'] = $rka->jumlah;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['tw1'] = $rka->alokasi_tw1;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['tw2'] = $rka->alokasi_tw2;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['tw3'] = $rka->alokasi_tw3;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['tw4'] = $rka->alokasi_tw4;
            // REALISASI
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['realisasi_tw1'] = $rka->realisasi_tw1;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['realisasi_tw2'] = $rka->realisasi_tw2;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['realisasi_tw3'] = $rka->realisasi_tw3;
            $hasil[$rka->parent][$rka->kode_program_id][$rka->kegiatan_id][$i]['realisasi_tw4'] = $rka->realisasi_tw4;
        }

        // return json_encode($kegiatans);
        // return json_encode($hasil);
        // return view('sekolah.rka.cetak',compact('hasil'));

        $baris= array();
        $jumlahperparent= array();
        $jumlahall= array();
        $arraykepala= array();

        $indexbaris=0;

        $jumlahall['jumlah'] = 0;
        $jumlahall['tw1'] = 0;
        $jumlahall['tw2'] = 0;
        $jumlahall['tw3'] = 0;
        $jumlahall['tw4'] = 0;

        foreach ($hasil as $i => $parent) {
            $jumlahperparent[$i]['jumlah'] = 0;
            $jumlahperparent[$i]['tw1'] = 0;
            $jumlahperparent[$i]['tw2'] = 0;
            $jumlahperparent[$i]['tw3'] = 0;
            $jumlahperparent[$i]['tw4'] = 0;

            $baris[$indexbaris]['koderekening'] = $parents[$i-1]['kode'];
            $baris[$indexbaris]['snp'] = '';
            $baris[$indexbaris]['uraian'] = '';
            $baris[$indexbaris]['kp'] = '';
            $baris[$indexbaris]['vol'] = '';
            $baris[$indexbaris]['sat'] = '';
            $baris[$indexbaris]['harga'] = '';
            $baris[$indexbaris]['jumlah'] = '';
            $baris[$indexbaris]['tw1'] = '';
            $baris[$indexbaris]['tw2'] = '';
            $baris[$indexbaris]['tw3'] = '';
            $baris[$indexbaris]['tw4'] = '';

            $indexbaris++;
            $baris[$indexbaris]['koderekening'] = $parents[$i-1]['nama'];
            $baris[$indexbaris]['snp'] = '';
            $baris[$indexbaris]['uraian'] = '';
            $baris[$indexbaris]['kp'] = '';
            $baris[$indexbaris]['vol'] = '';
            $baris[$indexbaris]['sat'] = '';
            $baris[$indexbaris]['harga'] = '';
            $baris[$indexbaris]['jumlah'] = '';
            $baris[$indexbaris]['tw1'] = '';
            $baris[$indexbaris]['tw2'] = '';
            $baris[$indexbaris]['tw3'] = '';
            $baris[$indexbaris]['tw4'] = '';

            $arraykepala[$i] = $indexbaris;

            // $parents[$i-1]['kode'];
            // $parents[$i-1]['nama'];
            foreach ($parent as $j => $program) {
                $indexbaris++;
                $baris[$indexbaris]['koderekening'] = '';
                $baris[$indexbaris]['snp'] = $j;
                $baris[$indexbaris]['uraian'] = $programs->find($j)->nama_program;
                $baris[$indexbaris]['kp'] = '';
                $baris[$indexbaris]['vol'] = '';
                $baris[$indexbaris]['sat'] = '';
                $baris[$indexbaris]['harga'] = '';
                $baris[$indexbaris]['jumlah'] = '';
                $baris[$indexbaris]['tw1'] = '';
                $baris[$indexbaris]['tw2'] = '';
                $baris[$indexbaris]['tw3'] = '';
                $baris[$indexbaris]['tw4'] = '';

                foreach ($program as $k => $kegiatan) {
                    $indexbaris++;
                    $baris[$indexbaris]['koderekening'] = '';
                    $baris[$indexbaris]['snp'] = $j.".".$k." ";
                    /*try {
                        $kegiatans->findOrFail($k)->uraian;
                    } catch (\Exception $e) {
                        return $k;
                    }*/
                    $baris[$indexbaris]['uraian'] = $kegiatans->find($k)->uraian;
                    $baris[$indexbaris]['kp'] = '';
                    $baris[$indexbaris]['vol'] = '';
                    $baris[$indexbaris]['sat'] = '';
                    $baris[$indexbaris]['harga'] = '';
                    $baris[$indexbaris]['jumlah'] = '';
                    $baris[$indexbaris]['tw1'] = '';
                    $baris[$indexbaris]['tw2'] = '';
                    $baris[$indexbaris]['tw3'] = '';
                    $baris[$indexbaris]['tw4'] = '';
                    // REALISASI
                    $baris[$indexbaris]['rtw1'] = '';
                    $baris[$indexbaris]['rtw2'] = '';
                    $baris[$indexbaris]['rtw3'] = '';
                    $baris[$indexbaris]['rtw4'] = '';

                    foreach ($kegiatan as $l => $rkadetail) {
                        # code...
                        $indexbaris++;
                        $baris[$indexbaris]['koderekening'] = '';
                        $baris[$indexbaris]['snp'] = $j.".".$k.".".$rkadetail['snp'];;
                        $baris[$indexbaris]['uraian'] = $rkadetail['uraian'];
                        $baris[$indexbaris]['kp'] = $rkadetail['kp'];
                        $baris[$indexbaris]['vol'] = $rkadetail['vol'];
                        $baris[$indexbaris]['sat'] = $rkadetail['sat'];
                        $baris[$indexbaris]['harga'] = $rkadetail['harga'];
                        $baris[$indexbaris]['jumlah'] = $rkadetail['jumlah'];
                        $baris[$indexbaris]['tw1'] = $rkadetail['tw1'];
                        $baris[$indexbaris]['tw2'] = $rkadetail['tw2'];
                        $baris[$indexbaris]['tw3'] = $rkadetail['tw3'];
                        $baris[$indexbaris]['tw4'] = $rkadetail['tw4'];
                        // REALISASI
                        $baris[$indexbaris]['rtw1'] = $rkadetail['realisasi_tw1'];
                        $baris[$indexbaris]['rtw2'] = $rkadetail['realisasi_tw2'];
                        $baris[$indexbaris]['rtw3'] = $rkadetail['realisasi_tw3'];
                        $baris[$indexbaris]['rtw4'] = $rkadetail['realisasi_tw4'];

                        // Hitung
                        $jumlahperparent[$i]['jumlah'] += $rkadetail['jumlah'];
                        $jumlahperparent[$i]['tw1'] += $rkadetail['tw1'];
                        $jumlahperparent[$i]['tw2'] += $rkadetail['tw2'];
                        $jumlahperparent[$i]['tw3'] += $rkadetail['tw3'];
                        $jumlahperparent[$i]['tw4'] += $rkadetail['tw4'];

                        //Hitung Lagi
                        $jumlahall['jumlah'] += $rkadetail['jumlah'];
                        $jumlahall['tw1'] += $rkadetail['tw1'];
                        $jumlahall['tw2'] += $rkadetail['tw2'];
                        $jumlahall['tw3'] += $rkadetail['tw3'];
                        $jumlahall['tw4'] += $rkadetail['tw4'];
                    }
                }
            }
            $indexbaris++;

        }

        foreach ($arraykepala as $a => $item) {
            $baris[$item]['jumlah'] = $jumlahperparent[$a]['jumlah'];
            $baris[$item]['tw1'] = $jumlahperparent[$a]['tw1'];
            $baris[$item]['tw2'] = $jumlahperparent[$a]['tw2'];
            $baris[$item]['tw3'] = $jumlahperparent[$a]['tw3'];
            $baris[$item]['tw4'] = $jumlahperparent[$a]['tw4'];

        }

        // return json_encode($baris);
        // Excel
        $spreadsheet = IOFactory::load('storage/format/rkas-2.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('nama_sekolah')->setValue($nama_sekolah);
        $worksheet->getCell('desa_kecamatan')->setValue($desa_kecamatan);
        $worksheet->getCell('ta')->setValue($ta);

        $worksheet->insertNewRowBefore(17 ,count($baris));
        $worksheet->fromArray(
            $baris,
            NULL,
            'B16'
        );

        $worksheet->getCell('sum_all')->setValue($jumlahall['jumlah']);

        $worksheet->getCell('sum_tw1')->setValue($jumlahall['tw1']);

        $worksheet->getCell('sum_tw2')->setValue($jumlahall['tw2']);

        $worksheet->getCell('sum_tw3')->setValue($jumlahall['tw3']);

        $worksheet->getCell('sum_tw4')->setValue($jumlahall['tw4']);

        $worksheet->getCell('nama_kepsek')->setValue($nama_kepsek);
        $worksheet->getCell('nip_kepsek')->setValue($nip_kepsek);

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Rkas'."_".$sekolah->npsn.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function realisasi()
    {
        return view('admin.laporan.realisasi');
    }

    public function proses_realisasi(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        $triwulan = $request->triwulan;

        $triwulan1= [1 ,2 ,3 ];
        $triwulan2= [4 ,5 ,6 ];
        $triwulan3= [7 ,8 ,9 ];
        $triwulan4= [10,11,12];

        $bulan = ${"triwulan".$triwulan};
        $bulan1= IntBulan($bulan[0]);
        $bulan2= IntBulan($bulan[1]);
        $bulan3= IntBulan($bulan[2]);

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }

        $filteredSekolah = $sekolah->has('rkas')->get();
        $data = array();
        $rekening = KodeRekening::whereNotNull('parent_id')->orderBy('parent_id')->get();
        
        foreach ($filteredSekolah as $key => $item) {
            $data[$key]['npsn'] = $item->npsn;
            $data[$key]['nama_sekolah'] = $item->name;
            $data[$key]['kecamatan'] = $item->kecamatan->nama_kecamatan;

            foreach ($bulan as $key_bln => $bln) {
                foreach ($rekening as $key_rek => $rek) {
                    $belanja_per_bln_per_rek= $item->belanjas()->ta($ta)->triwulan($triwulan)->whereMonth('tanggal', $bln)->rekening($rek->id)->sum('nilai');
                    $data[$key]['bulan'.($bln).'_'.$rek->parent->kode_rekening.".".$rek->kode_rekening] = $belanja_per_bln_per_rek;//0;
                }
            }
        }
        
        // return $data;
        $spreadsheet = IOFactory::load('storage/format/lap_realisasi_admin.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->fromArray(
            $data,
            null,
            'B3'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('A2:FE201');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('FE');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            "A"
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Realisasi_TA_'.$ta.'_TW_'.$triwulan.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
        
    }

    public function modal()
    {
        return view('admin.laporan.modal');
    }
    
    public function proses_modal(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        $triwulan = $request->triwulan;

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }

        $filteredSekolah = $sekolah->has('belanjas')->get();
        $data = array();
        // return $request;
        $i=0;
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $belanja= $item->belanjas()->modal()->ta($ta)->triwulan($triwulan)->get();
            if ($belanja->isNotEmpty()) {
                $belanjamodal = BelanjaModal::npsn($item->npsn)->ta($ta)->triwulan($triwulan)->get();
                foreach ($belanjamodal as $key => $modal) {
                    $data[$i]['npsn'] = $item->npsn;
                    $data[$i]['nama_sekolah'] = $item->name;
                    $data[$i]['kecamatan'] = $item->kecamatan->nama_kecamatan;
                    
                    $data[$i]['kode_barang']= $modal->kode_barang->kode_barang;
                    $data[$i]['nama_barang']= $modal->nama_barang;
                    $data[$i]['merek']= $modal->merek;
                    $data[$i]['warna']= $modal->warna;
                    $data[$i]['tipe']= $modal->tipe;
                    $data[$i]['bahan']= $modal->bahan;
                    $data[$i]['bukti_tanggal']= date('d', strtotime($modal->tanggal_bukti));
                    $data[$i]['bukti_bulan']= IntBulan(date('n', strtotime($modal->tanggal_bukti)));
                    $data[$i]['bukti_nomor']= $modal->nomor_bukti;
                    $data[$i]['qty']= $modal->qty;
                    $data[$i]['satuan']= $modal->satuan;
                    $data[$i]['jenis']= $modal->belanja->rka->rekening->parent_id;
                    $data[$i]['harga_satuan']= $modal->harga_satuan;
                    
                    $i++;
                }
                
            }
        }
        // return $data;

        // return $data;
        $spreadsheet = IOFactory::load('storage/format/lap_modal_admin.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->fromArray(
            $data,
            null,
            'B3'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('A2:T305');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('T');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            "A"
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'B_Modal_TA_'.$ta.'_TW_'.$triwulan.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function pajak()
    {
        return view('admin.laporan.pajak');
    }
    
    public function proses_pajak(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        $triwulan = $request->triwulan;
        // $bulan = $request->bulan;

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }

        $filteredSekolah = $sekolah->has('belanjas')->get();
        $data = array();
        // return $filteredSekolah;
        $i=0;
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $pajak_belanja= $item->belanjas()
            ->ta($ta)
            // ->whereMonth('tanggal', $bulan)
            ->where('triwulan', $triwulan)
            ->where(function ($query){
                $query->where('ppn','>',0)
                ->orWhere('pph21','>',0)
                ->orWhere('pph23','>',0)
                ->orderBy('tanggal');
            })
            ->get();

            if ($pajak_belanja->isNotEmpty()) {
                foreach ($pajak_belanja as $key_pajak => $pajak) {
                    $npsn         = $item->npsn;
                    $nama_sekolah = $item->name;
                    $kecamatan    = $item->kecamatan->nama_kecamatan;
                    $tanggal      = $pajak->tanggal->locale('id_ID')->isoFormat('LL');
                    $rekening     = $pajak->rka->rekening->parent->kode_rekening.'.';
                    $rekening     .=$pajak->rka->rekening->kode_rekening;
                    // PPN
                    if ($pajak->ppn > 0) {
                        $data[$i]['npsn'] = $npsn;
                        $data[$i]['nama_sekolah'] = $nama_sekolah;
                        $data[$i]['kecamatan'] = $kecamatan;
                        $data[$i]['jenis'] = 'PPN';
                        $data[$i]['tanggal'] = $tanggal;
                        $data[$i]['jumlah'] = $pajak->ppn;
                        $data[$i]['rekening'] = $rekening;
                        $i++;
                    }

                    // PPH21
                    if ($pajak->pph21 > 0) {
                        $data[$i]['npsn'] = $npsn;
                        $data[$i]['nama_sekolah'] = $nama_sekolah;
                        $data[$i]['kecamatan'] = $kecamatan;
                        $data[$i]['jenis'] = 'PPh 21';
                        $data[$i]['tanggal'] = $tanggal;
                        $data[$i]['jumlah'] = $pajak->pph21;
                        $data[$i]['rekening'] = $rekening;
                        $i++;
                    }

                    // PPH23
                    if ($pajak->pph23 > 0) {
                        $data[$i]['npsn'] = $npsn;
                        $data[$i]['nama_sekolah'] = $nama_sekolah;
                        $data[$i]['kecamatan'] = $kecamatan;
                        $data[$i]['jenis'] = 'PPh 23';
                        $data[$i]['tanggal'] = $tanggal;
                        $data[$i]['jumlah'] = $pajak->pph23;
                        $data[$i]['rekening'] = $rekening;
                        $i++;
                    }
                }
            }
        }

        // return $data;
        $spreadsheet = IOFactory::load('storage/format/lap_pajak_admin.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->fromArray(
            $data,
            null,
            'B3'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('A2:I204');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('I');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            "A"
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Pajak_TA_'.$ta.'_TW_'.$triwulan.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function saldo()
    {
        return view('admin.laporan.saldo');
    }
    
    public function proses_saldo(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        // $triwulan = $request->triwulan;

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }

        $filteredSekolah = $sekolah->get();
        // $data = array();
        // return $filteredSekolah;
        $i=0;
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $data[$i]['npsn'] = $item->npsn;
            $data[$i]['nama_sekolah'] = $item->name;
            $data[$i]['kecamatan'] = $item->kecamatan->nama_kecamatan;
            
            $saldo_tunai= array();
            $saldo_bank= array();

            for ($j=1; $j <= 12 ; $j++) {
                
                if ($j==12) {
                    $saldotunai= 0;
                    $saldobank= 0;
                }
                else{
                    $fromDate = Carbon::createFromDate($ta, ($j+1), 1)->format('Y-m-d');
                    $saldo_akhir = $item->saldo_awals()
                    ->where('periode', $fromDate)->get();
                    $saldobank = $saldo_akhir->sum('saldo_bank');
                    $saldotunai = $saldo_akhir->sum('saldo_tunai');
                }

                $saldo_tunai[$j] = $saldotunai;
                $saldo_bank[$j] = $saldobank;
            }

            for ($j=1; $j <= 12 ; $j++) {
                $data[$i]['tunai_bln_'.$j] = $saldo_tunai[$j];
            }

            for ($j=1; $j <= 12 ; $j++) {
                $data[$i]['bank_bln_'.$j] = $saldo_bank[$j];
            }

            $i++;
        }
        // return $data;
        $spreadsheet = IOFactory::load('storage/format/lap_saldo_admin.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->fromArray(
            $data,
            null,
            'B3'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('A2:AC605');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('AC');
        $columnFilter->createRule()
        ->setRule(
            \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            "A"
        );

        $autoFilter->showHideRows();

        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'Saldo_TA_'.$ta.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function persediaan()
    {
        return view('admin.laporan.persediaan');
    }
    
    public function proses_persediaan(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        $triwulan = $request->triwulan;

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

        $triwulan1= [1 ,2 ,3 ];
        $triwulan2= [4 ,5 ,6 ];
        $triwulan3= [7 ,8 ,9 ];
        $triwulan4= [10,11,12];

        $bulan = ${"triwulan".$triwulan};
        if ($triwulan > 1) {
            $bulan_sebelumnya = ${"triwulan".($triwulan-1)};
            $bulan_sebelumnya[3] = $bulan[0];
        }
        // return $bulan_sebelumnya;

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }

        $filteredSekolah = $sekolah->get();
        $data = array();
        // return $filteredSekolah;

        $i=0;
        $kode_jenis = array();
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $npsn = $item->npsn;
            $nama_sekolah = $item->name;
            $kecamatan = $item->kecamatan->nama_kecamatan;
            
            $persediaans = $item->persediaans()->get();
        
            $persediaan_all = array();
            $pengeluaran_persediaan = array();
            

            foreach ($persediaans as $key => $persediaan) {
                $data[$i]['npsn'] = $npsn;
                $data[$i]['nama_sekolah'] = $nama_sekolah;
                $data[$i]['kecamatan'] = $kecamatan;
                
                $data[$i]['nama_persediaan'] = $persediaan->nama_persediaan;
                $data[$i]['satuan'] = $persediaan->satuan;
                $data[$i]['harga_satuan'] = $persediaan->harga_satuan;

                $kode_jenis[$i]['kode'] = $persediaan->jenis;

                // ikiyo
                if ($triwulan > 1) {
                    for ($i=3; $i > 0; $i--) { 
                        $saldo= $persediaan->stok_awals()
                            ->where('periode', Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan_sebelumnya[$i])."-1")->startOfMonth())->get();
                        if ($saldo->isNotEmpty()) {
                            break;
                        }
                    }   
                    $saldo= $saldo->sum('stok');
                }
                else{
                    $saldo = 0;
                }
                
                // return AwalTriwulan(($triwulan),$ta)->format('Y-m-d');//$saldo;

                $data[$i]['saldo'] = $saldo;
                
                $penerimaan_1 = 0;
                $penerimaan_2 = 0;
                $penerimaan_3 = 0;

                $pengeluaran_1 = 0;
                $pengeluaran_2 = 0;
                $pengeluaran_3 = 0;

                $trx_masuk_1 = PersediaanTrx::npsn($item->npsn)->ta($ta)->in()->persediaanId($persediaan->id)->whereMonth('tanggal', $bulan[0])->sum('qty');
                $trx_masuk_2 = PersediaanTrx::npsn($item->npsn)->ta($ta)->in()->persediaanId($persediaan->id)->whereMonth('tanggal', $bulan[1])->sum('qty');
                $trx_masuk_3 = PersediaanTrx::npsn($item->npsn)->ta($ta)->in()->persediaanId($persediaan->id)->whereMonth('tanggal', $bulan[2])->sum('qty');

                $belanja_1 = BelanjaPersediaan::npsn($item->npsn)->ta($ta)->triwulan($triwulan)->bulan($bulan[0])->persediaanId($persediaan->id)->sum('qty');
                $belanja_2 = BelanjaPersediaan::npsn($item->npsn)->ta($ta)->triwulan($triwulan)->bulan($bulan[1])->persediaanId($persediaan->id)->sum('qty');
                $belanja_3 = BelanjaPersediaan::npsn($item->npsn)->ta($ta)->triwulan($triwulan)->bulan($bulan[2])->persediaanId($persediaan->id)->sum('qty');

                $penerimaan_1 += $trx_masuk_1 + $belanja_1;
                $penerimaan_2 += $trx_masuk_2 + $belanja_2;
                $penerimaan_3 += $trx_masuk_3 + $belanja_3;

                $trx_keluar_1 = PersediaanTrx::npsn($item->npsn)->ta($ta)->out()->persediaanId($persediaan->id)->whereMonth('tanggal', $bulan[0])->sum('qty');
                $trx_keluar_2 = PersediaanTrx::npsn($item->npsn)->ta($ta)->out()->persediaanId($persediaan->id)->whereMonth('tanggal', $bulan[1])->sum('qty');
                $trx_keluar_3 = PersediaanTrx::npsn($item->npsn)->ta($ta)->out()->persediaanId($persediaan->id)->whereMonth('tanggal', $bulan[2])->sum('qty');

                $pengeluaran_1 += $trx_keluar_1;
                $pengeluaran_2 += $trx_keluar_2;
                $pengeluaran_3 += $trx_keluar_3;

                $data[$i]['penerimaan_1'] = $penerimaan_1;
                $data[$i]['penerimaan_2'] = $penerimaan_2;
                $data[$i]['penerimaan_3'] = $penerimaan_3;
                
                $data[$i]['pengeluaran_1'] = $pengeluaran_1;
                $data[$i]['pengeluaran_2'] = $pengeluaran_2;
                $data[$i]['pengeluaran_3'] = $pengeluaran_3;
                $i++;
            }

        }

        // return $data;
        // Excel
        $spreadsheet = IOFactory::load('storage/format/lap_persediaan_admin.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->getCell('ta')->setValue($ta);
        $worksheet->getCell('twhuruf')->setValue($twhuruf);
        
        $worksheet->fromArray(
            $data,
            null,
            'B10'
        );

        $worksheet->fromArray(
            $kode_jenis,
            null,
            'R10'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('B9:T609');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('T');
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
        $file= 'B_Persediaan_TA_'.$ta.'_TW_'.$triwulan.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function persediaan_tahun()
    {
        return view('admin.laporan.persediaantahun');
    }
    
    public function proses_persediaan_tahun(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }

        $filteredSekolah = $sekolah->get();
        $data = array();
        // return $filteredSekolah;

        $i=0;
        $kode_jenis = array();
        $bulan= $request->bulan;
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $npsn = $item->npsn;
            $nama_sekolah = $item->name;
            $kecamatan = $item->kecamatan->nama_kecamatan;
            
            $persediaans = $item->persediaans()->get();
        
            $persediaan_all = array();
            $pengeluaran_persediaan = array();
            
            $data_keluar = array();
            foreach ($persediaans as $key => $persediaan) {
                $data[$i]['npsn'] = $npsn;
                $data[$i]['nama_sekolah'] = $nama_sekolah;
                $data[$i]['kecamatan'] = $kecamatan;
                
                $data[$i]['nama_persediaan'] = $persediaan->nama_persediaan;
                $data[$i]['satuan'] = $persediaan->satuan;
                $data[$i]['harga_satuan'] = $persediaan->harga_satuan;

                $kode_jenis[$i]['kode'] = $persediaan->jenis;

                $saldo = 0;
                
                // return AwalTriwulan(($triwulan),$ta)->format('Y-m-d');//$saldo;

                $data[$i]['saldo'] = $saldo;
                
                $penerimaan_1 = 0;
                $penerimaan_2 = 0;
                $penerimaan_3 = 0;
                $penerimaan_4 = 0;
                $penerimaan_5 = 0;
                $penerimaan_6 = 0;
                $penerimaan_7 = 0;
                $penerimaan_8 = 0;
                $penerimaan_9 = 0;
                $penerimaan_10 = 0;
                $penerimaan_11 = 0;
                $penerimaan_12 = 0;


                $pengeluaran_1 = 0;
                $pengeluaran_2 = 0;
                $pengeluaran_3 = 0;
                $pengeluaran_4 = 0;
                $pengeluaran_5 = 0;
                $pengeluaran_6 = 0;
                $pengeluaran_7 = 0;
                $pengeluaran_8 = 0;
                $pengeluaran_9 = 0;
                $pengeluaran_10 = 0;
                $pengeluaran_11 = 0;
                $pengeluaran_12 = 0;

                for ($j=0; $j < $bulan ; $j++) { 
                    $trx_masuk_bulan = "trx_masuk_".($j+1);
                    $$trx_masuk_bulan = PersediaanTrx::npsn($npsn)->ta($ta)->in()->persediaanId($persediaan->id)->whereMonth('tanggal', ($j+1))->sum('qty');  
                
                    $belanja_bulan = "belanja_".($j+1);
                    $$belanja_bulan = BelanjaPersediaan::npsn($npsn)->ta($ta)->bulan(($j+1))->persediaanId($persediaan->id)->sum('qty');
                    
                    $penerimaan_bulan = "penerimaan_".($j+1);
                    $$penerimaan_bulan += $$trx_masuk_bulan + $$belanja_bulan;
                
                    $data[$i][$penerimaan_bulan] = $$penerimaan_bulan;
                
                    $trx_keluar_bulan = "trx_keluar_".($j+1);
                    $$trx_keluar_bulan = PersediaanTrx::npsn($npsn)->ta($ta)->out()->persediaanId($persediaan->id)->whereMonth('tanggal', ($j+1))->sum('qty');
                
                    $pengeluaran_bulan = "pengeluaran_".($j+1);
                    $$pengeluaran_bulan += $$trx_keluar_bulan;

                    $data_keluar[$i][$pengeluaran_bulan] = $$pengeluaran_bulan;
                
                
                }

                
                $i++;
            }

        }

        // return $data;
        // Excel
        $spreadsheet = IOFactory::load('storage/format/lap_persediaan_tahun_admin.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        
        $worksheet->getCell('ta')->setValue($ta);
        // $worksheet->getCell('twhuruf')->setValue($twhuruf);
        
        $worksheet->fromArray(
            $data,
            null,
            'B10'
        );

        $worksheet->fromArray(
            $data_keluar,
            null,
            'U10'
        );

        $worksheet->fromArray(
            $kode_jenis,
            null,
            'AS10'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('B9:AU609');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('AU');
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
        $file= 'B_Persediaan_TA_'.$ta.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function k8()
    {
        return view('admin.laporan.k8');
    }
    
    public function proses_k8(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        $triwulan = $request->triwulan;
        
        $judul= "REKAPITULASI REALISASI PENGGUNAAN DANA BOS ".$ta;
        $periode="PERIODE TANGGAL : ".AwalTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL')." s/d ".AkhirTriwulan($triwulan, $ta)->locale('id_ID')->isoFormat('LL')." (Triwulan ".$triwulan." Tahun ".$ta.")";
        // return $periode;
        $nama_kecamatan=$status=$jenjang= "";

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $nama_kecamatan= Kecamatan::find($request->kecamatan_id)->nama_kecamatan;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $status= $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $jenjang= $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }
        // return $jenjang;

        $filteredSekolah = $sekolah->get();
        $data = array();
        // return $filteredSekolah;
        // $i=0;
        $program = KodeProgram::all();
        $komponen = KomponenPembiayaan::all();
        // $program_kp= array();
        // return json_encode($komponen);
        foreach ($program as $key => $p) {
            foreach ($komponen as $kpkey => $kp) {
                $data[$p->id][$kp->id] = 0;
            }
        }
        
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $program_kp= array();
            foreach ($program as $key => $p) {
                foreach ($komponen as $kpkey => $kp) {
                    $program_id=$p->id;
                    $pembiayaan_id=$kp->id;
                    $program_kp_detail= $item->belanjas()->ta($ta)->triwulan($triwulan)
                        ->whereHas('rka', function ($qrka) use ($program_id) {
                            $qrka->where('kode_program_id', $program_id);
                        })
                        ->whereHas('rka', function ($qrka) use ($pembiayaan_id) {
                            $qrka->where('komponen_pembiayaan_id', $pembiayaan_id);
                        })
                        ->sum('nilai');
                    
                    $program_kp[$p->id][$kp->id] = $program_kp_detail;
                    $data[$p->id][$kp->id] += $program_kp[$p->id][$kp->id];
                }
            }
        }

        // return $data;
        $spreadsheet = IOFactory::load('storage/format/k8.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('judul')->setValue($judul);
        $worksheet->getCell('periode')->setValue($periode);
        $worksheet->getCell('jenjang')->setValue($jenjang);
        $worksheet->getCell('status')->setValue($status);
        $worksheet->getCell('kecamatan')->setValue($nama_kecamatan);
        
        
        
        $worksheet->fromArray(
            $data,
            null,
            'E15'
        );
        
        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'K8_TA_'.$ta.'_TW_'.$triwulan.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    public function k8_bulanan()
    {
        return view('admin.laporan.k8_bulanan');
    }
    
    public function proses_k8_bulanan(Request $request)
    {
        set_time_limit(1800);
        $sekolah = Sekolah::where('id', '>', '2');
        $ta = $request->cookie('ta');
        // $triwulan = $request->triwulan;
        $bulan_awal = $request->bulan_awal;
        $bulan_akhir = $request->bulan_akhir;

        $judul= "REKAPITULASI REALISASI PENGGUNAAN DANA BOS ".$ta;
        $fromDate = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan_awal)."-1");
        $tillDate = Carbon::createFromFormat("!Y-n-j", $ta."-".($bulan_akhir)."-1")->endOfMonth();
        
        $periode= $fromDate->locale('id_ID')->isoFormat('LL')." - ".$tillDate->locale('id_ID')->isoFormat('LL');
        
        $nama_kecamatan=$status=$jenjang= "";

        if ($request->filled('kecamatan_id')) {
            // return $request->kecamatan_id;
            $nama_kecamatan= Kecamatan::find($request->kecamatan_id)->nama_kecamatan;
            $sekolah->kecamatanId($request->kecamatan_id);
        }

        if ($request->filled('status')) {
            // return $request->status;
            $status= $request->status;
            $sekolah->status($request->status);
        }

        if ($request->filled('jenjang')) {
            // return $request->jenjang;
            $jenjang= $request->jenjang;
            $sekolah->jenjang($request->jenjang);
        }
        // return $jenjang;

        $filteredSekolah = $sekolah->get();
        $data = array();
        // return $filteredSekolah;
        // $i=0;
        $program = KodeProgram::all();
        $komponen = KomponenPembiayaan::all();
        // $program_kp= array();
        // return json_encode($komponen);
        foreach ($program as $key => $p) {
            foreach ($komponen as $kpkey => $kp) {
                $data[$p->id][$kp->id] = 0;
            }
        }
        
        foreach ($filteredSekolah as $key_sekolah => $item) {
            $program_kp= array();
            foreach ($program as $key => $p) {
                foreach ($komponen as $kpkey => $kp) {
                    $program_id=$p->id;
                    $pembiayaan_id=$kp->id;
                    $program_kp_detail= $item->belanjas()->ta($ta)
                        ->whereBetween('tanggal',[$fromDate,$tillDate])
                        ->whereHas('rka', function ($qrka) use ($program_id) {
                            $qrka->where('kode_program_id', $program_id);
                        })
                        ->whereHas('rka', function ($qrka) use ($pembiayaan_id) {
                            $qrka->where('komponen_pembiayaan_id', $pembiayaan_id);
                        })
                        ->sum('nilai');
                    
                    $program_kp[$p->id][$kp->id] = $program_kp_detail;
                    $data[$p->id][$kp->id] += $program_kp[$p->id][$kp->id];
                }
            }
        }

        // return $data;
        $spreadsheet = IOFactory::load('storage/format/k8.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('judul')->setValue($judul);
        $worksheet->getCell('periode')->setValue($periode);
        $worksheet->getCell('jenjang')->setValue($jenjang);
        $worksheet->getCell('status')->setValue($status);
        $worksheet->getCell('kecamatan')->setValue($nama_kecamatan);
        
        
        
        $worksheet->fromArray(
            $data,
            null,
            'E15'
        );
        
        // Cetak
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'Excel');
        $writer->save($temp_file);
        $file= 'K8_TA_'.$ta.'_Bulan_'.$bulan_awal.'_sd_'.$bulan_akhir.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }
}
