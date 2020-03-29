<?php
namespace app\api\model;

use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use app\lib\RSADecrypt;
use think\Model;
use think\Request;

class User extends Model
{
    protected $hidden=[
        'create_time','update_time','authority','status'
    ];
    public function newUser($username,$psw)
    {
        $user = self::create([
            'username' => $username,
            'psw' => md5($psw)
        ]);
        return $user->id;
    }
    private function generateToken(){
        $randChars = getRandomChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }
    private function saveToCache($token,$cachedValue){
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $request = cache($token,$value,$expire_in);
        if(!$request){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
    }
    private function prepareCachedValue($id,$scope){
        $cachedValue['id'] = $id;
        $cachedValue['scope'] = $scope;
        return $cachedValue;
    }
    public function getToken()
    {
        $req = Request::instance();
        $data = $req->post();
        $rsa = new RSADecrypt();
        $psw = $rsa->privDecrypt($data['psw']);
        $result = self::where('username','=',$data['name'])->where('psw','=',md5($psw))->where('rank','=',$data['rank'])->find();
        if($result){
            if($result->status){
                $token = $this->generateToken();
                $cachedValue = $this->prepareCachedValue($result->id,$result->scope);
                // $token = $this->saveToCache($token,$cachedValue);
                return $token;
            }
            else{
                throw new UserException([
                    'code' => 401,
                    'msg' => '您被禁止登陆！',
                    'errorCode' => 2001
                ]);
            }
        }
        else{
            throw new UserException([
                'code' => 401,
                'msg' => "账号或密码错误",
                'errorCode' => 2000
            ]);
        }
    }
}