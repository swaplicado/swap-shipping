<div class="row" v-for="oLocation in oData.oCartaPorte.ubicaciones">
    <label>@{{ oLocation.tipoUbicacion }}</label>
    <div class="row">
        <div class="col-md-2">
            <label>Identificador</label>
            <input type="text" class="form-control" :value="oLocation.IDUbicacion" readonly>
        </div>
        <div class="col-md-3">
            <label>Distancia recorrida (KM)</label>
            <input style="text-align: right" type="text" class="form-control" :value="oLocation.distanciaRecorrida" readonly>
        </div>
        <div class="col-md-3">
            <label>Fecha-Hora</label>
            <input style="text-align: right" type="text" class="form-control" :value="oLocation.fechaHoraSalidaLlegada" readonly>
        </div>
        <div class="col-md-3">
            <label>RFC Rem/Dest</label>
            <input style="text-align: right" type="text" class="form-control" :value="oLocation.rFCRemitenteDestinatario" readonly>
        </div>
    </div>
    <div class="row">
        <label>Domicilio</label>
        <div class="row">
            {{-- <div class="col-md-2">
                <label>Colonia</label>
                <input style="text-align: right" type="text" class="form-control" :value="oLocation.domicilio.colonia" readonly>
            </div>
            <div class="col-md-2">
                <label>Localidad</label>
                <input style="text-align: right" type="text" class="form-control" :value="oLocation.domicilio.localidad" readonly>
            </div> --}}
            <div class="col-md-4">
                <label>Municipio</label>
                <input type="text" class="form-control" :value="oLocation.domicilio.municipioName" readonly>
            </div>
            <div class="col-md-3">
                <label>Estado</label>
                <input type="text" class="form-control" :value="oLocation.domicilio.estadoName" readonly>
            </div>
            <div class="col-md-2">
                <label>CP</label>
                <input type="text" class="form-control" :value="oLocation.domicilio.codigoPostal" readonly>
            </div>
            <div class="col-md-3">
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
