<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "creditanddata".
 *
 * @property int $id
 * @property string $img
 * @property string $name
 * @property string $dked
 * @property string $loanTime
 * @property double $perMil
 * @property string $liucheng
 * @property string $certfifcate
 * @property string $question
 * @property double $number
 * @property string $content
 * @property string $biaoq1
 * @property string $biaoq2
 * @property string $biaoq3
 * @property int $mininum
 * @property int $maxnum
 * @property double $minifybl
 * @property double $zjfybl
 * @property double $maxfybl
 * @property string $ptconnect
 * @property int $xiajiasp
 * @property string $payroll
 * @property string $haibaoimg
 * @property int $lunboxs
 * @property string $keyMsg
 * @property string $pointMsg
 * @property string $payMsg
 * @property int $type
 * @property int $important
 *
 * @property Ordergoods[] $ordergoods
 */
class CreditAndData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'creditAndData';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['perMil', 'number', 'minifybl', 'zjfybl', 'maxfybl'], 'number'],
            [['mininum', 'maxnum', 'xiajiasp', 'lunboxs', 'type', 'important'], 'integer'],
            [['keyMsg', 'pointMsg', 'payMsg'], 'string'],
            [['img', 'name', 'dked', 'loanTime', 'liucheng', 'certfifcate', 'question', 'content', 'biaoq1', 'biaoq2', 'biaoq3', 'ptconnect', 'payroll', 'haibaoimg'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'dked' => 'Dked',
            'loanTime' => 'Loan Time',
            'perMil' => 'Per Mil',
            'liucheng' => 'Liucheng',
            'certfifcate' => 'Certfifcate',
            'question' => 'Question',
            'number' => 'Number',
            'content' => 'Content',
            'biaoq1' => 'Biaoq1',
            'biaoq2' => 'Biaoq2',
            'biaoq3' => 'Biaoq3',
            'mininum' => 'Mininum',
            'maxnum' => 'Maxnum',
            'minifybl' => 'Minifybl',
            'zjfybl' => 'Zjfybl',
            'maxfybl' => 'Maxfybl',
            'ptconnect' => 'Ptconnect',
            'xiajiasp' => 'Xiajiasp',
            'payroll' => 'Payroll',
            'haibaoimg' => 'Haibaoimg',
            'lunboxs' => 'Lunboxs',
            'keyMsg' => 'Key Msg',
            'pointMsg' => 'Point Msg',
            'payMsg' => 'Pay Msg',
            'type' => 'Type',
            'important' => 'Important',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdergoods()
    {
        return $this->hasMany(Ordergoods::className(), ['cadid' => 'id']);
    }
}
