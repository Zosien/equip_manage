<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Menu extends Model
{
    protected $hidden = ['pid', 'scope'];

    public function child()
    {
        return $this->hasMany('Menu', 'pid', 'id');
    }

    public static function getMenu($scope)
    {
        $rules = self::table('rules')->where('scope','=',$scope)->value('rights');
        // $rules = explode(',',$rules);
        $sql = "select id,name,url from menu where pid is null and id in (".$rules.")";
        $data = Db::query($sql);
        foreach ($data as $key => &$value) {
            $sql = "select id,name,url from menu where pid =". $value['id'] ." and id in (".$rules.")";
            $child = Db::query($sql);
            $value['child'] = $child;
            foreach ($value['child'] as $key => &$value) {
                $sql = "select id,name,url from menu where pid =". $value['id'] ." and id in (".$rules.")";
                $child = Db::query($sql);
                $value['child'] = $child;
            }
        }
        return $data;
    }
}
