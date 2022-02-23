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
<h2>Series de documentos</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_serie'])

<div class="container table-responsive">
    <table id="T_series" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Nombre</th>
                <th>Prefijo</th>
                <th>Numero inicial</th>
                <th>Descripción</th>
                <th>Transportista</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_serie}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->id_carrier}}</td>
                <td>{{$d->serie_name}}</td>
                <td>{{$d->prefix}}</td>
                <td>{{$d->initial_number}}</td>
                <td>{{$d->description}}</td>
                <td>{{$d->Carrier->fullname}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_series', 'editar' => 'editar_serie', 
        'eliminar' => 'eliminar_serie', 'recuperar' => 'recuperar_serie'] )
@endsection