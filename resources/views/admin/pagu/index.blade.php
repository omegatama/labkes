@extends('layouts.admin')

@section('titleBar', 'Pagu Awal')

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
                                Pagu Awal
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.pagu.create') }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <a href="{{ route('admin.pagu.upload') }}" class="btn btn-secondary btn-sm m-0" id="tambah-data">Upload</a>
                            <a href="{{ route('admin.pagu.update_upload') }}" class="btn btn-warning btn-sm m-0" id="tambah-data">Update</a>
                            

                            <table id="tabelPagu" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Ta</th>
                                        <th>NPSN</th>
                                        <th>Nama Sekolah</th>
                                        <th>Pagu</th>
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
    function hapus_pagu(id) {
        swal({
            title: "Apakah anda yakin?",
            text: "Anda akan menghapus Pagu Sekolah",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0CC27E',
            cancelButtonColor: '#FF586B',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Tidak, batalkan!',
            confirmButtonClass: 'btn btn-success btn-raised mr-5',
            cancelButtonClass: 'btn btn-danger btn-raised',
            buttonsStyling: false
        }).then(function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "DELETE",
                    url: "pagu/"+id,
                    success: function (data) {
                        swal(
                            'Data Terhapus!',
                            'Data Pagu telah dihapus',
                            'success'
                        ).then(function() {
                            var oTable = $('#tabelPagu').dataTable(); 
                            oTable.fnDraw(false);
                        });
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        }).catch(swal.noop);
    }

    $(function() {
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#tabelPagu').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.pagu.index') }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'ta', name: 'ta' },     
                { data: 'npsn', name: 'npsn' },
                { data: 'sekolah.name', name: 'sekolah.name' },
                { data: 'pagu', name: 'pagu' },
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
