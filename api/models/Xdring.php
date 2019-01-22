<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "xdring".
 *
 * @property int $id
 * @property int $teamid
 * @property string $time
 * @property string $content
 * @property string $img1
 * @property string $img2
 * @property string $img3
 */
class Xdring extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xdring';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid'], 'integer'],
            [['content'], 'string'],
            [['time'], 'string', 'max' => 100],
            [['img1', 'img2', 'img3'], 'string', 'max' => 500],
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
            'time' => 'Time',
            'content' => 'Content',
            'img1' => 'Img1',
            'img2' => 'Img2',
            'img3' => 'Img3',
        ];
    }
}
