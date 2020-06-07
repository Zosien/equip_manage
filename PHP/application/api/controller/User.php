<?php

namespace app\api\controller;

use app\api\model\Menu as MenuModel;
use app\api\model\Token;
use app\api\model\User as UserModel;
use app\api\validate\Modify;
use app\lib\enum\ScopeEnum;
use think\Request;
use app\api\model\UploadHandler as UploadModel;
use app\api\validate\PageValidator;
use app\api\validate\UserInfoValidate;
use app\lib\exception\ParameterException;

class User extends BaseController
{
    /**
     * 前置操作.
     *
     * @var array
     */
    protected $beforeActionList = [
        'checkAdministratorScope' => 
        ['only' => 'getuserbykey,getuserbyid,deluser,activeuser,save,upload,getmenubytoken']
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
     * @param int $id
     *
     * @return void
     */
    public function getUserById($id)
    {
        $scope = $this->scope;
        $res = UserModel::getUser($id);
        $res = UserModel::addEditAble($scope,$res);
        return $res;
    }
    /**
     * Undocumented function.
     * TODO:
     * - keyword参数验证
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
    public function getInfoByToken()
    {
        $id = Token::getCurrentTokenVar('id');
        $userInfo = UserModel::getUser($id);
        
        return $userInfo;
    }
    public function getMenuByToken()
    {
        $scope = $this->scope;
        $menu =  MenuModel::getMenu($scope);
        return $menu;
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
    public function changePsw()
    {
        $id = Token::getCurrentTokenVar('id');
        $data = Request::instance();
        $options = $data->patch();
        $old_psw = $options['old_psw'];
        $new_psw = $options['new_psw'];
        $psw = UserModel::get($id)->psw;
        if($old_psw === $psw)
        {
            $res = UserModel::modifyUserByID($id,['psw'=>$new_psw]);
            if($res == 1){
                return json(['data'=>'修改成功']);
            }
            else{
                return json(['data'=>'修改失败']);
            }
        }
        else{
            throw new ParameterException(['msg' => '密码错误！']);
        }
    }
}
