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
<h2>Aseguradoras</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_insurance', 'filterCarrier' => (auth()->user()->isAdmin() || auth()->user()->isClient())])

<div class="container table-responsive">
    <table id="T_insurances" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Nombre</th>
                <th>transportista</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_insurance}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->carrier_id}}</td>
                <td>{{$d->full_name}}</td>
                <td>{{$d->carrier->fullname}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_insurances', 'editar' => 'editar_insurance', 
        'eliminar' => 'eliminar_insurance', 'recuperar' => 'recuperar_insurance'] )
@endsection