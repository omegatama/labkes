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
