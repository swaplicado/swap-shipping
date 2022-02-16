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
<h2>Municipio</h2>
<br>

<button id="btn_edit" type="button" class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;">
    <span class="icon bx bx-edit-alt"></span>
</button>
<br>
<br>

<div class="container table-responsive">
    <table id="T_municipalities" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>id</th>
                <th>Código del municipio</th>
                <th>Municipio</th>
                <th>Estado</th>
                <th>Distancia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{$d->id}}</td>
                <td>{{$d->key_code}}</td>
                <td>{{$d->municipality_name}}</td>
                <td>{{$d->state->state_name}}</td>
                <td>{{$d->distance}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {

        var table = $('#T_municipalities').DataTable({
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }
            ]
        });

        $('#T_municipalities tbody').on('click', 'tr', function () {
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
            var url = '{{route("editar_municipalitie", ":id")}}';
            url = url.replace(':id',id);
            window.location.href = url;
        });

    });
</script>
@endsection