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
<h2>Choferes</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_driver'])

<div class="container table-responsive">
    <table id="T_drivers" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>Nombre</th>
                <th>RFC</th>
                <th>RFC extranjero</th>
                <th>Licencia</th>
                <th>País</th>
                <th>Estado</th>
                <th>Código postal</th>
                <th>Localidad</th>
                <th>Colonia</th>
                <th>Calle</th>
                <th>Número exterior</th>
                <th>Número interior</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_trans_figure}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->fullname}}</td>
                <td>{{$d->fiscal_id}}</td>
                <td>{{$d->fiscal_fgr_id}}</td>
                <td>{{$d->driver_lic}}</td>
                <td>{{$d->sat_FAddress->description}}</td>
                <td>{{$d->FAddress->state}}</td>
                <td>{{$d->FAddress->zip_code}}</td>
                <td>{{$d->FAddress->locality}}</td>
                <td>{{$d->FAddress->neighborhood}}</td>
                <td>{{$d->FAddress->street}}</td>
                <td>{{$d->FAddress->street_num_ext}}</td>
                <td>{{$d->FAddress->street_num_int}}</td>
                <td>{{$d->FAddress->telephone}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_drivers', 'editar' => 'editar_driver', 
        'eliminar' => 'eliminar_driver', 'recuperar' => 'recuperar_driver'] )
@endsection