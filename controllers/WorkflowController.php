<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

use app\models\User;
use app\models\Team;
use app\models\Tasks;
use app\models\TeamLocs;
use app\models\TeamlocationMaster;
use app\models\Teamservice;
use app\models\search\TeamserviceSearch;
use app\models\search\ServicetaskSearch;
use app\models\TeamserviceLocs;
use app\models\ProjectSecurity;
use app\models\Servicetask;
use app\models\ServicetaskTeamLocs;
use app\models\TasksTemplates;
use app\models\FormBuilder;
use app\models\FormElementOptions;
use app\models\TeamserviceSla;
use app\models\Unit;
use app\models\TaskInstruct;
use app\models\EvidenceType;
use app\models\PriorityProject;
use app\models\ActivityLog;
use app\models\TasksTemplatesServiceTasks;
use app\models\CaseXteam;
use app\models\ClientXteam;
use app\models\ProjectRequestType;
use app\models\TemplatesRequestTypes;
use yii\widgets\ActiveForm;

use yii\helpers\Url;
use yii\db\Query;

class WorkflowController extends Controller
{
	/**
	 * Load Case Team layout for case teamservice and service task
	 * */
	public function actionCaseteam()
    {
    	$roleId =  Yii::$app->user->identity->role_id;
    	$TEAM_ID = Yii::$app->request->get('team_id',0);
    	$team_ids = '';
    	$teamsecurity_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0')->all();
    	if ($roleId != 0) {
    		$teamsecurity_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0 AND user_id='.Yii::$app->user->identity->id)->all();
    	}
    	if (!empty($teamsecurity_data)) {
    		foreach ($teamsecurity_data as $team_data) {
    			if ($team_ids == '')
    				$team_ids = $team_data->team_id;
    			else
    				$team_ids.=',' . $team_data->team_id;
    		}
    	}
    	$teamList = array();
    	if ($team_ids != '' || $TEAM_ID != "") {
    		if ($TEAM_ID == "") {
    			if ($roleId == 0) {
    				$teamList = Team::find()->select(['id', 'team_name'])->where('id NOT IN (1)')->orderBy('sort_order')->all();
    			} else {
    				$teamList = Team::find()->select(['id', 'team_name'])->where('id In (' . $team_ids . ') AND id NOT IN (1)')->orderBy('sort_order')->all();
    			}
    		} else {
    			$teamList = Team::find()->select(['id', 'team_name'])->where('id In (' . $TEAM_ID . ')')->orderBy('sort_order')->all(); 
    		}
    	}
    	$teamLocation = TeamlocationMaster::getTeamLocationList(); 
        return $this->renderAjax('Caseteam',['teamList' => $teamList, 'teamLocation' => $teamLocation, 'TEAM_ID' => $TEAM_ID]);
    }
    /*show Teamservice, Servicetask and User Tabs */
    public function actionUpdateteam() {
    	$teamId = Yii::$app->request->get('team_id',0);
    	$team = Team::findOne($teamId);
    	$teamLocation = TeamlocationMaster::getTeamLocationList();
    	return $this->renderAjax('updateteam', ['model' => $team, 'teamservice' => $teamservice, 'teamId' => $teamId, 'teamLocation' => $teamLocation]);
    }
    
    /**
    * Filter GridView with Ajax
    * */
    public function actionAjaxFilter()
    {
		$searchModel = new TeamserviceSearch();
		$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			if($val == ''){
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		return json_encode($out);
    }
    /** 
     * show listing of Teamservice
    */
    public function actionTeamservice(){
    	$teamId = Yii::$app->request->get('team_id',0);
    	$searchModel = new TeamserviceSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	/*IRT 67,68,86,87,258*/
    	/*IRT 96,398 */
        $filter_type=\app\models\User::getFilterType(['tbl_teamservice.service_name','tbl_teamservice.service_description','tbl_teamservice.hastasks'],['tbl_teamservice']);
        $config = ['hastasks'=>['All'=>'All','1'=>'Yes','0'=>'No']];
		$config_widget_options = [];		
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['workflow/ajax-filter']).'&teamId='.$teamId, $config, $config_widget_options);
        /*IRT 67,68,86,87,258*/
        
        return $this->renderAjax('Teamservice', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'teamId' =>$teamId,
			'filter_display' => $filter_display	,
        	'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption
    	]);
    }
    /**
     * Add Team Service in selected team.
     * */
    public function actionAddteamservice(){
    	$teamId = Yii::$app->request->get('team_id',0);
    	$model = new Teamservice();
    	$model->teamid =$teamId; 
    	$teamLocation = Teamlocs::getTeamLocationList($teamId);
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {                
			$teamservice_id = Yii::$app->db->getLastInsertId();
    		$model->logAndLocation($teamservice_id,$model->service_name);
            $model->processSlaLogic(Yii::$app->request->post(),$model);
    		return 'OK'; // $this->redirect(['view', 'id' => $model->id]);
    	} else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
			return $this->renderAjax('AddTeamservice', [
				'model' => $model,
				'teamId'=>$teamId,
				'teamLocation'=>$teamLocation,
				'model_field_length' => $model_field_length
    		]);
    	}
    }
    /**
     * Updates an existing Teamservice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateteamservice(){
    	$id = Yii::$app->request->get('id',0);
    	$teamId = Yii::$app->request->get('team_id',0);
    	$model = $this->findModelTeamservice($id);
    	$teamLocation = Teamlocs::getTeamLocationList($teamId);
    	if ($model->load(Yii::$app->request->post()) && $model->save()){    		
    		$model->logAndLocation($id,$model->service_name,'update');
    		$model->processSlaLogic(Yii::$app->request->post(),$model);
    		return 'OK';//$this->redirect(['view', 'id' => $model->id]);
    	} else {
    		$modelteamsla = TeamserviceSla::find()->where(["teamservice_id" => $id])->orderBy('team_loc_id,start_logic,start_qty')->all();
    		$listUnit = ArrayHelper::map(Unit::find()->select(['id', 'unit_name'])->orderBy(['unit_name' => 'ASC'])->where("remove=0")->all(), 'id', 'unit_name');
    		$projectPriority = ArrayHelper::map(PriorityProject::find()->select(['id', 'priority'])->where("remove=0")->all(), 'id', 'priority');
    		$model->team_location=ArrayHelper::map(TeamserviceLocs::find()->select('team_loc')->where('teamservice_id='.$id)->all(), 'team_loc','team_loc');
    		$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		return $this->renderAjax('UpdateTeamservice', [
				'model' => $model,
				'teamLocation'=>$teamLocation,
				'listUnit'=>$listUnit,
				'projectPriority'=>$projectPriority,
				'modelteamsla' => $modelteamsla,
				'model_field_length' => $model_field_length
    		]);
    	}
    }
    /*
     * Check if the location is associated with the team services
     * @params integer $id
     * @return mixed
     */
    public function actionCheckLocationTeamExist($id){        
        $location_id = Yii::$app->request->get('location_id',0);         
        $sql = 'SELECT team_loc FROM tbl_teamservice INNER JOIN tbl_teamservice_locs on tbl_teamservice.id = tbl_teamservice_locs.teamservice_id WHERE teamid = '.$id.' AND tbl_teamservice_locs.team_loc = '.$location_id.' GROUP BY tbl_teamservice_locs.team_loc';
        $counter = Teamservice::findBySql($sql)->count();
        if($counter > 0){
            return 'exist';
        }else{
            return 'valid';
        }
        die;        
    }
    /*
     * Check if the location is associated with the team services task
     * @params integer $id
     * @return mixed
     */
    public function actionCheckLocationServiceTaskExist($id){        
        $location_id = Yii::$app->request->get('location_id',0);                 
        $sql = 'SELECT team_loc FROM tbl_servicetask INNER JOIN tbl_servicetask_team_locs AS stl ON stl.servicetask_id = tbl_servicetask.id WHERE tbl_servicetask.teamservice_id = '.$id.' AND stl.team_loc = '.$location_id.' GROUP BY stl.team_loc';        
        $counter = Servicetask::findBySql($sql)->count();
        if($counter > 0){
            return 'exist';
        }else{
            return 'valid';
        }
        die;        
    }
    /**
     * Load Add form for SLA logic.
     * @param integer $id
     * @return mixed
     */
    public function actionAddLogicTaskform()
    {
    	$teamservice_teamid = Yii::$app->request->post('teamservice_teamid',0);
    	$teamservice_id = Yii::$app->request->post('teamservice_id',0);
    	$team_loc =  Yii::$app->request->post('teamservice_loc',0);
        if($teamservice_id == 0){
            $modelteamservice = new Teamservice();
        }else {
            $modelteamservice = Teamservice::findOne($teamservice_id);
        }        
    	$teamLocation = array();
    	if (isset($team_loc)) {
    		$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where("id IN (" . $team_loc . ")")->all(), 'id', 'team_location_name');
    	}
    	$listUnit = ArrayHelper::map(Unit::find()->select(['id', 'unit_name'])->orderBy(['unit_name' => 'ASC'])->where("remove=0")->all(), 'id', 'unit_name');
    	$evidenceType = ArrayHelper::map(EvidenceType:: find()->select(['id', 'evidence_name'])->orderBy(['evidence_name' => 'ASC'])->where("remove=0")->all(), 'id', 'evidence_name');
    	$projectPriority = ArrayHelper::map(PriorityProject::find()->select(['id', 'priority'])->where("remove=0")->all(), 'id', 'priority');
    	$model = new TeamserviceSla();
    	if (Yii::$app->request->post('TeamserviceSla')) {
            $post_data = Yii::$app->request->post('TeamserviceSla');            
            if($post_data['team_loc_id'] == '' || $post_data['start_qty'] == '' || $post_data['size_start_unit_id'] == '' || $post_data['end_qty'] == '' || $post_data['size_end_unit_id'] == '' || $post_data['del_qty'] == '' || $post_data['del_time_unit'] == '' ){
				echo 'no';
                die;
            }
            $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->all(), 'id', 'team_location_name');
            return $this->renderPartial('saveLogicTaskform', [
				'data'=>Yii::$app->request->post(),
				'evidenceType'=>$evidenceType, 
				'projectPriority'=>$projectPriority, 
				'listUnit'=>$listUnit, 
				'teamId' => $modelteamservice->teamid, 
				'teamLocation' => $teamLocation 
            ]);
    	}
    	return $this->renderAjax('addLogicTaskform',[
			'model'=>$model,
			'data'=>Yii::$app->request->post(),
			'modelteamservice' => $modelteamservice,
			'evidenceType'=>$evidenceType,
			'projectPriority'=>$projectPriority,
			'listUnit'=>$listUnit,
			'teamId' => $teamservice_teamid,
			'teamLocation' => $teamLocation,
			'action'=>'Add'
    	]);
    }
    
    /*
     * Validate the Add Logic Form's Data
     */
    public function actionValidateslalogin(){
		$model = new TeamserviceSla();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
				Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
				return ActiveForm::validate($model);
		}
		return "[]";	
	
	}
    
    /**
     * Load Edit form for SLA logic by SLA id.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateSlaLogicForm(){
    	$teamservice_id = Yii::$app->request->post('teamservice_id',0);
    	$team_loc =  Yii::$app->request->post('team_loc_id',0);
    	$modelteamservice = Teamservice::findOne($teamservice_id);
    	$teamservice_teamid = $modelteamservice->teamid;
    	$teamLocation = array();
    	$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where("id IN (" . $team_loc . ")")->all(), 'id', 'team_location_name');
    	$listUnit = ArrayHelper::map(Unit::find()->select(['id', 'unit_name'])->orderBy(['unit_name' => 'ASC'])->where("remove=0")->all(), 'id', 'unit_name');
    	$evidenceType = ArrayHelper::map(EvidenceType:: find()->select(['id', 'evidence_name'])->orderBy(['evidence_name' => 'ASC'])->where("remove=0")->all(), 'id', 'evidence_name');
    	$projectPriority = ArrayHelper::map(PriorityProject::find()->select(['id', 'priority'])->where("remove=0")->all(), 'id', 'priority');
    	$model = new TeamserviceSla();
    	if (Yii::$app->request->post('TeamserviceSla')) {
    		$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->all(), 'id', 'team_location_name');

    		return $this->renderPartial('saveLogicTaskform', [
				'data'=>Yii::$app->request->post(),
				'evidenceType'=>$evidenceType,
				'projectPriority'=>$projectPriority,
				'listUnit'=>$listUnit,
				'teamId' => $modelteamservice->teamid,
				'teamLocation' => $teamLocation,
				'teamservice_id'=>$teamservice_id,
				'action'=>'Edit',
    		]);
    	}
    	return $this->renderAjax('updateSlaLogicForm', [
    		'data'=>Yii::$app->request->post(),
    		'model' => $model,
    		'teamLocation'=>$teamLocation,
    		'modelteamsla' => $modelteamsla,
    		'evidenceType'=>$evidenceType,
    		'projectPriority'=>$projectPriority,
    		'listUnit'=>$listUnit,
    		'modelteamservice'=>$modelteamservice,
    	]);
    }
    
    /**
     * Deletes an existing Teamservice model.
     * If deletion is successful, the browser will be redirected to the 'Teamservice' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteteamservice($id){
    	$serviceModel = new ServiceTask();
    	$service = $serviceModel->CheckIsServiceTaskUsed($id);
    	if($service == 'N'){
			$sla_teamservice = TeamserviceSla::find()->where('tbl_teamservice_sla.teamservice_id = '.$id)->all();
			if(!empty($sla_teamservice)){
				return 'Team Service is used in Teamservice Sla.';
			}
			else{
				$xteam_teamservice = CaseXteam::find()->where('tbl_case_xteam.teamservice_id = '.$id)->all();
				$clientxteam_teamservice = ClientXteam::find()->where('tbl_client_xteam.teamservice_id = '.$id)->all();
				if(!empty($xteam_teamservice)){
					return 'Team Service is used in Exclude Case Service Logic.';
				} else if(!empty($clientxteam_teamservice)){
					return 'Team Service is used in Exclude Client Service Logic.';
				} else{
					$this->DeleteTeamserviceLocation($id);
					$model = $this->findModelTeamservice($id);
					if ($model->delete()) {
						return 'OK';
					}
				}
		    }
    	}else{
    		return 'Team Service have service task in workflow template.';
    	}
    	exit;
    }
    
    /**
     * Delete From Team Location Before Delete Team service
     * If deletion is successful, the return value goes to the Deleteteamservice action
     * 
     */
    public function DeleteTeamserviceLocation($id){
		$res = TeamServiceLocs::DeleteAll('teamservice_id IN ('.$id.')');return $res;
    }
    
    /**
     * Deletes an selected existing Teamservice model.
     * If deletion is successful, the browser will be redirected to the 'listing' page.
     * @param integer $keylist
     * @return mixed
     */
    public function actionDeleteselectedteamservice() {
    	if (isset($_POST['keylist'])) {
    		foreach($_POST['keylist'] as $keylist){
     			$serviceModel = new ServiceTask();
     			/** Check service task available of service team **/
     			$service = $serviceModel->CheckIsServiceTaskUsed($keylist);
     			if($service != 'N'){
     				return 'Fail';
     			}
     			$xteam_teamservice = CaseXteam::find()->where('tbl_case_xteam.teamservice_id = '.$keylist)->all();
     			if(!empty($xteam_teamservice)){
     				return 'Fail';//Team Service is used in Exclude Case Service Logic.';
     			}
    		}
    		$keys = implode(",",$_POST['keylist']);
    		/** Below action Delete Team service location from tbl_service_loc table before delete team service**/
	    	$this->DeleteTeamserviceLocation($keys);
		    Teamservice::deleteAll('id IN ('.$keys.')');
     		return 'OK';
    	}
    	exit;
    }
    
    /**
     * Sorting TeamService
     * @param integer $keylist
     * @return mixed
     */
    public function actionSortteamservice(){
    	$sort_ids = explode(",",Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
    				$model = Teamservice::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->sort_order = $order + 1;
    				$model->save(false);
    			}
    			$transaction->commit();
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
    }
    
    /**
     * Sorting ServiceTask
     * @param integer $keylist
     * @return mixed
     */
    public function actionSortservicetask(){
    	$this->enableCsrfValidation = false;
    	$sort_ids = explode(",",Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
    				$model = Servicetask::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->service_order = $order + 1;
    				$model->save(false);
    			}
    			$transaction->commit();
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
    }
    
    /**
     * Show ServiceTask layout with teamservice in dropdown
     * @param integer $keylist
     * @return mixed
     */
    public function actionServicetask(){
    	$team_id = Yii::$app->request->post('team_id',0);
    	$teamService = ArrayHelper::map(Teamservice::find()->select(['id','service_name'])->orderBy('sort_order')->where('teamid='.$team_id)->asArray()->all(), 'id','service_name');
    	return $this->renderAjax('Servicetask', [
			'teamService' => $teamService,
			'team_id' => $team_id,
    	]);
    }
    
    /**
     * Show ServiceTask Grid based on selected teamservice id
     * @param integer $keylist
     * @return mixed
     */
    public function actionServicetaskajax(){
    	$teamservice_id = Yii::$app->request->get('teamservice_id',0);
    	$task_hide = Yii::$app->request->get('task_hide',0);
    	$searchModel = new ServicetaskSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	/* IRT 67,68,86,87,258 */
    	/* IRT 96,398 */
        $filter_type=\app\models\User::getFilterType(['tbl_servicetask.service_task','tbl_servicetask.billable_item','tbl_servicetask.hasform','tbl_servicetask.data_hasform'],['tbl_servicetask']);
        $config = ['hasform'=>['All'=>'All','1'=>'Yes','0'=>'No'],'data_hasform'=>['All'=>'All','1'=>'Yes','0'=>'No'],'billable_item'=>['All'=>'All','1'=>'Yes','2'=>'No']];
		$config_widget_options = [];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['workflow/ajax-task-filter']).'&teamservice_id='.$teamservice_id.'&task_hide='.$task_hide, $config, $config_widget_options);
        /*IRT 67,68,86,87,258*/
        
        return $this->renderAjax('Servicetask_ajax', [
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    		'teamservice_id' =>$teamservice_id,
    		'filter_display' => $filter_display,
        	'filter_type' => $filter_type,
			'filterWidgetOption' => $filterWidgetOption
    	]);
    }
    
    /**
    * Filter GridView with Ajax
    * */
    public function actionAjaxTaskFilter()
    {
		$searchModel = new ServicetaskSearch();
		$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			if($val == ''){
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		return json_encode($out);
    }
    
    /**
     * Creates a new Servicetask model.
     * If creation is successful, the browser will be redirected to the 'list' page.
     * @return mixed
     */
    public function actionServicetaskcreate(){
    	$team_id = Yii::$app->request->get('team_id',0);
    	$teamservice_id = Yii::$app->request->get('teamservice_id',0);
    	$model = new Servicetask();
    	$model->teamId =$team_id;
    	$model->teamservice_id=$teamservice_id;
    	$teamLocation = TeamserviceLocs::getTeamLocationList($teamservice_id);
    	if ($model->load(Yii::$app->request->post())) {
			//echo "<pre>";print_r(Yii::$app->request->post()); exit;
			$model->force_entry=0;
    		if($model->billable_item==2) {
    			$model->force_entry=1;
    		//	$model->billable_item=1;
    		}
    		if($model->save()) {
    			$id = Yii::$app->db->getLastInsertId();
    			$model->logAndLocation($id,$model->service_task);
    			return 'OK'; // $this->redirect(['vi	ew', 'id' => $model->id]);
    		} else {
				$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    			return $this->renderAjax('Servicetaskcreate', [
    				'model' => $model,
    				'teamId' => $team_id,
    				'teamLocation' => $teamLocation,
    				'model_field_length' => $model_field_length
    			]);
    		}
    	} else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		return $this->renderAjax('Servicetaskcreate', [
				'model' => $model,
				'teamId'=>$team_id,
				'teamLocation'=>$teamLocation,
				'model_field_length' => $model_field_length
    		]);
    	}
    }
    /**
     * Updates an existing Servicetask model.
     * If update is successful, the browser will be redirected to the 'list' page.
     * @param integer $id
     * @return mixed
     */
    public function actionServicetaskupdate($id){
    	$id = Yii::$app->request->get('id',0);
    	$team_id = Yii::$app->request->get('team_id',0);
    	$teamservice_id = Yii::$app->request->get('teamservice_id',0);
    	$model = $this->findModelServiceTask($id);
    	$model->teamId =$team_id ;
    	$model->teamservice_id=$teamservice_id;
    	$teamLocation = TeamserviceLocs::getTeamLocationList($teamservice_id);
    	if ($model->load(Yii::$app->request->post())){
    		if($model->billable_item==2) {
    			$model->force_entry=1;
    		//	$model->billable_item=1;
    		}
    		if($model->save()){
    			$model->logAndLocation($id,$model->service_task,'update');
    			return 'OK'; // $this->redirect(['view', 'id' => $model->id]);
    		}else{
				$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    			$model->team_location=ArrayHelper::map(ServicetaskTeamLocs::find()->select('team_loc')->where('servicetask_id='.$id)->all(), 'team_loc','team_loc');
    			return $this->renderAjax('Servicetaskupdate', [
    					'model'					=> $model,
    					'teamId'				=> $team_id,
    					'teamLocation'			=> $teamLocation,
    					'model_field_length' 	=> $model_field_length
    			]);
    		}
    	} else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		$model->team_location=ArrayHelper::map(ServicetaskTeamLocs::find()->select('team_loc')->where('servicetask_id='.$id)->all(), 'team_loc','team_loc');
    		return $this->renderAjax('Servicetaskupdate', [
    			'model' => $model,
    			'teamId'=>$team_id,
    			'teamLocation'=>$teamLocation,
    			'model_field_length' => $model_field_length
    		]);
    	}
    }
    
    /**
     * Deletes an existing Datatype model.
     * If deletioin is successfull, the browser will be redirect to the 'index' page
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteservicetaskall()
    {
    	if(Yii::$app->request->post('keylist'))
    	{
			$ids = implode(',',Yii::$app->request->post('keylist'));
			$teamservice_id = Yii::$app->request->post('teamservice_id');
			$query = 'SELECT tbl_tasks.id FROM tbl_tasks 
				INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id 
				INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id 
				WHERE tbl_task_instruct.isactive = 1 AND tbl_task_instruct_servicetask.servicetask_id IN ('.$ids.')';
			// GROUP BY tbl_task_instruct_servicetask.servicetask_id   
			
			$servicetask_model = Servicetask::find()->where('teamservice_id = '.$teamservice_id)->count();  
			$total_count = count(Yii::$app->request->post('keylist'));
			if(Tasks::findBySql($query)->count() > 0) {                    
				return 'You cannot remove this Team Location because it is used in a Workflow Template.';
				exit;
			}   
			
			//$query_result = 'SELECT * FROM tbl_pricing_service_task WHERE servicetask_id IN('.$ids.')';
			$query_result = 'SELECT tbl_pricing.id FROM tbl_pricing_service_task INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_service_task.pricing_id WHERE tbl_pricing_service_task.servicetask_id IN('.$ids.') AND tbl_pricing.remove=0';
			if(Tasks::findBySql($query_result)->count() > 0) {                    
				return 'You cannot remove Task(s) because it is used in Pricing.';
				exit;
			}     
			
			foreach(Yii::$app->request->post('keylist') as $id) {
				$res = $this->DeleteServiceTaskLocation($id);
				$model = $this->findModelServiceTask($id);
				if($model->delete()) {
					$model->logAndLocation($id,$model->service_task,'delete');
				}
			}
			if($servicetask_model == $total_count) {
				Teamservice::updateAll(['hastasks' => '0'], ['id'=>$teamservice_id]);	
			}
			
    		return 'OK';
    	}
    }
    
    /**
     * Deletes an existing DataType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteservicetask($id,$teamservice_id){
    	/** Delete Service Task location **/
        $query = 'SELECT tbl_tasks.id FROM tbl_tasks INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id WHERE tbl_task_instruct.isactive = 1 AND tbl_task_instruct_servicetask.servicetask_id= '.$id.''; // GROUP BY tbl_task_instruct_servicetask.servicetask_id  
        $servicetask_model = Servicetask::find()->where('id != '.$id.' AND teamservice_id = '.$teamservice_id)->all();
        if(Tasks::findBySql($query)->count() > 0) {
            return 'You cannot remove a Task if the Task is used in 1 or more Project.';
            exit;
        }    
        // check tbl pricing service task  
        $query_result = 'SELECT tbl_pricing.id FROM tbl_pricing_service_task INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_service_task.pricing_id WHERE tbl_pricing_service_task.servicetask_id IN('.$id.') AND tbl_pricing.remove=0';
        if(Tasks::findBySql($query_result)->count() > 0) {                    
                return 'You cannot remove a Task because it is used in Pricing.';
                exit;
        }     
    	$res = $this->DeleteServiceTaskLocation($id);
    	$model = $this->findModelServiceTask($id);
    	if ($model->delete()) {
    		$model->logAndLocation($id,$model->service_task,'delete');
    		if(empty($servicetask_model)) {
				Teamservice::updateAll(['hastasks' => '0'], ['id'=>$teamservice_id]);	
			}
    		return 'OK';
    	}
    	exit;
    }
    
    /**
     * Delete Service task location By service_task_id
     * if deletion is successful, the return value send to deleteservicetask action
     * @return mixed
     */
    public function DeleteServiceTaskLocation($id) {
    	$res = ServiceTaskTeamLocs::deleteAll('servicetask_id='.$id);
    	return $res;
    }
    
    /**
     * Check Form Existing for selected servicetask
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionChktaskfrom(){
    	$id = Yii::$app->request->get('serviceTask',0);
    	$form_type = Yii::$app->request->get('form',0);
    	
    	// form type 
    	if($form_type == 'instruction'){
    		$data = FormBuilder::find()->where(['formref_id'=>$id,'form_type'=>1,'remove'=>0])->asArray()->one();
    	
    		if(empty($data))
				echo 'no';
			else
				echo 'yes';
    	} 
    	// form type
    	if($form_type == 'data') {
    		$data = FormBuilder::find()->where(['formref_id'=>$id,'form_type'=>2,'remove'=>0])->asArray()->one();
    		if(empty($data))
                    echo 'no';
                else
                    echo 'yes';
    	}
    	die;
    }
    /**
     * Add / Edit Service Task Form Builder for selected service task
     * @return mixed
     */
    public function actionAddservicetaskbuilder()
    {
    	$get_data = Yii::$app->request->get();
      //  echo "<pre>",print_R($get_data); die();
    	$id = isset($get_data['servicetask_id'][0])?$get_data['servicetask_id'][0]:0;
    	$model = new FormBuilder();
    	$servicetaskModel = $this->findModelServiceTask($id);
    	if (Yii::$app->request->post()) {
			if(isset($_POST['properties'])) {
				foreach($_POST['properties'] as $el=>$val) {
					$_POST['properties'][$el]['label']=htmlentities($val['label']);
					$_POST['properties'][$el]['description']=htmlentities($val['description']);
					$_POST['properties'][$el]['default_answer']=htmlentities($val['default_answer']);
					if(isset($_POST['properties'][$el]['text_val'])) {
						$_POST['properties'][$el]['text_val']=htmlentities($val['text_val']);
					}
					if(isset($_POST['properties'][$el]['type']) && $_POST['properties'][$el]['type']=='dropdown') {
						if(isset($_POST['properties'][$el]['values'])) {
						$_POST['properties'][$el]['values']=html_entity_decode($val['values']);
						}
					}
				}
			}
			//echo "<pre>",print_R($_POST); die();
            $form_data = $model->ProcessFormData($_POST,'Servicetask');
            if($get_data['form'] == 'instruction') {
                $model->saveFormData($form_data,$servicetaskModel->id,1,'system'); // Form instruction Form
                $servicetaskModel->publish=Yii::$app->request->post('publish',0);
                $servicetaskModel->hasform=1;
            } else {
                $model->saveFormData($form_data,$servicetaskModel->id,2,'system'); // Form Data Form
                $servicetaskModel->data_publish=Yii::$app->request->post('publish',0);
                $servicetaskModel->data_hasform=1;
            }
            $servicetaskModel->save(false);
            return 'OK';
    	}    
    	if($get_data['form'] == 'instruction'){
            $formbuilder_data = $model->getFromData($id,1,'DESC','formbuilder',0,'system');                
    	}else{    	
            $formbuilder_data = $model->getFromData($id,2,'DESC','formbuilder',0,'system');
    	}
        return $this->renderAjax('Addservicetaskbuilder', [
            'model' => $model,
            'servicetaskModel'=>$servicetaskModel,
            'get_data'=>$get_data,
            'formbuilder_data'=>$formbuilder_data
    	]);
    }
    
    public function actionCheckisserviceuse(){
    	$id = Yii::$app->request->get('id',0);
    	$teamservice_id = Yii::$app->request->get('teamservice_id',0);
        $query = 'SELECT tbl_tasks.id FROM tbl_tasks INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id WHERE tbl_task_instruct.isactive = 1 AND tbl_task_instruct_servicetask.servicetask_id= '.$id.''; // GROUP BY tbl_task_instruct_servicetask.servicetask_id  
        $servicetask_model = Servicetask::find()->where('id != '.$id.' AND teamservice_id = '.$teamservice_id)->all();
        if(Tasks::findBySql($query)->count() > 0) {
            return 'You cannot remove a Task if the Task is used in 1 or more Project.';
            exit;
        }    
        // check tbl pricing service task  
        $query_result = 'SELECT tbl_pricing.id FROM tbl_pricing_service_task INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_service_task.pricing_id WHERE tbl_pricing_service_task.servicetask_id IN('.$id.') AND tbl_pricing.remove=0';
        if(Tasks::findBySql($query_result)->count() > 0) {                    
                return 'You cannot remove a Task because it is used in Pricing.';
                exit;
        }
    	$serviceModel = new ServiceTask();
    	return $serviceModel->CheckIsServiceUsed($id); 
    }
    
    public function actionCheckisserviceuseall(){
    	$id = Yii::$app->request->get('id',0);
    	$teamservice_id = Yii::$app->request->get('teamservice_id',0);
        $idArr = implode(',',$id);
        $query = 'SELECT tbl_tasks.id FROM tbl_tasks INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id WHERE tbl_task_instruct.isactive = 1 AND tbl_task_instruct_servicetask.servicetask_id IN ('.$idArr.')'; // GROUP BY tbl_task_instruct_servicetask.servicetask_id  
        $servicetask_model = Servicetask::find()->where('id NOT IN ('.$idArr.') AND teamservice_id = '.$teamservice_id)->all();
        if(Tasks::findBySql($query)->count() > 0) {
            return 'You cannot remove a Task if the Task is used in 1 or more Project.';
            exit;
        }    
        // check tbl pricing service task  
        $query_result = 'SELECT tbl_pricing.id FROM tbl_pricing_service_task INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_service_task.pricing_id WHERE tbl_pricing_service_task.servicetask_id IN('.$idArr.') AND tbl_pricing.remove=0';
        if(Tasks::findBySql($query_result)->count() > 0) {                    
                return 'You cannot remove a Task because it is used in Pricing.';
                exit;
        }
    	$serviceModel = new ServiceTask();
    	foreach($id as $val){
    		$res = $serviceModel->CheckIsServiceUsed($val);
    		if($res != 'N'){
    			return $res;
    		}
    	}
    	return $res;	
    }
    /**
     * Delete Selected Service  instruction or data Form
     * */
    public function actionDeleteselectedform(){
    	$id = Yii::$app->request->post('id',0);
    	$form= Yii::$app->request->post('form',0);
    	$model = new FormBuilder();
    	if($form == 'instruction'){
    		$model->deleteFromData($id,1);
    	}else{
    		$model->deleteFromData($id,2);
    	}
    	return 'OK';
    }
    /**
     * @abstract This action is define for a List of Assigned Team Users
     * @access Public
     * @since 1.0.0
     */
    public function actionTeamusers() {
    	$team_id = Yii::$app->request->get('team_id',0);

		if ($team_id != 1) {
    		$sqlselect = "tbl_user.id IN (SELECT tbl_project_security.user_id FROM tbl_project_security WHERE (tbl_project_security.team_id=".$team_id.") AND (tbl_project_security.team_id!=0)) AND tbl_user.usr_type NOT IN (0)";
    	} else {
    		$sqlselect = "tbl_user.id IN (SELECT tbl_user.id FROM tbl_user LEFT OUTER
				 JOIN tbl_role ON (tbl_user.role_id=tbl_role.id)  WHERE (tbl_user.id!=0 And tbl_user.role_id!=0 and
				 tbl_role.role_type like '%1%') GROUP BY tbl_user.id) AND tbl_user.usr_type NOT IN (0)";
    		// $criteria->condition="user.id!=0 And user.role_id!=-1 AND user.id IN (".implode(',',$user_ids).")";
    	}
		$role_sql="SELECT DISTINCT tbl_role.id,tbl_role.role_name FROM tbl_role INNER JOIN tbl_user ON tbl_user.role_id = tbl_role.id  WHERE ".$sqlselect." ORDER BY role_name";
		/*
		SELECT `tbl_user`.* FROM `tbl_user` LEFT JOIN `tbl_role` ON `tbl_user`.`role_id` = `tbl_role`.`id` WHERE tbl_user.i IN (SELECT tbl_user.id FROM tbl_user LEFT OUTER
JOIN tbl_role ON (tbl_user.role_id=tbl_role.id) WHERE (tbl_user.id!=0 And tbl_user.role_id!=0 and
tbl_role.role_type like '%1%') GROUP BY tbl_user.id) AND tbl_user.usr_type NOT IN (0) ORDER BY `role_name`, `usr_lastname`
		*/
		$roles=Yii::$app->db->createCommand($role_sql)->queryAll();
		$userList = [];
		if(!empty($roles)){
			foreach($roles as $role){
				$role_user_sql="SELECT tbl_user.id,tbl_user.usr_first_name,tbl_user.usr_lastname,tbl_user.usr_username FROM tbl_user WHERE tbl_user.role_id=".$role['id']." AND ".$sqlselect." ORDER BY tbl_user.usr_lastname";
				$usersbyrole=Yii::$app->db->createCommand($role_user_sql)->queryAll();
				if(!empty($usersbyrole)){
				$roleuser = [];
				$roleuser['title'] = $role['role_name'];
				$roleuser['isFolder'] = true;
				$roleuser['key'] = $role['id'];
						foreach($usersbyrole as $user){
							$users=[];
							$users['title'] = $user['usr_first_name']." ".$user['usr_lastname'];
							if($user['usr_first_name']=="" && $user['usr_lastname']=="")
								$users['title'] = $user['usr_username'];

							$users['key'] = $user['id'];
							$roleuser['children'][] = $users;
						}
				}
				if(!empty($roleuser['children']))
				$userList[]=$roleuser;
			}
		}
		//echo "<pre>",print_r($userList),"</pre>";die;
    	//$query = User::find()->joinWith('role')->where($sqlselect)->orderBy(['role_name' => SORT_ASC, 'usr_lastname'=>SORT_ASC])->all(); 
		//echo "<pre>",print_r($query);die;


//searchModel = new User();
//$dataProvider = $searchModel->searchTeamAssigned(Yii::$app->request->queryParams,$team_id);
    	
    	/*IRT 67,68,86,87,258*/
    	/*IRT 96,398 */
        //$filter_type=\app\models\User::getFilterType(["tbl_user.usr_username", "tbl_role.role_name"],['tbl_user','tbl_role']);
        //$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['workflow/ajax-user-filter']).'&team_id='.$team_id);
        /*IRT 67,68,86,87,258*/
       
        return $this->renderAjax('TeamAssignedUser', [
				'userList'=>$userList,
    			//'searchModel' => $searchModel,
    			//'dataProvider' => $dataProvider,
    			'teamservice_id' =>$teamservice_id,
    			//'filter_type' => $filter_type,
				//'filterWidgetOption' => $filterWidgetOption
    	]);
    }
    
    /**
    * Filter GridView with Ajax
    **/ 
    public function actionAjaxUserFilter()
    {
		$team_id = Yii::$app->request->get('team_id',0);
		$searchModel = new User();
		$dataProvider = $searchModel->searchFilterTeamAssigned(Yii::$app->request->queryParams, $team_id);
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			if($val == ''){
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		return json_encode($out);
    }
    
    /**
	 * Load Operational Team layout for Operational teamservice and service task
	 * */
    public function actionOperationalteam()
    {
    	$roleId =  Yii::$app->user->identity->role_id;
    	$TEAM_ID = Yii::$app->request->get('team_id',0);
    	
    	$team_ids = '';
    	$teamsecurity_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0')->groupBy('team_id')->all();
    	if ($roleId != 0) {
    		$teamsecurity_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0 AND user_id='.Yii::$app->user->identity->id)->groupBy('team_id')->all();
    	}
    	
    	if (!empty($teamsecurity_data)) {
    		foreach ($teamsecurity_data as $team_data) {
    			if ($team_ids == '')
    				$team_ids = $team_data->team_id;
    			else
    				$team_ids.=',' . $team_data->team_id;
    		}
    	}
    	$teamList = array();
    	if ($team_ids != '') {
    			if ($roleId == 0) {
    				$teamList = Team::find()->select(['id', 'team_name','team_type'])->where('id NOT IN (1)')->orderBy('sort_order')->all();
    			} else {
    				$teamList = Team::find()->select(['id', 'team_name','team_type'])->where('id In (' . $team_ids . ') AND id NOT IN (1)')->orderBy('sort_order')->all();
    			}
    	}

        $teamLocation = TeamlocationMaster::getTeamLocationList();
    	$model = new Team();
    	$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    	return $this->renderAjax('Operationalteam',[
			'model'=>$model,
			'teamList' => $teamList,
			'teamLocation' => $teamLocation, 
			'TEAM_ID' => $TEAM_ID,
			'model_field_length' => $model_field_length
			]);
    }
    /**
     * Add Oprational Team.
     * */
    public function actionAddteam(){
    	$teamId = Yii::$app->request->get('team_id',0);
    	$model = new Team();
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		$team_id = Yii::$app->db->getLastInsertId();
    		$model->logAndSecurity($team_id,$model->team_name);
    		return 'OK';
    		//$this->redirect(['view', 'id' => $model->id]);
    	} else {
    		$teamLocation = TeamlocationMaster::getTeamLocationList();
    		return $this->renderAjax('_teamform', [
    				'model' => $model,
    				'teamLocation'=>$teamLocation
    		]);
    	}
    }
    /**
     * Edit Oprational Team.
     * */
    public function actionEditeam(){
    	$team_id = Yii::$app->request->post('team_id',0);
    	$model = $this->findModelTeam($team_id);
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		$model->logAndSecurity($team_id,$model->team_name,'update');
    		return 'OK';
    		//$this->redirect(['view', 'id' => $model->id]);
    	} else {
    		$model->team_location=ArrayHelper::map(TeamLocs::find()->select('team_loc')->where('team_id='.$team_id)->all(), 'team_loc','team_loc');
    		$teamLocation = TeamlocationMaster::getTeamLocationList();
    		$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		return $this->renderAjax('_teamform', [
				'model' => $model,
				'teamLocation'=>$teamLocation,
				'model_field_length' => $model_field_length
    		]);
    	}
    }
    /**
     * Remove Operational Team.
     * */
    public function actionRemoveteam(){
        $team_id = Yii::$app->request->get('id',0);
        $teamservice_data = Teamservice::find()->where('tbl_teamservice.teamid = '.$team_id)->count();
        $servicetask_data = Servicetask::find()->where('tbl_teamservice.teamid = '.$team_id)->joinwith('teamservice')->all();
        $team_locs_data = TeamLocs::find()->where('tbl_team_locs.team_id = '.$team_id)->all();
        //Do not allow if service is added
        if($teamservice_data > 0 ){
           echo "false";
           exit; 
        }
        $flag = true;
        $err = "";
        if (!empty($teamservice_data)) {
            $flag = false;
        }
        if (!empty($servicetask_data)) {
            $flag = false;
        }
        if (!empty($team_locs_data)) {
            TeamLocs::DeleteAll('team_id = '.$team_id);
            $flag = true;
        }
        if($flag == true){
			ProjectSecurity::DeleteAll('team_id = '.$team_id);
			$modelTeam= Team::findOne($team_id);
			$teamname=$modelTeam->team_name;
			$modelTeam->delete();
			$activityLog = new ActivityLog();
			$activityLog->generateLog('Team','Deleted', $team_id, $teamname);
			echo "true";
			exit;
		}
		echo "false";
		exit;
	}
    /**
     * Sort Team.
     * */
    public function actionSortteam(){
    	$sort_ids = explode(",", Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
    				$model = Team::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->sort_order = $order + 1;
    				$model->save(false);
    			}
    			$transaction->commit();
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
    }
    /**
     * Load WorkFlow Teamplate 
     **/
    public function actionTemplates(){
    	$template_list =ArrayHelper::map(TasksTemplates::find()->select(['id','temp_name'])->orderBy(['temp_sortorder'=>SORT_ASC])->all(),'id','temp_name');
    	$model = new TasksTemplates(); 
    	$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);		
    	return $this->renderAjax('Templates', [
    			'model' => $model,
    			'template_list' => $template_list,
    			'model_field_length' => $model_field_length
    	]);
    }
    /**
     * get Service list
     **/
    public function actionTeamplatestask(){
    	$teamService = Teamservice::getPublicServiceTask();
		$serviceList=array();
		
		///echo "<pre>",print_r($teamService),"</pre>";//die;
		$teamservice_ids=array();
		if(!empty($teamService)){
			foreach($teamService as $tsdata){
				if(!in_array($tsdata['teamservice_id'].'_'.$tsdata['team_loc'],$teamservice_ids)){
				$teamser = [];				
				if($tsdata['team_loc'] == 0)
					$teamser['title'] = $tsdata['service_name'];
				else
					$teamser['title'] = $tsdata['service_name']." - ".$tsdata['team_location_name'];

				$teamser['isFolder'] = true;
				$teamser['key'] = $tsdata['teamservice_id'];	
				
				$servicetask = [];
				foreach($teamService as $inner_tsdata){
					if($inner_tsdata['teamservice_id'].'_'.$inner_tsdata['team_loc']!=$tsdata['teamservice_id'].'_'.$tsdata['team_loc']){
						continue;
					}
					$servicetask['title'] = $inner_tsdata['service_task'];
					$servicetask['key'] = $inner_tsdata['servicetask_id'].','.$inner_tsdata['team_loc'];
					$servicetask['service']=$inner_tsdata['service_task'];
					$servicetask['teamservice']=$teamser['title'];
					$servicetask['loc']=$inner_tsdata['team_loc'];
					$servicetask['servicetask_id'] = $inner_tsdata['servicetask_id'];
					$teamser['children'][] = $servicetask;
				}
				if(!empty($teamser['children']))
					$serviceList[] = $teamser;

				$teamservice_ids[$tsdata['teamservice_id'].'_'.$tsdata['team_loc']]=$tsdata['teamservice_id'].'_'.$tsdata['team_loc'];
				}
		}
		}
		//echo "<pre>",print_r($serviceList),"</pre>";die;
		/*foreach($clientListAr as $client_name => $clientCases){
			$client = [];
			foreach($clientCases as $client_id => $cases){
				$client['title'] = $client_name;
				$client['isFolder'] = true;
				$client['key'] = $client_id;
				$case = [];
				foreach($cases as $case_id => $case_name){
					$case['title'] = $case_name;
					$case['key'] = $client_id.','.$case_id;
					if($projectsecurity[$case_id] == $case['key']){
						$case['select'] = true;
						$selectedCases[] = $case['key'];
					} else {
						$case['select'] = false;
					}

					$client['children'][] = $case;
				}
				if(!empty($client['children']))
					$clientList[] = $client;
			}
		}*/
    	return $this->renderAjax('Teamplatestask', [
    		'teamService'=>$teamService,
			'serviceList'=>$serviceList
    	]);
    }
    /**
     * Add Workflow Templates.
     * */
    public function actionAddtemplate()
    {
    	if(Yii::$app->request->post()){			
	    	$model = new TasksTemplates();
	    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
	    		$id = Yii::$app->db->getLastInsertId();	    		
				/* IRT-42 starts*/				  
				$post_data = Yii::$app->request->post('request_type');
				  if(!empty($post_data) && isset($post_data)){				  
					  foreach($post_data as $single){
						  $rows[] = ['task_template_id'=>$id,'project_request_type_id'=>$single];
					  }
				  }
				  if(!empty($rows)){
					  $coulmns = (new TemplatesRequestTypes)->attributes();				  
					  unset($coulmns[array_search('id',$coulmns)]);				 
					  TemplatesRequestTypes::deleteAll(['task_template_id'=>$id]);				 
					  Yii::$app->db->createCommand()->batchInsert(TemplatesRequestTypes::tableName(),$coulmns,$rows)->execute();				  				  
				  }
			   /* IRT-42 ends*/
	    		$model->saveServices(Yii::$app->request->post(),$id);
	    		return 'OK';
	    	}
    	}else{
    		throw new yii\web\NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Edit Workflow Templates.
     * */
    public function actionEdittemplate($id){
    	if($id==0){
    		$model = new TasksTemplates();
    		$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);	
    		return $this->renderAjax('UpdateTeamplatestask', [
    			'model' => $model,
    			'model_field_length' => $model_field_length
    		]);
    	}
    	$model = $this->findModelTasksTemplates($id);
    	$services = $model->getServiceLocs($id);
    	if(Yii::$app->request->post()){
    		if ($model->load(Yii::$app->request->post()) && $model->save()) {
    			$model->saveServices(Yii::$app->request->post(),$id,'update');
                        /* IRT-42 starts*/				  
                        $post_data = Yii::$app->request->post('request_type');
                          if(!empty($post_data) && isset($post_data)){				  
                            foreach($post_data as $single){
                                    $rows[] = ['task_template_id'=>$id,'project_request_type_id'=>$single];
                            }
                          }
                          
                          if(!empty($rows)){
                            $coulmns = (new TemplatesRequestTypes)->attributes();				  
                            unset($coulmns[array_search('id',$coulmns)]);				 
                            TemplatesRequestTypes::deleteAll(['task_template_id'=>$id]);				 
                            Yii::$app->db->createCommand()->batchInsert(TemplatesRequestTypes::tableName(),$coulmns,$rows)->execute();				  				  
                          } else {
                          	TemplatesRequestTypes::deleteAll(['task_template_id'=>$id]);
                          }
			   /* IRT-42 ends*/
    			return 'OK';
    		}
    	}else{
//                        $all_request_types = ArrayHelper::map((new ProjectRequestType)->find()->select(['request_type','id'])->where('id IN (select project_request_type_id FROM tbl_templates_request_types where task_template_id = '.$id.')')->all(),'id','request_type');                        
			$query_all = (new TemplatesRequestTypes)->find()->select(['project_request_type_id'])->joinWith('projectRequestType')->where(['task_template_id'=>$id])->andWhere(['remove'=>'0'])->all();                       
			//echo '<pre>';                       
                        $all_request_types = [];
			foreach($query_all as $single){ 
                            $all_request_types[$single->project_request_type_id] = $single->projectRequestType->request_type;
			}	
//                        print_r($newArr);
//			die;
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);	
    		return $this->renderAjax('UpdateTeamplatestask', [
    				'model'=>$model,
    				'services'=>$services,
    				'model_field_length' => $model_field_length,
    				'all_request_types' => $all_request_types
    		]);
    	}
    }
    
    /**
     * Delete Workflow Templates.
     * */
    public function actionRemovetemplate(){
		$template_id = Yii::$app->request->get('id');
		if(isset($template_id) && $template_id != ''){
			TasksTemplatesServiceTasks::DeleteAll('task_template_id = '.$template_id);
			TemplatesRequestTypes::deleteAll(['task_template_id'=>$template_id]);				 
			$modelTemplate= TasksTemplates::findOne($template_id);
			$templatename=$modelTemplate->temp_name;
			$modelTemplate->delete();
			$activityLog = new ActivityLog();
			$activityLog->generateLog('Workflow Templates','Deleted', $template_id, $templatename);
		}
		
	} 
    /**
     * Sort Workflow Templates.
     * */
    public function actionSorttempalte(){
    	$sort_ids = explode(",",Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
    				$model = TasksTemplates::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->temp_sortorder = $order + 1;
    				$model->save(false);
    			}
    			$transaction->commit();
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
    }
    
    /*Check Team location is used already in service or template or in project before remove it from teamservice*/
    public function actionCheckteamloc(){
    	$team_id = Yii::$app->request->post('team_id',0);
    	$old_locs=Yii::$app->request->post('old_locs',array());
    	$curr_locs=Yii::$app->request->post('curr_locs',array());
    	$removed_loc=array();
		if(!empty($old_locs)){
			foreach($old_locs as $old_loc){
				if(isset($old_loc) && $old_loc!=""){
					if(!in_array($old_loc,$curr_locs)){
						$removed_loc[$old_loc]=$old_loc;
					}
				}
			}
		}
    	if(!empty($removed_loc)){
    		$checkuseInservice="SELECT tbl_teamservice.id FROM tbl_teamservice INNER JOIN tbl_teamservice_locs on tbl_teamservice_locs.teamservice_id=tbl_teamservice.id WHERE tbl_teamservice.teamid=$team_id AND tbl_teamservice_locs.team_loc IN (".implode(',',$removed_loc).") GROUP BY tbl_teamservice.id";
    		if(Teamservice::findBySql($checkuseInservice)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		$teamservice_idsql="SELECT tbl_teamservice.id FROM tbl_teamservice WHERE tbl_teamservice.teamid=$team_id";
    		$checkuseIntemplate="SELECT tbl_servicetask.id FROM tbl_tasks_templates_service_tasks INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_templates_service_tasks.service_task INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_servicetask.teamservice_id IN (".$teamservice_idsql.")  AND tbl_servicetask_team_locs.team_loc IN (".implode(',',$removed_loc).") GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseIntemplate)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		$checkuseInproject="SELECT tbl_servicetask.id FROM tbl_task_instruct_servicetask
    		INNER join tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id
    		INNER join tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id
    		INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_task_instruct_servicetask.servicetask_id INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_task_instruct_servicetask.teamservice_id IN (".$teamservice_idsql.") AND tbl_task_instruct_servicetask.team_loc IN (".implode(',',$removed_loc).")
    		AND tbl_task_instruct.isactive=1
    		GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseInproject)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		return "OK";
    	}else{
    		return "OK";
    	}
    }
    
    
    /*Check Team Service location is used already in service or template or in project before remove it from teamservice*/
    public function actionCheckteamsecrviceteamloc(){
    	$teamservice_id = Yii::$app->request->post('teamservice_id',0);
    	$old_locs=Yii::$app->request->post('old_locs',array());
    	$curr_locs=Yii::$app->request->post('curr_locs',array());
    	$removed_loc=array();
    	foreach($old_locs as $old_loc){
    		if(!in_array($old_loc,$curr_locs)){
    			$removed_loc[$old_loc]=$old_loc;
    		}
    	}
    	if(!empty($removed_loc)){
    		$checkuseInservice="SELECT tbl_servicetask.id FROM tbl_servicetask INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_servicetask.teamservice_id=$teamservice_id AND tbl_servicetask_team_locs.team_loc IN (".implode(',',$removed_loc).") GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseInservice)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		$checkuseIntemplate="SELECT tbl_servicetask.id FROM tbl_tasks_templates_service_tasks INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_templates_service_tasks.service_task INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_servicetask.teamservice_id=$teamservice_id AND tbl_servicetask_team_locs.team_loc IN (".implode(',',$removed_loc).") GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseIntemplate)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		$checkuseInproject="SELECT tbl_servicetask.id FROM tbl_task_instruct_servicetask
				INNER join tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id
				INNER join tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id
				INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_task_instruct_servicetask.servicetask_id INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_task_instruct_servicetask.teamservice_id=$teamservice_id AND tbl_task_instruct_servicetask.team_loc IN (".implode(',',$removed_loc).")
				AND tbl_task_instruct.isactive=1
				GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseInproject)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		return "OK";
    	}else{
    		return "OK";
    	}
    }
    /*Check Team Service location is used already in service or template or in project before remove it from teamservice*/
    public function actionCheckserviceteamloc(){
    	$servicetask_id = Yii::$app->request->post('servicetask_id',0);
    	$old_locs=Yii::$app->request->post('old_locs',array());
    	$curr_locs=Yii::$app->request->post('curr_locs',array());
    	$removed_loc=array();
    	foreach($old_locs as $old_loc){
    		if(!in_array($old_loc,$curr_locs)){
    			$removed_loc[$old_loc]=$old_loc;
    		}
    	}
    	if(!empty($removed_loc)){
    		$checkuseIntemplate="SELECT tbl_servicetask.id FROM tbl_tasks_templates_service_tasks INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_templates_service_tasks.service_task INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_servicetask.id=$servicetask_id AND tbl_servicetask_team_locs.team_loc IN (".implode(',',$removed_loc).") GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseIntemplate)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		$checkuseInproject="SELECT tbl_servicetask.id FROM tbl_task_instruct_servicetask
    		INNER join tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_servicetask.task_instruct_id
    		INNER join tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id
    		INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_task_instruct_servicetask.servicetask_id INNER JOIN tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id WHERE tbl_task_instruct_servicetask.servicetask_id=$servicetask_id AND tbl_task_instruct_servicetask.team_loc IN (".implode(',',$removed_loc).")
    		AND tbl_task_instruct.isactive=1
    		GROUP BY tbl_servicetask.id";
    		if(Servicetask::findBySql($checkuseInproject)->count()){
    			return 'You cannot remove this Team Location because it is used in a Workflow Template.';
    		}
    		return "OK";
    	}
    	else{
    		return "OK";
    	}
    }
    
    /**
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriorityProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelTeam($id)
    {
    	if (($model = Team::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new yii\web\NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Finds the Teamservice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriorityProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelTeamservice($id)
    {
    	if (($model = Teamservice::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new yii\web\NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Finds the ServiceTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriorityProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelServiceTask($id)
    {
    	if (($model = Servicetask::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new yii\web\NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Finds the TasksTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriorityProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelTasksTemplates($id)
    {
    	if (($model = TasksTemplates::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new yii\web\NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /*
     * IRT-42
     * Get the Project Request Type List
     * @param inter $id
     * @return mixed
     * @trows NotFoundHttpException if the model cannot be found.
     * */
     public function actionGetProjectRequestTypeLists($id){
        $model = new ProjectRequestType;
        $id = Yii::$app->request->get('id');			
        $post_data = Yii::$app->request->post();                    
        $request_typeids = [];
        if(isset($post_data['request_typeids']) && $post_data['request_typeids'] != ''){
            $request_typeids = explode(',',$post_data['request_typeids']);            
        }
        $requesttypeList = ArrayHelper::map(ProjectRequestType::find()->select(['id', 'request_type'])->where(['not in','id',$request_typeids])->andWhere(['remove'=>0])->orderBy('request_type')->all(),'id','request_type');      
		$rtList = [];
		foreach($requesttypeList as $id => $request_type) {
			    $member = [];
			
				$member['title'] = $request_type;
				$member['isFolder'] = false;
				$member['key'] = $id;
				
				$rtList[] = $member;
		}  
        return $this->renderAjax('get_all_project_request_types', [
    		'model' => $model,
    		'request_typeids' => $requesttypeList,	
			'rtList'=>$rtList
    	]);
		 
	 }
 
}
