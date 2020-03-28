| 接口名称  | 请求方法 | URL    | 参数          |
| --------- | -------- | ------ | ------------- |
| 获取Token | POST     | /token | name<br />psw |
| 获取公钥  | GET      | /key   |               |
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

用随机字符串、当前时间戳、盐进行md5加密生成token，传到前端，放入sessionStorage中，客户端配置每次请求数据加入token。

后端以token为键生成数据库缓存，内容是用户id，优先级等隐私信息，设置合适的过期时间，参考腾讯7200s。

客户端每次携带token请求数据，如果过期则重新请求。



## think PHP中.env的使用

为了保护自己的mysql账号密码，最初我的选择是将database.php加入到gitignore中，但是这样对其他人来说总是不完整的，于是使用了.env文件作为配置文件，在其中放入账号密码，然后将.env文件放入gitignore中就完了（env文件要放到项目文件夹下，官方文档的说法是应用根目录，多少有一些迷惑行为。毕竟APP_PATH的定义是application），操作的过程遇到了很多问题，thinkphp框架提供了Env类的get方法来操作配置文件，看他的源代码其实只是对于`getenv`函数的一个封装，发现在操作配置文件的时候都会加入一个常量`ENV_PREFIX`，在base.php中该常量定义为`PHP_`，同时也在base.php文件中发现在加载环境变量配置文件的时候每一项都会主动加上一个`ENV_PREFIX`，为了简化自己的代码，而且自己.env里面配置的环境变量主要是mysql的信息，并不是很多也不会出错，所以放弃使用Env类，并且将base.php处理配置文件时的前缀`ENV_PREFIX`全部去掉，这样就能直接使用`getenv`函数直接获取.env文件下的配置项而不用自己加前缀。（其实用Env类是一个不错的选择，但是他每次都会检查app_debug和app_trace，对我来说感觉有点多余。）

[Think PHP官方文档]: https://www.kancloud.cn/manual/thinkphp5/189989	"环境变量配置"



## JS中的base64编码

js中支持base64加解密，通过`window.btoa()`函数进行base64编码，`window.atob()`进行解码。



## JSEncrypt+PHP 实现RSA加密解密

RSA是一种非对称加密算法，通信双方分别持有公钥和私钥，公钥和私钥都可以加密，而且每次加密的结果都不相同。同样，自己加密的信息对方都可以解密，这样就大幅度提高通信安全（当然能用HTTPS更好）

[JSEncrypt](https://github.com/travist/jsencrypt)是一个用于执行OpenSSL RSA加密，解密和密钥生成的Javascript库，我们用它来给前端数据加密，然后传输到后端。

前端代码，Vue

```js
login() {
      this.$refs.loginForm.validate(async valid => {
        if (!valid) return
        this.$http.get('/key').then(async res => {//获取公钥
          if (res.status === 200) {
            const jse = new this.$jse()
            const PublicKeyEncode = res.data.key //公钥
            const PublicKey = window.atob(PublicKeyEncode)//atob是base64解码
            // console.log(PublicKey)
            jse.setPublicKey(PublicKey)//设置公钥
            const psw = jse.encrypt(this.form.psw)//使用公钥加密，加密的结果已base64编码
            // console.log(psw)
            const result = await this.$http.post('/token', { name: this.form.name, psw: psw })
            // console.log(result)
            if (result.status !== 200) {
              return this.$message.error('登陆失败！')
            } else {
              this.$message.success('登陆成功！')
              window.sessionStorage.setItem('token', result.data)
              this.$router.push('/home')
            }
          } else {
            this.$message.error('获取公钥失败，请联系管理员')
          }
        })
      })
    }
```

后端tp5代码-RSA类

```php
<?php
namespace app\lib;
class RSADecrypt
{
    private $_config = [
        'public_key' => '',
        'private_key' => '',
    ];
    private $private_path;
    private $public_path;
    public function __construct() {
        $this->private_path = getenv('PRIVATE');
        $this->public_path = getenv('PUBLIC');
        $this->_config['private_key'] = $this->_getContents($this->private_path);
        $this->_config['public_key'] = $this->_getContents($this->public_path);
    }

    /**
     * @uses 获取文件内容
     * @param $file_path string
     * @return bool|string
     */
    private function _getContents($file_path) {
        file_exists($file_path) or die ('密钥或公钥的文件路径错误');
        return file_get_contents($file_path);
    }

    /**     
     * @uses 获取公钥
     * @return bool|resource     
     */    
    public function _getPublicKey() {        
        $public_key = $this->_config['public_key'];
        return openssl_pkey_get_public($public_key);
    }
    public function getPublicKey(){
        $public_key = $this->_config['public_key'];
        return $public_key;
    }

    /**     
     * @uses 私钥加密
     * @param string $data     
     * @return null|string     
     */    
    public function privEncrypt($data = '') {        
        if (!is_string($data)) {
            return null;       
        }
        return openssl_private_encrypt($data, $encrypted, $this->_getPrivateKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * @uses 私钥解密     
     * @param string $encrypted     
     * @return null     
     */    
    public function privDecrypt($encrypted = '') {        
        if (!is_string($encrypted)) {
            return null;        
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->_getPrivateKey())) ? $decrypted : $decrypted;
    }    
}
```

后端tp5代码-获取公钥

```php
class PublicKey{
    public function get(){
        $decrypt = new RSADecrypt();
        $publicKey = $decrypt->getPublicKey();
        if(!$publicKey){
            throw new KeyErrorException();
        }
        return [
            'key' =>base64_encode($publicKey)//含\n等特殊字符，base64编码后发送方便处理
        ];
    }    
}
```

后端tp5代码-私钥解码并返回token

```php
//model
public function getToken($username,$psw)
    {
        $rsa = new RSADecrypt();
        $psw = $rsa->privDecrypt($psw);
        $result = self::where('username','=',$username)->where('psw','=',md5($psw))->find();
```

第一次用JSEncrypt，不知道前端加密后已经base64编码，又base64编码一遍，导致后端就无法解码。其实也是没有好好看代码，从后端私钥加密代码也可以看的出来，加密后返回的是base64编码的数据。



TODO：

- [ ] 学生界面可以看到设备评价

- [ ] 前端登陆密码的加密传输算法 考虑用rsa