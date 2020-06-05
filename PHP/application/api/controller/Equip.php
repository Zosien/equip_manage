<?php
namespace app\api\controller;

use app\api\model\Equip as EquipModel;
use app\api\model\UploadHandler;
use app\api\validate\EquipInfoValidate;
use app\api\validate\FileValidator;
use app\api\validate\IDArrayValidator;
use app\api\validate\Modify;
use app\api\validate\PageValidator;
use app\lib\exception\ParameterException;
use Exception;
use think\Request;

class Equip extends BaseController
{
    protected $beforeActionList = [
        'checkAdministratorScope' => [
            'only' => 'getEquipByKey,delEquipByID,save,upload,modifyInfo'
        ],
    ];
    public function save()
    {
        (new EquipInfoValidate())->goCheck();
        
        $num = EquipModel::newEquip();
        return json(['num'=>$num]);
    }
    public function upload()
    {
        (new FileValidator())->goCheck();
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
        else{
            throw new ParameterException();
        }
    }
    public function getEquipByKey()
    {
        (new PageValidator())->goCheck();
        $req = Request::instance();
        $page = $req->param('page');
        $limit = $req->param('limit');
        $keyword = $req->param('keyword');
        $data = EquipModel::getEquips($keyword, $this->scope,$page,$limit);
        
        return $data;
    }
    public function modifyInfo()
    {
        (new Modify())->goCheck();
        $req = Request::instance();
        $data = $req->patch();
        $id = $data['id'];
        $options = $data['options'];
        $res = EquipModel::modify($id,$options);
        return json(['data' => $res]);
    }
    public function delEquipByID()
    {
        (new IDArrayValidator())->goCheck();
        $req = Request::instance();
        $id = $req->delete();
        $res = EquipModel::delEquipByID($id);    
        return json(['data' => $res]);
    }
}