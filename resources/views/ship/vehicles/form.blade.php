<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="alias" class="form-label">Alias (nombre identificador)</label>
    <input name="alias" type="text" placeholder="p. ej. o: camión rojo, carro grande, trailer, doble remolque, camioneta chica" class="form-control" value="{{ old('alias', $data->alias ?? '') }}">
    @error('alias')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="plates" class="form-label">Placas*</label>
    <input name="plates" placeholder="Placas del vehículo" type="text" class="form-control uppercase" value="{{ old('plates', $data->plates ?? '') }}" required pattern="[A-Za-z0-9]+">
    <p class="form-text text-muted">
        Solo letras y números [A-Za-z0-9]+
    </p>
    @error('plates')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="year_model" class="form-label">Modelo (año) *</label>
    <input name="year_model" type="number" class="form-control uppercase" value="{{ old('year_model', $data->year_model ?? '') }}" required>
    @error('year_model')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="license_sct_id" class="form-label">Permiso SCT *</label>
    <select class="form-select" name="license_sct_id" required>
        <option value="" selected>Permiso SCT</option>
        @foreach($LicenceSct as $lsct => $index)
            @if($data->license_sct_id == $index)
                <option selected value='{{$index}}'>{{$lsct}}</option>
            @else
                <option value='{{$index}}'>{{$lsct}}</option>
            @endif
        @endforeach
    </select>
    @error('license_sct_id')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="license_sct_num" class="form-label">Número de permiso SCT *</label>
    <input name="license_sct_num" type="text" class="form-control uppercase" value="{{ old('license_sct_num', $data->license_sct_num ?? '') }}" required>
    @error('license_sct_num')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
{!! $data->id_vehicle == null ? (session()->has('form') ? session('form') : "") : "" !!}
@if(!auth()->user()->isCarrier())
    <div class="form-group">
        <label for="insurance" class="form-label">Aseguradora *</label>
        <div id="sel_insurances">
            @if(!is_null($data->id_vehicle))
            <select class="form-select" name="insurance" required>
                <option value="" selected>Aseguradora</option>
                @foreach($insurances as $ins => $index)
                    @if($data->insurance_id == $index)
                        <option selected value='{{$index}}'>{{$ins}}</option>
                    @else
                        <option value='{{$index}}'>{{$ins}}</option>
                    @endif
                @endforeach
            </select>
            @endif
        </div>
        @error('insurance')
            <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
@else
<div class="form-group">
    <label for="insurance" class="form-label">Aseguradora *</label>
    <div id="sel_insurances">
        <select class="form-select" name="insurance" required>
            <option value="" selected>Aseguradora</option>
            @foreach($insurances as $ins => $index)
                @if($data->insurance_id == $index)
                    <option selected value='{{$index}}'>{{$ins}}</option>
                @else
                    <option value='{{$index}}'>{{$ins}}</option>
                @endif
            @endforeach
        </select>
    </div>
    @error('insurance')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
@endif
<div class="form-group">
    <label for="policy" class="form-label">Póliza responsabilidad civil *</label>
    <input name="policy" type="text" class="form-control uppercase" value="{{ old('policy', $data->policy ?? '') }}" required>
    @error('policy')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="veh_cfg_id" class="form-label">Configuración del vehículo *</label>
    <select class="form-select" name="veh_cfg_id" required>
        <option value="" selected>Configuración del vehículo</option>
        @foreach($VehicleConfig as $vcfg => $index)
            @if($data->veh_cfg_id == $index)
                <option selected value='{{$index}}'>{{$vcfg}}</option>
            @else
                <option value='{{$index}}'>{{$vcfg}}</option>
            @endif
        @endforeach
    </select>
    @error('veh_cfg_id')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="veh_key_id" class="form-label">Clave del vehículo</label>
    <select class="form-select" name="veh_key_id">
            @foreach ($lVehicleKeys as $item)
                @if($data->veh_key_id == $item->id_key)
                    <option selected value='{{ $item->id_key }}'>{{ $item->key_code.' - '.$item->description }}</option>
                @else
                    <option value='{{ $item->id_key }}'>{{ $item->key_code.' - '.$item->description }}</option>
                @endif
            @endforeach
    </select>
    @error('veh_key_id')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<br>
<div class="form-check">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input" name="is_own" id="is_own" v-model="bIsOwn" value="checkedValue">
    El emisor es propietario del vehículo
  </label>
</div>
<br>
<div v-if="! bIsOwn">
    <div class="form-group">
      <label for="">Seleccione tipo vehículo</label>
      <select class="form-control" v-model="oTransCfg.trans_part_id" name="trans_part_id" id="trans_part_id">
        <option v-for="oTransPart in lTransParts" :value="oTransPart.id" >@{{ oTransPart.key_code + ' - ' + oTransPart.description }}</option>
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
        <option v-for="oFigure in lFigures" :value="oFigure.id_trans_figure" >@{{ oFigure.fiscal_id + ' - ' + oFigure.fullname }}</option>
      </select>
    </div>
</div>
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>