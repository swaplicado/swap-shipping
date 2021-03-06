<div class="row">
    <div class="col-md-3">
        <label for="rfc_emisor" class="form-label">RFC</label>
        <input type="text" class="form-control" id="rfc_emisor" :value="oData.emisor.rfcEmisor" readonly>
    </div>
    <div class="col-md-9">
        <label for="nombre_emisor" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre_emisor" :value="oData.emisor.nombreEmisor" readonly>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="regimen_fiscal" class="form-label">Régimen Fiscal</label>
        <input type="text" class="form-control" id="regimen_fiscal" :value="oData.emisor.regimenFiscal" readonly>
    </div>
    <div class="col-md-3" v-if="oData.emisor.oCustomAttributes != undefined">
        <div v-if="oData.emisor.oCustomAttributes.provider != undefined">
            <div v-if="oData.emisor.oCustomAttributes.provider != null && oData.emisor.oCustomAttributes.provider != ''">
                <label for="regimen_fiscal" class="form-label">Clave proveedor</label>
                <input type="text" class="form-control" id="clave_proveedor" :value="oData.emisor.oCustomAttributes.provider" readonly>
            </div>
        </div>
    </div>
</div>
<br>