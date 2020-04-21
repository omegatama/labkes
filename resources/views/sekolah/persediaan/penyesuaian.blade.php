@extends('layouts.sekolah')

@section('titleBar', 'Penyesuaian Persediaan')

@section('extraCss')
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
                                Penyesuaian Persediaan
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('sekolah.trxpersediaan.create',['jenis'=>'adjustment']) }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <div>
                                <table id="tabelPenggunaan" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>TA</th>
                                            <th>Tanggal</th>
                                            <th>Nama Persediaan</th>
                                            <th>Harga</th>
                                            <th>In/Out</th>
                                            <th>Qty</th>
                                            <th>Keterangan</th>
                                            <th>Pilihan</th>
                                        </tr>
                                    </thead>
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
        $('#tabelPenggunaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sekolah.persediaan.penyesuaian') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'ta', name: 'ta' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'persediaan.nama_persediaan', name: 'persediaan.nama_persediaan' },
                { data: 'persediaan.harga_satuan', name: 'persediaan.harga_satuan' },
                { data: 'io', name: 'io' },
                { data: 'qty', name: 'qty' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            initComplete: function () {
                $('.confirmation').on('click', function () {
                    return confirm('Apakah anda yakin akan menghapus Trx ini?');
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