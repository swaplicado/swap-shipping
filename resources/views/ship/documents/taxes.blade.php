<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="total_retenidos">Total Impuestos Retenidos</label>
            <input style="text-align: right" type="text" class="form-control" id="total_retenidos" readonly :value="formatCurrency(oData.totalImpuestosRetenidos)" readonly>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="total_trasladados">Total Impuestos Trasladados</label>
            <input style="text-align: right" type="text" class="form-control" id="total_trasladados" readonly :value="formatCurrency(oData.totalImpuestosTrasladados)" readonly>
        </div>
    </div>
</div>
<br>