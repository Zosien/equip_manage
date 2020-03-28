<?php
namespace app\api\validate;

class Token extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'psw' => 'require',
    ];
    protected $message = [
        'name' => '用户名不能为空',
        'psw' => '密码不能为空',
        // 'psw.min' => '密码最短6位'
    ];
}