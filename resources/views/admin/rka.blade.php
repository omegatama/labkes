@extends('layouts.admin')

@section('titleBar', 'Rka Awal')

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
                                Rka Awal
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('sekolah.rka.create') }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <div class="table-responsive">
                                <table id="tabelRka" class="table table-bordered nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            {{-- <th>Ta</th> --}}
                                            <th>NPSN</th>
                                            <th>Nama Sekolah</th>
                                            <th>Program</th>
                                            <th>Kegiatan</th>
                                            <th>KP</th>
                                            <th>Rekening</th>
                                            <th>Uraian</th>
                                            <th>Volume</th>
                                            <th>Satuan</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>TW1</th>
                                            <th>TW2</th>
                                            <th>TW3</th>
                                            <th>TW4</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            {{-- <th>Ta</th> --}}
                                            <th>NPSN</th>
                                            <th>Nama Sekolah</th>
                                            <th>Program</th>
                                            <th>Kegiatan</th>
                                            <th>KP</th>
                                            <th>Rekening</th>
                                            <th>Uraian</th>
                                            <th>Volume</th>
                                            <th>Satuan</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>TW1</th>
                                            <th>TW2</th>
                                            <th>TW3</th>
                                            <th>TW4</th>
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
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#tabelRka').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('admin.rka.index') }}",
            dom: 'flrtp',
            columnDefs: [
                { responsivePriority: 1, targets: 2 },
                { responsivePriority: 2, targets: 0 },
                { responsivePriority: 3, targets: 7 },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                // { data: 'ta', name: 'ta' }, 
                { data: 'npsn', name: 'npsn' }, 
                { data: 'sekolah.name', name: 'sekolah.name' }, 
                { data: 'program.nama_program', name: 'program.nama_program' },
                { data: 'kegiatan.uraian', name: 'kegiatan.uraian' },
                { data: 'kp.kode_komponen', name: 'kp.kode_komponen' },
                { data: 'rekening.nama_rekening', name: 'rekening.nama_rekening' },
                { data: 'uraian', name: 'uraian' },
                { data: 'volume', name: 'volume' },
                { data: 'satuan', name: 'satuan' },
                { data: 'harga_satuan', name: 'harga_satuan' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'alokasi_tw1', name: 'alokasi_tw1' },
                { data: 'alokasi_tw2', name: 'alokasi_tw2' },
                { data: 'alokasi_tw3', name: 'alokasi_tw3' },
                { data: 'alokasi_tw4', name: 'alokasi_tw4' },
                
            ],
            initComplete: function () {
                this.api().columns(':gt(0):lt(8)').every(function () {
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

                $('.confirmation').on('click', function () {
                    return confirm('Apakah anda yakin akan menghapus RKA ini?');
                });
            }
        });

        // $('#tabelRka').on( 'draw.dt', function () {
            
        // } );
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