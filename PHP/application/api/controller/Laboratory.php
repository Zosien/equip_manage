<?php
namespace app\api\controller;

use app\api\model\Laboratory as LabModel;
use app\api\validate\PageValidator;
use think\Request;

class Laboratory extends BaseController
{
    protected $beforeActionList = [
        'checkAdministratorScope' => ['only' => 'getLabByKey,getUserById,delUser,activeUser'],
    ];
    public function getLabByKey()
    {
        (new PageValidator())->goCheck();
        $req = Request::instance();
        $page = $req->param('page') ? $req->param('page') : 1;
        $limit = $req->param('limit') ? $req->param('limit') : 20;
        $keyword = $req->param('keyword');
        $data = LabModel::getLabs($keyword, $this->scope,$page,$limit);
        
        return $data;
    }
}