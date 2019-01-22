<?php
namespace api\models;

use Exception;

class Fangsl{
    
    public function __construct()
    {
        $this->privateKey = "MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAJzPC6qCNQ/Hj5uHAcFUPnHFbQcnploiPfWhnVwt71zuly8HNc/Bp4lrW5iD7I1MUUOkyoLDVTGTvVYiwV/jC3Dvaqx5nRuICsMAqDAIauFD0IASpDJR6AYetU2IEwOMLD8tGUwAN0x5PmHeBDwJ0lzgbQvHPSCfjiA5EqOfLelvAgMBAAECgYEAhlCy5XJykPmXANk7n6XRdxJsuVw1Ga+K8wNkDrkp9qhHx3idlz+BiivzYUhfLwjX8uEMtcUrDDRIUaeji8am3NYyih8TbQyhMuKJUxQynGRyn5Io22xlYItmIcb7CqVd4jLk+soA6swiqYZvbM92utqQN3CAxubhcMbLfKZ0mgECQQDQk4qalvae7Ywb98zawKQc4VvAJr78rPrU6hnv3AqJBfOpzG8XzMHEg8XF4CS9tfDUIzZnKls92yFHmJc5f+nPAkEAwHZOMMG4VmNfhKWix3y0ozxSO3epKFpllGg8i8nhsqqCvo1WsTNk9a4SAHWULt9EKpd1tsrBssyJEPEmLoMOYQJBAJdFWnHx6R2stUAXgXhp3NyhSTTcJQoGvsiqnHMMyItGSMkuXqgJNcM7urVfceYxTV/dxfgejRwYkFNnxM3MCpsCQERPeU8q4t+eo79z/sOpSoAJn/JFdX/CHf2/xYWkIPyGMqOpjNSWNkZRA0iwcuj0C8DGQ1yeuINav2eJABFQKOECQFQKxLfatO6UfWJbdivs953Q7s6qS/1M45/1IpzCebHCuAzBCpntvB2yx5PIoEP2khfYgYM61dm7EqcAe5EOLfo=";
    }
    
    // 房司令
    public function fslInterface()
    { 
        $bizDataOne = $this->fslParamsOne();
        $bizDataTwo = $this->fslParamsTwo();
    }
    
    // 暂停
    public function fslParamsOne()
    {
        $client   = Client::findOne($this->clientID);
        if($client)
        {
            $phone    = str_replace(substr($client->phone, 7, 11), "****", $client->phone);
            $identity = str_replace(substr($client->sfznumber, 13, 18), "*****", $client->sfznumber);
            
            $params   = [];
            $params['user_name']   = $client->name;
            $params['user_mobile'] = $phone;
            $params['id_card']     = $identity;
            
            return json_encode($params);
        }
        
        throw new \ErrorException('clientID error');
    }
    
    // 处理方司令接口传参格式
    public function fslParamsTwo()
    {
        $client   = Client::findOne($this->clientID);
        if($client)
        {
            $params   = [];
            $params['mobile']   = $client->phone;
            $params['order_no'] = $client->orderbhid;
            
            return json_encode($params);
        }
        
        throw new \ErrorException('clientID error');
    }
    
    public function doPushBindResultTwo($channel, $client, $url, $privateKey) 
    {
            $cardJson = $client;//JSON.toJSONString(client);
            $bizData = "";
            $result = "";
            
            try {
                $params = getPostdata($channel, $privateKey, $cardJson);
                $result = request(params, url);
            } catch (Exception $e) {
                // log.error("youjie pre request error.", e);
            }
            return result.toString();
    }
    
    /**
     * 包装需要提交的参数
     *
     * @param privatekey
     *            私钥
     * @param parambiz_data
     *            9f接口需要的参数
     * @return 业务参数、签名等信息
     */
    private function getPostdata($channel, $privatekey, $parambiz_data) {
        $params = [];
        
        $params['biz_data']  = $parambiz_data;
        $params['sign_type'] = "RSA";
        $params['app_id']    = $channel;
        $params['version']   = "1.0";
        $params['format']    = "json";
        $params['timestamp'] = Tools::getMillisecond();
        
        try {
            $sign = RSA.sign($this->formatUrlMap($params), $privatekey, "utf-8");
            
        } catch (Exception $e) {
            
        }
        
        $params['sign'] = $sign;
        
        return JSON.toJSONString(params);
    }
    
    /**
     *
     * 方法用途: 对所有传入参数按照字段名的 ASCII 码从小到大排序（字典序），并且生成url参数串<br>
     * 实现步骤: <br>
     *
     * @param paraMap
     *            要排序的Map对象
     * @return
     */
    /* public static function formatUrlMap($paramMap) 
    {
        String buff = "";
        Map<String, Object> tmpMap = paraMap;
        try {
            List<Map.Entry<String, Object>> infoIds = new ArrayList<Map.Entry<String, Object>>(
                tmpMap.entrySet());
            // 对所有传入参数按照字段名的 ASCII 码从小到大排序（字典序）
            Collections.sort(infoIds, new Comparator<Map.Entry<String, Object>>() {
                    public int compare(Map.Entry<String, Object> o1,
                        Map.Entry<String, Object> o2) {
                            return (o1.getKey()).toString().compareTo(
                                o2.getKey());
                    }
                });
            // 构造URL 键值对的格式
            StringBuilder buf = new StringBuilder();
            for (Map.Entry<String, Object> item : infoIds) {
                if (ignoreParamList.contains(item.getKey()))
                    continue;
                    if (StringUtils.isNotBlank(item.getKey())) {
                        String key = item.getKey();
                        Object val = item.getValue();
                        buf.append(key + "=" + val);
                        buf.append("&");
                    }
                    
            }
            buff = buf.toString();
            if (buff.isEmpty() == false) {
                buff = buff.substring(0, buff.length() - 1);
            }
        } catch (Exception e) {
            return null;
        }
        
        System.out.println("buff-------------" + buff);
        
        return buff;
    } */
    
    /**
     * 签名字符串
     *
     * @param text
     *            需要签名的字符串
     * @param privateKey
     *            私钥(BASE64编码)
     *
     * @param charset
     *            编码格式
     * @return 签名结果(BASE64编码)
     */
    /* public static String sign($text, $privateKey, $charset) {
        
        try {
            byte[] keyBytes = Base64.decodeBase64($privateKey);
            System.out.println("keyBytes--------" + keyBytes.toString());
            
            PKCS8EncodedKeySpec pkcs8KeySpec = new PKCS8EncodedKeySpec(keyBytes);
            System.out
            .println("pkcs8KeySpec--------" + pkcs8KeySpec.toString());
            
            KeyFactory keyFactory = KeyFactory.getInstance(KEY_ALGORITHM);
            System.out.println("keyFactory--------" + keyFactory.toString());
            
            PrivateKey privateK = keyFactory.generatePrivate(pkcs8KeySpec);
            
            System.out.println("privateK--------" + privateK.toString());
            Signature signature = Signature.getInstance(SIGNATURE_ALGORITHM);
            System.out.println("signature--------" + signature.toString());
            signature.initSign(privateK);
            signature.update(getContentBytes(text, charset));
            byte[] result = signature.sign();
            System.out.println("result------" + result);
            return Base64.encodeBase64String(result);
        } catch (Exception e) {
            e.printStackTrace();
        }
        
        return null;
        
    } */
    
    /**
     * 发送请求
     *
     * @param params
     *            请求参数
     * @param url
     *            请求地址
     * @return 接口响应的内容
     * @throws Exception
     *             Exception
     */
    /* @SuppressWarnings("all")
    private String request(Map<String, Object> params, String url)
    throws Exception {
        Map<String, Object> mapParams = (Map<String, Object>) JSON.parse(params);
        System.out.println("mapParams" + mapParams);
        
        Map<String, String> header = new HashMap<String, String>();
        header.put("Content-Type", "application/json");
        //HttpClientUtil qo = new HttpClientUtil();
        String ret;
        System.out.println("params:---------"+params);
        
        RespResult rr = HttpUtil.post(url, header, params, true, 40000);
        System.out.println("rr111111111:"+rr.toString());
        if (rr != null && rr.getStatus() == HttpStatus.SC_OK) {
            return rr.getRespMsg();
        } else {
            throw new Exception("第三方渠道响应异常," + (rr == null ? "无返回信息"
                : ("状态码:" + rr.getStatus() + ",响应内容:" + rr.getRespMsg())));
        }
        
        
    } */
}