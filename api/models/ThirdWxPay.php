<?php
namespace api\models;

use Exception;
use Yii;

class ThirdWxPay
{
    protected $thirdWxPayParams;

    protected $actionUrl;

    protected $mchId;

    protected $reqKey;

    protected $resKey;

    protected $channelId;

    protected $currency;

    protected $notifyUrl;

    protected $signType;

    protected $values = array();

    public function __construct()
    {
        $this->thirdWxPayParams = Yii::$app->params['thirdWxPayParams'];

        $this->actionUrl    = $this->thirdWxPayParams['actionUrl'];
        $this->mchId        = $this->thirdWxPayParams['mchId'];
        $this->reqKey        = $this->thirdWxPayParams['reqKey'];
        $this->resKey        = $this->thirdWxPayParams['resKey'];
        $this->channelId    = "WX_JSAPI_S";
        $this->currency     = "cny";
        $this->notifyUrl    = "https://zxgybj.com/api/buy-member-callback";
        $this->signType     = "HMAC-SHA256";
    }

    public function payWxShareAddReceiverMch($actionName, $teamId) {
        return $this->payWxShareAddReceiver($actionName, $teamId, $this->mchId, "中鑫国源(北京)金融服务外包有限公司", 1);
    }

    public function payWxShareAddReceiver($actionName, $teamId, $account, $name, $type) {
        $this->values = array();
        $this->values['mchId'] = $this->mchId;
        $this->values['channelId'] = $this->channelId;
        $extra = array();
        $extra['receiver'] = array(
            'account' => $account,
            'name' => $name,
            'type' => $type,
        );
        $this->values['extra'] = json_encode($extra, JSON_UNESCAPED_UNICODE);
        $this->values['signType'] = "HMAC-SHA256";
        $this->values['sign'] = $this->makeSign($this->values, $this->values['signType'], $this->reqKey);
        try {
            $resultJson = self::doPost(json_encode($this->values, JSON_UNESCAPED_UNICODE), $this->actionUrl."/pay/ps_add_receiver");
            $result = json_decode($resultJson, true);
            if ($result['retCode'] == 'SUCCESS') {
                if ($result['retCode'] == 'SUCCESS') {
                    return $result;
                }
            }
            return false;
        } catch(\Exception $exception) {
            Tools::log(['funcName' => 'payWxShareAddReceiver', $exception->getCode() => $exception->getMessage()], $actionName, $teamId);
        }
        return false;
    }

    public function payWxOrderWithShare($actionName, $teamId, $mchOrderNo, $amount, $clientIp, $body, $openId, $shareAmount, $toOpenId) {
        $this->values = array();
        $this->values['mchId'] = $this->mchId;
        $this->values['mchOrderNo'] = $mchOrderNo;
        $this->values['channelId'] = $this->channelId;
        $this->values['amount'] = $amount;
        $this->values['currency'] = $this->currency;
        $this->values['clientIp'] = $clientIp;
        $this->values['body'] = $body;
        $this->values['notifyUrl'] = $this->notifyUrl;
        $this->values['param1'] = "";
        $this->values['param2'] = "";
        $this->values['share'] = 1;
        $extra = array();
        $extra['openId'] = $openId;
        $extra['receivers'] = array(
            array(
                'account' => $toOpenId,
                'amount' => "".$shareAmount,
                'description' => '推荐奖励',
                'type' => 4,
            )
        );

        $this->values['extra'] = json_encode($extra, JSON_UNESCAPED_UNICODE);
        $this->values['signType'] = "HMAC-SHA256";
        $this->values['sign'] = $this->makeSign($this->values, $this->values['signType'], $this->reqKey);

        try {
            $resultJson = self::doPost(json_encode($this->values, JSON_UNESCAPED_UNICODE), $this->actionUrl."/pay/create_order");
            $result = json_decode($resultJson, true);

            if ($result['retCode'] == 'SUCCESS') {
                if ($result['retCode'] == 'SUCCESS') {
                    return $result;
                }
            }
            return false;
        } catch(\Exception $exception) {
            Tools::log(['funcName' => 'payWxOrderWithShare', $exception->getCode() => $exception->getMessage()], $actionName, $teamId);
        }
        return false;
    }

    public function payWxOrder($actionName, $teamId, $mchOrderNo, $amount, $clientIp, $body, $openId) {
        $this->values['mchId'] = $this->mchId;
        $this->values['mchOrderNo'] = $mchOrderNo;
        $this->values['channelId'] = $this->channelId;
        $this->values['amount'] = $amount;
        $this->values['currency'] = $this->currency;
        $this->values['clientIp'] = $clientIp;
        $this->values['body'] = $body;
        $this->values['notifyUrl'] = $this->notifyUrl;
        $this->values['param1'] = "";
        $this->values['param2'] = "";
        $this->values['share'] = 0;
        $extra = array();
        $extra['openId'] = $openId;
        $this->values['extra'] = json_encode($extra, JSON_UNESCAPED_UNICODE);
        $this->values['signType'] = "HMAC-SHA256";
        $this->values['sign'] = $this->makeSign($this->values, $this->values['signType'], $this->reqKey);
        try {
            $resultJson = self::doPost(json_encode($this->values), $this->actionUrl."/pay/create_order");
            $result = json_decode($resultJson, true);
            print_r($result);
            if ($result['retCode'] == 'SUCCESS') {
                if ($result['retCode'] == 'SUCCESS') {
                    return $result;
                }
            }
            return false;
        } catch(\Exception $exception) {
            Tools::log(['funcName' => 'payWxOrder', $exception->getCode() => $exception->getMessage()], $actionName, $teamId);
        }
        return false;
    }

    private function makeSign($arr, $signType, $key)
    {
        //签名步骤一：按字典序排序参数
        ksort($arr);
        //签名步骤二：在string后加入KEY
        $string = "";
        foreach ($arr as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $string .= $k . "=" . $v . "&";
            }
        }
        $string = trim($string, "&");
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if($signType == "MD5"){
            $string = md5($string);
        } else if($signType == "HMAC-SHA256") {
            $string = hash_hmac("sha256",$string ,$key);
        } else {
            throw new \Exception("签名类型不支持！");
        }
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    private static function doPost($reqData, $url, $useCert = false, $second = 30)
    {
        $header = array();
        $header[] = 'Accept:application/json';
        $header[] = 'Content-Type:application/json;charset=utf-8';

        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            $sslCertPath = "../config/cert/apiclient_cert.pem";
            $sslKeyPath = "../config/cert/apiclient_key.pem";
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $reqData);
        //运行curl
        $data = curl_exec($ch);

        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch).":".curl_error($ch);
            curl_close($ch);
            throw new \Exception("curl出错，错误信息[".$error."]");
        }
    }

}