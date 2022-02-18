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

@include('layouts.table_buttons', [
        'crear' => 'crear_carrier', 
        'moreButtons' => [
            ['id' => 'id_sign', 'class' => 'dark', 'icon' => 'bx-bell', 'url' => '#', 'title' => 'Timbrar'],
            ['id' => 'id_down_xml', 'class' => 'primary', 'icon' => 'bx-download', 'url' => '#', 'title' => 'Descagar XML'],
            ['id' => 'id_down_pdf', 'class' => 'secondary', 'icon' => 'bxs-file-pdf', 'url' => '#', 'title' => 'Descagar PDF'],
        ],
    ])

<div class="container table-responsive">
    <table id="t_documents" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is_deleted</th>
                <th></th>
                <th>Estatus</th>
                <th>Version CFDI</th>
                <th>Version carta porte</th>
                <th>RFC emisor</th>
                <th>Nombre emisor</th>
                <th>Serie</th>
                <th>Folio</th>
                <th>Fecha petición</th>
                <th>Fecha generación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lDocuments as $doc)
            <tr>
                <td>{{ $doc->id_document }}</td>
                <td>{{ $doc->is_deleted }}</td>
                <td></td>
                <td>{{ (!$doc->is_processed) ? "PENDIENTE" : ($doc->is_signed ? "TIMBRADO" : ($doc->is_processed ? "PROCESADO" : "CANCELADO")) }}</td>
                <td>{{ $doc->xml_version }}</td>
                <td>{{ $doc->comp_version }}</td>
                <td>{{ $doc->fiscal_id }}</td>
                <td>{{ $doc->fullname }}</td>
                <td>{{ $doc->serie }}</td>
                <td>{{ str_pad($doc->folio, 6, "0", STR_PAD_LEFT) }}</td>
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
@endsection