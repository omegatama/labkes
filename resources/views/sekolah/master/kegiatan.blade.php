@extends('layouts.sekolah')

@section('titleBar', 'Master Data Kegiatan')

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
                                Master Data Kegiatan
                            </h4>
                        </div>
                        <div class="card-body">
	                        <a href="javascript:void(0)" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <table id="tabelkegiatan" class="table table-bordered">
								<thead>
									<tr>
										<th>No</th>
										<th>Nama Kegiatan</th>
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
		        <form id="formKegiatan" name="formKegiatan" class="form-horizontal">
		        	<input type="hidden" name="kegiatan_id" id="kegiatan_id">
		            <div class="form-group">
		                <label for="name" class="col-sm-4 control-label">Nama Kegiatan</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="uraian" name="uraian" placeholder="Masukkan Nama" value="" maxlength="50" required="">
		                </div>
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

    $('#tabelkegiatan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('sekolah.kegiatan.index') }}",
        dom: 'flrtp',
        columns: [
        	{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},      
            { data: 'uraian', name: 'uraian' },
            { data: 'action', name: 'action', orderable: false, searchable: false}

        ]
    });

    $('#tambah-data').click(function () {
        $('#btn-save').val("simpan-data");
        $('#kegiatan_id').val('');
        $('#formKegiatan').trigger("reset");
        $('#judulModal').html("Tambah Kegiatan");
        $('#ajax-modal').modal('show');
    });

    $('body').on('click', '.edit-data', function () {
		var kegiatan_id = $(this).data('id');
		$.get('kegiatan/' + kegiatan_id +'/edit', function (data) {
			$('#uraian-error').hide();
			$('#judulModal').html("Edit Kegiatan");
		 	$('#btn-save').val("edit-data");
			$('#ajax-modal').modal('show');
			$('#kegiatan_id').val(data.id);
			$('#uraian').val(data.uraian);
		});
	});

	$('body').on('click', '#delete-data', function () {
	    var kegiatan_id = $(this).data("id");
	    swal({
        	title: "Apakah anda yakin?",
        	text: "Anda akan menghapus data kegiatan",
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
		            url: "kegiatan/delete/"+kegiatan_id,
		            success: function (data) {
		            	swal(
					        'Data Terhapus!',
					        'Data Kegiatan telah dihapus',
					        'success'
					    ).then(function() {
					    	var oTable = $('#tabelkegiatan').dataTable(); 
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


	if ($("#formKegiatan").length > 0) {
    	$("#formKegiatan").validate({
    		submitHandler: function(form, event) {
    			event.preventDefault();
				var actionType = $('#btn-save').val();
				$('#btn-save').html('Menyimpan..');
				if (actionType=="simpan-data") {
					$.ajax({
			        	data: $('#formKegiatan').serialize(),
			        	url: "{{ route('sekolah.kegiatan.store') }}",
			        	type: "POST",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKegiatan').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkegiatan').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Save Changes');
						}
					});
				} else if (actionType=="edit-data") {
					$.ajax({
			        	data: $('#formKegiatan').serialize(),
			        	url: "{{ route('sekolah.kegiatan.index') }}/"+$('#kegiatan_id').val(),
			        	type: "PUT",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKegiatan').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkegiatan').dataTable();
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