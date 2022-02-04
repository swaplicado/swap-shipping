<?php namespace App\SXml;

use Carbon\Carbon;
use DOMDocument;
use DOMAttr;
use XSLTProcessor;
use phpseclib\Crypt\RSA;

class XmlGeneration {

    public static function generateCartaPorte($oDocument, $oMongoDocument, $oCarrier) {
        $oConfigurations = \App\Utils\Configuration::getConfigurations();
        $dom = new DOMDocument();

		$dom->encoding = 'UTF-8';
		$dom->xmlVersion = '1.0';
        $dom->standalone = "yes";
		$dom->formatOutput = true;
		$root = $dom->createElement('cfdi:Comprobante');

        // Comprobante
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/4");
        $root->setAttribute("xmlns:cartaporte20", "http://www.sat.gob.mx/CartaPorte20");
        $root->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/CartaPorte20 http://www.sat.gob.mx/sitio_internet/cfd/CartaPorte/CartaPorte20.xsd");
        $root->setAttribute("Version", $oDocument->xml_version);
        $root->setAttribute("Serie", $oMongoDocument->serie);
        $root->setAttribute("Folio", $oMongoDocument->folio);
        $root->setAttribute("Fecha", Carbon::parse($oMongoDocument->dtDate)->format('Y-m-d'));
        // $root->setAttribute("Sello", "");
        $root->setAttribute("FormaPago", $oMongoDocument->formaPago);
        // $root->setAttribute("NoCertificado", "");
        // $root->setAttribute("Certificado", "");
        $root->setAttribute("SubTotal", $oMongoDocument->subTotal);
        $root->setAttribute("Moneda", $oMongoDocument->currency);
        $root->setAttribute("TipoCambio", $oMongoDocument->tipoCambio);
        $root->setAttribute("Total", $oMongoDocument->total);
        $root->setAttribute("TipoDeComprobante", $oConfigurations->cfdi4_0->tipoComprobante);
        $root->setAttribute("MetodoPago", $oMongoDocument->metodoPago);
        $root->setAttribute("LugarExpedicion", $oConfigurations->cfdi4_0->lugarExpedicion);
        $root->setAttribute("Exportacion", "01");
        // $root->setAttribute("Confirmacion", "ECVH1");

        // Emisor
        $nodeEmisor = $dom->createElement('cfdi:Emisor');
        $nodeEmisor->setAttribute('Rfc', $oCarrier->fiscal_id);
        $nodeEmisor->setAttribute('Nombre', $oCarrier->fullname);
        $nodeEmisor->setAttribute('RegimenFiscal', '601');
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
            $nodeConcepto->setAttribute('Cantidad', $aConcept["quantity"]);
            $nodeConcepto->setAttribute('ClaveUnidad', $aConcept["claveUnidad"]);
            $nodeConcepto->setAttribute('Unidad', 'No aplica');
            $nodeConcepto->setAttribute('Descripcion', $aConcept["description"]);
            $nodeConcepto->setAttribute('ValorUnitario', $aConcept["valorUnitario"]);
            $nodeConcepto->setAttribute('Importe', $base);
            $nodeConcepto->setAttribute('ObjetoImp', '02');
            $nodeConceptos->appendChild($nodeConcepto);

            // Impuestos Concepto
            $nodeImpuestosConcepto = $dom->createElement('cfdi:Impuestos');
            $nodeConcepto->appendChild($nodeImpuestosConcepto);
    
            // Traslados Concepto
            $nodeTrasladosConcepto = $dom->createElement('cfdi:Traslados');
            $nodeImpuestosConcepto->appendChild($nodeTrasladosConcepto);
    
            foreach ($aConcept["oImpuestos"]["lTraslados"] as $aTraslado) {
                // Traslado Concepto
                $nodeTrasladoConcepto = $dom->createElement('cfdi:Traslado');
                $nodeTrasladoConcepto->setAttribute('Base', $base);
                $nodeTrasladoConcepto->setAttribute('Impuesto', $aTraslado["impuesto"]);
                $nodeTrasladoConcepto->setAttribute('TipoFactor', 'Tasa');
                $nodeTrasladoConcepto->setAttribute('TasaOCuota', $aTraslado["tasa"]);
                $nodeTrasladoConcepto->setAttribute('Importe', $aTraslado["importe"]);
                $nodeTrasladosConcepto->appendChild($nodeTrasladoConcepto);
            }
    
            // Retenciones Concepto
            $nodeRetencionesConcepto = $dom->createElement('cfdi:Retenciones');
            $nodeImpuestosConcepto->appendChild($nodeRetencionesConcepto);
    
            foreach ($aConcept["oImpuestos"]["lRetenciones"] as $aRetencion) {
                // Retencion Concepto
                $nodeRetencionConcepto = $dom->createElement('cfdi:Retencion');
                $nodeRetencionConcepto->setAttribute('Base', $base);
                $nodeRetencionConcepto->setAttribute('Impuesto', $aRetencion["impuesto"]);
                $nodeRetencionConcepto->setAttribute('TipoFactor', 'Tasa');
                $nodeRetencionConcepto->setAttribute('TasaOCuota', $aRetencion["tasa"]);
                $nodeRetencionConcepto->setAttribute('Importe', $aRetencion["importe"]);
                $nodeRetencionesConcepto->appendChild($nodeRetencionConcepto);
            }
        }

        // Nodo Impuestos
        $nodeImpuestos = $dom->createElement('cfdi:Impuestos');
        $nodeImpuestos->setAttribute('TotalImpuestosRetenidos', $oMongoDocument->oImpuestos["totalImpuestosRetenidos"]);
        $nodeImpuestos->setAttribute('TotalImpuestosTrasladados', $oMongoDocument->oImpuestos["totalImpuestosTrasladados"]);
        $root->appendChild($nodeImpuestos);

        // Retenciones
        $nodeRetenciones = $dom->createElement('cfdi:Retenciones');
        $nodeImpuestos->appendChild($nodeRetenciones);

        // Retencion
        foreach ($oMongoDocument->oImpuestos["lRetenciones"] as $aRetencion) {
            $nodeRetencion = $dom->createElement('cfdi:Retencion');
            $nodeRetencion->setAttribute('Impuesto', $aRetencion["impuesto"]);
            $nodeRetencion->setAttribute('Importe', $aRetencion["importe"]);
            $nodeRetenciones->appendChild($nodeRetencion);
        }

        // Traslados
        $nodeTraslados = $dom->createElement('cfdi:Traslados');
        $nodeImpuestos->appendChild($nodeTraslados);

        foreach ($oMongoDocument->oImpuestos["lTraslados"] as $aTraslado) {
            // Traslado
            $nodeTraslado = $dom->createElement('cfdi:Traslado');
            $nodeTraslado->setAttribute('Impuesto', '002');
            $nodeTraslado->setAttribute('TipoFactor', 'Tasa');
            $nodeTraslado->setAttribute('TasaOCuota', $aTraslado["tasa"]);
            $nodeTraslado->setAttribute('Importe', $aTraslado["importe"]);
            $nodeTraslados->appendChild($nodeTraslado);
        }

        // Complemento
        $nodeComplemento = $dom->createElement('cfdi:Complemento');
        $root->appendChild($nodeComplemento);

        // Carta Porte
        $nodeCartaPorte = $dom->createElement('cartaporte20:CartaPorte');
        $nodeCartaPorte->setAttribute('Version', $oDocument->comp_version);
        $nodeCartaPorte->setAttribute('TranspInternac', "No");
        $nodeCartaPorte->setAttribute('TotalDistRec', $oMongoDocument->oCartaPorte["totalDistancia"]);
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
                $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', $aUbicacion["fechaHoraSalidaLlegada"]);
                $nodeUbicaciones->appendChild($nodeUbicacion);

                // Domicilio
                $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
                // $nodeDomicilio->setAttribute('Localidad', "09");
                // $nodeDomicilio->setAttribute('Municipio', $ubicacion["domicilio"]["municipio"]);
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
                continue;
            }

            // Ubicacion Destino
            $nodeUbicacion = $dom->createElement('cartaporte20:Ubicacion');
            $nodeUbicacion->setAttribute('TipoUbicacion', "Destino");
            $nodeUbicacion->setAttribute('IDUbicacion', $aUbicacion["IDUbicacion"]);
            $nodeUbicacion->setAttribute('RFCRemitenteDestinatario', $aUbicacion["rFCRemitenteDestinatario"]);
            $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', $aUbicacion["fechaHoraSalidaLlegada"]);
            $nodeUbicacion->setAttribute('DistanciaRecorrida',$aUbicacion["distanciaRecorrida"]);
            $nodeUbicaciones->appendChild($nodeUbicacion);
    
            // Domicilio
            $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
            // $nodeDomicilio->setAttribute('Localidad', "09");
            // $nodeDomicilio->setAttribute('Municipio', $ubicacion->domicilio->municipio);
            $nodeDomicilio->setAttribute('Estado', $aUbicacion["domicilio"]["estado"]);
            $nodeDomicilio->setAttribute('Pais', $aUbicacion["domicilio"]["pais"]);
            $nodeDomicilio->setAttribute('CodigoPostal', $aUbicacion["domicilio"]["codigoPostal"]);            
            $nodeUbicacion->appendChild($nodeDomicilio);
        }

        // Mercancías
        $nodeMercancias = $dom->createElement('cartaporte20:Mercancias');
        $nodeMercancias->setAttribute('PesoBrutoTotal', $oMongoDocument->oCartaPorte["mercancia"]["pesoBrutoTotal"]);
        $nodeMercancias->setAttribute('UnidadPeso', $oMongoDocument->oCartaPorte["mercancia"]["unidadPeso"]);
        $nodeMercancias->setAttribute('NumTotalMercancias', $oMongoDocument->oCartaPorte["mercancia"]["numTotalMercancias"]);
        $nodeCartaPorte->appendChild($nodeMercancias);

        foreach ($oMongoDocument->oCartaPorte["mercancia"]["mercancias"] as $aMerch) {            
            //Mercancía
            $nodeMercancia = $dom->createElement('cartaporte20:Mercancia');
            $nodeMercancia->setAttribute('BienesTransp', $aMerch["bienesTransp"]);
            $nodeMercancia->setAttribute('Descripcion', $aMerch["descripcion"]);
            $nodeMercancia->setAttribute('Cantidad', $aMerch["cantidad"]);
            $nodeMercancia->setAttribute('ClaveUnidad', $aMerch["claveUnidad"]);
            $nodeMercancia->setAttribute('PesoEnKg', $aMerch["pesoEnKg"]);
            $nodeMercancias->appendChild($nodeMercancia);
    
            // CantidadTransporta
            // $nodeCantidadTransporta = $dom->createElement('cartaporte20:CantidadTransporta');
            // $nodeCantidadTransporta->setAttribute('Cantidad', "10.000000");
            // $nodeCantidadTransporta->setAttribute('IDOrigen', "OR022191");
            // $nodeCantidadTransporta->setAttribute('IDDestino', "DE000131");
            // $nodeMercancia->appendChild($nodeCantidadTransporta);
    
            // $nodeCantidadTransporta = $dom->createElement('cartaporte20:CantidadTransporta');
            // $nodeCantidadTransporta->setAttribute('Cantidad', "10.000000");
            // $nodeCantidadTransporta->setAttribute('IDOrigen', "OR029621");
            // $nodeCantidadTransporta->setAttribute('IDDestino', "DE000131");
            // $nodeMercancia->appendChild($nodeCantidadTransporta);
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

        // FiguraTransporte
        $nodeFiguraTransporte = $dom->createElement('cartaporte20:FiguraTransporte');
        $nodeCartaPorte->appendChild($nodeFiguraTransporte);

        // TiposFigura
        $nodeTiposFigura = $dom->createElement('cartaporte20:TiposFigura');
        $nodeTiposFigura->setAttribute('TipoFigura', $oMongoDocument->oFigure["figure_type_key_code"]);
        $nodeTiposFigura->setAttribute('RFCFigura', $oMongoDocument->oFigure["fiscal_id"]);
        $nodeTiposFigura->setAttribute('NumLicencia', $oMongoDocument->oFigure["driver_lic"]);
        $nodeFiguraTransporte->appendChild($nodeTiposFigura);

        // PartesTransporte
        // $nodePartesTransporte = $dom->createElement('cartaporte20:PartesTransporte');
        // $nodePartesTransporte->setAttribute('ParteTransporte', "PT04");
        // $nodeTiposFigura->appendChild($nodePartesTransporte);

		$dom->appendChild($root);
        // $xml_file_name = base_path("temp\\temp.xml");
	    // $dom->save($xml_file_name);

        $sXml = $dom->saveXML();

        return $sXml;
    }

    public static function generateCarta() {
        $dom = new DOMDocument();

		$dom->encoding = 'UTF-8';
		$dom->xmlVersion = '1.0';
        $dom->standalone = "yes";
		$dom->formatOutput = true;
		$root = $dom->createElement('cfdi:Comprobante');

        // Comprobante
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/4");
        $root->setAttribute("xmlns:cartaporte20", "http://www.sat.gob.mx/CartaPorte20");
        $root->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/CartaPorte20 http://www.sat.gob.mx/sitio_internet/cfd/CartaPorte/CartaPorte20.xsd");
        $root->setAttribute("Version", "4.0");
        $root->setAttribute("Serie", "A");
        $root->setAttribute("Folio", "3");
        $root->setAttribute("Fecha", "2021-11-24T00:00:00");
        // $root->setAttribute("Sello", "");
        $root->setAttribute("FormaPago", "03");
        // $root->setAttribute("NoCertificado", "");
        // $root->setAttribute("Certificado", "");
        $root->setAttribute("SubTotal", "200.00");
        $root->setAttribute("Moneda", "MXN");
        $root->setAttribute("TipoCambio", "1");
        $root->setAttribute("Total", "212.00");
        $root->setAttribute("TipoDeComprobante", "I");
        $root->setAttribute("MetodoPago", "PUE");
        $root->setAttribute("LugarExpedicion", "60135");
        $root->setAttribute("Exportacion", "01");
        // $root->setAttribute("Confirmacion", "ECVH1");

        // Emisor
        $nodeEmisor = $dom->createElement('cfdi:Emisor');
        $nodeEmisor->setAttribute('Rfc', 'AAA010101AAA');
        $nodeEmisor->setAttribute('Nombre', 'Nombre Emisor');
        $nodeEmisor->setAttribute('RegimenFiscal', '601');
        $root->appendChild($nodeEmisor);

        // Receptor
        $nodeEmisor = $dom->createElement('cfdi:Receptor');
        $nodeEmisor->setAttribute('Rfc', 'AAA010101AAA');
        $nodeEmisor->setAttribute('Nombre', 'Nombre Receptor');
        $nodeEmisor->setAttribute('DomicilioFiscalReceptor', '60135');
        $nodeEmisor->setAttribute('RegimenFiscalReceptor', '616');
        $nodeEmisor->setAttribute('UsoCFDI', 'S01');
        $root->appendChild($nodeEmisor);

        // Conceptos
        $nodeConceptos = $dom->createElement('cfdi:Conceptos');
        $root->appendChild($nodeConceptos);

        // Concepto
        $nodeConcepto = $dom->createElement('cfdi:Concepto');
        $nodeConcepto->setAttribute('ClaveProdServ', '60121001');
        $nodeConcepto->setAttribute('NoIdentificacion', '1');
        $nodeConcepto->setAttribute('Cantidad', '1');
        $nodeConcepto->setAttribute('ClaveUnidad', 'ACT');
        $nodeConcepto->setAttribute('Unidad', 'No aplica');
        $nodeConcepto->setAttribute('Descripcion', 'Descripcion');
        $nodeConcepto->setAttribute('ValorUnitario', '0.01');
        $nodeConcepto->setAttribute('Importe', '0.01');
        $nodeConcepto->setAttribute('ObjetoImp', '02');
        $nodeConceptos->appendChild($nodeConcepto);

        // Impuestos Concepto
        $nodeImpuestosConcepto = $dom->createElement('cfdi:Impuestos');
        $nodeConcepto->appendChild($nodeImpuestosConcepto);

        // Traslados Concepto
        $nodeTrasladosConcepto = $dom->createElement('cfdi:Traslados');
        $nodeImpuestosConcepto->appendChild($nodeTrasladosConcepto);

        // Traslado Concepto
        $nodeTrasladoConcepto = $dom->createElement('cfdi:Traslado');
        $nodeTrasladoConcepto->setAttribute('Base', '0.01');
        $nodeTrasladoConcepto->setAttribute('Impuesto', '002');
        $nodeTrasladoConcepto->setAttribute('TipoFactor', 'Tasa');
        $nodeTrasladoConcepto->setAttribute('TasaOCuota', '0.160000');
        $nodeTrasladoConcepto->setAttribute('Importe', '0.00');
        $nodeTrasladosConcepto->appendChild($nodeTrasladoConcepto);

        // Retenciones Concepto
        $nodeRetencionesConcepto = $dom->createElement('cfdi:Retenciones');
        $nodeImpuestosConcepto->appendChild($nodeRetencionesConcepto);

        // Retencion Concepto
        $nodeRetencionConcepto = $dom->createElement('cfdi:Retencion');
        $nodeRetencionConcepto->setAttribute('Base', '0.01');
        $nodeRetencionConcepto->setAttribute('Impuesto', '002');
        $nodeRetencionConcepto->setAttribute('TipoFactor', 'Tasa');
        $nodeRetencionConcepto->setAttribute('TasaOCuota', '0.040000');
        $nodeRetencionConcepto->setAttribute('Importe', '0.00');
        $nodeRetencionesConcepto->appendChild($nodeRetencionConcepto);

        // Nodo Impuestos
        $nodeImpuestos = $dom->createElement('cfdi:Impuestos');
        $nodeImpuestos->setAttribute('TotalImpuestosRetenidos', '0');
        $nodeImpuestos->setAttribute('TotalImpuestosTrasladados', '0');
        $root->appendChild($nodeImpuestos);

        // Retenciones
        $nodeRetenciones = $dom->createElement('cfdi:Retenciones');
        $nodeImpuestos->appendChild($nodeRetenciones);

        // Retencion
        $nodeRetencion = $dom->createElement('cfdi:Retencion');
        $nodeRetencion->setAttribute('Impuesto', '002');
        $nodeRetencion->setAttribute('Importe', '0.00');
        $nodeRetenciones->appendChild($nodeRetencion);

        // Traslados
        $nodeTraslados = $dom->createElement('cfdi:Traslados');
        $nodeImpuestos->appendChild($nodeTraslados);

        // Traslado
        $nodeTraslado = $dom->createElement('cfdi:Traslado');
        $nodeTraslado->setAttribute('Base', '0.01');
        $nodeTraslado->setAttribute('Impuesto', '002');
        $nodeTraslado->setAttribute('TipoFactor', 'Tasa');
        $nodeTraslado->setAttribute('TasaOCuota', '0.160000');
        $nodeTraslado->setAttribute('Importe', '0.00');
        $nodeTraslados->appendChild($nodeTraslado);

        // Complemento
        $nodeComplemento = $dom->createElement('cfdi:Complemento');
        $root->appendChild($nodeComplemento);

        // Carta Porte
        $nodeCartaPorte = $dom->createElement('cartaporte20:CartaPorte');
        $nodeCartaPorte->setAttribute('Version', "2.0");
        $nodeCartaPorte->setAttribute('TranspInternac', "No");
        $nodeCartaPorte->setAttribute('TotalDistRec', "70.000");
        $nodeComplemento->appendChild($nodeCartaPorte);

        // Ubicaciones
        $nodeUbicaciones = $dom->createElement('cartaporte20:Ubicaciones');
        $nodeCartaPorte->appendChild($nodeUbicaciones);

        // Ubicacion Origen
        $nodeUbicacion = $dom->createElement('cartaporte20:Ubicacion');
        $nodeUbicacion->setAttribute('TipoUbicacion', "Origen");
        $nodeUbicacion->setAttribute('IDUbicacion', "OR022191");
        $nodeUbicacion->setAttribute('RFCRemitenteDestinatario', "EKU9003173C9");
        $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', "2021-11-16T00:00:00");
        $nodeUbicaciones->appendChild($nodeUbicacion);

        // Domicilio
        $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
        $nodeDomicilio->setAttribute('Localidad', "09");
        $nodeDomicilio->setAttribute('Municipio', "102");
        $nodeDomicilio->setAttribute('Estado', "MIC");
        $nodeDomicilio->setAttribute('Pais', "MEX");
        $nodeDomicilio->setAttribute('CodigoPostal', "60135");
        $nodeUbicacion->appendChild($nodeDomicilio);

        // Ubicacion Destino
        $nodeUbicacion = $dom->createElement('cartaporte20:Ubicacion');
        $nodeUbicacion->setAttribute('TipoUbicacion', "Destino");
        $nodeUbicacion->setAttribute('IDUbicacion', "DE000131");
        $nodeUbicacion->setAttribute('RFCRemitenteDestinatario', "ADU800131T10");
        $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', "2021-11-17T00:00:00");
        $nodeUbicacion->setAttribute('DistanciaRecorrida', "70.000");
        $nodeUbicaciones->appendChild($nodeUbicacion);

        // Domicilio
        $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
        $nodeDomicilio->setAttribute('Localidad', "11");
        $nodeDomicilio->setAttribute('Municipio', "108");
        $nodeDomicilio->setAttribute('Estado', "MIC");
        $nodeDomicilio->setAttribute('Pais', "MEX");
        $nodeDomicilio->setAttribute('CodigoPostal', "59690");
        $nodeUbicacion->appendChild($nodeDomicilio);

        // Mercancías
        $nodeMercancias = $dom->createElement('cartaporte20:Mercancias');
        $nodeMercancias->setAttribute('PesoBrutoTotal', "35.000");
        $nodeMercancias->setAttribute('UnidadPeso', "KGM");
        $nodeMercancias->setAttribute('NumTotalMercancias', "2");
        $nodeCartaPorte->appendChild($nodeMercancias);

        //Mercancía
        $nodeMercancia = $dom->createElement('cartaporte20:Mercancia');
        $nodeMercancia->setAttribute('BienesTransp', "50151500");
        $nodeMercancia->setAttribute('Descripcion', "ACEITE CRUDO CANOLA ORGANICO ALTO OLEICO PRENSA A GRANEL");
        $nodeMercancia->setAttribute('Cantidad', "20.000000");
        $nodeMercancia->setAttribute('ClaveUnidad', "KGM");
        $nodeMercancia->setAttribute('PesoEnKg', "20.000");
        $nodeMercancias->appendChild($nodeMercancia);

        // CantidadTransporta
        $nodeCantidadTransporta = $dom->createElement('cartaporte20:CantidadTransporta');
        $nodeCantidadTransporta->setAttribute('Cantidad', "10.000000");
        $nodeCantidadTransporta->setAttribute('IDOrigen', "OR022191");
        $nodeCantidadTransporta->setAttribute('IDDestino', "DE000131");
        $nodeMercancia->appendChild($nodeCantidadTransporta);

        $nodeCantidadTransporta = $dom->createElement('cartaporte20:CantidadTransporta');
        $nodeCantidadTransporta->setAttribute('Cantidad', "10.000000");
        $nodeCantidadTransporta->setAttribute('IDOrigen', "OR029621");
        $nodeCantidadTransporta->setAttribute('IDDestino', "DE000131");
        $nodeMercancia->appendChild($nodeCantidadTransporta);

        //Mercancía
        $nodeMercancia = $dom->createElement('cartaporte20:Mercancia');
        $nodeMercancia->setAttribute('BienesTransp', "50401736");
        $nodeMercancia->setAttribute('Descripcion', "AGUACATE MADURO");
        $nodeMercancia->setAttribute('Cantidad', "15.000000");
        $nodeMercancia->setAttribute('ClaveUnidad', "KGM");
        $nodeMercancia->setAttribute('PesoEnKg', "15.000");
        $nodeMercancias->appendChild($nodeMercancia);

        // CantidadTransporta
        $nodeCantidadTransporta = $dom->createElement('cartaporte20:CantidadTransporta');
        $nodeCantidadTransporta->setAttribute('Cantidad', "15.000000");
        $nodeCantidadTransporta->setAttribute('IDOrigen', "OR000131");
        $nodeCantidadTransporta->setAttribute('IDDestino', "DE028591");
        $nodeMercancia->appendChild($nodeCantidadTransporta);

        // Autotransporte
        $nodeAutotransporte = $dom->createElement('cartaporte20:Autotransporte');
        $nodeAutotransporte->setAttribute('PermSCT', "TPAF01");
        $nodeAutotransporte->setAttribute('NumPermisoSCT', "666");
        $nodeMercancias->appendChild($nodeAutotransporte);

        // IdentificacionVehicular
        $nodeIdentificacionVehicular = $dom->createElement('cartaporte20:IdentificacionVehicular');
        $nodeIdentificacionVehicular->setAttribute('ConfigVehicular', "C2");
        $nodeIdentificacionVehicular->setAttribute('PlacaVM', "55XSD");
        $nodeIdentificacionVehicular->setAttribute('AnioModeloVM', "2021");
        $nodeAutotransporte->appendChild($nodeIdentificacionVehicular);

        // Seguros
        $nodeSeguros = $dom->createElement('cartaporte20:Seguros');
        $nodeSeguros->setAttribute('AseguraRespCivil', "SEGUROS PRUEBA");
        $nodeSeguros->setAttribute('PolizaRespCivil', "444");
        $nodeAutotransporte->appendChild($nodeSeguros);

        // FiguraTransporte
        $nodeFiguraTransporte = $dom->createElement('cartaporte20:FiguraTransporte');
        $nodeCartaPorte->appendChild($nodeFiguraTransporte);

        // TiposFigura
        $nodeTiposFigura = $dom->createElement('cartaporte20:TiposFigura');
        $nodeTiposFigura->setAttribute('TipoFigura', "01");
        $nodeTiposFigura->setAttribute('RFCFigura', "AVI980824Q32");
        $nodeTiposFigura->setAttribute('NumLicencia', "B33555");
        $nodeFiguraTransporte->appendChild($nodeTiposFigura);

        $nodeTiposFigura = $dom->createElement('cartaporte20:TiposFigura');
        $nodeTiposFigura->setAttribute('TipoFigura', "02");
        $nodeTiposFigura->setAttribute('RFCFigura', "ALI030821T86");
        $nodeFiguraTransporte->appendChild($nodeTiposFigura);

        // PartesTransporte
        $nodePartesTransporte = $dom->createElement('cartaporte20:PartesTransporte');
        $nodePartesTransporte->setAttribute('ParteTransporte', "PT04");
        $nodeTiposFigura->appendChild($nodePartesTransporte);

		$dom->appendChild($root);
        $xml_file_name = base_path("temp\\temp.xml");
	    $dom->save($xml_file_name);
    }

    public static function createOriginalString()
    {
        //ruta al archivo XML del CFDI
        $xmlFileRoute = base_path("temp\\temp.xml");
    
        // Ruta al archivo XSLT
        $xslFile = base_path("cadenaoriginal\\cadenaoriginal.xslt"); 
    
        // Crear un objeto DOMDocument para cargar el CFDI
        $xmlObj = new DOMDocument("1.0", "UTF-8");
        // Cargar el CFDI
        $xmlObj->load($xmlFileRoute);
    
        // Crear un objeto DOMDocument para cargar el archivo de transformación XSLT
        $xsl = new DOMDocument();
        $xsl->load($xslFile);
    
        // Crear el procesador XSLT que nos generará la cadena original con base en las reglas descritas en el XSLT
        // $proc = new XsltProcessor(new NullCache());
        $proc = new XSLTProcessor;
        // Cargar las reglas de transformación desde el archivo XSLT.
        $proc->importStyleSheet($xsl);
        // Generar la cadena original y asignarla a una variable
        $cadenaOriginal = $proc->transformToXML($xmlObj);
    
        return $cadenaOriginal;
    }

    public static function createOriginalStringFromString($theXml)
    {
        file_put_contents(base_path("temp\\temp.xml"), $theXml);

        //ruta al archivo XML del CFDI
        $xmlFileRoute = base_path("temp\\temp.xml");
    
        // Ruta al archivo XSLT
        $xslFile = base_path("cadenaoriginal\\cadenaoriginal.xslt"); 
    
        // Crear un objeto DOMDocument para cargar el CFDI
        $xmlObj = new DOMDocument("1.0", "UTF-8");
        // Cargar el CFDI
        $xmlObj->load($xmlFileRoute);
    
        // Crear un objeto DOMDocument para cargar el archivo de transformación XSLT
        $xsl = new DOMDocument();
        $xsl->load($xslFile);
    
        // Crear el procesador XSLT que nos generará la cadena original con base en las reglas descritas en el XSLT
        // $proc = new XsltProcessor(new NullCache());
        $proc = new XSLTProcessor;
        // Cargar las reglas de transformación desde el archivo XSLT.
        $proc->importStyleSheet($xsl);
        // Generar la cadena original y asignarla a una variable
        $cadenaOriginal = $proc->transformToXML($xmlObj);
    
        return $cadenaOriginal;
    }

    public function sellarXml($originalString, $key, $certificate)
    {
        $pem = chunk_split($certificate, 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n" . $pem . "-----END CERTIFICATE-----\n";
        $pkeyid = openssl_get_privatekey($pem);
        openssl_sign($originalString, $signature, $pkeyid);
        openssl_free_key($pkeyid);
        $signature = base64_encode($signature);
        
        return $signature;
    }

    public static function getStamp($originalString = "")
    {
        //Sellar
        $fileKey = base_path("docs/sat/CSD_INNOVACION_VALOR_Y_DESARROLLO_SA_DE_CV_IVD920810GU2_20190617_133410.key"); // Ruta al archivo key
        // $key = new RSA();
        // $key->setPassword("12345678a");//Clave 
        $contents = file_get_contents($fileKey);
        // $key->loadKey($contents);
        // $key->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        // $private = openssl_pkey_get_private($key->getPrivateKey(), "12345678a");//Otra vez la clave
        // $k = $key->getPrivateKey();
        // $pkey = openssl_get_privatekey( $key->getPrivateKey() );
        // $signature = "";
        // openssl_sign( $originalString, $signature, $pkey );
        // $sello = $key->sign($originalString);
        // $sig = "";
        // openssl_sign($originalString, $sig, $private, OPENSSL_ALGO_SHA256);
        // $sello = base64_encode($sig);

        // $str = $contents;
        // $str = chunk_split($str, 64, "\n");
        // $key = "-----BEGIN RSA PRIVATE KEY-----\n$str-----END RSA PRIVATE KEY-----\n";
        // $signature = '';
        // if (openssl_sign($originalString, $signature, $key, OPENSSL_ALGO_MD5)) {
        //     dd(base64_encode($signature));
        // }

        $key = new RSA();
        $key->loadKey($contents);
        $key->setPassword("12345678a"); //"$this->password_key": your .key file password
        $key->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
        $key->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        // $key->setSignatureMode(RSA::SIGNATURE_PSS);
        $key->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $signature = $key->sign($originalString);

        return $signature;
    }

    public function getCertificate()
    {
        $certificado = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($archivo_cer)));

        return $certificado;
    }

    public static function loadCarta() {
        $dom = new DOMDocument();
        $testXML = file_get_contents(base_path("test.xml"));
        $dom->loadXML($testXML);

        return $dom;
    }
}