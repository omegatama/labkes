@extends('layouts.sekolah')

@section('titleBar', "Detail Belanja Persediaan")

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
                                {{ "Belanja Persediaan" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered my-0">
                                <thead>
                                    <tr class="bg-info text-white">
                                        <th>Tanggal</th>
                                        <th>Uraian</th>
                                        <th>Nilai</th>
                                        <th>Rekening</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{$belanja->tanggal->format('d/m/Y')}}
                                        </td>
                                        <td>
                                            {{$belanja->nama}}
                                        </td>
                                        <td>
                                            {{FormatMataUang($belanja->nilai)}}
                                        </td>
                                        <td>
                                            {{
                                                $belanja->rka->rekening->parent->kode_rekening.".".
                                                $belanja->rka->rekening->kode_rekening." - ".
                                                $belanja->rka->rekening->nama_rekening
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ "Detail Belanja Persediaan" }}
                                <span class="badge badge-info">
                                    {{ $belanja->nama }} 
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('sekolah.belanja.createpersediaan', ['id'=>$belanja->id]) }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <div>
                                <table id="tabelPersediaan" class="table table-bordered nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle">No</th>
                                            <th rowspan="2" class="align-middle">Pilihan</th>
                                            <th colspan="2" class="text-center">Data Persediaan</th>
                                            <th colspan="2" class="text-center">Data Pembelian</th>
                                        </tr>
                                        <tr>
                                            <th>Nama Persediaan</th>
                                            <th>Harga Persediaan</th>

                                            <th>Qty</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Pilihan</th>
                                            <th>Nama Persediaan</th>
                                            <th>Harga Persediaan</th>
                                            <th>Qty</th>
                                            <th>Total: <span id="total"></span></th>
                                        </tr>
                                    </tfoot>

                                    
                                </table>
                            </div>

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
        var table = $('#tabelPersediaan').DataTable({
            order: [],
            processing: true,
            serverSide: true,
            // responsive: true,
            ajax: "{{ route('sekolah.belanja.getpersediaan',['id' => $belanja->id]) }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'barang_persediaan.nama_persediaan', name: 'barang_persediaan.nama_persediaan' },
                { data: 'barang_persediaan.harga_satuan', name: 'barang_persediaan.harga_satuan' },
                { data: 'qty', name: 'qty' },
                { data: 'total', name: 'total' },
                
            ],
            initComplete: function () {
                $('#total').html( this.api().ajax.json().total );
                $('.confirmation').on('click', function () {
                    return confirm('Apakah anda yakin akan menghapus Trx ini?');
                });
                /*this.api().columns('.cari').every(function () {
                    var column = this;
                    var input = document.createElement('input');
                    $(input).addClass('form-control m-0');
                    $(column.footer()).addClass('p-1');
                    $(input).appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                        column.search(val ? val : '', true, false).draw();
                    });
                });*/   
            }
        });
        
        /* jshint ignore:start */
        @if($errors->any())
            toastr.error("{{ $errors->first() }}", "Error!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif


        @if (isset($belanja))
            
        @else
            
        @endif
        
        /* jshint ignore:end */
    });

</script>
@endsection
