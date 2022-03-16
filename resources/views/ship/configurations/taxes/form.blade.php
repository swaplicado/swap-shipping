<div class="form-group">
    <label for="config_type" class="form-label">Tipo impuesto*</label>
    <select name="config_type" class="form-select" value="{{ isset($oCfg) ? $oCfg->config_type : null }}" required>
        <option value="">Seleccione</option>
        @if (isset($oCfg) && $oCfg->config_type == "traslado")
            <option value="traslado" selected>Traslado</option>
        @else
            <option value="traslado">Traslado</option>
        @endif
        @if (isset($oCfg) && $oCfg->config_type == "retencion")
            <option value="retencion" selected>Retención</option>
        @else
            <option value="retencion">Retención</option>
        @endif
    </select>
    @error('config_type')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<br>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="date_from" class="form-label">Desde:</label>
            <input value="{{ isset($oCfg) ? $oCfg->date_from : null }}" type="date" name="date_from" class="form-control">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="date_to" class="form-label">Hasta:</label>
            <input value="{{ isset($oCfg) ? $oCfg->date_to : null }}" type="date" name="date_to" class="form-control">
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="person_type_emisor" class="form-label">Emisor:</label>
            <select value="{{ isset($oCfg) ? $oCfg->person_type_emisor : null }}" name="person_type_emisor" class="form-select">
                <option value="">TODOS</option>
                @if (isset($oCfg) && $oCfg->person_type_emisor == "fisica")
                    <option value="fisica" selected>Persona fisica</option>    
                @else
                    <option value="fisica">Persona fisica</option>
                @endif
                @if (isset($oCfg) && $oCfg->person_type_emisor == "moral")
                    <option value="moral" selected>Persona moral</option>
                @else
                    <option value="moral">Persona moral</option>
                @endif
            </select>
            @error('person_type_emisor')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="person_type_receptor" class="form-label">Receptor:</label>
            <select name="person_type_receptor" class="form-select">
                <option value="">TODOS</option>
                {{-- <option value="fisica">Persona fisica</option> --}}
                @if (isset($oCfg) && $oCfg->person_type_receptor == "moral")
                    <option value="moral" selected>Persona moral</option>
                @else
                    <option value="moral">Persona moral</option>
                @endif
            </select>
            @error('person_type_receptor')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="form-group">
    <label for="fiscal_regime_id" class="form-label">Régimen fiscal</label>
    <select name="fiscal_regime_id" class="form-select">
        <option value="">TODOS</option>
        @foreach ($lRegimes as $regime)
            @if (isset($oCfg) && $oCfg->fiscal_regime_id == $regime->id)
                <option value="{{ $regime->id }}" selected>{{ $regime->key_code."-".$regime->description }}</option>
            @else
                <option value="{{ $regime->id }}">{{ $regime->key_code."-".$regime->description }}</option>
            @endif
        @endforeach
    </select>
    @error('fiscal_regime_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="concept_id" class="form-label">Concepto</label>
    <select name="concept_id" class="form-select">
        <option value="">TODOS</option>
        @foreach ($lConcepts as $item)
            @if (isset($oCfg) && $oCfg->concept_id == $item->id)
                <option value="{{ $item->id }}" selected>{{ $item->key_code."-".$item->description }}</option>
            @else
                <option value="{{ $item->id }}">{{ $item->key_code."-".$item->description }}</option>
            @endif
        @endforeach
    </select>
    @error('concept_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tax_id" class="form-label">Impuesto*</label>
            <select name="tax_id" class="form-select" required>
                @foreach ($lTaxes as $tax)
                    @if (isset($oCfg) && $oCfg->tax_id == $tax->id)
                        <option value="{{ $tax->id }}" selected>{{ $tax->key_code."-".$tax->description }}</option>
                    @else
                        <option value="{{ $tax->id }}">{{ $tax->key_code."-".$tax->description }}</option>
                    @endif
                @endforeach
            </select>
            @error('tax_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="mb-3">
            <label for="rate" class="form-label">Tasa*</label>
            <input type="number" value="{{ isset($oCfg) ? $oCfg->rate : null }}" class="form-control" name="rate" step="0.001" placeholder="Tasa" min="0.000" max="1.000" required>
            @error('rate')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
{!! Auth::user()->isAdmin() ? (session()->has('form') ? session('form') : "") : "" !!}
<br>
<button id="save" type="submit" class="btn btn-primary">Guardar</button>