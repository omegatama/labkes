<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Rka;
use App\RkaLimit;
use App\Pagu;
use App\KodeProgram;
use Auth;
use Cookie;
use Response;
use DataTables;

class RkaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $query = Auth::user()->rkas()->with(['sekolah','program','kegiatan','kp','rekening'])->select('rkas.*');
            $ta = Cookie::get('ta');
            return DataTables::eloquent($query)
            ->filter(function ($query) use ($ta) {
                $query->where(
                    [
                        'ta' => $ta,
                        // 'npsn' => $npsn
                    ]
                );
            },true)
            ->addColumn('action', function(Rka $rka) {
                $urledit= route('sekolah.rka.edit', ['id' => $rka->id]);
                $urlhapus= route('sekolah.rka.destroy', ['id' => $rka->id]);
                
                if (!(Auth::user()->kunci_rka)) {
                    # code...
                    return RenderTombol("success", $urledit, "Edit")." ".RenderTombol("danger confirmation", $urlhapus, "Hapus");
                }
                else{
                    return "-";
                }
            })
            ->editColumn('harga_satuan', '{{FormatMataUang($harga_satuan)}}')
            ->editColumn('jumlah', '{{FormatMataUang($jumlah)}}')
            
            ->editColumn('alokasi_tw1', '{{FormatMataUang($alokasi_tw1)}}')
            ->editColumn('alokasi_tw2', '{{FormatMataUang($alokasi_tw2)}}')
            ->editColumn('alokasi_tw3', '{{FormatMataUang($alokasi_tw3)}}')
            ->editColumn('alokasi_tw4', '{{FormatMataUang($alokasi_tw4)}}')
            ->editColumn('rekening.nama_rekening', function(Rka $rka) {
                return 
                    $rka->rekening->parent->kode_rekening.".".
                    $rka->rekening->kode_rekening." - ".
                    $rka->rekening->nama_rekening;
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('sekolah.rka.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sekolah.rka.tambah', ['aksi' => "tambah"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ta= $request->cookie('ta');
        $npsn = Auth::user()->npsn;
        $pagu = Auth::user()->pagus->where('ta',$ta)->first();

        $harga_satuan = floatval(str_replace(",",".",$request->harga_satuan));
        $jumlah = floatval(str_replace(",",".",$request->jumlah));
        $alokasi_tw1 = floatval(str_replace(",",".",$request->alokasi_tw1));
        $alokasi_tw2 = floatval(str_replace(",",".",$request->alokasi_tw2));
        $alokasi_tw3 = floatval(str_replace(",",".",$request->alokasi_tw3));
        $alokasi_tw4 = floatval(str_replace(",",".",$request->alokasi_tw4));
        
        if ($jumlah == ($alokasi_tw1 + $alokasi_tw2 + $alokasi_tw3 + $alokasi_tw4)) {
            // return json_encode($pagus);
            /*if (
                ($alokasi_tw1 + $pagu->penggunaan_tw1) <= (0.2 * $pagu->pagu) &&
                ($alokasi_tw2 + $pagu->penggunaan_tw2) <= (0.4 * $pagu->pagu) &&
                ($alokasi_tw3 + $pagu->penggunaan_tw3) <= (0.2 * $pagu->pagu) &&
                ($alokasi_tw4 + $pagu->penggunaan_tw4) <= (0.2 * $pagu->pagu)    
            )*/ 
            
            // if( ($jumlah + $pagu->penggunaan_tw1 + $pagu->penggunaan_tw2 + $pagu->penggunaan_tw3 + $pagu->penggunaan_tw4) <= $pagu->pagu )
            // tes
            if($jumlah <= $pagu->sisa)
            {
                # code...
                DB::beginTransaction();
                try {
                    // Step 1: Create RKA
                    $rka= new Rka;
                    $rka->ta= $ta;
                    $rka->npsn= $npsn;
                    $rka->kode_program_id= $request->kode_program_id;
                    $rka->kegiatan_id= $request->kegiatan_id;
                    $rka->komponen_pembiayaan_id= $request->komponen_pembiayaan_id;
                    $rka->kode_rekening_id= $request->kode_rekening_id;
                    $rka->uraian= $request->uraian;
                    $rka->volume= $request->volume;
                    $rka->satuan= $request->satuan;
                    $rka->harga_satuan= $harga_satuan;
                    $rka->jumlah= $jumlah;
                    $rka->alokasi_tw1= $alokasi_tw1;
                    $rka->alokasi_tw2= $alokasi_tw2;
                    $rka->alokasi_tw3= $alokasi_tw3;
                    $rka->alokasi_tw4= $alokasi_tw4;
                    $rka->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('sekolah.rka.create')
                    ->withErrors(['msg' => 'Oops, ada yang salah! (R1)']);
                }

                try {
                    // Step 2: Update Penggunaan Pagu
                    $pagu->penggunaan_tw1 += $rka->alokasi_tw1;
                    $pagu->penggunaan_tw2 += $rka->alokasi_tw2;
                    $pagu->penggunaan_tw3 += $rka->alokasi_tw3;
                    $pagu->penggunaan_tw4 += $rka->alokasi_tw4;
                    $pagu->sisa -= $jumlah;
                    $pagu->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('sekolah.rka.create')
                    ->withErrors(['msg' => 'Oops, ada yang salah! (R2)']);
                }

                DB::commit(); // All transaction will commit if statement reach on this
                return redirect()->route('sekolah.rka.index')->with('success','Data RKA berhasil disimpan!');

            }
            else{
                return redirect()->back()->withErrors(['msg' => 'Jumlah melebihi Pagu']);
            }
        }
        else{
            return redirect()->back()->withErrors(['msg' => 'Jumlah dan Alokasi Triwulan belum sesuai']);
        }

    }

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
        try {
            $rka= Auth::user()->rkas()->findOrFail($id);  
            return view('sekolah.rka.tambah', 
                [
                    'aksi' => "edit",
                    'rka' => $rka,
                ]
            );
        } catch (\Exception $e) {
            return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak ditemukan']);
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
        $ta= $request->cookie('ta');
        $pagu = Auth::user()->pagus->where('ta',$ta)->first();
        $rka= Auth::user()->rkas()->find($id);
        
        if (!empty($rka)) {
            $npsn= $rka->npsn;
            $harga_satuan_lama  = $rka->harga_satuan;
            $jumlah_lama        = $rka->jumlah;
            $alokasi_tw1_lama   = $rka->alokasi_tw1;
            $alokasi_tw2_lama   = $rka->alokasi_tw2;
            $alokasi_tw3_lama   = $rka->alokasi_tw3;
            $alokasi_tw4_lama   = $rka->alokasi_tw4;

            $harga_satuan_baru = floatval(str_replace(",",".",$request->harga_satuan));
            $jumlah_baru = floatval(str_replace(",",".",$request->jumlah));
            $alokasi_tw1_baru = floatval(str_replace(",",".",$request->alokasi_tw1));
            $alokasi_tw2_baru = floatval(str_replace(",",".",$request->alokasi_tw2));
            $alokasi_tw3_baru = floatval(str_replace(",",".",$request->alokasi_tw3));
            $alokasi_tw4_baru = floatval(str_replace(",",".",$request->alokasi_tw4));

            $selisih_harga_satuan = $harga_satuan_baru - $harga_satuan_lama;
            $selisih_jumlah = $jumlah_baru - $jumlah_lama; 
            $selisih_alokasi_tw1 = $alokasi_tw1_baru - $alokasi_tw1_lama;
            $selisih_alokasi_tw2 = $alokasi_tw2_baru - $alokasi_tw2_lama;
            $selisih_alokasi_tw3 = $alokasi_tw3_baru - $alokasi_tw3_lama;
            $selisih_alokasi_tw4 = $alokasi_tw4_baru - $alokasi_tw4_lama;

            /*if (
                ($selisih_alokasi_tw1 + $pagu->penggunaan_tw1) <= (0.2 * $pagu->pagu) &&
                ($selisih_alokasi_tw2 + $pagu->penggunaan_tw2) <= (0.4 * $pagu->pagu) &&
                ($selisih_alokasi_tw3 + $pagu->penggunaan_tw3) <= (0.2 * $pagu->pagu) &&
                ($selisih_alokasi_tw4 + $pagu->penggunaan_tw4) <= (0.2 * $pagu->pagu)    
            )*/
            if($selisih_jumlah <= $pagu->sisa)
            {
                DB::beginTransaction();
                try {
                    // Step 1: Update RKA
                    $rka->kode_program_id= $request->kode_program_id;
                    $rka->kegiatan_id= $request->kegiatan_id;
                    $rka->komponen_pembiayaan_id= $request->komponen_pembiayaan_id;
                    $rka->kode_rekening_id= $request->kode_rekening_id;
                    
                    $rka->uraian = $request->uraian;
                    $rka->volume = $request->volume;
                    $rka->satuan = $request->satuan;

                    $rka->harga_satuan += $selisih_harga_satuan;
                    $rka->jumlah += $selisih_jumlah;
                    $rka->alokasi_tw1 += $selisih_alokasi_tw1;
                    $rka->alokasi_tw2 += $selisih_alokasi_tw2;
                    $rka->alokasi_tw3 += $selisih_alokasi_tw3;
                    $rka->alokasi_tw4 += $selisih_alokasi_tw4;
                    $rka->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RE-1)']);
                
                }

                try {
                    // Step 1.1 Tambahan pemeriksaan Jumlah RKA
                    if($rka->jumlah != 
                        (
                            $rka->alokasi_tw1 + 
                            $rka->alokasi_tw2 + 
                            $rka->alokasi_tw3 + 
                            $rka->alokasi_tw4
                        )
                    ){
                        DB::rollback();
                        return redirect()->back()
                        ->withErrors(['msg' => 'Jumlah RKA tidak sesuai dengan alokasi Triwulan!']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RE-1.1)']);
                
                }

                try {
                    // Step 2: Update Pagu
                    $pagu->penggunaan_tw1 += $selisih_alokasi_tw1;
                    $pagu->penggunaan_tw2 += $selisih_alokasi_tw2;
                    $pagu->penggunaan_tw3 += $selisih_alokasi_tw3;
                    $pagu->penggunaan_tw4 += $selisih_alokasi_tw4;
                    $pagu->sisa -= $selisih_jumlah;
                    $pagu->save();

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RE-2)']);
                
                }


                DB::commit(); // All transaction will commit if statement reach on this
                return redirect()->route('sekolah.rka.index')->with('success','Data RKA berhasil diperbarui!');
                
            }
            else{
                return redirect()->back()->withErrors(['msg' => 'Alokasi Triwulan melebihi Pagu']);
            }
            
        }
        else{
            return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak ditemukan']);
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
        try {
            $rka = Auth::user()->rkas()->findOrFail($id);
            // return $rka->belanjas()->count();
            if ($rka->belanjas()->count() > 0) {
                return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak bisa dihapus karena sudah digunakan!']);
            }

            DB::beginTransaction();
            try {
                // Step 1: Get required info then Delete RKA
                $ta = $rka->ta;
                $npsn = $rka->npsn;
                $kode_rekening_id = $rka->kode_rekening_id;
                $pagu = Auth::user()->pagus->where('ta',$ta)->first();

                $alokasi_tw1 = $rka->alokasi_tw1;
                $alokasi_tw2 = $rka->alokasi_tw2;
                $alokasi_tw3 = $rka->alokasi_tw3;
                $alokasi_tw4 = $rka->alokasi_tw4;
                $jumlah      = $rka->jumlah;

                $rka->delete();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RD-1)']);

            }

            try {
                // Step 2: Update Alokasi dan Sisa Pagu
                $pagu->penggunaan_tw1 -= $alokasi_tw1;
                $pagu->penggunaan_tw2 -= $alokasi_tw2;
                $pagu->penggunaan_tw3 -= $alokasi_tw3;
                $pagu->penggunaan_tw4 -= $alokasi_tw4;
                $pagu->sisa += $jumlah;
                $pagu->save();

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['msg' => 'Oops, ada yang salah! (RD-2)']);

            }

            DB::commit(); // All transaction will commit if statement reach on this
            return redirect()->route('sekolah.rka.index')->with('success','Data RKA berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('sekolah.rka.index')->withErrors(['msg' => 'RKA tidak ditemukan']);
        }
    }

    public function cetak(Request $request)
    {
        $sekolah= Auth::user();
        $ta = $request->cookie('ta');
        $programs = KodeProgram::all();
        $kegiatans = Auth::user()->kegiatans()->get();
        $nama_sekolah = $sekolah->name;
        $desa_kecamatan = $sekolah->desa." / ".$sekolah->kecamatan->nama_kecamatan;
        $nama_kepsek = $sekolah->nama_kepsek;
        $nip_kepsek = $sekolah->nip_kepsek;

        $rkas = Auth::user()->rkas()->where('ta','=',$request->cookie('ta'))->get()->sortBy('parent');
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
