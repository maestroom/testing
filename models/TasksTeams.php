<?php

namespace app\models;

use Yii;
use app\models\TasksTeamSla;

/**
 * This is the model class for table "{{%tasks_teams}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $team_id
 * @property integer $team_loc
 * @property integer $teamservice_id
 */
class TasksTeams extends \yii\db\ActiveRecord
{
	public $team_priority;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_teams}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'team_id', 'team_loc'], 'required'],
            [['task_id', 'team_id', 'team_loc','team_loc_prority'], 'integer'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['team_id' => 'id']],
            [['team_loc'], 'exist', 'skipOnError' => true, 'targetClass' => TeamlocationMaster::className(), 'targetAttribute' => ['team_loc' => 'id']],
            /*[['teamservice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teamservice::className(), 'targetAttribute' => ['teamservice_id' => 'id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
            'teamservice_id' => 'Teamservice ID',
            'team_loc_prority'=>'Team Location Prority'
        ];
    }
    public function getTasks(){
		   return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
	}
	
	public function getTasksTeamSla(){
		return $this->hasOne(TasksTeamSla::className(), ['tasks_teams_id' => 'id']);
	}
	
	public function getTeamservice(){
		return $this->hasOne(Teamservice::className(),['id'=>'teamservice_id']);
	}
	
	public function getTeamlocationMaster(){
		return $this->hasOne(TeamlocationMaster::className(),['id'=>'team_loc']);
	}
	
	public function getProjectSecurity()
    {
        return $this->hasMany(ProjectSecurity::className(), ['team_id' => 'team_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamPriority()
    {
        return $this->hasOne(PriorityTeam::className(), ['id' => 'team_loc_prority']);
    }
}
