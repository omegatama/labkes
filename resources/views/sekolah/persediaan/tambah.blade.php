@extends('layouts.sekolah')

@section('titleBar', 'Input Transaksi Persediaan')

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
                                {{(isset($aksi))? $aksi:''}} {{ ($jenis=='adjustment')? 'Penyesuaian':'Penggunaan'}} Persediaan
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{(isset($trx))? route('sekolah.trxpersediaan.update',['id'=>$trx->id]) : route('sekolah.trxpersediaan.store')}}">
                                @csrf
                                @isset ($trx)
                                    @method('PUT')
                                @endisset
                                <div class="form-body">
                                    
                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="jenis">Jenis</label>
                                            <select name="jenis" id="jenis" class="form-control" required>
                                                <option></option>
                                                <option value="Usage">Penggunaan</option>
                                                <option value="Adjustment">Penyesuaian</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="io">Tipe</label>
                                            <select name="io" id="io" class="form-control" required>
                                                <option></option>
                                                <option value="i">Masuk</option>
                                                <option value="o">Keluar</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="barang_persediaan_id">Barang Persediaan</label>
                                            <select name="barang_persediaan_id" id="barang_persediaan_id" class="form-control" required>
                                                
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="qty">Quantity</label>
                                            <input type="number" min="1" id="qty" class="form-control" name="qty" required placeholder="Masukkan Quantity" value="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="keterangan">Keterangan</label>
                                            <input type="text" id="keterangan" class="form-control" name="keterangan" required placeholder="Masukkan Keterangan" value="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="tanggal">Tanggal</label>
                                            <input type="date" id="tanggal" class="form-control" name="tanggal" required placeholder="Masukkan Tanggal" value="">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-actions pb-0">
                                    <a href="{{ ($jenis=='adjustment')? route('sekolah.persediaan.penyesuaian') : route('sekolah.persediaan.penggunaan')}}">
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
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('#io').select2({
            theme: 'bootstrap4',
            placeholder: "Pilih tipe {{ ($jenis=='adjustment')? 'Penyesuaian':'Penggunaan'}}"
        });

        $('#jenis').select2({
            theme: 'bootstrap4',
        });

        $('#barang_persediaan_id').select2({
            ajax: {
                url: "{{ route('sekolah.select.barangpersediaan') }}",
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
                                text: item.nama_persediaan + " - " + item.harga_satuan
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
            theme: 'bootstrap4',
            placeholder: 'Pilih Barang Persediaan'
        });

        /* jshint ignore:start */
        @isset ($trx)
            var option = new Option('{{$trx->persediaan->nama_persediaan}}' , '{{$trx->barang_persediaan_id}}', true, true);
            $('#barang_persediaan_id').append(option).trigger('change');
            $('#barang_persediaan_id').attr('readonly','readonly');
            $('#qty').val("{{$trx->qty}}");
            $('#keterangan').val("{{$trx->keterangan}}");
            $('#tanggal').val("{{$trx->tanggal->format('Y-m-d')}}");
            $('#tanggal').attr('readonly','readonly');
            $('#io').val('{{$trx->io}}');
            $('#io').trigger('change');
            $('#io').attr('readonly','readonly');
        @endisset

        @if ($jenis!='adjustment')
            $('#jenis').val('Usage');
            $('#jenis').trigger('change');
            $('#jenis').attr('readonly','readonly');
            $('#io').val('o');
            $('#io').trigger('change');
            $('#io').attr('readonly','readonly');
        @else
            $('#jenis').val('Adjustment');
            $('#jenis').trigger('change');
            $('#jenis').attr('readonly','readonly');
        @endif

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