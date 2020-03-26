@extends('layouts.admin')

@section('titleBar', "Ubah Password")

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2/select2-bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
<style>
select[readonly].select2-hidden-accessible + .select2-container {
  pointer-events: none;
  touch-action: none;
}

select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
  background: #ECEFF1;
  border: 1px solid #A6A9AE;
  box-shadow: none;
}

select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
  display: none;
}
</style>
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
                                Ubah Password
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{route('admin.password.update')}}">
                                @csrf
                                
                                <div class="form-body">

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="password">Password Lama</label>
                                            <input type="password" id="password" class="form-control" name="password" required placeholder="Masukkan Password Lama" value="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="password_baru">Password Baru</label>
                                            <input type="password" id="password_baru" class="form-control" name="password_baru" required placeholder="Masukkan Password Baru" value="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="konfirmasi">Konfirmasi Password</label>
                                            <input type="password" id="konfirmasi" class="form-control" name="konfirmasi" required placeholder="Masukkan Konfirmasi Password" value="">
                                        </div>
                                    </div>

                                    

                                </div>

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('admin.profil.index') }}">
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

        /* jshint ignore:start */
        @if($errors->any())
            toastr.error("{{ $errors->first() }}", "Error!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif
        /* jshint ignore:end */

    });

</script>

@endsection
