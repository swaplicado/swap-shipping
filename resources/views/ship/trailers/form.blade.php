<div class="form-group">
    <label for="plates" class="form-label">Placas</label>
    <input name="plates" type="text" class="form-control" value="{{ old('plates', $data->plates ?? '') }}">
    @error('plates')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="trailer_subtype_id" class="form-label">Subtipo de trailer</label>
    <select class="form-select" name="trailer_subtype_id">
        <option value="0" selected>Select subtipo</option>
        @foreach($TrailerSubtype as $subType => $index)
            @if($data->trailer_subtype_id == $index)
                <option selected value='{{$index}}'>{{$subType}}</option>
            @else
                <option value='{{$index}}'>{{$subType}}</option>
            @endif
        @endforeach
    </select>
    @error('TrailerSubtype')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
{!! $data->id_trailer == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button type="submit" class="btn btn-primary">Guardar</button>