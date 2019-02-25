<?php
namespace api\models;

use Exception;
use Yii;

class WeChat
{
    // 小程序appID
    protected $appID;
    // 小程序秘钥
    protected $secret;
    // 授权url
    protected $loginUrl;
    // getAccessToken url
    protected $getAccessTokenUrl;
    // 小程序参数
    protected $littleBeeParams;
    // 生成小程序码url
    protected $createMiniCodeUrl;
    
    // 构造函数中赋值成员变量
    public function __construct($code = null)
    {
        $this->littleBeeParams = Yii::$app->params['littleBeeParams'];
        
        $this->appID     = $this->littleBeeParams['appid'];
        $this->secret    = $this->littleBeeParams['secret'];
        
        $this->loginUrl  = sprintf($this->littleBeeParams['loginUrl'], $this->appID, $this->secret, $code);
        $this->getAccessTokenUrl = sprintf($this->littleBeeParams['getAccessTokenUrl'], $this->appID, $this->secret);
    }

    /**
     * 获取用户的令牌方法  
     * @throws Exception
     */
    public function getOpenID()
    {
        // curl
        $result = Tools::curl($this->loginUrl, array("Accept-Charset: utf-8"));

        // 将返回的json处理成数组
        $wxResult = json_decode($result, true);
        
        // 判空
        if (empty($wxResult)) 
            throw new Exception('获取session_key, openID时异常，微信内部错误');
        else 
        {
            // 判断返回的结果中是否有错误码
            if (isset($wxResult['errcode'])) 
                // 如果有错误码，调用抛出错误方法
                $this->_throwWxError($wxResult);
            else 
                // 没有错误码，返回openID
                return $this->_grantOpenID($wxResult);
        }
    }
    
    /**
     * 获取access_token
     */
    public function getAccessToken()
    {
        // 2019-01-11 停用session设置accessToken
        /* $session     = Yii::$app->session;
        $sessionName = "weChatAccessToken";
        
        // 检查session是否开启, 如果没有开启则开启session
        if ($session->isActive) $session->open();
        // 获取session
        $sessionAccessToken = $session->get($sessionName);
        
        // session
        if($sessionAccessToken)
            $accessToken = $this->sessionGetAccessToken($session, $sessionAccessToken, $sessionName);
        // 请求获取
        else
            $accessToken = $this->curlGetAccessToken($session, $sessionName);
        
        // 关闭session
        $session->close(); */
        
        $accessToken = $this->fileGetAccessToken();
        
        return $accessToken;
    }
    
    /**
     * 获取小程序码
     */
    public function getMiniProgramCode($teamID, $page, $data)
    {
        // 获取accessToken
        $accessToken = $this->getAccessToken();
        
        $this->createMiniCodeUrl = sprintf($this->littleBeeParams['createMiniCode'], $accessToken);
        
        $httpRes = Tools::curl($this->createMiniCodeUrl, array(), true, json_encode($data, JSON_UNESCAPED_SLASHES));
        
        Tools::log("createMiniCodeUrl httpRes: ".$httpRes, "getMiniProgramCode", $teamID);
        
        $decodeHttpRes = json_decode($httpRes, true);
        
        if($decodeHttpRes)
        {
            // 有错
            if(array_key_exists("errcode", $decodeHttpRes))
                throw new \ErrorException($decodeHttpRes['errmsg']);
        }
        
        // 替换 /
        $repPage = str_replace("/", "_", $page);
        
        // 文件名
        $fileName = $teamID."_".$repPage.'.jpg';
        $codePath = './'.$fileName;
        
        file_put_contents($codePath, $httpRes);
        
        // 复制文件
        rename($codePath, "/data/xdqtg888/".$fileName);
        // 本地测试路径
        // rename($codePath, "D:\\Xampp\\htdocs\\dashboard\\littleBee\\".$fileName);
        $finallyPath = Yii::getAlias('@upload').'/'.$fileName;
        
        return $finallyPath;
    }
    
    // 从文件中获取accessToken 2019-01-11 启用
    private function fileGetAccessToken()
    {
        if(file_exists(Yii::$app->params['accessTokenFile']))
        {
            $accessToken = file_get_contents(Yii::$app->params['accessTokenFile']);
            
            $expireTime = substr($accessToken, strripos($accessToken, "_") + 1);
            
            // 未过期
            if($expireTime > time())
                $accessToken = substr($accessToken, 0, strripos($accessToken, "_"));
            // 重新请求
            else
                $accessToken = $this->curlGetAccessToken();
        }
        else 
            $accessToken = $this->curlGetAccessToken();
        
        return $accessToken;
    }
    
    // 从session获取access_token 2019-01-11 停用session获取accessToken, 修改为文件中获取
    private function sessionGetAccessToken($session, $sessionAccessToken, $sessionName)
    {
        /* Tools::log($sessionAccessToken, "sessionGetAccessToken: ", null);
        
        $expireTime = substr($sessionAccessToken, strripos($sessionAccessToken, "_") + 1);
        
        // 未过期
        if($expireTime > time())
            $accessToken = substr($sessionAccessToken, 0, strripos($sessionAccessToken, "_"));
        // 重新请求
        else 
            $accessToken = $this->curlGetAccessToken();
            
        return $accessToken; */
    }
    
    // 从微信获取access_token
    private function curlGetAccessToken()
    {
        $httpRes = Tools::curl($this->getAccessTokenUrl);
        $httpRes = json_decode($httpRes, true);
        
        // 生成access_token成功
        if(array_key_exists("access_token", $httpRes))
        {
            // 过期时间
            $expireTime = time() + $httpRes["expires_in"];
            $accessToken= $httpRes["access_token"]."_".$expireTime;
            
            Tools::log($accessToken, "curlGetAccessToken: ", null);
            
            /* // 设置session 2019-01-11 停止session设置accessToken, 修改为写入文件
            $session->set($sessionName, $accessToken); */
            
            file_put_contents(Yii::$app->params['accessTokenFile'], $accessToken);
            
            return $httpRes["access_token"];
        }
        // 错误
        else 
            Tools::log($httpRes, "WeChat curlGetAccessToken", null);
        
        return false;
    }

    /**
     * 微信获取open_id失败，抛出异常方法 
     * @param $wxResult 
     * @throws WxException
     */
    private function _throwWxError($wxResult)
    {
        throw new WxException([
            'message'   => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }

    /**
     * 获取openid
     *
     * @param
     *            $wxResult
     * @return string
     * @throws Exception
     */
    private function _grantOpenID($wxResult)
    {
        // 拿到open_id
        $openID     = $wxResult['openid'];
        $sessionKey = $wxResult['session_key'];

        Tools::log($wxResult, "WeChat jscode2session wxResult:", null);

        return array($openID, $sessionKey);
    }
}

// 微信异常类
class WxException extends \Exception
{
    protected $message;  // 错误信息
    protected $errorCode;// 错误码
    
    // 构造器使 message 变为必须被指定的属性
    public function __construct($message)
    {
        $this->message   = $message['message'];
        $this->errorCode = $message['errorCode'];
    }

    // 自定义字符串输出的样式
    public function __toString()
    {
        return __CLASS__ . ": errorCode: {$this->errorCode}, message: {$this->message}\n";
    }
}

