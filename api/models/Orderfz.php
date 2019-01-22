<?php

namespace api\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string $fzorderid
 * @property string $orderid
 * @property string $payorderid
 * @property int $fzteamid
 * @property string $fzopenid
 * @property string $fzpayorderid
 * @property int $price
 * @property int $ammount
 * @property int $createtime
 * @property int $updatetime
 * @property int $status
 */
class Orderfz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderfz';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fzteamid','price','ammount','createtime','updatetime','status'], 'integer'],
            [['fzorderid','orderid','payorderid','fzopenid','fzpayorderid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fzorderid' => 'Fzorderid',
            'orderid' => 'Orderid',
            'payorderid' => 'Payorderid',
            'fzteamid' => 'Fzteamid',
            'fzopenid' => 'Fzopenid',
            'fzpayorderid' => 'Fzpayorderid',
            'price' => 'Price',
            'ammount' => 'Ammount',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
            'status' => 'Status',
        ];
    }
}
