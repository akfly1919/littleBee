<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property int $id
 * @property string $name
 * @property string $thistime
 * @property string $lasttime
 * @property int $num
 * @property int $aid
 * @property string $password
 * @property string $img
 * @property string $qianming
 * @property string $rank
 * @property int $shiid
 * @property int $cunid
 * @property int $xiangid
 * @property string $shi
 * @property string $cun
 * @property string $xiang
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num', 'aid', 'shiid', 'cunid', 'xiangid'], 'integer'],
            [['name', 'thistime', 'lasttime', 'password', 'img'], 'string', 'max' => 100],
            [['qianming', 'rank', 'shi', 'cun', 'xiang'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'thistime' => 'Thistime',
            'lasttime' => 'Lasttime',
            'num' => 'Num',
            'aid' => 'Aid',
            'password' => 'Password',
            'img' => 'Img',
            'qianming' => 'Qianming',
            'rank' => 'Rank',
            'shiid' => 'Shiid',
            'cunid' => 'Cunid',
            'xiangid' => 'Xiangid',
            'shi' => 'Shi',
            'cun' => 'Cun',
            'xiang' => 'Xiang',
        ];
    }
}
