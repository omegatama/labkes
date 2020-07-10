@extends('layouts.sekolah')

@section('titleBar', ucfirst($aksi)." RKA")

@section('extraCss')
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
                                {{ ucfirst($aksi)." RKA" }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{ (isset($rka))? route('sekolah.rka.update', ['id'=>$rka->id]) : route('sekolah.rka.store') }}">
                                @csrf
                                @isset ($rka)
                                    @method('PUT')
                                @endisset
                                <div class="form-body">
                                    <div class="row">
                                        @if (isset($rka))
                                        <div class="col-lg-6 col-md-6">
                                        @else
                                        <div class="col-lg-6 col-md-6">
                                        @endif
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="kode_program_id">Program</label>
                                                    <select id='kode_program_id' class="form-control" name="kode_program_id" style="width:100%" required>
                                                    @isset ($rka)
                                                        <option value="{{ $rka->program->id }}" selected>{{ $rka->program->kode_program." - ".$rka->program->nama_program }}</option>        
                                                    @endisset
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="kegiatan_id">Kegiatan</label>
                                                    <select id='kegiatan_id' class="form-control" name="kegiatan_id" style="width:100%" required>
                                                    @isset ($rka)
                                                        <option value="{{ $rka->kegiatan->id }}" selected>{{ $rka->kegiatan->uraian }}</option>        
                                                    @endisset
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="komponen_pembiayaan_id">Komponen Pembiayaan</label>
                                                    <select id='komponen_pembiayaan_id' class="form-control" name="komponen_pembiayaan_id" style="width:100%" required>
                                                    @isset ($rka)
                                                        <option value="{{ $rka->kp->id }}" selected>{{ $rka->kp->kode_komponen." - ".$rka->kp->nama_komponen }}</option>        
                                                    @endisset
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="kode_rekening_id">Kode Rekening</label>
                                                    <select id='kode_rekening_id' class="form-control" name="kode_rekening_id" style="width:100%" required>
                                                    @isset ($rka)
                                                        <option value="{{ $rka->rekening->id }}" selected>{{ $rka->rekening->parent->kode_rekening.".".$rka->rekening->kode_rekening." - ".$rka->rekening->nama_rekening }}</option>        
                                                    @endisset
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="uraian">Uraian</label>
                                                    <input type="text" id="uraian" class="form-control" name="uraian" required placeholder="Masukkan Uraian RKA" value="{{ (isset($rka))? $rka->uraian : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="volume">Volume</label>
                                                    <input type="text" id="volume" class="form-control" name="volume" required placeholder="Masukkan Banyaknya Volume" value="{{ (isset($rka))? $rka->volume : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="satuan">Satuan</label>
                                                    <input type="text" id="satuan" class="form-control" name="satuan" required placeholder="Masukkan Satuan" value="{{ (isset($rka))? $rka->satuan : '' }}">
                                                </div>
                                            </div>

                                        @if (isset($rka))
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="harga_satuan">Harga Satuan</label>
                                                    <input type="text" id="harga_satuan" class="form-control rupiah" name="harga_satuan" required value="{{ (isset($rka))? str_replace(".",",", $rka->harga_satuan) : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="jumlah">Jumlah</label>
                                                    <input type="text" id="jumlah" class="form-control rupiah" name="jumlah" required readonly value="{{ (isset($rka))? str_replace(".",",", $rka->jumlah) : '' }}">
                                                </div>
                                            </div>
                                        @endif
                                        </div>
                                        
                                        @if (isset($rka))
                                        <div class="col-lg-3 col-md-3">
                                        @else
                                        <div class="col-lg-6 col-md-6">
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="harga_satuan">Harga Satuan</label>
                                                    <input type="text" id="harga_satuan" class="form-control rupiah" name="harga_satuan" required value="{{ (isset($rka))? str_replace(".",",", $rka->harga_satuan) : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="jumlah">Jumlah</label>
                                                    <input type="text" id="jumlah" class="form-control rupiah" name="jumlah" required readonly value="{{ (isset($rka))? str_replace(".",",", $rka->jumlah) : '' }}">
                                                </div>
                                            </div>
                                        @endif

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="alokasi_tw1">Alokasi Triwulan 1</label>
                                                    <input type="text" id="alokasi_tw1" class="form-control rupiah" name="alokasi_tw1" required value="{{ (isset($rka))? str_replace(".",",", $rka->alokasi_tw1) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="alokasi_tw2">Alokasi Triwulan 2</label>
                                                    <input type="text" id="alokasi_tw2" class="form-control rupiah" name="alokasi_tw2" required value="{{ (isset($rka))? str_replace(".",",", $rka->alokasi_tw2) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="alokasi_tw3">Alokasi Triwulan 3</label>
                                                    <input type="text" id="alokasi_tw3" class="form-control rupiah" name="alokasi_tw3" required value="{{ (isset($rka))? str_replace(".",",", $rka->alokasi_tw3) : '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="alokasi_tw4">Alokasi Triwulan 4</label>
                                                    <input type="text" id="alokasi_tw4" class="form-control rupiah" name="alokasi_tw4" required value="{{ (isset($rka))? str_replace(".",",", $rka->alokasi_tw4) : '' }}">
                                                </div>
                                            </div>
                                        
                                        </div>

                                        @if (isset($rka))
                                        <div class="col-lg-3 col-md-3">    
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="realisasi_tw1">Realisasi TW1</label>
                                                    <input type="text" style="font-weight: bolder" id="realisasi_tw1" readonly class="form-control rupiah" name="realisasi_tw1" required value="{{ (isset($rka))? str_replace(".",",", $rka->realisasi_tw1) : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="realisasi_tw2">Realisasi TW2</label>
                                                    <input type="text" style="font-weight: bolder" id="realisasi_tw2" readonly class="form-control rupiah" name="realisasi_tw2" required value="{{ (isset($rka))? str_replace(".",",", $rka->realisasi_tw2) : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="realisasi_tw3">Realisasi TW3</label>
                                                    <input type="text" style="font-weight: bolder" id="realisasi_tw3" readonly class="form-control rupiah" name="realisasi_tw3" required value="{{ (isset($rka))? str_replace(".",",", $rka->realisasi_tw3) : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-12 mb-1">
                                                    <label class="m-0" for="realisasi_tw4">Realisasi TW4</label>
                                                    <input type="text" style="font-weight: bolder" id="realisasi_tw4" readonly class="form-control rupiah" name="realisasi_tw4" required value="{{ (isset($rka))? str_replace(".",",", $rka->realisasi_tw4) : '' }}">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                        @endif
                                        
                                    </div>

                                </div>

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('sekolah.rka.index') }}">
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
        $('#kode_program_id').select2({
            ajax: {
                url: "{{ route('sekolah.select.program') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.kode_program+" - "+item.nama_program
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    };
                },
                cache: true,
                delay: 250
            },
            placeholder: 'Pilih Program Kegiatan',
            theme: 'bootstrap4',
        });

        $('#kegiatan_id').select2({
            ajax: {
                url: "{{ route('sekolah.select.kegiatan') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.uraian
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    };
                },
                cache: true,
                delay: 250
            },
            placeholder: 'Pilih Kegiatan',
            theme: 'bootstrap4',
        });

        $('#komponen_pembiayaan_id').select2({
            ajax: {
                url: "{{ route('sekolah.select.kp') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.kode_komponen+" - "+item.nama_komponen
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    };
                },
                cache: true,
                delay: 250
            },
            placeholder: 'Pilih KP',
            theme: 'bootstrap4',
        });

        $('#kode_rekening_id').select2({
            ajax: {
                url: "{{ route('sekolah.select.rekening') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.parent.kode_rekening+"."+item.kode_rekening+" - "+item.nama_rekening
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    };
                },
                cache: true,
                delay: 250
            },
            placeholder: 'Pilih Kode Rekening',
            theme: 'bootstrap4',
        });

        /*if ($('#npsn').val() != null) {
            $('#npsn').trigger('change');
            // alert($('#npsn').val());
        }*/

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

        $("#volume").on('change', checkJumlah);
        $("#harga_satuan, #alokasi_tw1, #alokasi_tw2, #alokasi_tw3, #alokasi_tw4").on('change', checkJumlah);
    });

    function checkJumlah() {
        var volume= $("#volume").val();
        var harga_satuan= parseFloat($("#harga_satuan").inputmask('unmaskedvalue').replace(",","."));
        var jumlah= volume*harga_satuan;
        var alokasi_tw1= parseFloat($("#alokasi_tw1").inputmask('unmaskedvalue').replace(",","."));
        var alokasi_tw2= parseFloat($("#alokasi_tw2").inputmask('unmaskedvalue').replace(",","."));
        var alokasi_tw3= parseFloat($("#alokasi_tw3").inputmask('unmaskedvalue').replace(",","."));
        var alokasi_tw4= parseFloat($("#alokasi_tw4").inputmask('unmaskedvalue').replace(",","."));
        // alert(harga_satuan);
        $("#jumlah").val(jumlah);

        if (jumlah != alokasi_tw1+alokasi_tw2+alokasi_tw3+alokasi_tw4) {
            toastr.warning("Jumlah dan Alokasi tidak sesuai", "Error!", {
                closeButton: 1,
                timeOut: 5000
            });
        }
        else{
            if (jumlah!= 0) {
                toastr.success("Jumlah dan Alokasi telah sesuai", "Benar!", {
                    closeButton: 1,
                    timeOut: 5000
                });
                
            }
        }
    }
</script>
@if($errors->any())
<script>
    toastr.error("{{ $errors->first() }}", "Error!", {
        closeButton: 1,
        timeOut: 0
    });
</script>
@endif

@empty ($rka)
    <script type="text/javascript">
        $(".rupiah").val(0);
    </script>
@endempty

@isset ($rka)
    <script>
        // $('#kode_program_id').attr("readonly", "readonly");
        // $('#kegiatan_id').attr("readonly", "readonly");
        // $('#komponen_pembiayaan_id').attr("readonly", "readonly");
        // $('#kode_rekening_id').attr("readonly", "readonly");
    </script>
@endisset
@endsection
