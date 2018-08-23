<?php

namespace app\controllers;

use Yii;
use app\models\TeamlocationMaster;
use app\models\Tasks;
use yii\helpers\ArrayHelper;
use app\models\Teamservice;
use app\models\TasksUnitsBilling;
use app\models\Options;
use app\models\Servicetask;
use app\models\Team;
//use app\models\SavedFilters;
use app\models\TasksUnitsData;
use app\models\search\UnitSearch;
use app\models\Unit;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PHPExcel;
use PHPExcel_IOFactory;

/**
 * ProcessingReportController implements the CRUD actions for  model.
 */
class ProcessingReportController extends Controller
{
    public function behaviors(){
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => ['delete' => ['get'],],],
        	];
    }
    
    /**
     * Processing Report Layout
     */
    public function actionIndex(){}

    /**
     * Data Processed by service page
     * @return
     */
    public function actionDataService(){
    	$this->layout = 'report';
    	$filter_data =  Yii::$app->request->post('filtervalue');
    	if(Yii::$app->request->post()){
    		if(isset($filter_data) && !empty($filter_data)){
    			$filter_data = json_decode($filter_data);
    		}
    	}
    	/** get all team services **/
    	$teams = $this->getteamservices();
    	return $this->render('dataservice', ['teamLocation' => $teams['teamLocation'], 'teamservices' => $teams['teamservices'], 'filter_data' => $filter_data]);
    }
    
    /**
     * Data Processed by client/case page
     * @return
     */
    public function actionDataProcessing(){
    	$this->layout = 'report';
    	$filter_data =  Yii::$app->request->post('filtervalue');
    	if(Yii::$app->request->post()){
    		if(isset($filter_data) && !empty($filter_data)){
    			$filter_data = json_decode($filter_data);
    		}
	    }
	    /** get all team services **/
	    $teams = $this->getteamservices();
    	return $this->render('dataprocessing', ['teamLocation' => $teams['teamLocation'], 'teamservices' => $teams['teamservices'], 'filter_data' => $filter_data]);
    }
    
    /**
     * get team services and teamlocations
     * @return 
     */
    public function getteamservices(){
    	$teams = array();
    	$teamLocation = TeamLocationMaster:: find()->select('id','team_location_name')->where('remove=0')->orderBy('id ASC')->all();
    	$teamservices1 = ArrayHelper::map(TasksUnitsBilling::find()->select(['teamservice.service_name as service_name','teamservice.id as teamservice_id'])->where('t.remove=0')->from('tbl_tasks_units_billing as billingdatas')
    		->join('INNER JOIN','tbl_pricing as t','t.id=billingdatas.pricing_id')
    		->join('INNER JOIN','tbl_teamservice as teamservice','t.team_id=teamservice.teamid')
    		->all(),'teamservice_id','service_name');
    	$teamservices2 =ArrayHelper::map(TasksUnitsData::find()->select(['teamservice.service_name as service_name','teamservice.id as teamservice_id'])->from('tbl_tasks_units_data as taskunitdata')
    		->join('INNER JOIN','tbl_servicetask as servicetask','servicetask.id = taskunitdata.task_id')
    		->join('INNER JOIN','tbl_teamservice as teamservice','servicetask.teamId=teamservice.teamid')
    		->join('INNER JOIN','tbl_form_builder as formbuilder','taskunitdata.form_builder_id=formbuilder.id')->where('formbuilder.element_field_type=1')
    		->groupBy(['teamservice.id','teamservice.service_name'])->all(),'teamservice_id','service_name');
    	$teamservices = (array_replace($teamservices1,$teamservices2));
    	$teams['teamLocation'] = $teamLocation;
    	$teams['teamservices'] = $teamservices;
    	return $teams;
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
     * get All Clients or CaseClients for processing report
     * @return 
     */
    public function actionGetCaseclientsCriteria(){
    	$start_date = Yii::$app->request->post('start_date');
    	$end_date = Yii::$app->request->post('end_date');
    	$datedropdown = Yii::$app->request->post('datedropdown');
    	$type = Yii::$app->request->post('type');
    	$filter_data =  Yii::$app->request->post('filter_data');
    	if(Yii::$app->request->post()){
    		if(isset($filter_data) && !empty($filter_data)){
    			$filter_data = json_decode($filter_data);
    			$start_date = $filter_data->start_date;
    			$end_date = $filter_data->end_date;
    			$datedropdown = $filter_data->datedropdown;
    			if(isset($filter_data->client) && $filter_data->client!='')
    				$type = 'client';	
    			if(isset($filter_data->clientcases) && $filter_data->clientcases!='')
    				$type = 'clientcases';
    		}
    	}
    	
    	if(isset($datedropdown) && $datedropdown!=0){
    		$date = $this->getalldatebydropdown($datedropdown);
    		if(!empty($date))
    			$start_date = $date['start_date']; $end_date = $date['end_date'];
    	}
    	$datesql = $this->getdatesqlformat($start_date,$end_date);
    	$clientli='';
    	/** get client **/
    	if($type=='client'){
    		$casesecurity_data1 = TasksUnitsBilling::find()->select(['client.id as client_id','client.client_name'])
	    		->from('tbl_tasks_units_billing as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
	    		->join('INNER JOIN','tbl_client as client','ta.client_id=client.id')
	    		->where("$datesql")
	    		->asArray()->orderBy('client.client_name')->groupBy('client.id,client.client_name')->all();
    		$arr = array();
    		foreach ($casesecurity_data1 as $key => $client) {
    			if (!in_array($client['client_id'], $arr)) {
    				$checked = "";
    				if (isset($filter_data->client) && in_array($client['client_id'], $filter_data->client))
    				  	$checked = 'checked="checked"';
    				$clientli .= '<li><input type="checkbox" class="client tutmbs" name="client[]" ' . $checked . ' id="client_' . $client["client_id"] . '" value="' . $client["client_id"] . '" aria-label="'.$client["client_name"].'" ><label for="client_' . $client["client_id"] . '" class="clientlabel">' . $client["client_name"]. '</label></li>';
    				$arr[] = $client["client_id"];
    			}
    		}
			
    		$casesecurity_data2 = TasksUnitsData::find()->select(['client.id as client_id','client.client_name'])
    			->from('tbl_tasks_units_data as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
    			->join('INNER JOIN','tbl_client as client','ta.client_id=client.id')
    			->join('INNER JOIN','tbl_form_builder as f','t.form_builder_id = f.id')
    			->where("$datesql AND (f.element_field_type=1)")
    			->asArray()->orderBy('client.client_name')->groupBy('client.id,client.client_name')->all();
    		
    		foreach ($casesecurity_data2 as $key => $client) {
    			if (!in_array($client['client_id'], $arr)) {
     				$checked = "";
     				if (isset($filter_data->client) && in_array($client['client_id'], $filter_data->client))
     				  	$checked = 'checked="checked"';
     				$clientli .= '<li><input type="checkbox" class="client tutmbs" name="client[]" ' . $checked . ' id="client_' . $client["client_id"] . '" value="' . $client["client_id"] . '"  aria-label="'.$client["client_name"].'" ><label for="client_' . $client["client_id"] . '" class="clientlabel">' . $client["client_name"]. '</label></li>';
     				$arr[] = $client["client_id"];
    			}
    		}
    		echo $clientli; die();
    	}
    	
    	$clientcaseli='';
    	/** get clientcase **/	
    	if($type=='clientcases'){
    		$casesecurity_data1 = TasksUnitsBilling::find()->select(['ta.client_case_id','client.client_name','client.id','clientcase.case_name','clientcase.id'])
    		->from('tbl_tasks_units_billing as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
    		->join('INNER JOIN','tbl_client as client','ta.client_id=client.id')
    		->join('INNER JOIN','tbl_client_case as clientcase','ta.client_case_id=clientcase.id')
    		->where("$datesql")
    		->asArray()->orderBy('client.client_name','clientcase.case_name')->groupBy(['ta.client_case_id','clientcase.id','clientcase.case_name','client.id','client.client_name'])->all();
    		
    		$arr = array();
    		foreach ($casesecurity_data1 as $key => $client) {
    			if (!in_array($client['client_case_id'], $arr)){
    				$checked = "";
           			if (isset($filter_data->clientcases) && in_array($client['client_case_id'], $filter_data->clientcases))
           				$checked = 'checked="checked"';
    				$clientcaseli .= '<li><input type="checkbox" class="clientcases tutmbs" name="clientcases[]" ' . $checked . ' id="clientcases_' . $client["client_case_id"]  . '" value="' . $client["client_case_id"] . '" aria-label="'.$client["client_name"] . ' - ' . $client["case_name"] .'"><label for="clientcases_' . $client["client_case_id"]  . '" class="clientcaselabel">' . $client["client_name"] . ' - ' . $client["case_name"] . '</label></li>';
    				$arr[] = $client['client_case_id'];
    			}
    		}
    		
    		$casesecurity_data2 = TasksUnitsData::find()->select(['ta.client_case_id','client.client_name','client.id','clientcase.case_name','clientcase.id'])
    		->from('tbl_tasks_units_data as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
    		->join('INNER JOIN','tbl_client as client','ta.client_id=client.id')
    		->join('INNER JOIN','tbl_client_case as clientcase','ta.client_case_id=clientcase.id')
    		->join('INNER JOIN','tbl_form_builder as f','t.form_builder_id = f.id')
    		->where("$datesql AND (f.element_field_type=1)")
    		->asArray()->orderBy('client.client_name','clientcase.case_name')->groupBy(['ta.client_case_id','clientcase.id','clientcase.case_name','client.id','client.client_name'])->all();
	    	
	    	foreach ($casesecurity_data2 as $key => $client) {
	    		if (!in_array($client['client_case_id'], $arr)){
	    			$checked = "";
	      			if (isset($filter_data->clientcases) && in_array($client['client_case_id'], $filter_data->clientcases))
	      				$checked = 'checked="checked"';
	    			$clientcaseli .= '<li><input type="checkbox" class="clientcases tutmbs" name="clientcases[]" ' . $checked . ' id="clientcases_' . $client["client_case_id"]  . '" value="' . $client["client_case_id"] . '" aria-label="'.$client["client_name"] . ' - ' . $client["case_name"] .'"><label for="clientcases_' . $client["client_case_id"]  . '" class="clientcaselabel">' . $client["client_name"] . ' - ' . $client["case_name"] . '</label></li>';
	    			$arr[] = $client['client_case_id'];
	    		}
	    	}
        	echo $clientcaseli; die();
    	}
    }
    
    /**
     * get the statistics(Data out) & service locations for processing by client/case report & service report
     * @return
     */
    public function actionGetUnitDataByServiceTaskCriteria(){
	    	$start_date = Yii::$app->request->post('start_date');
	    	$end_date = Yii::$app->request->post('end_date');
	    	$datedropdown = Yii::$app->request->post('datedropdown');
	    	$team_service = Yii::$app->request->post('team_service');
	    	$client = Yii::$app->request->post('client');
	    	$clientcase = Yii::$app->request->post('clientcases');
	     	$filter_data =  Yii::$app->request->post('filter_data');
	    	if(Yii::$app->request->post()){
	    		if(isset($filter_data) && !empty($filter_data)){
	    			$filter_data = json_decode($filter_data);
	    			$start_date = $filter_data->start_date;
	    			$end_date = $filter_data->end_date;
	    			$datedropdown = $filter_data->datedropdown;
	    			$team_service = $filter_data->team_service;
	    			if(isset($filter_data->chkclientcases) && $filter_data->chkclientcases == 'client')
	    				$client = implode(",", $filter_data->client);
	    			if(isset($filter_data->chkclientcases) && $filter_data->chkclientcases == 'clientcases')
	    				$clientcase = implode(",", $filter_data->clientcases);
	    		}
	    	}
	    	
	    	if(isset($datedropdown) && $datedropdown!=0){
	    		$date = $this->getalldatebydropdown($datedropdown);
	    		if(!empty($date))
	    			$start_date = $date['start_date']; $end_date = $date['end_date'];
	    	}
	    	$datesql = $this->getdatesqlformat($start_date,$end_date);

	    	$clientcasesql = "";
	    	if($client != "")
	    		$clientcasesql = " AND ta.client_id IN ($client)";
	    	if($clientcase != "")
	    		$clientcasesql = " AND ta.client_case_id IN ($clientcase)";
	    	
	    	$datesql = $this->getdatesqlformat($start_date, $end_date);
    	 
	    	/* Start : To get Billing Data */
	    	$teamservicear = array();
	    	$teamLocs = array();
	    	$teamLoc = "<li class='by_teamlocs'><label style='color:#222;'>No Service Locations are applicable for selected criteria</label></li>";
	    	$data = "<option value='0'>Select Statistics</option>";
	    	$task_unitbillingdata = TasksUnitsBilling::find()
		    	->select(['price_point','t.id as billing_id','t.pricing_id','teamLoc.id as LocationId','teamLoc.team_location_name as LocationName'])
		    	->from('tbl_tasks_units_billing as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
		    	->join('INNER JOIN','tbl_tasks_units as taskunit','taskunit.id=t.tasks_unit_id')
		    	->join('INNER JOIN','tbl_task_instruct_servicetask as taskinstruct','taskunit.task_instruct_servicetask_id=taskinstruct.id')
		    	->join('INNER JOIN','tbl_teamlocation_master as teamLoc','teamLoc.id = taskinstruct.team_loc')
		    	->join('INNER JOIN','tbl_client as client','ta.client_id=client.id')
		    	->join('INNER JOIN','tbl_pricing as tp','tp.id=t.pricing_id')
		    	->join('INNER JOIN','tbl_teamservice as teamservice','tp.team_id=teamservice.teamid')
		    	->where("(teamservice.id=$team_service AND tp.remove=0) AND $datesql $clientcasesql")
		    	->asArray()->all();

	    	if (!empty($task_unitbillingdata)) {
	    		foreach ($task_unitbillingdata as $tasksdata) {
	    			if($tasksdata['LocationId']!="" && !in_array($tasksdata['LocationId'], $teamLocs)){
	    				$checked = "";
	    				if(isset($filter_data->teamlocs) && in_array($tasksdata['LocationId'], $filter_data->teamlocs)){
	    					$checked = "checked='checked'";
	    				}
	    				if($teamLoc != "<li class='by_teamlocs'><label style='color:#222;'>No Service Locations are applicable for selected criteria</label></li>")
	    					$teamLoc .= "<li class='by_teamlocs'><input {$checked} type='checkbox' id='telocname_{$tasksdata['LocationId']}' value='{$tasksdata['LocationId']}' class='teloc' name='teamlocs[]' aria-label='{$tasksdata['LocationName']}'><label style='color:#222;' class='locationlabel' for='telocname_{$tasksdata['LocationId']}' id='telocname_{$tasksdata['LocationId']}'>{$tasksdata['LocationName']}</label></li>";
	    				else
	    					$teamLoc = "<li class='by_teamlocs'><input {$checked} type='checkbox' id='telocname_{$tasksdata['LocationId']}' value='{$tasksdata['LocationId']}' class='teloc' name='teamlocs[]' aria-label='{$tasksdata['LocationName']}'><label style='color:#222;' class='locationlabel' for='telocname_{$tasksdata['LocationId']}' id='telocname_{$tasksdata['LocationId']}'>{$tasksdata['LocationName']}</label></li>";
	    	
	    				$teamLocs[$tasksdata['LocationId']] = $tasksdata['LocationId'];
	    			}
	    			$teamservicear['Bill'][$tasksdata['price_point']] = $tasksdata['pricing_id'];
	    		}
	    	}
	    	
	    	if (!empty($teamservicear)) {
	    		foreach ($teamservicear['Bill'] as $key => $vals) {
	    			$keyvalue = $key;
	    			$checked = "";
	    			if ("Bill||$vals||$keyvalue" == $filter_data->statistics)
	    				$checked = "selected='selected'";
	    			$data .= "<option value='Bill||$vals||$keyvalue' $checked >Bill:$key</option>";
	    		}
	    	}
	    	/* End : To get Billing Data */
    	
    		/*  Start : To get Unit Data  */
    		$dataAr = array();
    		$taskdataArr12 = TasksUnitsData::find()
    	 		->select(['f.element_label','f.element_id','t.id as taskunitdata_id','teamLoc.id as LocationId','teamLoc.team_location_name as LocationName'])
    	 		->from('tbl_tasks_units_data as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
    	 		->join('INNER JOIN','tbl_task_instruct_servicetask as taskinstruct','t.task_instruct_servicetask_id=taskinstruct.id')
    	 		->join('LEFT JOIN','tbl_teamlocation_master as teamLoc','teamLoc.id = taskinstruct.team_loc')
    	 		->join('INNER JOIN','tbl_form_builder as f','t.form_builder_id=f.id')
 				->where("(f.element_field_type=1 AND taskinstruct.teamservice_id=$team_service AND f.form_type=2) AND $datesql $clientcasesql")
				->asArray()->all();
    			
    			if(!empty($taskdataArr12)){
    				foreach($taskdataArr12 as $dataout){
    					if($dataout['LocationId']!="" && !in_array($dataout['LocationId'], $teamLocs)) {
    						$checked = "";
     						if(isset($filter_data->teamlocs) && in_array($dataout['LocationId'], $filter_data->teamlocs)) {
     							$checked = "checked='checked'";
     						}
    						if($teamLoc != "<li class='by_teamlocs'><label style='color:#222;'>No Service Locations are applicable for selected criteria</label></li>")
    							$teamLoc .= "<li class='by_teamlocs'><input {$checked} type='checkbox' id='telocname_{$tasksdata['LocationId']}' value='{$dataout['LocationId']}' class='teloc' name='teamlocs[]' aria-label='{$dataout['LocationName']}'><label class='locationlabel' for='telocname_{$tasksdata['LocationId']}' style='color:#222;' id='telocname_{$dataout['LocationId']}'>{$dataout['LocationName']}</label></li>";
    						else
    							$teamLoc = "<li class='by_teamlocs'><input {$checked} type='checkbox' id='telocname_{$tasksdata['LocationId']}' value='{$dataout['LocationId']}' class='teloc' name='teamlocs[]' aria-label='{$dataout['LocationName']}'><label class='locationlabel' for='telocname_{$tasksdata['LocationId']}' style='color:#222;' id='telocname_{$dataout['LocationId']}'>{$dataout['LocationName']}</label></li>";
    							$teamLocs[$dataout['LocationId']] = $dataout['LocationId'];
    						}
    						$dataAr['Data'][$dataout['element_label']] = $dataout['element_id'];
    					}
    				}
   
	    			if (!empty($dataAr)) {
	    				foreach ($dataAr['Data'] as $key => $vals) {
		    				$keyvalue = $key;
		    				$checked = "";
		    				if ("Data||$vals||$keyvalue" == $filter_data->statistics)
		    					$checked = "selected='selected'";
		    				$data .= "<option value='Data||$vals||$keyvalue' $checked >Data:$key</option>";
	    				}
	    			}
		    	$data_ar = json_encode(array('TeamLoc'=>$teamLoc,'unitdata'=>$data));
		    	echo $data_ar;
		        die();
    }
    
    /**
     * Get start_date and end_date by dropdown date value
     * @return
     */
    public function getalldatebydropdown($datedropdown){
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
     * Data process client case data of Processing Report
     * @return
     */
    public function actionDataprocessclientcasedata(){
    	$this->layout = 'report';
    	$filter_data = Yii::$app->request->post('filtervalue');
    	if(Yii::$app->request->post()){
    		$post_data = array();
	    	if(isset($filter_data) && !empty($filter_data)){
	    		$save = '';
	    	}else{
	    		$save = 'save';
	    		$post_data['start_date'] = Yii::$app->request->post('start_date');
	    		$post_data['end_date'] = Yii::$app->request->post('end_date');
	    		$post_data['datedropdown'] = Yii::$app->request->post('datedropdown');
	    		$post_data['chkclientcases'] = Yii::$app->request->post('chkclientcases');
	    		$post_data['client'] = Yii::$app->request->post('client');
	    		$post_data['clientcases'] = Yii::$app->request->post('clientcases');
	    		$post_data['service_location'] = Yii::$app->request->post('service_location');
	    		$post_data['team_service'] = Yii::$app->request->post('team_service');
	    		$post_data['teamlocs'] = Yii::$app->request->post('teamlocs');
	    		$post_data['statistics'] = Yii::$app->request->post('statistics');
	    	}
	    }
    	
	    $select_client_case = Yii::$app->request->post('chkclientcases');
	    $start_date = Yii::$app->request->post('start_date');
    	$end_date = Yii::$app->request->post('end_date');
    	$datedropdown = Yii::$app->request->post('datedropdown');
    	
    	$TeamLocation = ArrayHelper::map(TeamLocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name ASC')->where(['remove'=>0])->asArray()->all(), 'id','team_location_name');
    	$servicetasks1 = ArrayHelper::map(Servicetask::find()->select(['id','service_task'])->all(), 'id','service_task');
    	
    	if(isset($datedropdown) && $datedropdown!=0){
    		$date = $this->getalldatebydropdown($datedropdown);
    		if(!empty($date))
    			$start_date = $date['start_date']; $end_date = $date['end_date'];
    	}
    	
    	$client_case = Yii::$app->request->post('clientcases');
    	$client = Yii::$app->request->post('client');
    	$team_loc = Yii::$app->request->post('teamlocs');
    	$teamId = Yii::$app->request->post('team_service');
    	$statistics = explode("||",Yii::$app->request->post('statistics'));	
    	
    	$unitdataIds='';
    	$dataout='';
    	$clientcase = "";
    	$teamlocstr = "";
    	if(!empty($statistics)){
    		if ($statistics[0] == "Data") {
            	$unitdataIds = $statistics[1];
            	$dataout = "<strong>Data</strong> : " . $statistics[2];
            	$element_name = $statistics[2];                        
            } else {
            	$billingdataIds = $statistics[1];
            	$dataout = "<strong>Bill</strong> : " .  $statistics[2];
           		$element_name =  $statistics[2];
            }
    	}
    	
    	$cids='';
    	$ccids='';
    	$teamloc='';
    	if (isset($client) && count($client) > 0) {
    		$cids = implode(",", $client);
    	}
    	if (isset($client_case) && count($client_case) > 0) {
    		$ccids = implode(",", $client_case);
    	}
    	if (isset($team_loc) && count($team_loc) > 0) {
    		$teamloc = implode(",", $team_loc);
    	}
    	
    	$varteam='';
    	if(isset($teamId) && $teamId!=''){
    		$teamCond = "";
    		$varteam = '%' . $teamId . '%';
    		if ($teamCond == "") {
    			$teamCond = " t.teamservice_id LIKE '" . $varteam . "'";
    		} else {
    			$teamCond = $teamCond . " OR t.teamservice_id LIKE '" . $varteam . "'";
    		}
    	}
    	
    	$unitsdata = Unit::find()->All();
    	$unit = array();
    	$unitIdName= array();
    	foreach($unitsdata as $unitdata){
    		$unit[$unitdata->unit_name] = $unitdata->est_size;
    		$unitIdName[$unitdata->id] = $unitdata->unit_name;
    	}
    	
    	$unitsAr = array();
    	$exceldata = array();
    	$clientArr = array();
    			
    	$datesql = $this->getdatesqlformat($start_date,$end_date);
    	if($teamloc != "")
    		$teamlocstr = " AND ts.team_loc IN (".$teamloc.")";
    	 
    	if($cids != "")
    		$clientcasesql = " AND ta.client_id IN (".$cids.")";
    	else
    		$clientcasesql = " AND ta.client_case_id IN (".$ccids.")";
    	
    	$export_data=array();
    	if ($unitdataIds != "") {
    		$teamservice = " AND ts.teamservice_id = $teamId";
    		$unitdataArr1 = (new TasksUnitsData)->getUnitData($datesql,$teamservice,$teamlocstr,$teamlocstr,$clientcase,$unitdataIds);
			$unitdataArr = \Yii::$app->db->createCommand($unitdataArr1)->queryAll();
			$unitcount = 0;
    		if(!empty($unitdataArr)){
    				foreach ($unitdataArr as $tasukey1 => $taskunitval){
	    				$export_data[$v]['id']=$taskunitval['id'];
	    				$export_data[$v]['client']=$taskunitval['client_name'];
	    				$export_data[$v]['case']=$taskunitval['case_name'];
	    				$export_data[$v]['task_id']=$taskunitval['task_id'];
	    				$export_data[$v]['submmitted_date']=$taskunitval['created'];
	    				$export_data[$v]['location']=$TeamLocation[$taskunitval['team_loc']];
	    				$export_data[$v]['task']=$servicetasks1[$taskunitval['servicetask_id']];
	    				$export_data[$v]['service_name'] = $taskunitval['service_name'];
	    				if($taskunitval['element_unit'] != "") {
	    					$units = "";
	    					$unit_name = $unitIdName[$taskunitval['element_unit']];
	    					$est_size = $unit[$unit_name];
	    					if($est_size > 0 && $unit_name != 'GB'){
	    						$unitsAr['GB'] = 'GB';
	    						$units = 'GB';
	    						$kb = $est_size;
	    						$total_kbs = $taskunitval['element_details']; //get qty value in kb
	    						if($unit_name != 'KB')
	    							$total_kbs = $kb * $taskunitval['element_details']; //get qty value in kb
	    		
	    						$total_bytes = $total_kbs * 1024; //get total values in bytes to convert it to max unit
	    						$unitcount = number_format($total_bytes / 1073741824, 3,'.','');
	    					} else {
	    						$unitsAr[$unit_name]=$unit_name;
	    						$units = $unit_name;
	    						$unitcount = $taskunitval['element_details'];
	    					}
	    		
	    					if($cids != ""){
	    						if(isset($clientDataArr[$taskunitval['client_name']][$taskunitval['service_name']][$units]))
	    							$clientDataArr[$taskunitval['client_name']][$taskunitval['service_name']][$units] += (float) $unitcount;
	    						else
	    							$clientDataArr[$taskunitval['client_name']][$taskunitval['service_name']][$units] = (float) $unitcount;
	    					} else {
	    						if(isset($clientDataArr[$taskunitval['case_name']][$taskunitval['service_name']][$units]))
	    							$clientDataArr[$taskunitval['case_name']][$taskunitval['service_name']][$units] += (float) $unitcount;
	    						else
	    							$clientDataArr[$taskunitval['case_name']][$taskunitval['service_name']][$units] = (float) $unitcount;
	    					}
	    					
	    					$export_data[$v]['data_out_stat_unit'] = $units;
	    					if($cids != "")
	    						$export_data[$v]['data_out_stat'] = $unitcount;
	    					else
	    						$export_data[$v]['data_out_stat'] = $unitcount;
	    				}
	    				$totalsum = $unitcount; // $totalsumArr['totalsum'];
	    				$clientTeamArr[$taskunitval['service_name']] = $taskunitval['service_name'];
	    				$servicetasks[$taskunitval['servicetask_id']] = $taskunitval['servicetask_id'];
	    				if($cids != "")
	    					$clientArr[$taskunitval['client_name']] = $taskunitval['client_name'];
	    				else
	    					$clientArr[$taskunitval['case_name']] = $taskunitval['case_name'];
	    				$v++;
	    			}
	    		}
    		}else{
	    		$task_unitbillingdata = TasksUnitsBilling::find()->select(['t.task_id as task_id','t.created as created','tp.price_point', 'ts.team_loc','ts.servicetask_id','cl.client_name','ca.case_name','t.id as billing_id','t.quantity as qty','t1.unit_name','teamserviceId.service_name'])
	    			->from('tbl_tasks_units_billing as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
	    			->join('INNER JOIN','tbl_client as cl','cl.id=ta.client_id')
	    			->join('INNER JOIN','tbl_client_case as ca','ca.id = ta.client_case_id AND is_close=0')
	    			->join('INNER JOIN','tbl_pricing as tp','tp.id=t.pricing_id')
	    			//->join('INNER JOIN','tbl_unit_price as t1','t1.id=tp.unit_price_id')
	    			->join('INNER JOIN','tbl_unit as t1','t1.id=tp.unit_price_id')
	    			->join('INNER JOIN','tbl_tasks_units as tu','t.tasks_unit_id=tu.id')
	    			->join('INNER JOIN','tbl_task_instruct_servicetask as ts','tu.task_instruct_servicetask_id=ts.id')
	    			->join('INNER JOIN','tbl_teamservice as teamserviceId','ts.teamservice_id=teamserviceId.id')
	    			->where("ts.teamservice_id={$teamId} $clientcasesql $teamlocstr AND t.pricing_id = $billingdataIds AND tp.remove=0 AND $datesql")
	    			->asArray()->all();
	    		if (!empty($task_unitbillingdata)) {
	            	foreach ($task_unitbillingdata as $tasukey1 => $tasksdata) {
	            		$export_data[$t]['client']=$tasksdata['client_name'];
	            		$export_data[$t]['case']=$tasksdata['case_name'];
	            		$export_data[$t]['task_id']=$tasksdata['task_id'];
	            		$export_data[$t]['submmitted_date']=$tasksdata['created'];
	            		$export_data[$t]['service_name']=$tasksdata['service_name'];
	            		$export_data[$t]['location']=$TeamLocation[$tasksdata['team_loc']];
	            		$export_data[$t]['task']=$servicetasks1[$tasksdata['servicetask_id']];
	            		$teamservicear['Bill'][$tasksdata['price_point']][]=$tasksdata['billing_id'];
		            	$unitsAr[$tasksdata['unit_name']]=$tasksdata['unit_name'];
		            	if($cids != "")
		                	$clientDataArr[$tasksdata['client_name']][$tasksdata['service_name']][$tasksdata['unit_name']] += (float) $tasksdata['qty'];
		                else 
		                	$clientDataArr[$tasksdata['case_name']][$tasksdata['service_name']][$tasksdata['unit_name']] += (float) $tasksdata['qty'];
		                        
		                if($cids != "")
							$clientArr[$tasksdata['client_name']] = $tasksdata['client_name'];
						else 
							$clientArr[$tasksdata['case_name']] = $tasksdata['case_name'];
						
						$export_data[$t]['data_out_stat_unit'] = $tasksdata['unit_name'];
						$export_data[$t]['data_out_stat'] = $tasksdata['qty'];//$clientDataArr[$export_data[$t]['client']][$export_data[$t]['service_name']][$export_data[$t]['data_out_stat_unit']];
						$t++;
					}
				}
	    	}
	
	    	$clientTeamArr = array();
			if (!empty($clientDataArr)) {
				foreach ($clientDataArr as $key => $clientdata) {
					foreach ($clientTeamArr as $teamkey => $clientteam) {
						if (!isset($clientDataArr[$key][$teamkey])) {
							$clientDataArr[$key][$teamkey]=0;
						}
					}
				}
			}
			
			$finalArr = array(); $arr = array();
			$category_key = array();
			foreach ($clientArr as $data) {
				$category_key['categories'][] = $data;
			}
			$teamdatas = $this->getTeamService($teamId);
			$teamservicevals = $teamdatas->service_name;
			if(!empty($unitsAr)) {
				foreach ($unitsAr as $unit) {
					$clientteamunitdata = array();
					foreach($clientArr as $client) {
						if(isset($clientDataArr[$client])) {
							$clientteamunitdata[] = $clientDataArr[$client][$teamservicevals][$unit];
						} else {
							$clientteamunitdata[] = 0;
						}
					}
					$teamunit = $teamservicevals."-".$unit;
					$arr['series'][] = array('name'=>$teamunit,'data'=>$clientteamunitdata);
				}
			}
			
			$clientchartCategories = json_encode($category_key);
			$clientchartSeries = json_encode($arr);
			$exportclientdata = $clientDataArr;
		
			$teamdata = $this->getTeamService($teamId);
			$teamserviceval = $teamdata->service_name;
			$export_data['unitsAr'] = $unitsAr;
			
			return $this->render('rundataprocessclientcase', [
				"clientchart" => $clientchart,
				"clientchartCategories" => $clientchartCategories,
				"clientchartSeries" => $clientchartSeries,
				"start_date" => $start_date,
				"end_date" => $end_date,
				"element_name" => $element_name,
				'unitsAr' => $unitsAr,
				'isshowdatasource' => $isshowdatasource,
				'post_data' => $post_data,
				'select_client_case' => $select_client_case,	
				'export_data'=>$export_data,
				'save' => $save,
			], false, true);
				//
		}
    
    /**
     * Get team service id by teamid
     * @return
     */
    public function getTeamService($teamId){
    	$teamdatas = Teamservice::findOne($teamId);
    	if(!empty($teamdatas))
    			return $teamdatas;
    }
    
    /*
     * Save the Chart.
     * */
    public function actionSavedataprocessfilter(){
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
    
    /**
     * Export Data processed by client/case
     * @return
     */
    public function actionExportdataclientcase(){
    	$filter_data =  Yii::$app->request->post('filtervalue');
    	if(Yii::$app->request->post()){
    		if(isset($filter_data) && !empty($filter_data)){
    			$export_data = json_decode(Yii::$app->request->post('dataexport'));
				$filter_data = json_decode($filter_data);
    			$start_date = $filter_data->start_date;
    			$end_date = $filter_data->end_date;
    			$datedropdown = $filter_data->datedropdown;
    			if(isset($filter_data->client) && $filter_data->client!='')
    				$type = 'client';	
    			if(isset($filter_data->clientcases) && $filter_data->clientcases!='')
    				$type = 'clientcases';
    		}
    	}
    	
    	$teamdatas = $this->getTeamService($filter_data->team_service);
		$teamservicevals = $teamdatas->service_name;
		$filename = "DataProcessByClientCase_" . date('m_d_Y', time()) . ".csv";

		$objPHPExcel = new PHPExcel();
		$activesheet = 0;
		$rowCount = 7;
		$showclientcase = "Clients";
		if(!empty($casechart)){
			$showclientcase = "Clients/Cases";
		}
		$unitsAr = (array)$export_data->unitsAr;
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Data Process By Selected ".$showclientcase." (".implode(',',$unitsAr).")");
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Start Date");
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', "End Date");
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', date('m/d/Y',strtotime($start_date)));
		$objPHPExcel->getActiveSheet()->SetCellValue('C2', date('m/d/Y',strtotime($end_date)));
		$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Service:');
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', $teamservicevals);
		$objPHPExcel->getActiveSheet()->mergeCells('B3:C3');
		$objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Statistics(Data Out):');
		$objPHPExcel->getActiveSheet()->SetCellValue('B4', $dataout);
		$objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
		$objPHPExcel->getActiveSheet()->SetCellValue('A6', 'Client');
		$objPHPExcel->getActiveSheet()->SetCellValue('B6', 'Case');
		$objPHPExcel->getActiveSheet()->SetCellValue('C6', 'Project #');
		$objPHPExcel->getActiveSheet()->SetCellValue('D6', 'Project Submitted Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('E6', 'Service');
		$objPHPExcel->getActiveSheet()->SetCellValue('F6', 'Location');
		$objPHPExcel->getActiveSheet()->SetCellValue('G6', 'Task');
		$objPHPExcel->getActiveSheet()->SetCellValue('H6', 'Data Out Stat');
		$objPHPExcel->getActiveSheet()->SetCellValue('I6', 'Data Out Stat Unit');
		if(!empty($export_data)){
			foreach ((array)$export_data as $value) {
				$submitted = "";
				if($value->submmitted_date!="")
					$submitted = (new Options)->ConvertOneTzToAnotherTz($value->submmitted_date, 'UTC', $_SESSION['usrTZ'], "requestdate");
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,$value->client);
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$value->case);
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$value->task_id);
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$value->submmitted_date);
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,$value->service_name);
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,$value->location);
					$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,$value->task);
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount,$value->data_out_stat);
					$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$value->data_out_stat_unit);
					$rowCount++;
			}
		}
		
		header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit(); 
	}
	
	/**
	 * Calculation of Data Processed service report 
	 * @return
	 */
	public function actionDataprocessServicedata(){
		$this->layout = 'report';
		$start_date = Yii::$app->request->post('start_date');
		$end_date = Yii::$app->request->post('end_date');
		$datedropdown = Yii::$app->request->post('datedropdown');
		//$team_service = Yii::$app->request->post('team_service');
		$teamlocs = Yii::$app->request->post('teamlocs');
		$teamId = Yii::$app->request->post('team_service');
		$statistics = Yii::$app->request->post('statistics');
		$filter_data = Yii::$app->request->post('filtervalue');
		if(Yii::$app->request->post()){
			$post_data=''; 
			$save='';
			if(!isset($filter_data)){
				$post_data['start_date'] = Yii::$app->request->post('start_date');
				$post_data['end_date'] = Yii::$app->request->post('end_date');
				$post_data['datedropdown'] = Yii::$app->request->post('datedropdown');
				$post_data['team_service'] = Yii::$app->request->post('team_service');
				$post_data['teamlocs'] = Yii::$app->request->post('teamlocs');
				$post_data['team_service'] = Yii::$app->request->post('team_service');
				$post_data['statistics'] = Yii::$app->request->post('statistics');
				$post_data['service_location'] = Yii::$app->request->post('service_location');
				$post_data['chartgroupcriteria'] = Yii::$app->request->post('chartgroupcriteria');
				$save='save';
			}
		}
			
		$date1=date_create($start_date);
		$date2=date_create($end_date);
		$diff=date_diff($date1,$date2);
		$isshowdatasource = $diff->format("%a") > 180 ? "no" : "yes";
		
		$UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
		$UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);
		
		if(isset($datedropdown) && $datedropdown!=0){
			$date = $this->getalldatebydropdown($datedropdown);
			if(!empty($date))
				$start_date = $date['start_date']; $end_date = $date['end_date'];
		}
		
		$dates = array();
		$current = strtotime($start_date);
		$last = strtotime($end_date);
		$timezoneOffset = (new Options)->getOffsetOfTimeZone();
		
		while ($current <= $last) {
			$dates[] = date('Y-m-d', $current);
			if (isset($_REQUEST['chartgroupcriteria']) && $_REQUEST['chartgroupcriteria'] == "week" || ($_REQUEST['chartgroupcriteria'] == '0' && $_REQUEST['datedropdown'] == 3)) {
				$current = strtotime('+7 day', $current);
			} else if (isset($_REQUEST['chartgroupcriteria']) && $_REQUEST['chartgroupcriteria'] == "month" || ($_REQUEST['chartgroupcriteria'] == '0' && $_REQUEST['datedropdown'] == 4)) {
				$current = strtotime('+1 month', $current);
			} else if (isset($_REQUEST['chartgroupcriteria']) && $_REQUEST['chartgroupcriteria'] == "years" || ($_REQUEST['chartgroupcriteria'] == '0' && $_REQUEST['datedropdown'] == 5)) {
				$current = strtotime('+1 year', $current);
			} else {
				$current = strtotime('+1 month', $current);
			}
		}	
		
		$TeamLocation = ArrayHelper::map(TeamLocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name ASC')->where(['remove'=>0])->asArray()->all(), 'id','team_location_name');
		$servicetasks1 = ArrayHelper::map(Servicetask::find()->select(['id','service_task'])->all(), 'id','service_task');
		
		$i=0; $v=0;$t=0;
 		$teamserviceList = Teamservice::findOne($teamId);
 		$teamdata = Team::findOne($teamserviceList->teamid);
		
 		$unitsdata = Unit::find()->all();
		$unit = array();
		$unitIdName= array();
		foreach($unitsdata as $unitdata){
			$unit[$unitdata->unit_name] = $unitdata->est_size;
			$unitIdName[$unitdata->id] = $unitdata->unit_name;
		}
		
		$export_data = array();
		foreach ($dates as $d) {
			if (isset($_REQUEST['chartgroupcriteria']) && $_REQUEST['chartgroupcriteria'] == "week" || ($_REQUEST['chartgroupcriteria'] == '0' && $_REQUEST['datedropdown'] == 3)) {
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
			} else if (isset($_REQUEST['chartgroupcriteria']) && $_REQUEST['chartgroupcriteria'] == "years" || ($_REQUEST['chartgroupcriteria'] == '0' && $_REQUEST['datedropdown'] == 5)) {
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
				$ymd = date("Y",strtotime($first_day_this_months));
			} else if (isset($_REQUEST['chartgroupcriteria']) && $_REQUEST['chartgroupcriteria'] == "month" || ($_REQUEST['chartgroupcriteria'] == '0' && $_REQUEST['datedropdown'] == 4)) {
				$first_day_this_months = date('Y-m-01', strtotime($d));
				if (strtotime(date('Y-m-01', strtotime($d))) < strtotime($start_date)) {
					$first_day_this_months = date('Y-m-d', strtotime($d));
				}
				$last_day_this_month = date('Y-m-t', strtotime($d));
				if (strtotime(date('Y-m-t', strtotime($d))) > strtotime($end_date)) {
					$last_day_this_month = date('Y-m-d', strtotime($end_date));
				}
				$ymd = date("M-y",strtotime($first_day_this_months));
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
			
			$unitdataIds='';
			if ($statistics) {
				$dataIds = explode("||", $statistics);
				if ($dataIds[0] == "Data") {
					$unitdataIds = $dataIds[1];
					$dataout = "<strong>Data</strong> : " . $dataIds[2];
					$element_name = $dataIds[2];
				} else {
					$billingdataIds = $dataIds[1];
					$dataout = "<strong>Bill</strong> : " .  $dataIds[2];
					$element_name =  $dataIds[2];
				}
			}
			
			if (isset($teamlocs) && count($teamlocs) > 0) {
				$teamloc = implode(",", $teamlocs);
			}
			
			$varteam='';
	    	if(isset($teamId) && $teamId!=''){
	    		$teamCond = "";
	    		$varteam = '%' . $teamId . '%';
	    		if ($teamCond == "") {
	    			$teamCond = " t.teamservice_id LIKE '" . $varteam . "'";
	    		} else {
	    			$teamCond = $teamCond . " OR t.teamservice_id LIKE '" . $varteam . "'";
	    		}
	    	}
			$datesql = $this->getdatesqlformat($first_day_this_months,$last_day_this_month);
			if ($unitdataIds != "") {
				$teamservice = " AND ts.teamservice_id = $teamId";
				$unitdataArr1 = (new TasksUnitsData)->getUnitData($datesql,$teamservice,$teamlocstr,$teamlocstr,$clientcase,$unitdataIds);
				$unitdataArr = \Yii::$app->db->createCommand($unitdataArr1)->queryAll();
				$unitcount = 0;
				if(!empty($unitdataArr)){
					foreach ($unitdataArr as $tasukey1 => $taskunitval){
						$export_data[$v]['task_id']=$taskunitval['task_id'];
						$export_data[$v]['submmitted_date']=$taskunitval['created'];
						$export_data[$v]['location']=$TeamLocation[$taskunitval['service_task_loc_id']];
						$export_data[$v]['task']= $servicetasks1[$taskunitval['service_task_id']];
						$export_data[$v]['service_name'] = $taskunitval['service_name'];
						if($taskunitval['element_unit'] != "") {
							$units = "";
							$unit_name = $unitIdName[$taskunitval['element_unit']];
							$est_size = $unit[$unit_name];
							if($est_size > 0 && $unit_name != 'GB'){
								$unitsAr['GB'] = 'GB';
								$units = 'GB';
								$kb = $est_size;
								$total_kbs = $taskunitval['element_details']; //get qty value in kb
								if($unit_name != 'KB')
									$total_kbs = $kb * $taskunitval['element_details']; //get qty value in kb
			
								$total_bytes = $total_kbs * 1024; //get total values in bytes to convert it to max unit
								$unitcount = number_format($total_bytes / 1073741824, 3,'.','');
							} else {
								$unitsAr[$unit_name]=$unit_name;
								$units = $unit_name;
								$unitcount = $taskunitval['element_details'];
							}
			
							if(isset($clientDataArr[$taskunitval['service_name']][$ymd][$units]))
                            	$clientDataArr[$taskunitval['service_name']][$ymd][$units] += (float)$unitcount;
                            else 
                            	$clientDataArr[$taskunitval['service_name']][$ymd][$units] = (float)$unitcount;
                            
                            $export_data[$v]['data_out_stat_unit'] = $units;
                            $export_data[$v]['data_out_stat'] = $unitcount;//$clientDataArr[$export_data[$tasukey1]['service_name']][$first_day_this_months][$export_data[$tasukey1]['data_out_stat_unit']];
                        }
						$v++;
					}
				}
				$datesArr[$ymd] = $ymd;
			}else{
				$task_unitbillingdata = TasksUnitsBilling::find()->select(['teamserviceId.service_name','ts.servicetask_id','tp.price_point','t.created as created','ta.id as task_id','cl.client_name','ca.case_name','t.id as billing_id','t.quantity as qty','t1.unit_name','teamserviceId.service_name','ts.team_loc'])
				->from('tbl_tasks_units_billing as t')->join('INNER JOIN','tbl_tasks as ta','t.task_id=ta.id')
				->join('INNER JOIN','tbl_client as cl','cl.id=ta.client_id')
				->join('INNER JOIN','tbl_client_case as ca','ca.id = ta.client_case_id AND is_close=0')
				->join('INNER JOIN','tbl_pricing as tp','tp.id=t.pricing_id')
				//->join('INNER JOIN','tbl_unit_price as t1','t1.id=tp.unit_price_id')
				->join('INNER JOIN','tbl_unit as t1','t1.id=tp.unit_price_id')
				->join('INNER JOIN','tbl_tasks_units as tu','t.tasks_unit_id=tu.id')
				->join('INNER JOIN','tbl_task_instruct_servicetask as ts','tu.task_instruct_servicetask_id=ts.id')
				->join('INNER JOIN','tbl_teamservice as teamserviceId','ts.teamservice_id=teamserviceId.id')
				->where("ts.teamservice_id={$teamId} $clientcasesql $teamlocstr AND t.pricing_id = $billingdataIds AND tp.remove=0 AND $datesql")
				->asArray()->all();
				if (!empty($task_unitbillingdata)) {
					foreach ($task_unitbillingdata as $tasukey1 => $tasksdata) {
						$export_data[$t]['task_id']=$tasksdata['task_id'];
						$export_data[$t]['submmitted_date']=$tasksdata['created'];
						$export_data[$t]['service_name'] = $tasksdata['service_name'];
						$export_data[$t]['location']=$TeamLocation[$tasksdata['team_loc']];
						$export_data[$t]['task']=$servicetasks1[$tasksdata['servicetask_id']];
						$export_data[$t]['data_out_stat_unit'] = $tasksdata['unit_name'];
						$export_data[$t]['data_out_stat'] = $tasksdata['qty'];
						
						$teamservicear['Bill'][$tasksdata['price_point']][] = $tasksdata['billing_id'];
						$unitsAr[$tasksdata['unit_name']] = $tasksdata['unit_name'];
						
						if(isset($clientDataArr[$tasksdata['service_name']][$ymd][$tasksdata['unit_name']]))
                        	$clientDataArr[$tasksdata['service_name']][$ymd][$tasksdata['unit_name']] += (float)$tasksdata['qty'];
                        else 
                        	$clientDataArr[$tasksdata['service_name']][$ymd][$tasksdata['unit_name']] = (float)$tasksdata['qty'];
						
                        $t++;
					}
				}
			
				$datesArr[$ymd] = $ymd;
				$clientTeamArr[$teamserviceList->service_name] = $teamserviceList->service_name;
			}
			$i++;
		}
		
		$finalcArr = array();
		$teamservicevals = $teamserviceList->service_name;
		$finalArr = array();
		$arr = array();
		$arrkeys = array();
		$arrkeys['categories'] = array_values($datesArr);
	
		if(!empty($unitsAr)){
			foreach ($unitsAr as $unit){
				$clientteamunitdata = array();
				foreach($datesArr as $date){
					if(isset($clientDataArr[$teamservicevals][$date][$unit])){
						$clientteamunitdata[] = $clientDataArr[$teamservicevals][$date][$unit];
					}else{
						$clientteamunitdata[] = 0;
					}
				}
				$teamunit = $teamservicevals."-".$unit;
				$arr['series'][] = array('name'=>$teamunit,'data'=>$clientteamunitdata);
			}
		}
		
		$clientchartCategories = json_encode($arrkeys);
		$clientchartSeries = json_encode($arr);
		$clientchart = json_encode($finaldarrval);
		$exportclientdata = $clientDataArr;
		$export_data['unitsAr'] = $unitsAr;
		
		return $this->render('rundataprocessservice', array(
				'clientchartCategories' => $clientchartCategories,
				'clientchartSeries' => $clientchartSeries,
				'clientchart' => $clientchart,
				"start_date" => $start_date,
				"end_date" => $end_date,
				'teamserviceval' => $teamservicevals,
				"element_name"=>$element_name,
				'dataout' => $dataout,
				'unitsAr' => $unitsAr,
				'export_data' => $export_data,
				'save'=>$save,
				'post_data' =>$post_data,
		), false, true);
	}
	
	/**
	 * Export xls file of Data processed service report
	 * @return
	 */
	public function actionExportDataservice(){
		$filter_data = Yii::$app->request->post('filtervalue');
		if(Yii::$app->request->post()){
			if(isset($filter_data)){
				$export_data = json_decode(Yii::$app->request->post('dataexport'));
				$filter_data = json_decode($filter_data);
				$start_date = $filter_data->start_date;
				$end_date = $filter_data->end_date;
				$datedropdown = $filter_data->datedropdown;
			}
		}

		if(isset($datedropdown) && $datedropdown!=0){
			$date = $this->getalldatebydropdown($datedropdown);
			if(!empty($date))
				$start_date = $date['start_date']; $end_date = $date['end_date'];
		}
		
		$filename = "DataProcessedByService_" . date('m_d_Y', time()) . ".csv";
		$objPHPExcel = new PHPExcel();
		$activesheet = 0;
		$rowCount = 7;
		
		$unitsAr = (array)$export_data->unitsAr;
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Data Process By Selected Services (".implode(",",$unitsAr).")");
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Start Date");
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', "End Date");
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', date('m/d/Y',strtotime($start_date)));
		$objPHPExcel->getActiveSheet()->SetCellValue('C2', date('m/d/Y',strtotime($end_date)));
		$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Service:');
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', $teamservicevals);
		$objPHPExcel->getActiveSheet()->mergeCells('B3:C3');
		$objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Statistics(Data Out):');
		$objPHPExcel->getActiveSheet()->SetCellValue('B4', $dataout);
		$objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
		$objPHPExcel->getActiveSheet()->SetCellValue('A6', 'Project #');
		$objPHPExcel->getActiveSheet()->SetCellValue('B6', 'Project Submitted Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('C6', 'Service');
		$objPHPExcel->getActiveSheet()->SetCellValue('D6', 'Location');
		$objPHPExcel->getActiveSheet()->SetCellValue('E6', 'Task');
		$objPHPExcel->getActiveSheet()->SetCellValue('F6', 'Data Out Stat');
		$objPHPExcel->getActiveSheet()->SetCellValue('G6', 'Data Out Stat Unit');
		
		if(!empty($export_data)){
			foreach ((array)$export_data as $value) {
				$submitted = "";
				if($value->submmitted_date!="")
					$submitted = (new Options)->ConvertOneTzToAnotherTz($value->submmitted_date, 'UTC', $_SESSION['usrTZ'], "requestdate");
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,$value->task_id);
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$submitted);
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$value->service_name);
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$value->location);
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,$value->task);
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,$value->data_out_stat);
					$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,$value->data_out_stat_unit);
					$rowCount++;
			}
		}
		header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit();
	}
}
