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
                    <span>Nuevo rol</span>
                </div>
                <div class="card-body">
                    <form onSubmit="document.getElementById('submit').disabled=true; wait();"
                    action="{{ route('guardar_role') }}" method="POST">
                        @csrf
                        @include('sys.roles.form')
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
            const select = document.getElementById('permission_select');
            const permissions = document.getElementsByClassName('input_permission');
            
            $('#btn_add_permission').click( function () {
                select.style = "display: block";
            });

            select.addEventListener('change', function handleChange(event) {
                var table = document.getElementById('tabla_permisos');
                var tr = document.createElement('tr');
                var td = document.createElement('td');
                var value = select.value;
                select.disable = true;
                var obj = JSON.parse(value);

                var checked = false;
                for(var i=0; permissions[i]; ++i){
                    if(permissions[i].value == obj.key_code){
                        checked = true;                        
                    }
                }

                if(!checked){
                    select.disable = true;
                    var input = document.createElement('input');
                    input.setAttribute('type', 'checkbox');
                    input.setAttribute('class', 'input_permission');
                    input.setAttribute('value', obj.id);
                    input.setAttribute('checked', 'true');
                    
                    td.appendChild(input);
                    tr.appendChild(td);

                    td = document.createElement('td');
                    td.appendChild(document.createTextNode(obj.key_code));
                    tr.appendChild(td);

                    td = document.createElement('td');
                    td.appendChild(document.createTextNode(obj.description));
                    tr.appendChild(td);

                    table.appendChild(tr);

                    select.style = "display: none";
                }
            });

            $('#submit').click( function () {
                var checkboxes = document.getElementsByClassName('input_permission');
                var arr = [];
                for(var i=0; permissions[i]; ++i){
                    arr.push('{' + '"permission":' + permissions[i].value + ',"checked":' + permissions[i].checked + '}');          
                }
                var input = document.getElementById('checkboxes');
                input.setAttribute('value', '{"permissions":[' + arr + ']}');
            });
        });
    </script>
@endsection