@extends('layouts.principal')

@section('aside')
@include('layouts.aside')
@endsection

@section('nav-up')
@include('layouts.nav-up')
@endsection

@section('content')
@if(session('mesage'))
    <script>
        msg = "<?php echo session('mesage'); ?>";
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
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
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