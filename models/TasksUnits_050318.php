<?php

namespace app\models;

use Yii;
use app\models\Tasks;
use app\models\ActivityLog;
use app\models\TasksUnitsTransactionLog;
use app\models\Servicetask;
use app\models\Pricing;
use app\models\TasksUnitsBilling;
use app\models\TasksUnitsData;
use app\models\TasksUnitsTodos;
use app\models\SettingsEmail;
use app\models\EmailCron;
use app\models\Mydocument;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%tasks_units}}".
 *
 * @property integer $id
 * @property integer $task_instruct_id
 * @property integer $task_instruct_servicetask_id
 * @property integer $unit_assigned_to
 * @property integer $unit_status
 * @property string $is_transition
 * @property string $duration
 * @property string $unit_complete_date
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TasksUnits extends \yii\db\ActiveRecord
{
    public $totalunits, $task_priority,$team_priority,$client_id,$client_case_id;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id','task_instruct_id', 'unit_assigned_to', 'unit_status', 'is_transition'], 'required'],
            [['task_id','task_instruct_id', 'task_instruct_servicetask_id', 'unit_assigned_to', 'unit_status', 'created_by', 'modified_by'], 'integer'],
            [['is_transition'], 'string'],
            [['duration', 'unit_complete_date', 'created', 'modified','client_case_id','client_id'], 'safe'],
            [['task_instruct_servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstructServicetask::className(), 'targetAttribute' => ['task_instruct_servicetask_id' => 'id']],
            [['task_instruct_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstruct::className(), 'targetAttribute' => ['task_instruct_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_instruct_id' => 'Task Instruct ID',
            'task_instruct_servicetask_id' => 'Task Instruct Servicetask ID',
            'unit_assigned_to' => 'Unit Assigned To',
            'unit_status' => 'Unit Status',
            'is_transition' => 'Is Transition',
            'duration' => 'Duration',
            'unit_complete_date' => 'Unit Complete Date',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'client_case_id' => 'Cases',
            'client_id' => 'Client'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
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
    public function changeTaskStatus($servicetask_data,$task_id,$status)
    {
		$task_info = Tasks::find()->where(['tbl_tasks.id' => $task_id])->joinWith('clientCase')->one();
		$error="";
    	if(!empty($servicetask_data)){
    		foreach ($servicetask_data['servicetask_id'] as $key=>$servicetask_id){
    			$taskunit = TasksUnits::findOne($servicetask_data['taskunit_id'][$key]);
    			$taskunit_id = $taskunit->id; 
    			if(isset($status) && $status==1){ // STARTED
    				if($status == $taskunit->unit_status){
    					$error = 'One of the selected Task already in start mode.';
    					break;
    				}
    				if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    					$error = 'The selected task can not be started unless it has been assigned.';
    					break;
    				}
    			}
    			else if (isset($status) && $status==2) {// Task On Pause
    				if($status == $taskunit->unit_status){
    					$error = 'One of the selected task already in pause mode.';
    					break;
    				}
    				if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    					$error='The selected task cannot be paused unless the task has been assigned.';
    					break;
    				}
    			}
    			else if (isset($status) && $status==3) {// Task On HOLD
    				if($status == $taskunit->unit_status){
    					$error = 'one of the selected task already in hold mode.';
    					break;
    				}
    				if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    					$error= 'The selected task cannot be hold unless the task has been assigned and started.';
    					break;
    				}
    			}
    			else if (isset($status) && $status==4) {// Completed
    			//die($status."test".$taskunit->unit_status." = ".$taskunit->id);
    				if($status == $taskunit->unit_status){
    					$error = 'One of the selected task already in complete mode.';
    					break;
    				}
    				if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    					$error= 'The selected task cannot be completed unless the task has been assigned, on hold, started or paused.';
    					break;
    				}
    				// $clientId = $task_info->client_id;
    				$clientId = $task_info->clientCase->client_id;
    				$caseId   = $task_info->client_case_id;
    				$service_info =Servicetask::findOne($servicetask_id);
    				$team_id=$service_info->teamId;
    				$team_loc=$taskunit->taskInstructServicetask->team_loc;
    				
    				if($service_info->billable_item == 2 && $service_info->force_entry == 1){
    					/*$pricepointexist = Pricing::model()->chkPricePointExistByClientCaseTeam($clientId,$caseId,$team_id,$servicetask_id);
    					if(!empty($pricepointexist)){
    						$billabledata = TasksUnitsBilling::find()->where('task_id = '.$task_id.' AND  tasks_unit_id = '.$taskunit_id)->count();
    						if($billabledata){
    							$error= "The selected tasks cannot be completed unless one or more billable items are added to the task.";
    							break;
    						}
    					}*/
	    				$pricepointexist = (new Pricing)->chkPricePointExistByClientCaseTeam($clientId,$caseId,$team_id,$servicetask_id,$team_loc);                                       
	    				if(!empty($pricepointexist)){
                                                $billabledata = TasksUnitsBilling::find()->joinWith(['tasksUnits' => function (\yii\db\ActiveQuery $query) use ($task_id) {
                                                    $query->joinWith('tasks')->where(['task_id'=>$task_id]);
                                                }])->where('tasks_unit_id = '.$taskunit_id)->count();                                                
		    				if($billabledata == 0){
		    					return "The selected Tasks cannot be Completed unless one or more Billable Items are added to the Task";
		    				}
	    				}
    				}
    				// $todoCount = TasksUnitsTodos::find()->where("task_id = ".$task_id." AND tasks_unit_id = ".$taskunit_id." AND complete = 0")->count();
    				$todoCount = TasksUnitsTodos::find()->joinWith('taskUnit')->where("tbl_tasks_units.task_id = ".$task_id." AND tasks_unit_id = ".$taskunit_id." AND complete = 0")->count();
    				if($todoCount){
    					$error= "In order to complete service task, please complete all todo items.";
    					break;
    				}
    			}
    		}
    	}
    	if(isset($error) && $error!=""){
    		return $error;
    	}else{
			if(!empty($servicetask_data)){
    			foreach ($servicetask_data['servicetask_id'] as $key=>$servicetask_id){
    				$taskunit_id = $servicetask_data['taskunit_id'][$key];
    				$taskunit = TasksUnits::findOne($taskunit_id);
    				if(isset($status) && $status==1){//STARTED
    					$taskunit->unit_status=1;
    					$taskunit->duration=date('Y-m-d H:i:s');
    					$taskunit->save(false);
    					$task_info->task_status=1;
    					$task_info->save(false);
    					$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    					(new ActivityLog)->generateLog('Project', 'StartedTask', $task_id, $activity_name);
    					$duration = "0 days 0 hours 0 min";
    					(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,1,$duration);
    				}
    				else if (isset($status) && $status==2) {// Task On Pause
    					$taskunit->unit_status=2;
    					$taskunit->duration=date('Y-m-d H:i:s');
    					$taskunit->save(false);
    					$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    					(new ActivityLog)->generateLog('Project', 'PausedTask', $task_id, $activity_name);
    					$duration = "0 days 0 hours 0 min";
    					(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,2,$duration);
    					(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    				}
    				else if (isset($status) && $status==3) {// Task On HOLD
    					$taskunit->unit_status=3;
    					$taskunit->duration=date('Y-m-d H:i:s');
    					$taskunit->save(false);
    					$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    					(new ActivityLog)->generateLog('Project', 'OnHoldTask', $task_id, $activity_name);
    					$duration = "0 days 0 hours 0 min";
    					(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,2,$duration);
    					(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    				}
    				else if (isset($status) && $status==4) {// Completed
						$service_info =Servicetask::findOne($servicetask_id);
    					$taskunit->unit_status=4;
    					$taskunit->duration=date('Y-m-d H:i:s');
    					$taskunit->unit_complete_date=date('Y-m-d H:i:s');
						if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
							$taskunit->unit_assigned_to=Yii::$app->user->identity->id;
						}
    					$taskunit->save(false);
    					$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    					(new ActivityLog)->generateLog('Project', 'CompletedTask', $task_id, $activity_name);
    					$duration = "0 days 0 hours 0 min";
    					(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,4,$duration);
    					(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    					
    					$taskInstructServicetask = $taskunit->taskInstructServicetask;
						$sort_order = $taskInstructServicetask->sort_order;
						$previoustaskInstructServicetask = TaskInstructServicetask::find()->select('id')->where('tbl_task_instruct_servicetask.task_id = '.$task_id.'  AND tbl_task_instruct_servicetask.sort_order<='.($sort_order-1))->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) { $query->where('tbl_tasks_units.unit_status = 4'); }])->count();
    					$nexttaskInstructServicetask = TaskInstructServicetask::find()->select(['tbl_task_instruct_servicetask.id','tbl_task_instruct_servicetask.servicetask_id'])->where('tbl_task_instruct_servicetask.task_id = '.$task_id.'  AND tbl_task_instruct_servicetask.sort_order = '.($sort_order + 1))->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) {  $query->where("tbl_tasks_units.unit_assigned_to != 0 AND tbl_tasks_units.unit_assigned_to != '' AND tbl_tasks_units.unit_status != 4"); }])->one();
						if(isset($previoustaskInstructServicetask) && $previoustaskInstructServicetask==($sort_order-1) && 	$nexttaskInstructServicetask != 0){
							$nextunit=TasksUnits::find()->where("unit_assigned_to != 0 AND unit_assigned_to != '' AND unit_status = 0 AND task_id = ".$task_id)->orderBy('sort_order')->one();
							$checkprev_is_complete=0;
							if(isset($nextunit->sort_order) && $nextunit->sort_order!="") {	
								$checkprev_is_complete=TasksUnits::find()->where("task_id = ".$task_id." AND unit_status = 4 AND  sort_order=".($nextunit->sort_order-1) )->orderBy('sort_order')->count();
							}
							if($checkprev_is_complete > 0) {
								//SettingsEmail::sendEmail
								EmailCron::saveBackgroundEmail(15,'pending_tasks',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'unit_arr'=>$nextunit->id,'unit_assigned_to'=>$nexttaskInstructServicetask->tasksUnits[0]->unit_assigned_to,'servicetask_id'=>$nextunit->taskInstructServicetask->servicetask_id));
							}
						}else if($sort_order == 0 && $nexttaskInstructServicetask != 0) {
							$nextunit=TasksUnits::find()->where("unit_assigned_to != 0 AND unit_assigned_to != '' AND unit_status = 0 AND task_id = ".$task_id)->orderBy('sort_order')->one();
							$checkprev_is_complete=0;
							if(isset($nextunit->sort_order) && $nextunit->sort_order!="") {	
								$checkprev_is_complete=TasksUnits::find()->where("task_id = ".$task_id." AND unit_status = 4 AND  sort_order=".($nextunit->sort_order-1) )->orderBy('sort_order')->count();
							}
							if($checkprev_is_complete > 0) {
								//SettingsEmail::sendEmail
								EmailCron::saveBackgroundEmail(15,'pending_tasks',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'unit_arr'=>$nextunit->id,'unit_assigned_to'=>$nexttaskInstructServicetask->tasksUnits[0]->unit_assigned_to,'servicetask_id'=>$nextunit->taskInstructServicetask->servicetask_id));
							}
						}
    					
    					/*Check Team service completed or not And sent Service complete mail*/
    					$query = TasksUnits::find()->where('task_instruct_servicetask_id IN ( SELECT id FROM tbl_task_instruct_servicetask WHERE teamservice_id ='.$service_info->teamservice_id .' )');
    					$teambyservicetask_count =  $query->count();
    					$completedteambyservicetask_count =  TasksUnits::find()->where('unit_status=4 AND task_instruct_servicetask_id IN ( SELECT id FROM tbl_task_instruct_servicetask WHERE teamservice_id ='.$service_info->teamservice_id .' )')->count();
    					if($teambyservicetask_count == $completedteambyservicetask_count){
    						$settingsEmail = SettingsEmail::find()->select('email_teamservice')->where('id=20')->one();
    						if(isset($settingsEmail->email_teamservice) && $settingsEmail->email_teamservice != ""){
    							if(is_numeric($settingsEmail->email_teamservice) && $settingsEmail->email_teamservice == $service_info->teamservice_id){
    								//SettingsEmail::model()->sendEmail(5, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'teamservice'=>$service_info->teamservice_id));
    							}else{
    								$services = explode(",",$settingsEmail->email_teamservice);
    								if(in_array($service_info->teamservice_id,$services)){
    									//SettingsEmail::model()->sendEmail(5, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'teamservice'=>$service_info->teamservice_id));
    								}
    							}
    						}else{
    							//SettingsEmail::model()->sendEmail(5, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'teamservice'=>$service_info->teamservice_id));
    						}
    					}
    					$task_all = TasksUnits::find()->where('tbl_tasks_units.task_id = '.$taskunit->task_id)->count();
						$task_complete = TasksUnits::find()->where('tbl_tasks_units.task_id = '.$taskunit->task_id.' AND unit_status = 4')->count();
						if($task_all == $task_complete){
							//SettingsEmail::sendEmail
							EmailCron::saveBackgroundEmail(5, 'is_sub_com_task', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $taskunit->task_id));	
						}
    					/*Check Team service completed or not And sent Service complete mail*/
    				}
    			}
    		}
    		return 'OK';
    	}
    	return 'OK';//No Response
    }
    
    /** IRT 159 **/
    public function getTasksUnitsAttachments()
    {
    	return $this->hasMany(Mydocument::className(), ['reference_id' => 'id'])->andOnCondition(['origination' => 'Data Statistics']);
    }
    
    /** IRT 159 **/
    public function getTasksUnitsData()
    {
    	return $this->hasMany(TasksUnitsData::className(), ['tasks_unit_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedUser(){
    	return $this->hasOne(User::className(), ['id' => 'unit_assigned_to']);
    }
    
    public function getTasks(){
    	return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetask(){
    	return $this->hasOne(TaskInstructServicetask::className(), ['id' => 'task_instruct_servicetask_id']);
    }
    
    
    public function getTaskInstruct(){
    	return $this->hasOne(TaskInstruct::className(), ['id' => 'task_instruct_id'])->andOnCondition(['isactive' => 1]);
    }
    
    
    
    /*
     * Check The Servicetask Is Pending Or NOT..? IN Case Assignments
     * */
    public function getServiceTaskIdPending($task_id,$case_id=0,$team_id=0,$belongtocurr_team_serarr=array())
	{
		$pending=0;
		if(empty($belongtocurr_team_serarr)){
			$belongtocurr_team = Servicetask::find()->select('tbl_servicetask.id')->where("teamId IN ('".$team_id."')")->all();
 			if(isset($belongtocurr_team[0]->id) && $belongtocurr_team[0]->id!=""){
				foreach ($belongtocurr_team as $btct){
					$belongtocurr_team_serarr[$btct->id]=$btct->id;
				}
			}
		}
		
		$userId = Yii::$app->user->identity->id;
	
		$assign_data = TasksUnits::find()->select(['tbl_tasks_units.task_instruct_servicetask_id','tbl_tasks_units.unit_status'])->where('tbl_tasks_units.task_id ='.$task_id)->andWhere('tbl_tasks_units.unit_assigned_to = '.$userId.'')
		->innerJoinWith(['taskInstructServicetask'=> function (\yii\db\ActiveQuery $query)  { 
			$query->select(['tbl_task_instruct_servicetask.servicetask_id','tbl_task_instruct_servicetask.sort_order']); 
			}])->all();
		
		if(!empty($assign_data)){
			foreach ($assign_data as $assi){
				$ordkey = $assi->taskInstructServicetask->sort_order;
				if($ordkey==1 && ($assi->unit_status !=1 && $assi->unit_status !=4)){
					$pending++;
					$service_ids[$assi->taskInstructServicetask->servicetask_id]=$assi->taskInstructServicetask->servicetask_id;
				}else{
					if($ordkey != "" && $assi->unit_status == 0){
						$pending++;
						$service_ids[$assi->taskInstructServicetask->servicetask_id]=$assi->taskInstructServicetask->servicetask_id;
					}
				}
				
			}
		}	
		return $service_ids;
	}
	 /*
     * Check The Servicetask Is Pending Or NOT..? IN Team Assignments
     */
	public function getTaskPendingServiceTaskTeam()
	{
		$userId = Yii::$app->user->identity->id;
		$service_ids = array();
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
		}
		$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as C ON Taskindividual.id = C.id ";
		if (Yii::$app->db->driverName == 'mysql') {
			$subquery="Select unit_status From tbl_tasks_units as tu
    INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
     Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";				
		}else{
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
    INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
     Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
		}
		$sql = "Select B.servicetask_id,B.task_unit_id,B.task_id,location.team_location_name,team.team_name,servicetask.service_task,B.case_name,B.client_name,B.priority,B.task_duedate,B.unit_status,B.task_timedue,location.id as team_location,team.id as team_id, B.task_date_time From ( 
    Select A.* From ( 
        SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue, C.task_date_time FROM tbl_tasks_units as t 
        LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
		$taskduedatejoin
        INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)  
        LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
        LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id 
        LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
        WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.team_id != 1 AND t.team_loc != 0) as A
    Where ($subquery) = 4 
    Union All 
    SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue, C.task_date_time  FROM tbl_tasks_units as t 
    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
	$taskduedatejoin
    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id) 
    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
    WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.team_id != 1 AND t.team_loc != 0
	) AS B INNER JOIN tbl_teamlocation_master as location ON location.id = B.team_loc INNER JOIN tbl_team as team ON team.id = B.team_id INNER JOIN tbl_servicetask as servicetask ON servicetask.id = B.servicetask_id GROUP BY B.servicetask_id,B.task_unit_id, B.task_id,location.team_location_name,team.team_name,servicetask.service_task,B.case_name,B.client_name,B.priority,B.task_duedate,B.unit_status,B.task_timedue,location.id,team.id,B.task_date_time";
    
    $service_ids = \Yii::$app->db->createCommand($sql)->queryAll();
			
		//$service_ids = ArrayHelper::map(Servicetask::find()->select('id')->where('id IN ('.$sql.')')->all(),'id','id');
		return $service_ids;
	}
	
	/* Function for Get Pending Task On My Case Assignment */
	public function getTaskPendingServiceTaskCase(){
		$userId = Yii::$app->user->identity->id;
		$service_ids = array();
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
		}
		$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as C ON Taskindividual.id = C.id ";
		if (Yii::$app->db->driverName == 'mysql') {
			$subquery="Select unit_status From tbl_tasks_units as tu 
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
		Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
		}else{
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu 
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
		Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
		}
		$sql = "Select B.servicetask_id,B.task_unit_id,B.task_id,location.team_location_name,team.team_name,servicetask.service_task,B.case_name,B.client_name,B.priority,B.task_duedate,B.unit_status,B.task_timedue,location.id as team_location,team.id as team_id,B.client_case_id, B.task_date_time From ( 
		Select A.* From ( 
			SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time as task_date_time FROM tbl_tasks_units as t 
			LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
			$taskduedatejoin
			INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)  
			LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
			LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id 
			LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
			WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.team_id = 1 AND t.team_loc =0) as A
		Where ( $subquery ) = 4 
		Union All 
		SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time as task_date_time FROM tbl_tasks_units as t 
		LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
		$taskduedatejoin
		INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)  
		LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
		LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
		LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
		WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.team_id = 1 AND t.team_loc =0
		) AS B INNER JOIN tbl_teamlocation_master as location ON location.id = B.team_loc INNER JOIN tbl_team as team ON team.id = B.team_id INNER JOIN tbl_servicetask as servicetask ON servicetask.id = B.servicetask_id GROUP BY B.servicetask_id,B.task_unit_id, B.task_id,location.team_location_name,team.team_name,servicetask.service_task,B.case_name,B.client_name,B.priority,B.task_duedate,B.unit_status,B.task_timedue,location.id,team.id,B.client_case_id,B.task_date_time";
		$service_ids = \Yii::$app->db->createCommand($sql)->queryAll();
		return $service_ids;
		
	
	}
	/* Function for Not Started Task of My Team Assignment
	 * */
	public function getTaskNotstartedTaskTeam(){
		$userId = Yii::$app->user->identity->id;
		$service_ids = array();
		if (Yii::$app->db->driverName == 'mysql') {
			$subquery="Select unit_status From tbl_tasks_units as tu
			    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
			    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
		}else{
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
			    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
			    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
		}
		$sql = "Select B.task_unit_id From ( 
		Select A.* From ( 
	        SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id FROM tbl_tasks_units as t 
	        LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
	        INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)  
	        LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
	        LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id 
	        LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
	        WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId.") as A
			    Where ($subquery) = 4 
			Union All 
			    SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id  FROM tbl_tasks_units as t 
			    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
			    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)  
			    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
			    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
			    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
			    WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId.") AS B GROUP BY B.task_unit_id";
	$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
	if (Yii::$app->db->driverName == 'mysql') {
		$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
	} else {
		//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
		$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
	}
	$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as C ON Taskindividual.id = C.id ";
    $final_data = "SELECT t.id as task_unit_id,location.team_location_name,team.team_name,Taskindividual.task_id as task_id, t.servicetask_id,taskservice.service_task,  t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time  FROM tbl_tasks_units as t 
	    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
		$taskduedatejoin
	    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)  
	    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
	    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
	    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
	    INNER JOIN tbl_teamlocation_master as location ON location.id = t.team_loc
	    INNER JOIN tbl_team as team ON team.id = t.team_id
	    INNER JOIN tbl_servicetask as taskservice ON taskservice.id = t.servicetask_id
	    WHERE t.unit_status=0 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND t.unit_assigned_to = ".$userId." AND t.id NOT IN(".$sql.") AND t.team_id != 1 AND t.team_loc != 0";
    
    $service_ids = \Yii::$app->db->createCommand($final_data)->queryAll();
		return $service_ids;
	}
	public function getTaskPendingTaskTeamOverviewQuery($team_id, $team_loc,$pendingDateCondition = '',$forQuery)
	{
            $groupby = '';
            if($forQuery == 'notStarted'){
                $selectVars = 'B.task_unit_id';
            }else{
                $selectVars = 'B.unit_assigned_to, COUNT(B.task_unit_id) as totalunits';
                $groupby = ' GROUP BY B.unit_assigned_to';
            }
			if (Yii::$app->db->driverName == 'mysql') {
				$subquery="Select unit_status From tbl_tasks_units as tu
		    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
		    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
			}else{
				$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
		    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
		    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
			}
            $sql_pending = "Select $selectVars  From (
		Select A.* From (
        SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, t.unit_assigned_to FROM tbl_tasks_units as t
        LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
        INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
        LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
        LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
        LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
        WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc.") as A
		    Where ( $subquery ) = 4
		Union All
		    SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id,t.unit_assigned_to  FROM tbl_tasks_units as t
		    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
		    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
		    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
		    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
		    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
		    WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc.") AS B WHERE B.unit_assigned_to != 0 ".$pendingDateCondition." $groupby";
            return $sql_pending;
        }
	public function getTaskPendingTaskTeamOverview($team_id, $team_loc,$pendingDateCondition = '')
	{            
                $sql_pending = $this->getTaskPendingTaskTeamOverviewQuery($team_id, $team_loc,$pendingDateCondition,'pending');
		$data_pending = \Yii::$app->db->createCommand($sql_pending)->queryAll();                
		return $data_pending;
	}
	
	/* Function for Not Started Task of My Team Assignment
	 * */
	public function getTaskNotstartedTaskTeamOverview($team_id, $team_loc,$notStartedDateCondition = ''){
		
		$service_ids = array();
                $sql = $this->getTaskPendingTaskTeamOverviewQuery($team_id, $team_loc,$notStartedDateCondition,'notStarted');
//		$sql = "Select B.task_unit_id From (
//		Select A.* From (
//	        SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id FROM tbl_tasks_units as t
//	        LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
//	        INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
//	        LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
//	        LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
//	        LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
//	        WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc.") as A
//			    Where ( Select unit_status From tbl_tasks_units as tu
//			    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
//			    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ) = 4
//			Union All
//			    SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id  FROM tbl_tasks_units as t
//			    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
//			    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
//			    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
//			    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
//			    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
//			    WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc.") AS B GROUP BY B.task_unit_id";
		 
	
		$final_data = "SELECT COUNT(t.id) as totalunits,  t.unit_assigned_to FROM tbl_tasks_units as t
	    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
	    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
	    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
	    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
	    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
	    INNER JOIN tbl_teamlocation_master as location ON location.id = t.team_loc
	    INNER JOIN tbl_team as team ON team.id = t.team_id
	    INNER JOIN tbl_servicetask as taskservice ON taskservice.id = t.servicetask_id
	    WHERE t.unit_status=0 AND task.task_closed=0 AND task.task_cancel=0 AND t.unit_assigned_to != 0 AND t.id NOT IN ($sql) AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND t.team_id = $team_id AND t.team_loc = ".$team_loc.$notStartedDateCondition.' group by t.unit_assigned_to';
		//echo $final_data;die;
		$service_ids = \Yii::$app->db->createCommand($final_data)->queryAll();
		
		return $service_ids;
	}
	
    public function getTasksUnitsDetails($task_id,$case_id='',$team_id = '',$task_status,$type='',$source = '',$team_loc = '')
	{
		
		$actual_times   = $actual_mins = $total_est_time = 0;
        $mainarra       = array();

        if(Yii::$app->db->driverName=="mysql"){
            $query = "SELECT 
                    unit_status,tbl_teamservice.id as team_service_id,
                    (CASE WHEN char_length(tbl_tasks_units.sort_order) = 1 THEN CONCAT('0',tbl_tasks_units.sort_order) ELSE tbl_tasks_units.sort_order END) as sort_order,
                    tbl_teamservice.service_name as teamservice_name,
                    tbl_teamlocation_master.team_location_name as team_loc,
                    tbl_servicetask.service_task as servicetask_name,
                    tbl_tasks_units.servicetask_id as service_task_id,
                    (CASE WHEN unit_status != 0 THEN (SELECT MIN(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=1) END) as started, 
                    (CASE WHEN unit_status = 4 THEN (SELECT MAX(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=4) END) as completed, 
                    tbl_tasks_units.est_time as est
            FROM tbl_tasks_units 
            INNER JOIN tbl_teamservice ON tbl_teamservice.id = tbl_tasks_units.teamservice_id
            INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_tasks_units.team_loc
            INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id
            WHERE task_id = {$task_id}
            ORDER BY tbl_tasks_units.sort_order";
        } else {
            $query = "SELECT 
                    unit_status,tbl_teamservice.id as team_service_id,
                    LEN(tbl_tasks_units.sort_order),
                    (CASE WHEN LEN(tbl_tasks_units.sort_order) = 1 THEN '0'+CAST(tbl_tasks_units.sort_order as VARCHAR) ELSE CAST(tbl_tasks_units.sort_order as VARCHAR) END) as sort_order,
                    tbl_teamservice.service_name as teamservice_name,                    
                    tbl_teamlocation_master.team_location_name as team_loc,
                    tbl_servicetask.service_task as servicetask_name,
                    (CASE WHEN unit_status != 0 THEN (SELECT MIN(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=1) END) as started, 
                    (CASE WHEN unit_status = 4 THEN (SELECT MAX(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=4) END) as completed, 
                    tbl_tasks_units.est_time as est
            FROM tbl_tasks_units 
            INNER JOIN tbl_teamservice ON tbl_teamservice.id = tbl_tasks_units.teamservice_id
            INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_tasks_units.team_loc
            INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id
            WHERE task_id = {$task_id}
            ORDER BY tbl_tasks_units.sort_order";
        }
        $tasksUnitsData = \Yii::$app->db->createCommand($query)->queryAll();        
        
        if(!empty($tasksUnitsData)){                                                    
            foreach($tasksUnitsData as $key => $unitdata){
                $mainarra[$key]['started'] ='';
                $mainarra[$key]['completed'] = '';	
                $unit_status = $unitdata['unit_status'];                        
                if($unit_status!=0 && $unitdata['started']!='0000-00-00 00:00:00')
                {
                    $mainarra[$key]['started']= (new Options)->ConvertOneTzToAnotherTz($unitdata['started'],"UTC",$_SESSION["usrTZ"]);
                }
                if($unit_status==4 && $unitdata['completed'] != '0000-00-00 00:00:00')
                    $mainarra[$key]['completed']=(new Options)->ConvertOneTzToAnotherTz($unitdata['completed'],"UTC",$_SESSION["usrTZ"]);
                if($unit_status=="0"){
                    if($task_status=="3"){
                        if ($source == 'pdf')
                            $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold">&#xf017;</em>';
                        else
                            $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Task On Hold"></span>';
                        }else{
                            if ($source == 'pdf')
                                $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-primary" title="Task Not Started">&#xf017;</em>';
                        else
                            $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';	 
                        }	                    
                }else if($unit_status=="1"){
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-success" title="Task Started">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-success" title="Task Started"></span>';                    
                }else if($unit_status=="2"){
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-info" title="Task On Pause">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-info" title="Task On Pause"></span>'; 
                }else if($unit_status=="3"){	      		 	
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Task On Hold"></span>';
                }else if($unit_status=="4"){                                
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-dark" title="Task Completed">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-dark" title="Task Completed"></span>';
                }
               // print_r($unitdata);die;
                $name = $unitdata['sort_order'].' - '.$unitdata['teamservice_name'].' - '.$unitdata['team_loc'].' - '.$unitdata['servicetask_name'];               
                if($type=='team'){
					                               
					//'team_loc'=>$unitdata['team_loc'],
                        $mainarra[$key]['task_name'] = Html::a($name, null, ['href'=>Url::toRoute(['track/index', 'taskid' => $task_id, 'team_id' => $team_id,'team_loc'=>$team_loc, 'servicetask_id'=>$unitdata['service_task_id'], 'option'=>'All'], true)]);
                }if($type=='case'){                                
                        $mainarra[$key]['task_name'] = Html::a($name, null, ['href'=>Url::toRoute(['track/index', 'taskid' => $task_id, 'case_id' => $case_id, 'servicetask_id'=>$unitdata['service_task_id'],'option'=>'All'],true)]);
                }                                                 
                $mainarra[$key]['estHr']    = round($unitdata['est']);
                $mainarra[$key]['est']      = $unitdata['est'];                
                $total_est_time             +=$unitdata['est'];
                if($unit_status == 4){
                    //In terms of days                                         
                    if(isset($unitdata['started']) && $unitdata['started'] != '' && isset($unitdata['completed']) && $unitdata['completed']!= ''){
							$time_diff = strtotime($unitdata['completed'])-strtotime($unitdata['started']);
							if(floor($time_diff/60) > 0){
								$mainarra[$key]['actual']   = (new TasksUnits)->dateDiff($unitdata['started'],$unitdata['completed'],3);                            
								//print_r($mainarra[$key]['actual']);
							}else{
								$mainarra[$key]['actual']   = 'less then min';
							}
					}else{
						$mainarra[$key]['actual']   = '0';
					}
                    $intervals                  = array('day','hour','minutes');                            
                    $durationArr                = explode(' ', $mainarra[$key]['actual']);  
                    $counter                    = 1;
                    $durationHours              = 0;
                    $durationmins               = 0;
                    foreach($intervals as $single_val){
                        if(isset($durationArr[$counter]) && ($durationArr[$counter] == 'days' || $durationArr[$counter] == 'day')){
                            $durationHours = $durationHours + floor(($durationArr[($counter-1)]*86400)/3600);
                        }else if(isset($durationArr[$counter]) && ($durationArr[$counter] == 'hours' || $durationArr[$counter] == 'hour') ){
                            $durationHours = $durationHours + $durationArr[($counter-1)];
                        }else if(isset($durationArr[$counter]) && ($durationArr[$counter] == 'minutes' || $durationArr[$counter] == 'minute')){
                            $durationmins = $durationmins + $durationArr[($counter-1)];
                        }
                        $counter += 2;
                    }                                        
                    $mainarra[$key]['actualHr']  = $durationHours;
                    $actual_times               += $durationHours;
                    $actual_mins                += $durationmins;
                }else{
                    $mainarra[$key]['actual']   = '';
                }                                                
            }
        }        
        $result['arrResult']    = $mainarra;        
        if($actual_mins > 60){
            // Add Hours to actual time if minute exceed 60
            $temp_hours =  round(($actual_mins / 60),2);
            $actual_times += $temp_hours;
        }else{
			$temp_hours =  round(($actual_mins / 60),2);
			$actual_times += $temp_hours;
		}
        $result['actual_times']     = $actual_times;        
        $result['total_est_time']   = $total_est_time;        
        return $result;
    }

	public function getTeamTasksUnitsDetails($task_id,$case_id='',$team_id = '',$task_status,$type='',$source = '',$team_loc = '')
	{
		
		$actual_times   = $actual_mins = $total_est_time = 0;
        $mainarra       = array();

        if(Yii::$app->db->driverName=="mysql"){
            $query = "SELECT 
                    unit_status,tbl_teamservice.id as team_service_id,
                    (CASE WHEN char_length(tbl_tasks_units.sort_order) = 1 THEN CONCAT('0',tbl_tasks_units.sort_order) ELSE tbl_tasks_units.sort_order END) as sort_order,
                    tbl_teamservice.service_name as teamservice_name,
                    tbl_teamlocation_master.team_location_name as team_loc,
                    tbl_servicetask.service_task as servicetask_name,
                    tbl_tasks_units.servicetask_id as service_task_id,
                    (CASE WHEN unit_status != 0 THEN (SELECT MIN(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=1) END) as started, 
                    (CASE WHEN unit_status = 4 THEN (SELECT MAX(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=4) END) as completed, 
                    tbl_tasks_units.est_time as est
            FROM tbl_tasks_units 
            INNER JOIN tbl_teamservice ON tbl_teamservice.id = tbl_tasks_units.teamservice_id
            INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_tasks_units.team_loc
            INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id
            WHERE task_id = {$task_id} AND team_id={$team_id} AND team_loc={$team_loc}
            ORDER BY tbl_tasks_units.sort_order";
        } else {
            $query = "SELECT 
                    unit_status,tbl_teamservice.id as team_service_id,
                    LEN(tbl_tasks_units.sort_order),
                    (CASE WHEN LEN(tbl_tasks_units.sort_order) = 1 THEN '0'+CAST(tbl_tasks_units.sort_order as VARCHAR) ELSE CAST(tbl_tasks_units.sort_order as VARCHAR) END) as sort_order,
                    tbl_teamservice.service_name as teamservice_name,                    
                    tbl_teamlocation_master.team_location_name as team_loc,
                    tbl_servicetask.service_task as servicetask_name,
                    (CASE WHEN unit_status != 0 THEN (SELECT MIN(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=1) END) as started, 
                    (CASE WHEN unit_status = 4 THEN (SELECT MAX(transaction_date) FROM tbl_tasks_units_transaction_log WHERE tbl_tasks_units_transaction_log.tasks_unit_id = tbl_tasks_units.id AND transaction_type=4) END) as completed, 
                    tbl_tasks_units.est_time as est
            FROM tbl_tasks_units 
            INNER JOIN tbl_teamservice ON tbl_teamservice.id = tbl_tasks_units.teamservice_id
            INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_tasks_units.team_loc
            INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id
            WHERE task_id = {$task_id} AND team_id={$team_id} AND team_loc={$team_loc}
            ORDER BY tbl_tasks_units.sort_order";
        }
        $tasksUnitsData = \Yii::$app->db->createCommand($query)->queryAll();        
        
        if(!empty($tasksUnitsData)){                                                    
            foreach($tasksUnitsData as $key => $unitdata){
                $mainarra[$key]['started'] ='';
                $mainarra[$key]['completed'] = '';	
                $unit_status = $unitdata['unit_status'];                        
                if($unit_status!=0 && $unitdata['started']!='0000-00-00 00:00:00')
                {
                    $mainarra[$key]['started']= (new Options)->ConvertOneTzToAnotherTz($unitdata['started'],"UTC",$_SESSION["usrTZ"]);
                }
                if($unit_status==4 && $unitdata['completed'] != '0000-00-00 00:00:00')
                    $mainarra[$key]['completed']=(new Options)->ConvertOneTzToAnotherTz($unitdata['completed'],"UTC",$_SESSION["usrTZ"]);
                if($unit_status=="0"){
                    if($task_status=="3"){
                        if ($source == 'pdf')
                            $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold">&#xf017;</em>';
                        else
                            $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Task On Hold"></span>';
                        }else{
                            if ($source == 'pdf')
                                $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-primary" title="Task Not Started">&#xf017;</em>';
                        else
                            $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';	 
                        }	                    
                }else if($unit_status=="1"){
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-success" title="Task Started">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-success" title="Task Started"></span>';                    
                }else if($unit_status=="2"){
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-info" title="Task On Pause">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-info" title="Task On Pause"></span>'; 
                }else if($unit_status=="3"){	      		 	
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Task On Hold"></span>';
                }else if($unit_status=="4"){                                
                    if ($source == 'pdf')
                        $mainarra[$key]['status'] = '<em class="fa fa-clock-o text-dark" title="Task Completed">&#xf017;</em>';
                    else
                        $mainarra[$key]['status'] = '<span tabindex="0" class="fa fa-clock-o text-dark" title="Task Completed"></span>';
                }
               // print_r($unitdata);die;
                $name = $unitdata['sort_order'].' - '.$unitdata['teamservice_name'].' - '.$unitdata['team_loc'].' - '.$unitdata['servicetask_name'];               
                if($type=='team'){
					                               
					//'team_loc'=>$unitdata['team_loc'],
                        $mainarra[$key]['task_name'] = Html::a($name, null, ['href'=>Url::toRoute(['track/index', 'taskid' => $task_id, 'team_id' => $team_id,'team_loc'=>$team_loc, 'servicetask_id'=>$unitdata['service_task_id'], 'option'=>'All'], true)]);
                }if($type=='case'){                                
                        $mainarra[$key]['task_name'] = Html::a($name, null, ['href'=>Url::toRoute(['track/index', 'taskid' => $task_id, 'case_id' => $case_id, 'servicetask_id'=>$unitdata['service_task_id'],'option'=>'All'],true)]);
                }                                                 
                $mainarra[$key]['estHr']    = round($unitdata['est']);
                $mainarra[$key]['est']      = $unitdata['est'];                
                $total_est_time             +=$unitdata['est'];
                if($unit_status == 4){
                    //In terms of days                                         
                    if(isset($unitdata['started']) && $unitdata['started'] != '' && isset($unitdata['completed']) && $unitdata['completed']!= ''){
							$time_diff = strtotime($unitdata['completed'])-strtotime($unitdata['started']);
							if(floor($time_diff/60) > 0){
								$mainarra[$key]['actual']   = (new TasksUnits)->dateDiff($unitdata['started'],$unitdata['completed'],3);                            
								//print_r($mainarra[$key]['actual']);
							}else{
								$mainarra[$key]['actual']   = 'less then min';
							}
					}else{
						$mainarra[$key]['actual']   = '0';
					}
                    $intervals                  = array('day','hour','minutes');                            
                    $durationArr                = explode(' ', $mainarra[$key]['actual']);  
                    $counter                    = 1;
                    $durationHours              = 0;
                    $durationmins               = 0;
                    foreach($intervals as $single_val){
                        if(isset($durationArr[$counter]) && ($durationArr[$counter] == 'days' || $durationArr[$counter] == 'day')){
                            $durationHours = $durationHours + floor(($durationArr[($counter-1)]*86400)/3600);
                        }else if(isset($durationArr[$counter]) && ($durationArr[$counter] == 'hours' || $durationArr[$counter] == 'hour') ){
                            $durationHours = $durationHours + $durationArr[($counter-1)];
                        }else if(isset($durationArr[$counter]) && ($durationArr[$counter] == 'minutes' || $durationArr[$counter] == 'minute')){
                            $durationmins = $durationmins + $durationArr[($counter-1)];
                        }
                        $counter += 2;
                    }                                        
                    $mainarra[$key]['actualHr']  = $durationHours;
                    $actual_times               += $durationHours;
                    $actual_mins                += $durationmins;
                }else{
                    $mainarra[$key]['actual']   = '';
                }                                                
            }
        }        
        $result['arrResult']    = $mainarra;        
        if($actual_mins > 60){
            // Add Hours to actual time if minute exceed 60
            $temp_hours =  round(($actual_mins / 60),2);
            $actual_times += $temp_hours;
        }else{
			$temp_hours =  round(($actual_mins / 60),2);
			$actual_times += $temp_hours;
		}
        $result['actual_times']     = $actual_times;        
        $result['total_est_time']   = $total_est_time;        
        return $result;
    }
	
	/* Function for Not Started Task of My Case Assignment
	 * */
	
	public function getTaskNotstartedTaskCase(){
		
		$userId = Yii::$app->user->identity->id;
		$service_ids = array();
		 if(Yii::$app->db->driverName=="mysql") {
			$subquery="Select unit_status From tbl_tasks_units as tu 
    INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
		 } else {
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu 
    INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
		 }
		$sql = "Select B.task_unit_id From ( 
    Select A.* From ( 
        SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id FROM tbl_tasks_units as t 
        LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
        INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id) 
        LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
        LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id 
        LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
        WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.team_id = 1 AND t.team_loc = 0) as A
    Where ($subquery) = 4 
    Union All 
    SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id  FROM tbl_tasks_units as t 
    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id) 
    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
    WHERE t.unit_status=0 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.sort_order=1 AND t.unit_assigned_to = ".$userId." AND t.team_id = 1 AND t.team_loc = 0) AS B GROUP BY B.task_unit_id";	
  
    $final_data = "SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id,taskservice.service_task  FROM tbl_tasks_units as t 
    LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
    INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id) 
    LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
    LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
    LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
    INNER JOIN tbl_servicetask as taskservice ON taskservice.id = t.servicetask_id
    WHERE t.unit_status=0 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.team_id = 1 AND t.team_loc = 0 AND t.id NOT IN (".$sql.")";
    
    $service_ids = \Yii::$app->db->createCommand($final_data)->queryAll();
    
    return $service_ids;
		
	}
	
	public function getTaskPendingServiceTask($task_id,$case_id=0,$team_id=0,$location,$belongtocurr_team_serarr=array())
	{
		$service_ids = array();
		if (Yii::$app->db->driverName == 'mysql') {
			$subquery="Select unit_status From tbl_tasks_units as tu INNER JOIN 	tbl_task_instruct_servicetask tbl_task_instruct_servicetask ON (tu.task_instruct_servicetask_id=tbl_task_instruct_servicetask.id)  Where tbl_task_instruct_servicetask.sort_order = A.sort_order - 1 And tu.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
		}else{
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu INNER JOIN 	tbl_task_instruct_servicetask tbl_task_instruct_servicetask ON (tu.task_instruct_servicetask_id=tbl_task_instruct_servicetask.id)  Where tbl_task_instruct_servicetask.sort_order = A.sort_order - 1 And tu.task_id = A.task_id ORDER BY tu.id DESC";
		}	
		$sql = "Select B.servicetask_id From (
	Select A.* From ( 
		SELECT t.task_id as task_id, tbl_task_instruct_servicetask.servicetask_id, tbl_task_instruct_servicetask.sort_order
		FROM tbl_tasks_units as t 
		INNER JOIN tbl_tasks task ON (t.task_id=task.id) 
        INNER JOIN 	tbl_task_instruct_servicetask tbl_task_instruct_servicetask ON (t.task_instruct_servicetask_id=tbl_task_instruct_servicetask.id) 
		LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
		LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.task_id=task.id) 
		WHERE tbl_task_instruct_servicetask.team_id=".$team_id." AND tbl_task_instruct_servicetask.team_loc=".$location." AND t.unit_status=0 AND (tbl_task_instruct_servicetask.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND tbl_task_instruct_servicetask.team_id=".$team_id." AND tbl_task_instruct_servicetask.team_loc=".$location." AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0
	) as A 
	Where ($subquery) = 4 
	Union All 
	SELECT t.task_id as task_id, tbl_task_instruct_servicetask.servicetask_id,  tbl_task_instruct_servicetask.sort_order
	FROM tbl_tasks_units as t 
	INNER JOIN tbl_tasks task ON (t.task_id=task.id) 
    INNER JOIN 	tbl_task_instruct_servicetask tbl_task_instruct_servicetask ON (t.task_instruct_servicetask_id=tbl_task_instruct_servicetask.id) 
	LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id) 
	LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.task_id=task.id) 
	WHERE tbl_task_instruct_servicetask.team_id=".$team_id." AND tbl_task_instruct_servicetask.team_loc=".$location." AND t.unit_status=0 AND tbl_task_instruct_servicetask.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND tbl_task_instruct_servicetask.team_id=".$team_id." AND tbl_task_instruct_servicetask.team_loc=".$location." AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0
) AS B
GROUP BY B.servicetask_id";	

		$service_ids = ArrayHelper::map(Servicetask::find()->select('id')->where('id IN ('.$sql.')')->all(),'id','id');
		return $service_ids;
	}
	
	
	

	/**
	 * To get Array for Estimated Servicetasks of a Project #
	 * @param $task_service_data [array] (sorted servicetask of a project)
	 * @param $taskId [int]
	 * @param $task_status [int]
	 * @param $est_hours [float] (total estimated hours)
	 * @param $serviceest_data [array] (servicetask wise est_time)
	 * @param $caseId [int]
	 * @param $teamId [int]
	 * @param $team_loc [int]
	 * @param $type [string] ( Case / Team / Canceled / Closed )
	 * @param $teamLocation [array] (TeamLocation ID => Name)
	 */
	public function getTrackTaskProgress($task_service_data, $taskId, $task_status, $est_hours, $serviceest_data, $caseId=0, $teamId=0, $team_loc=0, $type="", $teamLocation, $source=''){
    	
        $mainarra=array();
    	$orderkey=0;
    	$i=0;
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$roleInfo=Role::find()->select(['role_type'])->where(['id'=>$roleId])->one();
		$User_Role=explode(',',$roleInfo->role_type);
		
		if(!empty($task_service_data))
    	{
    		foreach ($task_service_data as $key=>$value)
    		{
    			//echo "<pre>",print_r($value->attributes),"</pre>";die;
    			$name= "";
    			$stask=Servicetask::findOne($value->servicetask_id);
				$unit_data=TasksUnits::find()->where(['servicetask_id'=>$value->servicetask_id,'task_id'=>$taskId])->andWhere('unit_status!=0')->all();
				//echo "<pre>",print_r($unit_data),"</pre>";die;
				$unit_status=0;
				$unit_created="";
				$unit_complete_date="";
				$unit_started_date="";
				$mainarra[$i]['created']="";
                $totaldurationdays = "";
                $durationArr = array();
				$mainarra[$i]['completed']="";
				if(isset($unit_data[0]->id) && $unit_data[0]->id!=0)
				{
                	foreach ($unit_data as $udata)
                    {
                    	$unit_status=$udata->unit_status;
                        $unit_assigned_to=$udata->unit_assigned_to;
                        $unit_created=$udata->created;
                        $unit_complete_date=$udata->unit_complete_date;
                        $unit_started_date  = (new TasksUnitsTransactionLog)->getStartedDateTimeByUnit($udata->id);//Options::model()->ConvertOneTzToAnotherTz($unit_created,"UTC",$_SESSION["usrTZ"]);;;
//                        echo '<pre>';print_r($unit_started_date);die('nelson');
                        $taskunittodologArr = TasksUnitsTodoTransactionLog::find()->select(['duration'])->joinWith('tasksUnitsTodos')->where(['tbl_tasks_units_todos.tasks_unit_id'=>$udata->id])->all();
                        $tasktranslogArr    = TasksUnitsTransactionLog::find()->select(['duration'])->where(['tasks_unit_id' => $udata->id])->all();
                        $ttotalday = 0;
                        $tminute   = 0;
                        $tmhours   = 0;
                        $thours    = 0;

                        $todototalday = 0;
                        $todominute = 0;
                        $todomhours = 0;
                        $todohours  = 0;
                        $totalday   = 0;
                        $hours      = 0;
                        $minute     = 0;
                        $todoactualhours = 0;
                        $tactualhours = 0;
                        $ttotalhours = 0;
                        $todototalhours = 0;
                        $totalactualhours = 0;
                        if($unit_status == "4"){                            
                        	if(count($tasktranslogArr) > 0){  
//                                        echo '<pre>';
                        		foreach($tasktranslogArr as $tasktranslogdata){	
//                                                echo $tasktranslogdata->duration.'<br>';                                               
			                        $duration       = $tasktranslogdata->duration;
//                                                echo '<br>';
			                        $durationArr    = explode(' ', $duration);
                                                $tminute      = $tminute+$durationArr[4];
                                                $thours       = $thours+$durationArr[2];                                                
                                                $ttotalday    = $ttotalday+$durationArr[0];
                                                $ttotalhours  = floor(($ttotalday * 86400) / 3600) + $thours;
                                                $tactualhours = $tactualhours+$ttotalhours;
								}                                                   
							}
                            if(count($taskunittodologArr) > 0){
//                                echo '<br>taskunittodologArr<br>';
                                //echo '<pre>',print_r($taskunittodologArr);//die;
                            	foreach($taskunittodologArr as $tasktodologdata){                                    
                                    $tododuration = $tasktodologdata->duration;                                    
                                    $tododurationArr  = explode(' ', $tododuration);
                                    $todominute       = $todominute+$tododurationArr[4];
                                    //$todomhours       = $todomhours+floor($todominute / 60);
                                    $todohours        = $todohours+$tododurationArr[2];
                                    $todototalday     = $todototalday+$tododurationArr[0];
                                    $todototalhours  = floor(($todototalday * 86400) / 3600) + $todohours;                                    
                                    $todoactualhours = $todoactualhours+$todototalhours;
								} 
							}
						}                                        
						$totalday = $ttotalday+$todototalday;
                                                $hours    = $thours+$todohours;						
                                                $minute   = $tminute+$todominute;                                                                                                
                                                if($minute > 60){
                                                    $temp_hours =  floor($minute / 60);
                                                    $minute = $minute-($temp_hours*60);
                                                    $hours      = $hours+$temp_hours;
                                                }
                                                
						$totalactualhours = $tactualhours+$todoactualhours;
                                               // die;

						if($totalday > 0) {
							if($hours > 24) {
                                                            if(intval($hours / 24) == 0){
                                                                $totaldurationdays = $totaldurationdays." ".$totalday+intval($hours / 24)." d ";
                                                            }else{                                                                              
                                                                $totaldurationdays = $totaldurationdays." ".$totalday+intval($hours / 24)." d ".($hours - intval($hours / 24)*24)." h ";
                                                            }
								
							} else {
								$totaldurationdays = $totaldurationdays." ".$totalday." d ".$hours." h ";
							}
			  			} else {
			  				if($hours > 0){
			  					if($minute > 0){
			  						$totaldurationdays = $totaldurationdays." ".$hours." h ".$minute." m ";
			  					} else {
			  						$totaldurationdays = $totaldurationdays." less then min";
			  					}
			  				} else {
			  					if($minute > 0){
			  						$totaldurationdays = $totaldurationdays." ".$minute." m ";
			  					} else {
			  						$totaldurationdays = $totaldurationdays." less then min";
			  					}
			  				}
			  			}
					}
					$mainarra[$i]['actualHr'] = $totalactualhours;
					$mainarra[$i]['actual'] = $totaldurationdays;
                                        //echo $i.'=>'.$mainarra[$i]['actual'];

					$mainarra[$i]['created']='';
					if($unit_created != '0000-00-00 00:00:00'){
						$mainarra[$i]['created']=(new Options)->ConvertOneTzToAnotherTz($unit_created,"UTC",$_SESSION["usrTZ"]);
					}
				} 
				//echo $_SESSION["usrTZ"],"<br/>";
				$mainarra[$i]['started'] ='';
//				echo '<pre>';
//                                print_r($mainarra);
//                                die('testing');
				if($unit_status!=0 && $unit_started_date!='0000-00-00 00:00:00')
				{
					$mainarra[$i]['started']= $unit_started_date;
				}
				$mainarra[$i]['completed'] = '';	
				if($unit_status==4 && $unit_complete_date != '0000-00-00 00:00:00')
					$mainarra[$i]['completed']=(new Options)->ConvertOneTzToAnotherTz($unit_complete_date,"UTC",$_SESSION["usrTZ"]);;
				
	    		 if($unit_status=="0")
	      		 {
	      			if($task_status=="3"){
	      				if ($source == 'pdf')
	      				$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold">&#xf017;</em>';
	      				else
						$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold"></em>';
					}else{
						if ($source == 'pdf')
	      				$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-primary" title="Task Not Started">&#xf017;</em>';
	      				else
						$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-primary" title="Task Not Started"></em>';	 
					}	
	      		 }
	      		 else if($unit_status=="1")
	      		 {
	      		 	if ($source == 'pdf')
      				$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-success" title="Task Started">&#xf017;</em>';
      				else
	      		 	$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-success" title="Task Started"></em>';
	      		 }
	      		 else if($unit_status=="2")
	      		 {
	      		 	if ($source == 'pdf')
      				$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-info" title="Task On Pause">&#xf017;</em>';
      				else
	      		 	$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-info" title="Task On Pause"></em>'; 
	      		 }
	      		 else if($unit_status=="3")
	      		 {
	      		 	if ($source == 'pdf')
      				$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold">&#xf017;</em>';
      				else
	      		 	$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-gray" title="Task On Hold"></em>';
	      		 	
	      		 }
	 			 else if($unit_status=="4")
	      		 {	
	      		 	if ($source == 'pdf')
      				$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-dark" title="Task Completed">&#xf017;</em>';
      				else
	      		 	$mainarra[$i]['status'] = '<em class="fa fa-clock-o text-dark" title="Task Completed"></em>';
	      		 }
				 if(($orderkey+1) < 10)
					$name="&nbsp; 0".($orderkey+1);
				 else
					$name="&nbsp; ".($orderkey+1);
					 			
	 			$name .= " - &nbsp;".$stask->teamservice->service_name;
	 			//if($value->team_loc!=0)
	 			// $name .=" - ".$teamLocation[$value->team_loc];
	 			 
	 		    $name .=" - ".$stask->service_task; 
	 			//if($evid_id!="") $name .=" - <a style='color:#57A1CF !important;cursor:pointer;' href=javascript:go_toMedia('".$evid_id."');> (Media #".$evid_id.")</a>";
	 			
	 			if($type=='team'){
	 				//$mainarra[$i]['task_name'] = CHtml::link($name,"index.php?r=task/taskprogress&taskid=".$taskId."&teamId=".$teamId."&servicetask_id=".$value->servicetask_id."&team_loc=".$value->team_loc."&option=All", array("class"=>"num_a"));
	 				$mainarra[$i]['task_name'] = Html::a($name, null, ['href'=>Url::toRoute(['track/index', 'taskid' => $taskId, 'team_id' => $teamId, 'servicetask_id'=>$value->servicetask_id,'team_loc'=>$value->team_loc, 'option'=>'All'], true)]);
	 			} 
	 			if($type=='case'){
					//$mainarra[$i]['task_name'] = CHtml::link($name,"index.php?r=case/taskprogress&taskid=".$taskId."&caseId=".$caseId."&servicetask_id=".$value->servicetask_id."&option=All", array("class"=>"num_a"));
					//$mainarra[$i]['task_name'] = Html::a($name, array('track/index', 'taskid' => $taskId, 'case_id' => $caseId, 'servicetask_id'=>$value->servicetask_id,'option'=>'All'));
					$mainarra[$i]['task_name'] = Html::a($name, null, ['href'=>Url::toRoute(['track/index', 'taskid' => $taskId, 'case_id' => $caseId, 'servicetask_id'=>$value->servicetask_id,'option'=>'All'],true)]);
	 			}
				$strest_timesest="";
              	if($est_hours > 0) 
              	{
              		$est_time  =$serviceest_data[$value->servicetask_id];
              		$mainarra[$i]['estHr'] =$est_time;
              		if($est_time > 1) 
              		{
				  		$days=0;
		  				if($est_time > 24)
		  				{
		  					$days=intval($est_time/24);
		  				}
		  				if($days > 0)
		  				{
		  					$strest_timesest = $strest_timesest . " " . $days . " d " . intval(($est_time-($days*24))) . " h";
		  				}
		  				else
		  				{
		  					$strest_timesest = $strest_timesest . " ". intval($est_time). " h";
		  				}	
		  			
		  			}
              		else{
			  			$min=intval($est_time*60);
			  			if($min>0)
			  				$strest_timesest = $strest_timesest ." ". $min ." m";
			  			else 	
			  				$strest_timesest = $strest_timesest ." less then min";
			  		}
              	}
              	$mainarra[$i]['est'] = $strest_timesest;
				$i++;
    			$orderkey++;
    		}
    	}    	
//        echo '<pre>';
//        print_r($mainarra);die;
    	return $mainarra;
    }
    /*
     * Get Date Difference from two times
     */
	public function dateDiff($time1, $time2, $precision = 3) {
    	// If not numeric then convert texts to unix timestamps
    	if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
 
		// If time1 is bigger than time2
    	// Then swap time1 and time2
		if ($time1 > $time2) {
			$ttime = $time1;
			$time1 = $time2;
			$time2 = $ttime;
		}
 
		// Set up intervals and diffs arrays
		$intervals = array('day','hour','minute');
		$diffs = array();
 
    	// Loop thru all intervals
		foreach ($intervals as $interval) {
			// Create temp time from time1 and interval
			$ttime = strtotime('+1 ' . $interval, $time1);
			// Set initial values
			$add = 1;
			$looped = 0;
			// Loop until temp time is smaller than time2
			while ($time2 >= $ttime) {
	        	// Create new temp time from time1 and interval
				$add++;
				$ttime = strtotime("+" . $add . " " . $interval, $time1);
				$looped++;
			}
	
			$time1 = strtotime("+" . $looped . " " . $interval, $time1);
			$diffs[$interval] = $looped;
		}
	
		$count = 0;
		$times = array();
		// Loop thru all diffs
		foreach ($diffs as $interval => $value) {
			// Break if we have needed precission
			if ($count >= $precision) {
			break;
			}
			// Add value and interval
			// if value is bigger than 0
			if ($value > 0) {
			// Add s if value is not 1
			if ($value != 1) {
			$interval .= "s";
			}
			// Add value and interval to times array
			$times[] = $value . " " . $interval;
			$count++;
			}
		}

		// Return string with times
		return implode(" ", $times);
	}
	
	 public function imageHelperCase($data, $is_accessible_submodule_tracktask = 0) 
    {
    	$imghtml = "";
        if($data->unit_status == "0") {
            $imghtml = '<em class="fa fa-clock-o text-primary" title="Project Not Started" aria-label="Project Not Started"></em>';
        }
        
        if ($data->unit_status == "1"){
            $imghtml = '<em class="fa fa-clock-o text-success" title="Project Started" aria-label="Project Started"></em>';
        }
        
        if ($data->unit_status == "2"){
            $imghtml = '<em class="fa fa-clock-o text-info" title="Project Paused" aria-label="Project Paused"></em>';
        }
            
        if ($data->unit_status == "3") {
            $imghtml = '<em class="fa fa-clock-o text-gray" title="Project On Hold" aria-label="Project On Hold"></em>';
    	}
    	
        if ($data->unit_status == "4" && $data->tasks->task_closed == "1"){
            //$imghtml = '<em class="fa fa-clock-o text-dark text-dark-green" title="Project Completed, Closed"></em>';
            $imghtml = '<span class="icon-stack" title="Project Completed, Closed" aria-label="Project Completed, Closed">
				   <em class="fa fa-minus-circle icon-stack-2x text-theme-blue text-right"></em>
				   <em class="fa fa-clock-o icon-stack-1x text-left text-dark"></em>
				</span>';
    	} else if ($data->unit_status == "4") {
            $imghtml = '<em class="fa fa-clock-o text-dark" title="Project Completed" aria-label="Project Completed"></em>';
    	}
            
    	if($data->tasks->task_cancel == "1"){
            $imghtml = '<em class="fa fa-clock-o text-danger" title="Project Canceled" aria-label="Project Canceled" ></em>';
    	}
    	
        if ($is_accessible_submodule_tracktask == 0)
           $imghtml = $imghtml;
        else
           $imghtml = Html::a($imghtml, array('case/taskprogress', 'taskid' => $data->id, 'caseId' => $data->client_case_id));
        
        $imghtml1 = "";
        $is_pastdue = (new Tasks)->ispastduetask($data->tasks->id);
        if ($is_pastdue)
            $imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task" aria-label="Past Due Task"></span>';
        else
            $imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';
        	//$imghtml1 = Html::image(Yii::$app()->theme->baseUrl . "/images/checkmark_pastdue.png", "Past Due Task", array("title" => "Past Due Task"));
            
        
        if ($is_accessible_submodule == 0)
        	$imghtml1 = $imghtml1;
        else
        	$imghtml1 = $imghtml1;
        return  $imghtml . $imghtml1;
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
    public function getTeamservice()
    {
        return $this->hasOne(Teamservice::className(), ['id' => 'teamservice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }

	public function getProjectedHrs($task_id){
		date_default_timezone_set($_SESSION['usrTZ']);
		$businesshours = TeamserviceSlaBusinessHours::find()->one();
    	$workinghours = $businesshours->workinghours;
    	$start_time = $businesshours->start_time;
    	$end_time = $businesshours->end_time;
    	$workingdays = json_decode($businesshours->workingdays,true);	

	 	$task_info = Tasks::findOne($task_id);
		$org_startdatetime = (new Options)->ConvertOneTzToAnotherTz($task_info->created,'UTC',$_SESSION['usrTZ'],"YMDHIS");
		$org_enddatetime   = (new Options)->ConvertOneTzToAnotherTz($task_info->activeTaskInstruct->task_duedate." ".$task_info->activeTaskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"YMDHIS");
		$startdatetime = $org_startdatetime;
		$enddatetime   = $org_enddatetime;
						
		if(date('i',strtotime($startdatetime)) > 30) {
			if(date("Y-m-d H:i:00", strtotime($startdatetime." +1 Hour")) > date("Y-m-d $end_time:00",strtotime($startdatetime))){
				$startdatetime = date("Y-m-d $start_time:00", strtotime($startdatetime." +1 days"));
			}
			else if(date("Y-m-d H:i:00", strtotime($startdatetime)) < date("Y-m-d $start_time:00",strtotime($startdatetime))) {
				$startdatetime = date("Y-m-d $start_time:00", strtotime($startdatetime));
			} else {
				$startdatetime = date("Y-m-d H:00:00", strtotime($startdatetime." +1 hour"));
			}
		}
		if(date("i", strtotime($startdatetime)) > 0 && date("i",strtotime($startdatetime)) < 30){
			if(date("Y-m-d H:i:00", strtotime($startdatetime." +30 minutes")) > date("Y-m-d $end_time:s",strtotime($startdatetime))){
				$startdatetime = date("Y-m-d $start_time:00", strtotime($startdatetime." +1 days"));
			} else if(date("Y-m-d H:i:00",strtotime($startdatetime)) < date("Y-m-d $start_time:00", strtotime($startdatetime))) {
				$startdatetime = date("Y-m-d $start_time:00", strtotime($startdatetime));
			} else {
				$startdatetime = date("Y-m-d H:30:00",strtotime($startdatetime));
			}
		}
		if(date('i',strtotime($enddatetime)) > 30) {
			if(date("Y-m-d H:i:00", strtotime($enddatetime." +1 Hour")) > date("Y-m-d $end_time:00",strtotime($enddatetime))){
				$enddatetime = date("Y-m-d $start_time:00", strtotime($enddatetime." +1 days"));
			} else if(date("Y-m-d H:i:00", strtotime($enddatetime)) < date("Y-m-d $start_time:00",strtotime($enddatetime))) {
				$enddatetime = date("Y-m-d $start_time:00", strtotime($enddatetime));
			} else {
				$enddatetime = date("Y-m-d H:00:00", strtotime($enddatetime." +1 hour"));
			}
		}
		if(date("i", strtotime($enddatetime)) > 0 && date("i",strtotime($enddatetime)) < 30){
			if(date("Y-m-d H:i:00", strtotime($enddatetime." +30 minutes")) > date("Y-m-d $end_time:s",strtotime($enddatetime))){
				$enddatetime = date("Y-m-d $start_time:00", strtotime($enddatetime." +1 days"));
			} else if(date("Y-m-d H:i:00",strtotime($enddatetime)) < date("Y-m-d $start_time:00", strtotime($enddatetime))) {
				$enddatetime = date("Y-m-d $start_time:00", strtotime($enddatetime));
			} else {
				$enddatetime = date("Y-m-d H:30:00",strtotime($enddatetime));
			}
		}
		//echo "startdatetime=>",$startdatetime,"enddatetime=>",$enddatetime,"<br>";
		if($startdatetime = $enddatetime && $org_startdatetime != $org_enddatetime){
			$startdatetime = $org_startdatetime;
			$enddatetime   = $org_enddatetime;
		}
		$holidaysRec = TeamserviceSlaHolidays::find()->all();
		$holidayAr = array();
		foreach ($holidaysRec as $hol){
			$holidayAr[] = $hol->holidaydate;
		}
		$occupiedhours = 0;
		$differenceInSeconds =0;
		while ($startdatetime <= $enddatetime) {
			$currentday = date("N",strtotime($startdatetime));
			$currentdateforholiday = date("m/d/Y",strtotime($startdatetime));
			if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
				//echo "working date => ",date("Y-m-d",strtotime($startdatetime))."<br>";
				if(date("Y-m-d",strtotime($startdatetime)) == date("Y-m-d",strtotime($enddatetime))  && $enddatetime <  date("Y-m-d $end_time:s",strtotime($enddatetime))){
					$differenceInSeconds = strtotime($enddatetime) - strtotime($startdatetime);
					//echo "Hrs=>",($differenceInSeconds/3600)."<br>";
					$occupiedhours = $occupiedhours + $differenceInSeconds;
				}else{
					$differenceInSeconds = strtotime(date("Y-m-d $end_time:00",strtotime($startdatetime))) - strtotime($startdatetime);
					//echo "Hrs=>",($differenceInSeconds/3600)."<br>";
					$occupiedhours = $occupiedhours + $differenceInSeconds;
				}
			}
			$startdatetime = date("Y-m-d $start_time:00",strtotime($startdatetime." +1 days"));
		}
		//echo ($occupiedhours/3600)."<br>";
		//echo $workinghours."<br>";
		$returnResult=0;
		
		$returnResult = round(($occupiedhours / 3600),2);
		date_default_timezone_set('UTC');
		return $returnResult;
	}
	
}
