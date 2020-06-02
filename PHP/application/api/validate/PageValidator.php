<?php
namespace app\api\validate;

class PageValidator extends BaseValidate
{
    protected $rule = [
        'page' => 'positiveInt',
        'limit' => 'positiveInt'
    ];
    protected $message = [
        'page' => 'page必须是正整数',
        'limit' => 'limit必须是正整数',
    ];
}
