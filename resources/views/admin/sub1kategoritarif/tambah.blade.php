@extends('layouts.admin')

@section('titleBar', ucfirst($aksi)." Data Sub1 Katagori Tarif")

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
                                {{ ucfirst($aksi)." Data Sub1 Katagori Tarif" }}
                                @isset($kategori)
                                <div style='color:red'>
                                    {{$kategori->namakategori." (".$kategori->kodekategori.")"}}
                                </div>
                                    
                                @endisset
                                
                            </h4>
                        </div>
                        <div class="card-body">
                            
                                <form class="form" method="POST" action="{{ (isset($sub1kategori))? route('admin.sub1kategoritarif.update', ['id'=>$sub1kategori->id, 'idkategori' => $kategori->id]) : route('admin.sub1kategoritarif.store',['idkategori' => $kategori->id]) }}">
                                    @csrf

                                    @if (isset($sub1kategori))
                                        @method('PUT')
                                    @endif
                                    <div class="form-body">
                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">KodeSub1</label>
	                                        <div class="col-lg-3">
	                                            <input class="form-control" type="text" name="kodesub1kategori" placeholder="masukan kode sub1 kategori" value="{{ (isset($sub1kategori) ? ($sub1kategori->kodesub1kategori) : '') }}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Nama Sub1 Kategori</label>
	                                        <div class="col-lg-7">
	                                            <input class="form-control" type="text" name="namasub1kategori" placeholder="masukan nama sub1 kategori" value="{{ (isset($sub1kategori) ? ($sub1kategori->namasub1kategori) : '') }}">
	                                        </div>
	                                    </div>

                                </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.sub1kategoritarif.index',['idkategori' => $kategori->id]) }}">
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
