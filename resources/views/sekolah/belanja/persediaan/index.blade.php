@extends('layouts.sekolah')

@section('titleBar', 'Belanja Persediaan')

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
<style>
td.details-control {
    background: url("{{ asset('app-assets/img/icons/details_open.png') }}") no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url("{{ asset('app-assets/img/icons/details_close.png') }}") no-repeat center center;
}
</style>
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
                                Belanja Persediaan
                            </h4>
                        </div>
                        <div class="card-body">
                            {{-- <a href="{{ route('sekolah.belanja.create') }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a> --}}
                            <div class="table-responsive">
                                <table id="tabelBelanjaPersediaan" class="table table-bordered nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>No</th>
                                            <th>Pilihan</th>
                                            <th class="cari" width="5">TW</th>
                                            <th class="cari">Tanggal</th>
                                            <th class="cari" width="5">No Bukti</th>
                                            <th class="cari">Uraian</th>
                                            <th>Nominal</th>
                                            {{-- <th class="cari">Kegiatan</th> --}}
                                            {{-- <th class="cari">RKA</th> --}}
                                            <th class="cari">Nama Rekening</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>No</th>
                                            <th>Pilihan</th>
                                            <th>TW</th>
                                            <th>Tanggal</th>
                                            <th>No Bukti</th>
                                            <th>Uraian</th>
                                            <th>Nominal</th>
                                            {{-- <th>Kegiatan</th> --}}
                                            {{-- <th>RKA</th> --}}
                                            <th>Nama Rekening</th>
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
<script src="{{ asset('app-assets/vendors/js/handlebars.min.js') }}" type="text/javascript"></script>
<script id="details-template" type="text/x-handlebars-template">
    @verbatim
    <span class="badge badge-info">Detail Belanja Persediaan {{ nama }}</span>
    <table class="table details-table" id="persediaan-{{id}}">
        <thead>
            <tr>
                <th>Id</th>
                <th>Bank account number</th>
                <th>Company</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Id</th>
                <th>Bank account number</th>
                <th>Company</th>
            </tr>
        </tfoot>
    </table>
    @endverbatim
</script>
<script>
    function initTable(tableId, data) {
        $('#' + tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: data.details_url,
            dom: 'frtp',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'bank_acc_number', name: 'bank_acc_number' },
                { data: 'company', name: 'company'}
            ]
        });
    }

    $(function() {

        var template = Handlebars.compile($("#details-template").html());
        var table= $('#tabelBelanjaPersediaan').DataTable({
            processing: true,
            serverSide: true,
            // responsive: true,
            ajax: "{{ route('sekolah.belanjapersediaan.index') }}",
            dom: 'flrtp',
            /*columnDefs: [
                { responsivePriority: 1, targets: 3 },
                { responsivePriority: 2, targets: 0 },
                { responsivePriority: 3, targets: 4 },
            ],*/
            order: [[1, 'asc']],
            columns: [
                {
                    "className":      'details-control',
                    "orderable":      false,
                    "searchable":     false,
                    "data":           null,
                    "defaultContent": ''
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'triwulan', name: 'triwulan' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'nomor', name: 'nomor' },
                { data: 'nama', name: 'nama' },
                { data: 'nilai', name: 'nilai' },
                // { data: 'rka.kegiatan.uraian', name: 'rka.kegiatan.uraian' },
                // { data: 'rka.uraian', name: 'rka.uraian' },
                { data: 'rka.rekening.nama_rekening', name: 'rka.rekening.nama_rekening' },
                
                
            ],
            initComplete: function () {
                /*this.api().columns('.cari').every(function () {
                    var column = this;
                    var input = document.createElement('input');
                    $(input).addClass('form-control m-0');
                    $(column.footer()).addClass('p-1');
                    $(input).appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $(this).val();
                        column
                        .search( val )
                        .draw();
                    });
                });

                $('.confirmation').on('click', function () {
                    return confirm('Apakah anda yakin akan menghapus Trx ini?');
                });*/
            }
        });

        // Add event listener for opening and closing details
        $('#tabelBelanjaPersediaan tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var tableId = 'persediaan-' + row.data().id;
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(template(row.data())).show();
                initTable(tableId, row.data());
                console.log(row.data());
                tr.addClass('shown');
                tr.next().find('td').addClass('no-padding bg-gray');
            }
        });

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