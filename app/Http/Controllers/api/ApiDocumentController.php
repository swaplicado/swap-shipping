<?php

namespace App\Http\Controllers\api;

use App\Models\Document;
use App\Models\M\MDocument;
use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SXml\XmlGeneration;

class ApiDocumentController extends Controller
{
    public function store(Request $request)
    {
        $oObjData = (object) $request->info;

        $oCarrier = Carrier::where('fiscal_id', $oObjData->rfcTransportista)
                            ->where('is_deleted', false)
                            ->first();

        $oMongoDocument = new MDocument();
        $oMongoDocument->body_request = json_encode($oObjData);
        $oMongoDocument->body_json = null;
        $oMongoDocument->body_xml = null;
        $oMongoDocument->cfdi_xml = null;
        $oMongoDocument->carrier_id = $oCarrier->id_carrier;
        $oMongoDocument->save();

        $oDoc = new Document();
        $oDoc->dt_request = date('Y-m-d H:i:s');
        $oDoc->dt_generated = date('Y-m-d H:i:s');
        $oDoc->comp_version = $oObjData->versionComplemento;
        $oDoc->xml_version = $oObjData->versionCfdi;
        $oDoc->is_processed = false;
        $oDoc->is_deleted = false;
        $oDoc->mongo_document_id = $oMongoDocument->id;
        $oDoc->carrier_id = $oCarrier->id_carrier;
        $oDoc->usr_gen_id = 1;
        $oDoc->usr_sign_id = 1;
        $oDoc->usr_can_id = 1;
        $oDoc->usr_new_id = 1;
        $oDoc->usr_upd_id = 1;
        $oDoc->save();

        return json_encode($oDoc->id_document);
    }
}
