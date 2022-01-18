<div class="form-group">
    <label for="username" class="form-label">Nombre de usuario</label>
    <input name="username" type="text" class="form-control" value="{{ old('username', $data->User->username ?? '') }}">
    @error('username')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="fullname" class="form-label">Nombre</label>
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
<div class="form-group">
    <label for="RFC_ex" class="form-label">RFC extrangero</label>
    <input name="RFC_ex" type="text" class="form-control" value="{{ old('RFC_ex', $data->fiscal_fgr_id ?? '') }}">
    @error('RFC_ex')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="licence" class="form-label">Licencia</label>
    <input name="licence" type="text" class="form-control" value="{{ old('licence', $data->driver_lic ?? '') }}">
    @error('licence')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tp_figure" class="form-label">Tipo de figura</label>
    <select class="form-select" name="tp_figure">
        <option value="0" selected>Tipo de figura</option>
        @foreach($tp_figures as $tp => $index)
            @if($data->tp_figure_id == $index)
                <option selected value='{{$index}}'>{{$tp}}</option>
            @else
                <option value='{{$index}}'>{{$tp}}</option>
            @endif
        @endforeach
    </select>
    @error('tp_figure')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="carrier" class="form-label">Transportista</label>
    <select class="form-select" name="carrier">
        <option value="0" selected>Transportista</option>
        @foreach($carriers as $c => $index)
            @if($data->carrier_id == $index)
                <option selected value='{{$index}}'>{{$c}}</option>
            @else
                <option value='{{$index}}'>{{$c}}</option>
            @endif
        @endforeach
    </select>
    @error('carrier')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="country" class="form-label">País</label>
    <select class="form-select" name="country">
        <option value="0" selected>País</option>
        @foreach($countrys as $cty => $index)
        @if($data->fis_address_id == $index)
            <option selected value='{{$index}}'>{{$cty}}</option>
        @else
            <option value='{{$index}}'>{{$cty}}</option>
        @endif
        @endforeach
    </select>
    @error('country')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="zip_code" class="form-label">Código postal</label>
    <input name="zip_code" type="text" class="form-control" value="{{old('zip_code', $data->FAddress->zip_code ?? '')}}">
    @error('zip_code')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="state" class="form-label">Estado</label>
    <select class="form-select" name="state">
        <option value="0" selected>Estado</option>
        @foreach($states as $st => $index)
            @if($data->FAddress->state_id == $index)
                <option selected value='{"id":"{{$index}}","name":"{{$st}}"}'>{{$st}}</option>
            @else
                <option value='{"id":"{{$index}}","name":"{{$st}}"}'>{{$st}}</option>
            @endif
        @endforeach
    </select>
    @error('country')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="locality" class="form-label">Localidad</label>
    <input name="locality" type="text" class="form-control" value="{{old('locality', $data->FAddress->locality ?? '')}}">
    @error('locality')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="neighborhood" class="form-label">Colonia</label>
    <input name="neighborhood" type="text" class="form-control" value="{{old('neighborhood', $data->FAddress->neighborhood ?? '')}}">
    @error('neighborhood')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="street" class="form-label">Calle</label>
    <input name="street" type="text" class="form-control" value="{{old('street', $data->FAddress->street ?? '')}}">
    @error('street')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="street_num_ext" class="form-label">Numero exterior</label>
    <input name="street_num_ext" type="text" class="form-control" value="{{old('street_num_ext', $data->FAddress->street_num_ext ?? '')}}">
    @error('street_num_ext')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="street_num_int" class="form-label">Numero interior</label>
    <input name="street_num_int" type="text" class="form-control" value="{{old('street_num_int', $data->FAddress->street_num_int ?? '')}}">
    @error('street_num_int')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="reference" class="form-label">Referencia</label>
    <input name="reference" type="text" class="form-control" value="{{old('reference', $data->FAddress->reference ?? '')}}">
    @error('reference')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="telephone" class="form-label">Teléfono</label>
    <input name="telephone" type="text" class="form-control" value="{{old('telephone', $data->FAddress->telephone ?? '')}}">
    @error('telephone')
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