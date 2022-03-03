<div class="form-group">
    <label for="fullname" class="form-label">Nombre completo</label>
    <input name="fullname" type="text" class="form-control uppercase" value="{{ old('fullname', $data->fullname ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="comercial_name" class="form-label">Nombre comercial</label>
    <input name="comercial_name" type="text" class="form-control uppercase" value="{{ old('comercial_name', $data->comercial_name ?? '') }}">
    @error('comercial_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!is_null($data->users))
<div class="form-group">
    <label for="email" class="form-label">Email</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->users->email ?? '') }}" readonly required>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@else
<div class="form-group">
    <label for="email" class="form-label">Email</label>
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->users->email ?? '') }}" required>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@endif
<div class="form-group">
    <label for="RFC" class="form-label">RFC</label>
    <input name="RFC" type="text" class="form-control uppercase" value="{{ old('RFC', $data->fiscal_id ?? '') }}" required>
    @error('RFC')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tax_regimes" class="form-label">Régimen fiscal</label>
    <select class="form-select" name="tax_regimes">
        <option value="0" selected>Régimen fiscal</option>
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
    <label for="contact1" class="form-label">Nombre de contacto 1</label>
    <input name="contact1" type="text" class="form-control uppercase" value="{{ old('contact1', $data->contact1 ?? '') }}" required>
    @error('contact1')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="telephone1" class="form-label">Teléfono de contacto 1</label>
    <input name="telephone1" type="text" class="form-control uppercase" value="{{ old('telephone1', $data->telephone1 ?? '') }}" required>
    @error('telephone1')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="contact2" class="form-label">Nombre de contacto 2</label>
    <input name="contact2" type="text" class="form-control uppercase" value="{{ old('contact2', $data->contact2 ?? '') }}">
    @error('contact2')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="telephone2" class="form-label">Teléfono de contacto 2</label>
    <input name="telephone2" type="text" class="form-control uppercase" value="{{ old('telephone2', $data->telephone2 ?? '') }}">
    @error('telephone2')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!$data->users)
<br>
<div class="form-check">
    @if(!$data->carrier_stamp)
        <input name="carrier_stamp" class="form-check-input" type="checkbox" value="1" id="carrier_stamp">
    @else
        <input name="carrier_stamp" class="form-check-input" type="checkbox" value="1" id="carrier_stamp" checked>
    @endif
    <label class="form-check-label" for="carrier_stamp">
        El transportista realizará el timbrado de sus CFDI Carta Porte.
    </label>
</div>
<br>
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