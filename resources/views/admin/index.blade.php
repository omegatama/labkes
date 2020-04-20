@extends('layouts.admin')

@section('titleBar', 'Halaman Utama')

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card gradient-green-tea">
                        <div class="card-content">
                            <div class="px-2 py-2">
                                <div class="media">
                                    <div class="media-body white text-center">
                                        <span>Username</span>
                                        <h3 class="text-bold-600 font-large-3">{{ Auth::user()->username }}</h3>
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

                <div class="col-lg-4">
                    <div class="card gradient-man-of-steel">
                        <div class="card-content">
                            <div class="px-2 py-2">
                                <div class="media">
                                    <div class="media-body white text-center">
                                        <span>Sekolah Terdaftar</span>
                                        <h3 class="text-bold-600 font-large-3">{{$jumlahsekolah}}</h3>
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
                                        <h3 class="text-bold-600">{{ !empty($jumlahpagu)? FormatUang($jumlahpagu) : "-" }}</h3>
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
                                        <h3 class="text-bold-600">{{ !empty($sisapagu)? FormatUang($sisapagu) : "-" }}</h3>
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
                                        
                                        <h3 class="text-bold-600">{{ !empty($jumlahpencairanbos)? FormatUang($jumlahpencairanbos) : "-" }}</h3>
                                        <span>Jumlah Pencairan Dana BOS</span>
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
                            Selamat Datang kembali admin
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
