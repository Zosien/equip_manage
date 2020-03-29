<?php
namespace app\api\validate;

class Token extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'psw' => 'require',
        'rank' => 'require',
    ];
    protected $message = [
        'name' => '用户名不能为空',
        'psw' => '密码不能为空',
        'rank' => '身份信息不能为空',
    ];
}