<div class="row">
    <div class="col-md-2">
        <label for="version_comp">Version</label>
        <input type="text" class="form-control" name="version_comp" id="version_comp" :value="oData.oCartaPorte.version" readonly>
    </div>
    <div class="col-md-2">
        <label for="transp_int">Transporte int.</label>
        <input style="text-align: center" type="text" class="form-control" name="transp_int" id="transp_int" :value="oData.oCartaPorte.transpInternac" readonly>
    </div>
    <div class="col-md-3">
        <label for="total_distancia">Total dist. recorrida (KM)</label>
        <input style="text-align: right" type="number" class="form-control" name="total_distancia" id="total_distancia" :value="formatNumber(oData.oCartaPorte.totalDistancia, 4)" readonly>
    </div>
</div>
<br>
