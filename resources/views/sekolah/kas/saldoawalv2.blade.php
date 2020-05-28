@extends('layouts.sekolah')

@section('titleBar', 'Saldo Awal')

@section('extraCss')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/js/dt/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/toastr.css') }}">
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
                                Saldo Awal
                            </h4>
                        </div>
                        <div class="card-body">
                            
                            <div class="table-responsive">
                                <table id="tabelSaldo" class="table table-bordered nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Saldo Bank</th>
                                            <th>Saldo Tunai</th>
                                            <th>Pilihan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $ta= Cookie::get('ta');
                                        @endphp
                                        @for ($i = 2; $i <= 12; $i++)
                                            @php
                                                $period= \Carbon\Carbon::createFromFormat("!Y-n-j", $ta."-".$i."-1");
                                                $ada_saldo_awal= $saldoawal->contains('periode', $period);
                                                $saldo_bank_awal_bln= $saldoawal->where('periode',$period)->sum('saldo_bank'); 
                                                $saldo_tunai_awal_bln= $saldoawal->where('periode',$period)->sum('saldo_tunai'); 
                                            @endphp
                                            <tr>
                                                <td>{{($i-1)}}</td>
                                                <td>{{$period->locale('id_ID')->isoFormat('LL')}}</td>
                                                <td align="right">{{FormatUang($saldo_bank_awal_bln)}}</td>
                                                <td align="right">{{FormatUang($saldo_tunai_awal_bln)}}</td>
                                                <td>
                                                    @php
                                                        if ($ada_saldo_awal) {
                                                            $id_sa= $saldoawal->firstWhere('periode',$period)->id;
                                                            $urlhitung= route('sekolah.saldoawal.hitung', ['id' => $id_sa]);
                                                            echo RenderTombol("success", $urlhitung, "Hitung Ulang");
                                                        }
                                                        else{
                                                            $urlkalkulasi= route('sekolah.saldoawal.kalkulasi', ['periode' => $period->format('Y-m-d')]);
                                                            echo RenderTombol("warning", $urlkalkulasi, "Kalkulasi");
                                                        }
                                                    @endphp
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Saldo Bank</th>
                                            <th>Saldo Tunai</th>
                                            <th>Pilihan</th>
                                        </tr>
                                    </tfoot>
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
<script src="{{ asset('app-assets/vendors/js/dt/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    $(function() {

        // $('#tabelSaldo').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     responsive: true,
        //     ajax: "{{ route('sekolah.saldoawal.index') }}",
        //     dom: 'flrtp',
        //     order: [1,'asc'],
        //     /*columnDefs: [
        //         { responsivePriority: 1, targets: 3 },
        //         { responsivePriority: 2, targets: 0 },
        //         { responsivePriority: 3, targets: 6 },
        //         { responsivePriority: 4, targets: 4 },
        //     ],*/
        //     columns: [
        //         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        //         { data: 'periode', name: 'periode' },
        //         { data: 'saldo_bank', name: 'saldo_bank' },
        //         { data: 'saldo_tunai', name: 'saldo_tunai' },
        //         { data: 'action', name: 'action', orderable: false, searchable: false},
                
                
        //     ],
        //     /*initComplete: function () {
        //         this.api().columns(':gt(0):lt(4)').every(function () {
        //             var column = this;
        //             var input = document.createElement('input');
        //             $(input).addClass('form-control m-0');
        //             $(column.footer()).addClass('p-1');
        //             $(input).appendTo($(column.footer()).empty())
        //             .on('change', function () {
        //                 var val = $(this).val();
        //                 column
        //                 .search( val )
        //                 .draw();
        //             });
        //         });

        //         $('.confirmation').on('click', function () {
        //             return confirm('Apakah anda yakin akan menghapus Pendapatan ini?');
        //         });
        //     }*/
        // });

        /* jshint ignore:start */
        @if($errors->any())
            toastr.error("{{ $errors->first() }}", "Error!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif

        @if($message = Session::get('success'))
            toastr.success("{{ $message }}", "Sukses!", {
                closeButton: 1,
                timeOut: 0
            });
        @endif
        /* jshint ignore:end */
    });
</script>
@endsection