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
    <h1 class="normal-text">Home</h1>
    <br>
    <button type="button" class="btn btn-success" style="border-radius: 50%; padding: 5px 10px;">
        <span class="icon bx bx-plus"></span>
    </button>
    <button type="button" class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;">
        <span class="icon bx bx-edit-alt"></span>
    </button>
    <button id="btn_delete" type="button" class="btn btn-danger" style="border-radius: 50%; padding: 5px 10px;">
        <span class="icon bx bx-trash"></span>
    </button>
    <br>
    <br>
    <div class="container table-responsive">
        <table id="example" class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                    <th>CFD</th>
                    <th>Transporte internacional</th>
                    <th>E/S</th>
                    <th>Vía E/S</th>
                    <th>Distancia total</th>
                    <th>Vehículo</th>
                    <th>Placa vehículo</th>
                    <th>Remolque 1</th>
                    <th>Placa remolque 1</th>
                    <th>Remolque 2</th>
                    <th>Placa remolque 2</th>
                    <th>Usr nuevo</th>
                    <th>Ts nuevo</th>
                    <th>Usr mod</th>
                    <th>Ts mod</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>9560</td>
                    <td>29/12/2021</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>350</td>
                    <td>camion1</td>
                    <td>abc12345</td>
                    <td>remolque1</td>
                    <td>bcd78955</td>
                    <td></td>
                    <td></td>
                    <td>super</td>
                    <td>29/12/2021</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                </tr>
                <tr>
                    <td>9560</td>
                    <td>29/12/2021</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>350</td>
                    <td>camion1</td>
                    <td>abc12345</td>
                    <td>remolque1</td>
                    <td>bcd78955</td>
                    <td></td>
                    <td></td>
                    <td>super</td>
                    <td>29/12/2021</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                </tr>
                <tr>
                    <td>9560</td>
                    <td>29/12/2021</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>350</td>
                    <td>camion1</td>
                    <td>abc12345</td>
                    <td>remolque1</td>
                    <td>bcd78955</td>
                    <td></td>
                    <td></td>
                    <td>super</td>
                    <td>29/12/2021</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {
    var table = $('#example').DataTable();
 
    $('#example tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );

    $('#maincontainer').click(function() {
        table.$('tr.selected').removeClass('selected');
    });
 
    $('#btn_delete').click( function () {
        table.row('.selected').remove().draw( false );
    } );
} );
</script>

@endsection