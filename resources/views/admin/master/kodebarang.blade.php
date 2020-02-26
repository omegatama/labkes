@extends('layouts.admin')

@section('titleBar', 'Master Data Barang')

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
                                Master Data Barang
                            </h4>
                        </div>
                        <div class="card-body">
	                        <a href="javascript:void(0)" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <table id="tabelkodebarang" class="table table-bordered">
								<thead>
									<tr>
										<th>No</th>
										<th>Kode Barang</th>
										<th>Nama Barang</th>
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
		        <form id="formKodeBarang" name="formKodeBarang" class="form-horizontal">
		        	<input type="hidden" name="kodebarang_id" id="kodebarang_id">
		        	<div class="form-group">
		                <label for="kode_barang" class="col-sm-4 control-label">Kode Barang</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" placeholder="Masukkan Kode Barang" value="" maxlength="255" required="">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="nama_barang" class="col-sm-4 control-label">Nama Barang</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="Masukkan Nama Barang" value="" maxlength="255" required="">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="parent_id" class="col-sm-4 control-label">Parent ID</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="parent_id" name="parent_id" placeholder="Masukkan Parent ID [ 3 ; 4 ; 5 ]" value="" maxlength="255" required="">
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

    $('#tabelkodebarang').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.kodebarang.index') }}",
        dom: 'flrtp',
        columns: [
        	{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        	{ data: 'kode_barang', name: 'kode_barang' },     
            { data: 'nama_barang', name: 'nama_barang' },
            { data: 'action', name: 'action', orderable: false, searchable: false}

        ]
    });

    $('#tambah-data').click(function () {
        $('#btn-save').val("simpan-data");
        $('#kodebarang_id').val('');
        $('#formKodeBarang').trigger("reset");
        $('#judulModal').html("Tambah Kode Barang");
        $('#ajax-modal').modal('show');
    });

    $('body').on('click', '.edit-data', function () {
		var kodebarang_id = $(this).data('id');
		$.get('kodebarang/' + kodebarang_id +'/edit', function (data) {
			$('#kode_barang-error').hide();
			$('#nama_barang-error').hide();
			$('#parent_id-error').hide();
			$('#judulModal').html("Edit Kode Barang");
		 	$('#btn-save').val("edit-data");
			$('#ajax-modal').modal('show');
			$('#kodebarang_id').val(data.id);
			$('#kode_barang').val(data.kode_barang);
			$('#nama_barang').val(data.nama_barang);
			$('#parent_id').val(data.parent_id);
		});
	});

	$('body').on('click', '#delete-data', function () {
	    var kodebarang_id = $(this).data('id');
		swal({
        	title: "Apakah anda yakin?",
        	text: "Anda akan menghapus Data Barang",
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
		            url: "kodebarang/delete/"+kodebarang_id,
		            success: function (data) {
		            	swal(
					        'Data Terhapus!',
					        'Data Barang telah dihapus',
					        'success'
					    ).then(function() {
					    	var oTable = $('#tabelkodebarang').dataTable(); 
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


	if ($("#formKodeBarang").length > 0) {
    	$("#formKodeBarang").validate({
    		submitHandler: function(form, event) {
    			event.preventDefault();
				var actionType = $('#btn-save').val();
				$('#btn-save').html('Menyimpan..');
				if (actionType=="simpan-data") {
					$.ajax({
			        	data: $('#formKodeBarang').serialize(),
			        	url: "{{ route('admin.kodebarang.store') }}",
			        	type: "POST",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKodeBarang').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkodebarang').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Save Changes');
						}
					});
				} else if (actionType=="edit-data") {
					$.ajax({
			        	data: $('#formKodeBarang').serialize(),
			        	url: "{{ route('admin.kodebarang.index') }}/"+$('#kodebarang_id').val(),
			        	type: "PUT",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formKodeBarang').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelkodebarang').dataTable();
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