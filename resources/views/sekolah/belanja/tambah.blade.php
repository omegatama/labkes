@extends('layouts.sekolah')

@section('titleBar', ucfirst($aksi)." Belanja")

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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ ucfirst($aksi)." Belanja" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ (isset($belanja))? route('sekolah.belanja.update', ['id'=>$belanja->id]) : route('sekolah.belanja.store') }}">
                                @csrf
                                
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="nomor">Nomor Bukti</label>
                                                    <input type="text" id="nomor" class="form-control" name="nomor" required value="{{ (isset($belanja))? $belanja->nomor : '' }}" placeholder="Masukkan Nomor Bukti" autofocus>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="rka_id">Kode Rekening</label>
                                                    <div class="input-group m-0">
                                                        <input type="text" id="nama_rka" class="form-control" name="nama_rka" required placeholder="Pilih RKA" readonly>
                                                        <input type="text" id="rka_id" class=" d-none" name="rka_id" required>
                                                        <div class="input-group-append" id="btn-cari">
                                                            <button class="btn btn-outline-info" type="button" onclick="cariRekening()">Cari</button>
                                                        </div>
                                                    </div>
                                                    <!-- <select id='rba_id' class="form-control" name="rba_id" style="width:100%" required>
                                                        <option></option>
                                                    </select> -->
                                                    
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="nama">Uraian</label>
                                                    <input type="text" id="nama" class="form-control" name="nama" required value="{{ (isset($belanja))? $belanja->nama : '' }}" placeholder="Uraian Belanja">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="penerima">Penerima</label>
                                                    <input type="text" id="penerima" class="form-control" name="penerima" required value="{{ (isset($belanja))? $belanja->penerima : '' }}" placeholder="Di bayarkan kepada">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="tanggal">Tanggal</label>
                                                    <input type="date" id="tanggal" class="form-control" name="tanggal" required value="{{ (isset($belanja))? $belanja->tanggal->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-lg-6">
                                                                                        
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="kas">Kas</label>
                                                    <select id='kas' class="form-control" name="kas" style="width:100%" required>
                                                        <option></option>
                                                        <option value="B">Kas Bank</option>
                                                        <option value="T">Kas Tunai</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="nominal">Nominal</label>
                                                    <input type="text" id="nominal" class="form-control rupiah" name="nominal" required value="{{ (isset($belanja))? str_replace(".",",", $belanja->nilai) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="ppn">PPn</label>
                                                    <input type="text" id="ppn" class="form-control rupiah" name="ppn" required value="{{ (isset($belanja))? str_replace(".",",", $belanja->ppn) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="pph21">PPh 21</label>
                                                    <input type="text" id="pph21" class="form-control rupiah" name="pph21" required value="{{ (isset($belanja))? str_replace(".",",", $belanja->pph21) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="pph23">PPh 23</label>
                                                    <input type="text" id="pph23" class="form-control rupiah" name="pph23" required value="{{ (isset($belanja))? str_replace(".",",", $belanja->pph23) : '' }}">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('sekolah.belanja.index') }}">
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
    @include('sekolah.modal.rekening')
@endsection

@section('extraJs')
<script src="{{ asset('app-assets/vendors/js/dt/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/inputmask/jquery.inputmask.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    function cariRekening() {
        $('#modalrekening').modal('show');
        $('#modalrekening').on('click',('#pilihRekening'), function(){
            $('#nama_rka').val( $(this).attr('data-kegiatan')+" - "+$(this).attr('data-rka') );
            $('#rka_id').val($(this).attr('data-id_rka'));
            $('#modalrekening').modal('hide');
        });
    }

    $(document).ready(function () {
        var table = $('#tabelRekening').DataTable({
            order: [],
            initComplete: function () {
                this.api().columns('.cari').every(function () {
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


        $('#kas').select2({
            placeholder: 'Pilih Sumber Kas',
            theme: 'bootstrap4',
        });

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
        
        /* jshint ignore:start */
        @if($errors->any())
            toastr.error("{{ $errors->first() }}", "Error!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif

        @if (isset($belanja))
            $('#rka_id').val('{{$belanja->rka_id}}');
            $('#nama_rka').val('{{ $belanja->rka->kegiatan->uraian ." - ". $belanja->rka->rekening->nama_rekening }}');
            $('#btn-cari').remove();
            $('#kas').val('{{$belanja->transaksi->kas}}');
            $('#kas').trigger('change');
            $('#kas').attr('readonly','readonly');
            $('#tanggal').attr('readonly','readonly');
            
        @else
            $(".rupiah").val(0);
        @endif
        
        /* jshint ignore:end */
    });

</script>
@endsection
