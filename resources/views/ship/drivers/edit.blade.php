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
                    <span>Editar chofer</span>
                </div>
                <div class="card-body">
                    <form onSubmit="document.getElementById('save').disabled=true; wait();"
                    action="{{ route('actualizar_driver', ['id' => $data->id_trans_figure]) }}" method="POST">
                        @csrf @method("put")
                        @include('ship.drivers.form')
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