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
                <td>{{$m->state_name}}</td>
                <td></td>
                <td>{{$m->state_name}}</td>
                <td>{{$m->municipality_name}}</td>
                @foreach ($veh as $v)
                    @php
                        $name = (string)$v->key_code;
                    @endphp
                    <td><input type="text" value="{{$m->$name}}"></td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<button type="button" class="btn btn-success" onclick="getIn()">guardar</button>
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
            "initComplete": function(){ 
                $("#T_rates").show(); 
            }
        });

        $('#T_rates').on('draw.dt', function(){
            $('#T_rates').Tabledit({
            url:'action.php',
            dataType:'json',
            columns:{
                identifier : [0, 'id'],
                editable:[[1, 'first_name'], [2, 'last_name'], [3, 'gender', '{"1":"Male","2":"Female"}']]
            },
            restoreButton:false,
            onSuccess:function(data, textStatus, jqXHR)
            {
                if(data.action == 'delete')
                {
                $('#' + data.id).remove();
                $('#T_rates').DataTable().ajax.reload();
                }
            }
            });
            });
    });
</script>
<script>
    // $(function () {
    //   $('#rates_form').submit(function(event) {
    //     event.preventDefault();
    
    //     var actionUrl = $(this).attr('action');
    //     var formData = new FormData(this);

    //     $.ajax({
    //       url: actionUrl,
    //       type: "POST",
    //       dataType: "json",
    //       contentType: false,
    //       data: formData,
    //       processData: false,
    //       success: function(data) {
    //         Swal.fire({
    //             icon: 'success',
    //             title: 'Guardado con exito'
    //         })
    //         console.log(data);
    //       },
    //     });
    //   });
    // });
</script>
@endsection