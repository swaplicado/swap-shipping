<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrier;
use App\Models\Sat\Payment_forms;
use App\Models\Sat\Payment_methods;
use App\Models\Sat\Currencies;
use App\Models\Sat\Units;
use App\Models\Sat\Items;
use App\Models\Sat\FiscalAddress;
use App\Models\Sat\PostalCodes;

class VerifyController extends Controller
{
    public function verifyJson(){
        $file = file_get_contents("./doc/verify.json");
        $json = json_decode($file);
        $info = $json->info;

        $infoValues = array(
            "rfcTransportista" => Carrier::where([['fiscal_id', $info->rfcTransportista],['is_deleted', 0]])->value('fiscal_id'),
            "formaPago" => Payment_forms::where('key_code', $info->formaPago)->value('key_code'),
            "metodoPago" => Payment_methods::where('key_code', $info->metodoPago)->value('key_code'),
            "moneda" => Currencies::where('key_code', $info->moneda)->value('key_code')
        );

        $infoUbicaciones = array("ubicaciones" => array());

        $hasOrigen = false;
        $hasDestino = false;
        foreach($info->ubicaciones as $u){
            $u->tipoUbicacion === "Origen" ? $hasOrigen = true : ""; 
            $u->tipoUbicacion === "Destino" ? $hasDestino = true : "";

            $ubicacion =  array(
                "tipoUbicacion" => $u->tipoUbicacion
            );

            $domicilio = array(
                "estado" =>  PostalCodes::where([['postal_code', $u->domicilio->codigoPostal],['State', $u->domicilio->estado]])->value('State'),
                "pais" => FiscalAddress::where('key_code', $u->domicilio->pais)->value('key_code'),
                "codigoPostal" => PostalCodes::where('postal_code', $u->domicilio->codigoPostal)->value('postal_code')
            );
            $ubicacion["domicilio"] = $domicilio;
            array_push($infoUbicaciones["ubicaciones"], $ubicacion);
        }
        
        $infoMercania = array(
            "unidadPeso" => Units::where([['key_code', $info->mercancia->unidadPeso], ['is_deleted', 0]])->value('key_code'),
            "mercancias" => array()  
        );
        
        foreach($info->mercancia->mercancias as $m){
            $mercancia = array(
                "bienesTransp" => Items::where('key_code', $m->bienesTransp)->value('key_code'),
                "claveUnidad" => Units::where([['key_code', $m->claveUnidad], ['is_deleted', 0]])->value('key_code'),
                "moneda" => Currencies::where('key_code', $m->moneda)->value('key_code')
            );
            array_push($infoMercania["mercancias"], $mercancia);
        }

        $infoValues["ubicaciones"] = $infoUbicaciones["ubicaciones"];
        $infoValues["mercancia"] = $infoMercania;
        $obj = json_encode($infoValues);
        $result = json_decode($obj);

        $response = array("code" => null, "message" => null, "checked_values" => null);

        $message = "";
        is_null($result->rfcTransportista) ? $message = $message."rfcTransportista not found in database. " : "";
        is_null($result->formaPago) ? $message = $message."formaPago not found in database. " : "";
        is_null($result->metodoPago) ? $message = $message."metodoPago not found in database. " : "";
        is_null($result->moneda) ? $message = $message."moneda not found in database. " : "";

        if(!is_null($result->ubicaciones)){
            $hasOrigen ? "" : $message = $message."ubicaci??n Origen not found. ";
            $hasDestino ? "" : $message = $message."ubicaci??n Destino not found. ";
            foreach($result->ubicaciones as $index => $u){
                is_null($u->domicilio->estado) ? $message = $message."estado[".$index."] not match with postal code. " : "";
                is_null($u->domicilio->pais) ? $message = $message."pais[".$index."] not found in database. " : "";
                is_null($u->domicilio->codigoPostal) ? $message = $message."codigoPostal[".$index."] not found in database. " : "";
            }
        } else {
            $message = $message."Node ubicaciones is null. ";
        }

        if(!is_null($result->mercancia)){
            is_null($result->mercancia->unidadPeso) ? $message = $message."unidadPeso not found in database. " : "";
            foreach($result->mercancia->mercancias as $index => $m){
                is_null($m->bienesTransp) ? $message = $message."bienesTransp[".$index."] not found in database. " : "";
                is_null($m->claveUnidad) ? $message = $message."claveUnidad[".$index."] not found in database. " : "";
                is_null($m->moneda) ? $message = $message."moneda[".$index."] not found in database. " : "";
            }
        } else {
            $message = $message."Node mercancia is null. ";
        }

        
        $code = 0;
        strlen($message) > 0 ? $code = 500 : $code = 200;
        $response["code"] = $code;
        $response["message"] = $message;
        $response["Original_values"] = $info;
        $response["checked_values"] = $infoValues;

        $response = json_encode($response);
        dd($response, json_decode($response));
    }
}
