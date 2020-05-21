<?php

namespace app\api\controller;

class ApiTest
{
    public function getCache($name)
    {
        $cache = cache($name);
        var_dump($cache);
    }
}
