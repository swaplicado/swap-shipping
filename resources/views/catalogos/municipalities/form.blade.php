<div class="form-group">
    <label for="key_code" class="form-label">Código del municipio:</label>
    <input disabled name="key_code" type="number" class="form-control" value="{{ old('key_code', $data->key_code ?? '') }}" disabled>
    @error('key_code')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="municipality_name" class="form-label">Municipio:</label>
    <input disabled name="municipality_name" type="text" class="form-control" value="{{ old('municipality_name', $data->municipality_name ?? '') }}" disabled>
    @error('municipality_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="state_name" class="form-label">Estado:</label>
    <input disabled name="state_name" type="text" class="form-control" value="{{ old('state_name', $data->state->state_name ?? '') }}" disabled>
    @error('state_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="distance" class="form-label">Distancia:</label>
    <input name="distance" type="number" min="1" max="10000" step="1" class="form-control" value="{{ old('distance', $data->distance ?? '') }}">
    @error('distance')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
<button type="submit" class="btn btn-primary">Guardar</button>