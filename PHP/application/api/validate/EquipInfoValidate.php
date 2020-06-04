<?php
namespace app\api\validate;

class EquipInfoValidate extends BaseValidate 
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'price' => 'require|priceValidator',
        'factory' => 'require',
        'life_span' => 'require|number',
        'buyer' => 'require',
        'lab' => 'require'
    ];
    protected $message = [
        'id.require' => '设备号不能为空',
        'name.require' => '设备名不能为空',
        'price.require' => '价格不能为空',
        'price.priceValidator' => '价格必须是数字，且小数点后最多两位',
        'factory.require' => '厂家不能为空',
        'life_span.require' => '寿命不能为空',
        'life_span.number' => '寿命必须是数字',
        'buyer.require' => '负责人不能为空',
        'lab.require' => '实验室不能为空',
    ];
}