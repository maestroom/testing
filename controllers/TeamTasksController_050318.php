<?php
namespace app\controllers;
use Yii;
use app\models\search\TasksUnitsSearch;
use app\models\PriorityProject;
use app\models\TasksUnitsTodos;
use app\models\TasksUnits;
use app\models\TasksTeams;
use yii\web\Session;
use app\models\SettingsEmail;
use app\models\Servicetask;
use app\models\TaskInstructServicetask;
use app\models\TasksUnitsTransactionLog;
use app\models\Tasks;
use app\models\TaskInstruct;
use app\models\ActivityLog;
use app\models\Todocats;
use app\models\User;
use app\models\ProjectSecurity;
use app\models\Pricing;
use app\models\PriorityTeam;
use app\models\TasksUnitsTodoTransactionLog;
use app\models\ClientCase;
use app\models\TeamlocationMaster;
use app\models\EmailCron;
use yii\web\Controller;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class TeamTasksController extends Controller
{
	/*
	 * Return the Grid For the TeamTasks
	 * */

	public function beforeAction($action) {
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));


		if (!(new User)->checkAccess(5.014))/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');

		return parent::beforeAction($action);
	}

	public function actionGetDescTeamPriority()
	{
		$priority = Yii::$app->request->get('priority',0);
		$result = PriorityTeam::find()->select(['priority_desc'])->where(['id' => $priority])->asArray()->one();
		echo $result['priority_desc'];
		die();
	}

    public function actionIndex()
    {
        $this->layout = "myteam";
        $team_id = Yii::$app->request->get('team_id', 0);
        $team_loc = Yii::$app->request->get('team_loc', 0);
        $session = new Session;
        $session->open();
        $is_accessible_submodul = 0;
        $assigned_name = "";
        $unit_assigned_to = Yii::$app->request->get('unit_assigned_to', 0);
        $unit_name = Yii::$app->request->get('unit_id', 0);
        if ($unit_assigned_to != '' && $unit_assigned_to != 0) {
            $assigned_name = (new User)->getusernamefromid($unit_assigned_to);
        }
        if (!isset($session['is_accessible_submodul'])) {
            $session['is_accessible_submodul'] = (new User)->checkAccess(5.02);
        }
        $is_accessible_submodul = $session['is_accessible_submodul'];
        if (!isset($session['is_accessible_submodul']) || $session['is_accessible_submodul'] == '') {
            $is_accessible_submodul = 0;
        }
				if (Yii::$app->request->isAjax) {
						$this->layout = '';
						Yii::$app->request->queryParams += Yii::$app->request->post();
				}
        $searchModel = new TasksUnitsSearch();
        $pporder = PriorityProject::find()->select(['priority_order'])->where('remove = 0')->orderBy('priority_order asc')->one()->priority_order;
        $params['grid_id']='dynagrid-teamtaskprojects';
		Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $totalCount = $dataProvider->totalCount; // count($dataProviderClone->getModels());
        $data = '';
        //(new ProjectSecurity)->getUsersAssignTransit(0, 0, $team_loc, $team_id);
        $unit_assigned_user_name = $workflow_selected = $client_case_selected = array();
        $params = Yii::$app->request->queryParams;
        $selected_val = Yii::$app->request->queryParams['TasksUnitsSearch']['workflow_task'];
        if ($selected_val != '' && $selected_val != 'All' && !is_array($selected_val)) {
            if (is_numeric($selected_val)) {
                $res = Servicetask::find()->select(['service_task'])->where(['id' => $selected_val])->asArray()->one();
            }/* else{
              $service_loc = explode('_',$selected_val);
              $res = Servicetask::find()->select(['service_task'])->where(['id' => $service_loc[0]])->asArray()->one();
              } */
            $workflow_selected = array($res['service_task'] => $res['service_task']);
        } else if (isset($params['TasksUnitsSearch']['workflow_task']) && !empty($params['TasksUnitsSearch']['workflow_task'])) {
            $service_ids = array();
            foreach ($params['TasksUnitsSearch']['workflow_task'] as $k => $v) {
                if ($v == 'All' || strpos($v, ",") !== false || $v == '') {
                    unset($params['TasksUnitsSearch']['workflow_task']);
                    $workflow_selected = array();
                    $service_ids = array();
                    break;
                } else {
                    $service_loc = explode('_', $v);
                    $service_ids[$service_loc[0]] = $service_loc[0];
                }
            }
            if (!empty($service_ids)) {
                $workflow_selected = ArrayHelper::htmlDecode(ArrayHelper::map(Servicetask::find()->select(['tbl_servicetask.id','tbl_servicetask.teamservice_id','service_task','tbl_teamservice.service_name'])->joinWith(['teamservice'])->where(['tbl_servicetask.id' => $service_ids])->all(), function($model){ return $model->teamservice->service_name.' - '.$model->service_task;},function($model){return $model->teamservice->service_name.' - '.$model->service_task;}));
                //echo "<pre>",print_r($workflow_selected),"</pre>";die;
            }
        }
        if (isset($params['TasksUnitsSearch']['unit_assigned_to']) && !empty($params['TasksUnitsSearch']['unit_assigned_to'])) {
            $additional_arr = array();
            if (!empty($params['TasksUnitsSearch']['unit_assigned_to'])) {
                foreach ($params['TasksUnitsSearch']['unit_assigned_to'] as $k => $v) {
                    if ($v == 'All' || strpos($v, ",") !== false || $v == '') {
                        unset($params['TasksUnitsSearch']['unit_assigned_to']);
                        $unit_assigned_user_name = array();
                        break;
                    }
                    if ($v == 'assignedonly' || $v == 'unassigned') {
                        unset($params['TasksUnitsSearch']['unit_assigned_to'][$k]);
                        if ($v == 'assignedonly')
                            $additional_arr['All Assigned'] = 'All Assigned';
                        if ($v == 'unassigned')
                            $additional_arr['All UnAssigned'] = 'All UnAssigned';
                    }
                }
            }
            if (isset($params['TasksUnitsSearch']['unit_assigned_to'])) {
                $unit_assigned_user_name = array();
                if (!empty($params['TasksUnitsSearch']['unit_assigned_to'])) {
                    $unit_assigned_user_name = ArrayHelper::map(User::find()->select(["id", "CONCAT(usr_first_name, ' ' ,usr_lastname) as usr_first_name"])->where('id in (' . implode(",", $params['TasksUnitsSearch']['unit_assigned_to']) . ')')->orderby('tbl_user.usr_first_name')->all(), 'usr_first_name', 'usr_first_name');
                }
                $unit_assigned_user_name = array_merge($unit_assigned_user_name, $additional_arr);
            }
        }
        /* IRT 96,398 Code Starts */
        if (isset($params['TasksUnitsSearch']['client_case_id']) && !empty($params['TasksUnitsSearch']['client_case_id'])) {
            $client_case_selected = (new User)->getSelectedGridCases($params['TasksUnitsSearch']['client_case_id'], 'All');
            if ($client_case_selected == 'ALL') {
                unset($params['TasksUnitsSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        if (isset($params['TasksUnitsSearch']['client_id']) && !empty($params['TasksUnitsSearch']['client_id'])) {
            $clients_selected = (new User)->getSelectedGridClients($params['TasksUnitsSearch']['client_id'], 'All');
            if ($clients_selected == 'ALL') {
                unset($params['TasksUnitsSearch']['client_id']);
                $clients_selected = array();
            }
        }
        /* IRT 96,398 Code Code Ends */
        /* IRT 67,68,86,87,258 */
        $filter_type = \app\models\User::getFilterType(['tbl_tasks_units.task_id', 'tbl_tasks_units.id', 'tbl_tasks_units.servicetask_id', 'tbl_tasks_units.unit_status', 'tbl_tasks_units.unit_assigned_to', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_priority', 'tbl_task_instruct.project_name', 'tbl_tasks.team_priority', 'tbl_tasks.client_case_id', 'tbl_client_case.client_id'], ['tbl_tasks_units', 'tbl_tasks', 'tbl_task_instruct', 'tbl_client_case']);
        $config = ['unit_status' => ['All' => 'All', '0' => 'Not Started', '7' => 'Pending', '1' => 'Started', '2' => 'Paused', '3' => 'On Hold', '4' => 'Completed', '5' => 'Active', '6' => 'Active Past Due', '8' => 'Incomp ToDos', '9' => 'Random Sampling']];
        $config_widget_options = [
            'unit_assigned_to' => [
                'initValueText' => $unit_assigned_user_name,
                'pluginEvents' => ["select2:select" => 'function(evt) {var abc = evt.params.data.label;$(document).on("pjax:end",   function(xhr, textStatus, options) {$("#select2-tasksunitssearch-unit_assigned_to-container").html(abc); });var id_value = evt.params.data.id;if(id_value != "unassigned" && id_value != "All"){$(".unassignedonly_content").hide();$(".assignedonly_content").show();}else if(id_value == "unassigned"){$(".unassignedonly_content").show();$(".assignedonly_content").hide();}else{$(".assignedonly_content").hide();$(".unassignedonly_content").hide();}}'
                ]
            ],
            'servicetask_id' => [
                'initValueText' => $workflow_selected,
                'pluginEvents' => ["select2:select" => 'function(evt) {var abc = evt.params.data.label;$(document).on("pjax:end",   function(xhr, textStatus, options) {$("#select2-tasksunitssearch-workflow_task-container").html(abc); });}', "select2:open" => 'function(evt) {$("#select2-tasksunitssearch-workflow_task-container").remove(); }'],
                'field_alais' => 'workflow_task',
            ],
            'client_case_id' => [
                'initValueText' => $client_case_selected
            //'field_alais'   =>'client_wise',
            ],
            'client_id' => [
                'initValueText' => $clients_selected
            //'field_alais'   =>'client_wise',
            ]
        ];
        $filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['team-tasks/ajax-filter', 'team_id' => $team_id, 'team_loc' => $team_loc]), $config, $config_widget_options,Yii::$app->request->queryParams, 'team_project');
        /* IRT 67,68,86,87,258 */

        return $this->render('index', ['params'=>$params,'unit_assigned_user_name' => $unit_assigned_user_name, 'filterWidgetOption' => $filterWidgetOption, 'filter_type' => $filter_type, 'dataProvider' => $dataProvider, 'team_id' => $team_id, 'team_loc' => $team_loc, 'searchModel' => $searchModel, 'pporder' => $pporder, 'totalCount' => $totalCount, 'data' => $data, 'userdata' => $userdata, 'is_accessible_submodule_tracktask' => $is_accessible_submodul, 'assigned_name' => $assigned_name, 'workflow_selected' => $workflow_selected]);
    }
    public function actionBulktransition(){
        $team_id = Yii::$app->request->get('team_id', 0);
        $team_loc = Yii::$app->request->get('team_loc', 0);
        $data = (new ProjectSecurity)->getUsersAssignTransit(0, 0, $team_loc, $team_id);

        return $this->renderAjax('bulktransition', ['data' => $data]);
    }
    public function actionBulkassign(){
        $team_id = Yii::$app->request->get('team_id', 0);
        $team_loc = Yii::$app->request->get('team_loc', 0);
        $data = (new ProjectSecurity)->getUsersAssignTransit(0, 0, $team_loc, $team_id);

        return $this->renderAjax('bulkassign', ['data' => $data]);
    }
    public function actionGetloadtaskgriddetails() {
      $postdata=Yii::$app->request->post();
      $team_id = Yii::$app->request->post('team_id');
      $team_loc = Yii::$app->request->post('team_loc');
      $id=$postdata['models'][$postdata['expandRowInd']]['id'];
      return $this->renderPartial('getloadtaskgriddetails', ['id'=>$id,'team_id'=>$team_id,'team_loc'=>$team_loc]);
    }
    /*
     * For Ajax Filter of TeamTasks Grid
     * */
    public function actionAjaxFilter(){
		$team_id = Yii::$app->request->get('team_id');
		$team_loc = Yii::$app->request->get('team_loc');
        $bodyparams=Yii::$app->request->bodyParams;
        Yii::$app->request->queryParams +=$bodyparams;
        //echo "<pre>",print_r($bodyparams),"</prE>";
        //die;
		$searchModel = new TasksUnitsSearch();
		$qparams = Yii::$app->request->queryParams;
		$params = array();
		//$dataProvider = $searchModel->searchFilter($qparams,$params);
		$params = array_merge($qparams, Yii::$app->request->bodyParams,$params);
	    $dataProvider = $searchModel->searchFilter($params);
	    if($params['field'] == 'unit_assigned_to' || $params['field'] == 'workflow_task' || $params['field'] == 'client_id' || $params['field'] == 'client_case_id' || $params['field'] == 'task_id') {
			foreach ($dataProvider as $key=>$val) {
				$out['results'][] = ['id' => $key, 'text' => Html::decode($val),'label' => Html::decode($val)];
			}
		} else {
			foreach ($dataProvider as $key=>$val) {
				$out['results'][] = ['id' => $val, 'text' => Html::decode($val),'label' => Html::decode($val)];
			}
		}
	    return json_encode($out);
	}

	/*
	 * For Expand data of TeamTasks Grid
	 **/
	 public function actionGetloadtasksdeatails() {
		$team_id = Yii::$app->request->get('team_id');
		$team_loc = Yii::$app->request->get('team_loc');
		$expandRowKey = Yii::$app->request->post('expandRowKey',0);
		$params = Yii::$app->request->post();
		$load_task_data = (new TasksUnitsSearch)->search($params);
		$models = $load_task_data->getModels();
		$task_unit_id = $models[$expandRowKey]['id'];
		$tasktodo_info = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id','tbl_tasks_units_todos.complete','tbl_tasks_units_todos.todo','tbl_tasks_units_todos.todo_cat_id','tbl_tasks_units_todos.assigned',"concat(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as assigned_user"])->join('left join','tbl_user','tbl_user.id=tbl_tasks_units_todos.assigned')->where(['tasks_unit_id'=>$task_unit_id])->orderBy('tbl_tasks_units_todos.modified desc')->all();
		//echo "<pre>"; print_r($tasktodo_info); exit;
		return $this->renderPartial('getloadtaskgriddetails',['team_id'=>$team_id,'team_loc'=>$team_loc,'todoinfo'=>$tasktodo_info]);
	 }
	 /*
	  * Bulk Complete Tasks For TeamTasks
	  * */
	 public function actionChkcancompletetasks()
	 {
        $team_id = Yii::$app->request->post('team_id');
        $team_loc = Yii::$app->request->post('team_loc');
        $userId = Yii::$app->user->identity->id;
        $taskunitIds = Yii::$app->request->post('taskunitIds');
        $type = Yii::$app->request->post('type', 'selected');
        $error = "";
        $error2 = "";
        $tasksdata = "";
        if ($type == 'selected') {
            $unassignunit = TasksUnits::find()->where('tbl_tasks_units.id IN (' . $taskunitIds . ')')->all();
        } else if ($type == 'all') {
            $postdata = Yii::$app->request->post();
            $postdata['data_mode'] = 'bulk_complete_tasks';
            $postdata['export']    = 'export';
            $searchModel = new TasksUnitsSearch();
            $dataProvider = $searchModel->search($postdata);
            if (!empty($dataProvider)) {
                $newData = [];
                foreach ($dataProvider as $single) {
                    $newData[] = $single['id'];
                }
                $unassignunit = TasksUnits::find()->where('tbl_tasks_units.id IN (' . implode(',', $newData) . ')')->all();
            }
        }
       // echo "<prE>",print_r($unassignunit),"</prE>";die;
        $task_ids=[];
        if (!empty($unassignunit)) {
            foreach ($unassignunit as $taskunit) {
                //if($taskunit->unit_status == 4 && $error2=="") {
                //	$error = "-One or more of the selected Tasks is in 'Complete' Task Status, so it cannot be Completed\n";
                //	break;
                //}else{
                $task_info = $taskunit->tasks;
                $tasklist = $taskunit->task_id;
                $task_ids[$taskunit->task_id]=$taskunit->task_id;
                //if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0 && $taskunit->unit_status==0){
                //	$error = 'The selected Task cannot be Completed unless the task has been Assigned, On Hold, Started or Paused.';
                //	break;
                //} else {
                $clientId = $task_info->clientCase->client_id;
                $caseId = $task_info->client_case_id;
                // $service_info = Servicetask::findOne($taskunit->servicetask_id);
                // echo "<pre>",print_r($service_info);
                $team_id = $taskunit->team_id;
                $team_loc = $taskunit->team_loc;
                /* if($service_info->billable_item == 2 && $service_info->force_entry == 1){
                  $pricepointexist = (new Pricing)->chkPricePointExistByClientCaseTeam($clientId,$caseId,$team_id,$servicetask_id,$team_loc);
                  if(!empty($pricepointexist)){
                  $billabledata = TasksUnitsBilling::find()->where('task_id = '.$taskunit->task_id.' AND  tasks_unit_id = '.$taskunit->id)->count();
                  if($billabledata == 0){
                  $error = "The selected Tasks cannot be Completed unless one or more Billable Items are added to the Task";
                  break;
                  }
                  }
                  }
                  $todoCount = TasksUnitsTodos::find()->where("task_id = ".$taskunit->task_id." AND tasks_unit_id = ".$taskunit->id." AND complete = 0")->count();
                  if($todoCount){
                  $error = "In Order to Complete Service Task, Please Complete All Todo Items";
                  break;
                  } */

                $taskunit->unit_assigned_to = Yii::$app->user->identity->id;
                $taskunit->unit_status = 4;
                $taskunit->duration = date('Y-m-d H:i:s');
                $taskunit->unit_complete_date = date('Y-m-d H:i:s');
                $taskunit->bulk_complete = 1;
               //$taskunit->unit_assigned_to = $userId;
//                    echo '<pre>';print_r($taskunit);die;
                $taskunit->save(false);
                $activity_name = $taskunit->task_id . "|project#:" . $taskunit->task_id . '|unit#' . $taskunit->id;
                (new ActivityLog)->generateLog('Project', 'CompletedTask', $taskunit->task_id, $activity_name);
                $duration = "0 days 0 hours 0 min";
                (new TasksUnitsTransactionLog)->generateLog($taskunit->task_id, $taskunit->id, $taskunit->unit_assigned_to, 4, $duration);
                (new Tasks)->setProjectTasksStatus($taskunit->task_id, $taskunit->id);
                $query = TasksUnits::find()->where('teamservice_id =' . $taskunit->teamservice_id . ' AND task_id=' . $taskunit->task_id);
                $teambyservicetask_count = $query->count();
                $completedteambyservicetask_count = TasksUnits::find()->where('unit_status=4 AND teamservice_id =' . $taskunit->teamservice_id . ' AND task_id=' . $taskunit->task_id . ' ')->count();

                if ($teambyservicetask_count == $completedteambyservicetask_count) {
                    $settingsEmail = SettingsEmail::find()->select('email_teamservice')->where('id=20')->one();
                    //$task_info = Tasks::findOne($taskunit->task_id);
                    if (isset($settingsEmail->email_teamservice) && $settingsEmail->email_teamservice != "") {
                        if (is_numeric($settingsEmail->email_teamservice) && $settingsEmail->email_teamservice == $taskunit->teamservice_id) {
                            //(new SettingsEmail)->sendEmail
                            EmailCron::saveBackgroundEmail(5, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $taskunit->task_id, 'teamservice' => $taskunit->teamservice_id, 'teamservice_id' => $taskunit->teamservice_id));
                        } else {
                            $services = explode(",", $settingsEmail->email_teamservice);
                            if (in_array($service_info->teamservice_id, $services)) {
                                //(new SettingsEmail)->sendEmail
                                EmailCron::saveBackgroundEmail(5, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $taskunit->task_id, 'teamservice' => $taskunit->teamservice_id, 'teamservice_id' => $taskunit->teamservice_id));
                            }
                        }
                    } else {
                        //(new SettingsEmail)->sendEmail
                        EmailCron::saveBackgroundEmail(5, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $taskunit->task_id, 'teamservice' => $taskunit->teamservice_id, 'teamservice_id' => $taskunit->teamservice_id));
                    }
                }
                $task_all = TasksUnits::find()->where('task_id = ' . $taskunit->task_id)->count();
                $task_complete = TasksUnits::find()->where('task_id = ' . $taskunit->task_id . ' AND unit_status = 4')->count();
                if ($task_all == $task_complete) {
                    //(new SettingsEmail)->sendEmail
                    EmailCron::saveBackgroundEmail(5, 'is_sub_com_task', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $taskunit->task_id));
                    $delete_pastdue_sql="DELETE FROM tbl_project_pastdue WHERE task_id IN (".$taskunit->task_id.")";
                    Yii::$app->db->createCommand($delete_pastdue_sql)->execute();
                }
                $final = 'OK';
                //}
                //	}
            }
        }
        echo json_encode(array("error" => $error, 'finalresult' => $final));
        die;
    }
    /*
     * Core Common Function for selected and bulk  to assign tasks.
     */
    private function coreAssignTasks($params, $assignnumber, $taskunitIds) {
        //echo "<pre> in coreAssignTasks function",print_r($params),print_r($assignnumber),print_r($taskunitIds),"</prE>";die;
        foreach ($params['chkusers'] as $userId) {
            if (empty($taskunitIds)) {
                break;
            } else {
                $taskunitIds = array_values($taskunitIds);
                //print_r($taskunitIds);
                for ($i = 0; $i < $assignnumber; $i++) {
                    if (!isset($taskunitIds[$i])) {
                        continue;
                    } else {

                        $taskunitId = $taskunitIds[$i];
                        unset($taskunitIds[$i]);
                        $unitdata = TasksUnits::findOne($taskunitId);
                        $unitdata->unit_assigned_to = $userId;
                        $unitdata->save(false);
                        $task_info = $unitdata->tasks;
                        EmailCron::saveBackgroundEmail(13,'is_sub_self_assign',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_info->id,'task_unit_id'=>$taskunitId));
                        $tododata = TasksUnitsTodos::find()->where('tbl_tasks_units_todos.tasks_unit_id = ' . $taskunitId . ' AND tbl_tasks_units_todos.complete = 0')->all();

                        if (!empty($tododata)) {
                            foreach ($tododata as $todo_data) {
                                $mbl_TasksUnitsTodos = TasksUnitsTodos::findOne($todo_data->id);
                                $mbl_TasksUnitsTodos->assigned = $userId;
                                $mbl_TasksUnitsTodos->save(false);
                                $duration = "0 days 0 hours 0 min";
                                if (isset($unitdata->id) && $unitdata->id != 0) {
                                    $activity_name = $mbl_TasksUnitsTodos->todo;
                                    if ($unitdata->unit_assigned_to == 0) {
                                        (new TasksUnitsTodoTransactionLog)->generateLog($todo_data->id, $task_info->id, $unitdata->id, $userId, 7, $duration);
                                        (new ActivityLog)->generateLog('ToDo', 'Assigned', $todo_data->id, $task_info->id);
                                    } else {
                                        (new TasksUnitsTodoTransactionLog)->generateLog($todo_data->id, $task_info->id, $unitdata->id, $userId, 8, $duration);
                                        (new ActivityLog)->generateLog('ToDo', 'Transition', $todo_data->id, $task_info->id);
                                    }
                                }
                                $actLogactivity_name = $task_info->id . "|project#:" . $task_info->id . "|unit#:" . $unitdata->id;
                                (new TasksUnitsTransactionLog)->generateLog($task_info->id, $unitdata->id, $userId, 5, $duration);
                                (new ActivityLog)->generateLog('Project', 'AssignedTask', $task_info->id, $actLogactivity_name);
                            }
                        }
                    }
                }
            }
        }
       //echo "<pre> in coreAssignTasks function",print_r($params),print_r($assignnumber),print_r($taskunitIds),"</prE>";die;
    }

    /*
	  * Bulk AssignTask
	  * */
	 public function actionChkcanassigntasks() {
	 	$team_id = Yii::$app->request->post('team_id');
		$team_loc = Yii::$app->request->post('team_loc');
		$params = Yii::$app->request->post();
        $params['chkusers'] = explode(',',$params['user_id']);
        $countofassignee = isset($params['chkusers']) && !empty($params['chkusers'])?count($params['chkusers']):"";
                if($params['type'] == 'bulkall'){
                    // In Bulk All Transition task
                    $postdata = Yii::$app->request->post();
                    $postdata['data_mode'] = 'bulkAllTasks';
                    $postdata['export']    = 'export';
                    //echo "<pre>",print_r($postdata),"</pre>";
                    $searchModel = new TasksUnitsSearch();
                    $data_result = $searchModel->search($postdata);
                    if(gettype($data_result) == 'object'){
                        $dataResult = ArrayHelper::map($data_result->getModels(),'id','id');
                    }else{
                        $dataResult = ArrayHelper::map($data_result,'id','id');
                    }
                    //echo "<prE>",print_r($dataResult),"</pre>";die;
                    $countofservicetask = count($dataResult);
                    $assignnumber = ceil($countofservicetask / $countofassignee);
                    if($countofassignee > $countofservicetask){
                        $error = 'The number of Users selected exceeds the number of Tasks to Assign. Please reduce the number of Users to complete perform a Bulk Assign Process.';
                        echo json_encode(array("error"=>$error));
                        die;
                    }
                    //echo "<pre>",print_r($params),$assignnumber,"tunitids",print_r($dataResult),"</pre>";
                    //die;
                    $this->coreAssignTasks($params,$assignnumber,$dataResult);
                }else{
                    $taskunitIds = explode(',',Yii::$app->request->post('taskunitIds'));
                    $taskunit_ids = Yii::$app->request->post('taskunitIds');
                    $assignunits = TasksUnits::find()->where('tbl_tasks_units.unit_assigned_to != 0 AND tbl_tasks_units.id IN ('.$taskunit_ids.')')->asArray()->all();
                    if(!empty($assignunits)){
                            $error = "-One or more of the selected Tasks is already Assigned to a User.\n -Please only select UnAssigned Tasks to perform a Bulk Assign Process.. \n";
                            echo json_encode(array("error"=>$error));	die;
                    }
                    $countofservicetask = count($taskunitIds);
                    $assignnumber = ceil($countofservicetask/$countofassignee);
                    $this->coreAssignTasks($params,$assignnumber,$taskunitIds);
                }
		echo json_encode(array("error"=>'','success'=>'success'));
                die;
	 }
    /*
     * Bulk Transition Tasks Common Call
     * */
    private function coreTransitionTasks($params,$assignnumber,$taskunitIds){
        if(!empty($params['chkusers'])){
        foreach ($params['chkusers'] as $userId) {
            if (empty($taskunitIds)) {
                break;
            } else {
                $taskunitIds = array_values($taskunitIds);
                for ($i = 0; $i < $assignnumber; $i++) {
                    if (!isset($taskunitIds[$i])) {
                        break;
                    } else {
                        $taskunitId = $taskunitIds[$i];
                        unset($taskunitIds[$i]);
                        $unitdata = TasksUnits::findOne($taskunitId);
                        $unitdata->unit_assigned_to = $userId;
                        $unitdata->is_transition = 1;
                        $unitdata->save(false);
                        $task_info = $unitdata->taskInstruct->tasks;
                        $last_assigntask = TasksUnitsTransactionLog::find()->innerJoinWith('tasksUnits')->where('tbl_tasks_units_transaction_log.tasks_unit_id = ' . $taskunitId . ' AND tbl_tasks_units.task_id = ' . $unitdata->task_id)->orderby('tbl_tasks_units_transaction_log.id DESC')->one();
                        if (!empty($last_assigntask)) {
                            $currtime = time();
                            $trans_date = strtotime($last_assigntask->transaction_date);
                            $diff = abs($currtime - $trans_date);
                            $days = intval((floor($diff / 86400)));
                            $hours = intval((floor($diff / 3600)));
                            $hours = $hours % 24;
                            $minutes = intval((floor($diff / 60)));
                            $minutes = $minutes % 60;
                            $duration = $days . " days " . $hours . " hours " . $minutes . " min";
                            TasksUnitsTransactionLog::updateAll(['current_time' => $currtime, 'duration' => $duration], 'tbl_tasks_units_transaction_log.id = ' . $last_assigntask->id);
                        }
                        $tododata = TasksUnitsTodos::find()->where('tbl_tasks_units_todos.tasks_unit_id = ' . $taskunitId . ' AND tbl_tasks_units_todos.complete = 0')->all();
                        if (!empty($tododata)) {
                            foreach ($tododata as $todo_data) {
                                $mbl_TasksUnitsTodos = TasksUnitsTodos::findOne($todo_data->id);
                                $mbl_TasksUnitsTodos->assigned = $userId;
                                $mbl_TasksUnitsTodos->save(false);
                                $last_assigntodo = TasksUnitsTodoTransactionLog::find()->innerJoinWith(['tasksUnitsTodos'])->where('tbl_tasks_units_todo_transaction_log.todo_id = ' . $todo_data->id . ' AND tbl_tasks_units_todos.tasks_unit_id = ' . $unitdata->id)->orderby('tbl_tasks_units_todo_transaction_log.id DESC')->one();
                                if (!empty($last_assigntodo)) {
                                    $currtime = time();
                                    $trans_date = strtotime($last_assigntodo->transaction_date);
                                    $diff = abs($currtime - $trans_date);
                                    $days = intval((floor($diff / 86400)));
                                    $hours = intval((floor($diff / 3600)));
                                    $hours = $hours % 24;
                                    $minutes = intval((floor($diff / 60)));
                                    $minutes = $minutes % 60;
                                    $duration = $days . " days " . $hours . " hours " . $minutes . " min";
                                    TasksUnitsTodoTransactionLog::updateAll(['current_time' => $currtime, 'duration' => $duration], 'tbl_tasks_units_todo_transaction_log.id = ' . $last_assigntodo->id);
                                }
                                $duration = "0 days 0 hours 0 min";
                                if (isset($unitdata->id) && $unitdata->id != 0) {
                                    $activity_name = $mbl_TasksUnitsTodos->todo;
                                    if ($unitdata->unit_assigned_to == 0) {
                                        (new TasksUnitsTodoTransactionLog)->generateLog($todo_data->id, $unitdata->task_id, $unitdata->id, $userId, 7, $duration);
                                        (new ActivityLog)->generateLog('ToDo', 'Assigned', $todo_data->id, $unitdata->task_id);
                                    } else {
                                        (new TasksUnitsTodoTransactionLog)->generateLog($todo_data->id, $unitdata->task_id, $unitdata->id, $userId, 8, $duration);
                                        (new ActivityLog)->generateLog('ToDo', 'Transition', $todo_data->id, $unitdata->task_id);
                                    }
                                }
                                $actLogactivity_name = $unitdata->task_id . "|project#:" . $unitdata->task_id . "|unit#:" . $unitdata->id;
                                (new TasksUnitsTransactionLog)->generateLog($unitdata->task_id, $unitdata->id, $userId, 5, $duration);
                                (new ActivityLog)->generateLog('Project', 'AssignedTask', $unitdata->task_id, $actLogactivity_name);
                            }
                        }
                    }
                }
            }
        }
        }
    }
    /*
     * Bulk Transition Tasks
     * */
    public function actionChkcantransitiontasks()
    {
        $team_id = Yii::$app->request->post('team_id');
        $team_loc = Yii::$app->request->post('team_loc');
        $params = Yii::$app->request->post();
        $params['chkusers'] = explode(',', $params['user_id']);
        $countofassignee = isset($params['chkusers']) && !empty($params['chkusers']) ? count($params['chkusers']) : "";
        /* Modified for bulk all transitions */
        if($params['type'] == 'bulkall'){
            // In Bulk All Transition task
            $postdata = Yii::$app->request->post();
            $postdata['data_mode'] = 'bulkAllTasks';
            $postdata['export']    = 'export';
            $searchModel = new TasksUnitsSearch();
            $dataResult = ArrayHelper::map($searchModel->search($postdata),'id','id');
            $countofservicetask = count($dataResult);
            if($countofassignee > $countofservicetask){
                $error = 'The number of Users selected exceeds the number of Tasks to Assign. Please reduce the number of Users to complete perform a Bulk Assign Process.';
                echo json_encode(array("error"=>$error));
                die;
            }
            $assignnumber = ceil($countofservicetask / $countofassignee);
            $this->coreTransitionTasks($params,$assignnumber,$dataResult);
        }else{
            // In selection mode
            $taskunitIds = explode(',', Yii::$app->request->post('taskunitIds'));
            $taskunit_ids = Yii::$app->request->post('taskunitIds');
            $unassignunit = TasksUnits::find()->where('tbl_tasks_units.id IN (' . $taskunit_ids . ') AND (tbl_tasks_units.unit_assigned_to = 0 OR tbl_tasks_units.unit_status = 4)')->all();
            $error = "";
            $error2 = "";
            if (!empty($unassignunit)) {
                foreach ($unassignunit as $unit) {
                    if ($unit->unit_assigned_to == 0 && $error == "") {
                        $error = "-One or more of the selected Tasks is not yet Assigned to a User.\n Please only select Assigned Tasks to perform a Bulk Transition Process. \n";
                        echo json_encode(array("error" => $error));
                        die;
                    }
                    if ($unit->unit_status == 4 && $error2 == "") {
                        $error2 = "-One or more of the selected Tasks is in 'Complete' Task Status, so it cannot be Transitioned to another user. \n ";
                        echo json_encode(array("error" => $error2));
                        die;
                    }
                }
            }
            $countofservicetask = count($taskunitIds);
            $assignnumber = ceil($countofservicetask / $countofassignee);
            $this->coreTransitionTasks($params,$assignnumber,$taskunitIds);
        }
        echo json_encode(array("error" => '', 'success' => 'success'));
        die;
    }
    /*
     * Core Common Task For Bulk unassign
     */
    private function coreBulkUnassign($taskunitIds) {
        if(!empty($taskunitIds)){
            foreach ($taskunitIds as $taskUnitId) {
                $task_unit_data = TasksUnits::findOne($taskUnitId);
                $task_info = $task_unit_data->taskInstruct->tasks;
                $taskId = $task_info->id;
                $last_assigntask = TasksUnitsTransactionLog::find()->innerJoinWith('tasksUnits')->where('tbl_tasks_units_transaction_log.tasks_unit_id = ' . $taskUnitId . ' AND tbl_tasks_units.task_id = ' . $taskId)->orderby('tbl_tasks_units_transaction_log.id DESC')->one();
                if (!empty($last_assigntask)) {
                    $currtime = time();
                    $trans_date = strtotime($last_assigntask->transaction_date);
                    $diff = abs($currtime - $trans_date);
                    $days = intval((floor($diff / 86400)));
                    $hours = intval((floor($diff / 3600)));
                    $hours = $hours % 24;
                    $minutes = intval((floor($diff / 60)));
                    $minutes = $minutes % 60;
                    $duration = $days . " days " . $hours . " hours " . $minutes . " min";
                    TasksUnitsTransactionLog::updateAll(['current_time' => $currtime, 'duration' => $duration], 'tbl_tasks_units_transaction_log.id = ' . $last_assigntask->id);
                }
                $taskUnitTodos = TasksUnitsTodos::find()->where('tbl_tasks_units_todos.tasks_unit_id = ' . $taskUnitId . ' AND tbl_tasks_units_todos.complete = 0 AND tbl_tasks_units_todos.assigned != 0')->all();
                $tododuration = "0 days 0 hours 0 min";
                if (!empty($taskUnitTodos)) {
                    foreach ($taskUnitTodos as $todos) {
                        $todoModel = TasksUnitsTodos::findOne($todos->id);
                        $todoModel->assigned = 0;
                        $todoModel->save(false);
                        $last_assigntodo = TasksUnitsTodoTransactionLog::find()->innerJoinWith(['tasksUnitsTodos'])->where('tbl_tasks_units_todo_transaction_log.todo_id = ' . $todos->id . ' AND tbl_tasks_units_todos.tasks_unit_id = ' . $taskUnitId)->orderby('tbl_tasks_units_todo_transaction_log.id DESC')->one();
                        if (!empty($last_assigntodo)) {
                            $currtime = time();
                            $trans_date = strtotime($last_assigntodo->transaction_date);
                            $diff = abs($currtime - $trans_date);
                            $days = intval((floor($diff / 86400)));
                            $hours = intval((floor($diff / 3600)));
                            $hours = $hours % 24;
                            $minutes = intval((floor($diff / 60)));
                            $minutes = $minutes % 60;
                            $duration = $days . " days " . $hours . " hours " . $minutes . " min";
                            TasksUnitsTodoTransactionLog::updateAll(['current_time' => $currtime, 'duration' => $duration], 'tbl_tasks_units_todo_transaction_log.id = ' . $last_assigntodo->id);
                        }
                        (new TasksUnitsTodoTransactionLog)->generateLog($todos->id, $taskId, $taskUnitId, 0, 11, $tododuration);
                        (new ActivityLog)->generateLog('ToDo', 'Transition', $todos->id, $taskId);
                    }
                }

                (new TasksUnitsTransactionLog)->generateLog($taskId, $taskUnitId, 0, 12, $tododuration);
                //$task_unit_data->id = $taskUnitId;
                EmailCron::saveBackgroundEmail(14, 'is_unassign', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $taskId, 'task_unit_id' => $taskUnitId,'current_assigned'=>$task_unit_data->unit_assigned_to));
                $task_unit_data->is_transition = '0';
                if ($task_unit_data->unit_status != 0) {
                    $task_unit_data->unit_status = '2';
                }
                //if($task_unit_data->unit_assigned_to == NULL)
                $task_unit_data->unit_assigned_to = 0;
                $task_unit_data->save(false);

                $actLogactivity_name = $taskId . "|project#:" . $taskId . "|unit#:" . $taskUnitId;
                (new ActivityLog)->generateLog('Project', 'UnAssigned', $taskId, $actLogactivity_name);
            }
        }
    }
         /*
	   * Bulk Unassign Tasks For Team Tasks
	   * */
        public function actionChkcanunassigntasks()
        {
            $team_id = Yii::$app->request->post('team_id');
            $team_loc = Yii::$app->request->post('team_loc');
            $taskunitIds = explode(',', Yii::$app->request->post('taskunitIds'));
            $params = Yii::$app->request->post();
            $taskunit_ids = Yii::$app->request->post('taskunitIds');
            $type = Yii::$app->request->post('type', 'selected');
            if (!empty($taskunitIds) && $type == 'selected') {
                $unassignunit = TasksUnits::find()->where('tbl_tasks_units.id IN (' . $taskunit_ids . ') AND (tbl_tasks_units.unit_assigned_to = 0 OR tbl_tasks_units.unit_status = 4)')->all();

                $error = "";
                $error2 = "";
                if (!empty($unassignunit)) {
                    foreach ($unassignunit as $unit) {
                        if ($unit->unit_assigned_to == 0 && $error == "") {
                            $error = "-One or more of the selected Tasks is not yet Assigned to a User.\n Please only select Assigned Tasks to perform a Bulk Transition Process. \n";
                            echo json_encode(array("error" => $error));
                            die;
                        }
                        if ($unit->unit_status == 4 && $error2 == "") {
                            $error2 = "-One or more of the selected Tasks is in 'Complete' Task Status, so it cannot be Transitioned to another user. \n ";
                            echo json_encode(array("error" => $error2));
                            die;
                        }
                    }
                }
                $this->coreBulkUnassign($taskunitIds);
            }else if( $type == 'bulkall'){
                // In Bulk All Transition task
                $postdata = Yii::$app->request->post();
                $postdata['data_mode'] = 'bulkAllTasks';
                $postdata['export']    = 'export';
                $searchModel = new TasksUnitsSearch();
                $dataResult = ArrayHelper::map($searchModel->search($postdata),'id','id');
                //echo "<pre>",print_r($dataResult),"</pre>";die;
                $this->coreBulkUnassign($dataResult);
            }
        echo json_encode(array("error"=>$error, 'success'=>'success'));	die;
	  }
	  /*
	   * Bulk Transfer Tasks For Team Tasks
	   * */
        public function actionChkbulktransfertasks() {
          $team_id = Yii::$app->request->post('team_id');
          $team_loc = Yii::$app->request->post('team_loc');
          $taskunitIds = explode(',', Yii::$app->request->post('taskunitIds'));
          $params = Yii::$app->request->post();
          $taskunit_ids = Yii::$app->request->post('taskunitIds');
          $type = Yii::$app->request->post('type', 'selected');
          $error = "";
          if (!empty($taskunitIds) && $type == 'selected') {
              foreach ($taskunitIds as $taskunit_id) {
                  if (isset($taskunit_id) && is_numeric($taskunit_id)) {
                      $duration = "0 days 0 hours 0 min";
                      $location = Yii::$app->request->post('loc', 0);
                      $taskunit = TasksUnits::findOne($taskunit_id);
                      $taskunit->unit_assigned_to = 0;
                      $taskunit->unit_status = 2;
                      $taskunit->save(false);
                      $taskInstructServicetask = $taskunit->taskInstructServicetask;
                      $previous_loc = $taskunit->team_loc;
                      $teamservice_id = $taskunit->teamservice_id;
                      $instruction_team_id = $taskunit->team_id;
                      $task_id = $taskunit->task_id;
                      TasksTeams::updateAll(['team_loc' => $location], 'task_id = ' . $task_id . ' AND team_id = ' . $instruction_team_id);
                      $taskInstructServicetask->team_loc = $location;
                      $taskunit->team_loc = $location;
                      $taskunit->save(false);
                      $taskInstructServicetask->save(false);
                      $task_info = Tasks::findOne($task_id);
                      if ($task_info->task_status == 4) {
                          $task_info->task_status = 1;
                          $task_info->save(false);
                      }
                      $activity_name = $task_id . "|project#:" . $task_id . "|unit#" . $taskunit_id;
                      (new ActivityLog)->generateLog('Project', 'Transferred', $task_id, $activity_name);
                      (new TasksUnitsTransactionLog)->generateLog($task_id, $taskunit_id, $taskunit->unit_assigned_to, 10, $duration);
                      (new TasksUnitsTodos)->todoTransition($task_id, $taskunit_id);

                      /* Sending Transfer Task Location  Email */
                      //SettingsEmail::sendEmail
                      EmailCron::saveBackgroundEmail(22, 'is_servicetask_transists', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'previous_tl' => $previous_loc, 'tl' => $location, 'servicetask_id' => $servicetask_id, 'task_unit_id' => $taskunit_id));
                      /* Sending Transfer Task Location  Email */
                  }
              }
          } else if ($type == 'bulkall') {
              $postdata = Yii::$app->request->post();
              $searchModel = new TasksUnitsSearch();
              $postdata['data_mode'] = 'bulkAllTasks';
              $postdata['export']    = 'export';
              $dataProvider = $searchModel->search($postdata);
              if (!empty($dataProvider)) {
                  foreach ($dataProvider as $taskunitdata) {
                      if (isset($taskunitdata['id']) && is_numeric($taskunitdata['id'])) {
                          $taskunit_id = $taskunitdata['id'];
                          $duration = "0 days 0 hours 0 min";
                          $location = Yii::$app->request->post('loc', 0);
                          $taskunit = TasksUnits::findOne($taskunit_id);
                          $taskunit->unit_assigned_to = 0;
                          $taskunit->unit_status = 2;
                          $taskunit->save(false);
                          $taskInstructServicetask = $taskunit->taskInstructServicetask;
                          $previous_loc = $taskunit->team_loc;
                          $teamservice_id = $taskunit->teamservice_id;
                          $instruction_team_id = $taskunit->team_id;
                          $task_id = $taskunit->task_id;
                          TasksTeams::updateAll(['team_loc' => $location], 'task_id = ' . $task_id . ' AND team_id = ' . $instruction_team_id);
                          $taskInstructServicetask->team_loc = $location;
                          $taskunit->team_loc = $location;
                          $taskunit->save(false);
                          $taskInstructServicetask->save(false);
                          $task_info = Tasks::findOne($task_id);
                          if ($task_info->task_status == 4) {
                              $task_info->task_status = 1;
                              $task_info->save(false);
                          }
                          $activity_name = $task_id . "|project#:" . $task_id . "|unit#" . $taskunit_id;
                          (new ActivityLog)->generateLog('Project', 'Transferred', $task_id, $activity_name);
                          (new TasksUnitsTransactionLog)->generateLog($task_id, $taskunit_id, $taskunit->unit_assigned_to, 10, $duration);
                          (new TasksUnitsTodos)->todoTransition($task_id, $taskunit_id);

                          /* Sending Transfer Task Location  Email */
                          //SettingsEmail::sendEmail
                          EmailCron::saveBackgroundEmail(22, 'is_servicetask_transists', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'previous_tl' => $previous_loc, 'tl' => $location, 'servicetask_id' => $servicetask_id, 'task_unit_id' => $taskunit_id));
                          /* Sending Transfer Task Location  Email */
                      }
                  }
              }
              //echo "<pre>",print_r($postdata),print_r($model),"</pre>";die;
          }

          echo json_encode(array("error" => $error, 'success' => 'success'));
          die;
      }

      public function actionChklocation(){
		  $team_id = Yii::$app->request->post('team_id');
		  $service_location = Yii::$app->request->post('service_location');
		  $service_location = explode("_",$service_location);
		  $sql="SELECT team_loc FROM tbl_servicetask_team_locs WHERE servicetask_id =".$service_location[0];
		  $userId = Yii::$app->user->identity->id;
		  $roleId = Yii::$app->user->identity->role_id;
		  if($roleId !='0'){
			  $sql="SELECT tbl_servicetask_team_locs.team_loc FROM tbl_servicetask_team_locs INNER JOIN tbl_project_security on tbl_project_security.team_loc=tbl_servicetask_team_locs.team_loc and tbl_project_security.team_id=$team_id and tbl_project_security.user_id=$userId WHERE servicetask_id =".$service_location[0];
		  }
    	  $teamLocation=TeamlocationMaster::find()->where('remove=0 and id IN ('.$sql.')')->count();
		  if($teamLocation > 1){
			  return 'OK';
		  }
		  return false;
	  }
	  public function actionBulktransferlocation(){
		  $team_id  = Yii::$app->request->get('team_id');
		  $team_loc = Yii::$app->request->get('team_loc');
		  $service_location = Yii::$app->request->post('service_location');
		  $service_location = explode("_",$service_location);
		  $sql="SELECT team_loc FROM tbl_servicetask_team_locs WHERE servicetask_id =".$service_location[0];
		  $userId = Yii::$app->user->identity->id;
		  $roleId = Yii::$app->user->identity->role_id;
		  if($roleId !='0'){
			  $sql="SELECT tbl_servicetask_team_locs.team_loc FROM tbl_servicetask_team_locs INNER JOIN tbl_project_security on tbl_project_security.team_loc=tbl_servicetask_team_locs.team_loc and tbl_project_security.team_id=$team_id and tbl_project_security.user_id=$userId WHERE servicetask_id =".$service_location[0];
		  }
    	  $teamLocation=ArrayHelper::map(TeamlocationMaster::find()->orderBy('id')->where('remove=0 and id IN ('.$sql.') and id !='.$team_loc)->all(),'id','team_location_name');
		  return $this->renderAjax('bulktransferlocation',[
			  	'team_id'=>$team_id,
		  		'team_loc'=>$team_loc,
		  		'service_location'=>$service_location,
		  		'teamLocation'=>$teamLocation
		  ]);
	  }
}
