<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Pagu;
use Auth;
use Cookie;
use Response;
use DataTables;

class PaguController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            // $model = Pagu::query();
            $query = Pagu::with('sekolah')->select('pagus.*');

            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->addColumn('action', function(Pagu $pagu) {
                if ($pagu->pagu==$pagu->sisa) {
                    # code...
                    $urledit= route('admin.pagu.edit', ['id' => $pagu->id]);
                    $fungsidelete= "hapus_pagu($pagu->id)";
                    return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger", "#", "Hapus", $fungsidelete);
                }
                else{
                    return '-';
                }
            })
            ->editColumn('pagu', function ($pagu) {
                return FormatMataUang($pagu->pagu);
            })
            ->filter(function ($query) use ($ta) {
                $query->where('ta', '=', $ta);
            },true)
            ->addIndexColumn()
            ->make(true);
        }

        return view('admin.pagu.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pagu.tambah', ['aksi' => "tambah"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check= Pagu::where([
            'ta' =>$request->cookie('ta'),
            'npsn' => $request->npsn
        ])->doesntExist();

        if ($check) {
            # code...
            $pagu= new Pagu;
            $pagu->ta= $request->cookie('ta');
            $pagu->npsn= $request->npsn;
            $pagu->pagu= floatval(str_replace(",",".",$request->nominal));
            $pagu->sisa= $pagu->pagu;
            if($pagu->save()){
                return redirect()->route('admin.pagu.index');
            }
        }
        else{
            return back()->withErrors(['msg' => 'Pagu untuk '.$request->npsn.' sudah ada!']);
        }
    }

    public function upload()
    {
        return view('admin.pagu.upload');
    }

    public function proses_upload(Request $request)
    {
        $ta = $request->cookie('ta');
        // $ta = 2020;

        $this->validate($request, [
            'file' => 'required|max:1024|mimes:xlsx'
        ]);
        
        $filename = "pagu_".$ta;

        $path = Storage::putFileAs(
            'uploads', 
            $request->file('file'), 
            $filename.'.'.$request->file('file')->getClientOriginalExtension()
        );

        $spreadsheet = IOFactory::load('storage/'.$path);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10

        $pagus = array();
        $i=0;

        for ($row = 2; $row <= $highestRow; ++$row) {
            $npsn = $worksheet->getCellByColumnAndRow(2, $row)->getCalculatedValue();
            
            if (empty($npsn)) {
                continue;
            }

            else{
                $check= Pagu::where([
                    'ta' =>$ta,
                    'npsn' => $npsn
                ])->exists();

                if ($check) {
                    break;
                }
                
                $pagu = $worksheet->getCellByColumnAndRow(4, $row)->getCalculatedValue();
                $pagus[$i]['ta'] = $ta;
                $pagus[$i]['npsn'] = $npsn;
                $pagus[$i]['pagu'] = $pagu;
                $pagus[$i]['sisa'] = $pagu;
                // $pagus[$i]['created_at'] = Carbon::now();

            }
            $i++;
        
        }

        if ($check) {
            // return "Pagu TA: ".$ta." untuk ".$npsn." sudah ada!";
            return back()->withErrors(['msg' => "Pagu untuk ".$npsn." sudah ada!"]);
        }

        // return $pagus;
        DB::beginTransaction();

        try {
            $pagus=array_map(function ($pagus) { 
                return array_merge($pagus,['created_at'=> 
                    Carbon::now()->toDateTimeString(),'updated_at'=> Carbon::now()->toDateTimeString()]
                ); 
            }, $pagus); 
            // return $pagus;
            $result= Pagu::insert($pagus);

        } catch (Exception $e) {
            return back()->withErrors(['msg' => 'Error! :'.$e->getMessage()]);
        }

        DB::commit();
        return redirect()->route('admin.pagu.index');

        // DB::rollback();
        // return json_encode($result);
    }

    public function update_upload()
    {
        return view('admin.pagu.updateupload');
    }

    public function download_pagulama(){
        $ta = Cookie::get('ta');

        $pagus= Pagu::with('sekolah')->where([
            ['ta', '=', $ta]
        ])->get();

        // return $pagus;
        $arraypagu= array();
        foreach ($pagus as $key => $item) {
            $arraypagu[$key]['npsn'] = $item->sekolah->npsn;
            $arraypagu[$key]['sekolah'] = $item->sekolah->name;
            $arraypagu[$key]['pagu_lama'] = $item->pagu;
            $arraypagu[$key]['pagu_baru'] = null;
            $arraypagu[$key]['tw1'] = $item->penggunaan_tw1;
            $arraypagu[$key]['tw2'] = $item->penggunaan_tw2;
            $arraypagu[$key]['tw3'] = $item->penggunaan_tw3;
            $arraypagu[$key]['tw4'] = $item->penggunaan_tw4;
        }

        // return $arraypagu;
        // Excel
        $spreadsheet = IOFactory::load('storage/format/formatpagu_lama.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(
            $arraypagu,
            NULL,
            'B2'
        );

        $spreadsheet->getActiveSheet()->setAutoFilter('A1:L620');
        
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
        $columnFilter = $autoFilter->getColumn('L');
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
        $file= 'Pagu_Lama_TA_'.$ta.'.xlsx';
        $documento = file_get_contents($temp_file);
        unlink($temp_file);  // delete file tmp
        header("Content-Disposition: attachment; filename= ".$file."");
        header('Content-Type: application/excel');
        return $documento;
    }

    // public function proses_update_upload(Request $request)
    // {
    //     $ta = $request->cookie('ta');
    //     // $ta = 2020;

    //     $this->validate($request, [
    //         'file' => 'required|max:1024|mimes:xlsx'
    //     ]);
        
    //     $filename = "pagu_perubahan_".$ta;

    //     $path = Storage::putFileAs(
    //         'uploads', 
    //         $request->file('file'), 
    //         $filename.'.'.$request->file('file')->getClientOriginalExtension()
    //     );

    //     $spreadsheet = IOFactory::load('storage/'.$path);
    //     $worksheet = $spreadsheet->getActiveSheet();
    //     $highestRow = $worksheet->getHighestRow(); // e.g. 10

    //     $pagus = array();
    //     $i=0;

    //     for ($row = 2; $row <= $highestRow; ++$row) {
    //         $npsn = $worksheet->getCellByColumnAndRow(2, $row)->getCalculatedValue();
            
    //         if (empty($npsn)) {
    //             continue;
    //         }

    //         else{
    //             $pagu_baru = $worksheet->getCellByColumnAndRow(5, $row)->getCalculatedValue();
    //             $sisa_baru = $worksheet->getCellByColumnAndRow(11, $row)->getCalculatedValue();
                
    //             if ($sisa_baru < 0) {
    //                 break;
    //             }
    //         }
            
    //         $i++;
    //     }
    // }

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
        $pagu= Pagu::find($id);
        if ($pagu) {
            # code...
            return view('admin.pagu.tambah', 
                [
                    'aksi' => "edit",
                    'pagu' => $pagu
                ]
            );
        }
        else{
            return redirect()->back()->withErrors(['msg' => 'Pagu dengan ID: '.$id.' tidak ditemukan!']);
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
        $pagu= Pagu::find($id);
        $pagu->pagu= floatval(str_replace(",",".",$request->nominal));
        $pagu->sisa= $pagu->pagu;
        if ($pagu->save()) {
            return redirect()->route('admin.pagu.index');
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
        $pagu= Pagu::find($id)->delete();
     
        return Response::json($pagu);

    }
}
