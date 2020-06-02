<?php

namespace app\api\validate;

class Modify extends BaseValidate
{
    protected $rule = [
        'id' => 'require|array|positiveIntArray',
        'status' => 'require|in:0,1',
    ];
    protected $message = [
        'id.require' => 'id不能为空',
        'id.array' => 'id必须是数组', 
        'status' => 'status参数错误',
    ];
}
