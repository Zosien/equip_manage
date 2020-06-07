<?php
namespace app\api\controller;

use app\api\model\Rules as RulesModel;
use app\lib\enum\ScopeEnum;

class Rules extends BaseController
{
    protected $beforeActionList = [
        'checkSuperAdministratorScope' => ['only' => 'getrules']
    ];
    public function getRules()
    {
        $rules = RulesModel::getRules();
        return $rules;
    }
}