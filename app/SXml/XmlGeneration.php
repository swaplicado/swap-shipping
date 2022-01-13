<?php
namespace App\SXml;

use Carbon\Carbon;
use DOMDocument;
use DOMAttr;
use XSLTProcessor;
// use Genkgo\Xsl\XsltProcessor;
// use Genkgo\Xsl\Cache\NullCache;

class XmlGeneration {

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

    public function FunctionName(Type $var = null)
    {
        //Sellar
        $fileKey = Principal::$AppPath . "docs/sat/ACO560518KW7-20001000000300005692.key"; // Ruta al archivo key
        $rsa = new RSA();
        $rsa->setPassword("12345678a");//Clave 
        $rsa->load(file_get_contents($fileKey));
        $private = openssl_pkey_get_private($rsa->getPrivateKey(), "12345678a");//Otra vez la clave
        $sig = "";
        openssl_sign($this->CadenaOriginal, $sig, $private, OPENSSL_ALGO_SHA256);
        $sello = base64_encode($sig);
        $this->Comprobante->Sello = $sello;
    }

    public static function loadCarta() {
        $dom = new DOMDocument();
        $testXML = file_get_contents(base_path("test.xml"));
        $dom->loadXML($testXML);

        return $dom;
    }
}