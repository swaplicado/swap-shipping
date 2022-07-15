<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Document;
use App\Models\DocumentStamp;
use App\Models\M\MDocument;
use App\Models\M\MSignLog;
use App\Models\M\MRequestLog;
use App\Models\Carrier;
use App\Models\VehicleKey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\SXml\XmlGeneration;
use App\SXml\transformJson;
use App\Core\RequestCore;
use App\Core\FinkokCore;
use App\Utils\CfdiUtils;
use App\Utils\SFormats;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendXmlPdf;
use App\Utils\MailUtils;
use App\Utils\GralUtils;
use App\Core\SATCore;

class DocumentController extends Controller
{
    public function index(Request $request, $viewType = 0)
    {
        // Comienza la declaración de la obtención de los documentos
        $lDocuments = \DB::table('f_documents')
                            ->join('f_carriers', 'f_carriers.id_carrier', '=', 'f_documents.carrier_id')
                            ->join('f_vehicles_keys', 'f_vehicles_keys.id_key', '=', 'f_documents.veh_key_id')
                            ->join('users', 'users.id', '=', 'f_documents.usr_gen_id')
                            ->select('f_documents.*',
                                    'f_carriers.*',
                                    'f_vehicles_keys.key_code AS veh_key_code', 
                                    'f_vehicles_keys.description AS veh_key_description');

        $withCarrierFilter = false;
        $carriers = [];
        // Se obtienen los documentos candidatos para ser referenciados para una cancelación
        $lUuids = \DB::table('f_documents')
                        ->select('uuid', 'serie', 'folio', 'requested_at', 'id_document')
                        ->where('is_signed', true)
                        ->where('is_deleted', false);

        // Se realiza el filtrado de documentos dependiendo el tipo de usuario
        if (auth()->user()->isCarrier()) {
            $lDocuments = $lDocuments->where('f_documents.carrier_id', auth()->user()->carrier()->first()->id_carrier);
            $lUuids = $lUuids->where('carrier_id', auth()->user()->carrier()->first()->id_carrier);
        }
        else if (auth()->user()->isDriver()) {
            $lDocuments = $lDocuments->where('f_documents.carrier_id', auth()->user()->driver()->first()->carrier_id);
            $lUuids = $lUuids->where('carrier_id', auth()->user()->driver()->first()->carrier_id);
        }
        else {
            $carriers = Carrier::where('is_deleted', false)
                                ->orderBy('fiscal_id', 'ASC')
                                ->orderBy('fullname', 'ASC')
                                ->select('id_carrier', 'fiscal_id', 'fullname')
                                ->get();
    
            $withCarrierFilter = true;
        }

        $lUuids = $lUuids->orderBy('requested_at', 'ASC')->get();

        $title = "";
        $ic = 0;
        $withDateFilter = false;
        // se filtra por transportista si así se requiere
        if ($request->has('ic') && !is_null($request->ic)) {
            $lDocuments = $lDocuments->where('f_documents.carrier_id', $request->ic);
            $ic = $request->ic;
        }

        // Se determina el tipo de filtro dependiento del tipo de vista
        switch ($viewType) {
            case "0":
                $title = "todas";
                $withDateFilter = true;
                break;

            // Por procesar
            case "1":
                $lDocuments = $lDocuments->where('is_processed', false)
                                            ->where('f_documents.is_deleted', false)
                                            ->where('f_documents.is_archive', false);
                $title = "por procesar";
                $withDateFilter = false;
                break;

            // Por timbrar
            case "2":
                $lDocuments = $lDocuments->where('is_processed', true)
                                            ->where('is_signed', false)
                                            ->where('f_documents.is_deleted', false)
                                            ->where('f_documents.is_archive', false);
                $title = "por timbrar";
                $withDateFilter = false;
                break;

            // Timbrados
            case "3":
                $lDocuments = $lDocuments->where('is_processed', true)
                                            ->where('is_signed', true)
                                            ->where('f_documents.is_deleted', false)
                                            ->where('f_documents.is_archive', false);
                $title = "timbradas";
                $withDateFilter = true;
                break;
            
                // Archviados
            case "4":
                $lDocuments = $lDocuments->where('f_documents.is_archive', true)
                                            ->where('f_documents.is_signed', false)
                                            ->where('f_documents.is_canceled', false)
                                            ->where('f_documents.is_deleted', false);
                $title = "archivadas";
                $withDateFilter = true;
                break;
            
            default:
                $title = "todas";
                $withDateFilter = true;
                break;
        }

        // si se determinó que llevará filtro de fechas se realiza la consulta
        if ($withDateFilter) {
            if (! is_null($request->calendarStart)) {
                $start = getDate(strtotime($request->calendarStart));
            }
            else {
                $start = getDate(strtotime(date('Y-m-01')));
            }
    
            if (! is_null($request->calendarEnd)) {
                $end = getDate(strtotime($request->calendarEnd));
            }
            else {
                $end = getDate(strtotime(date('Y-m-t')));
            }

            $lDocuments = $lDocuments->whereBetween('requested_at', [$start['year'] . '-' . $start['mon'] . '-' . $start['mday'], 
                                                                    $end['year'] . '-' . $end['mon'] . '-' . $end['mday']]);
        }
        else {
            $start = null;
            $end = null;
        }

        $lDocuments = $lDocuments->get();

        // Se obtienen los montos de los documentos
        $enableTotales = false;
        if (auth()->user()->hasAnyRole(['Admin', 'Carrier', 'user', 'driverT1', 'driverT2'])) {
            if (!is_null($lDocuments) && count($lDocuments) > 0) {
                foreach ($lDocuments as $ld) {
                    $mdocument = MDocument::where('_id', $ld->mongo_document_id)->select('subTotal', 'total',
                        'totalImpuestosTrasladados', 'totalImpuestosRetenidos', 'discounts', 'oCartaPorte')->first();
                    $ld->discounts = SFormats::formatMoney($mdocument->discounts);
                    $ld->totalImpuestosRetenidos = SFormats::formatMoney($mdocument->totalImpuestosRetenidos);
                    $ld->totalImpuestosTrasladados = SFormats::formatMoney($mdocument->totalImpuestosTrasladados);
                    $ld->subTotal = SFormats::formatMoney($mdocument->subTotal);
                    $ld->total = SFormats::formatMoney($mdocument->total);

                    $ld->idLocSrc = "";
                    $ld->idLocDest = "";
                    if (isset($mdocument->oCartaPorte)) {
                        if (count($mdocument->oCartaPorte["ubicaciones"]) > 0) {
                            if (isset($mdocument->oCartaPorte["ubicaciones"][0]["IDUbicacion"])) {
                                $ld->idLocSrc = $mdocument->oCartaPorte["ubicaciones"][0]["IDUbicacion"];
                                if (isset($mdocument->oCartaPorte["ubicaciones"][0]["domicilio"])) {
                                    $ld->srcAddress = (object) $mdocument->oCartaPorte["ubicaciones"][0]["domicilio"]; 
                                }
                            }
                            if (isset($mdocument->oCartaPorte["ubicaciones"][count($mdocument->oCartaPorte["ubicaciones"]) - 1]["IDUbicacion"])) {
                                $ld->idLocDest = $mdocument->oCartaPorte["ubicaciones"][count($mdocument->oCartaPorte["ubicaciones"]) - 1]["IDUbicacion"];
                                if (isset($mdocument->oCartaPorte["ubicaciones"][count($mdocument->oCartaPorte["ubicaciones"]) - 1]["domicilio"])) {
                                    $ld->destAddress = (object) $mdocument->oCartaPorte["ubicaciones"][count($mdocument->oCartaPorte["ubicaciones"]) - 1]["domicilio"]; 
                                }
                            }
                        }
                    }
                }
            }

            $enableTotales = true;
        }

        $lCancelReasons = \DB::table('f_documents_cancel_reasons')->get();
        
        return view('ship.documents.index', [
            'lDocuments' => $lDocuments,
            'viewType' => $viewType,
            'title' => $title,
            'carriers' => $carriers,
            'withCarrierFilter' => $withCarrierFilter,
            'withDateFilter' => $withDateFilter,
            'ic' => $ic,
            'start' => $start != null ? ($start['year'].'-'.$start['mon'].'-'.$start['mday']) : null,
            'end' => $end != null ? ($end['year'].'-'.$end['mon'].'-'.$end['mday']) : null,
            'enableTotales' => $enableTotales,
            'lCancelReasons' => $lCancelReasons,
            'cancelRoute' => 'documents.cancel',
            'lUuids' => $lUuids
        ]);
    }

    public function getArrayFromString($string){

        $parts = [];
        $tok = strtok($string, ", ");
        while ($tok !== false) {
            $parts[] = $tok;
            $tok = strtok(", ");
        }
        return $parts;
    }

    /**
     * Muestra la pantalla de captura del CFDI.
     * Obtiene los datos ya sea del request o del documento almacenado en MongoDB
     *
     * @param integer $idDocument
     * 
     * @return view
     */
    public function edit($idDocument)
    {
        $oDocument = Document::find($idDocument);
        if(auth()->user()->isAdmin()){
            auth()->user()->authorizePermission(['001']);
        }else{
            if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
                abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
            }else{
                auth()->user()->authorizePermission(['113']);
                auth()->user()->carrierAutorization($oDocument->carrier_id);
            }
        }

        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);

        if ($oDocument->is_signed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento ya ha sido timbrado, no se puede modificar"]);
        }
        if ($oDocument->is_canceled) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento está cancelado, no se puede modificar"]);
        }
        if ($oDocument->is_archive) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento está archivado, no se puede modificar"]);
        }

        // Validación de entorno de transportista
        $resValidation = $this->isValidCarrierSpace($oDocument->carrier_id);

        if (! $resValidation['isValid']) {
            return redirect()->back()->with(['icon' => "error", 'message' => $resValidation['message']]);
        }

        $oDocument->update(['is_editing' => true, 'dt_editing' => date('Y-m-d H:i:s')]);

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
                                'v.policy',
                                'v.is_deleted',
                                'v.license_sct_id',
                                'v.veh_cfg_id',
                                'v.veh_key_id',
                                'v.carrier_id',
                                'vcfg.key_code AS vcfg_key_code',
                                'vcfg.description AS vcfg_description',
                                'vcfg.trailer AS vcfg_trailer',
                                'slic.key_code AS slic_key_code',
                                'slic.description AS slic_description',
                                'i.full_name AS insurance_full_name'
                            )
                            ->where('v.carrier_id', $oCarrier->id_carrier)
                            ->where('v.is_deleted', false);

        $lCurrenciesQuery = \DB::table('sat_currencies AS cur')
                            ->where('cur.is_active', true);

        // Obtiene los remolques que tiene dados de alta el transportista
        $lTrailers = \DB::table('f_trailers AS t')
                            ->join('sat_trailer_subtypes AS ts', 't.trailer_subtype_id', '=', 'ts.id')
                            ->select('t.*', 'ts.description AS trailer_subtype_description', 'ts.key_code AS trailer_subtype_key_code')
                            ->where('t.is_deleted', false)
                            ->where('t.carrier_id', $oCarrier->id_carrier)
                            ->get();

        // si el documento ya está procesado obtiene los datos de la base de datos
        $iVehKeyId = 0;
        if ($oDocument->is_processed) {
            $oObjData = clone $oMongoDocument;
            $oObjData->id = 0;
            $oVehicle = $oObjData->oVehicle;
            $oFigure = $oObjData->oFigure;
            $oTrailer = [];
            foreach($oObjData->lTrailers as $trailer){
                array_push($oTrailer, $trailer['oTrailer']);
            }

            $lSuburbs = [];
            for($i = 0; $i<count($oObjData["oCartaPorte"]["ubicaciones"]); $i++){
                $colonias = $this->getArrayFromString($oObjData["oCartaPorte"]["ubicaciones"][$i]["domicilio"]["colonia"]);
                $arrSuburbs = [];
                foreach($colonias as $c){
                    $colonia = \DB::table('sat_suburb')
                                    ->where([
                                        ['zip_code', $oObjData["oCartaPorte"]["ubicaciones"][$i]["domicilio"]["codigoPostal"]],
                                        ['key_code', $c]
                                        ])->first();
                    if(!is_null($colonia)){
                        array_push($arrSuburbs, $colonia);
                    }
                }
                array_push($lSuburbs, $arrSuburbs);
            }
        }
        else {
            // Si el documento no está procesado obtiene los datos del request
            $oJson = json_decode($oMongoDocument->body_request);
            $lCurs = clone $lCurrenciesQuery;
            $lCurs = $lCurs->selectRaw('cur.*, CONCAT(cur.key_code, " - ", cur.description) AS _description')
                            ->pluck('_description', 'key_code');

            $oRequestObj = RequestCore::adaptRequest($oJson);

            $lVehs = clone $lVehicles;
            
            $oVehicle = $lVehs->where('v.plates', $oRequestObj->placaTransporte)
                                    ->where('v.carrier_id', $oCarrier->id_carrier)
                                    ->first();

            $plates = $this->getArrayFromString($oRequestObj->placaRemolque);
            $lTra = clone $lTrailers;
            $oTrailer = [];

            foreach($plates as $p){
                $Trailer = $lTra->where('plates', $p)
                                    ->where('carrier_id', $oCarrier->id_carrier)
                                    ->first();
                if(is_null($Trailer)){
                    $Trailer = new \stdClass();
                }
                array_push($oTrailer, $Trailer);
            }

            $oObjData = RequestCore::requestToCfdiObject($oDocument, $oRequestObj, $lCurs, $oVehicle);
            $oObjData->conceptos = array_reverse($oObjData->conceptos);
            $array = json_decode(json_encode(clone $oObjData), true);
            foreach ($array as $key => $value) {
                $oMongoDocument->$key = $value;
            }
    
            $oMongoDocument->save();
            
            if ($oVehicle == null) {
                $oVehicle = new \stdClass(); 
            }
            $oFigure = new \stdClass();

            $lSuburbs = [];
            for($i = 0; $i<count($oObjData->oCartaPorte->ubicaciones); $i++){
                $colonias = $this->getArrayFromString($oObjData->oCartaPorte->ubicaciones[$i]->domicilio->colonia);
                $arrSuburbs = [];
                foreach($colonias as $c){
                    $colonia = \DB::table('sat_suburb')
                                    ->where([
                                        ['zip_code', $oObjData->oCartaPorte->ubicaciones[$i]->domicilio->codigoPostal],
                                        ['key_code', $c]
                                        ])->first();
                    if(!is_null($colonia)){
                        array_push($arrSuburbs, $colonia);
                    }
                }
                if(count($arrSuburbs) > 1){
                    $oObjData->oCartaPorte->ubicaciones[$i]->domicilio->colonia = null;
                }
                array_push($lSuburbs, $arrSuburbs);
            }
        }

        $lVehicles = $lVehicles->get();

        $lVehicleKeys = VehicleKey::get();

        // Obtiene las figuras de transporte que tiene dados de alta el transportista
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
                    'lSuburbs' => $lSuburbs,
                    'oTrailer' => $oTrailer,
                    'lFigures' => $lFigures,
                    'oVehicle' => $oVehicle,
                    'lVehicleKeys' => $lVehicleKeys,
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
            // return ['isValid' => false, 'message' => 'No existen series de folios configurados para el transportista, favor de configurarlas.'];
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

    /**
     * Guarda los datos del documento
     *
     * @param Request $request
     * @param integer $idDocument
     * 
     * @return redirect
     */
    public function update(Request $request, $idDocument)
    {
        $oCfdiData = json_decode($request->the_cfdi_data);

        $oDocument = Document::find($idDocument);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }else{
            auth()->user()->authorizePermission(['113']);
            auth()->user()->carrierAutorization($oDocument->carrier_id);
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
        $oMongoDocument->serie = strlen($oCfdiData->oData->serie) > 0 ? $oCfdiData->oData->serie : "";
        $oMongoDocument->folio = strlen($oCfdiData->oData->folio) > 0 ? $oCfdiData->oData->folio : "";

        $oDocument->serie = $oMongoDocument->serie;
        $oDocument->folio = $oMongoDocument->folio;

        $oMongoDocument->shipType = $oCfdiData->oData->shipType;
        $oDocument->ship_type = $oCfdiData->oData->shipType;
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
        $aRetenciones = [];
        $dRetention = 0;
        $indexConcept = 0;
        $lConcepts = [];
        foreach ($oMongoDocument->conceptos as $aConcept) {
            $oClientConcept = $oCfdiData->oData->conceptos[$indexConcept];
            $aConcept["valorUnitario"] = round($oClientConcept->valorUnitario, 2);
            $aConcept["isOfficialRate"] = $oClientConcept->isOfficialRate;
            $aConcept["importe"] = round($aConcept["valorUnitario"] * $aConcept["quantity"], 2);
            $aConcept["discount"] = round($oClientConcept->discount, 2);
            $aConcept["description"] = $oClientConcept->description;
            $aConcept["unidad"] = $oClientConcept->unidad;
            $aConcept["numIdentificacion"] = $oClientConcept->numIndentificacion;
            if (env('WITH_CUSTOM_ATTRIBUTES')) {
                $aConcept["oCustomAttributes"] = $oClientConcept->oCustomAttributes;
            }
            
            $lCptTraslados = [];
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

                $lCptTraslados[] = $aTraslado;
                $dTraslados += $aTraslado["importe"];
            }

            $lCptRetenciones = [];
            foreach ($aConcept["oImpuestos"]["lRetenciones"] as $aRetecion) {
                $aRetecion["tasa"] = round($aRetecion["tasa"], 4);
                $aRetecion["base"] = $aConcept["importe"];
                $aRetecion["importe"] = round($aRetecion["base"] * $aRetecion["tasa"], 2);

                if (array_key_exists(($aRetecion["tasa"].""), $aRetenciones)) {
                    $aRetenciones[($aRetecion["tasa"]."")] = [
                                                                "base" => ($aRetenciones[($aRetecion["tasa"]."")]["base"] + $aRetecion["base"]),
                                                                "importe" => ($aRetenciones[($aRetecion["tasa"]."")]["importe"] + $aRetecion["importe"]),
                                                                "impuesto" => ($aRetenciones[($aRetecion["tasa"]."")]["impuesto"]),
                                                            ];
                }
                else {
                    $aRetenciones[($aRetecion["tasa"]."")] = [
                                                                "base" => $aRetecion["base"],
                                                                "importe" => $aRetecion["importe"],
                                                                "impuesto" => $aRetecion["impuesto"],
                                                            ];
                }

                $lCptRetenciones[] = $aRetecion;
                $dRetention += $aRetecion["importe"];
            }

            $dSubTotal += $aConcept["importe"];
            $dDiscount += $aConcept["discount"];

            $aConcept["oImpuestos"]["lTraslados"] = $lCptTraslados;
            $aConcept["oImpuestos"]["lRetenciones"] = $lCptRetenciones;
            $lConcepts[] = $aConcept;
            $indexConcept++;
        }

        $oMongoDocument->conceptos = $lConcepts;

        // Impuestos
        $oImpuestos = new \stdClass();
        $oImpuestos->totalImpuestosTrasladados = $dTraslados;
        $oImpuestos->totalImpuestosRetenidos = $dRetention;

        $oImpuestos->lTraslados = [];
        foreach ($aTraslados as $key => $value) {
            $oTraslado = new \stdClass();
            $oTraslado->tasa = $key;
            $oTraslado->base = $value["base"];
            $oTraslado->importe = $value["importe"];
            $oImpuestos->lTraslados[] = $oTraslado;
        }
        
        $oImpuestos->lRetenciones = [];
        foreach ($aRetenciones as $key => $value) {
            $oRetencion = new \stdClass();
            $oRetencion->tasa = $key;
            $oRetencion->base = $value["base"];
            $oRetencion->importe = $value["importe"];
            $oRetencion->impuesto = $value["impuesto"];
            $oImpuestos->lRetenciones[] = $oRetencion;
        }

        $oMongoDocument->oImpuestos = json_decode(json_encode($oImpuestos), true);

        $oMongoDocument->subTotal = round($dSubTotal, 2);
        $oMongoDocument->discounts = round($dDiscount, 2);
        $oMongoDocument->totalImpuestosRetenidos = $dRetention;
        $oMongoDocument->totalImpuestosTrasladados = $dTraslados;
        $oMongoDocument->total = round($dSubTotal - $dDiscount + $dTraslados - $dRetention, 2);

        // Carta Porte
        $oMongoDocument->oVehicle = json_decode(json_encode($oCfdiData->oVehicle), true);
        $oMongoDocument->vehKeyId = $oCfdiData->oVehicle->veh_key_id;
        $oDocument->veh_key_id = $oCfdiData->oVehicle->veh_key_id;
        $oMongoDocument->oFigure = json_decode(json_encode($oCfdiData->oFigure), true);
        $oMongoDocument->lTrailers = isset($oCfdiData->lTrailers) && count($oCfdiData->lTrailers) > 0 ? json_decode(json_encode($oCfdiData->lTrailers), true) : [];

        // Ubicaciones
        $totalDistancia = 0.0;
        $locations = [];
        foreach ($oMongoDocument->oCartaPorte["ubicaciones"] as $index => $location) {
            $oClientLoc = $oCfdiData->oData->oCartaPorte->ubicaciones[$index];

            if ($index == 0) {
                $distance = 0.0;
            }
            else {
                $distance = $oClientLoc->distanciaRecorrida;
            }
            
            try {
                $oDateArriveDep = Carbon::parse($oClientLoc->fechaHoraSalidaLlegada);
                $location["fechaHoraSalidaLlegada"] = $oDateArriveDep->format('Y-m-d').'T'.$oDateArriveDep->format('H:i:s');
            }
            catch (\Throwable $th) {
                return redirect("documents")->with(['icon' => "error", 'message' => "La fecha de salida/llegada no es válida"]);
            }

            $location["distanciaRecorrida"] = $distance;
            $location["IDUbicacion"] = $oClientLoc->IDUbicacion;
            $location["domicilio"]["colonia"] = $oClientLoc->domicilio->colonia;
            $locations[] = $location;
            $totalDistancia += $oClientLoc->distanciaRecorrida;
        }

        $oCP = $oMongoDocument->oCartaPorte;
        $oCP["totalDistancia"] = $totalDistancia;
        $oCP["ubicaciones"] = $locations;

        // Mercancías
        $mercancias = [];
        $pesoBrutoTotal = 0.0;
        foreach ($oCP["mercancia"]["mercancias"] as $index => $merch) {
            $pesoBrutoTotal += round($merch["pesoEnKg"], 3);

            // Cantidades transportadas
            if (count($locations) > 2) {
                $cantidadesTransportadas = [];
                foreach ($merch["cantidadesTransportadas"] as $qtyTransport) {
                    $qtyTransport["idOrigen"] = $locations[0]["IDUbicacion"];
                    $qtyTransport["idDestino"] = $locations[$qtyTransport["index"]]["IDUbicacion"];
                    $cantidadesTransportadas[] = $qtyTransport;
                }

                $merch["cantidadesTransportadas"] = $cantidadesTransportadas;
            }

            unset($merch["merchs"]);
            $mercancias[] = $merch;
        }
        $oCP["mercancia"]["mercancias"] = $mercancias;
        $oCP["mercancia"]["pesoBrutoTotal"] = $pesoBrutoTotal;

        $oMongoDocument->oCartaPorte = $oCP;
        
        // Se genera el XML de la carta porte
        $sXml = XmlGeneration::generateCartaPorte($oDocument, $oMongoDocument, $oCarrier);

        $oDocument->generated_at = date('Y-m-d H:i:s');
        $oDocument->is_editing = true;
        $oDocument->dt_editing = null;
        $oDocument->is_processed = true;
        GralUtils::saveRates($oDocument->carrier_id, $oDocument->ship_type, $oMongoDocument->vehKeyId, $locations, $oMongoDocument->conceptos);
        $oCon = $oMongoDocument->conceptos;
        for($i=0; $i<count($oCon); $i++){
            $oCon[$i]['isOfficialRate'] = false; 
        }
        $oMongoDocument->conceptos = $oCon;
        $oDocument->save();

        $oMongoDocument->xml_cfdi = $sXml;
        $oMongoDocument->save();

        // Generamos el pdf
        $pdf = CfdiUtils::updatePdf($oMongoDocument->_id, $sXml, $oDocument->carrier_id, $oMongoDocument);

        return redirect("documents");
    }

    public function sign($id)
    {
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }else{
            auth()->user()->authorizePermission(['116']);
            auth()->user()->carrierAutorization($oDocument->carrier_id);
        }
        
        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);

        if ($oDocument->is_archive) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento está archivado, no se puede timbrar"]);
        }

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
                $log->message = $cfdiResponse["message"];
                $log->idError = $cfdiResponse["error_code"];
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
        $pdf = CfdiUtils::updatePdf($oMongoDocument->_id, $oMongoDocument->xml_cfdi, $oDocument->carrier_id, $oMongoDocument);
        // enviar correo
        $mails = MailUtils::getMails();
        $comercial_name = MailUtils::getComercialName();
        foreach ($mails as $m) {
            Mail::to($m)->send(new SendXmlPdf($oMongoDocument->xml_cfdi, $pdf, $comercial_name, $oMongoDocument->folio, $oMongoDocument->serie, $oMongoDocument->uuid));
        }

        return redirect("documents")->with(['message' => "El documento ha sido timbrado exitosamente", 'icon' => "success"]);
    }

    public function regeneratePDF($id){
        $pdf = $this->callRegeneratePDF($id);
        if($pdf){
            return redirect("documents")->with(['message' => "PDF regenerado", 'icon' => "success"]);
        }else{
            return redirect("documents")->with(['message' => "No se pudo regenerar el pdf", 'icon' => "error"]);
        }
    }

    public function callRegeneratePDF($id){
        try {
            $oDocument = Document::find($id);
            $oMongoDocument = MDocument::find($oDocument->mongo_document_id);
            $pdf = CfdiUtils::updatePdf($oMongoDocument->_id, $oMongoDocument->xml_cfdi, $oDocument->carrier_id, $oMongoDocument);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    public function cancel(Request $request)
    {
        $id = $request->id;
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()){
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }else{
            auth()->user()->authorizePermission(['117']);
            auth()->user()->carrierAutorization($oDocument->carrier_id);
        }

        $oMongoDocument = MDocument::find($oDocument->mongo_document_id);
        if ($oDocument->is_archive) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento está archivado, no se puede cancelar"]);
        }

        if (! $oDocument->is_processed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento no ha sido procesado"]);
        }

        if (! $oDocument->is_signed) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento no ha sido timbrado"]);
        }

        if ($oDocument->is_canceled) {
            return redirect("documents")->with(['icon' => "error", 'message' => "El documento ya está cancelado"]);
        }

        $oCarrier = Carrier::find($oDocument->carrier_id);

        $oMDocumentRef = null;
        if (isset($request->ref) && $request->ref > 0) {
            $oDocRef = Document::find($request->ref);
            $oMDocumentRef = $oDocRef->mongo_document_id;
        }

        /**
         * Validación de cancelación
         */
        $responseValidation = SATCore::validateCfdi($oMongoDocument->emisor["rfcEmisor"], 
                                                    $oMongoDocument->receptor["rfcReceptor"], 
                                                    $oMongoDocument->total, 
                                                    $oMongoDocument->uuid);

        if ($responseValidation->EsCancelable == "" || $responseValidation->Estado == "No encontrado." || 
            ($responseValidation->EsCancelable != "Cancelable sin aceptación" && $responseValidation->EsCancelable != "Cancelable con aceptación")) {
            $log = new MSignLog();
            $log->message = $responseValidation->CodigoEstatus." - ".$responseValidation->Estado." - ".$responseValidation->EstatusCancelacion.' - '.$responseValidation->EsCancelable;
            $log->idError = $responseValidation->CodigoEstatus;
            $log->mongoDocumentId = $oMongoDocument->id;
            $log->idDocument = $oDocument->id_document;
            $log->idUser = \Auth::user()->id;
            $log->save();

            return redirect()->back()->with(['icon' => "error", 'message' => $log->idError."-".$log->message]);
        }

        // Motivo de cancelación
        $oReason = \DB::table('f_documents_cancel_reasons')->where('id_reason', $request->reason)->first();
        if ($oReason == null) {
            $log = new MSignLog();
            $log->message = 'No se encontró la razon de cancelación';
            $log->idError = "-1";
            $log->mongoDocumentId = $oMongoDocument->id;
            $log->idDocument = $oDocument->id_document;
            $log->idUser = \Auth::user()->id;
            $log->save();

            return redirect()->back()->with(['icon' => "error", 'message' => $log->idError."-".$log->message]);
        }

        // Documento de referencia
        $folioRef = "";
        if ($oReason->with_reference) {
            if ($oMDocumentRef == null) {
                $log = new MSignLog();
                $log->message = 'No se encontró el documento de referencia';
                $log->idError = "-2";
                $log->mongoDocumentId = $oMongoDocument->id;
                $log->idDocument = $oDocument->id_document;
                $log->idUser = \Auth::user()->id;
                $log->save();

                return redirect()->back()->with(['icon' => "error", 'message' => $log->idError."-".$log->message]);
            }

            $folioRef = $oMDocumentRef->uuid;
        }

        // cancelar cfdi
        $cfdiResponse = FinkokCore::cancelCfdi($oMongoDocument, $oCarrier, $oReason, $oMDocumentRef);

        if ($cfdiResponse['success']) {
            $oResponse = $cfdiResponse['data'];

            // Cancelable con aceptación
            if ($responseValidation->EsCancelable == "Cancelable con aceptación") {
                $oMongoDocument->cancel_status = "pendiente";
            }
            else {
                // Cancelable sin aceptación
                $oMongoDocument->is_canceled = true;
                $oMongoDocument->cancel_status = "cancelado";
                $oMongoDocument->canceled_at = $oResponse->date;
                $oMongoDocument->cancellation_acknowledgment = $oResponse->acuse;   
            }

            $oMongoDocument->save();

            if ($responseValidation->EsCancelable == "Cancelable con aceptación") {
                $oDocument->cancel_status = "pendiente";
            }
            else {
                $oDocument->is_canceled = true;
                $oDocument->cancel_status = "cancelado";
                $oDocument->canceled_at = Carbon::parse($oResponse->date)->toDateTimeString();
            }
            
            $oDocument->usr_can_id = \Auth::user()->id;
            $oDocument->save();

            // Guardar registro de timbre en la base de datos
            $oDocStamp = new DocumentStamp();
            $oDocStamp->dt_stamp = date('Y-m-d H:i:s');
            $oDocStamp->stamp_type = "cancelacion";
            $oDocStamp->document_id = $oDocument->id_document;
            $oDocStamp->usr_new_id = \Auth::user()->id;
            $oDocStamp->save();

            return redirect("documents")->with(['message' => "El documento ha sido cancelado exitosamente", 'icon' => "success"]);
        }
        else {
            $log = new MSignLog();
            $log->message = $cfdiResponse["message"];
            $log->idError = $cfdiResponse["error_code"];
            $log->mongoDocumentId = $oMongoDocument->id;
            $log->idDocument = $oDocument->id_document;
            $log->idUser = \Auth::user()->id;
            $log->save();

            return redirect()->back()->with(['icon' => "error", 'message' => $log->idError."-".$log->message]);
        }
    }

    public function toStock($id) {
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }else{
            auth()->user()->authorizePermission(['114']);
            auth()->user()->carrierAutorization($oDocument->carrier_id);
        }
        $success = true;
        $error = "0";

        if($oDocument->is_signed == false && $oDocument->is_canceled == false){
            try {
                DB::transaction(function () use ($oDocument) {
                    $oDocument->is_archive = 1;
                    $oDocument->update();
                });
            } catch (QueryException $e) {
                $success = false;
                $error = messagesErros::sqlMessageError($e->errorInfo[2]);
            }
        }else{
            $success = false;
            $error = "El documento está timbrado o ha sido cancelado.";
        }

        if ($success) {
            $msg = "Se archivó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al archivar el registro. Error: " . $error;
            $icon = "error";
        }
        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }

    public function restore($id) {
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }else{
            auth()->user()->authorizePermission(['114']);
            auth()->user()->carrierAutorization($oDocument->carrier_id);
        }
        $success = true;
        $error = "0";
        
        try {
            DB::transaction(function () use ($oDocument) {
                $oDocument->is_archive = 0;
                $oDocument->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se recuperó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al recuperar el registro. Error: " . $error;
            $icon = "error";
        }
        return redirect("documents")->with(['message' => $msg, 'icon' => $icon]);
    }

    public function copy($id) {
        $oDocument = Document::find($id);
        if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
            abort_unless(CfdiUtils::remisionistaCanEdit($oDocument->carrier_id), 401);
        }else{
            auth()->user()->authorizePermission(['117']);
            auth()->user()->carrierAutorization($oDocument->carrier_id);
        }
        $success = true;
        $error = "0";

        try {
            DB::transaction(function () use ($oDocument) {
                $oMongoDocument = MDocument::findOrFail($oDocument->mongo_document_id);

                $oMongoDocumentCopy = new MDocument();
                $oMongoDocumentCopy->body_request = $oMongoDocument->body_request;
                $oMongoDocumentCopy->xml_cfdi = null;
                $oMongoDocumentCopy->carrier_id = $oMongoDocument->carrier_id;
                $oMongoDocumentCopy->save();

                $oDocumentCopy = new Document();
                $oDocumentCopy->serie = "";
                $oDocumentCopy->folio = "";
                $oDocumentCopy->shipping_folio = $oDocument->shipping_folio;
                $oDocumentCopy->scale_ticket = $oDocument->scale_ticket;
                $oDocumentCopy->ship_type = $oDocument->ship_type;
                $oDocumentCopy->requested_at = date('Y-m-d H:i:s');
                $oDocumentCopy->generated_at = date('Y-m-d H:i:s');
                $oDocumentCopy->comp_version = $oDocument->comp_version;
                $oDocumentCopy->xml_version = $oDocument->xml_version;
                $oDocumentCopy->is_processed = false;
                $oDocumentCopy->is_signed = false;
                $oDocumentCopy->is_canceled = false;
                $oDocumentCopy->is_deleted = false;
                $oDocumentCopy->is_editing = false;
                $oDocumentCopy->dt_editing = null;
                $oDocumentCopy->mongo_document_id = $oMongoDocumentCopy->id;
                $oDocumentCopy->carrier_id = $oDocument->carrier_id;
                $oDocumentCopy->veh_key_id = 1;
                $oDocumentCopy->usr_gen_id = 1;
                $oDocumentCopy->usr_sign_id = 1;
                $oDocumentCopy->usr_can_id = 1;
                $oDocumentCopy->usr_new_id = 1;
                $oDocumentCopy->usr_upd_id = 1;
                $oDocumentCopy->save();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Copia realizada con éxito del registro con embarque: ".$oDocument->shipping_folio;
            $icon = "success";
        } else {
            $msg = "Error al realizar la copia del registro. Error: " . $error;
            $icon = "error";
        }
        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Reenviar correo con PDF y XML
     *
     * @param [type] $id
     * @return void
     */
    public function forwardMail($id) {
        $oDocument = Document::find($id);
        $oMongoDocument = MDocument::where('_id', $oDocument->mongo_document_id)->first();
        $pdf = $oMongoDocument->pdf;

        // enviar correo
        $mails = MailUtils::getMails($oDocument->carrier_id);
        $comercial_name = MailUtils::getComercialName($oDocument->carrier_id);
        foreach ($mails as $m) {
            Mail::to($m)->send(new SendXmlPdf($oMongoDocument->xml_cfdi, $pdf, $comercial_name, $oMongoDocument->folio, $oMongoDocument->serie, $oMongoDocument->uuid));
        }

        $msg = "Correo reenviado con éxito";
        $icon = "success";

        return redirect("documents")->with(['message' => $msg, 'icon' => $icon]);
    }
}