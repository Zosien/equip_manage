<?php

namespace app\api\model;

use app\lib\exception\UserException;
use app\lib\RSADecrypt;
use think\Model;
use think\Request;

class User extends Model
{
    protected $hidden = ['create_time', 'update_time', 'scope', 'psw', 'user_id'];

    public function details()
    {
        return $this->hasOne('UserInfo', 'user_id', 'id');
    }

    public function newUser($username, $psw)
    {
        $user = self::create([
            'username' => $username,
            'psw' => md5($psw),
        ]);

        return $user->id;
    }

    public static function getUser($keyword, $scope)
    {
        if ($keyword) {
            $data = self::where('username', 'LIKE', $keyword)->where('scope', '<', $scope)->with('details')->select();
        } else {
            $data = self::where('scope', '<', $scope)->with('details')->select();
        }

        return $data;
    }

    public static function delUser($id)
    {
        $res = self::where('id', '=', $id)->update(['status' => 0]);

        return $res;
    }

    /**
     * Undocumented function.
     *
     * @param [int] $id
     *
     * @return int(0,1)
     */
    public static function modifyUser($id, $status)
    {
        $res = self::where('id', '=', $id)->update(['status' => $status]);

        return $res;
    }

    /**
     * 获取token
     * TODO：根据header里面有无token判断是第一次获取token还是更新token.
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
                $this->saveToCache($token, $cachedValue);

                return $token;
            } else {
                throw new UserException(['code' => 401, 'msg' => '您被禁止登陆！', 'errorCode' => 2001]);
            }
        } else {
            throw new UserException(['code' => 401, 'msg' => '账号或密码错误', 'errorCode' => 2000]);
        }
    }
}
