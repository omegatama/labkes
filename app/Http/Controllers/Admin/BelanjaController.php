<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Belanja;
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
    		$query = Belanja::ta($ta)->with('rka.rekening','rka.program','rka.kp','rka.kegiatan','sekolah');

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
                $jenisrekening= $belanja->rka->rekening->jenis; //1: Belanja Modal //2: Belanja Persediaan

                switch ($jenisrekening) {
                	case '1':
                		$urltambahan = route('admin.belanja.modal', ['id' => $belanja->id]);
                		$labeltambahan = "Belanja Modal";
                		break;

                	case '2':
                		$urltambahan = route('admin.belanja.persediaan', ['id' => $belanja->id]);
                		$labeltambahan = "Belanja Persediaan";
                		break;
                	
                	default:
                		$urltambahan = "";
                		break;
                }

                /*$btnaction =
	                RenderTombol("success", $urledit, "Edit")." ".
	                RenderTombol("danger confirmation", $urlhapus, "Hapus")." ".
	                RenderTombol("info", $urla2, "Download A2")." ";*/

	            $btnaction = (!empty($urltambahan)) ? RenderTombol("warning", $urltambahan, $labeltambahan) : "-" ;
	            return $btnaction;
	            // return 0;
            })
            ->addIndexColumn()
            ->make(true);
    	}

    	return view('admin.belanja.index');
    }
}
