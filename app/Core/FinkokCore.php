<?php namespace App\Core;

use Illuminate\Support\Facades\Storage;
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

    public static function regCertificates($pcFile, $pvFile, $pwDecrypted, $fiscalId)
    {
        $cerFile = Storage::disk('local')->path($pcFile);
        $keyFile = Storage::disk('local')->path($pvFile);

        $contenidoCer = file_get_contents($cerFile);
        $contenidoKey = file_get_contents($keyFile);
        $typeUser= "O";
        $passKey = $pwDecrypted;

        $username = env('FINKOK_USERNAME'); # Usuario de Finkok
        $password = env('FINKOK_PASSWORD'); # Contraseña de Finkok
        
        $params = array(
            "reseller_username" => $username,
            "reseller_password" => $password,
            "taxpayer_id" => $fiscalId,
            "type_user" => 'O',
            "cer"=> $contenidoCer,
            "key" => $contenidoKey,
            "passphrase" => $passKey
        );
                 
        $client = new SoapClient('http://demo-facturacion.finkok.com/servicios/soap/registration.wsdl', array('trace' => 1));
        try {
            $result = $client->__soapCall("add", array($params));
        }
        catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

        $request = $client->__getLastRequest();
        $response = $client->__getLastResponse();

        if (isset($result->addResult)) {
            return ['success' => $result->addResult->success, 'message' => $result->addResult->message];
        }

        return null;
    }
}