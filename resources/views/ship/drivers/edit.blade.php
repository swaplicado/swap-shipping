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
                    <span>Nuevo chofer</span>
                </div>
                <div class="card-body">
                    @foreach($data as $data)
                    <form action="{{ route('actualizar_driver', ['id' => $data->id_trans_figure]) }}" method="POST">
                        @csrf @method("put")
                        @include('ship.drivers.form')
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection