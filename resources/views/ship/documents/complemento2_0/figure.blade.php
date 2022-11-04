<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="figure">Seleccione chofer*</label>
            <select class="form-select" id="figure" v-model="oFigure">
                <option v-for="figure in lFigures" :value="figure">@{{ figure.fiscal_id + " - " + figure.fullname }}</option>
            </select>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-3">
        <label>Licencia</label>
        <input type="text" class="form-control" :value="oFigure.driver_lic" readonly>
    </div>
    <div class="col-md-3">
        <label>RFC</label>
        <input type="text" class="form-control" :value="oFigure.fiscal_id" readonly>
    </div>
    <div class="col-md-3">
        <label>Tipo figura</label>
        <input type="text" class="form-control" :value="oFigure.figure_type_description" readonly>
    </div>
</div>