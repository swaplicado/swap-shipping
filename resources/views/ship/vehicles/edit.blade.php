@extends('layouts.principal')

@section('aside')
@include('layouts.aside')
@endsection

@section('nav-up')
@include('layouts.nav-up')
@endsection

@section('headJs')
    <script src="{{ asset('js/vue2/vue.js') }}"></script>
    <script src="{{ asset('js/axios/axios.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-md-center" id="vehiclesApp">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <span>Editar vehículo</span>
                </div>
                <div class="card-body">
                    <form id="form_veh" onSubmit="document.getElementById('save').disabled=true; wait();"
                    action="{{ route('actualizar_vehicle', ['id' => $data->id_vehicle]) }}" method="POST">
                        @csrf @method("put")
                        @include('ship.vehicles.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@include('ship.vehicles.js')
@endsection