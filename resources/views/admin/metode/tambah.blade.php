@extends('layouts.admin')

@section('titleBar', ucfirst($aksi)." Data Metode")

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
                <div class="col-lg-9 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)."Data Metode" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            
                                <form class="form" method="POST" action="{{ (isset($metode))? route('admin.metode.update', ['id'=>$metode->id]) : route('admin.metode.store') }}">
                                    @csrf
                                    
                                    @if (isset($metode))
                                        @method('PUT')
                                    @endif

                                    <div class="form-body">
                                        
                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Kode</label>
	                                        <div class="col-lg-3">
	                                            <input class="form-control" type="text" name="kode" placeholder="ketik kode" value="{{ (isset($metode) ? $metode->kode : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Nama Metode</label>
	                                        <div class="col-lg-7">
	                                            <input class="form-control" type="text" name="nama_metode" placeholder="ketik nama metode" value="{{ (isset($metode) ? $metode->nama_metode : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Badan Sertifikasi</label>
	                                        <div class="col-lg-7">
	                                            <input class="form-control" type="text" name="nama_bs" placeholder="ketik nama badan sertifikasi" value="{{ (isset($metode) ? $metode->nama_metode : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Expire Date</label>
	                                        <div class="col-lg-7">
	                                            <input class="form-control" type="text" name="ed" placeholder="ketik tanggal expire date" value="{{ (isset($metode) ? $metode->nama_metode : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Status</label>
	                                        <div class="col-lg-3">
                                                <select name="status" class="form-control select2" value="{{ (isset($metode) ? $metode->status : '' )}}">
                                                @if(isset($tenagamedis))
                                                    <option value ={{ $metode->status }}> {{ ($metode->status=="On")? "Aktif" : "Non Aktif" }}
                                                
                                                @endif
                                                    <option value ="0">Silahkan Pilih</option>
                                                    <option value ="Aktif">Aktif</option>
                                                    <option value ="Non Aktif">Non Aktif</option>
                                                </select>
	                                            <!-- <input class="form-control" type="text" name="status" placeholder="masukan nip/nik" value="{{ (isset($tenagamedis) ? $tenagamedis->status : '' )}}"> -->
	                                        </div>
	                                    </div>
                                    
                                    </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.metode.index') }}">
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
