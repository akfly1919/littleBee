<?php
namespace api\models;

use Yii;
use yii\web\UnauthorizedHttpException;

class Filters{
    
    const TYPE_G = 'get';
    const TYPE_P = 'save';
    
    const BOOL_T = true;
    const BOOL_F = false;
    
    private $checkTeam;   // 检查team是否存在 true=检查  false=不检查
    private $controlPreg; // true=开启正则
    private $controlType; // 类型控制
    private $apiToke;
    private $phone;       // 手机号码
    
    public $paramName;  // 参数名
    public $miniProgram;
    public $request;    // 请求
    
    private $NullParam  = null;
    private $NumberPreg = '/^[0-9]+$/';
    private $StringPreg = '/^[a-z0-9A-Z]+$/';
    private $IDNumPreg  = '/(^\d{15}$)|(^\d{17}([0-9]|X)$)/isu';
    private $phonePreg  = '/^1[356789]\d{9}$/';
    private $TokenPreg  = '/^[A-Za-z0-9\=]+$/';
    
    private $randomStr;
    private $msgCodeNameSuffix = '_msg_code';
    
    // 自定义错误
    private $errorCustom = ['code' => 10000, 'message' => ""];
    
    // 构造
    public function __construct()
    {
        $this->request     = Yii::$app->request;
        $this->miniProgram = new MiniProgram();
    }
    
    // set
    public function setRandomStr($randomStr)
    {
        $this->randomStr = $randomStr;
    }
    
    public function setControlPreg($controlPreg = false)
    {
        $this->controlPreg = $controlPreg;
    }
    
    public function setControlType($controlType)
    {
        $this->controlType = $controlType;
    }
    
    /**
     * 参数操作
     * @param string $method 请求方式
     */
    public function operationParams()
    {
        $this->apiToken = $this->request->headers->get('token') ? $this->request->headers->get('token') : null;
        
        if(is_null($this->apiToken))
            throw new UnauthorizedHttpException('token 非法...');
        // 从token中获取teamID 
        else 
            $this->getTeamIDFromToken();
        
        return $this->apiToken;
    }
    
    /**
     * 从token中获取teamID
     */
    private function getTeamIDFromToken()
    {
        $decodeApiToken = base64_decode(Yii::$app->getSecurity()->validateData($this->apiToken, Team::encryptKey));
        
        $this->miniProgram->teamID = substr($decodeApiToken, strrpos($decodeApiToken, '_') + 1); // 获取下来teamID
    }
    
    // 自定义错误
    public function errorCustom($e)
    {
        // 如果code不为空，则覆盖code
        if($e->getCode())
            $this->errorCustom['code'] = $e->getCode();
        
        $this->errorCustom['message']  = $e->getMessage();
            
        return $this->errorCustom;
    }
    
    /**
     * 获取用户微信昵称
     */
    public function teamNickName()
    {
        $paramName = 'nickName';
        // 昵称
        $name = $this->request->post($paramName) ? htmlspecialchars(trim($this->request->post($paramName))) : $this->NullParam;
        
        if(!is_null($name))
            return $name;
        
        throw new \InvalidArgumentException('请获取昵称');
    }
    
    /**
     * 获取用户微信头像
     */
    public function teamHeaderImg()
    {
        $paramHeaderImg = 'headerImg';
        // 头像
        $headerImg = $this->request->post($paramHeaderImg) ? htmlspecialchars(trim($this->request->post($paramHeaderImg))) : $this->NullParam;
        
        if(!is_null($headerImg))
            return $headerImg;
            
        throw new \InvalidArgumentException('请获取头像');
    }
    
    /**
     * 保存team姓名时检查
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function teamName()
    {
        return $this->checkName(self::BOOL_T);
    }
    
    /**
     * 保存client姓名时检查, 不检查team
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function clientName()
    {
        return $this->checkName(self::BOOL_F);
    }
    
    private function checkName($checkTeam)
    {
        $paramName = 'name';
        // 姓名
        $name = $this->request->post($paramName) ? htmlspecialchars(trim($this->request->post($paramName))) : $this->NullParam;
        
        if(!is_null($name))
        {
            // 姓名是否存在
            $checkVariableRs = $this->checkVariable($name, $paramName, self::BOOL_F, $this->NullParam, $checkTeam);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $name;
        }
        
        throw new \InvalidArgumentException('请输入姓名');
    }
    
    /**
     * 保存team身份证时检查
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function teamIdentity()
    {
        return $this->checkIdentity(self::BOOL_T);
    }
    
    /**
     * 保存client身份证时检查
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function clientIdentity()
    {
        return $this->checkIdentity(self::BOOL_F);
    }
    
    // 检查身份证号码
    private function checkIdentity($checkTeam)
    {
        // 身份证号码
        $identity = $this->request->post('identity') ? htmlspecialchars(trim($this->request->post('identity'))) : $this->NullParam;
        
        if(!is_null($identity))
        {
            // 身份证号码是否正确/身份证号码是否存在
            $checkVariableRs = $this->checkVariable($identity, 'sfznumber', self::BOOL_T, $this->IDNumPreg, $checkTeam);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $identity;
        }
        
        throw new \InvalidArgumentException('请输入身份证号码');
    }
    
    /**
     * 保存team银行卡号时检查
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function teamBankCode()
    {
        $paramName = 'bankCode';
        // 身份证号码
        $bankCode  = $this->request->post($paramName) ? htmlspecialchars(trim($this->request->post($paramName))) : $this->NullParam;
        
        if(!is_null($bankCode))
        {
            // 身份证号码是否正确/身份证号码是否存在
            $checkVariableRs = $this->checkVariable($bankCode, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $bankCode;
        }
        
        throw new \InvalidArgumentException('请输入银行卡号');
    }
    
    /**
     * 1.获取手机号时检查手机号格式是否正确
     * 2.保存team手机号时检查
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function teamPhone($actionName)
    {
        // 手机号码
        $phone = $this->request->post('phone') ? htmlspecialchars(trim($this->request->post('phone'))) : null;
        
        if(!is_null($phone))
        {
            $checkAlready = true;
            
            // 不判断手机号存在
            if(in_array($actionName, Yii::$app->params['phoneOptional'])) $checkAlready = false;
            
            // 手机号码是否存在/手机号码格式是否正确
            $checkVariableRs = $this->checkVariable($phone, 'phone', self::BOOL_T, $this->phonePreg, $checkAlready);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            $this->phone = $phone;
                
            return $phone;
        }
        
        throw new \ErrorException('请输入手机号码');
    }
    
    /**
     * 获取用户上级ID
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getCsjTeamID()
    {
        $sjTeamID = $this->controlType === self::TYPE_G ? $this->request->get('sjTeamID') : $this->request->post('sjTeamID');
        $sjTeamID = $sjTeamID ? htmlspecialchars(trim($sjTeamID)) : $this->NullParam;
        
        if(!is_null($sjTeamID))
        {
            $checkVariableRs = $this->checkVariable($sjTeamID, 'sjTeamID', self::BOOL_T, $this->StringPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $sjTeamID;
        }
        
        throw new \InvalidArgumentException("必须有sjTeamID");
    }
    
    /**
     * 验证码
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function msgCode()
    {
        $paramName = 'msgCode';
        // 验证码
        $msgCode   = $this->request->post($paramName) ? htmlspecialchars(trim($this->request->post($paramName))) : $this->NullParam;
        
        if(!is_null($msgCode))
        {
            // 验证码格式是否正确
            $checkVariableRs = $this->checkVariable($msgCode, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            // 检查验证码是否正确
            return $this->checkHashMsgCode($msgCode);
        }
        
        throw new \ErrorException('请输入验证码');
    }
    
    /**
     * 获取分享ID
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getShareID()
    {
        $paramName = 'shareID';
        $shareID = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $shareID = $shareID ? htmlspecialchars(trim($shareID)) : $this->NullParam;
        
        if(!is_null($shareID))
        {
            $checkVariableRs = $this->checkVariable($shareID, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $shareID;
        }
        
        throw new \InvalidArgumentException('请提供分享ID');
    }
    
    /**
     * 获取省份
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getProvince()
    {
        $paramName    = 'provinceCode';
        $provinceCode = $this->request->get($paramName) ? htmlentities(trim($this->request->get($paramName))) : $this->NullParam;
        
        if(!is_null($provinceCode))
        {
            $checkVariableRs = $this->checkVariable($provinceCode, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
        }
        
        return $provinceCode;
    }
    
    /**
     * 获取城市
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getCity()
    {
        $cityCode = $this->request->get('cityCode') ? htmlentities(trim($this->request->get('cityCode'))) : $this->NullParam;
        
        if(!is_null($cityCode))
        {
            $checkVariableRs = $this->checkVariable($cityCode, 'cityCode', true, $this->NumberPreg, false);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
        }
        return $cityCode;
    }
    
    /**
     * 获取limit
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getLimit()
    {
        $limit = $this->request->get('limit')  ? htmlspecialchars(trim($this->request->get('limit'))) : 10;
        
        $checkVariableRs = $this->checkVariable($limit, 'limit', self::BOOL_T, $this->NumberPreg, self::BOOL_F);
        if(is_string($checkVariableRs))
            throw new \ErrorException($checkVariableRs);
            
        return (int)$limit;
    }
    
    /**
     * 获取offset
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getOffset()
    {
        $offset = $this->request->get('offset') ? htmlspecialchars(trim($this->request->get('offset'))): 0;
        
        $checkVariableRs = $this->checkVariable($offset, 'offset', self::BOOL_T, $this->NumberPreg, self::BOOL_F);
        if(is_string($checkVariableRs))
            throw new \ErrorException($checkVariableRs);
            
        return (int)$offset;
    }
    
    /**
     * 获取产品ID
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getProID()
    {
        $paramName = $this->paramName ? $this->paramName : 'proID';
        
        $proID = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $proID = $proID ? htmlspecialchars(trim($proID)) : $this->NullParam;
        
        if(!is_null($proID))
        {
            $checkVariableRs = $this->checkVariable($proID, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $proID;
        }
        
        throw new \InvalidArgumentException('请提供产品ID');
    }
    
    /**
     * 获取产品类型
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getProType()
    {
        $paramName = 'proType';
        
        $proType = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $proType = $proType ? htmlspecialchars(trim($proType)) : $this->NullParam;
        
        if(!is_null($proType))
        {
            $checkVariableRs = $this->checkVariable($proType, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $proType;
        }
        
        return $this->NullParam;
    }
    
    /**
     * 获取用户上级ID
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getTeamID()
    {
        $paramName = 'teamID';
        $teamID = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $teamID = $teamID ? htmlspecialchars(trim($teamID)) : $this->NullParam;
        
        if(!is_null($teamID))
        {
            $checkVariableRs = $this->checkVariable($teamID, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $teamID;
        }
        
        throw new \InvalidArgumentException("teamID非法");
    }
    
    /**
     * 获取文章ID
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getPromotionID()
    {
        $paramName   = 'promotionID';
        $promotionID = $this->request->get($paramName) ? htmlspecialchars(trim($this->request->get($paramName))) : $this->NullParam;
        
        if(!is_null($promotionID))
        {
            $checkVariableRs = $this->checkVariable($promotionID, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $promotionID;
        }
        
        throw new \InvalidArgumentException('请提供文章ID');
    }
    
    /**
     * 获取用户上级ID
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getsjTeamID()
    {
        $paramName= 'sjTeamID';
        $sjTeamID = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $sjTeamID = $sjTeamID ? htmlspecialchars(trim($sjTeamID)) : $this->NullParam;
        
        if(!is_null($sjTeamID))
        {
            $checkVariableRs = $this->checkVariable($sjTeamID, $paramName, self::BOOL_T, $this->StringPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
        }
        
        return $sjTeamID;
    }
    
    /**
     * 获取提现金额
     * @throws \ErrorException
     * @return NULL|string
     */
    public function getMoney()
    {
        $paramName = 'money';
        $money = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $money = ((int) $money && (int) $money != 0 && (int) $money > 0) ? htmlspecialchars(trim($money)) : $this->NullParam;
        
        if(!is_null($money))
        {
            $checkVariableRs = $this->checkVariable($money, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $money;
        }
        
        throw new \InvalidArgumentException("请输入正确的金额");
    }
    
    // 获取咨询内容
    public function getConsultComment()
    {
        // 姓名
        $comment = $this->request->post('comment') ? htmlspecialchars(trim($this->request->post('comment'))) : $this->NullParam;
        
        if(!is_null($comment))
            return $comment;
        
        throw new \ErrorException('请输入想要咨询的内容');
    }
    
    // 行业动态详情
    public function getTradeNewID()
    {
        $paramName  = 'tradeNewID';
        // 姓名
        $tradeNewID = $this->request->get($paramName) ? htmlspecialchars(trim($this->request->get($paramName))) : $this->NullParam;
        
        if(!is_null($tradeNewID))
        {
            // 姓名是否存在
            $checkVariableRs = $this->checkVariable($tradeNewID, $paramName, self::BOOL_T, $this->NumberPreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
                
            return $tradeNewID;
        }
        
        throw new \ErrorException('非法的行业动态ID');
    }
    
    /**
     * 检查是否存在
     * @param string $check
     * @param string $type
     * @return boolean
     */
    private function checkVariable($check, $type, $bool, $preg = '', $needTeam = true)
    {
        $text = $type == 'sfznumber' ? '身份证' : $type;
        
        // 正则验证
        if($bool)
        {
            if(!preg_match($preg, $check))
                return $text.'是非法的';
        }
        
        // 如果需要检测team
        if($needTeam)
        {
            $team = Team::find()->where($type.'=:type')->addParams([':type' => $check])->one();
            
            if($team)
                return $text.'已注册，请直接登录';
        }
        
        return false;
    }
    
    // 验证码hash
    public function msgCodeSetHash($phone)
    {
        $this->miniProgram->randomStr = str_replace("_", "A", Yii::$app->getSecurity()->generateRandomString());
        
        // 验证码
        $msgCode = Tools::randomkeys(4);
        
        $this->miniProgram->randomStr = $this->miniProgram->hashCustomData($msgCode."_".$this->miniProgram->randomStr."_".$phone);
        
        Tools::log("msgCodeSetHash randomStr: ".$this->miniProgram->randomStr, "msgCodeSetHash", null);
        
        $this->miniProgram->randomStr = Tools::encryptLittleBee($this->miniProgram->randomStr, "E", Team::encryptKey);
        
        Tools::log("msgCodeSetHash after encry randomStr: ".$this->miniProgram->randomStr, "msgCodeSetHash", null);
        
        return $msgCode;
    }
    
    /**
     * 检查hash的验证码是否正确
     * @param string $msgCode 提交过来的短信验证码
     * @return boolean
     */
    private function checkHashMsgCode($msgCode)
    {
        Tools::log("checkHashMsgCode randomStr: ".$this->randomStr, "checkHashMsgCode", null);
        
        // 解密整体数据
        $hashMsgCode = Tools::encryptLittleBee($this->randomStr, "D", Team::encryptKey);
        // 验证hash是否正确
        $hashMsgCode = $this->miniProgram->decryptHashCustomData($hashMsgCode);
        
        Tools::log("checkHashMsgCode hashMsgCode: ".$hashMsgCode, "checkHashMsgCode", null);
        
        // 解密手机
        $decryPhone     = substr($hashMsgCode, strrpos($hashMsgCode, '_') + 1);
        // 解密验证码
        $decryptMsgCode = substr($hashMsgCode, 0, strpos($hashMsgCode, '_'));
        
        Tools::log("checkHashMsgCode decryptMsgCode: ".$decryptMsgCode.", decryPhone: ".$decryPhone, "checkHashMsgCode", null);
        Tools::log("checkHashMsgCode msgCode: ".$msgCode.", this->phone".$this->phone, "checkHashMsgCode", null);
        
        $compareRs = ((int)$msgCode === (int)$decryptMsgCode) && ($decryPhone === $this->phone);
        
        Tools::log("checkHashMsgCode compareRs: ".$compareRs.", this->phone".$this->phone, "checkHashMsgCode", null);
        
        return $compareRs ? true : false;
    }
    
    // 检查验证码是否正确  
    // 2018-12-23 弃用
    private function checkMsgCode($msgCode)
    {
        $session     = Yii::$app->session;
        $sessionName = $this->randomStr.$this->msgCodeNameSuffix;
        
        // 检查session是否开启, 如果没有开启则开启session
        if ($session->isActive) $session->open();
        
        $sessionMsgCode = $session->get($sessionName);
        
        // 关闭session
        $session->close();
        
        Tools::log("sessionMsgCode: ". $sessionMsgCode.", msgCode: ". $msgCode, "msgCode", null);
        
        return (int)$msgCode === (int) $sessionMsgCode ? true : false;
    }
    
    // 短信验证码放入session
    // 2018-12-23 弃用
    public function msgCodeSetSession()
    {
        $session = Yii::$app->session;
        $this->miniProgram->randomStr = Yii::$app->getSecurity()->generateRandomString();
        
        $sessionName = $this->miniProgram->randomStr.$this->msgCodeNameSuffix;
        
        $msgCode = Tools::randomkeys(4);
        
        // 检查session是否开启, 如果没有开启则开启session
        if ($session->isActive) $session->open();
        
        $session->set($sessionName, $msgCode);
        
        $getSessionCode = $session->get($sessionName);
        
        // 关闭session
        $session->close();
        
        return $msgCode;
    }
    
    /**
     * 获取用户上级手机号码
     * @param bool $checkVariableRs
     * @throws \ErrorException
     */
    public function getsjTeamPhone()
    {
        $paramName   = 'sjTeamPhone';
        $sjTeamPhone = $this->controlType === self::TYPE_G ? $this->request->get($paramName) : $this->request->post($paramName);
        $sjTeamPhone = $sjTeamPhone ? htmlspecialchars(trim($sjTeamPhone)) : $this->NullParam;
        
        if(!is_null($sjTeamPhone))
        {
            $checkVariableRs = $this->checkVariable($sjTeamPhone, $paramName, self::BOOL_T, $this->phonePreg, self::BOOL_F);
            if(is_string($checkVariableRs))
                throw new \ErrorException($checkVariableRs);
        }
        
        return $sjTeamPhone;
    }
    
    /**
     * 获取小程序页面
     */
    public function getMiniPage()
    {
        $page = $this->controlType === self::TYPE_G ? $this->request->get('page') : $this->request->post('page');
        $page = $page ? htmlspecialchars(trim($page)) : $this->NullParam;
        
        if(is_null($page))
            throw new \ErrorException("请提供page");
        
        $this->miniProgram->miniPage = $page;
    }
    
    /**
     * 获取解密用户手机号的需要的数据
     * @throws \ErrorException
     */
    public function getDecryptUserPhoneData()
    {
        // openID
        $openID = $this->request->post('openID') ? htmlspecialchars(trim($this->request->post('openID'))) : $this->NullParam;
        if(is_null($openID))
            throw new \ErrorException("请提供openID");
        
        // sessionKey
        $sessionKey = $this->request->post('sessionKey') ? htmlspecialchars(trim($this->request->post('sessionKey'))) : $this->NullParam;
        if(is_null($sessionKey))
            throw new \ErrorException("请提供sessionKey");
        
        // iv
        $iv = $this->request->post('iv') ? htmlspecialchars(trim($this->request->post('iv'))) : $this->NullParam;
        if(is_null($iv))
            throw new \ErrorException("请提供iv");
        
        // encryptedData
        $encryptedData = $this->request->post('encryptedData') ? htmlspecialchars(trim($this->request->post('encryptedData'))) : $this->NullParam;
        if(is_null($encryptedData))
            throw new \ErrorException("请提供encryptedData");
        
        
        return [$openID, $sessionKey, $iv, $encryptedData];
    }
}