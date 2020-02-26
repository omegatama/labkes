@foreach($subrekenings as $subrekening)
        <tr data-id="{{$subrekening->id}}" data-parent="{{$dataParent}}" data-nama_rekening="{{$subrekening->nama_rekening}}" data-kode_rekening="{{ $kodeParent.".".$subrekening->kode_rekening}}" data-level = "{{$dataLevel + 1}}">
            <td data-column="name">
            	{{ $kodeParent.".".$subrekening->kode_rekening}}
            </td>
            <td colspan="1">
            	{{$subrekening->nama_rekening}}
            </td>
            <td nowrap>
                @php
                    $limitparent= $limitRka->where('rekening.id',$subrekening->id);
                    echo FormatMataUang($limitparent->sum('limit'));
                @endphp
            </td>
            <td nowrap>
                @php
                    echo FormatMataUang($limitparent->sum('sisa'));
                @endphp
            </td>
            
        </tr>
        @for ($i = 0; $i < 4; $i++)
            {{-- expr --}}
        <tr data-id="{{$subrekening->id}}detail" data-parent="{{$subrekening->id}}" data-level = "{{$dataLevel + 2}}">
            <td data-column="name">{{ $i+1 }}</td>
            <td>Triwulan {{ $i+1 }}</td>
            <td nowrap>
                @php
                    $limit= $limitRka->where('kode_rekening_id',$subrekening->id)->where('triwulan',$i+1)->first();
                    if(!empty($limit)){
                        echo FormatMataUang($limit->limit);
                    }
                    else{
                        echo FormatMataUang(0);
                    }
                @endphp
            </td>
            <td nowrap>
                @php
                    if(!empty($limit)){
                        echo FormatMataUang($limit->sisa);
                    }
                    else{
                        echo FormatMataUang(0);
                    }
                @endphp
            </td>
        </tr>
        @endfor
	    
@endforeach