@foreach($subrekenings as $subrekening)
        <ul>
            <li>{{$subrekening->nama_rekening}}</li> 
	    @if(count($subrekening->subrekening))
            @include('admin.master.subrekening',['subrekenings' => $subrekening->subrekening])
        @endif
        </ul> 
@endforeach