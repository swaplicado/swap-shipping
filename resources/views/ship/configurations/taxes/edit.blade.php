@extends('layouts.principal')

@section('aside')
@include('layouts.aside')
@endsection

@section('nav-up')
@include('layouts.nav-up')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <span>Editar configuración de impuesto</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('config.taxes.update', $oCfg->id_config) }}" method="POST">
                        @csrf @method("put")
                        @include('ship.configurations.taxes.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection