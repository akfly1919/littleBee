<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "proewm".
 *
 * @property int $id
 * @property int $teamid
 * @property string $img
 * @property string $time
 */
class Proewm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proewm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid'], 'integer'],
            [['img', 'time'], 'string', 'max' => 255],
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
            'img' => 'Img',
            'time' => 'Time',
        ];
    }
}
