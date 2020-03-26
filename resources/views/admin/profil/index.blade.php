@extends('layouts.admin')

@section('titleBar', 'Profil')

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row justify-content-md-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Profil
                            </h4>
                        </div>
                        <div class="card-body">
                            
                            <ul class="no-list-style">
                                <li class="mb-2">
                                    <span class="text-bold-500 primary"><a><i class="ft-briefcase font-small-3"></i> Nama:</a></span>
                                    <span class="d-block overflow-hidden">{{$user->name}}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-bold-500 primary"><a><i class="ft-hash font-small-3"></i> Username:</a></span>
                                    <span class="d-block overflow-hidden">{{$user->username}}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-bold-500 primary"><a><i class="ft-at-sign font-small-3"></i> Email:</a></span>
                                    <span class="d-block overflow-hidden">{{$user->email}}</span>
                                </li>
                                {{-- <li class="mb-2">
                                    <span class="text-bold-500 primary"><a><i class="ft-phone font-small-3"></i> Telepon:</a></span>
                                    <span class="d-block overflow-hidden">{{(!empty($user->telepon))? $user->telepon: '-'}}</span>
                                </li> --}}
                            </ul>  

                            <div class="pb-0 pt-2 text-center" style="border-top: 1px solid #D3DCE9;">
                                <a href="{{ route('admin.profil.edit') }}">
                                    <button type="button" class="btn btn-raised btn-success mb-0 mr-1">
                                        <i class="ft-edit-3"></i> Edit
                                    </button>  
                                </a>

                                <a href="{{ route('admin.password.edit') }}">
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