<?php namespace App\Core;

use Illuminate\Support\Facades\Storage;
use SoapClient;
use App\Utils\CfdUtils;
use App\Utils\FileUtils;
use App\Models\Certificate;

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

    public static function cancelCfdi($oMDocument, $oCarrier)
    {
        // env('DEST_PATH');
        $cerEncFile = Storage::disk('local')->path(env('DEST_PATH')).'/'.$oCarrier->fiscal_id.'_cer.enc';
        $keyEncFile = Storage::disk('local')->path(env('DEST_PATH')).'/'.$oCarrier->fiscal_id.'_key.enc';

        $cerFile = Storage::disk('local')->path(env('DEST_PATH')).'/'.$oCarrier->fiscal_id.'_.cer';
        $cerFile = CfdUtils::decryptFile($cerEncFile, $cerFile, env('FL_KEY'));

        $keyFile = Storage::disk('local')->path(env('DEST_PATH')).'/'.$oCarrier->fiscal_id.'_.key';
        $keyFile = CfdUtils::decryptFile($keyEncFile, $keyFile, env('FL_KEY'));


        $cer = Certificate::where('carrier_id', $oCarrier->id_carrier)->first();

        if ($cer == null) {
            return ['success' => false, 'message' => 'No se encontró el certificado'];
        }

        $pw = CfdUtils::decryptPass($cer->pswrd);
        
        # Generar el certificado y llave en formato .pem
        $cerPem = (Storage::disk('local')->path(env('DEST_PATH')."/".$oCarrier->fiscal_id))."_cer.pem";
        $keyPem = (Storage::disk('local')->path(env('DEST_PATH')."/".$oCarrier->fiscal_id))."_key.pem";
        $encKey = Storage::disk('local')->path(env('DEST_PATH'))."/".($oCarrier->fiscal_id)."_llave.enc";
        shell_exec("openssl x509 -inform DER -outform PEM -in ".($cerFile)." -pubkey -out ".$cerPem);
        shell_exec("openssl pkcs8 -inform DER -in ".($keyFile)." -passin pass:".($pw)." -out ".$keyPem);
        shell_exec("openssl rsa -in ".$keyPem." -des3 -out ".$encKey." -passout pass:".env('FINKOK_PASSWORD'));
        
        $username = env('FINKOK_USERNAME');
        $password = env('FINKOK_PASSWORD');
        $taxpayer = $oCarrier->fiscal_id;
        
        # Read the x509 certificate file on PEM format and encode it on base64
        $cer_path = $cerPem; 
        $cer_file = fopen($cer_path, "r");
        $cer_content = fread($cer_file, filesize($cer_path));
        fclose($cer_file);
        # In newer PHP versions the SoapLib class automatically converts FILE parameters to base64, so the next line is not needed, otherwise uncomment it
        #$cer_content = base64_encode($cer_content);

        # Read the Encrypted Private Key (des3) file on PEM format and encode it on base64
        $key_path = $encKey;
        $key_file = fopen($key_path, "r");
        $key_content = fread($key_file, filesize($key_path));
        fclose($key_file);
        # In newer PHP versions the SoapLib class automatically converts FILE parameters to base64, so the next line is not needed, otherwise uncomment it
        #$key_content = base64_encode($key_content);

        $client = new SoapClient("https://demo-facturacion.finkok.com/servicios/soap/cancel.wsdl", array('trace' => 1));
        
        $uuids = array("UUID" => $oMDocument->uuid, "Motivo" => "02", "FolioSustitucion" => "");
        $uuid_ar = array('UUID' => $uuids);
        $uuids_ar = array('UUIDS' => $uuid_ar);
        // print_r($uuids_ar);
        
        $params = array("UUIDS"=> $uuid_ar,
                        "username" => $username,
                        "password" => $password,
                        "taxpayer_id" => $taxpayer,
                        "cer" => $cer_content,
                        "key" => $key_content);
        
        // print_r($params);
        
        $response = $client->__soapCall("cancel", array($params));
        
        if (isset($response->cancelResult)) {
            if (isset($response->cancelResult->Folios->Folio)) {
                $resp = $response->cancelResult->Folios->Folio;

                if ($resp->EstatusUUID == "201") {
                    $oResponse = new \stdClass();
                    $oResponse->success = true;
                    $oResponse->code = $resp->EstatusUUID;
                    $oResponse->message = $resp->EstatusCancelacion;
                    $oResponse->acuse = $response->cancelResult->Acuse;
                    $oResponse->date = $response->cancelResult->Fecha;

                    return ['success' => true, 'data' => $oResponse];
                }
            }
        }

        return ['success' => false, 'message' => "Error al cancelar el CFDI"];
    }
}