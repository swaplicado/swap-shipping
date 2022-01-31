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
<h2>Roles de usuarios</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_role'])

<div class="container table-responsive">
    <table id="T_role" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>Rol</th>
                <th>Descripción</th>
                <th>Permisos asociados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->name}}</td>
                <td>{{$d->description}}</td>
                <td>
                    <table>
                        <tbody>
                            @foreach ($d->RolePermissions as $rp)
                                @foreach ($rp->Permission()->get() as $p)
                                    <tr>
                                        <td>{{$p->key_code}}</td>                                         
                                        <td>{{$p->description}}</td>                                         
                                    </tr>                                         
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_role', 'editar' => 'editar_role', 
        'eliminar' => 'eliminar_role', 'recuperar' => 'recuperar_role'] )
@endsection