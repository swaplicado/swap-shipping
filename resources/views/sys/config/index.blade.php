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
<h2>Configuración</h2>
<br>

<button id="btn_edit" type="button" class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;">
    <span class="icon bx bx-edit-alt"></span>
</button>
<br>
<br>

<div class="container table-responsive">
    <table id="T_config" class="display" style="width:100%;">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Email</th>
                <th>Moneda local</th>
                <th>Tarifa base</th>
                <th>tarifa base escala</th>
                <th>Distancia minima</th>
                <th>Clave prod/servicio</th>
                <th>Prod/Serv</th>
                <th>Clave unidad</th>
                <th>Simbolo unidad</th>
                <th>Tipo de comprobante</th>
                <th>RFC</th>
                <th>Nombre del receptor</th>
                <th>Domicilio fiscal receptor</th>
                <th>Regimen fiscal receptor</th>
                <th>Uso CFDI</th>
                <th>Lugar expedición</th>
                <th>objeto impuesto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>0</td>
                <td></td>
                <td>{{$data->email}}</td>
                <td>{{$data->localCurrency}}</td>
                <td>{{$data->tarifaBase}}</td>
                <td>{{$data->tarifaBaseEscala}}</td>
                <td>{{$data->distanciaMinima}}</td>
                <td>{{$data->cfdi4_0->claveServicio}}</td>
                <td>{{$data->cfdi4_0->prodServDescripcion}}</td>
                <td>{{$data->cfdi4_0->claveUnidad}}</td>
                <td>{{$data->cfdi4_0->simboloUnidad}}</td>
                <td>{{$data->cfdi4_0->tipoComprobante}}</td>
                <td>{{$data->cfdi4_0->rfc}}</td>
                <td>{{$data->cfdi4_0->nombreReceptor}}</td>
                <td>{{$data->cfdi4_0->domicilioFiscalReceptor}}</td>
                <td>{{$data->cfdi4_0->regimenFiscalReceptor}}</td>
                <td>{{$data->cfdi4_0->usoCFDI}}</td>
                <td>{{$data->cfdi4_0->lugarExpedicion}}</td>
                <td>{{$data->cfdi4_0->objetoImp}}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        var table = $('#T_config').DataTable({
            "columnDefs": [
                {
                    "targets": [0,1,2],
                    "visible": false,
                    "searchable": true
                }
            ]
        });

        $('#T_config tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });

        $('#maincontainer').click(function () {
            table.$('tr.selected').removeClass('selected');
        });
        
        $('#btn_edit').click(function () {
            var id = table.row('.selected').data()[0];
            var url = '{{route("editar_config", ":id")}}';
            url = url.replace(':id',id);
            window.location.href = url;
        });
    });
</script>
@endsection