<?php

namespace api\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "pro".
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
 * @property int $fyType
 * @property double $retPay
 * @property int $important
 *
 * @property Fanyongsz[] $fanyongszs
 * @property Ordergoods[] $ordergoods
 */
class Pro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pro';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['perMil', 'number', 'minifybl', 'zjfybl', 'maxfybl', 'retPay'], 'number'],
            [['liucheng', 'certfifcate', 'question', 'content', 'payroll'], 'string'],
            [['mininum', 'maxnum', 'xiajiasp', 'lunboxs', 'fyType', 'important'], 'integer'],
            [['img', 'name', 'dked', 'loanTime', 'biaoq1', 'biaoq2', 'biaoq3', 'ptconnect', 'haibaoimg'], 'string', 'max' => 255],
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
            'fyType' => 'Fy Type',
            'retPay' => 'Ret Pay',
            'important' => 'Important',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFanyongszs()
    {
        return $this->hasMany(Fanyongsz::className(), ['proid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdergoods()
    {
        return $this->hasMany(Ordergoods::className(), ['proId' => 'id']);
    }
}
