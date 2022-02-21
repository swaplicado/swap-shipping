<div class="form-check">
    <input class="form-check-input" type="checkbox" name="is_with_user" id="is_with_user" checked>
    <label class="form-check-label" for="is_with_user">
      Crear usuario para el chofer
    </label>
</div>
<div class="form-group">
    <label for="fullname" class="form-label">Nombre</label>
    <input name="fullname" type="text" class="form-control uppercase" value="{{ old('fullname', $data->fullname ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@if(!is_null($data->users))
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->users->email ?? '') }}" readonly required>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
@else
<div class="form-group">
    <label for="email" class="form-label">E-mail</label>
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
    <label for="RFC_ex" class="form-label">RFC extranjero</label>
    <input name="RFC_ex" type="text" class="form-control uppercase" value="{{ old('RFC_ex', $data->fiscal_fgr_id ?? '') }}">
    @error('RFC_ex')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="licence" class="form-label">Licencia</label>
    <input name="licence" type="text" class="form-control uppercase" value="{{ old('licence', $data->driver_lic ?? '') }}" required>
    @error('licence')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tp_figure" class="form-label">Tipo de figura de transporte</label>
    <select class="form-select" name="tp_figure">
        <option value="0" selected>Tipo de figura</option>
        @foreach($tp_figures as $tp => $index)
            @if($data->tp_figure_id == $index)
                <option selected value='{{$index}}'>{{$tp}}</option>
            @else
                @if (1 == $index)
                    <option selected value='{{$index}}'>{{$tp}}</option>
                @else
                    <option value='{{$index}}'>{{$tp}}</option>
                @endif
            @endif
        @endforeach
    </select>
    @error('tp_figure')
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
            @if (251 == $index)
                <option selected value='{{$index}}'>{{$cty}}</option>
            @else
                <option value='{{$index}}'>{{$cty}}</option>
            @endif
        @endif
        @endforeach
    </select>
    @error('country')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="zip_code" class="form-label">Código postal</label>
    <input name="zip_code" type="text" class="form-control" value="{{old('zip_code', $data->FAddress->zip_code ?? '')}}" required>
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
    @error('state')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="locality" class="form-label">Localidad</label>
    <input name="locality" type="text" class="form-control uppercase" value="{{old('locality', $data->FAddress->locality ?? '')}}">
    @error('locality')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="neighborhood" class="form-label">Colonia</label>
    <input name="neighborhood" type="text" class="form-control uppercase" value="{{old('neighborhood', $data->FAddress->neighborhood ?? '')}}">
    @error('neighborhood')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="street" class="form-label">Calle</label>
    <input name="street" type="text" class="form-control uppercase" value="{{old('street', $data->FAddress->street ?? '')}}">
    @error('street')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="street_num_ext" class="form-label">Numero exterior</label>
    <input name="street_num_ext" type="text" class="form-control uppercase" value="{{old('street_num_ext', $data->FAddress->street_num_ext ?? '')}}">
    @error('street_num_ext')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="street_num_int" class="form-label">Numero interior</label>
    <input name="street_num_int" type="text" class="form-control uppercase" value="{{old('street_num_int', $data->FAddress->street_num_int ?? '')}}">
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
{!! $data->id_trans_figure == null ? (session()->has('form') ? session('form') : "") : "" !!}
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