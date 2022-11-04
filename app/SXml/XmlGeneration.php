<?php namespace App\SXml;

use Carbon\Carbon;
use DOMDocument;
use App\Utils\SFormats;

class XmlGeneration {

    public static function generateCartaPorte($oDocument, $oMongoDocument, $oCarrier) {
        $oConfigurations = \App\Utils\Configuration::getConfigurations();
        $dom = new DOMDocument();

		$dom->encoding = 'UTF-8';
		$dom->xmlVersion = '1.0';
        $dom->standalone = true;
		$dom->formatOutput = true;
		$root = $dom->createElement('cfdi:Comprobante');

        // Comprobante
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/4");
        $root->setAttribute("xmlns:cartaporte20", "http://www.sat.gob.mx/CartaPorte20");
        $root->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/CartaPorte20 http://www.sat.gob.mx/sitio_internet/cfd/CartaPorte/CartaPorte20.xsd");
        $root->setAttribute("Version", $oDocument->xml_version);
        if (isset($oMongoDocument->serie) && $oMongoDocument->serie != '' && $oMongoDocument->serie != null) {
            $root->setAttribute("Serie", $oDocument->serie);
        }
        if (isset($oMongoDocument->folio) && $oMongoDocument->folio != '' && $oMongoDocument->folio != null) {
            $root->setAttribute("Folio", $oDocument->folio);
        }

        $oDate = Carbon::parse($oMongoDocument->dtDate);
        $root->setAttribute("Fecha", $oDate->format('Y-m-d').'T'.$oDate->format('H:i:s'));
        // $root->setAttribute("Sello", "");
        $root->setAttribute("FormaPago", $oMongoDocument->formaPago);
        // $root->setAttribute("NoCertificado", "");
        $root->setAttribute("Certificado", "");
        $root->setAttribute("SubTotal", SFormats::formatNumber($oMongoDocument->subTotal));
        $root->setAttribute("Descuento", SFormats::formatNumber($oMongoDocument->discounts));
        $root->setAttribute("Moneda", $oMongoDocument->currency);
        $root->setAttribute("TipoCambio", $oMongoDocument->currency == $oConfigurations->localCurrency ? "1" : SFormats::formatNumber($oMongoDocument->tipoCambio, 4));
        $root->setAttribute("Total", SFormats::formatNumber($oMongoDocument->total));
        $root->setAttribute("TipoDeComprobante", $oConfigurations->cfdi4_0->tipoComprobante);
        $root->setAttribute("MetodoPago", $oMongoDocument->metodoPago);
        $root->setAttribute("LugarExpedicion", $oConfigurations->cfdi4_0->lugarExpedicion);
        $root->setAttribute("Exportacion", "01");
        // $root->setAttribute("Confirmacion", "ECVH1");

        // Emisor
        $nodeEmisor = $dom->createElement('cfdi:Emisor');
        $nodeEmisor->setAttribute('Rfc', $oMongoDocument->emisor->rfcEmisor);
        $nodeEmisor->setAttribute('Nombre', $oMongoDocument->emisor->nombreEmisor);
        $nodeEmisor->setAttribute('RegimenFiscal', $oMongoDocument->emisor->regimenFiscal);
        $root->appendChild($nodeEmisor);

        // Receptor
        $nodeReceptor = $dom->createElement('cfdi:Receptor');
        $nodeReceptor->setAttribute('Rfc', $oConfigurations->cfdi4_0->rfc);
        $nodeReceptor->setAttribute('Nombre', $oConfigurations->cfdi4_0->nombreReceptor);
        $nodeReceptor->setAttribute('DomicilioFiscalReceptor', $oConfigurations->cfdi4_0->domicilioFiscalReceptor);
        $nodeReceptor->setAttribute('RegimenFiscalReceptor', $oConfigurations->cfdi4_0->regimenFiscalReceptor);
        $nodeReceptor->setAttribute('UsoCFDI', $oConfigurations->cfdi4_0->usoCFDI);
        $root->appendChild($nodeReceptor);

        // Conceptos
        $nodeConceptos = $dom->createElement('cfdi:Conceptos');
        $root->appendChild($nodeConceptos);

        // Conceptos - Concepto
        foreach ($oMongoDocument->conceptos as $aConcept) {
            $base = round($aConcept["importe"], 2);
            $nodeConcepto = $dom->createElement('cfdi:Concepto');
            $nodeConcepto->setAttribute('ClaveProdServ', $aConcept["claveProdServ"]);
            $nodeConcepto->setAttribute('NoIdentificacion', $aConcept["numIndentificacion"]);
            $nodeConcepto->setAttribute('Cantidad', SFormats::formatNumber($aConcept["quantity"], 4));
            $nodeConcepto->setAttribute('ClaveUnidad', $aConcept["claveUnidad"]);
            $nodeConcepto->setAttribute('Unidad', 'No aplica');
            $nodeConcepto->setAttribute('Descripcion', $aConcept["description"]);
            $nodeConcepto->setAttribute('ValorUnitario', SFormats::formatNumber($aConcept["valorUnitario"]));
            $nodeConcepto->setAttribute('Descuento', SFormats::formatNumber($aConcept["discount"]));
            $nodeConcepto->setAttribute('Importe', SFormats::formatNumber($base));
            $nodeConcepto->setAttribute('ObjetoImp', '02');
            $nodeConceptos->appendChild($nodeConcepto);

            // Impuestos Concepto
            $nodeImpuestosConcepto = $dom->createElement('cfdi:Impuestos');
            $nodeConcepto->appendChild($nodeImpuestosConcepto);
    

            // Traslados Concepto
            if (count($aConcept["oImpuestos"]["lTraslados"]) > 0) {
                $nodeTrasladosConcepto = $dom->createElement('cfdi:Traslados');
                $nodeImpuestosConcepto->appendChild($nodeTrasladosConcepto);
        
                foreach ($aConcept["oImpuestos"]["lTraslados"] as $aTraslado) {
                    // Traslado Concepto
                    $nodeTrasladoConcepto = $dom->createElement('cfdi:Traslado');
                    $nodeTrasladoConcepto->setAttribute('Base', SFormats::formatNumber($base));
                    $nodeTrasladoConcepto->setAttribute('Impuesto', $aTraslado["impuesto"]);
                    $nodeTrasladoConcepto->setAttribute('TipoFactor', 'Tasa');
                    $nodeTrasladoConcepto->setAttribute('TasaOCuota', SFormats::formatNumber($aTraslado["tasa"], 6));
                    $nodeTrasladoConcepto->setAttribute('Importe', SFormats::formatNumber($aTraslado["importe"]));
                    $nodeTrasladosConcepto->appendChild($nodeTrasladoConcepto);
                }
            }
    
            // Retenciones Concepto
            if (count($aConcept["oImpuestos"]["lRetenciones"]) > 0) {
                $nodeRetencionesConcepto = $dom->createElement('cfdi:Retenciones');
                $nodeImpuestosConcepto->appendChild($nodeRetencionesConcepto);
        
                foreach ($aConcept["oImpuestos"]["lRetenciones"] as $aRetencion) {
                    // Retencion Concepto
                    $nodeRetencionConcepto = $dom->createElement('cfdi:Retencion');
                    $nodeRetencionConcepto->setAttribute('Base', SFormats::formatNumber($base));
                    $nodeRetencionConcepto->setAttribute('Impuesto', $aRetencion["impuesto"]);
                    $nodeRetencionConcepto->setAttribute('TipoFactor', 'Tasa');
                    $nodeRetencionConcepto->setAttribute('TasaOCuota', SFormats::formatNumber($aRetencion["tasa"], 6));
                    $nodeRetencionConcepto->setAttribute('Importe', SFormats::formatNumber($aRetencion["importe"]));
                    $nodeRetencionesConcepto->appendChild($nodeRetencionConcepto);
                }
            }
        }

        // Nodo Impuestos
        $nodeImpuestos = $dom->createElement('cfdi:Impuestos');

        // Retenciones
        if (count($oMongoDocument->oImpuestos["lRetenciones"]) > 0) {
            $nodeRetenciones = $dom->createElement('cfdi:Retenciones');
            $nodeImpuestos->appendChild($nodeRetenciones);
    
            // Retencion
            foreach ($oMongoDocument->oImpuestos["lRetenciones"] as $aRetencion) {
                $nodeRetencion = $dom->createElement('cfdi:Retencion');
                $nodeRetencion->setAttribute('Impuesto', $aRetencion["impuesto"]);
                $nodeRetencion->setAttribute('Importe', SFormats::formatNumber($aRetencion["importe"]));
                $nodeRetenciones->appendChild($nodeRetencion);
            }
        }

        // Traslados
        if (count($oMongoDocument->oImpuestos["lTraslados"]) > 0) {
            $nodeTraslados = $dom->createElement('cfdi:Traslados');
            $nodeImpuestos->appendChild($nodeTraslados);
    
            foreach ($oMongoDocument->oImpuestos["lTraslados"] as $aTraslado) {
                // Traslado
                $nodeTraslado = $dom->createElement('cfdi:Traslado');
                $nodeTraslado->setAttribute('Base', SFormats::formatNumber($aTraslado["base"]));
                $nodeTraslado->setAttribute('Impuesto', '002');
                $nodeTraslado->setAttribute('TipoFactor', 'Tasa');
                $nodeTraslado->setAttribute('TasaOCuota', SFormats::formatNumber($aTraslado["tasa"], 6));
                $nodeTraslado->setAttribute('Importe', SFormats::formatNumber($aTraslado["importe"]));
                $nodeTraslados->appendChild($nodeTraslado);
            }
        }

        $nodeImpuestos->setAttribute('TotalImpuestosRetenidos', SFormats::formatNumber($oMongoDocument->oImpuestos["totalImpuestosRetenidos"]));
        $nodeImpuestos->setAttribute('TotalImpuestosTrasladados', SFormats::formatNumber($oMongoDocument->oImpuestos["totalImpuestosTrasladados"]));
        $root->appendChild($nodeImpuestos);

        // Complemento
        $nodeComplemento = $dom->createElement('cfdi:Complemento');
        $root->appendChild($nodeComplemento);

        // Carta Porte
        $nodeCartaPorte = $dom->createElement('cartaporte20:CartaPorte');
        $nodeCartaPorte->setAttribute('Version', $oDocument->comp_version);
        $nodeCartaPorte->setAttribute('TranspInternac', "No");
        $nodeCartaPorte->setAttribute('TotalDistRec', SFormats::formatNumber($oMongoDocument->oCartaPorte["totalDistancia"], 3));
        $nodeComplemento->appendChild($nodeCartaPorte);

        // Ubicaciones
        $nodeUbicaciones = $dom->createElement('cartaporte20:Ubicaciones');
        $nodeCartaPorte->appendChild($nodeUbicaciones);

        $fromIndex = 0;
        foreach ($oMongoDocument->oCartaPorte["ubicaciones"] as $aUbicacion) {
            if ($aUbicacion["tipoUbicacion"] == "Origen") {
                // Ubicacion Origen
                $nodeUbicacion = $dom->createElement('cartaporte20:Ubicacion');
                $nodeUbicacion->setAttribute('TipoUbicacion', "Origen");
                $nodeUbicacion->setAttribute('IDUbicacion', $aUbicacion["IDUbicacion"]);
                $nodeUbicacion->setAttribute('RFCRemitenteDestinatario', $aUbicacion["rFCRemitenteDestinatario"]);
                $oDateU = Carbon::parse($aUbicacion["fechaHoraSalidaLlegada"]);
                $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', $oDateU->format('Y-m-d').'T'.$oDateU->format('H:i:s'));
                $nodeUbicaciones->appendChild($nodeUbicacion);

                // Domicilio
                $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
                if ($aUbicacion["domicilio"]["numeroExterior"] != null && strlen($aUbicacion["domicilio"]["numeroExterior"]) > 0) {
                    $nodeDomicilio->setAttribute('NumeroExterior', $aUbicacion["domicilio"]["numeroExterior"]);
                }
                if ($aUbicacion["domicilio"]["numeroInterior"] != null && strlen($aUbicacion["domicilio"]["numeroInterior"]) > 0) {
                    $nodeDomicilio->setAttribute('NumeroInterior', $aUbicacion["domicilio"]["numeroInterior"]);
                }
                if ($aUbicacion["domicilio"]["calle"] != null && strlen($aUbicacion["domicilio"]["calle"]) > 0) {
                    $nodeDomicilio->setAttribute('Calle', $aUbicacion["domicilio"]["calle"]);
                }
                if ($aUbicacion["domicilio"]["colonia"] != null && strlen($aUbicacion["domicilio"]["colonia"]) > 0) {
                    $nodeDomicilio->setAttribute('Colonia', $aUbicacion["domicilio"]["colonia"]);
                }
                if ($aUbicacion["domicilio"]["localidad"] != null && strlen($aUbicacion["domicilio"]["localidad"]) > 0) {
                    $nodeDomicilio->setAttribute('Localidad', $aUbicacion["domicilio"]["localidad"]);
                }
                if ($aUbicacion["domicilio"]["referencia"] != null && strlen($aUbicacion["domicilio"]["referencia"]) > 0) {
                    $nodeDomicilio->setAttribute('Referencia', $aUbicacion["domicilio"]["referencia"]);
                }
                if ($aUbicacion["domicilio"]["municipio"] != null && strlen($aUbicacion["domicilio"]["municipio"]) > 0) {
                    $nodeDomicilio->setAttribute('Municipio', $aUbicacion["domicilio"]["municipio"]);
                }
                $nodeDomicilio->setAttribute('Estado', $aUbicacion["domicilio"]["estado"]);
                $nodeDomicilio->setAttribute('Pais', $aUbicacion["domicilio"]["pais"]);
                $nodeDomicilio->setAttribute('CodigoPostal', $aUbicacion["domicilio"]["codigoPostal"]);            
                $nodeUbicacion->appendChild($nodeDomicilio);

                break;
            }
        }

        $ubicacionIndex = 0;
        foreach ($oMongoDocument->oCartaPorte["ubicaciones"] as $aUbicacion) {
            if ($ubicacionIndex == $fromIndex) {
                $ubicacionIndex++;
                continue;
            }

            // Ubicacion Destino
            $nodeUbicacion = $dom->createElement('cartaporte20:Ubicacion');
            $nodeUbicacion->setAttribute('TipoUbicacion', "Destino");
            $nodeUbicacion->setAttribute('IDUbicacion', $aUbicacion["IDUbicacion"]);
            $nodeUbicacion->setAttribute('RFCRemitenteDestinatario', $aUbicacion["rFCRemitenteDestinatario"]);
            if ($aUbicacion["nombreRFC"] != null && strlen($aUbicacion["nombreRFC"]) > 0) {
                $nodeUbicacion->setAttribute('NombreRemitenteDestinatario', $aUbicacion["nombreRFC"]);
            }
            $oDateUD = Carbon::parse($aUbicacion["fechaHoraSalidaLlegada"]);
            $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', $oDateUD->format('Y-m-d').'T'.$oDateUD->format('H:i:s'));
            $nodeUbicacion->setAttribute('DistanciaRecorrida', SFormats::formatNumber($aUbicacion["distanciaRecorrida"], 3));
            $nodeUbicaciones->appendChild($nodeUbicacion);
    
            // Domicilio
            $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
            if ($aUbicacion["domicilio"]["numeroExterior"] != null && strlen($aUbicacion["domicilio"]["numeroExterior"]) > 0) {
                $nodeDomicilio->setAttribute('NumeroExterior', $aUbicacion["domicilio"]["numeroExterior"]);
            }
            if ($aUbicacion["domicilio"]["numeroInterior"] != null && strlen($aUbicacion["domicilio"]["numeroInterior"]) > 0) {
                $nodeDomicilio->setAttribute('NumeroInterior', $aUbicacion["domicilio"]["numeroInterior"]);
            }
            if ($aUbicacion["domicilio"]["calle"] != null && strlen($aUbicacion["domicilio"]["calle"]) > 0) {
                $nodeDomicilio->setAttribute('Calle', $aUbicacion["domicilio"]["calle"]);
            }
            if ($aUbicacion["domicilio"]["colonia"] != null && strlen($aUbicacion["domicilio"]["colonia"]) > 0) {
                $nodeDomicilio->setAttribute('Colonia', $aUbicacion["domicilio"]["colonia"]);
            }
            if ($aUbicacion["domicilio"]["localidad"] != null && strlen($aUbicacion["domicilio"]["localidad"]) > 0) {
                $nodeDomicilio->setAttribute('Localidad', $aUbicacion["domicilio"]["localidad"]);
            }
            if ($aUbicacion["domicilio"]["referencia"] != null && strlen($aUbicacion["domicilio"]["referencia"]) > 0) {
                $nodeDomicilio->setAttribute('Referencia', $aUbicacion["domicilio"]["referencia"]);
            }
            if ($aUbicacion["domicilio"]["municipio"] != null && strlen($aUbicacion["domicilio"]["municipio"]) > 0) {
                $nodeDomicilio->setAttribute('Municipio', $aUbicacion["domicilio"]["municipio"]);
            }
            $nodeDomicilio->setAttribute('Estado', $aUbicacion["domicilio"]["estado"]);
            $nodeDomicilio->setAttribute('Pais', $aUbicacion["domicilio"]["pais"]);
            $nodeDomicilio->setAttribute('CodigoPostal', $aUbicacion["domicilio"]["codigoPostal"]);            
            $nodeUbicacion->appendChild($nodeDomicilio);

            $ubicacionIndex++;
        }

        // Mercancías
        $nodeMercancias = $dom->createElement('cartaporte20:Mercancias');
        $nodeMercancias->setAttribute('PesoBrutoTotal', SFormats::formatNumber($oMongoDocument->oCartaPorte["mercancia"]["pesoBrutoTotal"], 3));
        $nodeMercancias->setAttribute('UnidadPeso', $oMongoDocument->oCartaPorte["mercancia"]["unidadPeso"]);
        $nodeMercancias->setAttribute('NumTotalMercancias', $oMongoDocument->oCartaPorte["mercancia"]["numTotalMercancias"]);
        $nodeCartaPorte->appendChild($nodeMercancias);

        foreach ($oMongoDocument->oCartaPorte["mercancia"]["mercancias"] as $aMerch) {
            //Mercancía
            $nodeMercancia = $dom->createElement('cartaporte20:Mercancia');
            $nodeMercancia->setAttribute('BienesTransp', $aMerch["bienesTransp"]);
            $nodeMercancia->setAttribute('Descripcion', $aMerch["descripcion"]);
            $nodeMercancia->setAttribute('Cantidad', SFormats::formatNumber($aMerch["cantidad"], 4));
            $nodeMercancia->setAttribute('MaterialPeligroso', "No");
            $nodeMercancia->setAttribute('ClaveUnidad', $aMerch["claveUnidad"]);
            $nodeMercancia->setAttribute('PesoEnKg', SFormats::formatNumber($aMerch["pesoEnKg"], 3));
            $nodeMercancia->setAttribute('ValorMercancia', SFormats::formatNumber($aMerch["valorMercancia"]));
            $nodeMercancia->setAttribute('Moneda', $aMerch["moneda"]);
    
            // CantidadTransporta
            if (count($oMongoDocument->oCartaPorte["ubicaciones"]) > 2) {
                foreach ($aMerch["cantidadesTransportadas"] as $qtyTransporta) {
                    $nodeCantidadTransporta = $dom->createElement('cartaporte20:CantidadTransporta');
                    $nodeCantidadTransporta->setAttribute('Cantidad', SFormats::formatNumber($qtyTransporta["cantidad"], 4));
                    $nodeCantidadTransporta->setAttribute('IDOrigen', $qtyTransporta["idOrigen"]);
                    $nodeCantidadTransporta->setAttribute('IDDestino', $qtyTransporta["idDestino"]);
                    $nodeMercancia->appendChild($nodeCantidadTransporta);
                }
            }

            $nodeMercancias->appendChild($nodeMercancia);
        }

        // Autotransporte
        $nodeAutotransporte = $dom->createElement('cartaporte20:Autotransporte');
        $nodeAutotransporte->setAttribute('PermSCT', $oMongoDocument->oVehicle["slic_key_code"]);
        $nodeAutotransporte->setAttribute('NumPermisoSCT', $oMongoDocument->oVehicle["license_sct_num"]);
        $nodeMercancias->appendChild($nodeAutotransporte);

        // IdentificacionVehicular
        $nodeIdentificacionVehicular = $dom->createElement('cartaporte20:IdentificacionVehicular');
        $nodeIdentificacionVehicular->setAttribute('ConfigVehicular', $oMongoDocument->oVehicle["vcfg_key_code"]);
        $nodeIdentificacionVehicular->setAttribute('PlacaVM', $oMongoDocument->oVehicle["plates"]);
        $nodeIdentificacionVehicular->setAttribute('AnioModeloVM', $oMongoDocument->oVehicle["year_model"]);
        $nodeAutotransporte->appendChild($nodeIdentificacionVehicular);

        // Seguros
        $nodeSeguros = $dom->createElement('cartaporte20:Seguros');
        $nodeSeguros->setAttribute('AseguraRespCivil', $oMongoDocument->oVehicle["insurance_full_name"]);
        $nodeSeguros->setAttribute('PolizaRespCivil', $oMongoDocument->oVehicle["policy"]);
        $nodeAutotransporte->appendChild($nodeSeguros);

        // Remolques
        if (count($oMongoDocument->lTrailers) > 0) {
            $nodeRemolques = $dom->createElement('cartaporte20:Remolques');
            $nodeAutotransporte->appendChild($nodeRemolques);

            foreach ($oMongoDocument->lTrailers as $oTrailer) {
                // Remolque
                $nodeRemolque = $dom->createElement('cartaporte20:Remolque');
                $nodeRemolque->setAttribute('SubTipoRem', $oTrailer["oTrailer"]["trailer_subtype_key_code"]);
                $nodeRemolque->setAttribute('Placa', $oTrailer["oTrailer"]["plates"]);
                $nodeRemolques->appendChild($nodeRemolque);
            }
        }

        /**
         * Figuras transporte
         */

        // FiguraTransporte (Chofer)
        $nodeFiguraTransporte = $dom->createElement('cartaporte20:FiguraTransporte');
        $nodeCartaPorte->appendChild($nodeFiguraTransporte);

        // TiposFigura (Chofer)
        $nodeTiposFigura = $dom->createElement('cartaporte20:TiposFigura');
        $nodeTiposFigura->setAttribute('TipoFigura', $oMongoDocument->oFigure["figure_type_key_code"]);
        $nodeTiposFigura->setAttribute('RFCFigura', $oMongoDocument->oFigure["fiscal_id"]);
        $nodeTiposFigura->setAttribute('NumLicencia', $oMongoDocument->oFigure["driver_lic"]);
        $nodeTiposFigura->setAttribute('NombreFigura', $oMongoDocument->oFigure["fullname"]);
        $nodeFiguraTransporte->appendChild($nodeTiposFigura);

        if (! $oMongoDocument->oVehicle["is_own"]) {
            // TiposFigura (Vehículo)
            $nodeTiposFiguraVeh = $dom->createElement('cartaporte20:TiposFigura');
            $nodeTiposFiguraVeh->setAttribute('TipoFigura', $oMongoDocument->oVehicle["oFigTranCfg"]["key_figure_type"]);
            $nodeTiposFiguraVeh->setAttribute('RFCFigura', $oMongoDocument->oVehicle["oFigTranCfg"]["fiscal_id"]);
            $nodeTiposFiguraVeh->setAttribute('NombreFigura', $oMongoDocument->oVehicle["oFigTranCfg"]["fullname"]);
            $nodeFiguraTransporte->appendChild($nodeTiposFiguraVeh);

            // PartesTransporte (Vehículo)
            $nodePartesTransporteVeh = $dom->createElement('cartaporte20:PartesTransporte');
            $nodePartesTransporteVeh->setAttribute('ParteTransporte', $oMongoDocument->oVehicle["oFigTranCfg"]["key_trans_part"]);
            $nodeTiposFiguraVeh->appendChild($nodePartesTransporteVeh);
        }

        if (count($oMongoDocument->lTrailers) > 0) {
            foreach ($oMongoDocument->lTrailers as $oTrailer) {
                // Remolque
                if (! $oTrailer["oTrailer"]["is_own"]) {
                    // TiposFigura (Vehículo)
                    $nodeTiposFiguraTra = $dom->createElement('cartaporte20:TiposFigura');
                    $nodeTiposFiguraTra->setAttribute('TipoFigura', $oTrailer["oTrailer"]["oFigTranCfg"]["key_figure_type"]);
                    $nodeTiposFiguraTra->setAttribute('RFCFigura', $oTrailer["oTrailer"]["oFigTranCfg"]["fiscal_id"]);
                    $nodeTiposFiguraTra->setAttribute('NombreFigura', $oTrailer["oTrailer"]["oFigTranCfg"]["fullname"]);
                    $nodeFiguraTransporte->appendChild($nodeTiposFiguraTra);

                    // PartesTransporte (Vehículo)
                    $nodePartesTransporteTra = $dom->createElement('cartaporte20:PartesTransporte');
                    $nodePartesTransporteTra->setAttribute('ParteTransporte', $oTrailer["oTrailer"]["oFigTranCfg"]["key_trans_part"]);
                    $nodeTiposFiguraTra->appendChild($nodePartesTransporteTra);
                }
            }
        }

		$dom->appendChild($root);

        $sXml = $dom->saveXML();

        return $sXml;
    }
    
}