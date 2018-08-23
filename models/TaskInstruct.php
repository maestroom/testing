<?php

namespace app\models;

use Yii;
use app\models\FormInstructionValues;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%task_instruct}}".
 *
 * @property integer $id
 * @property integer $sales_user_id
 * @property integer $client_case_id
 * @property integer $task_type
 * @property integer $task_id
 * @property string $task_duedate
 * @property string $project_name
 * @property string $requestor
 * @property string $task_timedue
 * @property integer $task_priority
 * @property integer $task_projectreqtype
 * @property string $instruct_version
 * @property string $isactive
 * @property integer $saved
 * @property integer $mediadisplay_by
 * @property integer $load_prev
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property FormInstructionValues[] $formInstructionValues
 * @property PriorityProject $taskPriority
 * @property TaskInstructEvidence[] $taskInstructEvidences
 * @property TaskInstructServicetask[] $taskInstructServicetasks
 * @property TasksUnits[] $tasksUnits
 */
class TaskInstruct extends \yii\db\ActiveRecord
{
	public $attachments;
	public $service_name = '';
    //public $start_datetime;
    //public $manual_hrs;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task_instruct}}';
    }

	public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['task_date_time','task_date_time24']
        );
    }

    public function afterFind(){
		
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		$task_duedate=parent::__get('task_duedate');
		$task_timedue=parent::__get('task_timedue');
		if(isset($task_duedate) && isset($task_timedue)){
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = Yii::$app->db->createCommand("SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}','".parent::__get('task_duedate')."','".parent::__get('task_timedue')."','%m/%d/%Y %h:%i %p')")->queryScalar();
			$data_query24 = Yii::$app->db->createCommand("SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}','".parent::__get('task_duedate')."','".parent::__get('task_timedue')."','%m/%d/%Y %H:%i')")->queryScalar();
		} else {
			$data_query = Yii::$app->db->createCommand("SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}','".parent::__get('task_duedate')."','".parent::__get('task_timedue')."','%m/%d/%Y %h:%i %p')")->queryScalar();
			$data_query24 = Yii::$app->db->createCommand("SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}','".parent::__get('task_duedate')."','".parent::__get('task_timedue')."','%m/%d/%Y %H:%I')")->queryScalar();
		}
		parent::__set('task_date_time24',$data_query24);
		parent::__set('task_date_time',$data_query);
		}
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_case_id','sales_user_id', 'task_type', 'task_id', 'task_priority', 'task_projectreqtype', 'saved', 'mediadisplay_by', 'load_prev'], 'integer'],
            [['task_priority'], 'required'],
            [['task_duedate', 'created', 'modified', 'total_slack_hours'], 'safe'],
            [['project_name'], 'string'],
            [['requestor'], 'string'],
            [['task_timedue'], 'string'],
        	[['task_duedate'], 'string'],
            [['instruct_version'], 'string'],
            [['isactive'], 'string'],
            [['task_priority'], 'exist', 'skipOnError' => true, 'targetClass' => PriorityProject::className(), 'targetAttribute' => ['task_priority' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sales_user_id' => 'Sales User ID',
        	'client_case_id' => 'Client Case ID',
            'task_type' => 'Task Type',
            'task_id' => 'Task ID',
            'task_duedate' => 'Due Date',
            'project_name' => 'Project Name',
            'requestor' => 'Project Requester',
            'task_timedue' => 'Task Timedue',
            'task_priority' => 'Project Priority',
            'task_projectreqtype' => 'Task Projectreqtype',
            'instruct_version' => 'Instruct Version',
            'isactive' => 'Isactive',
            'saved' => 'Saved',
            'mediadisplay_by' => 'Mediadisplay By',
            'load_prev' => 'Load Prev',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
			'total_slack_hours' => 'total_slack_hours',
			//'start_datetime' => 'start_datetime',
			//'manual_hrs'=>'manual_hrs'
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {

			if(isset($this->task_date_time24))
				unset($this->task_date_time24);
			if(isset($this->task_date_time))
				unset($this->task_date_time);

			if(!isset($this->total_slack_hours) || $this->total_slack_hours==''){
				$this->total_slack_hours = 0.00;
			}

    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    			$this->task_type =2;
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

    public function removeTaskInstructAttachmentsByProject($taskId){
    	if(isset($taskId) && $taskId!=""){
    		$sql="select tbl_mydocument.id  FROM tbl_mydocument inner join tbl_task_instruct_servicetask on tbl_mydocument.reference_id=tbl_task_instruct_servicetask.id and tbl_mydocument.origination='instruct' where tbl_task_instruct_servicetask.task_id=".$taskId;
    		//$ids=ArrayHelper::map(Mydocument::find()->select('id')->where('id IN ('.$sql.')')->all(),'id','id');
    		//if(!empty($ids)){
	    		/*Remove Attachments*/
	    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$sql.'))');
				$deletesql="DELETE tbl_mydocument FROM tbl_mydocument inner join tbl_task_instruct_servicetask on tbl_mydocument.reference_id=tbl_task_instruct_servicetask.id and tbl_mydocument.origination='instruct' where tbl_task_instruct_servicetask.task_id=".$taskId;
				Yii::$app->db->createCommand($deletesql)->execute();
	    		//Mydocument::deleteAll('id IN ('.implode(",",$ids).')');
	    		/*Remove Attachments*/
    		//}
    	}
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
    	return $this->hasOne(ClientCase::className(), ['id' => 'client_case_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormInstructionValues()
    {
        return $this->hasMany(FormInstructionValues::className(), ['task_instruct_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskPriority()
    {
        return $this->hasOne(PriorityProject::className(), ['id' => 'task_priority']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructEvidences()
    {
        return $this->hasMany(TaskInstructEvidence::className(), ['task_instruct_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetasks()
    {
        return $this->hasMany(TaskInstructServicetask::className(), ['task_instruct_id' => 'id'])->orderBy('sort_order ASC');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetasksWithoutOrder()
    {
    	return $this->hasMany(TaskInstructServicetask::className(), ['task_instruct_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnits()
    {
        return $this->hasMany(TasksUnits::className(), ['task_instruct_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }
    
    public function getAttachments($id){
    	$attachment="";
    	$model=$this->findOne($id);
    	if(!empty($model->taskInstructServicetasks)){
    		foreach ($model->taskInstructServicetasks as $taskInstructServicetasks) {
    			if (!empty($taskInstructServicetasks->instructionAttachments)) {
    				foreach ($taskInstructServicetasks->instructionAttachments as $at) {
    					if ($attachment == "")
    						$attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em class="fa fa-paperclip" title="Attachment"></em><span class="screenreader">Download Attachment</span></a>';
    					else
    						$attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em class="fa fa-paperclip" title="Attachment"></em><span class="screenreader">Download Attachment</span></a>';
    				}
    			}		
    		}
    	}
    	return $attachment;
    }
    
    public function getStatusReport($postdata){
		
		if(isset($postdata['Report'])){
			$postdata = $postdata['Report'];
		}
		$datedropdown = $postdata['datedropdown'];
		
           
            
            $cal_data = (new Tasks)->calculatedate($datedropdown,$postdata['start_date'],$postdata['end_date']);	
			$start_date = $cal_data['start_date'];
			$end_date = $cal_data['end_date'];
           
            $unitdataIds = "";
            $billingdataIds = "";
            $projectstatus = 0;
            $cids = 0;
            $ccids = 0;
            $teamloc = "";
            $datasourceIds = 0;
            $chart_data = array();
            $projectbyclientcasechart = "";
            $ticks = array();
            $statusticks = array();
            $task_status_data = $postdata['task_status'];
            if (count($task_status_data) > 0) {
                $projectstatus = implode(",", array_unique($task_status_data));
            }
            
            $dates = array();
            $current = strtotime($start_date);
            $last = strtotime($end_date);
            
            $exceldata = array();
            $statusarray = Yii::$app->params['task_status'];
            $chartgroupcriteria = $postdata['chartgroupcriteria'];
            
           
             while ($current <= $last) {
                $dates[] = date('Y-m-d', $current);
                if (isset($chartgroupcriteria) && $chartgroupcriteria == "week" || ($chartgroupcriteria == '0' && $datedropdown == 3)) {
                    $current = strtotime('+7 day', $current);
                } else if (isset($chartgroupcriteria) && $chartgroupcriteria == "month" || ($chartgroupcriteria == '0' && $datedropdown == 4)) {
                    $current = strtotime('+1 month', $current);
                } else if (isset($chartgroupcriteria) && ($chartgroupcriteria == "years" || ($chartgroupcriteria == '0' && $datedropdown == 5))) {//(isset($chartgroupcriteria) && $chartgroupcriteria == "years") {
                    $current = strtotime('+1 year', $current);
                } else {
                    $current = strtotime('+1 month', $current);
                }
            }
            $clientcases = $postdata['clientcases'];
            $client  = $postdata['client'];
            $client_case_dt = array();
			$client_case_id = isset($clientcases)?implode(",",$clientcases):implode(",",$client);
			
			$data_analysis = array();
			$date_client_analysis = array();
			$i=0;
			
            foreach ($dates as $d) {
            	//$count = 1;
            	
                if (isset($chartgroupcriteria) && $chartgroupcriteria == "week" || ($chartgroupcriteria == '0' && $datedropdown == 3)) {
                    if (date('Y-m-d', strtotime($d)) == date('Y-m-d', strtotime($start_date))) {
                        $first_day_this_months = date('Y-m-d', strtotime($d));
                    } else {
                        $first_day_this_months = date('Y-m-d', strtotime('+1 day', strtotime($d)));
                    }
                    $weekenddate = strtotime('+7 day', strtotime($d));
                    $last_day_this_month = date('Y-m-d', $weekenddate);
                    if (strtotime('+7 day', strtotime($d)) > strtotime($end_date)) {
                        $last_day_this_month = date('Y-m-d', strtotime($end_date));
                    }
					$ymd = $first_day_this_months;
                } else if (isset($chartgroupcriteria) && ($chartgroupcriteria == "years" || ($chartgroupcriteria == '0' && $datedropdown == 5))) {
                    $first_day_this_months = date('Y-m-d', strtotime($d));
                    $first_day = date('Y', strtotime($d));
                    $yearenddate = strtotime('+1 year', strtotime($d));

                    if ($start_date != $first_day_this_months) {
                        $first_day_this_months = $first_day . "-01-01";
                    }
                    if ($yearenddate > $last) {
                        $last_day_this_month = $end_date;
                    } else {
                        $last_day_this_month = $first_day . "-12-31";
                    }
                    $ymd = date('Y', strtotime($first_day_this_months));
                } else if (isset($chartgroupcriteria) && $chartgroupcriteria == "month" || ($chartgroupcriteria == '0' && $datedropdown == 4)) {
                    $first_day_this_months = date('Y-m-01', strtotime($d));
                    if (strtotime(date('Y-m-01', strtotime($d))) < strtotime($start_date)) {
                        $first_day_this_months = date('Y-m-d', strtotime($d));
                    }
                    $last_day_this_month = date('Y-m-t', strtotime($d));
                    if (strtotime(date('Y-m-t', strtotime($d))) > strtotime($end_date)) {
                        $last_day_this_month = date('Y-m-d', strtotime($end_date));
                    }
                    $ymd = date('M-y', strtotime($first_day_this_months));
                } else {
                    if ($start_date == $end_date) {
                        $first_day_this_months = date('Y-m-d', strtotime($start_date));
                        $last_day_this_month = date('Y-m-d', strtotime($end_date));
                    } else {
                        $datediff = abs(strtotime($start_date) - strtotime($end_date));
                        $days = floor($datediff / (60 * 60 * 24));
                        if ($days < 28) {
                            $first_day_this_months = date('Y-m-d', strtotime($start_date));
                            $last_day_this_month = date('Y-m-d', strtotime($end_date));
                        } else {
                            $first_day_this_months = date('Y-m-01', strtotime($d));
                            if (strtotime(date('Y-m-01', strtotime($d))) < strtotime($start_date)) {
                                $first_day_this_months = date('Y-m-d', strtotime($d));
                            }
                            $last_day_this_month = date('Y-m-t', strtotime($d));
                            if (strtotime(date('Y-m-t', strtotime($d))) > strtotime($end_date)) {
                                $last_day_this_month = date('Y-m-d', strtotime($end_date));
                            }
                        }
                    }
                    $ymd = $first_day_this_months;
                }
                $drivername = Yii::$app->db->driverName;
                $chkclientcases = $postdata['chkclientcases'];
                $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
                $taskcriteria = Tasks::find();
				$taskcriteria->joinWith(['clientCase','client']);
				$taskcriteria->select(['tbl_tasks.client_id','tbl_tasks.client_case_id', 'tbl_tasks.id', 'task_status', 'tbl_tasks.created', 'task_complete_date']);
				
				if(isset($chkclientcases) && $chkclientcases == 'selac'){
                	$taskcriteria->where("client_case_id IN (".$client_case_id.") AND task_status IN (" . $projectstatus . ")");
                }else{
                	$taskcriteria->where("tbl_tasks.client_id IN (".$client_case_id.") AND task_status IN (" . $projectstatus . ")");
                }
                
                if ($drivername == 'mysql') {
					$taskcriteria->andWhere('DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") >= "'.$first_day_this_months.'" AND DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") <= "'.$last_day_this_month.'"');
                } else {
                    $taskcriteria->andWhere("CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$first_day_this_months."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$last_day_this_month."'");
                }
				
                $taskdataArr = $taskcriteria->all();  
                
			
				 if(!empty($taskdataArr)){
                	foreach($taskdataArr as $data){
                		if(isset($chkclientcases) && $chkclientcases == 'selac'){
                			$data_analysis[$ymd][$data->clientCase->case_name] += 1;
                			$client_case_dt[$data->clientCase->id] = $data->clientCase->case_name; 
                		}else{
                			$data_analysis[$ymd][$data->client->client_name] += 1;
                			$client_case_dt[$data->client->id] = $data->client->client_name;	
                		}
						//echo $first_day_this_months." > ".$data->client->client_name." > ".$count."<br/>";
                		$exceldata[$i]['client'] = $data->client->client_name;
                		$exceldata[$i]['case'] = $data->clientCase->case_name;
                		$exceldata[$i]['taskId'] = $data->id;
                		$exceldata[$i]['Created'] = $data->created;
                		$exceldata[$i]['task_status'] = $statusarray[$data->task_status];
                		$exceldata[$i]['Completed'] = $data->task_complete_date;
						$count += 1;
                		$i++;
                	}
                }
                $date_client_analysis[$ymd]=$ymd;	
			}
			
		
		$final['date_client_analysis'] = $date_client_analysis;
		$final['client_case_dt'] = $client_case_dt;
		$final['chkclientcases'] = $chkclientcases;
		$final['start_date'] = $start_date;
		$final['end_date'] = $end_date;
		$final['data_analysis'] = $data_analysis;
		$final['task_status'] = $postdata['task_status'];
		$final['exceldata'] = $exceldata;
		
		return $final;
	}
	/**
	* Check Project Instruction value is change or not 
	**/
	public function checkInstructionChange($task_id){
		$sql = "SELECT form_builder_id, element_value
		FROM (
		(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value FROM tbl_form_builder
		INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1
		INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
		WHERE tbl_form_builder.form_type=1)
		UNION ALL
		(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value FROM tbl_form_builder
		INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.instruct_version = (SELECT instruct_version FROM tbl_task_instruct WHERE  tbl_task_instruct.task_id=$task_id AND tbl_task_instruct.isactive=1)-1
		INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
		WHERE tbl_form_builder.form_type=1)
		) as table1
		GROUP BY  form_builder_id, element_value
		HAVING count(*) = 1";
		return FormInstructionValues::findBySql($sql)->count();
	}
	
	/**
	 * get Change Services
	 **/
	public function getChangeServices($task_id){
		$sql = "SELECT form_builder_id, element_value, servicetask_id,instruct_servicetask_id
		FROM (
		(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value, tbl_task_instruct_servicetask.servicetask_id, tbl_task_instruct_servicetask.id as instruct_servicetask_id   FROM tbl_form_builder
		INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1
		INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
		WHERE tbl_form_builder.form_type=1)
		UNION ALL
		(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value, tbl_task_instruct_servicetask.servicetask_id, tbl_task_instruct_servicetask.id as instruct_servicetask_id FROM tbl_form_builder
		INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.instruct_version = (SELECT instruct_version FROM tbl_task_instruct WHERE  tbl_task_instruct.task_id=$task_id AND tbl_task_instruct.isactive=1)-1
		INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
		WHERE tbl_form_builder.form_type=1)
		) as table1
		GROUP BY  form_builder_id, element_value, servicetask_id ,instruct_servicetask_id 
		HAVING count(*) = 1";

		$model = Yii::$app->db->createCommand("SELECT * FROM ($sql) as services");
		$services = $model->queryAll();
		return $services;
	}
	/* Return The Graph Data For Project By Team Service Report */
	public function getStatusteamservice($post){
		
		if(isset($post['Report'])){
			$post = $post['Report'];
		}
		
		$cal_data = (new Tasks)->calculatedate($post['datedropdown'],$post['start_date'],$post['end_date']);	
				$start_date = $cal_data['start_date'];
				$end_date = $cal_data['end_date'];
				
				if (isset($post['task_status']) && count($post['task_status']) > 0) {
					$projectstatus = implode(",", $post['task_status']);
				}
				if (isset($post['teamlocs']) && count($post['teamlocs']) > 0) {
					$teamLocCond = implode(",",$post['teamlocs']);
				}
				
				$dates = array();
				$current = strtotime($start_date);
				$last = strtotime($end_date);
				
				
				 while ($current <= $last) {
					$dates[] = date('Y-m-d', $current);
					if (isset($post['chartgroupcriteria']) && $post['chartgroupcriteria'] == "week" || ($post['chartgroupcriteria'] == '0' && $post['datedropdown'] == 3)) {
						$current = strtotime('+7 day', $current);
					} else if (isset($post['chartgroupcriteria']) && $post['chartgroupcriteria'] == "month" || ($post['chartgroupcriteria'] == '0' && $post['datedropdown'] == 4)) {
						$current = strtotime('+1 month', $current);
					} else if (isset($post['chartgroupcriteria']) && ($post['chartgroupcriteria'] == "years" || ($post['chartgroupcriteria'] == '0' && $post['datedropdown'] == 5))) {
						$current = strtotime('+1 year', $current);
					} else {
						$current = strtotime('+1 month', $current);
					}
				}
				$drivername = Yii::$app->db->driverName;
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				$statusarray = Yii::$app->params['task_status'];
				
				$teamservice_dt = array();
				$team_id = isset($post['teamservice'])?implode(",",$post['teamservice']):'';
				
				$team_name = Teamservice::find()->select(['id','service_name'])->where('id In ('.$team_id.')')->all();
				foreach($team_name as $d){
					$teamservice_dt[$d->id] = $d->service_name;
				}
				$teamsLoc = array();
				$team_locs = isset($post['teamlocs'])?$post['teamlocs']:'';
				$teamlocs_name = TeamlocationMaster::find()->select(['id','team_location_name'])->all();
				foreach($teamlocs_name as $d){
					$teamsLoc[$d->id] = $d->team_location_name;
				}
				$data_analysis = array();
				$date_client_analysis = array();
				$i=0;
				$varservice='';
				$serviceCond = '';
				if (isset($post['teamservice']) && count($post['teamservice']) > 0) {
					$serviceCond = implode(",", $post['teamservice']);
				}
				foreach ($dates as $d) {
					if (isset($post['chartgroupcriteria']) && $post['chartgroupcriteria'] == "week" || ($post['chartgroupcriteria'] == '0' && $post['datedropdown'] == 3)) {
	                    if (date('Y-m-d', strtotime($d)) == date('Y-m-d', strtotime($start_date))) {
	                        $first_day_this_months = date('Y-m-d', strtotime($d));
	                    } else {
	                        $first_day_this_months = date('Y-m-d', strtotime('+1 day', strtotime($d)));
	                    }
	                    $ymd = $first_day_this_months;
	                    $weekenddate = strtotime('+7 day', strtotime($d));
	                    $last_day_this_month = date('Y-m-d', $weekenddate);
	                    if (strtotime('+7 day', strtotime($d)) > strtotime($end_date)) {
	                        $last_day_this_month = date('Y-m-d', strtotime($end_date));
	                    }
	                } else if (isset($post['chartgroupcriteria']) && $post['chartgroupcriteria'] == "years" || ($post['chartgroupcriteria'] == '0' && $post['datedropdown'] == 5)) {
	                    $first_day_this_months = date('Y-m-d', strtotime($d));
	                    $first_day = date('Y', strtotime($d));
	                    $yearenddate = strtotime('+1 year', strtotime($d));
						
	                    if ($start_date != $first_day_this_months) {
	                        $first_day_this_months = $first_day . "-01-01";
	                    }
	                    if ($yearenddate > $last) {
	                        $last_day_this_month = $end_date;
	                    } else {
	                        $last_day_this_month = $first_day . "-12-31";
	                    }
	                    $ymd = date('Y', strtotime($first_day_this_months));
	                } else if (isset($post['chartgroupcriteria']) && $post['chartgroupcriteria'] == "month" || ($post['chartgroupcriteria'] == '0' && $post['datedropdown'] == 4)) {
	                    $first_day_this_months = date('Y-m-01', strtotime($d));
	                    if (strtotime(date('Y-m-01', strtotime($d))) < strtotime($start_date)) {
	                        $first_day_this_months = date('Y-m-d', strtotime($d));
	                    }
	                    
	                    $last_day_this_month = date('Y-m-t', strtotime($d));
	                    if (strtotime(date('Y-m-t', strtotime($d))) > strtotime($end_date)) {
	                        $last_day_this_month = date('Y-m-d', strtotime($end_date));
	                    }
	                    
	                    $ymd = date('M-y', strtotime($first_day_this_months));
	                } else {
	                    if ($start_date == $end_date) {
	                        $first_day_this_months = date('Y-m-d', strtotime($start_date));
	                        $last_day_this_month = date('Y-m-d', strtotime($end_date));
	                    } else {
	                        $datediff = abs(strtotime($start_date) - strtotime($end_date));
	                        $days = floor($datediff / (60 * 60 * 24));
	                        if ($days < 28) {
	                            $first_day_this_months = date('Y-m-d', strtotime($start_date));
	                            $last_day_this_month = date('Y-m-d', strtotime($end_date));
	                        } else {
	                            $first_day_this_months = date('Y-m-01', strtotime($d));
	                            if (strtotime(date('Y-m-01', strtotime($d))) < strtotime($start_date)) {
	                                $first_day_this_months = date('Y-m-d', strtotime($d));
	                            }
	                            $last_day_this_month = date('Y-m-t', strtotime($d));
	                            if (strtotime(date('Y-m-t', strtotime($d))) > strtotime($end_date)) {
	                                $last_day_this_month = date('Y-m-d', strtotime($end_date));
	                            }
	                        }
	                    }
	                    $ymd = $first_day_this_months;
	                }
	            if ($drivername == 'mysql') {
					 $wherequery = 'DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") >= "'.$first_day_this_months.'" AND DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") <= "'.$last_day_this_month.'"';
                } else {
                    $wherequery = "CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$first_day_this_months."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$last_day_this_month."'";
                }
	                       
	            
	                $taskdataArr = Tasks::find()->select(['tbl_tasks.id','tbl_tasks.task_status','tbl_tasks.created','tbl_tasks.task_complete_date'])->where($wherequery)->andWhere('tbl_tasks.task_status IN ('.$projectstatus.')')->joinWith(['tasksTeams'=>function(\yii\db\ActiveQuery $query) use($serviceCond,$teamLocCond){ $query->select(['tbl_tasks_teams.task_id','tbl_tasks_teams.teamservice_id','tbl_tasks_teams.team_loc']); $query->where('tbl_tasks_teams.teamservice_id IN ('.$serviceCond.')'); 
						if($teamLocCond != ""){ 
							$query->andWhere('tbl_tasks_teams.team_loc IN ('.$teamLocCond.')'); 
						} }])->all();
	               
					 if(!empty($taskdataArr)){
	                	foreach($taskdataArr as $data => $value){
	                		$teamsAr = '';	                	
		                		foreach ($taskdataArr[$data]['tasksTeams'] as $teamser){
		                			if(isset($teamservice_dt[$teamser->teamservice_id])) {
		                				if (!empty($team_locs) != "" && !in_array($teamser->team_loc,$team_locs)) {
		                					continue;
		                				}
		                				if($teamAr == ""){
			                				$data_analysis[$ymd][$teamservice_dt[$teamser->teamservice_id]] += 1;
			                				$teamsAr = $teamservice_dt[$teamser->teamservice_id];
		                				}
		                				$exceldata[$i]['serive'] = $teamservice_dt[$teamser->teamservice_id];
				                		$exceldata[$i]['loc'] = $teamsLoc[$teamser->teamservice_id];
				                		$exceldata[$i]['taskId'] = $value->id;
				                		$exceldata[$i]['Created'] = $value->created;
				                		$exceldata[$i]['task_status'] = $statusarray[$value->task_status];
				                		$exceldata[$i]['Completed'] = $value->task_complete_date;
				                		$i++;
				                		
		                		}
		                	} 
	                	}
	                }
				
							
	                $date_client_analysis[$ymd]=$ymd;
	              
				}	// End of Foreach 
				$final['date_client_analysis'] = $date_client_analysis;
				$final['teamservice_dt'] = $teamservice_dt;
				$final['data_analysis'] = $data_analysis;
				$final['start_date'] = $start_date;
				$final['end_date'] = $end_date;
				$final['exceldata'] = $exceldata;
				return $final;
				
	}
	/* Method for the ToDO Follow-up items by Service */
	public function getTodofollowup($post){
		
					$locationArr = array();
					$chart_data = array();
					$ticks = array();
					$mychart_data = array();
					$mychart_datas = array();
					$teamloc = "";
					$todostatus = "";
					$exceldata = array();
					$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
					$cal_data = (new Tasks)->calculatedate($post['Report']['datedropdown'],$post['Report']['start_date'],$post['Report']['end_date']);	
					$start_date = $cal_data['start_date'];
					$end_date = $cal_data['end_date'];
					if($post['Report']['teamlocs'] > 0){
						$teamloc = implode(',',$post['Report']['teamlocs']);
					}
					if($post['Report']['teamservice'] > 0){
						$teamservice_implode = implode(',',$post['Report']['teamservice']);
					}
					if($post['Report']['todostatus'] > 0){
						$todostatus = implode(',',$post['Report']['todostatus']);
					}
					
					$drivername = Yii::$app->db->driverName;
					if ($drivername == 'mysql') {
					 $wherequery = 'DATE_FORMAT(tbl_tasks_units_todos.created, "%Y-%m-%d") >= "'.$start_date.'" AND DATE_FORMAT(tbl_tasks_units_todos.created, "%Y-%m-%d") <= "'.$end_date.'"';
					} else {
					 $wherequery = "CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todos.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$start_date."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todos.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$end_date."'";
					}
					$todo_and = "";
					if(!empty($teamloc)){
						$todo_and = " AND tbl_task_instruct_servicetask.team_loc IN (".$teamloc.")";
					}
					$join = "";
					$select = "";
					if(isset($post['dataexport']) && $post['dataexport'] == "export"){
						$join = "LEFT JOIN tbl_tasks_units_todo_transaction_log as todolog ON todolog.todo_id = tbl_tasks_units_todos.id AND todolog.transaction_type=9 
						INNER JOIN tbl_tasks as task ON task.id = tbl_tasks_units_todos.task_id
						INNER JOIN tbl_client as client ON client.id = task.client_id
						INNER JOIN tbl_client_case as clientcase ON clientcase.id = task.client_case_id
						INNER JOIN tbl_servicetask as servicetask ON servicetask.id = tbl_task_instruct_servicetask.servicetask_id";
						$select = ",servicetask.id as servicetask_id,servicetask.service_task,tbl_tasks_units_todos.complete,todolog.transaction_date,client.client_name,clientcase.case_name,tbl_tasks_units_todos.task_id";
                    }
					
					$sql1 = 'SELECT tbl_tasks_units_todos.id, tbl_tasks_units_todos.task_id, tbl_tasks_units_todos.todo_cat_id, tbl_tasks_units_todos.tasks_unit_id, tbl_tasks_units_todos.created,tbl_task_instruct_servicetask.teamservice_id,tbl_task_instruct_servicetask.team_loc,tbl_teamlocation_master.team_location_name,tbl_teamservice.service_name,tbl_todo_cats.todo_cat,tbl_todo_cats.todo_desc '.$select.' FROM tbl_tasks_units_todos 
					INNER JOIN tbl_tasks_units ON tbl_tasks_units_todos.tasks_unit_id = tbl_tasks_units.id 
					INNER JOIN tbl_task_instruct_servicetask ON tbl_tasks_units.task_instruct_servicetask_id = tbl_task_instruct_servicetask.id 
					INNER JOIN tbl_teamlocation_master ON tbl_task_instruct_servicetask.team_loc = tbl_teamlocation_master.id 
					INNER JOIN tbl_teamservice ON tbl_task_instruct_servicetask.teamservice_id = tbl_teamservice.id 
					INNER JOIN tbl_todo_cats ON tbl_tasks_units_todos.todo_cat_id = tbl_todo_cats.id'. $join .' 
					WHERE '.$wherequery.' AND (tbl_tasks_units_todos.todo_cat_id != 0 AND tbl_tasks_units_todos.complete IN ('.$todostatus.')) AND ((tbl_task_instruct_servicetask.teamservice_id IN ('.$teamservice_implode.') AND tbl_task_instruct_servicetask.teamservice_id != ""))'.$todo_and.' ORDER BY tbl_tasks_units_todos.todo_cat_id';	
					
				
					
					$taskunittodoArr = \Yii::$app->db->createCommand($sql1)->queryAll();
					
					 if (count($taskunittodoArr) > 0) {
						$totaltodoArr = array();
                        $totaltodocatArr = array();
                        $i = 1;
                        $check = array();
                        
						foreach ($taskunittodoArr as $tododata) {
							$totaltodocatArr[$tododata['todo_cat_id']][] = $tododata['id'];
							if (!empty($tododata)) {
								$todofollowup = $tododata['todo_cat'] . " - " . trim($tododata['todo_desc']);
                                $ticks[$tododata['todo_cat_id']] = $todofollowup;
							}	
							ksort($ticks);
							$totaltodoArr[$tododata['todo_cat_id']] = 0;
							if (isset($totaltodoArr[$tododata['todo_cat_id']]))
                            $totaltodoArr[$tododata['todo_cat_id']] = count($totaltodocatArr[$tododata['todo_cat_id']]);
                            if(!array_key_exists($tododata['teamservice_id'],$check)){
								$check[$tododata['teamservice_id']] = $i;
								$i++;
							}	
							$locationArr[$tododata['service_name']] = $tododata['team_location_name'];
                            $chart_data[$check[$tododata['teamservice_id']]] = array("todofollowup" => $ticks, "name" => $tododata['service_name'], "data" => $totaltodoArr);
                           $exceldata[$tododata['id']]=array('todo_id'=>$tododata['id'],'client'=>$tododata['client_name'],'case'=>$tododata['case_name'],'task_id'=>$tododata['task_id'],'todo_cat'=>$tododata['todo_cat'].' - '.$tododata['todo_desc'],'service'=>$tododata['service_name'],'teamloc'=>$tododata['team_location_name'],'servicetask'=>$tododata['service_task'],'todostatus'=>$tododata['complete'],'created'=>$tododata['created'],'completed'=>$tododata['transaction_date']);
						}	
					 }
					$k = 0;
					if (!empty($chart_data)) {
						foreach ($chart_data as $kdata => $kvalue) {
							$dataArr = array();
							$datalabelArr = array();
							$j = 0;
							foreach ($ticks as $catId => $catval) {
								$datalabelArr[$j] = $catval;
								if (isset($kvalue['data'][$catId])) {
									$dataArr[$j] = $kvalue['data'][$catId];
								} else {
									$dataArr[$j] = 0;
								}
								$j++;
							}
							if ($teamloc != "") {
								if (!empty($locationArr[$kvalue['name']])) {
									$serviceloc = $kvalue['name'] . " - " . $locationArr[$kvalue['name']];
								} else {
									$serviceloc = $kvalue['name'];
								}
							} else {
								$serviceloc = $kvalue['name'];
							}
							$mychart_data[$k] = array('name' => $serviceloc, 'data' => $dataArr);
							$mychart_datas[$kvalue['name']] = array('todofollowup' => $datalabelArr, 'location' => $locationArr[$kvalue['name']], "quantity" => $dataArr);
							$k++;
						}
						$teamverticalchartticks = $ticks;
					}
					$teamverticalchart = json_encode($mychart_data);
					 $final = array();
					 $final['start_date'] = $start_date;
					 $final['end_date'] = $end_date;
					 $final['teamverticalchart'] = $teamverticalchart;
					 $final['teamverticalchartticks'] = $teamverticalchartticks;
					 $final['exportchart'] = $mychart_datas;
					 $final['locationArr'] = $locationArr;
					 $final['exceldata'] = $exceldata;
					 return $final;
	}
	public function getTodofollowbyduration($post){
			
			if(isset($post['Report'])){
				$post = $post['Report'];
			}		
			$cal_data = (new Tasks)->calculatedate($post['datedropdown'],$post['start_date'],$post['end_date']);
			$start_date = $cal_data['start_date'];
			$end_date = $cal_data['end_date'];
			
			if($post['teamlocs'] > 0){
						$teamloc = implode(',',$post['teamlocs']);
					}
					if($post['teamservice'] > 0){
						$teamservice_implode = implode(',',$post['teamservice']);
					}
					if($post['todostatus'] > 0){
						$todostatus = implode(',',$post['todostatus']);
					}
					
					$drivername = Yii::$app->db->driverName;
					if ($drivername == 'mysql') {
					 $wherequery = 'DATE_FORMAT(tbl_tasks_units_todos.created, "%Y-%m-%d") >= "'.$start_date.'" AND DATE_FORMAT(tbl_tasks_units_todos.created, "%Y-%m-%d") <= "'.$end_date.'"';
					} else {
					 $wherequery = "CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todos.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$start_date."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todos.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$end_date."'";
					}
					$todo_and = "";
					if(!empty($teamloc)){
						$todo_and = " AND tbl_task_instruct_servicetask.team_loc IN (".$teamloc.")";
					}
					$join = "";
					$select = "";
					
						$join = "LEFT JOIN tbl_tasks_units_todo_transaction_log as todolog ON todolog.todo_id = tbl_tasks_units_todos.id AND todolog.transaction_type=9 
						INNER JOIN tbl_tasks as task ON task.id = tbl_tasks_units_todos.task_id
						INNER JOIN tbl_client as client ON client.id = task.client_id
						INNER JOIN tbl_client_case as clientcase ON clientcase.id = task.client_case_id
						INNER JOIN tbl_servicetask as servicetask ON servicetask.id = tbl_task_instruct_servicetask.servicetask_id";
						$select = ",servicetask.id as servicetask_id,servicetask.service_task,tbl_tasks_units_todos.complete,todolog.transaction_date,client.client_name,clientcase.case_name,tbl_tasks_units_todos.task_id";
                    
					
					$sql1 = 'SELECT tbl_tasks_units_todos.id, tbl_tasks_units_todos.task_id, tbl_tasks_units_todos.todo_cat_id, tbl_tasks_units_todos.tasks_unit_id, tbl_tasks_units_todos.created,tbl_task_instruct_servicetask.teamservice_id,tbl_task_instruct_servicetask.team_loc,tbl_teamlocation_master.team_location_name,tbl_teamservice.service_name,tbl_todo_cats.todo_cat,tbl_todo_cats.todo_desc '.$select.' FROM tbl_tasks_units_todos 
					INNER JOIN tbl_tasks_units ON tbl_tasks_units_todos.tasks_unit_id = tbl_tasks_units.id 
					INNER JOIN tbl_task_instruct_servicetask ON tbl_tasks_units.task_instruct_servicetask_id = tbl_task_instruct_servicetask.id 
					INNER JOIN tbl_teamlocation_master ON tbl_task_instruct_servicetask.team_loc = tbl_teamlocation_master.id 
					INNER JOIN tbl_teamservice ON tbl_task_instruct_servicetask.teamservice_id = tbl_teamservice.id 
					INNER JOIN tbl_todo_cats ON tbl_tasks_units_todos.todo_cat_id = tbl_todo_cats.id'. $join .' 
					WHERE '.$wherequery.' AND (tbl_tasks_units_todos.todo_cat_id != 0 AND tbl_tasks_units_todos.complete IN ('.$todostatus.')) AND ((tbl_task_instruct_servicetask.teamservice_id IN ('.$teamservice_implode.') AND tbl_task_instruct_servicetask.teamservice_id != ""))'.$todo_and.' ORDER BY tbl_tasks_units_todos.todo_cat_id';	
					
					$taskunittodoArr = \Yii::$app->db->createCommand($sql1)->queryAll();
					 if (count($taskunittodoArr) > 0) {
                        $totaltodoArr = array();
                        foreach ($taskunittodoArr as $tododata) {
							$completed = "";
                        	$started = "";
                        	
                        	if(!isset($arrtodo[$tododata['id']])){
	                        	$arrtodo[$tododata['id']] = (new Tasks)->getTotalSLADaysTodoFollowupwithtodo($tododata['task_id'], $tododata['teamservice_id'], 0, $tododata['servicetask_id'], $tododata['id']);
                        	}
                        	
                        	$transactionlog = (new Tasks)->getTransactionTodoLog($tododata['id']);
                        	if(count($transactionlog) > 0){
								foreach($transactionlog as $todolog){
									if($todolog['transaction_type'] == 7 || $todolog['transaction_type'] == 13){
	                        			if($todolog['transaction_type'] == 7)	
		                        			$started = $todolog['transaction_date'];
	                        			if($todolog['transaction_type'] == 13)
	                        				$completed = ""; 
	                        		}
	                        		if($todolog['transaction_type'] == 9){
	                        			$completed = $todolog['transaction_date'];
	                        		}
	                        		
	                        		$exceldata[$tododata['id']][$todolog['id']]=array(
										'todo_id'=>$tododata['id'],
										'client'=>$tododata['client_name'],
										'case'=>$tododata['case_name'],
										'task_id'=>$tododata['task_id'],
										'todo_cat'=>$tododata['todo_cat']." - ".$tododata['todo_desc'],
										'service'=>$tododata['service_name'],
										'teamloc'=>$tododata['team_location_name'],
										'servicetask'=>$tododata['service_task'],
										'todostatus'=>$tododata['complete'],
										'created'=>$tododata['created'],
										'started'=>$started,
										'completed'=>$completed,
										'trans_type'=>$todolog['transaction_type'],
										'followup_days'=>$arrtodo[$tododata['id']]
									);
								}
							} else {
                        		$exceldata[$tododata['id']][0]=array(
										'todo_id'=>$tododata['id'],
										'client'=>$tododata['client_name'],
										'case'=>$tododata['case_name'],
										'task_id'=>$tododata['task_id'],
										'todo_cat'=>$tododata['todo_cat']." - ".$tododata['todo_desc'],
										'service'=>$tododata['service_name'],
										'teamloc'=>$tododata['team_location_name'],
										'servicetask'=>$tododata['service_task'],
										'todostatus'=>$tododata['complete'],
										'created'=>$tododata['created'],
										'started'=>$tododata['created'],
										'completed'=>'',
										'trans_type'=>7,
										'followup_days'=>$arrtodo[$tododata['id']]
								);
                        	}
					    }
					 }
					$excelchartdata = array();

					foreach($exceldata as $key=>$datas){
						$count = count($datas);
						$i=1;
						foreach($datas as $key1=>$data){
							if($count == $i || $i == 1){
								$excelchartdata[$data['todo_id']]['todo_id'] = $data['todo_id'];
								$excelchartdata[$data['todo_id']]['client'] = $data['client'];
								$excelchartdata[$data['todo_id']]['case'] = $data['case'];
								$excelchartdata[$data['todo_id']]['task_id'] = $data['task_id'];
								$excelchartdata[$data['todo_id']]['todo_cat'] = $data['todo_cat'];
								$excelchartdata[$data['todo_id']]['service'] = $data['service'];
								$excelchartdata[$data['todo_id']]['teamloc'] = $data['teamloc'];
								$excelchartdata[$data['todo_id']]['servicetask'] = $data['servicetask'];
								$excelchartdata[$data['todo_id']]['todostatus'] = $data['todostatus'];
								$excelchartdata[$data['todo_id']]['created'] = $data['created'];
								if($data['trans_type'] == 7 || $data['trans_type'] == 13)
									$excelchartdata[$data['todo_id']]['started'] = $data['started'];
								$excelchartdata[$data['todo_id']]['completed'] = $data['completed'];
								$excelchartdata[$data['todo_id']]['trans_type'] = $data['trans_type'];
								$excelchartdata[$data['todo_id']]['followup_days'] = $data['followup_days'];
							}
							$i++;
						}
					}   
					$final = array();
					$final['excelchartdata'] = $excelchartdata;
					$final['start_date'] = $start_date;
					$final['end_date'] = $end_date;		
					$final['todo_status'] = $post['todostatus'];			
					return $final; 
	}
	
	/**
	 * Get Service Name for Saved project Grid Search
	 * @return mixed
	 */
	public function getServiceNameSavedProject($task_instruct_id)
	{
		$result = ArrayHelper::map(TaskInstruct::find()->select(['ts.id','ts.service_name'])->from('tbl_task_instruct as ti')
			->join('INNER JOIN','tbl_task_instruct_servicetask as tss','tss.task_instruct_id=ti.id ')
			->join('INNER JOIN','tbl_teamservice as ts','ts.id=tss.teamservice_id')->where('ti.saved=1 AND ti.id='.$task_instruct_id)->all(),'id','service_name');
		$service_name = "";
		if(!empty($result))
			$service_name = implode("; ",$result);
		
		return $service_name;
	}
	
	/* get task instruct submitted date and time according to user's timezone settings */
	public function getTaskSubmittedDate($model)
	{
		return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model->created, 'UTC', $_SESSION['usrTZ']).'</label>';
	}
	/* get task instruct submitted date and time according to user's timezone settings */
	public function getTaskModifiedDate($model)
	{
		return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model->modified, 'UTC', $_SESSION['usrTZ']).'</label>';
	}
}
