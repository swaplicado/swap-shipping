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
    <h1 class="normal-text">Transportistas</h1>
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
                    <th>Nombre</th>
                    <th>RFC</th>
                    <th>RFC extrangero</th>
                    <th>Teléfono</th>
                    <th>Calle</th>
                    <th>No. Exterior</th>
                    <th>No. Interior</th>
                    <th>Colonia</th>
                    <th>Referencia</th>
                    <th>Localidad</th>
                    <th>Estado</th>
                    <th>Código postal</th>
                    <th>Usr nuevo</th>
                    <th>Ts nuevo</th>
                    <th>Usr mod</th>
                    <th>Ts mod</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Alejandro Avilés</td>
                    <td>jlassjdlkad</td>
                    <td></td>
                    <td>4525223239</td>
                    <td>ajkldkjlsa</td>
                    <td>5</td>
                    <td>12</td>
                    <td>aksldjasñ</td>
                    <td>alñsdñalsdkañsldkaslñdkalñsdkalñdkalñdvdafa</td>
                    <td>lkakffap</td>
                    <td>saldkñasl</td>
                    <td>65645</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                </tr>
                <tr>
                    <td>Alejandro Avilés</td>
                    <td>jlassjdlkad</td>
                    <td></td>
                    <td>4525223239</td>
                    <td>ajkldkjlsa</td>
                    <td>5</td>
                    <td>12</td>
                    <td>aksldjasñ</td>
                    <td>alñsdñalsdkañsldkaslñdkalñsdkalñdkalñdvdafa</td>
                    <td>lkakffap</td>
                    <td>saldkñasl</td>
                    <td>65645</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                    <td>super</td>
                    <td>29/12/2021</td>
                </tr>
                <tr>
                    <td>Alejandro Avilés</td>
                    <td>jlassjdlkad</td>
                    <td></td>
                    <td>4525223239</td>
                    <td>ajkldkjlsa</td>
                    <td>5</td>
                    <td>12</td>
                    <td>aksldjasñ</td>
                    <td>alñsdñalsdkañsldkaslñdkalñsdkalñdkalñdvdafa</td>
                    <td>lkakffap</td>
                    <td>saldkñasl</td>
                    <td>65645</td>
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

<!-- Chart library -->
<script src="./plugins/chart.min.js"></script>
<!-- Icons library -->
<script src="plugins/feather.min.js"></script>
<!-- Custom scripts -->
<script src="js/script.js"></script>

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
 
    $('#btn_delete').click( function () {
        table.row('.selected').remove().draw( false );
    } );
} );
</script>

@endsection