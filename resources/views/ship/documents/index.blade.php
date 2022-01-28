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
<h2>Cartas Porte</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_carrier'])

<div class="container table-responsive">
    <table id="t_documents" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>Version CFDI</th>
                <th>Version carta porte</th>
                <th>RFC emisor</th>
                <th>Nombre emisor</th>
                <th>Serie</th>
                <th>Folio</th>
                <th>Fecha petición</th>
                <th>Fecha generación</th>
                <th>XML</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lDocuments as $doc)
            <tr>
                <td>{{ $doc->id_document }}</td>
                <td>{{ $doc->is_deleted }}</td>
                <td>{{ $doc->xml_version }}</td>
                <td>{{ $doc->comp_version }}</td>
                <td>{{ $doc->fiscal_id }}</td>
                <td>{{ $doc->fullname }}</td>
                <td>{{ "Serie" }}</td>
                <td>{{ "Folio" }}</td>
                <td>{{ $doc->dt_request }}</td>
                <td>{{ $doc->dt_generated }}</td>
                <td>{{ "" }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
@include('layouts.table_Jscontroll', ['table_id' => 't_documents', 'editar' => 'documents.edit', 'eliminar' => 'documents.destroy', 'recuperar' => 'documents.restore'])
@endsection