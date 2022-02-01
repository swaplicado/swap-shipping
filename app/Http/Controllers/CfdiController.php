<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Pdf;

class CfdiController extends Controller
{

    public function index(){
        $mpdf = $this->generatePDF();
        // $str = mb_convert_encoding($mpdf, "UTF-8", "ISO-8859-1");
        $base64 = base64_encode($mpdf);
        $bson = \MongoDB\BSON\fromPHP(['data' => $base64]);
        // $value = \MongoDB\BSON\toPHP($bson);
        return view('pdf', ['base64' => $base64]);
    }

    function claveDescription($clave, $catalogo){
        $value = null;
        switch($catalogo){
            case 'RegimenFiscal':
                $value = DB::table('sat_tax_regimes')->select('description')->where('key_code', $clave)->first();
                break;
            case 'FormaPago':
                $value = DB::table('sat_payment_forms')->select('description')->where('key_code','=', $clave)->first();
                break;
            case 'MetodoPago':
                $value = DB::table('sat_payment_methods')->select('description')->where('key_code', $clave)->first();
                break;
            case 'taxes':
                $value = DB::table('sat_taxes')->select('description')->where('key_code', $clave)->first();
            default:
                break;
        }

        if($value != null){
            $description = ' - '.$value->description;
        }else{
            $description = null;
        }
        return $description;
    }

    function generateQR($comprobante){
        $QR = null;
        $url = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?";

        if($comprobante != null){
            $Emisor = $comprobante->searchNode('cfdi:Emisor');
            $Receptor = $comprobante->searchNode('cfdi:Receptor');
            $Conceptos = $comprobante->searchNode('cfdi:Conceptos');
            $Impuestos = $comprobante->searchNode('cfdi:Impuestos');
            $Complemento = $comprobante->searchNode('cfdi:Complemento');
        } else {
            $Emisor = null;
            $Receptor = null;
            $Conceptos = null;
            $Impuestos = null;
            $Complemento = null;
        }

        null == $Complemento ? $tfd = null : $tfd = $Complemento->searchNode('tfd:TimbreFiscalDigital');
        null == $tfd ? $id = null : (null == $tfd['UUID'] ? $id = null : $id = "&id=".$tfd['UUID']);
        null == $Emisor ? $re = null : (null == $Emisor['Rfc'] ? $re = null : $re = "&re=".$Emisor['Rfc']);
        null == $Receptor ? $rr = null : (null == $Receptor['Rfc'] ? $rr = null : $rr = "&rr=".$Receptor['Rfc']);
        null == $comprobante ? $tt = null : (null == $comprobante['Total'] ? $tt = null : $tt = "&tt=".$comprobante['Total']);
        null == $comprobante ? $sello = null : $sello = $comprobante['Sello'];

        if (null == $sello) {
            $fe = null;
        } else {
            $fe = "&fe=";
            for($i = strlen($sello) - 8; $i<strlen($sello); $i++){
                $fe .= $sello[$i];
            }
        }

        if($id != null && $re != null && $rr != null && $tt != null && $fe != null){
            $ObjQr = QrCode::size(100)->generate($url.$id.$re.$rr.$tt.$fe);
            $arrQR = explode("\n", (String)$ObjQr); 
            $QR = $arrQR[1];
        }

        return $QR;
    }

    public function generatePDF(){
        $filepath = file_get_contents("./doc/prueba.xml");

        $cfdi = \CfdiUtils\Cfdi::newFromString($filepath);
        $cfdi->getVersion();
        $cfdi->getDocument();
        $cfdi->getSource();
    
        $comprobante = $cfdi->getNode(); // Nodo de trabajo del nodo cfdi:Comprobante

        /*Obtención de los nodos padres Emisor, Receptor, Conceptos, Impuestos, Complemento, CFDIRelacionados*/
        if($comprobante != null){
            $Emisor = $comprobante->searchNode('cfdi:Emisor');
            $Receptor = $comprobante->searchNode('cfdi:Receptor');
            $Conceptos = $comprobante->searchNode('cfdi:Conceptos');
            $Impuestos = $comprobante->searchNode('cfdi:Impuestos');
            $Complemento = $comprobante->searchNode('cfdi:Complemento');
            $CFDIRelacionados = $comprobante->searchNode('cfdi:CfdiRelacionados');
        } else {
            $Emisor = null;
            $Receptor = null;
            $Conceptos = null;
            $Impuestos = null;
            $Complemento = null;
            $CFDIRelacionados = null;
        }
        /*Nodos hijos (Concepto) del nodo Conceptos*/
        null == $Conceptos ? $Concepto = null : $Concepto = $Conceptos->searchNodes('cfdi:Concepto');

        /*Nodos hijos (TimbreFiscalDigital, CartaPorte) del nodo complemento*/        
        if($Complemento != null){
            $tfd = $Complemento->searchNode('tfd:TimbreFiscalDigital');
            $CartaPorte = $Complemento->searchNode('cartaporte20:CartaPorte');
        } else {
            $tfd = null;
            $CartaPorte = null;
        }

        /*Nodo hijo (CFDIRelacionado) del nodo CFDIRelacionados*/
        null == $CFDIRelacionados ? $CFDIRelacionado = null : $CFDIRelacionado = $CFDIRelacionados->searchNode('cfdi:CFDIRelacionado');

        $QR = $this->generateQR($comprobante);

        /*Atributos (Serie, Folio, Fecha, LugarExpedicion, TipoDeComprobante, Moneda, FormaPago, TipoCambio,
             MetodoPago, SubTotal, Total, NoCertificado) del nodo Comprobante */        
        if($comprobante != null){
            $serie = $comprobante['Serie'];
            $Folio = $comprobante['Folio'];
            $Fecha = $comprobante['Fecha'];
            $LugarExpedicion = $comprobante['LugarExpedicion'];
            $TipoDeComprobante = $comprobante['TipoDeComprobante'];
            $Moneda = $comprobante['Moneda'];
            $FormaPago = $comprobante['FormaPago'].$this->claveDescription($comprobante['FormaPago'], 'FormaPago');
            $TipoCambio = $comprobante['TipoCambio'];
            $MetodoPago = $comprobante['MetodoPago'].$this->claveDescription($comprobante['MetodoPago'], 'MetodoPago');
            $SubTotal = $comprobante['SubTotal'];
            $Total = $comprobante['Total'];
            $NoCertificado = $comprobante['NoCertificado'];
        } else {
            $serie = null;
            $Folio = null;
            $Fecha = null;
            $LugarExpedicion = null;
            $TipoDeComprobante = null;
            $Moneda = null;
            $FormaPago = null;
            $TipoCambio = null;
            $MetodoPago = null;
            $SubTotal = null;
            $Total = null;
            $NoCertificado = null;
        }

        /*Atributos (Rfc, Nombre, RegimenFiscal) del nodo Emisor */
        if($Emisor != null){
            $Rfc_E = $Emisor['Rfc'];
            $Nombre_E = $Emisor['Nombre'];
            $RegimenFiscal_E = $Emisor['RegimenFiscal'].$this->claveDescription($Emisor['RegimenFiscal'], 'RegimenFiscal');
        }else{
            $Rfc_E = null;
            $Nombre_E = null;
            $RegimenFiscal_E = null;
        }

        /*Atributos (Rfc, Nombre, RegimenFiscal) del nodo Receptor*/        
        if($Receptor != null){
            $Rfc_R = $Receptor['Rfc'];
            $Nombre_R = $Receptor['Nombre'];
            $RegimenFiscal_R = $Receptor['RegimenFiscalReceptor'].$this->claveDescription($Receptor['RegimenFiscal'], 'RegimenFiscal');;
            $DomicilioFiscalReceptor = $Receptor['DomicilioFiscalReceptor'];
        } else {
            $Rfc_R = null;
            $Nombre_R = null;
            $RegimenFiscal_R = null;
            $DomicilioFiscalReceptor = null;
        }

        /*Atributos (TipoRelacion, UUID) del nodo CFDIRelacionado*/        
        if($CFDIRelacionado != null){
            $TipoRelacion = $CFDIRelacionado['TipoRelacion'];
            $UUID_CFDIRelacionado = $CFDIRelacionado['UUID'];
        } else {
            $TipoRelacion = null;
            $UUID_CFDIRelacionado = null;
        }

        /*Atributos (TotalImpuestosTrasladados, TotalImpuestosRetenidos) del nodo Impuestos*/        
        if($Impuestos != null){
            $TotalImpuestosTrasladados = $Impuestos['TotalImpuestosTrasladados'];
            $TotalImpuestosRetenidos = $Impuestos['TotalImpuestosRetenidos'];
        } else {
            $TotalImpuestosTrasladados = null;
            $TotalImpuestosRetenidos = null;
        }

        /*Atributos (UUID, SelloSAT, SelloCFD) del nodo TimbreFiscalDigital*/
        if($tfd != null){
            $UUID_tfd = $tfd['UUID'];
            $SelloSAT = $tfd['SelloSAT'];
            $SelloCFD = $tfd['SelloCFD'];
            $FechaTimbrado = $tfd['FechaTimbrado'];
        } else {
            $UUID_tfd = null;
            $SelloSAT = null;
            $SelloCFD = null;
        }

        /*Atributos (Version, TranspInternac, TotalDistRec) y nodos hijos (Ubicaciones, Mercancias, FiguraTransporte)
            del nodo CartaPorte  */
        if($CartaPorte != null) {
            $Version = $CartaPorte['Version'];
            $TranspInternac = $CartaPorte['TranspInternac'];
            $TotalDistRec = $CartaPorte['TotalDistRec'];
            $Ubicaciones = $CartaPorte->searchNode('cartaporte20:Ubicaciones');
            $Mercancias = $CartaPorte->searchNode('cartaporte20:Mercancias');
            $FiguraTransporte = $CartaPorte->searchNode('cartaporte20:FiguraTransporte');
        } else {
            $Version = null;
            $TranspInternac = null;
            $TotalDistRec = null;
            $Ubicaciones = null;
            $Mercancias = null;
        }

        /*Nodos hijos (Ubicacion) del nodo Ubicaciones*/        
        $Ubicaciones != null ? $Ubicacion = $Ubicaciones->searchNodes('cartaporte20:Ubicacion') : $Ubicacion = null; 

        /*Atributos (NumTotalMercancias, PesoBrutoTotal, UnidadPeso) y nodos hijos (Mercancia, Autotransporte)
            del nodo Mercancias*/        
        if($Mercancias != null) {
            $NumTotalMercancias = $Mercancias['NumTotalMercancias'];
            $PesoBrutoTotal = $Mercancias['PesoBrutoTotal'];
            $UnidadPeso = $Mercancias['UnidadPeso'];
            $Mercancia = $Mercancias->searchNodes('cartaporte20:Mercancia');
            $Autotransporte = $Mercancias->searchNode('cartaporte20:Autotransporte');
        } else {
            $NumTotalMercancias = null;
            $PesoBrutoTotal = null;
            $UnidadPeso = null;
            $Mercancia = null;
        }

        /*Atributos (PermSCT, NumPermisoSCT) y nodos hijos(Seguros, Remolques, IdentificacionVehicular)
            del nodo Autotransporte */        
        if($Autotransporte != null){
            $PermSCT = $Autotransporte['PermSCT'];
            $NumPermisoSCT = $Autotransporte['NumPermisoSCT'];
            $Seguros = $Autotransporte->searchNode('cartaporte20:Seguros');
            $Remolques = $Autotransporte->searchNode('cartaporte20:Remolques');
            $IdentificacionVehicular = $Autotransporte->searchNode('cartaporte20:IdentificacionVehicular');
        }

        /*Atributos (AseguraRespCivil, AseguraMedAmbiente, AseguraCarga, PolizaRespCivil, PolizaMedAmbiente,  PolizaCarga)
            del nodo Seguros */        
        if($Seguros != null){
            $AseguraRespCivil = $Seguros['AseguraRespCivil'];
            $AseguraMedAmbiente = $Seguros['AseguraMedAmbiente'];
            $AseguraCarga = $Seguros['AseguraCarga'];
            $PolizaRespCivil = $Seguros['PolizaRespCivil'];
            $PolizaMedAmbiente = $Seguros['PolizaMedAmbiente'];
            $PolizaCarga = $Seguros['PolizaCarga'];

            /*Aseguradoras contiene todas las aseguradoras que puede tener el nodo seguros */
            $Aseguradoras = $AseguraRespCivil;
            null == $AseguraMedAmbiente ? '' : $Aseguradoras = $Aseguradoras.'<br><br>'.$AseguraMedAmbiente;
            null == $AseguraCarga ? '' : $Aseguradoras = $Aseguradoras.'<br><br>'.$AseguraCarga;

            /*polizas contiene todas las polizas que puede tener el nodo seguros */
            $polizas = $PolizaRespCivil;
            null == $PolizaMedAmbiente ? '' : $polizas = $polizas.'<br><br>'.$PolizaMedAmbiente;
            null == $PolizaCarga ? '' : $polizas = $polizas.'<br><br>'.$PolizaCarga;
        }

        /*Atributos (ConfigVehicular, PlacaVM, AnioModeloVM) del nodo IdentificacionVehicular */        
        if($IdentificacionVehicular != null){
            $ConfigVehicular = $IdentificacionVehicular['ConfigVehicular'];
            $PlacaVM = $IdentificacionVehicular['PlacaVM'];
            $AnioModeloVM = $IdentificacionVehicular['AnioModeloVM'];
        }

        /*Nodos hijos (Remolque) del nodo Remolques*/        
        $Remolques != null ? $Remolque = $Remolques->searchNodes('cartaporte20:Remolque') : $Remolque = null;

        /*Atributos (Placa, SubTipoRem) */        
        $Placa_Remolque = '';
        $SubTipoRem = '';
        foreach($Remolque as $r){
            $Placa_Remolque = $Placa_Remolque.$r['Placa'].'<br>';
            $SubTipoRem = $SubTipoRem.$r['SubTipoRem'].'<br>';
        }

        /*Nodos hijos (TiposFigura) del nodo FiguraTransporte */        
        $FiguraTransporte != null ? $TiposFigura = $FiguraTransporte->searchNodes('cartaporte20:TiposFigura') : $TiposFigura = null;

        /*Creación de la tabla partidas */
        $tabla_partidas = '';
        foreach($Concepto as $c){
            null == $c->searchNode('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado') ? $Base = null : $Base = $c->searchNode('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado')['Base'];
            $Traslados = $c->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
            $Retenciones = $c->searchNodes('cfdi:Impuestos', 'cfdi:Retenciones', 'cfdi:Retencion');

            $tabla_impuestosT_concepto = "";
            foreach($Traslados as $t){
                $tipoImpuesto = $this->claveDescription($t['Impuesto'], 'taxes');
                $tabla_impuestosT_concepto = $tabla_impuestosT_concepto.
                    '
                    <tr>
                        <td class = "text-c">Traslados</td>
                        <td class = "text-c">'.$t['Impuesto'].$tipoImpuesto.'</td>
                        <td class = "text-r">'.$t['Base'].'</td>
                        <td class = "text-c">'.$t['TipoFactor'].'</td>
                        <td class = "text-r">'.$t['TasaOCuota'].'</td>
                        <td class = "text-r">'.$t['Importe'].'</td>
                    </tr>
                    ';
            }

            $tabla_impuestosR_concepto = "";
            foreach($Retenciones as $r){
                $tipoImpuesto = $this->claveDescription($t['Impuesto'], 'taxes');
                $tabla_impuestosR_concepto = $tabla_impuestosR_concepto.
                    '
                    <tr>
                        <td class = "text-c">Retenciones</td>
                        <td class = "text-c">'.$r['Impuesto'].$tipoImpuesto.'</td>
                        <td class = "text-r">'.$r['Base'].'</td>
                        <td class = "text-c">'.$r['TipoFactor'].'</td>
                        <td class = "text-r">'.$r['TasaOCuota'].'</td>
                        <td class = "text-r">'.$r['Importe'].'</td>
                    </tr>
                    ';
            }

            $tabla_partidas = $tabla_partidas.
                            '
                            <tr>
                                <td class="td1 text-c">'.$c['ClaveProdServ'].'</td>
                                <td class="td1 text-c">'.$c['Descripcion'].'</td>
                                <td class="td1 text-r">'.$c['Cantidad'].'</td> 
                                <td class="td1 text-c">'.$c['ClaveUnidad'].'</td>
                                <td class="td1 text-r">'.$c['ValorUnitario'].'</td> 
                                <td class="td1 text-r">'.$c['Importe'].'</td>
                                <td class="td1 text-r">'.$c['Descuento'].'</td>
                            </tr>
                            <tr>
                                <td style = "border-bottom: solid 0.03cm black;"></td>
                                <td colspan = "6" style = "border-bottom: solid 0.03cm black;">
                                    <table style = "width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td class = "th3">Impuestos:</td>
                                                <td class = "th3">Impuesto:</td>
                                                <td class = "th3">Base:</td>
                                                <td class = "th3">Tipo Factor:</td>
                                                <td class = "th3">Tasa o cuota:</td>
                                                <td class = "th3">Importe</td>
                                            </tr>
                                            '.$tabla_impuestosT_concepto.'
                                            '.$tabla_impuestosR_concepto.'
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            ';
        }

        /*Creación de la tabla Mercania */        
        $tabla_Mercancia='';
        foreach($Mercancia as $m){
            $tabla_Mercancia = $tabla_Mercancia.'
                            <tr>
                                <td class="td1 text-c">'.$m['BienesTransp'].'</td>
                                <td class="td1 text-c">'.$m['Descripcion'].'</td>
                                <td class="td1 text-c">'.$m['Cantidad'].'</td>
                                <td class="td1 text-c">'.$m['ClaveUnidad'].'</td>
                                <td class="td1 text-c">'.$m['MaterialPeligroso'].'</td>
                                <td class="td1 text-c">'.$m['PesoEnKg'].'</td>
                                <td class="td1 text-r">'.$m['ValorMercancia'].'</td>
                                <td class="td1 text-c">'.$m['Moneda'].'</td>
                            </tr>                                    
            ';
        }

        /*Creación de la tabla operador */
        $tabla_Operador = '';
        foreach($TiposFigura as $tf){
            $tabla_Operador = '
                        <tr>
                            <td class="td1 text-c">'.$tf['RFCFigura'].'</td>
                            <td class="td1 text-c">'.$tf['NumLicencia'].'</td>
                            <td class="td1 text-c">'.$tf['Nombre'].'</td>
                        </tr>
            ';
        }

        /*Creación de la tabla Remitente/Destinatario de la carta porte */
        $tabla_Remitente_Destinatario = '';
        foreach($Ubicacion as $u){
            $CP_Rfc_r = null;
            $CP_Nombre_r = null;
            $CP_FechaHoraSalidaLlegada_r = null;
            $CP_Rfc_d = null;
            $CP_Nombre_d = null;
            $CP_FechaHoraSalidaLlegada_d = null;

            if($u['TipoUbicacion'] == "Origen"){
                $CP_Rfc_r = $u['RFCRemitenteDestinatario'];
                $CP_Nombre_r = $u['NombreRemitenteDestinatario'];
                $CP_FechaHoraSalidaLlegada_r = $u['FechaHoraSalidaLlegada'];
            } else {
                $CP_Rfc_d = $u['RFCRemitenteDestinatario'];
                $CP_Nombre_d = $u['NombreRemitenteDestinatario'];
                $CP_FechaHoraSalidaLlegada_d = $u['FechaHoraSalidaLlegada'];
            }
            $tabla_Remitente_Destinatario = $tabla_Remitente_Destinatario.
                                            '
                                            <tr>
                                                <td class="td1 text-c">'.$CP_Rfc_r.'</td>
                                                <td class="td1 text-c">'.$CP_Nombre_r.'</td>
                                                <td class="td1 text-c">'.$CP_FechaHoraSalidaLlegada_r.'</td>
                                                <td class="td1 text-c">'.$CP_Rfc_d.'</td>
                                                <td class="td1 text-c">'.$CP_Nombre_d.'</td>
                                                <td class="td1 text-c">'.$CP_FechaHoraSalidaLlegada_d.'</td>
                                            </tr>
                                            ';
        }

        /*Creación de la tabla ubicaciones origen */
        $tabla_ubicacion_origen = '';
        foreach($Ubicacion as $u){
            if($u['TipoUbicacion'] == "Origen"){
                $Domicilio = $u->searchNode('cartaporte20:Domicilio');
                if($Domicilio != null){
                    $tabla_ubicacion_origen = $tabla_ubicacion_origen.
                                            '
                                            <tr>
                                                <td class="td1 text-c">'.$Domicilio['Calle'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroExterior'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroInterior'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Colonia'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Localidad'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Referencia'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Municipio'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Estado'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Pais'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['CodigoPostal'].'</td>
                                            </tr> 
                                            ';
                }
            }
        }

        /*Creación de la tabla ubicaciones destino */
        $tabla_ubicacion_destino = '';
        foreach($Ubicacion as $u){
            if($u['TipoUbicacion'] == "Destino"){
                $Domicilio = $u->searchNode('cartaporte20:Domicilio');
                if($Domicilio != null){
                    $tabla_ubicacion_destino = $tabla_ubicacion_destino.
                                            '
                                            <tr>
                                                <td class="td1 text-c">'.$Domicilio['Calle'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroExterior'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroInterior'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Colonia'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Localidad'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Referencia'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Municipio'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Estado'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['Pais'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['CodigoPostal'].'</td>
                                                <td class="td1 text-c">'.$u['DistanciaRecorrida'].'</td>
                                            </tr> 
                                            ';
                }
            }
        }

        /*Encabezado del pdf */
        $header = '
            <table class = "container border">
                <tbody>
                    <tr>
                        <td class = "border" style = "width: 15%;">
                            <img src="./img/svg/orange.svg" style="width: 2cm;">
                        </td>
                        <td class = "border" style = "width: 54%;">
                            <b style = "font-size: 3.5mm;">'.$Nombre_E.'</b>
                            <p style="margin-top: 0; font-size: 3mm">
                                <b>RFC: </b>'.$Rfc_E.'<br>
                                <b>Regimen fiscal: </b>'.$RegimenFiscal_E.'<br>
                            </p>
                        </td>
                        <td class = "border text-c" style = "width: 30%;">
                            <b style = "font-size: 3mm">Folio fiscal:</b>
                            <p style = "font-size: 3mm; margin:0px; outline:none;">'.$Folio.'</p>
                            <b style = "font-size: 3mm;">Serie:</b>
                            <p style = "font-size: 3mm; margin:0px; outline:none;">'.$serie.'</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "2" class = "border" style = "width: 76.3%; text-align: left">
                            <p style="margin-top: 0; font-size: 3mm">
                                <b>Receptor: </b>'.$Nombre_R.'<br>
                                <b>RFC: </b>'.$Rfc_R.'
                                <b>&nbsp; Regimen fiscal: </b>'.$RegimenFiscal_R.'<br>
                                <b>Domicilio: </b>'.$DomicilioFiscalReceptor.'
                            </p>
                        </td>
                        <td class = "border text-c" style = "width: 23%;">
                            <b>FACTURA</b>
                            <p style="margin-top: 0; font-size: 3mm">
                                8040<br>
                                <b>Lugar y fecha de expedición:</b><br>
                                '.$LugarExpedicion.'<br>'.$Fecha.'<br>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "3" class = "border">
                            <table class = "container">
                                <tbody>
                                    <tr>
                                        <td class = "th3">Embarque</td>
                                        <td class = "th3">Contrato</td>
                                        <td class = "th3">Pedido</td>
                                        <td class = "th3">Cliente</td>
                                        <td class = "th3">Sucursal</td>
                                        <td class = "th3">Agente</td>
                                        <td class = "th3">Moneda</td>
                                        <td class = "th3">Tipo de cambio</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-c"></td>
                                        <td class = "text-c"></td>
                                        <td class = "text-c"></td>
                                        <td class = "text-c"></td>
                                        <td class = "text-c"></td>
                                        <td class = "text-c"></td>
                                        <td class = "text-c">'.$Moneda.'</td>
                                        <td class = "text-r">'.$TipoCambio.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        ';


        /*Pie de pagina del pdf */
        $footer = '
            <table class = "container" style = "border-top: 0.03cm solid #000000;">
                <tbody>
                    <tr>
                        <td class = "th3">
                            Este documento es una representación impresa de un CFDI
                        </td>
                        <td class = "th3" style = "text-align: right;">
                            Página {PAGENO} de {nb}
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "2" class = "text-c" style = "font-size: 2.5mm">
                            EL PRESENTE DOCUMENTO NO CONSTITUYE UN RECIBO DE PAGO DE LA CANTIDAD CONSIGNADA EN EL MISMO, 
                            SI NO CUENTA CON RECIBO DE PAGO Y/O CERTIFICADO DEL MISMO TRATÁNDOSE DE PAGO EFECTUADO, 
                            O COMPROBANTE DE DEPÓSITO O TRANSFERENCIA BANCARIA SUJETOS A SU VERIFICACIÓN EN CUANTO A SU
                            AUTENTICIDAD.
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "2" class = "text-c" style = "font-size: 2mm">
                            COMPROBANTE FISCAL GENERADO CON *NOMBRE PENDIENTE SWOFTWARE APLICADO S.A. DE C.V.
                            Tels.:(443) 204-1146 y 47 www.swaplicado.com.mx
                        </td>
                    </tr>
                </tbody>
            </table>
        ';

        $cfdi_relacionado = '';
        if($UUID_CFDIRelacionado != null){
            $cfdi_relacionado = '<tr>
                                    <td style="border-bottom: 0.03cm solid">Tipo Relación: </td>
                                    <td style="border-bottom: 0.03cm solid">'.$TipoRelacion.'</td>
                                    <td style="border-bottom: 0.03cm solid">CFDI Relacionado: </td>
                                    <td style="border-bottom: 0.03cm solid">'.$UUID_CFDIRelacionado.'</td>
                                </tr>
                    ';
        }

        /*codigo Html que sera transformado a PDF */  
        $html = '
            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <td class="th2">ClaveProdServ</td>
                        <td class="th2">Descripción</td>
                        <td class="th2">Cantidad</td>
                        <td class="th2">Clave Unidad</td>
                        <td class="th2">Valor Unitario</td>
                        <td class="th2">Importe</td>
                        <td class="th2">Descuento</td>
                    </tr>
                    '.$tabla_partidas.'
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <td style = "width: 40%;"><b>Importe con letra:</b></td>
                        <td style = "width: 20%"><b>Uso CFDI:</b></td>
                        <td rowspan = "4" style = "border-bottom: 0.03cm solid black; width: 40%">
                            <table style = "width: 100%;">
                                <tbody>
                                    <tr>
                                        <td class = "text-r"><b>SubTotal:</b></td>
                                        <td class = "text-r">'.$SubTotal.' '.$Moneda.'</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-r"><b>Descuento:</b></td>
                                        <td class = "text-r">'.$Moneda.'</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-r"><b>Total impuestos trasslado:</b></td>
                                        <td class = "text-r">'.$TotalImpuestosTrasladados.' '.$Moneda.'</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-r"><b>Total impuestoa retenido:</b></td>
                                        <td class = "text-r">'.$TotalImpuestosRetenidos.' '.$Moneda.'</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-r"><b>Total comprobante:</b></td>
                                        <td class = "text-r">'.$Total.' '.$Moneda.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style = "width: 40%;">Importe letra</td>
                        <td style = "width: 20%;">uso cfdi</td>
                    </tr>
                    <tr>
                        <td style = "width: 40%;"><b>Forma pago:</b></td>
                        <td style = "width: 20%"><b>Metodo pago:</b></td>
                    </tr>
                    <tr>
                        <td style = "border-bottom: 0.03cm solid black; width: 40%;">'.$FormaPago.'</td>
                        <td style = "border-bottom: 0.03cm solid black; width: 20%;">'.$MetodoPago.'</td>
                    </tr>
                </tbody>
            </table>

            <div class = "container">
                <div style = "font-size: 2.5mm; font-family: sans-serif; font-weight: bold;">
                    Sello digital del CFDI:
                </div>
                <div style = "font-size: 2mm">'.$SelloCFD.'</div>
                <div style = "font-size: 2.5mm; font-family: sans-serif; font-weight: bold;">
                    Sello digital del SAT:
                </div>
                <div style = "font-size: 2mm; font-family: sans-serif;">'.$SelloSAT.'</div>
                <div style = "font-size: 2.5mm; font-family: sans-serif; font-weight: bold;">
                    Comprobante original del complemento de certificación digital del SAT:
                </div>
                <div style = "font-size: 2mm; font-family: sans-serif;">cadena original</div>
            </div>
            
            <br>

            <table style = "width: 100%; margin-left: 0.5cm;">
                <tbody>
                    <tr>
                        <td rowspan = "4" style = "width: 16%;">'.$QR.'</td>
                        <td style = "font-size: 2.5mm; font-weight: bold; width: 22%;">
                            Número de serie del certificado:
                        </td>
                        <td  style = "font-size: 2.5mm; width: 22%;">
                            '.$NoCertificado.'
                        </td>
                        <td style = "font-size: 2.5mm; font-weight: bold; width: 22%;">
                            Fecha y hora de certificación:
                        </td>
                        <td style = "font-size: 2.5mm">
                            '.$FechaTimbrado.'
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "4">
                            <table>
                                <tbody>
                                    <tr>
                                        <td style = "width: 6%; font-size: 2.5mm; font-weight: bold;">UUID:</td>
                                        <td style = "font-size: 2.5mm">'.$UUID_tfd.'&nbsp;</td>
                                        <td style = "width: 17%; font-size: 2.5mm; font-weight: bold;">Régimen fiscal:</td>
                                        <td style = "font-size: 2.5mm">'.$RegimenFiscal_E.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "4" style = "font-size: 2.5mm">
                            LUGAR DE EXPEDICIÓN: Morelia Michoácan a ##.<bR>
                            POR ESTE PAGARÉ DEBO(EMOS) Y PAGARÉ(EMOS) INCONDICIONALMENTE A LA ORDEN DE ##
                            EL DIA ## , LA CANTIDAD DE '.$Total.' '.$Moneda.', EN ESTA CIUDAD DE ## O DONDE EXIJA EL TENEDOR,
                            IMPORTE DE LA MERCANCIA ARRIBA DESCRITA A MI(NUESTRA) ENTERA CONFORMIDAD. EN CASO DE MORA SE 
                            CONOVIENE EN PACTAR UN INTERÉS MORATORIO DEL ## MENSUAL DESDE SU VENCIMIENTO HASTA SU TOTAL LIQUIDACIÓN.
                         </td>
                    </tr>
                    <tr>
                        <td colspan = "4" class = "text-r" style = "font-size: 2.5mm">____________________<br>ACEPTO(AMOS)</td>
                    </tr>
                </tbody>
            </table>

            <table class = "container" style = "border-top: 0.03cm solid #000000;">
                <tbody>
                    <tr>
                        <td class = "th3" style = "font-size: 4mm; text-align: left;">
                            Complemento carta porte
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <td class="th2">Versión</td>
                        <td class="th2">Traslado internacional</td>
                        <td class="th2">Total distancia recorrida</td>
                        <td class="th2">Peso total mercancía</td>
                        <td class="th2">No. Total Mercancias</td>
                        <td class="th2">Clave transporte</td>
                    </tr>
                    <tr>
                        <td class="td1 text-c">'.$Version.'</td>
                        <td class="td1 text-c">'.$TranspInternac.'</td>
                        <td class="td1 text-c">'.$TotalDistRec.'</td>
                        <td class="td1 text-c">'.$PesoBrutoTotal.'</td>
                        <td class="td1 text-c">'.$NumTotalMercancias.'</td>
                        <td class="td1">01-Autotransporte federal</td>
                    </tr>    
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <th colspan="3" class="th1 border">Remitente:</th>
                        <th colspan="3" class="th1 border">Destinatario:</th>
                    </tr>
                    <tr>
                        <td class="th2">RFC remitente</td>
                        <td class="th2">Nombre remitente</td>
                        <td class="th2">Fecha y hora salida</td>
                        <td class="th2">RFC destinatario</td>
                        <td class="th2">Nombre destinatario</td>
                        <td class="th2">Fecha y hora salida</td>
                    </tr>
                    '.$tabla_Remitente_Destinatario.'
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <th colspan="10" class="th1">Ubicación origen:</th>
                    </tr>
                    <tr>
                        <td class="th2">Calle</td>
                        <td class="th2">No. Exterior</td>
                        <td class="th2">No. Interior</td>
                        <td class="th2">Colonia</td>
                        <td class="th2">Localidad</td>
                        <td class="th2">Referencia</td>
                        <td class="th2">Municipio</td>
                        <td class="th2">Estado</td>
                        <td class="th2">País</td>
                        <td class="th2">Código postal</td>
                    </tr>
                    '.$tabla_ubicacion_origen.'
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <th colspan="11" class="th1">Ubicación destino:</th>
                    </tr>
                    <tr>
                        <td class="th2">Calle</td>
                        <td class="th2">No. Exterior</td>
                        <td class="th2">No. Interior</td>
                        <td class="th2">Colonia</td>
                        <td class="th2">Localidad</td>
                        <td class="th2">Referencia</td>
                        <td class="th2">Municipio</td>
                        <td class="th2">Estado</td>
                        <td class="th2">País</td>
                        <td class="th2">Código postal</td>
                        <td class="th2">Distancia recorrida</td>
                    </tr>
                    '.$tabla_ubicacion_destino.' 
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <th colspan="8" class="th1">Mercancias:</th>
                    </tr>
                    <tr>
                        <td class="th2">Bienes transportados</td>
                        <td class="th2">Descripción</td>
                        <td class="th2">Cantidad</td>
                        <td class="th2">Clave unidad</td>
                        <td class="th2">Material peligroso</td>
                        <td class="th2">Peso en kg</td>
                        <td class="th2">Valor mercancia</td>
                        <td class="th2">Moneda</td>
                    </tr>
                    '.$tabla_Mercancia.' 
                </tbody>
            </table>

            <div style="width: 100%;">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <th colspan="4" class="th1 border" style="border-right: 0.03cm solid">Autotransporte federal:</th>
                            <th colspan="3" class="th1 border" style="border-right: 0.03cm solid">Identificación vehicular:</th>
                            <th colspan="2" class="th1 border">Remolque</th>
                        </tr>
                        <tr>
                            <td class="th2">Permiso SCT</td>
                            <td class="th2">No. Permiso SCT</td>
                            <td class="th2">Aseguradora</td>
                            <td class="th2">Póliza</td>
                            <td class="th2">Conf. Vehíc.</td>
                            <td class="th2">Placa vehíc.</td>
                            <td class="th2">Año modelo</td>
                            <td class="th2">Subt. remolque</td>
                            <td class="th2">Placa</td>
                        </tr>
                        <tr>
                            <td class="td1 text-c">'.$PermSCT.'</td>
                            <td class="td1 text-c">'.$NumPermisoSCT.'</td>
                            <td class="td1 text-c">'.$Aseguradoras.'</td>
                            <td class="td1 text-c">'.$polizas.'</td>
                            <td class="td1 text-c">'.$ConfigVehicular.'</td>
                            <td class="td1 text-c">'.$PlacaVM.'</td>
                            <td class="td1 text-c">'.$AnioModeloVM.'</td>
                            <td class="td1 text-c">'.$SubTipoRem.'</td>
                            <td class="td1 text-c">'.$Placa_Remolque.'</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="width: 100%;">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <th colspan="3" class="th1">Operador:</th>
                        </tr>
                        <tr>
                            <td class="th2">RFC Operador</td>
                            <td class="th2">No. Licencia</td>
                            <td class="th2">Nombre operador</td>
                        </tr>
                        '.$tabla_Operador.'
                    </tbody>
                </table>
            </div>
        ';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 65,
            'margin_bottom' => 30,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;
        $mpdf->use_kwt = true;

        $stylesheet = file_get_contents('./css/mpdf/mpdfMycss.css');

        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLfooter($footer);
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html,2);
        // $mpdf->Output('filename.pdf', \Mpdf\Output\Destination::FILE);
        return $mpdf->Output('', 'S');
    }
}