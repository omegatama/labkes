@extends('layouts.admin')

@section('titleBar', 'Master Komponen Pembiayaan')

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
                                Master Komponen Pembiayaan
                            </h4>
                        </div>
                        <div class="card-body">
	                        <a href="javascript:void(0)" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <table id="tabelkomponenpembiayaan" class="table table-bordered">
								<thead>
									<tr>
										<th>No</th>
										<th>Kode Komponen</th>
										<th>Nama Komponen</th>
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
		        <form id="formKomponenPembiayaan" name="formKomponenPembiayaan" class="form-horizontal">
		        	<input type="hidden" name="komponenpembiayaan_id" id="komponenpembiayaan_id">
		        	<div class="form-group">
		                <label for="kode_komponen" class="col-sm-4 control-label">Kode Komponen</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="kode_komponen" name="kode_komponen" placeholder="Masukkan Kode Komponen" value="" maxlength="255" required="">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="nama_komponen" class="col-sm-4 control-label">Nama Komponen</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="nama_komponen" name="nama_komponen" placeholder="Masukkan Nama Komponen" value="" maxlength="255" required="">
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

    $('#tabelkomponenpembiayaan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.komponenpembiayaan.index') }}",
        dom: 'flrtp',
        columns: [
        	{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        	{ data: 'kode_komponen', name: 'kode_komponen' },     
            { data: 'nama_komponen', name: 'nama_komponen' },
            { data: 'action', name: 'action', orderable: false, searchable: false}

        ]
    });

    $('#tambah-data').click(function () {
        $('#btn-save').val("simpan-data");
        $('#komponenpembiayaan_id').val('');
        $('#formKomponenPembiayaan').trigger("reset");
        $('#judulModal').html("Tambah Komponen Pembiayaan");
        $('#ajax-modal').modal('show');
    });

    $('body').on('click', '.edit-data', function () {
		var komponenpembiayaan_id = $(this).data('id');
		$.get('komponenpembiayaan/' + komponenpembiayaan_id +'/edit', function (data) {
			$('#kode_komponen-error').hide();
			$('#nama_komponen-error').hide();
			$('#judulModal').html("Edit Komponen Pembiayaan");
		 	$('#btn-save').val("edit-data");
			$('#ajax-modal').modal('show');
			$('#komponenpembiayaan_id').val(data.id);
			$('#kode_komponen').val(data.kode_komponen);
			$('#nama_komponen').val(data.nama_komponen);
		});
	});

	$('body').on('click', '#delete-data', function () {
	    var komponenpembiayaan_id = $(this).data('id');
		swal({
        	title: "Apakah anda yakin?",
        	text: "Anda akan menghapus Komponen Pembiayaan",
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
		            url: "komponenpembiayaan/delete/"+komponenpembiayaan_id,
		            success: function (data) {
		            	swal(
					        'Data Terhapus!',
					        'Komponen Pembiayaan telah dihapus',
					        'success'
					    ).then(function() {
					    	var oTable = $('#tabelkomponenpembiayaan').dataTable(); 
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


	if ($("#formKomponenPembiayaan").length > 0) {
    	$("#formKomponenPembiayaan").validate({
    		submitHandler: function(form, event) {
    			event.preventDefault();
				var actionType = $('#btn-save').val();
				$('#btn-save').html('Menyimpan..');
				if (actionType=="simpan-data") {
					$.ajax({
			        	data: $('#formKomponenPembiayaan').serialize(),
			        	url: "{{ route('admin.komponenpembiayaan.store') }}",
			        	type: "POST",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKomponenPembiayaan').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkomponenpembiayaan').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Save Changes');
						}
					});
				} else if (actionType=="edit-data") {
					$.ajax({
			        	data: $('#formKomponenPembiayaan').serialize(),
			        	url: "{{ route('admin.komponenpembiayaan.index') }}/"+$('#komponenpembiayaan_id').val(),
			        	type: "PUT",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKomponenPembiayaan').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkomponenpembiayaan').dataTable();
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