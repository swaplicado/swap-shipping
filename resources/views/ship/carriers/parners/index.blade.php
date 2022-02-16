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
<h2>Asociados</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_parner', 'filterCarrier' => (auth()->user()->isAdmin() || auth()->user()->isClient())])

<div class="container table-responsive">
    <table id="T_parners" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th></th>
                <th>Nombre</th>
                <th>E-mail</th>
                <th>Transportista</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->user()->first()->id}}</td>
                <td>{{$d->user()->first()->is_deleted}}</td>
                <td>{{$d->carrier->id_carrier}}</td>
                <td>{{$d->user()->first()->full_name}}</td>
                <td>{{$d->user()->first()->email}}</td>
                <td>{{$d->user()->first()->Carrier->fullname}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_parners', 'editar' => 'editar_parner', 
        'eliminar' => 'eliminar_parner', 'recuperar' => 'recuperar_parner'] )
@endsection