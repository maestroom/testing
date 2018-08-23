<?php

namespace app\controllers;

use Yii;
use app\models\Unit;
use app\models\Tasks;
use app\models\Client;
use app\models\Options;
use app\models\Report;
use app\models\TaskInstruct;
use app\models\search\UnitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\ClientCase;
use app\models\SavedFilters;
use app\models\TasksTeams;
use app\models\Team;
use app\models\Teamservice;
use app\models\TasksUnitsTodos;
use app\models\TeamlocationMaster;
use app\models\TodoCats;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use PHPExcel;
use PHPExcel_IOFactory;

/**
 * StatusReportController implements the CRUD actions for  model.
 */
class StatusReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Return the Layout of Requestbyclientcase Report.
     */
    public function actionRequestbyclientcase(){
	
		$this->layout = 'report';
		$model = new Report;
		$filter_data = Yii::$app->request->post('filtervalue');
        if (isset($filter_data) && !empty($filter_data)){
            $filter_data = json_decode($_REQUEST['filtervalue']);
		}
		return $this->render('requestbyclientcase', array("filter_data" => $filter_data,'model'=>$model));
	}
	
	/*
	 *  Return the All client Or All Clientcase data.
	 * */
	public function actionGetrevenuecriteria(){
		$filter_data = json_decode(Yii::$app->request->post('filter_data'), true);
		$criteria = Yii::$app->request->post('criteria');
		$clientli = "";
		$clientcaseli = ""; 
		$clientList = Client::find()->select(['id','client_name'])->orderBy('client_name asc')->all();
		 
		if (isset($criteria) && $criteria == "client") {
            foreach ($clientList as $key => $client) {
                $checked = "";
                if (!empty($filter_data['client']) && in_array($client->id, $filter_data['client']))
                    $checked = 'checked="checked"';
                
                $clientli .= '<li class="custom-full-width"><input type="checkbox" class="client tutmbs" name="Report[client][]" ' . $checked . ' id="client_' . $client->id . '" value="' . $client->id . '" ><label for="client_' . $client->id . '">' .  $client->client_name . '</label></li>';
            }
            echo $clientli;exit;
        }
		 $Casedata1 = ClientCase::find()->select(['tbl_client_case.id','tbl_client_case.client_id','tbl_client_case.case_name','tbl_client.client_name'])->joinWith('client')->where('tbl_client_case.is_close = 0')->orderBy('tbl_client_case.case_name asc')->all();
        $client_case = array();
        foreach ($Casedata1 as $ccase) {
                $client_case[$ccase->id] = $ccase->client->client_name . '-' . $ccase->case_name;
        }
        if (isset($criteria) && $criteria == "clientcase") {
            foreach ($client_case as $ccasekey => $ccaseval) {
                $checked = "";
                if (!empty($filter_data['clientcases']) && (in_array($ccasekey, $filter_data['clientcases'])) || (!empty($clientcases) && in_array($ccasekey, $clientcases))){
                    $checked = 'checked="checked"';
                }
                
                $clientcaseli .= "<li class='custom-full-width'><input type='checkbox' class='clientcases tutmbs' name='Report[clientcases][]' $checked id='clientcases_$ccasekey' value='$ccasekey' aria-label='$ccaseval'><label for='clientcases_$ccasekey'>$ccaseval</label></li>";
            }
            echo $clientcaseli;exit;
        }
		
	}
	/*
	 * Return All Project Task.
	 * */
	public function actionGetrequestprojstatuscriteria(){
		$filter_data = json_decode(Yii::$app->request->post('filter_data'), true);
		$clientids = Yii::$app->request->post('clientids');
		$clientcaseids = Yii::$app->request->post('clientcaseids');
		
		$datedropdown = Yii::$app->request->post('datedropdown');
		$cal_data = (new Tasks)->calculatedate($datedropdown,Yii::$app->request->post('start_date'),Yii::$app->request->post('end_date'));	
		$start_date = $cal_data['start_date'];
		$end_date = $cal_data['end_date'];
		$drivername = Yii::$app->db->driverName;
		
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		
		$taskcriteria = Tasks::find();
        $taskcriteria->select('task_status');
       
       
	     $UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
		 $UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);
		   if ($drivername == 'mysql') {
				$taskcriteria->where("DATE_FORMAT(CONVERT_TZ(created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') >= '".$start_date."' AND DATE_FORMAT( CONVERT_TZ(created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') <= '".$end_date."'");
		   }else{
			  $taskcriteria->where("Cast(switchoffset(todatetimeoffset(Cast(created as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$start_date."' AND Cast(switchoffset(todatetimeoffset(Cast(created as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) <= '".$end_date."'");
		   }
       $taskcriteria->groupBy('task_status');
       $taskcriteria->orderBy('task_status asc'); 
       $taskdataArr = $taskcriteria->all();  
       $projstatusli = "";
	   if (!empty($taskdataArr)) {
            foreach ($taskdataArr as $taskdata) {
                $checked = "";
                if (!empty($filter_data['task_status']) && in_array($taskdata->task_status, $filter_data['task_status']))
                    $checked = 'checked="checked"';

                if ($taskdata->task_status == 0)
                    $status = "Not Started";
                else if ($taskdata->task_status == 1)
                    $status = "Started";
                else if ($taskdata->task_status == 3)
                    $status = "OnHold";
                else if ($taskdata->task_status == 4)
                    $status = "Completed";

                $projstatusli .='<li class="by_teamlocs custom-full-width" ><input type="checkbox" name="Report[task_status][]" ' . $checked . ' class="pstatus"  value="' . $taskdata->task_status . '" id="statusname_' . $taskdata->task_status . '" aria-label="'.$status.'"> <label for="statusname_' . $taskdata->task_status . '" style="color:#222;font-weight:normal;">' . $status . '</label></li>';
            }
        }
        if($projstatusli != "")
        	echo $projstatusli;
        else 
        	echo $projstatusli = '<li class="by_teamlocs" ><label id="" style="color:#222;">No project status are available for selected date range</label></li>'; 
	}
	/*
	 * Return the Graph Of Project by Client/Cases
	 * */
	public function actionRequestclientcasedata(){
		
		$this->layout = 'report';
		$posting_data = yii::$app->request->post('posting_data');
			
		if (isset($_POST)) {
			$_POST['requestclientcase'] = 'requestclientcase';
			if(isset($posting_data) && !empty($posting_data)){
				$_POST = json_decode($posting_data,true);
				$post_data = $_POST;
				$save = '';
			}else{
				$post_data = array_slice($_POST,1);
				$save = 'save';
			}
			
			$final = (new TaskInstruct)->getstatusReport($_POST);
			$date_client_analysis = $final['date_client_analysis'];
			$client_case_dt = $final['client_case_dt'];
			$data_analysis = $final['data_analysis'];
			$chkclientcases= $final['chkclientcases'];
			$start_date = $final['start_date'];
			$end_date = $final['end_date'];
		}
		
			$arr = array();
    		$arrkeys = array();
    		$arrkeys['categories'] = array_values($date_client_analysis);
    		foreach($client_case_dt as $data2){
    			$count_set = array();
    			foreach($date_client_analysis as $data1){
        			if(!isset($data_analysis[$data1][$data2])){
        				$count_set[] = 0;
        			}else{
        				$count_set[] = $data_analysis[$data1][$data2];
        			}
    			}		
    			$arr['series'][] = array('name' => $data2, 'data' => $count_set)                                                                                                                                                                                ;
    		}
			$arr_data = json_encode($arr);
			$arrkeys_cat = json_encode($arrkeys);
			
			$client_by_case = 'client';
            if(isset($chkclientcases) && $chkclientcases == 'selac'){
            	$client_by_case = 'case';
            }
            
            
            return $this->render('runprojectbyclientcase', array(
                "arr_data" => $arr_data,
                "arrkeys_cat" => $arrkeys_cat,
                'startdate' => $start_date,
                'client_by_case' => $client_by_case,
                'enddate' => $end_date,
                'post_data' => $post_data,
                'save'=>$save
            ), false, true);
		}
		/*
		 * Save the Chart.
		 * */
		public function actionSavebillingfilter(){
			$filtername = Yii::$app->request->post('filter_name');
			if(isset($filtername) && $filtername != ''){
					$save_filter = new SavedFilters();
	     			$save_filter->user_id = Yii::$app->user->identity->id;
	     			$save_filter->filter_name = $filtername;
	     			$save_filter->filter_type = 2;
					$save_filter->filter_attributes = Yii::$app->request->post('filtervalue');
	     			$save_filter->save();
			}
			exit;
			}
			/* 
			 * Retrun the Saved Charts on Left Side of Report Panel.
			 * */
			public function actionGetsavedreports(){
				$saved_reports = SavedFilters::find()->select(['id','filter_name'])->orderBy('id DESC')->all();
				$project_list = "";
				foreach($saved_reports as $saved){
					 $project_list .='<li style="width:100%"><a href="#" onclick="runsavefilter('.$saved->id.')" javascript:void(0); class="" id="'.$saved->id.'" style="width:91%;float:left">'.$saved->filter_name.'</a><div><em class="fa fa-remove" style="padding:6px;cursor:pointer;color:#167FAC" onclick="deletesavefilter('.$saved->id.')" ></em></div></li>';
				}
				echo $project_list;
				exit;
				
			}
			/*
			 * To get the Filter Attributes For Saved Repots.
			 * */
			public function actionGetbillingfilter() {
				$filter_id = Yii::$app->request->post('filter_id');
				if (isset($filter_id) && $filter_id != "") {
					$model = SavedFilters::find()->where('id ='.$filter_id)->one();
					echo $model->filter_attributes;
					exit;
				}
			}
			/*
			 * Delete the saved Report
			 * */
			 public function actionDeletesavefilter(){
				$id = Yii::$app->request->post('filter_id'); 
				SavedFilters::deleteAll(['id' => $id]);
				exit;
			 }
			/*
			 * Return the Layout of Projectbyteamservice Report.
			 * */
			public function actionProjectbyteamservice(){
				$this->layout = 'report';
				$model = new Report;
				$filter_data = Yii::$app->request->post('filtervalue');
				if (isset($filter_data) && !empty($filter_data)){
					$filter_data = json_decode($_REQUEST['filtervalue']);
				}
			return $this->render('projectbyteamservice', array("filter_data" => $filter_data,'model'=>$model));
			}
			
			/*
			 * Get The Teamservice For Projectbyteamservices.
			 * */
			public function actionProjteamservicecriteria(){
				
				$post = Yii::$app->request->post();
				
				$filter_data = json_decode(Yii::$app->request->post('filter_data'), true);
				$cal_data = (new Tasks)->calculatedate($post['datedropdown'],$post['start_date'],$post['end_date']);	
				$start_date = $cal_data['start_date'];
				$end_date = $cal_data['end_date'];
				$drivername = Yii::$app->db->driverName;
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				if ($drivername == 'mysql') {
					 $wherequery = 'DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") >= "'.$start_date.'" AND DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") <= "'.$end_date.'"';
                } else {
                    $wherequery = "CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$start_date."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$end_date."'";
                }
				
				$team_data = TasksTeams::find()->select(['tbl_tasks_teams.teamservice_id'])->groupBy('tbl_tasks_teams.teamservice_id')->innerJoinWith(['tasks'=>function (\yii\db\ActiveQuery $query) use ($start_date,$end_date){
					  $query->select(['tbl_tasks.id','tbl_tasks.created']);
					  $query->where($wherequery); }])
					  ->innerJoinWith(['teamservice'=>function(\yii\db\ActiveQuery $query){ $query->select(['tbl_teamservice.id','tbl_teamservice.service_name']); }])
					  ->all();
					  $projstatusli = "";
					  
				foreach($team_data as $data => $value){
					$checked = "";
                if (!empty($filter_data['teamservice']) && in_array($team_data[$data]->teamservice->id, $filter_data['teamservice']))
                    $checked = 'checked="checked"';
					
					$projstatusli .='<li class="by_teamlocs custom-full-width" ><input type="checkbox" name="Report[teamservice][]" ' . $checked . ' class="te processclientteams"  value="' . $team_data[$data]->teamservice->id . '" id="teamser_' . $team_data[$data]->teamservice->id . '" aria-label="'.$team_data[$data]->teamservice->service_name.'"> <label for="teamser_' . $team_data[$data]->teamservice->id . '" style="color:#222;font-weight:normal;">' . $team_data[$data]->teamservice->service_name . '</label></li>';	
				}
				echo $projstatusli;
				exit;
			}
			/*
			 * Project By Teamservice Report Graph.
			 * */
			public function actionProjectbyteamservicedata(){
				
				$this->layout = 'report';
				$chart_data = array();
				$statusticks = array();
				$locationticks = array();
				$exportchart = array();
				$servicelinechart = "";
				$ticks = array();
				$posting_data = yii::$app->request->post('posting_data');
				
				if (isset($_POST)) {
				$_POST['requestteamservice'] = 'requestteamservice';	
				if(isset($posting_data) && !empty($posting_data)){
					$_POST = json_decode($posting_data,true);
					$post_data = $_POST;
					$save = '';
				}else{
					$post_data = array_slice($_POST,1);
					$save = 'save';
				}	
				
					$final = (new TaskInstruct)->getStatusteamservice($_POST);	
					$date_client_analysis = $final['date_client_analysis'];
					$teamservice_dt = $final['teamservice_dt'];
					$start_date = $final['start_date'];
					$end_date = $final['end_date'];
					$data_analysis = $final['data_analysis'];
				}
				$arr = array();
				$arrkeys = array();
				
				$arrkeys['categories'] = array_values($date_client_analysis);
    		
				foreach($teamservice_dt as $data2){
					$count_set = array();
					foreach($date_client_analysis as $data1){
						if(!isset($data_analysis[$data1][$data2])){
							$count_set[] = 0;
						}else{
							$count_set[] = $data_analysis[$data1][$data2];
						}
					}		
					$arr['series'][] = array('name' => $data2, 'data' => $count_set);
				}
			
				$arr_data = json_encode($arr);
				$arrkeys_cat = json_encode($arrkeys);  
				return $this->render('runprojectbyteamservice', array(
					"arr_data" => $arr_data,
					"arrkeys_cat" => $arrkeys_cat,
					'startdate' => $start_date,
					'enddate' => $end_date,
					'post_data' => $post_data,
					'save'=>$save
				));
				
			}
			/* Get the Teamservice Location For Project By Teamservice Report */
			public function actionGetteamlocbyservicetaskcriteria(){
				
				$post = yii::$app->request->post();
				$filterservice = yii::$app->request->post('filterLoc'); 
				$filter_data = explode(',',$filterservice);
				$cal_data = (new Tasks)->calculatedate($post['datedropdown'],$post['start_date'],$post['end_date']);	
				$start_date = $cal_data['start_date'];
				$end_date = $cal_data['end_date'];
				$projectstatus = $post['projectstatus'];
				$teamLoc = "";
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				$drivername = Yii::$app->db->driverName;
				if($projectstatus != "" && $start_date!= "" && $end_date!=""){
					 if ($drivername == 'mysql') {
					 $wherequery = 'DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") >= "'.$start_date.'" AND DATE_FORMAT(tbl_tasks.created, "%Y-%m-%d") <= "'.$end_date.'"';
					} else {
                    $wherequery = "CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$start_date."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$end_date."'";
					}
					
					$teamloc_data = Tasks::find()->select(['tbl_tasks.id'])->where($wherequery)->andWhere("tbl_tasks.task_cancel=0 AND tbl_tasks.task_closed=0 AND tbl_tasks.task_status IN (" . $projectstatus . ")")->innerJoinWith(['tasksTeams'=>function (\yii\db\ActiveQuery $query){ $query->where("tbl_tasks_teams.teamservice_id != ''"); $query->groupBy('tbl_tasks_teams.team_loc')->innerJoinWith(['teamlocationMaster'=>function(\yii\db\ActiveQuery $query){ $query->select(['tbl_teamlocation_master.id','tbl_teamlocation_master.team_location_name','tbl_teamlocation_master.remove']); $query->where('tbl_teamlocation_master.remove = 0'); }]); }])->all();	  
					$teamLoc = "";
						foreach($teamloc_data as $data => $value){
							foreach($teamloc_data[$data]->tasksTeams as $data1 => $value){
								
							$teamloc_id = $teamloc_data[$data]->tasksTeams[$data1]->teamlocationMaster->id;
							$teamloc_name = $teamloc_data[$data]->tasksTeams[$data1]->teamlocationMaster->team_location_name;
							$checked = "";
							if (!empty($filter_data) && in_array($teamloc_id, $filter_data))
								$checked = 'checked="checked"';	
							$teamLoc .= "<li class='by_teamloc2 custom-full-width'><input type='checkbox' ".$checked." id='telocname_{$teamloc_id}' value='{$teamloc_id}' class='teloc' name='Report[teamlocs][]' aria-label='{$teamloc_name}'/><label for='telocname_{$teamloc_id}'>{$teamloc_name}</label></li>";
							  }      		
						}
			    }  
				$data_ar = json_encode(array('TeamLoc'=>$teamLoc));
				echo $data_ar;
			}
			/* Return the Layout of ToDo Follow-up Items By Service Report */
			public function actionTodofollowitembyteam(){
				$this->layout = "report";
				$model = new Report;
				$filter_data = Yii::$app->request->post('filtervalue');
				$todo_status = Yii::$app->params['todo_status'];
				if (isset($filter_data) && $filter_data != "")
					$filter_data = json_decode(Yii::$app->request->post('filtervalue'));
					

				return $this->render('todofollowitembyteam', array(
					"filter_data" => $filter_data,'model'=>$model,'todostatusArr'=>$todo_status
						));
			}
			/* Return The Service Of ToDo Follow-up Items By Service Report  */
			public function actionTodoservicecriteria(){
				$post = Yii::$app->request->post();
				$filter_data = json_decode(Yii::$app->request->post('filter_data'), true);
				
				$cal_data = (new Tasks)->calculatedate($post['datedropdown'],$post['start_date'],$post['end_date']);
				$start_date = $cal_data['start_date'];
				$end_date = $cal_data['end_date'];
				$drivername = Yii::$app->db->driverName;
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				
				if ($drivername == 'mysql') {
				 $wherequery = "DATE_FORMAT(tbl_tasks_units_todos.created, '%Y-%m-%d') >= '".$start_date."' AND DATE_FORMAT(tbl_tasks_units_todos.created, '%Y-%m-%d') <= '".$end_date."'";
				} else {
				 $wherequery = "CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todos.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$start_date."' AND CAST(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todos.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '".$end_date."'";
				}
				
				$userId = Yii::$app->user->identity->id;
		
				$role_info = Yii::$app->user->identity->role;
				$role_type = $role_info->role_type;
				$role_type_explode = explode(',',$role_type);

			
				$teamservice_data = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.task_id','tbl_tasks_units_todos.tasks_unit_id','tbl_tasks_units_todos.created'])->where($wherequery)->innerJoinWith(['taskUnit'=>function(\yii\db\ActiveQuery $query) { $query->select(['tbl_tasks_units.id','tbl_tasks_units.task_instruct_servicetask_id'])->innerJoinWith(['taskInstructServicetask'=>function(\yii\db\ActiveQuery $query) { $query->select(['tbl_task_instruct_servicetask.id','tbl_task_instruct_servicetask.teamservice_id','tbl_task_instruct_servicetask.team_id'])->groupBy('tbl_task_instruct_servicetask.teamservice_id')->innerJoinWith(['teamservice'=>function(\yii\db\ActiveQuery $query)use($role_type_explode){ 
					if(is_array($role_type_explode) && in_array(1,$role_type_explode)){
						$query->where('teamservice.teamid IN (1)');
					}
					 $query->select(['tbl_teamservice.id','tbl_teamservice.service_name']); }]); }]); }])->all();
				
				$teamservice_li = "";
				$i = 1;
				if(count($teamservice_data) > 0){
					foreach($teamservice_data as $teamservice => $value){
						$checked = "";
						$teamservice_id = $value->taskUnit->taskInstructServicetask->teamservice_id;
						$teamservice_name = $value->taskUnit->taskInstructServicetask->teamservice->service_name;
						if (isset($filter_data['teamservice']) && in_array($teamservice_id, $filter_data['teamservice'])) {
							$checked = "checked='checked'";
						}
						$teamservice_li .= "<li class='by_casemanager custom-full-width'><input type='checkbox' ".$checked." id='tename_{$i}' value='{$teamservice_id}' class='te processclientteams' name='Report[teamservice][]' aria-label='{$teamservice_name}'/><label for='tename_{$i}'>{$teamservice_name}</label></li>";
					$i++;
					}
				}else{
					$teamservice_li = "<li>No teamservice are available.</li>";
				}
				echo $teamservice_li;
				exit;
				
			}
			/* ToDo Follow-up Items By Service Report Graph. */
			public function actionTodofollowitembyteamdata(){
				$this->layout = 'report';
				$teamtrendlinechartsri = array();
				$teamverticalchartticks = array();
				$servicelinechart = "";
				$post = Yii::$app->request->post();
				$posting_data = Yii::$app->request->post('posting_data');
				if (isset($_POST)) {
					$_POST['todofollowitem'] = 'todofollowitem';
					if(isset($posting_data) && !empty($posting_data)){
						$_POST = json_decode($posting_data,true);
						$post_data = $_POST;
						$save = '';
					}else{
						$post_data = array_slice($_POST,1);
						$save = 'save';
					}
					$post_data = array_slice($_POST,1);
					$k = 0;
					$final = (new TaskInstruct)->getTodofollowup($_POST);
					$teamverticalchart = $final['teamverticalchart'];
					$teamverticalchartticks = $final['teamverticalchartticks'];
					$start_date = $final['start_date'];
					$end_date = $final['end_date'];
				}
				return $this->render('runtodofollowitembyteam', array(
                'todobarchart' => $teamverticalchart,
                "todobarchartticks" => $teamverticalchartticks,
                'startdate' => $start_date,
                'enddate' => $end_date,'post_data'=>$post_data,'save'=>$save));	
			}
			/* Return the Layout of ToDO Follow-up item by Duration */
			public function actionTodofollowitembyduration(){
				$this->layout = "report";
				$filter_data = '';
				$model = new Report;
				$filter_value = Yii::$app->request->post('filtervalue');
				$todostatusArr = Yii::$app->params['todo_status'];
				if (isset($filter_value) && $filter_value != "")
					$filter_data = json_decode($_REQUEST['filtervalue']);
					
				return $this->render('todofollowitembyduration', array(
					"filter_data" => $filter_data,"model" => $model,"todostatusArr"=>$todostatusArr
						));
			}
			/*Return the Excel File of ToDO Follow-up item by Duration*/
			public function actionTodofollowitembydurationdata(){
				 $todoexportArr = array();
				 $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				 $task_status = yii::$app->params['todo_status']; 
				 $posting_data = Yii::$app->request->post('posting_data');
				 if(isset($_POST)){
					$_POST['todofollowitembyduration'] = 'todofollowitembyduration';
					if(isset($posting_data) && !empty($posting_data)){
						$_POST = json_decode($posting_data,true);
						$post_data = $_POST;
						$save = '';
					}else{
						$post_data = array_slice($_POST,1);
						$save = 'save';
					}
					//echo "<pre>"; print_r($_POST); exit;	
					//$_POST = array_slice($_POST,1);
					$final = (new TaskInstruct)->getTodofollowbyduration($_POST);
					$exceldata = $final['excelchartdata'];
					$start_date = $final['start_date'];
					$end_date = $final['end_date'];
					$todo_status = $final['todo_status'];
					$this->layout = "";
					
					if(!empty($exceldata)){
						
						$filename = "TodoFollowupItemsByDuration_" . date('m_d_Y', time()) . ".xls";
				
						$objPHPExcel     = new \PHPExcel();
						
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.'2','ToDo Follow-up Items by Duration');

						$objPHPExcel->getActiveSheet()->SetCellValue('B'.'2','ToDo Submitted Start Date');
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.'2','ToDo Submitted End Date');
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.'2','ToDo Status');
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.'3',' ');

						$objPHPExcel->getActiveSheet()->SetCellValue('B'.'3',date('m/d/Y',strtotime($start_date)));
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.'3',date('m/d/Y',strtotime($end_date)));
						$sts= implode(',',$task_status);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.'3',$sts);
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.'5','Client');
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.'5','Case');
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.'5','Project#');
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.'5','ToDo Follow-up');
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.'5','Service');
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.'5','Service Task');
						$objPHPExcel->getActiveSheet()->SetCellValue('G'.'5','ToDo Status');
						$objPHPExcel->getActiveSheet()->SetCellValue('H'.'5','ToDo Started');
						$objPHPExcel->getActiveSheet()->SetCellValue('I'.'5','ToDo Completed');
						$objPHPExcel->getActiveSheet()->SetCellValue('J'.'5','Business Days in Follow-up');
						$rowcount = 6;
						foreach ($exceldata as $key => $data) {		
								$status = ($data['trans_type']==9?'Completed':'Not Completed');
								$completed = ($data['completed']!="" && $data['trans_type']==9)?(new Options)->ConvertOneTzToAnotherTz($data['completed'],'UTC', $_SESSION['usrTZ'], "requestdate"):"";
								$started = (new Options)->ConvertOneTzToAnotherTz($data['started'], 'UTC', $_SESSION['usrTZ'],"requestdate");
								$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount,$data['client']);
								$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowcount,$data['case']);
								$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount,$data['task_id']);
								$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount,$data['todo_cat']);
								$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowcount,$data['service']);
								$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowcount,$data['servicetask']);
								$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowcount,$status);
								$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowcount,$started);
								$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowcount,$completed);
								$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowcount,$data['followup_days']);
								$rowcount++;
						}
						
					 header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
					 header('Content-Disposition: attachment;filename="'.$filename.'"');
					 header('Cache-Control: max-age=0');

					 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					 $objWriter->save('php://output');
					 exit(); 
					}
				}	 
			}
			/* Save Functionality of Todo Duration In saved filter */
			public function actionSavetododurationfilter(){
				$filter_value =  yii::$app->request->post();
				if(isset($filter_value['filter_name']) && $filter_value['filter_name'] != ""){
					$cal_data = (new Tasks)->calculatedate($filter_value['Report']['datedropdown'],$filter_value['Report']['start_date'],$filter_value['Report']['end_date']);	
					$start_date = $cal_data['start_date'];
					$end_date = $cal_data['end_date'];
					$todostatus = "";
					$teamservice = "";
					$teamloc = "";
					if (count($filter_value['Report']['todostatus']) > 0) {
						$todostatus =$filter_value['Report']['todostatus'];
					}
					if (count($filter_value['Report']['teamservice']) > 0) {
						$teamservice = $filter_value['Report']['teamservice'];
					}
					if (count($filter_value['Report']['teamlocs']) > 0) {
						$teamloc = $filter_value['Report']['teamlocs'];
					}
					
					$filtervalueArr = array(
						'start_date' => $start_date,
						'end_date' => $end_date,
						'chtodostatus' => $filter_value['Report']['chktodostatus'],
						'chtodoteamserv' => $filter_value['Report']['chtodoteamserv'],
						'todoteamloc' => $filter_value['Report']['chkprocessteamlocs'],
						'todostatus' => $todostatus,
						'teamservice' => $teamservice,
						'teamlocs' => $teamloc,
						'tododuration' => 'tododuration',
					);
					$filtervalue = json_encode($filtervalueArr);
					$save_filter = new SavedFilters();
	     			$save_filter->user_id = Yii::$app->user->identity->id;
	     			$save_filter->filter_name = $filter_value['filter_name'];
	     			$save_filter->filter_type = 2;
					$save_filter->filter_attributes = $filtervalue;
	     			$save_filter->save();
				}
				exit;
			}
		}
        
