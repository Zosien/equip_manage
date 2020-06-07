<?php

namespace app\api\controller;

use app\api\model\FileHandler;
use app\lib\exception\ParameterException;
use app\lib\RSADecrypt;
use think\Env;
use think\Request;

class ApiTest
{
    protected $beforeActionList = ['test' => ['only' =>'getTest']];
    public function getCache($name)
    {
        $cache = cache($name);
        var_dump($cache);
    }
    public function getTest()
    {
        var_dump("ok");
        // $en = "dkfljsf";
        // $rsa = new RSADecrypt();
        // $de = $rsa->privDecrypt($en);
        // if(!$de){
        //     throw new ParameterException();
        // }
    }
    public function postTest()
    {
        $req = Request::instance();
        $param = $req->post('psw');
        $rsa = new RSADecrypt();
        $res = $rsa->privDecrypt($param);
        var_dump($res);
    }
}
