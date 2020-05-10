@extends('layouts.admin')

@section('titleBar', 'Data Sekolah')

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
                                Data Sekolah
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <a href="{{ route('admin.sekolah.create') }}" class="btn btn-info btn-sm mb-2" id="tambah-data">Tambah</a>
                                <a href="{{ route('admin.sekolah.set_lockrka') }}" class="btn btn-secondary btn-sm mb-2" id="tambah-data">Setel RKA</a>
                                <a href="{{ route('admin.sekolah.set_periode') }}" class="btn btn-primary btn-sm mb-2" id="tambah-data">Setel Periode</a>

                                <table id="tabelSekolah" class="table table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Pilihan</td>
                                            <td>NPSN</td>
                                            <td>Nama Sekolah</td>
                                            <td>Jenjang</td>
                                            <td>Status</td>
                                            <td>Alamat</td>
                                            <td>Desa</td>
                                            <td>Kecamatan</td>
                                            <td>Telepon</td>
                                            <td>Nama Kepala Sekolah</td>
                                            <td>Nip Kepala Sekolah</td>
                                            <td>Nama Bendahara</td>
                                            <td>Nip Bendahara</td>
                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <td>No</td>
                                            <td>Pilihan</td>
                                            <td>NPSN</td>
                                            <td>Nama Sekolah</td>
                                            <td>Jenjang</td>
                                            <td>Status</td>
                                            <td>Alamat</td>
                                            <td>Desa</td>
                                            <td>Kecamatan</td>
                                            <td>Telepon</td>
                                            <td>Nama Kepsek</td>
                                            <td>Nip Kepsek</td>
                                            <td>Nama Bendahara</td>
                                            <td>Nip Bendahara</td>
                                            
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
        $('#tabelSekolah').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sekolah.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                { data: 'action', name: 'action', orderable: false, searchable: false}, 
                { data: 'npsn', name: 'npsn' },
                { data: 'name', name: 'name' },
                { data: 'jenjang', name: 'jenjang' },
                { data: 'status', name: 'status' },
                { data: 'alamat', name: 'alamat' },
                { data: 'desa', name: 'desa' },
                { data: 'kecamatan.nama_kecamatan', name: 'kecamatan.nama_kecamatan' },
                { data: 'telepon', name: 'telepon' },
                { data: 'nama_kepsek', name: 'nama_kepsek' },
                { data: 'nip_kepsek', name: 'nip_kepsek' },
                { data: 'nama_bendahara', name: 'nama_bendahara' },
                { data: 'nip_bendahara', name: 'nip_bendahara' },
            ],
            initComplete: function () {
                this.api().columns(':gt(1)').every(function () {
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

        @if($message = Session::get('success'))
            toastr.success("{{ $message }}", "Sukses!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif
        /* jshint ignore:end */

    });
</script>
@endsection
