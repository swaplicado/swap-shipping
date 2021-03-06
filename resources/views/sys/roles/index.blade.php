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
<h2>Roles de usuarios</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_role'])

<div class="container table-responsive">
    <table id="T_role" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th></th>
                <th></th>
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
                <td></td>
                <td style="width: 10%; text-align:center;"><button id="button{{$d->id}}" class='bx bxs-down-arrow' type="button" class="btn btn-secondary" onclick="show({{$d->id}})"></button></td>
                <td>{{$d->name}}</td>
                <td>{{$d->description}}</td>
                <td></td>
            </tr>
            <tr id="{{$d->id}}" style="display: none">
                <td>{{$d->id}}</td>
                <td>{{$d->is_deleted}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
    <script>
        function show(id){
            var tr = document.getElementById(id);
            var button = document.getElementById('button' + id);
            if(tr.style.display == "none"){
                tr.style.display = "table-row";
                button.style.transform = 'rotate(180deg)';
            }else{
                tr.style.display = "none";
                button.style.transform = 'rotate(360deg)';
            }
        }
    </script>
@endsection