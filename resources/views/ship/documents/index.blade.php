@extends('layouts.principal')

@section('headStyles')
<link rel="stylesheet" href="{{ asset('css/DataTables/datatables.css') }}">
<style>
    .dataTables_wrapper {
        font-size: 0.8em;
    }

</style>
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

@include('layouts.table_buttons', [
        'crear' => 'crear_carrier', 
        'moreButtons' => [
            ['id' => 'id_sign', 'class' => 'dark', 'icon' => 'bx-bell', 'url' => '#', 'title' => 'Timbrar'],
            ['id' => 'id_down_xml', 'class' => 'primary', 'icon' => 'bx-download', 'url' => '#', 'title' => 'Descagar XML'],
            ['id' => 'id_down_pdf', 'class' => 'secondary', 'icon' => 'bxs-file-pdf', 'url' => '#', 'title' => 'Descagar PDF'],
            ['id' => 'id_cancel', 'class' => 'danger', 'icon' => 'bx-block', 'url' => 'can', 'title' => 'Cancelar CFDI'],
        ],
        'moreFilters' => [
            '<div class="col-md-5"></div>
            <div class="col-md-4">
                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-primary">
                    <i class="bx bx-search-alt"></i>
                </button>
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
                <th>Estatus</th>
                {{-- <th>Version CFDI</th>
                <th>Version carta porte</th> --}}
                <th>RFC emisor</th>
                <th>Nombre emisor</th>
                <th>Serie</th>
                <th>Folio</th>
                <th>Embarque</th>
                <th>Báscula</th>
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
                <td>{{ (!$doc->is_processed) ? "PENDIENTE" : ($doc->is_signed ? "TIMBRADO" : ($doc->is_processed ? "PROCESADO" : "CANCELADO")) }}</td>
                {{-- <td>{{ $doc->xml_version }}</td>
                <td>{{ $doc->comp_version }}</td> --}}
                <td>{{ $doc->fiscal_id }}</td>
                <td>{{ $doc->fullname }}</td>
                <td>{{ $doc->serie }}</td>
                <td>{{ str_pad($doc->folio, 6, "0", STR_PAD_LEFT) }}</td>
                <td>{{ str_pad($doc->shipping_folio, 6, "0", STR_PAD_LEFT) }}</td>
                <td>{{ str_pad($doc->scale_ticket, 6, "0", STR_PAD_LEFT) }}</td>
                <td>{{ $doc->requested_at }}</td>
                <td>{{ $doc->generated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
@include('layouts.table_Jscontroll', ['table_id' => 't_documents', 
                                        'signRoute' => 'documents.sign', 
                                        'editar' => 'documents.edit', 
                                        'eliminar' => 'documents.destroy', 
                                        'recuperar' => 'documents.restore',
                                        'Pdf' => 'cfdiToPdf'])
<script type="text/javascript">
    moment.locale('es');
    $(function() {
    
        var start = moment().subtract(29, 'days');
        var end = moment();
    
        function cb(start, end) {
            $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
        }
    
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
    
        cb(start, end);
    
    });
</script>
@endsection