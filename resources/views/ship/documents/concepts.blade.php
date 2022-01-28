<div class="row">
    <div class="col-md-6">
        <label><b>Conceptos:</b></label>
    </div>
</div>
<div class="row" v-for="oConcept in oData.conceptos">
    <div class="row">
        <div class="col-md-2">
            <label for="clave_prod" class="form-label">Clave</label>
            <input type="text" class="form-control" id="clave_prod" :value="oConcept.claveProdServ" readonly>
        </div>
        <div class="col-md-7">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" v-model="oConcept.description">
        </div>
        <div class="col-md-3">
            <label for="num_id" class="form-label">No Identificacion</label>
            <input type="text" class="form-control" id="num_id" :value="oConcept.numIndentificacion" readonly>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <label for="quantity" class="form-label">Cantidad</label>
            <input style="text-align: right" type="number" class="form-control" id="quantity" :value="formatNumber(oConcept.quantity, 2)" readonly>
        </div>
        <div class="col-md-2">
            <label for="unidad_key" class="form-label">Clave unidad</label>
            <input type="text" class="form-control" id="unidad_key" :value="oConcept.claveUnidad" readonly>
        </div>
        <div class="col-md-4">
            <label for="unidad" class="form-label">Unidad</label>
            <input type="text" class="form-control" id="unidad" v-model="oConcept.unidad">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="valor_unitario" class="form-label">Valor unitario</label>
            <my-currency-input v-model="oConcept.valorUnitario" v-on:keyup="onChangeAmount()"></my-currency-input>
        </div>
        <div class="col-md-4">
            <label for="valor_unitario" class="form-label">Descuento concepto</label>
            <my-currency-input v-model="oConcept.discount" v-on:keyup="onChangeAmount()"></my-currency-input>
        </div>
        <div class="col-md-4">
            <label for="importe" class="form-label">Importe</label>
            <input style="text-align: right" type="text" class="form-control" id="importe" :value="formatCurrency(oConcept.valorUnitario * oConcept.quantity)" readonly>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label>Impuestos:</label>
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label>Traslados:</label>
        </div>
    </div>
    <div class="row" v-for="oTras in oConcept.oImpuestos.lTraslados">
        <div class="col-md-2">
            <label for="impuesto" class="form-label">Impuesto</label>
            <input type="text" class="form-control" id="impuesto" :value="oTras.impuesto_name" readonly>
        </div>
        <div class="col-md-2">
            <label for="tasa" class="form-label">Tasa</label>
            <input style="text-align: right" type="text" class="form-control" id="tasa" :value="formatNumber(oTras.tasa, 4)" readonly>
        </div>
        <div class="col-md-4">
            <label for="base_impuesto_t" class="form-label">Base</label>
            <input style="text-align: right" type="text" class="form-control" id="base_impuesto_t" :value="formatCurrency(oTras.base)" readonly>
        </div>
        <div class="col-md-4">
            <label for="importe_total_t" class="form-label">Importe total</label>
            <input style="text-align: right" type="text" class="form-control" id="importe_total_t" :value="formatCurrency(oTras.importe)" readonly>
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label>Retenciones:</label>
        </div>
    </div>
    <div class="row" v-for="oRet in oConcept.oImpuestos.lRetenciones">
        <div class="col-md-2">
            <label for="impuesto_r" class="form-label">Impuesto</label>
            <input type="text" class="form-control" id="impuesto_r" :value="oRet.impuesto_name" readonly>
        </div>
        <div class="col-md-2">
            <label for="tasa_r" class="form-label">Tasa</label>
            <input style="text-align: right" type="text" class="form-control" id="tasa_r" :value="formatNumber(oRet.tasa, 4)" readonly>
        </div>
        <div class="col-md-4">
            <label for="base_impuesto_r" class="form-label">Base</label>
            <input style="text-align: right" type="text" class="form-control" id="base_impuesto_r" :value="formatCurrency(oRet.base)" readonly>
        </div>
        <div class="col-md-4">
            <label for="importe_total_r" class="form-label">Importe total</label>
            <input style="text-align: right" type="text" class="form-control" id="importe_total_r" :value="formatCurrency(oRet.importe)" readonly>
        </div>
    </div>
</div>
<br>