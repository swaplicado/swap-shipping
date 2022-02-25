<div class="row">
    <div class="col-md-2">
        <label for="num_mercancias">Num total merc.</label>
        <input style="text-align: right" type="number" class="form-control" id="num_mercancias" :value="oData.oCartaPorte.mercancia.numTotalMercancias" readonly>
    </div>
    <div class="col-md-3">
        <label for="peso_bruto">Peso Bruto</label>
        <input style="text-align: right" type="number" class="form-control" id="peso_bruto" :value="formatNumber(oData.oCartaPorte.mercancia.pesoBrutoTotal, 4)" readonly>
    </div>
    <div class="col-md-3">
        <label for="num_mercancias">Unidad Peso</label>
        <input type="text" class="form-control" id="num_mercancias" :value="oData.oCartaPorte.mercancia.unidadPesoName" readonly>
    </div>
</div>
<div class="row" v-for="merch in oData.oCartaPorte.mercancia.mercancias">
    <div style="border-radius: 15px; margin-left: 0%" class="row border border-info">
        <div class="row">
            <div class="col-md-2">
                <label for="bienes_transp">Bienes transp.</label>
                <input type="text" class="form-control" id="bienes_transp" :value="merch.bienesTransp" readonly>
            </div>
            <div class="col-md-6">
                <label>Descripción</label>
                <input type="text" class="form-control" :value="merch.descripcion" readonly>
            </div>
            <div class="col-md-3">
                <label>Cantidad</label>
                <input style="text-align: right" type="text" class="form-control" :value="formatNumber(merch.cantidad, 4)" readonly>
            </div>
            <div class="col-md-1">
                <label for="num_mercancias">Unidad</label>
                <input type="text" class="form-control" id="num_mercancias" :title="merch.unitName" :value="merch.unitName" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label for="num_mercancias">Peso en KG</label>
                <input style="text-align: right" type="number" class="form-control" id="num_mercancias" :value="formatNumber(merch.pesoEnKg, 3)" readonly>
            </div>
            <div class="col-md-2">
                <label for="num_mercancias">Valor Declarado</label>
                <input style="text-align: right" type="text" class="form-control" id="num_mercancias" :value="formatCurrency(merch.valorMercancia)" readonly>
            </div>
            <div class="col-md-3">
                <label for="num_mercancias">Moneda</label>
                <input type="text" class="form-control" id="num_mercancias" :value="merch.currencyName" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-12"><label for=""></label></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12"><label for=""></label></div>
    </div>
</div>
