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
  <div class="container">
    <div class="row">
      <div class="col">
      </div>
      <div class="col-md-8">
        <figure>
          <blockquote class="blockquote">
            <h1>Bienvenido a CPT</h1>
          </blockquote>
          <figcaption class="blockquote-footer">
            Bienvenido a Cartas Porte de Transportistas
          </figcaption>
        </figure>
      </div>
    </div>
  </div>

  @if ($showPanel)
    <br>
      <h3>Panel de tareas</h3>
    <br>
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <a href="{{ route('documents', 1) }}"><i class='bx bxs-hand-right bx-tada'>
              </i><label for="">Cartas porte por procesar</label>
              <input style="text-align: center;" type="text" class="form-control" value="{{ $pendingDocuments }}" aria-describedby="helpId" readonly>
            </a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <a href="{{ route('documents', 2) }}">
              <i class='bx bxs-hand-right bx-tada'></i><label for="">Cartas porte por timbrar</label>
              <input style="text-align: center" type="text" class="form-control" value="{{ $processedDocuments }}" aria-describedby="helpId" readonly>
            </a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <a href="{{ route('documents', 3) }}">
              <i class='bx bxs-hand-right bx-tada'></i><label for="">Cartas porte timbradas</label>
              <input style="text-align: center" type="text" class="form-control" value="{{ $stampedDocuments }}" aria-describedby="helpId" readonly>
            </a>
          </div>
        </div>
      </div>
    </div>

    <br>
    <hr>
  @endif

  <br>
    <h3>Gu??a r??pida</h3>
  <br>

  {!! session()->has('home') ? session('home') : "" !!}

  <br>
  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 250px; text-align: justify;">
          <h5 class="card-title">Navegaci??n</h5>
          <p class="card-text">
            A la izquierda de tu pantalla encontrar??s un men?? de color azul con el que podr??s navegar por 
            las distintas vistas del sistema, solo debes presionar una vez el bot??n de la secci??n a la que 
            deseas acceder y ser??s redirigido a dicha secci??n. El men?? se puede guardar o desplegar a gusto 
            del usuario, solamente debe presionar el bot??n
            <button class="btn-primary" type="button" style="background-color: #0f2e52;" >
              <span class="icon menu-toggle"></span>
            </button> para guardar o desplegar el men??.
          </p> 
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 250px; text-align: justify;">
          <h5 class="card-title">Creaci??n de registros</h5>
          <p class="card-text">
            En las distintas vistas como veh??culos, remolques, choferes, aseguradoras y cartas porte, se incluye
            en la parte superior un bot??n del tipo 
            <button class="btn btn-success" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-plus"></span>
            </button>
            al presionar este bot??n se te redirigir?? a una vista con un formulario para llenar los datos que deseas registrar.
          </p>
        </div>
      </div>
    </div>
  </div>
  <br>

  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 250px; text-align: justify;">
          <h5 class="card-title">Edici??n de registros</h5>
          <p class="card-text">
            En las distintas vistas como veh??culos, remolques, choferes, aseguradoras y cartas porte, 
            se incluye una tabla con los distintos registros que se han guardado, para editar un registro 
            solo debes presionar una vez sobre el rengl??n del registro que deseas editar, el rengl??n se 
            oscurecer?? se??alando esto que ha sido seleccionado, una vez seleccionado el rengl??n en la parte 
            superior izquierda se incluye un bot??n del tipo 
            <button class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-edit-alt"></span>
            </button>
            al presionar este bot??n se te redirigir?? a una vista con los datos que deseas editar.
          </p> 
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 250px; text-align: justify;">
          <h5 class="card-title">Eliminaci??n de registros</h5>
          <p class="card-text">
            En las distintas vistas como veh??culos, remolques, choferes, aseguradoras y cartas porte, 
            se incluye una tabla con los distintos registros que se han guardado, para borrar un registro 
            solo debes presionar una vez sobre el rengl??n del registro que deseas borrar, el rengl??n se 
            oscurecer?? se??alando esto que ha sido seleccionado, una vez seleccionado el rengl??n en la 
            parte superior izquierda se incluye un bot??n del tipo
            <button class="btn btn-danger" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-trash"></span>
            </button>
            al presionar este bot??n se te preguntar?? si deseas eliminar el registro, al aceptar, el registro se eliminar??.
          </p>
        </div>
      </div>
    </div>
  </div>
  <br>

  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 250px; text-align: justify;">
          <h5 class="card-title">Recuperaci??n de registros</h5>
          <p class="card-text">
            En las distintas vistas como veh??culos, remolques, choferes, aseguradoras y cartas porte, 
            se incluye una tabla con los distintos registros que se han guardado, para editar un registro 
            solo debes presionar una vez sobre el rengl??n del registro que deseas editar, el rengl??n se 
            oscurecer?? se??alando esto que ha sido seleccionado, una vez seleccionado el rengl??n en la parte 
            superior izquierda se incluye un bot??n del tipo 
            <button class="btn btn-info" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-recycle"></span>
            </button>
            al presionar este bot??n se te preguntar?? si deseas recuperar el registro, al aceptar, el registro se recuperar??.
          </p> 
        </div>
      </div>
    </div>
  </div>
  <br>
  
@endsection