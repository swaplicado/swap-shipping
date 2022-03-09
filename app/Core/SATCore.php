<?php namespace App\Core;

use SoapClient;

class SATCore
{
    /**
     * Mensajes de Respuesta
     * Los mensajes de respuesta que arroja el servicio de consulta de CFDI´s incluyen la descripción del resultado de la operación que corresponden a la siguiente clasificación:
     * Mensajes de Rechazo.
     * N 601: La expresión impresa proporcionada no es válida.
     * Este código de respuesta se presentará cuando la petición de validación no se haya respetado en el formato definido.
     * N 602: Comprobante no encontrado.
     * Este código de respuesta se presentará cuando el UUID del comprobante no se encuentre en la Base de Datos del SAT.
     * Mensajes de Aceptación.
     * S Comprobante obtenido satisfactoriamente.
     *
     * @param [type] $rfc_emisor
     * @param [type] $rfc_receptor
     * @param [type] $total_factura
     * @param [type] $uuid
     * 
     * @return void
     */
    public static function validateCfdi($rfcEmisor, $rfcReceptor, $totalFactura, $uuid)
    {
        $web_service = (env('APP_ENV') === "local" ? env('URL_SAT_CFDI_LOCAL') :
                        (env('APP_ENV') === "production" ? env('URL_SAT_CFDI_LOCAL_PRODUCTION') : ''));

        try {
            $hora_envio = date("Y-m-d H:i:s");
            $client = new SoapClient($web_service);
        }
        catch (Exception $e) {
            $resp = new \stdClass();
            $resp->success = false;
            $resp->message = "Error al conectar al servicio de validación de CFDI";
            $resp->error = $e->getMessage();
            return $resp;
        }

        $requestString = "re={$rfcEmisor}&rr={$rfcReceptor}&tt={$totalFactura}&id={$uuid}";
        $param = array('expresionImpresa' => $requestString);
        $response = $client->Consulta($param);
        
        if (isset($response->ConsultaResult)) {
            return $response->ConsultaResult;
        }

        dd($response);
    }
}