<?php
namespace app\api\validate;

class UserInfoValidate extends BaseValidate
{
    protected $rule = [
        'username' => 'require|min:3|max:10',
        // 'psw' => 'require|min:6|max:16',
        'institute' => 'require|instituteValidator',
        'class' => 'require',
        'name' => 'require',
        'stu_num' => 'require|stu_numValidator',
        'gender' => 'genderValidator',
        'age' => 'ageValidator',
    ];
    protected $message = [
        'username.require' => '用户名不能为空',
        'username.min' => '用户名最短3位',
        'username.max' => '用户名最长10位',
        // 'psw.require' => '密码不能为空',
        // 'psw.min' => '密码最短6位',
        // 'psw.max' => '密码最长16位',
        'institute.require' => '学院不能为空',
        'institute.instituteValidator' => '学院名称不正确',
        'class' => '班级不能为空',
        'name' => '姓名不能为空',
        'stu_num.require' => '学号不能为空',
        'stu_num.stu_numValidator' => '学号格式错误',
        'gender' => '性别信息错误',
        'age' => '年龄信息错误'
    ];
}