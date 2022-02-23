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
    <div class="row justify-content-md-center">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <span>Configuración de certificados</span>
                        </div>
                        <div class="card-body">
                            <form id="regForm" action="{{ route('config.certificates.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="pc">Archivo .cer</label>
                                    <input type="file" accept=".cer" id="pc" name="pc" class="form-control" required/>
                                </div>

                                <div class="form-group">
                                    <label for="pv">Archivo .key</label>
                                    <input type="file" accept=".key" id="pv" name="pv" class="form-control" required/>
                                </div>

                                <div class="form-group">
                                    <label for="pw">Contraseña</label>
                                    <input type="password" id="pw" name="pw" class="form-control" placeholder="Contraseña de certificados" required/>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-10"></div>
                                    <div class="col-2">
                                        <button class="btn btn-primary" type="submit">Registrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12">
                    <div class="card text-center">
                      <div class="card-header">
                            <span>Certificado Actual:</span>
                      </div>
                      <div class="card-body">
                       
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection