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
                    <span>Nuevo vehículo</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('guardar_vehicle') }}" method="POST">
                        @csrf
                        @include('ship.vehicles.form')
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
            const sel_carr = document.getElementById('select_carrier');
            sel_carr.addEventListener('change', function handleChange(event) {
                var value = sel_carr.value;
                pushInSelectInsurances(value);
            });
        });

        function pushInSelectInsurances(id){
            const sel_ins = document.getElementById('sel_insurances');
            var Insurances = '<?php echo json_encode($insurances) ?>';
            var obj = JSON.parse(Insurances);
            var options = '<select class="form-select" name="insurance"><option value="0" selected>Select Aseguradora</option>';
            for(var i = 0; i<obj.length; i++){
                if(obj[i].carrier_id == id){
                    options = options + '<option value="' + obj[i].id_insurance + '">'+ obj[i].full_name+'</option>';
                }
            }
            options = options + '</select>';
            sel_ins.innerHTML = options;
        };
    </script>
@endsection