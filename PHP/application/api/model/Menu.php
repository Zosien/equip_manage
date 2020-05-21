<?php

namespace app\api\model;

use think\Model;

class Menu extends Model
{
    protected $hidden = ['parent_id', 'scope'];

    public function child()
    {
        return $this->hasMany('Menu', 'parent_id', 'id');
    }

    public function getMenu($scope)
    {
        $data = self::where('parent_id', null)->where('scope', '>=', $scope)->with('child')->select();

        return $data;
    }
}
