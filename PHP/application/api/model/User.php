<?php

namespace app\api\model;

use app\api\validate\Keyword;
use app\lib\exception\ParameterException;
use app\lib\exception\UserException;
use app\lib\RSADecrypt;
use Exception;
use think\Db;
use think\Model;
use think\Request;

class User extends Model
{
    protected $hidden = ['create_time', 'update_time', 'scope', 'psw', 'uid'];

    public function detail()
    {
        return $this->hasOne('UserInfo', 'uid', 'id');
    }

    public function newUser($username, $psw)
    {
        $user = self::create([
            'username' => $username,
            'psw' => md5($psw),
        ]);

        return $user->id;
    }
    /**
     * Undocumented function
     * TODO:
     * 事务，一个表插入失败则回滚
     *
     * @return void
     */
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
        changeEmptyToNull($param);
        $info = array_slice($param,2);
        $user = array_slice($param,0,2);
        $insert = "insert into user_info(institute,class,name,stu_num,gender,age) values (:institute,:class,:name,:stu_num,:gender,:age);";
        $res = Db::execute($insert,$info);
        if(!$res){
            throw new Exception("插入失败");
        }
        $sql = "select id from user where username=? and psw = md5(?)";
        $id = Db::query($sql,[$info['stu_num'],$info['stu_num']])[0]['id'];
        $res = self::modifyUserByID($id,$user);
        return $res;
        // var_dump($res);
        // $update = "update user set username = :username, psw = md5(:psw) where username = :stu_num;";
        // $res = Db::execute($update,$user);
    }
    /**
     * 根据id查询详细信息,返回结果集
     *
     * @author lzx <1562248279@qq.com>
     *
     * @param int $id
     *
     * @return array
     */
    public static function getUser($id)
    {
        $sql = "select * from user_view u left join user_info ui on u.id = ui.uid where u.id = ? limit 1;";
        $res = Db::query($sql,[$id])[0];
        unset($res['uid']);
        return $res;
    }
    public static function addEditAble($scope,$arr)
    {
        // var_dump($scope);
        foreach($arr as $key => &$value)
        {
            $val = array('value'=>$value,'able'=>self::userEditAble($scope,$key));
            $value = $val;
        }
        unset($value);
        return $arr;
    }
    public static function userEditAble($scope,$value)
    {
        switch ($scope) {
            case 1:
                if($value === "username" || $value == "psw" || $value == "institute" || $value == "class" || $value == "name" || $value == "gender" || $value == "age")
                    return true;
                else
                    return false;
                break;
            case 2:
                if($value === "username" || $value == "psw" || $value === "rank" || $value === "status" || $value == "institute" || $value == "class")
                    return true;
                else
                    return false;
                break;
            case 4:
                return false;
                break;
            default:
                break;
        }
    }
    /**
     * 获取指定权限能够查看的用户列表或者特定用户
     *
     * @author lzx <1562248279@qq.com>
     *
     * @param string $keyword
     * @param int $scope
     * @param int $page
     *
     * @return void
     */
    public static function getUsers($keyword, $scope,$page=1,$limit=20)
    {
        if ($keyword) {
            $dataSql = "select * from user u left join user_info ui on u.id = ui.uid where u.status != -1 and u.scope < ".$scope." and u.username like '%".$keyword."%' limit ".$limit." offset ".($page-1)*$limit.";";
            $countSql = "select count(*) count from user_view u left join user_info ui on u.id = ui.uid where u.status != -1 and u.scope < ".$scope." and u.username like '%".$keyword."%';";
            // $data = self::where('username', 'LIKE', "%$keyword%")->where('scope', '<', $scope)->with('details')->limit($limit)->page($page)->select();
            // $count = self::where('username', 'LIKE', "%$keyword%")->where('scope', '<', $scope)->count('id');
        } else {
            $dataSql = "select * from user_view u left join user_info ui on u.id = ui.uid where u.status != -1 and u.scope < ".$scope." limit ".$limit." offset ".($page-1)*$limit.";";
            $countSql = "select count(*) count from user_view u left join user_info ui on u.id = ui.uid where u.status != -1 and u.scope < ".$scope;
        }
        $data = Db::query($dataSql);
            //数据绑定有数字变字符串的问题
        $count = Db::query($countSql)[0]['count'];
        $res['dataCount'] = $count;
        $res['data'] = $data;
        return $res;
    }

    public static function delUser($id)
    {
        $res = self::where('id', 'in', $id)->update(['status' => -1]);

        return $res;
    }
    public static function modify($id,$options=[])
    {
        $user = [];
        $info = [];
        foreach ($options as $key => $value) {
            if($key == 'username' || $key == 'psw'){
                $user[$key] = $value;
            }else{
                $info[$key] = $value;
            }
        }
        if(sizeof($user)){
            $res = self::modifyUserByID($id,$user);
        }
        if(sizeof($info)){
            // self::table('user_info')->where('id','=',$id)->count()
            $res = self::modifyUserInfoByID($id,$info);
        }
        return $res;
    }
    /**
     * 根据id修改信息
     *
     * @param array $id
     * @param array $option
     *
     * @return int(0,1)
     */
    public static function modifyUserByID($id=[], $options)
    {
        $res = self::where('id', 'in', $id)->update($options);

        return $res;
    }
    public static function modifyUserInfoByID($id=[], $options)
    {
        $res = self::table('user_info')->where('uid', 'in', $id)->update($options);

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
