<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%servicetask_team_locs}}".
 *
 * @property integer $id
 * @property integer $servicetask_id
 * @property integer $team_loc
 *
 * @property Servicetask $servicetask
 * @property TeamlocationMaster $teamLoc
 */
class ServicetaskTeamLocs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%servicetask_team_locs}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['servicetask_id', 'team_loc'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'servicetask_id' => 'Servicetask ID',
            'team_loc' => 'Team Loc',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicetask()
    {
        return $this->hasOne(Servicetask::className(), ['id' => 'servicetask_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
