@extends('layouts.admin')

@section('titleBar', 'Data Master Dokter dan Non Medis')

@section('extraCss')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
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
								Data Master Dokter dan Non Medis
                            </h4>
                        </div>
                        <div class="card-body">
	                        <a href="javascript:void(0)" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <table id="tabelkodeprogram" class="table table-m table-bordered m-t-20 color-table primary-table">
								<thead>
									<tr>
										<th>No</th>
										<th>NIP</th>
										<th>Nama</th>
										<th>Alamat</th>
										<th>Telpon</th>
										<th>Status</th>
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

@section('lowPage')
<div class="modal fade" id="ajax-modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
		        <h4 class="modal-title" id="judulModal"></h4>
		    </div>
		    <div class="modal-body">
		        <form id="formKodeProgram" name="formKodeProgram" class="form-horizontal">
		        	<input type="hidden" name="kodeprogram_id" id="kodeprogram_id">
		        	<div class="form-group">
		                <label for="kode_program" class="col-sm-4 control-label">NIP</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukan NIP" value="" maxlength="255" required="">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="nama_program" class="col-sm-4 control-label">Nama</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="nama_program" name="nama_program" placeholder="Masukkan Nama Program" value="" maxlength="255" required="">
		                </div>
		            </div>

					<div class="form-group">
		                <label for="alamat" class="col-sm-4 control-label">Alamat</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Nama Program" value="" maxlength="255" required="">
		                </div>
		            </div>

					<div class="form-group">
		                <label for="jenis_kelamin" class="col-sm-4 control-label">jenis_kelamin</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="jenis_kelamin" name="jenis_kelamin" placeholder="Masukkan Nama Program" value="" maxlength="255" required="">
		                </div>
		            </div>

					<div class="form-group">
		                <label for="telpon" class="col-sm-4 control-label">Nomor Telpon</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="telpon" name="telpon" placeholder="Masukkan Nama Program" value="" maxlength="255" required="">
		                </div>
		            </div>
		 
		            <div class="col-sm-offset-2 col-sm-10">
		            	<button type="submit" class="btn btn-primary" id="btn-save" value="create">Simpan </button>
		            	<button type="submit" class="btn btn-primary" id="btn-save" value="create">Simpan </button>
						
		            </div>

					<div class="col-sm-offset-2 col-sm-10">
		            	<button type="submit" class="btn btn-primary" id="btn-save" value="create">Simpan
		            	</button>
		            </div>

		        </form>
		    </div>
		</div>
	</div>
</div>
@endsection

@section('extraJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="{{ asset('app-assets/vendors/js/dt/datatables.min.js') }}" type="text/javascript"></script>
<script>
$(function() {
	$.ajaxSetup({
    	headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	}
	});

    $('#tabelkodeprogram').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.kodeprogram.index') }}",
        dom: 'flrtp',
        columns: [
        	{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        	{ data: 'kode_program', name: 'kode_program' },     
            { data: 'nama_program', name: 'nama_program' },
			{ data: 'nama_program', name: 'nama_program' },
			{ data: 'nama_program', name: 'nama_program' },
			{ data: 'nama_program', name: 'nama_program' },
            { data: 'action', name: 'action', orderable: false, searchable: false}

        ]
    });

    $('#tambah-data').click(function () {
        $('#btn-save').val("simpan-data");
        $('#kodeprogram_id').val('');
        $('#formKodeProgram').trigger("reset");
        $('#judulModal').html("Tambah Kode Program");
        $('#ajax-modal').modal('show');
    });

    $('body').on('click', '.edit-data', function () {
		var kodeprogram_id = $(this).data('id');
		$.get('kodeprogram/' + kodeprogram_id +'/edit', function (data) {
			$('#kode_program-error').hide();
			$('#nama_program-error').hide();
			$('#judulModal').html("Edit Kode Program");
		 	$('#btn-save').val("edit-data");
			$('#ajax-modal').modal('show');
			$('#kodeprogram_id').val(data.id);
			$('#kode_program').val(data.kode_program);
			$('#nama_program').val(data.nama_program);
		});
	});

	$('body').on('click', '#delete-data', function () {
	    var kodeprogram_id = $(this).data('id');
		swal({
        	title: "Apakah anda yakin?",
        	text: "Anda akan menghapus Program Kegiatan",
        	type: 'warning',
	    	showCancelButton: true,
	    	confirmButtonColor: '#0CC27E',
	    	cancelButtonColor: '#FF586B',
    		confirmButtonText: 'Ya, hapus!',
    		cancelButtonText: 'Tidak, batalkan!',
	    	confirmButtonClass: 'btn btn-success btn-raised mr-5',
	    	cancelButtonClass: 'btn btn-danger btn-raised',
	    	buttonsStyling: false
	    }).then(function (isConfirm) {
    		if (isConfirm) {
	           	$.ajax({
		            type: "get",
		            url: "kodeprogram/delete/"+kodeprogram_id,
		            success: function (data) {
		            	swal(
					        'Data Terhapus!',
					        'Program Kegiatan telah dihapus',
					        'success'
					    ).then(function() {
					    	var oTable = $('#tabelkodeprogram').dataTable(); 
			            	oTable.fnDraw(false);
					    });
		            },
		            error: function (data) {
		                console.log('Error:', data);
		            }
		        });
	        }
        }).catch(swal.noop);
	});   


	if ($("#formKodeProgram").length > 0) {
    	$("#formKodeProgram").validate({
    		submitHandler: function(form, event) {
    			event.preventDefault();
				var actionType = $('#btn-save').val();
				$('#btn-save').html('Menyimpan..');
				if (actionType=="simpan-data") {
					$.ajax({
			        	data: $('#formKodeProgram').serialize(),
			        	url: "{{ route('admin.kodeprogram.store') }}",
			        	type: "POST",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKodeProgram').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkodeprogram').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Save Changes');
						}
					});
				} else if (actionType=="edit-data") {
					$.ajax({
			        	data: $('#formKodeProgram').serialize(),
			        	url: "{{ route('admin.kodeprogram.index') }}/"+$('#kodeprogram_id').val(),
			        	type: "PUT",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKodeProgram').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkodeprogram').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Save Changes');
						}
					});
				}
				
		    }
		});
    }
});
</script>
@endsection