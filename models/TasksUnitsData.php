<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tasks_units_data}}".
 *
 * @property integer $id
 * @property integer $tasks_unit_id
 * @property integer $task_id
 * @property integer $task_instruct_servicetask_id
 * @property integer $evid_num_id
 * @property integer $form_builder_id
 * @property string $element_value
 * @property string $element_value_origin
 * @property integer $element_unit
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TasksUnitsData extends \yii\db\ActiveRecord
{
	
	public $element_id;
	public $LocationId;
	public $element_details;
	public $client_name;
	public $service_name;
	public $servicetask_id;
	public $teamservice_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_unit_id','form_builder_id', 'element_value', 'element_unit'], 'required'],
            [['tasks_unit_id', 'evid_num_id', 'form_builder_id', 'element_unit', 'created_by', 'modified_by'], 'integer'],
            [['element_value','element_value_origin'], 'string'],
            [['created', 'modified'], 'safe'],
            [['tasks_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnits::className(), 'targetAttribute' => ['tasks_unit_id' => 'id']],
            [['form_builder_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormBuilder::className(), 'targetAttribute' => ['form_builder_id' => 'id']],
            /*[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],*/
            /*[['task_instruct_servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstructServicetask::className(), 'targetAttribute' => ['task_instruct_servicetask_id' => 'id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tasks_unit_id' => 'Tasks Unit ID',
            'task_id' => 'Task ID',
            'task_instruct_servicetask_id' => 'Task Instruct Servicetask ID',
            'evid_num_id' => 'Evid Num ID',
            'form_builder_id' => 'Form Builder ID',
            'element_value' => 'Element Value',
            'element_value_origin'=>'Element Value Origin',
            'element_unit' => 'Element Unit',
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
				if(!isset($this->element_unit)){
					$this->element_unit=0;
				}
				if(!isset($this->created))
					$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			if(!isset($this->modified))
					$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
    
   /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormBuilder() {
    	return $this->hasOne(FormBuilder::className(), ['id' => 'form_builder_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser(){
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit(){
    	return $this->hasOne(Unit::className(), ['id' => 'element_unit']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnits()
    {
    	return $this->hasOne(TasksUnits::className(), ['id' => 'tasks_unit_id']);
    }
    
    public function getFormElementOptions(){
		return $this->hasOne(FormElementOptions::className(), ['id' => 'element_value']);
	}
    /*
     * get Unit Data for Processed by client/case
     * @return dsdd
    public function getUnitData($datesql,$teamservice,$teamlocstr,$teamlocstr,$clientcase,$unitdataIds)
    {
    	$unitdataArr1 = "SELECT `t`.`id`, `ta`.`client_id`, `ts`.`teamservice_id`, `t`.`created`, `ts`.`servicetask_id`, `t`.`element_unit`, `t`.`element_value` AS `element_details`, `t`.`task_id`, `client`.`client_name`, `client`.`id`, `clientcase`.`case_name`, `clientcase`.`id`, `teamserviceId`.`service_name`, `teamserviceId`.`id`, `ts`.`servicetask_id`, `ts`.`team_loc` FROM `tbl_tasks_units_data` `t`
    	INNER JOIN `tbl_tasks` `ta` ON t.task_id=ta.id
    	INNER JOIN `tbl_client` `client` ON ta.client_id=client.id
    	INNER JOIN `tbl_client_case` `clientcase` ON clientcase.id=ta.client_case_id
    	INNER JOIN `tbl_task_instruct_servicetask` `ts` ON ts.id=t.task_instruct_servicetask_id
    	INNER JOIN `tbl_teamservice` `teamserviceId` ON ts.teamservice_id=teamserviceId.id
    	INNER JOIN `tbl_form_builder` `f` ON t.form_builder_id=f.id WHERE $datesql $teamservice $teamlocstr $teamlocstr $clientcase AND f.element_id = '$unitdataIds' AND is_close=0 AND f.element_field_type=1";
    	return $unitdataArr1;
    }*/
}
