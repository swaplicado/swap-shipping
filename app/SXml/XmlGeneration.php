<?php namespace App\SXml;

use Carbon\Carbon;
use DOMDocument;
use DOMAttr;
use XSLTProcessor;
use phpseclib\Crypt\RSA;
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
        $root->setAttribute("Serie", $oMongoDocument->serie);
        $root->setAttribute("Folio", $oMongoDocument->folio);

        $oDate = Carbon::parse($oMongoDocument->dtDate);
        $root->setAttribute("Fecha", $oDate->format('Y-m-d').'T'.$oDate->format('H:i:s'));
        // $root->setAttribute("Sello", "");
        $root->setAttribute("FormaPago", $oMongoDocument->formaPago);
        // $root->setAttribute("NoCertificado", "");
        $root->setAttribute("Certificado", "");
        $root->setAttribute("SubTotal", SFormats::formatNumber($oMongoDocument->subTotal));
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
            $nodeConcepto->setAttribute('Cantidad', SFormats::formatNumber($aConcept["quantity"], 4));
            $nodeConcepto->setAttribute('ClaveUnidad', $aConcept["claveUnidad"]);
            $nodeConcepto->setAttribute('Unidad', 'No aplica');
            $nodeConcepto->setAttribute('Descripcion', $aConcept["description"]);
            $nodeConcepto->setAttribute('ValorUnitario', SFormats::formatNumber($aConcept["valorUnitario"]));
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
                // $nodeUbicacion->setAttribute('DistanciaRecorrida', SFormats::formatNumber(0, 3));
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
                $ubicacionIndex++;
                continue;
            }

            // Ubicacion Destino
            $nodeUbicacion = $dom->createElement('cartaporte20:Ubicacion');
            $nodeUbicacion->setAttribute('TipoUbicacion', "Destino");
            $nodeUbicacion->setAttribute('IDUbicacion', $aUbicacion["IDUbicacion"]);
            $nodeUbicacion->setAttribute('RFCRemitenteDestinatario', $aUbicacion["rFCRemitenteDestinatario"]);
            $oDateUD = Carbon::parse($aUbicacion["fechaHoraSalidaLlegada"]);
            $nodeUbicacion->setAttribute('FechaHoraSalidaLlegada', $oDateUD->format('Y-m-d').'T'.$oDateUD->format('H:i:s'));
            $nodeUbicacion->setAttribute('DistanciaRecorrida', SFormats::formatNumber($aUbicacion["distanciaRecorrida"], 3));
            $nodeUbicaciones->appendChild($nodeUbicacion);
    
            // Domicilio
            $nodeDomicilio = $dom->createElement('cartaporte20:Domicilio');
            // $nodeDomicilio->setAttribute('Localidad', "09");
            // $nodeDomicilio->setAttribute('Municipio', $ubicacion->domicilio->municipio);
            $nodeDomicilio->setAttribute('Estado', $aUbicacion["domicilio"]["estado"]);
            $nodeDomicilio->setAttribute('Pais', $aUbicacion["domicilio"]["pais"]);
            $nodeDomicilio->setAttribute('CodigoPostal', $aUbicacion["domicilio"]["codigoPostal"]);            
            $nodeUbicacion->appendChild($nodeDomicilio);

            $ubicacionIndex++;
        }

        // Mercancías
        $nodeMercancias = $dom->createElement('cartaporte20:Mercancias');
        $nodeMercancias->setAttribute('PesoBrutoTotal', SFormats::formatNumber($oMongoDocument->oCartaPorte["mercancia"]["pesoBrutoTotal"], 4));
        $nodeMercancias->setAttribute('UnidadPeso', $oMongoDocument->oCartaPorte["mercancia"]["unidadPeso"]);
        $nodeMercancias->setAttribute('NumTotalMercancias', $oMongoDocument->oCartaPorte["mercancia"]["numTotalMercancias"]);
        $nodeCartaPorte->appendChild($nodeMercancias);

        foreach ($oMongoDocument->oCartaPorte["mercancia"]["mercancias"] as $aMerch) {
            //Mercancía
            $nodeMercancia = $dom->createElement('cartaporte20:Mercancia');
            $nodeMercancia->setAttribute('BienesTransp', $aMerch["bienesTransp"]);
            $nodeMercancia->setAttribute('Descripcion', $aMerch["descripcion"]);
            $nodeMercancia->setAttribute('Cantidad', SFormats::formatNumber($aMerch["cantidad"], 4));
            $nodeMercancia->setAttribute('ClaveUnidad', $aMerch["claveUnidad"]);
            $nodeMercancia->setAttribute('PesoEnKg', SFormats::formatNumber($aMerch["pesoEnKg"], 4));
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
        $nodeTiposFigura->setAttribute('NombreFigura', $oMongoDocument->oFigure["fullname"]);
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
        dd(base64_encode($originalString));

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