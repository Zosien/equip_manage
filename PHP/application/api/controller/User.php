<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\validate\Modify;
use app\lib\enum\ScopeEnum;
use app\lib\exception\RequestException;
use think\Request;
use app\api\model\UploadHandler as UploadModel;
use app\api\validate\UserInfoValidate;
use Exception;

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
    public function save()
    {
        (new UserInfoValidate())->goCheck();
        
        UserModel::newUserInfo();
        return json(['msg'=>"插入成功"]);
    }
    /**
     * 批量添加用户文件处理
     *
     * @author lzx <1562248279@qq.com>
     *
     * @return affected rows
     */
    public function upload()
    {
        $file = Request::instance()->file('file');
        $path = ROOT_PATH . 'runtime' . DS . 'uploads'. DS;
        if($file){
            $name = $path;
            $name .= UploadModel::save($path,$file);
            if($name){
                $res = UploadModel::handler($name);
                return json($res);
            }
        }
    }
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
