<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%teamservice_sla}}".
 *
 * @property integer $id
 * @property integer $teamservice_id
 * @property integer $team_loc_id
 * @property integer $start_logic
 * @property integer $start_qty
 * @property integer $size_start_unit_id
 * @property integer $end_logic
 * @property integer $end_qty
 * @property integer $size_end_unit_id
 * @property integer $del_qty
 * @property integer $del_time_unit
 * @property integer $project_priority_id
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property TasksTeamSla[] $tasksTeamSlas
 * @property TeamlocationMaster $teamLoc
 * @property Teamservice $teamservice
 */
class TeamserviceSla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teamservice_sla}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamservice_id', 'team_loc_id', 'start_logic', 'start_qty', 'size_start_unit_id', 'end_logic', 'end_qty', 'size_end_unit_id', 'del_qty', 'del_time_unit'], 'required'],
            [['teamservice_id',  'start_logic', 'start_qty', 'size_start_unit_id', 'end_logic', 'end_qty', 'size_end_unit_id', 'del_qty', 'del_time_unit', 'project_priority_id', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamservice_id' => 'Teamservice ID',
            'team_loc_id' => 'Team Location',
            'start_logic' => 'Start Logic',
            'start_qty' => 'Quantity',
            'size_start_unit_id' => 'Unit',
            'end_logic' => 'End Logic',
            'end_qty' => 'Quantity',
            'size_end_unit_id' => 'Unit',
            'del_qty' => 'Quantity',
            'del_time_unit' => 'Unit',
            'project_priority_id' => 'Project Priority',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTeamSlas()
    {
        return $this->hasMany(TasksTeamSla::className(), ['teamservice_sla_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamservice()
    {
        return $this->hasOne(Teamservice::className(), ['id' => 'teamservice_id']);
    }
}
