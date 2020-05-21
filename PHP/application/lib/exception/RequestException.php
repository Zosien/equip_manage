<?php

namespace app\lib\exception;

class RequestException extends BaseException
{
    public $code = 400;
    public $msg = '请求错误';
    public $errorCode = 1002;
}
