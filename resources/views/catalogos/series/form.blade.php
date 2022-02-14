<div class="form-group">
    <label for="serie_name" class="form-label">Nombre de la serie</label>
    <input name="serie_name" type="text" class="form-control" value="{{ old('serie_name', $data->serie_name ?? '') }}">
    @error('serie_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="prefix" class="form-label">Prefijo</label>
    <input name="prefix" type="text" class="form-control" value="{{ old('prefix', $data->prefix ?? '') }}">
    @error('prefix')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="initial_number" class="form-label">Numero de inicio</label>
    <input name="initial_number" type="number" class="form-control" step="1" min="1" value="{{ old('initial_number', $data->initial_number ?? '') }}">
    @error('initial_number')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="description" class="form-label">Descripción</label>
    <input name="description" type="text" class="form-control" value="{{ old('description', $data->description ?? '') }}">
    @error('description')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
{!! $data->id_serie == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button type="submit" class="btn btn-primary">Guardar</button>