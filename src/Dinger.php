<?php

namespace Hmm\Dingerprebuildform;

use Hmm\Dingerprebuildform\Dinger;

class Dinger
{

    public static function getPublicKey(){
        return config('dinger.testing') . 'sd';
        // return "asd";
    }
}