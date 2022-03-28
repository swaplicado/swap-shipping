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
<h2>Tarifas</h2>
<br>
<div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
    <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off">
    <label class="btn btn-outline-warning" for="btncheck1">Editar <span class="icon bx bx-edit-alt"></span></label>
</div>
<br>
<br>
<form id="myForm" action="{{ route('guardar_fletes_rates') }}" method="POST">
    @csrf
<div class="container table-responsive">
    <table id="T_rates" class="display" style="width:100%;">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Estado</th>
                <th>Municipio</th>
                @foreach ($veh as $v)
                    <th style="text-align: center;">{{$v->description}} - tarifa</th>
                @endforeach
            </tr>
        </thead>
        <tbody id="tbody">
            @foreach($mun as $m)
            <tr>
                <td>{{$m->id}}</td>
                <td>{{$m->state_id}}</td>
                <td>{{$m->state_name}}</td>
                <td>{{$m->municipality_name}}</td>
                @foreach ($veh as $v)
                    @if (sizeof($rates->where('mun_id',$m->id)->where('veh_type_id',$v->id_key)) != 0)
                        <td>
                            <input id="{{$v->key_code}}" class="rate" type="number" name="rate[]"
                                value="{{$rates->where('mun_id',$m->id)->where('veh_type_id',$v->id_key)->values()[0]['rate']}}"
                                style="background-color: transparent;"
                                disabled
                            >
                            <p style="display: none;">
                                {{$rates->where('mun_id',$m->id)->where('veh_type_id',$v->id_key)->values()[0]['rate']}}
                            </p>
                        </td>
                    @else
                        <td>
                            <input id="{{$v->key_code}}" class="rate" name="rate[]" type="number" value="" style="background-color: transparent;" disabled>
                            <p style="display: none;">0</p>
                        </td>
                    @endif
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
</form>
{{-- <button type="button" class="btn btn-success" onclick="getIn()">guardar</button> --}}
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
                    "targets": [0,1],
                    "visible": false,
                    "searchable": false
                }
            ],
            "order": [[ 4, 'desc' ], [ 5, 'desc' ], [ 6, 'desc' ], [ 7, 'desc' ], [ 8, 'desc' ], [ 9, 'desc' ], [ 1, 'asc' ]],
            "initComplete": function(){ 
                $("#T_rates").show(); 
            }
        });
    });
</script>
<script>
    $(function () {
        const check = document.getElementById('btncheck1');
        check.addEventListener('change', function handleChange(event){
            if(check.checked){
                // inputs = document.getElementsByTagName('input');
                inputs = document.getElementsByClassName('rate');
                for (index = 0; index < inputs.length; ++index) {
                    inputs[index].removeAttribute('disabled');
                    inputs[index].style.background = '#fff';
                }
            }else{
                // inputs = document.getElementsByTagName('input');
                inputs = document.getElementsByClassName('rate');
                for (index = 0; index < inputs.length; ++index) {
                    inputs[index].setAttribute('disabled', 'disabled');
                    inputs[index].style.background = 'transparent';
                }
            }
        });
    });
</script>
<script>
    $(function () {
      $('#myForm').submit(function(event) {
        event.preventDefault();

        var table = $('#T_rates').DataTable();
        var data = table.rows( {page: 'current'} ).data();
        var actionUrl = $(this).attr('action');
        // var formData = new FormData(this);
        var formData = document.getElementsByClassName('rate');
        var BoxArray = Array.from(formData);
        // var aux = BoxArray.splice(0,6);
        var values = [];
        for (let i = 0; i< data.length; i++) {
            var aux = BoxArray.splice(0,6);
            data[i][4] = aux[0].value;
            data[i][5] = aux[1].value;
            data[i][6] = aux[2].value;
            data[i][7] = aux[3].value;
            data[i][8] = aux[4].value;
            data[i][9] = aux[5].value;
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
            console.log(data);
          },
        });
      });
    });
</script>
@endsection