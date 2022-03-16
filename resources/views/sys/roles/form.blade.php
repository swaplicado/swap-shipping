<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="name" class="form-label">Nombre del rol *</label>
    <input name="name" type="text" class="form-control uppercase" value="{{ old('name', $data->name ?? '') }}" required>
    @error('name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="description" class="form-label">Descripción</label>
    <input name="description" type="text" class="form-control" value="{{ old('description', $data->description ?? '') }}" required>
    @error('description')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
<div class="form-group">
    <label for="permissions" class="form-label">Permisos</label>
    <button id="btn_add_permission" type="button" class="btn btn-primary btn-sm">
        <span class="icon bx bx-plus"></span>
    </button>
    <select id="permission_select" class="form-select" name="permissions" style="display: none" multiple aria-label="multiple select example">
        @foreach($permissions as $p)
            <option value='{"id":"{{$p->id}}","key_code":"{{$p->key_code}}","description":"{{$p->description}}"}'>{{$p->key_code}}: {{$p->description}}</option>
        @endforeach
    </select>
    <table class="table">
        <tbody id="tabla_permisos">
            @foreach ($data->RolePermissions as $rp)
                @foreach ($rp->Permission()->get() as $p)
                    <tr>
                        <td><input class = "input_permission" type="checkbox" checked="true" value="{{$p->id}}"></td>
                        <td>{{$p->key_code}}</td>
                        <td>{{$p->description}}</td> 
                    </tr> 
                @endforeach
            @endforeach
        </tbody>
    </table>
    <input id = "checkboxes" name = "checkboxes" type="hidden" >
</div>
<br>
<button id="submit" class="btn btn-primary">Guardar</button>