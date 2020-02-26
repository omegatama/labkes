@foreach($subrekenings as $subrekening)
        <tr data-id="{{$subrekening->id}}" data-parent="{{$dataParent}}" data-nama_rekening="{{$subrekening->nama_rekening}}" data-kode_rekening="{{ $kodeParent.".".$subrekening->kode_rekening}}" data-level = "{{$dataLevel + 1}}">
            <td data-column="name">
            	{{ $kodeParent.".".$subrekening->kode_rekening}}
            </td>
            <td>
            	{{$subrekening->nama_rekening}}
            </td>
            <td>
            	Edit
            	@if(empty(count($subrekening->subrekening)))
            		Delete
            	@endif
            </td>
        </tr>
	    @if(count($subrekening->subrekening))
            @include('admin.master.subRekeningView',['subrekenings' => $subrekening->subrekening, 'dataParent' => $subrekening->id, 'dataLevel' => $dataLevel])
        @endif
@endforeach