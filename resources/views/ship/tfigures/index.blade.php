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
<h2>Figuras de transporte</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'crear_figure', 'filterCarrier' => (auth()->user()->isAdmin() || auth()->user()->isClient())])

<div class="container table-responsive">
    <table id="T_figures" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>id carrier</th>
                <th>Nombre</th>
                <th>RFC</th>
                <th>RFC extranjero</th>
                <th>País</th>
                <th>Transportista</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id_trans_figure}}</td>
                <td>{{$d->is_deleted}}</td>
                <td>{{$d->carrier_id}}</td>
                <td>{{$d->fullname}}</td>
                <td>{{$d->fiscal_id}}</td>
                <td>{{$d->fiscal_fgr_id}}</td>
                <td>{{$d->sat_FAddress->description}}</td>
                <td>{{$d->Carrier->fullname}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 'T_figures', 'editar' => 'editar_figure', 
        'eliminar' => 'eliminar_figure', 'recuperar' => 'recuperar_figure'] )
@endsection