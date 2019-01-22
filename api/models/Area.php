<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $citycode
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'citycode'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'citycode' => 'Citycode',
        ];
    }
}
