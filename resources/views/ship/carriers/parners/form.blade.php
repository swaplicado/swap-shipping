<div class="form-group">
    <label for="fullname" class="form-label">Nombre completo</label>
    <input name="fullname" type="text" class="form-control" value="{{ old('fullname', $data->full_name ?? '') }}">
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!is_null($data))
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}" readonly>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@else
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}">
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@endif
@if(!$data)
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
{!! is_null($data) ? (session()->has('form') ? session('form') : "") : "" !!}
@endif
<br>
<button type="submit" class="btn btn-primary">Guardar</button>