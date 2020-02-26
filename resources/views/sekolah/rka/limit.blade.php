@extends('layouts.sekolah')

@section('titleBar', 'Limit RKA')

@section('extraCss')
<style>
.container { margin:150px auto;}
    .treegrid-indent {
        width: 0px;
        height: 16px;
        display: inline-block;
        position: relative;
    }

    .treegrid-expander {
        width: 0px;
        height: 16px;
        display: inline-block;
        position: relative;
        left:-17px;
        cursor: pointer;
    }
</style>
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
                                Limit RKA
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tree-table" class="table table-hover table-bordered">
                                    <th width="15%">Kode Rekening</th>
                                    <th>Nama Rekening</th>
                                    <th>Limit RKA</th>
                                    <th>Sisa</th>
                                    <tbody>
                                        @foreach($parentRekenings as $rekening)
                                            <tr data-id="{{$rekening->id}}" data-nama_rekening="{{$rekening->nama_rekening}}" data-kode_rekening="{{$rekening->kode_rekening}}" data-parent="0" data-level="1">
                                                <td data-column="name">
                                                    {{$rekening->kode_rekening}}
                                                </td>
                                                <td colspan="1">
                                                    {{$rekening->nama_rekening}}
                                                </td>
                                                <td nowrap>
                                                    @php
                                                        $limitparent= $limitRka->where('rekening.parent_id',$rekening->id);
                                                        echo FormatMataUang($limitparent->sum('limit'));
                                                    @endphp
                                                </td>
                                                <td nowrap>
                                                    @php
                                                        echo FormatMataUang($limitparent->sum('sisa'));
                                                    @endphp
                                                </td>
                                            </tr>
                                            @if(count($rekening->subrekening))
                                                @include('sekolah.rka.subRekeningView',[
                                                    'subrekenings' => $rekening->subrekening, 
                                                    'dataParent' => $rekening->id , 
                                                    'kodeParent'=> $rekening->kode_rekening,
                                                    'limitRka' => $limitRka,
                                                    'dataLevel' => 1
                                                ])
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('extraJs')
<script src="{{ asset('app-assets/js/tree-table.js') }}" type="text/javascript"></script>
<script>
    $('tr').find('td[data-column="name"]').attr('style','cursor: pointer');
</script>
@endsection