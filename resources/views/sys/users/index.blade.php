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
<h2>Usuarios</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'register'])

<div class="container table-responsive">
    <table id="T_users" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>Usuario</th>
                <th>Nombre completo</th>
                <th>E-mail</th>
                <th>Tipo de usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->username}}</td>
                <td>{{$d->full_name}}</td>
                <td>{{$d->email}}</td>
                <td>
                    @foreach ($d->getRol() as $r)
                        ({{$r->description}})
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_users', 'editar' => 'editar_user', 
        'eliminar' => 'eliminar_user', 'recuperar' => 'recuperar_user'] )
@endsection