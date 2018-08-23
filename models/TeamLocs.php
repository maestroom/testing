<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Team;

/**
 * This is the model class for table "{{%team_locs}}".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $team_loc
 *
 * @property Team $team
 */
class TeamLocs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team_locs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'team_loc'], 'required'],
            [['team_id', 'team_loc'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
        ];
    }
    public static function getTeamLocationList($team_id){
    	return $teamLocation =ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name')->where('id IN (SELECT team_loc FROM tbl_team_locs WHERE team_id='.$team_id.') AND remove =0 AND  id NOT IN(0)')->all(), 'id','team_location_name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamlocationMaster()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
