<?php

namespace api\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string $openid
 * @property string $img
 * @property string $nickname
 * @property string $phone
 * @property string $name
 * @property double $allmoney
 * @property double $money
 * @property string $address
 * @property int $status
 * @property int $saomay
 * @property int $guanliy
 * @property string $wxnum
 * @property string $idImg
 * @property string $gsnamse
 * @property string $gsaddress
 * @property int $parid
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $sex
 * @property string $time
 * @property string $logImg
 * @property double $oldmoney
 * @property string $sfznumber
 * @property string $banknumber
 * @property string $qrcode
 * @property string $hctime
 * @property string $ticket
 * @property double $income
 * @property double $lastIncome
 * @property string $promotionid
 * @property string $access_token access_token
 *
 * @property Ordergoods[] $ordergoods
 * @property Ordergoods[] $ordergoods0
 * @property Point[] $points
 */
class Team extends \yii\db\ActiveRecord implements IdentityInterface
{
    const encryptKey = "littleBee";
    
    const STATUS_0 = 0; // 未申请
    const STATUS_1 = 1; // 申请提价待审核
    const STATUS_2 = 2; // 申请成功
    const STATUS_3 = 3; // 申请失败
    const STATUS_4 = 4; // 会员拉黑
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['allmoney', 'money', 'oldmoney', 'income', 'lastIncome'], 'number'],
            [['status', 'saomay', 'guanliy', 'parid', 'hctime'], 'integer'],
            [['openid', 'phone', 'name', 'address', 'province', 'city', 'area', 'sex', 'time'], 'string', 'max' => 100],
            [['img', 'nickname', 'password'], 'string', 'max' => 200],
            [['wxnum', 'idImg', 'gsnamse', 'gsaddress', 'logImg', 'sfznumber', 'banknumber', 'qrcode', 'ticket'], 'string', 'max' => 500],
            [['promotionid', 'api_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'img' => 'Img',
            'nickname' => 'Nickname',
            'password' => 'Password',
            'phone' => 'Phone',
            'name' => 'Name',
            'allmoney' => 'Allmoney',
            'money' => 'Money',
            'address' => 'Address',
            'status' => 'Status',
            'saomay' => 'Saomay',
            'guanliy' => 'Guanliy',
            'wxnum' => 'Wxnum',
            'idImg' => 'Id Img',
            'gsnamse' => 'Gsnamse',
            'gsaddress' => 'Gsaddress',
            'parid' => 'Parid',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'sex' => 'Sex',
            'time' => 'Time',
            'logImg' => 'Log Img',
            'oldmoney' => 'Oldmoney',
            'sfznumber' => 'Sfznumber',
            'banknumber' => 'Banknumber',
            'qrcode' => 'Qrcode',
            'hctime' => 'Hctime',
            'ticket' => 'Ticket',
            'income' => 'Income',
            'lastIncome' => 'Last Income',
            'promotionid' => 'Promotionid',
            'api_token' => 'Api Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdergoods()
    {
        return $this->hasMany(Ordergoods::className(), ['fuId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdergoods0()
    {
        return $this->hasMany(Ordergoods::className(), ['ziId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPoints()
    {
        return $this->hasMany(Point::className(), ['teamid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProewm()
    {
        return $this->hasOne(Proewm::className(), ['teamid' => 'id']);
    }
    
    /**
     * @inheritdoc
     * 根据user_backend表的主键（id）获取用户
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }
    
    /**
     * @inheritdoc
     * 根据access_token获取用户，我们暂时先不实现，我们在文章 http://www.manks.top/yii2-restful-api.html 有过实现，如果你感兴趣的话可以先看看
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // 如果token无效的话，
        if(!static::apiTokenIsValid($token))
            throw new UnauthorizedHttpException("token 是非法的");
            
        return static::findOne(['api_token' => $token]);
    }
    
    /**
     * @inheritdoc
     * 用以标识 Yii::$app->user->id 的返回值
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    /**
     * @inheritdoc
     * 获取auth_key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * @inheritdoc
     * 验证auth_key
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * 生成 api_token
     */
    public function generateApiToken($sessionKey, $teamID)
    {
        $security  = Yii::$app->getSecurity();
        
        // 将下划线修替换为A
        $randomStr = str_replace('_', 'A', $security->generateRandomString());
        $tokenStr  = base64_encode($sessionKey.'_'.$randomStr.'_'.time().'_'.$teamID);
        
        // Yii2 hash加密
        $this->api_token = $security->hashData($tokenStr, self::encryptKey);
    }
    
    /**
     * 生成 app api_token
     */
    public function appGenerateApiToken($teamID)
    {
        $security  = Yii::$app->getSecurity();
        
        // 将下划线修替换为A
        $randomStr = str_replace('_', 'A', $security->generateRandomString());
        $tokenStr  = base64_encode($randomStr.'_'.$randomStr.'_'.time().'_'.$teamID);
        
        // Yii2 hash加密
        $this->api_token = $security->hashData($tokenStr, self::encryptKey);
    }
    
    /**
     * 校验api_token是否有效
     */
    public static function apiTokenIsValid($apiToken)
    {
        if (empty($apiToken)) return false;
        
        $apiToken = base64_decode(Yii::$app->getSecurity()->validateData($apiToken, self::encryptKey));
        
        $teamID    = substr($apiToken, strrpos($apiToken, '_') + 1); // 获取下来teamID
        $apiToken  = substr($apiToken, 0, strrpos($apiToken, '_'));
        $timestamp = substr($apiToken, strrpos($apiToken, '_') + 1); // 获取下来时间戳
        $expire    = Yii::$app->params['littleBeeParams']['tokenExpire'];
        $isExpire  = ($timestamp + $expire) >= time();
        
        return $isExpire;
    }
    
    protected function getTeamIDFromToken()
    {
        $apiToken = base64_decode(Yii::$app->getSecurity()->validateData($apiToken, self::encryptKey));
        $teamID    = substr($apiToken, strrpos($apiToken, '_') + 1); // 获取下来teamID
    }
}
