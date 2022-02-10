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
<h2>Aseguradoras</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_insurance', 'filterCarrier' => auth()->user()->isAdmin()])

<div class="container table-responsive">
    <table id="T_insurances" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Nombre</th>
                <th>Responsabilidad civil</th>
                <th>Ambiental</th>
                <th>Carga</th>
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
                @if ($d->is_civ_resp)
                    <td style="text-align: center"><i class='bx bx-check bx-sm'></i></td>
                @else
                    <td style="text-align: center"><i class='bx bx-x bx-sm'></i></td>
                @endif
                @if ($d->is_ambiental)
                    <td style="text-align: center"><i class='bx bx-check bx-sm'></i></td>
                @else
                    <td style="text-align: center"><i class='bx bx-x bx-sm'></i></td>
                @endif
                @if ($d->is_cargo)
                    <td style="text-align: center"><i class='bx bx-check bx-sm'></i></td>
                @else
                    <td style="text-align: center"><i class='bx bx-x bx-sm'></i></td>
                @endif
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