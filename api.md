| 接口名称  | 请求方法 | URL    | 参数          |
| --------- | -------- | ------ | ------------- |
| 获取Token | POST     | /token | name<br />psw |
|           |          |        |               |
|           |          |        |               |



## thinkphp 解决跨域问题

在app\api\behavior下创建一个类CORS

```php
<?php
namespace app\api\behavior;


use think\Response;

class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        if(request()->isOptions()){
            exit();
        }
    }
}
```

然后在application文件夹下的tags.php修改`app_init`项如下

```php
'app_init'     => [
        'app\\api\\behavior\\CORS'
    ],
```

也就是在项目初始化的时候设置header。



## token的使用

用随机字符串、当前时间戳、盐进行md5加密生成token

以token为键生成数据库缓存，内容是用户id，优先级等隐私信息。

客户端每次携带token请求数据，如果过期则重新请求。





TODO：
学生界面可以看到设备评价

前端登陆密码的加密传输算法 考虑用rsa