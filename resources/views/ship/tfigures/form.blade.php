<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="fullname" class="form-label">Nombre *</label>
    <input name="fullname" type="text" class="form-control uppercase" value="{{ old('fullname', $data->fullname ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="RFC" class="form-label">RFC *</label>
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
    <label for="tp_figure" class="form-label">Tipo de figura de transporte *</label>
    <select class="form-select" name="tp_figure" required>
        <option value="" selected>Tipo de figura</option>
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
    <label for="country" class="form-label">País *</label>
    <select class="form-select" name="country" required>
        <option value="" selected>País</option>
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
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>