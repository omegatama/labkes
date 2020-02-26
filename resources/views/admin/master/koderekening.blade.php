@extends('layouts.admin')

@section('titleBar', 'Master Data Rekening')

@section('extraCss')
<style>
@font-face{font-family:'Glyphicons Halflings';src:url('https://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.eot');src:url('https://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'),url('https://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.woff') format('woff'),url('https://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.ttf') format('truetype'),url('https://netdna.bootstrapcdn.com/bootstrap/3.0.0/fonts/glyphicons-halflings-regular.svg#glyphicons-halflingsregular') format('svg');}
.glyphicon{position:relative;top:1px;display:inline-block;font-family:'Glyphicons Halflings';font-style:normal;font-weight:normal;line-height:1;-webkit-font-smoothing:antialiased;}
.glyphicon-chevron-left:before{content:"\e079";}
.glyphicon-chevron-right:before{content:"\e080";}
.glyphicon-plus-sign:before{content:"\e081";}
.glyphicon-minus-sign:before{content:"\e082";}
.glyphicon-chevron-up:before{content:"\e113";}
.glyphicon-chevron-down:before{content:"\e114";}

body { background-color:#fafafa; font-family:'Open Sans';}
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
                                Master Data Rekening
                            </h4>
                        </div>
                        <div class="card-body">
                            <table id="tree-table" class="table table-hover table-bordered">
                                <th width="15%">Kode Rekening</th>
                                <th>Nama Rekening</th>
                                <th width="15%">Pilihan</th>
                                <tbody>
                                    @foreach($parentRekenings as $rekening)
                                        <tr data-id="{{$rekening->id}}" data-nama_rekening="{{$rekening->nama_rekening}}" data-kode_rekening="{{$rekening->kode_rekening}}" data-parent="0" data-level="1">
                                            <td data-column="name">
                                                {{$rekening->kode_rekening}}
                                            </td>
                                            <td>
                                                {{$rekening->nama_rekening}}
                                            </td>
                                            <td>
                                                Tambah Edit
                                                @if(empty(count($rekening->subrekening)))
                                                    Delete
                                                @endif
                                            </td>
                                        </tr>
                                        @if(count($rekening->subrekening))
                                            @include('admin.master.subRekeningView',['subrekenings' => $rekening->subrekening, 'dataParent' => $rekening->id , 'kodeParent'=> $rekening->kode_rekening, 'dataLevel' => 1])
                                        @endif      
                                    @endforeach
                                </tbody>
                            </table>
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
@endsection
