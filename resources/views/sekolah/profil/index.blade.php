@extends('layouts.sekolah')

@section('titleBar', 'Profil')

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Profil
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <ul class="no-list-style">
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-briefcase font-small-3"></i> Nama Sekolah:</a></span>
                                            <span class="d-block overflow-hidden">{{$user->name}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-hash font-small-3"></i> Kode NPSN:</a></span>
                                            <span class="d-block overflow-hidden">{{$user->npsn}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-at-sign font-small-3"></i> Email:</a></span>
                                            <span class="d-block overflow-hidden">{{$user->email}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-phone font-small-3"></i> Telepon:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->telepon))? $user->telepon: '-'}}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <ul class="no-list-style">
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-book font-small-3"></i> Jenjang:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->jenjang))? $user->jenjang: '-'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-shield font-small-3"></i> Status:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->status))? $user->status: '-'}}</span>
                                        </li>

                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-map font-small-3"></i> Kecamatan:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->kecamatan))? $user->kecamatan->nama_kecamatan: '-'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-map-pin font-small-3"></i> Desa:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->desa))? $user->desa: '-'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-home font-small-3"></i> Alamat:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->alamat))? $user->alamat: '-'}}</span>
                                        </li>
                                        
                                    </ul>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <ul class="no-list-style">
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-user font-small-3"></i> Nama Kepala Sekolah:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->nama_kepsek))? $user->nama_kepsek: '-'}}</span>

                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-hash font-small-3"></i> NIP Kepala Sekolah:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->nip_kepsek))? $user->nip_kepsek: '-'}}</span>
                                        </li>

                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-user font-small-3"></i> Nama Bendahara:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->nama_bendahara))? $user->nama_bendahara: '-'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary"><a><i class="ft-hash font-small-3"></i> NIP Bendahara:</a></span>
                                            <span class="d-block overflow-hidden">{{(!empty($user->nip_bendahara))? $user->nip_bendahara: '-'}}</span>
                                        </li>

                                    </ul>
                                </div>
                            </div>   

                            <div class="pb-0 pt-2" style="border-top: 1px solid #D3DCE9;">
                                <a href="{{ route('sekolah.profil.edit') }}">
                                    <button type="button" class="btn btn-raised btn-success mb-0 mr-1">
                                        <i class="ft-edit-3"></i> Edit
                                    </button>  
                                </a>

                                <a href="{{ route('sekolah.password.edit') }}">
                                    <button type="button" class="btn btn-raised btn-warning mb-0 mr-1">
                                        <i class="ft-lock"></i> Ubah Password
                                    </button>  
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection