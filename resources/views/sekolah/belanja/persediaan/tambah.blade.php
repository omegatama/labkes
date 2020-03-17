@extends('layouts.sekolah')

@section('titleBar', ucfirst($aksi)." Belanja Modal")

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/select2/select2-bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
<style>
select[readonly].select2-hidden-accessible + .select2-container {
  pointer-events: none;
  touch-action: none;
}

select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
  background: #ECEFF1;
  border: 1px solid #A6A9AE;
  box-shadow: none;
}

select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
  display: none;
}
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <section id="full-layout">
            <div class="row justify-content-md-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)." Belanja Persediaan" }}
                                <span class="badge badge-info">
                                    {{ $nama }} 
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="
                            {{
                                (isset($belanjapersediaan))? route('sekolah.belanja.updatepersediaan', ['id'=>$id, 'persediaan_id' =>$belanjapersediaan->id]) : route('sekolah.belanja.storepersediaan',['id'=>$id])
                            }}
                            ">
                                @csrf
                                
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="barang_persediaan_id">Barang Persediaan</label>
                                                    <div class="input-group m-0">
                                                        <input type="text" id="nama_persediaan" class="form-control" name="nama_persediaan" required placeholder="Pilih Barang Persediaan" readonly>
                                                        <input type="text" id="barang_persediaan_id" class=" d-none" name="barang_persediaan_id" required>
                                                        <div class="input-group-append" id="btn-cari">
                                                            <button class="btn btn-outline-info" type="button" onclick="cariPersediaan()">Cari</button>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="satuan">Satuan</label>
                                                    <input type="text" id="satuan" class="form-control" name="satuan" required value="{{ (isset($belanjamodal))? $belanjamodal->satuan : '' }}" placeholder="Masukkan Satuan">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="harga_satuan">Harga Satuan</label>
                                                    <input type="text" id="harga_satuan" class="form-control rupiah" name="harga_satuan" required value="{{ (isset($belanjamodal))? str_replace(".",",", $belanjamodal->harga_satuan) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="qty">Qty</label>
                                                    <input type="text" id="qty" class="form-control" name="qty" required value="{{ (isset($belanjamodal))? $belanjamodal->qty : '' }}" placeholder="Masukkan Qty">
                                                </div>
                                            </div>

                                            

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="total">Total</label>
                                                    <input type="text" id="total" class="form-control rupiah" name="total" required value="{{ (isset($belanjamodal))? str_replace(".",",", $belanjamodal->total) : '' }}">
                                                </div>
                                            </div>

                                        </div>

                                        <!-- <div class="col-lg-6"> 
                                        </div> -->
                                    </div>
                                    
                                </div>

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('sekolah.belanja.persediaan',['id'=> $id]) }}">
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

@section('lowPage')
    @include('sekolah.modal.barangpersediaan')
@endsection

@section('extraJs')
<script src="{{ asset('app-assets/vendors/js/dt/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/inputmask/jquery.inputmask.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    function cariPersediaan() {
        $('#modalpersediaan').modal('show');
        $('#modalpersediaan').on('click',('#pilihPersediaan'), function(){
            $('#barang_persediaan_id').val($(this).attr('data-barang_persediaan_id'));
            $('#nama_persediaan').val( $(this).attr('data-nama_persediaan'));
            $('#satuan').val( $(this).attr('data-satuan'));
            $('#harga_satuan').val( $(this).attr('data-harga_satuan'));
            $('#modalpersediaan').modal('hide');
        });
    }

    $(document).ready(function () {
        
        $('#satuan').attr('readonly','readonly');
        $('#harga_satuan').attr('readonly','readonly');
        $('#total').attr('readonly','readonly');

        Inputmask.extendAliases({
            rupiah: {
                prefix: "Rp ",
                alias: "numeric",
                radixPoint: ',',
                groupSeparator: '.',
                autoGroup: true,
                digits: 2,
                digitsOptional: !1,
                clearMaskOnLostFocus: !1,
                removeMaskOnSubmit:true,
            }
        });

        $(".rupiah").inputmask({ alias : "rupiah" });

        $("#qty").on('change',function () {
            var qty= $(this).val();
            var hargasatuan= $("#harga_satuan").inputmask('unmaskedvalue');
            hargasatuan= parseFloat(hargasatuan.replace(",", "."));
            var total= qty * hargasatuan;
            $("#total").val(total);
            // alert(total); 
        });
        
        /* jshint ignore:start */
        @if($errors->any())
            toastr.error("{{ $errors->first() }}", "Error!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif

        @if (isset($belanjapersediaan))
            {{-- // var option = new Option('{{$belanjamodal->kode_barang->nama_barang}}' , '{{$belanjamodal->kode_barang_id}}', true, true); --}}
            // $('#kode_barang_id').append(option).trigger('change');
            // $('#kode_barang_id').attr('readonly','readonly');
            // $("#tanggal_bukti").attr('readonly','readonly');
        @else
            $(".rupiah").val(0);
        @endif
        
        /* jshint ignore:end */
    });

</script>
@endsection
