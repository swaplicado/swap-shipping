<?php

namespace App\SXml;

use App\Models\Carrier;
use App\Models\Document;
use App\Models\M\MDocument;
use App\Models\Sat\Currencies;
use App\Models\Sat\FiscalAddress;
use App\Models\Sat\Items;
use App\Models\Sat\PostalCodes;
use App\Models\Sat\States;
use App\Models\Sat\Units;
use Carbon\Carbon;
use ErrorException;
use Exception;

class verifyDocument
{
    public static function verifyJson($info)
    {
        try {
            $oOriginLocation = \DB::table('f_local_origins')
                                        ->where('origin_code', $info->idOrigen)
                                        ->where('is_deleted', false)
                                        ->first();

            if ($oOriginLocation == null) {
                $response = new \stdClass();
                $response->code = 401;
                $response->message = "NO EXISTE EL ID ORIGEN";
                $response->checked_values = $info->idOrigen;

                return $response;
            }

            $oDoc = Document::where('shipping_folio', $info->embarque)
                                ->orderBy('is_signed', 'desc')
                                ->orderBy('is_canceled', 'desc')
                                ->orderBy('is_processed', 'desc')
                                ->first();
            if ($oDoc != null) {
                if ($oDoc->is_editing && Carbon::parse($oDoc->dt_editing)->addMinutes(env('TIME_EDIT_MIN'))->lessThan(Carbon::now())) {
                    $oDoc->is_editing = false;
                    $oDoc->dt_editing = null;

                    $oMongoDocument = MDocument::find($oDoc->mongo_document_id);
                    if(!is_null($oMongoDocument)){
                        $oMongoDocument->body_request = json_encode($info);
                        $oMongoDocument->update();
                    }

                    $oDoc->update();
                }
            }
            if ($oDoc != null && $oDoc->is_archive) {
                $response = new \stdClass();
                $response->code = 201;
                $response->message = "EL DOCUMENTO SE ENCUENTRA ARCHIVADO";
                $response->checked_values = $info;
                $response->doc_id = $oDoc->id_document;
                $response->mongo_doc_id = $oDoc->mongo_document_id;

                return $response;
            }
            if ($oDoc != null && ($oDoc->is_signed || $oDoc->is_canceled || ($oDoc->is_processed && $oDoc->is_editing))) {
                $response = new \stdClass();
                $response->code = 202;
                $response->message = "EL DOCUMENTO YA FUE PROCESADO O SE ESTÁ EDITANDO";
                $response->checked_values = $info;
                $response->doc_id = $oDoc->id_document;
                $response->mongo_doc_id = $oDoc->mongo_document_id;

                return $response;
            }

            $infoValues = array(
                "rfcTransportista" => Carrier::where([['fiscal_id', $info->rfcTransportista], ['is_deleted', 0]])->value('fiscal_id'),
                "moneda" => Currencies::where('key_code', $info->moneda)->value('key_code'),
            );

            $infoUbicaciones = array("ubicaciones" => array());

            $hasDestino = false;

            $infoMercania = array(
                "mercancias" => array(),
            );
            foreach ($info->ubicaciones as $u) {
                $u = (object) $u;
                $u->tipoUbicacion === "Destino" ? $hasDestino = true : "";

                $ubicacion = array(
                    "tipoUbicacion" => $u->tipoUbicacion,
                    "rfcRemitenteDestinatario" => $u->rfcRemitenteDestinatario,
                );

                $u->domicilio = (object) $u->domicilio;
                $domicilio = array(
                    "estado" => States::where('key_code', $u->domicilio->estado)->value('key_code'),
                    "pais" => FiscalAddress::where('key_code', $u->domicilio->pais)->value('key_code'),
                    "codigoPostal" => PostalCodes::where('postal_code', $u->domicilio->codigoPostal)->value('postal_code'),
                );
                $ubicacion["domicilio"] = $domicilio;
                array_push($infoUbicaciones["ubicaciones"], $ubicacion);

                foreach ($u->mercancias as $m) {
                    $m = (object) $m;
                    $mercancia = array(
                        "bienesTransp" => Items::where('key_code', $m->bienesTransp)->value('key_code'),
                        "claveUnidad" => Units::where([['key_code', $m->claveUnidad], ['is_deleted', 0]])->value('key_code'),
                        "moneda" => Currencies::where('key_code', $m->moneda)->value('key_code'),
                    );
                    array_push($infoMercania["mercancias"], $mercancia);
                }
            }

            $infoValues["ubicaciones"] = $infoUbicaciones["ubicaciones"];
            $infoValues["mercancia"] = $infoMercania;
            $obj = json_encode($infoValues);
            $result = json_decode($obj);

            $response = new \stdClass();
            $response->code = null;
            $response->message = null;
            $response->checked_values = null;

            $message = "";
            is_null($result->rfcTransportista) ? [$message = $message . "rfcTransportista not found in database. ", $code=402] : "";
            is_null($result->moneda) ? [$message = $message . "moneda not found in database. ", $code=403] : "";

            if (!is_null($result->ubicaciones)) {
                $hasDestino ? "" : [$message = $message . "ubicación Destino not found. ", $code=404];
                foreach ($result->ubicaciones as $index => $u) {
                    is_null($u->domicilio->estado) ? [$message = $message . "estado[" . $index . "] not match with postal code. ", $code=405] : "";
                    is_null($u->domicilio->pais) ? [$message = $message . "pais[" . $index . "] not found in database. ", $code=406] : "";
                    is_null($u->domicilio->codigoPostal) ? [$message = $message . "codigoPostal[" . $index . "] not found in database. ", $code=407] : "";
                    is_null($u->rfcRemitenteDestinatario) ? [$message = $message."rfcRemitenteDestinatario[".$index."] is null. ", $code=408] : "";
                }
            }
            else {
                $message = $message . "Node ubicaciones is null. ";
                $code = 501;
            }

            if (!is_null($result->mercancia)) {
                foreach ($result->mercancia->mercancias as $index => $m) {
                    is_null($m->bienesTransp) ? [$message = $message . "bienesTransp[" . $index . "] not found in database. ", $code = 408] : "";
                    is_null($m->claveUnidad) ? [$message = $message . "claveUnidad[" . $index . "] not found in database. ", $code = 409] : "";
                    is_null($m->moneda) ? [$message = $message . "moneda[" . $index . "] not found in database. ", $code = 410] : "";
                }
            }
            else {
                $message = $message . "Node mercancia is null. ";
                $code = 502;
            }

            strlen($message) > 0 ? $code : $code = 200;

            $response->code = $code;
            $response->message = $message;
            $response->original_values = $info;
            $response->checked_values = $infoValues;

            return $response;

        }
        catch (Exception $ex) {
            $response = new \stdClass();
            $response->code = 500;
            $response->message = "Error al leer el request.";
            return $response;
        }
        catch (ErrorException $e) {
            $response = new \stdClass();
            $response->code = 500;
            $response->message = "Error al leer el request.";
            return $response;
        }
    }
}
