@extends('layouts.sekolah')

@section('titleBar', 'Transaksi Kas')

@section('extraCss')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
@endsection

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Transaksi Kas
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('sekolah.trxkas.create') }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            
                            <div class="table-responsive">
                                <table id="tabelTrxKas" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Ta</th>
                                            <th>Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Nominal</th>
                                            <th>Pilihan</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Ta</th>
                                            <th>Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Nominal</th>
                                            <th>Pilihan</th>
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
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    $(function() {
        $('#tabelTrxKas').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sekolah.trxkas.index') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'ta', name: 'ta' }, 
                { data: 'tanggal', name: 'tanggal' },
                { data: 'tipe', name: 'tipe' },
                { data: 'nominal', name: 'nominal' },
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            initComplete: function () {
                this.api().columns(':gt(0):lt(3)').every(function () {
                    var column = this;
                    var input = document.createElement('input');
                    $(input).addClass('form-control m-0');
                    $(column.footer()).addClass('p-1');
                    $(input).appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                        column.search(val ? val : '', true, false).draw();
                    });
                });

                $('.confirmation').on('click', function () {
                    return confirm('Apakah anda yakin akan menghapus Data Trx ini?');
                });
            }
        });
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