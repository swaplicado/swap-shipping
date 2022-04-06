<div v-if="! bShipCfg">
    <div class="row">
        <div class="col-md-12">
            <label for="shipType">Tipo de flete</label>
        </div>
    </div>
    <div class="row">
        <div class="offset-md-2 col-md-4 form-check">
            <input class="form-check-input" type="radio" name="shipType" id="flexRadioDefault1" v-on:change="changeShipType()" value="L" v-model="sShipType">
            <label class="form-check-label" for="flexRadioDefault1">
              Local
            </label>
        </div>
        <div class="col-md-4 form-check">
            <input class="form-check-input" type="radio" name="shipType" id="flexRadioDefault2" v-on:change="changeShipType()" value="F" v-model="sShipType">
            <label class="form-check-label" for="flexRadioDefault2">
              Foráneo
            </label>
        </div>
    </div>
    <br>
</div>
<div class="row" v-for="(oLocation, i) in oData.oCartaPorte.ubicaciones">
    <div style="border-radius: 15px; margin-left: 0%" class="row border border-info">
        <label>@{{ oLocation.tipoUbicacion }}</label>
        <div class="row">
            <div class="col-md-2">
                <label>Identificador</label>
                <input type="text" class="form-control" v-model="oLocation.IDUbicacion" readonly>
            </div>
            <div class="col-md-3">
                <label>Distancia recorrida (KM)</label>
                <input :id="'distanceId' + i" style="text-align: right" type="number" class="form-control" v-on:keyup="onChangeDistance()" :readonly="i == 0" v-model="oLocation.distanciaRecorrida">
            </div>
            <div class="col-md-4">
                <label>Fecha-hora @{{ i == 0 ? 'salida' : 'llegada' }}</label>
                <input :id="'dateTimeLocId' + i" :name="'dateTimeLocName' + i" style="text-align: right" type="datetime-local" class="form-control" v-model="oLocation.fechaHoraSalidaLlegada">
            </div>
            <div class="col-md-3">
                <label>RFC Rem/Dest</label>
                <input style="text-align: right" type="text" class="form-control" :value="oLocation.rFCRemitenteDestinatario" readonly>
            </div>
        </div>
        <div class="row">
            <label>Domicilio</label>
            <div class="row">
                <div class="col-md-3">
                    <label>Municipio</label>
                    <input type="text" class="form-control" :value="oLocation.domicilio.municipioName" readonly>
                </div>
                <div class="col-md-3" v-if="lSuburbs[i].length > 0">
                    <label>Colonia:</label>
                    <select class="form-select" v-model="oLocation.domicilio.colonia">
                        <option v-for="suburb in lSuburbs[i]" :value="suburb.key_code">@{{ suburb.suburb_name }}</option>
                    </select>
                </div>
                <div v-else style="display: none">
                    @{{oLocation.domicilio.colonia = ''}}
                </div>
                <div class="col-md-3">
                    <label>Estado</label>
                    <input type="text" class="form-control" :value="oLocation.domicilio.estadoName" readonly>
                </div>
                <div class="col-md-1">
                    <label>CP</label>
                    <input type="text" class="form-control" :value="oLocation.domicilio.codigoPostal" readonly>
                </div>
                <div class="col-md-2">
                    <label>País</label>
                    <input type="text" class="form-control" :value="oLocation.domicilio.paisName" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <label for=""></label>
        </div>
    </div>
</div>
