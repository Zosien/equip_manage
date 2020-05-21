<?php

namespace app\api\controller;

use app\api\model\Token as TokenModel;
use app\api\validate\Token as TokenValidate;
use think\Request;

class Token
{
    public function getToken()
    {
        (new TokenValidate())->goCheck();
        $user = new TokenModel();
        $token = $user->getToken();

        return [
            'token' => $token,
            'expire' => 1000 * config('setting.token_expire_in'),
        ];
    }

    public function refreshToken()
    {
        $user = new TokenModel();
        $token = $user->refreshToken();

        return [
            'token' => $token,
            'expire' => 1000 * config('setting.token_expire_in'),
        ];
    }

    public function delete()
    {
        $req = Request::instance();
        $token = $req->header('token');
        $req = cache($token, null);
        // $res = cache($token);

        return json($req);
    }
}
