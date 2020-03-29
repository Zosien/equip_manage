<?php

namespace app\api\controller;

use app\api\validate\Token as TokenValidate;
use app\api\model\User;
use think\Env;
use think\Request;

class Token
{
    public function getToken()
    {
        (new TokenValidate())->goCheck();
        $user = new User();
        $token = $user->getToken();
        return [
            'token' => $token
        ];
        // echo UserModel::getLastSql();
    }
    public function test(){
        $req = Request::instance();
        var_dump($req->param());
    }
}
