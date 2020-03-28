<?php
namespace app\lib\exception;

class KeyErrorException extends BaseException
{
    public $code = 404;
    public $msg = "获取公钥失败";
    public $errorCode = 1000;
}