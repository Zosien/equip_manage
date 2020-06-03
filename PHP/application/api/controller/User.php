<?php

namespace app\api\controller;

use app\admin\model\User as ModelUser;
use app\api\model\Token;
use app\api\model\User as UserModel;
use app\api\validate\Modify;
use app\lib\enum\ScopeEnum;
use app\lib\exception\RequestException;
use think\Request;
use app\api\model\UploadHandler as UploadModel;
use app\api\validate\PageValidator;
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
        'checkAdministratorScope' => ['only' => 'getUserByKey,getUserById,delUser,activeUser'],
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
     * 根据id获取用户信息
     *
     * @author lzx <1562248279@qq.com>
     *
     * @param int $id
     *
     * @return void
     */
    public function getUserById($id)
    {
        $scope = Token::getCurrentTokenVar('scope');
        $res = UserModel::getUser($id);
        $res = UserModel::addEditAble($scope,$res);
        return $res;
    }
    /**
     * Undocumented function.
     * TODO:
     * - keyword参数验证
     *
     * @author lzx <1562248279@qq.com>
     *
     * @param string $keyword
     *
     * @return json 用户列表
     */
    public function getUserByKey()
    {
        (new PageValidator())->goCheck();
        $req = Request::instance();
        $page = $req->param('page');
        $limit = $req->param('limit');
        $keyword = $req->param('keyword');
        $data = UserModel::getUsers($keyword, ScopeEnum::Administrator,$page,$limit);
        
        return $data;
    }

    public function modifyInfo($id)
    {
        $req = Request::instance();
        $options = $req->param();
        unset($options['id']);
        // 前端已经做过处理
        $res = UserModel::modify($id,$options);
        // $options = array_filter($options);
        // $res = UserModel::modifyUserByID($id,$options);
        return $res;
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
    public function modifyStatus()
    {
        (new Modify())->goCheck();
        $data = Request::instance()->patch();
        $status = ['status'=>$data['status']];
        $id = $data['id'];
        UserModel::modifyUserByID($id, $status);
        $res = [
            'msg' => 'success',
        ];
        return json($res, 201);
    }
}
