@extends('layouts.sekolah')

@section('titleBar', 'Penerimaan Dana')

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
                                Penerimaan Dana
                            </h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <table id="tabelPenerimaan" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Ta</th>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
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
        $('#tabelPenerimaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sekolah.kas.penerimaan') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'ta', name: 'ta' }, 
                { data: 'tanggal', name: 'tanggal' },
                { data: 'nominal', name: 'nominal' },
            ]
        });
    });
</script>

@endsection