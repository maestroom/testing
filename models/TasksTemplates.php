<?php

namespace app\models;
use kotchuprik\sortable\behaviors;


use Yii;
use app\models\TasksTemplatesServiceTasks;
use app\models\ActivityLog;

/**
 * This is the model class for table "{{%tasks_templates}}".
 *
 * @property integer $id
 * @property integer $temp_sortorder
 * @property string $temp_name
 * @property string $temp_desc
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property TasksTemplatesServiceTasks[] $tasksTemplatesServiceTasks
 */
class TasksTemplates extends \yii\db\ActiveRecord
{

	public $templates_service_tasks;
	public $orderAttribute ='temp_sortorder';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['temp_name',], 'required'],
            [['temp_sortorder', 'created_by', 'modified_by'], 'integer'],
            [['temp_name', 'temp_desc'], 'string'],
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
            'temp_sortorder' => 'Temp Sortorder',
            'temp_name' => 'Temp Name',
            'temp_desc' => 'Temp Desc',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    /**
     * @sortable behaviors
     */
    public function behaviors()
    {
    	return [
    			'sortable' => [
    					'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
    					'query' => self::find(),
    					'orderAttribute'=>'temp_sortorder',
    			],
    	];
    }
    /**
     * @inheritdoc
     */
    public function beforeInsert()
    {
    	$last = $this->find()->orderBy([$this->orderAttribute => SORT_DESC])->limit(1)->one();
    	if ($last === null) {
    		$this->{$this->orderAttribute} = 1;
    	} else {
    		$this->{$this->orderAttribute} = $last->{$this->orderAttribute} + 1;
    	}
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
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Process Service task and its location for Workflow Template
     * */
    public function saveServices($post_data,$id,$mod='add'){
    	$activityLog = new ActivityLog();
    	if($mod=='update'){
    		$activityLog->generateLog('Workflow Templates','Updated', $id, $this->temp_name);
    		TasksTemplatesServiceTasks::deleteAll('task_template_id='.$id);
    	}else{
    		$activityLog->generateLog('Workflow Templates','Added', $id, $this->temp_name);
    	}
    	if(!empty($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $service_task_id=>$locs){
    			if(is_array($locs)){
    				foreach ($locs as $loc){
    					$model=new TasksTemplatesServiceTasks();
    					$model->task_template_id=$id;
    					$model->service_task=$service_task_id;
    					$model->team_loc=$loc;
    					$model->save();
    				}
    			}
    		}
    	}
    	return;
    }
    /**
     * get Service task and its location for Workflow Template
     * */
    public function  getServiceLocs($id = 0){
    	 $sql2 = "SELECT tbl_teamservice.id as teamservice_id, service_name,tbl_servicetask.id as servicetask_id, tbl_servicetask.service_task, tbl_tasks_templates_service_tasks.team_loc, tbl_teamlocation_master.team_location_name 
FROM tbl_teamservice 
INNER JOIN tbl_team ON tbl_teamservice.teamid = tbl_team.id 
INNER JOIN tbl_servicetask ON tbl_teamservice.id = tbl_servicetask.teamservice_id 
INNER JOIN tbl_tasks_templates_service_tasks ON tbl_tasks_templates_service_tasks.service_task = tbl_servicetask.id 
INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_tasks_templates_service_tasks.team_loc 
WHERE tbl_tasks_templates_service_tasks.task_template_id =$id 
ORDER BY tbl_tasks_templates_service_tasks.sort_order";
    	return \Yii::$app->db->createCommand($sql2)->queryAll();
    	
    }
    
    public function processWorkflowData($serviceTaskTemplate_data,$location=array(), $case_id){
    	$serviceTaskTemplate_servicedata=array();
    	if(!empty($serviceTaskTemplate_data)) {
			foreach ($serviceTaskTemplate_data as $serviceData) {
    			$temp_id=$serviceData["id"];
    			if(isset($temp_id) && $temp_id>0) {
	    			$sql2 = "SELECT tbl_teamservice.teamid as teamId, tbl_teamservice.id as teamservice_id, service_name,tbl_servicetask.id as servicetask_id, tbl_servicetask.service_task, tbl_tasks_templates_service_tasks.team_loc, tbl_teamlocation_master.team_location_name
	    			FROM tbl_teamservice
	    			INNER JOIN tbl_team ON tbl_teamservice.teamid = tbl_team.id
	    			INNER JOIN tbl_servicetask ON tbl_teamservice.id = tbl_servicetask.teamservice_id
	    			INNER JOIN tbl_tasks_templates_service_tasks ON tbl_tasks_templates_service_tasks.service_task = tbl_servicetask.id
	    			INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_tasks_templates_service_tasks.team_loc
	    			WHERE tbl_servicetask.publish=1 and tbl_servicetask.task_hide=0 AND tbl_tasks_templates_service_tasks.task_template_id =".$temp_id." 
	    			AND (tbl_tasks_templates_service_tasks.team_loc in (select team_loc from tbl_teamservice_locs where tbl_teamservice_locs.teamservice_id=tbl_servicetask.teamservice_id)	OR tbl_tasks_templates_service_tasks.team_loc=0)
	    			ORDER BY tbl_tasks_templates_service_tasks.sort_order";
	    			$servicesdata = Yii::$app->db->createCommand($sql2)->queryAll();
	    			foreach ($servicesdata as $k=>$mydata) {
	    				if($mydata['teamId']==1) {
	    					$mydata['team_loc']=0;
	    				}
	    				$teamId=$mydata['teamId'];	
	    				$exculdeservice=CaseXteam::find()->where(['client_case_id'=>$case_id,'team_loc'=>$mydata['team_loc'],'teamservice_id'=>$mydata['teamservice_id']])->select(['id'])->innerJoinWith(['teamservice' => function(\yii\db\ActiveQuery $query) use ($teamId) {
							$query->where(['tbl_teamservice.teamid'=>$teamId]);
						}],false)->count();
	    				if($exculdeservice==0) {
	    					if(!empty($location)) {
	    						if(in_array($mydata['team_loc'],$location)) {
	    							$serviceTaskTemplate_servicedata[$temp_id][]=$mydata;
	    						}
	    					} else {
	    						$serviceTaskTemplate_servicedata[$temp_id][]=$mydata;
	    					}
	    				}
	    			}
    			}
    		}
    	}
    	return $serviceTaskTemplate_servicedata;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTemplatesServiceTasks()
    {
        return $this->hasMany(TasksTemplatesServiceTasks::className(), ['task_template_id' => 'id']);
    }
}
