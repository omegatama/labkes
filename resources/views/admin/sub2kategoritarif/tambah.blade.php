@extends('layouts.admin')

@section('titleBar', ucfirst($aksi)." Data Sub2 Katagori Tarif")

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
                                {{ ucfirst($aksi)." Data Sub2 Katagori Tarif" }}
                                @isset($sub1kategori)
                                <div style='color:red'>
                                    {{$sub1kategori->kategori->namakategori." (".$sub1kategori->kategori->kodekategori.")"}}
                                </div>
                                <div style='color:blue'>
                                    {{$sub1kategori->namasub1kategori." (".$sub1kategori->kodesub1kategori.")"}}
                                </div>
                                @endisset
                            </h4>
                        </div>
                        <div class="card-body">
                            
                                <form class="form" method="POST" action="{{ route('admin.sub2kategoritarif.store',['idsub1' => $sub1kategori->id]) }}">
                                    @csrf

                                    @if (isset($sub2kategori))
                                        @method('PUT')
                                    @endif
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="form-group col-12 mb-2">
                                                
                                                
                                            </div>
                                        </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">KodeSub2</label>
	                                        <div class="col-lg-3">
	                                            <input class="form-control" type="text" name="kodesub2kategori" placeholder="masukan kode sub2 kategori" value="{{ (isset($sub2kategori) ? ($sub2kategori->kodesub2kategori) : '') }}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Nama Sub2 Kategori</label>
	                                        <div class="col-lg-7">
	                                            <input class="form-control" type="text" name="namasub2kategori" placeholder="masukan nama sub2 kategori">
	                                        </div>
	                                    </div>

                                </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.sub2kategoritarif.index') }}">
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
