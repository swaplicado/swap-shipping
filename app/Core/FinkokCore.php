<?php namespace App\Core;

use SoapClient;

class FinkokCore {

    public static function signCfdi($xml = "") {
        $username = env('FINKOK_USERNAME'); # Usuario de Finkok
        $password = env('FINKOK_PASSWORD'); # Contraseña de Finkok
        // $xml_content = base64_encode($xml); # En base64
        $xml_content = $xml;
        
        # Se almacenan las variables con los datos en el array $params
        $params = array(
            "xml" => $xml_content,
            "username" => $username,
            "password" => $password
        );
        
        # Petición al web service
        $client = new SoapClient("https://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl", array('trace' => 1));
        $result = $client->__soapCall("sign_stamp", array($params));
        
        $request = $client->__getLastRequest();
        $response = $client->__getLastResponse();

        if (isset($result->sign_stampResult)) {
            if (isset($result->sign_stampResult->xml) && isset($result->sign_stampResult->UUID)) {
                $resp = $result->sign_stampResult;

                return $resp;
            }

            if (isset($result->sign_stampResult->Incidencias->Incidencia)) {
                $incidencia = $result->sign_stampResult->Incidencias->Incidencia;
                $error = ["error_code" => $incidencia->CodigoError, 
                            "message" => $incidencia->MensajeIncidencia.(isset($incidencia->ExtraInfo) ? ("-".$incidencia->ExtraInfo) : "")];

                return [$error];
            }
        }

        return null;
    }
}