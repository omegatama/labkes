@extends('layouts.sekolah')

@section('titleBar', 'Pagu Awal')

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
                                Pagu Awal
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabelPagu" class="table table-bordered nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Ta</th>
                                            <th>Pagu</th>
                                            <th>Penggunaan TW1</th>
                                            <th>Penggunaan TW2</th>
                                            <th>Penggunaan TW3</th>
                                            <th>Penggunaan TW4</th>
                                            <th>Sisa</th>
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
        $('#tabelPagu').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('sekolah.pagu.index') }}",
            dom: 't',
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 7 },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'ta', name: 'ta' },     
                { data: 'pagu', name: 'pagu' },
                { data: 'penggunaan_tw1', name: 'penggunaan_tw1' },
                { data: 'penggunaan_tw2', name: 'penggunaan_tw2' },
                { data: 'penggunaan_tw3', name: 'penggunaan_tw3' },
                { data: 'penggunaan_tw4', name: 'penggunaan_tw4' },
                { data: 'sisa', name: 'sisa' },
            ]
        });
    });
</script>
@endsection