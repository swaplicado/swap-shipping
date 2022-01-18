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
                    <form action="{{ route('guardar_driver') }}" method="POST">
                        @csrf
                        @include('ship.drivers.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection