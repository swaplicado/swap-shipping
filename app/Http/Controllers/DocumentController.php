<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\M\MDocument;
use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SXml\XmlGeneration;
use App\Core\RequestCore;

class DocumentController extends Controller
{
    public function index(Request $request, $viewType = 0)
    {
        $lDocuments = \DB::table('f_documents')
                        ->join('f_carriers', 'f_carriers.id_carrier', '=', 'f_documents.carrier_id')
                        ->join('users', 'users.id', '=', 'f_documents.usr_gen_id');

        $title = "";

        switch ($viewType) {
            case "0":
                $lDocuments = $lDocuments->get();
                $title = "todas";
                break;

            // Pendientes
            case "1":
                $lDocuments = $lDocuments->where('is_processed', true)
                                            ->get();
                $title = "por timbrar";
                break;

            // Timbrados
            case "2":
                $lDocuments = $lDocuments->where('is_signed', true)
                                            ->get();
                $title = "timbradas";
                break;
            
            default:
                # code...
                break;
        }

        return view('ship.documents.index', [
            'lDocuments' => $lDocuments,
            'viewType' => $viewType,
            'title' => $title
        ]);
    }

    public function edit($idDocument)
    {
        $oDocument = Document::find($idDocument);
        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);

        $lCurrenciesQuery = \DB::table('sat_currencies AS cur')
                            ->where('cur.is_active', true);

        if ($oDocument->is_processed) {
            $oObjData = clone $oMongoDocument;
            $oObjData->id = 0;
            $oVehicle = $oObjData->oVehicle;
            $oFigure = $oObjData->oFigure;
        }
        else {
            $oJson = json_decode($oMongoDocument->body_request);

            $lCurs = clone $lCurrenciesQuery;
            $lCurs = $lCurs->selectRaw('cur.*, CONCAT(cur.key_code, " - ", cur.description) AS _description')
                            ->pluck('_description', 'key_code');

            $oObjData = RequestCore::requestToJson($oDocument, $oJson, $lCurs);
            $array = json_decode(json_encode(clone $oObjData), true);
            foreach ($array as $key => $value) {
                $oMongoDocument->$key = $value;
            }
    
            $oMongoDocument->save();
            $oVehicle = new \stdClass();
            $oFigure = new \stdClass();
        }

        $oCarrier = Carrier::find($oDocument->carrier_id);

        $lVehicles = \DB::table('f_vehicles AS v')
                            ->join('sat_veh_cfgs AS vcfg', 'vcfg.id', '=', 'v.veh_cfg_id')
                            ->join('sat_sct_licenses AS slic', 'slic.id', '=', 'v.license_sct_id')
                            ->join('f_insurances AS i', 'v.insurance_id', '=', 'i.id_insurance')
                            ->select(
                                'v.id_vehicle',
                                'v.alias',
                                'v.plates',
                                'v.year_model',
                                'v.license_sct_num',
                                'v.drvr_reg_trib',
                                'v.policy',
                                'v.is_deleted',
                                'v.license_sct_id',
                                'v.veh_cfg_id',
                                'v.carrier_id',
                                'vcfg.key_code AS vcfg_key_code',
                                'vcfg.description AS vcfg_description',
                                'slic.key_code AS slic_key_code',
                                'slic.description AS slic_description',
                                'i.full_name AS insurance_full_name'
                            )
                            ->where('v.carrier_id', $oCarrier->id_carrier)
                            ->where('v.is_deleted', false)
                            ->get();

        $lTrailers = \DB::table('f_trailers AS t')
                            ->join('sat_trailer_subtypes AS ts', 't.trailer_subtype_id', '=', 'ts.id')
                            ->select('t.*', 'ts.description AS trailer_subtype_description', 'ts.key_code AS trailer_subtype_key_code')
                            ->where('t.is_deleted', false)
                            ->where('t.carrier_id', $oCarrier->id_carrier)
                            ->get();

        $lFigures = \DB::table('f_trans_figures AS f')
                            ->join('sat_figure_types AS ft','f.tp_figure_id', '=', 'ft.id')
                            ->join('sat_fiscal_addresses AS fa','f.fis_address_id', '=', 'fa.id')
                            ->select('f.*', 
                                        'ft.description AS figure_type_description', 
                                        'ft.key_code AS figure_type_key_code', 
                                        'fa.description AS fiscal_address_description',
                                        'fa.key_code AS fiscal_address_key_code')
                            ->where('f.carrier_id', $oCarrier->id_carrier)
                            ->where('is_deleted', false)
                            ->get();

        $lPayMethods = \DB::table('sat_payment_methods AS spm')
                            ->selectRaw('spm.*, CONCAT(spm.key_code, " - ", spm.description) AS _description')
                            ->get();

        $lPayForms = \DB::table('sat_payment_forms AS spf')
                            ->selectRaw('spf.*, CONCAT(spf.key_code, " - ", spf.description) AS _description')
                            ->get();

        $lCarrierSeries = \DB::table('f_document_series AS ds')
                            ->where('is_deleted', false)
                            ->where('carrier_id', $oCarrier->id_carrier)
                            ->get();

        foreach ($lCarrierSeries as $serie) {
            $folio = MDocument::where('carrier_id', $oCarrier->id_carrier)->where('serie', $serie->prefix)->max('folio');
            $serie->folio = $folio + 1;
        }

        $lCurrencies = clone $lCurrenciesQuery;
        $lCurrencies = $lCurrencies->selectRaw('cur.*, CONCAT(cur.key_code, " - ", cur.description) AS _description')
                                    ->get();

        $oConfigurations = \App\Utils\Configuration::getConfigurations();
        
        return view('ship.documents.edit', [
                    'oConfigurations' => $oConfigurations,
                    'idDocument' => $oDocument->id_document,
                    'oObjData' => $oObjData,
                    'lVehicles' => $lVehicles,
                    'lTrailers' => $lTrailers,
                    'lFigures' => $lFigures,
                    'oVehicle' => $oVehicle,
                    'oFigure' => $oFigure,
                    'lPayMethods' => $lPayMethods,
                    'lPayForms' => $lPayForms,
                    'lCarrierSeries' => $lCarrierSeries,
                    'lCurrencies' => $lCurrencies
                ]);
    }

    public function generateXml($idDocument)
    {
        XmlGeneration::generateCarta();
        $originalString = XmlGeneration::createOriginalString();
    }

    public function update(Request $request, $idDocument)
    {
        $oCfdiData = json_decode($request->the_cfdi_data);

        $oDocument = Document::find($idDocument);
        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);
        $oCarrier = Carrier::find($oDocument->carrier_id);

        /**
         * Procesamiento de modificaciones en el documento
         */

        // Encabezado
        if ($oDocument->is_processed) {

        }
        else {
            $oMongoDocument->serie = $oCfdiData->oData->serie;
            $oMongoDocument->folio = $oCfdiData->oData->folio;

            $oDocument->serie = $oCfdiData->oData->serie;
            $oDocument->folio = $oCfdiData->oData->folio;
        }

        $oMongoDocument->formaPago = $oCfdiData->oData->formaPago;
        $oMongoDocument->metodoPago = $oCfdiData->oData->metodoPago;
        $oMongoDocument->currency = $oCfdiData->oData->currency;
        $oMongoDocument->tipoCambio = $oCfdiData->oData->tipoCambio;
        
        // Conceptos
        $dSubTotal = 0;
        $dDiscount = 0;
        $dTraslados = 0;
        $aTraslados = [];
        $dRetention = 0;
        $indexConcept = 0;
        foreach ($oMongoDocument->conceptos as $aConcept) {
            $oClientConcept = $oCfdiData->oData->conceptos[$indexConcept];
            $aConcept["valorUnitario"] = $oClientConcept->valorUnitario;
            $aConcept["importe"] = $aConcept["valorUnitario"] * $aConcept["quantity"];
            $aConcept["discount"] = $oClientConcept->discount;
            $aConcept["description"] = $oClientConcept->description;
            $aConcept["unidad"] = $oClientConcept->unidad;
            $aConcept["numIdentificacion"] = $oClientConcept->numIndentificacion;
            
            foreach ($aConcept["oImpuestos"]["lTraslados"] as $aTraslado) {
                $aTraslado["tasa"] = round($aTraslado["tasa"], 4);
                $aTraslado["base"] = $aConcept["importe"];
                $aTraslado["importe"] = $aTraslado["base"] * $aTraslado["tasa"];

                if (array_key_exists(($aTraslado["tasa"].""), $aTraslados)) {
                    $aTraslados[($aTraslado["tasa"]."")] = $aTraslados[($aTraslado["tasa"]."")] + $aTraslado["importe"];
                }
                else {
                    $aTraslados[($aTraslado["tasa"]."")] = $aTraslado["importe"];
                }
                $dTraslados += $aTraslado["importe"];
            }

            foreach ($aConcept["oImpuestos"]["lRetenciones"] as $aRetecion) {
                $aRetecion["tasa"] = round($aRetecion["tasa"], 4);
                $aRetecion["base"] = $aConcept["importe"];
                $aRetecion["importe"] = $aRetecion["base"] * $aRetecion["tasa"];

                $dRetention += $aRetecion["importe"];
            }

            $dSubTotal += $aConcept["importe"];
            $dDiscount += $aConcept["discount"];
        }

        // Impuestos
        $oImpuestos = new \stdClass();
        $oImpuestos->totalImpuestosTrasladados = $dTraslados;
        $oImpuestos->totalImpuestosRetenidos = $dRetention;

        $oImpuestos->lTraslados = [];
        foreach ($aTraslados as $key => $value) {
            $oTraslado = new \stdClass();
            $oTraslado->tasa = $key;
            $oTraslado->importe = $value;
            $oImpuestos->lTraslados[] = $oTraslado;
        }
        
        $oImpuestos->lRetenciones = [];
        $oRetencion = new \stdClass();
        $oRetencion->importe = $dRetention;
        $oRetencion->impuesto = "002";
        $oImpuestos->lRetenciones[] = $oRetencion;

        $oMongoDocument->oImpuestos = json_decode(json_encode($oImpuestos), true);

        // Carta Porte
        $oMongoDocument->oVehicle = json_decode(json_encode($oCfdiData->oVehicle), true);
        $oMongoDocument->oFigure = json_decode(json_encode($oCfdiData->oFigure), true);
        $oMongoDocument->lTrailers = isset($oCfdiData->lTrailers) && count($oCfdiData->lTrailers) > 0 ? json_decode(json_encode($oCfdiData->lTrailers), true) : [];
        
        $sXml = XmlGeneration::generateCartaPorte($oDocument, $oMongoDocument, $oCarrier);
        $oDocument->generated_at = date('Y-m-d H:i:s');
        $oDocument->is_processed = true;

        $oDocument->save();

        $oMongoDocument->xml_cfdi = $sXml;
        $oMongoDocument->save();

        //Generamos el pdf

        return redirect("documents");
    }

    public function sign(Type $var = null)
    {
        // timbrar

        // generar pdf

        // enviar correo
    }
}