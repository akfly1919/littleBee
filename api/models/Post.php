<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $yinwen
 * @property string $img
 * @property string $writer
 * @property string $time
 * @property string $content
 * @property int $leixing
 */
class Post extends \yii\db\ActiveRecord
{ 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['leixing'], 'integer'],
            [['title', 'writer', 'time'], 'string', 'max' => 100],
            [['yinwen'], 'string', 'max' => 500],
            [['img'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'yinwen' => 'Yinwen',
            'img' => 'Img',
            'writer' => 'Writer',
            'time' => 'Time',
            'content' => 'Content',
            'leixing' => 'Leixing',
        ];
    }
}
