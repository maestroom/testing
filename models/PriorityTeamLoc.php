<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\TeamlocationMaster;

/**
 * This is the model class for table "{{%priority_team_loc}}".
 *
 * @property integer $id
 * @property integer $priority_team_id
 * @property integer $team_id
 * @property integer $team_loc_id
 *
 * @property PriorityTeam $priorityTeam
 * @property Team $team
 * @property TeamlocationMaster $teamLoc
 */
class PriorityTeamLoc extends \yii\db\ActiveRecord
{
	public $priority_team_location;
	public $team_location;
	
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%priority_team_loc}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['priority_team_id', 'team_id', 'team_loc_id'], 'required'],
            [['priority_team_id', 'team_id', 'team_loc_id', 'priority_order'], 'integer'],
            [['priority_team_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriorityTeam::className(), 'targetAttribute' => ['priority_team_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['team_id' => 'id']],
            [['team_loc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeamlocationMaster::className(), 'targetAttribute' => ['team_loc_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'priority_team_id' => 'Priority Team ID',
            'team_id' => 'Team ID',
            'team_loc_id' => 'Team Loc ID',
            'priority_order' => 'Priority Order'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriorityTeam()
    {
        return $this->hasOne(PriorityTeam::className(), ['id' => 'priority_team_id']);
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
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc_id']);
    }
    
    /**
     * @return Team Name & Team Location Name
     */
     public function getPriorityTeamLocation($id)
     {
		// return $id;
		$teamLocPriority = explode(" - ",$id);
		$teamPrority = Team::find()->where(['id' => $teamLocPriority[0]])->asArray()->one();	
		$teamProrityLoc = TeamlocationMaster::find()->where(['id' => $teamLocPriority[1]])->asArray()->one();
		
		return $teamPrority['team_name']." - ".$teamProrityLoc['team_location_name'];
	 } 
}
