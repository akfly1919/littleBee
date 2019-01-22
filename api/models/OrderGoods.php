<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "ordergoods".
 *
 * @property int $id
 * @property string $ordId
 * @property int $ziId
 * @property int $fuId
 * @property int $proId
 * @property string $startTime
 * @property string $endTime
 * @property double $commission
 * @property int $status
 * @property string $sfznumber
 * @property string $midTime
 * @property double $loans
 * @property string $hkTime
 * @property int $type
 * @property int $futeamid
 * @property int $prospid
 * @property int $procadzt
 * @property int $cadid
 * @property int $cadspid
 *
 * @property Client[] $clients
 * @property ClientCopy[] $clientCopies
 * @property Pro $pro
 * @property Creditanddata $cad
 * @property Team $fu
 * @property Team $zi
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordergoods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ziId', 'fuId', 'proId', 'status', 'type', 'futeamid', 'prospid', 'procadzt', 'cadid', 'cadspid'], 'integer'],
            [['commission', 'loans'], 'number'],
            [['ordId', 'startTime', 'endTime', 'sfznumber', 'midTime', 'hkTime'], 'string', 'max' => 255],
            [['proId'], 'exist', 'skipOnError' => true, 'targetClass' => Pro::className(), 'targetAttribute' => ['proId' => 'id']],
            [['cadid'], 'exist', 'skipOnError' => true, 'targetClass' => Creditanddata::className(), 'targetAttribute' => ['cadid' => 'id']],
            [['fuId'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['fuId' => 'id']],
            [['ziId'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['ziId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ordId' => 'Ord ID',
            'ziId' => 'Zi ID',
            'fuId' => 'Fu ID',
            'proId' => 'Pro ID',
            'startTime' => 'Start Time',
            'endTime' => 'End Time',
            'commission' => 'Commission',
            'status' => 'Status',
            'sfznumber' => 'Sfznumber',
            'midTime' => 'Mid Time',
            'loans' => 'Loans',
            'hkTime' => 'Hk Time',
            'type' => 'Type',
            'futeamid' => 'Futeamid',
            'prospid' => 'Prospid',
            'procadzt' => 'Procadzt',
            'cadid' => 'Cadid',
            'cadspid' => 'Cadspid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Client::className(), ['ordId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCopies()
    {
        return $this->hasMany(ClientCopy::className(), ['ordId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPro()
    {
        return $this->hasOne(Pro::className(), ['id' => 'proId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCad()
    {
        return $this->hasOne(Creditanddata::className(), ['id' => 'cadid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFu()
    {
        return $this->hasOne(Team::className(), ['id' => 'fuId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZi()
    {
        return $this->hasOne(Team::className(), ['id' => 'ziId']);
    }
}
