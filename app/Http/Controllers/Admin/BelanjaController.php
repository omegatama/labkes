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
    		$query = Belanja::ta($ta)
    		->with('rka.rekening','rka.program','rka.kp','rka.kegiatan','sekolah');

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
            ->setRowClass(function ($belanja) {
                return $belanja->keterangan == -1 ? 'bg-darken-1 bg-red text-white' : '';
            })
            ->addIndexColumn()
            ->make(true);
    	}

    	return view('admin.belanja.index');
    }

    public function modal($id)
    {
    	$belanja = Belanja::modal()->findOrFail($id);
    	return view('admin.belanja.modal.detail', compact('belanja'));
    }

    public function getmodal($id)
    {
    	// if(request()->ajax()) {
            $ta = Cookie::get('ta');
            $belanja = Belanja::findOrFail($id);
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
            
            ->addIndexColumn()
            ->make(true);
            // return $belanja;
        // }
    }

    public function persediaan($id)
    {
    	$belanja = Belanja::persediaan()->findOrFail($id);
    	return view('admin.belanja.persediaan.detail', compact('belanja'));
    }

    public function getpersediaan($id)
    {
    	// if(request()->ajax()) {
            $ta = Cookie::get('ta');
            $belanja = Belanja::findOrFail($id);
            $query = $belanja->persediaans()->ta($ta)->with('barang_persediaan');
            $bpersediaan = $belanja->persediaans()->ta($ta)->with('barang_persediaan')->get();
            
            return DataTables::eloquent($query)
            // ->filter(function ($query) use ($ta) {
            //     $query->where('ta', '=', $ta);
            // },true)
            ->with('total', function() use ($bpersediaan) {
                $total=0;
                foreach ($bpersediaan as $key => $item) {
                    $total += $item->qty * $item->barang_persediaan->harga_satuan;
                }
                return FormatMataUang($total);
            })
            ->editColumn('barang_persediaan.harga_satuan', function ($belanjapersediaan) {
                return FormatMataUang($belanjapersediaan->barang_persediaan->harga_satuan);
            })
            ->addColumn('total', function ($belanjapersediaan) {
                return FormatMataUang(($belanjapersediaan->qty * $belanjapersediaan->barang_persediaan->harga_satuan));
            })
            /*->addColumn('action', function($belanjapersediaan) {
                $urledit= route('sekolah.belanja.editpersediaan', ['id' => $belanjapersediaan->belanja_id, 'modal_id' => $belanjapersediaan->id]);
                $urlhapus= route('sekolah.belanja.destroypersediaan', ['id' => $belanjapersediaan->belanja_id, 'modal_id' => $belanjapersediaan->id]);
                
                $btnaction =
                    RenderTombol("success", $urledit, "Edit")." ".
                    RenderTombol("danger confirmation", $urlhapus, "Hapus");

                return $btnaction;
            })*/
            ->addIndexColumn()
            ->make(true);
            // return $belanja;
        // }
    }
}
