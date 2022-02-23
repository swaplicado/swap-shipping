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

    public static function encryptPass($sPassword)
    {
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = env('ENC_VECTOR');
        
        // Store the encryption key
        $encryption_key = env('ENC_PKEY');
        
        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($sPassword, $ciphering,
                    $encryption_key, $options, $encryption_iv);

        return $encryption;
    }

    public static function decryptPass($sPassword)
    {
        // Non-NULL Initialization Vector for decryption
        $decryption_iv = env('ENC_VECTOR');
        
        // Store the decryption key
        $decryption_key = env('ENC_PKEY');

        // Store the cipher method
        $ciphering = "AES-128-CTR";
        $options = 0;
        
        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt($sPassword, $ciphering, 
                $decryption_key, $options, $decryption_iv);

        return $decryption;
    }
}