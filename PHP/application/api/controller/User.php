<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\validate\Modify;
use app\lib\enum\ScopeEnum;
use app\lib\exception\RequestException;
use think\Request;

class User extends BaseController
{
    /**
     * 前置操作.
     *
     * @var array
     */
    protected $beforeActionList = [
        'checkAdministratorScope' => ['only' => 'getUser,delUser,activeUser'],
    ];

    /**
     * Undocumented function.
     * TODO:
     * - keyword参数验证
     *
     * @author lzx <1562248279@qq.com>
     *
     * @param [string] $keyword
     *
     * @return json 用户列表
     */
    public function getUser($keyword = null)
    {
        $data = UserModel::getUser($keyword, ScopeEnum::Administrator);

        return $data;
    }

    /**
     * Undocumented function.
     *
     * @author lzx <1562248279@qq.com>
     * TODO:
     * - 权限验证
     *
     * @return void
     */
    public function modify()
    {
        (new Modify())->goCheck();
        $data = Request::instance()->patch();
        $status = $data['status'];
        $id = $data['id'];
        $code = UserModel::modifyUser($id, $status);
        if (1 === $code) {
            $res = [
                'msg' => 'success',
            ];

            return json($res, 201);
        } else {
            throw new RequestException();
        }
    }
}
