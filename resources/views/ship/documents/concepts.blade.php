<div v-for="(oConcept, itemObjKey) in oData.conceptos">
    <div style="border-radius: 15px" class="row border border-primary">
        <div class="row" style="background-color: lightgray; border-radius: 15px; width: 90%; margin-left: 5%;">
            <div class="col-11">
                <label class="form-label">@{{ "Concepto " + (itemObjKey + 1 + "").padStart(3, '0') }}</label>
            </div>
            <div class="col-1">
                <button title="Eliminar concepto" class="btn btn-danger btn-sm"><i class='bx bxs-message-square-x bx-sm'></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label for="clave_prod" class="form-label">Clave</label>
                <input type="text" class="form-control" id="clave_prod" :value="oConcept.claveProdServ" readonly>
            </div>
            <div class="col-md-7">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" v-model="oConcept.description">
            </div>
            <div class="col-md-3">
                <label for="num_id" class="form-label">No Identificacion</label>
                <input type="text" class="form-control" id="num_id" v-model="oConcept.numIndentificacion">
                <small class="text-muted">De 1 hasta 100 caracteres alfanuméricos.</small>
            </div>
        </div>
        <div v-if="bCustomAtts" class="row">
            <div class="col-md-3">
                <label class="form-label">Folios de embarque</label>
                <input type="text" :title="oConcept.oCustomAttributes.shippingOrders" class="form-control" v-model="oConcept.oCustomAttributes.shippingOrders">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tarifa</label>
                <input type="text" :title="oConcept.oCustomAttributes.rateCode" class="form-control" v-model="oConcept.oCustomAttributes.rateCode">
            </div>
            <div class="col-md-3">
                <label class="form-label">Destino</label>
                <input type="text" :title="oConcept.oCustomAttributes.destinyName" class="form-control" v-model="oConcept.oCustomAttributes.destinyName">
            </div>
            <div class="col-md-4">
                <label class="form-label">Cliente</label>
                <input type="text" :title="oConcept.oCustomAttributes.customerName" class="form-control" v-model="oConcept.oCustomAttributes.customerName">
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label for="quantity" class="form-label">Cantidad</label>
                <input style="text-align: right" type="number" class="form-control" id="quantity" :value="formatNumber(oConcept.quantity, 2)" readonly>
            </div>
            <div class="col-md-2">
                <label for="unidad_key" class="form-label">Clave unidad</label>
                <input type="text" class="form-control" id="unidad_key" :value="oConcept.claveUnidad" readonly>
            </div>
            <div class="col-md-4">
                <label for="unidad" class="form-label">Unidad</label>
                <input type="text" class="form-control" id="unidad" v-model="oConcept.unidad">
            </div>
            <div class="col-md-2">
                <br>
                <br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="oConcept.isOfficialRate" id="official_rate">
                    <label class="form-check-label" for="official_rate">
                        Guardar tarifa
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="valor_unitario" class="form-label">Valor unitario*</label>
                <my-currency-input :id="'valor_unitario' + itemObjKey" v-model="oConcept.valorUnitario" v-on:keyup="onChangeAmount()"></my-currency-input>
            </div>
            <div class="col-md-4">
                <label for="valor_unitario" class="form-label">Descuento concepto</label>
                <my-currency-input :id="'descuento' + itemObjKey" v-model="oConcept.discount" v-on:keyup="onChangeAmount()"></my-currency-input>
            </div>
            <div class="col-md-4">
                <label for="importe" class="form-label">Importe</label>
                <input style="text-align: right" type="text" class="form-control" id="importe" :value="formatCurrency(oConcept.valorUnitario * oConcept.quantity)" readonly>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <label>Impuestos:</label>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <div class="col-md-6">
                <label>Traslados:</label>
            </div>
        </div>
        <div class="row" v-for="oTras in oConcept.oImpuestos.lTraslados">
            <div class="col-md-2">
                <label for="impuesto" class="form-label">Impuesto</label>
                <input type="text" class="form-control" id="impuesto" :value="oTras.impuesto_name" readonly>
            </div>
            <div class="col-md-2">
                <label for="tasa" class="form-label">Tasa</label>
                <input style="text-align: right" type="text" class="form-control" id="tasa" :value="formatNumber(oTras.tasa, 4)" readonly>
            </div>
            <div class="col-md-4">
                <label for="base_impuesto_t" class="form-label">Base</label>
                <input style="text-align: right" type="text" class="form-control" id="base_impuesto_t" :value="formatCurrency(oTras.base)" readonly>
            </div>
            <div class="col-md-4">
                <label for="importe_total_t" class="form-label">Importe total</label>
                <input style="text-align: right" type="text" class="form-control" id="importe_total_t" :value="formatCurrency(oTras.importe)" readonly>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <div class="col-md-6">
                <label>Retenciones:</label>
            </div>
        </div>
        <div class="row" v-for="oRet in oConcept.oImpuestos.lRetenciones">
            <div class="col-md-2">
                <label for="impuesto_r" class="form-label">Impuesto</label>
                <input type="text" class="form-control" id="impuesto_r" :value="oRet.impuesto_name" readonly>
            </div>
            <div class="col-md-2">
                <label for="tasa_r" class="form-label">Tasa</label>
                <input style="text-align: right" type="text" class="form-control" id="tasa_r" :value="formatNumber(oRet.tasa, 4)" readonly>
            </div>
            <div class="col-md-4">
                <label for="base_impuesto_r" class="form-label">Base</label>
                <input style="text-align: right" type="text" class="form-control" id="base_impuesto_r" :value="formatCurrency(oRet.base)" readonly>
            </div>
            <div class="col-md-4">
                <label for="importe_total_r" class="form-label">Importe total</label>
                <input style="text-align: right" type="text" class="form-control" id="importe_total_r" :value="formatCurrency(oRet.importe)" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <label for=""></label>
            </div>
        </div>
    </div>
    <br>
</div>
<div class="row">
    <div class="col-9"></div>
    <div class="col-3">
        <button type="button" class="btn btn-success" v-on:click="addConcept()">Nuevo concepto</button>
    </div>
</div>
<br>