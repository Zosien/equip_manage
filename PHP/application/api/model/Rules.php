<?php
namespace app\api\model;

use app\api\model\Menu;
use app\lib\enum\ScopeEnum;
use think\Db;
use think\Model;

class Rules extends Model
{
    public static function getRules()
    {
        $sql = "select scope,name,description from rules where scope < ?";
        // $res = self::where('scope','<',ScopeEnum::Super)->field('scope')->select();
        $res = Db::query($sql,[ScopeEnum::Super]);
        $rules = $res;
        foreach ($rules as $key => &$value) {
            $rule = Menu::getMenu($value['scope']);
            $value['child'] = $rule;
        }
        unset($value);
        // var_dump($rules);
        return $rules;
    }
}