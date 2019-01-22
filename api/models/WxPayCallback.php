<?php
namespace api\models;

use Exception;
use Yii;

class WxPayCallback
{
    protected $values = array();

    public function SetReturn_code($value)
    {
        $this->values['return_code'] = $value;
    }

    public function GetReturn_code()
    {
        return $this->values['return_code'];
    }

    public function IsReturn_codeSet()
    {
        return array_key_exists('return_code', $this->values);
    }

    public function SetReturn_msg($value)
    {
        $this->values['return_msg'] = $value;
    }

    public function GetReturn_msg()
    {
        return $this->values['return_msg'];
    }

    public function IsReturn_msgSet()
    {
        return array_key_exists('return_msg', $this->values);
    }

    /**
     * 设置签名，详见签名生成算法
     * @param string $value
     **/
    public function SetSign($signType = "MD5", $key = "")
    {
        $sign = $this->MakeSign($signType, $key);
        $this->values['sign'] = $sign;
        return $sign;
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return 值
     **/
    public function GetSign()
    {
        return $this->values['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     * @return true 或 false
     **/
    public function IsSignSet()
    {
        return array_key_exists('sign', $this->values);
    }

    public function FromXml($xml)
    {
        if(!$xml){
            throw new \Exception("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }

    public function CheckSign($key)
    {
        if(!$this->IsSignSet()){
            throw new \Exception("签名错误！");
        }

        $sign = $this->MakeSign($key);
        if($this->GetSign() == $sign){
            //签名正确
            return true;
        }
        throw new \Exception("签名错误！");
    }

    public function MakeSign($key)
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if(strlen($this->GetSign()) <= 32){
            $string = md5($string);
        } else {
            $string = hash_hmac("sha256",$string ,$key);
        }
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    public function ToUrlParams()
    {
        $buff = "";
        foreach ($this->values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function ToXml()
    {
        if(!is_array($this->values) || count($this->values) <= 0)
        {
            throw new WxPayException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($this->values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public static function notify($key, $callback)
    {
        //获取通知的数据
        $xml = file_get_contents("php://input");
        $result = self::Init($key, $xml);

        return self::NotifyView($callback, $result->values);
    }

    public static function Init($key, $xml)
    {
        $obj = new self();
        $obj->FromXml($xml);
        //失败则直接返回失败
        $obj->CheckSign($key);
        return $obj;
    }

    public static function NotifyView($callback, $data)
    {
        $result = call_user_func_array($callback, array($data));
        $obj = new self();
        if($result == true){
            $obj->SetReturn_code("SUCCESS");
            $obj->SetReturn_msg("OK");
        } else {
            $obj->SetReturn_code("FAIL");
            $obj->SetReturn_msg("ERROR");
        }
        return $obj->ToXml();
    }


}