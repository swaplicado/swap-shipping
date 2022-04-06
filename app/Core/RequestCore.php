<?php namespace App\Core;

use App\Models\Carrier;
use Carbon\Carbon;
use App\Utils\SFormats;
use App\Utils\GralUtils;

class RequestCore {

    const MORAL = 12;
    const FISICA = 13;

    public static function adaptRequest($oRequest)
    {
        $mercanciasTodas = [];
        foreach ($oRequest->ubicaciones as $index => $location) {
            $location->rFCRemitenteDestinatario = $location->rfcRemitenteDestinatario;
            foreach ($location->mercancias as $merch) {
                $merch->index = $index + 1;
                if (! isset($mercanciasTodas[$merch->bienesTransp])) {
                    $mercanciasTodas[$merch->bienesTransp] = new \stdClass();
                    $mercanciasTodas[$merch->bienesTransp]->merchs = [];
                }
                $mercanciasTodas[$merch->bienesTransp]->merchs[] = $merch;
            }
        }

        foreach ($mercanciasTodas as $cveBien => $oMerch) {
            $oMerch->cantidad = 0;
            $oMerch->valorMercancia = 0;
            $oMerch->pesoEnKg = 0;
            $oMerch->bienesTransp = $cveBien;
            $oMerch->claveUnidad = $oMerch->merchs[0]->claveUnidad;
            $oMerch->moneda = $oMerch->merchs[0]->moneda;

            // agrupar por claveUnidad y destino
            $merchsAgrupadas = collect($oMerch->merchs);
            $lGrouped = $merchsAgrupadas->groupBy('index')->map(function ($row) {
                        $registry = (object) [
                            'cantidad' => $row->sum('cantidad'),
                            'valorMercancia' => $row->sum('valorMercancia'),
                            'pesoEnKg' => $row->sum('pesoEnKg')
                        ];

                return $registry;
            });

            foreach ($lGrouped as $merch) {
                $oMerch->cantidad += $merch->cantidad;
                $oMerch->valorMercancia += $merch->valorMercancia;
                $oMerch->pesoEnKg += $merch->pesoEnKg;
            }

            $oMerch->lGrouped = $lGrouped;
        }

        $oRequest->mercanciasTodas = $mercanciasTodas;

        $oDbLoc = \DB::table('f_local_origins')
                        ->where('origin_code', $oRequest->idOrigen)
                        ->first();

        if ($oDbLoc == null) {
            return null;
        }

        $oOriginLocation = new \stdClass;
        $oOriginLocation->tipoUbicacion = "Origen";
        $oOriginLocation->rFCRemitenteDestinatario = $oDbLoc->rfcRemitenteDestinatario;
        $oOriginLocation->domicilio = new \stdClass;
        $oOriginLocation->domicilio->estado = $oDbLoc->estado;
        $oOriginLocation->domicilio->codigoPostal = $oDbLoc->codigoPostal;
        $oOriginLocation->domicilio->municipio = $oDbLoc->municipio;
        $oOriginLocation->domicilio->calle = $oDbLoc->calle;
        $oOriginLocation->domicilio->numeroExterior = $oDbLoc->numeroExterior;
        $oOriginLocation->domicilio->localidad = $oDbLoc->localidad;
        $oOriginLocation->domicilio->numeroInterior = $oDbLoc->numeroInterior;
        $oOriginLocation->domicilio->referencia = $oDbLoc->referencia;
        $oOriginLocation->domicilio->colonia = $oDbLoc->colonia;
        $oOriginLocation->domicilio->pais = $oDbLoc->pais;

        array_unshift($oRequest->ubicaciones, $oOriginLocation);

        return $oRequest;
    }

    /**
     * Transforma un objeto Request a un objeto stdClass MongoDB para su edición
     *
     * @param Document $oDocument
     * @param StdClass $oRequest
     * @param array $lCurrencies
     * @param Vehicle $oVehicle puede ser null
     * 
     * @return stdClass
     */
    public static function requestToCfdiObject($oDocument, $oRequest, $lCurrencies, $oVehicle)
    {
        $oConfigurations = \App\Utils\Configuration::getConfigurations();
        $lUnits = \DB::table('sat_units AS u')
                        ->where('u.is_deleted', false)
                        ->selectRaw('key_code, CONCAT(u.key_code, " - ", u.description) AS _description')
                        ->pluck('_description', 'key_code');

        $oObjData = new \stdClass();

        $oObjData->localCurrency = $lCurrencies[$oConfigurations->localCurrency];
        $oObjData->localCurrencyCode = $oConfigurations->localCurrency;

        $oDate = Carbon::parse($oDocument->requested_at);

        if(is_null($oVehicle)){
            $oVehicle = new \stdClass();
            $oVehicle->veh_key_id = null;
        }

        /**
         * Encabezado
         */
        //*********************************************************************************************
        $oObjData->cfdiVersion = "4.0";
        $oObjData->tipoDeComprobante = $oConfigurations->cfdi4_0->tipoComprobante;
        $oObjData->dtDate = $oDocument->requested_at;
        $oObjData->serie = "";
        $oObjData->folio = 0;
        $oObjData->lugarExpedicion = $oConfigurations->cfdi4_0->lugarExpedicion;
        $oObjData->objetoImp = $oConfigurations->cfdi4_0->objetoImp;
        $oObjData->usoCfdi = $oConfigurations->cfdi4_0->usoCFDI;
        $oObjData->formaPago = $oConfigurations->formaPago;
        $oObjData->metodoPago = $oConfigurations->metodoPago;
        $oObjData->currency = $oConfigurations->localCurrency;
        $oObjData->tipoCambio = isset($oRequest->tipoCambio) && $oRequest->tipoCambio > 1 ? $oRequest->tipoCambio : 1;
        $oObjData->subTotal = 0;
        $oObjData->discounts = 0;
        $oObjData->total = 0;

        /**
         * Emisor
         */
        //*********************************************************************************************
        $oCarrier = Carrier::find($oDocument->carrier_id);
        $oEmisor = new \stdClass();
        $oEmisor->rfcEmisor = $oCarrier->fiscal_id;
        $oEmisor->nombreEmisor = $oCarrier->fullname;
        $oEmisor->regimenFiscal = $oCarrier->tax_regime->key_code;

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
         * Documento, datos internos
         */
        $oObjData->vehKeyId = 0;
        $oObjData->shippingFolio = $oRequest->embarque;

        /**
         * Determinación de retenciones y traslados
         */
        $sDate = $oDate->format('Y-m-d');
        $lCfgTaxes = \DB::table('f_tax_configurations AS cfg')
                        ->leftjoin('sat_tax_regimes AS reg', 'cfg.fiscal_regime_id', '=', 'reg.id')
                        ->leftjoin('sat_taxes AS tax', 'cfg.tax_id', '=', 'tax.id')
                        ->select('cfg.*', 'reg.key_code AS regimen_fiscal', 'tax.key_code AS tax_key_code', 'tax.description AS tax_description')
                        ->where(function ($query) use ($sDate) {
                            $query->where(function ($query) use ($sDate) {
                                $query->where('cfg.date_from', '<=', $sDate)
                                        ->where(function ($query) use ($sDate) {
                                            $query->where('cfg.date_to', '>=', $sDate)
                                                ->orWhereNull('cfg.date_to');
                                        })
                                        ->orWhereNull('cfg.date_from');
                            });
                        })
                        ->where('cfg.is_deleted', false)
                        ->where(function ($query) use ($oCarrier) {
                                $query->where('cfg.carrier_id', $oCarrier->id_carrier)
                                        ->orWhereNull('cfg.carrier_id');
                        })
                        ->orderBy('cfg.config_type', 'DESC')
                        ->orderBy('cfg.date_from', 'ASC')
                        ->get();

        $oObjData->traslados = [];
        $oObjData->retenciones = [];
        $emisor = strlen($oEmisor->rfcEmisor) == self::FISICA ? "fisica" : "moral";
        $receptor = strlen($oReceptor->rfcReceptor) == self::FISICA ? "fisica" : "moral";
        foreach ($lCfgTaxes as $cfgTax) {
            $oTaxCfg = new \stdClass();
            if (! ($cfgTax->person_type_emisor == null || ($cfgTax->person_type_emisor != null && $cfgTax->person_type_emisor == $emisor))) {
                continue;
            }
            
            if (! ($cfgTax->person_type_receptor == null || ($cfgTax->person_type_receptor != null && $cfgTax->person_type_receptor == $receptor))) {
                continue;
            }

            if (! ($cfgTax->fiscal_regime_id == null || ($cfgTax->fiscal_regime_id != null && $cfgTax->regimen_fiscal == $oReceptor->regimenFiscalReceptor))) {
                continue;
            }

            //prov_serv_id
            if (! ($cfgTax->concept_id == null || ($cfgTax->concept_id != null && $cfgTax->concept_id == $oCarrier->prov_serv_id))) {
                continue;
            }

            // if (! ($cfgTax->group_id == null || ($cfgTax->group_id != null && $cfgTax->group_id == ???))) {
            //     continue;
            // }

            $oTaxCfg->impuesto = $cfgTax->tax_key_code;
            $oTaxCfg->tasa = $cfgTax->rate;
            $oTaxCfg->tax_description = $cfgTax->tax_description;

            if ($cfgTax->config_type == "traslado") {
                $oObjData->traslados[] = $oTaxCfg;
            }
            else {
                $oObjData->retenciones[] = $oTaxCfg;
            }
        }
        
        /**
         * Conceptos
         */
        //*********************************************************************************************
        $lMerchsLocation = [];
        $nLocationsTemp = count($oRequest->ubicaciones);
        $nLocation = ceil($nLocationsTemp);
        $lConcepts = [];
        $iSource = 0;
        $iDestination = 1;
        $traveledDistance = 0;
        $dSubTotal = 0;
        while ($iDestination < $nLocationsTemp) {
            $oLocSource = $oRequest->ubicaciones[$iSource];
            $oLocDest = $oRequest->ubicaciones[$iDestination];

            $oConcept = new \stdClass();

            $oConcept->quantity = 1;
            $oConcept->discount = 0;
            $oConcept->claveProdServ = $oCarrier->prod_serv->key_code;
            $oConcept->claveUnidad = $oConfigurations->cfdi4_0->claveUnidad;
            $oConcept->simboloUnidad = $oConfigurations->cfdi4_0->simboloUnidad;
            $oConcept->unidad = $lUnits[$oConfigurations->cfdi4_0->claveUnidad];
            $oConcept->numIndentificacion = (date('YmdHis').''.(random_int(100, 999)));

            $oState = \DB::table('sat_states')
                            ->where('key_code', $oLocDest->domicilio->estado)
                            ->first();

            $oMun = \DB::table('sat_municipalities')
                            ->where('key_code', $oLocDest->domicilio->municipio)
                            ->where('state_id', $oState->id)
                            ->first();

            // En caso de que haya un error al obtener el estado, se asigna el estado por default
            if ($oState == null) {
                $oState = new \stdClass();
                $oState->rate = 1.000;
                $oState->distance = 10.000;
            }

            $freightType = "";
            if ($iDestination == $nLocationsTemp - 1) {
                $infoRate = GralUtils::getInfoRate(
                    $oCarrier->id_carrier,
                    $oLocDest->domicilio->estado,
                    $oLocDest->domicilio->municipio,
                    $oLocDest->domicilio->codigoPostal,
                    $oVehicle->veh_key_id
                );
                $rate = 0;
                if(!is_null($infoRate)){
                    $rate = $infoRate->rate;
                }
                // $oConcept->valorUnitario = $oConfigurations->tarifaBase * $oState->rate;
                $oConcept->valorUnitario = $rate;
                $freightType = "Destino";
            }
            else {
                $infoRate = GralUtils::getInfoRate(
                    $oCarrier->id_carrier,
                    $oLocDest->domicilio->estado,
                    $oLocDest->domicilio->municipio,
                    $oLocDest->domicilio->codigoPostal,
                    $oVehicle->veh_key_id,
                    1
                );
                $rate = 0;
                if(!is_null($infoRate)){
                    $rate = $infoRate->rate;
                }
                // $oConcept->valorUnitario = $oConfigurations->tarifaBaseEscala * $oState->rate;
                $oConcept->valorUnitario = $rate;
                $freightType = "Reparto";
            }
            $oConcept->isOfficialRate = false;

            $oConcept->description = $oConfigurations->cfdi4_0->prodServDescripcion." - ".$freightType.
                                            " [".$oLocSource->domicilio->municipio."(".$oLocSource->domicilio->estado.") - ".
                                            $oLocDest->domicilio->municipio."(".$oLocDest->domicilio->estado.")]";

            $oConcept->importe = $oConcept->valorUnitario * $oConcept->quantity;

            $oConcept->oImpuestos = new \stdClass();

            $oConcept->oImpuestos->lTraslados = [];
            foreach ($oObjData->traslados as $trasImp) {
                $oTraslado = new \stdClass();

                $oTraslado->base = $oConcept->importe;
                $oTraslado->impuesto = $trasImp->impuesto;
                $oTraslado->impuesto_name = $trasImp->tax_description;
                $oTraslado->tasa = $trasImp->tasa;
                $oTraslado->importe = $oTraslado->base * $oTraslado->tasa;

                $oConcept->oImpuestos->lTraslados[] = $oTraslado;
            }

            $oConcept->oImpuestos->lRetenciones = [];
            foreach ($oObjData->retenciones as $retImp) {
                $oRetencion = new \stdClass();
                $oRetencion->base = $oConcept->importe;
                $oRetencion->impuesto = $retImp->impuesto;
                $oRetencion->impuesto_name = $retImp->tax_description;
                $oRetencion->tasa = $retImp->tasa;
                $oRetencion->importe = $oRetencion->base * $oRetencion->tasa;

                $oConcept->oImpuestos->lRetenciones[] = $oRetencion;
            }

            // Atributos del concepto que no se incluyen en el XML
            if (env('WITH_CUSTOM_ATTRIBUTES')) {
                $oConcept->oCustomAttributes = new \stdClass();
                $oConcept->oCustomAttributes->customerName = isset($oLocDest->nombreRFC) ? strtoupper($oLocDest->nombreRFC) : "";
                $oConcept->oCustomAttributes->customerFiscalId = isset($oLocDest->rfcRemitenteDestinatario) ? $oLocDest->rfcRemitenteDestinatario : "";
                $oConcept->oCustomAttributes->shippingOrders = isset($oLocDest->talones) ? $oLocDest->talones : "";
                $oMunicipality = GralUtils::getMunicipalityByCode($oLocDest->domicilio->estado, $oLocDest->domicilio->municipio);
                $oConcept->oCustomAttributes->destinyName = $oMunicipality == null ? "" : strtoupper($oMunicipality->municipality_name);
                if ($oVehicle != null) {
                    $rate_key = "";
                    
                    if($freightType == "Reparto"){
                        $rate_key = "Reparto";
                    }else if(!is_null($infoRate)){
                        if(!is_null($infoRate->id_rate)){
                            $rate_key = $infoRate->id_rate;
                        }else{
                            $rate_key = GralUtils::generateRateKey(
                                $oLocDest->domicilio->estado,
                                $oLocDest->domicilio->municipio,
                                $oLocDest->domicilio->codigoPostal,
                                $oVehicle->veh_key_id);    
                        }
                    }else{
                        $rate_key = GralUtils::generateRateKey(
                            $oLocDest->domicilio->estado,
                            $oLocDest->domicilio->municipio,
                            $oLocDest->domicilio->codigoPostal,
                            $oVehicle->veh_key_id);
                    }
                    $oConcept->oCustomAttributes->rateCode = $rate_key;
                }
                else {
                    $oConcept->oCustomAttributes->rateCode = "";
                }
            }

            $dSubTotal += $oConcept->importe;
            $lConcepts[] = $oConcept;

            /**
             * Distancia recorrida
             */
            if ($oLocSource->tipoUbicacion == "Origen") {
                $oLocSource->distanciaRecorrida = SFormats::formatNumber(0, 3);
                $oLocDest->distanciaRecorrida = SFormats::formatNumber($oMun->distance, 3);
            }
            else {
                if ($oLocSource->domicilio->municipio == $oLocDest->domicilio->municipio) {
                    $oLocDest->distanciaRecorrida = SFormats::formatNumber($oConfigurations->distanciaMinima, 3);
                }
                else {
                    $oLocDest->distanciaRecorrida = SFormats::formatNumber($oMun->distance > $traveledDistance ? $oMun->distance - $traveledDistance : 0, 3);
                }
            }

            $traveledDistance += $oLocDest->distanciaRecorrida;
            $iSource++;
            $iDestination++;
        }

        $oObjData->shipType = GralUtils::getShipType($oState->id, $oMun->id, $oLocDest->domicilio->codigoPostal);

        $oObjData->conceptos = $lConcepts;

        /**
         * Impuestos
         */
        //*********************************************************************************************
        $oObjData->totalImpuestosTrasladados = 0.00;
        $oObjData->totalImpuestosRetenidos = 0.00;

        foreach ($oObjData->conceptos as $oConcept) {
            foreach ($oConcept->oImpuestos->lTraslados as $traslado) {
                $oObjData->totalImpuestosTrasladados += $traslado->importe;
            }
            foreach ($oConcept->oImpuestos->lRetenciones as $retencion) {
                $oObjData->totalImpuestosRetenidos += $retencion->importe;
            }
        }

        /**
         * Totales
         */
        //*********************************************************************************************
        $oObjData->subTotal = $dSubTotal;
        $oObjData->total = $dSubTotal + $oObjData->totalImpuestosTrasladados - $oObjData->totalImpuestosRetenidos;

        /**
         * Carta Porte
         */
        //*********************************************************************************************
        $oObjData->oCartaPorte = new \stdClass();
        $oObjData->oCartaPorte->version = "2.0";
        $oObjData->oCartaPorte->totalDistancia = SFormats::formatNumber($traveledDistance, 3);
        $oObjData->oCartaPorte->transpInternac = isset($oRequest->transpInternac) ? $oRequest->transpInternac : "No";

        $oObjData->oCartaPorte->ubicaciones = $oRequest->ubicaciones;

        /**
         * Ubicaciones
         */
        //*********************************************************************************************
        $lCountries = \DB::table('sat_fiscal_addresses')
                            ->selectRaw('key_code, CONCAT(key_code, " - ", description) AS _description')
                            ->pluck('_description', 'key_code');

        $lStates = \DB::table('sat_states')->get()->keyBy('key_code');

        $lMunicipies = \DB::table('sat_municipalities AS m')
                            ->join('sat_states AS s', 's.id', '=', 'm.state_id');

        foreach ($oObjData->oCartaPorte->ubicaciones as $location) {
            $location->domicilio = $location->domicilio;
            $location->domicilio->paisName = $lCountries[$location->domicilio->pais];
            $location->domicilio->estadoName = $lStates[$location->domicilio->estado]->state_name;
            $location->domicilio->estadoId = $lStates[$location->domicilio->estado]->id;

            $lMun = clone $lMunicipies;
            $municipio = $lMun->where('s.key_code', $location->domicilio->estado)
                                ->where('m.key_code', $location->domicilio->municipio)
                                ->first();

            if ($municipio != null) {
                $location->domicilio->municipioName = $location->domicilio->municipio." - ".$municipio->municipality_name;
            }
            else {
                $location->domicilio->municipioName = $location->domicilio->municipio;
            }

            $startId = "";
            if ($location->tipoUbicacion == "Origen") {
                $startId = "OR";
            }
            else {
                $startId = "DE";
            }

            // Fecha-hora salida-llegada
            $oDate = Carbon::now();
            $location->fechaHoraSalidaLlegada = $oDate->format('Y-m-d').'T'.$oDate->format('H:i:s');

            $location->IDUbicacion = $startId.
                                    "1".
                                    str_pad($location->domicilio->estadoId, 2, "0", STR_PAD_LEFT).
                                    $location->domicilio->municipio;
        }

        /**
         * Mercancías
         */
        //*********************************************************************************************
        $oObjData->oCartaPorte->mercancia = new \stdClass();
        $oObjData->oCartaPorte->mercancia->unidadPeso = 'KGM';
        $oUnitP = \DB::table('sat_units')->where('key_code', $oObjData->oCartaPorte->mercancia->unidadPeso)->first();
        $dTotalPeso = 0;
        $oObjData->oCartaPorte->mercancia->unidadPesoName = $oObjData->oCartaPorte->mercancia->unidadPeso." - ".$oUnitP->description;
        $oObjData->oCartaPorte->mercancia->numTotalMercancias = count($oRequest->mercanciasTodas);
        $oObjData->oCartaPorte->mercancia->mercancias = $oRequest->mercanciasTodas;
        foreach ($oObjData->oCartaPorte->mercancia->mercancias as $merch) {
            $oItem = \DB::table('sat_items')->where('key_code', $merch->bienesTransp)->first();
            $merch->descripcion = $oItem->description;

            $oUnit = \DB::table('sat_units')->where('key_code', $merch->claveUnidad)->first();
            $merch->simboloUnidad = $oUnit->symbol;
            $merch->descripcionUnidad = $oUnit->description;

            $merch->currencyName = $lCurrencies[$merch->moneda];
            $merch->unitName = $lUnits[$merch->claveUnidad];

            $dTotalPeso += $merch->pesoEnKg;

            // Mercancías Cantidades Transportadas
            if (count($oObjData->oCartaPorte->ubicaciones) > 2) {
                $merch->cantidadesTransportadas = [];
                foreach ($merch->lGrouped as $key => $merchTransp) {
                    $oQtyTransp = new \stdClass();
                    $oQtyTransp->cantidad = $merchTransp->cantidad;
                    $oQtyTransp->idOrigen = $oObjData->oCartaPorte->ubicaciones[0]->IDUbicacion;
                    $oQtyTransp->idDestino = $oObjData->oCartaPorte->ubicaciones[$key]->IDUbicacion;
                    $oQtyTransp->cvesTrasporte = "";
                    $oQtyTransp->index = $key;
    
                    $merch->cantidadesTransportadas[] = $oQtyTransp;
                }
            }
        }
        $oObjData->oCartaPorte->mercancia->pesoBrutoTotal = $dTotalPeso;

        return $oObjData;
    }
}