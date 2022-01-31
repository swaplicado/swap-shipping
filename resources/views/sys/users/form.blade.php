<div class="form-group">
    <label for="username" class="form-label">Usuario</label>
    <input name="username" type="text" class="form-control" value="{{ old('username', $data->username ?? '') }}">
    @error('username')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="full_name" class="form-label">Nombre completo</label>
    <input name="full_name" type="text" class="form-control" value="{{ old('full_name', $data->full_name ?? '') }}">
    @error('full_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}">
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="roles" class="form-label">Roles</label>
    <button id="btn_add_role" type="button" class="btn btn-primary btn-sm">
        <span class="icon bx bx-plus"></span>
    </button>
    <select id="role_select" class="form-select" name="roles" style="display: none" multiple aria-label="multiple select example">
        @foreach($roles as $r)
            <option value='{"id":"{{$r->id}}","name":"{{$r->name}}","description":"{{$r->description}}"}'>{{$r->name}}: {{$r->description}}</option>
        @endforeach
    </select>
    <table class="table">
        <tbody id="tabla_roles">
            @foreach ($data->getRoles as $rol)
                <tr>
                    <td><input class = "input_role" type="checkbox" checked="true" value="{{$rol->id}}"></td>
                    <td>{{$rol->name}}</td>
                    <td>{{$rol->description}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <input id = "checkboxes" name = "checkboxes" type="hidden" >
</div>
<br>
<button id="submit" class="btn btn-primary">Guardar</button>