<?php

namespace app\api\controller;

use app\api\validate\Token as TokenValidate;
use app\api\model\User;
use think\Env;
use think\Request;

class Token
{
    public function getToken($name, $psw)
    {
        (new TokenValidate())->goCheck();
        $user = new User();
        // var_dump(Env::get('MYSQL_HOST'));
        $token = $user->getToken($name, $psw);
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
