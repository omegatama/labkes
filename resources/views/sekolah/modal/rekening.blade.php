<div class="modal fade" id="modalrekening" tabindex="-1" role="dialog" aria-labelledby="modalrekening" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kode Rekening</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body table-responsive">
            	<table class="table table-bordered nowrap" id="tabelRekening">
            		<thead>
            			<tr>
            				<th class="cari">Kegiatan</th>
            				<th class="cari">Uraian</th>
            				<th>Jumlah</th>
            				<th>Sisa TW1</th>
            				<th>Sisa TW2</th>
            				<th>Sisa TW3</th>
            				<th>Sisa TW4</th>
            				<th class="cari">Rekening</th>
            				<th class="cari">Program</th>
            				<th class="cari">KP</th>

            			</tr>
            			
            		</thead>
            		<tbody>
            			<?php
            			foreach ($rka as $key => $item) {
            				?>
            			
            			<tr 
            			style="cursor: pointer;" 
            			id="pilihRekening" 
            			data-id_rka="{{$item->id}}" 
            			data-kegiatan="{{$item->kegiatan->uraian}}"
            			data-rka="{{$item->uraian}}"
            			>
            				<td>
            					{{
            						$item->kegiatan->uraian
            					}}
            				</td>

            				<td>
            					{{
            						$item->uraian
            					}}
            				</td>

            				<td>
            					{{
            						FormatMataUang($item->jumlah)
            					}}
            				</td>
            				<td>
            					{{
            						FormatMataUang($item->alokasi_tw1 - $item->realisasi_tw1)
            					}}
            				</td>
            				<td>
            					{{
            						FormatMataUang($item->alokasi_tw2 - $item->realisasi_tw2)
            					}}
            				</td>
            				<td>
            					{{
            						FormatMataUang($item->alokasi_tw3 - $item->realisasi_tw3)
            					}}
            				</td>
            				<td>
            					{{
            						FormatMataUang($item->alokasi_tw4 - $item->realisasi_tw4)
            					}}
            				</td>
            				
            				<td>
            					{{
            						$item->rekening->parent->kode_rekening.".".
            						$item->rekening->kode_rekening." - ".
            						$item->rekening->nama_rekening
                				}}
            				</td>
            				<td>
            					{{
            						$item->program->kode_program." - ".
            						$item->program->nama_program
            					}}
            				</td>
            				<td>
            					{{
            						$item->kp->kode_komponen." - ".
            						$item->kp->nama_komponen
            					}}
            				</td>

            			</tr>
            				<?php
            			}
            			?>
            		</tbody>
            		<tfoot>
            			<tr>
            				<th>Kegiatan</th>
            				<th>Uraian</th>
            				<th>Jumlah</th>
            				<th>Sisa TW1</th>
            				<th>Sisa TW2</th>
            				<th>Sisa TW3</th>
            				<th>Sisa TW4</th>
            				<th>Rekening</th>
            				<th>Program</th>
            				<th>KP</th>

            			</tr>
            			
            		</tfoot>
            	</table>
            </div>
        </div>
    </div>
</div>