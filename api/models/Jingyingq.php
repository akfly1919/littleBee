<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "jingyingq".
 *
 * @property int $id
 * @property int $teamid
 * @property string $name
 * @property string $phone
 * @property string $img
 */
class Jingyingq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jingyingq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid'], 'integer'],
            [['name', 'phone', 'img'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'phone' => 'Phone',
            'img' => 'Img',
        ];
    }
}
