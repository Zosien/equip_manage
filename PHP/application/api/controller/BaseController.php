<?php

namespace app\api\controller;

use app\api\model\Token;
use app\lib\enum\ScopeEnum;
use think\Controller;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;

class BaseController extends Controller
{
    /**
     * 检查管理员权限，高于管理员权限可调用
     *
     * @author lzx <1562248279@qq.com>
     *
     * @return true or Exception
     */
    protected $scope = '';
    protected function checkAdministratorScope()
    {
        $scope = Token::getCurrentTokenVar('scope');
        $this->scope = $scope;
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
    protected function checkSuperAdministratorScope()
    {
        $scope = Token::getCurrentTokenVar('scope');
        $this->scope = $scope;
        if ($scope) {
            if ($scope == ScopeEnum::Super) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }
}
