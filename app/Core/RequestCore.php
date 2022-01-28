<?php namespace App\Core;

use App\Models\Carrier;

class RequestCore {

    public static function requestToJson($oDocument, $oRequest, $lCurrencies)
    {
        $oConfigurations = \App\Utils\Configuration::getConfigurations();
        $lUnits = \DB::table('sat_units AS u')
                        ->where('u.is_deleted', false)
                        ->selectRaw('key_code, CONCAT(u.key_code, " - ", u.description) AS _description')
                        ->pluck('_description', 'key_code');

        $oObjData = new \stdClass();

        $oObjData->localCurrency = $lCurrencies[$oConfigurations->localCurrency];
        $oObjData->localCurrencyCode = $oConfigurations->localCurrency;

        /**
         * Encabezado
         */
        //*********************************************************************************************
        $oObjData->cfdiVersion = "4.0";
        $oObjData->tipoDeComprobante = $oConfigurations->cfdi4_0->tipoComprobante;
        $oObjData->dtDate = $oDocument->dt_request;
        $oObjData->serie = "A";
        $oObjData->folio = "1";
        $oObjData->lugarExpedicion = $oConfigurations->cfdi4_0->lugarExpedicion;
        $oObjData->objetoImp = $oConfigurations->cfdi4_0->objetoImp;
        $oObjData->formaPago = $oRequest->formaPago;
        $oObjData->metodoPago = $oRequest->metodoPago;
        $oObjData->currency = $oRequest->moneda;
        $oObjData->tipoCambio = isset($oRequest->tipoCambio) && $oRequest->tipoCambio > 1 ? $oRequest->tipoCambio : 1;
        $oObjData->tipoCambioReadonly = $oObjData->currency == $oConfigurations->localCurrency;
        $oObjData->subTotal = $oRequest->subTotal;
        $oObjData->discounts = 0;
        $oObjData->total = $oRequest->total;

        /**
         * Emisor
         */
        //*********************************************************************************************
        $oCarrier = Carrier::find($oDocument->carrier_id);
        $oEmisor = new \stdClass();
        $oEmisor->rfcEmisor = $oCarrier->fiscal_id;
        $oEmisor->nombreEmisor = $oCarrier->fullname;
        $oEmisor->regimenFiscal = "601";

        /**
         * Receptor
         */
        //*********************************************************************************************
        $oReceptor = new \stdClass();
        $oReceptor->rfcReceptor = $oConfigurations->cfdi4_0->rfc;
        $oReceptor->nombreReceptor = $oConfigurations->cfdi4_0->nombreReceptor;
        $oReceptor->regimenFiscalReceptor = $oConfigurations->cfdi4_0->regimenFiscalReceptor;
        $oReceptor->domicilioFiscalReceptor = $oConfigurations->cfdi4_0->domicilioFiscalReceptor;

        $oObjData->receptor = $oReceptor;

        $oObjData->emisor = $oEmisor;

        /**
         * Determinación de retenciones y traslados
         */
        $oObjData->retenciones = [];
        $oObjData->traslados = [];
        if (strlen($oReceptor->rfcReceptor) == 12 && strlen($oEmisor->rfcEmisor) == 13) {
            $oObjData->traslados[] = (object) [
                                                    "impuesto" => "002",
                                                    "tasa" => 0.16
                                                ];
            $oObjData->retenciones[] = (object) [
                                                    "impuesto" => "002",
                                                    "tasa" => 0.04
                                                ];
        }
        elseif (strlen($oReceptor->rfcReceptor) == 12 && strlen($oEmisor->rfcEmisor) == 12) {
            $oObjData->traslados[] = (object) [
                                                    "impuesto" => "002",
                                                    "tasa" => 0.16
                                                ];
        }

        $taxDescriptions = [];
        foreach ($oObjData->retenciones as $ret) {
            $oTax = \DB::table('sat_taxes')
                            ->where('key_code', $ret->impuesto)
                            ->first();

            if (array_key_exists($ret->impuesto, $taxDescriptions)) {
                continue;
            }
            else {
                $taxDescriptions[$oTax->key_code] = $oTax->key_code." - ".$oTax->description;
            }
        }
        
        /**
         * Conceptos
         */
        //*********************************************************************************************
        $nLocationsTemp = count($oRequest->ubicaciones);
        $nLocation = ceil($nLocationsTemp);
        $lConcepts = [];
        $iSource = 0;
        $iDestination = 1;
        $traveledDistance = 0;
        while ($iDestination < $nLocationsTemp) {
            $oLocSource = $oRequest->ubicaciones[$iSource];
            $oLocDest = $oRequest->ubicaciones[$iDestination];

            $oConcept = new \stdClass();

            $oConcept->quantity = 1;
            $oConcept->discount = 0;
            $oConcept->claveProdServ = $oConfigurations->cfdi4_0->claveServicio;
            $oConcept->description = $oConfigurations->cfdi4_0->prodServDescripcion;
            $oConcept->claveUnidad = $oConfigurations->cfdi4_0->claveUnidad;
            $oConcept->simboloUnidad = $oConfigurations->cfdi4_0->simboloUnidad;
            $oConcept->unidad = $oConfigurations->cfdi4_0->simboloUnidad;
            $oConcept->numIndentificacion = (date('YmdHis').'_'.(random_int(100, 999)));

            $oState = \DB::table('sat_states')
                            ->where('key_code', $oLocDest->domicilio->estado)
                            ->first();

            // En caso de que haya un error al obtener el estado, se asigna el estado por default
            if ($oState == null) {
                $oState = new \stdClass();
                $oState->rate = 1.000;
                $oState->distance = 10.000;
            }
        
            if ($oLocSource->tipoUbicacion == "Origen") {
                $oConcept->valorUnitario = $oConfigurations->tarifaBase * $oState->rate;
            }
            else {
                $oConcept->valorUnitario = $oConfigurations->tarifaBaseEscala * $oState->rate;
            }

            $oConcept->importe = $oConcept->valorUnitario * $oConcept->quantity;

            $oConcept->oImpuestos = new \stdClass();

            $oConcept->oImpuestos->lTraslados = [];
            foreach ($oObjData->traslados as $trasImp) {
                $oTraslado = new \stdClass();

                $oTraslado->base = $oConcept->importe;
                $oTraslado->impuesto = $trasImp->impuesto;
                $oTraslado->impuesto_name = $taxDescriptions[$trasImp->impuesto];
                $oTraslado->tasa = $trasImp->tasa;
                $oTraslado->importe = $oTraslado->base * $oTraslado->tasa;

                $oConcept->oImpuestos->lTraslados[] = $oTraslado;
            }

            $oConcept->oImpuestos->lRetenciones = [];
            foreach ($oObjData->retenciones as $retImp) {
                $oRetencion = new \stdClass();
                $oRetencion->base = $oConcept->importe;
                $oRetencion->impuesto = $retImp->impuesto;
                $oRetencion->impuesto_name = $taxDescriptions[$retImp->impuesto];
                $oRetencion->tasa = $retImp->tasa;
                $oRetencion->importe = $oRetencion->base * $oRetencion->tasa;

                $oConcept->oImpuestos->lRetenciones[] = $oRetencion;
            }

            $lConcepts[] = $oConcept;

            /**
             * Distancia recorrida
             */
            if ($oLocSource->tipoUbicacion == "Origen") {
                $oLocSource->distanciaRecorrida = 0;
                $oLocDest->distanciaRecorrida = $oState->distance;
            }
            else {
                if ($oLocSource->domicilio->estado == $oLocDest->domicilio->estado) {
                    $oLocDest->distanciaRecorrida = $oConfigurations->distanciaMinima;
                }
                else {
                    $oLocDest->distanciaRecorrida = $oState->distance > $traveledDistance ? $oState->distance - $traveledDistance : 0;
                }
            }

            $traveledDistance += $oLocDest->distanciaRecorrida;

            $iSource++;
            $iDestination++;
        }

        $oObjData->conceptos = $lConcepts;

        /**
         * Impuestos
         */
        //*********************************************************************************************
        $oObjData->totalImpuestosTrasladados = 0.00;
        $oObjData->totalImpuestosRetenidos = 0.00;

        foreach ($lConcepts as $oConcept) {
            foreach ($oConcept->oImpuestos->lTraslados as $traslado) {
                $oObjData->totalImpuestosTrasladados += $traslado->importe;
            }
            foreach ($oConcept->oImpuestos->lRetenciones as $retencion) {
                $oObjData->totalImpuestosRetenidos += $retencion->importe;
            }
        }

        /**
         * Carta Porte
         */
        //*********************************************************************************************
        $oObjData->oCartaPorte = new \stdClass();
        $oObjData->oCartaPorte->version = "2.0";
        $oObjData->oCartaPorte->totalDistancia = $traveledDistance;
        $oObjData->oCartaPorte->transpInternac = $oRequest->transpInternac;

        $oObjData->oCartaPorte->ubicaciones = $oRequest->ubicaciones;

        /**
         * Mercancías
         */
        //*********************************************************************************************
        $oObjData->oCartaPorte->mercancia = $oRequest->mercancia;
        $oUnitP = \DB::table('sat_units')->where('key_code', $oRequest->mercancia->unidadPeso)->first();
        $oObjData->oCartaPorte->mercancia->unidadPesoName = $oRequest->mercancia->unidadPeso." - ".$oUnitP->description;
        foreach ($oObjData->oCartaPorte->mercancia->mercancias as $merch) {
            $oItem = \DB::table('sat_items')->where('key_code', $merch->bienesTransp)->first();
            $merch->descripcion = $oItem->description;

            $oUnit = \DB::table('sat_units')->where('key_code', $merch->claveUnidad)->first();
            $merch->simboloUnidad = $oUnit->symbol;
            $merch->descripcionUnidad = $oUnit->description;


            $merch->currencyName = $lCurrencies[$merch->moneda];
            $merch->unitName = $lUnits[$merch->claveUnidad];
        }

        /**
         * Ubicaciones
         */
        //*********************************************************************************************
        $lCountries = \DB::table('sat_fiscal_addresses')
                            ->selectRaw('key_code, CONCAT(key_code, " - ", description) AS _description')
                            ->pluck('_description', 'key_code');

        $lStates = \DB::table('sat_states')
                            ->selectRaw('key_code, CONCAT(key_code, " - ", state_name) AS _description')
                            ->pluck('_description', 'key_code');

        $lMunicipies = \DB::table('sat_municipalities AS m')
                            ->join('sat_states AS s', 's.id', '=', 'm.state_id');

        foreach ($oObjData->oCartaPorte->ubicaciones as $location) {
            $location->domicilio->paisName = $lCountries[$location->domicilio->pais];
            $location->domicilio->estadoName = $lStates[$location->domicilio->estado];

            $lMun = clone $lMunicipies;
            $municipio = $lMun->where('s.key_code', $location->domicilio->estado)
                                ->where('m.key_code', $location->domicilio->municipio)->first();

            if ($municipio != null) {
                $location->domicilio->municipioName = $location->domicilio->municipio." - ".$municipio->municipality_name;
            }
            else {
                $location->domicilio->municipioName = $location->domicilio->municipio;
            }
        }

        return $oObjData;
    }
}