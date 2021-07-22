@extends('layouts.admin')

@section('titleBar', ucfirst($aksi)." Data Nama Tarif")

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2/select2-bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
@endsection

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row justify-content-md-center">
                <div class="col-lg-9 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)." Data Nama Tarif" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            
                                <form class="form" method="POST" action="{{ (isset($namatarif))? route('admin.namatarif.update', ['id'=>$namatarif->id]) : route('admin.namatarif.store') }}">
                                    @csrf
                                    
                                    @if (isset($namatarif))
                                        @method('PUT')
                                    @endif

                                    <div class="form-body">
                                        
                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Kode Kategori</label>
	                                        <div class="col-lg-7">
                                            <select name="kategori" class="form-control" style="width:100%">
                                                <option value="">--- Kode Kategori ---</option>
                                                @foreach ($kategori as $key => $item)
                                                <option value="{{ $item->id }}">{{ $item->namakategori }}</option>
                                                @endforeach
                                            </select>
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Kode Sub1</label>
	                                        <div class="col-lg-5">
                                            <select name="kodesub1" class="form-control"style="width:100%">
                                                <option>--Kode Sub 1--</option>
                                            </select>
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Kode Sub2</label>
	                                        <div class="col-lg-5">
                                            <select name="kodesub2" class="form-control"style="width:100%">
                                                <option>--Kode Sub 2--</option>
                                            </select>
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Kode Tarif</label>
	                                        <div class="col-lg-3">
	                                            <input class="form-control" type="text" name="kodenamatarif" placeholder="masukan kode nama tarif" value="{{ (isset($namatarif) ? $namatarif->kodenamatarif : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Nama Tarif</label>
	                                        <div class="col-lg-7">
	                                            <input class="form-control" type="text" name="namatarif" placeholder="masukan nama tarif" value="{{ (isset($namatarif) ? $namatarif->namatarif : '' )}}">
	                                        </div>
	                                    </div>

                                        <div class="form-group row" style="margin-bottom: 5px">
	                                        <label for="example-text-input" class="col-lg-3 col-form-label">Status</label>
	                                        <div class="col-lg-3">
                                            <select name="namatarif" class="form-control select2" value="{{ (isset($namatarif) ? $namatarif->status : '' )}}">
                                                @if(isset($namatarif))
                                                    <option value ={{ $namatarif->status }}> {{ ($namatarif->status=="1")? "Aktif" : "Non Aktif" }}
                                                
                                                @endif
                                                    <option value ="0">Silahkan Pilih</option>
                                                    <option value ="1">Aktif</option>
                                                    <option value ="2">Non Aktif</option>
                                                </select>
	                                            <!-- <input class="form-control" type="text" name="status" placeholder="isi status" value="{{ (isset($namatarif) ? $namatarif->status : '' )}}"> -->
	                                        </div>
	                                    </div>
                                    
                                    </div>

                                    <div class="form-actions pb-0">
                                        <a href="{{ route('admin.namatarif.index') }}">
                                            <button type="button" class="btn btn-raised btn-warning mb-0 mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </button>  
                                        </a>
                                        <button type="submit" class="btn btn-raised btn-primary mb-0">
                                            <i class="fa fa-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('extraJs')
<script src="{{ asset('app-assets/vendors/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/inputmask/jquery.inputmask.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        jQuery('select[name="kategori"]').on('change',function(){
               var kategoriID = jQuery(this).val();
               if(kategoriID)
               {
                  jQuery.ajax({
                     url : '{{route('admin')}}/select/sub1kategori?idkategori=' +kategoriID,
                     type : "GET",
                     dataType : "json",
                     success:function(data)
                     {
                        console.log(data);
                        jQuery('select[name="kodesub1"]').empty();
                        jQuery('select[name="kodesub1"]').append('<option value="">--Kode Sub 1--</option>');
                        jQuery.each(data, function(key,value){
                           $('select[name="kodesub1"]').append('<option value="'+ value.id +'">'+ value.namasub1kategori +'</option>');
                        });
                     }
                  });
               }
                else
                {
                    jQuery('select[name="kodesub1"]').empty();
                    jQuery('select[name="kodesub1"]').append('<option value="">--Kode Sub 1--</option>');
                }
            });

            jQuery('select[name="kodesub1"]').on('change',function(){
               var sub1id = jQuery(this).val();
               if(sub1id)
               {
                  jQuery.ajax({
                     url : '{{route('admin')}}/select/sub2kategori?idsub1=' +sub1id,
                     type : "GET",
                     dataType : "json",
                     success:function(data)
                     {
                        console.log(data);
                        jQuery('select[name="kodesub2"]').empty();
                        jQuery('select[name="kodesub2"]').append('<option value="">--Kode Sub 2--</option>');
                        jQuery.each(data, function(key,value){
                           $('select[name="kodesub2"]').append('<option value="'+ value.id +'">'+ value.namasub2kategori +'</option>');
                        });
                     }
                  });
               }
                else
                {
                    jQuery('select[name="kodesub2"]').empty();
                    jQuery('select[name="kodesub2"]').append('<option value="">--Kode Sub 2--</option>');
                }
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
