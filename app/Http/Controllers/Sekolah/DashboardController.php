<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
    	$sekolah = Auth::user();
    	$statusprofil = 0;
    	if (
    		!empty($sekolah->desa) &&
    		!empty($sekolah->alamat) && 
    		!empty($sekolah->telepon) &&
    		!empty($sekolah->nama_kepsek) &&
    		!empty($sekolah->nama_bendahara)
    	) {
    		$statusprofil = 1;
    	}

    	$pagu= $sekolah->pagus();
    	$rka= $sekolah->rkas();
    	$saldo= $sekolah->saldos();

    	return view('sekolah.index', compact('sekolah', 'statusprofil', 'pagu', 'rka', 'saldo'));
    }
}
