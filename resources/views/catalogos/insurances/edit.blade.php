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
                    <span>Editar aseguradora</span>
                </div>
                <div class="card-body">
                    <form onSubmit="document.getElementById('save').disabled=true; wait();"
                    action="{{ route('actualizar_insurance', ['id' => $data->id_insurance]) }}" method="POST">
                        @csrf @method("put")
                        @include('catalogos.insurances.form')
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
        const is_civ_resp = '<?php echo $data->is_civ_resp; ?>';
        const is_ambiental = '<?php echo $data->is_ambiental; ?>';
        const is_cargo = '<?php echo $data->is_cargo; ?>';

        if(is_civ_resp == 1){
            var checkbox1 = document.getElementById('checkbox1');
            checkbox1.setAttribute('checked', 'true');
        }
        if(is_ambiental == 1){
            var checkbox2 = document.getElementById('checkbox2');
            checkbox2.setAttribute('checked', 'true');
        }
        if(is_cargo == 1){
            var checkbox3 = document.getElementById('checkbox3');
            checkbox3.setAttribute('checked', 'true');
        }
    });
</script>
@endsection