<?php

namespace app\api\controller;

use app\api\validate\Token as TokenValidate;
use app\api\model\User;
use think\Request;

class Token
{
    public function getToken($name, $psw)
    {
        (new TokenValidate())->goCheck();
        $user = new User();
        $result = $user->login($name, $psw);
        echo($result);
        // echo UserModel::getLastSql();
    }
    public function test(){
        $req = Request::instance();
        var_dump($req->param());
    }
}
