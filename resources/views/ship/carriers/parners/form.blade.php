<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="fullname" class="form-label">Nombre completo *</label>
    <input name="fullname" type="text" class="form-control uppercase" value="{{ old('fullname', $data->full_name ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!is_null($data))
<div class="form-group">
    <label for="email" class="form-label">Email *</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}" readonly required>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@else
<div class="form-group">
    <label for="email" class="form-label">Email *</label>
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}" required>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@endif
@if(!$data)
<div class="form-group">
    <label for="password" class="form-label">{{ __('Contraseña') }} *</label>
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
    <label for="password-confirm" class="form-label">{{ __('Confirmar contraseña') }} *</label>
    <input id="password-confirm" type="password"
        class="form-control"
        name="password_confirmation" required
        autocomplete="new-password">
</div>
{!! is_null($data) ? (session()->has('form') ? session('form') : "") : "" !!}
@endif
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>