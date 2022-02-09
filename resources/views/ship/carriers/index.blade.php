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

@include('layouts.table_buttons', ['crear' => 'crear_carrier'])
<div class="col-lg-2" style="float: left">
    <select class="form-select" name="carriers" id="carriers">
        <option value="0" selected>Transportista</option>
        @foreach ($data as $d)
            <option value="{{$d->id_carrier}}">{{$d->fullname}}</option>
        @endforeach
    </select>
</div>

<div class="container table-responsive">
    <table id="T_carriers" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>Nombre</th>
                <th>RFC</th>
                <th>Regimen fiscal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_carrier}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->fullname}}</td>
                <td>{{$d->fiscal_id}}</td>
                <td>{{$d->tax_regime->description}}</td>
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