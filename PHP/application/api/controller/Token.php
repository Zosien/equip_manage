<?php

namespace app\api\controller;

use app\api\validate\Token as TokenValidate;
use app\api\model\User;

class Token
{
    public function getToken($username, $psw)
    {
        (new TokenValidate())->goCheck();
        $user = new User();
        $result = $user->login($username, $psw);
        // var_dump($result);
        // echo UserModel::getLastSql();
    }
}
