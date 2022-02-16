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
<h2>Remolques</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_trailer', 'filterCarrier' => (auth()->user()->isAdmin() || auth()->user()->isClient())])

<div class="container table-responsive">
    <table id="T_trailer" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Placas</th>
                <th>Clave subtipo de remolque</th>
                <th>Subtipo de remolque</th>
                <th>Transportista</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_trailer}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->carrier_id}}</td>
                <td>{{$d->plates}}</td>
                <td>{{$d->TrailerSubtype->key_code}}</td>
                <td>{{$d->TrailerSubtype->description}}</td>
                <td>{{$d->Carrier->fullname}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_trailer', 'editar' => 'editar_trailer', 
        'eliminar' => 'eliminar_trailer', 'recuperar' => 'recuperar_trailer'] )
@endsection