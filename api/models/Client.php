<?php

namespace api\models;

use Yii;
use api\models\Tools;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property int $teamid
 * @property string $phone
 * @property string $phoneby
 * @property int $shangjiid
 * @property string $time
 * @property string $sfznumber
 * @property string $name
 * @property string $orderbhid
 * @property int $ordId
 * @property int $proid
 * @property int $type
 * @property int $cadid
 *
 * @property Ordergoods $ord
 */
class Client extends \yii\db\ActiveRecord
{
    const TYPE_2 = 2; // 信用卡
    const TYPE_3 = 3; // 贷款
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'shangjiid', 'ordId', 'proid', 'type', 'cadid', 'client_info_id'], 'integer'],
            [['phone', 'phoneby', 'time', 'sfznumber', 'name'], 'string', 'max' => 100],
            [['orderbhid'], 'string', 'max' => 255],
            // [['ordId'], 'exist', 'skipOnError' => true, 'targetClass' => Ordergoods::className(), 'targetAttribute' => ['ordId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamid' => 'Teamid',
            'phone' => 'Phone',
            'phoneby' => 'Phoneby',
            'shangjiid' => 'Shangjiid',
            'time' => 'Time',
            'sfznumber' => 'Sfznumber',
            'name' => 'Name',
            'orderbhid' => 'Orderbhid',
            'ordId' => 'Ord ID',
            'proid' => 'Proid',
            'type' => 'Type',
            'cadid' => 'Cadid',
			'client_info_id' => 'Client Info Id'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrd()
    {
        return $this->hasOne(Ordergoods::className(), ['id' => 'ordId']);
    }

    // c端用户
    public static function putClient($sjTeamID, $identity, $phone, $name, $shareID, $type)
    {
        $miniProgram = new MiniProgram();
        
        // 上级ID
        $sjTeamID = $miniProgram->DecryptHash($sjTeamID);
            
        // 分享ID非空时解析
        if(!is_null($shareID) && $sjTeamID)
        {
            $shareID = $miniProgram->DecryptHash($shareID);
            $chare = Chare::findOne($shareID);
            
            if($chare)
            {
                $clientInfo = ClientInfo::findOne(['sfznumber' => $identity]);
                
                if($clientInfo) $clientInfoSaveRs = true;
                else 
                {
                    // 保存C端用户信息
                    $clientInfo = new ClientInfo();
                    
                    $clientInfo->name      = $name;
                    $clientInfo->sfznumber = $identity;
                    $clientInfo->phone     = $phone;
                    $clientInfo->time      = date('Y-m-d H:i:s', time());
                    
                    $clientInfoSaveRs = $clientInfo->save();
                }
                
                Tools::log($clientInfoSaveRs, "putClient clientInfo save", "");
                
                // 保存用户信息成功
                if($clientInfoSaveRs)
                {
                    // 保存申请产品ID
                    $client = new self();
                    $client->client_info_id = $clientInfo->id;
                    $client->shangjiid      = $sjTeamID;
                    $client->name           = $name;
                    $client->sfznumber      = $identity;
                    $client->phone          = $phone;
                    $client->proid          = $chare->proid;
                    $client->type           = $type;
                    $client->time           = date('Y-m-d H:i:s', time());
                    $client->orderbhid      = (string) Tools::getMillisecond();
                    
                    $clientSaveRs = $client->save();
                    
                    Tools::log($clientSaveRs, "putClient client save", "");
                    
                    return $clientSaveRs;
                }
                
                // 保存失败
                return false;
            }
            
            throw new \InvalidArgumentException('shareID 是非法的');
        }
        
        throw new \InvalidArgumentException('shareID 是非法的或上级ID错误');
    }
}
