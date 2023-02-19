<?php

namespace Hmm\Dingerprebuildform;

use phpseclib\Crypt\RSA;
use Hmm\Dingerprebuildform\Dinger;
use Illuminate\Validation\ValidationException;

class Dinger
{

    public static function prebuildFormUrl(Array $items, Array $credentials){
        return $items;
        // $this->checkItem($items);

    }

    public static function formCheck(Array $array){

        // $array = [
        //     "ClientId",
        //     "publicKey",
        //     "items",
        //     "customerName",

        // ];
        // $publicKey =  config('dinger.public_key');

        // $rsa = new RSA();

        // extract($rsa->createKey(1024));

        // $rsa->loadKey($publicKey); // public key

        // $rsa->setEncryptionMode(2);

        // $ciphertext = $rsa->encrypt($data);

        // $value = base64_encode($ciphertext);
    }


    // public function checkItem($items){
        
    //     throw ValidationException::withMessages(['items must be array']);

    // }
    
}