<?php
namespace app\api\validate;

class Token extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'psw' => 'require|min:6|max:16',
    ];
    protected $message = [
        'name' => '用户名不能为空',
        'psw' => '密码长度为6-16位',
        'psw.min' => '密码最短6位'
    ];
}