<?php
namespace api\models;

use Yii;

/**
 * This is the model class for table "consult".
 *
 * @property int $id
 * @property int $teamid
 * @property string $comment
 * @property string $time
 * @property string $img1
 * @property string $img2
 * @property string $img3
 * @property int $status
 * @property string $huifunr
 * @property string $time1
 * @property int $tongji
 */
class Consult extends \yii\db\ActiveRecord
{
    
    const STATUS_1 = 1; // 待处理
    const STATUS_2 = 2; // 已处理
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consult';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'status', 'tongji'], 'integer'],
            [['comment', 'time', 'img1', 'img2', 'img3', 'huifunr', 'time1'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamid' => 'Teamid',
            'comment' => 'Comment',
            'time' => 'Time',
            'img1' => 'Img1',
            'img2' => 'Img2',
            'img3' => 'Img3',
            'status' => 'Status',
            'huifunr' => 'Huifunr',
            'time1' => 'Time1',
            'tongji' => 'Tongji',
        ];
    }
    
    // 添加咨询
    public function add($teamID, $comment)
    {
        $consult = new self();
        $consult->teamid  = $teamID;
        $consult->comment = $comment;
        $consult->time    = date('Y-m-d H:i:s', time());
        $consult->status  = self::STATUS_1;
        
        return $consult->save();
    }
}
