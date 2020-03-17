<?php
use Carbon\Carbon;

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

if (!function_exists('FormatUang')) {
    function FormatUang($expression) {
        return number_format($expression, 0, ',', '.');
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

if (!function_exists('AwalTriwulan')) {
    function AwalTriwulan($triwulan, $tahun) {
        $bulan = ( ($triwulan-1) * 3 ) + 1;
        $tanggal = Carbon::createFromDate($tahun, $bulan, 1);
        return $tanggal;
    }
}

if (!function_exists('AkhirTriwulan')) {
    function AkhirTriwulan($triwulan, $tahun) {
        $bulan = $triwulan * 3;
        $tanggal = Carbon::createFromDate($tahun, $bulan, 1);
        $tanggal = $tanggal->endOfMonth();
        return $tanggal;
    }
}

if (!function_exists('AwalCaturwulan')) {
    function AwalCaturwulan($caturwulan, $tahun) {
        $bulan = ( ($caturwulan-1) * 4 ) + 1;
        $tanggal = Carbon::createFromDate($tahun, $bulan, 1);
        return $tanggal;
    }
}

if (!function_exists('AkhirCaturwulan')) {
    function AkhirCaturwulan($caturwulan, $tahun) {
        $bulan = $caturwulan * 4;
        $tanggal = Carbon::createFromDate($tahun, $bulan, 1);
        $tanggal = $tanggal->endOfMonth();
        return $tanggal;
    }
}

if (!function_exists('Terbilang')) {
    function Terbilang($number)
    {
        $number = str_replace('.', '', $number);
        if ( ! is_numeric($number)) throw new Exception("Please input number.");
        $base    = array('nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan');
        $numeric = array('1000000000000000', '1000000000000', '1000000000000', 1000000000, 1000000, 1000, 100, 10, 1);
        $unit    = array('kuadriliun', 'triliun', 'biliun', 'milyar', 'juta', 'ribu', 'ratus', 'puluh', '');
        $str     = null;
        $i = 0;
        if ($number == 0) {
            $str = 'nol';
        } else {
            while ($number != 0) {
                $count = (int)($number / $numeric[$i]);
                if ($count >= 10) {
                    $str .= Terbilang($count) . ' ' . $unit[$i] . ' ';
                } elseif ($count > 0 && $count < 10) {
                    $str .= $base[$count] . ' ' . $unit[$i] . ' ';
                }
                $number -= $numeric[$i] * $count;
                $i++;
            }
            $str = preg_replace('/satu puluh (\w+)/i', '\1 belas', $str);
            $str = preg_replace('/satu (ribu|ratus|puluh|belas)/', 'se\1', $str);
            $str = preg_replace('/\s{2,}/', ' ', trim($str));
        }
        return $str;
    }
}

if (!function_exists('IntBulan')) {
    function IntBulan($expression) {
        $bulan = ['','Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $bulan[$expression];
    }
}
