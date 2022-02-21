<div class="form-group">
    <label for="fullname" class="form-label">Nombre</label>
    <input name="fullname" type="text" class="form-control uppercase" value="{{ old('fullname', $data->full_name ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
<div class="form-group">
    <input name="checkbox[]" type="checkbox" id="checkbox1" value="1">
    <label class="form-check-label" for="checkbox1">Responsabilidad civil</label>
</div>
<div class="form-group">
    <input name="checkbox[]" type="checkbox" id="checkbox2" value="2">
    <label class="form-check-label" for="checkbox2">Ambiental</label>
</div>
<div class="form-group">
    <input name="checkbox[]" type="checkbox" id="checkbox3" value="3">
    <label class="form-check-label" for="checkbox3">carga</label>
</div>
@error('checkbox')
    <span class="text-danger">{{ $message }}</span>
@enderror
{!! $data->id_insurance == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button type="submit" class="btn btn-primary">Guardar</button>