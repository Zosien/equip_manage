<?php

namespace app\api\model;

use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use app\lib\RSADecrypt;
use think\Cache;
use think\Exception;
use think\Model;
use think\Request;

class Token extends Model
{
    protected $table = 'user';

    private function generateToken()
    {
        $randChars = getRandomChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }

    private function saveToCache($token, $value)
    {
        $expire_in = config('setting.token_expire_in');
        $request = cache($token, $value, $expire_in);
        if (!$request) {
            throw new TokenException(['msg' => '服务器缓存异常', 'errorCode' => 10005]);
        }
    }

    private function prepareCachedValue($id, $scope)
    {
        $cachedValue['id'] = $id;
        $cachedValue['scope'] = $scope;

        return $cachedValue;
    }

    /**
     * 获取token
     * TODO: 根据header里面有无token判断是第一次获取token还是更新token.
     *
     * @return void
     */
    public function getToken()
    {
        $req = Request::instance();
        $data = $req->post();
        $rsa = new RSADecrypt();
        $psw = $rsa->privDecrypt($data['psw']);
        $result = self::where('username', '=', $data['name'])->where('psw', '=', md5($psw))->where('rank', '=', $data['rank'])->find();
        if ($result) {
            if ($result->status) {
                $token = $this->generateToken();
                $cachedValue = $this->prepareCachedValue($result->id, $result->scope);
                $value = json_encode($cachedValue);
                $this->saveToCache($token, $value);

                return $token;
            } else {
                throw new UserException(['code' => 401, 'msg' => '您被禁止登陆！', 'errorCode' => 2001]);
            }
        } else {
            throw new UserException(['code' => 401, 'msg' => '账号或密码错误', 'errorCode' => 2000]);
        }
    }

    public function refreshToken()
    {
        $req = Request::instance();
        $token = $req->header('token');
        if ('null' !== $token) {
            $res = cache($token);
            if ($res) {
                $token = $this->generateToken();
                $this->saveToCache($token, $res);

                return $token;
            } else {
                throw new TokenException(['msg' => 'token已过期', 'errCode' => 1001]);
            }
        } else {
            throw new TokenException(['msg' => '没有token，你还想刷新token？做梦哦', 'errCode' => 1001]);
        }
    }

    public static function getCurrentTokenVar($key)
    {
        $req = Request::instance();
        $token = $req->header('Authorization');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的Token不存在');
            }
        }
    }
}
