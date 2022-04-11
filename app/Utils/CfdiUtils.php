<?php

namespace App\Utils;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Models\Pdf;
use App\Models\Document;
use App\Models\M\MDocument;
use App\Utils\Configuration;
use App\Models\Carrier;
use App\Models\Sat\Units;
use App\Models\Sat\VehicleConfig;
use App\Models\Sat\Municipalities;
use App\Models\Sat\States;
use App\Models\M\MCarrierLogos;

class CfdiUtils
{
    public static function remisionistaCanEdit($carrier_id){
        $canEditStamp = Carrier::where('id_carrier', $carrier_id)->value('delega_edit_stamp');
        if($canEditStamp == 1){
            return true;
        }else{
            return false;
        }
    }

    public static function remisionistaCanStamp($carrier_id){
        $canEditStamp = Carrier::where('id_carrier', $carrier_id)->value('delega_edit_stamp');
        $canStamp = Carrier::where('id_carrier', $carrier_id)->value('delega_stamp');
        if($canEditStamp == 1 || $canStamp == 1){
            return true;
        }else{
            return false;
        }
    }

    public static function updatePdf($id, $xml, $carrier_id, $mdocument){
        // $logo_name = Carrier::where('id_carrier',$carrier_id)->value('logo');
        $logo = MCarrierLogos::where('carrier_id',$carrier_id)->first();
        $pdf = CfdiUtils::generatePDF($xml, $logo, $mdocument);
        DB::transaction( function () use($id, $pdf) {
            $Document = MDocument::findOrFail($id);
            $Document->pdf = $pdf;
            $Document->update();
        });
        return $pdf;
    }

    public static function index($id){
        $MDocumentId = Document::where('id_document', $id)->value('mongo_document_id');

        $pdf = MDocument::where('_id', $MDocumentId)->value('pdf');

        return view('pdf', ['pdf' => $pdf]);
    }

    static function getCoinKey($denomincacion){
        $value = '';
        switch ($denomincacion) {
            case 'MXN':
                $value = 'pesos';
                break;
            case 'USD':
                $value = 'dollars';
                break;
            default:
                break;
        }
        return $value;
    }

    static function claveDescription($clave, $catalogo){
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
            case 'impuestos':
                $value = DB::table('sat_taxes')->select('description')->where('key_code', $clave)->first();
                break;
            case 'Exportacion':
                $value = DB::table('sat_exports')->select('description')->where('key_code', $clave)->first();
                break;
            case 'ObjetoImp':
                $value = DB::table('sat_object_tax')->select('description')->where('key_code', $clave)->first();
                break;
            case 'UsoCFDI':
                $value = DB::table('sat_uso_cfdis')->select('description')->where('key_code', $clave)->first();
                break;
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

    static function generateQR($comprobante){
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

    public static function generatePDF($xml, $logo, $mdocument){
        $atributos_concepto = $mdocument->conceptos;
        $formatterES = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

        $cfdi = \CfdiUtils\Cfdi::newFromString($xml);
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

        $QR = CfdiUtils::generateQR($comprobante);

        /*Atributos (Serie, Folio, Fecha, LugarExpedicion, TipoDeComprobante, Moneda, FormaPago, TipoCambio,
             MetodoPago, SubTotal, Total, NoCertificado) del nodo Comprobante */        
        if($comprobante != null){
            $serie = $comprobante['Serie'];
            $Folio = $comprobante['Folio'];
            $Fecha = $comprobante['Fecha'];
            $LugarExpedicion = $comprobante['LugarExpedicion'];
            $TipoDeComprobante = $comprobante['TipoDeComprobante'];
            $Moneda = $comprobante['Moneda'];
            $VersionComprobante = $comprobante['Version'];
            $Exportacion = $comprobante['Exportacion'].CfdiUtils::claveDescription($comprobante['Exportacion'], 'Exportacion');
            $NoCertificado = $comprobante['NoCertificado'];
            $FormaPago = $comprobante['FormaPago'].CfdiUtils::claveDescription($comprobante['FormaPago'], 'FormaPago');
            $TipoCambio = number_format($comprobante['TipoCambio'],4,'.',',');
            $MetodoPago = $comprobante['MetodoPago'].CfdiUtils::claveDescription($comprobante['MetodoPago'], 'MetodoPago');
            $SubTotal = $comprobante['SubTotal'];
            $Descuento = $comprobante['Descuento'];
            $Total = $comprobante['Total'];
            $NoCertificado = $comprobante['NoCertificado'];
        } else {
            $serie = null;
            $Folio = null;
            $Fecha = null;
            $LugarExpedicion = null;
            $TipoDeComprobante = null;
            $Moneda = null;
            $Version = null;
            $Exportacion = null;
            $NoCertificado = null;
            $FormaPago = null;
            $TipoCambio = null;
            $MetodoPago = null;
            $SubTotal = null;
            $Descuento = null;
            $Total = null;
            $NoCertificado = null;
        }

        /*Atributos (Rfc, Nombre, RegimenFiscal) del nodo Emisor */
        if($Emisor != null){
            $Rfc_E = $Emisor['Rfc'];
            $Nombre_E = $Emisor['Nombre'];
            $RegimenFiscal_E = $Emisor['RegimenFiscal'].CfdiUtils::claveDescription($Emisor['RegimenFiscal'], 'RegimenFiscal');
            $Clave_proveedor = isset($mdocument->emisor->oCustomAttributes->provider) ? $mdocument->emisor->oCustomAttributes->provider : null;
        }else{
            $Rfc_E = null;
            $Nombre_E = null;
            $RegimenFiscal_E = null;
        }

        /*Atributos (Rfc, Nombre, RegimenFiscal) del nodo Receptor*/        
        if($Receptor != null){
            $Rfc_R = $Receptor['Rfc'];
            $Nombre_R = $Receptor['Nombre'];
            $RegimenFiscal_R = $Receptor['RegimenFiscalReceptor'].CfdiUtils::claveDescription($Receptor['RegimenFiscalReceptor'], 'RegimenFiscal');
            $DomicilioFiscalReceptor = $Receptor['DomicilioFiscalReceptor'];
            $UsoCFDI = $Receptor['UsoCFDI'];
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
        $FechaTimbrado = '';
        if($tfd != null){
            $UUID_tfd = $tfd['UUID'];
            $SelloSAT = $tfd['SelloSAT'];
            $SelloCFD = $tfd['SelloCFD'];
            $FechaTimbrado = $tfd['FechaTimbrado'];
            $RFC_PAC = $tfd['RfcProvCertif'];
        } else {
            $UUID_tfd = null;
            $SelloSAT = null;
            $SelloCFD = null;
            $RFC_PAC = null;
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

            $ConfVehiDescription = VehicleConfig::where('key_code', $ConfigVehicular)->value('description');

            $PlacaVM = $IdentificacionVehicular['PlacaVM'];
            $AnioModeloVM = $IdentificacionVehicular['AnioModeloVM'];
        }

        /*Nodos hijos (Remolque) del nodo Remolques*/        
        $Remolques != null ? $Remolque = $Remolques->searchNodes('cartaporte20:Remolque') : $Remolque = null;

        /*Atributos (Placa, SubTipoRem) */        
        $Placa_Remolque = '';
        $SubTipoRem = '';
        $DescRem = '';
        if(!is_null($Remolque)){
            foreach($Remolque as $r){
                $Placa_Remolque = $Placa_Remolque.$r['Placa'];
                $SubTipoRem = $SubTipoRem.$r['SubTipoRem'];
                $DescRem = \DB::table('sat_trailer_subtypes')->where('key_code', $SubTipoRem)->value('description');
            }
        }
        /*Nodos hijos (TiposFigura) del nodo FiguraTransporte */        
        $FiguraTransporte != null ? $TiposFigura = $FiguraTransporte->searchNodes('cartaporte20:TiposFigura') : $TiposFigura = null;

        /*Creación de la tabla partidas */
        $tabla_partidas = '';
        $index_concepto = 0;
        foreach($Concepto as $c){
            null == $c->searchNode('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado') ? $Base = null : $Base = $c->searchNode('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado')['Base'];
            $Traslados = $c->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
            $Retenciones = $c->searchNodes('cfdi:Impuestos', 'cfdi:Retenciones', 'cfdi:Retencion');

            /*Tabla interna para los impuestos de traslado*/
            $tabla_impuestosT_concepto = "";
            foreach($Traslados as $t){
                $tipoImpuesto = CfdiUtils::claveDescription($t['Impuesto'], 'impuestos');
                $tabla_impuestosT_concepto = $tabla_impuestosT_concepto.
                    '
                    <tr>
                        <td class = "text-c" style="font-size: 2.7mm">Traslados</td>
                        <td class = "text-c" style="font-size: 2.7mm">'.$t['Impuesto'].$tipoImpuesto.'</td>
                        <td class = "text-r" style="font-size: 2.7mm">'.number_format($t['Base'], (int) strpos(strrev($t['Base']), "."), '.', ',').'</td>
                        <td class = "text-c" style="font-size: 2.7mm">'.$t['TipoFactor'].'</td>
                        <td class = "text-r" style="font-size: 2.7mm">'.$t['TasaOCuota'].'</td>
                        <td class = "text-r" style="font-size: 2.7mm">'.number_format($t['Importe'], (int) strpos(strrev($t['Importe']), "."), '.', ',').'</td>
                    </tr>
                    ';
            }

            /*Tabla interna para los impuestos de retenciones*/
            $tabla_impuestosR_concepto = "";
            foreach($Retenciones as $r){
                $tipoImpuesto = CfdiUtils::claveDescription($r['Impuesto'], 'impuestos');
                $tabla_impuestosR_concepto = $tabla_impuestosR_concepto.
                    '
                    <tr>
                        <td class = "text-c" style="font-size: 2.7mm">Retenciones</td>
                        <td class = "text-c" style="font-size: 2.7mm">'.$r['Impuesto'].$tipoImpuesto.'</td>
                        <td class = "text-r" style="font-size: 2.7mm">'.number_format($r['Base'], (int) strpos(strrev($r['Base']), "."), '.', ',').'</td>
                        <td class = "text-c" style="font-size: 2.7mm">'.$r['TipoFactor'].'</td>
                        <td class = "text-r" style="font-size: 2.7mm">'.$r['TasaOCuota'].'</td>
                        <td class = "text-r" style="font-size: 2.7mm">'.number_format($r['Importe'], (int) strpos(strrev($r['Importe']), "."), '.', ',').'</td>
                    </tr>
                    ';
            }

            $UnitDescription = Units::where('key_code', $c['ClaveUnidad'])->value('description');
            $tabla_atributos_concepto = '';
            if(isset($atributos_concepto[$index_concepto])){
                if(!is_null($atributos_concepto[$index_concepto]['oCustomAttributes']->shippingOrders)){
                    if(strlen($atributos_concepto[$index_concepto]['oCustomAttributes']->shippingOrders) != 0){
                        $tabla_atributos_concepto = '
                            <tr>
                                <td colspan = "7">
                                    <table style = "width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td class = "td2" style="width: 15%;"><b>Orden embarque:</b></td>
                                                <td class = "td2" style="width: 15%;">'.$atributos_concepto[$index_concepto]['oCustomAttributes']->shippingOrders.'</td>
                                                <td class = "td2" style="width: 10%;"><b>Destino:</b></td>
                                                <td class = "td2" style="width: 10%;">'.$atributos_concepto[$index_concepto]['oCustomAttributes']->destinyName.'</td>
                                                <td class = "td2" style="width: 10%;"><b>Cliente:</b></td>
                                                <td class = "td2" style="width: 30%;">'.$atributos_concepto[$index_concepto]['oCustomAttributes']->customerName.'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        ';
                    }
                }
            }

            $ObjImpDesc = CfdiUtils::claveDescription($c['ObjetoImp'], 'ObjetoImp');

            $tabla_partidas = $tabla_partidas.
                            '
                            <tr>
                                <td colspan = "3" class="th2">ClaveProdServ</td>
                                <td colspan = "3" class="th2">Descripción</td>
                            </tr>
                            <tr>
                                <td colspan = "3" class="td1 text-c">'.$c['ClaveProdServ'].'</td>
                                <td colspan = "3" class="td1 text-c">'.$c['Descripcion'].'</td>
                            </tr>
                            <tr>
                                <td class="th2">Cantidad</td>
                                <td class="th2">Clave Unidad</td>
                                <td class="th2">Valor Unitario</td>
                                <td class="th2">Importe</td>'.

                                (!is_null($c['Descuento']) && (int)$c['Descuento']  > 0.00 ? '<td class="th2">Descuento</td>' : '')

                                .'<td class="th2" '.(!is_null($c['Descuento']) && (int)$c['Descuento']  > 0.00 ? '' : 'colspan = "2"').'>Objecto impuesto</td>
                            </tr>
                            <tr>
                                <td class="td1 text-r">'.$c['Cantidad'].'</td> 
                                <td class="td1 text-c">'.$c['ClaveUnidad'].' - '.$UnitDescription.'</td>
                                <td class="td1 text-r">'.number_format($c['ValorUnitario'], (int) strpos(strrev($c['ValorUnitario']), "."), '.', ',').'</td> 
                                <td class="td1 text-r">'.number_format($c['Importe'], (int) strpos(strrev($c['Importe']), "."), '.', ',').'</td>'.

                                (!is_null($c['Descuento']) && (int)$c['Descuento']  > 0.00 ? '<td class="td1 text-r">'.number_format($c['Descuento'], (int) strpos(strrev($c['Descuento']), "."), '.', ',').'</td>' : '')

                                .'<td class="td1 text-c"'.(!is_null($c['Descuento']) && (int)$c['Descuento']  > 0.00 ? '' : 'colspan = "2"').'>'.$c['ObjetoImp'].$ObjImpDesc.'</td>
                            </tr>
                            <tr>
                                <td colspan = "6">
                                    <table style = "width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td class="text-c" style = "font-size: 2.7mm;"><b>Impuestos:</b></td>
                                                <td class="text-c" style = "font-size: 2.7mm;"><b>Impuesto:</b></td>
                                                <td class="text-c" style = "font-size: 2.7mm;"><b>Base:</b></td>
                                                <td class="text-c" style = "font-size: 2.7mm;"><b>Tipo Factor:</b></td>
                                                <td class="text-c" style = "font-size: 2.7mm;"><b>Tasa o cuota:</b></td>
                                                <td class="text-c" style = "font-size: 2.7mm;"><b>Importe:</b></td>
                                            </tr>
                                            '.$tabla_impuestosT_concepto.'
                                            '.$tabla_impuestosR_concepto.'
                                            '.$tabla_atributos_concepto.'
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            ';
            $index_concepto = $index_concepto + 1;
        }

        /*Creación de la tabla Mercania */        
        $tabla_Mercancia='';
        foreach($Mercancia as $m){
            $UnitMercDescription = Units::where('key_code', $m['ClaveUnidad'])->value('description');

            $CantidadTransporta = $m->searchNodes('cartaporte20:CantidadTransporta');
            $content_tabla_cantidadTransporta = '';
            foreach($CantidadTransporta as $ct){
                $content_tabla_cantidadTransporta = $content_tabla_cantidadTransporta.'
                                <tr>
                                    <td class="text-r">'.$ct['Cantidad'].'</td>
                                    <td class="text-r">'.$ct['IDOrigen'].'</td>
                                    <td class="text-r">'.$ct['IDDestino'].'</td>
                                </tr>                                    
                ';    
            }

            $tabla_cantidadTransporta = '
                                    <tr>
                                        <td colspan = "8">
                                            <table style = "width: 100%;">
                                                <tbody>
                                                    <tr>
                                                        <td class = "th3">Cantidad:</td>
                                                        <td class = "th3">ID origen:</td>
                                                        <td class = "th3">ID destino:</td>
                                                    </tr>
                                                    '.$content_tabla_cantidadTransporta.'
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
        ';

            $tabla_Mercancia = $tabla_Mercancia.'
                            <tr>
                                <td class="td1 text-c">'.$m['BienesTransp'].'</td>
                                <td class="td1 text-c">'.$m['Descripcion'].'</td>
                                <td class="td1 text-c">'.$m['Cantidad'].'</td>
                                <td class="td1 text-c">'.$m['ClaveUnidad'].' - '.$UnitMercDescription.'</td>
                                <td class="td1 text-c">'.$m['MaterialPeligroso'].'</td>
                                <td class="td1 text-c">'.$m['PesoEnKg'].'</td>
                                <td class="td1 text-r">'.number_format($m['ValorMercancia'], (int) strpos(strrev($m['ValorMercancia']), "."), '.', ',').'</td>
                                <td class="td1 text-c">'.$m['Moneda'].'</td>
                            </tr>'.

                            ($content_tabla_cantidadTransporta != '' ? $tabla_cantidadTransporta : '')

                            .'                           
            ';
        }

        /*Creación de la tabla operador */
        $tabla_Operador = '';
        foreach($TiposFigura as $tf){
            $tabla_Operador = '
                        <tr>
                            <td class="td1 text-c">'.$tf['RFCFigura'].'</td>
                            <td class="td1 text-c">'.$tf['NumLicencia'].'</td>
                            <td class="td1 text-c">'.$tf['NombreFigura'].'</td>
                        </tr>
            ';
        }

        /*Creación de la tabla Remitente/Destinatario de la carta porte */
        $tabla_Remitente_Destinatario = '';
        foreach($Ubicacion as $u){
            $IDUbicacion_r = null;
            $CP_Rfc_r = null;
            $CP_Nombre_r = null;
            $CP_FechaHoraSalidaLlegada_r = null;
            $IDUbicacion_d = null;
            $CP_Rfc_d = null;
            $CP_Nombre_d = null;
            $CP_FechaHoraSalidaLlegada_d = null;

            if($u['TipoUbicacion'] == "Origen"){
                $IDUbicacion_r = $u['IDUbicacion'];
                $CP_Rfc_r = $u['RFCRemitenteDestinatario'];
                $CP_Nombre_r = $u['NombreRemitenteDestinatario'];
                $CP_FechaHoraSalidaLlegada_r = $u['FechaHoraSalidaLlegada'];
            } else {
                $IDUbicacion_d = $u['IDUbicacion'];
                $CP_Rfc_d = $u['RFCRemitenteDestinatario'];
                $CP_Nombre_d = $u['NombreRemitenteDestinatario'];
                $CP_FechaHoraSalidaLlegada_d = $u['FechaHoraSalidaLlegada'];
            }
            $tabla_Remitente_Destinatario = $tabla_Remitente_Destinatario.
                                            '
                                            <tr>
                                                <td class="td1 text-c">'.$IDUbicacion_r.'</td>
                                                <td class="td1 text-c">'.$CP_Rfc_r.'</td>
                                                <td class="td1 text-c">'.$CP_Nombre_r.'</td>
                                                <td class="td1 text-c">'.$CP_FechaHoraSalidaLlegada_r.'</td>
                                                <td class="td1 text-c">'.$IDUbicacion_d.'</td>
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
                    $colonia_name = \DB::table('sat_suburb')
                                    ->where([
                                        ['zip_code',$Domicilio['CodigoPostal']],
                                        ['key_code',$Domicilio['Colonia']]
                                    ])
                                    ->value('suburb_name');
                    $state_id = States::where('key_code', $Domicilio['Estado'])->value('id');
                    $mun_name = Municipalities::where([
                                                    ['state_id', $state_id],
                                                    ['key_code', $Domicilio['Municipio']],
                                                ])
                                                ->value('municipality_name');

                    $tabla_ubicacion_origen = $tabla_ubicacion_origen.
                                            '
                                            <tr>
                                                <td class="td1 text-c">'.$Domicilio['Calle'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroExterior'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroInterior'].'</td>
                                                <td class="td1 text-c">'.$colonia_name.'</td>
                                                <td class="td1 text-c">'.$Domicilio['Localidad'].'</td>
                                                <td class="td1 text-c">'.$mun_name.'</td>
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
                    $colonia_name = \DB::table('sat_suburb')
                                    ->where([
                                        ['zip_code',$Domicilio['CodigoPostal']],
                                        ['key_code',$Domicilio['Colonia']]
                                    ])
                                    ->value('suburb_name');
                    $state_id = States::where('key_code', $Domicilio['Estado'])->value('id');
                    $mun_name = Municipalities::where([
                                                    ['state_id', $state_id],
                                                    ['key_code', $Domicilio['Municipio']],
                                                ])
                                                ->value('municipality_name');
                    $tabla_ubicacion_destino = $tabla_ubicacion_destino.
                                            '
                                            <tr>
                                                <td class="td1 text-c">'.$Domicilio['Calle'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroExterior'].'</td>
                                                <td class="td1 text-c">'.$Domicilio['NumeroInterior'].'</td>
                                                <td class="td1 text-c">'.$colonia_name.'</td>
                                                <td class="td1 text-c">'.$Domicilio['Localidad'].'</td>
                                                <td class="td1 text-c">'.$mun_name.'</td>
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
                        <td class = "border" style = "width: 15%; text-align: center">'.
                            // (!is_null($logo_name) ? '<img src="./logos/'.$logo_name.'" style="max-width: 2cm; max-height: 1.5cm">' : '')
                            (!is_null($logo) ? '<img src="data:image/'.$logo->extension.';base64,'.$logo->image_64.'" style="max-width: 2cm; max-height: 1.5cm">' : '')
                        .'</td>
                        <td colspan="3" class = "border" style = "width: 54%;">
                            <table style = "width: 100%;">
                                <tbody>
                                    <tr>
                                        <td colspan = "2" class = "text-l"><b>Emisor: </b><span>'.$Nombre_E.'</span></td>
                                    </tr>
                                    <tr>
                                        <td class = "text-l"><b>RFC emisor:</b><span>'.$Rfc_E.'</span></td>
                                        <td class = "text-r">'.(!is_null($Clave_proveedor) ? '<b>Clave proveedor:</b><span>'.$Clave_proveedor.'</span>' : '').'</td>
                                    </tr>
                                    <tr>
                                        <td colspan = "2" class = "text-l"><b>Régimen fiscal emisor:</b><span>'.$RegimenFiscal_E.'</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class = "border text-c" style = "width: 30%;">
                            <p style = "font-size: 3mm; margin:0px; outline:none;">FACTURA</p>
                            '.
                            
                            (!is_null($serie) && $serie != '' ? '<p style = "font-size: 3mm; margin:0px; outline:none;">
                                                    <b style = "font-size: 3mm;">Serie:</b>
                                                    '.
                                                    $serie
                                                    .'&nbsp;<b style = "font-size: 3mm">Folio:</b>
                                                    '.
                                                    $Folio
                                                    .'
                                                </p>'
                            : '')

                            .'
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "4" class = "border" style = "width: 76.3%; text-align: left">
                            <table style = "width: 100%;">
                                <tbody>
                                    <tr>
                                        <td class="text-l"><b>Receptor: </b>'.$Nombre_R.'</td>   
                                    </tr>   
                                    <tr>
                                        <td class="text-l"><b>RFC receptor: </b>'.$Rfc_R.'</td>   
                                    </tr>   
                                    <tr>
                                        <td class="text-l"><b>Régimen fiscal receptor: </b>'.$RegimenFiscal_R.'</td>   
                                    </tr>   
                                    <tr>
                                        <td class="text-l"><b>Código postal receptor: </b>'.$DomicilioFiscalReceptor.'</td>   
                                    </tr>   
                                </tbody>   
                            </table>
                        </td>
                        <td class = "border text-c" style = "width: 23%;">
                            <b>Tipo comprobante:</b>
                            <p>'.$TipoDeComprobante.'</p>
                            <b>Lugar y fecha expedición:</b>
                            <p>'.$LugarExpedicion.'</p>
                            <p style="margin-top: 0; font-size: 3mm">
                                '.$Fecha.'
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class = "border text-c">
                            <b>Versión:</b>
                            <p>'.$VersionComprobante.'</p>
                        </td>
                        <td class = "border text-c">
                            <b>Exportación:</b>
                            <p>'.$Exportacion.'</p>
                        </td>
                        <td class = "border text-c">
                            <b>No. Certificado:</b>
                            <p>'.$NoCertificado.'</p>
                        </td>
                        <td class = "border text-c">
                            <b>Moneda:</b>
                            <p>'.$Moneda.'</p>
                        </td>
                        <td class = "border text-c">
                            <b>Tipo de cambio:</b>
                            <p>'.$TipoCambio.'</p>
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
                            COMPROBANTE FISCAL GENERADO CON CPT 1.0 SOFTWARE APLICADO S.A. DE C.V. &nbsp;
                            Tels:&nbsp;(443) 204-1146 y 47 &nbsp; www.swaplicado.com.mx
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
                    '.$tabla_partidas.'
                </tbody>
            </table>

            <table style = "width: 100%;">
                <tbody>
                    <tr>
                        <td style = "border-top: solid 0.03cm black;"><b>Forma pago:</b></td>
                        <td style = "border-top: solid 0.03cm black;"><b>Método pago:</b></td>
                        <td style = "border-top: solid 0.03cm black;"><b>Uso CFDI:</b></td>
                        <td rowspan = "4" style = "border-bottom: 0.03cm solid black; border-top: solid 0.03cm black;">
                            <table style = "width: 100%;">
                                <tbody>
                                    <tr>
                                        <td class = "text-r"><b>Subtotal:</b></td>
                                        <td class = "text-r">'.number_format($SubTotal, (int) strpos(strrev($SubTotal), "."), '.', ',').' '.$Moneda.'</td>
                                    </tr>
                                    <tr>'.
                                        
                                        (!is_null($Descuento) && (int)$Descuento  > 0.00 ? 
                                        '<td class = "text-r"><b>Descuento:</b></td>
                                        <td class = "text-r">'.(!is_null($Descuento) ? number_format($Descuento, (int) strpos(strrev($Descuento), "."), '.', ',') : '0.00').' '.$Moneda.'</td>'
                                        : '')
                                    
                                    .'</tr>
                                    <tr>
                                        <td class = "text-r"><b>Total impuestos trasladados:</b></td>
                                        <td class = "text-r">'.number_format($TotalImpuestosTrasladados, (int) strpos(strrev($TotalImpuestosTrasladados), "."), '.', ',').' '.$Moneda.'</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-r"><b>Total impuestos retenidos:</b></td>
                                        <td class = "text-r">'.number_format($TotalImpuestosRetenidos, (int) strpos(strrev($TotalImpuestosRetenidos), "."), '.', ',').' '.$Moneda.'</td>
                                    </tr>
                                    <tr>
                                        <td class = "text-r"><b>Total:</b></td>
                                        <td class = "text-r">'.number_format($Total, (int) strpos(strrev($Total), "."), '.', ',').' '.$Moneda.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style = "">'.$FormaPago.'</td>
                        <td style = "">'.$MetodoPago.'</td>
                        <td style = "">'.$UsoCFDI.CfdiUtils::claveDescription($UsoCFDI, 'UsoCFDI').'</td>
                    </tr>
                    <tr>
                        <td colspan = "3" style = ""><b>Importe total con letra:</b></td>
                    </tr>
                    <tr>
                        <td colspan = "3" style = "border-bottom: 0.03cm solid black;">'.ucfirst($formatterES->format($Total)).'&nbsp;'.CfdiUtils::getCoinKey($Moneda).
                        '&nbsp;'.substr(strrchr($Total, "."), 1).'/100'.'&nbsp;'.$Moneda.'</td>
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
                            Fecha certificación:
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
                                        <td style = "width: 17%; font-size: 2.5mm; font-weight: bold;">RFC certificación:</td>
                                        <td style = "font-size: 2.5mm">'.$RFC_PAC.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan = "4" style = "font-size: 2.5mm">
                         </td>
                    </tr>
                    <tr>
                        <td colspan = "4" class = "text-r" style = "font-size: 2.5mm"></td>
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
                        <td class="th2">Total distancia recorrida (km)</td>
                        <td class="th2">Peso total mercancía</td>
                        <td class="th2">No. total mercancías</td>
                    </tr>
                    <tr>
                        <td class="td1 text-c">'.$Version.'</td>
                        <td class="td1 text-c">'.$TranspInternac.'</td>
                        <td class="td1 text-c">'.$TotalDistRec.'</td>
                        <td class="td1 text-c">'.$PesoBrutoTotal.' '.$UnidadPeso.'</td>
                        <td class="td1 text-c">'.$NumTotalMercancias.'</td>
                    </tr>    
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <th colspan="4" class="th1 border">Remitente:</th>
                        <th colspan="4" class="th1 border">Destinatario:</th>
                    </tr>
                    <tr>
                        <td class="th2">ID ubicación</td>
                        <td class="th2">RFC remitente</td>
                        <td class="th2">Nombre remitente</td>
                        <td class="th2">Fecha y hora salida</td>
                        <td class="th2">ID ubicación</td>
                        <td class="th2">RFC destinatario</td>
                        <td class="th2">Nombre destinatario</td>
                        <td class="th2">Fecha y hora llegada</td>
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
                        <td class="th2">Distancia recorrida (km)</td>
                    </tr>
                    '.$tabla_ubicacion_destino.' 
                </tbody>
            </table>

            <table style = "width: 100%">
                <tbody>
                    <tr>
                        <th colspan="8" class="th1">Mercancías:</th>
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
                        </tr>
                        <tr>
                            <td class="th2">Permiso SCT</td>
                            <td class="th2">No. permiso SCT</td>
                            <td class="th2">Aseguradora</td>
                            <td class="th2">Póliza</td>
                            <td class="th2">Configuración vehicular</td>
                            <td class="th2">Placa vehicular</td>
                            <td class="th2">Año modelo</td>
                        </tr>
                        <tr>
                            <td class="td1 text-c">'.$PermSCT.'</td>
                            <td class="td1 text-c">'.$NumPermisoSCT.'</td>
                            <td class="td1 text-c">'.$Aseguradoras.'</td>
                            <td class="td1 text-c">'.$polizas.'</td>
                            <td class="td1 text-c">'.$ConfigVehicular.' - '.$ConfVehiDescription.'</td>
                            <td class="td1 text-c">'.$PlacaVM.'</td>
                            <td class="td1 text-c">'.$AnioModeloVM.'</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="width: 100%;">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <th colspan="2" class="th1">Remolque:</th>
                        </tr>
                        <tr>
                            <td class="th2">Subtipo remolque</td>
                            <td class="th2">Placa remolque</td>
                        </tr>
                        <tr>
                            <td class="td1 text-c">'.$SubTipoRem.' - '.$DescRem.'</td>
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
                            <td class="th2">RFC operador</td>
                            <td class="th2">No. licencia</td>
                            <td class="th2">Nombre operador</td>
                        </tr>
                        '.$tabla_Operador.'
                    </tbody>
                </table>
            </div>
        ';

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => __DIR__ . '/../../tmp',
            'mode' => 'c',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 60,
            'margin_bottom' => 30,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;
        $mpdf->use_kwt = true;

        $stylesheet = file_get_contents('./css/mpdf/mpdfMycss.css');

        if($SelloSAT == null){
            $mpdf->SetWatermarkText('Sin Timbrar', 0.3);
            $mpdf->showWatermarkText = true;
        }
        
        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLfooter($footer);
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html,2);
        // $mpdf->Output('filename.pdf', \Mpdf\Output\Destination::FILE);
        // return $mpdf->Output('', 'S');
        $base64 = base64_encode($mpdf->Output('', 'S'));
        return $base64;
    }
}