<div class="form-group">
    <label for="username" class="form-label">Nombre de usuario</label>
    <input name="username" type="text" class="form-control" value="{{ old('username', $data->User->username ?? '') }}">
    @error('username')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="fullname" class="form-label">Nombre completo</label>
    <input name="fullname" type="text" class="form-control" value="{{ old('fullname', $data->fullname ?? '') }}">
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input name="email" type="text" class="form-control" value="{{ old('email', $data->User->email ?? '') }}">
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="RFC" class="form-label">RFC</label>
    <input name="RFC" type="text" class="form-control" value="{{ old('RFC', $data->fiscal_id ?? '') }}">
    @error('RFC')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!$data->user)
<div class="form-group">
    <label for="password" class="form-label">{{ __('Password') }}</label>
    <input id="password" type="password"
        class="form-control @error('password')
        is-invalid @enderror" name="password"
        required autocomplete="new-password">
    @error('password')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>
<div class="form-group">
    <label for="password-confirm" class="form-label">{{ __('ConfirmPassword') }}</label>
    <input id="password-confirm" type="password"
        class="form-control"
        name="password_confirmation" required
        autocomplete="new-password">
</div>
@endif
<br>
<button type="submit" class="btn btn-primary">Guardar</button>