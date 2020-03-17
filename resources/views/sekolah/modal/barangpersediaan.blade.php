<div class="modal fade" id="modalpersediaan" tabindex="-1" role="dialog" aria-labelledby="modalpersediaan" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90%; width: 650px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Barang Persediaan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body table-responsive">
            	<table class="table table-bordered nowrap" id="tabelPersediaan">
            		<thead>
            			<tr>
            				<th class="cari">Nama Barang</th>
                            <th>Satuan</th>
            				<th class="cari">Harga Barang</th>

            			</tr>
            			
            		</thead>
            		<tbody>
                        @foreach ($persediaans as $key => $persediaan)
                            <tr id="pilihPersediaan" 
                            data-barang_persediaan_id="{{$persediaan->id}}" 
                            data-nama_persediaan="{{$persediaan->nama_persediaan}}" 
                            data-harga_satuan="{{$persediaan->harga_satuan}}"
                            data-satuan="{{$persediaan->satuan}}"
                            >
                                <td>
                                    {{$persediaan->nama_persediaan}}
                                </td>
                                <td>
                                    {{$persediaan->satuan}}
                                </td>
                                <td>
                                    {{FormatMataUang($persediaan->harga_satuan)}}
                                </td>
                                    
                            </tr>
                        @endforeach          
                        </tbody>
            		<tfoot>
            		    <tr>
              				<th>Nama Barang</th>
                            <th>Satuan</th>
              				<th>Harga Barang</th>

            		    </tr>
            			
            		</tfoot>
            	</table>
            </div>
        </div>
    </div>
</div>