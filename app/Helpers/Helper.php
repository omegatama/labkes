<?php

if (!function_exists('RenderTombol')) {

    /**
     * description
     *
     * @param
     * $warna, $link, $teks
     * @return
     */
    function RenderTombol($warna, $link, $teks, $onclick = null) {
    	if (empty($onclick)) {
    		# code...
    		return "<a href=\"$link\" class=\"btn btn-$warna btn-sm m-0\">$teks</a>";
    	}
    	else{
    		return "<a href=\"$link\" class=\"btn btn-$warna btn-sm m-0\" onclick=\"$onclick\">$teks</a>";
    	}
    }
}

if (!function_exists('FormatMataUang')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function FormatMataUang($expression) {
        return "Rp. ".number_format($expression, 2, ',', '.');
    }
}

if (!function_exists('GetTriwulan')) {
    function GetTriwulan($stringtanggal){
        # code...
        $bln = date("m",strtotime($stringtanggal));
        $triwulan = $bln >= 1 && $bln <= 3 ? 1 : ($bln >= 4 && $bln <= 6 ? 2 : ($bln >= 7 && $bln <= 9 ? 3 : 4));
        return $triwulan;
    }
}
