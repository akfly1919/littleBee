<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "lunbotp".
 *
 * @property int $id
 * @property string $tupian
 * @property string $lianjie
 * @property int $proid
 * @property int $cadid
 * @property int $type
 */
class Lunbotp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lunbotp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proid', 'cadid', 'type'], 'integer'],
            [['tupian', 'lianjie'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tupian' => 'Tupian',
            'lianjie' => 'Lianjie',
            'proid' => 'Proid',
            'cadid' => 'Cadid',
            'type' => 'Type',
        ];
    }
}
