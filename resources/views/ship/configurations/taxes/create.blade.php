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
                    <span>Nueva configuración</span>
                </div>
                <div class="card-body">
                    <form onSubmit="wait(); document.getElementById('save').disabled=true;"
                    action="{{ route('config.taxes.store') }}" method="POST">
                        @csrf
                        @include('ship.configurations.taxes.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection