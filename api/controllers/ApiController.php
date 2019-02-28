<?php
namespace api\controllers;

use api\models\LoginForm;
use api\models\Order;
use api\models\Orderfz;
use api\models\Point;
use api\models\Team;
use api\models\ThirdWxPay;
use api\models\WeChat;
use api\models\WeChatLogin;
use api\models\Sms;
use api\models\Tools;
use api\models\WxFz;
use api\models\WxPayCallback;
use Exception;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use api\models\Client;
use api\models\Filters;
use api\models\QRcode;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

/**
 * Api controller
 */
class ApiController extends ActiveController
{
    public $modelClass = 'api\models\Team';
    
    private $filters;
    private $apiToken;
    private $actionName;
    
    // tokan 正则
    private $TokenPreg  = '/^[A-Za-z0-9\=]+$/';
    
    /**
     * 错误码 - 解密数据
     * error code 说明.
     * <ul>
     *    <li>-41001: encodingAesKey 非法</li>
     *    <li>-41003: aes 解密失败</li>
     *    <li>-41004: 解密后得到的buffer非法</li>
     *    <li>-41005: base64加密失败</li>
     *    <li>-41016: base64解密失败</li>
     * </ul>
     */
    public static $OK                = 0;
    public static $IllegalAesKey     = -41001;
    public static $IllegalIv         = -41002;
    public static $IllegalBuffer     = -41003;
    public static $DecodeBase64Error = -41004;
    
    
    // 测试
    public function actionMyTest()
    {
        exit(123);
        $teamID = 5;
        $security  = Yii::$app->getSecurity();

        // 将下划线修替换为A
        $randomStr = str_replace('_', 'A', $security->generateRandomString());
        $tokenStr  = base64_encode($randomStr.'_'.$randomStr.'_'.time().'_'.$teamID);

        // Yii2 hash加密
        $api_token = $security->hashData($tokenStr, "littleBee");
        echo $api_token;
        exit;
        $team = Team::findOne(517);
        print_r($team);die;
    }
    
    public function behaviors() {
        $behaviors = parent::behaviors();
        
        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);
        
        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Allow-Methods' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['*'],
            ]
        ];
        
        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'header' => 'token',
            'pattern' => $this->TokenPreg,
            'optional' => Yii::$app->params['optional'],
        ];
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        // $behaviors['authenticator']['except'] = ['options'];
        
        $this->filters = new Filters();

        return $behaviors;
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        
        // 禁用 "delete" 和 "create" 动作
        unset($actions['delete'], $actions['create']);
        
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    
    // app接口 >>>>>> satrt
    /**
     * app 登录接口
     */
    public function actionAppLogin()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
             // 新登录，不需要密码，只要验证码
             return $this->loginRegister($this->actionName);
             
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
            
        }
    }
    
    /**
     * app 注册接口
     */
    public function actionRegister()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            return $this->loginRegister($this->actionName);
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
            
        }
    }
    
    /**
     * app 忘记密码/重置密码
     * 暂未用到此接口
     */
    public function actionResetPassword()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            // 必须是post请求
            if($this->filters->request->isPost)
            {
                $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;
                
                // 获取随机字符串
                if(!is_null($randomStr))
                {
                    $this->filters->setRandomStr($randomStr);
                    
                    $phone  = $this->filters->teamPhone();
                    
                    // 检查验证码是否正确
                    $checkRs = $this->filters->msgCode();
                    if($checkRs)
                    {
                        $this->filters->miniProgram->randomStr = $randomStr;
                        $password = $this->filters->request->post('password') ? htmlspecialchars(trim($this->filters->request->post('password'))) : null;
                        
                        if(is_null($password))
                            throw new \ErrorException('请输入密码');
                            
                        // 加密密码
                        $password = Yii::$app->getSecurity()->generatePasswordHash($password);
                        
                        // 更新密码
                        $team = Team::findOne(['phone' => $phone]);
                        if($team)
                        {
                            $team->password = $password;
                            $updateRs = $team->save();
                            
                            $this->filters->miniProgram->teamID = $team->id;
                            $this->apiToken            = $team->api_token;
                            
                            // 更新成功
                            if($updateRs)
                                return $this->apiPrepare();
                                
                            throw new \ErrorException('重置失败, 请重试');
                            // 重置失败
                        }
                        throw new \ErrorException('手机号非法');
                        // 根据手机号查不到team表示手机号错误
                    }
                    throw new \ErrorException('验证码错误');
                    // 判断验证码
                }
                throw new \ErrorException('randomStr 必填');
                // 如果没有这个随机串将获取不到session的验证码
            }
            throw new \ErrorException('请求方法错误');
            // 请求方法错误
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
            
        }
    }
    
    /**
     * app 保存team profile B端
     */
    public function actionAppSaveTeamProfile()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            // 获取teamID
            $this->apiToken = $this->filters->operationParams();
            
            // 姓名不需要正则，在姓名之后开启正则验证
            $name        = $this->filters->teamName();    // 姓名
            $identity    = $this->filters->teamIdentity();// 身份证号码
            $bankCode    = $this->filters->teamBankCode();// 银行卡号码
            $sjTeamPhone = $this->filters->getsjTeamPhone(); // 上级手机号码
            
            $company  = $this->filters->request->post('company')  ? htmlspecialchars(trim($this->filters->request->post('company')))  : null; // 公司
            $province = $this->filters->request->post('province') ? htmlspecialchars(trim($this->filters->request->post('province'))) : null; // 省、市、县/区
            $city     = $this->filters->request->post('city')     ? htmlspecialchars(trim($this->filters->request->post('city')))     : null;
            $area     = $this->filters->request->post('area')     ? htmlspecialchars(trim($this->filters->request->post('area')))     : null;
            
            // 保存
            $save_rs  = $this->putTeam($name, $identity, $company, $province, $city, $area, $sjTeamPhone);
            
            if(!$save_rs) 
                throw new \ErrorException('保存失败, 请重试');
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app 绑定会员
     */
    public function actionAppBindMember()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;
            
            // 获取随机字符串
            if(!is_null($randomStr))
            {
                $this->filters->setRandomStr($randomStr);
                
                $phone   = $this->filters->teamPhone($this->actionName);
                
                // 检查验证码是否正确
                $checkRs = $this->filters->msgCode();
                if($checkRs)
                {
                    $sjTeamID = $this->filters->getCsjTeamID();
                    $save_rs  = $this->bindMember($phone, $sjTeamID);
                    
                    if(!$save_rs)
                        throw new \ErrorException('保存失败, 请重试');
                        
                    return $this->apiPrepare();
                }
                throw new \ErrorException('验证码错误');
            }
            throw new \ErrorException('randomStr 必填');
            // 如果没有这个随机串将获取不到session的验证码
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
            
        }
    }
    
    /**
     * app分享下载生成二维码
     */
    public function actionCreateDownCode()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->apiPrepare();
    }
    
    /**
     * app
     * 1.扫B端码进入-(申请前,C端用户的产品详情)
     * 2.点击B端分享的链接-推广赚钱
     */
    public function actionAppFromShare()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            $this->filters->setControlType(Filters::TYPE_G);
            
            $this->filters->miniProgram->proID   = $this->filters->getProID();
            $this->filters->miniProgram->proType = $this->filters->getProType();
            $this->filters->miniProgram->teamID  = $this->filters->getTeamID();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    // app接口 >>>>>> end  
    

    // 小程序接口 >>>>>>> start
    
    /**
     * 小程序短信验证码登录
     */
    public function actionMiniCodeLogin()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try{
            
            // 新登录，不需要密码，只要验证码
            return $this->loginRegister($this->actionName);
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 小程序登录接口
     */
    /* public function actionLogin()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {
            
            $code = $this->filters->request->get('code') ? htmlspecialchars(trim($this->filters->request->get('code'))) : null;
            
            // 登录
            if(!is_null($code))
            {
                $weChat    = new WeChat($code); // 获取teamID、token
                $loginForm = new LoginForm;     // 登录model
                
                list($openID, $sessionKey) = $weChat->getOpenID();
                $loginForm->openid         = $openID;
                $this->filters->miniProgram->teamID = $loginForm->getTeamID($openID);
                $this->apiToken                     = $loginForm->login($sessionKey);
                
                if (preg_match($this->TokenPreg, $this->apiToken))
                    return $this->filters->miniProgram->processReturnData($this->actionName, $this->apiToken);
                    
                throw new \ErrorException('登录失败, 请重试');
            }
            throw new \ErrorException('请提供微信随机码 code');
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
            
        }
    } */
    
    /**
     * 获取sessionKey 和 openID
     */
    public function actionLogin()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {
            
            $code = $this->filters->request->get('code') ? htmlspecialchars(trim($this->filters->request->get('code'))) : null;
            
            // 登录
            if(!is_null($code))
            {
                $weChat    = new WeChat($code); // 获取teamID、token
                $loginForm = new LoginForm;     // 登录model
                
                list($openID, $sessionKey) = $weChat->getOpenID();
                
                // hash openid/sessionKey
                $hashOpenID     = $this->filters->miniProgram->hashCustomData($openID);
                $hashSessionKey = $this->filters->miniProgram->hashCustomData($sessionKey);
                
                $this->filters->miniProgram->setOpenID($hashOpenID);
                $this->filters->miniProgram->setSessionKey($hashSessionKey);
                
                return $this->apiPrepare();
            }
            throw new \ErrorException('请提供微信随机码 code');
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 解密用户手机号
     */
    public function actionDecryptUserPhone()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            // 获取参数
            list($openID, $sessionKey, $iv, $encryptedData) = $this->filters->getDecryptUserPhoneData();
            
            $logData = "openID: ".$openID.", sessionKey: ".$sessionKey.", iv: ".$iv.", encryptedData: ".$encryptedData;
            Tools::log($logData, $this->actionName, null);
            
            // 解密sessionKey
            $sessionKey = $this->filters->miniProgram->decryptHashCustomData($sessionKey);
            // 解密openID
            $openID     = $this->filters->miniProgram->decryptHashCustomData($openID);
            
            $logData = "after decryptHashCustomData openID: ".$openID.", sessionKey: ".$sessionKey;
            Tools::log($logData, $this->actionName, null);
            
            // 解密信息
            $errorCode = $this->decryptData($encryptedData, $iv, $data, $sessionKey);
            
            Tools::log("errorCode: ".$errorCode, $this->actionName, null);
            
            // 解密成功
            if ($errorCode == 0)
            {
                Tools::log("decrypt data: ".$data, $this->actionName, null);

                $data  = json_decode($data, true);
                
                // 解密获取到的手机号
                $phone = $data['purePhoneNumber'] ? $data['phoneNumber'] : null;
                
                Tools::log("decrypt phone: ".$phone, $this->actionName, null);
                
                if(is_null($phone))
                    throw new \ErrorException("解码错误");
                
                $loginForm = new LoginForm();     // 登录model
                $loginForm->openid = $openID;
                
                // 查询当前手机号是否已经有账户
                $team = Team::findOne(['phone' => $phone]);
                
                if($team)
                {
                    Tools::log("team exists: true", $this->actionName, null);
                    
                    // 更新openid
                    $team->openid = $openID;
                    $team->save();
                    
                    $this->filters->miniProgram->teamID = $team->id;
                    $this->apiToken                     = $loginForm->login($sessionKey);
                    
                    Tools::log("apiToken: ", $this->apiToken, null);
                    
                    if (preg_match($this->TokenPreg, $this->apiToken))
                        return $this->filters->miniProgram->processReturnData($this->actionName, $this->apiToken);
                }
                else
                {
                    Tools::log("team exists: false", $this->actionName, null);
                    
                    $this->filters->miniProgram->teamID = $loginForm->getTeamID($openID);
                    $this->apiToken                     = $loginForm->login($sessionKey);
                    
                    Tools::log("apiToken: ", $this->apiToken, null);
                    
                    if (preg_match($this->TokenPreg, $this->apiToken))
                        return $this->filters->miniProgram->processReturnData($this->actionName, $this->apiToken);
                }
            }
            
            throw new \ErrorException("失败");
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 小程序保存team profile
     */
    public function actionSaveTeamProfile()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            // 获取teamID
            $this->apiToken = $this->filters->operationParams();
            
            $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;
            
            // 获取随机字符串
            if(!is_null($randomStr))
            {
                $this->filters->setRandomStr($randomStr);
                
                $phone = $this->filters->teamPhone($this->actionName);   // 手机号
                
                // 检查验证码是否正确
                $checkRs = $this->filters->msgCode();
                if($checkRs)
                {
                    $nickname = $this->filters->teamNickName(); // 昵称
                    $headerImg= $this->filters->teamHeaderImg();// 头像
                    $name     = $this->filters->teamName();     // 姓名
                    $identity = $this->filters->teamIdentity(); // 身份证号码
                    $bankCode = $this->filters->teamBankCode(); // 银行卡号
                    $sjTeamID = $this->filters->getsjTeamID();
                    
                    $company  = $this->filters->request->post('company')  ? htmlspecialchars(trim($this->filters->request->post('company')))  : ''; // 公司
                    $province = $this->filters->request->post('province') ? htmlspecialchars(trim($this->filters->request->post('province'))) : ''; // 省、市、县/区
                    $city     = $this->filters->request->post('city')     ? htmlspecialchars(trim($this->filters->request->post('city')))     : '';
                    $area     = $this->filters->request->post('area')     ? htmlspecialchars(trim($this->filters->request->post('area')))     : '';
                    
                    // 保存
                    $save_rs  = $this->putTeam($name, $identity, $company, $province, $city, $area, $sjTeamID, $phone, $nickname, $headerImg);
                    
                    if(!$save_rs) throw new \ErrorException('保存失败, 请重试');
                    
                    return $this->apiPrepare();
                }
                throw new \ErrorException('验证码错误');
            }
            throw new \ErrorException('randomStr 必填');
            // 如果没有这个随机串将获取不到session的验证码
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 小程序
     * 1.扫B端码进入-(申请前,C端用户的产品详情)
     * 2.点击B端分享的链接-推广赚钱
     * 没用到这个接口, 都用的appFromShare
     */
    public function actionFromShare()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->setControlType(Filters::TYPE_G);
            $this->filters->miniProgram->shareID = $this->filters->getShareID();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 生成小程序码 会员绑定
     */
    public function actionCreateMiniCodeMember()
    {
        try {
        
            $this->actionName = Yii::$app->controller->action->id;
            
            return $this->createMiniCode();
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    
    /**
     * 生成小程序码 客户绑定
     */
    public function actionCreateMiniCodeClient()
    {
        try {
            $this->actionName = Yii::$app->controller->action->id;
            
            $this->filters->paramName = 'id'; // 为了配合前端方便, 产品ID参数名: id
            
            // 获取产品ID
            $this->filters->miniProgram->proID   = $this->filters->getProID();
            // 获取产品类型
            $this->filters->miniProgram->proType = $this->filters->getproType();
            
            return $this->createMiniCode();
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    
    // 生成小程序码
    private function createMiniCode()
    {
        try {
            // 获取将跳转的page
            $this->filters->getMiniPage();
            
            // 获取teamID
            $this->apiToken = $this->filters->operationParams();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    // 小程序接口 >>>>>>> end  
    
    
    // 公共接口 >>>>>>> start
    /**
     * app/小程序 C端保存用户信息
     * 需要修改保存用户信息到单独的表中
     */
    public function actionSaveClient()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;
            
            // 获取随机字符串
            if(!is_null($randomStr))
            {
                $this->filters->setRandomStr($randomStr);
                
                $phone   = $this->filters->teamPhone($this->actionName);
                
                // 检查验证码是否正确
                $checkRs = $this->filters->msgCode();
                if($checkRs)
                {
                    $name     = $this->filters->clientName();
                    $identity = $this->filters->clientIdentity();// 身份证号码
                    $shareID  = $this->filters->getShareID();
                    $sjTeamID = $this->filters->getCsjTeamID();
                    // $type     = $this->getTypeID();
                    $type     = Client::TYPE_3; // 暂时写死成3吧 只有贷款业务
                    
                    $data = "shareID: ".$shareID.", name: ".$name.", phone: ".$phone.", identity: ".$identity.", getCsjTeamID: ".$sjTeamID;
                    
                    Tools::log($data, $this->actionName, "");
                    
                    $save_rs = Client::putClient($sjTeamID, $identity, $phone, $name, $shareID, $type);
                    
                    Tools::log($save_rs, $this->actionName, "");
                    
                    if(!$save_rs)
                        throw new \ErrorException('保存失败, 请重试');
                        
                    return $this->apiPrepare();
                }
                throw new \ErrorException('验证码错误');
            }
            throw new \ErrorException('randomStr 必填');
            // 如果没有这个随机串将获取不到session的验证码
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 提现
     * 1.金额必须为大于零的整数
     */
    public function actionGetMoney()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            $this->apiToken = $this->filters->operationParams();
            
            $getMoney = $this->filters->getMoney();// 获取输入的金额
            $afterTax = $getMoney * 0.92;          // 税后金额
            
            $team = Team::findOne($this->filters->miniProgram->teamID);
            
            if(!$team)
                throw new \ErrorException("用户状态异常,请重新登录");
            
            if((int)$getMoney > (int)$team->allmoney)
                throw new \ErrorException("提现金额超过可提现余额");
                
            $saveRs = Point::putPoint($getMoney, $afterTax, $this->filters->miniProgram->teamID);
            
            if($saveRs)
                throw new \ErrorException("提现失败, 请稍后重试");
            
            return $this->apiPrepare();
                    
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序
     * 1.扫码做单(生成二维码)
     * 2.推广赚钱
     */
    public function actionCodeStart()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->setControlType(Filters::TYPE_G);
            
            // 获取产品ID
            $this->filters->miniProgram->proID   = $this->filters->getProID();
            // 获取产品类型
            $this->filters->miniProgram->proType = $this->filters->getProType();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 用户资料填写页面
     */
    public function actionTeamProfile()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->setControlType(Filters::TYPE_G);
            // 获取会员上级ID
            $this->filters->miniProgram->sjID = $this->filters->getsjTeamID();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 team 查询接口
     * 查询是否需要填写其他信息姓名、手机、身份证号码等信息
     */
    public function actionGetTeam()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->setControlType(Filters::TYPE_G);
            $this->filters->miniProgram->teamID = $this->filters->getTeamID();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 client 查询接口
     * 查询是否需要填写其他信息姓名、手机、身份证号码等信息
     */
    public function actionGetClient()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->miniProgram->identity = $this->filters->clientIdentity();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 用户资料填写页面 获取城市/县、区
     */
    public function actionGetPlace()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {
            
            $this->filters->miniProgram->provinceCode = $this->filters->getProvince();
            $this->filters->miniProgram->cityCode     = $this->filters->getCity();
            
            // 每次请求省份code和城市code只能存在一个
            if($this->filters->miniProgram->provinceCode && $this->filters->miniProgram->cityCode)
                throw new \ErrorException('请选择省份或城市');
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 获取短信验证码
     */
    public function actionGetMsgCode()
    {
        header('Content-Type: text/plain; charset=utf-8');
        
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $phone = $this->filters->teamPhone($this->actionName);

            // 获取短信验证码并且将短信验证码hash
            $msgCode  = $this->filters->msgCodeSetHash($phone);
            $response = Sms::sendSms($phone, $msgCode);
            
            if($response && $response->Message != 'OK') throw new \ErrorException("发送失败");
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 首页
     */
    public function actionIndex()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->pagePublic();
    }
    
    /**
     * app/小程序 首页-贷款产品详情
     */
    public function actionProDetail()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
        
            $this->filters->setControlType(Filters::TYPE_G);
            // 获取产品ID
            $this->filters->miniProgram->proID   = $this->filters->getProID();
            // 获取产品类型
            $this->filters->miniProgram->proType = $this->filters->getProType();
            
            return $this->apiPrepare();
            
        } catch (Exception $e){
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 个人中心
     */
    public function actionPerCenter()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 我的客户
     */
    public function actionMyClient()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->pagePublic();
    }
    
    /**
     * app/小程序 我的会员
     */
    public function actionMyMember()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->pagePublic();
    }
    
    /**
     * app/小程序 我的推广-发展会员
     */
    public function actionDevMember()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->apiPrepare();
    }
    
    /**
     * app/小程序 我的推广-图文推广
     */
    public function actionImgTxtExtend()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->pagePublic();
    }
    
    /**
     * app/小程序 我的推广-图文推广-图文详情
     */
    public function actionImgTxtDetail()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->miniProgram->promotionID = $this->filters->getPromotionID();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * app/小程序 我的推广-会员精英群
     */
    public function actionMemberGroup()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->apiPrepare();
    }
    
    /**
     * app/小程序 会员指南/新手指南 post 表 id = 3
     */
    public function actionMemberGuide()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->apiPrepare();
    }
    
    /**
     * app/小程序 奖励说明 post 表 id = 5
     */
    public function actionPrizeExplain()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->apiPrepare();
    }
    
    /**
     * app/小程序 行业动态列表
     */
    public function actionTradeNews()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->apiPrepare();
    }
    
    /**
     * app/小程序 咨询
     */
    public function actionConsult()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            $this->filters->miniProgram->comment = $this->filters->getConsultComment();
            
            return $this->apiPrepare();
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 获取 app/小程序 咨询历史
     */
    public function actionGetConsultList()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        try {
            
            return $this->pagePublic();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }

    /**
     * app/小程序 我的佣金
     */
    public function actionMyMoney()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->incomeLists();
    }
    
    /**
     * app/小程序 提现记录
     */
    public function actionGetMoneyList()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->incomeLists();
    }
    
    /**
     * app/小程序 贷款收入记录
     */
    public function actionGetLoanIncomeList()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->incomeLists();
    }
    
    /**
     * app/小程序 信用卡收入记录
     */
    public function actionGetCreditCardIncomeList()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->incomeLists();
    }
    
    /**
     * app/小程序 大数据收入记录
     */
    public function actionGetBigDataIncomeList()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->incomeLists();
    }
    // 公共接口 >>>>>>> end  
    
    private function incomeLists()
    {
        try {
            
            return $this->pagePublic();
            
        } catch (Exception $e) {
            
            return $this->filters->errorCustom($e);
        }
    }
    
    
    // 暂时没有用到的接口 >>>>>>>> start
    /**
     * app/小程序 行业动态详情 (点击就是详情，不需要此接口)
     */
    /* public function actionTradeNewsDetail()
     {
     $this->actionName = Yii::$app->controller->action->id;
     
     try {
     
     $this->filters->miniProgram->tradeNewID = $this->filters->getTradeNewID();
     
     return $this->apiPrepare();
     } catch (Exception $e) {
     
     return $this->filters->errorCustom($e);
     }
     } */
    
    /**
     * app/小程序 管理奖收入记录（暂时么有）
     */
    public function actionGetManageIncomeList()
    {
        $this->actionName = Yii::$app->controller->action->id;
        
        return $this->incomeLists();
    }
    // 暂时没有用到的接口 >>>>>>>> end  
    
    
    /**
     * api 预处理
     * @param string $this->actionName action名
     */
    private function apiPrepare()
    {
        // 需要获取参数
        if(!in_array($this->actionName, Yii::$app->params['optional']))
            $this->apiToken = $this->filters->operationParams();
            
        $returnData = $this->filters->miniProgram->processReturnData($this->actionName, $this->apiToken);
        
        Tools::log($returnData, $this->actionName, $this->filters->miniProgram->teamID. ", token: ".$this->apiToken);
        
        return $returnData;
    }
    
    /**
     * 带有分页的公共方法
     */
    private function pagePublic()
    {
        try {
            
            $this->filters->miniProgram->limit  = $this->filters->getLimit();
            $this->filters->miniProgram->offset = $this->filters->getOffset();
            
            return $this->apiPrepare();
            
        } catch (Exception $e) {
         
            return $this->filters->errorCustom($e);
        }
    }
    
    /**
     * 绑定会员
     */
    private function bindMember($phone, $sjTeamID)
    {
        // 上级ID
        $sjTeamID = $this->filters->miniProgram->DecryptHash($sjTeamID);
        
        $team = new Team();
        
        $team->phone = $phone;
        $team->parid = $sjTeamID;
        
        $teamSaveRs = $team->save();
        
        return $teamSaveRs;
    }
    
    // add/update team
    private function putTeam($name, $identity, $company, $province, $city, $area, $sjTeamInfo, $phone = '', $nickName = '', $headerImg = '')
    {
        $team = Team::findOne($this->filters->miniProgram->teamID);
        
        // 不存在时添加
        if(!$team) $team = new Team();
        
        $team->name      = $name;
        $team->sfznumber = $identity;
        
        // phone不为空时添加
        if($phone)
            $team->phone = $phone;
        
        $team->gsnamse   = $company;
        $team->province  = $province;
        $team->city      = $city;
        $team->area      = $area;
        $team->status    = Team::STATUS_2;
        
        // 不为空时查询上级人ID
        if(!is_null($sjTeamInfo) && $this->actionName == 'app-save-team-profile')
        {
            // 手机号禁止与上级相同
            if((string) $team->phone == (string) $sjTeamInfo)
                throw new \ErrorException("上级手机号不允许与当前注册手机号一致");
            
            $sjTeam      = Team::findOne(['phone' => $sjTeamInfo]);
            $team->parid = $sjTeam ? $sjTeam->id : "";
        }
        // 小程序保存B端上级、头像、昵称
        else if($this->actionName == 'save-team-profile')
        {
            $team->parid     = $sjTeamInfo;
            $team->nickname  = $nickName;
            $team->img       = $headerImg;
        }
        
        return $team->save();
    }
    
    // 登录注册公共逻辑
    private function loginRegister($actionName)
    {
        // 必须是post请求
        if($this->filters->request->isPost)
        {
            $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;
            
            // 获取随机字符串
            if(!is_null($randomStr))
            {
                Tools::log("randomStr: ". $randomStr, $actionName, null);
                
                $this->filters->setRandomStr($randomStr);
                
                // 获取手机号
                $phone   = $this->filters->teamPhone($actionName);
                // 检查验证码是否正确
                $checkRs = $this->filters->msgCode();
                
                Tools::log("checkRs: ".$checkRs, $actionName, null);
                
                if($checkRs)
                {
                    $loginForm = new LoginForm(); // 登录model
                    $loginForm->phone = $phone;
                    
                    // 去掉密码
                    /*$password    = $this->filters->request->post('password') ? htmlspecialchars(trim($this->filters->request->post('password'))) : null;
                     if(is_null($password))
                     throw new \ErrorException('请输入密码');
                     $password = Yii::$app->getSecurity()->generatePasswordHash($password);*/
                     
                     $this->filters->miniProgram->teamID = $loginForm->appGetTeamID();
                     
                     Tools::log("appGetTeamID: ", $actionName, $this->filters->miniProgram->teamID );
                     
                     $this->apiToken                     = $loginForm->appLogin($actionName);
                     
                     return $this->apiPrepare();
                }
                throw new \ErrorException('验证码错误');
            }
            throw new \ErrorException('randomStr 必填');
            // 如果没有这个随机串将获取不到session的验证码
        }
        throw new \ErrorException('请求方法非法', 405);
    }


    
    
    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv   string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    private function decryptData( $encryptedData, $iv, &$data, $sessionKey )
    {
        if (strlen($sessionKey) != 24)
            throw new \ErrorException("非法的sessionKey", self::$IllegalAesKey);
        
        if (strlen($iv) != 24) 
            throw new \ErrorException("非法的iv", self::$IllegalIv);
        
        $aesKey    = base64_decode($sessionKey);
        $aesIV     = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result    = openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj   = json_decode( $result );
        
        if( $dataObj  == NULL ) 
            throw new \ErrorException("aes 解密失败", self::$IllegalBuffer);
        
        if( $dataObj->watermark->appid != Yii::$app->params['littleBeeParams']['appid'] ) 
            throw new \ErrorException("非法的appID", self::$IllegalBuffer);
        
        $data = $result;
        
        return self::$OK;
    }

    /**
     * app 购买会员
     */
    public function actionBuyMember()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {

            $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;

            // 获取随机字符串
            if(!is_null($randomStr))
            {
                $this->filters->setRandomStr($randomStr);

                $this->filters->miniProgram->shareOpenId = $this->filters->getShareOpenId();
                $this->filters->miniProgram->shareRealName = $this->filters->getShareRealName();
                $this->filters->miniProgram->tradeType = $this->filters->getTradeType();
                $this->filters->miniProgram->remoteIp = $this->filters->getRemoteIp();
                $this->filters->miniProgram->wxId = $this->filters->getWxId();
                $this->filters->miniProgram->wxName = $this->filters->getWxName();

                return $this->apiPrepare();
            }
            throw new \ErrorException('randomStr 必填');
            // 如果没有这个随机串将获取不到session的验证码

        } catch (Exception $e) {

            return $this->filters->errorCustom($e);
        }
    }


    /**
     * app 购买会员回调
     */
    public function actionBuyMemberCallback()
    {
        $this->actionName = Yii::$app->controller->action->id;

        Tools::log($_GET, $this->actionName, null);
        try {
            $thirdWxPay = new ThirdWxPay();
            $res = $thirdWxPay->notify($_GET);
            if ($res === false) {
                Tools::log(['errMsg'=>'验签错误','data'=>$_GET], $this->actionName, null);
                die('FAIL');
            }
            if ($this->buyMemberCallback($_GET)) {
                die('SUCCESS');
            }
            die('FAIL');
        } catch (Exception $e) {
            return $this->filters->errorCustom($e);
        }
    }

    public function buyMemberCallback($data) {
        $payfee = $data["amount"];
        $payorderid = $data["payOrderId"];
        $chorderid = $data["channelOrderNo"];
        $orderid = $data["mchOrderNo"];
        $paytime = $data["paySuccTime"];
        $order = Order::findOne(array("orderid"=>$orderid));
        if ($order) {
            if ($order->status == 1) {
                Tools::log(['outMsg'=>'订单已经支付成功','orderid'=>$orderid], $this->actionName, null);
                return true;
            }
            $order->payfee = $payfee;
            $order->payorderid = $payorderid;
            $order->chorderid = $chorderid;
            $order->paytime = $paytime;
            $order->updatetime = Tools::getMillisecond();
            if ($order->payfee == $payfee) {
                $order->status = 1;
            } else {
                $order->status = 2;
                Tools::log(['errMsg'=>'订单金额不符','orderid'=>$orderid,'payfee'=>$payfee,'dbPayfee'=>$order->payfee],
                    $this->actionName, null);
            }
            if ($order->save()) {
                Tools::log(['outMsg'=>'订单支付成功','orderid'=>$orderid], $this->actionName, null);
                $team = Team::findOne((int) $order->teamid);
                $team->ispay = 1;
                if (!$team->save()) {
                    Tools::log(['errMsg'=>'修改用户ispay状态为1失败','teamid'=>$order->teamid], $this->actionName, null);
                }
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * app 购买会员回调
     */
    public function actionBuyMemberCheck()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {
            $randomStr = $this->filters->request->post('randomStr') ? htmlspecialchars(trim($this->filters->request->post('randomStr'))) : null;

            // 获取随机字符串
            if(!is_null($randomStr))
            {
                $this->filters->setRandomStr($randomStr);

                $this->filters->miniProgram->tradeId = $this->filters->getTradeId();

                return $this->apiPrepare();
            }
            throw new \ErrorException('randomStr 必填');
            // 如果没有这个随机串将获取不到session的验证码

        } catch (Exception $e) {

            return $this->filters->errorCustom($e);
        }
    }



    /**
     * 获取sessionKey 和 openID
     */
    public function actionWxLogin2()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {

            $code = $this->filters->request->get('code') ? htmlspecialchars(trim($this->filters->request->get('code'))) : null;

            // 登录
            if(!is_null($code))
            {
                $weChatLogin    = new WeChatLogin($code); // 获取teamID、token
                list($openID, $accessToken) = $weChatLogin->getOpenID();

                $unionId = $weChatLogin->getUserInfo($accessToken, $openID);

                $loginForm = new LoginForm();     // 登录model
                $loginForm->openid = $openID;

                $this->filters->miniProgram->teamID = $loginForm->getTeamID($openID);
                $this->apiToken                     = $loginForm->login($accessToken);

                return $this->apiPrepare();
            }
            throw new \ErrorException('请提供微信随机码 code');

        } catch (Exception $e) {

            return $this->filters->errorCustom($e);
        }
    }


    public function actionWxLogin()
    {
        $this->actionName = Yii::$app->controller->action->id;
        try {

            $loginData = $this->filters->request->post('loginData') ? $this->filters->request->post('loginData') : null;

            // 登录
            if(!is_null($loginData))
            {
                $loginData = json_decode($loginData, true);
                $openID = isset($loginData['openid']) ? $loginData['openid'] : 0;
                $unionid = isset($loginData['unionid']) ? $loginData['unionid'] : 0;
                $accessToken = isset($loginData['accessToken']) ? $loginData['accessToken'] : "";
                $screenName = isset($loginData['screen_name']) ? $loginData['screen_name'] : "";

                if ($openID && $unionid && $accessToken && $screenName) {
                    $loginForm = new LoginForm();     // 登录model
                    $loginForm->openid = $openID;
                    $this->filters->miniProgram->teamID = $loginForm->getTeamID($openID);
                    $this->apiToken                     = $loginForm->login($accessToken);

                    $team = Team::findOne(['openid' => $openID]);
                    if (empty($team->nickname)) {
                        $team->nickname = $screenName;
                        $team->save();
                    }

                    return $this->apiPrepare();
                }
                throw new \ErrorException('参数错误');
            }
            throw new \ErrorException('登录信息为null');

        } catch (Exception $e) {

            return $this->filters->errorCustom($e);
        }
    }
}
