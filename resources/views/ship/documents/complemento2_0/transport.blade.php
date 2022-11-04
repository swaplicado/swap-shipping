<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="autotransporte">Autotransporte*</label>
            <select class="form-select" id="autotransporte" v-model="oVehicle" v-on:change="onVehicleKeyChange()">
                <option v-for="vehicle in lVehicles" :value="vehicle">@{{ vehicle.alias }}</option>
            </select>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-2">
        <label>Año vehiculo</label>
        <input type="text" class="form-control" :value="oVehicle.year_model" readonly>
    </div>
    <div class="col-md-2">
        <label>Placa</label>
        <input type="text" class="form-control" :value="oVehicle.plates" readonly>
    </div>
    <div class="col-md-2">
        <label>Permiso SCT</label>
        <input type="text" class="form-control" :value="oVehicle.slic_key_code" readonly>
    </div>
    <div class="col-md-6">
        <label>Permiso SCT</label>
        <input type="text" class="form-control" :value="oVehicle.slic_description" readonly>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label>Num permiso SCT</label>
        <input type="text" class="form-control" :value="oVehicle.license_sct_num" readonly>
    </div>
    <div class="col-md-2">
        <label>Cfg. vehicular</label>
        <input type="text" class="form-control" :value="oVehicle.vcfg_key_code" readonly>
    </div>
    <div class="col-md-7">
        <label>Cfg. vehicular</label>
        <input type="text" class="form-control" :title="oVehicle.vcfg_description" :value="oVehicle.vcfg_description" readonly>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="vehicle_key">Clave de transporte</label>
            <select class="form-select" id="vehicle_key" v-on:change="onVehicleKeyChange()" v-model="oVehicle.veh_key_id">
                <option v-for="vehicleKey in lVehicleKeys" :value="vehicleKey.id_key">
                    @{{ vehicleKey.key_code + ' - ' + vehicleKey.description }}
                </option>
            </select>
        </div>
    </div>
</div>
<br>
<div class="div" v-if="! oVehicle.is_own">
    <div class="row">
        <div class="col-md-5">
            <label for="trans_part">Tipo SAT:</label>
            <input type="text" id="trans_part" class="form-control" :title="oVehicle.oFigTranCfg.desc_trans_part" 
                    :value="oVehicle.oFigTranCfg.key_trans_part + ' - ' + oVehicle.oFigTranCfg.desc_trans_part" readonly>
        </div>
        <div class="col-md-5">
            <label for="figure_type_id">Relación:</label>
            <input type="text" id="figure_type_id" class="form-control" :title="oVehicle.oFigTranCfg.desc_figure_type" 
                    :value="oVehicle.oFigTranCfg.key_figure_type + ' - ' + oVehicle.oFigTranCfg.desc_figure_type" readonly>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <label for="figure_type_id">Figura de transporte:</label>
            <input type="text" id="figure_type_id" class="form-control" :title="oVehicle.oFigTranCfg.fiscal_id + ' - ' + oVehicle.oFigTranCfg.fullname" 
                    :value="oVehicle.oFigTranCfg.fiscal_id + ' - ' + oVehicle.oFigTranCfg.fullname" readonly>
        </div>
    </div>
    <br>
</div>
<div class="row">
    <div class="col-md-2">
        <label>Seguros:</label>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="insurance">Aseguradora</label>
        <input type="text" class="form-control" id="insurance" :value="oVehicle.insurance_full_name" readonly>
    </div>
    <div class="col-md-3">
        <label for="insurance">Póliz resp. civ.</label>
        <input type="text" class="form-control" id="insurance" :value="oVehicle.policy" readonly>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-2">
        <label id="labRems">Remolques:</label>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <button type="button" class="btn btn-info" v-on:click="addTrailer()" :disabled="oVehicle.vcfg_trailer == 0">Agregar remolque</button>
    </div>
</div>
<div class="row" v-for="selTrailer in lSelectedTrailers">
    <div class="col-md-3">
        <label>Remolque:</label>
        <select class="form-select" v-model="selTrailer.oTrailer">
            <option v-for="trailer in lTrailers" :value="trailer">@{{ trailer.plates }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label>Código</label>
        <input type="text" class="form-control" :value="selTrailer.oTrailer.trailer_subtype_key_code" readonly>
    </div>
    <div class="col-md-5">
        <label>Tipo</label>
        <input type="text" class="form-control" :value="selTrailer.oTrailer.trailer_subtype_description" readonly>
    </div>
    <div class="col-md-1">
        <label>Eliminar</label>
        <button type="button" class="btn btn-danger" v-on:click="removeTrailer(lSelectedTrailers.indexOf(selTrailer))">X</button>
    </div>
    <div class="div" v-if="! selTrailer.oTrailer.is_own">
        <div class="row">
            <div class="col-md-5">
                <label for="trans_part">Tipo SAT:</label>
                <input type="text" id="trans_part" class="form-control input-sm" :title="selTrailer.oTrailer.oFigTranCfg.desc_trans_part" 
                        :value="selTrailer.oTrailer.oFigTranCfg.key_trans_part + ' - ' + selTrailer.oTrailer.oFigTranCfg.desc_trans_part" readonly>
            </div>
            <div class="col-md-5">
                <label for="figure_type_id">Relación:</label>
                <input type="text" id="figure_type_id" class="form-control input-sm" :title="selTrailer.oTrailer.oFigTranCfg.desc_figure_type" 
                        :value="selTrailer.oTrailer.oFigTranCfg.key_figure_type + ' - ' + selTrailer.oTrailer.oFigTranCfg.desc_figure_type" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 form-group form-group-sm">
                <label for="figure_type_id">Figura de transporte:</label>
                <input type="text" id="figure_type_id" class="form-control input-sm" :title="selTrailer.oTrailer.oFigTranCfg.fiscal_id + ' - ' + selTrailer.oTrailer.oFigTranCfg.fullname" 
                        :value="selTrailer.oTrailer.oFigTranCfg.fiscal_id + ' - ' + selTrailer.oTrailer.oFigTranCfg.fullname" readonly>
            </div>
        </div>
        <br>
    </div>
</div>
<br>