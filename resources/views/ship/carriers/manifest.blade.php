
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
                    <span>Manifiesto Finkok</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p style="text-align: justify">{{ $privacy }}</p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <p style="text-align: justify">{{ $contract }}</p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('config.certificates.sign_manifest', $idcarrier) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="">Contraseña CPT:</label>
                                            <input type="password"
                                            class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                                            <small id="helpId" class="form-text text-muted">Contraseña de sistema de Cartas Porte</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="">Firmar</label>
                                            <button type="submit" class="btn btn-primary">Firmar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection