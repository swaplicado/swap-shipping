<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Document;
use App\Models\M\MDocument;
use App\Models\M\MRequestLog;
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

            $oDoc = Document::where('shipping_folio', $oObjData->embarque)
                                ->first();
            if(is_null($oDoc)){
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
                $oDoc->is_editing = false;
                $oDoc->dt_editing = null;
                $oDoc->mongo_document_id = $oMongoDocument->id;
                $oDoc->carrier_id = $oCarrier->id_carrier;
                $oDoc->veh_key_id = 1;
                $oDoc->usr_gen_id = 1;
                $oDoc->usr_sign_id = 1;
                $oDoc->usr_can_id = 1;
                $oDoc->usr_new_id = 1;
                $oDoc->usr_upd_id = 1;
                $oDoc->save();

                $oLog = new MRequestLog();
                $oLog->shipping_folio = $oObjData->embarque;
                $oLog->request_body = json_encode($data);
                $oLog->response_code = 200;
                $oLog->response_message = "OK";
                $oLog->document_id = $oDoc->id;
                $oLog->mongo_document_id = $oMongoDocument->id;
                $oLog->dt_request = date('Y-m-d H:i:s');
                $oLog->save();
            }else{
                $oMongoDocument = MDocument::find($oDoc->mongo_document_id);
                $oMongoDocument->body_request = json_encode($data);
                $oMongoDocument->xml_cfdi = null;
                $oMongoDocument->update();
                
                $oDoc->shipping_folio = $oObjData->embarque;
                $oDoc->scale_ticket = $oObjData->boleto;
                $oDoc->requested_at = date('Y-m-d H:i:s');
                $oDoc->generated_at = date('Y-m-d H:i:s');
                $oDoc->comp_version = $oConfigurations->cfdi4_0->cartaPorteVersion;
                $oDoc->xml_version = $oConfigurations->cfdi4_0->cfdiVersion;
                $oDoc->is_processed = false;
                $oDoc->is_signed = false;
                $oDoc->is_canceled = false;
                $oDoc->is_deleted = false;
                $oDoc->is_editing = false;
                $oDoc->dt_editing = null;
                $oDoc->update();

                $oLog = MRequestLog::where('document_id', $oDoc->id)->first();
                $oLog->shipping_folio = $oObjData->embarque;
                $oLog->request_body = json_encode($data);
                $oLog->response_code = 200;
                $oLog->response_message = "OK";
                $oLog->document_id = $oDoc->id;
                $oLog->mongo_document_id = $oMongoDocument->id;
                $oLog->dt_request = date('Y-m-d H:i:s');
                $oLog->update();
            }
            
            return json_encode(['code' => $verify->code, 'message' => 'Documento guardado correctamente', 'data' => $verify], JSON_PRETTY_PRINT);
        }
        else {
            $oLog = new MRequestLog();
            $oLog->shipping_folio = $data["embarque"];
            $oLog->request_body = json_encode($data);
            $oLog->response_code = 500;
            $oLog->response_message = $verify->message;
            $oLog->document_id = isset($verify->doc_id) ? $verify->doc_id : null;
            $oLog->mongo_document_id = isset($verify->mongo_doc_id) ? $verify->mongo_doc_id : null;
            $oLog->dt_request = date('Y-m-d H:i:s');
            $oLog->save();

            return json_encode(['code' => $verify->code, 'message' => $verify->message, 'data' => $verify->checked_values], JSON_PRETTY_PRINT);
        }
    }
}
