@extends('layouts.admin')

@section('titleBar', "Update Pagu")

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
                                Update Pagu
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ route('admin.pagu.proses_update_upload') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="form-group col-12 mb-2">
                                            <select id='ta' class="form-control" name="ta">
                                                <option value="{{Cookie::get('ta')}}" selected>{{Cookie::get('ta')}}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row">
                                        
                                        <div class="form-group col-12 mb-2">
                                            
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="file" name="file">
                                                <label class="custom-file-label form-control" for="file" style="text-transform: capitalize;">Pilih File</label>
                                            </div>
                                        
                                        </div>
                                    </div>
                                
                                </div>

                                <div class="form-actions pb-0">
                                    <a href="{{ route('admin.pagu.download_pagulama') }}" target="_blank">
                                        <button type="button" class="btn btn-raised mb-1 mr-1 btn-success">
                                            <i class="fa fa-download"></i> Download Pagu Lama
                                        </button>
                                    </a>

                                    <a href="{{ route('admin.pagu.index') }}">
                                        <button type="button" class="btn btn-raised btn-warning mb-1 mr-1">
                                            <i class="ft-x"></i> Cancel
                                        </button>  
                                    </a>
                                    <button type="submit" class="btn btn-raised btn-primary mb-1">
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
        $('.custom-file input').change(function (e) {
            $(this).next('.custom-file-label').html(e.target.files[0].name);
        });


        $('#ta').select2({
            theme: 'bootstrap4'
        });
        $('#ta').attr("readonly", "readonly");

        

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
