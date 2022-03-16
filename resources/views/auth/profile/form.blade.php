<div class="form-group">
    <label for="fullname" class="form-label">Nombre completo *</label>
    <input name="fullname" type="text" class="form-control" value="{{ old('fullname', $data->full_name ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}" required readonly>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
{!! session()->has('myProfile') ? session('myProfile') : "" !!}
<div class="form-group">
    <label for="new_password" class="form-label">Nueva contraseña</label>
    <input id="newPassword" name="newPassword" type="checkbox">
    <input id="new_password" name="new_password" type="password" class="form-control" readonly>
    @error('new_password')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="password_confirm" class="form-label">Confirmar contraseña</label>
    <input id="password_confirm" name="password_confirm" type="password" class="form-control" readonly>
    @error('password_confirm')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
<button type="submit" class="btn btn-primary">Guardar</button>