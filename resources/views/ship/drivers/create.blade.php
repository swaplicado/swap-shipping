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
                    <span>Nuevo chofer</span>
                </div>
                <div class="card-body">
                    <form onSubmit="document.getElementById('save').disabled=true; wait();"
                    action="{{ route('guardar_driver') }}" method="POST">
                        @csrf
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
            const check = document.getElementById('is_with_user');
            check.addEventListener('change', function handleChange(event){
                if(check.checked){
                    document.getElementById('email').removeAttribute('readonly');
                    document.getElementById('email').setAttribute('required', 'required');
                    document.getElementById('rol1').removeAttribute('disabled');
                    document.getElementById('rol2').removeAttribute('disabled');
                    document.getElementById('rol3').removeAttribute('disabled');
                    document.getElementById('password').removeAttribute('disabled');
                    document.getElementById('password-confirm').removeAttribute('disabled');

                    document.getElementById('divEmail').style.opacity = 1;
                    document.getElementById('div_rol').style.opacity = 1;
                    document.getElementById('div_pass').style.opacity = 1;
                    document.getElementById('div_confpass').style.opacity = 1;
                }else{
                    document.getElementById('email').setAttribute('readonly', 'readonly');
                    document.getElementById('email').removeAttribute('required');
                    document.getElementById('rol1').setAttribute('disabled', 'disabled');
                    document.getElementById('rol2').setAttribute('disabled', 'disabled');
                    document.getElementById('rol3').setAttribute('disabled', 'disabled');
                    document.getElementById('password').setAttribute('disabled', 'disabled');
                    document.getElementById('password-confirm').setAttribute('disabled', 'disabled');

                    document.getElementById('divEmail').style.opacity = 0.3;
                    document.getElementById('div_rol').style.opacity = 0.3;
                    document.getElementById('div_pass').style.opacity = 0.3;
                    document.getElementById('div_confpass').style.opacity = 0.3;
                }
            });

            const div = document.querySelector('#checkboxes');
            const checkboxes = div.querySelectorAll('input[type=radio]');
            const checkboxLength = checkboxes.length;
            const firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;

            function init() {
                if (firstCheckbox) {
                    for (let i = 0; i < checkboxLength; i++) {
                        checkboxes[i].addEventListener('change', checkValidity);
                    }

                    checkValidity();
                }
            }

            function isChecked() {
                for (let i = 0; i < checkboxLength; i++) {
                    if (checkboxes[i].checked) return true;
                }

                return false;
            }

            function checkValidity() {
                const errorMessage = !isChecked() ? 'Debe seleccionar una opción.' : '';
                firstCheckbox.setCustomValidity(errorMessage);
            }

            init();

        });
    </script>
@endsection