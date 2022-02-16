<div class="form-group">
    <label for="fullname" class="form-label">Nombre completo</label>
    <input name="fullname" type="text" class="form-control" value="{{ old('fullname', $data->fullname ?? '') }}">
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!is_null($data->users))
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->users->email ?? '') }}" readonly>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@else
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->users->email ?? '') }}">
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@endif
<div class="form-group">
    <label for="RFC" class="form-label">RFC</label>
    <input name="RFC" type="text" class="form-control" value="{{ old('RFC', $data->fiscal_id ?? '') }}">
    @error('RFC')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tax_regimes" class="form-label">Regimen fiscal</label>
    <select class="form-select" name="tax_regimes">
        <option value="0" selected>Regimen fiscal</option>
        @foreach($tax_regimes as $tr => $index)
            @if($data->tax_regime->id == $index)
                <option selected value='{"id":"{{$index}}","name":"{{$tr}}"}'>{{$tr}}</option>
            @else
                <option value='{"id":"{{$index}}","name":"{{$tr}}"}'>{{$tr}}</option>
            @endif
        @endforeach
    </select>
    @error('tax_regimes')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="prod_serv" class="form-label">Concepto</label>
    <select class="form-select" name="prod_serv">
        <option value="0" selected>Concepto</option>
        @foreach($prod_serv as $ps => $index)
            @if($data->prod_serv->id == $index)
                <option selected value='{"id":"{{$index}}","name":"{{$ps}}"}'>{{$ps}}</option>
            @else
                <option value='{"id":"{{$index}}","name":"{{$ps}}"}'>{{$ps}}</option>
            @endif
        @endforeach
    </select>
    @error('prod_serv')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="contact1" class="form-label">Contacto 1</label>
    <input name="contact1" type="text" class="form-control" value="{{ old('contact1', $data->contact1 ?? '') }}">
    @error('contact1')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="telephone1" class="form-label">Teléfono 1</label>
    <input name="telephone1" type="text" class="form-control" value="{{ old('telephone1', $data->telephone1 ?? '') }}">
    @error('telephone1')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="contact2" class="form-label">Contacto 2</label>
    <input name="contact2" type="text" class="form-control" value="{{ old('contact2', $data->contact2 ?? '') }}">
    @error('contact2')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="telephone2" class="form-label">Teléfono 2</label>
    <input name="telephone2" type="text" class="form-control" value="{{ old('telephone2', $data->telephone2 ?? '') }}">
    @error('telephone2')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!$data->users)
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