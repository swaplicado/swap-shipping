@extends('layouts.principal')

@section('headStyles')
<link rel="stylesheet" href="{{ asset('css/DataTables/datatables.css') }}">

@endsection

@section('headJs')
<script src="{{ asset('css/DataTables/datatables.js') }}"></script>
@endsection

@section('aside')
@include('layouts.aside')
@endsection

@section('nav-up')
@include('layouts.nav-up')
@endsection

@section('content')
<h2>Cartas Porte ({{ $title }})</h2>
<br>

@php
    $carrierFilter = 
    '
    <div class="col-md-4">
        <select class="form-select" name="ic" id="ic">
            <option value="">Todos</option>';
            foreach ($carriers as $carrier) {
                $carrierFilter .= '<option value="'.$carrier->id_carrier.'"'.($carrier->id_carrier == $ic ? 'selected' : '').'>'.$carrier->fiscal_id.' - '.$carrier->fullname.'</option>';
            }
    $carrierFilter .= '</select>
    </div>
    ';
@endphp

@include('layouts.table_buttons', [ 'withOutEliminar' => true, 'withOutRecuperar' => true,
        'moreButtons' => [
            ['id' => 'id_sign', 'class' => 'dark', 'icon' => 'bx-bell', 'url' => '#', 'title' => 'Timbrar'],
            ['id' => 'id_down_xml', 'class' => 'primary', 'icon' => 'bx-download', 'url' => '#', 'title' => 'Descagar XML'],
            ['id' => 'id_down_pdf', 'class' => 'secondary', 'icon' => 'bxs-file-pdf', 'url' => '#', 'title' => 'Descagar PDF'],
            ['id' => 'id_cancel', 'class' => 'danger', 'icon' => 'bx-block', 'url' => 'can', 'title' => 'Cancelar CFDI'],
            ['id' => 'id_stock', 'class' => 'secondary-danger', 'icon' => 'bx-archive', 'url' => 'arc', 'title' => 'Archivar documento'],
            ['id' => 'id_restore', 'class' => 'info', 'icon' => 'bxs-archive-out', 'url' => 'arc', 'title' => 'Recuperar documento'],
            ['id' => 'id_copy', 'class' => 'purple', 'icon' => 'bx-copy-alt', 'url' => 'cpy', 'title' => 'Hacer copia'],
            ['id' => 'id_forward_mail', 'class' => 'brown', 'icon' => 'bx-mail-send', 'url' => '#', 'title' => 'Reenviar correo'],
        ],
        'moreFilters' => [
            '
            <div class="'.($withCarrierFilter ? 'col-md-10' : 'col-md-5').'" style="float: right">
                <form action="'.(route('documents', $viewType)).'" class="form-inline">
                    <div class="row" >
                        '.($withCarrierFilter ? $carrierFilter : '')
                        .($withDateFilter ? '
                        <div class="'.($withCarrierFilter ? 'col-md-5' : 'col-md-10').'" >
                            <button id = "less" class = "btn-secondary" type="button" style = "float: left; width: 30px; height: 100%;">
                                -
                            </button>
                            <button id = "plus" class = "btn-secondary" type="button" style = "float: right; width: 30px; height: 100%;">
                                +
                            </button>
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                                <input id = "calendarStart" type="hidden" name="calendarStart" value="">
                                <input id = "calendarEnd" type="hidden" name="calendarEnd" value="">
                            </div>
                        </div>' : '').
                        ($withDateFilter || $withCarrierFilter ? '<div class="col-md-1">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search-alt"></i>
                            </button>
                        </div>' : '').'
                    </div>
                </form>
            </div>'
        ]
    ])

<div class="container table-responsive">
    <table id="t_documents" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is_deleted</th>
                <th>id carrier</th>
                <th>is_archive</th>
                <th>Estatus</th>
                {{-- <th>Version CFDI</th>
                <th>Version carta porte</th> --}}
                <th>RFC emisor</th>
                <th>Nombre emisor</th>
                <th>Tipo</th>
                <th>Veh</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Serie</th>
                <th>Folio</th>
                <th>Embarque</th>
                <th>Báscula</th>
                @if($enableTotales)
                    <th>Subtotal</th>
                    <th>Total Impuestos Retenidos</th>
                    <th>Total Impuestos Trasladados</th>
                    <th>Descuentos</th>
                    <th>Total</th>
                @endif
                <th>Fecha petición</th>
                <th>Fecha generación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lDocuments as $doc)
            <tr>
                <td>{{ $doc->id_document }}</td>
                <td>{{ $doc->is_deleted }}</td>
                <td>{{$doc->id_carrier}}</td>
                <td>{{$doc->is_archive}}</td>
                <td>{{ $doc->is_archive ? "ARCHIVADO" : ((! $doc->is_processed) ? "PENDIENTE" : ($doc->is_canceled ? "CANCELADO" : ($doc->is_signed ? "TIMBRADO" : "PROCESADO"))) }}</td>
                {{-- <td>{{ $doc->xml_version }}</td>
                <td>{{ $doc->comp_version }}</td> --}}
                <td>{{ $doc->fiscal_id }}</td>
                <td>{{ $doc->fullname }}</td>
                <td>{{ $doc->ship_type }}</td>
                <td title="{{ $doc->veh_key_description }}">{{ $doc->veh_key_code }}</td>
                <td title="{{ isset($doc->srcAddress) ? $doc->srcAddress->estadoName.' / '.$doc->srcAddress->municipioName : "" }}">{{ isset($doc->idLocSrc) ?  $doc->idLocSrc : ''}}</td>
                <td title="{{ isset($doc->destAddress) ? $doc->destAddress->estadoName.' / '.$doc->destAddress->municipioName : "" }}">{{ isset($doc->idLocDest) ?  $doc->idLocDest : ''}}</td>
                <td>{{ $doc->serie }}</td>
                <td>{{ strlen($doc->folio) > 0 ? str_pad($doc->folio, 7, "0", STR_PAD_LEFT) : "" }}</td>
                <td>{{ str_pad($doc->shipping_folio, 7, "0", STR_PAD_LEFT) }}</td>
                <td>{{ str_pad($doc->scale_ticket, 7, "0", STR_PAD_LEFT) }}</td>
                @if($enableTotales)
                    <td>{{ $doc->subTotal }}</td>
                    <td>{{ $doc->totalImpuestosRetenidos }}</td>
                    <td>{{ $doc->totalImpuestosTrasladados }}</td>
                    <td>{{ $doc->discounts }}</td>
                    <td>{{ $doc->total }}</td>
                @endif
                <td>{{ $doc->requested_at }}</td>
                <td>{{ $doc->generated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('ship.documents.cancel_modal')
@endsection

@section('scripts')
<script>
    var urlCancel = '{{ route($cancelRoute, [":id", ":id_reason", ":ref"]) }}';
</script>
@include('layouts.table_Jscontroll', ['table_id' => 't_documents', 
                                        'signRoute' => 'documents.sign',
                                        'cancelRoute' => 'documents.cancel',
                                        'editar' => 'documents.edit',
                                        'Pdf' => 'cfdiToPdf',
                                        'toStock' => 'documents.toStock',
                                        'restore' => 'documents.restore',
                                        'copy' => 'documents.copy',
                                        'forwardMail' => 'documents.forward_mail'])
<script type="text/javascript">
$(document).ready(function () {
    var column = table.column(3);
    column.visible( false );
});
</script>
<script type="text/javascript">
    moment.locale('es');

    $(function() {
        var n = 0;
        // var start = moment().startOf('month');
        // var end = moment().endOf('month');
        var requestStart = '<?php echo $start ?>';
        var requestEnd = '<?php echo $end ?>';

        if (requestStart == "" || requestStart == null || requestStart == undefined) {
            return;
        }
        
        var start = moment(requestStart, 'YYYY-MM-DD');
        var end = moment(requestEnd, 'YYYY-MM-DD');

        var s = start;
        var e = end;

        var less = document.getElementById('less');
        var plus = document.getElementById('plus');

        var calendarStart = document.getElementById('calendarStart');
        var calendarEnd = document.getElementById('calendarEnd');

        less.addEventListener('click', function(){
            var a = s.diff(e, 'days');
            switch (a) {
                case 0:
                    n = n + 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'days');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'days');
                    break;
                case -5:
                case -6:
                case -7:
                    n = n + 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'week').startOf('week');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'week').endOf('week');
                    break;
                case -27:
                case -28:
                case -29:
                case -30:
                case -31:
                    n = n + 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'month').startOf('month');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'month').endOf('month');
                    break;
                case -363:
                case -364:
                case -365:
                    n = n + 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'year').startOf('year');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'year').endOf('year');
                    break;
            
                default:
                    n = n + 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'days');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'days');
                    break;
            }
            cb(s,e);
        });
        
        plus.addEventListener('click', function(){
            var a = s.diff(e, 'days');
            switch (a) {
                case 0:
                    n = n - 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'days');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'days');
                    break;
                case -5:
                case -6:
                case -7:
                    n = n - 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'week').startOf('week');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'week').endOf('week');
                    break;
                case -27:
                case -28:
                case -29:
                case -30:
                case -31:
                    n = n - 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'month').startOf('month');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'month').endOf('month');
                    break;
                case -363:
                case -364:
                case -365:
                    n = n - 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'year').startOf('year');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'year').endOf('year');
                    break;
            
                default:
                    n = n - 1;
                    s = moment(requestStart, 'YYYY-MM-DD').subtract(n, 'days');
                    e = moment(requestEnd, 'YYYY-MM-DD').subtract(n, 'days');
                    break;
            }
            cb(s,e);
        });

        function cb(start, end) {
            s = start;
            e = end;
            calendarStart.value = s;
            calendarEnd.value = e;
            $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Día': [moment(), moment()],
                'Semana': [moment().startOf('week'), moment().endOf('week')],
                'Mes': [moment().startOf('month'), moment().endOf('month')],
                'Año': [moment().startOf('year'), moment().endOf('year')]
            }
        }, cb);

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [year, month, day].join('-');
        }
    
        $('#reportrange').on('apply.daterangepicker', (e, picker) => {
            n = 0;
            date = new Date();
            requestStart = formatDate(date);
            requestEnd = formatDate(date);
        });

        cb(start, end);
    
    });
</script>
<script src="{{ asset('js/myapp/documents/SDocumentsIndex.js') }}"></script>
@endsection