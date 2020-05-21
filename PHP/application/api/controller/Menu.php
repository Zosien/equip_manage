<?php

namespace app\api\controller;

use app\api\model\Menu as AppMenu;

class Menu extends BaseController
{
    public function Menu($scope = 0)
    {
        $menu = new AppMenu();
        $data = $menu->getMenu($scope);

        return $data;
    }
}
