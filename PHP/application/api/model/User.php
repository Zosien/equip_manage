<?php
namespace app\api\model;

use think\Model;

class User extends Model
{
    protected $hidden=[
        'create_time','update_time','authority','status'
    ];
    public function login($username,$psw)
    {
        $result = self::where('username','=',$username)->where('psw','=',$psw)->select();
        return $result;
    }
}