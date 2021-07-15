@extends('layouts.admin')

@section('titleBar', 'Data Sub1 kategori tarif')

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
                                Data Sub1 Kategori
                                @isset($kategori)
                                <span style='color:red'>
                                    {{$kategori->namakategori." (".$kategori->kodekategori.")"}}
                                </span>
                                    
                                @endisset
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.sub1kategoritarif.create',['idkategori' => $kategori->id]) }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            

                            <table id="sub1kategoritarif" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>KodeSub1</th>
                                        <th>Nama Sub1 Katagori</th>
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

        $('#sub1kategoritarif').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sub1kategoritarif.index',['idkategori' => $kategori->id]) }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'kodesub1kategori', name: 'kodesub1kategori' },     
                { data: 'namasub1kategori', name: 'namasub1kategori' },
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
