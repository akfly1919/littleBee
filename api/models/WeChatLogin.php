<?php
namespace api\models;

use Exception;
use Yii;

class WeChatLogin
{
    // 小程序appID
    protected $appID;
    // 小程序秘钥
    protected $secret;
    // 授权url
    protected $loginUrl;
    // 小程序参数
    protected $littleBeeParams;
    // 获取用户信息url
    protected $userInfoUrl;
    
    // 构造函数中赋值成员变量
    public function __construct($code = null)
    {
        $this->littleBeeParams = Yii::$app->params['littleBeeParams'];

        $this->appID     = $this->littleBeeParams['appid'];
        $this->secret    = $this->littleBeeParams['secret'];
        
        $this->loginUrl  = sprintf($this->littleBeeParams['wxLoginUrl'], $this->appID, $this->secret, $code);
        $this->userInfoUrl = $this->littleBeeParams['wxUserInfoUrl'];
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
            throw new Exception('获取access_token, openid时异常，微信内部错误');
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

    public function getUserInfo($accessToken, $openId) {
        $userInfoUrl = sprintf($this->userInfoUrl, $accessToken, $openId);

        // curl
        $result = Tools::curl($userInfoUrl, array("Accept-Charset: utf-8"));

        // 将返回的json处理成数组
        $wxResult = json_decode($result, true);

        // 判空
        if (empty($wxResult))
            throw new Exception('获取unionid时异常，微信内部错误');
        else
        {
            // 判断返回的结果中是否有错误码
            if (isset($wxResult['errcode']))
                // 如果有错误码，调用抛出错误方法
                $this->_throwWxError($wxResult);
            else
                // 没有错误码，返回openID
                return $this->_grantUnionID($wxResult);
        }
    }

    /**
     * 微信获取open_id失败，抛出异常方法 
     * @param $wxResult 
     * @throws WxException
     */
    private function _throwWxError($wxResult)
    {
        throw new WxLoginException([
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
        $accessToken = $wxResult['access_token'];

        return array($openID, $accessToken);
    }

    private function _grantUnionID($wxResult)
    {
        // 拿到unionid
        $unionId = $wxResult['unionid'];

        return $unionId;
    }
}

// 微信异常类
class WxLoginException extends \Exception
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

