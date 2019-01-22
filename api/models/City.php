<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $provincecode
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'provincecode'], 'string', 'max' => 300],
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
            'provincecode' => 'Provincecode',
        ];
    }
}
