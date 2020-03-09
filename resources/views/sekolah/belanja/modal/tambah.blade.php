@extends('layouts.sekolah')

@section('titleBar', ucfirst($aksi)." Belanja Modal")

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)." Belanja Modal" }}
                                <span class="badge badge-info">
                                    {{ $nama }} 
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ (isset($belanja))? route('sekolah.belanja.update', ['id'=>$belanja->id]) : route('sekolah.belanja.store') }}">
                                @csrf
                                @isset ($belanja)
                                    @method('PUT')
                                @endisset
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            
                                            
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="kode_barang_id">Kode Barang</label>
                                                    {{-- <input type="text" id="kode_barang_id" class="form-control" name="kode_barang_id" required value="{{ (isset($belanja))? $belanja->kode_barang_id : '' }}" placeholder="Kode Barang"> --}}
                                                    <select id='kode_barang_id' class="form-control" name="kode_barang_id" style="width:100%" required>
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="warna">Warna</label>
                                                    <input type="text" id="warna" class="form-control" name="warna" required value="{{ (isset($belanja))? $belanja->warna : '' }}" placeholder="Masukkan Warna">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="merek">Merek</label>
                                                    <input type="text" id="merek" class="form-control" name="merek" required value="{{ (isset($belanja))? $belanja->merek : '' }}" placeholder="Masukkan Merek">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="tipe">Tipe</label>
                                                    <input type="text" id="tipe" class="form-control" name="tipe" required value="{{ (isset($belanja))? $belanja->tipe : '' }}" placeholder="Masukkan Tipe">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="bahan">Bahan</label>
                                                    <input type="text" id="bahan" class="form-control" name="bahan" required value="{{ (isset($belanja))? $belanja->bahan : '' }}" placeholder="Masukkan Bahan">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-lg-6">

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="nomor_bukti">Nomor Bukti</label>
                                                    <input type="text" id="nomor_bukti" class="form-control" name="nomor_bukti" required value="{{ (isset($belanja))? $belanja->nomor_bukti : '' }}" placeholder="Masukkan Nomor Bukti">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="tanggal_bukti">Tanggal Bukti</label>
                                                    <input type="date" id="tanggal_bukti" class="form-control" name="tanggal_bukti" required value="{{ (isset($belanja))? $belanja->tanggal_bukti : '' }}" placeholder="Masukkan Nomor Bukti">
                                                </div>
                                            </div>
                                  
                                            <!-- <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="kas">Kas</label>
                                                    <select id='kas' class="form-control" name="kas" style="width:100%" required>
                                                        <option></option>
                                                        <option value="B">Kas Bank</option>
                                                        <option value="T">Kas Tunai</option>
                                                    </select>
                                                </div>
                                            </div> -->
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="qty">Qty</label>
                                                    <input type="text" id="qty" class="form-control" name="qty" required value="{{ (isset($belanja))? $belanja->qty : '' }}" placeholder="Masukkan Qty">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="satuan">Satuan</label>
                                                    <input type="text" id="satuan" class="form-control" name="satuan" required value="{{ (isset($belanja))? $belanja->satuan : '' }}" placeholder="Masukkan Satuan">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="harga_satuan">Harga Satuan</label>
                                                    <input type="text" id="harga_satuan" class="form-control rupiah" name="harga_satuan" required value="{{ (isset($belanja))? str_replace(".",",", $belanja->harga_satuan) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="total">Total</label>
                                                    <input type="text" id="total" class="form-control rupiah" name="total" required value="{{ (isset($belanja))? str_replace(".",",", $belanja->total) : '' }}">
                                                </div>
                                            </div>

                                            

                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('sekolah.belanja.modal',['id'=> $id]) }}">
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
<script src="{{ asset('app-assets/vendors/js/dt/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/inputmask/jquery.inputmask.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>

    $(document).ready(function () {
        $('#kode_barang_id').select2({
            ajax: {
                url: "{{ route('sekolah.select.kodebarang',['parent'=>$parent]) }}",
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
                                text: item.kode_barang+" - "+item.nama_barang
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
            placeholder: 'Pilih Kode Barang',
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
        @if($errors->any())
            toastr.error("{{ $errors->first() }}", "Error!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif

        @if (isset($belanja))
            
        @else
            $(".rupiah").val(0);
        @endif
        
        /* jshint ignore:end */
    });

</script>
@endsection
