<div class="form-group">
    <p>Los campos marcados con un * son obligatorios.</p>
</div>
<br>
<div class="form-group">
    <label for="email" class="form-label">Email *</label>
    <input id="editEmail" name="editEmail" type="checkbox">
    <input id="email" name="email" type="text" class="form-control" value="{{ old('email', $data->email ?? '') }}" readonly required>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="localCurrency" class="form-label">Moneda local *</label>
    <select class="form-select" name="localCurrency" required>
        <option value="" selected>Moneda</option>
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
    <label for="distanciaMinima" class="form-label">Distancia mínima</label>
    <input name="distanciaMinima" type="number" class="form-control" value="{{ old('distanciaMinima', $data->distanciaMinima ?? '') }}">
    @error('distanciaMinima')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="prod_serv" class="form-label">Producto/servicio *</label>
    <select class="form-select" name="prod_serv" required>
        <option value="" selected>producto/servicio</option>
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
    <label for="units" class="form-label">Unidad *</label>
    <select class="form-select" name="units" required>
        <option value="" selected>unit</option>
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
    <label for="rfc" class="form-label">RFC *</label>
    <input name="rfc" type="text" class="form-control uppercase" value="{{ old('rfc', $data->cfdi4_0->rfc ?? '') }}" required>
    @error('rfc')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="nombreReceptor" class="form-label">Nombre del receptor *</label>
    <input name="nombreReceptor" type="text" class="form-control uppercase" value="{{ old('nombreReceptor', $data->cfdi4_0->nombreReceptor ?? '') }}" required>
    @error('nombreReceptor')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="domicilioFiscalReceptor" class="form-label">Código postal del domicilio fiscal *</label>
    <input name="domicilioFiscalReceptor" type="number" class="form-control" value="{{ old('domicilioFiscalReceptor', $data->cfdi4_0->domicilioFiscalReceptor ?? '') }}" required>
    @error('domicilioFiscalReceptor')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="tax_regimes" class="form-label">Régimen fiscal *</label>
    <select class="form-select" name="tax_regimes" required>
        <option value="" selected>régimen fiscal</option>
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
    <label for="usoCFDI" class="form-label">Uso CFDI *</label>
    <select class="form-select" name="usoCFDI" required>
        <option value="" selected>Uso CFDI</option>
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
    <label for="payForm" class="form-label">Forma de pago *</label>
    <select class="form-select" name="payForm" required>
        <option value="" selected>Forma de pago</option>
        @foreach($payForm as $pf => $index)
            @if($data->payForm == $pf)
                <option selected value='{{$index}}'>{{$pf}}</option>
            @else
                <option value='{{$index}}'>{{$pf}}</option>
            @endif
        @endforeach
    </select>
    @error('payForm')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="payMethod" class="form-label">Método de pago *</label>
    <select class="form-select" name="payMethod" required>
        <option value="" selected>Método de pago</option>
        @foreach($payMethod as $pm => $index)
            @if($data->payMethod == $pm)
                <option selected value='{{$index}}'>{{$pm}}</option>
            @else
                <option value='{{$index}}'>{{$pm}}</option>
            @endif
        @endforeach
    </select>
    @error('payMethod')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
<div class="form-group">
    <label for="taxes" class="form-label">Impuesto *</label>
    <select class="form-select" name="taxes" required>
        <option value="" selected>impuesto</option>
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
{{-- <div class="mb-3">
    <label for="formFile" class="form-label">Seleccionar una imagen (.jpg,.jpeg,.png,.ico, .svg, .eps)</label>
    <input class="form-control" type="file" id="formFile" name="logo" accept=".jpg,.jpeg,.png,.ico, .svg, .eps">
</div> --}}
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>