Vue.component('my-currency-input', {
    props: ['value'],
    template: `<input style="text-align: right;" v-on:keyup="$emit('keyup', $event.target.value)" class="form-control" type="text" v-model="displayValue" @blur="isInputActive = false" @focus="isInputActive = true"/>`,
    data: function() {
        return {
            isInputActive: false
        }
    },
    computed: {
        displayValue: {
            get: function() {
                if (this.isInputActive) {
                    // Cursor is inside the input field. unformat display value for user
                    return this.value.toString()
                } else {
                    this.value = parseFloat(this.value);
                    // User is not modifying now. Format display value for user interface
                    return "$ " + this.value.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
                }
            },
            set: function(modifiedValue) {
                // Recalculate value after ignoring "$" and "," in user input
                let newValue = parseFloat(modifiedValue.replace(/[^\d\.]/g, ""))
                    // Ensure that it is not NaN
                if (isNaN(newValue)) {
                    newValue = 0
                }
                // Note: we cannot set this.value as it is a "prop". It needs to be passed to parent component
                // $emit the event so that parent component gets it
                this.$emit('input', newValue)
            }
        }
    }
});

var app = new Vue({
    el: '#cfdi_app',
    data: {
        idDocument: oServerData.idDocument,
        iFolio: oServerData.oData.folio,
        oData: oServerData.oData,
        lVehicles: oServerData.lVehicles,
        lVehicleKeys: oServerData.lVehicleKeys,
        lTrailers: oServerData.lTrailers,
        lSuburbs: oServerData.lSuburbs,
        lFigures: oServerData.lFigures,
        lPayMethods: oServerData.lPayMethods,
        lPayForms: oServerData.lPayForms,
        lCurrencies: oServerData.lCurrencies,
        lCarrierSeries: oServerData.lCarrierSeries,
        oVehicle: oServerData.oVehicle,
        oTrailer: oServerData.oTrailer,
        bShipCfg: oServerData.bShipCfg,
        bCustomAtts: oServerData.bCustomAtts,
        sShipType: oServerData.oData.shipType,
        oFigure: oServerData.oFigure,
        lSelectedTrailers: oServerData.oData.lTrailers != undefined && oServerData.oData.lTrailers.lenght > 0 ? oServerData.oData.lTrailers : [],
        oCfdiData: {}
    },
    mounted() {
        if (this.oData.vehKeyId > 0) {
            this.oVehicle.veh_key_id = this.oData.vehKeyId;
        }
        this.changeSerie(this.oData.serie);
        this.setLocationsIds();
    },
    methods: {
        addConcept() {
            let oCpt = this.oData.conceptos[0];
            let oNewCpt = {
                claveProdServ: oCpt.claveProdServ,
                claveUnidad: oCpt.claveUnidad,
                description: oCpt.description,
                discount: 0,
                importe: oCpt.importe,
                numIndentificacion: oCpt.numIndentificacion,
                oImpuestos: {
                    lRetenciones: this.calculateRetenciones(oCpt.importe),
                    lTraslados: this.calculateTraslados(oCpt.importe)
                },
                quantity: oCpt.quantity,
                simboloUnidad: oCpt.simboloUnidad,
                unidad: oCpt.unidad,
                valorUnitario: oCpt.valorUnitario
            };

            this.oData.conceptos.push(oNewCpt);
            this.onChangeAmount();
        },
        removeConcept(iIndex) {
            if (this.oData.conceptos.length == 0) {
                SGui.showError("No puede eliminar el último concepto");
                return;
            }

            this.oData.conceptos.splice(iIndex, 1);
            this.onChangeAmount();
        },
        addTrailer() {
            if(this.oVehicle != null && this.oVehicle != undefined){
                if(this.oVehicle.vcfg_trailer != 0){
                    this.lSelectedTrailers.push({ oTrailer: 0 });
                }
            }
        },
        removeTrailer(index) {
            this.lSelectedTrailers.splice(this.lSelectedTrailers.indexOf(index), 1);
        },
        cleanTrailer(){
            this.lSelectedTrailers = [];
        },
        formatCurrency(value) {
            return "$ " + value.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
        },
        formatNumber(value, decimals) {
            let num = numbro(value).format({ thousandSeparated: false, mantissa: decimals })
            return num;
        },
        clickAndFocus(idButton, idInput) {
            if (document.getElementById(idButton) == null) {
                return;
            }
            document.getElementById(idButton).click();

            if (document.getElementById(idInput) == null) {
                return;
            }
            document.getElementById(idInput).focus();
        },
        setData() {
            let oCfdiData = {};

            oCfdiData.oVehicle = this.oVehicle;
            oCfdiData.oFigure = this.oFigure;
            oCfdiData.lTrailers = this.lSelectedTrailers;
            oCfdiData.oData = this.oData;
            oCfdiData.idDocument = this.idDocument;
            oCfdiData.oData.shipType = this.sShipType;

            let sDta = JSON.stringify(oCfdiData);
            $("#the_cfdi_data").val(sDta);
        },
        calculateRetenciones(amount) {
            let retenciones = [];
            for (const retencion of this.oData.retenciones) {
                let oRetencion = new Object();
                oRetencion.base = amount;
                oRetencion.impuesto = retencion.impuesto;
                oRetencion.impuesto_name = retencion.tax_description;
                oRetencion.tasa = retencion.tasa;
                oRetencion.importe = oRetencion.base * oRetencion.tasa;

                retenciones.push(oRetencion);
            }

            return retenciones;
        },
        calculateTraslados(amount) {
            let traslados = [];
            for (const traslado of this.oData.traslados) {
                let oTraslado = new Object();
                oTraslado.base = amount;
                oTraslado.impuesto = traslado.impuesto;
                oTraslado.impuesto_name = traslado.tax_description;
                oTraslado.tasa = traslado.tasa;
                oTraslado.importe = oTraslado.base * oTraslado.tasa;

                traslados.push(oTraslado);
            }

            return traslados;
        },
        onChangeAmount() {
            let subtotal = 0;
            let discounts = 0;
            let traslados = 0;
            let retenciones = 0;

            for (let concept of this.oData.conceptos) {
                concept.importe = concept.valorUnitario * concept.quantity;
                subtotal += concept.importe;
                discounts += concept.discount;

                for (let traslado of concept.oImpuestos.lTraslados) {
                    traslado.base = concept.importe;
                    traslado.importe = traslado.tasa * traslado.base;

                    traslados += traslado.importe;
                }

                for (let retencion of concept.oImpuestos.lRetenciones) {
                    retencion.base = concept.importe;
                    retencion.importe = retencion.tasa * retencion.base;

                    retenciones += retencion.importe;
                }
            }

            this.oData.subTotal = subtotal;
            this.oData.discounts = discounts;
            this.oData.totalImpuestosTrasladados = traslados;
            this.oData.totalImpuestosRetenidos = retenciones;
            this.oData.total = subtotal - discounts + traslados - retenciones;
        },
        changeSerie(serie) {
            for (const ser of this.lCarrierSeries) {
                if (ser.prefix == serie) {
                    this.oData.folio = ser.folio;
                    break;
                }
            }
        },
        onChangeDistance() {
            this.oData.oCartaPorte.totalDistancia = 0.0;
            for (const oLocation of this.oData.oCartaPorte.ubicaciones) {
                this.oData.oCartaPorte.totalDistancia += parseFloat(oLocation.distanciaRecorrida);
            }
        },
        changeShipType() {
            this.setLocationsIds();
        },
        onVehicleKeyChange() {
            this.cleanTrailer();
            this.setLocationsIds();
        },
        setLocationsIds() {
            let shipDigit = 0;
            if (this.oTrailer != null) {
                if(this.oVehicle.vcfg_trailer != 0){
                    for(let trailer of this.oTrailer){
                        this.lSelectedTrailers.push( { oTrailer: trailer } );
                    }
                }else if(this.oVehicle.vcfg_trailer == 0){
                    this.cleanTrailer();    
                }
            }else{
                this.cleanTrailer();
            }
            for (const oVehKey of this.lVehicleKeys) {
                if (oVehKey.id_key == this.oVehicle.veh_key_id) {
                    shipDigit = this.sShipType == "F" ? oVehKey.foreign_digit : oVehKey.local_digit;
                    break;
                }
            }
            for (let oLocation of this.oData.oCartaPorte.ubicaciones) {
                oLocation.IDUbicacion = oLocation.IDUbicacion.slice(0, 2) + shipDigit + oLocation.IDUbicacion.slice(3);
            }
        },
        validateAll() {
            // Se comenta porque la serie y el folio son opcionales
            // if (this.oData.serie == undefined || this.oData.serie == "") {
            //     SGui.showError("Debe seleccionar una serie para el documento");
            //     this.clickAndFocus("btnHeader", "serie");
            //     return false;
            // }

            if (this.oData.folio < 0) {
                SGui.showError("Debe ingresar un folio mayor que 0 o dejarlo en blanco");
                this.clickAndFocus("btnHeader", "folio");
                return false;
            }

            if (this.oData.formaPago == undefined || this.oData.formaPago == "") {
                SGui.showError("Debe seleccionar una forma de pago para el documento");
                this.clickAndFocus("btnHeader", "forma_pago");
                return false;
            }

            if (this.oData.metodoPago == undefined || this.oData.metodoPago == "") {
                SGui.showError("Debe seleccionar un método de pago para el documento");
                this.clickAndFocus("btnHeader", "metodo_pago");
                return false;
            }

            if (this.oData.localCurrencyCode != this.oData.currency && this.oData.tipoCambio == 1) {
                SGui.showError("El tipo de cambio es 1, no se puede usar la moneda local");
                this.clickAndFocus("btnHeader", "tc");
                return false;
            }

            let idxConcept = 0;
            let regEx = /^[0-9a-zA-Z]+$/;
            for (const oConcept of this.oData.conceptos) {
                if (oConcept.valorUnitario <= 0) {
                    SGui.showError("El concepto " + (oConcept.description) + " no tiene un valor unitario válido");
                    this.clickAndFocus("btnConcepts", "valor_unitario" + idxConcept);
                    return false;
                }
                if (oConcept.discount < 0) {
                    SGui.showError("El concepto " + (oConcept.description) + " no tiene un descuento válido");
                    this.clickAndFocus("btnConcepts", "discount" + idxConcept);
                    return false;
                }

                if (!oConcept.numIndentificacion.match(regEx)) {
                    SGui.showError("El concepto " + (oConcept.description) + " no tiene un número de identificación válido");
                    this.clickAndFocus("btnConcepts", "discount" + idxConcept);
                    return false;
                }

                idxConcept++;
            }

            for (let index = 0; index < this.oData.oCartaPorte.ubicaciones.length; index++) {
                let loc = this.oData.oCartaPorte.ubicaciones[index];
                if (index > 0 && loc.distanciaRecorrida <= 0) {
                    SGui.showError("La distancia recorrida en la ubicación " + (index + 1) + " es inválida");
                    this.clickAndFocus("btnLocations", "distanceId" + index);
                    return false;
                }
                if (loc.fechaHoraSalidaLlegada == null || loc.fechaHoraSalidaLlegada == "") {
                    SGui.showError("La fecha y hora de salida/llegada en la ubicación " + (index + 1) + " es inválida");
                    this.clickAndFocus("btnLocations", "dateTimeLocId" + index);
                    return false;
                }
                let oDate = moment(loc.fechaHoraSalidaLlegada, "YYYY-MM-DDTHH:mm:ss");
                if (oDate.year() < 2000 || oDate.year() > 3000) {
                    SGui.showError("La fecha y hora de salida/llegada en la ubicación " + (index + 1) + " es inválida");
                    this.clickAndFocus("btnLocations", "dateTimeLocId" + index);
                    return false;
                }
                if(this.lSuburbs[index].length > 0){
                    if(loc.domicilio.colonia == null){
                        SGui.showError("Debe seleccionar una colonia en la ubicación " + (index + 1));
                        this.clickAndFocus("btnLocations", "distanceId" + index);
                        return false;
                    }
                }
            }

            if (this.oVehicle.id_vehicle == undefined || this.oVehicle.id_vehicle <= 0) {
                SGui.showError("Debe seleccionar un transporte");
                this.clickAndFocus("btnTransport", "autotransporte");
                return false;
            }

            if (this.lSelectedTrailers.length > 0) {
                let trailers = [];
                for (let i = 0; i < this.lSelectedTrailers.length; i++) {
                    if (this.lSelectedTrailers[i].oTrailer.id_trailer in trailers) {
                        SGui.showError("No se puede agregar más de una vez el mismo remolque");
                        this.clickAndFocus("btnTransport", "labRems");
                        return false;
                    }

                    trailers[this.lSelectedTrailers[i].oTrailer.id_trailer] = true;
                }
            }

            if(this.lSelectedTrailers.length > 0){
                for (let i = 0; i < this.lSelectedTrailers.length; i++) {
                    if (this.lSelectedTrailers[i].oTrailer.id_trailer == undefined) {
                        SGui.showError("No se seleccionó un remolque");
                        this.clickAndFocus("btnTransport", "labRems");
                        return false;
                    }
                }
            }

            if(this.oVehicle.vcfg_trailer == 1){
                var haveTrailer = false;
                if(this.lSelectedTrailers.length < 1){
                    SGui.showError("El tipo de autotransporte seleccionado requiere de un remolque");
                    this.clickAndFocus("btnTransport", "labRems");
                    return false;
                }else if(this.lSelectedTrailers.length > 0){
                    for (let i = 0; i < this.lSelectedTrailers.length; i++) {
                        if (this.lSelectedTrailers[i].oTrailer != 0) {
                            haveTrailer = true;
                        }
                    }
                }
                if(!haveTrailer){
                    SGui.showError("El tipo de autotransporte seleccionado requiere de un remolque");
                    this.clickAndFocus("btnTransport", "labRems");
                    return false;
                }
            }

            if (this.oFigure.id_trans_figure == undefined || this.oFigure.id_trans_figure <= 0) {
                SGui.showError("Debe seleccionar una figura de transporte");
                this.clickAndFocus("btnFigure", "figure");
                return false;
            }

            return true;
        }
    },
});