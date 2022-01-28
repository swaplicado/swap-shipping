<div class="row">
    <div class="col-md-6">
        <label><b>Autotransporte:</b></label>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="autotransporte">Autotransporte</label>
            <select class="form-select" id="autotransporte" v-model="oVehicle" required>
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
    <div class="col-md-2">
        <label>Seguros:</label>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <label for="insurance">Aseguradora</label>
        <input type="text" name="" class="form-control" id="insurance" :value="oVehicle.insurance_full_name" readonly>
    </div>
    <div class="col-md-3">
        <label for="insurance">Póliz resp. civ.</label>
        <input type="text" name="" class="form-control" id="insurance" :value="oVehicle.policy" readonly>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-2">
        <label>Remolques:</label>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <button type="button" class="btn btn-info" v-on:click="addTrailer()">Agregar remolque</button>
    </div>
</div>
<div class="row" v-for="selTrailer in lSelectedTrailers">
    <div class="col-md-4">
        <label>Remolque:</label>
        <select name="" id="" class="form-select" v-model="selTrailer.oTrailer">
            <option v-for="trailer in lTrailers" :value="trailer">@{{ trailer.plates }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label>Código</label>
        <input type="text" name="" class="form-control" :value="selTrailer.oTrailer.trailer_subtype_key_code" readonly>
    </div>
    <div class="col-md-5">
        <label>Tipo</label>
        <input type="text" name="" class="form-control" :value="selTrailer.oTrailer.trailer_subtype_description" readonly>
    </div>
</div>
<br>