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
<h2>Tarifas repartos</h2>
<br>
<div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
    <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off">
    <label class="btn btn-outline-warning" for="btncheck1">Editar <span class="icon bx bx-edit-alt"></span></label>
</div>
<br>
<br>
<form id="myForm" action="{{ route('guardar_reparto_rates') }}" method="POST" onSubmit="wait();">
    @csrf
<div class="container table-responsive">
    <table id="T_rates" class="display" style="width:100%;">
        <thead>
            <tr>
                <th></th>
                <th>Tipo de destino</th>
                @foreach ($veh as $v)
                    <th style="text-align: center;">{{$v->description}} - tarifa</th>
                @endforeach
            </tr>
        </thead>
        <tbody id="tbody">
            <tr>
                <td>L</td>
                <td>
                    <p>Local</p>
                </td>
                @foreach ($veh as $v)
                    @if (sizeof($rates->where('veh_type_id',$v->id_key)->where('Local_foreign', 'L')) != 0)
                        <td>
                            <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]" step=".01"
                                value="{{$rates->where('veh_type_id',$v->id_key)->where('Local_foreign', 'L')->values()[0]['rate']}}"
                                style="background-color: transparent;"
                                disabled
                            >
                        </td>
                    @else
                        <td>
                            <input id="{{$v->key_code}}" class="rate" name="rate[]" type="number" step=".01" value="" style="background-color: transparent;" disabled>
                        </td>
                    @endif
                @endforeach
            </tr>
            <tr>
                <td>F</td>
                <td>
                    <p>Foraneo</p>
                </td>
                @foreach ($veh as $v)
                    @if (sizeof($rates->where('veh_type_id',$v->id_key)->where('Local_foreign', 'F')) != 0)
                    <td>
                        <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]" step=".01"
                            value="{{$rates->where('veh_type_id',$v->id_key)->where('Local_foreign', 'F')->values()[0]['rate']}}"
                            style="background-color: transparent;"
                            disabled
                        >
                    </td>
                    @else
                    <td>
                        <input id="{{$v->key_code}}" class="rate" name="rate[]" type="number" step=".01" value="" style="background-color: transparent;" disabled>
                    </td>
                    @endif
                @endforeach
            </tr>
        </tbody>
    </table>
</div>
<br>
<div class="container">
    <div class="row">
        <div class="col-11">
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
        var table = $('#T_rates').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "EmptyTable": "Ningún dato disponible en esta tabla",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }
            ],
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
        var BoxArray = Array.from(formData)
        var values = [];
        for (let i = 0; i< data.length; i++) {
            var rowValues = BoxArray.splice(0,6);
            data[i][2] = rowValues[0].value;
            data[i][3] = rowValues[1].value;
            data[i][4] = rowValues[2].value;
            data[i][5] = rowValues[3].value;
            data[i][6] = rowValues[4].value;
            data[i][7] = rowValues[5].value;
            values.push(data[i]);
        };
        $.ajax({
          url: actionUrl,
          type: "POST",
          data: {val: values, _token: '{{csrf_token()}}'},
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