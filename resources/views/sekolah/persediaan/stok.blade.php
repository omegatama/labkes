@extends('layouts.sekolah')

@section('titleBar', 'Stok Persediaan')

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
                                Stok Persediaan
                            </h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <table id="tabelPersediaan" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Persediaan</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
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
<script>
    $(function() {
        $('#tabelPersediaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sekolah.persediaan.stok') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'nama_persediaan', name: 'nama_persediaan' },
                { data: 'harga_satuan', name: 'harga_satuan' },
                { data: 'stok', name: 'stok' },
            ]
        });
    });
</script>

@endsection