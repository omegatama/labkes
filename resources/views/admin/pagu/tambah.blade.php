@extends('layouts.admin')

@section('titleBar', ucfirst($aksi)." Pagu")

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
                                {{ ucfirst($aksi)." Pagu" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            @if (!empty($pagu))
                                <form class="form" method="POST" action="{{ route('admin.pagu.update', ['id'=>$pagu->id]) }}">
                                    @method('PUT')
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="form-group col-12 mb-2">
                                                
                                                <select id='npsn' class="form-control" name="npsn" style="width:100%"> 
                                                    <option value="{{ $pagu->npsn }}" selected>{{ $pagu->sekolah->name }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-12 mb-2">
                                                <input type="text" value="{{ str_replace(".",",",$pagu->pagu) }}" id="nominal" class="form-control py-2 text-left" name="nominal" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.pagu.index') }}">
                                            <button type="button" class="btn btn-raised btn-warning mb-0 mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </button>  
                                        </a>
                                        <button type="submit" class="btn btn-raised btn-primary mb-0">
                                            <i class="fa fa-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </form>
                            @else
                                <form class="form" method="POST" action="{{ route('admin.pagu.store') }}">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="form-group col-12 mb-2">
                                                
                                                <select id='npsn' class="form-control" name="npsn" style="width:100%"> 
                                                    {{-- <option value="23456789" selected>SMP OMEGATAMA</option> --}}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-12 mb-2">
                                                <input type="text" id="nominal" class="form-control text-left py-2" name="nominal" required>
                                            </div>
                                        </div>
                                    
                                        {{-- <div class="row">
                                            <div class="form-group col-12 mb-2">
                                                <input type="date" id="tanggal" class="form-control round" name="tanggal">
                                            </div>
                                        </div> --}}
                                    </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.pagu.index') }}">
                                            <button type="button" class="btn btn-raised btn-warning mb-0 mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </button>  
                                        </a>
                                        <button type="submit" class="btn btn-raised btn-primary mb-0">
                                            <i class="fa fa-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </form>
                            
                            @endif
                            
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
        $('#npsn').select2({
            ajax: {
                url: "{{ route('admin.sekolah.select') }}",
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
                                id: item.npsn,
                                text: item.name
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
            placeholder: 'Pilih Sekolah',
            theme: 'bootstrap4',
            // minimumInputLength: 2,
            // multiple: true
        });

        if ($('#npsn').val() != null) {
            $('#npsn').trigger('change');
            // alert($('#npsn').val());
        }

        Inputmask.extendAliases({
            rupiah: {
                prefix: "Rp ",
                alias: "numeric",
                radixPoint: ',',
                groupSeparator: '.',
                autoGroup: true,
                digits: 2,
                digitsOptional: !1,
                clearMaskOnLostFocus: !1,
                removeMaskOnSubmit:true,
            }
        });
        $("#nominal").inputmask({ alias : "rupiah" });

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

@isset ($pagu)
<script>
    $('#npsn').select2({
        disabled:'readonly'
    });
</script>
@endisset

@endsection
