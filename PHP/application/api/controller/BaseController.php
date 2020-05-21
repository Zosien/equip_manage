<?php

namespace app\api\controller;

use app\api\model\Token;
use app\lib\enum\ScopeEnum;
use think\Controller;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;

class BaseController extends Controller
{
    protected function checkAdministratorScope()
    {
        $scope = Token::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::Administrator) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }
}
