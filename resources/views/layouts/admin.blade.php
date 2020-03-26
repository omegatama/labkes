<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- BEGIN : Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Aplikasi Pencatatan Informasi Keuangan Bantuan Operasional Sekolah Kabupaten Semarang">
    <meta name="keywords" content="aplikasi keuangan, aplikasi pencatatan informasi keuangan, bantuan operasional sekolah, kabupaten semarang, aplikasi pencatatan informasi keuangan bos kabupaten semarang, disdikbudpora kabupaten semarang">
    <meta name="author" content="MTROHMAN">
    <title> @yield('titleBar') - APIK BOS Kabupaten Semarang</title>
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('app-assets/img/ico/apple-icon-60.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('app-assets/img/ico/apple-icon-76.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('app-assets/img/ico/apple-icon-120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('app-assets/img/ico/apple-icon-152.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/img/ico/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('app-assets/img/ico/favicon-32.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/feather/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/perfect-scrollbar.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/prism.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/sweetalert2.min.css') }}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN APEX CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/app.min.css') }}">
    <!-- END APEX CSS-->
    <!-- BEGIN Page Level CSS-->
    @yield('extraCss')
    <!-- END Page Level CSS-->
</head>
<!-- END : Head-->

<!-- BEGIN : Body-->
<body data-col="2-columns" class=" 2-columns {{--layout-dark--}}">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="wrapper sidebar-lg">
        <div data-active-color="white" data-background-color="black" data-image="{{ asset('app-assets/img/side-01.png') }}" class="app-sidebar">
            <div class="sidebar-header">
                <div class="logo clearfix">
                    <a href="{{ route('admin') }}" class="logo-text float-left">
                        <div class="logo-img">
                            <img src="{{ asset('app-assets/img/logo-kabsemarang.webp') }}" style="width: 100%" />
                        </div>
                        <span class="text align-middle" style="font-size: 100%">SIM - BOS</span>
                    </a>
                    <a
                    id="sidebarToggle"
                    href="javascript:;"
                    class="nav-toggle d-none d-sm-none d-md-none d-lg-block">
                        <i
                        data-toggle="expanded"
                        class="ft-toggle-right toggle-icon"
                        ></i>
                    </a>
                    <a
                    id="sidebarClose"
                    href="javascript:;"
                    class="nav-close d-block d-md-block d-lg-none d-xl-none">
                        <i class="ft-x"></i>
                    </a>
                </div>
            </div>
            <div class="sidebar-content">
                <div class="nav-container">
                    <ul
                    id="main-menu-navigation"
                    data-menu="menu-navigation"
                    data-scroll-to-active="true"
                    class="navigation navigation-main">
                        <li class="nav-item">
                            <a href="{{ route('admin') }}">
                                <i class="ft-home"></i>
                                <span data-i18n="" class="menu-title">
                                    Halaman Utama
                                </span>
                            </a>
                        </li>
                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-globe"></i>
                                <span data-i18n="" class="menu-title">
                                    Master Data
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li>
                                    <a href="{{ route('admin.kodeprogram.index') }}" class="menu-item">Kode Program</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.komponenpembiayaan.index') }}" class="menu-item">Kode Komponen</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.koderekening.index') }}" class="menu-item">Kode Rekening</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.kodebarang.index') }}" class="menu-item">Kode Barang</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-percent"></i>
                                <span data-i18n="" class="menu-title">
                                    Pagu
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li>
                                    <a href="{{ route('admin.pagu.index') }}" class="menu-item">Pagu Awal</a>
                                </li>
                                {{-- <li>
                                    <a href="#" class="menu-item">Pagu Perubahan</a>
                                </li> --}}
                            </ul>
                        </li>
                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-clipboard"></i>
                                <span data-i18n="" class="menu-title">
                                    RKA
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li>
                                    <a href="{{ route('admin.rka.index') }}" class="menu-item">RKA Awal</a>
                                </li>
                                {{-- <li>
                                    <a href="#" class="menu-item">RKA Perubahan</a>
                                </li> --}}
                            </ul>
                        </li>
                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-credit-card"></i>
                                <span data-i18n="" class="menu-title">
                                    Saldo
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li>
                                    <a href="{{ route('admin.penerimaan.index') }}" class="menu-item">Penerimaan Dana</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.kas.saldolalu') }}" class="menu-item">Saldo Th Lalu</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.kas.saldo') }}" class="menu-item">Saldo Kas</a>
                                </li>
                                
                            </ul>
                        </li>
                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-shopping-cart"></i>
                                <span data-i18n="" class="menu-title">
                                    Belanja
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li class="has-sub">
                                    <a href="javascript:void(0)" class="menu-item">Belanja Th Berjalan</a>
                                    <ul class="menu-content">
                                        <li>
                                            <a href="{{ route('admin.belanja.index') }}" class="menu-item">Data Belanja</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.belanjapersediaan.index') }}" class="menu-item">Data Persediaan</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.belanjamodal.index') }}" class="menu-item">Data Belanja Modal</a>
                                        </li>
                                    </ul>
                                </li>
                                
                                {{-- <li class="has-sub">
                                    <a href="javascript:void(0)" class="menu-item">Belanja Perubahan</a>
                                    <ul class="menu-content">
                                        <li>
                                            <a href="belanja" class="menu-item">Data Belanja</a>
                                        </li>
                                        <li>
                                            <a href="belanjapersediaan" class="menu-item">Data Persediaan</a>
                                        </li>
                                        <li>
                                            <a href="belanjamodal" class="menu-item">Data Belanja Modal</a>
                                        </li>
                                    </ul>
                                </li> --}}

                                <li class="has-sub">
                                    <a href="javascript:void(0)" class="menu-item">Belanja Th Lalu</a>
                                    <ul class="menu-content">
                                        <li>
                                            <a href="belanja" class="menu-item">Data Belanja</a>
                                        </li>
                                        <li>
                                            <a href="belanjapersediaan" class="menu-item">Data Persediaan</a>
                                        </li>
                                        <li>
                                            <a href="belanjamodal" class="menu-item">Data Belanja Modal</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-package"></i>
                                <span data-i18n="" class="menu-title">
                                    Persediaan
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li>
                                    <a href="{{ route('admin.persediaan.stok') }}" class="menu-item">Stok Persediaan</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.persediaan.penggunaan') }}" class="menu-item">Penggunaan</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.persediaan.penyesuaian') }}" class="menu-item">Penyesuaian</a>
                                </li>
                            </ul>
                        </li>

                        <li class="has-sub nav-item">
                            <a href="javascript:void(0)">
                                <i class="ft-file-text"></i>
                                <span data-i18n="" class="menu-title">
                                    Laporan
                                </span>
                            </a>
                            <ul class="menu-content">
                                <li class="has-sub">
                                    <a href="javascript:void(0)" class="menu-item">Th Berjalan</a>
                                    <ul class="menu-content">
                                        <li>
                                            <a href="{{ route('admin.laporan.rkaall') }}" class="menu-item">RKA Seluruh Sekolah</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.laporan.rka') }}" class="menu-item">RKA per Sekolah</a>
                                        </li>
                                        {{-- <li>
                                            <a href="#" class="menu-item">Lap Realisasi</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">SPTJ</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">SPTMH</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">K7 Provinsi</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">K7 Kabupaten</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">Belanja Modal</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">Belanja Persediaan</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">BKU</a>
                                        </li> --}}
                                        
                                    </ul>
                                </li>

                                {{-- <li class="has-sub">
                                    <a href="javascript:void(0)" class="menu-item">Perubahan</a>
                                    <ul class="menu-content">
                                        <li>
                                            <a href="Lap Realisasi" class="menu-item">Lap Realisasi</a>
                                        </li>
                                        <li>
                                            <a href="SPTJ" class="menu-item">SPTJ</a>
                                        </li>
                                        <li>
                                            <a href="SPTMH" class="menu-item">SPTMH</a>
                                        </li>
                                        <li>
                                            <a href="K7 Provinsi" class="menu-item">K7 Provinsi</a>
                                        </li>
                                        <li>
                                            <a href="K7 Kabupaten" class="menu-item">K7 Kabupaten</a>
                                        </li>
                                        <li>
                                            <a href="Belanja Modal" class="menu-item">Belanja Modal</a>
                                        </li>
                                        <li>
                                            <a href="Belanja Persediaan" class="menu-item">Belanja Persediaan</a>
                                        </li>
                                        <li>
                                            <a href="BKU" class="menu-item">BKU</a>
                                        </li>
                                        
                                    </ul>
                                </li> --}}

                                <li class="has-sub">
                                    <a href="javascript:void(0)" class="menu-item">Th Lalu</a>
                                    <ul class="menu-content">
                                        
                                        <li>
                                            <a href="#" class="menu-item">K7 Provinsi</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">K7 Kabupaten</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">Belanja Modal</a>
                                        </li>
                                        <li>
                                            <a href="#" class="menu-item">Belanja Persediaan</a>
                                        </li>
                                        
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sekolah.index') }}">
                                <i class="ft-users"></i>
                                <span data-i18n="" class="menu-title">
                                    Sekolah
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                                <i class="ft-power"></i>
                                <span data-i18n="" class="menu-title">
                                    Keluar
                                </span>
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </div>
            <div class="sidebar-background"></div>
        </div>

        <!-- Navbar (Header) Starts-->
        <nav class="navbar navbar-expand-lg navbar-light bg-faded header-navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button
                    type="button"
                    data-toggle="collapse"
                    class="navbar-toggle d-lg-none float-left">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <span class="d-lg-none navbar-right navbar-collapse-toggle">
                        <a
                        aria-controls="navbarSupportedContent"
                        href="javascript:;"
                        class="open-navbar-container black">
                            <i class="ft-more-vertical"></i>
                        </a>
                    </span>
                    <h4 class="text-bold-600 navbar-left text align-middle mt-2 pt-1">@yield('namaUser', Auth::user()->name)</h4>
                </div>
                <div class="navbar-container">
                    <div id="navbarSupportedContent" class="collapse navbar-collapse">
                        <ul class="navbar-nav">
                            <li class="nav-item mr-2">
                                <p class="font-medium-3 mt-1">TA: {{ Cookie::get('ta') }}</p>
                            </li>
                            <li class="nav-item mr-2 d-none d-lg-block">
                                <a
                                id="navbar-fullscreen"
                                href="javascript:;"
                                class="nav-link apptogglefullscreen">
                                    <i class="ft-maximize font-medium-3 blue-grey darken-4"></i>
                                    <p class="d-none">fullscreen</p>
                                </a>
                            </li>
                            <li class="dropdown nav-item">
                                <a
                                id="dropdownBasic3"
                                href="#"
                                data-toggle="dropdown"
                                class="nav-link position-relative dropdown-toggle">
                                    <i class="ft-user font-medium-3 blue-grey darken-4"></i>
                                    <p class="d-none">User Settings</p>
                                </a>
                                <div
                                ngbdropdownmenu=""
                                aria-labelledby="dropdownBasic3"
                                class="dropdown-menu text-left dropdown-menu-right">
                                    {{-- <a href="javascript:;" class="dropdown-item py-1">
                                        <i class="ft-settings mr-2"></i>
                                        <span>Pengaturan</span>
                                    </a> --}}
                                    <a href="{{ route('admin.profil.index') }}" class="dropdown-item py-1">
                                        <i class="ft-edit mr-2"></i>
                                        <span>Profil</span>
                                    </a>
                                    {{-- <a href="javascript:;" class="dropdown-item py-1"
                                      >
                                        <i class="ft-mail mr-2"></i>
                                        <span>Informasi</span>
                                    </a> --}}
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item py-1" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        <i class="ft-log-out mr-2"></i>
                                        <span>{{ __('Logout') }}</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                        <input type="text" name="guard" value="{{ Auth::getDefaultDriver() }}">
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="main-panel">
            <!-- BEGIN : Main Content-->
            @yield('content')
            <!-- END : End Main Content-->

            <!-- BEGIN : Footer-->
            <footer class="footer footer-static footer-light navbar-border">
                <p class="clearfix text-muted text-sm-center px-2">
                    <span>Aplikasi Pencatatan Informasi Keuangan Bantuan Operasional Sekolah Kabupaten Semarang<br>Copyright &copy; <?=date('Y');?>
                        <a
                        href="https://mtrohman.github.io/cv"
                        id="mtrohmancv"
                        target="_blank"
                        class="text-bold-800 primary darken-2">
                            OMEGATAMA
                        </a>, All rights reserved.
                    </span>
                </p>
            </footer>
            <!-- End : Footer-->
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    @yield('lowPage')

    <!-- BEGIN VENDOR JS-->
    <script src="{{ asset('app-assets/vendors/js/core/jquery-3.2.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/core/popper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/core/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/perfect-scrollbar.jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/prism.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/jquery.matchHeight-min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/screenfull.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pace/pace.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/sweetalert2.min.js') }}" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script>
        var url = window.location;
        // Will only work if string in href matches with location
        $('ul.navigation a[href="' + url + '"]').parent().addClass('active');

        // Will also work for relative and absolute hrefs
        $('ul.navigation a').filter(function() {
            // return url == this.href;
            var string = url.toString(),
                substring = this.href.toString();

            return string.includes(substring)
        }).addClass('active');
    </script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN APEX JS-->
    <script src="{{ asset('app-assets/js/app-sidebar.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/notification-sidebar.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/customizer.js') }}" type="text/javascript"></script>
    <!-- END APEX JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    @yield('extraJs')
    <!-- END PAGE LEVEL JS-->
</body>
<!-- END : Body-->
</html>