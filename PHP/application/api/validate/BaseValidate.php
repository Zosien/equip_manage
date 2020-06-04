<?php

namespace app\api\validate;

use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        $request = Request::instance();
        $params = $request->param();
        $result = $this->batch()->check($params);
        if (!$result) {
            $e = new ParameterException([
                'msg' => $this->error,
            ]);
            throw $e;
        } else {
            return true;
        }
    }

    public function notEmpty($value, $rule = '', $data = '', $field = '')
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }

    public function instituteValidator($value, $rule = '', $data = '', $field = '')
    {
        $arr = ['信息学院', '化工学院', '机械学院', '文法学院'];
        if (in_array($value, $arr)) {
            return true;
        } else
            return false;
    }
    public function stu_numValidator($value, $rule = '', $data = '', $field = '')
    {
        $regx = '/^20((1[4-9])|(20))\d{6}$/';
        $res = preg_match($regx, $value);
        if ($res)
            return true;
        else
            return false;
    }
    public function genderValidator($value, $rule = '', $data = '', $field = '')
    {
        if ($value == "" || $value == "男" || $value == "女")
            return true;
        else
            return false;
    }

    public function ageValidator($value, $rule = '', $data = '', $field = '')
    {
        if ($value == "")
            return true;
        else if (is_numeric($value) && $value > 0 && $value < 110)
            return true;
        else
            return false;
    }

    public function positiveInt($value, $rule = '', $data = '', $field = '')
    {
        $value = $value-'0';
        if (is_integer($value) && $value > 0) {
            return true;
        } else return false;
    }
    public function positiveIntArray($value, $rule = '', $data = '', $field = '')
    {
        foreach($value as $val){
            if(!$this->positiveInt($val)){
                return false;
            }
        }
        return true;
    }
    public function priceValidator($value, $rule = '', $data = '', $field = '')
    {
        $regx =  '/^\d+(\.(\d){1,2})?$/';
        $res = preg_match($regx,$value);
        if($res)
            return true;
        else
            return false;
    }
}
