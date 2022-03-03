<?php namespace App\Core;

use Illuminate\Support\Facades\Storage;
use SoapClient;

class FinkokCore {

    public static function signCfdi($xml = "") {
        // $username = env('FINKOK_USERNAME'); # Usuario de Finkok
        // $password = env('FINKOK_PASSWORD'); # Contraseña de Finkok
        $username = env('APP_ENV') === "local" ? env('FINKOK_USERNAME_LOCAL') : 
                    (env('APP_ENV') === "production" ? env('FINKOK_USERNAME_PRODUCTION') : '');

        $password = env('APP_ENV') === "local" ? env('FINKOK_PASSWORD_LOCAL') : 
                    (env('APP_ENV') === "production" ? env('FINKOK_PASSWORD_PRODUCTION') : '');
                    
        // $xml_content = base64_encode($xml); # En base64
        $xml_content = $xml;
        
        # Se almacenan las variables con los datos en el array $params
        $params = array(
            "xml" => $xml_content,
            "username" => $username,
            "password" => $password
        );
        
        # Petición al web service
        $client = new SoapClient((env('APP_ENV') === "local" ? env('FINKOK_URL_STAMP_LOCAL') :
                                (env('APP_ENV') === "production" ? env('FINKOK_URL_STAMP_PRODUCTION') : ''))
                                , array('trace' => 1));
        
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
                            "message" => str_replace('"', '', $incidencia->MensajeIncidencia).(isset($incidencia->ExtraInfo) ? ("-".str_replace('"', '', $incidencia->ExtraInfo)) : "")];

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
                 
        $client = new SoapClient((env('APP_ENV') === "local" ? env('FINKOK_URL_REGISTRATION_LOCAL') :
                                (env('APP_ENV') === "production" ? env('FINKOK_URL_REGISTRATION_PRODUCTION') : ''))
                                , array('trace' => 1));

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

    public static function cancelCfdi($pcFile, $pvFile, $pw, $fiscalId)
    {
        $cerFile = Storage::disk('local')->path($pcFile);
        $keyFile = Storage::disk('local')->path($pvFile);
        
        # Generar el certificado y llave en formato .pem
        shell_exec("openssl x509 -inform DER -outform PEM -in /home/user/Downloads/CSD/certificado.cer -pubkey -out /home/user/Downloads/CSD/certificado.pem");
        shell_exec("openssl pkcs8 -inform DER -in /home/user/Downloads/CSD/llave.key -passin pass:12345678a -out /home/user/Downloads/CSD/llave.key.pem");
        shell_exec("openssl rsa -in /home/user/Downloads/CSD/llave.key.pem -des3 -out /home/user/Downloads/CSD/llave.enc -passout pass:F.1994JCN");
        
        $username = 'pruebas@finkok.com';
        $password = 'S0port3.22';
        $taxpayer = 'EKU9003173C9';
        
        # Read the x509 certificate file on PEM format and encode it on base64
        $cer_path = "/home/user/Downloads/CSD/certificado.pem"; 
        $cer_file = fopen($cer_path, "r");
        $cer_content = fread($cer_file, filesize($cer_path));
        fclose($cer_file);
        # In newer PHP versions the SoapLib class automatically converts FILE parameters to base64, so the next line is not needed, otherwise uncomment it
        #$cer_content = base64_encode($cer_content);

        # Read the Encrypted Private Key (des3) file on PEM format and encode it on base64
        $key_path = "/home/user/Downloads/CSD/llave.enc";
        $key_file = fopen($key_path, "r");
        $key_content = fread($key_file,filesize($key_path));
        fclose($key_file);
        # In newer PHP versions the SoapLib class automatically converts FILE parameters to base64, so the next line is not needed, otherwise uncomment it
        #$key_content = base64_encode($key_content);

        $client = new SoapClient((env('APP_ENV') === "local" ? env('FINKOK_URL_CANCEL_LOCAL') :
                                (env('APP_ENV') === "production" ? env('FINKOK_URL_CANCEL_PRODUCTION') : ''))
                                , array('trace' => 1));
        
        $uuids = array("UUID" => "277C8C2C-4B76-50BD-851B-FB9EA3B8FCCB", "Motivo" => "02", "FolioSustitucion" => "");
        $uuid_ar = array('UUID' => $uuids);
        $uuids_ar = array('UUIDS' => $uuid_ar);
        print_r($uuids_ar);
        
        $params = array("UUIDS"=>$uuid_ar,
                        "username" => $username,
                        "password" => $password,
                        "taxpayer_id" => $taxpayer,
                        "cer" => $cer_content,
                        "key" => $key_content);
        
        print_r($params);
        
        $response = $client->__soapCall("cancel", array($params));
        
        # Generación de archivo .xml con el Request de timbrado
        $file = fopen("/home/user/Downloads/pruebas_can/SoapRequest.xml", "a");
        fwrite($file, $client->__getLastRequest() . "\n");
        fclose($file);
        
        $file = fopen("/home/user/Downloads/pruebas_can/SoapResponse.xml", "a");
        fwrite($file, $client->__getLastResponse() . "\n");
        fclose($file);
    }
}