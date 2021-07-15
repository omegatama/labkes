@extends('layouts.admin')

@section('titleBar', ucfirst($aksi)." Data Tenaga Medis")

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2/select2-bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
@endsection

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row justify-content-md-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)." Data Tenaga Medis" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            
                                <form class="form" method="POST" action="{{ (isset($tenagamedis))? route('admin.tenagamedis.update', ['id'=>$tenagamedis->id]) : route('admin.tenagamedis.store') }}">
                                    @csrf
                                    
                                    @if (isset($tenagamedis))
                                        @method('PUT')
                                    @endif

                                    <div class="form-body">
                                        
                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">NIP/NIK</label>
	                                        <div class="col-lg-4">
	                                            <input class="form-control" type="text" name="nip" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->nip : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Nama</label>
	                                        <div class="col-lg-9">
	                                            <input class="form-control" type="text" name="nama" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->nama : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Alamat</label>
	                                        <div class="col-lg-9">
	                                            <input class="form-control" type="text" name="alamat" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->alamat : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Email</label>
	                                        <div class="col-lg-6">
	                                            <input class="form-control" type="text" name="email" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->email : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Telpon</label>
	                                        <div class="col-lg-5">
	                                            <input class="form-control" type="text" name="telpon" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->telpon : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Gender</label>
	                                        <div class="col-lg-4">
	                                            <input class="form-control" type="text" name="jenis_kelamin" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->jenis_kelamin : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Jabatan</label>
	                                        <div class="col-lg-9">
	                                            <input class="form-control" type="text" name="jabatan" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->jabatan : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Marital</label>
	                                        <div class="col-lg-4">
	                                            <input class="form-control" type="text" name="marital" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->marital : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Status</label>
	                                        <div class="col-lg-3">
	                                            <input class="form-control" type="text" name="status" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->status : '' )}}">
	                                        </div>
	                                    </div>
                                    
                                    </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.tenagamedis.index') }}">
                                            <button type="button" class="btn btn-raised btn-warning mb-0 mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </button>  
                                        </a>
                                        <button type="submit" class="btn btn-raised btn-primary mb-0">
                                            <i class="fa fa-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('extraJs')
<script src="{{ asset('app-assets/vendors/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/inputmask/jquery.inputmask.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        

        

        

    });
</script>
@if($errors->any())
<script>
    toastr.error("{{ $errors->first() }}", "Error!", {
            closeButton: 1,
            timeOut: 0
        });
</script>
@endif



@endsection
