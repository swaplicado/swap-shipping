@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verificar tu correo electroninco') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Un nuevo link de verificación a sido enviado a tu correo electronico.') }}
                        </div>
                    @endif

                    {{ __('Antes de proceder, por favor revisa tu correo electronico para el link de verificación.') }}
                    {{ __('Si no has recivido el correo') }}, <a href="{{ route('verification.resend') }}">{{ __('click aqui para enviar otro') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
