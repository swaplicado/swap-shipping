<div class="form-group">
    <label for="key_code" class="form-label">Código del estado:</label>
    <input disabled name="key_code" type="text" class="form-control" value="{{ old('key_code', $data->key_code ?? '') }}">
    @error('key_code')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="state_name" class="form-label">Estado:</label>
    <input disabled name="state_name" type="text" class="form-control" value="{{ old('state_name', $data->state_name ?? '') }}">
    @error('state_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="rate" class="form-label">rate:</label>
    <input name="rate" type="number" min="0.1" max="100" step="0.1" class="form-control" value="{{ old('rate', $data->rate ?? '') }}">
    @error('rate')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="distance" class="form-label">Distancia:</label>
    <input name="distance" type="text" class="form-control" value="{{ old('distance', $data->distance ?? '') }}">
    @error('distance')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
<button type="submit" class="btn btn-primary">Guardar</button>