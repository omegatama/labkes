@extends('layouts.sekolah')

@section('titleBar', ucfirst($aksi)." Transaksi Kas")

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

@php
    $periode_awal= Auth::user()->periode_awal;
    $periode_akhir= Auth::user()->periode_akhir;      
@endphp

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row justify-content-md-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)." Transaksi Kas" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ (isset($trx))? route('sekolah.trxkas.update', ['id'=>$trx->id]) : route('sekolah.trxkas.store') }}">
                                @csrf
                                @isset ($trx)
                                    @method('PUT')
                                @endisset
                                <div class="form-body">
                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="tanggal">Tanggal</label>
                                            <input min="{{$periode_awal}}" max="{{$periode_akhir}}" type="date" id="tanggal" class="form-control" name="tanggal" required placeholder="Masukkan Tanggal Trx" value="{{ (isset($trx))? $trx->tanggal->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="tipe">Tipe Transaksi</label>
                                            <select id='tipe' class="form-control" name="tipe" style="width:100%" required>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="nominal">Nominal</label>
                                            <input type="text" id="nominal" class="form-control rupiah" name="nominal" required placeholder="Masukkan Nominal Trx" value="{{ (isset($trx))? $trx->nominal : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="keterangan">Keterangan</label>
                                            <input type="text" id="keterangan" class="form-control" name="keterangan" required placeholder="Masukkan Keterangan Trx" value="{{ (isset($trx))? $trx->keterangan : '' }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="form-actions pb-0">
                                    <a href="{{ route('sekolah.trxkas.index') }}">
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
        var jenistrx = [
            {
                id: 'Pindah Buku',
                text: 'Pemindahbukuan'
            },
            {
                id: 'Setor Kembali',
                text: 'Setor Sisa Kas ke Rekening Bank'
            },
            {
                id: 'Bunga',
                text: 'Bunga Bank'
            },
        ];

        $('#tipe').select2({
            data: jenistrx,
            placeholder: 'Pilih Tipe Transaksi',
            theme: 'bootstrap4',
        });

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

        $(".rupiah").inputmask({ alias : "rupiah" });
        /* jshint ignore:start */
        @isset ($trx)
            $('#tipe').val('{{$trx->tipe}}'); // Select the option with a value of '1'
            $('#tipe').trigger('change'); // Notify any JS components that the value changed
            $('#tipe').attr('readonly','readonly');
            $('#tanggal').attr('readonly','readonly');

        @endisset
        /* jshint ignore:end */

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
