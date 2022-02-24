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
            Bienvenido al sistema de emisión CFDI Carta Porte 
          </figcaption>
        </figure>
      </div>
    </div>
  </div>

  <br>
    <h3>Guía rápida</h3>
  <br>

  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 150pt;">
          <h5 class="card-title">Navegación</h5>
          <p class="card-text">
            A la izquierda de su pantalla encontrará un menu de color azul con el que podrá navegar por las
            distintas vistas del sistema, solo debe presionar una vez el boton de la sección a la que desea
            acceder y sera redirigido a dicha sección. El menu se pude guardar o dezplegar a gusto del usuario
            solo debe presionar el boton 
            <button class="btn-primary" type="button" >
              <span class="icon menu-toggle"></span>
            </button> para guardar o dezplegar el menu.
          </p> 
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 150pt;">
          <h5 class="card-title">Creación de registros</h5>
          <p class="card-text">
            En las distintas vistas como vehículos, remolques, choferes, aseguradoras y cartas porte, se incluye
            en la parte superior un boton del tipo 
            <button class="btn btn-success" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-plus"></span>
            </button>
            al presionar este boton se te redirigirá a una vista con un formulario para llenar los datos que deseas registrar.
          </p>
        </div>
      </div>
    </div>
  </div>
  <br>

  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 150pt;">
          <h5 class="card-title">Creación de registros</h5>
          <p class="card-text">
            En las distintas vistas como vehículos, remolques, choferes, aseguradoras y cartas porte, se incluye
            una tabla con los distintos registros que se han guardado, para editar un registro solo debes presionar
            una vez sobre el renglon del registros que deseas editar, el renglon se oscurecera señalando esto que ha sido
            seleccionado, una vez seleccionado el renglon en la parte superior izquierda se incluye
            un boton del tipo 
            <button class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-edit-alt"></span>
            </button>
            al presionar este boton se te redirigirá a una vista con los datos que deseas editar.
          </p> 
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 150pt;">
          <h5 class="card-title">Eliminación de registros</h5>
          <p class="card-text">
            En las distintas vistas como vehículos, remolques, choferes, aseguradoras y cartas porte, se incluye
            una tabla con los distintos registros que se han guardado, para borrar un registro solo debes presionar
            una vez sobre el renglon del registros que deseas borrar, el renglon se oscurecera señalando esto que ha sido
            seleccionado, una vez seleccionado el renglon en la parte superior izquierda se incluye
            un boton del tipo 
            <button class="btn btn-danger" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-trash"></span>
            </button>
            al presionar este boton se te preguntará si deseas elimiar el registro, al aceptar, el registro se eliminará.
          </p>
        </div>
      </div>
    </div>
  </div>
  <br>

  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body" style="height: 150pt;">
          <h5 class="card-title">recuperación de registros</h5>
          <p class="card-text">
            En las distintas vistas como vehículos, remolques, choferes, aseguradoras y cartas porte, se incluye
            una tabla con los distintos registros que se han guardado, para editar un registro solo debes presionar
            una vez sobre el renglon del registros que deseas editar, el renglon se oscurecera señalando esto que ha sido
            seleccionado, una vez seleccionado el renglon en la parte superior izquierda se incluye
            un boton del tipo 
            <button class="btn btn-info" style="border-radius: 50%; padding: 5px 10px;" >
              <span class="icon bx bx-recycle"></span>
            </button>
            al presionar este boton se te preguntará si deseas recuperar el registrp, al aceptar, el registro se recuperará.
          </p> 
        </div>
      </div>
    </div>
  </div>
  <br>

  {!! session()->has('home') ? session('home') : "" !!}
  
@endsection