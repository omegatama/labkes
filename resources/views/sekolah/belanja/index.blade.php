@extends('layouts.sekolah')

@section('titleBar', 'Belanja')

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
                                Belanja
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('sekolah.belanja.create') }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <div class="table-responsive">
                                <table id="tabelTrxKas" class="table table-bordered nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pilihan</th>
                                            <th class="cari" width="5">Triwulan</th>
                                            <th class="cari">Tanggal</th>
                                            <th class="cari" width="5">No Bukti</th>
                                            <th class="cari">Uraian</th>
                                            <th>Nominal</th>
                                            <th>Kegiatan</th>
                                            <th>RKA</th>
                                            <th>Nama Rekening</th>
                                            <th>Nama Program</th>
                                            <th>Nama Komponen</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Pilihan</th>
                                            <th>Triwulan</th>
                                            <th>Tanggal</th>
                                            <th>No Bukti</th>
                                            <th>Uraian</th>
                                            <th>Nominal</th>
                                            <th>Kegiatan</th>
                                            <th>RKA</th>
                                            <th>Rekening</th>
                                            <th>Program</th>
                                            <th>KP</th>
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

        $('#tabelTrxKas').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('sekolah.belanja.index') }}",
            dom: 'flrtp',
            /*columnDefs: [
                { responsivePriority: 1, targets: 3 },
                { responsivePriority: 2, targets: 0 },
                { responsivePriority: 3, targets: 4 },
            ],*/
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'triwulan', name: 'triwulan' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'nomor', name: 'nomor' },
                { data: 'nama', name: 'nama' },
                { data: 'nilai', name: 'nilai' },
                { data: 'rka.kegiatan.uraian', name: 'rka.kegiatan.uraian' },
                { data: 'rka.uraian', name: 'rka.uraian' },
                { data: 'rka.rekening.nama_rekening', name: 'rka.rekening.nama_rekening' },
                { data: 'rka.program.nama_program', name: 'rka.program.nama_program' },
                { data: 'rka.kp.nama_komponen', name: 'rka.kp.nama_komponen' },
                
            ],
            initComplete: function () {
                this.api().columns('.cari').every(function () {
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