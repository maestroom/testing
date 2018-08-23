<?php
 use app\models\Options;
 use app\models\Role;
 use app\models\User;
 use app\models\TaskInstructServicetask;
 use app\models\SettingsEmail;
 use yii\helpers\ArrayHelper;
 
 $bUrl = Yii::$app->getUrlManager()->getBaseUrl();
 $assign = array();
 $service = array();
 $userId = Yii::$app->user->identity->id;
 $roleId = Yii::$app->user->identity->role_id;
 $role_info = Role::find()->where('id = '.$roleId)->one();
 $role_type = explode(',', $role_info->role_type);
 $url = "";
	if(in_array(1, $role_type) && in_array(2, $role_type)) 
	{
		if((new User)->checkAccess(4.01)){
			$url = '/index.php?r=case-projects/index&case_id=' . $taskdata->client_case_id . '&task_id=' . $taskdata->id;
		}
	}else if (in_array(1, $role_type)) {///client/Case Manager
		if((new User)->checkAccess(4.01)){
			$url = '/index.php?r=case-projects/index&case_id=' . $taskdata->client_case_id . '&task_id=' . $taskdata->id;
		}
	}else if (in_array(2, $role_type)) {
		if((new User)->checkAccess(5.01)){
			$task_id = $taskdata->id;
			$taskdata_sql = "select t.id,team.team_id,team.team_loc from tbl_tasks as t INNER JOIN tbl_tasks_teams as team ON team.task_id = t.id where t.id= $task_id AND team.team_id IN (select team_id from tbl_project_security where user_id = $userId) AND team.team_loc IN (select team_loc from tbl_project_security where user_id = $userId)";
			$params = array(":task_id"=>$task_id,":user_id"=>$userId);
			$taskdata1 = \Yii::$app->db->createCommand($taskdata_sql)->queryAll();
			$url = '/index.php?r=team-projects/index&team_id=' . $taskdata1[0]['team_id'] . '&team_loc='.$taskdata1[0]['team_loc'].'&task_id=' . $taskdata->id;
		}
	}
	
 //foreach($taskdata as $task){
	//if(!empty($task['assigned_to'])){ $assign[] = $task['assigned_to']; }
	//if(!empty($task['service_task'])){ $service[] = $task['service_task']; }
 //}
$sql="SELECT unit_assigned_to FROM tbl_tasks_units WHERE unit_assigned_to  !=0 AND task_id =".$taskdata->id;
$assigedUser = ArrayHelper::map(User::find()->select([" CONCAT(usr_first_name,' ', usr_lastname) AS full_name "])->where('id IN ('.$sql.')')->all(),'full_name','full_name');
$assigned_to = implode(', ',$assigedUser);
$service_task = "";
$teamservice_data = (new SettingsEmail)->getTeamServicesByProjectID($taskdata->id);
//TaskInstructServicetask::find()->select(['tbl_task_instruct_servicetask.teamservice_id','tbl_teamservice.service_name'])->joinWith('teamservice')->where(['tbl_task_instruct_servicetask.task_id'=>$taskdata->id])->groupBy(['tbl_task_instruct_servicetask.teamservice_id', 'tbl_teamservice.service_name'])->all();  
$servicetask_names = "";
foreach($teamservice_data as $teamservice){
	if($servicetask_names != "")
	    $servicetask_names.='; ' . $teamservice;//->teamservice->service_name;
    else
      	$servicetask_names = $teamservice;//->teamservice->service_name;
}
$service_task=$servicetask_names;
 
 
 $str = "";
  $str.='<div class=""><div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Project">Project</a> </label><span class="text-muted col-sm-7">';
  if(!empty($url)){
  $str.='<a  href=' . $bUrl . $url . '>' . $taskdata->id . '</a></span></div>';
  }else{
	  $str.= $taskdata->id . '</span></div>';
	  }
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Submitted By">Submitted By</a> </label><span class="text-muted col-sm-7">' . $taskdata->createdUser->usr_first_name." ".$taskdata->createdUser->usr_lastname . '</span></div>';
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Assigned To">Assigned To</a> </label>  <span class="text-muted col-sm-7">' . $assigned_to . '</span></div>';
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Client">Client</a> </label> <span class="text-muted col-sm-7">' . $taskdata->clientCase->client->client_name . '</span></div>';
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Case">Case</a> </label> <span class="text-muted col-sm-7">' . str_replace("'", "&#39;", $taskdata->clientCase->case_name) . '</span></div>';
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Services">Services</a> </label> <span class="text-muted col-sm-7">'.$service_task.'</span></div>';
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Submitted Date">Submitted Date</a> </label> <span class="text-muted col-sm-7">' . (new Options)->ConvertOneTzToAnotherTz($taskdata->created,'UTC',$_SESSION['usrTZ']) . '</span></div>';
  $str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Due Date">Due Date</a> </label> <span class="text-muted col-sm-7">' . (new Options)->ConvertOneTzToAnotherTz($taskdata->activeTaskInstruct->task_duedate." ".$taskdata->activeTaskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"MDY") . '</span></div>';
  if ((isset($taskdata->activeTaskInstruct->task_duedate) && $taskdata->activeTaskInstruct->task_duedate != "0000:00:00") && (isset($taskdata->activeTaskInstruct->task_timedue) && $taskdata->activeTaskInstruct->task_timedue != "0000:00:00"))
  	$str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black"  title="Due Time">Due Time</a> </label> <span class="text-muted col-sm-7">' . (new Options)->ConvertOneTzToAnotherTz($taskdata->activeTaskInstruct->task_duedate." ".$taskdata->activeTaskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"time") . '</span></div>';
 
  	$str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Priority">Priority</a> </label> <span class="text-muted col-sm-7">' . $taskdata->activeTaskInstruct->taskPriority->priority . '</span></div>';
  	$str.='<div class="row"><label class="col-sm-5"><a href="javascript:void(0);" class="tag-header-black" title="Status">Status</a> </label> <span class="text-muted col-sm-7">' . $task_status[$taskdata->task_status] . '</span></div></div>'; 

echo $str; ?>
