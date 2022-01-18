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
    <label for="user_type_id" class="form-label">Tipo de usuario</label>
    <select class="form-select" name="user_type_id">
        <option value="0" selected>Select subtipo</option>
        @foreach($userType as $usrt => $index)
            @if($data->user_type_id == $index)
                <option selected value='{{$index}}'>{{$usrt}}</option>
            @else
                <option value='{{$index}}'>{{$usrt}}</option>
            @endif
        @endforeach
    </select>
    @error('user_type_id')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<br>
<button type="submit" class="btn btn-primary">Guardar</button>