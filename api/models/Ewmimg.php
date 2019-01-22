<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "ewmimg".
 *
 * @property int $id
 * @property string $img
 * @property int $proid
 */
class Ewmimg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ewmimg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proid'], 'integer'],
            [['img'], 'string', 'max' => 255],
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
            'proid' => 'Proid',
        ];
    }
}
