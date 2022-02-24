<?php namespace App\Utils;

use Illuminate\Support\Facades\Storage;

/**
 * Define the number of blocks that should be read from the source file for each chunk.
 * For 'AES-128-CBC' each block consist of 16 bytes.
 * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
 * to read/write shorter or longer chunks.
*/
define('FILE_ENCRYPTION_BLOCKS', 10000);
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


    /**
     *
     */
    public static function encryptFile($source, $dest, string $key)
    {
        $key = substr(sha1($key, true), 0, 16);
        $iv = openssl_random_pseudo_bytes(16);

        $error = false;
        if ($fpOut = fopen($dest, 'w')) {
            // Put the initialzation vector to the beginning of the file
            fwrite($fpOut, $iv);
            if ($fpIn = fopen($source, 'rb')) {
                while (!feof($fpIn)) {
                    $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
                    $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    // Use the first 16 bytes of the ciphertext as the next initialization vector
                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $ciphertext);
                }
                fclose($fpIn);
            } else {
                $error = true;
            }
            fclose($fpOut);
        } else {
            $error = true;
        }

        return $error ? false : $dest;
    }

    public static function decryptFile(string $source, string $dest, string $key)
    {
        $key = substr(sha1($key, true), 0, 16);

        $error = false;
        if ($fpOut = fopen($dest, 'w')) {
            if ($fpIn = fopen($source, 'rb')) {
                // Get the initialzation vector from the beginning of the file
                $iv = fread($fpIn, 16);
                while (!feof($fpIn)) {
                    $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1)); // we have to read one block more for decrypting than for encrypting
                    $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    // Use the first 16 bytes of the ciphertext as the next initialization vector
                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $plaintext);
                }
                fclose($fpIn);
            } else {
                $error = true;
            }
            fclose($fpOut);
        } else {
            $error = true;
        }

        return $error ? false : $dest;
    }
}