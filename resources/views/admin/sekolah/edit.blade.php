@extends('layouts.admin')

@section('titleBar', "Edit Sekolah")

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
                <div class="col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Edit Sekolah
                            </h4>
                        </div>
                        <div class="card-body">
                            <form class="form" method="POST" action="{{route('admin.sekolah.update',['id'=>$user->id])}}">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-body">

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="name">Nama Sekolah</label>
                                            <input type="text" id="name" class="form-control" name="name" required placeholder="Masukkan Nama" value="{{ (isset($user))? $user->name : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="email">Email</label>
                                            <input type="email" id="email" class="form-control" name="email" required placeholder="Masukkan Email" value="{{ (isset($user))? $user->email : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="telepon">Telepon</label>
                                            <input type="text" id="telepon" class="form-control" name="telepon" required placeholder="Masukkan Nomor Telepon" value="{{ (isset($user))? $user->telepon : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="kecamatan_id">Kecamatan</label>
                                            <select class="form-control" name="kecamatan_id" id="kecamatan_id" required>
                                                <option value=""></option>
                                            </select>
                                            
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="desa">Desa</label>
                                            <input type="text" id="desa" class="form-control" name="desa" required placeholder="Masukkan Desa" value="{{ (isset($user))? $user->desa : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="alamat">Alamat</label>
                                            <input type="text" id="alamat" class="form-control" name="alamat" required placeholder="Masukkan Alamat" value="{{ (isset($user))? $user->alamat : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="nama_kepsek">Nama Kepala Sekolah</label>
                                            <input type="text" id="nama_kepsek" class="form-control" name="nama_kepsek" required placeholder="Masukkan Nama Kepala Sekolah" value="{{ (isset($user))? $user->nama_kepsek : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="nip_kepsek">NIP Kepala Sekolah</label>
                                            <input type="text" id="nip_kepsek" class="form-control" name="nip_kepsek" required placeholder="Masukkan NIP Kepala Sekolah" value="{{ (isset($user))? $user->nip_kepsek : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="nama_bendahara">Nama Bendahara</label>
                                            <input type="text" id="nama_bendahara" class="form-control" name="nama_bendahara" required placeholder="Masukkan Nama Bendahara" value="{{ (isset($user))? $user->nama_bendahara : '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-12 mb-1">
                                            <label class="m-0" for="nip_bendahara">NIP Bendahara</label>
                                            <input type="text" id="nip_bendahara" class="form-control" name="nip_bendahara" required placeholder="Masukkan NIP Bendahara" value="{{ (isset($user))? $user->nip_bendahara : '' }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="form-actions pb-0 text-right">
                                    <a href="{{ route('admin.sekolah.index') }}">
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
        $('#kecamatan_id').select2({
            ajax: {
                url: "{{ route('admin.select.kecamatan') }}",
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
                                text: item.nama_kecamatan
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
            placeholder: 'Pilih Kecamatan',
            theme: 'bootstrap4',
        });

        /* jshint ignore:start */
        @isset ($user)
            var option = new Option('{{$user->kecamatan->nama_kecamatan}}' , '{{$user->kecamatan_id}}', true, true);
            $('#kecamatan_id').append(option).trigger('change');
        @endisset

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
