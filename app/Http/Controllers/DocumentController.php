<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Document;
use App\Models\DocumentStamp;
use App\Models\M\MDocument;
use App\Models\M\MSignLog;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\SXml\XmlGeneration;
use App\Core\RequestCore;
use App\Core\FinkokCore;
use App\Utils\CfdiUtils;
use App\Utils\SFormats;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendXmlPdf;
use App\Utils\MailUtils;

class DocumentController extends Controller
{
    public function index(Request $request, $viewType = 0)
    {
        
        if(!is_null($request->calendarStart)){
            $start = getDate(strtotime($request->calendarStart));
        }else{
            $start = getDate(strtotime(date('Y-m-01')));
        }
        if(!is_null($request->calendarEnd)){
            $end = getDate(strtotime($request->calendarEnd));
        }else{
            $end = getDate(strtotime(date('Y-m-t')));
        }

        $lDocuments = \DB::table('f_documents')
                        ->join('f_carriers', 'f_carriers.id_carrier', '=', 'f_documents.carrier_id')
                        ->join('users', 'users.id', '=', 'f_documents.usr_gen_id')
                        ->whereDate('requested_at', '>=', $start['year'].'-'.$start['mon'].'-'.$start['mday'])
                        ->whereDate('requested_at', '<=', $end['year'].'-'.$end['mon'].'-'.$end['mday']);

        $withCarrierFilter = false;
        $carriers = [];
        
        if (auth()->user()->isCarrier()) {
            $lDocuments = $lDocuments->where('f_documents.carrier_id', auth()->user()->carrier()->first()->id_carrier);
        }
        else if (auth()->user()->isDriver()) {
            $lDocuments = $lDocuments->where('f_documents.carrier_id', auth()->user()->driver()->first()->carrier_id);
        }
        else {
            $carriers = Carrier::where('is_deleted', false)
                                ->orderBy('fiscal_id', 'ASC')
                                ->orderBy('fullname', 'ASC')
                                ->select('id_carrier', 'fiscal_id', 'fullname')
                                ->get();
    
            $withCarrierFilter = true;
        }

        $title = "";
        $ic = 0;
        $withDateFilter = false;

        if ($request->has('ic') && !is_null($request->ic)) {
            $lDocuments = $lDocuments->where('f_documents.carrier_id', $request->ic);
            $ic = $request->ic;
        }

        switch ($viewType) {
            case "0":
                $lDocuments = $lDocuments->get();
                $title = "todas";
                $withDateFilter = true;
                break;

            // Por procesar
            case "1":
                $lDocuments = $lDocuments->where('is_processed', false)
                                            ->get();
                $title = "por procesar";
                break;

            // Por timbrar
            case "2":
                $lDocuments = $lDocuments->where('is_processed', true)
                                            ->where('is_signed', false)
                                            ->get();
                $title = "por timbrar";
                break;

            // Timbrados
            case "3":
                $lDocuments = $lDocuments->where('is_processed', true)
                                            ->where('is_signed', true)
                                            ->get();
                $title = "timbradas";
                $withDateFilter = true;
                break;
            
            default:
                $lDocuments = $lDocuments->get();
                $title = "todas";
                break;
        }

        return view('ship.documents.index', [
            'lDocuments' => $lDocuments,
            'viewType' => $viewType,
            'title' => $title,
            'carriers' => $carriers,
            'withCarrierFilter' => $withCarrierFilter,
            'withDateFilter' => $withDateFilter,
            'ic' => $ic,
            'start' => $start['year'].'-'.$start['mon'].'-'.$start['mday'],
            'end' => $end['year'].'-'.$end['mon'].'-'.$end['mday'],
        ]);
    }

    public function edit($idDocument)
    {
        $oDocument = Document::find($idDocument);

        if(auth()->user()->isAdmin() || auth()->user()->isClient()){
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }

        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);

        if ($oDocument->is_signed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento ya ha sido timbrado, no se puede modificar"]);
        }
        if ($oDocument->is_canceled) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento está cancelado, no se puede modificar"]);
        }

        // Validación de entorno de transportista
        $resValidation = $this->isValidCarrierSpace($oDocument->carrier_id);

        if (! $resValidation['isValid']) {
            return redirect()->back()->with(['icon' => "error", 'message' => $resValidation['message']]);
        }

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

        $oUsos = \DB::table('sat_uso_cfdis AS usos')
                            ->where('key_code', $oObjData->usoCfdi)
                            ->first();

        $usoCfdi = $oUsos->key_code.'-'.$oUsos->description;

        foreach ($lCarrierSeries as $serie) {
            $folio = MDocument::where('carrier_id', $oCarrier->id_carrier)->where('serie', $serie->prefix)->max('folio');
            if ($serie->initial_number > $folio) {
                $serie->folio = $serie->initial_number;
            }
            else {
                $serie->folio = $folio + 1;
            }
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
                    'usoCfdi' => $usoCfdi,
                    'lCurrencies' => $lCurrencies
                ]);
    }

    public function isValidCarrierSpace($idCarrier)
    {
        // Series de folios
        $lCarrierSeries = \DB::table('f_document_series AS ds')
                                        ->where('is_deleted', false)
                                        ->where('carrier_id', $idCarrier)
                                        ->get();

        if (count($lCarrierSeries) == 0) {
            return ['isValid' => false, 'message' => 'No existen series de folios configurados para el transportista, favor de configurarlas.'];
        }

        // Vehiculos
        $lVehicles = \DB::table('f_vehicles AS v')
                            ->where('v.carrier_id', $idCarrier)
                            ->where('v.is_deleted', false)
                            ->get();

        if (count($lVehicles) == 0) {
            return ['isValid' => false, 'message' => 'No existen vehículos configurados para el transportista, favor de configurarlos.'];
        }

        // Figuras
        $lFigures = \DB::table('f_trans_figures AS tf')
                            ->where('tf.carrier_id', $idCarrier)
                            ->where('tf.is_deleted', false)
                            ->get();

        if (count($lFigures) == 0) {
            return ['isValid' => false, 'message' => 'No existen choferes configurados para el transportista, favor de configurarlos.'];
        }

        return ['isValid' => true, 'message' => ''];
    }

    public function update(Request $request, $idDocument)
    {
        $oCfdiData = json_decode($request->the_cfdi_data);

        $oDocument = Document::find($idDocument);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()){
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }
        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);
        $oCarrier = Carrier::find($oDocument->carrier_id);

        if ($oDocument->is_signed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento ya ha sido timbrado, no se puede modificar"]);
        }
        if ($oDocument->is_canceled) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento está cancelado, no se puede modificar"]);
        }

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
        $oMongoDocument->usoCfdi = $oCfdiData->oData->usoCfdi;
        $oEmisor = $oCfdiData->oData->emisor;
        $oEmisor->regimenFiscal = $oCarrier->tax_regime->key_code;
        $oMongoDocument->emisor = $oEmisor;
        
        // Conceptos
        $dSubTotal = 0;
        $dDiscount = 0;
        $dTraslados = 0;
        $aTraslados = [];
        $dRetention = 0;
        $indexConcept = 0;
        $lConcepts = [];
        foreach ($oMongoDocument->conceptos as $aConcept) {
            $oClientConcept = $oCfdiData->oData->conceptos[$indexConcept];
            $aConcept["valorUnitario"] = round($oClientConcept->valorUnitario, 2);
            $aConcept["importe"] = round($aConcept["valorUnitario"] * $aConcept["quantity"], 2);
            $aConcept["discount"] = round($oClientConcept->discount, 2);
            $aConcept["description"] = $oClientConcept->description;
            $aConcept["unidad"] = $oClientConcept->unidad;
            $aConcept["numIdentificacion"] = $oClientConcept->numIndentificacion;
            
            foreach ($aConcept["oImpuestos"]["lTraslados"] as $aTraslado) {
                $aTraslado["tasa"] = round($aTraslado["tasa"], 4);
                $aTraslado["base"] = $aConcept["importe"];
                $aTraslado["importe"] = round($aTraslado["base"] * $aTraslado["tasa"], 2);

                if (array_key_exists(($aTraslado["tasa"].""), $aTraslados)) {
                    $aTraslados[($aTraslado["tasa"]."")] = [
                                                                "base" => ($aTraslados[($aTraslado["tasa"]."")]["base"] + $aTraslado["base"]),
                                                                "importe" => ($aTraslados[($aTraslado["tasa"]."")]["importe"] + $aTraslado["importe"]),
                                                            ];
                }
                else {
                    $aTraslados[($aTraslado["tasa"]."")] = [
                                                                "base" => $aTraslado["base"],
                                                                "importe" => $aTraslado["importe"],
                                                            ];
                }

                $dTraslados += $aTraslado["importe"];
            }

            foreach ($aConcept["oImpuestos"]["lRetenciones"] as $aRetecion) {
                $aRetecion["tasa"] = round($aRetecion["tasa"], 4);
                $aRetecion["base"] = $aConcept["importe"];
                $aRetecion["importe"] = round($aRetecion["base"] * $aRetecion["tasa"], 2);

                $dRetention += $aRetecion["importe"];
            }

            $dSubTotal += $aConcept["importe"];
            $dDiscount += $aConcept["discount"];

            $lConcepts[] = $aConcept;
            $indexConcept++;
        }

        $oMongoDocument->conceptos = $lConcepts;

        // Impuestos
        $oImpuestos = new \stdClass();
        $oImpuestos->totalImpuestosTrasladados = $dTraslados;
        $oImpuestos->totalImpuestosRetenidos = $dRetention;

        $oMongoDocument->subTotal = round($dSubTotal, 2);
        $oMongoDocument->total = round($dSubTotal - $dDiscount + $dTraslados - $dRetention, 2);
        $oMongoDocument->discounts = round($dDiscount, 2);

        $oImpuestos->lTraslados = [];
        foreach ($aTraslados as $key => $value) {
            $oTraslado = new \stdClass();
            $oTraslado->tasa = $key;
            $oTraslado->base = $value["base"];
            $oTraslado->importe = $value["importe"];
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

        // Mercancías
        $pesoBrutoTotal = 0.0;
        foreach ($oMongoDocument->oCartaPorte["mercancia"]["mercancias"] as $key => $value) {
            $oClientMerch = $oCfdiData->oData->oCartaPorte->mercancia->mercancias[$key];
            $value["pesoEnKg"] = $oClientMerch->pesoEnKg;
            $pesoBrutoTotal += $oClientMerch->pesoEnKg;
        }
        $oCP = $oMongoDocument->oCartaPorte;
        $oCP["mercancia"]["pesoBrutoTotal"] = $pesoBrutoTotal;
        $oMongoDocument->oCartaPorte = $oCP;
        
        $sXml = XmlGeneration::generateCartaPorte($oDocument, $oMongoDocument, $oCarrier);
        $oDocument->generated_at = date('Y-m-d H:i:s');
        $oDocument->is_processed = true;

        $oDocument->save();

        $oMongoDocument->xml_cfdi = $sXml;
        $oMongoDocument->save();

        //Generamos el pdf
        $pdf = CfdiUtils::updatePdf($oMongoDocument->_id, $sXml);

        return redirect("documents");
    }

    public function sign($id)
    {
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()){
            abort_unless(CfdiUtils::remisionistaCanStamp($oDocument->carrier_id), 401);
        }
        
        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);

        if (! $oDocument->is_processed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento no ha sido procesado"]);
        }

        if ($oDocument->is_signed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento ya ha sido timbrado"]);
        }

        // timbrar cfdi
        $cfdiResponse = FinkokCore::signCfdi($oMongoDocument->xml_cfdi);

        if (is_array($cfdiResponse) || null === $cfdiResponse) {
            if ($cfdiResponse != null) {
                $log = new MSignLog();
                $log->message = $cfdiResponse[0]["message"];
                $log->idError = $cfdiResponse[0]["error_code"];
                $log->mongoDocumentId = $oMongoDocument->id;
                $log->idDocument = $oDocument->id_document;
                $log->idUser = \Auth::user()->id;
                $log->save();
    
                return redirect()->back()->with(['icon' => "error", 'message' => $log->idError."-".$log->message]);
            }
        }

        $oMongoDocument->xml_cfdi = $cfdiResponse->xml;
        $oMongoDocument->uuid = $cfdiResponse->UUID;
        $oMongoDocument->is_signed = true;
        $oMongoDocument->signed_at = Carbon::parse($cfdiResponse->Fecha)->toDateTimeString();
        $oMongoDocument->save();

        $oDocument->is_signed = true;
        $oDocument->uuid = $cfdiResponse->UUID;
        $oDocument->signed_at = Carbon::parse($cfdiResponse->Fecha)->toDateTimeString();
        $oDocument->usr_sign_id = \Auth::user()->id;
        $oDocument->save();

        // Guardar registro de timbre en la base de datos
        $oDocStamp = new DocumentStamp();
        $oDocStamp->dt_stamp = date('Y-m-d H:i:s');
        $oDocStamp->stamp_type = "timbre";
        $oDocStamp->document_id = $oDocument->id_document;
        $oDocStamp->usr_new_id = \Auth::user()->id;
        $oDocStamp->save();

        // Guardar log de evento de timbrado en MongoDB
        $log = new MSignLog();
        $log->message = $cfdiResponse->CodEstatus;
        $log->satSeal = $cfdiResponse->SatSeal;
        $log->satCert = $cfdiResponse->NoCertificadoSAT;
        $log->mongoDocumentId = $oMongoDocument->id;
        $log->idDocument = $oDocument->id_document;
        $log->idUser = \Auth::user()->id;
        $log->idError = null;
        $log->save();

        // generar pdf
        $pdf = CfdiUtils::updatePdf($oMongoDocument->_id, $oMongoDocument->xml_cfdi);
        // enviar correo
        $mails = MailUtils::getMails();
        $comercial_name = MailUtils::getComercialName();
        foreach($mails as $m){
            Mail::to($m)->send(new SendXmlPdf($oMongoDocument->xml_cfdi, $pdf, $comercial_name, $oMongoDocument->folio, $oMongoDocument->serie));
        }

        return redirect("documents")->with(['message' => "El documento ha sido timbrado exitosamente", 'icon' => "success"]);
    }

    public function cancel(Request $request)
    {
        $id = $request->id;
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()){
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }
        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);

        if (! $oDocument->is_processed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento no ha sido procesado"]);
        }

        if (! $oDocument->is_signed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento no ha sido timbrado"]);
        }

        $oCarrier = Carrier::find($oDocument->carrier_id);

        // cancelar cfdi
        $cfdiResponse = FinkokCore::cancelCfdi($oMongoDocument, $oCarrier);
    }
}