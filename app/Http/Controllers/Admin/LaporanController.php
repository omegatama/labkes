<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Sekolah;
use App\KodeRekening;
use App\KodeProgram;

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
        $spreadsheet = IOFactory::load('storage/format/rkas.xlsx');
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
}
