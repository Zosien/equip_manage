<?php

namespace app\api\controller;

use app\api\model\Token;
use app\api\model\User as UserModel;
use app\api\validate\Modify;
use app\lib\enum\ScopeEnum;
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
        'checkAdministratorScope' => ['only' => 'getUserByKey,getUserById,delUser,activeUser,save,upload'],
    ];
    public function delUser()
    {
        $req = Request::instance();
        $id = $req->param();
        $res = UserModel::delUser($id);
        return json(['data'=>$res]);
    }
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
            $name .= saveFile($path,$file);
            if($name){
                $res = UploadModel::userHandler($name);
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

    public function modifyInfo()
    {
        (new Modify())->goCheck();
        $req = Request::instance();
        $data = $req->patch();
        $options = $data['options'];
        $id = $data['id'];
        // 前端已经做过处理
        $res = UserModel::modify($id,$options);
        // $options = array_filter($options);
        // $res = UserModel::modifyUserByID($id,$options);
        return json(['data'=>$res]);
    }
}
