<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KodeRekening;
use App\RkaLimit;
use Auth;
use Cookie;

class RkaLimitController extends Controller
{
    public function index()
    {
    	// $ta= Cookie::get('ta');  
     //    $npsn= Auth::user()->npsn;
    	// $parentRekenings = KodeRekening::where('parent_id',NULL)->get();
    	// $limitRka = RkaLimit::where(
    	// 	[
    	// 		'ta' => $ta,
    	// 		'npsn' => $npsn
    	// 	]
    	// )->get();
    	// return view('sekolah.rka.limit',(
	    // 		[
	    // 			'parentRekenings' => $parentRekenings,
	    // 			'limitRka' => $limitRka
	    // 		]
	    // 	)
    	// );
    }
}
