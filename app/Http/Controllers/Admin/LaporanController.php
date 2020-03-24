<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Sekolah;
use App\KodeRekening;

class LaporanController extends Controller
{
    public function rkaall()
    {
    	return view('admin.laporan.rkaall');
    }

    public function proses_rkaall(Request $request)
    {
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
}
