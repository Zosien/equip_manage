<?php

namespace app\api\behavior;

class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization,Origin, X-Requested-With, Content-Type, Accept');
        header('Access-Control-Allow-Methods: DELETE,POST,GET,PUT,PATCH');
        header('Access-Control-Max-Age: '.config('setting.token_expire_in'));
        if (request()->isOptions()) {
            exit();
        }
    }
}
