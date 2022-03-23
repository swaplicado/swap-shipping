<?php

namespace App\SXml;

use App\Models\Carrier;
use App\Models\Sat\Currencies;
use App\Models\Sat\FiscalAddress;
use App\Models\Sat\Items;
use App\Models\Sat\PostalCodes;
use App\Models\Sat\States;
use App\Models\Sat\Units;

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
                $response->code = 500;
                $response->message = "NO EXISTE EL ID ORIGEN";
                $response->checked_values = $info->idOrigen;

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
            is_null($result->rfcTransportista) ? $message = $message . "rfcTransportista not found in database. " : "";
            is_null($result->moneda) ? $message = $message . "moneda not found in database. " : "";

            if (!is_null($result->ubicaciones)) {
                $hasDestino ? "" : $message = $message . "ubicación Destino not found. ";
                foreach ($result->ubicaciones as $index => $u) {
                    is_null($u->domicilio->estado) ? $message = $message . "estado[" . $index . "] not match with postal code. " : "";
                    is_null($u->domicilio->pais) ? $message = $message . "pais[" . $index . "] not found in database. " : "";
                    is_null($u->domicilio->codigoPostal) ? $message = $message . "codigoPostal[" . $index . "] not found in database. " : "";
                }
            }
            else {
                $message = $message . "Node ubicaciones is null. ";
            }

            if (!is_null($result->mercancia)) {
                foreach ($result->mercancia->mercancias as $index => $m) {
                    is_null($m->bienesTransp) ? $message = $message . "bienesTransp[" . $index . "] not found in database. " : "";
                    is_null($m->claveUnidad) ? $message = $message . "claveUnidad[" . $index . "] not found in database. " : "";
                    is_null($m->moneda) ? $message = $message . "moneda[" . $index . "] not found in database. " : "";
                }
            }
            else {
                $message = $message . "Node mercancia is null. ";
            }

            $code = 0;
            strlen($message) > 0 ? $code = 500 : $code = 200;

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
