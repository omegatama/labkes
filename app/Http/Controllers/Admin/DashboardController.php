<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sekolah;
use App\Pagu;
use App\Rka;
use App\Pendapatan;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
    	$jumlahsekolah= Sekolah::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->count();

    	$jumlahpagu= Pagu::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('pagu');

    	$sisapagu= Pagu::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('sisa');

    	$jumlahrka= Rka::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('jumlah');

    	$realisasitw1= Rka::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('realisasi_tw1');

    	$realisasitw2= Rka::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('realisasi_tw2');

    	$realisasitw3= Rka::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('realisasi_tw3');

    	$realisasitw4= Rka::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789']
    	])->sum('realisasi_tw4');

    	$jumlahrealisasi = $realisasitw1 + $realisasitw2 + $realisasitw3 + $realisasitw4;

    	$sisarka = $jumlahrka - $jumlahrealisasi;

    	$jumlahpencairanbos= Pendapatan::where([
    		['npsn', '!=', '12345678'],
    		['npsn', '!=', '23456789'],
    		['sumber', '=', 'BOS']
    	])->sum('nominal');

    	return view('admin.index', compact('jumlahsekolah','jumlahpagu', 'sisapagu', 'jumlahrka', 'jumlahrealisasi', 'sisarka', 'jumlahpencairanbos'));
    }
}
