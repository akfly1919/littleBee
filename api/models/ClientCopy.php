<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "client_copy".
 *
 * @property int $id
 * @property int $teamid
 * @property string $phone
 * @property string $phoneby
 * @property int $shangjiid
 * @property string $time
 * @property string $sfznumber
 * @property string $name
 * @property int $ordId
 * @property int $proid
 * @property string $orderbhid
 * @property int $type
 * @property int $cadid
 *
 * @property Ordergoods $ord
 */
class ClientCopy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_copy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'shangjiid', 'ordId', 'proid', 'type', 'cadid'], 'integer'],
            [['phone', 'phoneby', 'time', 'sfznumber', 'name', 'orderbhid'], 'string', 'max' => 100],
            [['ordId'], 'exist', 'skipOnError' => true, 'targetClass' => Ordergoods::className(), 'targetAttribute' => ['ordId' => 'id']],
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
            'ordId' => 'Ord ID',
            'proid' => 'Proid',
            'orderbhid' => 'Orderbhid',
            'type' => 'Type',
            'cadid' => 'Cadid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrd()
    {
        return $this->hasOne(Ordergoods::className(), ['id' => 'ordId']);
    }
}
