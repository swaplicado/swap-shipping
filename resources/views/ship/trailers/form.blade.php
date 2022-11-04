<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="plates" class="form-label">Placas *</label>
    <input name="plates" type="text" class="form-control uppercase" value="{{ old('plates', $data->plates ?? '') }}" required pattern="[A-Za-z0-9]+">
    <p class="form-text text-muted">
        Solo letras y números [A-Za-z0-9]+
    </p>
    @error('plates')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="trailer_subtype_id" class="form-label">Subtipo de trailer *</label>
    <select class="form-select" name="trailer_subtype_id" required>
        <option value="" selected>Subtipo de trailer</option>
        @foreach($TrailerSubtype as $subType => $index)
            @if($data->trailer_subtype_id == $index)
                <option selected value='{{$index}}'>{{$subType}}</option>
            @else
                <option value='{{$index}}'>{{$subType}}</option>
            @endif
        @endforeach
    </select>
    @error('trailer_subtype_id')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
{!! $data->id_trailer == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<div class="form-check">
    <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="is_own" id="is_own" v-model="bIsOwn" value="checkedValue">
        El emisor es propietario del remolque
    </label>
</div>
<br>
<div v-if="! bIsOwn">
    <div class="form-group">
        <label for="">Seleccione tipo remolque</label>
        <select class="form-control" v-model="oTransCfg.trans_part_id" name="trans_part_id" id="trans_part_id">
            <option v-for="oTransPart in lTransParts" :value="oTransPart.id">@{{ oTransPart.key_code + ' - ' +
                oTransPart.description }}</option>
        </select>
    </div>
    <br>
    <div class="form-group">
        <label for="figure_type">Seleccione tipo relación</label>
        <select class="form-control" name="figure_type" id="figure_type" v-model="oTransCfg.figure_type_id" required>
            <option value="2" selected>Propietario</option>
            <option value="3">Arrendatario</option>
        </select>
    </div>
    <br>
    <div class="form-group">
        <label for="figure_id">Seleccione figura de transporte</label>
        <select class="form-control" name="figure_id" id="figure_id" v-model="oTransCfg.figure_trans_id" required>
            <option v-for="oFigure in lFigures" :value="oFigure.id_trans_figure">@{{ oFigure.fiscal_id + ' - ' +
                oFigure.fullname }}</option>
        </select>
    </div>
</div>
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>