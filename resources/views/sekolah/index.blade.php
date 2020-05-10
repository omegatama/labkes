@extends('layouts.sekolah')

@section('titleBar', 'Halaman Utama')

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            
            <div class="row">
                
                @if (empty(Auth::user()->periode_awal) || empty(Auth::user()->periode_akhir))
                    <div class="col-lg-4">
                        <div class="card gradient-green-tea">
                            <div class="card-content">
                                <div class="px-2 py-2">
                                    <div class="media">
                                        <div class="media-body white text-center">
                                            <span>NPSN</span>
                                            <h3 class="text-bold-600 font-large-3">{{ Auth::user()->npsn }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card gradient-ibiza-sunset">
                            <div class="card-content">
                                <div class="px-2 py-2">
                                    <div class="media">
                                        <div class="media-body white text-center">
                                            <span>Tahun Anggaran</span>
                                            <h3 class="text-bold-600 font-large-3">{{ Cookie::get('ta') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-lg-8">
                        <div class="card gradient-green-tea">
                            <div class="card-content">
                                <div class="px-2 py-2">
                                    <div class="media">
                                        <div class="media-body white text-center">
                                            <span>Periode Akhif</span>
                                            <h3 class="text-bold-600 font-large-1 py-2">{{ Carbon\Carbon::parse(Auth::user()->periode_awal)->format('d-m-Y')." s/d ".Carbon\Carbon::parse(Auth::user()->periode_akhir)->format('d-m-Y') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-4">
                    <div class="card gradient-purple-bliss">
                        <div class="card-content">
                            <div class="px-2 py-3">
                                <div class="media">
                                    <div class="media-body white text-left">
                                        <span>Kelengkapan Data Sekolah</span>
                                        <h3>{{ ($statusprofil)? "Profil Lengkap" : "Profil Belum Lengkap" }}</h3>
                                        
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-user white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card bg-success">
                        <div class="card-content">
                            <div class="px-3 py-3">
                                <div class="media">
                                    <div class="media-body white text-left">
                                        <h3 class="text-bold-600">{{ !empty($pagu)? FormatUang($pagu->ta(Cookie::get('ta'))->sum('pagu')) : "-" }}</h3>
                                        <span>Jumlah Pagu</span>
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-percent white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-primary">
                        <div class="card-content">
                            <div class="px-3 py-3">
                                <div class="media">
                                    <div class="media-body white text-left">
                                        @php
                                            if (!empty($rka)) {
                                                $rkataberjalan = $rka->ta(Cookie::get('ta'));
                                                $jumlahrka = $rkataberjalan->sum('jumlah');

                                                $realisasi1= $rkataberjalan->sum('realisasi_tw1');
                                                $realisasi2= $rkataberjalan->sum('realisasi_tw2');
                                                $realisasi3= $rkataberjalan->sum('realisasi_tw3');
                                                $realisasi4= $rkataberjalan->sum('realisasi_tw4');
                                                $jumlahrealisasi = $realisasi1 + $realisasi2 + $realisasi3 + $realisasi4;
                                                $sisarka = $jumlahrka-$jumlahrealisasi;
                                            }
                                        @endphp
                                        <h3 class="text-bold-600">{{ !empty($jumlahrka)? FormatUang($jumlahrka) : "-" }}</h3>
                                        <span>Jumlah RKA</span>
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-clipboard white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-blue-grey">
                        <div class="card-content">
                            <div class="px-3 py-3">
                                <div class="media">
                                    <div class="media-body white text-left">
                                        
                                        <h3 class="text-bold-600">{{ !empty($jumlahrealisasi)? FormatUang($jumlahrealisasi) : "-" }}</h3>
                                        <span>Realisasi RKA</span>
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-shopping-cart white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card bg-warning">
                        <div class="card-content">
                            <div class="px-3 py-3">
                                <div class="media">
                                    <div class="media-body black text-left">
                                        <h3 class="text-bold-600">{{ !empty($pagu)? FormatUang($pagu->ta(Cookie::get('ta'))->sum('sisa')) : "-" }}</h3>
                                        <span>Sisa Pagu</span>
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-percent black font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-amber">
                        <div class="card-content">
                            <div class="px-3 py-3">
                                <div class="media">
                                    <div class="media-body black text-left">
                                        
                                        <h3 class="text-bold-600">{{ !empty($sisarka)? FormatUang($sisarka) : "-" }}</h3>
                                        <span>Sisa RKA</span>
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-clipboard black font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light-blue">
                        <div class="card-content">
                            <div class="px-3 py-3">
                                <div class="media">
                                    <div class="media-body black text-left">
                                        @php
                                            if (!empty($saldo)) {
                                                $saldotaberjalan = $saldo->ta(Cookie::get('ta'));
                                                $saldobank = $saldo->sum('saldo_bank');
                                                $saldotunai = $saldo->sum('saldo_tunai');
                                                $totalsaldo = $saldobank+$saldotunai;
                                            }
                                        @endphp
                                        <h3 class="text-bold-600">{{ !empty($totalsaldo)? FormatUang($totalsaldo) : "-" }}</h3>
                                        <span>Saldo</span>
                                    </div>
                                    <div class="media-right align-self-center">
                                        <i class="ft-credit-card black font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Dashboard
                            </h4>
                        </div>
                        <div class="card-body">
                            Selamat Datang kembali!
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
</div>
@endsection