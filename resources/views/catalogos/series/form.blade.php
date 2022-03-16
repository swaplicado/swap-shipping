<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="serie_name" class="form-label">Nombre de la serie *</label>
    <input name="serie_name" type="text" class="form-control uppercase" value="{{ old('serie_name', $data->serie_name ?? '') }}" required>
    @error('serie_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="prefix" class="form-label">Prefijo *</label>
    <input name="prefix" type="text" class="form-control uppercase" value="{{ old('prefix', $data->prefix ?? '') }}" required>
    @error('prefix')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="initial_number" class="form-label">Numero de inicio *</label>
    <input name="initial_number" type="number" class="form-control" step="1" min="1" value="{{ old('initial_number', $data->initial_number ?? '') }}" required>
    @error('initial_number')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="description" class="form-label">Descripción *</label>
    <input name="description" type="text" class="form-control" value="{{ old('description', $data->description ?? '') }}" required>
    @error('description')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
{!! $data->id_serie == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>