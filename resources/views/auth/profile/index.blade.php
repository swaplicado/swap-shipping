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
                    <span>Mi perfil</span>
                </div>
                <div class="card-body">
                    <form action="{{ route(session('route'), ['id' => $data->id]) }}" method="POST">
                        @csrf @method("put")
                        @include('auth.profile.form')
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
            const newpass = document.getElementById('newPassword');

            check.addEventListener('change', function handleChange(event){
                if(check.checked){
                    document.getElementById('email').removeAttribute('readonly');
                }else{
                    document.getElementById('email').setAttribute('readonly', 'readonly');
                }
            });

            newpass.addEventListener('change', function handleChange(event){
                if(newpass.checked){
                    document.getElementById('new_password').removeAttribute('readonly');
                    document.getElementById('password_confirm').removeAttribute('readonly');
                }else{
                    document.getElementById('new_password').setAttribute('readonly', 'readonly');
                    document.getElementById('password_confirm').setAttribute('readonly', 'readonly');
                }
            });
        });
    </script>
@endsection