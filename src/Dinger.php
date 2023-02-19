<?php

namespace Hmm\Dingerprebuildform;

use ErrorException;
use phpseclib\Crypt\RSA;
use Hmm\Dingerprebuildform\Dinger;
use Hmm\Dingerprebuildform\CustomException;
use Illuminate\Validation\ValidationException;

class Dinger
{  
    ##############load_prebuildform_url
    public static function load(Array $items, String $customerName,Int $totalAmount, String $merchantOrderId){

        static::checkConfigData();

        $payData = [
            "clientId" => config('dinger.client_id'),
            "publicKey" => config('dinger.public_key'),
            "items" => json_encode(array($items)),
            "customerName" => $customerName,
            "totalAmount" => $totalAmount,
            "merchantOrderId" => $merchantOrderId,
            "merchantKey" => config('dinger.merchant_key'),
            "projectName" => config('dinger.project_name'),
            "merchantName" => config('dinger.merchant_name')
        ];

        $encryptData = static::encryptData($payData);

        return config('dinger.url') . '?hashValue='. $encryptData['encryptedHashValue'] .'&payload=' . $encryptData['urlencode_value'];
    }

    ##############config_validate
    public static function checkConfigData(){
        $config_data = [
            "public_key" => config('dinger.public_key'),
            "secret_key" => config('dinger.secret_key'),
            "url" => config('dinger.url'),
            "project_name" => config('dinger.project_name'),
            "merchant_name" => config('dinger.merchant_name'),
            "merchant_key" => config('dinger.merchant_key'),
            "client_id" => config('dinger.client_id'),
        ];
       
        foreach($config_data as $key => $data){
            $data == null ? throw new ErrorException($key . ' cannot be null in config/dinger.php') : '';
        }
    }

    ##############request_encryption
    public static function encryptData($payData){

        static::checkConfigData();

        $payData = json_encode($payData);

        $publicKey = '-----BEGIN PUBLIC KEY-----'. config('dinger.public_key') .'-----END PUBLIC KEY-----';

        $rsa = new RSA();

        extract($rsa->createKey(1024));

        $rsa->loadKey($publicKey); // public key

        $rsa->setEncryptionMode(2);

        $ciphertext = $rsa->encrypt($payData);

        $value = base64_encode($ciphertext);

        $urlencode_value = urlencode($value);

        $encryptedHashValue = hash_hmac('sha256', $payData, config('dinger.secret_key'));

        return [
            "encryptedHashValue" => $encryptedHashValue,
            "urlencode_value" => $urlencode_value
        ];
    }

    ##############callback
    public static function callback(String $paymentResult,String $checkSum){
        $callbackKey =  "d655c33205363f5450427e6b6193e466";
        $decrypted = openssl_decrypt($paymentResult,"AES-256-ECB", $callbackKey);
 
        if(hash("sha256", $decrypted) !== $checkSum){
            return throw new ErrorException('Payment result is incorrect Singanature.');
        } elseif (hash("sha256", $decrypted) == $checkSum) {
            $decryptedValues = json_decode($decrypted, true);
    
            return json_decode($decrypted, true);
        }
    }
    
}