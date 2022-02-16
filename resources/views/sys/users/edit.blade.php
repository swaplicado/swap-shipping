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
                    <span>Editar usuario</span>
                </div>
                <div class="card-body">
                    @foreach($data as $data)
                    <form action="{{ route('actualizar_user', ['id' => $data->id]) }}" method="POST">
                        @csrf @method("put")
                        @include('sys.users.form')
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const select = document.getElementById('role_select');
            const roles = document.getElementsByClassName('input_role');
            const check = document.getElementById('editEmail');
            
            check.addEventListener('change', function handleChange(event){
                if(check.checked){
                    document.getElementById('email').removeAttribute('readonly');
                }else{
                    document.getElementById('email').setAttribute('readonly', 'readonly');
                }
            });
            
            $('#btn_add_role').click( function () {
                select.style = "display: block";
            });

            select.addEventListener('change', function handleChange(event) {
                var table = document.getElementById('tabla_roles');
                var tr = document.createElement('tr');
                var td = document.createElement('td');
                var value = select.value;
                var obj = JSON.parse(value);
                select.disable = true;
                var checked = false;
                for(var i=0; i<roles.length; i++){
                    if(roles[i].value == obj.id){
                        checked = true;                        
                    }
                }

                if(!checked){
                    var input = document.createElement('input');
                    input.setAttribute('type', 'checkbox');
                    input.setAttribute('class', 'input_role');
                    input.setAttribute('value', obj.id);
                    input.setAttribute('checked', 'true');
                    
                    td.appendChild(input);
                    tr.appendChild(td);

                    td = document.createElement('td');
                    td.appendChild(document.createTextNode(obj.name));
                    tr.appendChild(td);

                    td = document.createElement('td');
                    td.appendChild(document.createTextNode(obj.description));
                    tr.appendChild(td);

                    table.appendChild(tr);

                    select.style = "display: none";
                }
            });

            $('#submit').click( function () {
                var checkboxes = document.getElementsByClassName('input_role');
                var arr = [];
                for(var i=0; roles[i]; ++i){
                    arr.push('{' + '"role":' + roles[i].value + ',"checked":' + roles[i].checked + '}');          
                }
                var input = document.getElementById('checkboxes');
                input.setAttribute('value', '{"roles":[' + arr + ']}');
            });
        });
    </script>
@endsection