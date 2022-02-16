<?php namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Http\Request;

class Configuration {

    public static function getConfigurations()
    {
        // Read File
        $jsonString = file_get_contents(base_path('shipconfig.json'));
        $data = json_decode($jsonString);

        return $data;
    }

    public static function setConfiguration($key, $value)
    {
        // Read File
        $jsonString = file_get_contents(base_path('shipconfig.json'));
        $data = json_decode($jsonString, true);

        // Update Key
        $data[$key] = $value;

        // Write File
        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(base_path('shipconfig.json'), stripslashes($newJsonString));
    }

    public static function updateConfiguration($jsonRequest)
    {
        // Read File
        $jsonString = file_get_contents(base_path('shipconfig.json'));
        $data = json_decode($jsonString, true);
        // Update Key
        $data['localCurrency'] = $jsonRequest['localCurrency'];
        $data['tarifaBase'] = $jsonRequest['tarifaBase'];
        $data['tarifaBaseEscala'] = $jsonRequest['tarifaBaseEscala'];
        $data['distanciaMinima'] = $jsonRequest['distanciaMinima'];
        $data['cfdi4_0']['claveServicio'] = $jsonRequest['claveServicio'];
        $data['cfdi4_0']['prodServDescripcion'] = $jsonRequest['prodServDescripcion'];
        $data['cfdi4_0']['claveUnidad'] = $jsonRequest['claveUnidad'];
        $data['cfdi4_0']['simboloUnidad'] = $jsonRequest['simboloUnidad'];
        $data['cfdi4_0']['rfc'] = $jsonRequest['rfc'];
        $data['cfdi4_0']['nombreReceptor'] = $jsonRequest['nombreReceptor'];
        $data['cfdi4_0']['domicilioFiscalReceptor'] = $jsonRequest['domicilioFiscalReceptor'];
        $data['cfdi4_0']['lugarExpedicion'] = $jsonRequest['domicilioFiscalReceptor'];
        $data['cfdi4_0']['regimenFiscalReceptor'] = $jsonRequest['regimenFiscalReceptor'];
        $data['cfdi4_0']['usoCFDI'] = $jsonRequest['usoCFDI'];
        $data['cfdi4_0']['objetoImp'] = $jsonRequest['objetoImp'];

        // Write File
        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(base_path('shipconfig.json'), stripslashes($newJsonString));
    }
}