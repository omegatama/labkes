@extends('layouts.admin')

@section('titleBar', 'data pekerjaan')

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
                                Data Pekerjaan
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.pekerjaan.create') }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            
                            <table id="pekerjaan" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Pekerjaan</th>
                                        <th>Pilihan</th>
                                    </tr>
                                </thead>
                            </table>
                            
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
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#pekerjaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.pekerjaan.index') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'kode', name: 'kode' },     
                { data: 'nama_pekerjaan', name: 'nama_pekerjaan' },
                { data: 'action', name: 'action', orderable: false, searchable: false}

            ]
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
