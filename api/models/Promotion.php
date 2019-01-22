<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "promotion".
 *
 * @property int $id
 * @property string $img
 * @property string $head
 * @property string $date
 * @property string $content
 */
class Promotion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['img', 'head', 'date'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img' => 'Img',
            'head' => 'Head',
            'date' => 'Date',
            'content' => 'Content',
        ];
    }
}
