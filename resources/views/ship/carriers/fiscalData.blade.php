@extends('layouts.principal')

@section('aside')
@include('layouts.aside')
@endsection

@section('nav-up')
@include('layouts.nav-up')
@endsection

@section('content')
@if(session('message'))
    <script>
        msg = "<?php echo session('message'); ?>";
        myIcon = "<?php echo session('icon'); ?>"

        Swal.fire({
            icon: myIcon,
            title: msg
        })
    </script>
@endif
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <span>Editar mis datos fiscales</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('actualizar_carrierFiscalData', ['id' => $data->id_carrier]) }}" method="POST">
                        @csrf @method("put")
                        <div class="form-group">
                            <label for="RFC" class="form-label">RFC</label>
                            <input name="RFC" type="text" class="form-control" value="{{ old('RFC', $data->fiscal_id ?? '') }}">
                            @error('RFC')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tax_regimes" class="form-label">Regimen fiscal</label>
                            <select class="form-select" name="tax_regimes">
                                <option value="0" selected>Regimen fiscal</option>
                                @foreach($tax_regimes as $tr => $index)
                                    @if($data->tax_regime->id == $index)
                                        <option selected value='{"id":"{{$index}}","name":"{{$tr}}"}'>{{$tr}}</option>
                                    @else
                                        <option value='{"id":"{{$index}}","name":"{{$tr}}"}'>{{$tr}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('tax_regimes')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="prod_serv" class="form-label">Concepto</label>
                            <select class="form-select" name="prod_serv">
                                <option value="0" selected>Concepto</option>
                                @foreach($prod_serv as $ps => $index)
                                    @if($data->prod_serv->id == $index)
                                        <option selected value='{"id":"{{$index}}","name":"{{$ps}}"}'>{{$ps}}</option>
                                    @else
                                        <option value='{"id":"{{$index}}","name":"{{$ps}}"}'>{{$ps}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('prod_serv')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-10"></div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
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
                                <input type="hidden" name="carrier" value="{{ $data->id_carrier }}">
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
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-striped table-inverse table-responsive">
                                        <thead class="thead-inverse">
                                            <tr>
                                                <th># Certificado</th>
                                                <th>Válido desde</th>
                                                <th>Válido hasta</th>
                                                <th>Creado por</th>
                                            </tr>
                                        </thead>
                                            <tbody>
                                                @foreach ($certificates as $cert)
                                                    <tr>
                                                        <td>{{ $cert->cert_number }}</td>
                                                        <td>{{ $cert->dt_valid_from }}</td>
                                                        <td>{{ $cert->dt_valid_to }}</td>
                                                        <td>{{ $cert->unew_username }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const check = document.getElementById('editEmail');
            check.addEventListener('change', function handleChange(event){
                if(check.checked){
                    document.getElementById('email').removeAttribute('readonly');
                }else{
                    document.getElementById('email').setAttribute('readonly', 'readonly');
                }
            });
        });
    </script>
@endsection