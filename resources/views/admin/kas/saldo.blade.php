@extends('layouts.admin')

@section('titleBar', 'Saldo Kas')

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
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
                                Saldo Kas
                            </h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <table id="tabelSaldo" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NPSN</th>
                                            <th>Sekolah</th>
                                            <th>Saldo Bank</th>
                                            <th>Saldo Tunai</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>NPSN</th>
                                            <th>Sekolah</th>
                                            <th>Saldo Bank</th>
                                            <th>Saldo Tunai</th>
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
<script>
    $(function() {
        $('#tabelSaldo').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.kas.saldo') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'npsn', name: 'npsn' },
                { data: 'sekolah.name', name: 'sekolah.name' }, 
                { data: 'saldo_bank', name: 'saldo_bank' },
                { data: 'saldo_tunai', name: 'saldo_tunai' },
            ],
            initComplete: function () {
                this.api().columns(':gt(0):lt(2)').every(function () {
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

            }
        });
    });
</script>

@endsection