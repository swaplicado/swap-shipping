<div class="row">
    <div class="col-md-6">
        <label><b> Emisor:</b></label>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="rfc_emisor" class="form-label">RFC</label>
        <input type="text" class="form-control" id="rfc_emisor" v-model="oData.emisor.rfcEmisor">
    </div>
    <div class="col-md-9">
        <label for="nombre_emisor" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre_emisor" v-model="oData.emisor.nombreEmisor">
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="regimen_fiscal" class="form-label">Régimen Fiscal</label>
        <input type="text" class="form-control" id="regimen_fiscal" v-model="oData.emisor.regimenFiscal">
    </div>
</div>
<br>