<div class="row">
    <div class="col-md-6">
        <label><b> Receptor:</b></label>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="rfc_receptor" class="form-label">RFC</label>
        <input type="text" class="form-control" id="rfc_receptor" :value="oData.receptor.rfcReceptor">
    </div>
    <div class="col-md-9">
        <label for="nombre_receptor" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre_receptor" :value="oData.receptor.nombreReceptor">
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="regimen_fiscal_rec" class="form-label">Régimen Fiscal</label>
        <input type="text" class="form-control" id="regimen_fiscal_rec" :value="oData.receptor.regimenFiscalReceptor">
    </div>
    <div class="col-md-3">
        <label for="dom_fiscal_rec" class="form-label">Domicilio Fiscal</label>
        <input type="text" class="form-control" id="dom_fiscal_rec" :value="oData.receptor.domicilioFiscalReceptor">
    </div>
</div>
<br>