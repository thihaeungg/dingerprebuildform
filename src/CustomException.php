<?php
namespace Hmm\Dingerprebuildform;
use Exception;
class CustomException extends Exception
{   
    public function render($request)
    {       
        return response()->json(["error" => true, "message" => $this->getMessage()]);       
    }
}