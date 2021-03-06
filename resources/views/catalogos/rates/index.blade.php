@extends('layouts.principal')

@section('headStyles')
<link rel="stylesheet" href="{{ asset('css/DataTables/datatables.css') }}">
<style>
    .in_sm{
        background-color: transparent;
        border: 0 solid transparent;
        min-height: 20px;
        text-align:
        center;
    }
</style>
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
<h2>{{$title}}</h2>
<br>
<div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
    <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off">
    <label class="btn btn-outline-warning" for="btncheck1">Editar <span class="icon bx bx-edit-alt"></span></label>
</div>
<br>
<br>
<form id="myForm" action="{{ route('guardar_fletes_rates') }}" method="POST" onSubmit="wait();">
    @csrf
<div class="container table-responsive">
    <table id="T_rates" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id state</th>
                <th>id mun</th>
                <th>id zona estado</th>
                <th>id zona municipio</th>
                <th>Estado</th>
                <th>Municipio</th>
                <th>Zona estado</th>
                <th>Zona municipio</th>
                @foreach ($veh as $v)
                    <th style="text-align: center;">{{$v->description}} $</th>
                @endforeach
                @if ($id == 1 || $id == 2)
                    @foreach ($veh as $v)
                        <th></th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody id="tbody">
            @foreach($mun as $m)
            <tr>
                @switch($id)
                    @case(1)
                        <td>{{$m->state_id}}</td>
                        <td>{{$m->mun_id}}</td>
                        <td></td>
                        <td></td>
                        <td>{{$m->state_name}}</td>
                        <td>{{$m->municipality_name}}</td>
                        <td></td>
                        <td></td>
                        @break
                    @case(2)
                        <td>{{$m->state_id}}</td>
                        <td>{{$m->mun_id}}</td>
                        <td></td>
                        <td>{{$m->id_mun_zone}}</td>
                        <td>{{$m->state_name}}</td>
                        <td>{{$m->municipality_name}}</td>
                        <td></td>
                        <td>{{$m->zone}}</td>
                        @break
                    @case(3)
                        <td>{{$m->state_id}}</td>
                        <td></td>
                        <td>{{$m->id_state_zone}}</td>
                        <td></td>
                        <td>{{$m->state_name}}</td>
                        <td></td>
                        <td>{{$m->zone}}</td>
                        <td></td>
                        @break
                    @case(4)
                        <td>{{$m->state_id}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$m->state_name}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        @break
                    @default
                @endswitch
                @foreach ($veh as $v)
                    @switch($id)
                        @case(1)
                            @if (sizeof($rates->where('mun_id',$m->mun_id)->where('veh_type_id',$v->id_key)) != 0)
                                <td style="text-align: center;">
                                    @if($m->local_foreign == 'F')
                                        <input class="in_sm" type="text" name="rateId[]"
                                         value="{{$m->local_foreign}}{{$v->foreign_digit}}{{$m->id_rate}}" disabled>
                                    @else
                                        <input class="in_sm" type="text" name="rateId[]"
                                         value="{{$m->local_foreign}}{{$v->local_digit}}{{$m->id_rate}}" disabled>
                                    @endif                    
                                    <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]" step=".01"
                                        value="{{$rates->where('mun_id',$m->mun_id)->where('veh_type_id',$v->id_key)->values()[0]['rate']}}"
                                        style="background-color: transparent; text-align: right;"
                                        disabled
                                    >
                                    <p style="display: none;">
                                        {{$rates->where('mun_id',$m->mun_id)->where('veh_type_id',$v->id_key)->values()[0]['rate']}}
                                    </p>
                                </td>
                            @else
                                <td style="text-align: center;">
                                    @if($m->local_foreign == 'F')
                                        <input class="in_sm" type="text" name="rateId[]"
                                         value="{{$m->local_foreign}}{{$v->foreign_digit}}{{$m->id_rate}}" disabled>
                                    @else
                                        <input class="in_sm" type="text" name="rateId[]"
                                         value="{{$m->local_foreign}}{{$v->local_digit}}{{$m->id_rate}}" disabled>
                                    @endif
                                    <input id="{{$v->key_code}}" class="rate" name="rate[]" type="number" step=".01" value="" style="background-color: transparent; text-align: right;" disabled>
                                    <p style="display: none;">0</p>
                                </td>
                            @endif
                            @break
                        @case(2)
                            @if (sizeof($rates->where('mun_id',$m->mun_id)->where('veh_type_id',$v->id_key)->where('zone_mun_id',$m->id_mun_zone)) != 0)
                                <td style="text-align: center;">
                                    @if($m->local_foreign == 'F')
                                        <input class="in_sm"
                                         value="{{$m->local_foreign}}{{$v->foreign_digit}}{{$m->id_rate}}" disabled>
                                    @else
                                        <input class="in_sm"
                                         value="{{$m->local_foreign}}{{$v->local_digit}}{{$m->id_rate}}" disabled>
                                    @endif 
                                    <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]" step=".01"
                                        value="{{$rates->where('mun_id',$m->mun_id)->where('veh_type_id',$v->id_key)->where('zone_mun_id',$m->id_mun_zone)->values()[0]['rate']}}"
                                        style="background-color: transparent; text-align: right;"
                                        disabled
                                    >
                                    <p style="display: none;">
                                        {{$rates->where('mun_id',$m->mun_id)->where('veh_type_id',$v->id_key)->where('zone_mun_id',$m->id_mun_zone)->values()[0]['rate']}}
                                    </p>
                                </td>
                            @else
                                <td style="text-align: center;">
                                    @if($m->local_foreign == 'F')
                                        <input class="in_sm"
                                         value="{{$m->local_foreign}}{{$v->foreign_digit}}{{$m->id_rate}}" disabled>
                                    @else
                                        <input class="in_sm"
                                         value="{{$m->local_foreign}}{{$v->local_digit}}{{$m->id_rate}}" disabled>
                                    @endif
                                    <input id="{{$v->key_code}}" class="rate" name="rate[]" type="number" step=".01" value="" style="background-color: transparent; text-align: right;" disabled>
                                    <p style="display: none;">0</p>
                                </td>
                            @endif
                            @break
                        @case(3)
                            @if (sizeof($rates->where('state_id',$m->state_id)->where('veh_type_id',$v->id_key)->where('zone_state_id',$m->id_state_zone)) != 0)
                                <td style="text-align: center;">
                                    <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]" step=".01"
                                        value="{{$rates->where('state_id',$m->state_id)->where('veh_type_id',$v->id_key)->where('zone_state_id',$m->id_state_zone)->values()[0]['rate']}}"
                                        style="background-color: transparent; text-align: right;"
                                        disabled
                                    >
                                    <p style="display: none;">
                                        {{$rates->where('state_id',$m->state_id)->where('veh_type_id',$v->id_key)->where('zone_state_id',$m->id_state_zone)->values()[0]['rate']}}
                                    </p>
                                </td>
                            @else
                                <td style="text-align: center;">
                                    <input id="{{$v->key_code}}" class="rate" name="rate[]" type="number" step=".01" value="" style="background-color: transparent; text-align: right;" disabled>
                                    <p style="display: none;">0</p>
                                </td>
                            @endif
                            @break
                        @case(4)
                            @if (sizeof($rates->where('state_id',$m->state_id)->where('veh_type_id',$v->id_key)) != 0)
                                <td style="text-align: center;">
                                    <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]" step=".01"
                                        value="{{$rates->where('state_id',$m->state_id)->where('veh_type_id',$v->id_key)->values()[0]['rate']}}"
                                        style="background-color: transparent; text-align: right;"
                                        disabled
                                    >
                                    <p style="display: none;">
                                        {{$rates->where('state_id',$m->state_id)->where('veh_type_id',$v->id_key)->values()[0]['rate']}}
                                    </p>
                                </td>
                            @else
                                <td style="text-align: center;">
                                    <input id="{{$v->key_code}}" step=".01" class="rate" name="rate[]" type="number" value="" style="background-color: transparent; text-align: right;" disabled>
                                    <p style="display: none;">0</p>
                                </td>
                            @endif
                            @break
                        @default
                    @endswitch
                @endforeach
                @if ($id == 1 || $id == 2)
                    @foreach ($veh as $v)
                        <td>
                            @if($m->local_foreign == 'F')
                                <p>{{$m->local_foreign}}{{$v->foreign_digit}}{{$m->id_rate}}</p>
                            @else
                                <p>{{$m->local_foreign}}{{$v->local_digit}}{{$m->id_rate}}</p>
                            @endif
                        </td>
                    @endforeach
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br>
<div class="container">
    <div class="row">
        <div class="col-11">
            <p>Nota: Al presionar en el bot??n "guardar", solo se guardar??n los valores mostrados en la pantalla actual.</p>
        </div>
        <div class="col-1">
            <button id="btnGuardar" type="submit" class="btn btn-primary" disabled>Guardar</button>
        </div>
    </div>
</div>
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        const idRender = '<?php echo $id ?>';
        var arr = [];
        var hiddenCol = [];
        switch (idRender) {
            case '1':
                arr = [0,1,2,3,6,7];
                hiddenCol = [14,15,16,17,18,19]; 
                break;
            case '2':
                arr = [0,1,2,3,6];
                hiddenCol = [14,15,16,17,18,19];
                break;
            case '3':
                arr = [0,1,2,3,5,7];
                break;
            case '4':
                arr = [0,1,2,3,5,6,7];
                break;
            default:
                break;
        }

        var table = $('#T_rates').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "EmptyTable": "Ning??n dato disponible en esta tabla",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "??ltimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {
                    "targets": arr,
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": hiddenCol,
                    "visible": false,
                    "searchable": true
                }
            ],
            "order": [[ 8, 'desc' ], [ 9, 'desc' ], [ 10, 'desc' ], [ 11, 'desc' ], [ 12, 'desc' ], [ 13, 'desc' ], [ 0, 'asc' ]],
            "fnDrawCallback": function( oSettings ) {
                document.getElementById('btncheck1').checked  = false;
                document.getElementById('btnGuardar').setAttribute('disabled', 'disabled');
                disableIns();
            },
            "initComplete": function(){
                $("#T_rates").show(); 
            }
        });
    });
</script>
<script>
    function enableIns(){
        inputs = document.getElementsByClassName('rate');
        for (index = 0; index < inputs.length; ++index) {
            inputs[index].removeAttribute('disabled');
            inputs[index].style.background = '#fff';
        }
    }
    function disableIns(){
        inputs = document.getElementsByClassName('rate');
        for (index = 0; index < inputs.length; ++index) {
            inputs[index].setAttribute('disabled', 'disabled');
            inputs[index].style.background = 'transparent';
        }
    }

    $(function () {
        const check = document.getElementById('btncheck1');
        check.addEventListener('change', function handleChange(event){
            if(check.checked){
                document.getElementById('btnGuardar').removeAttribute('disabled');
                enableIns();
            }else{
                document.getElementById('btnGuardar').setAttribute('disabled', 'disabled');
                disableIns();
            }
        });
    });
</script>
<script>
    $(function () {
      $('#myForm').submit(function(event) {
        event.preventDefault();
        document.getElementById('btnGuardar').setAttribute('disabled', 'disabled');
        disableIns();
        document.getElementById('btncheck1').checked  = false;
        var table = $('#T_rates').DataTable();
        var data = table.rows( {page: 'current'} ).data();
        var actionUrl = $(this).attr('action');
        var formData = document.getElementsByClassName('rate');
        var BoxArray = Array.from(formData);
        var values = [];
        for (let i = 0; i< data.length; i++) {
            var rowValues = BoxArray.splice(0,6);
            data[i][8] = rowValues[0].value;
            data[i][9] = rowValues[1].value;
            data[i][10] = rowValues[2].value;
            data[i][11] = rowValues[3].value;
            data[i][12] = rowValues[4].value;
            data[i][13] = rowValues[5].value;
            values.push(data[i]);
        };
        var ratesIds = [];
        const idRender = '<?php echo $id ?>';
        if(idRender == '1' || idRender == '2'){
            var dataRatesIds = document.getElementsByClassName('in_sm');
            var arrayRatesIds = Array.from(dataRatesIds);
            for (let i = 0; i< data.length; i++) {
                var rowRatesIds = arrayRatesIds.splice(0,6);
                ratesIds.push([
                    rowRatesIds[0].value,
                    rowRatesIds[1].value,
                    rowRatesIds[2].value,
                    rowRatesIds[3].value,
                    rowRatesIds[4].value,
                    rowRatesIds[5].value,
                ]);
            };
        }

        $.ajax({
          url: actionUrl,
          type: "POST",
          data: {val: values, ratesIds: ratesIds,  _token: '{{csrf_token()}}'},
          success: function(data) {
            Swal.fire({
                icon: 'success',
                title: 'Guardado con exito'
            })
          },
        });
      });
    });
</script>
@endsection