<div class="form-group">
    <label for="fullname" class="form-label">Nombre</label>
    <input name="fullname" type="text" class="form-control" value="{{ old('fullname', $data->fullname ?? '') }}">
    @error('fullname')
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
<br>
<button type="submit" class="btn btn-primary">Guardar</button>