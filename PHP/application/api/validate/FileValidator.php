<?php
namespace app\api\validate;

class FileValidator extends BaseValidate 
{
    protected $rule = [
        'file' => 'file',
    ];
    protected $message = [
        'file' => '必须是文件'
    ];
}