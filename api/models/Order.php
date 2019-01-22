<?php

namespace api\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property int $teamid
 * @property string $orderid
 * @property int $createtime
 * @property int $price
 * @property int $payfee
 * @property string $payorderid
 * @property int $paytime
 * @property int $sjteamid
 * @property string $sjopenid
 * @property int $status
 * @property int $updatetime
 * @property string $tradetype
 */
class Order extends \yii\db\ActiveRecord
{
    const encryptKey = "littleBee";
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid','createtime','price','payfee','paytime','sjteamid','status', 'updatetime'], 'integer'],
            [['orderid','payorderid','sjopenid', 'tradetype'], 'string', 'max' => 32],
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
            'orderid' => 'Orderid',
            'createtime' => 'Createtime',
            'price' => 'Price',
            'payfee' => 'Payfee',
            'payorderid' => 'Payorderid',
            'paytime' => 'Paytime',
            'sjteamid' => 'Sjteamid',
            'sjopenid' => 'Sjopenid',
            'status' => 'Status',
            'updatetime' => 'Updatetime',
            'tradetype' => 'Tradetype',
        ];
    }
}
