<div class="form-group">
    <label for="localCurrency" class="form-label">Moneda local</label>
    <select class="form-select" name="localCurrency">
        <option value="0" selected>Select Moneda</option>
        @foreach($currencies as $c => $index)
            @if($data->moneda == $c)
                <option selected value='{{$index}}'>{{$c}}</option>
            @else
                <option value='{{$index}}'>{{$c}}</option>
            @endif
        @endforeach
    </select>
    @error('localCurrency')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tarifaBase" class="form-label">Tarifa base</label>
    <input name="tarifaBase" type="number" class="form-control" value="{{ old('tarifaBase', $data->tarifaBase ?? '') }}">
    @error('tarifaBase')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tarifaBaseEscala" class="form-label">Tarifa base escala</label>
    <input name="tarifaBaseEscala" type="number" class="form-control" value="{{ old('tarifaBaseEscala', $data->tarifaBaseEscala ?? '') }}">
    @error('tarifaBaseEscala')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="distanciaMinima" class="form-label">Distancia minima</label>
    <input name="distanciaMinima" type="number" class="form-control" value="{{ old('distanciaMinima', $data->distanciaMinima ?? '') }}">
    @error('distanciaMinima')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="prod_serv" class="form-label">Producto/servicio</label>
    <select class="form-select" name="prod_serv">
        <option value="0" selected>Select producto/servicio</option>
        @foreach($prod_serv as $ps => $index)
            @if($data->prod_serv == $ps)
                <option selected value='{{$index}}'>{{$ps}}</option>
            @else
                <option value='{{$index}}'>{{$ps}}</option>
            @endif
        @endforeach
    </select>
    @error('prod_serv')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="units" class="form-label">Unidad</label>
    <select class="form-select" name="units">
        <option value="0" selected>Select unit</option>
        @foreach($units as $u => $index)
            @if($data->unidad == $u)
                <option selected value='{{$index}}'>{{$u}}</option>
            @else
                <option value='{{$index}}'>{{$u}}</option>
            @endif
        @endforeach
    </select>
    @error('units')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="rfc" class="form-label">RFC</label>
    <input name="rfc" type="text" class="form-control" value="{{ old('rfc', $data->cfdi4_0->rfc ?? '') }}">
    @error('rfc')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="nombreReceptor" class="form-label">Nombre del receptor</label>
    <input name="nombreReceptor" type="text" class="form-control" value="{{ old('nombreReceptor', $data->cfdi4_0->nombreReceptor ?? '') }}">
    @error('nombreReceptor')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="domicilioFiscalReceptor" class="form-label">Codigo postal del domicilio fiscal</label>
    <input name="domicilioFiscalReceptor" type="number" class="form-control" value="{{ old('domicilioFiscalReceptor', $data->cfdi4_0->domicilioFiscalReceptor ?? '') }}">
    @error('domicilioFiscalReceptor')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tax_regimes" class="form-label">Regimen fiscal</label>
    <select class="form-select" name="tax_regimes">
        <option value="0" selected>Select regimen fiscal</option>
        @foreach($tax_regimes as $tr => $index)
            @if($data->regimen == $tr)
                <option selected value='{{$index}}'>{{$tr}}</option>
            @else
                <option value='{{$index}}'>{{$tr}}</option>
            @endif
        @endforeach
    </select>
    @error('tax_regimes')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="usoCFDI" class="form-label">Uso CFDI</label>
    <select class="form-select" name="usoCFDI">
        <option value="0" selected>Select Uso CFDI</option>
        @foreach($usoCFDI as $cfdi => $index)
            @if($data->usoCFDI == $cfdi)
                <option selected value='{{$index}}'>{{$cfdi}}</option>
            @else
                <option value='{{$index}}'>{{$cfdi}}</option>
            @endif
        @endforeach
    </select>
    @error('usoCFDI')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="taxes" class="form-label">Impuesto</label>
    <select class="form-select" name="taxes">
        <option value="0" selected>Select impuesto</option>
        @foreach($taxes as $tax => $index)
            @if($data->impuesto == $tax)
                <option selected value='{{$index}}'>{{$tax}}</option>
            @else
                <option value='{{$index}}'>{{$tax}}</option>
            @endif
        @endforeach
    </select>
    @error('taxes')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<br>
<button type="submit" class="btn btn-primary">Guardar</button>