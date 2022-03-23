<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Document;
use App\Models\M\MDocument;
use App\SXml\verifyDocument;
use Illuminate\Http\Request;

class ApiDocumentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $verify = verifyDocument::verifyJson((object) $data);
        if ($verify->code === 200) {
            $oObjData = (object) $data;

            $oCarrier = Carrier::where('fiscal_id', $oObjData->rfcTransportista)
                                ->where('is_deleted', false)
                                ->first();
            
            $oConfigurations = \App\Utils\Configuration::getConfigurations();

            $oMongoDocument = new MDocument();
            $oMongoDocument->body_request = json_encode($data);
            $oMongoDocument->xml_cfdi = null;
            $oMongoDocument->carrier_id = $oCarrier->id_carrier;
            $oMongoDocument->save();

            $oDoc = new Document();
            $oDoc->serie = "";
            $oDoc->folio = "";
            $oDoc->shipping_folio = $oObjData->embarque;
            $oDoc->scale_ticket = $oObjData->boleto;
            $oDoc->ship_type = "F";
            $oDoc->requested_at = date('Y-m-d H:i:s');
            $oDoc->generated_at = date('Y-m-d H:i:s');
            $oDoc->comp_version = $oConfigurations->cfdi4_0->cartaPorteVersion;
            $oDoc->xml_version = $oConfigurations->cfdi4_0->cfdiVersion;
            $oDoc->is_processed = false;
            $oDoc->is_signed = false;
            $oDoc->is_canceled = false;
            $oDoc->is_deleted = false;
            $oDoc->mongo_document_id = $oMongoDocument->id;
            $oDoc->carrier_id = $oCarrier->id_carrier;
            $oDoc->veh_key_id = 1;
            $oDoc->usr_gen_id = 1;
            $oDoc->usr_sign_id = 1;
            $oDoc->usr_can_id = 1;
            $oDoc->usr_new_id = 1;
            $oDoc->usr_upd_id = 1;

            $oDoc->save();

            return json_encode(['code' => $verify->code, 'message' => 'Documento guardado correctamente', 'data' => $verify], JSON_PRETTY_PRINT);
        }
        else {
            return json_encode(['code' => $verify->code, 'message' => $verify->message, 'data' => $verify], JSON_PRETTY_PRINT);
        }
    }
}
