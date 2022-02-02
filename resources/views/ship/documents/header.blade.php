<div class="row">
    <div class="col-md-2">
        <label for="cfdi_version" class="form-label">Version</label>
        <input type="text" class="form-control" id="cfdi_version" :value="oData.cfdiVersion" readonly>
    </div>
    <div class="col-md-4">
        <label for="dt_date" class="form-label">Fecha</label>
        <input type="datetime" class="form-control" id="dt_date" :value="oData.dtDate" readonly>
    </div>
    <div class="col-md-3">
        <label for="serie" class="form-label">Serie</label>
        <select id="serie" class="form-select" v-model="oData.serie" v-on:change="changeSerie(oData.serie)" required>
            <option v-for="serie in lCarrierSeries" :value="serie.prefix">@{{ serie.prefix }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="folio" class="form-label">Folio</label>
        <input type="text" class="form-control" id="folio" v-model="oData.folio">
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <label for="forma_pago" class="form-label">Forma Pago</label>
        <select class="form-select" v-model="oData.formaPago" required>
            <option v-for="pf in lPayForms" :value="pf.key_code">@{{ pf._description }}</option>
        </select>
    </div>
    <div class="col-md-5">
        <label for="metodo_pago" class="form-label">Método Pago</label>
        <select class="form-select" v-model="oData.metodoPago" required>
            <option v-for="pm in lPayMethods" :value="pm.key_code">@{{ pm._description }}</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="local_currency" class="form-label">Moneda local</label>
        <input type="text" class="form-control" id="local_currency" :value="oData.localCurrency" readonly>
    </div>
    <div class="col-md-3">
        <label for="currency" class="form-label">Moneda CFDI</label>        
        <select class="form-select" v-model="oData.currency" required>
            <option v-for="cur in lCurrencies" :value="cur.key_code">@{{ cur._description }}</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="tc" class="form-label">Tipo de cambio</label>
        <input step="0.0001" type="number" class="form-control" id="tc" :value="oData.tipoCambio" required>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="sub_total" class="form-label">Subtotal</label>
        <input style="text-align: right" class="form-control" type="text" :value="formatCurrency(oData.subTotal)" readonly>
    </div>
    <div class="col-md-3">
        <label for="discounts_t" class="form-label">Descuentos</label>
        <input style="text-align: right" class="form-control" type="text" id="discounts_t" :value="formatCurrency(oData.discounts)" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Total</label>
        <input style="text-align: right" class="form-control" type="text" :value="formatCurrency(oData.total)" readonly>
    </div>
</div>
<br>