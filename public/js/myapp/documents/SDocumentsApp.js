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
        lTrailers: oServerData.lTrailers,
        lFigures: oServerData.lFigures,
        lPayMethods: oServerData.lPayMethods,
        lPayForms: oServerData.lPayForms,
        lCurrencies: oServerData.lCurrencies,
        lCarrierSeries: oServerData.lCarrierSeries,
        oVehicle: oServerData.oVehicle,
        oFigure: oServerData.oFigure,
        lSelectedTrailers: oServerData.oData.lTrailers,
        oCfdiData: {}
    },
    methods: {
        addTrailer() {
            this.lSelectedTrailers.push({ oTrailer: 0 });
        },
        formatCurrency(value) {
            return "$ " + value.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
        },
        formatNumber(value, decimals) {
            let num = numbro(value).format({ thousandSeparated: false, mantissa: decimals })
            return num;
        },
        validateAll() {
            if (this.oData.localCurrencyCode != this.oData.currency && this.oData.tipoCambio == 1) {
                alert("El tipo de cambio es 1, no se puede usar la moneda local");
                return false;
            }

            return true;
        },
        setData() {
            let oCfdiData = {};

            oCfdiData.oVehicle = this.oVehicle;
            oCfdiData.oFigure = this.oFigure;
            oCfdiData.lTrailers = this.lSelectedTrailers;
            oCfdiData.oData = this.oData;
            oCfdiData.idDocument = this.idDocument;

            let sDta = JSON.stringify(oCfdiData);
            $("#the_cfdi_data").val(sDta);
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
        }
    },
});