<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "point".
 *
 * @property int $id
 * @property int $teamid
 * @property double $num
 * @property string $time
 * @property int $proid
 * @property int $status
 * @property double $num1
 * @property int $cadid
 * @property int $type
 *
 * @property Team $team
 */
class Point extends \yii\db\ActiveRecord
{
    /**
     * 提现状态
     */
    const STATUS_0 = 0; // 默认
    const STATUS_1 = 1; // 提现申请
    const STATUS_2 = 2; // 审核通过
    const STATUS_3 = 3; // 审核不通过
    
    /**
     * 产品类型
     */
    const TYPE_1 = 1; // 信用卡
    const TYPE_2 = 2; // 大数据
    const TYPE_3 = 3; // 借贷
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'proid', 'status', 'cadid', 'type'], 'integer'],
            [['num', 'num1'], 'number'],
            [['time'], 'string', 'max' => 255],
            [['teamid'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['teamid' => 'id']],
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
            'num' => 'Num',
            'time' => 'Time',
            'proid' => 'Proid',
            'status' => 'Status',
            'num1' => 'Num1',
            'cadid' => 'Cadid',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'teamid']);
    }
    
    /**
     * 保存提现
     * @param integer $getMoney 提现金额
     * @param integer $afterTax 税后金额
     * @param integer $teamID   teamID 
     * 
     * @return boolean 保存结果
     */
    public static function putPoint($getMoney, $afterTax, $teamID)
    {
        $point = new self();
        
        $point->num    = (0 - $getMoney);
        $point->num1   = $afterTax;
        $point->teamid = $teamID;
        $point->time   = date('Y-m-d H:i:s', time());
        $point->status = self::STATUS_1;
        
        return $point->save();
    }
}
