@extends('layouts.admin')

@section('titleBar', "Setel Periode Aktif Sekolah")

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
                                Setel Periode Aktif
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ route('admin.sekolah.proses_set_periode') }}">
                                @csrf
                                
                                <div class="form-body">
                                    
                                    <!-- <div class="form-group mb-1">
                                        <label class="m-0" for="jenjang">Jenjang</label>
                                        <select id='jenjang' class="form-control" name="jenjang" style="width:100%">
                                            <option value="">Pilih Jenjang</option>
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-1">
                                        <label class="m-0" for="status">Status</label>
                                        <select id='status' class="form-control" name="status" style="width:100%">
                                            <option value="">Pilih Status</option>
                                            <option value="Negeri">Negeri</option>
                                            <option value="Swasta">Swasta</option>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="kecamatan_id">Kecamatan</label>
                                            <select class="form-control" name="kecamatan_id" id="kecamatan_id">
                                                <option value=""></option>
                                            </select>
                                            
                                        </div>
                                    </div> -->

                                    <div class="form-group mb-1">
                                        <label class="m-0" for="periode_awal">Periode Awal</label>
                                        <input type="date" name="periode_awal" id="periode_awal" class="form-control">
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="m-0" for="periode_akhir">Periode Akhir</label>
                                        <input type="date" name="periode_akhir" id="periode_akhir" class="form-control">
                                    </div>

                                </div>
                                        

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('admin.sekolah.index') }}">
                                        <button type="button" class="btn btn-raised btn-warning mb-0 mr-1">
                                            <i class="ft-x"></i> Cancel
                                        </button>  
                                    </a>
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
        /*$('#kecamatan_id').select2({
            ajax: {
                url: "{{ route('admin.select.kecamatan') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.nama_kecamatan
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    };
                },
                cache: true,
                delay: 250
            },
            placeholder: 'Pilih Kecamatan',
            theme: 'bootstrap4',
            allowClear: true
        });*/

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
