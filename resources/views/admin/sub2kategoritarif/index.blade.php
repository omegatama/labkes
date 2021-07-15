@extends('layouts.admin')

@section('titleBar', 'Data Sub2 katagori tarif')

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
                                Data Sub2 Kategori Tarif
                                
                                @isset($sub1kategori)
                                <div style='color:red'>
                                    {{$sub1kategori->kategori->namakategori." (".$sub1kategori->kategori->kodekategori.")"}}
                                </div>
                                <div style='color:blue'>
                                    {{$sub1kategori->namasub1kategori." (".$sub1kategori->kodesub1kategori.")"}}
                                </div> 
                                @endisset
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.sub2kategoritarif.create',['idsub1' => $sub1kategori->id]) }}" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            

                            <table id="sub2kategoritarif" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>KodeSub2</th>
                                        <th>Nama Sub2 Katagori</th>
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

        $('#sub2kategoritarif').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sub2kategoritarif.index',['idsub1' => $sub1kategori->id]) }}",
            dom: 'flrtp',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
                { data: 'kodesub2kategori', name: 'kodesub2kategori' },     
                { data: 'namasub2kategori', name: 'namasub2kategori' },
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
