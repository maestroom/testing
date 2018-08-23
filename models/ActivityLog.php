<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%activity_log}}".
 *
 * @property integer $id
 * @property string $date_time
 * @property integer $user_id
 * @property string $username
 * @property string $origination
 * @property string $activity_type
 * @property string $activity_module_id
 * @property string $activity_name
 * @property string $task_cancel_reason
 */
class ActivityLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origination', 'activity_type'], 'required'],
            [['date_time'], 'safe'],
            [['user_id'], 'integer'],
            [['username', 'activity_type'], 'string'],
            [['origination'], 'string',],
            [['activity_module_id', 'activity_name'], 'string'],
            [['task_cancel_reason'], 'string',]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_time' => Yii::t('app', 'Date Time'),
            'user_id' => Yii::t('app', 'User ID'),
            'username' => Yii::t('app', 'Username'),
            'origination' => Yii::t('app', 'Origination'),
            'activity_type' => Yii::t('app', 'Activity Type'),
            'activity_module_id' => Yii::t('app', 'Activity Module ID'),
            'activity_name' => Yii::t('app', 'Activity Name'),
            'task_cancel_reason' => Yii::t('app', 'Task Cancel Reason'),
        ];
    }

	/**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		$this->date_time = date('Y-m-d H:i:s');
    		$this->user_id = Yii::$app->user->identity->id;
    		$this->username = Yii::$app->user->identity->usr_username;
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
    * @to generate log entries system module wise
    */

    public function generateLog($origination, $type, $module, $name, $reason="")
    {
    	$this->origination = $origination;
    	$this->activity_type = $type;
    	$this->activity_module_id = $module;
    	$this->activity_name = $name;
    	$this->task_cancel_reason = $reason;
    	$this->isNewRecord=true;
    	$this->save(false);
    }

    /**
    * @to generate bulk log entries system module wise
    * @param $rows = array(
    * 					array('origination1','activity_type1','activity_module_id1','activity_name1','task_cancel_reason1'),
    * 					array('origination2','activity_type2','activity_module_id2','activity_name2','task_cancel_reason2')
    * 				);
    */
	public function generateBulkLog($rows)
    {
    	$columns = $this->attributes();
        unset($columns[array_search('id',$columns)]);
        Yii::$app->db->createCommand()->batchInsert(self::tableName(), $columns, $rows)->execute();
    }

    public function processActivity($data,$roleInfo,$all_usre_access_info,$userId)
	{
		//echo "<pre>"; print_r($data); exit;
		$roleId=Yii::$app->user->identity->role_id;

		$User_Role=explode(',',$roleInfo->role_type);

		$bUrl=Url::base(); //base URL
		$activities=array();
				foreach ($data as $md){
					$role_info = $md->user->role;
									$finaldate_time = (new Options)->ConvertOneTzToAnotherTz($md->date_time,'UTC',$_SESSION['usrTZ'],"");
									$cur_date=date('m/d/Y h:i A');
									$currentdate_time = (new Options)->ConvertOneTzToAnotherTz($cur_date,'UTC',$_SESSION['usrTZ'],"");
									$time1=$currentdate_time;
									$time2=$finaldate_time;
									$diff=$this->dateDiff($time1,$time2,1);
									if(!empty($diff))
										$time_ago=$diff." ago";
									else
										$time_ago="a few seconds ago";

									//echo $time_ago;
									$activity_name = $md->activity_name;
									$activity_module_id = $md->activity_module_id;
									$user=$md->username;
									$origin_name="";
									$taskunit_url="";

									if(isset($md->user_id) && $md->user_id!=0)
									{
										$user=$md->user->usr_first_name." ".$md->user->usr_lastname;
									}
									if(!empty($activity_name))
									{
										if($md->origination=='Media'){
											if((new User)->checkAccess(3)){
												$evid_id = $activity_module_id;
												$mediaurl = '/index.php?r=media/index&id='.$evid_id;
												$origin_name='<a href="'.$bUrl.$mediaurl.'" title="#'.$activity_name.'" style="color:#167FAC">  #'.$activity_name.'</a>';
												$image_icon='photo';
											}
									}
									if($md->origination!='Project' && $md->origination!='Change Instructions' && $md->origination!='Instruction Comment' && $md->origination!='Project Comment' && $md->origination!='Project Task Instruction Comment' &&  $md->origination!='Assign Comment' && $md->origination!='Transition Comment' && $md->origination!='ToDo' && $md->origination!='Workflow Templates' && $md->origination!='Project Task Comment')
									{

									if(in_array(2,$User_Role) || ($roleId=='0'))// Team Member
										{
											$team_ids=0;
											$team_loc=0;
											if($md->origination=='Team')
											{
                        $access_team_count = ProjectSecurity::find()->where(["team_id"=>$activity_module_id,"user_id"=>$userId])->count();
												if($access_team_count > 0 || ($roleId=='0'))
												{
													$origin_name=$activity_name;
													$model = Team::find()->select(['team_name','id'])->where('id = '.$activity_module_id)->one();
													if(!empty($model)) {
														$attributes = $model->getAttributes();
														if(!empty($attributes))
														{
															$origin_name = $model->team_name;
															$team_ids = $model->id;
														}
													}
													$image_icon = "folder-o";
												}
											}
											if($md->origination=='TeamService')
												{
													$md->origination = 'Team Service';
													$model = Teamservice::find()->select(['service_name','teamid'])->where('id = '.$activity_module_id)->one();
													if(isset($model) && !empty($model))
													{
														$t_id=$model->teamid;
                            $access_team_count = ProjectSecurity::find()->where(["team_id"=>$t_id,"user_id"=>$userId])->count();
                            if($access_team_count > 0 || ($roleId=='0'))
														{
                              $origin_name=$activity_name;
															$attributes = $model->getAttributes();
															if(!empty($attributes))
															{
																$origin_name=$model->service_name;
																$team_ids=$model->teamid;
															}
														}
														$image_icon = "folder-o";
													}
												}
												if($md->origination=='ServiceTask')
												{
													$md->origination = 'Service Task';
													$model = Servicetask::find()->where('tbl_servicetask.id = '.$activity_module_id)->innerJoinWith('teamservice')->one();

													if(isset($model))
													{
														$t_id=$model->teamservice->teamid;
                            $access_team_count = ProjectSecurity::find()->where(["team_id"=>$t_id,"user_id"=>$userId])->count();
														if($access_team_count > 0 || ($roleId=='0'))
														{
															$origin_name=$activity_name;
															$attributes = $model->getAttributes();
															if(!empty($attributes))
															{
																$origin_name=$model->service_task;
																$team_ids=$model->teamservice->teamid;
															}
															$image_icon = "folder-o";
															if($md->activity_type=='Deleted')
																$image_icon='remove';
														}
													}
												}
									   } // End Team member
									   if(in_array(1,$User_Role) || ($roleId=='0'))//Client/Case Manager
									   {
											if($md->origination=='Client')
												{
                          $access_client_count = ProjectSecurity::find()->select(['id'])->where(["client_id"=>$activity_module_id,"user_id"=>$userId])->count();
                          if($access_client_count > 0 || ($roleId=='0'))
													{
														$origin_name=$activity_name;
														$model = Client::find()->select(['client_name'])->where('id = '.$activity_module_id)->one();
														$attributes = $model->getAttributes();
														if(!empty($attributes))
															$origin_name=$model->client_name;

														$image_icon = "user";
													}
												}
												if($md->origination=='Case')
												{
													if($md->activity_type == 'Closed'){
															$module_id = explode('-',$activity_module_id);
															$activity_module_id = $module_id[0];
														}

                            $access_case_count = ProjectSecurity::find()->select(['id'])->where(["client_case_id"=>$activity_module_id,"user_id"=>$userId])->count();
													if($access_case_count > 0 ||  ($roleId=='0'))
													{
														$origin_name=$activity_name;
														$model = ClientCase::find()->select(['case_name'])->where('id = '.$activity_module_id)->one();
														$attributes = $model->getAttributes();
														if(!empty($attributes))
															$origin_name=$model->case_name;
														  if($md->activity_type == 'Closed')
															 $image_icon='remove';
														  else
															 $image_icon = "folder-o";
													}
												}

									   }	// End Client and case
									   if($md->origination =='Task Instruction Notes' || $md->origination =='Project Task Instruction Notes')
									   {
										      $md->origination =" Task Instructions";
											  if($md->activity_type != 'Deleted'){
											   $instruction_data = TaskInstructNotes::find()->where('id ='.$activity_module_id)->one();
											   $taskidsss = $instruction_data->task_id;
											  } else {
												$activity_name_explode = explode('|',$md->activity_name);
												$taskidsss = $activity_name_explode[1];
											  }
											  if($taskidsss != ''){
											   $task_info = Tasks::find()->where('id = '.$taskidsss)->one();
											   $caseId=$task_info->client_case_id;
											   $caseInfo=ClientCase::find()->where('id = '.$caseId)->one();
											   $service_task_id=$instruction_data->servicetask_id;
										   }

										   if((in_array(1,$User_Role) || ($roleId=='0')) && in_array(2,$User_Role) || ($roleId=='0'))//Client/Case Manager and Team Manager
										   {
										       if((new User)->checkAccess(4.03))
												$url=$bUrl.'/index.php?r=track/index&taskid='.$taskidsss."&case_id=".$caseId."&servicetask_id=".$service_task_id."&instructionnotes=instructionnotes&unit_id=".$activity_module_id;
											else
											    $url = 'javascript:void(0);';
											if((new User)->checkAccess(4.01))
												$projecturl=$bUrl.'/index.php?r=case-projects/index&case_id='.$caseId.'&task_id='.$taskidsss;
											else
											    $projecturl = 'javascript:void(0);';
												$origin_name='<a href="javascript:void(0);" style="color:#167FAC" title="#'.$activity_module_id.'" onclick=activityalltasknotes('.$taskidsss.','.$caseId.','.$service_task_id.',"instructionnotes",'.$activity_module_id.');> #'.$activity_module_id.' </a> of Project <a style="color:#167FAC" href="'.$projecturl.'" title="#'.$taskidsss.'">  #'.$taskidsss.'</a>';
												if(isset($caseInfo->client->client_name))
												$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
												$image_icon='comment';
												/* BY HNL here activity name not contain #project like pattern */
                      }else{
												if(in_array(1,$User_Role) || ($roleId=='0'))//Client/Case Manager
													{
                            $sql = "SELECT client_case_id FROM tbl_project_security WHERE user_id = $userId AND client_case_id != 0";
                            $client_case_task = Tasks::find()->where("client_case_id IN ($sql) AND id = $taskidsss")->count();

														if($client_case_task > 0)
														{
															if(!(new User)->checkCaseAccess($task_info->client_case_id)){
																continue;
															}
															if((new User)->checkAccess(4.01))
															    $projecturl=$bUrl.'/index.php?r=case-projects/index&case_id='.$caseId.'&task_id='.$taskidsss;
															else
															    $projecturl = 'javascript:void(0);';

															if((new User)->checkAccess(4.03))
															    $url=$bUrl.'/index.php?r=track/index&taskid='.$taskidsss."&case_id=".$caseId."&servicetask_id=".$service_task_id."&instructionnotes=instructionnotes&unit_id=".$activity_module_id;
															else
															    $url = 'javascript:void(0);';
															$origin_name='<a href="javascript:void(0);" style="color:#167FAC" title="" onclick=activityalltask1('.$taskidsss.','.$caseId.','.$service_task_id.',"instructionnotes",'.$activity_module_id.');> #'.$activity_module_id.' </a> of Project <a style="color:#167FAC" href="'.$projecturl.'" title="#'.$taskidsss.'" >  #'.$taskidsss.'</a>';

															if(isset($caseInfo->client->client_name))
																$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
														 	    $image_icon='comment';
														}
													}
												if(in_array(2,$User_Role) || ($roleId=='0'))//Team Member
													{
                            $sql = "SELECT team_id FROM tbl_project_security WHERE user_id = $userId AND team_id != 0";
                            $team_task = TasksTeams::find()->joinWith('tasks', false, 'INNER JOIN')->select(['tbl_tasks_teams.task_id'])->where("tbl_tasks_teams.team_id IN ($sql) AND tbl_tasks.id = $taskidsss")->count();

                            if($team_task > 0)
														{
															$team_data = TasksTeams::find()->where('task_id = '.$taskidsss)->all();
															$te_amid=0;
															$te_loc=0;
															//echo "<pre>"; print_r($team_data); exit;
															foreach ($team_data as $tda){
																if((new User)->checkTeamAccess($tda->team_id,$tda->team_loc))
																{
																	$te_amid=$tda->team_id;
																	$te_loc=$tda->team_loc;
																	break;
																}
															}



															/*if((isset($te_amid) && $te_amid!=0) && (isset($te_loc) && $te_loc!=0)){
																continue;
															}*/
															if((new User)->checkAccess(5.02)){
															    $url=$bUrl.'/index.php?r=track/index&taskid='.$taskidsss."&teamId=".$te_amid."&team_loc=".$te_loc."&servicetask_id=".$service_task_id."&instructionnotes=instructionnotes&unit_id=".$activity_module_id;
															}
															else
															{
															    $url="javascript:void(0);";
															}
															if((new User)->checkAccess(5.01)){
															    $projecturl=$bUrl."/index.php?r=team-projects/index&team_id=".$te_amid."&team_loc=".$te_loc."&task_id=".$taskidsss;
															}
															else
															{
															    $projecturl="javascript:void(0);";
															}

															$origin_name='<a href="'.$url.'" title="#'.$activity_module_id.'" style="color:#167FAC"> #'.$activity_module_id.' </a> of Project <a style="color:#167FAC" href="'.$projecturl.'" title="#'.$taskidsss.'">  #'.$taskidsss.'</a>';

															if(isset($caseInfo->client->client_name))
																$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
														 	$image_icon='comment';
														}
													}
										   }
										   //echo $origin_name;

									   }
									}else if($md->origination == 'ToDo'){  // For TODO Activity Log

										$todo_info = TasksUnitsTodos::find()->where('tbl_tasks_units_todos.id ='.$activity_module_id)->innerJoinWith(['taskUnit' => function (\yii\db\ActiveQuery $query) { $query->innerJoinWith(['taskInstruct'=>function(\yii\db\ActiveQuery $query){
												$query->innerJoinWith(['tasks' => function (\yii\db\ActiveQuery $query) { $query->select('tbl_tasks.client_case_id')->innerJoinWith('clientCase'); }]);
											}]); }])->one();

										$servicetask_id = $todo_info->taskUnit->servicetask_id;
										$team_id = $todo_info->taskUnit->team_id;
										$teamlocation = $todo_info->taskUnit->team_loc;
										$passtodo = "";
										if(isset($todo_info->id) && $todo_info->id!=""){
											$url='/index.php?r=track/index&taskid='.$todo_info->taskUnit->task_id."&case_id=".$todo_info->taskUnit->tasks->client_case_id.'&servicetask_id='.$servicetask_id.'&todo_filteredtodayact=todo_filteredtodayact';
												$passtodo = "passtodo";
										}else{
												$url='/index.php?r=site/index';
										}
										if((new User)->checkAccess(4.03))
										    $url=$bUrl.$url;
										else
										    $url="javascript:void(0);";
										$projecturl='';
										$taskidsss = $activity_name;
										$task_info = Tasks::find()->where('id = '.$taskidsss)->one();
									    $caseId=$task_info->client_case_id;
										if($caseId != ''){
											$caseInfo=ClientCase::find()->where('id = '.$caseId)->one();
										}
										$type = 'case';
										if((in_array(1,$User_Role) || ($roleId=='0')) && in_array(2,$User_Role) || ($roleId=='0')){
										    if((new User)->checkAccess(4.01))
											$projecturl=$bUrl.'/index.php?r=case-projects/index&case_id='.$caseId.'&task_id='.$taskidsss;
										    else
											$projecturl='javascript:void(0);';

										}
										else if(in_array(1,$User_Role) || ($roleId=='0'))//Client/Case Manager
											{
												if(!(new User)->checkCaseAccess($caseId)){
													continue;
												}
												if((new User)->checkAccess(4.01))
												    $projecturl=$bUrl.'/index.php?r=case-projects/index&case_id='.$caseId.'&task_id='.$taskidsss;
												else
												    $projecturl='javascript:void(0);';
											}
										else if(in_array(2,$User_Role) || ($roleId=='0')) {
												$type = 'team';
												$te_amid=0;
												$te_loc=0;
												if((new User)->checkAccess(5.01))
												    $projecturl=$bUrl."/index.php?r=team-projects/index&team_id={$todo_info->taskUnit->team_id}&team_loc={$todo_info->taskUnit->team_loc}&task_id={$taskidsss}";
												else
												    $projecturl='javascript:void(0);';


											}
										if($type == 'team'){
												if((new User)->checkTeamAccess($todo_info->taskUnit->taskInstructServicetask['team_id'],$todo_info->taskUnit->taskInstructServicetask['team_loc'])){
													if((new User)->checkAccess(5.02)){
													if($md->activity_type == 'Deleted'){
														$main_page_url = $bUrl."/index.php?r=site/index";
															$origin_name = '<a href="'.$main_page_url.'" style="color:#167FAC" title="#'.$activity_module_id.'" > #'.$activity_module_id.' </a>';
														}else{
															$origin_name = '<a href=javascript:void(0); style="color:#167FAC" onclick=activityalltask2("'.$taskidsss.'","'.$task_info->client_case_id.'","'.$servicetask_id.'","todo_filteredtodayact","'.$passtodo.'","'.$type.'","'.$teamid.'","'.$teamlocation.'"); title="#'.$activity_module_id.'" > #'.$activity_module_id.' </a>';
														}
													}else{
														$origin_name = '#'.$activity_module_id;
													}
													$origin_name.=' in Project';
													$origin_name_se = '<a style="color:#167FAC" href="'.$projecturl.'" title="#'.str_replace('project#:','',$taskidsss).'" >  #'.str_replace('project#:','',$taskidsss).'</a>';
													if((new User)->checkAccess(5.01)){
														$origin_name.= $origin_name_se;
													}else{
														$origin_name.= strip_tags($origin_name_se);
													}
													$origin_name.= ' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
												}else{
													continue;
												}
											}
											else{
												if((new User)->checkAccess(4.01)){
													if($md->activity_type == 'Deleted'){
														$main_page_url = $bUrl."/index.php?r=site/index";
														$origin_name='<a href="'.$main_page_url.'" style="color:#167FAC" title="#'.$activity_module_id.'" > #'.$activity_module_id.' </a> in Project <a href="'.$projecturl.'" title="#'.str_replace('project#:','',$taskidsss).'" style="color:#167FAC">  #'.str_replace('project#:','',$taskidsss).'</a> for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
													}else{
														$origin_name='<a href=javascript:void(0); style="color:#167FAC" onclick=activityalltask2("'.$taskidsss.'","'.$caseId.'","'.$servicetask_id.'","todo_filteredtodayact","'.$passtodo.'","'.$type.'"); title="#'.$activity_module_id.'" > #'.$activity_module_id.' </a> in Project <a href="'.$projecturl.'" title="#'.str_replace('project#:','',$taskidsss).'" style="color:#167FAC">  #'.str_replace('project#:','',$taskidsss).'</a> for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
													}
												}else{
													$origin_name = '#'.$activity_module_id.' in Project #'.str_replace('project#:','',$taskidsss).' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
												}
											}
											 $image_icon = 'bell';
											// echo $origin_name; exit;
									}
									else if($md->origination=='Workflow Templates'){
											if($md->activity_type == 'Deleted')
												$image_icon='remove';
											else
												$image_icon='pencil';
												$origin_name=strtolower($activity_name);
									}else{
											if($md->origination=='Project' && $md->activity_type=="Deleted")
											{
													$activity_name1=explode('|', $activity_name);
													$case_task=explode(":",$activity_name1[0]);
													$caseId=$case_task[1];
													$caseInfo= ClientCase::find()->where('id = '.$caseId)->one();
													$origin_name=strtolower($activity_name1[1]);
													$origin_name='<a href="javascript:void(0);" title="" style="color:#167FAC">'.'#'.str_replace('project#:','',$origin_name)."</a>";

													if(isset($caseInfo->client->client_name))
													$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
													$image_icon = "remove";
											}else if($md->origination=='Project' && ($md->activity_type=="Closed" || $md->activity_type=="Reopen")) {
													$case_task=explode(":",$activity_name);
													$caseId=$case_task[1];
													$caseInfo=ClientCase::find()->where('id = '.$caseId)->one();
													$origin_name=$activity_name;
													//print_r($origin_name);exit;
													$origin_name='<a href="javascript:void(0);" title="" style="color:#167FAC">'.'#'.str_replace('project#:','',$origin_name)."</a>";
													if(isset($caseInfo->client->client_name))
													$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
													$image_icon='check-circle';
											}
											$activity_task = explode('|',$activity_name);
											if(!empty($activity_task[1])){
												$get_task_id = $activity_task[1];
											}else{
												$get_task_id = $activity_name;
												$activity_task[1] = $activity_name;
											}
											$taskidsss=(preg_replace( '/[^0-9]/', '',$get_task_id));
											//echo $activity_name; exit;
											$task_info=Tasks::find()->where('id = '.$taskidsss)->one();
											$caseId=$task_info->client_case_id;
											if(!empty($caseId)){
												$caseInfo=ClientCase::find()->where('id = '.$caseId)->one();
											}
											$url='';
											$role_type=explode(',',$role_info->role_type);
											$has_team_loc_access=true;
											//Client/Case Manager and Team Manager
											if((in_array(1,$User_Role) || ($roleId=='0')) && in_array(2,$User_Role) || ($roleId=='0')){
												if(strtolower($md->activity_type)!="cancelled")
												{
												    if((new User)->checkAccess(4.01))
													$url=$bUrl.'/index.php?r=case-projects/index&case_id='.$caseId.'&task_id='.$taskidsss;
												    else
													$url="javascript:void(0);";
												}
												else if(strtolower($md->activity_type)=="cancelled")
												{
												    if((new User)->checkAccess(4.0811))
													$url=$bUrl.'/index.php?r=case-projects/load-cancelled-projects&case_id='.$caseId.'&task_id='.$taskidsss;
												    else
													$url="javascript:void(0);";
												$image_icon='check-circle';
												}
											}
											else if(in_array(1,$User_Role) || ($roleId=='0'))//Client/Case Manager
											{
												if(!(new User)->checkCaseAccess($caseId)){
													continue;
												}
												if(strtolower($md->activity_type)!="cancelled")
												{
												    if((new User)->checkAccess(4.01))
													$url=$bUrl.'/index.php?r=case-projects/index&case_id='.$caseId.'&task_id='.$taskidsss;
												    else
													$url="javascript:void(0);";
												}
												else if(strtolower($md->activity_type)=="cancelled"){
												    if((new User)->checkAccess(4.0811))
													$url=$bUrl.'/index.php?r=case-projects/load-cancelled-projects&case_id='.$caseId.'&task_id='.$taskidsss;
												    else
													$url="javascript:void(0);";
													$image_icon='check-circle';
												}
											}
											else if(in_array(2,$User_Role) || ($roleId=='0'))//Team Member
											{
												$te_amid=0;
												$te_loc=0;
												if(isset($task_info->id) && $task_info->id!="" && $task_info->id!=0){
												$team_data = TasksTeams::find()->where('task_id = '.$task_info->id)->all();
													foreach ($team_data as $tda){
														if((new User)->checkTeamAccess($tda->team_id,$tda->team_loc))
														{
															$te_amid=$tda->team_id;
															$te_loc=$tda->team_loc;
															break;

														}
													}
												}
												if($te_amid==0 && $te_loc==0){
													$has_team_loc_access=false;
													$url="#";
												}else{
												    if((new User)->checkAccess(5.01))
													$url=$bUrl.'/index.php?r=team-projects/index&team_id='.$te_amid.'&team_loc='.$te_loc.'&task_id='.$taskidsss;
												    else
													$url='javascript:void(0);';
												}
											}
											//Client/Case Manager and Team Manager

											$has_access=false;
											if((in_array(1,$User_Role) || ($roleId=='0')) && (in_array(2,$User_Role) || ($roleId=='0')))
											{
                        $sql = "SELECT client_case_id FROM tbl_project_security WHERE user_id = $userId AND client_case_id != 0";
                        $client_case_task = Tasks::find()->where("client_case_id IN ($sql) AND id = $taskidsss")->count();

                        $sql_team = "SELECT team_id FROM tbl_project_security WHERE user_id = $userId AND team_id != 0";
                        $team_task = TasksTeams::find()->joinWith('tasks', false, 'INNER JOIN')->select(['tbl_tasks_teams.task_id'])->where("tbl_tasks_teams.team_id IN ($sql_team) AND tbl_tasks.id = $taskidsss")->count();

												//if(in_array($taskidsss,$all_usre_access_info[$userId]['all_task']))
                        if($client_case_task > 0 || $team_task > 0)
												{
													$has_access=true;
													$origin_name1=(strtolower($activity_task[1]));
													$origin_name = str_replace("#:"," #",$origin_name1);

													if(stristr($origin_name,'project'))
														{
															/*Code for task units activity*/
															$activity_name1=explode('|', $activity_name);
															if(isset($activity_name1[2]))
															{
																$second_activity = str_replace('unit','',$activity_name1[2]);
																$unit_id=preg_replace("/[^0-9]/","",$activity_name1[2]);

																if($unit_id != ''){
																	$unit_data = TasksUnits::find()->where('id = '.	$unit_id)->one();
																}
																$taskunit_url='<a href="javascript:void(0);" style="color:#167FAC" title="'.str_replace(':','',$second_activity).'" onclick="activityalltask('.$taskidsss.','.$caseId.','.$unit_id.','.$unit_data->taskInstructServicetask->servicetask_id.');">'.str_replace(':','',$second_activity)."</a>";
																if((new User)->checkAccess(4.03)){
																    $assignurl=$bUrl.'/index.php?r=track/index&task_id='.$taskidsss.'&case_id='.$caseId.'&taskunit='.$unit_id.'&servicetask_id='.$unit_data->taskInstructServicetask->servicetask_id;
																}
																else{
																    $assignurl='javascript:void(0);';
																    $taskunit_url = strip_tags($taskunit_url);
																 }



																if(strtolower($md->activity_type)!="cancelled")
																{
																	$origin_name='<a href="'.$url.'" style="color:#167FAC" title="'.ucwords(str_replace('project','',$origin_name)).'">'.ucwords(str_replace('project','',$origin_name))."</a>";
																	//echo $origin_name; exit;
																}else{
																	$image_icon = 'remove';
																	$origin_name='<a href="'.$url.'" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";

																}
																if(isset($caseInfo->client->client_name))
																	$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
																if(strtolower($md->activity_type)=="transferred")
                                                                	$image_icon='clock-o';
                                                                else if(strtolower($md->activity_type)=="unassigned")
																	$image_icon='thumb-tack';
															 	else if(strtolower($md->activity_type)=="cancelled")
																	$image_icon='remove';
															 	else if(strtolower($md->activity_type)=="change")
															 		$image_icon='pencil';
															 	else
																	$image_icon='clock-o';
															}	//End of task units activity
															else
															{
															if(strtolower($md->activity_type)!="cancelled")
															{
																$origin_name='<a href="'.$url.'" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";

															}
															else{
																$origin_name='<a href="'.$url.'" title="#'.$activity_module_id.'" style="color:#167FAC">#'.$activity_module_id."</a>";

															}
															if(isset($caseInfo->client->client_name))
															$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
															if(strtolower($md->activity_type)=="updated")
															$image_icon='pencil';
															else if(strtolower($md->activity_type)=="completed")
															$image_icon='clock-o';
															elseif(strtolower($md->activity_type)=="closed")
															$image_icon='remove';
															elseif(strtolower($md->activity_type)=="reopen")
															$image_icon='clock-o';
															elseif(strtolower($md->activity_type)=="transferred")
                                                            $image_icon='map-marker';
                                                            else if(strtolower($md->activity_type)=="cancelled")
															$image_icon='remove';//$image='feed_task_grids_closed.png';
															else if(strtolower($md->activity_type)=="unassigned")
															$image_icon='thumb-tack';
															else if(strtolower($md->activity_type)=="change")
																$image_icon='pencil';
															else
															$image_icon='pencil';
															}
														}
														if($md->origination=='Instruction Comment' || $md->origination=='Project Comment' || $md->origination=='Assign Comment' || $md->origination=='Transition Comment' || $md->origination == 'Project Task Comment')
														{

															if($md->origination=='Instruction Comment') $md->origination='Project Comment';

															$model = Comments::find()->where('id = '.$activity_module_id)->one();
															if(!empty($model)){
															$user_model = User::find()->where('id = '.$model->created_by)->one();
															if($user_model->usr_type==2)
																	$image_icon='comment';
															else if($user_model->usr_type==1)
																	$image_icon='comment';
															}else{
																$image_icon='comment';
															}
														}
														if($md->origination=='Change Instructions')
														{
															$image_icon='pencil';

														}
														if($md->origination=='Change')
														{
															$image_icon='pencil';

														}
															//echo "good1"; exit;
												}
											}
											else if(in_array(1,$User_Role) || ($roleId=='0'))//Client/Case Manager
											{
                        $sql = "SELECT client_case_id FROM tbl_project_security WHERE user_id = $userId AND client_case_id != 0";
                        $client_case_task = Tasks::find()->where("client_case_id IN ($sql) AND id = $taskidsss")->count();

												if($client_case_task > 0)
												{
														$has_access=true;
														$origin_name1=(strtolower($activity_task[1]));
														$origin_name = str_replace("#:","#",$origin_name1);

														if(stristr($origin_name,'project'))
														{
																$activity_name1=explode('|', $activity_name);

																/*Code for task units activity*/
																if(isset($activity_name1[2]))
																{

																	$unit_id=preg_replace("/[^0-9]/","",$activity_name1[2]);
																	if(isset($unit_id) && $unit_id!=""){
																	$unit_data = TasksUnits::find()->where('id = '.$unit_id)->one();
																	if(!(new User)->checkCaseAccess($case_id)){
																		continue;
																	}
																	$find_column = strpos($activity_name1[2],':');
																	if($find_column != ''){
																		$unit_side = str_replace('unit#:','#',$activity_name1[2]);
																	}else{
																		$unit_side = str_replace('unit','',$activity_name1[2]);
																	}
																	if((new User)->checkAccess(4.03))
																	    $assignurl=$bUrl.'/index.php?r=track/index&taskid='.$taskidsss.'&case_id='.$caseId.'&taskunit='.$unit_id.'&servicetask_id='.$unit_data->servicetask_id;
																	else
																	    $assignurl='javascript:void(0);';

																	if((new User)->checkAccess(4.03)){
																	$taskunit_url='<a href="javascript:void(0);" style="color:#167FAC" title="'.$unit_side.'" onclick="activityalltask('.$taskidsss.','.$caseId.','.$unit_id.','.$unit_data->servicetask_id.');">'.$unit_side."</a>";
																	}else{
																		$taskunit_url = $unit_side;
																	}
																	//echo $taskunit_url; exit;
																	if(strtolower($md->activity_type)!="cancelled")
																	{
																		$origin_name1='<a href="'.$url.'" style="color:#167FAC" title="'.ucwords(str_replace('project','',$origin_name)).'">'.ucwords(str_replace('project','',$origin_name))."</a>";
																	}else{
																		$origin_name1='<a href="'.$url.'" style="color:#167FAC" title="'.ucwords(str_replace('project','',$origin_name)).'">'.ucwords(str_replace('project','',$origin_name))."</a>";
																	}
																	if((new User)->checkAccess(4.01)){
																		$origin_name = $origin_name1;
																	}else{
																		$origin_name = ucwords(str_replace('project','',$origin_name));
																	}
																	if(isset($caseInfo->client->client_name))
																		$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
																	if(strtolower($md->activity_type)=="transferred")
																		$image_icon='clock-o';
																	else if(strtolower($md->activity_type)=="unassigned")
																		$image_icon='thumb-tack';
																	else if(strtolower($md->activity_type)=="cancelled")
																		$image_icon='remove';
																	else if(strtolower($md->activity_type)=="change")
																		$image_icon='pencil';
																	else
																		$image_icon='remove';
																	}
																}	//End of task units activity
																else
																{
																$origin_name='<a href="'.$url.'" title="'.ucwords(str_replace('project','',$origin_name)).'">'.ucwords(str_replace('project','',$origin_name))."</a>";
																if(isset($caseInfo->client->client_name))
																$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
																if(strtolower($md->activity_type)=="updated")
																$image_icon='pencil';
																else if(strtolower($md->activity_type)=="completed")
																$image_icon='clock-o';
																elseif(strtolower($md->activity_type)=="closed")
																$image_icon='remove';
																elseif(strtolower($md->activity_type)=="reopen")
																$image_icon='clock-o';
																elseif(strtolower($md->activity_type)=="transferred")
																$image_icon='clock-o';
																else if(strtolower($md->activity_type)=="unassigned")
																$image_icon='thumb-tack';
																else if(strtolower($md->activity_type)=="cancelled")
																$image_icon='remove';
																elseif(strtolower($md->origination)=='change')
																	$image='pencil';
																else
																$image_icon='pencil';
																}
														}
														if($md->origination=='Instruction Comment' || $md->origination=='Project Task Instruction Comment' || $md->origination=='Project Comment' || $md->origination=='Assign Comment' || $md->origination=='Transition Comment' || $md->origination == 'Project Task Comment')
														{
															if($md->origination=='Instruction Comment') $md->origination='Project Comment';
															if($md->origination=='Project Task Instruction Comment')$md->origination='Project Comment';
															$model = Comments::find()->where('id = '.$activity_module_id)->one();
															$user_model = User::find()->where('id = '.$model->created_by)->one();
															if($user_model->usr_type==2)
																	$image_icon='comment';
															else if($user_model->usr_type==1)
																	$image_icon='comment';
													    }
													}
												}
												else if(in_array(2,$User_Role) || ($roleId=='0'))//Team Member
												{
                          $sql_team = "SELECT team_id FROM tbl_project_security WHERE user_id = $userId AND team_id != 0";
                          $team_task_arr = ArrayHelper::map(TasksTeams::find()->select('id')->joinWith('tasks', false, 'INNER JOIN')->select(['tbl_tasks_teams.task_id'])->where("tbl_tasks_teams.team_id IN ($sql_team) AND tbl_tasks.id = $taskidsss")->all(),'id','id');
                          $sql = "SELECT client_case_id FROM tbl_project_security WHERE user_id = $userId AND client_case_id != 0";
                          $client_case_task_arr = ArrayHelper::map(Tasks::find()->select('id')->where("client_case_id IN ($sql) AND id = $taskidsss")->all(),'id','id');
                          $result_team_case_diff = array_diff_assoc($team_task_arr, $client_case_task_arr);
                          //$result_team_case_diff = array_diff_assoc($all_usre_access_info[$userId]['team_task'], $all_usre_access_info[$userId]['client_case_task']);

                          $sql = "SELECT team_id FROM tbl_project_security WHERE user_id = $userId AND team_id != 0";
                          $team_task = TasksTeams::find()->joinWith('tasks', false, 'INNER JOIN')->select(['tbl_tasks_teams.task_id'])->where("tbl_tasks_teams.team_id IN ($sql) AND tbl_tasks.id = $taskidsss")->count();

                          if($team_task > 0)
													{
															$has_access=true;
															$origin_name1=(strtolower($activity_task[1]));
															$origin_name = str_replace("#:"," #",$origin_name1);
															if(stristr($origin_name,'project'))
															{
																	$activity_name1=explode('|', $activity_name);
																	$activity_name1[2] = str_replace("#:","#",$activity_name1[2]);
																	if(isset($activity_name1[2]))
																	{

 																	    $unit_id=preg_replace("/[^0-9]/","",$activity_name1[2]);
 																		if(isset($unit_id) && $unit_id!=""){
																		$unit_data = TasksUnits::find()->where('id = '.$unit_id)->one();
																		if(!(new User)->checkTeamAccess($unit_data->team_id,$unit_data->team_loc)){
																			continue;
																		}
																		if((new User)->checkAccess(5.02)){
																		$assignurl=$bUrl.'/index.php?r=track/index&taskid='.$taskidsss.'&team_id='.$unit_data->team_id.'&team_loc='.$unit_data->team_loc.'&taskunit='.$unit_id.'&servicetask_id='.$unit_data->servicetask_id;}
																		else{
																		$assignurl='javascript:void(0);';
																		}
																		$taskunit_url='<a href="'.$assignurl.'" style="color:#167FAC" title="'.str_replace('unit','',$activity_name1[2]).'">'.str_replace('unit','',$activity_name1[2])."</a>";
																		//echo $taskunit_url; exit;
																		if(!(new User)->checkAccess(5.02)){
																			$taskunit_url = strip_tags($taskunit_url);
																		}
																		if(strtolower($md->activity_type)!="cancelled")
																		{
																			$origin_name='<a href="'.$url.'" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";
																		}else{
																			$origin_name='<a href="javascript:void(0);" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";
																		}
																		if(isset($caseInfo->client->client_name))
																			$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;
																		//echo $origin_name; exit;
																		if(strtolower($md->activity_type)=="transferred")
																			$image_icon='clock-o';
																		else if(strtolower($md->activity_type)=="unassigned")
																			$image_icon='thumb-tack';
																		else if(strtolower($md->activity_type)=="cancelled")
																			$image_icon='remove';
																		else if(strtolower($md->activity_type)=="change")
																			$image_icon='pencil';
																		else
																			$image_icon='pencil';
 																		}

																	}	//End of task units activity
																	else
																	{
																	$origin_name='<a href="'.$url.'" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";
																	if(isset($caseInfo->client->client_name))
																		$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;

																	if(strtolower($md->activity_type)=="updated")
																	$image_icon='pencil';
																	else if(trim(strtolower($md->activity_type))=="completed")
																	$image_icon='clock-o';
																	elseif(strtolower($md->activity_type)=="closed")
																	$image_icon='clock-o';
																	elseif(strtolower($md->activity_type)=="reopen")
																	$image_icon='clock-o';
																	elseif(strtolower($md->activity_type)=="transferred")
																	$image_icon='clock-o';
																	else if(strtolower($md->activity_type)=="unassigned")
																	$image_icon='thumb-tack';
																	else if(strtolower($md->activity_type)=="cancelled")
																	$image_icon='remove';
																	elseif(strtolower($md->origination)=='change')
																	$image_icon='pencil';
																	else
																	$image_icon='pencil';
																	}
															}
															if($md->origination=='Instruction Comment' || $md->origination=='Project Comment' || $md->origination=='Assign Comment' || $md->origination=='Transition Comment' || $md->origination == 'Project Task Comment')
															{
																if($md->origination=='Instruction Comment') $md->origination='Project Comment';
																$model = Comments::find()->where('id = '.$activity_module_id)->one();
																if(!empty($model)){
																$user_model = User::find()->where('id = '.$model->created_by)->one();
																if($user_model->usr_type==2)
																		$image_icon='comment';
																else if($user_model->usr_type==1)
																		$image_icon='comment';
																}else{
																	$image_icon='comment';
																}
															}
													}
													else
													{
                          $team_services = Teamservice::find()->select(['id'])->where('teamid IN(select team_id from tbl_project_security where user_id='.$userId.' group by team_id)')->all();
													foreach ($result_team_case_diff as $res_team_case_diff)
													{
													if($taskidsss==$res_team_case_diff)
													{
														//foreach ($all_usre_access_info[$userId]['team_allservices'] as $service_id)
                            foreach ($team_services as $service_id)
														{
															{
																$has_access=true;
																	$origin_name1 =(strtolower($activity_task[1]));
																	$origin_name = str_replace("#:"," #",$origin_name1);
																	if(stristr($origin_name,'project'))
																	{

																		if(strtolower($md->activity_type)!="cancelled"){
																		$origin_name='<a href="'.$url.'" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";
																		}else{
																		$origin_name='<a href="javascript:void(0);" title="'.ucwords(str_replace('project','',$origin_name)).'" style="color:#167FAC">'.ucwords(str_replace('project','',$origin_name))."</a>";
																		}
																		if(isset($caseInfo->client->client_name))
																			$origin_name.=' for '.$caseInfo->client->client_name.' - '. $caseInfo->case_name;

																		if(strtolower($md->activity_type)=="updated")
																			$image_icon='edit';
																		else if(strtolower($md->activity_type)=="completed")
																			$image_icon='clock-o';
																		elseif(strtolower($md->activity_type)=="closed")
																			$image_icon='clock-o';
																		elseif(strtolower($md->activity_type)=="reopen")
																			$image_icon='';
																		elseif(strtolower($md->origination)=='change')
																			$image_icon='pencil';
																		else
																			$image_icon='pencil';
																	}
																	if($md->origination=='Instruction Comment' || $md->origination=='Project Comment' || $md->origination=='Assign Comment' || $md->origination=='Transition Comment' || $md->origination == 'Project Task Comment')
																	{
																		if($md->origination=='Instruction Comment') $md->origination='Project Comment';
																		$model = Comments::find()->where('id = '.$activity_module_id)->one();
																		if(!empty($model)){
																		$user_model = User::find()->where('id = '.$model->created_by)->one();
																		if($user_model->usr_type==2)
																				$image_icon='comment';
																		else if($user_model->usr_type==1)
																				$image_icon='comment';
																		}else{
																			$image_icon='comment';
																		}
																	}
															}
														}
													}

													}
													}
												}
												if(!$has_access){
												if((in_array(1,$User_Role) || ($roleId=='0')) && (in_array(2,$User_Role) || ($roleId=='0')))//Client/Case Manager and Team Manager
												{
													$has_team_loc_access=true;
													if($md->origination!='Project' && $md->activity_type!="Deleted")
														$origin_name="";
												}
												else if(in_array(1,$User_Role) || ($roleId=='0'))//Client/Case Manager
												{
													$has_team_loc_access=true;
													if($md->origination!='Project' && $md->activity_type!="Deleted")
														$origin_name="";
												}
												else if(in_array(2,$User_Role) || ($roleId=='0'))//Team Manager
												{
													$has_team_loc_access=true;
													if($md->origination!='Project' && $md->activity_type!="Deleted")
														$origin_name="";
												}
												else{
													$origin_name="";
												}
											}
											if(!$has_team_loc_access){
												$origin_name="";
												continue;
											}


										}
									}
									//echo $origin_name; exit;
									$activity_type=strtolower($md->activity_type);
									$title = strtolower($md->activity_type);

									if(strtolower($md->activity_type)=='assignedtask')
									{
										$title = "assigned task";
										$activity_type='assigned task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='transferred')
									{
										$title = "transferred task";
										$activity_type='transferred task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='startedtask')
									{
										$title = "started task";
										$activity_type='started task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='unassigned')
									{
										$title = "UnAssigned Project task";
										$activity_type='UnAssigned Project task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='pausedtask')
									{
										$title = "paused task";
										$activity_type='paused task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='onholdtask')
									{
										$title = "hold task";
										$activity_type='hold task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='transitionedtask')
									{
										$title = "transitioned task";
										$activity_type='transitioned task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='completedtask')
									{
										$title = "completed task";
										$activity_type='completed task '.$taskunit_url.' in ';
									}
									if(strtolower($md->activity_type)=='deleted'){
										$activity_type='removed';
										$title = "removed";
									}
									//echo 'done'.$activity_type.'<br>';
									if(strpos($origin_name,'javascript:void(0);') && $md->origination != 'ToDo'){
										$origin_name = strip_tags($origin_name);
									}
									$never_cap = array('in'=>'In','of'=>'Of','for'=>'For','to'=>'To');
									//$origin_name = str_replace("#:"," #",$origin_name1);
									$array_keys = array_keys($never_cap);
									if($origin_name!="")
									{
									 $activities[$md->id]['image'] = $image_icon;
									 $activity_upper = ucwords($activity_type);
									 $i=0;
									 foreach($never_cap as $cap){
										$find_pos = strpos($activity_upper,$cap);
										if($find_pos != ''){
											$activity_type_end = str_replace($cap,$array_keys[$i],$activity_upper);
											break;
										}else{
											$activity_type_end = $activity_upper;
										}
										$i++;
									 }
									/*if(strpos($activity_type_end,'javascript:void(0);')){
										$activity_type_end = strip_tags($activity_type_end);
									}*/
									/* $origin_name_upper = explode('-',$origin_name);

									 if(isset($origin_name_upper[1])){
										$origin_name_upper[1] = strtoupper($origin_name_upper[1]);
										$origin_name = implode('-',$origin_name_upper);

									 }else{
										$origin_name = $origin_name;
									 }*/
									 $activities[$md->id]['activity'] = $user." ".$activity_type_end." ".$md->origination." ".$origin_name;
									 $activities[$md->id]['time']=$time_ago;
									 $activities[$md->id]['title'] = $md->origination." ".ucwords($title);
									}

				}
				//exit;
				return $activities;
	}

	public function dateDiff($time1, $time2, $precision)
	{
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
    $intervals = array('hour','minute','second');
    $diffs = array();

    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Set default diff to 0
      $diffs[$interval] = 0;
      // Create temp time from time1 and interval
      $ttime = strtotime("+1 " . $interval, $time1);
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
	$time1 = $ttime;
	$diffs[$interval]++;
	// Create new temp time from time1 and interval
	$ttime = strtotime("+1 " . $interval, $time1);
      }
    }$count = 0;
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

	public function getUser(){
		   return $this->hasOne(User::className(), ['id' => 'user_id']);
	}


}
