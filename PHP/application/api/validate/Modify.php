<?php

namespace app\api\validate;

class Modify extends BaseValidate
{
    protected $rule = [
        'id' => 'require|min:0',
        'status' => 'require|in:0,1',
    ];
    protected $message = [
        'id' => 'id不能为空',
        'status' => 'status参数错误',
    ];
}
