<?php

namespace app\api\controller;

use app\lib\exception\KeyErrorException;
use app\lib\RSADecrypt;

class PublicKey{
    public function get(){
        $decrypt = new RSADecrypt();
        $publicKey = $decrypt->getPublicKey();
        if(!$publicKey){
            throw new KeyErrorException();
        }
        return [
            'key' =>base64_encode($publicKey)
        ];
    }    
}