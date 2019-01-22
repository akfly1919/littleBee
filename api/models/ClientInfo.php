<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "client_info".
 *
 * @property int $id
 * @property int $teamid
 * @property string $phone
 * @property string $phoneby
 * @property int $shangjiid
 * @property string $time
 * @property string $sfznumber
 * @property string $name
 */
class ClientInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid'], 'integer'],
            [['phone', 'phoneby', 'time', 'sfznumber', 'name'], 'string', 'max' => 100],
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
            'time' => 'Time',
            'sfznumber' => 'Sfznumber',
            'name' => 'Name',
        ];
    }
}
