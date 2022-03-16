@extends('layouts.principal')

@section('aside')
@include('layouts.aside')
@endsection

@section('nav-up')
@include('layouts.nav-up')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">{{ __('Registro') }}</div>

                <div class="card-body">
                    <form onSubmit="wait(); document.getElementById('registrar').disabled=true;"
                    method="POST" action="{{ route('guardar_user') }}">
                        @csrf

                        {{-- <div class="form-group row">
                            <label for="username" class="
                                text-md-right">{{ __('User name') }}</label>

                            <div class="">
                                <input id="username" type="text" class="form-control
                                    @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username') }}"
                                    required autocomplete="username" autofocus>
                                @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <p>Los campos marcados con un * son obligatorios.</p>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="full_name" class="text-md-right">{{ __('Nombre completo') }} *</label>
                            <div class="">
                                <input id="full_name" type="text" class="form-control uppercase 
                                    @error('full_name') is-invalid @enderror" 
                                    name="full_name" value="{{ old('full_name') }}" 
                                    required autocomplete="full_name">
                                @error('full_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="
                                text-md-right">{{ __('Email') }} *</label>
                            <div class="">
                                <input id="email" type="email"
                                    class="form-control @error('email')
                                    is-invalid @enderror" name="email" value="{{
                                    old('email') }}" required
                                    autocomplete="email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_type_id" class="
                                text-md-right">{{ __('Tipo de usuario') }} *</label>
                            <div class="">
                                <select class="form-select" id="user_type_id" name="user_type_id" type="integer" required>
                                    <option value="">Tipo de usuario</option>
                                    <option value="1">Admin</option>
                                    <option value="2">Remisionista</option>
                                </select>
                                @error('user_type_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="
                                text-md-right">{{ __('Contraseña') }} *</label>

                            <div class="">
                                <input id="password" type="password"
                                    class="form-control @error('password')
                                    is-invalid @enderror" name="password"
                                    required autocomplete="new-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password-confirm" class="
                                col-form-label text-md-right">{{ __('Confirmar contraseña') }} *</label>

                            <div class="">
                                <input id="password-confirm" type="password"
                                    class="form-control"
                                    name="password_confirmation" required
                                    autocomplete="new-password">
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <div class="">
                                <button id="registrar" type="submit" class="btn btn-primary">
                                    {{ __('Registrar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
