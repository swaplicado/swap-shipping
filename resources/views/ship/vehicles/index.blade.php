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
<h2>Vehículos</h2>
<br>

{{-- filterCarrier => [requerido?, id carrier es relacion?] --}}
@include('layouts.table_buttons', ['crear' => 'crear_vehicle', 'filterCarrier' => (auth()->user()->isAdmin() || auth()->user()->isClient())])

<div class="container table-responsive">
    <table id="T_vehicles" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Alías</th>
                <th>Placas</th>
                <th>Modelo</th>
                <th>Clave permiso SCT</th>
                <th>Permiso SCT</th>
                <th>Núm. permiso SCT</th>
                <th>Reg. tributario</th>
                <th>Póliza resp. civ.</th>
                <th>Aseguradora</th>
                <th>Clave conf. vehicular</th>
                <th>Conf. vehicular</th>
                <th>Clave vehicular</th>
                <th>Transportista</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_vehicle}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->carrier_id}}</td>
                <td>{{$d->alias}}</td>
                <td>{{$d->plates}}</td>
                <td>{{$d->year_model}}</td>
                <td>{{$d->LicenceSct->key_code}}</td>
                <td>{{$d->LicenceSct->description}}</td>
                <td>{{$d->license_sct_num}}</td>
                <td>{{$d->drvr_reg_trib}}</td>
                <td>{{$d->policy}}</td>
                <td>{{$d->Insurance->full_name}}</td>
                <td>{{$d->VehicleConfig->key_code}}</td>
                <td>{{$d->VehicleConfig->description}}</td>
                <td>{{$d->VehicleKey->key_code ." - ".$d->VehicleKey->description}}</td>
                <td>{{$d->Carrier->fullname}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_vehicles', 'editar' => 'editar_vehicle', 
        'eliminar' => 'eliminar_vehicle', 'recuperar' => 'recuperar_vehicle'] )
@endsection