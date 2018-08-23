<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\TeamLocs;

/**
 * This is the model class for table "{{%teamlocation_master}}".
 *
 * @property integer $id
 * @property string $team_location_name
 * @property integer $remove
 *
 * @property CaseXteam[] $caseXteams
 * @property ServicetaskTeamLocs[] $servicetaskTeamLocs
 * @property TaskInstructServicetask[] $taskInstructServicetasks
 * @property TasksTeams[] $tasksTeams
 * @property TasksTemplatesServiceTasks[] $tasksTemplatesServiceTasks
 * @property TeamserviceLocs[] $teamserviceLocs
 * @property TeamserviceSla[] $teamserviceSlas
 */
class TeamlocationMaster extends \yii\db\ActiveRecord
{
	
 public $task_count = 0;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teamlocation_master}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_location_name'], 'required'],
            [['team_location_name'], 'string'],
            [['remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_location_name' => 'Team Location',
            'remove' => 'Remove',
        ];
    }
    public function checkIsTeamUsed($id){
    	$response = "N";
    	$team_loc = new TeamLocs();
    	$data = $team_loc::find()->where('team_loc='.$id)->count();
    	if($data > 0){
    		$response = "The selected Team Location cannot be removed. It is already associated with a Team.";
    	}
    	return $response;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseXteams()
    {
        return $this->hasMany(CaseXteam::className(), ['team_loc' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicetaskTeamLocs()
    {
        return $this->hasMany(ServicetaskTeamLocs::className(), ['team_loc' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetasks()
    {
        return $this->hasMany(TaskInstructServicetask::className(), ['team_loc' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTeams()
    {
        return $this->hasMany(TasksTeams::className(), ['team_loc' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTemplatesServiceTasks()
    {
        return $this->hasMany(TasksTemplatesServiceTasks::className(), ['team_loc' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamserviceLocs()
    {
        return $this->hasMany(TeamserviceLocs::className(), ['team_loc' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamserviceSlas()
    {
        return $this->hasMany(TeamserviceSla::className(), ['team_loc_id' => 'id']);
    }
    
    /**
     * @return Team Location List view
     */
    public static function getTeamLocationList(){
    	return $teamLocation =ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name')->where('remove =0 AND  id NOT IN(0)')->all(), 'id','team_location_name');
    } 
    
}
