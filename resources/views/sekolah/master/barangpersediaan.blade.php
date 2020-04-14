@extends('layouts.sekolah')

@section('titleBar', 'Master Barang Persediaan')

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
                                Master Barang Persediaan
                            </h4>
                        </div>
                        <div class="card-body">
	                        <a href="javascript:void(0)" class="btn btn-info btn-sm m-0" id="tambah-data">Tambah</a>
                            <table id="tabelbarangpersediaan" class="table table-bordered">
								<thead>
									<tr>
										<th>No</th>
										<th>Nama Persediaan</th>
										<th>Satuan</th>
										<th>Harga</th>
										{{-- <th>Stok</th> --}}
										<th>Pilihan</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>No</th>
										<th>Nama Persediaan</th>
										<th>Satuan</th>
										<th>Harga</th>
										{{-- <th>Stok</th> --}}
										<th>Pilihan</th>
									</tr>
								</tfoot>
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
		        <form id="formBarangpersediaan" name="formBarangpersediaan" class="form-horizontal">
		        	<input type="hidden" name="barangpersediaan_id" id="barangpersediaan_id">
		            <div class="form-group">
		                <label for="nama_persediaan" class="col-sm-12 control-label">Nama Persediaan</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="nama_persediaan" name="nama_persediaan" placeholder="Masukkan Nama" value="" maxlength="50" required="">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="satuan" class="col-sm-12 control-label">Satuan</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="satuan" name="satuan" placeholder="Masukkan Satuan" value="" maxlength="50" required="">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="harga_satuan" class="col-sm-12 control-label">Harga Satuan</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="harga_satuan" name="harga_satuan" placeholder="Masukkan Harga Satuan" value="" maxlength="50" required="">
		                </div>
		            </div>

		            {{-- <div class="form-group">
		                <label for="stok" class="col-sm-12 control-label">Stok</label>
		                <div class="col-sm-12">
		                    <input type="text" class="form-control" id="stok" name="stok" placeholder="Masukkan Stok" value="" maxlength="50" required="">
		                </div>
		            </div> --}}
		 
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

    $('#tabelbarangpersediaan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('sekolah.barangpersediaan.index') }}",
        dom: 'flrtp',
        columns: [
        	{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},      
            { data: 'nama_persediaan', name: 'nama_persediaan' },
            { data: 'satuan', name: 'satuan' },
            { data: 'harga_satuan', name: 'harga_satuan' },
            // { data: 'stok', name: 'stok' },
            { data: 'action', name: 'action', orderable: false, searchable: false}

        ],
        initComplete: function () {
            this.api().columns(':gt(0):lt(3)').every(function () {
                var column = this;
                var input = document.createElement('input');
                $(input).addClass('form-control');
                $(column.footer()).addClass('p-1');
                $(input).appendTo($(column.footer()).empty())
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column.search(val ? val : '', true, false).draw();
                });
            });
        }
    });

    $('#tambah-data').click(function () {
        $('#btn-save').val("simpan-data");
        $('#barangpersediaan_id').val('');
        $('#formBarangpersediaan').trigger("reset");
        $('#judulModal').html("Tambah Barang Persediaan");
        $('#ajax-modal').modal('show');
    });

    $('body').on('click', '.edit-data', function () {
		var barangpersediaan_id = $(this).data('id');
		$.get('barangpersediaan/' + barangpersediaan_id +'/edit', function (data) {
			$('#nama_persediaan-error').hide();
			$('#satuan-error').hide();
			$('#harga_satuan-error').hide();
			// $('#stok-error').hide();
			$('#judulModal').html("Edit Persediaan");
		 	$('#btn-save').val("edit-data");
			$('#ajax-modal').modal('show');
			$('#barangpersediaan_id').val(data.id);
			$('#nama_persediaan').val(data.nama_persediaan);
			$('#satuan').val(data.satuan);
			$('#harga_satuan').val(data.harga_satuan);
			// $('#stok').val(data.stok);
		});
	});

	$('body').on('click', '#delete-data', function () {
	    var barangpersediaan_id = $(this).data("id");
	    swal({
        	title: "Apakah anda yakin?",
        	text: "Anda akan menghapus Barang Persediaan",
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
		            url: "barangpersediaan/delete/"+barangpersediaan_id,
		            success: function (data) {
		            	console.log(data);
		            	if(data){
			            	swal(
						        'Data Terhapus!',
						        'Barang Persediaan telah dihapus',
						        'success'
						    ).then(function() {
						    	var oTable = $('#tabelbarangpersediaan').dataTable(); 
				            	oTable.fnDraw(false);
						    });
		            	}
		            	else{
		            		swal(
						        'Error!',
						        'Barang Persediaan Telah digunakan\nBarang Persediaan Tidak dapat dihapus!',
						        'error'
						    ).then(function() {
						    	var oTable = $('#tabelbarangpersediaan').dataTable(); 
				            	oTable.fnDraw(false);
						    });
		            	}
		            	
		            },
		            error: function (data) {
		                console.log('Error:', data);
		            }
		        });
	        }
        }).catch(swal.noop);
	});   


	if ($("#formBarangpersediaan").length > 0) {
    	$("#formBarangpersediaan").validate({
    		submitHandler: function(form, event) {
    			event.preventDefault();
				var actionType = $('#btn-save').val();
				$('#btn-save').html('Menyimpan..');
				if (actionType=="simpan-data") {
					$.ajax({
			        	data: $('#formBarangpersediaan').serialize(),
			        	url: "{{ route('sekolah.barangpersediaan.store') }}",
			        	type: "POST",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formBarangpersediaan').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelbarangpersediaan').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Simpan');
						}
					});
				} else if (actionType=="edit-data") {
					$.ajax({
			        	data: $('#formBarangpersediaan').serialize(),
			        	url: "{{ route('sekolah.barangpersediaan.index') }}/"+$('#barangpersediaan_id').val(),
			        	type: "PUT",
			        	dataType: 'json',
			        	success: function (data) {
	            			$('#formBarangpersediaan').trigger("reset");
	            			$('#ajax-modal').modal('hide');
	            			$('#btn-save').html('Simpan');
	            			var oTable = $('#tabelbarangpersediaan').dataTable();
	            			oTable.fnDraw(false);
	            		},
	        			error: function (data) {
	            			console.log('Error:', data);
	            			$('#btn-save').html('Simpan');
						}
					});
				}
				
		    }
		});
    }
});
</script>
@endsection