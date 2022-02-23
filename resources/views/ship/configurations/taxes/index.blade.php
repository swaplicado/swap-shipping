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
<h2>Configuraciones de impuestos</h2>
<br>

@include('layouts.table_buttons', ['crear' => 'config.taxes.create'])

<div class="container table-responsive">
    <table id="t_tax_cfgs" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>is deleted</th>
                <th>Tipo</th>
                <th>T. persona emisor</th>
                <th>T. persona receptor</th>
                <th>Régimen</th>
                <th>Impuesto</th>
                <th>Tasa</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lConfigurations as $cfg)
            <tr>
                <td>{{ $cfg->id_config }}</td>
                <td>{{ $cfg->is_deleted }}</td>
                <td>{{ strtoupper($cfg->config_type) }}</td>
                <td>{{ $cfg->person_type_emisor == null ? "TODOS" : strtoupper($cfg->person_type_emisor) }}</td>
                <td>{{ $cfg->person_type_receptor == null ? "TODOS" : strtoupper($cfg->person_type_receptor) }}</td>
                <td>{{ $cfg->regime_name == null ? "TODOS" : strtoupper($cfg->regime_name) }}</td>
                <td>{{ strtoupper($cfg->tax_name) }}</td>
                <td>{{ $cfg->rate }}</td>
                <td>{{ $cfg->amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @include('layouts.table_Jscontroll', ['table_id' => 't_tax_cfgs', 
                                            'editar' => 'config.taxes.edit', 
                                            'eliminar' => 'config.taxes.delete', 
                                            'recuperar' => 'config.taxes.recovery'] )
@endsection