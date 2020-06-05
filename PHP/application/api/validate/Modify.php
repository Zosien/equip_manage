<?php

namespace app\api\validate;

class Modify extends BaseValidate
{
    protected $rule = [
        'id' => 'require|array|positiveIntArray',
        'options' => 'require',
    ];
    protected $message = [
        'id.require' => 'id不能为空',
        'id.array' => 'id必须是数组', 
        'options' => 'status不能为空',
    ];
}
