<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "chare".
 *
 * @property int $id
 * @property int $teamid
 * @property int $proid
 * @property string $time
 * @property string $img
 * @property string $content
 * @property string $cadid
 * @property string $type
 */
class Chare extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chare';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'proid'], 'integer'],
            [['time', 'img', 'content', 'cadid', 'type'], 'string', 'max' => 500],
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
            'proid' => 'Proid',
            'time' => 'Time',
            'img' => 'Img',
            'content' => 'Content',
            'cadid' => 'Cadid',
            'type' => 'Type',
        ];
    }
    
    public function postChare($teamID, $proID, $proType)
    {
        $chare = new self();
        $chare->teamid = $teamID;
        $chare->proid  = $proID;
        $chare->time   = date('Y-m-d H:i:s', time());
        $chare->type   = $proType;
        
        $chare->save();
        
        return $chare;
    }
}
