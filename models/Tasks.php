<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Session;
use yii\helpers\Url;
use yii\db\Query;

use app\models\Comments;
use app\models\TasksUnitsTodos;
use app\models\TaskInstruct;
use app\models\TaskInstructServicetask;
use app\models\EvidenceProductionMedia;
use app\models\TeamserviceSlaBusinessHours;
use app\models\TeamserviceSlaHolidays;
use app\models\Options;
use app\models\EmailCron;

/**
 * This is the model class for table "{{%tasks}}".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $client_case_id
 * @property integer $sales_user_id
 * @property integer $task_status
 * @property string $task_complete_date
 * @property integer $task_closed
 * @property integer $task_cancel
 * @property string $task_cancel_reason
 * @property integer $team_priority
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Tasks extends \yii\db\ActiveRecord
{
	public $statuscount = 0;
    public $cnttasks = 0;
    public $team_priority_order = 0;
    public $teamorderpriority = 0;
    public $task_priority = 0;
	public $per_complete = 0;
	public $team_per_complete = 0;
	public $project_name = NULL;
	public $porder=NULL; // task prority order
	public $pname=NULL; // task prority name
	public $ispastdue=NULL; // task prority name
	public $task_duedate=NULL;
	public $task_duetime=NULL;
	public $client_name=NULL;
	public $clientcase_name=NULL;
	public $task_date_time=NULL;
	public $tasks_priority_name=NULL;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'client_case_id', 'task_status', 'task_closed', 'task_cancel'], 'required'],
            [[ 'client_case_id', 'sales_user_id', 'task_status', 'task_closed', 'task_cancel', /*'team_priority',*/ 'created_by', 'modified_by'], 'integer'],
            [['task_complete_date', 'created', 'modified','team_priority_order', 'per_complete', 'team_per_complete', 'teamorderpriority'], 'safe'],
            [['task_cancel_reason'], 'string'],
            [['task_cancel_reason'], 'required','when'=>function($model){ return $model->task_cancel == 1;},'whenClient' => "function (attribute, value) {
				return $('#tasks-task_cancel').val() == 1;
		    }"],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
            /*[['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Project#',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
            'sales_user_id' => 'Sales User ID',
            'task_status' => 'Status',
            'task_complete_date' => 'Task Complete Date',
            'task_closed' => 'Task Closed',
            'task_cancel' => 'Task Cancel',
            'task_cancel_reason' => 'Project Cancel Reason',
            'team_priority' => 'Team Priority',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        	'statuscount' => 'statuscount',
        	'team_priority_order' => 'team_priority_order',
        	'per_complete' => 'Percentage Complete',
			'team_per_complete' => 'Team Complete %',
        	'teamorderpriority' => 'team_loc_prority'
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
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		} else {
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function submitProject($post_data,$attachments,$instruction_id=0){
    	//echo "<prE>",print_r($post_data),"</pre>";die;
    	$media_arr=array(1=>'M',2=>'PM');
    	$case_info= ClientCase::findOne($post_data['case_id']);
    	$model=new Tasks();
    	//$model->client_id      = $case_info->client_id;
    	$model->client_case_id = $post_data['case_id'];
    	$model->task_status    = 0;
    	$model->task_closed    = 0;
    	$model->task_cancel    = 0;
    	$model->sales_user_id  = 0;
    	$model->save();
    	$task_id = Yii::$app->db->getLastInsertId();
    	$media_arr=array(1=>'M',2=>'PM');
    	/*Save Instruction Data*/
    	$modelInstruct = new TaskInstruct();
    	if($instruction_id >  0){
    		$modelInstruct = TaskInstruct::findOne($instruction_id);
			if(isset($post_data['fromSaved']) && $post_data['fromSaved'] == 1){
				$modelInstruct->created = date('Y-m-d H:i:s');
			}
    	}
    	$modelInstruct->client_case_id = $post_data['case_id'];
    	$modelInstruct->task_priority = $post_data['TaskInstruct']['task_priority'];
    	$modelInstruct->task_id = $task_id;
    	$modelInstruct->isactive = '1';
    	$modelInstruct->project_name = $post_data['TaskInstruct']['project_name'];
    	$modelInstruct->requestor = $post_data['TaskInstruct']['requestor'];
    	$modelInstruct->task_projectreqtype = $post_data['TaskInstruct']['task_projectreqtype'];
    	$task_duedate = explode("/",$post_data['TaskInstruct']['task_duedate']);
    	$task_duedate = $task_duedate[2].'-'.$task_duedate[0].'-'.$task_duedate[1];
    	$trueUserDate = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"YMD");
    	$trueUserTime = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"time");
    	$modelInstruct->task_duedate     = $trueUserDate; //$post_data['TaskInstruct']['task_duedate'];
    	$modelInstruct->task_timedue     = $trueUserTime;
		$modelInstruct->total_slack_hours= $post_data['TaskInstruct']['total_slack_hours'];
    	$modelInstruct->instruct_version = '001';
    	$modelInstruct->saved 			 = 0;
    	$modelInstruct->mediadisplay_by  = ($post_data['display_by']=='PM'?2:1);
    	$modelInstruct->load_prev 		 = 0;
    	if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0){
    		$modelInstruct->load_prev 	 = 1;
    	}
    	$modelInstruct->save();
    	if($instruction_id >  0){
    		$instruction_id = $instruction_id;
			/*IRT - 275 Update Created date when submitting Saved Project */
			TaskInstruct::updateAll(['created' => date('Y-m-d H:i:s')],['id' => $instruction_id]);
			/*IRT - 275*/
    	}else{
    		$instruction_id = Yii::$app->db->getLastInsertId();
    	}
    	/*Save Instruction Form Value*/
    	if(isset($post_data['properties'])){
            (new FormBuilder())->saveInstructionFrom($post_data,$instruction_id);
    	}
    	/*Save Instruction Form Value*/
    	/*Save Instruction Media*/
    	$this->SaveUpdateMedia($post_data,$instruction_id,$task_id);
    	/*Save Instruction Media*/
    	/*Save Instruction Service Task*/
    	$struse_servicetask="";
		$TaskInstructServicetask_ids=array();
    	$i=1;
		$has_sla_est=false;
		if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
    			if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
    				foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){
    					if($struse_servicetask==""){
    						$struse_servicetask="('".$servicetask_id."_".$loc."')";
    					}else{
    						$struse_servicetask= $struse_servicetask .", ('".$servicetask_id."_".$loc."')";
    					}
    				}
    			}
    		}
    		if(!empty($struse_servicetask)) {
    			$sql ="DELETE FROM tbl_task_instruct_servicetask WHERE CONCAT(servicetask_id,'_',team_loc) NOT IN ($struse_servicetask) AND task_instruct_id = $instruction_id";
				Yii::$app->db->createCommand($sql)->execute();
    		}
    	}
		$removeAttachments=array();
    	if(isset($post_data['remove_attachment']) && $post_data['remove_attachment']!=""){
			if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0){
				$removeAttachments=explode(",",$post_data['remove_attachment']);
			}else{
    			(new Mydocument())->removeAttachments($post_data['remove_attachment']);
			}

    	}
    	if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
    			if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
    				foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){
    					$service_info=Servicetask::findOne($servicetask_id);
    					$taskInstructServicetask_model  =new TaskInstructServicetask();
    					if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
    						$taskInstructServicetask_model  =TaskInstructServicetask::findOne($post_data['ServicetaskInstruct'][$servicetask_id]);
    					}
    					$taskInstructServicetask_model->task_instruct_id=$instruction_id;
    					$taskInstructServicetask_model->task_id=$task_id;
    					$taskInstructServicetask_model->team_id=$service_info->teamId;
    					$taskInstructServicetask_model->teamservice_id=$service_info->teamservice_id;
    					$taskInstructServicetask_model->servicetask_id=$servicetask_id;
    					$taskInstructServicetask_model->team_loc=$loc;
    					$taskInstructServicetask_model->est_time=((isset($post_data['Est_times'][$servicetask_id]) && $post_data['Est_times'][$servicetask_id]!="") ? $post_data['Est_times'][$servicetask_id] : 0);
    					//$taskInstructServicetask_model->teamservice_sla_id=intval($post_data['hdn_service_logic'][$servicetask_id]);
    					$taskInstructServicetask_model->sort_order=$i;
    					//$taskInstructServicetask_model->save();
						if($taskInstructServicetask_model->est_time > 0){
							$has_sla_est=true;
						}
						
    					if($taskInstructServicetask_model->save()){
							
    					}else{
    						echo "<pre>",print_r($taskInstructServicetask_model->getErrors());die;
    					}
    					if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
    						$instructionservicetask_id = $post_data['ServicetaskInstruct'][$servicetask_id];
    					}else{
    						$instructionservicetask_id = Yii::$app->db->getLastInsertId();
    					}
    					
    					$TaskInstructServicetask_ids[$servicetask_id]=$instructionservicetask_id;
    					/*Save Taks Unit*/
    					$model_unit = new TasksUnits();
    					
    					//$model_unit->task_id 						=  $task_id;
    					$model_unit->task_instruct_id 				=  $instruction_id ;
    					$model_unit->task_instruct_servicetask_id   =  $instructionservicetask_id;
    					$model_unit->unit_assigned_to				=  0;
    					$model_unit->unit_status 					=  0;
    					$model_unit->is_transition 					= '0';
    					$model_unit->task_id=$task_id;
    					$model_unit->team_id=$service_info->teamId;
    					$model_unit->teamservice_id=$service_info->teamservice_id;
    					$model_unit->servicetask_id=$servicetask_id;
    					$model_unit->team_loc=$loc;
    					$model_unit->est_time=((isset($post_data['Est_times'][$servicetask_id]) && $post_data['Est_times'][$servicetask_id]!="") ? $post_data['Est_times'][$servicetask_id] : 0);
    					$model_unit->sort_order=$i;
    					//$model_unit->save();
    					if($model_unit->save()){
    					}else{
    						echo "<pre>",print_r($model_unit->getErrors());die;
    					}
    					/*save Task Teams */
    					if(!TasksTeams::find()->where(['task_id'=>$task_id,'team_id'=>$service_info->teamId,'team_loc'=>$loc])->count()){
	    					$modelTasksTeams = new TasksTeams();
	    					$modelTasksTeams->task_id   	 = $task_id;
	    					$modelTasksTeams->team_id   	 = $service_info->teamId;
	    					$modelTasksTeams->team_loc       = $loc;
	    				//	$modelTasksTeams->teamservice_id = $service_info->teamservice_id;
	    					//$modelTasksTeams->save();
	    					if($modelTasksTeams->save()){
	    					}else{
	    						echo "<pre>",print_r($modelTasksTeams->getErrors());die;
	    					}
    					}
    					/*Save Task Team sla*/
    					if(isset($post_data['hdn_service_logic'][$servicetask_id]) && $post_data['hdn_service_logic'][$servicetask_id] != 0){
    						
    						/*$tasks_teams_id= Yii::$app->db->getLastInsertId();
    						$modelTasksTeamSla = new TasksTeamSla();
    						$modelTasksTeamSla->teamservice_sla_id = $post_data['hdn_service_logic'][$servicetask_id];
    						$modelTasksTeamSla->tasks_teams_id     = $tasks_teams_id;
    						//$modelTasksTeamSla->save();
    						if($modelTasksTeamSla->save()){
    						}else{
    							echo "<pre>",print_r($modelTasksTeamSla->getErrors());die;
    						}*/
    						$logics = explode(",",$post_data['hdn_service_logic'][$servicetask_id]);
    						foreach ($logics as $sla_logic_id){
	    						$modelTasksInstServSla = new TaskInstructServicetaskSla();
	    						$modelTasksInstServSla->task_instruct_servicetask_id = $instructionservicetask_id;
	    						$modelTasksInstServSla->teamservice_sla_id = $sla_logic_id;
	    						if($modelTasksInstServSla->save()){
									$has_sla_est=true;
	    						}else{
	    							echo "<pre>",print_r($modelTasksInstServSla->getErrors());die;
	    						}
    						} 
    						
    						
    					}
    					if(!empty($_FILES['TaskInstruct']['name']['attachment'][$servicetask_id])){
    						$docmodel = new Mydocument();
    						$doc_arr['p_id']=0;
    						$doc_arr['reference_id']=$instructionservicetask_id;
    						$doc_arr['team_loc']=0;
    						$doc_arr['origination']="instruct";
    						$doc_arr['type']=0;
    						$doc_arr['is_private']=0;
    						$docmodel->origination = "instruct";
    						$file_arr=$docmodel->SaveInstructionmydocs('TaskInstruct','attachment',$servicetask_id,$doc_arr, "");
    					}
						/*IRT-649 copy over attachments from load prev project*/
						if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0) {
							$load_prev_project_id=$post_data['load_prev_project_id'];
								$sql_ref="SELECT tbl_task_instruct_servicetask.id FROM tbl_task_instruct_servicetask
										  INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id
										  WHERE tbl_task_instruct_servicetask.task_id=$load_prev_project_id AND tbl_task_instruct_servicetask.servicetask_id=$servicetask_id
										  AND tbl_task_instruct.isactive=1";
								$reference_id=Yii::$app->db->createCommand($sql_ref)->queryScalar();
								$doc_data=Mydocument::find()->where(['origination'=>'instruct','reference_id'=>$reference_id])->joinWith(['mydocumentsBlobs'])->all();
								if(!empty($doc_data)) {
									foreach($doc_data as $doc)
									{
										if(is_array($removeAttachments)) {
											if(!in_array($doc->id, $removeAttachments)) {
												$MydocumentsBlob_model = new MydocumentsBlob();
												$MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
												$MydocumentsBlob_model->save(false);
												$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
											// echo "<pre>",print_r($doc->mydocumentsBlobs);die;
												//mydocumentsBlobs
												$docmodel = new Mydocument();
												$docmodel->p_id = $doc->p_id;
												$docmodel->reference_id = $instructionservicetask_id;
												$docmodel->team_loc = $doc->team_loc;
												$docmodel->fname = $doc->fname;
												$docmodel->origination = "instruct";
												$docmodel->u_id = Yii::$app->user->identity->id;
												$docmodel->is_private = $doc->is_private;
												$docmodel->type = $doc->type;
												$docmodel->doc_id = $blob_doc_id;
												$docmodel->doc_size = $doc->doc_size;
												$docmodel->doc_type = $doc->doc_type;
												$docmodel->save(false);
											}
										}else{
												$MydocumentsBlob_model = new MydocumentsBlob();
												$MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
												$MydocumentsBlob_model->save(false);
												$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
											// echo "<pre>",print_r($doc->mydocumentsBlobs);die;
												//mydocumentsBlobs
												$docmodel = new Mydocument();
												$docmodel->p_id = $doc->p_id;
												$docmodel->reference_id = $instructionservicetask_id;
												$docmodel->team_loc = $doc->team_loc;
												$docmodel->fname = $doc->fname;
												$docmodel->origination = "instruct";
												$docmodel->u_id = Yii::$app->user->identity->id;
												$docmodel->is_private = $doc->is_private;
												$docmodel->type = $doc->type;
												$docmodel->doc_id = $blob_doc_id;
												$docmodel->doc_size = $doc->doc_size;
												$docmodel->doc_type = $doc->doc_type;
												$docmodel->save(false);
										}
									}
								}
						}
						/*IRT-649 copy over attachments from load prev project*/
    					$i++;
    				}
    			}
    		}
    	}
		
		/*//IRT - 275 check if sla or est time included in project if has sal or est time then remove time from due date and update save_datetime
		if($has_sla_est === true){
			//die('updateSaveDatetime');
			$this->updateSaveDatetime($task_id,$instruction_id);
		}else{
			//store full hrs in manual_hrs and update save_datetime
			$this->updateManualHrs($task_id,$instruction_id);
			//die('updateManualHrs');
		}
		//IRT - 275*/
    	(new ActivityLog)->generateLog('Project', 'Submitted', $task_id, "project#:".$task_id);
    	/*Sending New  Project Subscription Alert Email CHANGE*/
    	//(new SettingsEmail)->sendEmail
		EmailCron::saveBackgroundEmail(1,'is_sub_new_task',$data=array('case_id'=>$post_data['case_id'],'project_id'=>$task_id,'Production'=>$post_data['Production']));
		if($post_data['display_by'] == 'PM'){
			if(isset($post_data['Production'])){
				$prodevidexist = array();
				foreach($post_data['Production'] as $prod_id=>$production){
					//(new SettingsEmail)->sendEmail
					EmailCron::saveBackgroundEmail(2, 'is_sub_new_production', $data = array('case_id' => $post_data['case_id'], 'prod_id' => $prod_id,'project_id'=>$task_id));
				}
			}
		}

    	/*Sending New  Project Subscription Alert Email*/
    	return true;
    }
    public function saveUpdateInstruction($post_data,$instruction_id=0){
    	$media_arr=array(1=>'M',2=>'PM');
    	$modelInstruct = new TaskInstruct();
    	if($instruction_id >  0){
    		$modelInstruct = TaskInstruct::findOne($instruction_id);
			$trueUserTime = $modelInstruct->task_timedue;
    	}
    	$modelInstruct->client_case_id = $post_data['case_id'];
    	$modelInstruct->task_priority = $post_data['TaskInstruct']['task_priority'];
    	$modelInstruct->project_name = $post_data['TaskInstruct']['project_name'];
    	$modelInstruct->requestor = $post_data['TaskInstruct']['requestor'];
    	$modelInstruct->task_projectreqtype = $post_data['TaskInstruct']['task_projectreqtype'];
    	$task_duedate = explode("/",$post_data['TaskInstruct']['task_duedate']);
    	$task_duedate = $task_duedate[2].'-'.$task_duedate[0].'-'.$task_duedate[1];
    	 
    	$trueUserDate = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"YMD");
    	if(isset($post_data['TaskInstruct']['task_timedue']))
    		$trueUserTime = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"time");
    	
    	$modelInstruct->task_duedate = $trueUserDate; //$post_data['TaskInstruct']['task_duedate'];
    	$modelInstruct->task_timedue = $trueUserTime;
		$modelInstruct->total_slack_hours= $post_data['TaskInstruct']['total_slack_hours'];
    	$modelInstruct->instruct_version = '001';
    	$modelInstruct->saved = 1;
    	$modelInstruct->mediadisplay_by = ($post_data['display_by']=='PM'?2:1);
    	$modelInstruct->load_prev = 0;
    	if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0){
    		$modelInstruct->load_prev = 1;
    	}
    	//echo "<pre>",print_r($modelInstruct->attributes),"</pre>";die;
    	$modelInstruct->save();
    	if($instruction_id >  0){
    		return $instruction_id;
    	}else{
    		return Yii::$app->db->getLastInsertId();
    	}
    }
    
    public function SaveUpdateMedia($post_data,$instruction_id=0,$task_id=0){
    	if($instruction_id >  0){
    		TaskInstructEvidence::deleteAll('task_instruct_id = '.$instruction_id);
    		
    	}
    	if($post_data['display_by'] == 'M'){
    		$evid=array();
    		if(isset($post_data['Evidence_contents'])){
    			foreach($post_data['Evidence_contents'] as $evid_content){
    				$evid_data = explode("_",$evid_content);
    				$evidmodel = new TaskInstructEvidence();
    				$evidmodel->task_instruct_id =$instruction_id;
    				$evidmodel->evidence_id =$evid_data[0];
    				$evidmodel->evidence_contents_id =$evid_data[1];
    				//$evidmodel->task_id =$task_id;
    				$evidmodel->prod_id =0;
    				$evidmodel->save();
    				$evid[$evid_data[0]]=$evid_data[0];
    			}
    		}
    		if(isset($post_data['Evidence'])){
    			foreach($post_data['Evidence'] as $evid_id){
    				if(!in_array($evid_id,$evid)){
    					$evidmodel = new TaskInstructEvidence();
    					$evidmodel->task_instruct_id =$instruction_id;
    					$evidmodel->evidence_id =$evid_id;
    					$evidmodel->evidence_contents_id =0;
    					//$evidmodel->task_id =$task_id;
    					$evidmodel->prod_id =0;
    					$evidmodel->save();
    				}
    			}
    		}
    	}
    	if($post_data['display_by'] == 'PM'){
    		if(isset($post_data['Production'])){
    			$prodevidexist = array();
    			$is_production_bates = 0;
    			$is_prod_media_id = '';
    			foreach ($post_data['Production'] as $prod_id=>$production){
    				$sqlhaspro="update tbl_evidence_production set has_projects=1 WHERE  tbl_evidence_production.id IN ($prod_id)";
					Yii::$app->db->createCommand($sqlhaspro)->execute();
    				$evidmodel = new TaskInstructEvidence();
    				$is_prod_media_id_new = array();
    				if(is_array($post_data['Production'][$prod_id])){
						foreach ($post_data['Production'][$prod_id] as $evid_id=>$evid){
    						if(is_array($post_data['Production'][$prod_id][$evid_id])){
    							foreach ($post_data['Production'][$prod_id][$evid_id] as $evid_content_id){
    								$evidmodel = new TaskInstructEvidence();
    								$evidmodel->task_instruct_id = $instruction_id;
    								$evidmodel->evidence_id = $evid_id;
    								$evidmodel->evidence_contents_id = $evid_content_id;
    								// $evidmodel->task_id =$task_id;
    								$evidmodel->prod_id = $prod_id;
    								$evidmodel->save();
    							}
    						}else{
    							$evidmodel = new TaskInstructEvidence();
    							$evidmodel->task_instruct_id =$instruction_id;
    							$evidmodel->evidence_id =$evid_id;
    							$evidmodel->evidence_contents_id =0;
    							//$evidmodel->task_id =$task_id;
    							$evidmodel->prod_id =$prod_id;
    							$evidmodel->save();
    						}
    						$is_prod_media_id = EvidenceProductionMedia::find()->select(['tbl_evidence_production_media.id'])->where(['evid_id' => $evid_id, 'prod_id' => $prod_id])->one();
    						if(!isset($prodevidexist[$prod_id][$evid_id]) || !in_array($evid_id,$prodevidexist[$prod_id]))
    						{
								$is_production_bates = EvidenceProductionBates::find()->where(['prod_media_id' => $is_prod_media_id->id, 'task_id' => $task_id])->count();
								if($is_prod_media_id->id > 0)
								{
									$is_prod_media_id_new[$is_prod_media_id->id] = $is_prod_media_id->id;	
									if($is_production_bates==0){
										$evidProdBates = new EvidenceProductionBates();
										$evidProdBates->prod_id=$prod_id;
										// $evidProdBates->prod_media_id=$evid_id;
										$evidProdBates->prod_media_id=$is_prod_media_id->id;
										$evidProdBates->task_id=$task_id;
										$evidProdBates->save();
										$prodevidexist[$prod_id][$evid_id] = $evid_id;
									}
								}
    						} 
    					}
    					if(!empty($is_prod_media_id_new)){
							EvidenceProductionBates::deleteAll('task_id = '.$task_id.' AND prod_id = '.$prod_id.' AND prod_media_id NOT IN ('.implode(",",$is_prod_media_id_new).')');
						}
					}else{
    					$evidmodel = new TaskInstructEvidence();
    					$evidmodel->task_instruct_id =$instruction_id;
    					$evidmodel->evidence_id =0;
    					$evidmodel->evidence_contents_id =0;
    				//	$evidmodel->task_id =$task_id;
    					$evidmodel->prod_id =$prod_id;
    					$evidmodel->save();
    				}
    			}
    		}
			$sql_has_pro="update tbl_evidence_production set has_projects=1 WHERE  tbl_evidence_production.has_projects=0 AND tbl_evidence_production.id IN (
			SELECT tbl_task_instruct_evidence.prod_id 
			from tbl_task_instruct_evidence 
			INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id and tbl_task_instruct.isactive=1
			INNER JOIN tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id
			INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
			Where tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close= 0
			)";
			Yii::$app->db->createCommand($sql_has_pro)->execute();
			$sql_no_has_pro="update tbl_evidence_production set has_projects=0 WHERE  tbl_evidence_production.has_projects=1 AND tbl_evidence_production.id NOT IN (
					SELECT tbl_task_instruct_evidence.prod_id 
					from tbl_task_instruct_evidence 
					INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id and tbl_task_instruct.isactive=1
					INNER JOIN tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id
					INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
					Where tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close= 0
			)";
			Yii::$app->db->createCommand($sql_no_has_pro)->execute();
    	}
		
    }
    
    public function updateSaveProject($post_data,$attachments,$instruction_id){
		//echo "<pre>",print_r($post_data),print_r($attachments),$instruction_id,"</pre>";die;
    	/*Save Instruction Data*/
    	$instruction_id = $this->saveUpdateInstruction($post_data,$instruction_id);
    	/*Save Instruction Data*/
    	/*Save Instruction Form Value*/
    	if(isset($post_data['properties'])){
    		(new FormBuilder())->saveInstructionFrom($post_data,$instruction_id);
    	}
    	/*Save Instruction Form Value*/
    	/*Save Instruction Media*/
    	$this->SaveUpdateMedia($post_data,$instruction_id,0);
    	/*Save Instruction Media*/
    	/*Save Instruction Service Task*/
    	//$in_use_servicetask_id =array();
    	$struse_servicetask='';
    	$TaskInstructServicetask_ids=array();
    	$i=1;
    	if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
				if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
					foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){

    					if($struse_servicetask==""){
    						$struse_servicetask="('".$servicetask_id."_".$loc."')";
    					}else{
    						$struse_servicetask= $struse_servicetask .", ('".$servicetask_id."_".$loc."')";
    					}
    				}
    			}
    		}
    		if(!empty($struse_servicetask)){
    			$sql ="DELETE FROM tbl_task_instruct_servicetask WHERE CONCAT(servicetask_id,'_',team_loc) NOT IN ($struse_servicetask) AND task_instruct_id = $instruction_id";
				Yii::$app->db->createCommand($sql)->execute();
    		}
    	}
    	if(isset($post_data['remove_attachment']) && $post_data['remove_attachment']!=""){
    		(new Mydocument())->removeAttachments($post_data['remove_attachment']);
    	}
		$total_est_time=0;
    	if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
    			if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
    				foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){
    					$service_info=Servicetask::findOne($servicetask_id);
    					$taskInstructServicetask_model  =new TaskInstructServicetask();
    					if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
    						$taskInstructServicetask_model  =TaskInstructServicetask::findOne($post_data['ServicetaskInstruct'][$servicetask_id]);
    					}
    					$taskInstructServicetask_model->task_instruct_id=$instruction_id;
    					$taskInstructServicetask_model->task_id=0;
    					$taskInstructServicetask_model->team_id=$service_info->teamId;
    					$taskInstructServicetask_model->teamservice_id=$service_info->teamservice_id;
    					$taskInstructServicetask_model->servicetask_id=$servicetask_id;
    					$taskInstructServicetask_model->team_loc=$loc;
    					$taskInstructServicetask_model->est_time=((isset($post_data['Est_times'][$servicetask_id]) && $post_data['Est_times'][$servicetask_id]!="") ? $post_data['Est_times'][$servicetask_id] : 0);
						$total_est_time+=$taskInstructServicetask_model->est_time;
    					//$taskInstructServicetask_model->teamservice_sla_id=intval($post_data['hdn_service_logic'][$servicetask_id]);
    					$taskInstructServicetask_model->sort_order=$i;
    					$taskInstructServicetask_model->save();
    					if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
    						$instructionservicetask_id = $post_data['ServicetaskInstruct'][$servicetask_id];
    					}else{
    						$instructionservicetask_id = Yii::$app->db->getLastInsertId();
    					}
    					$TaskInstructServicetask_ids[$servicetask_id]=$instructionservicetask_id;
    					if(!empty($_FILES['TaskInstruct']['name']['attachment'][$servicetask_id])){
    						$docmodel = new Mydocument();
    						$doc_arr['p_id']=0;
    						$doc_arr['reference_id']=$instructionservicetask_id;
    						$doc_arr['team_loc']=0;
    						$doc_arr['origination']="instruct";
    						$doc_arr['type']=0;
    						$doc_arr['is_private']=0;
    						$docmodel->origination = "instruct";
    						$file_arr=$docmodel->SaveInstructionmydocs('TaskInstruct','attachment',$servicetask_id,$doc_arr, "");
    					}
    					$i++;
    				}
    			}
    		}
    	}
		if($total_est_time==0) {
			$sql="UPDATE tbl_task_instruct SET total_slack_hours=0 WHERE id=$instruction_id";
			Yii::$app->db->createCommand($sql)->execute();
		}
    	return true;
    }
    /**
     *  Save Project 
     *  */
    public function saveProject($post_data,$attachments){
		//echo "<prE>",print_r($post_data);die;
    	$media_arr=array(1=>'M',2=>'PM');
    	/*Save Instruction Data*/
    	$modelInstruct = new TaskInstruct();
    	$modelInstruct->task_priority  		= $post_data['TaskInstruct']['task_priority'];
    	$modelInstruct->client_case_id 		= $post_data['case_id'];
    	$modelInstruct->project_name   		= $post_data['TaskInstruct']['project_name'];
    	$modelInstruct->requestor 	   		= $post_data['TaskInstruct']['requestor'];
    	$modelInstruct->task_projectreqtype = $post_data['TaskInstruct']['task_projectreqtype'];
    	$task_duedate = explode("/",$post_data['TaskInstruct']['task_duedate']);
    	$task_duedate = $task_duedate[2].'-'.$task_duedate[0].'-'.$task_duedate[1];
    	
    	$trueUserDate = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"YMD");
    	
    	$trueUserTime = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"time");
		
    	$modelInstruct->task_duedate = $trueUserDate; //$post_data['TaskInstruct']['task_duedate'];
    	$modelInstruct->task_timedue = $trueUserTime;
		$modelInstruct->total_slack_hours= $post_data['TaskInstruct']['total_slack_hours'];
    	$modelInstruct->instruct_version = '001';
    	$modelInstruct->saved = 1;
    	$modelInstruct->mediadisplay_by = ($post_data['display_by']=='PM'?2:1);
    	$modelInstruct->load_prev = 0;
    	if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0){
    		$modelInstruct->load_prev = 1;
    	}
    	$modelInstruct->save();
    	$instruction_id = Yii::$app->db->getLastInsertId();
    	/*Save Instruction Data*/
    	
    	/*Save Instruction Form Value*/
    	if(isset($post_data['properties'])) {
    		(new FormBuilder())->saveInstructionFrom($post_data, $instruction_id);
    	}
    	/*Save Instruction Form Value*/
		$removeAttachments=array();
    	if(isset($post_data['remove_attachment']) && $post_data['remove_attachment']!=""){
			$removeAttachments=explode(",",$post_data['remove_attachment']);
    		//(new Mydocument())->removeAttachments($post_data['remove_attachment']);
    	}
    	/*Save Instruction Media*/
    	if($post_data['display_by'] == 'M'){
	    	$evid=array();
	    	if(isset($post_data['Evidence_contents'])){
	    		foreach($post_data['Evidence_contents'] as $evid_content){
	    			$evid_data = explode("_",$evid_content);
	    			$evidmodel = new TaskInstructEvidence();
	    			$evidmodel->task_instruct_id =$instruction_id;
	    			$evidmodel->evidence_id =$evid_data[0];
	    			$evidmodel->evidence_contents_id =$evid_data[1];
	    			//$evidmodel->task_id =0;
	    			$evidmodel->prod_id =0;
	    			$evidmodel->save();
	    			$evid[$evid_data[0]]=$evid_data[0];
	    		}
	    	}
	    	if(isset($post_data['Evidence'])){
	    		foreach($post_data['Evidence'] as $evid_id){
	    			if(!in_array($evid_id,$evid)){
			    		$evidmodel = new TaskInstructEvidence();
			    		$evidmodel->task_instruct_id =$instruction_id;
			    		$evidmodel->evidence_id =$evid_id;
			    		$evidmodel->evidence_contents_id =0;
			    		//$evidmodel->task_id =0;
			    		$evidmodel->prod_id =0;
			    		$evidmodel->save();
	    			}
	    		}
	    	}
    	}
    	if($post_data['display_by'] == 'PM'){
    		if(isset($post_data['Production'])){
    			foreach ($post_data['Production'] as $prod_id=>$production){
    				$evidmodel = new TaskInstructEvidence();
    				if(is_array($post_data['Production'][$prod_id])){
    					foreach ($post_data['Production'][$prod_id] as $evid_id=>$evid){
    						if(is_array($post_data['Production'][$prod_id][$evid_id])){
    							foreach ($post_data['Production'][$prod_id][$evid_id] as $evid_content_id){
    								$evidmodel = new TaskInstructEvidence();
    								$evidmodel->task_instruct_id =$instruction_id;
    								$evidmodel->evidence_id =$evid_id;
    								$evidmodel->evidence_contents_id =$evid_content_id;
    								//$evidmodel->task_id =0;
    								$evidmodel->prod_id =$prod_id;
    								$evidmodel->save();
    							}
    						}else{
    							$evidmodel = new TaskInstructEvidence();
    							$evidmodel->task_instruct_id =$instruction_id;
    							$evidmodel->evidence_id =$evid_id;
    							$evidmodel->evidence_contents_id =0;
    							//$evidmodel->task_id =0;
    							$evidmodel->prod_id =$prod_id;
    							$evidmodel->save();
    						}
    						
    					}
    				}else{
    					$evidmodel = new TaskInstructEvidence();
    					$evidmodel->task_instruct_id =$instruction_id;
    					$evidmodel->evidence_id =0;
    					$evidmodel->evidence_contents_id =0;
    					//$evidmodel->task_id =0;
    					$evidmodel->prod_id =$prod_id;
    					$evidmodel->save();
    				}
    			}
    		}
    	}
    	/*Save Instruction Media*/
    	/*Save Instruction Service Task*/
    	$TaskInstructServicetask_ids=array();
    	$i=1;
		$total_est_time=0;
    	if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
    			if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
    				foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){
    					$service_info=Servicetask::findOne($servicetask_id);
    					$taskInstructServicetask_model  =new TaskInstructServicetask();
    					$taskInstructServicetask_model->task_instruct_id=$instruction_id;
    					$taskInstructServicetask_model->task_id=0;
    					$taskInstructServicetask_model->team_id=$service_info->teamId;
    					$taskInstructServicetask_model->teamservice_id=$service_info->teamservice_id;
    					$taskInstructServicetask_model->servicetask_id=$servicetask_id;
    					$taskInstructServicetask_model->team_loc=$loc;
    					$taskInstructServicetask_model->est_time=((isset($post_data['Est_times'][$servicetask_id]) && $post_data['Est_times'][$servicetask_id]!="") ? $post_data['Est_times'][$servicetask_id] : 0);
    					$total_est_time+=$taskInstructServicetask_model->est_time;
						//$taskInstructServicetask_model->teamservice_sla_id=intval($post_data['hdn_service_logic'][$servicetask_id]);
    					$taskInstructServicetask_model->sort_order=$i;
    					$taskInstructServicetask_model->save();
    					$instructionservicetask_id = Yii::$app->db->getLastInsertId();
    					$TaskInstructServicetask_ids[$servicetask_id]=$instructionservicetask_id;
    					if(!empty($_FILES['TaskInstruct']['name']['attachment'][$servicetask_id])){
    						$docmodel = new Mydocument();
    						$doc_arr['p_id']=0;
    						$doc_arr['reference_id']=$instructionservicetask_id;
    						$doc_arr['team_loc']=0;
    						$doc_arr['origination']="instruct";
    						$doc_arr['type']=0;
    						$doc_arr['is_private']=0;
    						$docmodel->origination = "instruct";
    						$file_arr=$docmodel->SaveInstructionmydocs('TaskInstruct','attachment',$servicetask_id,$doc_arr, "");
    					}
						/*IRT-649 copy over attachments from load prev project*/
						if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0) {
								$load_prev_project_id=$post_data['load_prev_project_id'];
								$sql_ref="SELECT tbl_task_instruct_servicetask.id FROM tbl_task_instruct_servicetask
										  INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id
										  WHERE tbl_task_instruct_servicetask.task_id=$load_prev_project_id AND tbl_task_instruct_servicetask.servicetask_id=$servicetask_id
										  AND tbl_task_instruct.isactive=1";
								$reference_id=Yii::$app->db->createCommand($sql_ref)->queryScalar();
								$doc_data=Mydocument::find()->where(['origination'=>'instruct','reference_id'=>$reference_id])->joinWith(['mydocumentsBlobs'])->all();
								if(!empty($doc_data)) {
									foreach($doc_data as $doc)
									{
										if(is_array($removeAttachments)) {
											if(!in_array($doc->id, $removeAttachments)) {
												$MydocumentsBlob_model = new MydocumentsBlob();
												$MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
												$MydocumentsBlob_model->save(false);
												$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
											// echo "<pre>",print_r($doc->mydocumentsBlobs);die;
												//mydocumentsBlobs
												$docmodel = new Mydocument();
												$docmodel->p_id = $doc->p_id;
												$docmodel->reference_id = $instructionservicetask_id;
												$docmodel->team_loc = $doc->team_loc;
												$docmodel->fname = $doc->fname;
												$docmodel->origination = "instruct";
												$docmodel->u_id = Yii::$app->user->identity->id;
												$docmodel->is_private = $doc->is_private;
												$docmodel->type = $doc->type;
												$docmodel->doc_id = $blob_doc_id;
												$docmodel->doc_size = $doc->doc_size;
												$docmodel->doc_type = $doc->doc_type;
												$docmodel->save(false);
											}
										}else{
												$MydocumentsBlob_model = new MydocumentsBlob();
												$MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
												$MydocumentsBlob_model->save(false);
												$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
											// echo "<pre>",print_r($doc->mydocumentsBlobs);die;
												//mydocumentsBlobs
												$docmodel = new Mydocument();
												$docmodel->p_id = $doc->p_id;
												$docmodel->reference_id = $instructionservicetask_id;
												$docmodel->team_loc = $doc->team_loc;
												$docmodel->fname = $doc->fname;
												$docmodel->origination = "instruct";
												$docmodel->u_id = Yii::$app->user->identity->id;
												$docmodel->is_private = $doc->is_private;
												$docmodel->type = $doc->type;
												$docmodel->doc_id = $blob_doc_id;
												$docmodel->doc_size = $doc->doc_size;
												$docmodel->doc_type = $doc->doc_type;
												$docmodel->save(false);
										}
									}
								}
						}
						/*IRT-649 copy over attachments from load prev project*/
    					$i++;
    				}
    			}
    		}
    	}
		if($total_est_time==0) {
			$sql="UPDATE tbl_task_instruct SET total_slack_hours=0 WHERE id=$instruction_id";
			Yii::$app->db->createCommand($sql)->execute();
		}
    	return true;
    }
    public function changeProject($post_data,$attachments,$instruction_id=0){
		
		//echo "<pre>",print_r($post_data);
		//,print_r($attachments),print_r($instruction_id),"</prE>";
		//echo $instruction_id;die;
   		$media_arr=array(1=>'M',2=>'PM');
    	$task_id = $post_data['task_id'];
    	/*Save Instruction Data*/
    	$modelInstruct = new TaskInstruct();
    	$taskinstruct_data = TaskInstruct::find()->where(['task_id'=>$post_data['task_id'],'isactive'=>1])->one();
        $oldtaskinstruct_data=TaskInstructServicetask::find()->where(['task_instruct_id'=>$taskinstruct_data->id,'task_id'=>$task_id])->all();
        $oldtaskinstruct_arr=array();
        foreach($oldtaskinstruct_data as $oldinstruct){
            $oldtaskinstruct_arr[]=$oldinstruct->id;
        }
        $instruct_version=str_pad(($taskinstruct_data->instruct_version+1), 3, 0, STR_PAD_LEFT);
        $modelInstruct->client_case_id = $post_data['case_id'];
        $modelInstruct->task_priority = $post_data['TaskInstruct']['task_priority'];
    	$modelInstruct->task_id = $post_data['task_id'];
    	$modelInstruct->isactive = '1';
    	$modelInstruct->project_name = $post_data['TaskInstruct']['project_name'];
    	$modelInstruct->requestor = $post_data['TaskInstruct']['requestor'];
    	$modelInstruct->task_projectreqtype = $post_data['TaskInstruct']['task_projectreqtype'];
    	$task_duedate = explode("/",$post_data['TaskInstruct']['task_duedate']);
    	$task_duedate = $task_duedate[2].'-'.$task_duedate[0].'-'.$task_duedate[1];
    	 
    	$trueUserDate = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"YMD");
    	if(isset($post_data['TaskInstruct']['task_timedue']))
    		$trueUserTime = (new Options)->ConvertOneTzToAnotherTz($task_duedate." ".$post_data['TaskInstruct']['task_timedue'],$_SESSION['usrTZ'],'UTC',"time");
		else 
			$trueUserTime = $taskinstruct_data->task_timedue;
    	 
    	$modelInstruct->task_duedate     = $trueUserDate; //$post_data['TaskInstruct']['task_duedate'];
    	$modelInstruct->task_timedue     = $trueUserTime;
		$modelInstruct->total_slack_hours= $post_data['TaskInstruct']['total_slack_hours'];
    	$modelInstruct->instruct_version = "$instruct_version";
    	$modelInstruct->saved 			 = 0;
    	$modelInstruct->mediadisplay_by  = ($post_data['display_by']=='PM'?2:1);
    	$modelInstruct->load_prev 		 = 0;
    	if(isset($post_data['load_prev_project_id']) && $post_data['load_prev_project_id'] > 0){
    		$modelInstruct->load_prev 	 = 1;
    	}
        $modelInstruct->save();
		$task_due_datetime = date('Y-m-d H:i:s',strtotime($task_duedate." ".$post_data['TaskInstruct']['task_timedue']));
		$current_due_datetime  = date('Y-m-d H:i:s');
		
		if(strtotime($task_due_datetime) > strtotime($current_due_datetime)){
			$delete_pastdue_sql="DELETE FROM tbl_project_pastdue WHERE task_id IN (".$post_data['task_id'].")";
            Yii::$app->db->createCommand($delete_pastdue_sql)->execute();
		}

		$old_evid_prod_data=array();
		if(isset($taskinstruct_data->id) && $taskinstruct_data->id > 0){
			$old_evid_prod_data=ArrayHelper::map(TaskInstructEvidence::find()->where(['task_instruct_id'=>$taskinstruct_data->id])->all(),'prod_id','prod_id');
		}
        $instruction_id = Yii::$app->db->getLastInsertId();
		if($instruction_id == 0){
			$instruction_id=$modelInstruct->id;
		}
        TaskInstruct::updateAll(['isactive' => 0],'task_id='.$post_data['task_id'].' AND id < '.$instruction_id);        
		//die('mohsin');
        /*Save Instruction Form Value*/
    	if(isset($post_data['properties'])){
    		//(new FormBuilder())->saveInstructionFrom($post_data,$instruction_id,'change');
    		(new FormBuilder())->saveInstructionFrom($post_data,$instruction_id);
    	}
    	/*Save Instruction Form Value*/
    	
    	/*Save Instruction Media*/
    	$this->SaveUpdateMedia($post_data,$instruction_id,$task_id);
    	/*Save Instruction Media*/
    	/*Save Instruction Service Task*/
    	$TaskInstructServicetask_ids=array();
    	$i=1;
        if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
    			if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
    				foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){
    					if($struse_servicetask==""){
    						$struse_servicetask='('.$servicetask_id.','.$loc.')';
    					}else{
    						$struse_servicetask= $struse_servicetask .', ('.$servicetask_id.','.$loc.')';
    					}
    				}
    			}
    		}
    		if(!empty($in_use_servicetask_id)){
    			$sql ="DELETE FROM tbl_task_instruct_servicetask WHERE (servicetask_id,team_loc) NOT IN ($struse_servicetask) AND task_instruct_id = $instruction_id";
    			Yii::$app->db->createCommand($sql)->execute();
    		}
    		$mysql ="DELETE FROM tbl_task_instruct_servicetask_sla WHERE tbl_task_instruct_servicetask_sla.task_instruct_servicetask_id IN (SELECT id FROM tbl_task_instruct_servicetask WHERE task_instruct_id = $instruction_id)";
    		Yii::$app->db->createCommand($mysql)->execute();
    	}
    	
        $removedAttachments = array();
    	if(isset($post_data['remove_attachment']) && $post_data['remove_attachment']!=""){
    		//(new Mydocument())->removeAttachments($post_data['remove_attachment']);
    		$removeAttachments = explode(",",$post_data['remove_attachment']);
    	}
        
        TasksTeams::deleteAll(['task_id'=>$task_id]);
		$has_sla_est = false;
        if(isset($post_data['ServiceteamLoc1'])){
    		foreach ($post_data['ServiceteamLoc1'] as $servicetask_id=>$locs){
				$is_changeinstruction = $this->IsChangedTaskInstruction($task_id,$servicetask_id);
				if($is_changeinstruction){
					/*Sending Change Instructions subscribtion alert Email*/				
						//(new SettingsEmail)->sendEmail
						EmailCron::saveBackgroundEmail(4,'changed_instructions',$data=array('case_id'=>$post_data['case_id'],'project_id'=>$task_id,'instrid'=>$instruction_id));
					/*Sending Change Instructions subscribtion alert Email*/
		
				}
    			if(is_array($post_data['ServiceteamLoc1'][$servicetask_id])){
    				foreach($post_data['ServiceteamLoc1'][$servicetask_id] as $loc){
    					$service_info=Servicetask::findOne($servicetask_id);
    					$taskInstructServicetask_model  =new TaskInstructServicetask();
    					if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
    						//$taskInstructServicetask_model  =TaskInstructServicetask::findOne($post_data['ServicetaskInstruct'][$servicetask_id]);
    					}
    					$taskInstructServicetask_model->task_instruct_id=$instruction_id;
    					$taskInstructServicetask_model->task_id=$task_id;
    					$taskInstructServicetask_model->team_id=$service_info->teamId;
    					$taskInstructServicetask_model->teamservice_id=$service_info->teamservice_id;
    					$taskInstructServicetask_model->servicetask_id=$servicetask_id;
    					$taskInstructServicetask_model->team_loc=$loc;
    					$taskInstructServicetask_model->est_time=((isset($post_data['Est_times'][$servicetask_id]) && $post_data['Est_times'][$servicetask_id]!="") ? $post_data['Est_times'][$servicetask_id] : 0);
    					$taskInstructServicetask_model->sort_order=$i;
						
						if($taskInstructServicetask_model->est_time > 0){
							$has_sla_est =true;
						}

    					if($taskInstructServicetask_model->save()){
							$instructionservicetask_id = Yii::$app->db->getLastInsertId();            
							$TaskInstructServicetask_ids[$servicetask_id]=$instructionservicetask_id;
    					}else{
    						echo "<pre>",print_r($taskInstructServicetask_model->getErrors());die;
    					}
    					$unit_assigned_to=0;
    					$unit_status = 0;
    					$is_transition = '0';
    					if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
    						//$instructionservicetask_id = $post_data['ServicetaskInstruct'][$servicetask_id];
							$model_unit=TasksUnits::find()->where(['task_instruct_servicetask_id'=>$post_data['ServicetaskInstruct'][$servicetask_id],'tbl_tasks_units.task_instruct_id'=>$taskinstruct_data->id,'task_id'=>$task_id])->one();
							
							if(empty($model_unit))
								$model_unit = new TasksUnits();
							else
							{
								$key = array_search($model_unit->task_instruct_servicetask_id, $oldtaskinstruct_arr);
								$unit_status = $model_unit->unit_status;
								if($is_changeinstruction && $unit_status==4){
									$unit_status = 2;
								}
								$unit_assigned_to=$model_unit->unit_assigned_to;
								$is_transition = $model_unit->is_transition;
								unset($oldtaskinstruct_arr[$key]);
							}
							//echo $post_data['ServicetaskInstruct'][$servicetask_id].'<pre>'; print_r($model_unit);die;
    					}else{
    						$model_unit = new TasksUnits();
    					}
    					
                                        
    					/*Save Taks Unit*/
                        //$task_id=169;
    					$model_unit->task_id= $task_id;
    					$model_unit->task_instruct_id 				=  $instruction_id;
    					$model_unit->task_instruct_servicetask_id   =  $instructionservicetask_id;
    					$model_unit->unit_assigned_to				=  $unit_assigned_to;
    					$model_unit->unit_status 					=  $unit_status;
    					$model_unit->is_transition 					=  $is_transition;
    					$model_unit->team_id=$service_info->teamId;
    					$model_unit->teamservice_id=$service_info->teamservice_id;
    					$model_unit->servicetask_id=$servicetask_id;
    					$model_unit->team_loc=$loc;
    					$model_unit->est_time=((isset($post_data['Est_times'][$servicetask_id]) && $post_data['Est_times'][$servicetask_id]!="") ? $post_data['Est_times'][$servicetask_id] : 0);
    					$model_unit->sort_order=$i;
    					//$model_unit->save();
						if($model_unit->est_time > 0){
							$has_sla_est=true;
						}
    					if($model_unit->save()){
							//$has_sla_est = true;
    					}else{
    						echo "<pre>",print_r($model_unit->getErrors());die;
    					}
                                        //print_r($TaskInstructServicetask_ids);die;
    					/*save Task Teams */
    					if(!TasksTeams::find()->where(['task_id'=>$task_id,'team_id'=>$service_info->teamId,'team_loc'=>$loc])->count()){
	    					$modelTasksTeams = new TasksTeams();
	                        $modelTasksTeams->task_id   	 = $task_id;
	    					$modelTasksTeams->team_id   	 = $service_info->teamId;
	    					$modelTasksTeams->team_loc       = $loc;
	    					//$modelTasksTeams->teamservice_id = $service_info->teamservice_id;
	    					//$modelTasksTeams->save();
	    					if($modelTasksTeams->save()){
	    					}else{
	    						echo "<pre>",print_r($modelTasksTeams->getErrors());die;
	    					}
    					}
    					/*Save Task Team sla*/
    					if(isset($post_data['hdn_service_logic'][$servicetask_id]) && $post_data['hdn_service_logic'][$servicetask_id] != 0){
    						$logics = explode(",",$post_data['hdn_service_logic'][$servicetask_id]);
    						if(!empty($logics)){
	    						foreach ($logics as $sla_logic_id){
	    							$modelTasksInstServSla = new TaskInstructServicetaskSla();
	    							$modelTasksInstServSla->task_instruct_servicetask_id = $instructionservicetask_id;
	    							$modelTasksInstServSla->teamservice_sla_id = $sla_logic_id;
	    							if($modelTasksInstServSla->save()){
										$has_sla_est = true;
	    							}else{
	    								echo "<pre>",print_r($modelTasksInstServSla->getErrors());die;
	    							}
	    						}
    						}
    					}
                                        
					    if(isset($post_data['ServicetaskInstruct'][$servicetask_id]) && $post_data['ServicetaskInstruct'][$servicetask_id] > 0 && $post_data['ServicetaskInstruct'][$servicetask_id]!=""){
								//$post_data['ServicetaskInstruct'][$servicetask_id]=658;
								
								$doc_data=Mydocument::find()->where(['origination'=>'instruct','reference_id'=>$post_data['ServicetaskInstruct'][$servicetask_id]])->joinWith(['mydocumentsBlobs'])->all();
								
								foreach($doc_data as $doc)
								{
									if(is_array($removeAttachments)) {
										if(!in_array($doc->id, $removeAttachments)) {
											$MydocumentsBlob_model = new MydocumentsBlob();
											$MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
											$MydocumentsBlob_model->save(false);
											$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
										// echo "<pre>",print_r($doc->mydocumentsBlobs);die;
											//mydocumentsBlobs
											$docmodel = new Mydocument();
											$docmodel->p_id = $doc->p_id;
											$docmodel->reference_id = $instructionservicetask_id;
											$docmodel->team_loc = $doc->team_loc;
											$docmodel->fname = $doc->fname;
											$docmodel->origination = "instruct";
											$docmodel->u_id = Yii::$app->user->identity->id;
											$docmodel->is_private = $doc->is_private;
											$docmodel->type = $doc->type;
											$docmodel->doc_id = $blob_doc_id;
											$docmodel->doc_size = $doc->doc_size;
											$docmodel->doc_type = $doc->doc_type;
											$docmodel->save(false);
										}
									}else{
											$MydocumentsBlob_model = new MydocumentsBlob();
											$MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
											$MydocumentsBlob_model->save(false);
											$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
										// echo "<pre>",print_r($doc->mydocumentsBlobs);die;
											//mydocumentsBlobs
											$docmodel = new Mydocument();
											$docmodel->p_id = $doc->p_id;
											$docmodel->reference_id = $instructionservicetask_id;
											$docmodel->team_loc = $doc->team_loc;
											$docmodel->fname = $doc->fname;
											$docmodel->origination = "instruct";
											$docmodel->u_id = Yii::$app->user->identity->id;
											$docmodel->is_private = $doc->is_private;
											$docmodel->type = $doc->type;
											$docmodel->doc_id = $blob_doc_id;
											$docmodel->doc_size = $doc->doc_size;
											$docmodel->doc_type = $doc->doc_type;
											$docmodel->save(false);
									}
								}
								//echo $post_data['ServicetaskInstruct'][$servicetask_id];
    					}
    					if(!empty($_FILES['TaskInstruct']['name']['attachment'][$servicetask_id])){
    						$docmodel = new Mydocument();
    						$doc_arr['p_id']=0;
    						$doc_arr['reference_id']=$instructionservicetask_id;
    						$doc_arr['team_loc']=0;
    						$doc_arr['origination']="instruct";
    						$doc_arr['type']=0;
    						$doc_arr['is_private']=0;
    						$docmodel->origination = "instruct";
    						$file_arr=$docmodel->SaveInstructionmydocs('TaskInstruct','attachment',$servicetask_id,$doc_arr, "");
    					}
    					$i++;
    				}
    			}
    		}
    	}

        $removed_taskinstruct_arr=$oldtaskinstruct_arr;
        if(!empty($removed_taskinstruct_arr))
        {
            foreach($removed_taskinstruct_arr as $rinstruct)
            {
                $removedunit_data=TasksUnits::find()->where(['task_instruct_servicetask_id'=>$rinstruct,'tbl_tasks_units.task_instruct_id'=>$taskinstruct_data->id])->innerJoinWith(['taskInstructServicetask'=> function(\yii\db\ActiveQuery $query) use($task_id) { $query->where(['tbl_task_instruct_servicetask.task_id'=>$task_id]); }])->one();
                //echo $post_data['ServicetaskInstruct'][$servicetask_id].'--'.$removedunit_data->id;
                
                TasksUnitsTransactionLog::deleteAll(['tasks_unit_id'=>$removedunit_data->id]);
                $implode_todo = TasksUnitsTodos::find()->where(['tasks_unit_id'=>$removedunit_data->id])->select(['id']);
                TasksUnitsTodoTransactionLog::deleteAll(['in','todo_id',$implode_todo]);
                TasksUnitsTodos::deleteAll(['tasks_unit_id'=>$removedunit_data->id]);
                TasksUnitsBilling::deleteAll(['tasks_unit_id'=>$removedunit_data->id]);
                TasksUnitsData::deleteAll(['tasks_unit_id'=>$removedunit_data->id]);
                TasksUnits::deleteAll(['id'=>$removedunit_data->id]);
            }
        }
        /*//IRT - 275 check if sla or est time included in project if has sal or est time then remove time from due date and update save_datetime
		if($has_sla_est === true){
			$this->updateSaveDatetime($task_id,$instruction_id);
		}else{
			//store full hrs in manual_hrs and update save_datetime
			$this->updateManualHrs($task_id,$instruction_id);
		}
		//IRT - 275*/
    	(new ActivityLog)->generateLog('Project', 'Resubmitted', $task_id, "project#:".$task_id);
    	
    	/*Sending New  Project Subscription Alert Email CHANGE*/
    	//(new SettingsEmail)->sendEmail(1,'is_sub_new_task',$data=array('case_id'=>$post_data['case_id'],'project_id'=>$task_id));
    	/*Sending New  Project Subscription Alert Email*/

		if($post_data['display_by'] == 'PM' && $taskinstruct_data->mediadisplay_by='M'){
			if(isset($post_data['Production'])){
				$prodevidexist = array();
				foreach($post_data['Production'] as $prod_id=>$production){
					if(!in_array($prod_id,$old_evid_prod_data)){
					//(new SettingsEmail)->sendEmail
					EmailCron::saveBackgroundEmail(2, 'is_sub_new_production', $data = array('case_id' => $post_data['case_id'], 'prod_id' => $prod_id,'project_id'=>$task_id));
					}
				}
			}
		}

    	/*Sending New  Project Subscription Alert Email*/
    	(new Tasks)->setProjectTasksStatus($task_id);
    	return true;
    }

	/*
	*update Manual Hrs in TaksInstruct Table
	*
	*/
	/*public function updateManualHrs($task_id,$instruction_id){
		
		date_default_timezone_set($_SESSION['usrTZ']);

		$instruction_data=TaskInstruct::findOne($instruction_id);
		$duedatetime=$instruction_data->task_duedate." ".$instruction_data->task_timedue;
		$enddatetime=date('Y-m-d H:i:s',strtotime($duedatetime));

		$enddatetime=(new Options)->ConvertOneTzToAnotherTz($enddatetime,'UTC',$_SESSION['usrTZ'],"YMDHIS");
		$current_date=(new Options)->ConvertOneTzToAnotherTz($instruction_data->created,'UTC',$_SESSION['usrTZ'],"YMDHIS");

		$businesshours = TeamserviceSlaBusinessHours::find()->one();
    	$workinghours = $businesshours->workinghours;
    	$start_time = $businesshours->start_time;
    	$end_time = $businesshours->end_time;
    	$workingdays = json_decode($businesshours->workingdays,true);

		if(date("i", strtotime($current_date)) > 30){
			if(date("Y-m-d H:i:00", strtotime($current_date." +1 Hour")) > date("Y-m-d $end_time:00",strtotime($current_date))){
				$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
			} else if(date("Y-m-d H:i:00", strtotime($current_date)) < date("Y-m-d $start_time:00",strtotime($current_date))) {
				$current_date = date("Y-m-d $start_time:00", strtotime($current_date));
			} else {
				$current_date = date("Y-m-d H:00:00", strtotime($current_date." +1 hour"));
			}
		}
		if(date("i", strtotime($current_date)) > 0 && date("i",strtotime($current_date)) < 30){
			if(date("Y-m-d H:i:00", strtotime($current_date." +30 minutes")) > date("Y-m-d $end_time:s",strtotime($current_date))){
				$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
			} else if(date("Y-m-d H:i:00",strtotime($current_date)) < date("Y-m-d $start_time:00", strtotime($current_date))) {
				$current_date = date("Y-m-d $start_time:00", strtotime($current_date));
			} else {
				$current_date = date("Y-m-d H:30:00",strtotime($current_date));
			}
		}

		$slaenddatetime = date("Y-m-d $end_time:00",strtotime($current_date));

		if(strtotime($current_date) == strtotime($slaenddatetime) || strtotime($current_date) > strtotime($slaenddatetime)) {
			$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
		}
		
		$holidaysRec = TeamserviceSlaHolidays::find()->all();
		$holidayAr = array();
		foreach ($holidaysRec as $hol){
			$holidayAr[] = $hol->holidaydate;
		}

		$start_datetime = (new Options)->ConvertOneTzToAnotherTz($current_date,$_SESSION['usrTZ'],'UTC',"YMDHIS");
		$occupiedhours = 0;

		while($current_date < $enddatetime){

			$currentday = date("N",strtotime($current_date));
			$currentdateforholiday = date("m/d/Y",strtotime($current_date));
			
			if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
                            
				$todayremainhours = date("H:i:00",strtotime($current_date)); 
				if(strtotime($current_date) > strtotime($slaenddatetime)){
					$todayremainhours = date("$start_time:00",strtotime($current_date));
				} 

				if($todayremainhours=='00:00:00' && $end_time=='24:00')
				{
					$workingHours1 = '24';
					$workingHours2 = '00';
				} 
				else 
				{
					$time1 = new \DateTime($todayremainhours);
					$time2 = new \DateTime("$end_time:00");
					$interval = $time1->diff($time2);
					$workingHours1 = $interval->format('%H');
					$workingHours2 = $interval->format('%i');
				}

				if($workingHours2 == "30"){
					$workingHours1 += 0.5;
				}
            	$occupiedhours += $workingHours1;
			}
			//if(($lasthours!=$workinghours) && $occupiedhours != $totalhours){
			$current_date = date("Y-m-d $start_time:00",strtotime($current_date." +1 days"));
			//}
		}

		//if($current_date >= $enddatetime )
		
		date_default_timezone_set('UTC');

		
	}*/

	public function getHours($totalhours, $current_date = 0, $type)
	{
		date_default_timezone_set($_SESSION['usrTZ']);

		$businesshours = TeamserviceSlaBusinessHours::find()->one();
    	$workinghours = $businesshours->workinghours;
    	$start_time = $businesshours->start_time;
    	$end_time = $businesshours->end_time;
    	$workingdays = json_decode($businesshours->workingdays,true);
		
		//$current_date = date('Y-m-d 17:30:00', strtotime($current_date));
		
		if($current_date == 0){
			if(date("i") > 30){
				if(date("Y-m-d H:i:00", strtotime("+1 Hour")) > date("Y-m-d $end_time:00")){
					$current_date = date("Y-m-d $start_time:00", strtotime("+1 days"));
				} else if(date("Y-m-d H:i:00") < date("Y-m-d $start_time:00")) {
					$current_date = date("Y-m-d $start_time:00");
				} else {
					$current_date = date("Y-m-d H:00:00", strtotime("+1 hour"));
				}
			}
			if(date("i") > 0 && date("i") < 30){
				if(date("Y-m-d H:i:00", strtotime("+30 minutes")) > date("Y-m-d $end_time:s")){
					$current_date = date("Y-m-d $start_time:00", strtotime("+1 days"));
				} else if(date("Y-m-d H:i:00") < date("Y-m-d $start_time:00")) {
					$current_date = date("Y-m-d $start_time:00");
				} else {
					$current_date = date("Y-m-d H:30:00");
				}
			}
		} else {
			if(date("i", strtotime($current_date)) > 30){
				if(date("Y-m-d H:i:00", strtotime($current_date." +1 Hour")) > date("Y-m-d $end_time:00",strtotime($current_date))){
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
				} else if(date("Y-m-d H:i:00", strtotime($current_date)) < date("Y-m-d $start_time:00",strtotime($current_date))) {
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date));
				} else {
					$current_date = date("Y-m-d H:00:00", strtotime($current_date." +1 hour"));
				}
			}
			if(date("i", strtotime($current_date)) > 0 && date("i",strtotime($current_date)) < 30){
				if(date("Y-m-d H:i:00", strtotime($current_date." +30 minutes")) > date("Y-m-d $end_time:s",strtotime($current_date))){
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
				} else if(date("Y-m-d H:i:00",strtotime($current_date)) < date("Y-m-d $start_time:00", strtotime($current_date))) {
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date));
				} else {
					$current_date = date("Y-m-d H:30:00",strtotime($current_date));
				}
			}
		}

		$slaenddatetime = date("Y-m-d $end_time:00",strtotime($current_date));

		if(strtotime($current_date) == strtotime($slaenddatetime) || strtotime($current_date) > strtotime($slaenddatetime)) {
			$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
		}
		
		$holidaysRec = TeamserviceSlaHolidays::find()->all();
		$holidayAr = array();
		foreach ($holidaysRec as $hol){
			$holidayAr[] = $hol->holidaydate;
		}
		
		$occupiedhours = 0;
		$days = 0;
		$lasthours = 0;
		$lastminutes = 0;
		
		while($totalhours > $occupiedhours){
		 
			$currentday = date("N",strtotime($current_date));
			$currentdateforholiday = date("m/d/Y",strtotime($current_date));
			
			if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
                            
				$todayremainhours = date("H:i:00",strtotime($current_date)); 
				if(strtotime($current_date) > strtotime($slaenddatetime)){
					$todayremainhours = date("$start_time:00",strtotime($current_date));
				} 

				if($todayremainhours=='00:00:00' && $end_time=='24:00')
				{
					$workingHours1 = '24';
					$workingHours2 = '00';
				} 
				else 
				{
					$time1 = new \DateTime($todayremainhours);
					$time2 = new \DateTime("$end_time:00");
					$interval = $time1->diff($time2);
					$workingHours1 = $interval->format('%H');
					$workingHours2 = $interval->format('%i');
				}

                if($workingHours2 == "30"){
					$workingHours1 += 0.5;
				}
               
				if(($occupiedhours+$workingHours1) < $totalhours) {
					$occupiedhours += $workingHours1;
				} else {
					$lasthours = $totalhours - $occupiedhours; 
					$remainhoursmins = explode(".",$lasthours);
					if(!empty($remainhoursmins)){
						$remainlasthours = $remainhoursmins[0];
						$lastminutes = $remainhoursmins[1];
						if(isset($remainhoursmins[1]) && $remainhoursmins[1] == 5){
							$remainlastminutes = 30;
						}
					}
					$occupiedhours += $lasthours;
				} 
				
				$days += 1;
			}
			
			if(($lasthours!=$workinghours) && $occupiedhours != $totalhours){
				$current_date = date("Y-m-d $start_time:00",strtotime($current_date." +1 days"));
			}
		}
					
		$seconds = ($occupiedhours * 3600);
		$array = array();
		$array['due_date'] = date("m/d/Y",strtotime($current_date));
		$date = date("Y-m-d",strtotime($current_date));
		
		$currentTime = date("H:i",strtotime($current_date));
		
		if($remainlasthours >= 1)
			$currentTime = date("H:i",strtotime($date." ".$currentTime." +$remainlasthours hour"));
			
		if($remainlastminutes > 0)	
			$currentTime = date("H:i",strtotime($date." ".$currentTime." +$remainlastminutes minutes"));
			
		$time = $array['due_time'] = $currentTime;
		
		$array['org_due_time'] = (new Options)->ConvertOneTzToAnotherTz($date." ".$time.":00",$_SESSION['usrTZ'],'UTC',"HI");
		$array['totalhours'] =  $totalhours;
		$array['days'] = $this->secondsToWords($seconds,'d',$workinghours);
		$array['hours'] =  $this->secondsToWords($seconds,'h',$workinghours);
		$array['workingHours'] = $workinghours;
		
		date_default_timezone_set('UTC');
		return json_encode($array);
	}

    public function secondsToWords($seconds, $type, $workinghours)
    {
    	/*** get the days ***/
    	if($type == 'd'){
    		$days = intval(intval($seconds) / (3600 * $workinghours));
    		if($days > 0)
    			return $days;
    		else
    			return 0;
    	}
    
    	/*** get the hours ***/
    	if($type == 'h') {
    		$hours = (intval($seconds) / 3600) % $workinghours;
    		if($hours > 0)
    			return $hours;
    		else
    			return 0;
    	}
    
    	/*** get the minutes ***/
    	if($type == 'm') {
    		$minutes = (intval($seconds) / 60) % 60;
    		if($minutes > 0)
    			return $minutes;
    		else
    			return 0;
    	}
    
    	/*** get the seconds ***/
    	if($type == 's') {
    		$seconds = intval($seconds) % 60;
    		if ($seconds > 0)
    			return $seconds;
    		else
    			return 0;
    	}
    }

	/*
	*update Saved DateTime in TaksInstruct Table
	*
	
    public function updateSaveDatetime($task_id,$instruction_id){
		$totalhrs=0;
		$instruction_data=TaskInstruct::findOne($instruction_id);
		if (Yii::$app->db->driverName == 'mysql') {
					$update_sdt_sql='SELECT (CASE WHEN SUM(tbl_task_instruct_servicetask.est_time) IS NOT NULL  THEN SUM(tbl_task_instruct_servicetask.est_time) ELSE 0 END)
FROM tbl_task_instruct_servicetask
INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id and tbl_task_instruct.isactive=1
WHERE tbl_task_instruct.task_id = '.$task_id.'';
					$totalhrs=Yii::$app->db->createCommand($update_sdt_sql)->queryScalar();
				}else{
					$update_sdt_sql='SELECT (CASE WHEN SUM(tbl_task_instruct_servicetask.est_time) IS NOT NULL  THEN SUM(tbl_task_instruct_servicetask.est_time) ELSE 0 END)
FROM tbl_task_instruct_servicetask
INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id and tbl_task_instruct.isactive=1
WHERE tbl_task_instruct.task_id = '.$task_id.'';
					$totalhrs=Yii::$app->db->createCommand($update_sdt_sql)->queryScalar();
				}
				
		if($totalhrs > 0){
			$totalhrs = round($totalhrs);
			$businesshours = TeamserviceSlaBusinessHours::find()->one();
    		$workinghours = $businesshours->workinghours;
			$start_time = $businesshours->start_time;
			$end_time = $businesshours->end_time;
			$workingdays = json_decode($businesshours->workingdays,true);
			$holidaysRec = TeamserviceSlaHolidays::find()->all();
			$holidayAr = array();
			foreach ($holidaysRec as $hol){
				$holidayAr[] = $hol->holidaydate;
			}
			
			$duedatetime=$instruction_data->task_duedate." ".$instruction_data->task_timedue;
			$enddatetime=date('Y-m-d H:i:s',strtotime($duedatetime));
			$min=date('i',strtotime($enddatetime));
			$slaenddatetime = date("Y-m-d $end_time:00",strtotime($duedatetime));
			while($totalhrs > 0){
				$currentday = date("N",strtotime($enddatetime));
				$currentdateforholiday = date("m/d/Y",strtotime($enddatetime));
				if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
						$time1 = new \DateTime($enddatetime);
						$time2 = new \DateTime(date('Y-m-d',strtotime($enddatetime))." $start_time:00");
						$interval = $time1->diff($time2);
						$diff_hrs= $interval->format('%H');
						if($diff_hrs > 0){
							if($diff_hrs < $totalhrs){
								$totalhrs = $totalhrs - $diff_hrs;
								$enddatetime = date('Y-m-d '.$end_time.':00',strtotime($enddatetime.' -1 days'));
							}else{
								$enddatetime = date('Y-m-d H:i:s',strtotime($enddatetime.' -'.$totalhrs.' hours'));
								$totalhrs = 0;
							}
						}
				}else{
					$enddatetime = date('Y-m-d H:i:s',strtotime($enddatetime.' -1 days'));
				}
			}
			$enddatetime=date('Y-m-d H:i:s',strtotime($enddatetime.' +'.$min.' minutes'));
			if(date("i", strtotime($enddatetime)) > 30){
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
			$instruction_data->start_datetime=$enddatetime;
			$instruction_data->save(false);
		}		
		return;
	}*/
	
    public function fnGetUpdatedDueDate($due_date, $due_time, $totalhours)
	{
		$sign = '-';
		if(strpos($totalhours ,'-') === false){
			$sign = '+';	
		}
		$totalhours = abs($totalhours);
		$duedate = explode('/',$due_date);
		$due_date = $duedate[2]."-".$duedate[0]."-".$duedate[1];
		$current_date = $due_date." ".$due_time.":00";
		
		$businesshours = TeamserviceSlaBusinessHours::find()->one();
		$workinghours = $businesshours->workinghours;
		$start_time = $businesshours->start_time;
		$end_time = $businesshours->end_time;
		$workingdays = json_decode($businesshours->workingdays);
		
		/*if($end_time == '24:00')
			$slaenddatetime = date("Y-m-d 00:00:00",strtotime($current_date).' +1 day');
		else*/
			$slaenddatetime = date("Y-m-d $end_time:00",strtotime($current_date));
		//echo $current_date.' == '.$slaenddatetime;die;
		//if(date("Y-m-d $end_time:00",strtotime($current_date) == strtotime($slaenddatetime))

		$holidaysRec = TeamserviceSlaHolidays::find()->all();
		$holidayAr = array();
		foreach ($holidaysRec as $hol){
			$holidayAr[] = $hol->holidaydate;
		}
		//$i=0;
		if($sign  == '-'){

			$occupiedhours = 0;
			while($totalhours > $occupiedhours) {
			
				$currentday = date("N",strtotime($current_date));
				$currentdateforholiday = date("m/d/Y",strtotime($current_date));
				//echo "<br/>",$current_date," --- ";
				if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
					//echo "<br/>",$current_date," --- ";
					$cArr = explode(" ",$current_date);
					$todayremainhours = date("H:i:00",strtotime($current_date));
					//echo $start_time.' s --- a '.$todayremainhours;
					if(($cArr[1] == '24:00:00' || $todayremainhours=='24:00:00') && $start_time=='00:00')
					{
						$workingHours1 = '24';
						$workingHours2 = '00';
					}
					else 
					{//echo $start_time.' ==== '.$todayremainhours;*/
						$time1 = new \DateTime("$start_time:00");
						$time2 = new \DateTime($todayremainhours);
						$interval = $time1->diff($time2);
						$workingHours1 = $interval->format('%H');
						$workingHours2 = $interval->format('%i');
					}
					//echo $workingHours1;
					if($workingHours2 == "30"){
						$workingHours1 += 0.5;
					}

					if($totalhours <= $workingHours1){
						$remainhoursmins = explode(".",$totalhours);
						if(!empty($remainhoursmins)){
							$remainlasthours = $remainhoursmins[0];
							$lastminutes = $remainhoursmins[1];
							if(isset($remainhoursmins[1]) && $remainhoursmins[1] == 5){
								$remainlastminutes = 30;
							}
						}
						$resthours = $totalhours;
						if($remainlasthours > 0){
							$current_date = date("Y-m-d H:i:00",strtotime($current_date." -$remainlasthours hour "));
						}

						if($remainlastminutes > 0){
							$current_date = date("Y-m-d H:i:00",strtotime($current_date." -$remainlastminutes minutes"));
						}
						$totalhours = $totalhours - $resthours;
					} else {
						$totalhours = $totalhours - $workingHours1;
					}
				}
				
				if($occupiedhours < $totalhours) {
					if($end_time == '24:00'){
						$current_date = date("Y-m-d",strtotime($current_date." -1 days")).' 24:00:00';
					}else{
						$current_date = date("Y-m-d $end_time:00",strtotime($current_date." -1 days"));
					}
				}//echo ' end -- '.$current_date,"</br>";
				//$i++;
			}

			$hours['current_date'] = date('m/d/Y', strtotime($current_date));
			$hours['current_time'] = date('H:i', strtotime($current_date));

		} else {
			
			$occupiedhours = 0;
			$lasthours = 0;
			$lastminutes = 0;
			
			while($totalhours > $occupiedhours){
			
				$currentday = date("N",strtotime($current_date));
				$currentdateforholiday = date("m/d/Y",strtotime($current_date));
				
				if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
					//echo "<br/>",$current_date," --- ";			
					$todayremainhours = date("H:i:00",strtotime($current_date)); 
					if(strtotime($current_date) > strtotime($slaenddatetime)){
						$todayremainhours = date("$start_time:00",strtotime($current_date));
					}

					if($todayremainhours=='00:00:00' && $end_time=='24:00')
					{
						$workingHours1 = '24';
						$workingHours2 = '00';
					} 
					else 
					{
						$time1 = new \DateTime($todayremainhours);
						$time2 = new \DateTime("$end_time:00");
						$interval = $time1->diff($time2);
						$workingHours1 = $interval->format('%H');
						$workingHours2 = $interval->format('%i');
					}
					
					if($workingHours2 == "30"){
						$workingHours1 += 0.5;
					}
				
					if(($occupiedhours+$workingHours1) < $totalhours) {
						$occupiedhours += $workingHours1;
					} else {
						$lasthours = $totalhours - $occupiedhours; 
						$remainhoursmins = explode(".",$lasthours);
						if(!empty($remainhoursmins)){
							$remainlasthours = $remainhoursmins[0];
							$lastminutes = $remainhoursmins[1];
							if(isset($remainhoursmins[1]) && $remainhoursmins[1] == 5){
								$remainlastminutes = 30;
							}
						}
						$occupiedhours += $lasthours;
					} 
				}
				
				if(($lasthours!=$workinghours) && $occupiedhours != $totalhours){
					$current_date = date("Y-m-d $start_time:00",strtotime($current_date." +1 days"));
				}
			}
						
			$seconds = ($occupiedhours * 3600);
			$hours = array();
			$hours['current_date'] = date("m/d/Y",strtotime($current_date));
			$date = date("Y-m-d",strtotime($current_date));
			
			$currentTime = date("H:i",strtotime($current_date));
			
			if($remainlasthours >= 1)
				$currentTime = date("H:i",strtotime($date." ".$currentTime." +$remainlasthours hour"));
				
			if($remainlastminutes > 0)	
				$currentTime = date("H:i",strtotime($date." ".$currentTime." +$remainlastminutes minutes"));
				
			$hours['current_time'] = $currentTime;
		}

		return json_encode($hours);
		die;
	}
    
     public function getTotalTeamTasksIds($teamdata=array()) 
     {
        $task_id = array();
        $criteria = new CDbCriteria;
        $criteria->select = array('id');
        $teamlocationcondition = "";
        foreach($teamdata as $teamId)
        {
            $var = '%{"team_id":"' . $teamId . '"}%';
            //$criteria->addCondition("teamId LIKE '" . $var . "'");
            
            if($teamlocationcondition=="")
                $teamlocationcondition = "(teamId LIKE '" . $var . "')";
            else
                $teamlocationcondition .= " OR (teamId LIKE '" . $var . "')";
        }
        if($teamlocationcondition!="")
            $criteria->addCondition($teamlocationcondition);
            
        $criteria->addCondition('task_status IN(0,1,3,4)');

        $task_info = $this->findAll($criteria);
        foreach ($task_info as $ti) {
            $task_id[$ti->id] = $ti->id;
        }
        return $task_id;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceProductionBates()
    {
        return $this->hasMany(EvidenceProductionBates::className(), ['task_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructNotes()
    {
        return $this->hasMany(TaskInstructNotes::className(), ['task_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamPriority()
    {
        return $this->hasOne(PriorityTeam::className(), ['id' => 'team_priority']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstruct()
    {
        return $this->hasOne(TaskInstruct::className(), ['task_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveTaskInstruct()
    {
    	return $this->hasOne(TaskInstruct::className(), ['task_id' => 'id'])->andOnCondition(['isactive' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }*/

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
    public function getTasksTeams()
    {
        return $this->hasMany(TasksTeams::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnits()
    {
        return $this->hasMany(TasksUnits::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsBillings()
    {
        return $this->hasMany(TasksUnitsBilling::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsDatas()
    {
        return $this->hasMany(TasksUnitsData::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsTodoTransactionLogs()
    {
        return $this->hasMany(TasksUnitsTodoTransactionLog::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsTodos()
    {
        return $this->hasMany(TasksUnitsTodos::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsTransactionLogs()
    {
        return $this->hasMany(TasksUnitsTransactionLog::className(), ['task_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSecurity()
    {
        return $this->hasMany(ProjectSecurity::className(), ['client_case_id' => 'client_case_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by'])->from(['createdUser' => User::tableName()]);
    }
    
    public function imageHelperCase($data, $is_accessible_submodule_tracktask = 0) 
    {        
    	$imghtml = "";
    	//return $data->task_status;
        if($data->task_status == "0") {
            $aTitle = 'Project Not Started';
            $imghtml = '<em title="'.$aTitle.'" class="fa fa-clock-o text-primary"></em>';
        }
        
        if ($data->task_status == "1"){
            $aTitle = 'Project Started';
            $imghtml = '<em title="'.$aTitle.'" class="fa fa-clock-o text-success"></em>';
        }
            
        if ($data->task_status == "3") {
            $aTitle = 'Project On Hold';
            $imghtml = '<em title="'.$aTitle.'" class="fa fa-clock-o text-gray"></em>';
    	}
    	
        if ($data->task_status == "4" && $data->task_closed == "1"){
	    $aTitle = 'Project Completed, Closed';	
            //$imghtml = '<em class="fa fa-clock-o text-dark text-dark-green" title="Project Completed, Closed"></em>';
            $imghtml = '<span tabindex="0" class="icon-stack" title="'.$aTitle.'">
				   <em  title="'.$aTitle.'" class="fa fa-minus-circle icon-stack-2x text-theme-blue text-right"></em>
				   <em  title="'.$aTitle.'" class="fa fa-clock-o icon-stack-1x text-left text-dark"></em>
				</span>';
    	} else if ($data->task_status == "4") {
            $aTitle = 'Project Completed';
            $imghtml = '<em title="'.$aTitle.'" class="fa fa-clock-o text-dark"></em>';
    	}
            
    	if($data->task_cancel == "1"){
            $aTitle = 'Project Canceled';
            $imghtml = '<span tabindex="0" title="'.$aTitle.'" class="fa fa-clock-o text-danger"></span>';
    	}
        $screenSpan = '<span class="screenreader">Status</span>';
        if ($is_accessible_submodule_tracktask == 0)
           $imghtml = $imghtml;
        else
           $imghtml = Html::a($imghtml.$screenSpan, array('track/index', 'taskid' => $data->id, 'case_id' => $data->client_case_id),['title'=>$aTitle, 'data-pjax' => '0']);
        
        $imghtml1 = "";
        $is_pastdue = $data->ispastdue;
		//(new Tasks)->ispastduetask($data->id);
        if ($is_pastdue)
            $imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
        else
            $imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';
        	//$imghtml1 = Html::image(Yii::$app()->theme->baseUrl . "/images/checkmark_pastdue.png", "Past Due Task", array("title" => "Past Due Task"));            
        
        if ($is_accessible_submodule == 0)
        	$imghtml1 = $imghtml1;
        else
        	$imghtml1 = $imghtml1;
        return  $imghtml . $imghtml1;
    }
    
    public function imageHelperTeam($data, $is_accessible_submodule_tracktask = 0,$team_id,$team_loc) 
    {
	
    	$imghtml = "";
        if($data->task_status == "0") {
            $aTitle = 'Project Not Started';
            $imghtml = '<em class="fa fa-clock-o text-primary" title="'.$aTitle.'"></em>';
        }
        
        if ($data->task_status == "1"){
            $aTitle = 'Project Started';            
            $imghtml = '<em class="fa fa-clock-o text-success" title="'.$aTitle.'"></em>';
        }
            
        if ($data->task_status == "3") {
            $aTitle = 'Project On Hold';            
            $imghtml = '<em class="fa fa-clock-o text-gray" title="'.$aTitle.'"></em>';
    	}
    	
        if ($data->task_status == "4" && $data->task_closed == "1"){
            $aTitle = 'Project Completed, Closed';
            //$imghtml = '<em class="fa fa-clock-o text-dark text-dark-green" title="Project Completed, Closed"></em>';
            $imghtml = '<span class="icon-stack" title="'.$aTitle.'">
                        <em class="fa fa-minus-circle icon-stack-2x text-theme-blue text-right"></em>
                        <em class="fa fa-clock-o icon-stack-1x text-left text-dark"></em>
                     </span>';
    	} else if ($data->task_status == "4") {
            $aTitle = 'Project Completed';
            $imghtml = '<em class="fa fa-clock-o text-dark" title="'.$aTitle.'"></em>';
    	}
            
    	if($data->task_cancel == "1"){
            $aTitle = 'Project Canceled';
            $imghtml = '<em class="fa fa-clock-o text-danger" title="'.$aTitle.'"></em>';
    	}
    	
        if ($is_accessible_submodule_tracktask == 0)
           $imghtml = $imghtml;
        else
           $imghtml = Html::a($imghtml, array('track/index', 'taskid' => $data->id, 'team_id' => $team_id,'team_loc'=>$team_loc,'option'=>'Team'),['title'=>$aTitle, 'data-pjax' => '0']);
        
        $imghtml1 = "";
        $is_pastdue = $data->ispastdue;
		//(new Tasks)->ispastduetask($data->id);
        if ($is_pastdue)
            $imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
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
     * IRT 168 
     * @return Team Unit Status
     */
    public function imageHelperTeamStatus($data, $team_id, $team_loc)
    {
    	
    	$sql = 'SELECT tbl_tasks_units.unit_status,tbl_tasks_units.id
		FROM tbl_tasks INNER JOIN tbl_tasks_units ON tbl_tasks.id = tbl_tasks_units.task_id
		WHERE tbl_tasks_units.team_id = '.$team_id.' AND tbl_tasks.id = '.$data->id.' AND tbl_tasks_units.team_loc = '.$team_loc;
		$team_status = ArrayHelper::map(Yii::$app->db->createCommand($sql)->queryAll(),'id','unit_status');
		/*Task Status Login*/
		$task_id=$data->id;
		$sqljoin="SELECT COUNT(*) as cnt,unit_status FROM tbl_tasks_units WHERE tbl_tasks_units.task_id=$task_id AND tbl_tasks_units.team_id = $team_id AND tbl_tasks_units.team_loc = $team_loc AND unit_status IN (1,2,3,4) AND unit_assigned_to!=0
 GROUP BY unit_status";

		$count_completed_task_units_by_task_id = 0;
		//(new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND tbl_tasks_units.team_id = '.$team_id.' AND tbl_tasks_units.team_loc = '.$team_loc.' AND unit_status=4 AND unit_assigned_to!=0')->count();
    	
    	$count_hold_task_units_by_task_id = 0;
		//(new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND tbl_tasks_units.team_id = '.$team_id.' AND tbl_tasks_units.team_loc = '.$team_loc.' AND unit_status=3 AND unit_assigned_to!=0')->count();
    	
    	$count_pause_task_units_by_task_id = 0;
		//(new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND tbl_tasks_units.team_id = '.$team_id.' AND tbl_tasks_units.team_loc = '.$team_loc.' AND unit_status=2 AND unit_assigned_to!=0')->count();
    	
    	$count_started_task_units_by_task_id = 0;
		//(new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND tbl_tasks_units.team_id = '.$team_id.' AND tbl_tasks_units.team_loc = '.$team_loc.' AND unit_status=1 AND unit_assigned_to!=0')->count();
		$data_status=Yii::$app->db->createCommand($sqljoin)->queryAll();
		if(!empty($data_status)){
			foreach($data_status as $ds){
				if($ds['unit_status'] == 4) {
					$count_completed_task_units_by_task_id = $ds['cnt'];
				}if($ds['unit_status'] == 3) {
					$count_hold_task_units_by_task_id = $ds['cnt'];
				}if($ds['unit_status'] == 2) {
					$count_pause_task_units_by_task_id = $ds['cnt'];
				}if($ds['unit_status'] == 1) {
					$count_started_task_units_by_task_id = $ds['cnt'];
				}
			}
		}
		//echo "<pre>",print_r($team_status),"</pre>";die;

    	$task_status = 0;
		$occurences = array_count_values($team_status);
		//echo $data->id,"<pre>",print_r($occurences),print_r($team_status),'hold=>',$count_hold_task_units_by_task_id,'pause',$count_pause_task_units_by_task_id,'started=>',$count_started_task_units_by_task_id,'complete',$count_completed_task_units_by_task_id,"</pre>";die;
		if(count($team_status) != 0 && count($team_status) == $count_completed_task_units_by_task_id){
    		$task_status = 4;
    	} else if(count($team_status) != 0 && count($team_status) == $count_hold_task_units_by_task_id) {
    		$task_status = 3;
    	} else if(count($team_status) != 0 && (($count_pause_task_units_by_task_id > 0) || ($count_started_task_units_by_task_id > 0) || ($count_hold_task_units_by_task_id > 0) || ($count_completed_task_units_by_task_id > 0))){
    		$task_status = 1;
    	} 
		/*END*/






	    if(in_array('1', $team_status))
	    	$imghtml = '<span tabindex="0" class="fa fa-clock-o text-success" title="Team Started"></span>';
		
	    $team_unique = array_unique($team_status); // Array unique
	    $cnt = count($team_unique); // count team unique
	    
	    if(in_array("0",$team_unique) && $cnt ==1 )
		   	$imghtml = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Team Not Started"></span>';
	    else if(in_array("2",$team_unique) && $cnt ==1)
	    	$imghtml = '<span tabindex="0" class="fa fa-clock-o text-info" title="Team On Pause"></span>';
	    else if(in_array("3",$team_unique) && $cnt ==1)
	    	$imghtml = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Team On Hold"></span>';
	    else if(in_array("4",$team_unique) && $cnt ==1)
	    	$imghtml = '<span tabindex="0" class="fa fa-clock-o text-dark" title="Team Completed"></span>';
		//else if(in_array("4",$team_unique) && $cnt >1)
	    //	$imghtml = '<em class="fa fa-clock-o text-success" title="Team Started"></em>';
		else{	
			if($task_status == 4)	{
				$imghtml = '<span tabindex="0" class="fa fa-clock-o text-dark" title="Team Completed"></span>';
			}
			else if($task_status == 3)	{
				$imghtml = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Team On Hold"></span>';
			}
			else if($task_status == 1)	{
				$imghtml = '<span tabindex="0" class="fa fa-clock-o text-success" title="Team Started"></span>';
			}
			else{
				$imghtml = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Team Not Started"></span>';						
			}
		}
	    	
	    return $imghtml;
    }
    
    public function imageHelperTeamsql($data, $is_accessible_submodule_tracktask = 0,$team_id,$team_loc) 
    {
		//echo "<pre>",print_r($data);die;
    	$imghtml = "";
		$aTitle = "";
        if($data['unit_status'] == "0") {
			$aTitle = "Task Not Started";
            $imghtml = '<em class="fa fa-clock-o text-primary" title="Task Not Started"></em>';
        }
        
        if ($data['unit_status'] == "1"){
			$aTitle = "Task Started";
            $imghtml = '<em class="fa fa-clock-o text-success" title="Task Started"></em>';
        }
        
        if ($data['unit_status'] == "2"){
			$aTitle = "Task On Pause";
            $imghtml = '<em class="fa fa-clock-o text-info" title="Task On Pause"></em>';
        }
            
        if ($data['unit_status'] == "3") {
			$aTitle = "Task On Hold";
            $imghtml = '<em class="fa fa-clock-o text-gray" title="Task On Hold"></em>';
    	}
    	
        if ($data['unit_status'] == "4" && $data['task_closed'] == "1"){
			$aTitle = "Task Completed, Closed";
            $imghtml = '<span class="icon-stack" title="Task Completed, Closed">
				   <em class="fa fa-minus-circle icon-stack-2x text-theme-blue text-right"></em>
				   <em class="fa fa-clock-o icon-stack-1x text-left text-dark"></em>
				</span>';
    	} else if ($data['unit_status'] == "4") {
			$aTitle = "Task Completed";
            $imghtml = '<em class="fa fa-clock-o text-dark" title="Task Completed"></em>';
    	}
            
    	if($data['task_cancel'] == "1"){
			$aTitle = "Task Canceled";
            $imghtml = '<em class="fa fa-clock-o text-danger" title="Task Canceled"></em>';
    	}
    	
        if ($is_accessible_submodule_tracktask == 0)
           $imghtml = $imghtml;
        else
           $imghtml = Html::a($imghtml, array('track/index', 'taskid' => $data['task_id'], 'team_id' => $team_id,'team_loc'=>$team_loc,'option'=>'Team'),array('title'=>$aTitle));
        
        $imghtml1 = "";
        $is_pastdue = $data['ispastdue'];
		//(new Tasks)->ispastduetask($data['task_id']);
        if ($is_pastdue)
            $imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
        else
            $imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';
        	//$imghtml1 = Html::image(Yii::$app()->theme->baseUrl . "/images/checkmark_pastdue.png", "Past Due Task", array("title" => "Past Due Task"));
            
        
        if ($is_accessible_submodule == 0)
        	$imghtml1 = $imghtml1;
        else
        	$imghtml1 = $imghtml1;
        return  $imghtml . $imghtml1;
    }
    
    public function imageGlobalProjectHelperCase($data, $is_accessible_submodule_tracktask = 0){
    	//echo "<pre>",print_r($data);
    	$case_id=$data->client_case_id;
    	$task_id=$data->id;
		$role_info = $_SESSION['role'];
    	$role_type = explode(',', $role_info->role_type);
    	$imghtml = "";
    	$title="";
    	$is_close=0;
    	$is_cancel=0;
    	if($data->task_status == "0") {
    		$imghtml = '<em class="fa fa-clock-o text-primary" title="Project Not Started"><span class="not-set">Project Not Started</span></em>';
    		$title="Project Not Started";
    	}
    
    	if ($data->task_status == "1"){
    		$imghtml = '<em class="fa fa-clock-o text-success" title="Project Started"><span class="not-set">Project Started</span></em>';
    		$title="Project Started";
    	}
    
    	if ($data->task_status == "3") {
    		$imghtml = '<em class="fa fa-clock-o text-gray" title="Project On Hold"><span class="not-set">Project On Hold</span></em>';
    		$title="Project On Hold";
    	}
    	 
    	if ($data->task_status == "4" && $data->task_closed == "1"){
    		$is_close=1;
    		//$imghtml = '<em class="fa fa-clock-o text-dark text-dark-green" title="Project Completed, Closed"></em>';
    		$imghtml = '<span class="icon-stack" title="Project Completed, Closed">
				   <em class="fa fa-minus-circle icon-stack-2x text-theme-blue text-right"><span class="not-set">Project Completed</span></em>
				   <em class="fa fa-clock-o icon-stack-1x text-left text-dark"><span class="not-set">Closed</span></em>
				</span>';
    		$title="Project Completed, Closed";
    	} else if ($data->task_status == "4") {
    		$imghtml = '<em class="fa fa-clock-o text-dark" title="Project Completed"><span class="not-set">Project Completed</span></em>';
    		$title="Project Completed";
    	}
    
    	if($data->task_cancel == "1"){
    		$is_cancel=1;
    		$imghtml = '<em class="fa fa-clock-o text-danger" title="Project Canceled"><span class="not-set">Project Canceled</span></em>';
    		$title="Project Canceled";
    	}
    	 
    	/* if ($is_accessible_submodule_tracktask == 0)
    		$imghtml = $imghtml;
    	else
    		$imghtml = Html::a($imghtml, array('case/taskprogress', 'taskid' => $data->id, 'caseId' => $data->client_case_id)); */
    
    	$imghtml1 = "";
    	//echo date('Y-m-d h:i:s');
    	$is_pastdue = $data->ispastdue;
		//$is_pastdue = (new Tasks)->ispastduetask($data->id);

    	//echo $data->id;
    	if ($is_pastdue){
    		$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
    		$title=$title." (Past Due Task)";
    	}else{
    		$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';
    	}
    	if (in_array(1,$role_type) && in_array(2,$role_type)) {
    		if((new User)->checkAccess(4.01)){
				if($is_close==1){
					return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-closed-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
				}else if($is_cancel==1){
					return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-canceled-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
				}else{
					return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/index','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
				}
    		}elseif((new User)->checkAccess(4.081)){//Close 
    			if($is_close==1){
    				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-closed-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
    			}else{
    				return  $imghtml . $imghtml1;
    			}
    		}elseif((new User)->checkAccess(4.0811)){//Closed
    			if($is_cancel==1){
    				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-canceled-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
    			}else{
    				return  $imghtml . $imghtml1;
    			}
    		}else{
    			return  $imghtml . $imghtml1;
    		}
    	} else if (in_array(1, $role_type)) {///client/case manager
    		if((new User)->checkAccess(4.01)){
			 if($is_close==1){
				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-closed-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
			 }else if($is_cancel==1){
				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-canceled-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
			 }else{
				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/index','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
			 }	
    			
    		}elseif((new User)->checkAccess(4.081)){//Close 
    			if($is_close==1){
    				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-closed-projects','case_id'=>$case_id,'task_id'=>$task_id]),'title'=>$title]);
    			}else{
    				return  $imghtml . $imghtml1;
    			}
    		}elseif((new User)->checkAccess(4.0811)){//Closed
    			if($is_cancel==1){
    				return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['case-projects/load-canceled-projects','case_id'=>$case_id,'task_id'=>$task_id,'canceled'=>'canceled']),'title'=>$title]);
    			}else{
    				return  $imghtml . $imghtml1;
    			}
    		}else{
    			return  $imghtml . $imghtml1;
    		}
    	} else if (in_array(2, $role_type)) {///team manager
    		if((new User)->checkAccess(5.01)){
    			$user_id=Yii::$app->user->identity->id;
    			$securitysql="SELECT team_id FROM tbl_project_security WHERE user_id=".$user_id;
    			$securityteamlocsql="SELECT team_loc FROM tbl_project_security WHERE user_id=".$user_id;
    			$task_teams=TasksTeams::find()->where('task_id='.$task_id.' AND team_id IN ('.$securitysql.') AND team_loc IN ('.$securityteamlocsql.')')->one();
    			if(isset($task_teams->team_id) && isset($task_teams->team_loc)){
    				if($is_close==1){
    					return  $imghtml . $imghtml1;
    					//return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['team-projects/index','case_id'=>$case_id,'task_id'=>$task_id,'closed'=>'closed']),'title'=>$title]);
    				}elseif($is_cancel==1){
    					return  $imghtml . $imghtml1;
    					//return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['team-projects/index','case_id'=>$case_id,'task_id'=>$task_id,'canceled'=>'canceled']),'title'=>$title]);
    				}else{
    					return Html::a($imghtml . $imghtml1,null,['href'=>Url::toRoute(['team-projects/index','team_id'=>$task_teams->team_id,'team_loc'=>$task_teams->team_loc,'task_id'=>$task_id]),'title'=>$title]);
    				}
    			}else{
    				return  $imghtml . $imghtml1;
    			}
    		}else{
    			return  $imghtml . $imghtml1;
    		}
    	}
    	return  $imghtml . $imghtml1;
    }
    
    /*public function getTaskPercentageCompleted($task_id, $type, $caseId=0, $teamId=0, $team_loc=0, $returntype=null, $postdata=array()) 
    {
        $percomplete = 0;
        $task_info = Tasks::findOne(['id' => $task_id]);
        $submitted_date = (new Options)->ConvertOneTzToAnotherTz($task_info->created, "UTC", $_SESSION["usrTZ"], "YMDHIS");
        $taskinstruct_info = TaskInstruct::find()->where(['tbl_task_instruct.task_id' => $task_id, 'tbl_task_instruct.isactive' =>'1'])->select(['tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue'])->one();
        $duedatetime = date("Y-m-d H:i:s", strtotime($taskinstruct_info->task_duedate . " " . $taskinstruct_info->task_timedue));
        $dbdatetimeUTC = (new Options)->ConvertOneTzToAnotherTz($duedatetime, 'UTC', $_SESSION['usrTZ'], "YMDHIS");
        $hourdiff = abs((strtotime($dbdatetimeUTC) - strtotime($submitted_date)) / 3600);
        $hourdiff = round($hourdiff);
        $taskinstruct_info = TaskInstruct::find()->joinWith('taskInstructServicetasks')->where(['tbl_task_instruct.task_id' => $task_id, 'tbl_task_instruct.isactive' =>'1'])->select(['tbl_task_instruct.id','tbl_task_instruct_servicetask.sort_order','tbl_task_instruct_servicetask.servicetask_id'])->all();
         
        $est_hours = 0;
       // $services = explode(",", $taskinstruct_info->taskInstructServicetask->sort_order);
     
        $taskinstruct_data = $taskinstruct_info[0]->taskInstructServicetasks;
        $serviceest_data = array();
        if (!empty($taskinstruct_data)) {
            foreach ($taskinstruct_data as $est_data) {
                if (isset($est_data->est_time)) {
                    $est_hours +=$est_data->est_time;
                    $serviceest_data[$est_data->servicetask_id] = $est_data->est_time;
                }
            }
        }
        $main_hours = $hourdiff;*/
      /*  if ($est_hours > 0) {//based on est
            $main_hours = $est_hours;
            $getallserviceTaskUnits = TaskInstructServicetask::find()->joinWith('tasksUnits')->select(['tbl_tasks_units.unit_complete_date','tbl_task_instruct_servicetask.servicetask_id'])->where(['tbl_tasks_units.task_id' => $task_id, 'tbl_tasks_units.unit_status' => 4])->all();
            $unit_hourdiff = 0;
            $est_completed=0;
            if (!empty($getallserviceTaskUnits)) {
                foreach ($getallserviceTaskUnits as $unit_data) {
                	$est_completed++;
                    if (date("Y-m-d", strtotime($unit_data->tasksUnits->unit_complete_date)) != "1970-01-01") {
                        $unit_hourdiff += ($serviceest_data[$unit_data->servicetask_id]);
                    }
                }
            }
            if($est_completed == count($taskinstruct_data)){
            	$percomplete = (($unit_hourdiff / $main_hours) * 100);
            }
            else{
            	$totalcompleted = 0;
            	if (!empty($taskinstruct_data)) {
            		foreach ($taskinstruct_data as $service_data) {
            			if (isset($service_data->servicetask_id) && $service_data->servicetask_id != '') {
            				$getallserviceTaskUnits = TaskInstructServicetask::find()->joinWith('tasksUnits')->select(['tbl_tasks_units.id'])->where(['tbl_tasks_units.task_id' => $task_id, 'tbl_task_instruct_servicetask.servicetask_id' => $service_data->servicetask_id, 'tbl_tasks_units.unit_status' => 4])->all();
            				if (count($getallserviceTaskUnits) > 1) {//media condition
            					$totalcompleted = $totalcompleted + (1 / count($getallserviceTaskUnits));
            				}
            				if (count($getallserviceTaskUnits) == 1) {
            					$totalcompleted++;
            				}
            			}
            		}
            	}
            	$percomplete =((($totalcompleted) / count($taskinstruct_data)) * 100);
            }
        } else { *///based on task 
            /*$totalcompleted = 0;
            if (!empty($taskinstruct_data)) {
                foreach ($taskinstruct_data as $service_data) {
                    if (isset($service_data->servicetask_id) && $service_data->servicetask_id != '') {
                        $getallserviceTaskUnits = TaskInstructServicetask::find()->joinWith('tasksUnits')->select(['tbl_tasks_units.id'])->where(['tbl_task_instruct_servicetask.task_id' => $task_id, 'tbl_task_instruct_servicetask.servicetask_id' => $service_data->servicetask_id, 'tbl_tasks_units.unit_status' => 4])->all();
                        if (count($getallserviceTaskUnits) > 1) {//media condition
                            $totalcompleted = $totalcompleted + (1 / count($getallserviceTaskUnits));
                        }
                        if (count($getallserviceTaskUnits) == 1) {
                            $totalcompleted++;
                        }
                    }
                }
                $percomplete = (($totalcompleted / count($taskinstruct_data)) * 100);
            }
        //}
        $taskprogressurl = "";
        
        if (!empty($postdata)) {
            if ($type == "case")
                $taskprogressurl = '||taskid=' . $task_id . '||case_id=' . $postdata['caseId'];
            if ($type == "team")
                $taskprogressurl = '||taskid=' . $task_id . '||team_id=' . $teamId . '&team_loc=' . $team_loc;

            if (isset($postdata['option']))
                $taskprogressurl.='||option=' . $postdata['option'];
            if (isset($postdata['status']))
                $taskprogressurl.='||status=' . $postdata['status'];
            if (isset($postdata['routefrom']))
                $taskprogressurl.='||routefrom=' . $postdata['routefrom'];
            if (isset($postdata['services']))
                $taskprogressurl.='||services=' . $postdata['services'];
            if (isset($postdata['todofilter']))
                $taskprogressurl.='||todofilter=' . $postdata['todofilter'];
        }
        $qstrAr = explode("r=",$_SERVER['QUERY_STRING']);
        $queryString = $qstrAr[1];
        if ($type == "case" && !empty($postdata))
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString ) . $taskprogressurl;
        else if ($type == "case")
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString);
        else if ($type == "team" && !empty($postdata))
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString) . $taskprogressurl;
        else if ($type == "team")
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString);

        if($percomplete > 0){
        	if($task_info->task_status == 0){
        		$task_model = Tasks::find($task_id)->one();
                $task_model->task_status = 1;
                $task_model->save();
        	}
        }
        if ($returntype == "NUM")
            return round($percomplete)."%";
        else
            return Html::a(round($percomplete)."%", $url, array('title'=>'See % Complete',"class" => "num_a"));
    }*/
	public function getTaskPercentageCompletedMyCaseGird($task_info, $type, $caseId=0, $teamId=0, $team_loc=0, $returntype=null, $postdata=array(),$percomplete=0){ 
		//echo $percomplete;die;
		if($type=='team'){
			$caseId=0;
		}
		$task_id=$task_info->id;
        //$task_info = Tasks::findOne(['id' => $task_id]);
        $taskprogressurl = "";
        if (!empty($postdata)) {
            if ($type == "case")
                $taskprogressurl = '||taskid=' . $task_id . '||case_id=' . $postdata['caseId'];
            if ($type == "team")
                $taskprogressurl = '||taskid=' . $task_id . '||team_id=' . $teamId . '&team_loc=' . $team_loc;

            if (isset($postdata['option']))
                $taskprogressurl.='||option=' . $postdata['option'];
            if (isset($postdata['status']))
                $taskprogressurl.='||status=' . $postdata['status'];
            if (isset($postdata['routefrom']))
                $taskprogressurl.='||routefrom=' . $postdata['routefrom'];
            if (isset($postdata['services']))
                $taskprogressurl.='||services=' . $postdata['services'];
            if (isset($postdata['todofilter']))
                $taskprogressurl.='||todofilter=' . $postdata['todofilter'];
        }
        $qstrAr = explode("r=",$_SERVER['QUERY_STRING']);
        $queryString = $qstrAr[1];
        if ($type == "case" && !empty($postdata))
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString ) . $taskprogressurl;
        else if ($type == "case")
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString);
        else if ($type == "team" && !empty($postdata))
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString) . $taskprogressurl;
        else if ($type == "team")
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString);

        if($percomplete > 0){
        	if($task_info->task_status == 0){
				Tasks::updateAll(['task_status' => 1], 'id = '.$task_id);
        		/*$task_model = Tasks::find($task_id)->one();
                $task_model->task_status = 1;
                $task_model->save();*/
        	}
        }
        if ($returntype == "NUM")
            return round($percomplete)."%";
        else
            return Html::a(round($percomplete)."%", $url, array('title'=>'See % Complete',"class" => "num_a",'aria-label'=>'See % Complete'));
    
		//

	}
	
    public function getTaskPercentageCompleted($task_id, $type, $caseId=0, $teamId=0, $team_loc=0, $returntype=null, $postdata=array(),$percomplete=0,$task_status=null) 
    {
		if($percomplete == 0){
			$percomplete_sql = (new Tasks)->getTaskPercentageCompleteByTaskid($task_id);
			$percomplete=Yii::$app->db->createCommand($percomplete_sql)->queryScalar();
		}
		if ($returntype == "NUM") {
            return round($percomplete)."%";
		}
		//$percomplete = 0;
		if($type=='team'){
			$caseId=0;
		}
		//$percomplete =  \Yii::$app->db->createCommand("SELECT getTaskPercentageComplete ($task_id, '{$type}', $caseId, $teamId, $team_loc)")->queryScalar();
		
        /*$submitted_date = (new Options)->ConvertOneTzToAnotherTz($task_info->created, "UTC", $_SESSION["usrTZ"], "YMDHIS");
        $taskinstruct_info = TaskInstruct::find()->where(['tbl_task_instruct.task_id' => $task_id, 'tbl_task_instruct.isactive' =>'1'])->select(['tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue'])->one();
        $duedatetime = date("Y-m-d H:i:s", strtotime($taskinstruct_info->task_duedate . " " . $taskinstruct_info->task_timedue));
        $dbdatetimeUTC = (new Options)->ConvertOneTzToAnotherTz($duedatetime, 'UTC', $_SESSION['usrTZ'], "YMDHIS");
        $hourdiff = abs((strtotime($dbdatetimeUTC) - strtotime($submitted_date)) / 3600);
        $hourdiff = round($hourdiff);
        $taskinstruct_info = TaskInstruct::find()->joinWith('taskInstructServicetasks')->where(['tbl_task_instruct.task_id' => $task_id, 'tbl_task_instruct.isactive' =>'1'])->select(['tbl_task_instruct.id','tbl_task_instruct_servicetask.sort_order','tbl_task_instruct_servicetask.servicetask_id'])->all();
         
        $est_hours = 0;
       // $services = explode(",", $taskinstruct_info->taskInstructServicetask->sort_order);
     
        $taskinstruct_data = $taskinstruct_info[0]->taskInstructServicetasks;
        $serviceest_data = array();
        if (!empty($taskinstruct_data)) {
            foreach ($taskinstruct_data as $est_data) {
                if (isset($est_data->est_time)) {
                    $est_hours +=$est_data->est_time;
                    $serviceest_data[$est_data->servicetask_id] = $est_data->est_time;
                }
            }
        }
        $main_hours = $hourdiff;
		$totalcompleted = 0;
		if (!empty($taskinstruct_data)) {
			foreach ($taskinstruct_data as $service_data) {
				if (isset($service_data->servicetask_id) && $service_data->servicetask_id != '') {
					$getallserviceTaskUnits = TaskInstructServicetask::find()->joinWith('tasksUnits')->select(['tbl_tasks_units.id'])->where(['tbl_task_instruct_servicetask.task_id' => $task_id, 'tbl_task_instruct_servicetask.servicetask_id' => $service_data->servicetask_id, 'tbl_tasks_units.unit_status' => 4])->all();
					if (count($getallserviceTaskUnits) > 1) {//media condition
						$totalcompleted = $totalcompleted + (1 / count($getallserviceTaskUnits));
					}
					if (count($getallserviceTaskUnits) == 1) {
						$totalcompleted++;
					}
				}
			}
			$percomplete = (($totalcompleted / count($taskinstruct_data)) * 100);
		}
        */
        $taskprogressurl = "";
        
        if (!empty($postdata)) {
            if ($type == "case")
                $taskprogressurl = '||taskid=' . $task_id . '||case_id=' . $postdata['caseId'];
            if ($type == "team")
                $taskprogressurl = '||taskid=' . $task_id . '||team_id=' . $teamId . '&team_loc=' . $team_loc;

            if (isset($postdata['option']))
                $taskprogressurl.='||option=' . $postdata['option'];
            if (isset($postdata['status']))
                $taskprogressurl.='||status=' . $postdata['status'];
            if (isset($postdata['routefrom']))
                $taskprogressurl.='||routefrom=' . $postdata['routefrom'];
            if (isset($postdata['services']))
                $taskprogressurl.='||services=' . $postdata['services'];
            if (isset($postdata['todofilter']))
                $taskprogressurl.='||todofilter=' . $postdata['todofilter'];
        }
        $qstrAr = explode("r=",$_SERVER['QUERY_STRING']);
        $queryString = $qstrAr[1];
        if ($type == "case" && !empty($postdata))
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString ) . $taskprogressurl;
        else if ($type == "case")
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString);
        else if ($type == "team" && !empty($postdata))
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString) . $taskprogressurl;
        else if ($type == "team")
            $url = "index.php?r=track/est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString);

        if($percomplete > 0){
			if(!isset($task_status)){
				$task_info = Tasks::findOne(['id' => $task_id]);
				$task_status=$task_info->task_status;
			}
        	if($task_status == 0){
				Tasks::updateAll(['task_status'=>1],'id='.$task_id);
        		/*$task_model = Tasks::find($task_id)->one();
                $task_model->task_status = 1;
                $task_model->save();*/

        	}
        }
		
        return Html::a(round($percomplete)."%", $url, array('title'=>'See % Complete',"class" => "num_a",'aria-label'=>'See % Complete'));
    }

 	public function getTeamPercentageCompleted($task_id, $type, $caseId=0, $teamId=0, $team_loc=0, $returntype=null, $postdata=array(),$percomplete=0,$task_status=null)
    {
		if ($returntype == "NUM") {
           return round($percomplete)."%";
		}
        
    	if($type=='team') {
			$caseId=0;
		}
		
        
       
        $taskprogressurl = "";
        
        if (!empty($postdata)) {
            if ($type == "case")
                $taskprogressurl = '||taskid=' . $task_id . '||case_id=' . $postdata['caseId'];
            if ($type == "team")
                $taskprogressurl = '||taskid=' . $task_id . '||team_id=' . $teamId . '&team_loc=' . $team_loc;

            if (isset($postdata['option']))
                $taskprogressurl.='||option=' . $postdata['option'];
            if (isset($postdata['status']))
                $taskprogressurl.='||status=' . $postdata['status'];
            if (isset($postdata['routefrom']))
                $taskprogressurl.='||routefrom=' . $postdata['routefrom'];
            if (isset($postdata['services']))
                $taskprogressurl.='||services=' . $postdata['services'];
            if (isset($postdata['todofilter']))
                $taskprogressurl.='||todofilter=' . $postdata['todofilter'];
        }
        $qstrAr = explode("r=",$_SERVER['QUERY_STRING']);
        $queryString = $qstrAr[1];
        if ($type == "case" && !empty($postdata))
            $url = "index.php?r=track/team-est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString ) . $taskprogressurl;
        else if ($type == "case")
            $url = "index.php?r=track/team-est-report&task_id=" . $task_id . "&case_id=" . $caseId . "&querystr=" . str_replace("&", "||", $queryString);
        else if ($type == "team" && !empty($postdata))
            $url = "index.php?r=track/team-est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString) . $taskprogressurl;
        else if ($type == "team")
            $url = "index.php?r=track/team-est-report&task_id=" . $task_id . "&team_id=" . $teamId . "&team_loc=" . $team_loc . "&querystr=" . str_replace("&", "||", $queryString);

        if($percomplete > 0){
			if($task_status=="") {
			$task_info = Tasks::findOne(['id' => $task_id]);
			$task_status=$task_info->task_status;
			}
        	if($task_status == 0){
				 Tasks::updateAll(['task_status'=>1],'id='.$task_id);
        		/*$task_model = Tasks::find($task_id)->one();
                $task_model->task_status = 1;
                $task_model->save();*/

        	}
        }
            return Html::a(round($percomplete)."%", $url, array('title'=>'See % Complete',"class" => "num_a",'aria-label'=>'See % Complete'));
    }

    public function getTaskPercentageCompleteByTaskid($task_id){
		return $sql="(SELECT CAST((SUM(CASE WHEN unit_status = 4 THEN 1  ELSE 0 END)/CAST(COUNT(tbl_tasks_units.id) as DECIMAL(8,2))*100) as DECIMAL(8,2)) FROM tbl_tasks_units INNER JOIN tbl_task_instruct ON tbl_task_instruct.id=tbl_tasks_units.task_instruct_id WHERE tbl_task_instruct.isactive=1 AND tbl_tasks_units.task_id=$task_id)";	
	}

	public function getTeamTaskPercentageCompleteByTaskid($task_id, $team_id, $team_loc)
	{
		return $sql="(SELECT CAST((SUM(CASE WHEN unit_status = 4 THEN 1  ELSE 0 END)/CAST(COUNT(tbl_tasks_units.id) as DECIMAL(8,2))*100) as DECIMAL(8,2)) FROM tbl_tasks_units INNER JOIN tbl_task_instruct ON tbl_task_instruct.id=tbl_tasks_units.task_instruct_id WHERE tbl_task_instruct.isactive=1 AND tbl_tasks_units.task_id=$task_id AND tbl_tasks_units.team_id=$team_id AND tbl_tasks_units.team_loc=$team_loc)";	
	}

    /* get task due date and time according to user's timezone settings */
    public function getTaskDuedate($model)
    {
		//return '<label tabindex="0">'..'</label>';
        //return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model->activeTaskInstruct->task_duedate . " " . $model->activeTaskInstruct->task_timedue, 'UTC', $_SESSION['usrTZ']).'</label>';
		return '<label tabindex="0">'.$model->activeTaskInstruct->task_date_time.'</label>';
    }

	public function getTaskDuedateNew($model)
    {
		//return '<label tabindex="0">'..'</label>';
        //return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model->activeTaskInstruct->task_duedate . " " . $model->activeTaskInstruct->task_timedue, 'UTC', $_SESSION['usrTZ']).'</label>';
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$task_date_time = Yii::$app->db->createCommand("SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}','".$model->task_duedate."','".$model->task_duetime."','%m/%d/%Y %h:%i %p')")->queryScalar();
			//$data_query24 = Yii::$app->db->createCommand("SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}','".$model->task_duedate."','".$model->task_duetime."','%m/%d/%Y %H:%i')")->queryScalar();
		} else {
			$task_date_time = Yii::$app->db->createCommand("SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}','".$model->task_duedate."','".$model->task_duetime."','%m/%d/%Y %h:%i %p')")->queryScalar();
			//$data_query24 = Yii::$app->db->createCommand("SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}','".parent::__get('task_duedate')."','".parent::__get('task_timedue')."','%m/%d/%Y %H:%I')")->queryScalar();
		}

		return '<label tabindex="0">'.$task_date_time.'</label>';
    }

	public function getTaskDuedateByTaskid($task_id)
    {
		//return '<label tabindex="0">'..'</label>';
        //return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model->activeTaskInstruct->task_duedate . " " . $model->activeTaskInstruct->task_timedue, 'UTC', $_SESSION['usrTZ']).'</label>';
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$tast_duedate = Yii::$app->db->createCommand("(SELECT task_duedate FROM tbl_task_instruct where task_id=$task_id and isactive=1 limit 1)")->queryScalar();
			$task_duetime = Yii::$app->db->createCommand("(SELECT task_timedue FROM tbl_task_instruct where task_id=$task_id and isactive=1 limit 1)")->queryScalar();
			$task_date_time = Yii::$app->db->createCommand("SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}','".$tast_duedate."','".$task_duetime."','%m/%d/%Y %h:%i %p')")->queryScalar();
			//$data_query24 = Yii::$app->db->createCommand("SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}','".$model->task_duedate."','".$model->task_duetime."','%m/%d/%Y %H:%i')")->queryScalar();
		} else {
			$tast_duedate = Yii::$app->db->createCommand("(SELECT TOP 1 task_duedate FROM tbl_task_instruct where task_id=$task_id and isactive=1)")->queryScalar();
			$task_duetime = Yii::$app->db->createCommand("(SELECT TOP 1 task_timedue FROM tbl_task_instruct where task_id=$task_id and isactive=1)")->queryScalar();
			$sql_duedate="(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}','".$tast_duedate."','".$task_duetime."','%m/%d/%Y %h:%i %p'))";
			$task_date_time = Yii::$app->db->createCommand($sql_duedate)->queryScalar();
			//$task_date_time = Yii::$app->db->createCommand("SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}','".$tast_duedate."','".$task_duetime."','%m/%d/%Y %h:%i %p')")->queryScalar();
			//$data_query24 = Yii::$app->db->createCommand("SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}','".parent::__get('task_duedate')."','".parent::__get('task_timedue')."','%m/%d/%Y %H:%I')")->queryScalar();
		}

		return '<label tabindex="0">'.$task_date_time.'</label>';
    }
    
    public function getTaskDuedatesql($model)
    {
		if(strpos($model['task_date_time'],"-")!==false)
			return '<label tabindex="0">'.date('m/d/Y h:i A',strtotime($model['task_date_time'])).'</label>';
		else
			return '<label tabindex="0">'.$model['task_date_time'].'</label>';
		//return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model['task_duedate'] . " " . $model['task_timedue'], 'UTC', $_SESSION['usrTZ']).'</label>';
    }

	public function getTaskDuedateobj($model)
    {
		if(strpos($model->task_date_time,"-")!==false)
			return '<span tabindex="0" id="dd_'.$model->id.'">'.date('m/d/Y h:i A',strtotime($model->task_date_time)).'</span>';
		else
			return '<span tabindex="0" id="dd_'.$model->id.'">'.$model->task_date_time.'</span>';
		//return '<label tabindex="0">'.(new Options)->ConvertOneTzToAnotherTz($model['task_duedate'] . " " . $model['task_timedue'], 'UTC', $_SESSION['usrTZ']).'</label>';
    }
    
    /* get read and unread comments */
    public function findReadUnreadComment($task_id, $case_id, $has_access_408=0) 
    {
        $user_id = Yii::$app->user->identity->id;
        static $securityteam_ids = array();
        if (empty($securityteam_ids)) {
        	$securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
        	if (empty($securityteam_ids))
            	$securityteam_ids = array(0);
        }
        
        $roleId = Yii::$app->user->identity->role_id;
        $role_types = explode(",", $roleId);// $userInfo->role_id);
                              
        $data = Comments::find()->select(['tbl_comments.Id','tbl_comments.created_by', 'tbl_comments.comment_origination','tbl_comments_read.user_id', 'tbl_comment_teams.team_id','tbl_comment_roles.role_id'])->joinWith(['commentsRead', 'commentRoles', 'commentTeams'])->where(['task_id' => $task_id])->orderby(['Id' => 'desc'])->all();
        
        $is_read = "N";
        $comment_type = 0;
        $comment_id = 0;
        $has_access = false;
       
        if (!empty($data)) {
            foreach ($data as $comments) 
            {
                $has_access = false;
                if ($comments->created_by == $user_id) 
                {
                    $has_access = true;
                } 
                else 
                {
                     if (!empty($comments->commentTeams))
                            {
                                foreach($comments->commentTeams as $val)
                                {
                            
                                    if (isset($val->team_id) && $val->team_id != "") 
                                    {
                                        if (in_array($val->team_id, $securityteam_ids)) 
                                        {
                                            $has_access = true;
                                            break;
                                        }
                                    }
                                 }
                              }      

                            if (!empty($comments->commentRoles))
                            {
                                foreach($comments->commentRoles as $val)
                                {
                                    if (isset($val->role_id) && $val->role_id != "") 
                                    {
                                        $roles = Role::find()->select(['id'])->andWhere("id = " . $val->role_id . " AND id > 0")->one();
                                        if(isset($roles->id))
                                        {
                                            if (in_array($roles->id, $role_types)) 
                                            {
                                                $has_access = true;
                                                break;
                                            }
                                        }  
                                    }
                                }
                            }
                }
                if($has_access) 
                {
                    if($user_id == $comments->created_by) 
                    { 
                        $is_read = "Y";
                    } 
                    else 
                    { 
                        if(!empty($comments->commentsRead))
                        {
                            foreach($comments->commentsRead as $readcmt) 
                            {
                                $already_readuser = $readcmt->user_id;
                                
                                if($user_id==$already_readuser) 
                                {
                                    $is_read = "Y";
                                    $comment_type = $comments->comment_origination;
                                    $comment_id = $comments->Id;
                                    break;
                                } 
                                else 
                                {
                                    $is_read = "N";
                                    $comment_type = $comments->comment_origination;
                                    $comment_id = $comments->Id;
                                    
                                }
                            }
                        } 
                        else 
                        {
                            $is_read = "N";
                            $comment_type = $comments->comment_origination;
                            $comment_id = $comments->Id;
                        }
                    } 
                }
                else
                {
                    continue;
                }
            }
        } 
        else 
        {
            return "";
        }
        $session = new Session;
        $session->open();
       
        if($has_access_408==0){
        	$has_access_408=(new User)->checkAccess(4.08);
        }
        if ($has_access_408) {/* 73 */
            $link = "javascript:readComments($task_id,$case_id,$comment_type,$comment_id);";
        } else {
            $link = "javascript:void(0);";
        }
       
        if ($is_read == "N") {
            $comment_link ='<a href="'.$link.'" title="Unread Comment"><em class="fa fa-comments" title="Unread Comment"></em></a>';
            return $comment_link;
        } else {
            $comment_link ='<a href="'.$link.'" title="Read Comment"><em class="fa fa-comments" title="Read Comment"></em></a>';
            return $comment_link;
        }
    }
    
    
    /* get read and unread comments For Team-Project */
    public function findReadUnreadCommentTeam($task_id, $team_id, $team_loc) 
    {
        $user_id = Yii::$app->user->identity->id;
        static $securityteam_ids = array();
        if (empty($securityteam_ids)) {
        	$securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
        	if (empty($securityteam_ids))
            	$securityteam_ids = array(0);
        }
        
        $roleId = Yii::$app->user->identity->role_id;
        $role_types = explode(",", $roleId); // $userInfo->role_id);
                              
        /*$data = Comments::find()->select(['tbl_comments.Id','tbl_comments.created_by', 'tbl_comments.comment_origination','tbl_comments_read.user_id', 'tbl_comment_teams.team_id','tbl_comment_roles.role_id'])
        ->joinWith(['commentsRead', 'commentRoles', 'commentTeams'])
        ->where(['task_id' => $task_id])
        ->orderby(['Id' => 'desc'])->all();
        
        $is_read = "N";
        $comment_type = 0;
        $comment_id = 0;
        $has_access = false;

        if (!empty($data)) 
        {
            foreach ($data as $comments) 
            { 
                if($user_id == $comments->created_by) 
                { 
                    $is_read = "Y";
                } 
                else 
                { 
                    if(!empty($comments->commentsRead))
                    { 
                        foreach($comments->commentsRead as $readcmt) 
                        { 
                            $already_readuser = $readcmt->user_id;
                            if($user_id==$already_readuser) 
                            {
                                $is_read = "Y";
                                $comment_type = $comments->comment_origination;
                                $comment_id = $comments->Id;
                                break;
                            } 
                            else 
                            {
                                $is_read = "N";
                                $comment_type = $comments->comment_origination;
                                $comment_id = $comments->Id;
                            }
                        }
                      //  if($is_read=='Y') break;
                    } 
                    else 
                    {
                        $is_read = "N";
                        $comment_type = $comments->comment_origination;
                        $comment_id = $comments->Id;
                    }
                } 
            }
        } 
        else 
        {
            return "";
        }*/
        $sql = "SELECT * FROM tbl_comments 
            LEFT JOIN tbl_comment_teams ON tbl_comment_teams.comment_id = tbl_comments.Id
            LEFT JOIN tbl_comment_roles ON tbl_comment_roles.comment_id = tbl_comments.Id
            WHERE tbl_comments.id NOT IN (SELECT comment_id FROM tbl_comments_read WHERE tbl_comments_read.user_id = ".$user_id.") 
            AND tbl_comments.task_id = ".$task_id." AND tbl_comments.created_by != ".$user_id." 
            AND (tbl_comment_teams.team_id = ".$team_id." OR tbl_comment_roles.role_id = ".$roleId.")";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();

        $session = new Session;
        $session->open();

        $comment_type = 0;
        $comment_id = 0;
        $is_read = 'Y';
        if(count($data) > 0)
            $is_read = 'N';

        /* 73 */
        $link = "javascript:readComments($task_id,$team_id,$team_loc,$comment_type,$comment_id);";
       
        if ($is_read == "N") {
            $comment_link ='<a href="'.$link.'" title="Unread Comment"><em class="fa fa-comments" title="Unread Comment"></em></a>';
            return $comment_link;
        } else {
            $comment_link ='<a href="'.$link.'" title="Read Comment"><em class="fa fa-comments text-muted" title="Read Comment"></em></a>';
            return $comment_link;
        }
    }
    
    
    /* check if task is past due or not */
    public function ispastduetask($taskId) 
    {
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
    	if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s'))";
		}

        $sql="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks ON tbl_tasks.id = task_id
		INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tbl_task_instruct.id = A.id
    	WHERE tbl_task_instruct.task_id = :task_id
    	AND isactive=1";
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tbl_tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sql.=" AND A.task_date_time < CASE WHEN tbl_tasks.task_complete_date!='0000-00-00 00:00:00' AND tbl_tasks.task_complete_date!='' AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tbl_tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sql.=" AND A.task_date_time < CASE WHEN CAST(tbl_tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tbl_tasks.task_complete_date as varchar) IS NOT NULL AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }
    	$data =  \Yii::$app->db->createCommand($sql, [ ':task_id' => $taskId])->queryAll();
		
        if ($data[0]['totalitems'] > 0)
            return true;
        else
            return false;
    }
    /* get link for task instruction for particular task */
    public function getTaskInstruction($taskId, $task_status = 0) {
        //$task_instruct_data = TaskInstruct::find()->select(['tbl_task_instruct.id'])->where(['tbl_task_instruct.task_id' => $taskId, 'tbl_task_instruct.isactive' => 1])->orderBy(['tbl_task_instruct.id' => 'DESC'])->one();
        return Html::a($taskId, "javascript:viewInstruction($taskId);", array("class" => "dialog",'aria-label'=>'View Instruction Detail for task #'.$taskId,"title"=>"Project #".$taskId)) . "<input type='hidden' name='task_status_{$taskId}' class='task_status_{$taskId}' value='{$task_status}'> ";
    }
    /* get count for unassigned projects */
    public function getCaseProjectsUnassign($case_id, $is_accessible_submodule = 1) {
        
        /*$projectunassign_data = Tasks::find()->select(['tbl_tasks.id'])->where("tbl_tasks.client_case_id = $case_id AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0")
        ->innerJoinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) { $query->where(["tbl_task_instruct.isactive" => 1])->innerJoinWith(['tasksUnits' => function (\yii\db\ActiveQuery $query) { $query->where("(tbl_tasks_units.unit_assigned_to IS NULL OR tbl_tasks_units.unit_assigned_to =0)"); }]); }],false)
        ->all();*/
    	$sql=" SELECT count(*) as unassing_total FROM (
				SELECT tbl_tasks.id FROM tbl_tasks 
				INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id AND tbl_task_instruct.isactive=1
				INNER JOIN tbl_tasks_units ON tbl_task_instruct.id =tbl_tasks_units.task_instruct_id  
				WHERE tbl_tasks.client_case_id = $case_id AND tbl_task_instruct.task_id NOT IN (
					SELECT task.id FROM tbl_tasks as task
					INNER JOIN tbl_task_instruct as ti ON ti.task_id = task.id AND ti.isactive=1
					INNER JOIN tbl_tasks_units as tu ON ti.id = tu.task_instruct_id
					WHERE tbl_tasks.client_case_id=$case_id AND task.task_status IN (0,1,3) AND task.task_closed = 0 AND task.task_cancel = 0 AND ti.isactive = 1 AND unit_assigned_to!=0 AND unit_assigned_to!='' AND unit_assigned_to IS NOT NULL
					GROUP BY task.id)  AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_task_instruct.isactive = 1
					GROUP BY tbl_tasks.id) AS A"; 
		/*100% unassigned*/
    	//WHERE A.unassing_total = A.total_unit 
    	$data =  \Yii::$app->db->createCommand($sql)->queryAll();
    	
    	//echo "<pre>",print_r($data),"</pre>";die;
    	$header=0;
        if(!empty($data)){
        	if ($is_accessible_submodule == 0 || !(new User)->checkAccess(4.01)) {
        		$header = $data[0]['unassing_total'];//count($projectunassign_data);
	        } else {
	        	$header = Html::a($data[0]['unassing_total'], "index.php?r=case-projects/index&case_id=" . $case_id . "&unassignproject=unassignproject", ["data-pjax" => 0, "title" => $data[0]['unassing_total']." UnAssigned Projects"]); //;
	        }
        }
        
        return $header;
    }
    /* get count for case active Todos */
    public function getCaseTodos($caseId) {
        $casetodos = 0;
		$sql="SELECT DISTINCT(tbl_tasks.id) as id, tbl_tasks_units.task_id 
		FROM tbl_tasks_units_todos 
		INNER JOIN tbl_tasks_units ON tbl_tasks_units_todos.tasks_unit_id = tbl_tasks_units.id 
		INNER JOIN tbl_tasks ON tbl_tasks_units.task_id = tbl_tasks.id
		WHERE (tbl_tasks_units_todos.complete=0) AND 
		(tbl_tasks.client_case_id = $caseId AND tbl_tasks.task_closed = 0 
		AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_cancel = 0)";
        $project_list = Yii::$app->db->createCommand($sql)->queryAll();

		$todotaskid=0;
        if (count($project_list) > 0) {
            foreach ($project_list as $key => $value) {
                $todotaskid.=','.$value['task_id'];
			}
        }

        return Html::a(count($project_list), "index.php?r=case-projects/index&case_id=" . $caseId . "&todotaskids=" . $todotaskid, ["data-pjax" => 0, "title" => count($project_list)." Active Todos"]);
    }
    /* get count for unread comments */
    public function getUnreadComments($caseId, $output = '') {
        $task_ids = '';
        $taskdata = array();
        $user_id = Yii::$app->user->identity->id;
        /*$task_data = Tasks::find()->select(['tbl_tasks.id'])
		  ->innerJoinWith(['comments' => function (\yii\db\ActiveQuery $query) { $query->select(['tbl_comments.task_id','tbl_comments.Id','tbl_comments.created_by', 'tbl_comments.comment_origination']); }])
		  ->innerJoinWith(['clientCase' => function (\yii\db\ActiveQuery $query) { $query->where(['tbl_client_case.is_close' => 0]); }])
		  ->where("tbl_tasks.client_case_id = :caseId AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0", [':caseId' => $caseId])
		  ->orderBy("tbl_comments.id desc")->all();*/
		 $sql="SELECT tbl_comments.task_id, (select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = tbl_comments.Id and tbl_comments_read.user_id=$user_id) as readcount 
		 FROM tbl_comments 
		 INNER JOIN tbl_tasks ON tbl_comments.task_id = tbl_tasks.id 
		 INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id
		 WHERE tbl_tasks.client_case_id IN ($caseId) AND tbl_tasks.task_closed = 0 
		 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close=0";
		 $comment_data = Yii::$app->db->createCommand($sql)->queryAll();
		 //echo "<pre>",print_r($comment_data),"</pre>";die;
		 /*Comments::find()->select(['tbl_comments.I','tbl_comments.comment','tbl_comments.task_id','tbl_comments.created_by','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname', 'tbl_comments.comment_origination','(select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = tbl_comments.Id and tbl_comments_read.user_id='.$user_id.') as readcount'])
                ->innerJoinWith(['tasks' => function (\yii\db\ActiveQuery $query) { $query->innerJoinWith(['clientCase','createdUser'],false);}],false)
                ->where("tbl_tasks.client_case_id IN (:caseId) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close=0", [':caseId'=>$caseId])
                ->all();*/
        $notread = 0;
		if(!empty($comment_data)) {
			foreach($comment_data as $comments) {
				if($comments['readcount'] == 0) {
					$taskdata[$comments['task_id']]=$comments['task_id'];
				}
			}
		}
        $j = 0;
       
        /*if (!empty($task_data)) {
        	
        	
        	if (empty($securityteam_ids)) {
                $securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
                if (empty($securityteam_ids))
                    $securityteam_ids = array(0);
            }
            
            $roleId = Yii::$app->user->identity->role_id;
            $role_types = explode(",", $roleId);
            
            $data = $task_data;
			 
           //   $commentdata = array();
                $comment_type = 0;
                $comment_id = 0;
                $has_access = false;
                if (!empty($data)) {
                    foreach ($data as $cmt) {
                    	$comments1 = $cmt->comments;
                        foreach($comments1 as $comments)
                        {
                           
                        $is_read = "N";
                        $has_access = false;
                            
                        if ($comments->created_by == $user_id) 
                        {
                            $has_access = true;
                        } 
                        else 
                        {
                            if (!empty($comments->commentTeams))
                            {
                                foreach($comments->commentTeams as $val)
                                {
                            
                                    if (isset($val->team_id) && $val->team_id != "") 
                                    {
                                        if (in_array($val->team_id, $securityteam_ids)) 
                                        {
                                            $has_access = true;
                                            break;
                                        }
                                    }
                                 }
                              }      

                            if (!empty($comments->commentRoles))
                            {
                                foreach($comments->commentRoles as $val)
                                {
                                    if (isset($val->role_id) && $val->role_id != "") 
                                    {
                                        $roles = Role::find()->select(['id'])->andWhere("id = " . $val->role_id . " AND id > 0")->one();
                                        if(isset($roles->id))
                                        {
                                            if (in_array($roles->id, $role_types)) 
                                            {
                                                $has_access = true;
                                                break;
                                            }
                                        }  
                                    }
                                }
                            }
                        }
                        
                        if ($has_access) {
                        	if($user_id == $comments->created_by) { 
                                	$is_read = "Y";
                            } else { 
                                if (!empty($comments->commentsRead))
                                {
                                    foreach($comments->commentsRead as $val)
                                    {
                                        if ($val->user_id != "") {
                                            $already_readuser = $val->user_id;
                                            
                                                if($user_id==$already_readuser) {
                                                    $is_read = "Y";
                                                    break;
                                                } else {
                                                    $is_read = "N";
                                                }
                                            
                                        } 
                                    }
                                } else {
                                        $is_read = "N";
                                }
                            }
                          
                            $tid = $comments->task_id;
                            if ($is_read == "N")
                            {
                               $j = $j + 1;
                               $taskdata[$tid]=$tid;
                               
                              // $commentdata[$comments->Id] = $comments->Id;
                            }
                            
                        }
                      }
                    }
                }
            
        }
        else {
            $j = 0;
        }*/
       // echo "<Pre>";print_r($commentdata);
        $count = count($taskdata);
        
        if($output == "task_ids")
            return $taskdata;
            
        $html = "";
     
		$term = 'comment_search'; $ismagnified = 'unread_cnt';
		$unreadComment = (new ClientCase)->getCaseSearchResults($term, $caseId, $ismagnified); 
		
        $has_access_408=(new User)->checkAccess(4.08);
        
        if ($has_access_408 && (new User)->checkAccess(4.01)){
            $html .= Html::a($unreadComment, "index.php?r=case-projects/index&case_id=" . $caseId . "&comment=comment", ["data-pjax" => 0, "title" => $unreadComment." Unread Comments"]);
        } else {
            $html .=$count;
        }

        if ($has_access_408 && (new User)->checkAccess(4.01)) {
            $html .= Html::a('<em class="fa fa-search" style="color:grey" title="Search Comments"></em><span class="screenreader">Comments</span>', "javascript:void(0);", ["onclick" => "showsearchcomment('" . $caseId . "','".implode(',',$taskdata)."', this)", "title" => "Search Comments", "id" => "searchcomment_" . $caseId]);
        }
        $html .= "</div></div>";
        return $html;
        
    }
    /*Get Teamwise Project Priority data */
    public function getTeamProjectPriority($team_id,$team_loc,$user_id){
		
		$team_location_id = explode(',',$team_loc);
		$team_location = array_slice($team_location_id,1);
		$i= 0;
		//foreach($team_location as $location => $value){
		//	$team_loc=$value;
			/*$query = "SELECT tbl_task_instruct.task_priority, count(Distinct tbl_tasks.id) as cnttasks, tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order FROM tbl_tasks INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id AND (isactive=1) INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id INNER JOIN tbl_task_instruct_servicetask as service ON tbl_task_instruct.id = service.task_instruct_id LEFT JOIN tbl_project_security ON tbl_tasks.client_case_id = tbl_project_security.client_case_id AND ((tbl_project_security.user_id = $user_id) AND (tbl_project_security.team_id = $team_id AND tbl_project_security.team_loc = $team_loc)) WHERE ((service.team_id = $team_id AND service.team_loc = $team_loc)) AND (((task_status IN (0,1,3) and task_closed=0 and task_cancel=0)) )  GROUP BY tbl_task_instruct.task_priority,tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order";	*/
			 /*$query = "SELECT tbl_task_instruct.task_priority, count(Distinct tbl_tasks.id) as cnttasks, tbl_priority_project.id, tbl_priority_project.priority,tbl_priority_project.priority_order 
			 FROM tbl_tasks
			 INNER JOIN tbl_tasks_units as service ON tbl_tasks.id = service.task_id
			 INNER JOIN tbl_task_instruct ON service.task_id = tbl_task_instruct.task_id
			 INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id
			 INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id
			 WHERE (tbl_task_instruct.isactive=1) AND tbl_client_case.is_close=0  AND service.team_id = $team_id AND service.team_loc IN ($team_loc) AND task_status IN (0,1,3) and task_closed=0 and task_cancel=0 AND task_status != 4 GROUP BY tbl_task_instruct.task_priority,tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order,service.team_loc";
			 echo $query;die;*/

			 $query = "SELECT tbl_task_instruct.task_priority, COUNT(DISTINCT t.id) as cnttasks,
			 tbl_priority_project.id, tbl_priority_project.priority,tbl_priority_project.priority_order
			 FROM tbl_tasks t 
			 INNER JOIN tbl_tasks_teams as service ON t.id = service.task_id 
			 INNER JOIN tbl_client_case as t2 ON t.client_case_id = t2.id and t2.is_close = 0 
			 INNER JOIN tbl_task_instruct on tbl_task_instruct.task_id=t.id
			 INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id
			 WHERE service.team_id = $team_id 
			 AND service.team_loc IN ($team_loc) 
			 AND task_status IN (0, 1, 3) 
			 AND t.task_closed = 0 
			 AND task_cancel = 0 
			 AND task_status != 4 
			 AND (tbl_task_instruct.isactive=1) GROUP BY tbl_task_instruct.task_priority,tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order,service.team_loc";
			 //$params = array(':user_id'=>$user_id,':team_id'=>$team_id,':team_loc'=>$value);
			 //$priority[$location] = Yii::$app->db->createCommand($query)->queryAll();
			 $priority_data = Yii::$app->db->createCommand($query)->queryAll();
		//}
		//echo "<prE>",print_r($priority);
		/*$i=0;
		foreach($priority as $pr => $value){
			foreach($priority[$pr] as $d => $q){
				$priority_data[$i] = $priority[$pr][$d];
				$i++;
			}
		}*/
		//echo "<prE>",print_r($priority);
		return $priority_data;
		
	}
	
	/*Get TeamLocationwise Project Priority data*/
	public function getTeamLocProjectPriority($team_id,$team_loc,$user_id){
			/*$query = "SELECT tbl_task_instruct.task_priority, count(Distinct tbl_tasks.id) as cnttasks, tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order FROM tbl_tasks INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id AND (isactive=1) INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id INNER JOIN tbl_task_instruct_servicetask as service ON tbl_task_instruct.id = service.task_instruct_id LEFT JOIN tbl_project_security ON tbl_tasks.client_case_id = tbl_project_security.client_case_id AND ((tbl_project_security.user_id = $user_id) AND (tbl_project_security.team_id = $team_id AND tbl_project_security.team_loc = $team_loc)) WHERE ((service.team_id = $team_id AND service.team_loc = $team_loc)) AND (((task_status IN (0,1,3) and task_closed=0 and task_cancel=0)) )  GROUP BY tbl_task_instruct.task_priority,tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order";	
			$params = array(':user_id'=>$user_id,':team_id'=>$team_id,':team_loc'=>$team_loc);
			echo $query;die;*/
			
			/*$query ="SELECT DISTINCT t.id,t1.task_priority,count(Distinct t1.id) as cnttasks,tbl_priority_project.id,tbl_priority_project.priority,tbl_priority_project.priority_order
			FROM tbl_tasks t INNER JOIN tbl_task_instruct as t1 ON t.id = t1.task_id 
			INNER JOIN tbl_priority_project ON t1 .task_priority = tbl_priority_project.id 
			INNER JOIN tbl_task_instruct_servicetask as service ON t1.id = service.task_instruct_id 
			INNER JOIN tbl_client_case as t2 ON t.client_case_id = t2.id and t2.is_close=0 
			WHERE service.team_id = $team_id AND service.team_loc =$team_loc AND task_status IN (0,1,3) AND t.task_closed = 0 AND task_cancel = 0 AND t1.isactive='1' AND task_status != 4 GROUP BY t1.task_priority ORDER BY tbl_priority_project.priority ASC";



			if(Yii::$app->db->driverName!="mysql"){
				$query="SELECT DISTINCT t.id,
t1.task_priority,
count(Distinct t.id) as cnttasks,
tbl_priority_project.id,
tbl_priority_project.priority,
tbl_priority_project.priority_order
			FROM tbl_tasks t INNER JOIN tbl_task_instruct as t1 ON t.id = t1.task_id 
			INNER JOIN tbl_priority_project ON t1 .task_priority = tbl_priority_project.id 
			INNER JOIN tbl_task_instruct_servicetask as service ON t1.id = service.task_instruct_id 
			INNER JOIN tbl_client_case as t2 ON t.client_case_id = t2.id and t2.is_close=0 
			WHERE service.team_id = $team_id AND service.team_loc =$team_loc AND t.task_status IN (0,1,3) AND t.task_closed = 0
 AND task_cancel = 0 AND t1.isactive='1' AND task_status != 4 
 GROUP BY t1.task_priority,t.id,tbl_priority_project.id,tbl_priority_project.priority,tbl_priority_project.priority_order
 ORDER BY tbl_priority_project
.priority ASC";
			}*/
			$query = "SELECT tbl_task_instruct.task_priority, COUNT(DISTINCT t.id) as cnttasks,
			 tbl_priority_project.id, tbl_priority_project.priority,tbl_priority_project.priority_order
			 FROM tbl_tasks t 
			 INNER JOIN tbl_tasks_teams as service ON t.id = service.task_id 
			 INNER JOIN tbl_client_case as t2 ON t.client_case_id = t2.id and t2.is_close = 0 
			 INNER JOIN tbl_task_instruct on tbl_task_instruct.task_id=t.id
			 INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id
			 WHERE service.team_id = $team_id 
			 AND service.team_loc IN ($team_loc) 
			 AND task_status IN (0, 1, 3) 
			 AND t.task_closed = 0 
			 AND task_cancel = 0 
			 AND task_status != 4 
			 AND (tbl_task_instruct.isactive=1) GROUP BY tbl_task_instruct.task_priority,tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order,service.team_loc";
			//echo $query;die;
			$priority_data = Yii::$app->db->createCommand($query)->queryAll();
			return $priority_data;
	}

	/* Get past due teamlocationwise tasks by project status */
    public function getPastDueTeamLocTasksByStatusGroup($team_id,$team_loc, $taskStatus = '', $task_type="pastdue") { 
        
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

		$query = (new Tasks)->find()->select('COUNT(Distinct tbl_tasks.id) as cnttasks, tbl_tasks.task_status')
		->joinWith(['tasksTeams' => function(\yii\db\Activequery $query) use($team_id,$team_loc){
                $query->where('tbl_tasks_teams.team_id = '.$team_id.' AND tbl_tasks_teams.team_loc='.$team_loc);
            }],false)
		->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($team_id,$team_loc,$data_query) {
			$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id'); 
			$query->joinWith(['taskPriority'],false)->where('tbl_task_instruct.isactive = 1');
            
			/*->innerJoinWith(['taskInstructServicetasksWithoutOrder' => function(\yii\db\Activequery $query) use ($team_id,$team_loc) { 
				/* $query->where('tbl_task_instruct_servicetask.team_id = '.$team_id.' AND tbl_task_instruct_servicetask.team_loc='.$team_loc);*/
			/*}],false);*/
		}],false)
		->innerJoinWith(['clientCase'=>function(\yii\db\Activequery $query){ 
			$query->where('tbl_client_case.is_close = 0'); 
		}],false)
		->where('task_closed = 0 AND task_cancel = 0 AND tbl_tasks.task_status != 4');
                                      
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
		if($task_type == "pastdue")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time < '$todaysdate'");
			else
				$query->andWhere("A.task_date_time < '$todaysdate'");
		}
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time > '$todaysdate'");
			else
				$query->andWhere("A.task_date_time > '$todaysdate'");
		}
		$query->groupBy('tbl_tasks.task_status');
	    //$query->distinct();
		//$task_count = $query->count();
		$task_count = $query->all();
		
		return $task_count;
    }


    
    /* Get past due teamlocationwise tasks by project status */
    public function getPastDueTeamLocTasksByStatus($team_id,$team_loc, $taskStatus = '', $task_type="pastdue") { 
        
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

		$query = (new Tasks)->find()->select('tbl_tasks.id')
		->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($team_id,$team_loc,$data_query) {
			$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id'); 
			$query->joinWith('taskPriority')->where('tbl_task_instruct.isactive = 1')
            ->joinWith(['taskTeams' => function(\yii\db\Activequery $query) use($team_id,$team_loc){
                $query->where('tbl_tasks_teams.team_id = '.$team_id.' AND tbl_tasks_teams.team_loc='.$team_loc);
            }])
			->innerJoinWith(['taskInstructServicetasksWithoutOrder' => function(\yii\db\Activequery $query) use ($team_id,$team_loc) { 
				/* $query->where('tbl_task_instruct_servicetask.team_id = '.$team_id.' AND tbl_task_instruct_servicetask.team_loc='.$team_loc);*/
			}]);
		}])
		->innerJoinWith(['clientCase'=>function(\yii\db\Activequery $query){ 
			$query->where('tbl_client_case.is_close = 0'); 
		}])
		->where('tbl_tasks.task_status = :taskStatus AND task_closed = 0 AND task_cancel = 0 AND tbl_tasks.task_status != 4', [':taskStatus' => $taskStatus]);
                                      
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
		if($task_type == "pastdue")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time < '$todaysdate'");
			else
				$query->andWhere("A.task_date_time < '$todaysdate'");
		}
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time > '$todaysdate'");
			else
				$query->andWhere("A.task_date_time > '$todaysdate'");
		}

		/*if($task_type == "pastdue")
		{ 
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) < "' . date('Y-m-d H:i:s', time()) . '"');
			else
				$query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) <'" . date('Y-m-d H:i:s', time()) . "'");
		}   
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) > "' . date('Y-m-d H:i:s', time()) . '"');
			else
				$query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) >'" . date('Y-m-d H:i:s', time()) . "'");
		}*/
		$task_count = $query->distinct()->count();
		
		return $task_count;
    }
    
     /* Get past due Teamwise tasks by project status */
    public function getPastDueTeamTasksByStatusGroup($team_id, $taskStatus = '',$team_loc_ids='', $task_type="pastdue") {

		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

        $task_count = 0;
        $team_location = explode(',',$team_loc_ids);
		if(isset($team_loc_ids)){
        //foreach($team_location as $location => $value){
            $query = (new Tasks)->find()->select('COUNT(DISTINCT  tbl_tasks.id) as cnttasks, tbl_tasks.task_status')
			->joinWith(['tasksTeams' => function (\yii\db\ActiveQuery $query) use ($team_id,$team_loc_ids) {
				$query->where('tbl_tasks_teams.team_id='. $team_id .' AND tbl_tasks_teams.team_loc IN ('.$team_loc_ids.')');
			}],false)
			->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($team_id,$team_loc_ids,$data_query) {
				$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id'); 
				$query->joinWith(['taskPriority'],false)->where('tbl_task_instruct.isactive = 1');
				//->innerJoinWith(['taskInstructServicetasksWithoutOrder'=>function(\yii\db\Activequery $query) use ($team_id,$team_loc_ids){ 
					//$query->where('tbl_task_instruct_servicetask.team_id = '.$team_id.' AND tbl_task_instruct_servicetask.team_loc IN ('.$team_loc_ids.')');
				//}]);
			}],false)
			->innerJoinWith(['clientCase'=>function(\yii\db\Activequery $query){ 
				$query->where('tbl_client_case.is_close = 0'); 
			}],false)
            ->where('task_closed = 0 AND task_cancel = 0 AND tbl_tasks.task_status != 4');

			$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
			if($task_type == "pastdue")
			{
				if (Yii::$app->db->driverName == 'mysql')
					$query->andWhere("A.task_date_time < '$todaysdate'");
				else
					$query->andWhere("A.task_date_time < '$todaysdate'");
			}
			if($task_type == "active")
			{
				if (Yii::$app->db->driverName == 'mysql')
					$query->andWhere("A.task_date_time > '$todaysdate'");
				else
					$query->andWhere("A.task_date_time > '$todaysdate'");
			}
			$query->groupBy('tbl_tasks.task_status,tbl_tasks_teams.team_loc');
			//$query->distinct();
			$task_count= $query->all();
            //$task_count+= $query->distinct()->count();
		//}
		}
           
		return $task_count;
    }

    /* Get past due Teamwise tasks by project status */
    public function getPastDueTeamTasksByStatus($team_id, $taskStatus = '',$team_loc_ids=0, $task_type="pastdue") {

		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

        $task_count = 0;
        $team_location = explode(',',$team_loc_ids);
        foreach($team_location as $location => $value){
            $query = (new Tasks)->find()->select('tbl_tasks.id')
			->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($team_id,$value,$data_query) {
				$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id'); 
				$query->joinWith('taskPriority')
				->where('tbl_task_instruct.isactive = 1')
				->innerJoinWith(['taskInstructServicetasksWithoutOrder'=>function(\yii\db\Activequery $query) use ($team_id,$value){ 
					$query->where('tbl_task_instruct_servicetask.team_id = '.$team_id.' AND tbl_task_instruct_servicetask.team_loc = '.$value);
				}]);
			}])
			->innerJoinWith(['clientCase'=>function(\yii\db\Activequery $query){ 
				$query->where('tbl_client_case.is_close = 0'); 
			}])
            ->where('tbl_tasks.task_status = :taskStatus AND task_closed = 0 AND task_cancel = 0 AND tbl_tasks.task_status != 4',[':taskStatus' => $taskStatus]);

			$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
			if($task_type == "pastdue")
			{
				if (Yii::$app->db->driverName == 'mysql')
					$query->andWhere("A.task_date_time < '$todaysdate'");
				else
					$query->andWhere("A.task_date_time < '$todaysdate'");
			}
			if($task_type == "active")
			{
				if (Yii::$app->db->driverName == 'mysql')
					$query->andWhere("A.task_date_time > '$todaysdate'");
				else
					$query->andWhere("A.task_date_time > '$todaysdate'");
			}

            /*if($task_type == "pastdue")
            { 
                if (Yii::$app->db->driverName == 'mysql')
                    $query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) < "' . date('Y-m-d H:i:s', time()) . '"');
                else
                    $query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) <'" . date('Y-m-d H:i:s', time()) . "'");
            }   
            if($task_type == "active")
            {
                if (Yii::$app->db->driverName == 'mysql')
                    $query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) > "' . date('Y-m-d H:i:s', time()) . '"');
                else
                    $query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) >'" . date('Y-m-d H:i:s', time()) . "'");
            }*/
            $task_count+= $query->distinct()->count();
		}
           
		return $task_count;
    }
    
    
    
    /* Get past due casewise tasks by project status */
    public function getPastDueCaseTasksByStatus($caseId, $taskStatus = '', $task_type="pastdue") { 
       
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}   
		$query = (new Tasks)->find()->select('tbl_tasks.id')
		->joinWith([
			'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($caseId, $data_query) { 
				$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');
				$query->joinWith(['taskPriority'],false)->where('tbl_task_instruct.isactive = 1');
			}
		],false)
		->where('tbl_tasks.client_case_id = :caseId AND tbl_tasks.task_status = :taskStatus AND task_closed = 0 AND task_cancel = 0', [':caseId' => $caseId, ':taskStatus' => $taskStatus]);
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
		if($task_type == "pastdue")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time < '$todaysdate'");
			else
				$query->andWhere("A.task_date_time < '$todaysdate'");
		}
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time > '$todaysdate'");
			else
				$query->andWhere("A.task_date_time > '$todaysdate'");
		}
		$task_count = $query->count();
           
        return $task_count;
    }
	public function getPastDueCaseTasksByStatusGroup($caseId, $taskStatus = '', $task_type="pastdue") { 
       
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}   
		$query = (new Tasks)->find()->select('count(tbl_tasks.id) as cnttasks,tbl_tasks.task_status')
		->joinWith([
			'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($caseId, $data_query) { 
				$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');
				$query->joinWith(['taskPriority'],false)->where('tbl_task_instruct.isactive = 1');
			}
		],false)
		->where('tbl_tasks.client_case_id = :caseId AND task_closed = 0 AND task_cancel = 0', [':caseId' => $caseId]);
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
		if($task_type == "pastdue")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time < '$todaysdate'");
			else
				$query->andWhere("A.task_date_time < '$todaysdate'");
		}
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql')
				$query->andWhere("A.task_date_time > '$todaysdate'");
			else
				$query->andWhere("A.task_date_time > '$todaysdate'");
		}
		$query->groupBy('tbl_tasks.task_status');
		$task_count = $query->all();
           
        return $task_count;
    }
    /* Get past due clientwise tasks by project status */
    public function getPastDueClientTasksByStatus($client_id, $taskStatus = '', $case_ids=0, $task_type="pastdue") { 
        
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}   

		$query = (new Tasks)->find()->select('tbl_tasks.id')
		->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($client_id,$data_query) { 
			$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');
			$query->joinWith(['taskPriority'],false)->where('tbl_task_instruct.isactive = 1');
		}],false)
		->joinWith(['clientCase' => function (\yii\db\ActiveQuery $query) use ($client_id) { 
			$query->where('tbl_client_case.client_id = :client_id', [':client_id' => $client_id]);
		}])
		->where('tbl_tasks.client_case_id IN ('.$case_ids.') AND task_closed = 0 AND task_cancel = 0');


		if($taskStatus!=4)
			$query->andWhere("tbl_tasks.task_status = :taskStatus AND tbl_tasks.task_status!=4",[':taskStatus'=>$taskStatus]);
		else
			$query->andWhere(["tbl_tasks.task_status" => $taskStatus]);
		
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");

		if($task_type == "pastdue")
		{ 
			if (Yii::$app->db->driverName == 'mysql'){
				//$query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) < "' . date('Y-m-d H:i:s', time()) . '"');
				$query->andWhere("A.task_date_time < '$todaysdate'");
			}else{
				//$query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) <'" . date('Y-m-d H:i:s', time()) . "'");
				$query->andWhere("A.task_date_time < '$todaysdate'");
			}
		}   
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql'){
				//$query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) > "' . date('Y-m-d H:i:s', time()) . '"');
				$query->andWhere("A.task_date_time > '$todaysdate'");
			}else{
				//$query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) >'" . date('Y-m-d H:i:s', time()) . "'");
				$query->andWhere("A.task_date_time > '$todaysdate'");
			}
		}
		$task_count = $query->count();
           
        return $task_count;
    }

	/* Get past due clientwise tasks by project status */
    public function getPastDueClientTasksByStatusGroup($client_id, $taskStatus = '', $case_ids=0, $task_type="pastdue") { 
        
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}   

		$query = (new Tasks)->find()->select('COUNT(tbl_tasks.id) as cnttasks,tbl_tasks.task_status')
		->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($client_id,$data_query) { 
			$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');
			$query->joinWith(['taskPriority'],false)->where('tbl_task_instruct.isactive = 1');
		}],false)
		->joinWith(['clientCase' => function (\yii\db\ActiveQuery $query) use ($client_id) { 
			$query->where('tbl_client_case.client_id = :client_id', [':client_id' => $client_id]);
		}])
		->where('tbl_tasks.client_case_id IN ('.$case_ids.') AND task_closed = 0 AND task_cancel = 0');


		//if($taskStatus!=4)
			$query->andWhere("tbl_tasks.task_status!=4");
		//else
		//	$query->andWhere(["tbl_tasks.task_status" => $taskStatus]);
		
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");

		if($task_type == "pastdue")
		{ 
			if (Yii::$app->db->driverName == 'mysql'){
				//$query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) < "' . date('Y-m-d H:i:s', time()) . '"');
				$query->andWhere("A.task_date_time < '$todaysdate'");
			}else{
				//$query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) <'" . date('Y-m-d H:i:s', time()) . "'");
				$query->andWhere("A.task_date_time < '$todaysdate'");
			}
		}   
		if($task_type == "active")
		{
			if (Yii::$app->db->driverName == 'mysql'){
				//$query->andWhere('CONCAT(tbl_task_instruct.task_duedate," ", STR_TO_DATE(tbl_task_instruct.task_timedue, "%h:%i %p" )) > "' . date('Y-m-d H:i:s', time()) . '"');
				$query->andWhere("A.task_date_time > '$todaysdate'");
			}else{
				//$query->andWhere("CAST(CAST(tbl_task_instruct.task_duedate as varchar)+ ' ' +tbl_task_instruct.task_timedue as datetime) >'" . date('Y-m-d H:i:s', time()) . "'");
				$query->andWhere("A.task_date_time > '$todaysdate'");
			}
		}
		$query->groupBy('tbl_tasks.task_status');
		$task_count = $query->all();
           
        return $task_count;
    }

    /*Change Main Project Status based on instruction or taskunit*/
    public function setProjectTasksStatus($task_id,$tasksunitid=0){
    	$updatetasksstatus = 0;
    	$hasMultipleInstruction = TaskInstruct::find()->where('task_id='.$task_id)->count();
    	if($hasMultipleInstruction > 1 && $tasksunitid==0){
    		$isInstructionChange = (new TaskInstruct)->checkInstructionChange($task_id);
    		if($isInstructionChange){
    			$changeServiceids= (new TaskInstruct)->getChangeServices($task_id);
    			if(!empty($changeServiceids)){
    				$instrcution_service_ids=array();
    				foreach ($changeServiceids as $Serviceids){
    					$instrcution_service_ids[$Serviceids['instruct_servicetask_id']]=$Serviceids['instruct_servicetask_id'];
    				}
    				if(!empty($instrcution_service_ids)){
    						
    					$countchangeServiceUnit = TasksUnits::find()->where("task_id=$task_id AND task_instruct_servicetask_id IN (".implode(',',$instrcution_service_ids).") AND unit_status=4")->select('id')->count();
    					if($countchangeServiceUnit){
    						TasksUnits::updateAll(['unit_status' => 2],"task_id=$task_id AND task_instruct_servicetask_id IN (".implode(',',$instrcution_service_ids).") AND unit_status=4");
    						$updatetasksstatus = 1;
    					}
    				}
    			}
    		}
    	}
    	$service_data = (new TaskInstructServicetask)->getServicesByTaksid($task_id);
    	$count_completed_task_units_by_task_id = (new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND unit_status=4 AND unit_assigned_to!=0')->count();
    	
    	$count_hold_task_units_by_task_id = (new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND unit_status=3 AND unit_assigned_to!=0')->count();
    	
    	$count_pause_task_units_by_task_id = (new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND unit_status=2 AND unit_assigned_to!=0')->count();
    	
    	$count_started_task_units_by_task_id = (new TasksUnits)->find()->where('tbl_tasks_units.task_id='.$task_id.' AND unit_status=1 AND unit_assigned_to!=0')->count();
    	//echo "updatetasksstatus=>",$updatetasksstatus,"service_data count",count($service_data);
		//echo "<br>count_completed_task_units_by_task_id=>",$count_completed_task_units_by_task_id;
		//echo "<br>count_hold_task_units_by_task_id=>",$count_hold_task_units_by_task_id;
		//echo "<br>count_pause_task_units_by_task_id=>",$count_pause_task_units_by_task_id;
		//echo "<br>count_started_task_units_by_task_id=>",$count_started_task_units_by_task_id;
    	if(count($service_data) != 0 && count($service_data) == $count_completed_task_units_by_task_id){
    		if($updatetasksstatus > 0) {
    			Tasks::updateAll(['task_status' => 1], 'id = '.$task_id);
    		} else {
				$bill_date= date('Y-m-d H:i:s');
				//$bill_date = (new Options)->ConvertOneTzToAnotherTz($bill_date, $_SESSION['usrTZ'], 'UTC', "YMDHIS");
    			Tasks::updateAll(['task_status' => 4, 'task_complete_date' => $bill_date], 'id = '.$task_id);
    		}
    	} else if(count($service_data) != 0 && count($service_data) == $count_hold_task_units_by_task_id) {
    		Tasks::updateAll(['task_status' => 3],'id = '.$task_id);
    	} else if(count($service_data) != 0 && ( ($count_pause_task_units_by_task_id > 0) || ($count_started_task_units_by_task_id > 0) || ($count_hold_task_units_by_task_id > 0) || ($count_completed_task_units_by_task_id > 0) ) ){
    		Tasks::updateAll(['task_status' => 1],'id = '.$task_id);
    	} else {
    		Tasks::updateAll(['task_status' => 0],'id = '.$task_id);
    	}
    }
    /* Return The Startdate and Enddate for the Report */
    
    public function calculatedate($datedropdown,$start_date1,$end_date1){
				if (isset($datedropdown) && $datedropdown == 1) {
					$start_date = date("Y-m-d");
					$end_date = date("Y-m-d");
				} else if (isset($datedropdown) && $datedropdown == 2) {
					$yesterday = strtotime("-1 day");
					$start_date = date('Y-m-d', $yesterday);
					$end_date = date("Y-m-d");
				} else if (isset($datedropdown) && $datedropdown == 3) {
					$week = strtotime("-7 day");
					$start_date = date('Y-m-d', $week);
					$end_date = date("Y-m-d");
				} else if (isset($datedropdown) && $datedropdown == 4) {
					$month = strtotime("-1 month");
					$start_date = date('Y-m-d', $month);
					$end_date = date("Y-m-d");
				} else if (isset($datedropdown) && $datedropdown == 5) {
					$year = strtotime("-1 year");
					$start_date = date('Y-m-d', $year);
					$end_date = date("Y-m-d");
				}else {
					$start_date = $start_date1;
					$end_date = $end_date1;
				}
			$data['start_date'] = $start_date;
			$data['end_date'] = $end_date;
			return $data;	
	}
	
	/* Return the Current date upon the Groupcreterial on graph.*/
	/* Return the Total days to complete the todo */
	public function getTotalSLADaysTodoFollowupwithtodo($task_id, $teamservice_sla, $todo_status,$serviceTaskId = 0, $todoId=0){
			$sqlservice = " AND st.teamservice_id IN ($teamservice_sla)";
    	
    	if($serviceTaskId != 0)
    		$sqlservice .= " AND service.servicetask_id = $serviceTaskId";	
        
        $days = 0;
        $slaservicetasks = array();
        $start_date = "";
        $end_date = "";
		
        $task_unit_todo_id = 0;
	
		$command = "SELECT ul.id, ul.task_id, ul.tasks_unit_id, ul.todo_id, ul.transaction_type, ul.transaction_date 
		FROM tbl_tasks_units_todos AS tu 
		INNER JOIN tbl_tasks_units_todo_transaction_log as ul ON ul.todo_id = tu.id
		INNER JOIN tbl_tasks_units as unit ON unit.id = tu.tasks_unit_id
		INNER JOIN tbl_task_instruct_servicetask as service ON service.id = unit.task_instruct_servicetask_id
		INNER JOIN tbl_todo_cats AS tc ON tc.id = tu.todo_cat_id 
		INNER JOIN tbl_servicetask as st ON st.id = service.servicetask_id ".$sqlservice."
		INNER JOIN tbl_tasks as task ON task.id = tu.task_id
		WHERE tu.task_id={$task_id} AND task.task_closed=0 AND assigned!=0 AND ul.transaction_type IN (7,9,13) AND todo_id = ".$todoId." 
		ORDER BY ul.transaction_date ASC";
		
		$task_unitdata = \Yii::$app->db->createCommand($command)->queryAll();
		
		$tasksunits_ar = array();
		if(count($task_unitdata) > 0){
			$i=0;
			$j=0;
			$transtype = "";
            foreach ($task_unitdata as $data) {
            	if($transtype != $data['transaction_type'])
            		$transtype = $data['transaction_type'];
            	else 
            		continue;
            
            	if($task_unit_todo_id != 0 && $task_unit_todo_id != $data['todo_id']){
            		$i=0;$j=0;
            	}
            	$task_unit_todo_id=$data['todo_id'];	

            	if($data['transaction_type'] == 7 || $data['transaction_type'] == 13){
            		$tasksunits_ar[$data['tasks_unit_id']][$data['todo_id']][$i]['start_date'] = $data['transaction_date'];
            	} else { 
            		$tasksunits_ar[$data['tasks_unit_id']][$data['todo_id']][$i]['end_date'] = $data['transaction_date'];
            	}
            	$j++;	
            	if($j%2==0){
            		$i++;
            	}
			}
		}
		if(!empty($tasksunits_ar)){
	        foreach ($tasksunits_ar as $unitstodos) {
	        	
	        	foreach($unitstodos as $todo){   
	    			$datesAr = array();
	        		foreach($todo as $key => $dates){
	        			
			        	$start_date = $dates['start_date'];
			        	$end_date = "";
			        	if(isset($dates['end_date']))
			            	$end_date = $dates['end_date'];
			            	
			            $curUTCTime = "";
						if($start_date != "")
			            	$curUTCTime = (new Options)->ConvertOneTzToAnotherTz($start_date, 'UTC', $_SESSION['usrTZ'], "YMD");
			            
			            $curUTCendTime ="";
			            if($end_date != "")
			            	$curUTCendTime = (new Options)->ConvertOneTzToAnotherTz($end_date, 'UTC', $_SESSION['usrTZ'], "YMD");
			            
			            if(!in_array($curUTCTime,$datesAr)){	
				            if($curUTCendTime != "" && $curUTCTime == $curUTCendTime){
				            	if($days == 0)
				            		$days = 1;
				            	else 
				            	 	$days += 1;
				            } else {
				            
								$totaldays = $this->getSlaWorkingDayswithinDateRange($start_date,$end_date);
								  
				            	if($key > 0){
				     
			            			if($end_date == "")
			            				$totaldays = $totaldays - 1;
			            				
			            			if($days == 0)
				            			$days = $totaldays;
					            	else 
					            	 	$days += $totaldays;
				       
				            	} else {	
				            		if($days == 0)
				            			$days = $totaldays;
				            		else
				            			$days += $totaldays;
				            	}
				            }
			            }
			            $datesAr[$curUTCTime] =$curUTCTime;
	        		}
	        	}
	        }
		}
		return $days;
	}
	
	
	 public function getSlaWorkingDayswithinDateRange($startdate,$enddate){
    	
    	date_default_timezone_set($_SESSION['usrTZ']);
    	$current_date = date("Y-m-d",strtotime($startdate));
    	$adjusted_date = date("Y-m-d",strtotime($enddate));
    	if($enddate == ""){
    		$adjusted_date = date("Y-m-d");
    	}
		
    	$businesshours = TeamserviceSlaBusinessHours::find()->asArray()->one();
		$workinghours = $businesshours['workinghours'];
		$start_time = $businesshours['start_time'];
		$end_time = $businesshours['end_time'];
		$workingdays = json_decode($businesshours['workingdays']);

		$holidaysRec = TeamserviceSlaHolidays::find()->asArray()->all();
		$holidayAr = array();
		foreach ($holidaysRec as $hol){
			$holidayAr[] = $hol['holidaydate'];
		}
		//echo $current_date." => ".$adjusted_date."<br/>";
		$occupiedhours = 0;
		$days = 1;
		$lasthours = 0;
		if(strtotime($current_date) != strtotime($adjusted_date)){
			while(strtotime($current_date) <= strtotime($adjusted_date)){
				$currentday = date("N",strtotime($current_date));
				$currentdateforholiday = date("m-d-Y",strtotime($current_date));
				if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
					$todayremainhours = date("H:i:s",strtotime($current_date));
					$time1 = new \DateTime($todayremainhours);
					$time2 = new \DateTime("$end_time:00");
					$interval = $time1->diff($time2);
					$workingHours = $interval->format('%H');
					$occupiedhours += $workingHours;
					$days += 1;
				}
				//echo "occhours : ",$occupiedhours," - Totalhours : ",$totalhours."<br/>";
				$current_date = date("Y-m-d $start_time:00",strtotime($current_date." +1 days"));
			}
			$seconds = ($occupiedhours * 3600);
			//echo $seconds.' = '.$workinghours;echo "<Br>";
			$days = (new Tasks)->secondsToWords($seconds,'d',$workinghours);
		}
		date_default_timezone_set('UTC');
		return $days;
    }
    
	/*  Return the TransactionTodo Log data For Todo Follow up item duration */
	public function getTransactionTodoLog($todo_id){
		$sql = "select * from tbl_tasks_units_todo_transaction_log where todo_id = ".$todo_id." AND (transaction_type=9 OR transaction_type=8 OR transaction_type=7 OR transaction_type=13)";
		$data = \Yii::$app->db->createCommand($sql)->queryAll();
		return $data;
	}
    /* get new comments by team / case id */
    public function getnewcommentsByTeamOrCase($Id, $type = "") {
        $task_ids = '';
        $query = Tasks::find()->select('tbl_tasks.id');
	
        if ($type == 'case') {
			$caseId = $Id;
	//      $query = $query->innerJoinWith(['clientCase' => function (\yii\db\ActiveQuery $query) { $query->where(['is_close' => 0]);}])->where("client_case_id = :caseId AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0", [':caseId' => $caseId]);
			$query = $query->innerJoinWith(
			['clientCase' => function (\yii\db\ActiveQuery $query) 
				{ 
					$query->where(['tbl_client_case.is_close' => 0]); 
				}
			])->where("tbl_tasks.client_case_id = :caseId AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0", [':caseId' => $caseId]);
        }
        else if ($type == 'task') {
            $taskId = $Id;
            $query = $query->where("tbl_tasks.id = :taskId AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0", [':taskId' => $taskId]); // AND tbl_tasks.task_status IN (0,1,3)
        } else {
            $query = $query->joinWith('taskTeams');
            $pos = strrpos($teamId, '_');
            if ($pos) {
                $team_loc_id = explode("_", $teamId);
                $query = $query->where(["team_id" =>  $team_loc_id[0], "team_loc" => $team_loc_id[1]]);
            } else {
                $query = $query->where(["team_id" =>  $teamId]);
            }
        }
        $task_data = $query->asArray()->all();
        
        $user_id = Yii::$app->user->identity->id;
        if (empty($securityteam_ids)) {
			$securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
			if (empty($securityteam_ids))
				$securityteam_ids = array(0);
		}
            
		$roleId = Yii::$app->user->identity->role_id;
		$role_types = explode(",", $roleId);
        
        $j = 0;
        $comment_ids = array();
        
        if (!empty($task_data)) {
            foreach ($task_data as $tid) {
               
                $comment_data = Comments::find()->select("tbl_comments.Id")->where(['task_id'=>$tid])->all();
				
                /* unread */
                $user_id = Yii::$app->user->identity->id;
                $comment_type = 0;
                $comment_id = 0;
                if (!empty($comment_data)) {
                    foreach ($comment_data as $comments) {
                        
                        $has_access = false;
                            
                        if ($comments->created_by == $user_id) 
                        {
                            $has_access = true;
                        } 
                        else 
                        {
                            if (!empty($comments->commentTeams))
                            {
                                foreach($comments->commentTeams as $val)
                                {
                            
                                    if (isset($val->team_id) && $val->team_id != "") 
                                    {
                                        if (in_array($val->team_id, $securityteam_ids)) 
                                        {
                                            $has_access = true;
                                            break;
                                        }
                                    }
                                 }
                              }      

                            if (!empty($comments->commentRoles))
                            {
                                foreach($comments->commentRoles as $val)
                                {
                                    if (isset($val->role_id) && $val->role_id != "") 
                                    {
                                        $roles = Role::find()->select(['id'])->andWhere("id = " . $val->role_id . " AND id > 0")->one();
                                        if(isset($roles->id))
                                        {
                                            if (in_array($roles->id, $role_types)) 
                                            {
                                                $has_access = true;
                                                break;
                                            }
                                        }  
                                    }
                                }
                            }
                        }
                        
                        $is_read = "N";
                        
                        if ($has_access) {
                         if($user_id == $comments->created_by) { 
                                	$is_read = "Y";
                            } else {   
                                if (!empty($comments->commentsRead))
                                {
                                    foreach($comments->commentsRead as $val)
                                    {
                                        if ($val->user_id != "") {
                                            $already_readuser = $val->user_id;
                                            
                                                if($user_id==$already_readuser) {
                                                    $is_read = "Y";
                                                    break;
                                                } else {
                                                    $is_read = "N";
                                                }
                                            
                                        } 
                                    }
                                } else {
                                        $is_read = "N";
                                }
                            }
                            if ($is_read == "N") {
                                $comment_ids[$comments->Id] = $comments->Id;
                            }
                        }
                    }
                }
            }
        } 

        
		return $comment_ids;
    }
    
    /**
     * get Slaservice todo status by projectId for Accuracy report (SLA TurnTime by client/cases)
     * @return
     */
    public function getSlaServiceTodoStatusByPojectId($task_id, $teamservice_sla){
    	foreach ($teamservice_sla as $sladetail){
    		$slaservices[$sladetail['teamservice_id']] = $sladetail['teamservice_id'];
    	}
    	if(!empty($slaservices))
    		$sqlservice = " AND st.teamservice_id IN (".implode(',',$slaservices).")";
    	 
    	$todo_status = 0;
    	$slaservicetasks = array();
    	$start_date = "";
    	$end_date = "";
    	$sql = "SELECT (SELECT count(tu.id)
	    	FROM tbl_tasks_units_todos AS tu
	    	INNER JOIN tbl_tasks as t ON t.id=tu.task_id
	    	INNER JOIN tbl_tasks_units_todo_transaction_log as ul ON ul.todo_id = tu.id
	    	INNER JOIN tbl_todo_cats AS tc ON tc.id = tu.todo_cat_id
	    	INNER JOIN tbl_tasks_units AS ttu ON tu.tasks_unit_id=ttu.id
	    	INNER JOIN tbl_task_instruct_servicetask as st ON st.id = ttu.task_instruct_servicetask_id $sqlservice
	    	WHERE tu.task_id={$task_id} AND t.task_closed=0) as todos,
	    	(SELECT count(tu.id)
	    	FROM tbl_tasks_units_todos AS tu
	    	INNER JOIN tbl_tasks as t ON t.id=tu.task_id
	    	INNER JOIN tbl_tasks_units_todo_transaction_log as ul ON ul.todo_id = tu.id
	    	INNER JOIN tbl_todo_cats AS tc ON tc.id = tu.todo_cat_id
	    	INNER JOIN tbl_tasks_units AS ttu ON tu.tasks_unit_id=ttu.id
	    	INNER JOIN tbl_task_instruct_servicetask as st ON st.id = ttu.task_instruct_servicetask_id $sqlservice
	    	WHERE tu.task_id={$task_id} AND t.task_closed=0 AND ul.transaction_type=9) as todoscomplete";
    	 
    	$result = \Yii::$app->db->createCommand($sql)->queryAll();
    	if(count($result) > 0){
    		foreach ($result as $data) {
    			if($data['todos'] == $data['todoscompleted'])
    				$todo_status = 4;
    			else
    				$todo_status = 1;
    		}
    	}
    	return $todo_status;
    }
    
    /**
     * get Total SLA Days TodoFollowup for Accuracy report (SLA TurnTime by client/cases)
     * @return
     */
    public function getTotalSLADaysTodoFollowup($task_id, $teamservice_sla, $todo_status,$serviceTaskId = 0)
    {
	    	$sqlservice = " AND st.teamservice_id IN ($teamservice_sla)";
	    	if($serviceTaskId != 0)
	    		$sqlservice .= " AND tu.servicetask_id = $serviceTaskId";
	    	 
	    	$days = 0;
	    	$slaservicetasks = array();
	    	$start_date = "";
	    	$end_date = "";
	    	 
	    	$query ="SELECT ul.id, tu.task_id, ul.tasks_unit_id, ul.todo_id, ul.transaction_type, ul.transaction_date
	    	FROM tbl_tasks_units_todos AS tu
	    	INNER JOIN tbl_tasks as t ON t.id=tu.task_id
	    	INNER JOIN tbl_tasks_units_todo_transaction_log as ul ON ul.todo_id = tu.id
	    	INNER JOIN tbl_todo_cats AS tc ON tc.id = tu.todo_cat_id
	    	INNER JOIN tbl_tasks_units AS ttu ON tu.tasks_unit_id=ttu.id
	    	INNER JOIN tbl_task_instruct_servicetask as st ON st.id = ttu.task_instruct_servicetask_id $sqlservice
	    	WHERE tu.task_id={$task_id} AND t.task_closed=0 AND ul.transaction_type IN (7,9,13)
	    	ORDER BY ul.transaction_date ASC";
	    	$task_unitdata = \Yii::$app->db->createCommand($query)->queryAll();
    	 
    	if(count($task_unitdata) > 0){
	    	$i=0;
	    	$j=0;
    		foreach ($task_unitdata as $data) {
    			if($task_unit_todo_id != 0 && $task_unit_todo_id != $data['todo_id']){
    				$i=0;$j=0;
    			}
    		$task_unit_todo_id=$data['todo_id'];
    		if($data['transaction_type'] == 7 || $data['transaction_type'] == 13)
   		 			$tasksunits_ar[$data['tasks_unit_id']][$data['todo_id']][$i]['start_date'] = $data['transaction_date'];
    			else
   					$tasksunits_ar[$data['tasks_unit_id']][$data['todo_id']][$i]['end_date'] = $data['transaction_date'];
    			$j++;
        		if($j%2==0){
        			$i++;
    			}
     		}
     	}
    
    	if(!empty($tasksunits_ar)){
    		foreach ($tasksunits_ar as $unitstodos) {
    			foreach($unitstodos as $todo){
    				$datesAr = array();
	    				foreach($todo as $dates){
	    					$start_date = $dates['start_date'];
	    					$end_date = "";
	    					if(isset($dates['end_date']))
	    						$end_date = $dates['end_date'];
	    
	    						$curUTCTime = (new Options)->ConvertOneTzToAnotherTz($start_date, 'UTC', $_SESSION['usrTZ'], "requestdate");
	    						$curUTCendTime ="";
	    					if($end_date != "")
	    						$curUTCendTime = (new Options)->ConvertOneTzToAnotherTz($end_date, 'UTC', $_SESSION['usrTZ'], "requestdate");
	    
	    						if(!in_array($curUTCTime,$datesAr)){
	    						if($curUTCendTime != "" && $curUTCTime == $curUTCendTime){
	    						if($days == 0)
	    							$days = 1;
	    						else
	    							$days += 1;
	    					} else {
	    						$days += $this->getSlaWorkingDayswithinDateRange($start_date,$end_date);
	    					}
	    				}
    					$datesAr[$curUTCTime] = $curUTCTime;
    				}
    			}
    		}
    	}
		return $days;
	}
	
	/**
	 * get Sla Team Service Status By Project Id for accuracy report (SLA TurnTime by client/cases)
	 * @return
	 */
	public function getSlaTeamserviceStatusByPojectId($task_id, $slaservices, $serviceTaskdata, $flag="")
	{
		$sqlservice = " AND teamservice_id = $slaservices";
		$slaservicetasks = array();
		$slaservicetask = array();
		$service_status =0;
		 
		if(!empty($serviceTaskdata)){
			$servicestatus = array();
			foreach ($serviceTaskdata as $servicetasks) {
				// Start : to check "Is Any of the servicetask started or on hold or completed or pause?"
				if($servicetasks['teamservice_id'] == $slaservices){
					$slaservicetasks[$servicetasks['servicetask_id']] = $servicetasks['servicetask_id'];
					$slaservicetask[$servicetasks['teamservice_id']][] = $servicetasks['servicetask_id'];
	
					$query = "SELECT t1.id, st.servicetask_id, t.transaction_type, t.transaction_date,stt.service_task
					FROM tbl_tasks_units AS t1
					INNER JOIN tbl_task_instruct_servicetask as st ON st.id = t1.task_instruct_servicetask_id $sqlservice
					INNER JOIN tbl_servicetask as stt ON stt.id = st.servicetask_id
					LEFT JOIN tbl_tasks_units_transaction_log AS t ON t.tasks_unit_id = t1.id
					WHERE (t1.unit_status!=0 AND st.servicetask_id={$servicetasks['servicetask_id']} AND t.task_id={$task_id})
					GROUP BY t1.id, t.transaction_type, st.servicetask_id, t.transaction_date
					ORDER BY t.transaction_date DESC";
					$task_unitdata = \Yii::$app->db->createCommand($query)->queryAll();
	
					$i=0;
					if(count($task_unitdata) > 0){
						foreach ($task_unitdata as $data) {
							if($data['transaction_type'] != "")
								$task_status = $data['transaction_type'];
							$servicestatus[$task_status][] = $data['service_task'];
							break;
						}
					}
				}
				// End : to check "Is Any of the servicetask started or on hold or completed or pause?"
			}
			if(count($slaservicetask[$slaservices]) == count($servicestatus[4]))
				$service_status = 4;
		}
		if($flag == 'iscompleted')
			return  $service_status;
		else
			return $servicestatus;
	}
	
	/**
	 * get sla Total Day Spent for Accuracy report 
	 * @return
	 */
	public function getSlaTotalDaySpent($task_id, $teamservice_sla, $service_status, $preCompletedDate = "")
	{
		$sqlservice = " AND st.teamservice_id IN (".$teamservice_sla.")";
	
		$days = 0;
		$slaservicetasks = array();
		$start_date = "";
		$end_date = "";
	
		$tasksunits_ar = array();
		$tasks_ar = array();
		$task_unit_id=0;
		$query = "SELECT ul.id, ul.task_id, ul.tasks_unit_id, ul.transaction_type, ul.transaction_date
			FROM tbl_tasks_units_transaction_log AS ul
			INNER JOIN tbl_tasks_units AS tu ON tu.id = ul.tasks_unit_id
			INNER JOIN tbl_task_instruct_servicetask as st ON st.id = tu.task_instruct_servicetask_id $sqlservice
			WHERE transaction_type IN(1,4) AND ul.task_id={$task_id}
			ORDER BY ul.tasks_unit_id,ul.id ASC, ul.transaction_date ASC";
			$task_unitdata = \Yii::$app->db->createCommand($query)->queryAll();

		if(count($task_unitdata) > 0){
			$i=0;
			$j=0;
			foreach ($task_unitdata as $data) {
				if($task_unit_id != 0 && $task_unit_id != $data['tasks_unit_id']){
					$i=0;$j=0;$oldstatus = "";
				}
				$task_unit_id=$data['tasks_unit_id'];
				 
				if($data['transaction_type'] == 1)
					$tasksunits_ar[$data['tasks_unit_id']][$i]['start_date'] = $data['transaction_date'];
				else
					$tasksunits_ar[$data['tasks_unit_id']][$i]['end_date'] = $data['transaction_date'];
				 
				if($oldstatus != $data['transaction_type']){
					$j++;
					if($j%2==0){
						$i++;
					}
				}
				$oldstatus = $data['transaction_type'];
			}
		}
		$datesAr = array();
		foreach ($tasksunits_ar as $units) {
			foreach($units as $dates){
				$start_date = $dates['start_date'];
				$end_date = "";
				if(isset($dates['end_date']))
					$end_date = $dates['end_date'];
	
				$curUTCTime = (new Options)->ConvertOneTzToAnotherTz($start_date, 'UTC', $_SESSION['usrTZ'], "YMD");
				$curUTCendTime ="";
				if($end_date != "")
					$curUTCendTime = (new Options)->ConvertOneTzToAnotherTz($end_date, 'UTC', $_SESSION['usrTZ'], "YMD");
	
				if(!in_array($curUTCTime,$datesAr)){
					if($curUTCendTime != "" && $curUTCTime == $curUTCendTime){
						if($days == 0)
							$days = 1;
						else
							$days += 1;
					} else {
						$days += $this->getSlaWorkingDayswithinDateRange($start_date,$end_date);
					}
				}
				$datesAr[$curUTCTime] =$curUTCTime;
			}
		}

		
		//echo "<pre>",$days;
		//die();
		return $days;
	}
	
	/**
	 * get sla stop clock days for accuracy report
	 * @return
	 */
	public function getSlaStopClkDays($task_id, $teamservice_sla)
	{
		$sqlservice = " AND st.teamservice_id IN ($teamservice_sla)";
		$days = 0;
		$slaservicetasks = array();
		$start_date = "";
		$end_date = "";
		$task_unit_todo_id = 0;
		$query = "SELECT ul.id, ul.task_id, ul.tasks_unit_id, ul.todo_id, ul.transaction_type, ul.transaction_date
		FROM tbl_tasks_units_todos AS tu
		INNER JOIN tbl_tasks as t ON tu.task_id=t.id
		INNER JOIN tbl_tasks_units_todo_transaction_log as ul ON ul.todo_id = tu.id
		INNER JOIN tbl_todo_cats AS tc ON tc.id = tu.todo_cat_id
		INNER JOIN tbl_tasks_units AS tau ON tau.id=tu.tasks_unit_id
		INNER JOIN tbl_task_instruct_servicetask as st ON st.id = tau.task_instruct_servicetask_id $sqlservice
		WHERE tu.task_id={$task_id} AND t.task_closed=0 AND tc.stop=1 AND ul.transaction_type IN (7,9,13)
		ORDER BY ul.transaction_date ASC";
		 
		$task_unitdata = \Yii::$app->db->createCommand($query)->queryAll();
		if(count($task_unitdata) > 0){
			$i=0;
			$j=0;
			foreach ($task_unitdata as $data) {
				if($task_unit_todo_id != 0 && $task_unit_todo_id != $data['todo_id']){
					$i=0;$j=0;
				}
				$task_unit_todo_id=$data['todo_id'];
				if($data['transaction_type'] == 7 || $data['transaction_type'] == 13)
					$tasksunits_ar[$data['tasks_unit_id']][$data['todo_id']][$i]['start_date'] = $data['transaction_date'];
				else
					$tasksunits_ar[$data['tasks_unit_id']][$data['todo_id']][$i]['end_date'] = $data['transaction_date'];
	
				$j++;
				if($j%2==0){
					$i++;
				}
			}
		}
		 
		if(!empty($tasksunits_ar)){
			foreach ($tasksunits_ar as $unitstodos) {
				foreach($unitstodos as $todo){
					foreach($todo as $key=>$dates){
						$start_date = $dates['start_date'];
						$end_date = "";
						if(isset($dates['end_date']))
							$end_date = $dates['end_date'];
	
						$curUTCTime = (new Options)->ConvertOneTzToAnotherTz($start_date, 'UTC', $_SESSION['usrTZ'], "YMD");
						$curUTCendTime="";
						if($end_date != "")
							$curUTCendTime = (new Options)->ConvertOneTzToAnotherTz($end_date, 'UTC', $_SESSION['usrTZ'], "YMD");
	
						//if(!in_array($curUTCTime,$datesAr)){
						if($curUTCendTime != "" && $curUTCTime == $curUTCendTime){
							if($days == 0)
								$days = 1;
							else
								$days += 1;
						} else {
							$totaldays = $this->getSlaWorkingDayswithinDateRange($start_date,$end_date);
							if($key > 0){
								if($end_date == "")
									$totaldays = $totaldays - 1;
									
								if($days == 0)
									$days = $totaldays;
								else
									$days += $totaldays;
							} else {
								if($days == 0)
									$days = $totaldays;
								else
									$days += $totaldays;
							}
						}
						//}
	
						$datesAr[$curUTCTime] =$curUTCTime;
						// $days += $this->getSlaWorkingDayswithinDateRange($start_date,$end_date);
						// echo $task_id."=>".$teamservice_sla." => ".$start_date."=>".$end_date." => ".$days."<br/>";
					}
				}
			}
		}
		return $days;
	}
	
	/**
	 * get Project unit completed date service for accuracy report
	 * @return
	 */
	public function getProjectUnitCompletedDateService($teamservice_id,$taskid)
	{
		$sql = "SELECT MAX( t.transaction_date ) AS transaction_date, t.task_id FROM tbl_tasks_units_todo_transaction_log AS t
		INNER JOIN tbl_tasks_units AS tu ON tu.id = t.tasks_unit_id
		INNER JOIN tbl_task_instruct_servicetask AS tist ON tist.id = tu.task_instruct_servicetask_id
		INNER JOIN tbl_task_instruct AS tists ON tists.id = tist.task_instruct_id AND isactive=1
		WHERE tu.unit_status =4 AND t.task_id=$taskid AND tist.teamservice_id =$teamservice_id
		GROUP BY t.task_id";
		$last_assigntask = \Yii::$app->db->createCommand($sql)->queryScalar();
	
		if(isset($last_assigntask))
			$date = $last_assigntask;
		 
		if($date!="")
			$date=(new Options)->ConvertOneTzToAnotherTz($date, "UTC", $_SESSION["usrTZ"],"requestdate");
	
		return $date;
	}
	
	/**
	 * get Element Unit value for accuracy report
	 * @return
	 */
	public function getElementUnitVal($unit_id,$size)
	{
		$unit = Unit::findOne($unit_id);
		$units = "";
		$unitcount = 0;
		if($unit->est_size > 0 && $unit->unit_name != 'GB'){
			$units = 'GB';
			$kb = $unit->est_size;
			$total_kbs = $size; //get qty value in kb
			if($unit->unit_name != 'KB')
				$total_kbs = $kb * $size; //get qty value in kb
			 
			$total_bytes = $total_kbs * 1024; //get total values in bytes to convert it to max unit
			$unitcount = number_format($total_bytes / 1073741824, 2);
		} else {
			$units = $unit->unit_name;
			$unitcount = $size;
		}
		return array('unit'=>$units,'value'=>$unitcount);
	}
	
	/**
	 * get Taskindividual Data for accuracy report
	 * @return
	 */
	public function getTaskindividualData($taskId, $feild, $flag = '',$pporder=-1) 
	{
		$task_individual = TaskInstruct::find()->select(['project_name', 'task_duedate', 'task_timedue', 'task_priority'])->where(['task_id' => $taskId, 'isactive' => '1'])->all();
		if ($feild == 'project_name') {
			return $task_individual->project_name;
		}
		if ($feild == "task_duedate&time" && $flag == 'mdy')
			return (new Options)->ConvertOneTzToAnotherTz($task_individual->task_duedate . " " . $task_individual->task_timedue, 'UTC', $_SESSION['usrTZ'],"date");
		if ($feild == "task_duedate&time" && $flag == 'ymdhis')
			return (new Options)->ConvertOneTzToAnotherTz($task_individual->task_duedate . " " . $task_individual->task_timedue, 'UTC', $_SESSION['usrTZ']);
	}
	
	/* get count for unread comments for MY team */
    public function getUnreadCommentsTeam($team_id, $team_loc,$output = '') 
    {
	    $task_ids = '';
	    $taskdata = array();
        $user_id = Yii::$app->user->identity->id;
        $task_data = Tasks::find()->select(['tbl_tasks.id'])
		  ->innerJoinWith(['comments' => function (\yii\db\ActiveQuery $query) use($user_id) { $query->select(['tbl_comments.task_id','tbl_comments.Id','tbl_comments.created_by', 'tbl_comments.comment_origination','(select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = tbl_comments.Id and tbl_comments_read.user_id='.$user_id.') as readcount']); }])
		  // ->innerJoinWith(['taskInstruct'=>function(\yii\db\ActiveQuery $query) use($team_id,$team_loc){ $query->select(['tbl_task_instruct.id'])->innerJoinWith(['taskInstructServicetasks'=>function(\yii\db\ActiveQuery $query) use($team_id,$team_loc){ $query->select(['tbl_task_instruct_servicetask.id'])->where('tbl_task_instruct_servicetask.team_id = '.$team_id.' AND tbl_task_instruct_servicetask.team_loc = '.$team_loc); }]); }])
		  ->innerJoinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query) use($team_id,$team_loc){
			$query->select(['tbl_tasks_units.id','tbl_tasks_units.task_id'])->where('tbl_tasks_units.team_id = '.$team_id.' AND tbl_tasks_units.team_loc = '.$team_loc); 
		  }])
		  ->innerJoinWith(['clientCase' => function (\yii\db\ActiveQuery $query) { $query->where(['tbl_client_case.is_close' => 0]); }])
		  ->where("tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_tasks.task_status IN (0,1,3)")
		  ->orderBy("tbl_comments.id desc")->all();
			//echo "<pre>"; print_r($task_data); exit;
        $readcnt = 0; 
        foreach($task_data as $value){
			foreach($value->comments as $val){
				if($val->readcount == 0){
					$taskdata[$value->id]=$value->id;
					$readcnt++;
				}
			}
		}
		//echo "<pre>"; print_r($taskdata); exit;
        /*   $j = 0;
        if (!empty($task_data)) {
			if (empty($securityteam_ids)) {
				$securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
				if (empty($securityteam_ids))
					$securityteam_ids = array(0);
			}
			$roleId = Yii::$app->user->identity->role_id;
			$role_types = explode(",", $roleId);
			$data = $task_data;
			
			$comment_type = 0;
			$comment_id = 0;
			$has_access = false;
			if (!empty($data)) {
				foreach ($data as $cmt) {
					$comments1 = $cmt->comments;
					foreach($comments1 as $comments)
					{
						// echo "<pre>",print_r($comments->commentsRead),"</pre>";
						$is_read = "N";
						$has_access = false;
						$has_access = (new Comments)->checkAccess($comments);
						if ($has_access) {
							if($user_id == $comments->created_by) { 
								$is_read = "Y";
							} else { 
								if (!empty($comments->commentsRead))
								{
									foreach($comments->commentsRead as $val)
									{
										if ($val->user_id != "") {
											$already_readuser = $val->user_id;
											// echo "<pre>",$already_readuser,":",$user_id,"</pre>";
											if($user_id==$already_readuser) {
												$is_read = "Y";
												break;
											} else {
												$is_read = "N";
											}
										} 
									}
								} else {
									$is_read = "N";
								}
							}
						// echo "<pre>",print_r($comments),"</pre>";
						$tid = $comments->task_id;
						if ($is_read == "N")
						{
						   $j = $j + 1;
						   $taskdata[$tid]=$tid;
						   // $commentdata[$comments->Id] = $comments->Id;
						}
					}
				  }
				}
			   // exit;
			}
        }
        else {
            $j = 0;
        }
        $count = count($taskdata);
        */
        // echo "<pre>",$j,"</pre>";
        if($output == "task_ids")
            return $taskdata;
           
        // get unread comment count
		// $unreadCommentCount = self::getCountofUnreadComment($taskdata, $team_id, $team_loc);
		//echo $unreadCommentCount; die;
		
		$html = "";
	    $has_access_408=(new User)->checkAccess(5.07);
        if ($has_access_408 && (new User)->checkAccess(5.01)){
		    $html .= Html::a($readcnt , "index.php?r=team-projects/index&team_id=" . $team_id . "&team_loc=" . $team_loc . "&comment=comment", ["data-pjax" => 0, "title" => $readcnt." Unread Comments"]);
        } else {
            $html .= $count_total; 
        }
        if ($has_access_408) {
            $html .= Html::a('<em class="fa fa-search" style="color:grey" title="Search Comments"></em><span class="screenreader">Comments</span>', "javascript:void(0);", ["onclick" => "showsearchcommentteam('" . $team_id . "_" . $team_loc . "', this)", "title" => "Search Comments", "id" => "searchcomment_" . $team_id."_".$team_loc]);
        }
        $html .= "</div></div>";
        // die;
        return $html;
        
    }
    
    /**
     * get count of unread comment
     */
    public function getCountofUnreadComment($comment_arr=array(), $te_id, $te_loc_id)
    {
		$user_id = Yii::$app->user->identity->id;
		// get comment array
		if(!empty($comment_arr))
			$comment_arr = implode(",", $comment_arr);
		else
			$comment_arr = 0;
		
		// comment data	
		$comment_data = Comments::find()->select(['tbl_comments.Id','tbl_comments.comment','tbl_comments.task_id','tbl_comments.created_by','tbl_tasks.client_case_id','tbl_client_case.case_name','usr_first_name' , 'usr_lastname', 'tbl_comments.comment_origination', '(select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = `tbl_comments`.Id and tbl_comments_read.user_id='.$user_id.') as readcount'])
			->innerJoinWith([
				'tasks' => function (\yii\db\ActiveQuery $query) use($te_id,$te_loc_id) {
					$query->innerJoinWith(['clientCase', 'createdUser'])
						->innerJoinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query) use($te_id,$te_loc_id){
							$query->select(['tbl_tasks_units.id'])->where('tbl_tasks_units.team_id = '.$te_id.' AND tbl_tasks_units.team_loc = '.$te_loc_id); 
						}]);
						/* ->innerJoinWith(['taskInstruct'=>function(\yii\db\ActiveQuery $query) use($te_id,$te_loc_id){
								$query->select(['tbl_task_instruct.id'])->where('tbl_task_instruct.isactive = 1')
								->innerJoinWith(['taskInstructServicetasks'=>function(\yii\db\ActiveQuery $query) use($te_id,$te_loc_id){ 
									$query->select(['tbl_task_instruct_servicetask.id','tbl_task_instruct_servicetask.task_instruct_id','tbl_task_instruct_servicetask.team_id','tbl_task_instruct_servicetask.team_loc'])->where('tbl_task_instruct_servicetask.team_id = '.$te_id.' AND tbl_task_instruct_servicetask.team_loc = '.$te_loc_id);  
								}
							]);
						}
					]); */
				}]
			)->where("tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close=0 AND tbl_comments.task_id IN (".$comment_arr.")")->all();
		  
		  $readcnt = 0;
		  foreach ($comment_data as $comments){
			  if($comments->readcount == 0){
				  $readcnt++;
			  }
		  }
		  // die();
		  /*  
		    if(!empty($comment_data))
			{
				foreach ($comment_data as $comments)
				{
					$has_access = false;
					$has_access = (new Comments)->checkAccess($comments);
					if($has_access){
						$is_read = "N";
						if($user_id == $comments->created_by) 
						{ 
							$is_read = "Y";
						} 
						else 
						{ 
							$commentsRead = CommentsRead::find()->where(['comment_id' => $comments->Id,'user_id' => $user_id])->count();
							if($commentsRead){
								$is_read = 'Y';
							}
							/*if (!empty($comments->commentsRead))
							{
								foreach($comments->commentsRead as $val)
								{
									echo "<pre>",print_r($val),"</pre>";
									if ($val->user_id != "") {
										$already_readuser = $val->user_id;
									//	echo "<pre>",$user_id,":",$already_readuser,"</pre>";
										if($user_id==$already_readuser) {
											$is_read = "Y";
											break;
										} else {
											$is_read = "N";
										}
									} 
								}
							} else {
								$is_read = "N";
							}	
						}
						
						
		  			if($is_read=="N")
						{
							echo $is_read,":",$comments->Id,"<br>";
							$searchArray[] = $comments->comment;
						}
					}
			}
		}*/
		// echo "<pre>",print_r($searchArray); die;
		// return count($searchArray);
		return $readcnt;
	}
    
    /**
     * To get Changed Instruction Data using Active TaskInstruction Object.
     * @param object TaskInstruct of active Instruction
     */
    public function IsChangedTaskInstruction($task_id,$servicetask_id=0)
    {
    	$html='';
    	$sql = "SELECT DISTINCT form_builder_id FROM (SELECT form_builder_id, element_value
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
    	) as InstructionDiff
    	GROUP BY form_builder_id, element_value
    	HAVING count(*) = 1) as ElementDiff";
    
    	$formElementArray = array();
    	$query = (new Query)->select(['tbl_form_builder.id','tbl_form_builder.element_type','tbl_form_builder.element_label', 'tbl_form_instruction_values.element_value', "CASE WHEN element_type IN ('checkbox','dropdown','radio') THEN (SELECT element_option FROM tbl_form_element_options WHERE id = tbl_form_instruction_values.element_value) ELSE '' END as option_value"])
		->from('tbl_form_builder')
    			->join('INNER JOIN','tbl_task_instruct_servicetask','tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id')
		->join('INNER JOIN','tbl_task_instruct','tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1')
		->join('INNER JOIN','tbl_form_instruction_values','tbl_form_instruction_values.task_instruct_id = tbl_task_instruct.id AND tbl_form_instruction_values.form_builder_id = tbl_form_builder.id')
		->where('tbl_form_builder.id IN ('.$sql.')')
		->andWhere(['tbl_task_instruct_servicetask.task_id' => $task_id]);
    	if($servicetask_id!=0){
    		$query->andWhere(['tbl_task_instruct_servicetask.servicetask_id' => $servicetask_id]);
    	}
		$formBuilder = $query->createCommand()->queryAll();
		if(!empty($formBuilder))
			return true;
		else 
			return false;
    }
    
    /**
     * To get Changed Instruction Form_builder_ids
     * @param object TaskInstruct of active Instruction
     */
    public function getChangedFBID($task_id,$oldversion = 0 )
    {
    	if($oldversion==0){
    		$oldversion=  "(SELECT instruct_version FROM tbl_task_instruct WHERE  tbl_task_instruct.task_id=$task_id AND tbl_task_instruct.isactive=1)-1";
    	}
    	$sql = "SELECT DISTINCT form_builder_id FROM (SELECT form_builder_id, element_value, element_unit
    	FROM (
    	(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value, tbl_form_instruction_values.element_unit FROM tbl_form_builder
    	INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
    	INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1
    	INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
    	WHERE tbl_form_builder.form_type=1)
    	UNION ALL
    	(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value, tbl_form_instruction_values.element_unit FROM tbl_form_builder
    	INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
    	INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.instruct_version = $oldversion
    	INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
    	WHERE tbl_form_builder.form_type=1)
    	) as InstructionDiff
    	GROUP BY form_builder_id, element_value, element_unit
    	HAVING count(*) = 1) as ElementDiff";
    	return ArrayHelper::map(FormBuilder::find()->where('id IN ('.$sql.')')->all(),'id','id');
    }
    public function getTeamPriorities($task_id,$team_id,$team_loc){
	//	$sql="SELECT team_loc_prority FROM  tbl_tasks_teams WHERE task_id=$task_id AND team_id=$team_id AND team_loc=$team_loc";
		$sql = "SELECT tbl_tasks_teams.team_loc_prority 
			FROM tbl_tasks_teams INNER JOIN tbl_priority_team_loc ON tbl_priority_team_loc.priority_team_id = tbl_tasks_teams.team_loc_prority 
			INNER JOIN tbl_priority_team ON tbl_priority_team.id = tbl_priority_team_loc.priority_team_id 
			WHERE tbl_tasks_teams.task_id=$task_id AND tbl_tasks_teams.team_id=$team_id 
			AND tbl_tasks_teams.team_loc=$team_loc";
	//	$res = Yii::$app->db->createCommand($sql)->one();
		return PriorityTeam::find()->where("id IN ($sql)")->one()->tasks_priority_name;
	}
}
