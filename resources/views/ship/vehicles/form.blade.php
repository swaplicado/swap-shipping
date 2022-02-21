<div class="form-group">
    <label for="alias" class="form-label">Alias (nombre identificador)</label>
    <input name="alias" type="text" class="form-control" value="{{ old('alias', $data->alias ?? '') }}">
    @error('alias')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="plates" class="form-label">Placas</label>
    <input name="plates" type="text" class="form-control" value="{{ old('plates', $data->plates ?? '') }}">
    @error('plates')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="year_model" class="form-label">Modelo (año)</label>
    <input name="year_model" type="number" class="form-control" value="{{ old('year_model', $data->year_model ?? '') }}">
    @error('year_model')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="license_sct_id" class="form-label">Permiso SCT</label>
    <select class="form-select" name="license_sct_id">
        <option value="0" selected>Select permiso</option>
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
    <label for="license_sct_num" class="form-label">Número de permiso SCT</label>
    <input name="license_sct_num" type="text" class="form-control" value="{{ old('license_sct_num', $data->license_sct_num ?? '') }}">
    @error('license_sct_num')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="drvr_reg_trib" class="form-label">Reg trib</label>
    <input name="drvr_reg_trib" type="text" class="form-control" value="{{ old('drvr_reg_trib', $data->drvr_reg_trib ?? '') }}">
    @error('drvr_reg_trib')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="insurance" class="form-label">Aseguradora</label>
    <select class="form-select" name="insurance">
        <option value="0" selected>Select Aseguradora</option>
        @foreach($insurances as $i => $index)
            @if($data->insurance_id == $index)
                <option selected value='{{$index}}'>{{$i}}</option>
            @else
                <option value='{{$index}}'>{{$i}}</option>
            @endif
        @endforeach
    </select>
    @error('insurance')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="policy" class="form-label">Poliza</label>
    <input name="policy" type="text" class="form-control" value="{{ old('policy', $data->policy ?? '') }}">
    @error('policy')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="veh_cfg_id" class="form-label">Configuración del vehículo</label>
    <select class="form-select" name="veh_cfg_id">
        <option value="0" selected>Select configuración</option>
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
{!! $data->id_vehicle == null ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button type="submit" class="btn btn-primary">Guardar</button>