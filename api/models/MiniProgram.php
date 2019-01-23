<?php
namespace api\models;

use Exception;
use Yii;
use yii\web\UnauthorizedHttpException;

class MiniProgram {
    
    const PATH_BACK  = 'back';
    const PATH_JUST  = 'just';
    const PATH__JUST = '_just';
    const PATH_TRIM  = 'trim';
    
    public $cityCode;    // 城市code
    public $provinceCode;// 省份code
    public $msgCode;     // 短信验证码
    public $limit;
    public $offset;
    public $team;        // team对象
    public $teamID;      // team表ID
    public $promotionID; // promotion表ID
    public $proID;       // pro表ID（产品ID）
    public $pro;         // pro object
    public $proType;     // 产品类型  1=信用卡 2=大数据 3=借贷产品
    public $chare;       // chare object
    public $ewming;      // ewming object
    public $shareID;     // chare 表ID
    public $shareUrl;    // 推广分享链接
    public $sjTeamID;    // 会员上级ID
    public $randomStr;   // 验证码session名称随机串
    public $identity;    // 身份证号码
    public $comment;     // 咨询的内容
    public $tradeNewID;  // 行业动态详情ID
    public $miniPage;    // 小程序码跳转的页面
    public $remoteIp;    // ip
    public $tradeType;   // 订单来源
    public $tradeId;     // 订单号
    private $openID;     // openid
    private $sessionKey; // sessionkey
    private $apiToken;   // token
    private $needUserInfo = true; // 需要用户填写信息标识
    private $needAuthUser = true; // 需要用户授权昵称头像信息标识
    private $needAuthPhone= true; // 需要用户授权手机号标识
    private $needBuyMember= true; // 需要购买会
    
    // ---------------- 以下为当前controller、model公用方法
    
    public function setOpenID($openID)
    {
        $this->openID = $openID;
    }
    
    public function setSessionKey($sessionKey)
    {
        $this->sessionKey = $sessionKey;
    }
    
    /**
     * 各个接口返回处理数据入口
     * @param string  $interFace 接口action
     * @param string  $apiToken  token
     */
    public function processReturnData($interFace, $apiToken)
    {
        try {
            // 获取是否需要用户信息标识
            // if(!in_array($interFace, Yii::$app->params['optional']))
            $this->getNeedInfoSign($apiToken);
            
            switch ($interFace) {
                
                // 注册
                case 'register':
                    return $this->registerInterface($apiToken);
                    break;
                
                // app登录
                case 'app-login':
                    return $this->appLoginInterface($apiToken);
                    break;
                    
                // 重置密码
                case 'reset-password':
                    return $this->resetPasswordInterface($apiToken);
                    break;
                    
                // 小程序登录接口
                case 'login':
                    return $this->loginInterface();
                    break;
                    
                // 解密手机号
                case 'decrypt-user-phone':
                    return $this->decryptUserPhoneInterface($apiToken);
                    break;
                
                // 小程序验证码登录接口
                case 'mini-code-login':
                    return $this->miniCodeLoginInterface($apiToken);
                    break;
                    
                // 会员资料页面
                case 'team-profile':
                    return $this->teamProfileInterface();
                    break;
                   
                // 获取城市/县、区
                case 'get-place':
                    return $this->getPlaceInterface();
                    break;
                    
                // 短信验证码
                case 'get-msg-code':
                    return $this->getMsgCodeInterface();
                    break;
                    
                // 填写会员资料
                case 'save-team-profile':
                    return $this->saveTeamProfileInterface();
                    break;
                    
                // 首页
                case 'index':
                    return $this->indexInterface();
                    break;
                
                // 贷款产品=3/大数据=1/信用卡=2产品详情
                case 'pro-detail':
                    return $this->proDetailInterface();
                    break;
                    
                // 申请前页面
                case 'from-share':
                    return $this->fromShareInterface();
                    break;
                   
                // 个人中心
                case 'per-center':
                    return $this->perCenterInterface();
                    break;
                    
                // 我的客户
                case 'my-client':
                    return $this->myClientInterface();
                    break;
                    
                // 我的会员
                case 'my-member':
                    return $this->myMemberInterface();
                    break;
                    
                // 我的推广-发展会员
                case 'dev-member':
                    return $this->devMemberInterface();
                    break;
                    
                // 我的推广-图文推广
                case 'img-txt-extend':
                    return $this->imgTxtExtendInterface();
                    break;
                
                // 我的推广-图文推广-图文详情
                case 'img-txt-detail':
                    return $this->imgTxtDetailInterface();
                    break;
                    
                // 我的推广-会员精英群
                case 'member-group':
                    return $this->memberGroupInterface();
                    break;
                    
                // 会员指南-新手指南
                case 'member-guide':
                    return $this->memberGuideInterface();
                    break;
                    
                // 会员指南-奖励说明
                case 'prize-explain':
                    return $this->prizeExplainInterface();
                    break;
                    
                // 保存C端用户信息
                case 'save-cuser-profile':
                    return $this->saveCUserProfileInterface();
                    break;
                  
                // 扫码做单
                case 'code-start':
                    return $this->codeStartInterface();
                    break;
                    
                // 获取用户信息
                case 'get-team':
                    return $this->getTeamInterface();
                    break;
                    
                // 保存C端用户
                case 'save-client':
                    return $this->saveClientInterface();
                    break;
                    
                // 我的佣金
                case 'my-money':
                    return $this->myMoneyInterface();
                    break;
                    
                // 提现记录
                case 'get-money-list':
                    return $this->getMoneyListInterface();
                    break;
                    
                // 贷款收入记录
                case 'get-loan-income-list':
                    return $this->getLoanIncomeListInterface();
                    break;
                    
                // 信用卡收入记录
                case 'get-credit-card-income-list':
                    return $this->getCreditCardIncomeListInterface();
                    break;
                    
                // 大数据收入记录
                case 'get-big-data-income-list':
                    return $this->getBigDataIncomeListInterface();
                    break;
                    
                // 获取C端用户信息
                case 'get-client':
                    return $this->getClientInterface();
                    break;
                    
                // app保存B端信息
                case 'app-save-team-profile':
                    return $this->appSaveTeamProfileInterface();
                    break;
                    
                // 提现
                case 'get-money':
                    return $this->getMoneyInterface();
                    break;
                    
                // 行业动态
                case 'trade-news':
                    return $this->tradeNewsInterface();
                    break;
                    
                // 行业动态详情
                /* case 'trade-news-detail':
                    return $this->tradeNewsDetailInterface();
                    break; */
                    
                // 咨询小蜜
                case 'consult':
                    return $this->consultInterface();
                    break;
                
                // 咨询小蜜历史记录
                case 'get-consult-list':
                    return $this->getConsultListInterface();
                    break;
                    
                // 生成下载二维码
                case 'create-down-code':
                    return $this->createDownCodeInterface();
                    break;
                    
                // app 来自分享 C 详情
                case 'app-from-share':
                    return $this->appFromShareInterface();
                    break;
                    
                // app 绑定会员关系
                case 'app-bind-member':
                    return $this->appBindMemberInterface();
                    break;
                    
                // 生成小程序码 会员绑定
                case 'create-mini-code-member':
                    return $this->createMiniCodeMemberInterface();
                    break;
                    
                // 生成小程序码 客户绑定
                case 'create-mini-code-client':
                    return $this->createMiniCodeClientInterface();
                    break;

                case 'buy-member':
                    return $this->buyMember();
                    break;

                case 'buy-member-check':
                    return $this->buyMemberCheck();
                    break;
            }
        
        } catch (Exception $e) {
            
            return ['code' => 10000, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * 公共数据
     */
    private function publicData($echoData)
    {
        $echoData['code']         = 200;
        $echoData['teamID']       = $this->teamID;
        $echoData['apiToken']     = $this->apiToken;
        $echoData['needAuthPhone']= $this->needAuthPhone;
        $echoData['needAuthUser'] = $this->needAuthUser;
        $echoData['needUserInfo'] = $this->needUserInfo;
        $echoData['needBuyMember'] = $this->needBuyMember;
        
        return $echoData;
    }
    
    /**
     * 是否需要信息(用户、手机)标识
     */
    private function getNeedInfoSign($apiToken)
    {
        $this->apiToken = $apiToken;
            
        if($this->teamID)
        {
            // 查询team
            $this->team = Team::findOne($this->teamID);
            
            if($this->team)
            {
                if($this->team->phone) $this->needAuthPhone = false;
                if($this->team->nickname && $this->team->img)   $this->needAuthUser = false;
                if($this->team->name && $this->team->sfznumber && $this->team->phone) $this->needUserInfo = false;
                if($this->team->ispay > 0) $this->needBuyMember = false;
            }
            else
            {
                $this->needAuthPhone = true;
                $this->needAuthUser  = true;
                $this->needUserInfo  = true;
                $this->needBuyMember = true;
            }
        }
        else
        {
            $this->needAuthPhone = true;
            $this->needAuthUser  = true;
            $this->needUserInfo  = true;
            $this->needBuyMember = true;
        }
    }
    // ---------------- 以上为当前controller、model公用方法
    
    /**
     * app注册
     */
    protected function registerInterface($apiToken)
    {
        return $this->appRegisterLoginReset($apiToken, 'registerRs');
    }
    
    /**
     * app登录
     */
    protected function appLoginInterface($apiToken)
    {
        return $this->appRegisterLoginReset($apiToken, 'loginRs');
    }
    
    /**
     * 重置密码
     */
    protected function resetPasswordInterface($apiToken)
    {
        return $this->appRegisterLoginReset($apiToken, 'resetRs');
    }
    
    // 注册/登录/重置密码
    private function appRegisterLoginReset($apiToken, $type)
    {
        $echoData = [];
        $echoData[$type] = true;
        
        $this->getNeedInfoSign($apiToken);
        
        if($type === 'loginRs')
            $this->needAuthPhone = false;
        
        return $this->publicData($echoData);
    }
    
    /**
     * 小程序登录
     */
    /* protected function loginInterface($apiToken)
    {
        $this->apiToken = $apiToken;
        
        return $this->publicData([]);
    } */
    
    /**
     * 小程序登录 先获取手机号
     */
    protected function loginInterface()
    {
        $echoData = [];
        
        $echoData['code']       = 200;
        $echoData['openID']     = $this->openID;
        $echoData['sessionKey'] = $this->sessionKey;
        
        return $echoData;
    }
    
    protected function decryptUserPhoneInterface($apiToken)
    {
        $this->apiToken = $apiToken;
     
        return $this->publicData([]);
    } 
    
    /**
     * 小程序验证码登录接口
     */
    protected function miniCodeLoginInterface($apiToken)
    {
        $this->apiToken = $apiToken;
        
        return $this->publicData([]);
    }
    
    /**
     * 获取短信验证码
     */
    protected function getMsgCodeInterface()
    {
        $echoData = [];
        
        $echoData['code'] = 200;
        $echoData['randomStr'] = $this->randomStr;
        
        return $echoData;
    }
    
    /**
     * 保存用户资料
     */
    protected function saveTeamProfileInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        $team = Team::findOne($this->teamID);

        $echoData['currentTeamID'] = $team ? $this->hash($team->id) : null;
        
        return $echoData;
    }
    
    /**
     * 会员资料页面
     */
    protected function teamProfileInterface()
    {
        $echoData = [];
        
        // 省
        $province = Province::find()->asArray()->all();
        
        $echoData['province'] = $province;
        $echoData['sjTeamID'] = $this->sjTeamID;
        
        return $this->publicData($echoData);
    }
    
    /**
     * 获取城市/县、区
     */
    protected function getPlaceInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        // 获取城市
        if($this->provinceCode)
        {
            $city = City::find()->where('provincecode=:code')
            ->addParams([':code' => $this->provinceCode])
            ->asArray()->all();
            
            $echoData['city'] = $city;
        }
            
        // 获取县、区
        if($this->cityCode)
        {
            $area = Area::find()->where('citycode=:code')
            ->addParams([':code' => $this->cityCode])
            ->asArray()->all();
            
            $echoData['area'] = $area;
        }
        
        return $echoData;
    }
    
    /**
     * 首页接口
     */
    protected function indexInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        // 轮播图
        $lunbotp  = Lunbotp::find()->asArray()->all();
        $lunbotp  = $this->mapImg($lunbotp, 'tupian');
        // 消息轮播
        $lunbo    = Lunbo::find()->asArray()->all();
        // 贷款产品
        $pro      = $this->getProInfo();
        // 信用卡/大数据产品
        $creData  = $this->getCreditAndData();
           
        $echoData['lunbotp'] = $lunbotp;  // 轮播图
        $echoData['lunbo']   = $lunbo;    // 轮播消息
        $echoData['pro']     = $pro;      // 贷款产品
        $echoData['creData'] = $creData;  // 信用卡/大数据
        
        return $echoData;
    }
    
    /**
     * 贷款产品
     */
    private function getProInfo()
    {
        $pro = Pro::find()
            ->select(['id', 'name', 'biaoq1', 'biaoq2', 'biaoq3', 'maxfybl', 'img', 'retPay'])
            ->limit($this->limit)
            ->offset($this->offset)
            ->orderBy('important ASC')
            ->asArray()->all();
        
        // 处理贷款产品biaoqian字段和图片路径
        $pro = $this->processBiaoQian($pro);
        $pro = $this->mapImg($pro, 'img');
        
        return $pro;
    }
    
    /**
     * 信用卡/大数据
     */
    private function getCreditAndData()
    {
        // 信用卡/大数据产品
        $creditanddata = CreditAndData::find()
            ->select(['id', 'name', 'biaoq1', 'biaoq2', 'biaoq3', 'maxfybl', 'img', 'ptconnect', 'keyMsg', 'pointMsg', 'payMsg', 'type'])
            ->limit($this->limit)
            ->offset($this->offset)
            ->orderBy('important ASC')
            ->asArray()->all();
        
        // 处理信用卡/大数据产品biaoqian字段和图片路径
        $creditanddata = $this->processBiaoQian($creditanddata);
        $creditanddata = $this->mapImg($creditanddata, 'img');
        
        return $creditanddata;
    }
    
    private function processBiaoQian($dataBQ)
    {
        foreach($dataBQ as $key => $val)
        {
            $b = [];
            
            array_push($b, $val['biaoq1'], $val['biaoq2'], $val['biaoq3']);
            
            $dataBQ[$key]['biaoq'] = $b;
            
            unset($dataBQ[$key]['biaoq1'], $dataBQ[$key]['biaoq2'], $dataBQ[$key]['biaoq3']);
        }
        
        return $dataBQ;
    }
    
    /**
     * 贷款产品详情
     */
    protected function proDetailInterface()
    {
        $team = Team::findOne($this->teamID);
        
        $echoData = [];
        $echoData = $this->getCustomerMangaer($echoData); // 获取客户经理
        $echoData = $this->proDetail($echoData);          // 获取产品详情
        
        $echoData['proDetail']['minfybl'] = $this->pro->minifybl;
        $echoData['proDetail']['maxfybl'] = $this->pro->maxfybl;
        $echoData['proDetail']['payroll'] = $this->strReplace($this->pro->payroll, self::PATH_TRIM);
        $echoData['currentTeamID']        = $team ? $this->hash($team->id) : null;
        
        return $this->publicData($echoData);
    }
    
    /**
     * 申请前页面
     */
    protected function fromShareInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        $shareID = $this->DecryptHash($this->shareID);

        // 查询chare分享表
        $share = Chare::findOne((int) $shareID);
        if($share)
        {
            $this->proID = $share->proid;
            $echoData    = $this->proDetail($echoData);
            
            // 上级teamID
            $echoData['sjTeamID'] = $this->hash($share->teamid);;
            $echoData['shareID']  = $shareID;
            
            return $echoData;
        }
        
        throw new \InvalidArgumentException('shareID 是非法的');
    }
    
    /**
     * app 申请前页面, c 详情
     */
    protected function appFromShareInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        if(is_null($this->proType))
        {
            // 查询chare分享表
            $share = Chare::find()
                ->where('teamid=:teamid AND proid=:proid')
                ->addParams([':teamid' => $this->teamID, ':proid' => $this->proID])
                ->one();
        }
        else 
        {
            // 查询chare分享表
            $share = Chare::find()
                ->where('teamid=:teamid AND proid=:proid AND type=:type')
                ->addParams([':teamid' => $this->teamID, ':proid' => $this->proID, ':type' => $this->proType])
                ->one();
        }
        
        if(!$share)
        {
            $share = new Chare();
            $share = $share->postChare($this->teamID, $this->proID, $this->proType);
        }
        
        $echoData  = $this->proDetail($echoData);
        
        // 上级teamID
        $echoData['sjTeamID'] = $this->hash($this->teamID);
        $echoData['shareID']  = $share->id;
        
        return $echoData;
    }
    
    /**
     * app绑定会员关系
     */
    protected function appBindMemberInterface()
    {
        $echoData = [];
        $echoData['code']    = 200;
        $echoData['post_rs'] = true;
        
        return $echoData;
    }
    
    /**
     * 生成小程序码 会员绑定
     */
    protected function createMiniCodeMemberInterface()
    {
        $echoData = [];
        $data     = [];
        
        $data['scene'] = 'sjTeamID='.$this->teamID;
        $data['page']  = $this->miniPage;
        
        $echoData['code']     = 200;
        $echoData['sjTeamID'] = $this->teamID;
        
        $wechat   = new WeChat();
        $codePath = $wechat->getMiniProgramCode($this->teamID, $this->miniPage, $data);
        
        $echoData['codePath'] = $codePath;
        
        return $echoData;
    }
    
    /**
     * 生成小程序码 客户绑定
     */
    protected function createMiniCodeClientInterface()
    {
        $wechat   = new WeChat();
        $echoData = [];
        $data     = [];
        
        $data['scene'] = 'teamID='.$this->teamID."&id=".$this->proID."&proType=".$this->proType;
        $data['page']  = $this->miniPage;
        
        // 查询chare是否存在
        $this->getChare();
        
        // 不存在时生成此产品的二维码同时添加数据
        if(!$this->chare)
        {
            // 先添加一条数据
            if(!$this->addChare()) 
                throw new \ErrorException('分享失败, 请重试');
        }
        
        $echoData['code']     = 200;
        $echoData['id']       = $this->proID;  // 产品ID 应前端要求修改为 id
        $echoData['teamID']   = $this->teamID; // sjTeamID 应前端要求修改为 teamID
        $echoData['codePath'] = $wechat->getMiniProgramCode($this->teamID, $this->miniPage, $data);
        
        return $echoData;
    }
    
    /**
     * 个人中心
     */
    protected function perCenterInterface()
    {
        $echoData = [];
        
        if($this->teamID)
        {
            $echoData['code'] = 200;
            $team = Team::findOne((int) $this->teamID);
            
            // 客户总数
            $count_c  = Client::find()
                ->where('shangjiid=:sjID')
                ->addParams([':sjID' => $this->teamID])
                ->count();
            
            if($team)
            {
                $echoData['img']         = $team->img;
                $echoData['all_money']   = $team->allmoney ? $team->allmoney : 0;
                $echoData['name']        = $team->name ? $team->name : $team->nickname;
                $echoData['client_count']= $count_c;
                
                return $echoData;
            }
        }
        
        throw new UnauthorizedHttpException('token 是非法的');
    }
    
    
    /**
     * 获取客户经理
     */
    private function getCustomerMangaer($echoData)
    {
        $teamID = null;
        $client = Client::findOne(['teamid' => $this->teamID]);
        if($client)
            $teamID = $client->shangjiid;
        
        $team = is_null($teamID) ? $this->teamID : $teamID;
            
        $echoData['cManager']['name']  = $team->name;
        $echoData['cManager']['phone'] = $team->phone;
        
        return $echoData;
    }
    
    /**
     * 产品详情、申请前页面公共方法
     * @param array  $echoData 重组数组array
     * @param object $pro      产品详情object
     * 
     * @return array
     */
    private function proDetail($echoData)
    {
        // proType 时表示为贷款产品
        if(is_null($this->proType))
            $this->pro = Pro::findOne($this->proID);
        // 大数据/信用卡产品
        else
            $this->pro = CreditAndData::findOne($this->proID);
        
        if($this->pro)
        {
            // 标签放入list
            $biaoq = [];
            array_push($biaoq, $this->pro->biaoq1, $this->pro->biaoq2, $this->pro->biaoq3);
            
            $echoData['proDetail']['img']        = $this->strReplace($this->pro->img, self::PATH_JUST);
            $echoData['proDetail']['name']       = $this->pro->name;
            $echoData['proDetail']['biaoq']      = $biaoq;
            $echoData['proDetail']['dked']       = $this->pro->dked;
            $echoData['proDetail']['loanTime']   = $this->pro->loanTime;
            $echoData['proDetail']['perMil']     = $this->pro->perMil;
            $echoData['proDetail']['liucheng']   = $this->strReplace($this->pro->liucheng, self::PATH_TRIM);
            $echoData['proDetail']['certfifcate']= $this->pro->certfifcate;
            $echoData['proDetail']['question']   = $this->pro->question;
            $echoData['proDetail']['ptconnect']  = $this->pro->ptconnect;
            
            return $echoData;
        }
        else throw new \InvalidArgumentException('产品ID是非法的');
    }
    
    /**
     * 我的会员
     */
    protected function myMemberInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        $team = Team::find()
                ->select(['id', 'img', 'name', 'phone', 'province', 'city', 'area'])
                ->where('parid=:parid')
                ->addParams([':parid' => $this->teamID])
                ->limit($this->limit)
                ->offset($this->offset)
                ->asArray()->all();
        
        $echoData['myMember'] = $team;
        
        return $echoData;
    }
    
    
    /**
     * 我的客户
     */
    protected function myClientInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        $client = Client::find()
                ->leftJoin('client_info', 'client_info_id = client.id')
                ->where('shangjiid=:sjid')
                ->addParams([':sjid' => $this->teamID])
                ->limit($this->limit)
                ->offset($this->offset)
                ->orderBy('client.id DESC')
                ->asArray()->all();
        
        $echoData['myClient'] = [];
                
        foreach($client as $ck => $cv)
        {
            // $team = Team::findOne($cv['teamid']);
            $pro  = Pro::findOne($cv['proid']);
            $order= OrderGoods::findOne($cv['orderbhid']);

            $echoData['myClient'][$ck]['img']   = null;
            $echoData['myClient'][$ck]['name']  = $cv['name'];
            $echoData['myClient'][$ck]['phone'] = $cv['phone'];

            
            if($pro)
                $echoData['myClient'][$ck]['pro'] = $pro->name;
            else
                $echoData['myClient'][$ck]['pro'] = null;
            
            if($order)
            {
                $echoData['myClient'][$ck]['money'] = $order->loans;
                $echoData['myClient'][$ck]['time']  = $order->hkTime;
            }
            else 
            {
                $echoData['myClient'][$ck]['money'] = null;
                $echoData['myClient'][$ck]['time']  = null;
            }
        }
                
        return $echoData;
    }
    
    /**
     * 我的推广-发展会员
     * @return array
     */
    protected function devMemberInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        // 存在时
        if($this->team->qrcode)
        {
            $echoData['qrcode']['img'] = $this->strReplace($this->team->qrcode, self::PATH_JUST);
        }
        // 生成二维码
        else
        {
            // 加密上级ID
            $devMemberUrl = sprintf(Yii::$app->params['shareParams']['devMemberUrl'], $this->hash($this->teamID));
            
            // 生成二维码
            $teamQrCode   = $this->updateTeamQRcode($devMemberUrl);
            
            $echoData['qrcode']['img'] = $teamQrCode;
        }
        
        $echoData['qrcode']['date']       = $this->team->time;
        $echoData['qrcode']['nickname']   = $this->team->nickname;
        $echoData['qrcode']['name']       = $this->team->name;
        
        return $echoData;
    }
    
    /**
     * 我的推广-图文推广
     */
    protected function imgTxtExtendInterface()
    {
        $echoData  = [];
        
        $promotion = Promotion::find()
                ->select(['id', 'img', 'head', 'date'])
                ->limit($this->limit)
                ->offset($this->offset)
                ->asArray()->all();
        
        // 替换图片地址
        $promotion = $this->mapImg($promotion, 'img');
        // 替换时间
        $promotion = $this->mapDate($promotion, 'date');
                
        $echoData['code'] = 200;
        $echoData['promotion'] = $promotion;
        
        return $echoData;
    }
    
    /**
     * 我的推广-图文推广-图文详情
     */
    protected function imgTxtDetailInterface()
    {
        $echoData  = [];
        
        $team = Team::findOne($this->teamID);
        if($team)
        {
            $promotion = Promotion::findOne((int) $this->promotionID);
            if($promotion)
            {
                $echoData['code'] = 200;
                $echoData['promotion']['head']    = $promotion->head;
                $echoData['promotion']['date']    = date('Y-m-d', strtotime($promotion->date));
                $echoData['promotion']['content'] = $this->strReplace($promotion->content, self::PATH__JUST);
                
                return $echoData;
            }
            else throw new \InvalidArgumentException ('文章ID是非法的');
        
            // 保存已经读过的文章ID 
            $this->savePromotionID($team);
        }
        else throw new UnauthorizedHttpException('token 是非法的');
    }
    
    
    /**
     * 我的推广-会员精英群
     */
    protected function memberGroupInterface()
    {
        $echoData = [];
        
        $memberGroup = Jingyingq::findOne(1);
        
        $echoData['code'] = 200;
        $echoData['memberGroup']['name']  = $memberGroup->name;
        $echoData['memberGroup']['phone'] = $memberGroup->phone;
        $echoData['memberGroup']['img']   = $this->strReplace($memberGroup->img, self::PATH_JUST);
        
        return $echoData;
    }
    
    /**
     * 会员指南
     */
    protected function memberGuideInterface()
    {
        return $this->memberGuide(3);
    }
    
    /**
     * 奖励说明
     */
    protected function prizeExplainInterface()
    {
        return $this->memberGuide(5);
    }
    
    /**
     * 保存C端用户信息
     */
    protected function saveCUserProfileInterface()
    {
        return $this->publicData([]);
    }
    
    /**
     * 扫码做单
     */
    protected function codeStartInterface()
    {
        $echoData = [];
        
        $this->getPro();
        $this->getChare();
        $this->getEwming();
        
        if(!$this->pro) throw new \ErrorException('产品ID是非法的');
        
        $echoData['code']    = 200;
        $echoData['proName'] = $this->pro->name;
        $echoData['proLogo'] = $this->strReplace($this->pro->img, self::PATH_JUST);
        $echoData['bjImg']   = $this->strReplace($this->ewming->img, self::PATH_JUST);
        
        // 产品跳转链接
        $this->shareUrl = sprintf(Yii::$app->params['shareParams']['shareUrl'], $this->proID, $this->teamID, $this->proType);
        
        // 2019-01-11全部重新生成二维码
        $this->createLogoCode();
        
        // 不存在时生成此产品的二维码同时添加数据
        if(!$this->chare)
        {
            // 先添加一条数据
            if(!$this->addChare()) 
                throw new \ErrorException('分享失败, 请重试');
            // 2019-01-11先走上面的全部生成二维码
            // $this->createLogoCode();
        }
        // 存在但是二维码有问题则重新生成   2019-01-11先走上面的全部生成二维码
        // else if(!$this->chare->img) $this->createLogoCode();
        
        $echoData['shareUrl'] = $this->shareUrl;
        $echoData['codeImg']  = $this->strReplace($this->chare->img, self::PATH_JUST);
        
        return $echoData;
    }
    
    /**
     * 获取team信息
     */
    protected function getTeamInterface()
    {
        $needInfo = true;
        $echoData = [];
        $echoData['code'] = 200;
        
        $team = Team::findOne($this->teamID);
        
        if($team && $team->phone && $team->name && $team->sfznumber) $needInfo = false;
        
        $echoData['needTeamInfo']  = $needInfo;
        $echoData['currentTeamID'] = $team ? $this->hash($team->id) : null;
        $echoData['name']  = $team ? $team->name  : null;
        $echoData['phone'] = $team ? $team->phone : null;
        
        return $echoData;
    }
    
    // 银行卡查询/绑定要写
    
    /**
     * 获取C端用户信息
     */
    protected function getClientInterface()
    {
        $echoData = [];
        $echoData['code']          = 200;
        $echoData['needCuserInfo'] = true;
        
        $clientInfo = ClientInfo::find()->where('sfznumber=:identity')->addParams([':identity' => $this->identity])->asArray()->one();
        
        if($clientInfo && $clientInfo['phone'] && $clientInfo['sfznumber'] && $clientInfo['name'])
            $echoData['needCuserInfo'] = false;
        
        return $echoData;
    }
    
    /**
     * app保存B端用户信息
     */
    protected function appSaveTeamProfileInterface()
    {
        $echoData = [];
        
        $echoData['code']   = 200;
        $echoData['saveRs'] = true;
        
        return $echoData;
    }
    
    /**
     * 提现
     */
    protected function getMoneyInterface()
    {
        $echoData = [];
        
        $echoData['code']  = 200;
        $echoData['getRs'] = true;
        
        return $echoData;
    }
    
    /**
     * 行业动态
     */
    protected function tradeNewsInterface()
    {
        $tradeNews = Xdring::find()
            ->select(['id', 'time', 'content', 'img1', 'img2', 'img3'])
            ->limit($this->limit)
            ->offset($this->offset)
            ->asArray()->all();
        
        $echoData['code']      = 200;
        $echoData['tradeNews'] = [];
        
        if($tradeNews)
        {
            $tradeNews = $this->mapDate($tradeNews, 'time');
            $tradeNews = $this->mapImg($tradeNews, 'img1');
            $tradeNews = $this->mapImg($tradeNews, 'img2');
            $tradeNews = $this->mapImg($tradeNews, 'img3');
            
            $echoData['tradeNews'] = $tradeNews;
        }
        
        return $echoData;
    }
    
    /**
     * 行业动态详情（不需要此接口）
     */
    /* protected function tradeNewsDetailInterface()
    {
        $tradeNew = Xdring::find()
            ->select(['content'])
            ->where('id=:id')
            ->addParams([':id' => $this->tradeNewID])
            ->asArray()->one();
        
        $echoData['code'] = 200;
        
        $tradeNew = $this->strReplace($tradeNew, self::PATH__JUST);
        
        $echoData['tradeNew'] = $tradeNew;
        
        return $echoData;
    } */
    
    /**
     * 咨询小蜜
     */
    protected function consultInterface()
    {
        $consult = new Consult();
        $saveRs = $consult->add($this->teamID, $this->comment);
        
        if(!$saveRs)
            throw new \ErrorException("保存失败");
        else 
        {
            $echoData = [];
            $echoData['code']  = 200;
            $echoData['addRs'] = true;
            
            return $echoData;
        }
    }
    
    /**
     * 获取咨询小蜜历史
     */
    protected function getConsultListInterface()
    {
        $echoData = [];
        $echoData['code']  = 200;
        
        $consult = Consult::find()
            ->where('teamid=:teamid')
            ->addParams([':teamid' => $this->teamID])
            ->limit($this->limit)
            ->offset($this->offset)
            ->orderBy('time DESC')
            ->asArray()->all();
            
        $echoData['consultList'] = $consult;
       
        return $echoData;
    }
    
    /**
     * 生成下载app二维码
     */
    protected function createDownCodeInterface()
    {
        $EWMcodeUrl = $this->createLogoCodeLogic(Yii::$app->params['downloadAppUrl'], 'downloadApp');
        
        $echoData = [];
        $echoData['code']       = 200;
        $echoData['EWMcodeUrl'] = $EWMcodeUrl;
        
        return $echoData;
    }
    
    
    /**
     * 保存C端用户信息
     */
    protected function saveClientInterface()
    {
        $echoData = [];
        $echoData['code']    = 200;
        $echoData['post_rs'] = true;
        
        return $echoData;
    }
    
    /**
     * 我的佣金
     */
    protected function myMoneyInterface()
    {
        $echoData = [];
        $echoData['code'] = 200;
        
        $point = Point::find(['teamid' => $this->teamID])->asArray()->all();
        
        $echoData['allow'] = 0;
        $echoData['total'] = 0;
        $echoData['outs']  = 0;
        
        foreach($point as $key => $val)
        {
            // 可提现余额
            $echoData['allow'] += $val['num'];
            
            // 累计佣金
            if($val['num'] > 0)
                $echoData['total'] += $val['num'];
            
            // 已提现
            if($val['num'] < 0)
                $echoData['outs'] += $val['num'];
        }
        
        return $echoData;
    }
    
    /**
     * 提现记录
     */
    protected function getMoneyListInterface()
    {
        $echoData = [];
        
        $point = Point::find()
                    ->select(['id', 'num', 'time'])
                    ->where('teamid=:teamID AND num < 0')
                    ->addParams([':teamID' => $this->teamID])
                    ->orderBy('time DESC')
                    ->asArray()->all();
        
        $echoData['code']      = 200;
        $echoData['getWMList'] = $point;
        
        return $echoData;
    }
    
    /**
     * 贷款收入记录
     */
    protected function getLoanIncomeListInterface()
    {
        $echoData = [];
        
        $point = $this->getPoint(Point::TYPE_3);
        
        $echoData['code']      = 200;
        $echoData['getLIList'] = $point;
        
        return $echoData;
    }
    
    /**
     * 信用卡收入记录
     */
    protected function getCreditCardIncomeListInterface()
    {
        $echoData = [];
        
        $point = $this->getPoint(Point::TYPE_1);
        
        $echoData['code']       = 200;
        $echoData['getCCIList'] = $point;
        
        return $echoData;
    }
    
    /**
     * 大数据收入记录
     */
    protected function getBigDataIncomeListInterface()
    {
        $echoData = [];
        
        $point = $this->getPoint(Point::TYPE_2);
        
        $echoData['code']       = 200;
        $echoData['getBDIList'] = $point;
        
        return $echoData;
    }
    
    private function getPoint($type)
    {
        
        $point = Point::find()
            // ->select(['id', 'num', 'time'])
            ->limit($this->limit)
            ->offset($this->offset)
            // num1 < 0 AND
            ->where('teamid=:teamID AND type=:type')
            ->addParams([':teamID' => $this->teamID, ':type' => $type])
            ->orderBy('time DESC')
            ->asArray()->all();
        
        return $point;
    }
    
    /**
     * 获取产品详情
     */
    private function getPro()
    {
        if(is_null($this->proType))
            $this->pro = Pro::findOne($this->proID);
        else 
            $this->pro = CreditAndData::findOne($this->proID);
    }
    
    private function getChare()
    {
        if(is_null($this->proType))
            $this->chare = Chare::findOne(['teamid' => $this->teamID, 'proid' => $this->proID]);
        else 
            $this->chare = Chare::findOne(['teamid' => $this->teamID, 'proid' => $this->proID, 'type' => $this->proType]);
    }
    
    private function getEwming()
    {
        // 如果存在产品ID的背景图，则使用相应背景图，否则使用主键为1的背景图
        if(is_null($this->proType))
            $pro_ewming = Ewmimg::findOne(['proid' => $this->proID]);
        else 
            $pro_ewming = Ewmimg::findOne(['proid' => $this->proID, 'type' => $this->proType]);
        
        $one_ewming = Ewmimg::findOne(1);
        
        $this->ewming = $pro_ewming ? $pro_ewming : $one_ewming;
    }
    
    private function updateTeamQRcode($devMemberUrl)
    {
        // 生成二维码
        $teamQrCode = $this->createLogoCodeLogic($devMemberUrl);
        
        $this->team->qrcode = $this->strReplace($teamQrCode, self::PATH_BACK);
        $this->team->save();
        
        return $teamQrCode;
    }
    
    // chare 添加数据
    private function addChare($logoQR = '')
    {
        // 不存在时添加
        if(!$this->chare)
        {
            $this->chare = new Chare();
            $this->chare->teamid = $this->teamID;
            $this->chare->proid  = $this->proID;
            $this->chare->time   = date('Y-m-d H:i:s', time());
            $this->chare->type   = $this->proType;
        }
        
        $this->chare->img = $this->strReplace($logoQR, self::PATH_BACK);
        
        return $this->chare->save();
    }
    
    // 生成带logo的二维码
    private function createLogoCode()
    {
        // 生成二维码
        $logoQR = $this->createLogoCodeLogic($this->shareUrl);
        
        // 更新二维码
        $this->addChare($logoQR);
        
        return $logoQR;
    }
    
    // hash
    public function hash($waitHashData)
    {
        return $waitHashData;
        // 暂时不hash了
        /*$encryptProID = Yii::$app->getSecurity()->hashData($this->chare->id, Team::encryptKey);
        return $encryptProID;*/
    }
    
    // 反hash
    public function DecryptHash($hashData)
    {
        return $hashData;
        
        /*$shareID = Yii::$app->getSecurity()->validateData($this->shareID, Team::encryptKey);
        return $shareID;*/
    }
    
    // hash
    public function hashCustomData($waitHashData)
    {
        $encryptData = Yii::$app->getSecurity()->hashData($waitHashData, Team::encryptKey);
        
        return $encryptData;
    }
    
    // 反 hash
    public function decryptHashCustomData($hashData)
    {
        $decryptData = Yii::$app->getSecurity()->validateData($hashData, Team::encryptKey);
        
        return $decryptData;
    }
    
    // 生成二维码逻辑
    private function createLogoCodeLogic($ptconnect, $fileName = null)
    {
        // file name
        $fileTime = $fileName === null ? self::msectime() : $fileName;
        
        // 存放二维码图片的临时文件夹
        $logo   = Yii::getAlias('@upload').'/logo001.jpg';
        $QR     = './QR.jpg';
        $logoQR = './'.$fileTime.'.jpg';
        
        // 二维码
        $QR = $this->createCode($ptconnect, $QR);
        $this->againCLogoCode($logo, $QR, $logoQR);
        
        // 复制文件
        rename($fileTime.'.jpg', "/data/xdqtg888/".$fileTime.'.jpg');
        $logoQR = Yii::getAlias('@upload').'/'.$fileTime.'.jpg';
        
        return $logoQR;
    }
    
    // 生成二维码
    private function createCode($ptconnect, $QR)
    {
        QRcode::png($ptconnect, $QR, "H", 8);
        
        return $QR;
    }
    
    // 重组二维码和logo
    private function againCLogoCode($logo, $QR, $logoQR)
    {
        if ($logo !== FALSE) 
        {
            $QR   = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width    = imagesx($QR);  // 二维码图片宽度
            $QR_height   = imagesy($QR);  // 二维码图片高度
            $logo_width  = imagesx($logo);// logo图片宽度
            $logo_height = imagesy($logo);// logo图片高度
            
            $logo_qr_width  = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            // 重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        // 输出图片
        imagepng($QR, $logoQR);
    }
    
    private function memberGuide($postID)
    {
        $echoData = [];
        
        $memberGuide = Post::find()->where("id=:id")->addParams([':id' => $postID])->asArray()->one();
        
        $echoData['code'] = 200;
        $echoData['memberGuide']['title']   = $memberGuide['title'];
        $echoData['memberGuide']['content'] = $this->strReplace($memberGuide['content'], self::PATH__JUST);
        $echoData['memberGuide']['image']   = $this->strReplace($memberGuide['img'], self::PATH_JUST);
        $echoData['memberGuide']['time']    = date('Y-m-d', strtotime($memberGuide['time']));
        
        return $echoData;
    }
    
    private function savePromotionID()
    {
        // 保存promotionid
        if($team->promotionid)
            $team->promotionid = $this->promotionid.','.$this->promotionID;
        else
            $team->promotionid = $this->promotionID;
            
        $team->save();
    }
    
    private function mapImg($mapObj, $valName)
    {
        return array_map(function($v) use ($valName)
        {
            $v[$valName] = $this->strReplace($v[$valName], self::PATH_JUST);

            return $v;
        }, $mapObj);
    }
    
    private function mapMImg($mapObj, $valName)
    {
        return array_map(function($v) use ($valName)
        {
            $v[$valName] = $this->strReplace($v[$valName], self::PATH__JUST);
            
            return $v;
        }, $mapObj);
    }
    
    private function mapDate($mapObj, $valName)
    {
        return array_map(function($v) use ($valName)
        {
            $v[$valName] =  date('Y-m-d', strtotime($v[$valName]));
            
            return $v;
        }, $mapObj);
    }
    
    public static function msectime() 
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        
        return $msectime;
    }
    
    /**
     * $type === self::PATH_BACK upload 替换为 /data/xdqtg888
     * $type === self::PATH_JUST /data/xdqtg888 替换为 upload
     * @param string $str
     * @param string $type
     * @return string 
     */
    public function strReplace($str, $type)
    {
        if(empty($str)) return ;
        
        $trim = str_replace('/upload', Yii::getAlias('@upload'), str_replace('\\', '/', $str));
        $back = str_replace(Yii::getAlias('@upload'), 'upload', str_replace('\\', '/', $str));
        $just = str_replace('upload', Yii::getAlias('@upload'), str_replace('\\', '/', $str));
        $_just = str_replace('/upload', Yii::getAlias('@upload'), str_replace('\\', '/', $str));
        
        switch ($type)
        {
            case self::PATH_BACK:
                return $back;
                break;
                
            case self::PATH_JUST:
                return $just;
                break;
                
            case self::PATH__JUST:
                return $_just;
                break;
                
            case self::PATH_TRIM:
                return $trim;
                break;
        }
    }

    /**
     * 购买会员
     */
    protected function buyMember()
    {
        $echoData = [];
        if($this->teamID)
        {
            $echoData['code'] = 200;
            $team = Team::findOne((int) $this->teamID);
            if ($team) {
                if ($team->ispay == 0) {
                    $conf = $this->littleBeeParams = Yii::$app->params['littleBeeParams'];
                    $mt = Tools::getMillisecond();
                    $tradeid = $this->teamID."_1_".$mt."_".Tools::randomkeys(4);

                    $attach = "附加数据";
                    $body = "小蜜蜂会员";
                    $ip = $this->remoteIp;

                    $tradeType = $this->tradeType;
                    $sceneInfo = "{\"h5_info\": {\"type\":\"Wap\",\"wap_url\": \"https://pay.qq.com\",\"wap_name\": \"小蜜蜂会员\"}}";
                    $price = 2;
                    $res = WxPay::unifiedOrder($conf, $team->openid, $attach, $body, $ip, $price, $tradeType, $sceneInfo, $tradeid);
                    if ($res["return_code"] == "SUCCESS") {
                        if ($res["result_code"] == "SUCCESS") {
                            $order = new Order();
                            $order->orderid = $tradeid;
                            $order->teamid = $this->teamID;
                            $order->price = $price;
                            $order->createtime = $mt;
                            $order->tradetype = $tradeType;
                            if ($this->sjTeamID > 0) {
                                $sjteam = Team::findOne((int) $this->sjTeamID);
                                if ($sjteam) {
                                    $order->sjteamid = $this->sjTeamID;
                                    $order->sjopenid = $sjteam->openid;
                                }
                            }
                            if ($order->save()) {
                                if ($tradeType == "MWEB") {
                                    $echoData['mweb_url'] = $res["mweb_url"];
                                } else if ($tradeType == "JSAPI") {
                                    $echoData['timeStamp'] = "".time();
                                    $echoData['nonceStr'] = date("YmdHis");
                                    $echoData['package'] = "prepay_id=".$res["prepay_id"];
                                    $echoData['signType'] = "MD5";
                                    $echoData['paySign'] = WxPay::MakePaySign([
                                        "appId"=>$conf['appid'],
                                        "timeStamp"=>$echoData['timeStamp'],
                                        "nonceStr"=>$echoData['nonceStr'],
                                        "package"=>$echoData['package'],
                                        "signType"=>$echoData['signType'],
                                    ], "MD5", $conf['key']);
                                }
                                return $echoData;
                            }
                            throw new UnauthorizedHttpException('创建预订单失败');
                        }
                    }
                    throw new UnauthorizedHttpException('请求微信失败:'.$res["return_msg"]);
                }
                throw new UnauthorizedHttpException('用户已经购买');
            }
            throw new UnauthorizedHttpException('用户不存在');
        }
        throw new UnauthorizedHttpException('token 是非法的');
    }

    protected function buyMemberCheck() {
        $echoData = [];

        if($this->teamID)
        {
            $echoData['code'] = 200;
            $echoData['message'] = "OK";
            $order = Order::findOne(array('orderid'=>$this->tradeId,'teamid'=>$this->teamID));
            if($order)
            {
                if ($order->status != 0) {
                    if ($order->status != 2) {
                        return $echoData;
                    }
                    throw new UnauthorizedHttpException('错误订单');
                }
                throw new UnauthorizedHttpException('处理中...');
            }
            throw new UnauthorizedHttpException('非法订单.');
        }
        throw new UnauthorizedHttpException('token 是非法的');
    }
}