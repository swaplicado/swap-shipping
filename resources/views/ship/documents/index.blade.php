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
@if(session('mesage'))
<script>
    msg = "<?php echo session('mesage'); ?>";
        myIcon = "<?php echo session('icon'); ?>"

        Swal.fire({
            icon: myIcon,
            title: msg
        })
</script>
@endif
<h2>Cartas Porte ({{ $title }})</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_carrier'])

@section('add_buttons')
    <a style="border-radius: 50%; padding: 5px 10px;" class="btn btn-primary" href="#" target="_blank" title="Descagar XML">
        <span class="icon bx bx-download"></span>
    </a>
    <a style="border-radius: 50%; padding: 5px 10px;" class="btn btn-secondary" href="#" target="_blank" title="Descagar PDF">
        <span class="icon bx bxs-file-pdf"></span>
    </a>
@endsection

<div class="container table-responsive">
    <table id="t_documents" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is_deleted</th>
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
                <td>{{ (!$doc->is_processed && !$doc->is_signed) ? "PENDIENTE" : ($doc->is_processed ? "PROCESADO" : ($doc->is_signed ? "TIMBRADO" : "CANCELADO")) }}</td>
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
@include('layouts.table_Jscontroll', ['table_id' => 't_documents', 'editar' => 'documents.edit', 'eliminar' => 'documents.destroy', 'recuperar' => 'documents.restore'])
@endsection