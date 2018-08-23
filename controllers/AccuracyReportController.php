<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\ProjectSecurity;
use yii\helpers\ArrayHelper;
use app\models\Team;
use app\models\Tasks;
use app\models\client;
use app\models\clientCase;
use app\models\Teamservice;
use app\models\TeamLocationMaster;
use app\models\TeamserviceSlaBusinessHours;
use app\models\TeamserviceSla;
use app\models\Options;
use app\models\Evidence;
use app\models\Servicetask;
use app\models\Unit;
use app\models\TaskInstruct;
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\SavedFilters;

/**
 * AccuracyReportController Provides Report
 */
class AccuracyReportController extends Controller
{
    public function behaviors(){
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => ['delete' => ['get'],],],
        	];
    }
    
    /**
     * Accuracy Report Layout
     */
    public function actionIndex(){}
    
    /**
     * SLA Turn Time by client/case report
     * @return
     */
    public function actionSlaTurntime()
    {
    	$this->layout = 'report';
    	$userId = Yii::$app->user->identity->id;
		$role_info = Yii::$app->user->identity->role;
		$roleId = $role_info->id;
		$filter_data = Yii::$app->request->post('filtervalue');
		
		$roletypes = array();
		if (isset($role_info->role_type) && $role_info->role_type != "")
			$roletypes = explode(",", $role_info->role_type);
		
		$where='ps.team_id!=1 AND ps.team_id!=0';
		if($roleId != 0){
			$where.=" AND ps.user_id=".$userId;
		}
		$teamList = ArrayHelper::map(ProjectSecurity::find()->select(['ps.team_id','t.team_name'])->from('tbl_project_security as ps')
				->join('INNER JOIN','tbl_team as t','t.id=ps.team_id')->where("$where")->all(), 'team_id', 'team_name');
		
		if (in_array(1, $roletypes))
			$teamList[1] = 'Case Manager';
		
		asort($teamList);
		$team_locs = array(0);
		$teamLocation = ArrayHelper::map(Team::find()->select(['tl.team_loc as team_loc','tm.team_location_name'])->from('tbl_team as t')
				->join('LEFT JOIN','tbl_team_locs as tl','t.id=tl.team_id')
				->join('LEFT JOIN','tbl_teamlocation_master as tm','tm.id=tl.team_loc')
				->where('tm.remove=0 AND tl.team_id IN('.implode(',', array_keys($teamList)).')')->orderBy('tm.team_location_name ASC')->all(), 'team_loc','team_location_name');
		
		$teamservices = ArrayHelper::map(Teamservice::find()->select(['service_name','id'])->all(), 'id','service_name');
		$filter_data = '';
		if (isset($filter_data) && $filter_data != ""){
			$filter_data = json_decode($filter_data);
		}
		
		return $this->render('slaturntimeclientcase',['teamLocation' => $teamLocation, 'filter_data' => $filter_data, 'team_services' => $teamservices]);
    }
    
    /**
     * Get start_date and end_date by dropdown date value
     * @return
     */
    public function getalldatebydropdown($datedropdown)
    {
    	if (isset($datedropdown) && $datedropdown==1) {
    		$start_date = date("Y-m-d");
    		$end_date = date("Y-m-d");
    	} else if (isset($datedropdown) && $datedropdown==2) {
    		$yesterday = strtotime("-1 day");
    		$start_date = date('Y-m-d', $yesterday);
    		$end_date = date("Y-m-d");
    	} else if (isset($datedropdown) && $datedropdown==3) {
    		$week = strtotime("-7 day");
    		$start_date = date('Y-m-d', $week);
    		$end_date = date("Y-m-d");
    	} else if (isset($datedropdown) && $datedropdown==4) {
    		$month = strtotime("-1 month");
    		$start_date = date('Y-m-d', $month);
    		$end_date = date("Y-m-d");
    	} else if (isset($datedropdown) && $datedropdown==5) {
    		$year = strtotime("-1 year");
    		$start_date = date('Y-m-d', $year);
    		$end_date = date("Y-m-d");
    	} else {
    		$start_date = $_REQUEST['start_date'];
    		$end_date = $_REQUEST['end_date'];
    	}
    	$data['start_date'] = $start_date;
    	$data['end_date'] = $end_date;
    	return $data;
    }
    
    /**
     * get selected client/cases for SLA Turn-Time by client/case
     * @return
     */
    public function actionGetClientCaseCriteria(){
    	$start_date = Yii::$app->request->post('start_date');
    	$end_date = Yii::$app->request->post('end_date');
    	$datedropdown = Yii::$app->request->post('datedropdown');
    	$datevalue = $this->getalldatebydropdown($datedropdown);
    	if(count($datevalue)>0)
    		$start_date = $datevalue['start_date']; $end_date = $datevalue['end_date'];
    	
    	$clientList = Client::find()->select(['id','client_name'])->orderBy('client_name asc')->all();
    	$clientli='';
    	if(Yii::$app->request->post('type')=='client'){
	    	foreach ($clientList as $key => $client) {
	//     		$checked = "";
	//     		if (in_array($client->id, $filter_data['client'])){
	//     			$checked = 'checked="checked"';
	//     		}
	    		$clientli .= '<li><input type="checkbox" class="client tutmbs" name="client[]" ' . $checked . ' id="client_' . $client->id . '" value="' . $client->id . '" aria-label="'.$client->client_name.'" ><label for="client_' . $client->id . '" class="clientlabel">' . $client->client_name . '</label></li>';
	    	}
    	}
    	
    	$clientcaseList = ClientCase::find()->select(['tbl_client_case.id','tbl_client_case.case_name','t.client_name'])->join('LEFT JOIN','tbl_client as t','tbl_client_case.client_id=t.id')->all();
    	if(Yii::$app->request->post('type')=='clientcase'){
    		foreach($clientcaseList as $key => $clientcase){
    //     		$checked = "";
    //     		if (in_array($client->id, $filter_data['client'])){
    //     			$checked = 'checked="checked"';
    //     		}
    			$clientli .= '<li><input type="checkbox" class="clientcase tutmbs" name="clientcase[]" ' . $checked . ' id="clientcase_' . $clientcase->id . '" value="' . $clientcase->id . '" aria-label="'.$clientcase->client_name.'"  ><label for="clientcase_' . $clientcase->id . '" class="clientcaselabel">' . $clientcase->client_name . ' - ' . $clientcase->case_name . '</label></li>';
    		}
    	}
    	echo $clientli;
    }
    
    /**
     * Get Project status for SLA Turn-time by client/cases
     * @return
     */
    public function actionGetProjectStatus(){
    	$start_date = Yii::$app->request->post('start_date');
    	$end_date = Yii::$app->request->post('end_date');
    	$datedropdown = Yii::$app->request->post('datedropdown');
    	
     	$filter_data = Yii::$app->request->post('filter_data');
     	if (isset($filter_data) && $filter_data != ""){
     		$filter_data = json_decode($filter_data);
     		$start_date = $filter_data->start_date;
     		$end_date = $filter_data->end_date;
     		$datedropdown = $filter_data->datedropdown;
     	}
     	
     	$datevalue = $this->getalldatebydropdown($datedropdown);
     	
     	if(count($datevalue)>0)
     		$start_date = $datevalue['start_date']; $end_date = $datevalue['end_date'];
 
     	$datesql = $this->getdatesqlformat($start_date,$end_date);
     	$task_status = Tasks::find()->from('tbl_tasks as t')->where("$datesql")->groupBy('t.task_status')->orderBy('t.task_status')->all();
     	$projstatusli = '';
     	if(!empty($task_status)){
     		foreach($task_status as $val){
     			$checked = "";
     			if(isset($filter_data)){
     				if (in_array($val->task_status, $filter_data->task_status))
     					$checked = 'checked="checked"';
     			}
     			if($val->task_status==0)
     				$status = "Not Started";
     			else if($val->task_status==1)
     				$status = "Started";
     			else if ($val->task_status==3)
                    $status = "OnHold";
                else if ($val->task_status==4)
                    $status = "Completed";
     			
          		$projstatusli .='<li class="by_teamlocs" ><input type="checkbox" name="task_status[]" ' . $checked . ' id="statusname_' . $val->task_status . '" class="pstatus"  value="' . $val->task_status . '" aria-label="'.$status.'" > <label for="statusname_' . $val->task_status . '" id="statusname_' . $val->task_status . '" class="statusname"  style="color:#222;">' . $status . '</label></li>';
     		}
     	}
     	
     	if($projstatusli!='')
     		echo $projstatusli;
     	else 
     		echo $projstatusli = '<li class="by_teamlocs" ><label>No project status are available for selected date range</label></li>';
    }
    
    /**
     * Get Date with different UTC timzone
     * @return
     */
    public function getdatesqlformat($start_date,$end_date){
    	$UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
    	$UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);
    	$datesql="";
    	if (DB_TYPE == 'sqlsrv') {
    		$datesql = "Cast(switchoffset(todatetimeoffset(Cast(created as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$start_date."' AND Cast(switchoffset(todatetimeoffset(Cast(created1 as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) <= '".$end_date."'";
    	} else {
    		$datesql = "DATE_FORMAT(CONVERT_TZ(t.created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(CONVERT_TZ(t.created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d') <= '$end_date'";
    	}
    	return $datesql;
    }
    
    /**
     * get SLA client case data in xml file
     * @return
     */
    public function actionDataslaclientcasedata()
    {
    	$start_date = Yii::$app->request->post('start_date');
    	$end_date = Yii::$app->request->post('end_date');
    	$task_status = Yii::$app->request->post('task_status');
    	$chkclientcases = Yii::$app->request->post('chkclientcases');
    	$client = Yii::$app->request->post('client');
    	$clientcase = Yii::$app->request->post('clientcase');
    	$teamservice = Yii::$app->request->post('servicestatus');
    	$teamlocs = Yii::$app->request->post('teamlocation');
    	$datedropdown = Yii::$app->request->post('datedropdown');
    	
    	$filter_data =  Yii::$app->request->post('filter_data');
    	if(Yii::$app->request->post()){
    		$post_data = [];
    		if(isset($filter_data) && !empty($filter_data)){
    			$save="";
    		}else{
    			$post_data['start_date'] = Yii::$app->request->post('start_date');
    			$post_data['end_date'] = Yii::$app->request->post('end_date');
    			$post_data['datedropdown'] = Yii::$app->request->post('datedropdown');
    			$post_data['task_status'] = Yii::$app->request->post('task_status');
    			$post_data['client'] = Yii::$app->request->post('client');
    			$post_data['chkclientcases'] = Yii::$app->request->post('chkclientcases');
    			$post_data['clientcase'] = Yii::$app->request->post('clientcase');
    			$post_data['servicestatus'] = Yii::$app->request->post('servicestatus');
    		}
    	}
    	
    	$datevalue = $this->getalldatebydropdown($datedropdown);
    	if(count($datevalue)>0)
    		$start_date = $datevalue['start_date']; $end_date = $datevalue['end_date'];
    	
    	if (!empty($task_status)) {
    		$projectstatus = implode(",", array_unique($task_status));
    	}
    	
    	$client_ids='';
    	$clientcase_ids='';
    	if(isset($chkclientcases) && $chkclientcases=='client'){
    		if(!empty($client))
    			$client_ids = implode(",", $client);
    	}
    	
    	if(isset($chkclientcases) && $chkclientcases=='clientcases'){
    		if(!empty($clientcase))
    			$clientcase_ids = implode(",", $clientcase);
    	}
    	
    	$serviceArr = array();
    	$serviceCond = "";
    	if (!empty($teamservice)) {
    		foreach ($teamservice as $service) {
    			$serviceArr[$service] = $service;
    			if ($serviceCond == "") {
    				$serviceCond = " tbl_tasks_teams.teamservice_id = '" . $service . "'";
    			} else {
    				$serviceCond = $serviceCond . " OR tbl_tasks_teams.teamservice_id = '" . $service . "'";
    			}
    		}
    	}
    	
    	$teamLocCond = "";
    	if (!empty($teamlocs)) {
    		foreach ($teamlocs as $teamloc) {
    			if ($teamLocCond == "") {
    				$teamLocCond = " tbl_tasks_teams.team_loc = '" . $teamloc . "'";
    			} else {
    				$teamLocCond = $teamLocCond . " OR tbl_tasks_teams.team_loc = '" . $teamloc . "'";
    			}
    		}
    	}
    	
    	$datesql = $this->getdatesqlformat($start_date, $end_date);
    	$clientcasesql = "";
    	$servicesql = "";
    	if($client != "")
    		$clientcasesql = " AND t.client_id IN ($client_ids)";
    	if($clientcase != "")
    		$clientcasesql = " AND t.client_case_id IN ($clientcase_ids)";
    	
    	$getTaskDataQuery = Tasks::find()->select(['t.id','t.client_case_id','t.client_id', 't.task_status', 't.task_complete_date', 't.created'])->from('tbl_tasks as t')
    		->with([
	    		'client' => function(\yii\db\ActiveQuery $query){
	     			$query->select(['tbl_client.id','tbl_client.client_name']);
	    		},
	    		'clientCase' => function(\yii\db\ActiveQuery $query){
	    			$query->select(['tbl_client_case.id','tbl_client_case.case_name'])->where(['tbl_client_case.is_close'=>0]);
	    		},
	    		'taskInstruct' => function(\yii\db\ActiveQuery $query){
	    			$query->select(['tbl_task_instruct.id','tbl_task_instruct.task_id','tbl_task_instruct.isactive', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue']);
	    			$query->with(['taskInstructServicetasks' => function(\yii\db\ActiveQuery $query){
	    				$query->select(['tbl_task_instruct_servicetask.team_id','tbl_task_instruct_servicetask.teamservice_sla_id','tbl_task_instruct_servicetask.team_loc','tbl_task_instruct_servicetask.est_time','tbl_task_instruct_servicetask.task_instruct_id','tbl_task_instruct_servicetask.servicetask_id','tbl_task_instruct_servicetask.teamservice_id']);
	    			}]);
	    			$query->with(['taskInstructEvidences' => function(\yii\db\ActiveQuery $query){
	    				$query->select(['tbl_task_instruct_evidence.task_instruct_id','tbl_task_instruct_evidence.evidence_id','tbl_task_instruct_evidence.prod_id','tbl_task_instruct_evidence.evidence_contents_id']);
	    			}]);
	    			$query->where('isactive=1');
	    		}, 
	    	])->innerJoinWith(['tasksTeams' => function(\yii\db\ActiveQuery $query) use($serviceCond,$teamLocCond){ 
	    			$query->select(['tbl_tasks_teams.id','tbl_tasks_teams.teamservice_id','tbl_tasks_teams.task_id','tbl_tasks_teams.id','tbl_tasks_teams.team_loc','tsla.teamservice_sla_id']);
	    			$query->join('INNER JOIN','tbl_tasks_team_sla as tsla','tbl_tasks_teams.id=tsla.tasks_teams_id');
	    			if(isset($serviceCond) && $serviceCond != "")
	    				$query->where("$serviceCond");
	    			if(isset($teamLocCond) && $teamLocCond != "")
	    				$query->where("$teamLocCond");
	    	}])->where("t.task_cancel=0 AND t.task_status IN ($projectstatus) AND $datesql $clientcasesql")->orderBy('t.id DESC')->asArray()->all();

	     if (!empty($getTaskDataQuery)){
	   		$projstatus = array(0 => "Not Started", 1 => "Started", 3 => "OnHold", 4 => "Completed");
    		foreach($getTaskDataQuery as $da){
    			$services = array();
    			$sla = array();
    			$serviceTaskArr = $da['tasksTeams'];
    			$todo_status = (new Tasks)->getSlaServiceTodoStatusByPojectId($da['id'], $da['taskInstruct']['taskInstructServicetasks']);
    			$teamarrCompletedDate = "";
    			if (!empty($serviceTaskArr)) {
    					$esttime = array();
	    				$working_hrs = TeamserviceSlaBusinessHours::find()->select('workinghours')->where(['order' => 'id desc']);
	    				$teamservicesArr = array();
	     				foreach ($serviceTaskArr as $servicetask){
     								$servicedata = Teamservice::findOne($servicetask['teamservice_id']);
		     						$locdata = TeamlocationMaster::findOne($servicetask['team_loc']);
		     						$services[$servicetask['teamservice_id']]['name'] = $servicedata->service_name;
		     						$services[$servicetask['teamservice_id']]['location'] = $locdata->team_location_name;
		     						
		     						if(!isset($services[$servicetask['teamservice_id']]['allotted_days']))
		     							$services[$servicetask['teamservice_id']]['allotted_days'] = 0;
		     						
		     						if(!isset($services[$servicetask['teamservice_id']]['day_spent']))	
		     						$services[$servicetask['teamservice_id']]['day_spent'] = 0;
		     						$services[$servicetask['teamservice_id']]['toto_followup_days'] = (new Tasks)->getTotalSLADaysTodoFollowup($da['id'], $servicetask['teamservice_id'], $todo_status);
		     						$services[$servicetask['teamservice_id']]['completed'] = (new Tasks)->getSlaTeamserviceStatusByPojectId($da['id'],$servicetask['teamservice_id'], $da['taskInstruct']['taskInstructServicetasks']); //Task::model()->getProjectUnitCompletedByService($servicetask->teamservice_id, $da->id);
		     						
		     						if(!isset($services[$servicetask['teamservice_id']]['stop_clk_business_days']))	
		     							$services[$servicetask['teamservice_id']]['stop_clk_business_days'] = 0;
		     						
		     						$services[$servicetask['teamservice_id']]['completed_date'] = '';	
	     					
		     						if(!empty($teamservicesArr) && $cntserv > 2 && $teamarrCompletedDate!="") {
		     							$cntserv = count($teamservicesArr)-2;
		     							if($teamservicesArr[$cntserv] == $teamarrCompletedDate)
		     								$teamarrCompletedDate = "";
		     						}
	     						
		     						/** allocated time **/
		     						$allocated_time = TeamserviceSla::findOne($servicetask['teamservice_sla_id']);
     								if (!empty($allocated_time->attributes)) {
										if ($allocated_time->del_time_unit==2) {
											$days = $allocated_time->del_qty;
										} else {
											if ($allocated_time->del_qty >= $working_hrs)
												$days = ($allocated_time->del_qty / $working_hrs);
											else
												$days = 1;
										}
										if($services[$servicetask['teamservice_id']]['allotted_days'] != "")
											$services[$servicetask['teamservice_id']]['allotted_days'] += $days;
										else
											$services[$servicetask['teamservice_id']]['allotted_days'] = $days;
									}
						
									$preCompletedDate = "";
									if($services[$teamarrCompletedDate]['completed_date'] != ""){
										$preCompletedDates = $services[$teamarrCompletedDate]['completed_date'];
										$preCompletedDates1 = explode(" ",$preCompletedDates);
										$preCompletedDateAr = explode("/",$preCompletedDates1[0]);
										$preCompletedDate = $preCompletedDateAr[2]."-".$preCompletedDateAr[0]."-".$preCompletedDateAr[1];
									}
									
									$services[$servicetask['teamservice_id']]['day_spent'] = (new Tasks)->getSlaTotalDaySpent($da['id'], $servicetask['teamservice_id'], $servicestatus, $preCompletedDate);
									$services[$servicetask['teamservice_id']]['stop_clk_business_days'] =  (new Tasks)->getSlaStopClkDays($da['id'], $servicetask['teamservice_id']);

		     						if ($services[$servicetask['teamservice_id']]['completed']){
										$services[$servicetask['teamservice_id']]['completed_date'] = (new Tasks)->getProjectUnitCompletedDateService($servicetask['teamservice_id'], $da['id']);
										$teamarrCompletedDate = $servicetask['teamservice_id'];
									}
									
									$services[$servicetask['teamservice_id']]['days_spent_minus_followup'] = ($services[$servicetask['teamservice_id']]['day_spent'] - $services[$servicetask['teamservice_id']]['stop_clk_business_days']);
									$dayslate = ($services[$servicetask['teamservice_id']]['days_spent_minus_followup'] - $services[$servicetask['teamservice_id']]['allotted_days']);
									$services[$servicetask['teamservice_id']]['days_late'] = $dayslate;
									
									if(!in_array($servicetask['teamservice_id'], $teamservicesArr))
										$teamservicesArr[] = $servicetask['teamservice_id'];
							}
						}
    					
						$dataSizes = 0;
		     			$projcompletedated = "";
		     			$daylate = 0;
		     			$evidIdArr = array();
						
		     			$taskeviddetails =$da['taskInstruct']['taskInstructEvidences']; 
		      			if (count($taskeviddetails) > 0) {
		      				foreach ($taskeviddetails as $eviddata) {
		      					if (!empty($eviddata['evidence_id']) && $eviddata['evidence_id'] != 0) {
		      						$evidIdArr[$eviddata['evidence_id']] = $eviddata['evidence_id'];
		      					}
		      				}
		      			}
		      			
		      			$evidIdArr = array_filter($evidIdArr);
		      			$evidId = implode(',', $evidIdArr);
		      			$eviddataArr = array();
		      			$unitArr = array();
		      			
		      			if (count($evidIdArr) > 0) {
		      					$eviddataArr = Evidence::find()->select(['tbl_evidence.contents_total_size', 'tbl_evidence.contents_total_size_comp', 'tbl_evidence.unit', 'tbl_evidence.comp_unit'])
		      						->with('evidenceunit')->where("tbl_evidence.id IN (" . $evidId . ")")->all();
		      			}
		      			
		      			if (!empty($eviddataArr)) {
		      				foreach ($eviddataArr as $keys => $eviddata) {
		      					if (!empty($eviddata->unit)) {
		      						$UnitVal = (new Tasks)->getElementUnitVal($eviddata->unit, $eviddata->contents_total_size);
		      						$unitArr[$UnitVal['unit']][] = $UnitVal['value'];
		      					} else {
		      						$UnitVal = (new Tasks)->getElementUnitVal($eviddata->comp_unit, $eviddata->contents_total_size_comp);
		      						$unitArr[$UnitVal['unit']][] = $UnitVal['value'];
		      					}
		      				}
		      			}
		      			if (!empty($unitArr)) {
		      				foreach ($unitArr as $key => $unitData) {
		      					$datasize = array_sum($unitData);
		      					$dataSizes = $datasize . " " . $key;
		      				}
		      			}
	      			
		      			if (isset($da['task_complete_date']) && !in_array(date('Y', strtotime($da['task_complete_date'])), array('1970', '-0001'))) {
	      					$datacomplete = $da['task_complete_date'];
		      				$duedatetime = date("Y-m-d H:i:s", strtotime($da['taskInstruct']['task_duedate'] . " " . $da['taskInstruct']['task_duedate']));
		      				$hourdiff = abs((strtotime($datacomplete) - strtotime($duedatetime)));
		      				$hourdiff = round($hourdiff/(60 * 60 * 24));
		      				$daylate = 0;
		      				if ($hourdiff > 0) {
		      					$daylate = $hourdiff;
		      				}
	      					if (date('Y-m-d', strtotime($da['task_complete_date'])) != '1970-01-01' && $da['task_status'] == 4)
	      						$projcompletedated = (new Options)->ConvertOneTzToAnotherTz($da['task_complete_date'], 'UTC', $_SESSION['usrTZ']);
		      			}
		      			
		      			$final_data[$da['id']] = array(
		      				'client' => $da['client']['client_name'],
		      				'case' => $da['clientCase']['case_name'],
		      				'date_submitted' => (new Options)->ConvertOneTzToAnotherTz($da['created'], "UTC", $_SESSION["usrTZ"]),
		      				'duedate' => (new Tasks)->getTaskindividualData($da['id'], "task_duedate&time"),
		      				'data_size' => $dataSizes,
		      				'status' => $projstatus[$da['task_status']],
		      				'completion_date' => $projcompletedated,
		      				'late' => $daylate,
		      				'sla' => $services,
		      			);
	      		}	
      		}
			
      		$filename = "SLATurnTimeByClientCases_" . date('m_d_Y', time()) . ".xls";
      		$content = $this->renderPartial('excelexport_projturntimbyservice', array(
      				"final_data" => $final_data,
      				'startdate' => $start_date,
      				'enddate' => $end_date
      		),true);
      		
      		$table    = $content;
      		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
      		file_put_contents($tmpfile, $table);
      		
      		$objPHPExcel     = new PHPExcel();
      		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
      		$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
      		unlink($tmpfile); // delete temporary file because it isn't needed anymore
      		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
      		header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
      		header('Cache-Control: max-age=0');
      		// Creates a writer to output the $objPHPExcel's content
      		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      		$writer->save('php://output');
      		exit;
    }
    
    /**
     * SLA turn time service report
     * @return
     */
    public function actionTurnaroundtimebyservicedata()
    {
    	$this->layout = 'report';
    	$filter_data = Yii::$app->request->post('filtervalue');
    	$export = Yii::$app->request->post('dataexport');
    	
    	$start_date = Yii::$app->request->post('start_date');
    	$end_date = Yii::$app->request->post('end_date');
    	$task_status = Yii::$app->request->post('task_status');
    	$chkselectservice = Yii::$app->request->post('chkselectservice');
    	$servicestatus = Yii::$app->request->post('servicestatus');
    	$team_location = Yii::$app->request->post('teamlocation');
    	$datedropdown = Yii::$app->request->post('datedropdown');
    	
    	if(Yii::$app->request->post()){
    		$post_data = array();
    		if(isset($export)){
    			$filter_data = json_decode(Yii::$app->request->post('filtervalue'));
    			$start_date = $filter_data->start_date;
    			$end_date = $filter_data->end_date; 
    			$task_status = $filter_data->task_status; 
    			$chkselectservice = $filter_data->chkselectservice; 
    			$servicestatus =  $filter_data->servicestatus; 
    			$team_location = $filter_data->teamlocation; 
    			$datedropdown =$filter_data->datedropdown; 
    		} else if(isset($filter_data) && !empty($filter_data)){
	    		$save = '';
	    	}else{
	    		$save = 'save';
	    		$post_data['start_date'] = Yii::$app->request->post('start_date');
	    		$post_data['end_date'] = Yii::$app->request->post('end_date');
	    		$post_data['datedropdown'] = Yii::$app->request->post('datedropdown');
	    		$post_data['chkselectservice'] = Yii::$app->request->post('chkselectservice');
	    		$post_data['servicestatus'] = Yii::$app->request->post('servicestatus');
	    		$post_data['teamlocation'] = Yii::$app->request->post('teamlocation');
	    		$post_data['task_status'] = Yii::$app->request->post('task_status');
	    	}
	    }
	    $datevalue = $this->getalldatebydropdown($datedropdown);
	  	if(count($datevalue)>0)
    		$start_date = $datevalue['start_date']; $end_date = $datevalue['end_date'];
    	
    	$team_locations = "";
    	if (count($team_location) > 0) {
    		$team_locations = implode(",", array_unique($team_location));
    	}
 
    	$projectstatus = "";
    	if (count($task_status) > 0) {
    		$projectstatus = implode(",", array_unique($task_status));
    	}
    	$datesql = $this->getdatesqlformat($start_date, $end_date);
     	$maxdays = 1;
     	
     	$working_hrs = TeamserviceSlaBusinessHours::find()->select('workinghours')->orderBy('id DESC')->one();
     	if (count($servicestatus) > 0){
     		$i = 0;
     		$serviceCond = "";
     		foreach ($servicestatus as $teamserviceId){
     			$teamservicedata = Teamservice::findOne($teamserviceId);
     			$taskdataArr = Tasks::find()->select(['t.id'])->from('tbl_tasks as t')->with([
     				'clientCase' => function(\yii\db\ActiveQuery $query){
     					$query->select(['tbl_client_case.id','tbl_client_case.case_name'])->where(['tbl_client_case.is_close'=>0]);
     				},
     				'taskInstruct' => function(\yii\db\ActiveQuery $query){
				    		$query->select(['tbl_task_instruct.id','tbl_task_instruct.task_id','tbl_task_instruct.isactive', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue']);
				    		$query->with(['taskInstructServicetasks' => function(\yii\db\ActiveQuery $query) {
				    			$query->select(['tbl_task_instruct_servicetask.team_id','tbl_task_instruct_servicetask.teamservice_sla_id','tbl_task_instruct_servicetask.team_loc','tbl_task_instruct_servicetask.est_time','tbl_task_instruct_servicetask.task_instruct_id','tbl_task_instruct_servicetask.servicetask_id','tbl_task_instruct_servicetask.teamservice_id']);
				    		}]);
				    		$query->where("tbl_task_instruct.isactive=1");
			    		},
				    ])
				   ->innerJoinWith(['tasksTeams' => function(\yii\db\ActiveQuery $query) use($teamserviceId,$team_locations){ 
			    		$query->select(['tbl_tasks_teams.id','tbl_tasks_teams.teamservice_id','tbl_tasks_teams.task_id','tbl_tasks_teams.id','tbl_tasks_teams.team_loc','tsla.teamservice_sla_id']);
			    		$query->join('INNER JOIN','tbl_tasks_team_sla as tsla','tbl_tasks_teams.id=tsla.tasks_teams_id');
				    	$query->where("tbl_tasks_teams.teamservice_id=".$teamserviceId);
				    	if(isset($team_locations) && $team_locations!=''){
				    		$query->andWhere("tbl_tasks_teams.team_loc IN (".$team_locations.")");
				    	}
			    }])->where("t.task_cancel=0 AND t.task_status IN (" . $projectstatus . ") AND $datesql")->asArray()->all();
	    		if (count($taskdataArr) > 0) {
			    	$taskidArr = array();
	    			$tday_spent = array();
	    			$totaldays = 0;
	    			$task_status = 0;
	    			foreach ($taskdataArr as $taskdata) {
	    				
	    				$serviceTasksla = $taskdata['tasksTeams'];
	    				$todo_status = (new Tasks)->getSlaServiceTodoStatusByPojectId($taskdata['id'], $taskdata['tasksTeams']);
	    				if (!empty($serviceTasksla)) {
	    					 $esttime = array();
                             foreach ($serviceTasksla as $servicetask) {
                             		$servicestatus = (new Tasks)->getSlaTeamserviceStatusByPojectId($taskdata['id'],$servicetask['teamservice_id'],$taskdata['taskInstruct']['taskInstructServicetasks'],"iscompleted");
                             		if($servicestatus != 4)
	                           			continue;
	                           		
                             		$services[$servicetask['teamservice_id']]['task_id'] = $taskdata['id'];
                             		$services[$servicetask['teamservice_id']]['allotted_days'] = 0;
	                           		$services[$servicetask['teamservice_id']]['day_spent'] = 0;
	                           		$services[$servicetask['teamservice_id']]['stop_clk_business_days'] = 0;
	                           		$services[$servicetask['teamservice_id']]['toto_followup_days'] = (new Tasks)->getTotalSLADaysTodoFollowup($taskdata['id'], $servicetask['teamservice_id'], $todo_status);
	                           		
	                           		if(isset($servicetask['teamservice_sla_id']) && $servicetask['teamservice_sla_id'] != 0 && $servicetask['teamservice_sla_id'] != ""){
	                           			$allocated_time = TeamserviceSla::findOne($servicetask['teamservice_sla_id']);
	                           			if (!empty($allocated_time->attributes)) {
	                           				if ($allocated_time->del_time_unit == 2) {
	                           					$days = $allocated_time->del_qty;
	                           				} else {
	                           					if ($allocated_time->del_qty >= $working_hrs)
	                           						$days = ($allocated_time->del_qty / $working_hrs);
	                           					else
	                           						$days = 1;
	                           				}
	                           				$services[$servicetask['teamservice_id']]['allotted_days'] = $days;
	                           			}
	                           			$services[$servicetask['teamservice_id']]['day_spent'] = (new Tasks)->getSlaTotalDaySpent($taskdata['id'], $servicetask['teamservice_id'], 4, "");
	                           			$services[$servicetask['teamservice_id']]['stop_clk_business_days'] = (new Tasks)->getSlaStopClkDays($taskdata['id'], $servicetask['teamservice_id']);
	                           		}
	                           		$services[$servicetask['teamservice_id']]['days_spent_minus_followup'] = ($services[$servicetask['teamservice_id']]['day_spent'] - $services[$servicetask['teamservice_id']]['stop_clk_business_days']);
	                           		if (isset($tday_spent[$services[$servicetask['teamservice_id']]['days_spent_minus_followup']]))
	                           			$tday_spent[$services[$servicetask['teamservice_id']]['days_spent_minus_followup']] += 1;
	                           		else
	                           			$tday_spent[$services[$servicetask['teamservice_id']]['days_spent_minus_followup']] = 1;
	
	                           		$totaldays = $totaldays + $services[$servicetask['teamservice_id']]['days_spent_minus_followup']; 
	                           		$services[$servicetask['teamservice_id']]['days_late'] = ($services[$servicetask['teamservice_id']]['day_spent'] - $services[$servicetask['teamservice_id']]['stop_clk_business_days']);
	                         }
	                    }
	                    $taskIdCountArr[$taskdata['id']] = 1;
                        $taskidArr[$taskdata['id']] = $taskdata['id'];
	    			}
	    			$actualdaysArr = range(1, $totaldays);
	    		    if ($totaldays != 0) {
	    				foreach ($actualdaysArr as $actualday) {
	    					$chart_data[$i][$actualday] = array($actualday, 0, "label" => $teamservicedata->service_name);
	    					if (isset($tday_spent[$actualday])) {
	    						$chart_data[$i][$actualday] = array($actualday, $tday_spent[$actualday], "label" => $teamservicedata->service_name);
	    						if($actualday > $maxdays)
	    							$maxdays = $actualday;
	    					}
	    					$ticks[$actualday] = $actualday;
	    				}
	    			}
	    		}
	    		$i++;
	    	}
	   	}
	   	
	   	$finalkeys = array();
	   	$mychart_data2 = array();
	   	$lable = array();
	   	if (!empty($chart_data)) {
	   		foreach ($chart_data as $k => $v) {
	   			foreach ($v as $data) {
	   				if ($data[1] > 0)
	   					$finalkeys[$k] = $k;
	   			}
	   		}
	   		foreach ($chart_data as $k => $v) {
	   			if (!in_array($k, $finalkeys))
	   				unset($chart_data[$k]);
	   		}
	   		$k = 0;
	   		foreach ($chart_data as $kdata => $kvalue) {
	   			foreach ($kvalue as $key=>$kv) {
	   				if($key > $maxdays)
	   					break;
	   				$mychart_data[$k][] = array(
   						$kv[0],
   						$kv[1]
	   				);
	   				$mychart_data2[$k][] = $kv[1];
	   				if (!in_array($kv['label'], $lable)) {
	   					$teamtrendlinechartsri[html_entity_decode($kv['label'])] = array(
	   						"label" => $kv['label']
	   					);
	   					$lable[$kv['label']] = $kv['label'];
	   				}
	   			}
	   			$k++;
	   		}
	   	}
	   	$teamverticalchartticks = $ticks;
	   	$teamverticalchart = json_encode($mychart_data2);

	   	if (isset($export) && $export == "export"){
	   		$filename = "SLATurnTimeByService_" . date('m_d_Y', time()) . ".xlsx";
	   		$content = $this->renderPartial('exportturnaroundbyservice', array(
	   				'serviceverticalchartticks' => $teamverticalchartticks,
	   				"serviceverticalchart" => $teamverticalchart,
	   				"teamtrendlinechartsri" => $teamtrendlinechartsri,
	   				'mychart_data2' => $mychart_data2,
	   				'startdate' => $start_date,
	   				'status' => $filter_data->task_status,
	   				'teamlocs'=> $filter_data->teamlocation,
	   				'chart_data' => $chart_data,
	   				'post_data' => $post_data,
	   				'enddate' => $end_date
	   			),true);
	   	
	   		$table = $content;
      		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
      		file_put_contents($tmpfile, $table);
      		
      		$objPHPExcel     = new PHPExcel();
      		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
      		$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
      		unlink($tmpfile); // delete temporary file because it isn't needed anymore
      		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
      		header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
      		header('Cache-Control: max-age=0');
      		// Creates a writer to output the $objPHPExcel's content
      		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      		$writer->save('php://output');
      		exit;
       	}else{
	   		return $this->render('runturnaroundbyservice', array(
		   		'serviceverticalchartticks' => $teamverticalchartticks,
		   		"serviceverticalchart" => $teamverticalchart,
		   		"teamtrendlinechartsri" => $teamtrendlinechartsri,
		   		'post_data' => $post_data,	
		   		'startdate' => $start_date,
		   		'save' => $save,
		   		'enddate' => $end_date), false, true);
	   	}
    }
    
    /**
     * SLA turn time servcies of accuracy report
     * @return
     */
    public function actionSlaTurntimeService()
    {
    	$this->layout = 'report';
    	$userId = Yii::$app->user->identity->id;
    	$role_info = Yii::$app->user->identity->role;
    	$roleId = $role_info->id;
    	$filter_data = Yii::$app->request->post('filtervalue');
    	
    	$roletypes = array();
    	if (isset($role_info->role_type) && $role_info->role_type != "")
    		$roletypes = explode(",", $role_info->role_type);
    	
    	$where='team.team_id!=1 and t.team_id!=0';
    	if($roleId != 0){
    		$where.=" AND userId=".$userId;
    	}
    	$teamList = ArrayHelper::map(ProjectSecurity::find()->select(['ps.team_id','t.team_name'])->from('tbl_project_security as ps')
    			->join('INNER JOIN','tbl_team as t','t.id=ps.team_id')->all(), 'team_id', 'team_name');
    	
    	if (in_array(1, $roletypes))
    		$teamList[1] = 'Case Manager';
    	
    	asort($teamList);
    	$team_locs = array(0);
    	$teamLocation = ArrayHelper::map(Team::find()->select(['tl.team_loc as team_loc','tm.team_location_name'])->from('tbl_team as t')
    		->join('LEFT JOIN','tbl_team_locs as tl','t.id=tl.team_id')
    		->join('LEFT JOIN','tbl_teamlocation_master as tm','tm.id=tl.team_loc')
    		->where('tm.remove=0 AND tl.team_id IN('.implode(',', array_keys($teamList)).')')->orderBy('tl.team_loc')->all(), 'team_loc','team_location_name');
    	
    	$teamservices = ArrayHelper::map(Teamservice::find()->select(['service_name','id'])->all(), 'id','service_name');
    	
    	if (isset($filter_data) && $filter_data != ""){
    		$filter_data = json_decode($filter_data);
    	}
    	
    	return $this->render('slaturntimeservice',['teamLocation' => $teamLocation, 'filter_data' => $filter_data, 'team_services' => $teamservices, 'filter_data' => $filter_data]);
    }
    
    /**
     * get sla turn-time project services in accuracy report
     * @return
     */
    public function actionSlaTurntimeProjectService()
    {
    	$this->layout = 'report';
    	$userId = Yii::$app->user->identity->id;
    	$role_info = Yii::$app->user->identity->role;
    	$roleId = $role_info->id;
    	$filter_data = Yii::$app->request->post('filtervalue');
    	 
    	$roletypes = array();
    	if (isset($role_info->role_type) && $role_info->role_type != "")
    		$roletypes = explode(",", $role_info->role_type);
    	 
    	$where='ps.team_id!=1 and ps.team_id!=0';
    	if($roleId != 0){
    		$where.=" AND ps.userId=".$userId;
    	}
    	
    	$teamList = ArrayHelper::map(ProjectSecurity::find()->select(['ps.team_id','t.team_name'])
    	->from('tbl_project_security as ps')->join('INNER JOIN','tbl_team as t','t.id=ps.team_id')->where("$where")->all(), 'team_id', 'team_name');
    	 
    	if (in_array(1, $roletypes))
    		$teamList[1] = 'Case Manager';
    	 
    	asort($teamList);
    	$team_locs = array(0);
    	$teamLocation = ArrayHelper::map(Team::find()->select(['tl.team_loc as team_loc','tm.team_location_name'])->from('tbl_team as t')
    		->join('LEFT JOIN','tbl_team_locs as tl','t.id=tl.team_id')
    		->join('LEFT JOIN','tbl_teamlocation_master as tm','tm.id=tl.team_loc')
    		->where('tm.remove=0 AND tl.team_id IN('.implode(',', array_keys($teamList)).')')->orderBy('tl.team_loc')->all(), 'team_loc','team_location_name');
    	$teamservices = ArrayHelper::map(Teamservice::find()->select(['service_name','id'])->all(), 'id','service_name');
    	
    	$filter_data = '';
    	if (isset($filter_data) && $filter_data != ""){
    		$filter_data = json_decode($filter_data);
    	}
    	return $this->render('slaturntimeprojectservice',['teamLocation' => $teamLocation, 'filter_data' => $filter_data, 'team_services' => $teamservices]);
    }
    
    public function actionSlaAccuracyService()
    {
    	$this->layout = 'report';
    	$userId = Yii::$app->user->identity->id;
    	$role_info = Yii::$app->user->identity->role;
    	$roleId = $role_info->id;
    	$filter_data = Yii::$app->request->post('filtervalue');
    	
    	$roletypes = array();
    	if (isset($role_info->role_type) && $role_info->role_type != "")
    		$roletypes = explode(",", $role_info->role_type);
    	
    	$where='team.team_id!=1 and t.team_id!=0';
    	if($roleId != 0){
    		$where.=" AND userId=".$userId;
    	}
    	$teamList = ArrayHelper::map(ProjectSecurity::find()->select(['ps.team_id','t.team_name'])->from('tbl_project_security as ps')
    			->join('INNER JOIN','tbl_team as t','t.id=ps.team_id')->all(), 'team_id', 'team_name');
    	
    	if (in_array(1, $roletypes))
    		$teamList[1] = 'Case Manager';
    	
    	asort($teamList);
    	$team_locs = array(0);
    	$teamLocation = ArrayHelper::map(Team::find()->select(['tl.team_loc as team_loc','tm.team_location_name'])->from('tbl_team as t')
    			->join('LEFT JOIN','tbl_team_locs as tl','t.id=tl.team_id')
    			->join('LEFT JOIN','tbl_teamlocation_master as tm','tm.id=tl.team_loc')
    			->where('tm.remove=0 AND tl.team_id IN('.implode(',', array_keys($teamList)).')')->orderBy('tl.team_loc')->all(), 'team_loc','team_location_name');
    	
    	$teamservices = ArrayHelper::map(Teamservice::find()->select(['service_name','id'])->all(), 'id','service_name');
    	$filter_data = '';
    	if (isset($filter_data) && $filter_data != ""){
    		$filter_data = json_decode($filter_data);
    	}
    	return $this->render('slaaccuracyservice',['teamLocation' => $teamLocation, 'filter_data' => $filter_data, 'team_services' => $teamservices]);
    }
    
    /*
     * Save the SLA turn-time by service
     * @return
     */
    public function actionSaveturntimeservice(){
    	$filter_name = Yii::$app->request->post('filter_name');
    	$filter_data = Yii::$app->request->post('filtervalue');
    	if(isset($filter_name) && $filter_name!=''){
    		$save_filter = new SavedFilters();
    		$save_filter->user_id = Yii::$app->user->identity->id;
    		$save_filter->filter_name = $filter_name;
    		$save_filter->filter_type = 2;
    		$save_filter->filter_attributes = $filter_data;
    		if($save_filter->save()){
    			echo "Ok";
    		}
    	}
    }
    
    /*
     * Save the Chart.
     * */
    public function actionSaveaccuracyreport()
    {
    	$filter_name = Yii::$app->request->post('filter_name');
    	$filtervalueArr = array(
    		'start_date' => Yii::$app->request->post('start_date'),
    		'end_date' => Yii::$app->request->post('end_date'),
    		'datedropdown' => Yii::$app->request->post('datedropdown'),
    		'chkprojectstatus' => Yii::$app->request->post('chkprojectstatus'),
    		'task_status' => Yii::$app->request->post('task_status'),
    		'chkclientcases' => Yii::$app->request->post('chkclientcases'),
    		'chkselectservice' => Yii::$app->request->post('chkselectservice'),
    		'servicestatus' => Yii::$app->request->post('servicestatus'),
    	);
    	$filter_data = json_encode($filtervalueArr);
    	if(isset($filtervalueArr) && !empty($filtervalueArr)){
    		$save_filter = new SavedFilters();
    		$save_filter->user_id = Yii::$app->user->identity->id;
    		$save_filter->filter_name = $filter_name;
    		$save_filter->filter_type = 2;
    		$save_filter->filter_attributes = $filter_data;
    		if($save_filter->save()){
    			echo "Ok";
    		}	
    	}
    	die();
    }
}
