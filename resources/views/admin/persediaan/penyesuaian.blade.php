@extends('layouts.admin')

@section('titleBar', 'Penyesuaian Persediaan')

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
                                Penyesuaian Persediaan
                            </h4>
                        </div>
                        <div class="card-body">
                            
                            <div>
                                <table id="tabelPenggunaan" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NPSN</th>
                                            <th>Sekolah</th>
                                            
                                            <th>Tanggal</th>
                                            <th>Nama Persediaan</th>
                                            <th>Harga</th>
                                            <th>In/Out</th>
                                            <th>Qty</th>
                                            <th>Keterangan</th>
                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>NPSN</th>
                                            <th>Sekolah</th>
                                            
                                            <th>Tanggal</th>
                                            <th>Nama Persediaan</th>
                                            <th>Harga</th>
                                            <th>In/Out</th>
                                            <th>Qty</th>
                                            <th>Keterangan</th>
                                            
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
        $('#tabelPenggunaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.persediaan.penyesuaian') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'persediaan.npsn', name: 'persediaan.npsn' },
                { data: 'persediaan.sekolah.name', name: 'persediaan.sekolah.name' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'persediaan.nama_persediaan', name: 'persediaan.nama_persediaan' },
                { data: 'persediaan.harga_satuan', name: 'persediaan.harga_satuan' },
                { data: 'io', name: 'io' },
                { data: 'qty', name: 'qty' },
                { data: 'keterangan', name: 'keterangan' },
                
            ],
            initComplete: function () {
                this.api().columns(':gt(0)').every(function () {
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