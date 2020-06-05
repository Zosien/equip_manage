<?php
namespace app\api\validate;

class IDArrayValidator extends BaseValidate
{
    protected $rule = [
        'id' => 'array',
    ];
    protected $message = [
        'id' => '参数错误'
    ];
}