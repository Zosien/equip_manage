<?php

namespace app\api\model;

use app\lib\exception\ParameterException;
use app\lib\exception\UserException;
use app\lib\RSADecrypt;
use Exception;
use think\Db;
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
    public static function newUserInfo()
    {
        $rsa = new RSADecrypt();
        $req = Request::instance();
        $param = $req->post();
        $psw = $param['psw'];
        $psw = $rsa->privDecrypt($psw);
        if(!$psw){
            throw new ParameterException();
        }
        $param['psw'] = $psw;
        foreach($param as $key => &$val){
            if(empty($val) && $val !== 0){
                $val = null;
            }
        }
        var_dump($param);
        $info = array_slice($param,2);
        $user = array_slice($param,0,2);
        $insert = "insert into user_info(institute,class,name,stu_num,gender,age) values (:institute,:class,:name,:stu_num,:gender,:age);";
        $res = Db::execute($insert,$info);
        if(!$res){
            throw new Exception();
        }
        $update = "update user set username = :username and psw = md5(:psw);";
        $res = Db::execute($update,$user);
        return $res;
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
