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
<h2>Transportistas</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_carrier', 'filterCarrier' => (auth()->user()->isAdmin() || auth()->user()->isClient())])

<div class="container table-responsive">
    <table id="T_carriers" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Nombre</th>
                <th>RFC</th>
                <th>Clave de regimen fiscal</th>
                <th>Regimen fiscal</th>
                <th>Teléfono 1</th>
                <th>Contacto 1</th>
                <th>Teléfono 2</th>
                <th>Contacto 2</th>
                <th>Clave de concepto</th>
                <th>Concepto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_carrier}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->id_carrier}}</td>
                <td>{{$d->fullname}}</td>
                <td>{{$d->fiscal_id}}</td>
                <td>{{$d->tax_regime->key_code}}</td>
                <td>{{$d->tax_regime->description}}</td>
                <td>{{$d->telephone1}}</td>
                <td>{{$d->contact1}}</td>
                <td>{{$d->telephone2}}</td>
                <td>{{$d->contact2}}</td>
                <td>{{$d->prod_serv->key_code}}</td>
                <td>{{$d->prod_serv->description}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_carriers', 'editar' => 'editar_carrier', 
        'eliminar' => 'eliminar_carrier', 'recuperar' => 'recuperar_carrier'] )
@endsection