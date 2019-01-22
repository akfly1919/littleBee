<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "fanyongsz".
 *
 * @property int $id
 * @property int $proid
 * @property int $mininum
 * @property int $maxnum
 * @property double $minifybl
 * @property double $zjfybl
 * @property double $maxfybl
 *
 * @property Pro $pro
 */
class Fanyongsz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fanyongsz';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proid', 'mininum', 'maxnum'], 'integer'],
            [['minifybl', 'zjfybl', 'maxfybl'], 'number'],
            [['proid'], 'exist', 'skipOnError' => true, 'targetClass' => Pro::className(), 'targetAttribute' => ['proid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'proid' => 'Proid',
            'mininum' => 'Mininum',
            'maxnum' => 'Maxnum',
            'minifybl' => 'Minifybl',
            'zjfybl' => 'Zjfybl',
            'maxfybl' => 'Maxfybl',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPro()
    {
        return $this->hasOne(Pro::className(), ['id' => 'proid']);
    }
}
