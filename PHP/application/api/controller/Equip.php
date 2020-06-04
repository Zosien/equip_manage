<?php
namespace app\api\controller;

use app\api\model\Equip as EquipModel;
use app\api\model\UploadHandler;
use app\api\validate\EquipInfoValidate;
use app\api\validate\PageValidator;
use think\Request;

class Equip extends BaseController
{
    protected $beforeActionList = [
        'checkAdministratorScope' => ['only' => 'getEquipByKey,delEquip,save,upload'],
    ];
    public function save()
    {
        (new EquipInfoValidate())->goCheck();
        
        $num = EquipModel::newEquip();
        return json(['num'=>$num]);
    }
    public function upload()
    {
        $file = Request::instance()->file('file');
        $path = ROOT_PATH . 'runtime' . DS . 'uploads'. DS;
        if($file){
            $name = $path;
            $name .= saveFile($path,$file);
            if($name){
                $res = UploadHandler::equipHandler($name);
                return json($res);
            }
        }
    }
    public function getEquipByKey()
    {
        (new PageValidator())->goCheck();

        $req = Request::instance();
        $page = $req->param('page') ? $req->param('page') : 1;
        $limit = $req->param('limit') ? $req->param('limit') : 20;
        $keyword = $req->param('keyword');
        $data = EquipModel::getEquips($keyword, $this->scope,$page,$limit);
        
        return $data;
    }
    public function modifyInfo($id)
    {
        $req = Request::instance();
        $options = $req->param();
        unset($options['id']);
        // 前端已经做过处理
        $res = EquipModel::modify($id,$options);
        // $options = array_filter($options);
        // $res = UserModel::modifyUserByID($id,$options);
        return json(['data' => $res]);
    }
    public function delEquip()
    {
        $req = Request::instance();
        $options = $req->param();
        $res = EquipModel::delEquipByID($options);    
        return json(['data' => $res]);
    }
}