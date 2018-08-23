<?php namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

use app\models\Client;
use app\models\ClientCase;
use app\models\Options; 
use app\models\Tasks;
use app\models\TasksTeams;
use app\models\TasksTeamSla;
use app\models\TaskInstruct;
use app\models\TaskInstructEvidence;
use app\models\TaskInstructNotes;
use app\models\TaskInstructServicetask;
use app\models\PriorityProject;
use app\models\search\TaskSearch;
use app\models\search\TaskInstructSearch;
use app\models\User;
use app\models\ActivityLog;
use app\models\SettingsEmail;
use app\models\ProjectSecurity;
use app\models\Role;
use app\models\Servicetask;
use app\models\Settings;
use app\models\TasksUnits;
use app\models\TasksUnitsBilling;
use app\models\TasksUnitsData;
use app\models\TasksUnitsTodos;
use app\models\TasksUnitsTodoTransactionLog;
use app\models\TasksUnitsTransactionLog;
use app\models\TeamlocationMaster;
use app\models\TaskInstructServicetaskSla;
use app\models\FormInstructionValues;

use app\models\Comments;
use app\models\CommentRoles;
use app\models\CommentTeams;
use app\models\CommentsRead;
use app\models\CommentTeamsUsers;
use app\models\CommentRolesUsers;
use app\models\Team;
use app\models\Mydocument;


use app\models\EvidenceProductionBates;
use app\models\EvidenceProduction;

use app\models\ProjectRequestType;
use app\models\EmailCron;

class CaseProjectsController extends \yii\web\Controller
{
     /**
    * load projects for selected client/case.
    * @return mixed
    * @param integer $case_id
    */
    public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(4.01) && $action->id == 'index')/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
			
		//$this->layout = 'mycase'; //your layout name	
		return parent::beforeAction($action);
	} 
    
    public function actionIndex()
    {
		$this->layout = "mycase";
		$case_id = Yii::$app->request->get('case_id',0);
		$active = Yii::$app->request->get('active',"active");
		$val = Yii::$app->request->get('val',"");
		$caseall = Yii::$app->request->get('caseall',"");
	
    	$session = new Session;
        $session->open();
        if (isset($case_id) && $case_id != '')
            $session['caseId'] = $caseId;
        if (isset($val) && $val != '')
            $session['val'] = $val;
        if (isset($caseall) && $caseall != '')
            $session['caseall'] = $caseall;
        
        $is_accessible_submodule=$is_accessible_submodule_tracktask=$has_access_40811=$has_access_40822=0;
        if(!isset($session['is_accessible_submodul'])){
        	$session['is_accessible_submodul']=(new User)->checkAccess(4.02); /* 50 */;
        }
        
        if(!isset($session['is_accessible_submodule_tracktask'])){
        	$session['is_accessible_submodule_tracktask']=(new User)->checkAccess(4.03); /* 50 */;
        }
        if(!isset($session['has_access_40811'])){
        	$session['has_access_40811']=(new User)->checkAccess(4.0811);
        }
        if(!isset($session['has_access_40822'])){
        	$session['has_access_40822']=(new User)->checkAccess(4.0822);
        }
        $has_access_40822=$session['has_access_40822'];
        $has_access_40811=$session['has_access_40811'];
        $is_accessible_submodule = $session['is_accessible_submodul'];
        
        $is_accessible_submodule_tracktask = $session['is_accessible_submodule_tracktask'];
        if(!isset($session['is_accessible_submodule_tracktask']) || $session['is_accessible_submodule_tracktask'] == '') {
        	$is_accessible_submodule_tracktask = 0;
        }
		$pporder=PriorityProject::find()->select(['priority_order'])->where('remove = 0')->orderBy(['priority_order'=>SORT_ASC])->one()->priority_order;
		
		/* IRT 67,68,86,87,258 */
        $filter_type=\app\models\User::getFilterType(['tbl_tasks.id','tbl_tasks.task_status','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_task_instruct.project_name','per_complete'],['tbl_tasks','tbl_task_instruct']);
        $config = ['task_status' => ['All' => 'All', '0'=>'Not Started','1'=>'Started','3'=>'On Hold','4'=>'Complete']];
        $config_widget_options = [
			'per_complete'=> [
				'filter_type' => 'range',
				'options' => ['placeholder' => 'Rate (0 - 100)','aria-label'=>'Range'],
				'html5Options' => ['min' => 0, 'max' => 100, 'step' => 1, 'aria-label' => 'Range Limit'],
			]
        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-projects/ajax-filter','case_id' => $case_id, 'params'=>Yii::$app->request->queryParams]),$config,$config_widget_options);

        /* IRT 67,68,86,87,258 */
        $searchModel = new TaskSearch();
		$params['grid_id']='dynagrid-caseprojects';
		Yii::$app->request->queryParams +=$params;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams,$params);
		$params = Yii::$app->request->queryParams;
		/* Start IRT 374 */
		$filterWidgetOption['task_status']['data'] = array_merge($filterWidgetOption['task_status']['data'], array('All Unread Comments'));
		/* End IRT 374 */
		
		return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'case_id' => $case_id, 
			'is_accessible_submodule_tracktask' => $is_accessible_submodule_tracktask,
			'pporder'=> $pporder,
			'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption,
			'params'=>$params
        ]);
    }
    
    /* Get Task Expand Details */
    public function actionGetTaskDetails()
    {
	$task_id = Yii::$app->request->post("expandRowKey");
	$flag = Yii::$app->request->get("flag",'');
	$task_data = Tasks::find()->select(['client_case_id', 'tbl_tasks.created', 'tbl_tasks.created_by', 'createdUser.usr_first_name', 'createdUser.usr_lastname', 'createdUser.usr_username'])->joinWith('createdUser')->where(['tbl_tasks.id'=>$task_id])->one();
	
	$teamservice_data = TaskInstructServicetask::find()->select(['tbl_task_instruct_servicetask.teamservice_id','tbl_teamservice.service_name'])->joinWith('teamservice')->where('tbl_task_instruct_servicetask.task_id='.$task_id.' AND task_instruct_id IN (select id from tbl_task_instruct where tbl_task_instruct.isactive=1 and tbl_task_instruct.task_id='.$task_id.')')->groupBy(['tbl_task_instruct_servicetask.teamservice_id', 'tbl_teamservice.service_name'])->all();  
	
	$servicetask_names = "";
	foreach($teamservice_data as $teamservice)     
	{
		if($servicetask_names != "")
		    $servicetask_names.='; ' . $teamservice->teamservice->service_name;
        else
        	$servicetask_names = $teamservice->teamservice->service_name;
	}
	
	$services=$servicetask_names;
	
	$has_access_408 = (new User)->checkAccess(4.08);

    $unread_comments = (new Tasks)->findReadUnreadComment($task_id,$task_data->client_case_id,$has_access_408);
	
   if($task_data->createdUser->usr_first_name!="" && $task_data->createdUser->usr_lastname!=""){
		$submitted_by = $task_data->createdUser->usr_first_name." ".$task_data->createdUser->usr_lastname;
	}else{
       	$submitted_by = $task_data->createdUser->usr_username;
    }
	
	$submitted_date = (new Options)->ConvertOneTzToAnotherTz($task_data->created, "UTC", $_SESSION["usrTZ"]);
	
	return $this->renderPartial('_loadtaskdetails', ['teamservice_data'=>$teamservice_data, 'services' => $services, 'comment' => $unread_comments, 'submitted_by' => $submitted_by, 'submitted_date' => $submitted_date,'flag'=>$flag,'task_id'=>$task_id]);
    }
    /**
     * Filter GridView with Ajax
     * */
    public function actionAjaxFilter(){
	    $case_id=Yii::$app->request->get('case_id',0);
	    $searchModel = new TaskSearch();
		$bodyparams=Yii::$app->request->bodyParams;
		Yii::$app->request->queryParams +=$bodyparams;
	    $dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams,$case_id);
	    $out['results']=array();
	    foreach ($dataProvider as $key=>$val){
		    $out['results'][] = ['id' => $val, 'text' => $val,'label' => $val];
	    }
	    return json_encode($out);
    }
    
    /**
    * load Canceled projects for selected client/case.
    * @return mixed
    * @param integer $case_id
    */
    public function actionLoadCanceledProjects()
    {
    	$this->layout = "mycase";
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    	$roleId = Yii::$app->user->identity->role_id; 
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
    	$searchModel = new TaskSearch();
    	$_REQUEST['TaskSearch']['task_cancel']=1;
    	$_REQUEST['TaskSearch']['task_status'][]='';
		$_REQUEST['grid_id']='dynagrid-canceled-projects';

    	$params = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);
        $dataProvider = $searchModel->search($params);
        $pporder=PriorityProject::find()->select(['priority_order'])->where('remove = 0')->orderBy('priority_order asc')->one()->priority_order;
        /*IRT 67,68,86,87,258*/
        //$filter_type=\app\models\User::getFilterType(['tbl_tasks.id','tbl_tasks.task_status','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_task_instruct.project_name','per_complete'],['tbl_tasks','tbl_task_instruct']);
        $filter_type=\app\models\User::getFilterType(['tbl_tasks.id','tbl_tasks.task_status','tbl_tasks.task_cancel_reason','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_task_instruct.project_name', 'per_complete'],['tbl_tasks','tbl_task_instruct']);
        $config = ['task_status'=>[''=>'Canceled']];
        $config_widget_options = [
			'per_complete' => [
				'filter_type' => 'range',
				'options' => ['placeholder' => 'Rate (0 - 100)','aria-label'=>'Range'],
				'html5Options' => ['min' => 0, 'max' => 100, 'step'=>1,'aria-label'=>'Range Limit'],
			]
        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-projects/ajax-filter','case_id' => $case_id,'task_cancel'=>1]),$config,$config_widget_options);
        
        
        /*IRT 67,68,86,87,258*/
    	return $this->render('_loadCanceledProjects', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
		    'case_id' => $case_id, 
    		'caseInfo' => $caseInfo,
    		'pporder' => $pporder,
    		'filter_type' => $filter_type,
    		'filterWidgetOption' => $filterWidgetOption
        ]);
    }
    
    /**
    * Uncancel projects for selected client/case.
    * @return mixed
    * @param integer $case_id
    * @param integer $task_list
    */
    public function actionUncancelProjects()
    { 
        $case_id = Yii::$app->request->get('case_id',0);
        $task_list = Yii::$app->request->post('task_list',0);
        Tasks::updateAll(['task_cancel' => 0, 'task_cancel_reason' => ''], ['in','id',$task_list]);
        
        foreach ($task_list as $task_id) {
        	(new ActivityLog())->generateLog('Project','Uncanceled', $task_id, 'project#:' . $task_id);
	        /* Sending UnCanceled Project Subscription Alert Email */
	        //SettingsEmail::sendEmail
			EmailCron::saveBackgroundEmail(8, 'is_uncanceled', $data = array('case_id' => $case_id, 'project_id' => $task_id));
	        /* Sending UnCanceled Project Subscription Alert Email */
        }
        
        return 'OK';
    }
    
	/**
    * load Closed projects for selected client/case.
    * @return mixed
    * @param integer $case_id
    */
    public function actionLoadClosedProjects()
    {
    	$this->layout = "mycase";
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
   
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
    	$searchModel = new TaskSearch();
       	$_REQUEST['TaskSearch']['task_closed']=1;
       	$_REQUEST['TaskSearch']['task_status'][]='';
		$_REQUEST['grid_id']='dynagrid-closed-projects';
    	$params = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);
        $dataProvider = $searchModel->search($params);
        $pporder=PriorityProject::find()->select(['priority_order'])->where('remove = 0')->orderBy('priority_order asc')->one()->priority_order;
        
        /* IRT 67,68,86,87,258 */
        $filter_type=\app\models\User::getFilterType(['tbl_tasks.id','tbl_tasks.task_status','tbl_tasks.task_cancel_reason','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_task_instruct.project_name', 'per_complete'],['tbl_tasks','tbl_task_instruct']);
        $config = ['task_status'=>[''=>'Closed']];
        $config_widget_options = [
			'per_complete' => [
				'filter_type' => 'range',
				'options' => ['placeholder' => 'Rate (0 - 100)','aria-label'=>'Range'],
				'html5Options' => ['min' => 0, 'max' => 100, 'step'=>1,'aria-label'=>'Range Limit'],
			]
        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-projects/ajax-filter','case_id' => $case_id,'task_closed'=>1]),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/
        
    	return $this->render('_loadClosedProjects', [ 
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
		    'case_id' => $case_id,
    		'caseInfo' => $caseInfo,
    		'pporder' => $pporder,
    		'filter_type'=>$filter_type,
    		'filterWidgetOption'=>$filterWidgetOption
        ]);
    }
    
    /**
    * ReOpen closed projects for selected client/case.
    * @return mixed
    * @param integer $case_id
    * @param integer $task_list
    */
    public function actionReopenProjects()
    {
        $case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
        
        $flag = Yii::$app->request->post('flag','all');
        if($flag == 'all') {
        	$searchModel = new TaskSearch();	       	
	       	$params = array_merge(Yii::$app->request->getQueryParams(),Yii::$app->request->post());
                $params['TaskSearch'] = array_merge($params['TaskSearch'],['task_closed'=>1]);
                $dataProvider = $searchModel->search($params);
	        $task_list = ArrayHelper::map($dataProvider->getModels(),"id","id");                
        } else {
        	$task_list = Yii::$app->request->post('task_list',array());
        }

        Tasks::updateAll(['task_closed' => 0],['in','id',$task_list]);
		
        foreach ($task_list as $task_id) {
        	(new ActivityLog())->generateLog('Project','Reopened', $task_id, 'project#:' . $task_id);
        	/* Start : Sending ReOpen Project Subscription Alert Email */				
	   		 //SettingsEmail::sendEmail
			 EmailCron::saveBackgroundEmail(9,'is_reopen_project',$data=array('case_id'=>$case_id,'project_id'=>$task_id));
			/* End : Sending ReOpen Project Subscription Alert Email */
        }
        return 'OK';
    }
    
    /**
    * Checks projects can be Canceled / Closed.
    * @return mixed
    * @param integer $case_id
    * @param integer $task_list
    */
    public function actionChkcanclosecancelproject()
    {
		$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
		$task_list = Yii::$app->request->post('task_list',array());
		
		$billableItemsLeft = TasksUnitsBilling::find()
			->joinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query) use($task_list,$case_id) {
			    $query->where(['tbl_tasks_units.task_id' => $task_list]) 
    				->innerJoinWith(['tasks'=>function(\yii\db\ActiveQuery $query) use($case_id){
    						$query->where(['tbl_tasks.client_case_id'=>$case_id]);
    					}]);
    				}])
			->where("tbl_tasks_units_billing.invoiced IS NULL OR tbl_tasks_units_billing.invoiced='' OR tbl_tasks_units_billing.invoiced=0")
			->count();
		
			
		if($billableItemsLeft > 0){
			return 'billableitemsleft';
		}
		$task_ids=0;
		if(is_array($task_list)){
			if(!empty($task_list)){
				$task_ids=implode(',',$task_list);
			}
		}else{
			$task_ids=$task_list;
		}
		$sql_billied="SELECT COUNT(*) FROM tbl_tasks_units inner join tbl_tasks_units_billing on tbl_tasks_units_billing.tasks_unit_id=tbl_tasks_units.id
		inner join tbl_invoice_final_billing on tbl_invoice_final_billing.billing_unit_id= tbl_tasks_units_billing.id WHERE tbl_tasks_units.task_id IN (".$task_ids.") ";

		$billabled = Yii::$app->db->createCommand($sql_billied)->queryScalar();
			
		if($billabled > 0){
			return 'billabled';
		}
		return '';
    }
    /**	
    * Close Projects.
    * @return mixed
    * @param integer $task_list
    */
    public function actionCloseProjects()
    {
		$task_list = Yii::$app->request->post('task_list',array());
		if(!empty($task_list)){
			$tasks = implode(",",$task_list);
			Tasks::updateAll(['task_closed'=>1],"id IN ($tasks)");
			$actLog = new ActivityLog();
	        $log = array();
			foreach ($task_list as $task_id) {
	        	$log[] = array('date_time'=>date('Y-m-d H:i:s'),'user_id'=>Yii::$app->user->identity->id,'username'=> Yii::$app->user->identity->usr_username,'origination'=> 'Project','activity_type'=>'Closed','activity_module_id'=> $task_id, 'activity_name'=>'project#:' . $task_id,'task_cancel_reason'=>'');
				$delete_pastdue_sql="DELETE FROM tbl_project_pastdue WHERE task_id IN (".$task_id.")";
                Yii::$app->db->createCommand($delete_pastdue_sql)->execute();
	        }
	        $actLog->generateBulkLog($log);
		}		
		return '';
    }
    
	/**	
    * Load & Cancel Project.
    * @return mixed
    * @param integer $task_list
    */
    public function actionLoadCancelProject()
    { 
		$id = Yii::$app->request->get('id',0);
		$model = Tasks::findOne($id);

		if ($model->load(Yii::$app->request->post())) {
			if(!$model->save()){
        		return $this->renderPartial('load-cancel-form',[
					'model' => $model
				]);            
        	} else {
        		/**
	        	 * When Task Canceled, below code will generate new log entry.  
	        	 */
    			(new ActivityLog)->generateLog('Project', 'Canceled', $model->id, 'project#:' . $model->id, $model->task_cancel_reason);
    			//(new SettingsEmail)->sendEmail
				EmailCron::saveBackgroundEmail(7, 'is_cancel', $data = array('case_id' => $model->client_case_id, 'project_id' => $id));
				$delete_pastdue_sql="DELETE FROM tbl_project_pastdue WHERE task_id IN (".$id.")";
                Yii::$app->db->createCommand($delete_pastdue_sql)->execute();
        	} 
        	return 'OK';
		}
		
		return $this->renderPartial('load-cancel-form',[
			'model' => $model
		]);
    }
    
    /**
     * Remove Project.
     * @return mixed
     * @param integer $task_list
     */
    public function actionRemoveProject()
    {
    	$task_list = Yii::$app->request->post('task_list',0);
    	$task_info = Tasks::findOne($task_list);
    	$taskId=$task_info->id;
    	if(isset($taskId) && $taskId!=0){
    		/*Remove Evid Production*/
    		$sql ="SELECT prod_id FROM tbl_task_instruct_evidence
    		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_evidence.task_instruct_id
    		 WHERE tbl_task_instruct.task_id =".$taskId." AND prod_id !=0 GROUP BY prod_id";
    		 
    		$sqlcount="SELECT prod_id FROM tbl_task_instruct_evidence
    		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_evidence.task_instruct_id
    		 WHERE prod_id IN (".$sql.") AND tbl_task_instruct.task_id !=".$taskId." GROUP BY prod_id";
    		 
    		if(EvidenceProduction::find()->where('id IN ('.$sqlcount.')')->count()){
    			EvidenceProduction::updateAll(['has_projects'=>1],'id IN ('.$sqlcount.')');
    		}else{
    			EvidenceProduction::updateAll(['has_projects'=>0],'id IN ('.$sql.')');
    		}
    		EvidenceProductionBates::deleteAll('task_id='.$taskId);
    		/*Remove Evid Production*/
    		
    		/*Remove Project Comments*/
    		(new Comments)->removeCommentsAttachmentsByProject($taskId);
    		$sql="SELECT Id FROM tbl_comments WHERE task_id=".$taskId;
			$sql_comment_role_ids="SELECT id FROM tbl_comment_roles WHERE comment_id IN ($sql)";
			$sql_comment_team_ids="SELECT id FROM tbl_comment_teams WHERE comment_id IN ($sql)";

			
    		CommentTeamsUsers::deleteAll('tbl_comment_team_id IN ('.$sql_comment_team_ids.')');
			CommentTeams::deleteAll('comment_id IN ('.$sql.')');
			CommentRolesUsers::deleteAll('tbl_comment_role_id IN ('.$sql_comment_role_ids.')');
    		CommentRoles::deleteAll('comment_id IN ('.$sql.')');
    		CommentsRead::deleteAll('comment_id IN ('.$sql.')');
    		Comments::deleteAll('task_id ='.$taskId); 
    		/*Remove Project Comments*/
    		/*Remove Billings*/
    		$task_unit_id = TasksUnits::find()->select(['tbl_tasks_units.id'])->where(['task_id'=>$taskId]);
    		TasksUnitsBilling::deleteAll(['IN','tasks_unit_id',$task_unit_id]);
    		TasksUnitsData::deleteAll(['IN','tasks_unit_id',$task_unit_id]);
    		/*Remove Billings*/
    		/*Remove Todo*/
    		(new TasksUnitsTodos)->removeTodoAttachmentsByProject($taskId);
    		
    		$todo_id = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id'])->innerJoinWith('taskUnit')->where(['tbl_tasks_units.task_id'=>$taskId]);
				
				
    		TasksUnitsTodoTransactionLog::deleteAll(['IN','todo_id',$todo_id]);
    		
    		$task_unit_ids = TasksUnits::find()->select(['tbl_tasks_units.id'])->where(['task_id'=>$taskId]);
    		
    		TasksUnitsTodos::deleteAll(['IN','tasks_unit_id',$task_unit_ids]);
    		/*Remove Todo*/
    		/*Remove Instruction Notes*/
    		(new TaskInstructNotes)->removeTaskInstructNotesAttachmentsByProject($taskId);
    		TaskInstructNotes::deleteAll("task_id=".$taskId);
    		/*Remove Instruction Notes*/
    		/*Remove Task Units*/
    		TasksUnitsTransactionLog::deleteAll(['IN','tasks_unit_id',$task_unit_ids]);
    		
    		$task_instruct_ids = TaskInstruct::find()->select(['tbl_task_instruct.id'])->where(['tbl_task_instruct.task_id'=>$taskId,'tbl_task_instruct.isactive'=>1]);
    		
    		TasksUnits::deleteAll(['IN','task_instruct_id',$task_instruct_ids]);
    		/*Remove Task Units*/
    		/*Remove Task Instructions*/
    		(new TaskInstruct)->removeTaskInstructAttachmentsByProject($taskId);
    		TaskInstructServicetaskSla::deleteAll("task_instruct_servicetask_id IN (SELECT id FROM tbl_task_instruct_servicetask WHERE task_id = ".$taskId.")");
    		TaskInstructServicetask::deleteAll("task_id=".$taskId);
    		
    		TaskInstructEvidence::deleteAll(['IN','task_instruct_id',$task_instruct_ids]);
    		
    		FormInstructionValues::deleteAll("task_instruct_id IN (SELECT id FROM tbl_task_instruct WHERE task_id={$taskId})");
    		
    		TaskInstruct::deleteAll("task_id=".$taskId);
    		/*Remove Task Instructions*/
    		/*Remove Task Teams*/
    		$sqlteamsql="tasks_teams_id IN (SELECT id FROM tbl_tasks_teams WHERE task_id=".$taskId.")";
    		////TasksTeamSla::deleteAll($sqlteamsql);
    		
    		TasksTeams::deleteAll("task_id=".$taskId);
    		/*Remove Task Teams*/
    		$activity_name=$taskId.":".$task_info->client_case_id."|project#:".$taskId;// usr_username
    		(new ActivityLog)->generateLog('Project', 'Deleted', $taskId, $activity_name);
    		$task_info->delete();
    	}
    	exit;
    }
    /* view task instruction details */
    public function actionViewTaskMedia($task_id) {
		$project_track_data = (new TaskInstructServicetask)->getTrackProjectMediaData($task_id);
		$task_instructions_data = $project_track_data->getModels();
		$task_data=Tasks::find()->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) {
		$query->where('isactive=1')
		 ->joinWith(['taskInstructEvidences'=> function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_task_instruct_evidence.id','tbl_task_instruct_evidence.prod_id','tbl_task_instruct_evidence.task_instruct_id'])->joinWith('evidenceProduction');
			}]);
		 },  
		 ])->where(['tbl_task_instruct.task_id' => $task_id])->one();
		$old_instruction_id = TaskInstruct::find()->select('id')->where('task_id = '.$task_id.' AND instruct_version = '.($task_data->taskInstruct->instruct_version-1))->one()->id;
		foreach($task_instructions_data as $key => $val) {
			// echo "<pre>",print_r($val),"</pre>";die;
		    $servicetask_id = $val['servicetask_id'];
		    $taskunit_id   = $val['taskunit_id'];
		    $sort_order    = $val['sort_order'];
		    $teamId        = $val['teamId'];
		    $team_loc      = $val['team_loc'];
		    $processTrackData[$key] 	= (new TaskInstructServicetask)->processTrackData($servicetask_id,$sort_order,$task_id,0,$teamId,$taskunit_id,$options);
		}
		
		$cnt_instruction_evidence = 0;
    	if(isset($old_instruction_id)) {
    	    $prev_instruction=TaskInstruct::find()->where('task_id = '.$task_id.' AND instruct_version = '.($task_data->taskInstruct->instruct_version-1))->one()->toArray();
            $cnt_instruction_evidence=TaskInstructEvidence::find()->select(['evidence_id'])->where('task_instruct_id IN('.$task_data->taskInstruct->id.','.$prev_instruction["id"].')')->groupBy('evidence_id')->having('COUNT(evidence_id) != 2')->count();
        } 
		return $this->renderPartial('view-task-media',[
		    'task_instructions_data'=>$task_instructions_data,
		    'task_id' => $task_id,
		    'processTrackData'=>$processTrackData,
		    'servicetask_id'=>$servicetask_id,
			'old_instruction_id'=>$old_instruction_id,
		    'teamId'=>$teamId,
		    'team_loc'=>$team_loc,
			'prev_instruction'=>$prev_instruction,
		    'cnt_instruction_evidence'=>$cnt_instruction_evidence
		    ]);
	}
    /* view task instruction details */
    public function actionViewTaskInstructions($task_id)
    {
		$duedate = Yii::$app->request->get('duedate','');
		$project_track_data = (new TaskInstructServicetask)->getTrackProjectDataWithMedia($task_id);
		$task_instructions_data = $project_track_data->getModels();
		$project_request_type = ArrayHelper::map(ProjectRequestType::find()->select(['id','request_type'])->orderBy('request_type ASC')->all(),'id','request_type');
		/*$task_data=Tasks::find()->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) {
		
		$query->select(['tbl_task_instruct.id','tbl_task_instruct.instruct_version','tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue','tbl_task_instruct.project_name','tbl_task_instruct.requestor','tbl_task_instruct.task_projectreqtype','tbl_task_instruct.task_priority'])
		->where('isactive=1')
		->joinWith(['taskPriority'=>function(\yii\db\ActiveQuery $query){
			 $query->select(['tbl_priority_project.priority']);
		}])
		->joinWith(['taskInstructEvidences'=> function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_task_instruct_evidence.id','tbl_task_instruct_evidence.prod_id','tbl_task_instruct_evidence.task_instruct_id'])->joinWith('evidenceProduction');
			}]);
		}, 'createdUser'=>function(\yii\db\ActiveQuery $query){
			 $query->select(['createdUser.usr_first_name','createdUser.usr_lastname']);
		}, 
		 'clientCase' => function (\yii\db\ActiveQuery $query) { 
			 $query->select(['tbl_client_case.id','tbl_client_case.case_name','tbl_client_case.client_id','tbl_client_case.case_manager','tbl_client_case.sales_user_id','tbl_client_case.internal_ref_no'])->joinWith('salesRepo'); 
		 }])
		 ->where(['tbl_task_instruct.task_id' => $task_id])->one();*/
		 if($duedate == '') {
			$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
			if (Yii::$app->db->driverName == 'mysql') {
				$data_query_sql = "SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			} else {
				$data_query_sql = "SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			}
		 } else {
			 $data_query_sql = "CONCAT(tbl_task_instruct.task_duedate,'',tbl_task_instruct.task_timedue)";
		 }
		 $taskdata_sql="SELECT 
		 tbl_tasks.id,tbl_tasks.created as submitted_date,(".$data_query_sql.") as task_date_time,tbl_task_instruct.id as instruction_id,tbl_task_instruct.instruct_version,tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,tbl_task_instruct.project_name,tbl_task_instruct.requestor,tbl_task_instruct.task_projectreqtype,tbl_task_instruct.task_priority,
		 tbl_priority_project.priority,
		 createdUser.usr_first_name as taskcreate_fn,createdUser.usr_lastname as taskcreate_ln,
		 tbl_user.usr_first_name as salserepofn,tbl_user.usr_lastname as salserepoln,
		 tbl_client.client_name,
		 tbl_client_case.id as client_case_id,tbl_client_case.case_name,tbl_client_case.client_id,tbl_client_case.case_manager,tbl_client_case.sales_user_id,tbl_client_case.internal_ref_no
		 FROM tbl_tasks 
		 LEFT JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id 
		 LEFT JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id 
		 LEFT JOIN tbl_task_instruct_evidence ON tbl_task_instruct.id = tbl_task_instruct_evidence.task_instruct_id 
		 LEFT JOIN tbl_evidence_production ON tbl_task_instruct_evidence.prod_id = tbl_evidence_production.id 
		 LEFT JOIN tbl_user createdUser ON tbl_tasks.created_by = createdUser.id 
		 LEFT JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id 
		 LEFT JOIN tbl_client ON tbl_client.id = tbl_client_case.client_id 
		 LEFT JOIN tbl_user ON tbl_client_case.sales_user_id = tbl_user.id 
		 WHERE (tbl_task_instruct.task_id='".$task_id."') AND (isactive=1)";
		$taskdata = Yii::$app->db->createCommand($taskdata_sql)->queryOne();

		//echo "<pre>",print_r($taskdata),"</pre>";die;
		$old_instruction_id = TaskInstruct::find()->select('id')->where('task_id = '.$task_id.' AND instruct_version = '.($taskdata['instruct_version']-1))->one()->id;
		$settings_data = array();
		foreach($task_instructions_data as $key => $val){
			// echo "<pre>",print_r($val),"</pre>";die;
		    $servicetask_id = $val['servicetask_id'];
		    $taskunit_id   = $val['taskunit_id'];
		    $sort_order    = $val['sort_order'];
		    $teamId        = $val['teamId'];
		    $team_loc      = $val['team_loc'];
		    $processTrackData[$key] 	= (new TaskInstructServicetask)->processTrackDataInstruction($servicetask_id,$sort_order,$task_id,0,$teamId,$taskunit_id,$options);
			//(new TaskInstructServicetask)->processTrackData($servicetask_id,$sort_order,$task_id,0,$teamId,$taskunit_id,$options);
		}
		//echo "<pre>",print_r($processTrackData);die;
		$acces_team_arr 	= (new ProjectSecurity)->getUserTeamsArr(Yii::$app->user->identity->id);
    	$acces_team_loc_arr = (new ProjectSecurity)->getUserTeamsLocArr(Yii::$app->user->identity->id);
    	$roleId             = Yii::$app->user->identity->role_id;
    	$roleInfo=Role::findOne($roleId);
    	$User_Role=explode(',',$roleInfo->role_type);
    	if($roleId=='0') { 
    		$acces_team_arr[1] = 1;
    	} if(in_array(1,$User_Role)) {
    		$acces_team_arr[1] = 1;
    	}
    	$belongtocurr_team_serarr = (new Servicetask)->getBelongto(Yii::$app->user->identity->id);
    	$stlocaccess = (new Servicetask)->getBelongtoLoc(Yii::$app->user->identity->id);
    	$changeFBIds=array();
        $prev_instruction=array();
        $instruction_evidence=array();
		$cnt_instruction_evidence = 0;
    	if(isset($old_instruction_id)){
    		$changeFBIds = (new Tasks)->getChangedFBID($task_id);
            $prev_instruction=TaskInstruct::find()->where('task_id = '.$task_id.' AND instruct_version = '.($taskdata["instruct_version"]-1))->one()->toArray();
            $cnt_instruction_evidence=TaskInstructEvidence::find()->select(['evidence_id'])->where('task_instruct_id IN('.$taskdata["instruction_id"].','.$prev_instruction["id"].')')->groupBy('evidence_id')->having('COUNT(evidence_id) != 2')->count();
        } 
        
        //echo "<pre>";print_r($processTrackData);die;
        
		return $this->renderPartial('view-task-instructions',[
		    'task_instructions_data'=>$task_instructions_data,
		    'task_id' => $task_id,
		    'settings_data' => $settings_data,
		    'task_data' => $task_data,
			'duedate'=>$duedate,
			'taskdata'=>$taskdata,
		    'stlocaccess'=>$stlocaccess,
		    'processTrackData'=>$processTrackData,
		    'servicetask_id'=>$servicetask_id,
		    'teamId'=>$teamId,
		    'team_loc'=>$team_loc,
		    'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
		    'project_request_type' => $project_request_type,
                            'old_instruction_id'=>$old_instruction_id,
		    'changeFBIds'=>$changeFBIds,
                            'prev_instruction'=>$prev_instruction,
                            'cnt_instruction_evidence'=>$cnt_instruction_evidence
		    ]);
    }
    
    /**
     * Post Project Comment Case wise
     * */
    public function actionPostComment($task_id,$case_id)
    {
    	$refere = Yii::$app->request->referrer;
    	if(!strpos($refere,'case-projects/post-comment')){
    		Yii::$app->getUser()->setReturnUrl(Yii::$app->request->referrer);
    	}
    	$this->layout = "mycase";
    	$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
    	if(!empty($comment_data)) {
    		$comments_rows=array();
    		foreach ($comment_data as $comment) {
				$commentsAttr = array();
				$commentsAttr['comment_id']=$comment->Id;
				$commentsAttr['user_id']=Yii::$app->user->identity->id;
				$comments_rows[] = $commentsAttr;
		    }
    		if(!empty($comments_rows)) {
    			$columns = (new CommentsRead)->attributes();
    			unset($columns[array_search('Id',$columns)]);
    			Yii::$app->db->createCommand()->batchInsert(CommentsRead::tableName(), $columns, $comments_rows)->execute();
    		}
    	}
    	$model = new Comments();
    	if(Yii::$app->request->post()) {
    		$post_data = Yii::$app->request->post();
			//echo "<pre>",print_r($post_data),"</pre>";die;
    		if($model->postComment($post_data,$task_id,$case_id)) {
				$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
				return $this->renderAjax('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'case_id'=>$case_id,'model'=>$model]);
    		} else {
    			return $this->render('PostComment',['comment_data'=>$comment_data,'task_id'=>$task_id,'case_id'=>$case_id,'model'=>$model]);
    		}
    	}
    	return $this->render('PostComment',['comment_data'=>$comment_data,'task_id'=>$task_id,'case_id'=>$case_id,'model'=>$model]);
    }

	/**
     * Case comment Receipents New approach 
     * */
	  public function actionNewrecipients(){
	  	$roleId = Yii::$app->user->identity->role_id;
		$userId = Yii::$app->user->identity->id;
		$role_type = Role::find()->select('role_type')->where('id = '.$roleId)->one()->role_type;
		$role_explode = explode(',',$role_type);
    	$task_id=Yii::$app->request->post('task_id');
    	$case_id=Yii::$app->request->post('case_id');
    	
		$fixed_emailsend_user_ids=explode(",",Yii::$app->request->post('fixed_emailsend_user_ids',0));
    	$case_ids=explode(", ",Yii::$app->request->post('case_ids',0));
    	$team_ids=explode(", ",Yii::$app->request->post('team_ids',0));
    	
    	$cases_ids=explode(", ",Yii::$app->request->post('cases_ids',0));
    	$teams_ids=explode(", ",Yii::$app->request->post('teams_ids',0));
    	/*case role users*/			
		$case_roles="SELECT id FROM tbl_role WHERE role_type like '%1%' AND id > 0";
		//$case_users = User::find()->select(['id','role_id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname"])->with('role')->where('role_id IN ('.$case_roles.')')->orderBy('role_id')->asArray()->all();
		$casemain_sql="SELECT tbl_user.id, role_id,role_name, CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname FROM tbl_user 
		INNER join tbl_project_security on tbl_project_security.user_id= tbl_user.id 
		INNER JOIN tbl_role on tbl_role.id=role_id 
		WHERE role_id IN ($case_roles) AND tbl_project_security.client_case_id=$case_id ORDER BY  role_name,fullname";
		$case_users = Yii::$app->db->createCommand($casemain_sql)->queryAll();
		$caseUserList = [];
		$role_ids=array();
		if(!empty($case_users)){
			$i=0;
			$k=0;
			foreach($case_users as $cuser){
				if(!in_array($cuser['role_id'],$role_ids)){
					if($k>0){$i++;}
					$caseUserList[$i]['title'] = $cuser['role_name'];
					$caseUserList[$i]['key'] = $cuser['role_id'];
					$caseUserList[$i]['isFolder'] = true;
					if(in_array($cuser['role_id'],$cases_ids)){
						$caseUserList[$i]['select'] = true;
					}
				}
				$user_arr=[];
				$span_id="caserole_".$cuser['role_id']."_".$cuser['id'];
				$span_class="";
				if(in_array($span_id,$fixed_emailsend_user_ids)){
					$span_class="fa fa-envelope";
					if(in_array($cuser['role_id']." ".$cuser['id'],$case_ids)){
						$user_arr['select'] = true;
					}
				}else{
					if(in_array($cuser['role_id']." ".$cuser['id'],$case_ids)){
						$user_arr['select'] = true;
						$span_class="fa fa-envelope-o";
					}
				}
				$user_arr['title'] = $cuser['fullname']."&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='caseemailids(this);' class='spn_caseemailids $span_class' id='caserole_".$cuser['role_id']."_".$cuser['id']."'></a>";
				$user_arr['titletext'] = $cuser['fullname'];
				$user_arr['key'] = $cuser['role_id']." ".$cuser['id'];
				$user_arr['isFolder'] = false;
				$user_arr['id'] = $cuser['id'];
				$user_arr['role_id']=$cuser['role_id'];
				$user_arr['role_name']=$cuser['role_name'];
				$caseUserList[$i]['children'][] = $user_arr;
				$role_ids[$cuser['role_id']]=$cuser['role_id'];
				$k++;
			}
		}
		/*Team users*/
		$sql = "SELECT team_id FROM tbl_tasks_units WHERE task_id = ".$task_id." group by team_id";		
		$main_sql="SELECT DISTINCT team_id,tbl_user.id,team_name,CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname FROM tbl_user
		INNER join tbl_project_security on tbl_project_security.user_id= tbl_user.id 
		INNER join tbl_team on tbl_team.id=tbl_project_security.team_id
		WHERE team_id IN ($sql)";
		$team_data=Yii::$app->db->createCommand($main_sql)->queryAll();
		$teamUserList = [];
		$teamids=array();
		if(!empty($team_data)){
			$i=0;
			$k=0;
			foreach($team_data as $tuser){
				if(!in_array($tuser['team_id'],$teamids)){
					if($k>0){$i++;}
					$teamUserList[$i]['title'] = $tuser['team_name'];
					$teamUserList[$i]['key'] = $tuser['team_id'];
					$teamUserList[$i]['isFolder'] = true;
					if(in_array($tuser['team_id'],$teams_ids)){
						$teamUserList[$i]['select'] = true;
					}
				}
				$user_arr=[];
				$span_id="teammanager_".$tuser['team_id']."_".$tuser['id'];
				$span_class="";
				if(in_array($span_id,$fixed_emailsend_user_ids)){
					$span_class="fa fa-envelope";
					if(in_array($tuser['team_id']." ".$tuser['id'],$team_ids)){
						$user_arr['select'] = true;
					}
				}else{
					if(in_array($tuser['team_id']." ".$tuser['id'],$team_ids)){
						$user_arr['select'] = true;
						$span_class="fa fa-envelope-o";
					}
				}
				$user_arr['title'] = $tuser['fullname']."&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='teamemailids(this);' class='spn_teamemailids $span_class' id='teammanager_".$tuser['team_id']."_".$tuser['id']."'></a>";
				$user_arr['titletext'] = $tuser['fullname'];
				$user_arr['key'] = $tuser['team_id']." ".$tuser['id'];
				$user_arr['isFolder'] = false;
				$user_arr['id']=$tuser['id'];
				$user_arr['team_id']=$tuser['team_id'];
				$user_arr['team_name']=$tuser['team_name'];
				$teamUserList[$i]['children'][] = $user_arr;
				$teamids[$tuser['team_id']]=$tuser['team_id'];
				$k++;
			}
		}
		return $this->renderAjax('Recipientsnew',['teamUserList'=>$teamUserList,'caseUserList'=>$caseUserList,'role_explode'=>$role_explode,'role_data'=>$role_data,'team_data'=>$team_data,'case_ids'=>$case_ids,'team_ids'=>$team_ids, 'cases_ids' => $cases_ids, 'teams_ids' => $teams_ids]);
    }
    /**
     * Case comment Receipents
     * */
    public function actionRecipients(){
	  	$roleId = Yii::$app->user->identity->role_id;
		$userId = Yii::$app->user->identity->id;
		$role_type = Role::find()->select('role_type')->where('id = '.$roleId)->one()->role_type;
		$role_explode = explode(',',$role_type);
    	$task_id=Yii::$app->request->post('task_id');
    	$case_id=Yii::$app->request->post('case_id');
    	
    	$case_ids=explode(", ",Yii::$app->request->post('case_ids',0));
    	$team_ids=explode(", ",Yii::$app->request->post('team_ids',0));
    	
    	$cases_ids=explode(", ",Yii::$app->request->post('cases_ids',0));
    	$teams_ids=explode(", ",Yii::$app->request->post('teams_ids',0));
    	
    	$role_data=ArrayHelper::map(Role::find()->select(['id','role_name'])->where("role_type like '%1%' AND id > 0")->orderBy('role_name')->all(),'id','role_name');

		
    	$sql="SELECT tbl_tasks_teams.team_id FROM tbl_tasks_teams INNER JOIN tbl_project_security ON tbl_tasks_teams.team_id=tbl_project_security.team_id AND tbl_tasks_teams.team_loc=tbl_project_security.team_loc WHERE task_id=$task_id AND tbl_project_security.user_id=$userId";
    	// To get Teams which are associated with Project.
    	$sql = "SELECT team_id FROM tbl_tasks_units WHERE task_id = ".$task_id;
    	$team_data=ArrayHelper::map(Team::find()->select(['id','team_name'])->where('id IN ('.$sql.') AND id NOT IN (1)')->orderBy('team_name')->all(),'id','team_name');
    	return $this->renderAjax('Recipients',['role_explode'=>$role_explode,'role_data'=>$role_data,'team_data'=>$team_data,'case_ids'=>$case_ids,'team_ids'=>$team_ids, 'cases_ids' => $cases_ids, 'teams_ids' => $teams_ids]);
    }
    
    /**
     * Case comment Receipents
     **/
    public function actionRecipientsUsers()
    {
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$case_roles = Yii::$app->request->post('case_roles');
    	$team_roles = Yii::$app->request->post('team_roles');
    	$case_ids=explode(", ",Yii::$app->request->post('case_ids',0));
    	$team_ids=explode(", ",Yii::$app->request->post('team_ids',0));
    	$case_users = '';
    	$team_users = '';
    	if($case_roles) 
            $case_users = User::find()->select(['id','role_id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname"])->with('role')->where('role_id IN ('.$case_roles.')')->asArray()->all();

    	if($team_roles) {
    		$team_users = Projectsecurity::find()->select(['tbl_project_security.user_id','tbl_project_security.team_id'])
    		->joinWith([
				'user' => function(\yii\db\ActiveQuery $query) {
    			$query->select(['tbl_user.id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname"]);
    			},
				'team'
			])
			->where('tbl_project_security.team_id IN ('.$team_roles.')')
    		->groupBy(['tbl_project_security.user_id','tbl_project_security.team_id'])->asArray()->all();
		}
    	return $this->renderAjax('RecipientsUsers',['case_users' => $case_users, 'team_users' => $team_users, 'case_ids' => $case_ids, 'team_ids' => $team_ids]);
    }
    
    /**
     * Case Edit comment
     * */
    public function actionEditComment($id,$task_id,$case_team_id){
    	$model = Comments::findOne($id);
    	if(Yii::$app->request->post()) {
    		$post_data = Yii::$app->request->post();
    		$model->editComment($post_data,$id,$task_id);
    		$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
    		return $this->renderAjax('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'case_id'=>$case_team_id,'model'=>$model]);
    	}
    	return $this->renderAjax('EditComment',['model'=>$model,'task_id'=>$task_id,'case_team_id'=>$case_team_id,]);
    	
    }
    /**
     * Case Comment Reply
     * */
    public function actionReplyComment($id,$task_id,$case_team_id){
    	$model = new Comments();
    	if(Yii::$app->request->post()) {
	    	$post_data = Yii::$app->request->post();
	    	$model->replyComment($post_data,$id,$task_id);
	    	$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
    		return $this->renderAjax('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'case_id'=>$case_team_id,'model'=>$model]);
    	}
    	return $this->renderAjax('ReplyComment',['model'=>$model,'id'=>$id,'task_id'=>$task_id,'case_team_id'=>$case_team_id]);
    }
    public function actionDeleteComment($id,$msg){
    	return (new Comments())->deleteComment($id,$msg);
    }
    /**
     * Add new project.
     * @return mixed
     */
    public function actionAdd()
    {
	$this->layout = "mycase";
	$case_id = Yii::$app->request->get('case_id',0);
        $model = new Tasks();
	$instruct_model = new TaskInstruct();
        
	$priorityList = PriorityProject::find()->select(['id', 'priority'])->where(['remove' => 0])->asArray()->all();
	
	$case_productions = EvidenceProduction::find()->where(['client_case_id' => $case_id, 'has_media' => 1])->orderBy('id desc')->all();
	
        return $this->render('add', [
        'model' => $model,
		'instruct_model' => $instruct_model,
		'priorityList' => $priorityList,
		'case_id' => $case_id,
		'case_productions' => $case_productions
	]);
        
    } 
    /* Read Comments */
    public function actionReadcomment()
    {
		$task_id = Yii::$app->request->post('task_id',0);
		
		$comment_arr = (new Tasks)->getnewcommentsByTeamOrCase($task_id, 'task');
		
	    $user_id = Yii::$app->user->identity->id;
		
		
	
		if(!empty($comment_arr))
		{
		    foreach($comment_arr as $cmt)
		    {
			$commentsRead = (new CommentsRead);
			$commentsRead->comment_id = $cmt;
			$commentsRead->user_id = $user_id;
			$commentsRead->save(false);
		    }  
		}
		die();
    }
    
    /**
     * Bulk Assign project Functionality
     * @return
     */
    public function actionBulkAssignProject()
    {
    	$this->layout = "mycase";
    	$caseId = Yii::$app->request->get('case_id');
    	return $this->render('_bulkassign',[
			'caseId' => $caseId, 
		]);
    }
    
    /**
     * get Bulk Assign user details
     * @return mixed
     */
    public function actionGetAssignData() 
    {
    	$taskId = Yii::$app->request->post('checkboxVal');
    	$dropdownVal = Yii::$app->request->post('dropdownVal');
    	$caseId = Yii::$app->request->post('caseId');
    	$client_name = $case_name = '';
    	if($taskId==''){
    		if (Yii::$app->db->driverName == 'mysql'){
    		$query = "SELECT count(tu.servicetask_id) AS task_count, tu.servicetask_id, cc.client_id, tcc.client_name, cc.case_name,tst.service_task,
    			tst.sampling,group_concat(tu.task_id) as task_list FROM tbl_tasks_units AS tu
    			INNER JOIN tbl_task_instruct as ti ON ti.id = tu.task_instruct_id
				INNER JOIN tbl_servicetask AS tst ON tst.id=tu.servicetask_id
				INNER JOIN tbl_tasks AS ta ON ta.id = tu.task_id
				INNER JOIN tbl_client_case AS cc ON ta.client_case_id = cc.id
				INNER JOIN tbl_client as tcc ON tcc.id = cc.client_id
				LEFT JOIN tbl_priority_project ON ti.task_priority = tbl_priority_project.id
				LEFT JOIN tbl_priority_team ON ta.team_priority = tbl_priority_team.id
				WHERE tu.team_id =1 AND ta.task_cancel=0 AND ta.task_closed =0 AND tu.unit_assigned_to=0 AND ta.client_case_id=$caseId AND cc.is_close=0 AND tst.publish=1
				GROUP BY tu.servicetask_id, cc.client_id , tcc.client_name, cc.case_name,tst.service_task,tst.sampling ORDER BY tu.servicetask_id";
    		}else{
    			$query = "declare @StrFieldList as varchar(MAX)
						SELECT @StrFieldList = COALESCE(@StrFieldList+',' ,'') +  cast(t.task_id AS varchar)     
						FROM tbl_tasks_units AS t
						INNER JOIN tbl_servicetask AS tst ON tst.id=t.servicetask_id
						INNER JOIN tbl_tasks AS ta ON ta.id = t.task_id
						INNER JOIN tbl_task_instruct AS ti ON ti.id = t.task_instruct_id
						INNER JOIN tbl_client_case AS cc ON ta.client_case_id = cc.id
						INNER JOIN tbl_client as tcc ON tcc.id = cc.client_id       
						LEFT JOIN tbl_priority_project ON ti.task_priority = tbl_priority_project.id
						LEFT JOIN tbl_priority_team ON ta.team_priority = tbl_priority_team.id
						WHERE ti.isactive=1 AND t.team_id =1 AND ta.task_cancel=0 AND ta.task_closed =0 AND t.unit_assigned_to=0 AND ta.client_case_id=$caseId AND cc.is_close=0 AND tst.publish=1

						select  
						count( t.servicetask_id ) AS task_count, t.servicetask_id, cc.client_id, tcc.client_name, cc.case_name,tst.service_task,
						tst.sampling,@StrFieldList as task_list 
						FROM tbl_tasks_units AS t
						INNER JOIN tbl_servicetask AS tst ON tst.id=t.servicetask_id
						INNER JOIN tbl_tasks AS ta ON ta.id = t.task_id
						INNER JOIN tbl_task_instruct AS ti ON ti.id = t.task_instruct_id
						INNER JOIN tbl_client_case AS cc ON ta.client_case_id = cc.id
						INNER JOIN tbl_client as tcc ON tcc.id = cc.client_id
						LEFT JOIN tbl_priority_project ON ti.task_priority = tbl_priority_project.id
						LEFT JOIN tbl_priority_team ON ta.team_priority = tbl_priority_team.id
						WHERE ti.isactive=1 AND team_id =1 AND ta.task_cancel=0 AND ta.task_closed =0 AND t.unit_assigned_to=0 
						AND ta.client_case_id=$caseId AND cc.is_close=0 AND tst.publish=1
						GROUP BY t.servicetask_id, cc.client_id , tcc.client_name, cc.case_name,tst.service_task,tst.sampling 
						ORDER BY t.servicetask_id";
    		}
    		
    		$instruction_data = \Yii::$app->db->createCommand($query)->queryAll();
    	}

     	if ($dropdownVal == "2"){ 
     		$individualTaskArr = ''; 
     		foreach($instruction_data as $instruData) {
     			$individualTaskArr[$instruData['client_name']][$instruData['service_task']]['count'] = $instruData['task_count'];
     			$individualTaskArr[$instruData['client_name']][$instruData['service_task']]['servicetask_id'] = $instruData['servicetask_id'];
     			$individualTaskArr[$instruData['client_name']][$instruData['service_task']]['task_list'] = $instruData['task_list'];
     			$client_name = $instruData['client_name'];
     		}
     	} else if ($dropdownVal == "3") {			
     		$individualTaskArr = ''; 
     		foreach($instruction_data as $instruData) {
     			$individualTaskArr[$instruData['case_name']][$instruData['service_task']]['count'] = $instruData['task_count'];
     			$individualTaskArr[$instruData['case_name']][$instruData['service_task']]['servicetask_id'] = $instruData['servicetask_id'];
     			$individualTaskArr[$instruData['case_name']][$instruData['service_task']]['task_list'] = $instruData['task_list'];
     			$client_name = $instruData['client_name'];
     			$case_name = $instruData['case_name'];
     		}     		
        } else {
        	$individualTaskArr = '';
        	foreach($instruction_data as $instruData) {
        		$individualTaskArr[$instruData['service_task']]['count'] = $instruData['task_count'];
        		$individualTaskArr[$instruData['service_task']]['servicetask_id'] = $instruData['servicetask_id'];
        		$individualTaskArr[$instruData['service_task']]['task_list'] = $instruData['task_list'];
        		
        		$sampling = isset($instruData['sampling']) && $instruData['sampling'] != 0 ? $instruData['sampling']." %" : "";
        		$sampling_percent = "";
        		if($sampling != "" && $sampling != 0 && $dropdownVal == "4"){
        			/*$clientstasks = Tasks::find()->select(['tbl_tasks.id','tbl_client_case.case_name'])->joinWith(['clientCase' => function(\yii\db\ActiveQuery $query){
							//$query->select(['client_id']);
						}])->where('tbl_tasks.id IN ('.$instruData['task_list'].')')->all(); 					*/
					$my_query = "SELECT tbl_tasks.id, tbl_client_case.client_id FROM tbl_tasks LEFT JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id WHERE tbl_tasks.id IN ({$instruData['task_list']})";
					$clientstasks = \Yii::$app->db->createCommand($my_query)->queryAll();
        			//(array("condition"=>'id IN ('.$tasks['tasklist'].')','select'=>array('id','client_id')));
        			//echo '<pre>';print_r($my_list_result);die;
        			$cltaskAr = array();
        			foreach($clientstasks as $clientstask){
						//echo '<pre>';print_r($clientstask);die;
        				$cltaskAr[$clientstask[client_id]][] = $clientstask[id];
						$cltaskAr[$clientstask[client_id]] = array_unique($cltaskAr[$clientstask[client_id]]); 
        			}
        			
        			$sampletask = ($instruData['task_count'] *  $instruData['sampling'] ) / 100;
	        		$sampletask = @round(@str_replace(",","",@number_format($sampletask, 1)));
	        		$new_tasks_ar = array();
	        		foreach ($cltaskAr as $client => $tasks_list_ar){
	        			if($sampletask!=0){
	        				$ar_sampling_percent = array_rand($tasks_list_ar, $sampletask);
	        				if(is_array($ar_sampling_percent))
		        				$sampling_percent = $sampling_percent + count($ar_sampling_percent);
		        			else
		        				$sampling_percent += 1;
	        			}else{
	        				$sampling_percent += 1;
	        			}
	        		}
	        		
	        		$tasks = explode(",",$instruData['task_list']);
	        		$new_tasks_ar_key = array_rand($tasks, $sampling_percent);
	        		$new_tasks_ar_key = !is_array($new_tasks_ar_key)?(array)$new_tasks_ar_key:$new_tasks_ar_key;
	        		$tasksAr = array_intersect_key($tasks,$new_tasks_ar_key);
	        		$individualTaskArr[$instruData['service_task']]['task_list'] = implode(",",$tasksAr);
        		}
        		$individualTaskArr[$instruData['service_task']]['sampling'] = $instruData['sampling'];
        		$individualTaskArr[$instruData['service_task']]['sampling_task'] = $sampling_percent;
        	}
        }
       
        /** Filter **/
       	$sql = "SELECT DISTINCT tu.id, tu.usr_first_name, CONCAT(tu.usr_first_name,CONCAT(' ',tu.usr_lastname)) as fullname from tbl_project_security as ps 
				INNER JOIN tbl_user as tu ON tu.id = ps.user_id
				INNER JOIN tbl_role as tr ON tr.id = tu.role_id
				WHERE ps.client_case_id = $caseId AND tu.id != 0 and tu.role_id != 0 and ps.team_id=0 order by tu.usr_first_name";
       	
     	$assignUsers = \Yii::$app->db->createCommand($sql)->queryAll();
     	
     	//$assignUsers = User::find()->with(['role'])->where('tbl_user.id IN (' . implode(",", $user_ids) . ')')->all();
     	/** End Filter **/
     	//echo '<pre>';
     	//print_r($individualTaskArr);die;
     	
     	$model = new Servicetask();
     	return $this->renderAjax('bulkusertable', ['model' => $model,'assignUsersArr' => $assignUsers, 'caseId' => $caseId, "taskId" => $taskId, 'dropdownVal' => $dropdownVal, "TaskArr" => $individualTaskArr,'client_name'=>$client_name,'case_name'=>$case_name], false, true);
    }
    
    /**
     * Filter Saved Projects GridView with Ajax
     * */
    public function actionAjaxSavedFilter()
    {
    	$task_instruct_id=Yii::$app->request->get('task_instruct_id',0);
    	$task_id=Yii::$app->request->get('task_id',0);

    	$searchModel = new TaskInstructSearch();
    	$_REQUEST['TaskInstructSearch']['saved']=1;
    	$params = array_merge(Yii::$app->request->getQueryParams(), $_REQUEST);
    	$dataProvider = $searchModel->searchFilter($params,$task_id);
    	$out['results']=array();
    	foreach ($dataProvider as $key => $val) {
            $out['results'][] = ['id' => $key, 'text' => $val, 'label' => $val];
    	}
    	return json_encode($out);
    }
    
    
    /**
     * Filter Change Projects GridView with Ajax
     * */
    public function actionAjaxChangeFilter()
    {
    	$task_instruct_id=Yii::$app->request->get('task_instruct_id',0);
    	$task_id=Yii::$app->request->get('task_id',0);
    
    	$searchModel = new TaskInstructSearch();
    	$_REQUEST['TaskInstructSearch']['saved']=0;
    	 
    	$params = array_merge(Yii::$app->request->getQueryParams(), $_REQUEST);
    	$dataProvider = $searchModel->searchFilter($params,$task_id);
    	$out['results']=array();
    	foreach ($dataProvider as $key=>$val){
    		$out['results'][] = ['id' => $key, 'text' => $val,'label' => $val];
    	}
    	 
    	return json_encode($out);
    }
    
    /**
     * Saved Project  
     * @return mixed
     */
    public function actionLoadSavedProjects()
    {
    	$this->layout = "mycase";
    	$case_id = Yii::$app->request->get('case_id',0);
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0){
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$searchModel = new TaskInstructSearch();
       	$_REQUEST['TaskInstructSearch']['saved']=1;
		$_REQUEST['grid_id']='dynagrid-saved-projects';
       	$params = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);
        $dataProvider = $searchModel->search($params);
        /*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['id','created','created_by','modified','modified_by',],'tbl_task_instruct');
        $config = [];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-projects/ajax-saved-filter','case_id' => $case_id]),$config);
        /*IRT 67,68,86,87,258*/
        return $this->render('_loadSavedProjects', [ 
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
		    'case_id' => $case_id,
		    'filter_type'=>$filter_type,
		    'filterWidgetOption'=>$filterWidgetOption
    	]);
    }
    
    /**
     * Delete Save Project 
     * @return mixed
     */
    public function actionDeletesavedprojects(){
    	$task_list = Yii::$app->request->post('task_list');
    	$task_instruct_ids = $task_list;
    	if(is_array($task_list)){
    		$task_instruct_ids = implode(",",$task_list);
    	}
    	
    	$tbl_form_instruction_values = \Yii::$app->db->createCommand("DELETE FROM tbl_form_instruction_values WHERE task_instruct_id IN ($task_instruct_ids)")->execute();
    	
    	$tbl_task_instruct_evidence = \Yii::$app->db->createCommand("DELETE FROM tbl_task_instruct_evidence WHERE task_instruct_id IN ($task_instruct_ids)")->execute();
    	$tbl_task_instruct_servicetask_sla = \Yii::$app->db->createCommand("DELETE FROM tbl_task_instruct_servicetask_sla WHERE task_instruct_servicetask_id IN (SELECT id FROM tbl_task_instruct_servicetask WHERE task_instruct_id IN ($task_instruct_ids))")->execute();
    	$tbl_task_instruct_servicetask = \Yii::$app->db->createCommand("DELETE FROM tbl_task_instruct_servicetask WHERE task_instruct_id IN ($task_instruct_ids)")->execute();
    	$tbl_task_instruct = \Yii::$app->db->createCommand("DELETE FROM tbl_task_instruct WHERE id IN ($task_instruct_ids)")->execute();
    	
    	/*$taskinstruct = \Yii::$app->db->createCommand("DELETE t, ts,te,tf FROM tbl_task_instruct as t
    		LEFT JOIN tbl_task_instruct_servicetask as ts ON t.id = ts.task_instruct_id 
    		LEFT JOIN tbl_task_instruct_evidence as te ON t.id = ts.task_instruct_id 
    		LEFT JOIN tbl_form_instruction_values as tf ON t.id = ts.task_instruct_id 
    		WHERE t.id IN (".$task_instruct_ids.")")->execute();*/
    	die;
    }
    
    /**
     * Change project Display Grid
     * @return
     */
    public function actionChangeProject(){
    	$this->layout = 'mycase';
    	$case_id = Yii::$app->request->get('case_id',0);
    	$task_id = Yii::$app->request->get('task_id',0);
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0){
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    	if(!Tasks::find()->where('id='.$task_id.' AND client_case_id='.$case_id)->count()){
    		throw new NotFoundHttpException('The requested Project does not exist in selected Client / Case.');
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$caseInfo = $clientCase->one();
    	 /*IRT 67,68,86,87,258 code Starts */
        $filter_type = \app\models\User::getFilterType(['tbl_task_instruct.instruct_version','tbl_task_instruct.created_by','tbl_task_instruct.created'],['tbl_task_instruct']);
        $config = [];
        $config_widget_options = [];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-projects/ajax-change-filter']).'&case_id='.$case_id.'&task_id='.$task_id,$config,$config_widget_options);        
    	 /*IRT 67,68,86,87,258 code Ends */
        
    	$searchModel = new TaskInstructSearch();
    	//$_REQUEST['TaskInstructSearch']['saved']=0;
    	$_REQUEST['grid_id']='dynagrid-changed-projects-case';
    	$params = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);
    	$dataProvider = $searchModel->search($params, $task_id);
    	
    	return $this->render('_loadChangeProjects', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'case_id' => $case_id,
                    'caseInfo' => $caseInfo,
                    'task_id' => $task_id,
                    'filter_type'=>$filter_type,
                    'filterWidgetOption' => $filterWidgetOption
        ]);
    }
    
    /**
     * Assign user by bulkuser in mycase
     * @return mixed
     */
    public function actionAssignBulkUser()
    {
    	$data = Yii::$app->request->post('servicetasks'); // servicetasks[33][tasklist]
    	$data1 = Yii::$app->request->post('Servicetask');
    	$displayResult = Yii::$app->request->post('displayResult');
    	$caseId = Yii::$app->request->post('caseId');
    	$dropdownVal = Yii::$app->request->post('dropdownVal');
    	
    	$role_id = Yii::$app->user->identity->role_id;
    	$role_name = Role::findOne($role_id);
    	if(!empty($data)){
    		foreach ($data as $key => $value){
			    $data[$key] = array_merge((array)$data1['servicetasks'][$key], (array)$value);
			}
			foreach($data as $servicetask_id => $value){
				$servicetask_data = Servicetask::findOne($servicetask_id);
    			$task_ids = explode(",",$value['tasklist']);
    			if ($value['assigntouser'] > 0) {
    				$sql = "SELECT tu.*,t.client_case_id from tbl_tasks_units as tu 
    						INNER JOIN tbl_tasks as t ON t.id = tu.task_id
							INNER JOIN tbl_task_instruct_servicetask as ts ON ts.id=tu.task_instruct_servicetask_id
							WHERE tu.task_id IN(".$value['tasklist'].") AND ts.servicetask_id =".$servicetask_id;
					$taskunits = \Yii::$app->db->createCommand($sql)->queryAll();
					$userId = $value['assigntouser']; // Assign userId
					
					if(!empty($taskunits)){
     					foreach($taskunits as $taskuni){
     						Yii::$app->db->createCommand("UPDATE tbl_tasks_units SET tbl_tasks_units.unit_assigned_to=".$userId.", tbl_tasks_units.modified = '".date('Y-m-d H:i:s')."' WHERE tbl_tasks_units.id=".$taskuni['id'])->execute();
     						
     						$taskAssigned = new TasksUnitsTransactionLog;
     						//$taskAssigned->task_id = $taskuni['task_id'];
     						$taskAssigned->tasks_unit_id = $taskuni['id'];
     						$taskAssigned->user_assigned = $userId;
     						$taskAssigned->transaction_type = 5; 
     						$taskAssigned->current_time = date('Y-m-d H:i:s A');
     						$taskAssigned->current_time = time();
     						$taskAssigned->duration = "0 days 0 hours 0 min";
     						$taskAssigned->transaction_by = $userId;
     						$taskAssigned->transaction_date = date('Y-m-d H:i:s A');
     						$taskAssigned->created_by = $userId;
     						$taskAssigned->created = date('Y-m-d H:i:s A');
     						$taskAssigned->modified_by = $userId;
     						$taskAssigned->modified = date('Y-m-d H:i:s A');
     						$taskAssigned->save();
    						/*(new SettingsEmail)->sendEmail(12, 'is_sub_self_assign',
	     						$data=array(
	     							'case_id'=>$caseId,
	     							'project_id'=>$taskuni['task_id'],
	     							'task_unit_id'=>$taskuni['id']
	     						)
	     					);*/
     						$activityLog = new ActivityLog();
      						$activityLog->generateLog('Project','AssignedTask', $taskuni['id'], $taskuni['task_id']."|project#:".$taskuni['task_id']."|unit#:".$taskuni['id']);
     					}
     				}
     				
     				$query = "SELECT ttu.*,t.client_case_id FROM tbl_tasks_units_todos as ttu
     						INNER JOIN tbl_tasks_units AS tu ON ttu.tasks_unit_id=tu.id
     						INNER JOIN tbl_tasks as t ON t.id = tu.task_id
							INNER JOIN tbl_task_instruct_servicetask AS ts ON ts.id=tu.task_instruct_servicetask_id
							WHERE tu.task_id IN(".$value['tasklist'].") AND ts.servicetask_id=".$servicetask_id;
     				$taskunitstodos = \Yii::$app->db->createCommand($query)->queryAll();
     				
     				if(!empty($taskunitstodos)){
     					foreach($taskunitstodos as $taskuni){
     						Yii::$app->db->createCommand("UPDATE tbl_tasks_units_todos SET tbl_tasks_units.assigned=".$userId.", tbl_tasks_units.modified='".date('Y-m-d H:i:s')."' WHERE tbl_tasks_units.id=".$taskuni['id'])->execute();
     						
     						$TasksUnitsTodo = new TasksUnitsTodoTransactionLog;
     						$TasksUnitsTodo->task_id = $taskuni['task_id'];
     						$TasksUnitsTodo->tasks_unit_id = $taskuni['tasks_unit_id'];
     						$TasksUnitsTodo->user_assigned = $userId;
     						$TasksUnitsTodo->todo_id = $taskuni['id'];
     						$TasksUnitsTodo->transaction_type = 5; 
     						$TasksUnitsTodo->current_time = date('Y-m-d H:i:s A');
     						$TasksUnitsTodo->current_time = time();
     						$TasksUnitsTodo->duration = "0 days 0 hours 0 min";
     						$TasksUnitsTodo->transaction_by = $userId;
     						$TasksUnitsTodo->transaction_date = date('Y-m-d H:i:s A');
     						$TasksUnitsTodo->created_by = $userId;
     						$TasksUnitsTodo->created = date('Y-m-d H:i:s A');
     						$TasksUnitsTodo->modified_by = $userId;
     						$TasksUnitsTodo->modified = date('Y-m-d H:i:s A');
     						$TasksUnitsTodo->save();
     						
     						/*(new SettingsEmail)->sendEmail(16, 'is_todos_assign_to_me', 
	     						$data = array(
	     								'case_id' => $caseId, 
	     								'todo_id' => $taskuni['id'], 
	     								'service_id' => $servicetask_id, 
	     								'project_id' =>  $taskuni['task_id'],
	     								'task_unit_id' => $taskuni['tasks_unit_id']
	     							)
	     						);*/

     						$activityLog = new ActivityLog();
     						$activityLog->generateLog('ToDo','Assigned/Transitioned', $taskuni['id'], $taskuni['task_id']."|project#:".$taskuni['task_id']."|unit#:".$taskuni['tasks_unit_id']."|ToDo#:".$taskuni['id']);
     					}
     				}
     			}
     		}	
    	}	
    	echo "Done";
    }
}
