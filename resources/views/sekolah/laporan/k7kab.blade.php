@extends('layouts.sekolah')

@section('titleBar', "Laporan K7Kab")

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
                                Laporan K7Kab
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ route('sekolah.proses.k7kab') }}">
                                @csrf
                                
                                <div class="form-body">
                                    
                                    <div class="form-group mb-1">
                                        <label class="m-0" for="triwulan">Triwulan</label>
                                        <select id='triwulan' class="form-control" name="triwulan" style="width:100%" required>
                                            <option>Pilih Triwulan</option>
                                            <option value="1">Triwulan 1</option>
                                            <option value="2">Triwulan 2</option>
                                            <option value="3">Triwulan 3</option>
                                            <option value="4">Triwulan 4</option>
                                            
                                        </select>
                                    </div>

                                </div>
                                        

                                <div class="form-actions pb-0 text-right">
                                    
                                    <button type="submit" class="btn btn-raised btn-primary mb-0">
                                        <i class="fa fa-check-square-o"></i> Proses
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
