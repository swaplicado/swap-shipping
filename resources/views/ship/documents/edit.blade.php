@extends('layouts.principal')

@section('headJs')
<script src="{{ asset('js/vue2/vue.js') }}"></script>
<script src="{{ asset('js/axios/axios.js') }}"></script>
<script src="{{ asset('js/numbro/numbro.min.js') }}"></script>
<script src="{{ asset('js/currency/currency.min.js') }}"></script>
<style>
    .editable-input {
        border: 1px solid 
                #0066ff
    }
</style>
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
        <div class="col-lg-11">
            <div class="card">
                <div class="card-header">
                    <span>CFDI 4.0 Carta Porte 2.0</span>
                </div>
                <div class="card-body" id="cfdi_app">
                    <form id="theForm">
                        <div>
                            @include('ship.documents.form')
                        </div>
                    </form>
                    <form id="sendForm" action="{{ route('documents.update', ['id' => $idDocument]) }}" method="POST">
                        @csrf @method("put")
                        <input type="hidden" name="the_cfdi_data" id="the_cfdi_data">
                    </form>
                </div>
                <div class="row">
                    <div class="col-9"></div>
                    <div class="col-3">
                        <button id="saveButton" onclick="onSave()" class="btn btn-success">Guardar</button>
                        <button type="reset" onclick="window.history.back();" class="btn btn-danger">Cancelar</button>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('ship.documents.edit_js')
    <script>
        function onSave() {
            let isValid = document.querySelector('#theForm').reportValidity();
            if (! isValid) {
                return;
            }

            let allValid = app.validateAll();
            if (! allValid) {
                return;
            }

            SGui.showWaiting(3000);
            app.setData();

            document.getElementById("saveButton").disabled = true;
            document.getElementById("sendForm").submit();
        }
    </script>
@endsection