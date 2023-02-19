<?php

namespace Hmm\Dingerprebuildform;

use phpseclib\Crypt\RSA;
use Hmm\Dingerprebuildform\Dinger;
use Hmm\Dingerprebuildform\CustomException;
use Illuminate\Validation\ValidationException;

class Dinger
{  
    public static function load(Array $items, String $customerName,Int $totalAmount, String $merchantOrderId){

        $this->checkConfigData();

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

        $encryptData = $this->encryptData($payData);

        return config('dinger.url') . '?hashValue='. $encryptData['encryptedHashValue'] .'&payload=' . $encryptData['urlencode_value'];
    }

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
            $data == null ? throw new CustomException($key . 'cannot be null in config/dinger.php') : '';
        }
    }

    public static function encryptData($payData){

        $this->checkConfigData();

        $payData = json_encode($payData);

        $rsa = new RSA();

        extract($rsa->createKey(1024));

        $rsa->loadKey(config('dinger.public_key')); // public key

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
    
}