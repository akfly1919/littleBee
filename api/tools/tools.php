<?php
namespace api\tools;


/**
 * 自定义工具类
 */
class Tools
{
    /**
     * 获取毫秒级时间戳
     * @return number
     */
    public static function getMillisecond() 
    {
        list($t1, $t2) = explode(' ', microtime());
        
        return (float)sprintf('%.0f',(floatval($t1) + floatval($t2)) * 1000) + mt_rand(1000, 8999);
    }
    
    /**
     * curl 请求
     * @param string  $requestUrl 请求url
     * @param array   $headers    请求头
     * @param boolean $isPost     是否为post请求
     * @param array   $params     请求参数
     * @param array   $buildQuery 建立查询参数
     * @return mixed
     */
    public static function curl($requestUrl, $headers = array(), $isPost = false, $params = array(), $buildQuery = array())
    {
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);       // 请求的url
        curl_setopt($ch, CURLOPT_HEADER, false);          // 设置头文件的信息作为数据流 1:输出 0:不输出
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);      // 超时时间
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent); // user_agent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // 设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 跳过SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 跳过SSL验证
        
        // 是否为post提交
        if ($isPost && $params) 
        {
            curl_setopt($ch, CURLOPT_POST, true);
            
            if ($buildQuery) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            else             curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        
        if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $output = curl_exec($ch); // 执行命令
        curl_close($ch);
        
        return $output;
    }

    public static function urlsafe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array(
            '+',
            '/'
        ), array(
            '-',
            '_'
        ), $data);
        return $data;
    }

    public static function randomkeys($length, $parent = "1234567890")
    {
        $key = "";
        for ($i = 0; $i < $length; $i ++) {
            $key .= $parent{mt_rand(0, 9)}; // 生成php随机数
        }
        return $key;
    }
    
    /**
	 * 自定义日志
	 *
	 * @param  mixed  $data      写入日志文件的内容
	 * @param  string $action    当前日志action
	 * @param  bool   $sign      是否删除日志
	 * @param  string $log_path  日志路径
	 */
	public static function log($data, $action, $teamID, $sign = false, $log_path = '/data/www/littleBee/api/log.log')
	{
	    if($sign && file_exists($log_path)) unlink($log_path);
	    
	    $data = date('Y-m-d H:i:s', time()).' teamID: '.$teamID.' action: '.$action.' : '.json_encode($data);
	    
	    file_put_contents($log_path, print_r($data, true).PHP_EOL,FILE_APPEND);
	}
	
	/**
	 * 加、解密函数
	 * @param string $string    要被加密或解密的字符串
	 * @param string $operation 操作: 'E' 是代表加密  ,  'D' 是代表解密
	 * @param string $key       根据业务定义的key
	 *
	 * @return string 加密或解密后的字符串
	 */
	public static function encryptLittleBee($string, $operation, $key)
	{
	    $key     = md5($key);
	    $key_len = strlen($key);
	    
	    $string  = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key),0,8) . $string;
	    $str_len = strlen($string);
	    
	    $rndkey = $box = array();
	    $result = '';
	    
	    for ($i = 0; $i <= 255; $i++)
	    {
	        $rndkey[$i] = ord($key[$i % $key_len]);
	        $box[$i]    = $i;
	    }
	    
	    for ($j = $i = 0; $i < 256; $i++)
	    {
	        $j = ($j + $box[$i] + $rndkey[$i])%256;
	        $tmp = $box[$i];
	        $box[$i] = $box[$j];
	        $box[$j] = $tmp;
	    }
	    
	    for ($a = $j = $i = 0 ;$i < $str_len; $i++)
	    {
	        $a = ($a + 1) % 256;
	        $j = ($j + $box[$a]) % 256;
	        $tmp = $box[$a];
	        $box[$a] = $box[$j];
	        $box[$j] = $tmp;
	        $result.= chr(ord($string[$i])^($box[($box[$a] + $box[$j]) % 256]));
	    }
	    
	    if ($operation == 'D')
	    {
	        if (substr($result,0,8) == substr(md5(substr($result,8) . $key),0,8)) return substr($result,8); else return '';
	    }
	    else
	    {
	        return str_replace('=','',base64_encode($result));
	    }
	}
}
