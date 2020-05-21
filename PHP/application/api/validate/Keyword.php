<?php

namespace app\api\validate;

class Keyword extends BaseValidate
{
    protected $rule = [
        'key' => 'require',
    ];
    protected $message = [
        'key' => '用户名不能为空',
    ];
}
