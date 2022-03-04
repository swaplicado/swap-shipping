<div class="form-group">
    <label for="fullname" class="form-label">Nombre</label>
    <input name="fullname" type="text" class="form-control uppercase" value="{{ old('fullname', $data->full_name ?? '') }}" required>
    @error('fullname')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
{!! $data->id_insurance == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button type="submit" class="btn btn-primary">Guardar</button>