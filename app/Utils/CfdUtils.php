<?php namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class CfdUtils
{
    /**
     * Obtiene el RFC del emisor del comprobante, fecha de emisión, fecha de caducidad y numero de certificado.
     * 
     * @param file $file
     * @return object $certificate
     * */
    public static function getCerData($pcUrl)
    {
        $certpath = Storage::disk('local')->path($pcUrl);
        $data = file_get_contents($certpath);
        $encoded = "-----BEGIN CERTIFICATE-----\n".base64_encode($data)."\n-----END CERTIFICATE-----";

        // parsear el certificado a objeto PHP
        $certinfo = openssl_x509_parse($encoded);
        
        $fromDate = date("Y-m-d", $certinfo['validFrom_time_t']);
        $expDate = date("Y-m-d", $certinfo['validTo_time_t']);
        $fiscalId = $certinfo['subject']['x500UniqueIdentifier'];
        
        // decodifircar el RFC
        $serialNumber = $certinfo['serialNumberHex'];
        $len = strlen($serialNumber);
        $certificateNumber = "";
        for ($i = 1; $i < $len; $i+=2) {
            $certificateNumber = $certificateNumber.$serialNumber[$i];
        }

        $response = (object) [
            'fromDate' => $fromDate,
            'expDate' => $expDate,
            'fiscalId' => $fiscalId,
            'certificateNumber' => $certificateNumber,
        ];

        return $response;
    }
}