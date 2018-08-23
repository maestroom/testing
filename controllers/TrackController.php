<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\HtmlPurifier;
use yii\helpers\ArrayHelper;
use app\models\ProjectSecurity;
use app\models\Role;
use app\models\ClientCase;
use app\models\TaskInstruct;
use app\models\TaskInstructServicetask;
use app\models\Tasks;
use app\models\TasksTeams;
use app\models\TaskInstructNotes;
use app\models\TasksUnits;
use app\models\TasksUnitsTransactionLog;
use app\models\TasksUnitsTodos;
use app\models\TasksUnitsTodoTransactionLog;
use app\models\Todocats;
use app\models\TeamlocationMaster;
use app\models\ActivityLog;
use app\models\Servicetask;
use app\models\Mydocument;
use app\models\MydocumentsBlob;
use app\models\Pricing;
use app\models\Unit;
use app\models\SettingsEmail;
use app\models\FormBuilder;
use app\models\User;
use app\models\TasksUnitsData;
use app\models\TasksUnitsBilling;
use app\models\Options;
use app\models\InvoiceFinal;
use app\models\Team;
use app\models\EvidenceProductionBates;
use app\models\TaskInstructEvidence;
use app\models\EmailCron;
use yii\helpers\Html;
use yii\helpers\Url;

class TrackController extends Controller
{
    /**
     * Track Project Case and Team Wise
     * */

    public function beforeAction($action) {
		if (((!(new User)->checkAccess(4.03) && $action->id == 'index' && isset($_REQUEST['case_id']) && $_REQUEST['case_id'] != 0)) || (!(new User)->checkAccess(5.02) && $action->id == 'index' && isset($_REQUEST['team_id']) && $_REQUEST['team_id'] != 0 && $_REQUEST['team_loc'] != 0 && isset($_REQUEST['team_loc'])))/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');

		if(!Yii::$app->user->isGuest && isset($_REQUEST['case_id']) && $_REQUEST['case_id'] != '' && !is_numeric($_REQUEST['case_id'])){
			throw new \yii\web\HttpException(404, 'Invalid Parameters.');
		}

		if((!Yii::$app->user->isGuest && isset($_REQUEST['team_id']) && $_REQUEST['team_id'] != '' && !is_numeric($_REQUEST['team_id'])) || (isset($_REQUEST['team_loc']) && $_REQUEST['team_loc'] != '' && !is_numeric($_REQUEST['team_loc']))){
			throw new \yii\web\HttpException(404, 'Invalid Parameters.');
		}

		return parent::beforeAction($action);
	}

	public function actionIndex(){
            //die('sm');
		$session = Yii::$app->session;
		$refere = Yii::$app->request->referrer;
		$qstr = explode("r=",$refere);
		$qstr_inner =array();
		if(isset($qstr[1])){
			$qstr_inner = explode("&",$qstr[1]);
		}
		if(isset($qstr_inner[0]) && $qstr_inner[0]!='track/index'){
			$session->set('querystr', Yii::$app->request->referrer);
		}

		$querystr = $session->get('querystr');
    	$task_id  = Yii::$app->request->get('taskid',0);
		$case_id  = Yii::$app->request->get('case_id',0);
    	$team_id  = Yii::$app->request->get('team_id',0);
    	$team_loc = Yii::$app->request->get('team_loc',0);
    	$option	  = Yii::$app->request->get('option','All');
    	$type="team";
    	$this->layout = "myteam";
    	if($case_id!=0 && $team_id == 0){
    		$this->layout = "mycase";
    		$type="case";
    	}
    	//$task_model = Tasks::findOne($task_id);
		if(!isset($_SESSION[$task_id.'_task_info'])) {
			$_SESSION[$task_id.'_task_info'] = serialize(Tasks::findOne($task_id));
		}
		$task_model = unserialize($_SESSION[$task_id.'_task_info']);

    	$options=Yii::$app->request->get();
    	$project_track_data = (new TaskInstructServicetask)->getTrackProjectData($task_id,$case_id,$team_id,$options);
		$project_name_sql = "SELECT project_name FROM tbl_task_instruct WHERE task_id=$task_id AND isactive=1";
    	$project_name     = Yii::$app->db->createCommand($project_name_sql)->queryScalar();
    	 //echo "<pre>",print_r($project_track_data),"</pre>";die;
    	$post_data = Yii::$app->request->get();
    	$belongtocurr_team_serarr = array();
    	/* Start: get belong to services */
    	$sql="SELECT team_id FROM tbl_project_security WHERE user_id IN (" . Yii::$app->user->identity->id . ") "; // and team_loc=".$team_loc;

    	$role_types=explode(",", Yii::$app->user->identity->role->role_type);

    	if(isset($team_id) && $team_id > 0) {
    		$sql = "SELECT DISTINCT team_id FROM tbl_project_security WHERE user_id IN (" . Yii::$app->user->identity->id . ")";
    		$role_types=explode(",", Yii::$app->user->identity->role->role_type);
    		if(in_array(1,$role_types)) {
				//$bsql="	SELECT tbl_servicetask.id,tbl_servicetask.id as name FROM tbl_servicetask LEFT JOIN tbl_teamservice ON tbl_servicetask.teamservice_id = tbl_teamservice.id WHERE tbl_teamservice.teamid IN (".$sql.") OR tbl_teamservice.teamid=1";
				$bsql="SELECT tbl_tasks_units.servicetask_id,tbl_tasks_units.servicetask_id as name FROM tbl_tasks_units WHERE (tbl_tasks_units.team_id IN (".$sql.") OR tbl_tasks_units.team_id=1) AND tbl_tasks_units.task_id=$task_id";
    			$belongtocurr_team = Yii::$app->db->createCommand($bsql)->queryAll(\PDO::FETCH_KEY_PAIR);
				//$belongtocurr_team = ArrayHelper::map(Servicetask::find()->joinWith('teamservice')->where('tbl_teamservice.teamid IN ('.$sql.') OR tbl_teamservice.teamid=1')->select('tbl_servicetask.id')->all(),'id','id');
    		}else{
				//$bsql="	SELECT tbl_servicetask.id,tbl_servicetask.id as name FROM tbl_servicetask LEFT JOIN tbl_teamservice ON tbl_servicetask.teamservice_id = tbl_teamservice.id WHERE tbl_teamservice.teamid IN (".$sql.")";
				$bsql="SELECT tbl_tasks_units.servicetask_id,tbl_tasks_units.servicetask_id as name FROM tbl_tasks_units WHERE (tbl_tasks_units.team_id IN (".$sql.")) AND tbl_tasks_units.task_id=$task_id";
				$belongtocurr_team = Yii::$app->db->createCommand($bsql)->queryAll(\PDO::FETCH_KEY_PAIR);
				//$belongtocurr_team = ArrayHelper::map(Servicetask::find()->joinWith('teamservice')->where('tbl_teamservice.teamid IN ('.$sql.')')->select('tbl_servicetask.id')->all(),'id','id');
			}
    	}else{
			//$bsql="	SELECT tbl_servicetask.id,tbl_servicetask.id as name FROM tbl_servicetask LEFT JOIN tbl_teamservice ON tbl_servicetask.teamservice_id = tbl_teamservice.id WHERE tbl_teamservice.teamid IN (".$sql.") OR tbl_teamservice.teamid=1";
			$bsql="SELECT tbl_tasks_units.servicetask_id,tbl_tasks_units.servicetask_id as name FROM tbl_tasks_units WHERE (tbl_tasks_units.team_id IN (".$sql.") OR tbl_tasks_units.team_id=1) AND tbl_tasks_units.task_id=$task_id";
			$belongtocurr_team = Yii::$app->db->createCommand($bsql)->queryAll(\PDO::FETCH_KEY_PAIR);
			//$belongtocurr_team = ArrayHelper::map(Servicetask::find()->joinWith('teamservice')->where('tbl_teamservice.teamid IN ('.$sql.') OR tbl_teamservice.teamid=1')->select('tbl_servicetask.id')->all(),'id','id');
		}
    	if(Yii::$app->db->driverName == 'mysql') {
			$getTaskPercentage = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid($task_id);
		} else {
			$getTaskPercentage = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid($task_id);
		}
    	$perc_complete = 0;
		//\Yii::$app->db->createCommand("SELECT ".$getTaskPercentage."")->queryScalar();

    	/*End: get belong to services*/
    	return $this->render('index',['project_name'=>$project_name,'dataProvider'=>$project_track_data,'task_model'=>$task_model,'team_loc'=>$team_loc,'task_id'=>$task_id,'case_id'=>$case_id,'team_id'=>$team_id,'type'=>$type,'post_data'=>$post_data,'option'=>$option,'options'=>$options,'belongtocurr_team'=>$belongtocurr_team, 'querystr' => $querystr, 'perc_complete' => $perc_complete]);
    }
    /**
     * GET task instrucion, task unit, billing iteam listing to track project service task
     * */
    public function actionGetTaskTrackDetails()
    {
        
        //die('sm');
        $task_id = Yii::$app->request->get('task_id',0);
		$case_id = Yii::$app->request->get('case_id',0);
        $team_id = Yii::$app->request->get('team_id',0);
    	$type = Yii::$app->request->get('type',0);
    	$expandRowKey=Yii::$app->request->post('expandRowKey',0);
    	$team_loc = Yii::$app->request->post('team_loc',0);
    	$option	  = Yii::$app->request->post('option','All');
    	$options=Yii::$app->request->post();
    	$project_track_data = (new TaskInstructServicetask)->getTrackProjectData($task_id,$case_id,$team_id,$options);
        //echo "<pre>"; print_r($project_track_data);die;
    	//$project_track_data = (new TaskInstructServicetask)->getTrackProjectData($task_id,$case_id,$team_id);
    	$models = $project_track_data->getModels();
    	$servietask_id = $models[$expandRowKey]['servicetask_id'];
    	$taskunit_id   = $models[$expandRowKey]['taskunit_id'];
    	$sort_order    = $models[$expandRowKey]['sort_order'];
    	$teamId        = $models[$expandRowKey]['teamId'];
    	$team_loc      = $models[$expandRowKey]['team_loc'];
    	$team_name = Team::findOne($teamId)->team_name;
    	$processTrackData 	= (new TaskInstructServicetask)->processTrackData($servietask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$options,$type);

    	//echo "<pre>"; print_r($processTrackData);die;
    	$acces_team_arr = (new ProjectSecurity)->getUserTeamsArr(Yii::$app->user->identity->id);
    	$acces_team_loc_arr = (new ProjectSecurity)->getUserTeamsLocArr(Yii::$app->user->identity->id);

    	$roleId = Yii::$app->user->identity->role_id;
    	$roleInfo = Role::findOne($roleId);
    	$User_Role = explode(',', $roleInfo->role_type);
    	if($roleId=='0') { // if super user all access
    		$acces_team_arr[1] = 1;
    	} if(in_array(1,$User_Role)) {
    		$acces_team_arr[1] = 1;
    	}

        $task_instruct_data = ArrayHelper::map(TaskInstruct::find()->select(['id'])->orderBy('id DESC')->where(['task_id'=>$task_id])->limit(2)->all(),'id','id');
        if(count($task_instruct_data) < 2)
        {
            $cnt_instruction_evidence=array();
        }
        else {
            $cnt_instruction_evidence=TaskInstructEvidence::find()->select(['evidence_id'])->where('task_instruct_id IN('.implode(",",$task_instruct_data).')')->groupBy('evidence_id')->having('COUNT(evidence_id) != 2')->one();
        }

        //echo "<pre>"; print_r($task_instruct_data);die;
    	$belongtocurr_team_serarr = (new Servicetask)->getBelongto(Yii::$app->user->identity->id);
    	$stlocaccess = (new Servicetask)->getBelongtoLoc(Yii::$app->user->identity->id);
    	return $this->renderPartial('GetTaskTrackDetails',[
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'models'=>$models,
			'taskunit_id'=>$taskunit_id,
			'servietask_id'=>$servietask_id,
			'stlocaccess'=>$stlocaccess,
			'processTrackData'=>$processTrackData,
			'teamId'=>$teamId,
			'team_loc'=>$team_loc,
			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
			'team_name'=>$team_name,
			'roleId'=>$roleId,
            'cnt_instruction_evidence'=>count($cnt_instruction_evidence)
    	]);
    }

    public function actionGettaskpopupdata()
    {
		$task_id = Yii::$app->request->post('task_id',0);
    	$case_id = Yii::$app->request->post('case_id',0);
        $team_id = Yii::$app->request->post('team_id',0);
		$teamId = Yii::$app->request->post('teamId',0);
		$team_loc = Yii::$app->request->post('team_loc',0);
		$servicetask_id = Yii::$app->request->post('servicetask_id',0);
		$sort_order = Yii::$app->request->post('sort_order',0);
		$taskunit_id = Yii::$app->request->post('taskunit_id',0);

		$processTrackData = (new TaskInstructServicetask)->processTrackDatapopup($servicetask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,0);
		//echo "<pre>",print_r($processTrackData),"</pre>";die;
		return $this->renderPartial('instruction_task',[
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'taskunit_id'=>$taskunit_id,
			'servietask_id'=>$servicetask_id,
			'processTrackData'=>$processTrackData,
			'teamId'=>$teamId,
    		'team_loc'=>$team_loc,
		]);
	}
    /**
     * Add / Edit instruction notes to track project service task
     * */
    public function actionInstructionNotes($servicetask_id, $task_id)
    {
    	$model = new TaskInstructNotes();
    	$data = TaskInstructNotes::find()
        ->where('servicetask_id ='.$servicetask_id.' AND task_id ='.$task_id)->one();

    	if(isset($data->id) && $data->id > 0) {
    		$model = TaskInstructNotes::findOne($data->id);
    	} else {
    		$model->servicetask_id = $servicetask_id;
    		$model->task_id = $task_id;
    	}
    	if(Yii::$app->request->post()) {
    	//	echo "<pre>",print_r($_FILES); die();
    		$remove_attachments = Yii::$app->request->post('remove_attachments');
    		$model->load(Yii::$app->request->post());
    		$model->notes=HtmlPurifier::process($model->notes);
    		if($model->notes==""){
    			if(isset($data->id) && $data->id > 0){
    				if (!empty($data->instructionattachments)) {
    					foreach ($data->instructionattachments as $filename) {
    						(new Mydocument())->removeAttachments($filename->id);
    					}
    				}
    				$model->delete();
    				return 'OK';
    			}
    		}
    		if($model->save()) {
    			if(isset($data->id) && $data->id > 0){
    				$id = $data->id;
    			} else {
    				$id =Yii::$app->db->getLastInsertId();
    			}
    			/* Code for evidence attachment start */
    			if(!empty($_FILES['TaskInstructNotes']['name']['attachment'][0])) {
    				$docmodel = new Mydocument();
    				$doc_arr['p_id']=0;
    				$doc_arr['reference_id']=$id;
    				$doc_arr['team_loc']=0;
    				$doc_arr['origination']="Instruct N";
    				$doc_arr['type']='F';
    				$doc_arr['is_private']=0;
    				$docmodel->origination = "Instruct N";
    				$file_arr=$docmodel->Savemydocs('TaskInstructNotes','attachment',$doc_arr,$remove_attachments);
    			}
    			else {
    				if($remove_attachments!="") {
    					(new Mydocument())->removeAttachments($remove_attachments);
    				}
    			}
    			/* Code for evidence attachment end */
    			(new ActivityLog)->generateLog('Project Task Instruction Notes', 'Updated', $id, $model->notes);
    			return 'OK';
    		}else{
    			return $this->renderAjax('InstructionNotes',[
    				'model'=>$model,
    				'servicetask_id'=>$servicetask_id,
    				'task_id'=>$task_id
    			]);
    		}
    	}
    	$tasks_units_notes_length = (new User)->getTableFieldLimit('tbl_tasks_units_notes');
    	return $this->renderAjax('InstructionNotes',[
    			'model'=>$model,
    			'servicetask_id'=>$servicetask_id,
    			'task_id'=>$task_id,
    			'tasks_units_notes_length'=> $tasks_units_notes_length
    			]);
    }
    /**
     * Add Todo in track project section
     * */
    public function actionAddtodo($servicetask_id,$task_id,$team_loc,$taskunit_id)
    {
    	$model = new TasksUnitsTodos();
    	//$model->task_id = $task_id;
    	$model->tasks_unit_id = $taskunit_id;
    	$taskunit_data =TasksUnits::findOne($taskunit_id);
    	$todo_cat_list = ArrayHelper::map(Todocats::find()->select(['id',"concat(todo_cat,' - ',todo_desc) as cat_desc"])->orderBy('cat_desc ASC')->where('remove=0')->all(),'id','cat_desc');
    	if(Yii::$app->request->post())
    	{
    		$task_info = Tasks::findOne($task_id);
    		$remove_attachments = Yii::$app->request->post('remove_attachments');
    		$model->load(Yii::$app->request->post());
    		$model->todo=HtmlPurifier::process($model->todo);
    		$model->assigned=$taskunit_data->unit_assigned_to;
    		$duration = "0 days 0 hours 0 min";
    		if($model->save()){
    			$id =Yii::$app->db->getLastInsertId();
    			if($taskunit_data->unit_status == 4){ // unit is completed and added new todo so below code will reopne task and unit
    				TasksUnits::updateAll(['unit_status'=>1,'duration'=>date('Y-m-d H:i:s'),'modified'=> date('Y-m-d H:i:s'),'modified_by'=>Yii::$app->user->identity->id],'id='.$taskunit_id);
    				Tasks::updateAll(['task_status'=>1],'id='.$task_id);
    				// This code is written to Add a Activity log into the database table
    				(new ActivityLog)->generateLog('Project', 'StartedTask', $task_id,"project#:".$task_id."|unit#".$taskunit_id);
    				(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit_data->unit_assigned_to,1,$duration);
    			}

    			/* Code for attachment start */
    			if(!empty($_FILES['TasksUnitsTodos']['name']['attachment'][0])) {
    				$docmodel = new Mydocument();
    				$doc_arr['p_id']=0;
    				$doc_arr['reference_id']=$id;
    				$doc_arr['team_loc']=0;
    				$doc_arr['origination']="Todo";
    				$doc_arr['is_private']=0;
    				$doc_arr['type']='F';
    				$docmodel->origination = "Todo";
    				$file_arr=$docmodel->Savemydocs('TasksUnitsTodos','attachment',$doc_arr,$remove_attachments);
    			}

    			/* Code for attachment end */
    			(new ActivityLog)->generateLog('ToDo', 'Added', $id, $task_id);

    			/* Sending New ToDo Email  CHANGE */
    			//SettingsEmail::sendEmail(16, 'is_new_todo_post', $data = array('case_id' => $task_info->client_case_id, 'todo_id' => $id, 'service_id' => $servicetask_id, 'project_id' => $task_id, 'task_unit_id' => $unitid, 'todo' =>$model->todo));
				EmailCron::saveBackgroundEmail(16, 'is_new_todo_post', $data = array('case_id' => $task_info->client_case_id, 'todo_id' => $id, 'service_id' => $servicetask_id, 'project_id' => $task_id, 'task_unit_id' => $taskunit_id, 'todo' =>$model->todo));

    			/* Sending New ToDo Email */
    			if($taskunit_data->unit_assigned_to){ // todo is Assigned
    				(new TasksUnitsTodoTransactionLog)->generateLog($id,$task_id,$taskunit_id,$taskunit_data->unit_assigned_to,14,$duration);
					(new TasksUnitsTodoTransactionLog)->generateLog($id,$task_id,$taskunit_id,$taskunit_data->unit_assigned_to,7,$duration);
    				/* Sending ToDo Assigned To Me Email CHANGE */
    				//SettingsEmail::sendEmail(17, 'is_todos_assign_to_me', $data = array('case_id' => $task_info->client_case_id, 'todo_id' => $id, 'service_id' => $servicetask_id, 'project_id' => $task_id, 'task_unit_id' => $taskunit_id, 'todo' => $model->todo));
					EmailCron::saveBackgroundEmail(17, 'is_todos_assign_to_me', $data = array('case_id' => $task_info->client_case_id, 'todo_id' => $id, 'service_id' => $servicetask_id, 'project_id' => $task_id, 'task_unit_id' => $taskunit_id, 'todo' => $model->todo));
    				/* Sending ToDo Assigned To Me Email */
    			}else{
					(new TasksUnitsTodoTransactionLog)->generateLog($id,$task_id,$taskunit_id,$taskunit_data->unit_assigned_to,14,$duration);
				}

    			if($taskunit_data->unit_status==4) { // added todo on completed service,so we make service mode start
    				$taskunit_data->unit_status=1;
    				$taskunit_data->duration = date('Y-m-d H:i:s');
    				$taskunit_data->save(false);
    				$task_info->task_status=1;
    				$task_info->save(false);
    				$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    				(new ActivityLog)->generateLog('Project', 'StartedTask', $task_id, $activity_name);
    				$duration = "0 days 0 hours 0 min";
    				(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit_data->unit_assigned_to,1,$duration);
    			}
    			return 'OK';
    		}else{
				$tasks_units_todos_length = (new User)->getTableFieldLimit('tbl_tasks_units_todos');
				return $this->renderAjax('AddTodo',[
					'model'=>$model,
					'servicetask_id'=>$servicetask_id,
					'task_id'=>$task_id,
					'team_loc'=>$team_loc,
					'taskunit_id'=>$taskunit_id,
					'todo_cat_list'=>$todo_cat_list,
					'tasks_units_todos_length'=>$tasks_units_todos_length
    			]);
    		}
    	}
    	$tasks_units_todos_length = (new User)->getTableFieldLimit('tbl_tasks_units_todos');
		return $this->renderAjax('AddTodo',[
    			'model'=>$model,
    			'servicetask_id'=>$servicetask_id,
    			'task_id'=>$task_id,
    			'team_loc'=>$team_loc,
    			'taskunit_id'=>$taskunit_id,
				'todo_cat_list'=>$todo_cat_list,
    			'tasks_units_todos_length'=>$tasks_units_todos_length
			]);
    }
    /**
     * Edit Todo in track project section
     * */
    public function actionEditodo($servicetask_id,$task_id,$team_loc,$taskunit_id,$todo_id)
    {
    	$model = $this->findTodoModel($todo_id);
    	$todo_cat_list = ArrayHelper::map(Todocats::find()->select(['id',"concat(todo_cat,' - ',todo_desc) as cat_desc"])->orderBy('cat_desc ASC')->where('remove=0')->all(),'id','cat_desc');
    	if(Yii::$app->request->post()){
    		$remove_attachments = Yii::$app->request->post('remove_attachments');
    		$model->load(Yii::$app->request->post());
    		$model->todo=HtmlPurifier::process($model->todo);
    		if($model->save()){
    			/* Code for attachment start */
    			if(!empty($_FILES['TasksUnitsTodos']['name']['attachment'][0])){
    				$docmodel = new Mydocument();
    				$doc_arr['p_id']=0;
    				$doc_arr['reference_id']=$todo_id;
    				$doc_arr['team_loc']=0;
    				$doc_arr['origination']="Todo";
    				$doc_arr['is_private']=0;
    				$doc_arr['type']='F';

    				$docmodel->origination = "Todo";
    				$file_arr=$docmodel->Savemydocs('TasksUnitsTodos','attachment',$doc_arr,$remove_attachments);
    			}else{
    				if($remove_attachments!=""){
    					(new Mydocument())->removeAttachments($remove_attachments);
    				}
    			}
    			/* Code for attachment end */
    			(new ActivityLog)->generateLog('ToDo', 'Updated',$todo_id,$task_id);
    			return 'OK';
    		}else{
    			return $this->renderAjax('AddTodo',[
					'model'=>$model,
					'servicetask_id'=>$servicetask_id,
					'task_id'=>$task_id,
					'team_loc'=>$team_loc,
					'taskunit_id'=>$taskunit_id,
					'todo_cat_list'=>$todo_cat_list
    			]);
    		}
    	}
    	return $this->renderAjax('AddTodo',[
    			'model'=>$model,
    			'servicetask_id'=>$servicetask_id,
    			'task_id'=>$task_id,
    			'team_loc'=>$team_loc,
    			'taskunit_id'=>$taskunit_id,
    			'todo_cat_list'=>$todo_cat_list
    	]);
    }
    /**
     * Complete Todo Track Project Section
     * */
    public function actionCompletetodo($servicetask_id,$task_id,$team_loc,$taskunit_id,$todo_id,$team_id,$case_id){
    	$checkAccess = (new ProjectSecurity)->checkTeamAccess($team_id,$team_loc,$case_id);
    	if(!$checkAccess){
            $team_name = Team::findOne($team_id)->team_name;
            return 'This action is available only to '.$team_name.' Team Members.';
        }
    	$taskunit = TasksUnits::findOne($taskunit_id);
    	$task_info = Tasks::findOne($task_id);
    	if($taskunit->unit_status!=1){
    		return 'ToDo cannot be complete unless the task has Started.';
    	}
   		$model = $this->findTodoModel($todo_id);
    	if ($model->assigned == 0 || $model->assigned == "") {
    		return "The Todo cannot be Completed unless the ToDo has been Assigned.";
    	}
    	$model->complete=1;
    	$model->save(false);
    	(new ActivityLog)->generateLog('ToDo', 'Completed', $todo_id, $task_id);
    	$duration = "0 days 0 hours 0 min";
    	(new TasksUnitsTodoTransactionLog)->generateLog($todo_id,$task_id,$taskunit_id,$taskunit->unit_assigned_to,9,$duration);
    	/* Sending ToDo Completed Subscription Alert Email CHANGE */
    	//SettingsEmail::sendEmail
		EmailCron::saveBackgroundEmail(18, 'is_completed_todos', $data = array('case_id' => $task_info->client_case_id, 'todo_id' =>$todo_id, 'service_id' => $servicetask_id, 'project_id' => $task_id, 'task_unit_id' => $taskunit_id));
    	/* Sending ToDo Completed Subscription Alert Email */
    	return 'OK';
    }

    /**
     * ReOpen Todo Track Project Section
     * */
    public function actionReopentodo($servicetask_id,$task_id,$team_loc,$taskunit_id,$todo_id,$team_id,$case_id){

    	$checkAccess = (new ProjectSecurity)->checkTeamAccess($team_id,$team_loc,$case_id);
    	if(!$checkAccess){
            $team_name = Team::findOne($team_id)->team_name;
            return 'This action is available only to '.$team_name.' Team Members.';
        }
    	$model = $this->findTodoModel($todo_id);
//        echo '<pre>';print_r($model);die;
    	$model->complete=0;
    	$model->save(false);
//        echo $todo_id;die;
    	(new ActivityLog)->generateLog('ToDo', 'Started', $todo_id, $task_id);
    	$duration = "0 days 0 hours 0 min";
    	$taskunit = TasksUnits::findOne($taskunit_id);
    	(new TasksUnitsTodoTransactionLog)->generateLog($todo_id,$task_id,$taskunit_id,$taskunit->unit_assigned_to,13,$duration); // todo uncomplete
    	/* Start : If servicetask is in complete mode then make it in start mode */
    	if($taskunit->unit_status == 4){
    		TasksUnits::updateAll(['unit_status'=>1,'duration'=>date('Y-m-d H:i:s'),'modified'=> date('Y-m-d H:i:s'),'modified_by'=>Yii::$app->user->identity->id],'id='.$taskunit_id);
    		Tasks::updateAll(['task_status'=>1],'id='.$task_id);
    		(new TasksUnitsTodoTransactionLog)->generateLog($todo_id,$task_id,$taskunit_id,$taskunit->unit_assigned_to,13,$duration);
    		(new ActivityLog)->generateLog('Project', 'StartedTask', $task_id, $task_id."|project#:".$task_id."|unit#".$taskunit_id);
    	}
    	return 'OK';
    }
    /**
     * Delete Todo Track Project Section
     * */
    public function actionDeletetodo($todo_id,$team_id,$case_id,$team_loc,$task_id) {
    	$model = $this->findTodoModel($todo_id);
    	$checkAccess = (new ProjectSecurity)->checkTeamAccess($team_id,$team_loc,$case_id);

    	if(!$checkAccess){
            /*nelson code start*/
            $team_name = Team::findOne($team_id)->team_name;
            /*nelson code ends*/
            return 'This action is available only to '.$team_name.' Team Members.';
            //return 'You do not have permission to perform this action because you are not a member of the team';
    	}
    	//$todo=$model->todo;
    	$model->complete=0;
    	TasksUnitsTodoTransactionLog::deleteAll('todo_id='.$todo_id);
    	if($model->delete()){
    		/*Remove Attachments*/
    		$sql = "select id  FROM tbl_mydocument where origination='Todo' and reference_id=".$todo_id;
    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$sql.'))');
    		Mydocument::deleteAll("origination='Todo' and reference_id=".$todo_id);
    		/*Remove Attachments*/
    		$todo = $task_id;
    		(new ActivityLog)->generateLog('ToDo', 'Deleted', $todo_id, $todo);
    	}
    	return 'OK';
    }

    /*
     * Delete Additional Task Instructions
     * */
     public function actionDeletetaskinstruction(){
		 $servicetask_id = Yii::$app->request->get('servicetask_id',0);
		 $task_id = Yii::$app->request->get('task_id',0);
		 $instruction_notes_data = TaskInstructNotes::find()->where('tbl_tasks_units_notes.servicetask_id='.$servicetask_id.' AND tbl_tasks_units_notes.task_id = '.$task_id)->all();
		 /*Remove Attachments*/
		 $instruction_note_id="SELECT id FROM tbl_tasks_units_notes Where servicetask_id='".$servicetask_id."' and task_id=".$task_id;
		 $sql = "select id  FROM tbl_mydocument where origination='Instruct N' and reference_id IN ($instruction_note_id)";
		 MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$sql.'))');
		 Mydocument::deleteAll("origination='Instruct N' and reference_id IN ($instruction_note_id)");
		/*Remove Attachments*/
		$delete = TaskInstructNotes::deleteAll("servicetask_id='".$servicetask_id."' and task_id=".$task_id);
		 /* Use foreach beacuse if multiple instruction is stored than.*/
		 if($delete > 0){
			 foreach($instruction_notes_data as $instruct){
				$combine = $instruct['notes'].'|'.$instruct['task_id'];
				(new ActivityLog)->generateLog('Project Task Instruction Notes', 'Deleted', $instruct['id'], $combine);
			 }
		 }
		 return 'OK';
	 }

    /**
     * Assign Transit Todo Track Project Section
     * */
    public function actionAssignTransitTodo($servicetask_id,$task_id,$team_loc,$taskunit_id,$todo_id,$team_id,$case_id)
    {
    	$checkAccess = (new ProjectSecurity)->checkTeamAccess($team_id,$team_loc,$case_id);
    	if (((new User)->checkAccess(4.0611) && $case_id != 0) || ((new User)->checkAccess(5.0611) && $team_id != 0)) {
			$checkAccess=true;
		}
    	if(!$checkAccess){
    	  /* nelson code start */
            $team_name = Team::findOne($team_id)->team_name;
            /* nelson code ends */
            return 'This action is available only to '.$team_name.' Team Members.';
            // return 'You do not have permission to perform this action because you are not a member of the team';

    	}
    	$taskunit = TasksUnits::findOne($taskunit_id);
    	$task_info = Tasks::findOne($task_id);
    	$todo_data = $this->findTodoModel($todo_id);
    	if($taskunit->unit_status!=1){
    		$msg="Assigned";
    		if(isset($todo_data->assigned) && $todo_data->assigned!=0){
    			$msg="Transitioned";
    		}
    		return 'ToDo cannot be '.$msg.' unless the task has Started.';
    	}

    	if(Yii::$app->request->post()){
    		$type="assign";
    		//print_r($todo_data); exit;
    		if(isset($todo_data->assigned) && $todo_data->assigned!=0){
    			$type="transit";
    		}
    		$user_id = Yii::$app->request->post('user_id');
    		$todo_data->assigned = $user_id;
    		//$todo=$todo_data->todo;
    		$todo = $task_id;
    		$todo_data->save(false);
    		if($type=='transit'){
    			(new ActivityLog)->generateLog('ToDo', 'Transitioned', $todo_id, $todo);
    			$duration = "0 days 0 hours 0 min";
    			(new TasksUnitsTodoTransactionLog)->generateLog($todo_id,$task_id,$taskunit_id,$user_id,8,$duration);
    		}else{
    			(new ActivityLog)->generateLog('ToDo', 'Assigned', $todo_id, $todo);
    			$duration = "0 days 0 hours 0 min";
    			(new TasksUnitsTodoTransactionLog)->generateLog($todo_id,$task_id,$taskunit_id,$user_id,7,$duration);
    		}
    		/* Start : to send mail to send assign todo to me */
    		//SettingsEmail::sendEmail
			EmailCron::saveBackgroundEmail(16,'is_todos_assign_to_me',$data=array('case_id'=>$task_info->client_case_id,'todo_id'=>$todo_id,'service_id'=>$servicetask_id,'project_id'=>$task_id,'task_unit_id'=>$taskunit_id));
    		/* End : to send mail to send assign todo to me */
    		return 'OK';
    	}
    	$data = (new ProjectSecurity)->getUsersAssignTransit($servicetask_id,$task_id,$team_loc,$team_id);
    	return $this->renderAjax('AssignTransitTodo',[
			'servicetask_id'=>$servicetask_id,
			'task_id'=>$task_id,
			'team_loc'=>$team_loc,
			'taskunit_id'=>$taskunit_id,
			'todo_id'=>$todo_id,
			'team_id'=>$team_id,
			'todo_data'=>$todo_data,
			'data'=>$data
    	]);
    }

    public function actionChangetaskstatus($servicetask_id,$task_id,$taskunit_id,$status){
    	$taskunit = TasksUnits::findOne($taskunit_id);
    	$task_info = Tasks::findOne($task_id);
    	if(isset($status) && $status==1){//STARTED
    		if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    			return 'The selected Task can not be started unless it has been Assigned';
    		}else{
	    		$taskunit->unit_status=1;
	    		$taskunit->duration=date('Y-m-d H:i:s');
	    		$taskunit->save(false);
	    		$task_info->task_status=1;
	    		$task_info->save(false);
	    		$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
	    		(new ActivityLog)->generateLog('Project', 'StartedTask', $task_id, $activity_name);
	    		$duration = "0 days 0 hours 0 min";
	    		(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,1,$duration);
	    		return 'OK';
    		}
    	}
    	else if (isset($status) && $status==2) {// Task On Pause
    		if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    			return 'The selected Task cannot be  Paused unless the task has been Assigned.';
    		} else {
    			$taskunit->unit_status=2;
    			$taskunit->duration=date('Y-m-d H:i:s');
    			$taskunit->save(false);
    			$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    			(new ActivityLog)->generateLog('Project', 'PausedTask', $task_id, $activity_name);
    			$duration = "0 days 0 hours 0 min";
    			(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,2,$duration);
    			// (new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    			return 'OK';
    		}
    	}
    	else if (isset($status) && $status==3){ // Task On HOLD
    		if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0 && $taskunit->unit_status!=1){
    			return 'The selected Task cannot be Hold unless the task has been Assigned and Started';
    		}else{
    			$taskunit->unit_status=3;
    			$taskunit->duration=date('Y-m-d H:i:s');
    			$taskunit->save(false);
    			$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    			(new ActivityLog)->generateLog('Project', 'OnHoldTask', $task_id, $activity_name);
    			$duration = "0 days 0 hours 0 min";
    			(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,2,$duration);
    			(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    			return 'OK';
    		}
    	}
    	else if (isset($status) && $status==4){ // Completed
    		if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0 && $taskunit->unit_status==0){
    			return 'The selected Task cannot be Completed unless the task has been Assigned, On Hold, Started or Paused.';
    		}else{
    			$clientId = $task_info->clientCase->client_id;
    			$caseId   = $task_info->client_case_id;
    			$service_info = Servicetask::findOne($servicetask_id);
    			$team_id = $service_info->teamservice->teamid;
    			$team_loc = $taskunit->taskInstructServicetask->team_loc;

				if($service_info->billable_item == 2 && $service_info->force_entry == 1){
					$pricepointexist = (new Pricing)->chkPricePointExistByClientCaseTeam($clientId,$caseId,$team_id, $servicetask_id, $team_loc);
					if(!empty($pricepointexist)){
						$billabledata = TasksUnitsBilling::find()->joinWith('tasksUnits')->where('tbl_tasks_units.task_id = '.$task_id.' AND  tasks_unit_id = '.$taskunit_id)->count();
	    				if($billabledata == 0){
	    					return "The selected Tasks cannot be Completed unless one or more Billable Items are added to the Task";
	    				}
    				}
    			}

    			$todoCount = TasksUnitsTodos::find()->innerJoinWith('taskUnit')->where("tasks_unit_id = ".$taskunit_id." AND complete = 0 AND tbl_tasks_units.task_id = ".$task_id)->count();
    			if($todoCount){
    				return "In Order to Complete Service Task, Please Complete All Todo Items";
    			}
    			$taskunit->unit_status=4;
    			$taskunit->duration=date('Y-m-d H:i:s');
    			$taskunit->unit_complete_date=date('Y-m-d H:i:s');
    			$taskunit->save(false);
    			$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    			(new ActivityLog)->generateLog('Project', 'CompletedTask', $task_id, $activity_name);
    			$duration = "0 days 0 hours 0 min";
    			(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,4,$duration);
    			(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    			/* Mail Send on complete of Tasks */
    			//$taskInstructServicetask = $taskunit->taskInstructServicetask;
				$sort_order = $taskunit->sort_order;
				$previoustaskInstructServicetask = TaskInstructServicetask::find()->select('id')->where('tbl_task_instruct_servicetask.task_id = '.$task_id.'  AND tbl_task_instruct_servicetask.sort_order<='.($sort_order-1))->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) { $query->where('unit_status = 4'); }])->count();

				$nexttaskInstructServicetask = TaskInstructServicetask::find()->select(['tbl_task_instruct_servicetask.id','tbl_task_instruct_servicetask.servicetask_id'])->where('tbl_task_instruct_servicetask.task_id = '.$task_id)->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) {  $query->where("unit_assigned_to != 0 AND unit_assigned_to != '' AND unit_status = 0"); }])->orderBy('tbl_tasks_units.sort_order')->one();

				if(isset($previoustaskInstructServicetask) && $previoustaskInstructServicetask==($sort_order-1) && $nexttaskInstructServicetask != 0) {
                    $nextunit=TasksUnits::find()->where("unit_assigned_to != 0 AND unit_assigned_to != '' AND unit_status = 0 AND task_id = ".$task_id)->orderBy('sort_order')->one();
					$checkprev_is_complete=0;
					if(isset($nextunit->sort_order) && $nextunit->sort_order!="") {
						$checkprev_is_complete=TasksUnits::find()->where("task_id = ".$task_id." AND unit_status = 4 AND  sort_order=".($nextunit->sort_order-1) )->count();
					}
					if($checkprev_is_complete > 0) {
						//SettingsEmail::sendEmail
						EmailCron::saveBackgroundEmail(15,'pending_tasks',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'unit_arr'=>$nextunit->id,'unit_assigned_to'=>$nexttaskInstructServicetask->tasksUnits[0]->unit_assigned_to,'servicetask_id'=>$nextunit->taskInstructServicetask->servicetask_id));
					}
				}
				else if($sort_order == 0 && $nexttaskInstructServicetask != 0) {
                    $nextunit=TasksUnits::find()->where("unit_assigned_to != 0 AND unit_assigned_to != '' AND unit_status = 0 AND task_id = ".$task_id)->orderBy('sort_order')->one();
					$checkprev_is_complete=0;
					if(isset($nextunit->sort_order) && $nextunit->sort_order!="") {
						$checkprev_is_complete=TasksUnits::find()->where("task_id = ".$task_id." AND unit_status = 4 AND  sort_order=".($nextunit->sort_order-1) )->count();
					}
					if($checkprev_is_complete > 0) {
						//SettingsEmail::sendEmail
						EmailCron::saveBackgroundEmail(15,'pending_tasks',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'unit_arr'=>$nextunit->id,'unit_assigned_to'=>$nexttaskInstructServicetask->tasksUnits[0]->unit_assigned_to,'servicetask_id'=>$nextunit->taskInstructServicetask->servicetask_id));
					}
				}
    			//echo '<pre>';print_r($nexttaskInstructServicetask);die;
    			/* Check Team service completed or not And sent Service complete mail */
    			$query = TasksUnits::find()->where('task_instruct_servicetask_id IN ( SELECT id FROM tbl_task_instruct_servicetask WHERE teamservice_id ='.$service_info->teamservice_id .' AND task_id='.$task_id.' )');
    			$teambyservicetask_count =  $query->count();
    			$completedteambyservicetask_count =  TasksUnits::find()->where('unit_status=4 AND task_instruct_servicetask_id IN ( SELECT id FROM tbl_task_instruct_servicetask WHERE teamservice_id ='.$service_info->teamservice_id .' AND task_id='.$task_id.' )')->count();
    			$task_info = Tasks::findOne($task_id);
    			if($teambyservicetask_count == $completedteambyservicetask_count){
    				$settingsEmail = SettingsEmail::find()->select('email_teamservice')->where('id=20')->one();
    				if(isset($settingsEmail->email_teamservice) && $settingsEmail->email_teamservice != ""){
    					if(is_numeric($settingsEmail->email_teamservice) && $settingsEmail->email_teamservice == $service_info->teamservice_id){
    						//SettingsEmail::sendEmail
							EmailCron::saveBackgroundEmail(6, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'teamservice'=>$service_info->teamservice_id,'teamservice_id'=>$service_info->teamservice_id));
    					}else{
    						$services = explode(",",$settingsEmail->email_teamservice);
    						if(in_array($service_info->teamservice_id,$services)){
    							//SettingsEmail::sendEmail
								EmailCron::saveBackgroundEmail(6, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'teamservice'=>$service_info->teamservice_id,'teamservice_id'=>$service_info->teamservice_id));
    						}
    					}
    				}else{
    					//SettingsEmail::sendEmail
						EmailCron::saveBackgroundEmail(6, 'is_sub_com_service', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'teamservice'=>$service_info->teamservice_id,'teamservice_id'=>$service_info->teamservice_id));
    				}
    			}
    			$task_all = TasksUnits::find()->where('tbl_tasks_units.task_id = '.$task_id)->count();

				$task_complete = TasksUnits::find()->where('tbl_tasks_units.task_id = '.$task_id.' AND unit_status = 4')->count();
				if($task_all == $task_complete){
					//SettingsEmail::sendEmail
					EmailCron::saveBackgroundEmail(5, 'is_sub_com_task', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id));
					$delete_pastdue_sql="DELETE FROM tbl_project_pastdue WHERE task_id IN (".$task_id.")";
                    Yii::$app->db->createCommand($delete_pastdue_sql)->execute();
				}
				/* Check Team service completed or not And sent Service complete mail */
    			return 'OK';
    		}
    	}
    }
    public function actionUnassigntask($servicetask_id,$task_id,$team_loc,$taskunit_id)
    {
    	$taskunit = TasksUnits::findOne($taskunit_id);
    	$task_info = Tasks::findOne($task_id);
    	if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    		return 'The selected task is already UnAssigned';
    	}
    	if(isset($taskunit->unit_status) && $taskunit->unit_status==4){
    		return 'The selected task is Complete and cannot be UnAssigned';
    	}

    	/* Sending Task UnAssigned Subscription Alert Email */
    	//(new SettingsEmail)->sendEmail
		if(Options::find()->where('is_unassign=1 and user_id='.$taskunit->unit_assigned_to)->count() > 0){
			EmailCron::saveBackgroundEmail(14, 'is_unassign', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'task_unit_id' => $taskunit_id,'current_assigned'=>$taskunit->unit_assigned_to));
		}

    	$taskunit->is_transition = 0;
    	if($taskunit->unit_status != 0){
    		$taskunit->unit_status = 2;
    		$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    		(new ActivityLog)->generateLog('Project', 'PausedTask', $task_id, $activity_name);
    		$duration = "0 days 0 hours 0 min";
    		(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,2,$duration);
    		(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    	}
		$taskunit->unit_assigned_to = 0;
    	$taskunit->save(false);
    	$duration = "0 days 0 hours 0 min";$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    	(new ActivityLog)->generateLog('Project', 'UnAssigned', $task_id, $activity_name);
    	(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,0,12,$duration);
    	(new TasksUnitsTodos)->todoTransition($task_id,$taskunit_id);

    	/* Sending Task UnAssigned Subscription Alert Email */
    	return 'OK';
    }
    public function actionAssigntask(){
    	$idkeys = Yii::$app->request->get('ids',0);
    	$servicetask_id = Yii::$app->request->get('servicetask_id',0);
    	$taskunit_id = Yii::$app->request->get('taskunit_id',0);
		$taskunit_info = TasksUnits::findOne($taskunit_id);
    	$task_id= Yii::$app->request->get('task_id');
    	$case_id= Yii::$app->request->get('case_id',0);
    	$team_id= Yii::$app->request->get('team_id',0);
    	$team_loc= Yii::$app->request->get('team_loc',0);
    	$options = Yii::$app->request->get('params','');
    	$servicetask_ids=array();$teams=array();
    	$error="";
    	if(Yii::$app->request->post()) {
    		$user_id      = Yii::$app->request->post('user_id');
    		$service_ids  = explode(",",Yii::$app->request->post('service_ids'));
    		$taskunit_ids = explode(",",Yii::$app->request->post('taskunit_ids'));
    		$task_info = Tasks::findOne($task_id);
    		if(!empty($taskunit_ids)){
    			foreach ($taskunit_ids as $key=>$taskunit_id){
    				$servicetask_id=$service_ids[$key];
    				$taskunit = TasksUnits::findOne($taskunit_id);
    				//$taskInstructServicetask = $taskunit->taskInstructServicetask;
    				$sort_order = $taskunit->sort_order;
    				$previoustaskInstructServicetask = TaskInstructServicetask::find()->select('id')->where('tbl_task_instruct_servicetask.task_id = '.$task_id.'  AND tbl_task_instruct_servicetask.sort_order<='.($sort_order-1))->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) { $query->where('unit_status = 4'); }])->count();

    				$duration = "0 days 0 hours 0 min";
    				$activity_name = $task_id."|project#:".$task_id."|unit#:".$taskunit_id;
    				if($taskunit->unit_assigned_to == 0){ // new Assign
    					$taskunit->unit_assigned_to = $user_id;
    					$taskunit->is_transition=0;
    					$taskunit->save(false);
    					(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,5,$duration);
    					(new ActivityLog)->generateLog('Project', 'AssignedTask', $taskunit_id, $activity_name);
    					$list_notcompeltedtodos=TasksUnitsTodos::find()->where('tasks_unit_id=' . $taskunit_id . ' AND complete=0')->select('id')->all();
    					if(!empty($list_notcompeltedtodos)){
    						foreach ($list_notcompeltedtodos as $todo){
    							$model = TasksUnitsTodos::findOne($todo->id);
    							// $todos = $model->todo;
    							$todos = $task_id;
    							$model->assigned = $user_id;
    							$model->save(false);
    							(new ActivityLog)->generateLog('ToDo', 'Assigned', $todo->id, $todos);
    							$duration = "0 days 0 hours 0 min";
    							(new TasksUnitsTodoTransactionLog)->generateLog($todo->id,$task_id,$taskunit_id,$user_id,7,$duration);
    						}
    					}
    					$sql="";
    					if(isset($previoustaskInstructServicetask) && $previoustaskInstructServicetask==($sort_order-1)){
    					//$sql=" select user_id from tbl_options where tbl_options.pending_tasks = 1";
    					//$email_ids = ArrayHelper::map(User::find()->select(["usr_email"])->where('id IN ('.$sql.')')->all(),'usr_email','usr_email');
    					//$email_ids[Yii::$app->user->identity->usr_email]=Yii::$app->user->identity->usr_email;
    					/* Sending Pending Tasks Alert Email CHANGE*/
    					//SettingsEmail::sendEmail
						EmailCron::saveBackgroundEmail(15,'pending_tasks',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'unit_arr'=>$taskunit_id,'unit_assigned_to'=>$user_id,'servicetask_id'=>$servicetask_id));
    					/* Sending Pending Tasks Alert Email*/
    					}
    					else if($sort_order == 0){
							$prevoius_taskunit = TasksUnits::findOne($taskunit_id);
							/* Sending Pending Tasks Alert Email CHANGE*/
							//SettingsEmail::sendEmail
							EmailCron::saveBackgroundEmail(15,'pending_tasks',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'unit_arr'=>$taskunit_id,'unit_assigned_to'=>$user_id,'servicetask_id'=>$servicetask_id));
							/* Sending Pending Tasks Alert Email*/
						}
    				}else{ // Transit already assign user
    					$taskunit->is_transition=1;
    					$taskunit->unit_assigned_to = $user_id;

    					if($taskunit->unit_status != 0){
    						$taskunit->unit_status=2;
    						$taskunit->duration=date('Y-m-d H:i:s');
    						$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    						(new ActivityLog)->generateLog('Project', 'PausedTask', $task_id, $activity_name);
    						(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,2,$duration);
    						(new Tasks)->setProjectTasksStatus($task_id,$taskunit_id);
    					}
    					$taskunit->save(false);
    					(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,6,$duration);
    					(new ActivityLog)->generateLog('Project', 'TransitionedTask', $taskunit_id, $activity_name);
    				}
    				/*Sending Tasks Assigned To Me Email CHANGE*/
    				//SettingsEmail::sendEmail
					EmailCron::saveBackgroundEmail(13,'is_sub_self_assign',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'task_unit_id'=>$taskunit_id));
    				/*Sending Tasks Assigned To Me Email*/

    			}
    		}
    		return 'OK';
    	}
    	if($servicetask_id!=0){
    		$servicetask_ids['servicetask_id'][$servicetask_id]=$servicetask_id;
    		$servicetask_ids['taskunit_id'][$taskunit_id]=$taskunit_id;
    		$teams[$team_id]=$team_id;
    	}else{
			$project_track_data = (new TaskInstructServicetask)->getTrackProjectData($task_id,$case_id,$team_id,$options);
    		$models = $project_track_data->getModels();
	    	foreach ($idkeys as $id){
	    		$team_id = $models[$id]['teamId'];
	    		$servicetask_ids['servicetask_id'][$models[$id]['servicetask_id']]=$models[$id]['servicetask_id'];
	    		$servicetask_ids['taskunit_id'][$models[$id]['servicetask_id']]=$models[$id]['taskunit_id'];
	    		$teams[$models[$id]['teamId']]=$models[$id]['teamId'];
	    		$checkAccess = (new ProjectSecurity)->checkTeamAccess($models[$id]['teamId'],$models[$id]['team_loc']);
	    		if(!$checkAccess){
	    			 /*nelson code start*/
                     $team_name = Team::findOne($models[$id]['teamId'])->team_name;
                     /*nelson code ends*/
                     $error = 'This action is available only to '.$team_name.' Team Members.';
                            //  return 'You do not have permission to perform this action because you are not a member of the team';
	    			echo json_encode(array('error'=>$error,'data'=>''));exit;
	    		}
	    	}
    	}
    	if(count($teams) > 1){
    		$error= 'The selected Tasks cannot be Assigned or Transitioned because the selected Tasks are from different Teams.';
    		echo json_encode(array('error'=>$error,'data'=>''));exit;
    	}
    	$data = (new ProjectSecurity)->getUsersAssignTransit($servicetask_ids,$task_id,$team_loc,$team_id);
//    	echo '<pre>',print_r($data);die;
    	$mydata =  $this->renderAjax('AssignTransitTask',[
    			'data'=>$data,
    			'servicetask_ids'=>$servicetask_ids,
    			'services'=>implode(",",$servicetask_ids['servicetask_id']),
    			'taskunit_id'=>implode(",",$servicetask_ids['taskunit_id']),
    			'taskunit_info'=>$taskunit_info
    	]);
    	echo json_encode(array('error'=>$error,'data'=>$mydata));exit;
    	die;
    }
    /* Transfer Task Location Track Project Section */
    public function actionTransfertask(){
    	$servicetask_id = Yii::$app->request->get('servicetask_id',0);
    	$taskunit_id = Yii::$app->request->get('taskunit_id',0);
    	$task_id= Yii::$app->request->get('task_id',0);
    	$case_id= Yii::$app->request->get('case_id',0);
    	$team_id= Yii::$app->request->get('team_id',0);
    	$team_loc= Yii::$app->request->get('team_loc',0);
    	$mydata=array();
    	$sql="SELECT team_loc FROM tbl_servicetask_team_locs WHERE servicetask_id =$servicetask_id AND team_loc NOT IN ( $team_loc ) ";
    	$teamLocation=ArrayHelper::map(TeamlocationMaster::find()->orderBy('team_location_name ASC')->where('remove=0 and id IN ('.$sql.')')->all(), 'id','team_location_name');
    	if(Yii::$app->request->post()){
    		$duration = "0 days 0 hours 0 min";
    		$location= Yii::$app->request->post('loc',0);
    		$taskunit = TasksUnits::findOne($taskunit_id);
    		$taskunit->unit_assigned_to = 0;
    		$taskunit->unit_status = 2;
    		$taskunit->save(false);
    		$taskInstructServicetask = $taskunit->taskInstructServicetask;
    		$previous_loc = $taskunit->team_loc;
    		$teamservice_id = $taskunit->teamservice_id;
    		$instruction_team_id = $taskunit->team_id;
    		TasksTeams::updateAll(['team_loc'=>$location],'task_id = '.$task_id.' AND team_id = '.$instruction_team_id);
    		$taskInstructServicetask->team_loc = $location;
    		$taskunit->team_loc = $location;
    		$taskunit->save(false);
    		$taskInstructServicetask->save(false);
    		$task_info = Tasks::findOne($task_id);
    		if($task_info->task_status == 4){
    			$task_info->task_status = 1;
    			$task_info->save(false);
    		}
    		$activity_name=$task_id."|project#:".$task_id."|unit#".$taskunit_id;
    		(new ActivityLog)->generateLog('Project', 'Transferred', $task_id, $activity_name);
    		(new TasksUnitsTransactionLog)->generateLog($task_id,$taskunit_id,$taskunit->unit_assigned_to,10,$duration);
    		(new TasksUnitsTodos)->todoTransition($task_id,$taskunit_id);

    		/* Sending Transfer Task Location  Email */
    		//SettingsEmail::sendEmail
			EmailCron::saveBackgroundEmail(22, 'is_servicetask_transists', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id, 'previous_tl' => $previous_loc, 'tl' => $location, 'servicetask_id' => $servicetask_id,'task_unit_id'=>$taskunit_id));
    		/* Sending Transfer Task Location  Email */

    		return 'OK';
    	}
    	if(empty($teamLocation)){
    		return json_encode(array('error'=>'The selected Task cannot be Transferred to another Location','data'=>$mydata));
    	}
    	else{
    		$mydata =  $this->renderAjax('TransferTaskLoc',[
    				'teamLocation'=>$teamLocation,
    				'servicetask_id'=>$servicetask_id,
    				'taskunit_id'=>$taskunit_id,
    		]);
    	}
    	echo json_encode(array('error'=>'','data'=>$mydata));exit;
    }

    public function actionChangeMultipleTaskStatus()
    {
		$idkeys = Yii::$app->request->get('ids',0);
    	$servicetask_id = Yii::$app->request->get('servicetask_id',0);
    	$taskunit_id = Yii::$app->request->get('taskunit_id',0);
    	$task_id= Yii::$app->request->get('task_id');
    	$case_id= Yii::$app->request->get('case_id',0);
    	$team_id= Yii::$app->request->get('team_id',0);
    	$team_loc= Yii::$app->request->get('team_loc',0);
    	$options = Yii::$app->request->get('params','');
    	$project_track_data = (new TaskInstructServicetask)->getTrackProjectData($task_id,$case_id,$team_id,$options);
    	$models = $project_track_data->getModels();
    	$error="";
    	if(Yii::$app->request->post()){
    		$status    = Yii::$app->request->post('status',null);
    		$services  = Yii::$app->request->post('services',0);
    		$taskunits = Yii::$app->request->post('taskunits',0);
    		$taskunit_ids=explode(",",$taskunits);
    		foreach (explode(",",$services) as $key=>$servicetaskid){
    			$servicetask_data['servicetask_id'][$servicetaskid]=$servicetaskid;
    			$servicetask_data['taskunit_id'][$servicetaskid]=$taskunit_ids[$key];
    		}
    		return (new TasksUnits)->changeTaskStatus($servicetask_data,$task_id,$status);
    	}
    	if($servicetask_id!=0){
    		$servicetask_ids['servicetask_id'][$servicetask_id]=$servicetask_id;
    		$servicetask_ids['taskunit_id'][$taskunit_id]=$taskunit_id;
    		$teams[$team_id]=$team_id;
    	}else{
    		foreach ($idkeys as $id){
    			$team_id = $models[$id]['teamId'];
    			$servicetask_ids['servicetask_id'][$models[$id]['servicetask_id']]=$models[$id]['servicetask_id'];
    			$servicetask_ids['taskunit_id'][$models[$id]['servicetask_id']]=$models[$id]['taskunit_id'];
    			$teams[$models[$id]['teamId']]=$models[$id]['teamId'];
    			//$teams[$models[$id]['teamId']]=$models[$id]['teamId'];

    			$checkAccess = (new ProjectSecurity)->checkTeamAccess($models[$id]['teamId'],$models[$id]['team_loc']);
    			if(!$checkAccess){
					/*nelson code start*/
					$team_name = Team::findOne($models[$id]['teamId'])->team_name;
					/*nelson code ends*/
					$error = 'This action is available only to '.$team_name.' Team Members.';
					//return 'You do not have permission to perform this action because you are not a member of the team';
					echo json_encode(array('error'=>$error,'data'=>'')); exit;
    			}
    		}
    	}
    	$mydata =  $this->renderAjax('ChangeStatus',[
			'servicetask_ids'=>$servicetask_ids,
			'services'=>implode(",",$servicetask_ids['servicetask_id']),
			'taskunit_id'=>implode(",",$servicetask_ids['taskunit_id']),
    	]);
    	echo json_encode(array('error'=>$error,'data'=>$mydata));exit;
    }

    /**
     * Add Billing Item Service task Track Project Section
     **/
    public function actionAddbillableitems($task_id,$case_id,$teamId,$team_loc,$servicetask_id,$taskunit_id)
    {
    	$task_info = Tasks::findOne($task_id);
    	$taskunit = TasksUnits::findOne($taskunit_id);
    	$ordernum = $taskunit->sort_order;
    	$mydata=array();
    	$error="";
    	$clientId = $task_info->clientCase->client_id;
    	$caseId   = $task_info->client_case_id;
    	$service_info =Servicetask::findOne($servicetask_id);
    	$billingDataFrom=array();

    	if($service_info->data_publish == 1)
    	{
    		$formbuilder_data = new FormBuilder();
    		$billingDataFrom = $formbuilder_data->getFromData($servicetask_id,2,'DESC','formbuilder',0,'system');
    	}
    	//echo "<pre>",print_r(Yii::$app->request->get()),"</pre>"; die;
	if(Yii::$app->request->post()) {
			
			$remove_attachments = Yii::$app->request->post('remove_attachments');
			$post_data = Yii::$app->request->post();
			$media_ids= Yii::$app->request->post('evid_num_id');
			$syncfields = array(
				1 => 'prod_bbates',
				2 => 'prod_ebates',
				3 => 'prod_vol'
			);
    		$taskInstructServicetask = $taskunit->taskInstructServicetask;
    		if(isset($media_ids) && $media_ids!="") {
    			/** IRT 159 Add Attachment Functionality **/
    			/* Code for evidence attachment start */
    			$id =Yii::$app->db->getLastInsertId();
    			if(!empty($_FILES['TasksUnitsBilling']['name']['attachment'])) {
                            $docmodel = new Mydocument();
                            $doc_arr['p_id']=0;
                            $doc_arr['reference_id']=$taskunit_id;
                            $doc_arr['team_loc']=0;
                            $doc_arr['origination']="Data Statistics";
                            $doc_arr['type']='F';
                            $doc_arr['is_private']=0;
                            $docmodel->origination = "Data Statistics";
                            $file_arr=$docmodel->Savemydocs('TasksUnitsBilling', 'attachment', $doc_arr, $remove_attachments);
    			} else {
                            if($remove_attachments!="") {
                                (new Mydocument())->removeAttachments($remove_attachments);
                            }
    			}
    			/* End IRT 159 Attachment Functionality */

    			/* Code for evidence attachment end */
				foreach (explode(",",$media_ids) as $media_id) {
	    			if(!empty($post_data['priceVal'][$media_id])) {
						foreach($post_data['priceVal'][$media_id] as $key => $pricevalue) {
							if(isset($pricevalue['quantity']) && $pricevalue['quantity'] != "" && $pricevalue['quantity'] != 0){
								$desc = ""; $invoiced = '';
			    				if(isset($pricevalue['desc'])) $desc = $pricevalue['desc'];
			    				$nonbillableitem = isset($pricevalue['bill_todo_items']) ? 1 : "";
			    				if($nonbillableitem == 1) {
                                	$invoiced = 2;
			    				}
			    				$model_billable = new TasksUnitsBilling();
			    				// $model_billable->task_id        	= $task_id;
			    				$model_billable->tasks_unit_id  	= $taskunit_id;
			    				$model_billable->evid_num_id    	= $pricevalue['evid_num_id'];
			    				$model_billable->pricing_id     	= $pricevalue['princing_id'];
			    				$model_billable->quantity       	= round($pricevalue['quantity'],2);
			    				$model_billable->billing_desc     	= $desc;
			    				$model_billable->invoiced       	= $invoiced;
			    				$model_billable->internal_ref_no_id	= $task_info->clientCase->internal_ref_no;
			    				$bill_date=$pricevalue['bill_date'];
								$res = explode("/", $bill_date);
								$changedDate = $res[2]."-".$res[0]."-".$res[1];
								/*IRT - 817*/
								$time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ'],  "HIS");
								$bill_datetime = (new Options)->ConvertOneTzToAnotherTz($changedDate." ".$time, $_SESSION['usrTZ'],'UTC', "YMDHIS");
			    				$model_billable->created = $bill_datetime;
								/*IRT - 817*/
			    				$model_billable->save(false);

			    				/* Check alert approch Case Budget */
			    				if($invoiced !=2) {
			    					$is_overbudget  = false;
			    					$caseId         = $task_info->client_case_id;
			    					$invoiced       = (new InvoiceFinal)->pendingBillInvoiceByCase($caseId,"invoice");
			    					$pending        = (new InvoiceFinal)->pendingBillInvoiceByCase($caseId);
			    					$total          = ($invoiced+$pending);
                                    //echo $pending;die;
			    					if(isset($task_info->clientCase->budget_value) && $task_info->clientCase->budget_value > 0) {
			    						if($total  >=  $task_info->clientCase->budget_value) {
											/*CHANGE*/
			    							//SettingsEmail::sendEmail
											EmailCron::saveBackgroundEmail(21,'reached_case_budget_spend',$data=array('case_id'=>$caseId,'project_id'=>$task_id));
			    							$is_overbudget=true;
										}
			    					}
			    					if(!$is_overbudget) {
			    						if(isset($task_info->clientCase->budget_alert) && $task_info->clientCase->budget_alert > 0 ) {
			    							if($total > $task_info->clientCase->budget_alert) {
												/*Send approch Case Budget Email Alert*/
			    								/*Sending Approaching Case Budget Spend Alert Email CHANGE*/
			    								//SettingsEmail::sendEmail
												EmailCron::saveBackgroundEmail(20,'approaching_case_budget_spend',$data=array('case_id'=>$caseId,'project_id'=>$task_id));
			    							}
			    						}
			    					}
			    				}
			    			}
			    		}
			    	}

			    	if(!empty($post_data[$media_id])){
	    				//echo "<pre>",print_r($post_data),"</pre>";
	    				foreach ($post_data[$media_id] as  $formElement => $formData){
	    					if(isset($post_data['priceVal'][$media_id]['epbid']) && $post_data['priceVal'][$media_id]['epbid'] != "" && $post_data['priceVal'][$media_id]['epbid'] != 0){
	    						$sync_field = $post_data['properties'][$formElement]['sync_prod'];
	    						$sync_field_val = $formData['value'];
	    						$epbid = $post_data['priceVal'][$media_id]['epbid'];
	    						// echo "<pre>IN0 - $epbid > ",$sync_field, " == ",$syncfields[$sync_field]," = ",$sync_field_val,"<br/>";
	    						if(isset($syncfields[$sync_field]) && $syncfields[$sync_field]!="" && $sync_field_val!='') {
	    							// echo "<pre>IN1 $media_id - $epbid",$syncfields[$sync_field]," = ",$sync_field_val,"<br/>";
									$prod_id_sql="SELECT prod_id FROM tbl_task_instruct_evidence INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id where tbl_task_instruct.isactive=1 and tbl_task_instruct.task_id=".$task_id;
	    							EvidenceProductionBates::updateAll([$syncfields[$sync_field]=>$sync_field_val,'prod_date_loaded'=>date('Y-m-d'),'modified'=> date('Y-m-d H:i:s'),'modified_by'=>Yii::$app->user->identity->id],"id = $epbid AND prod_media_id IN (SELECT id FROM tbl_evidence_production_media WHERE evid_id  = $media_id AND prod_id IN ($prod_id_sql))");
	    						}
	    					}
	    					$form_builder_id=$billingDataFrom[$formElement]['form_builder_id'];
	    					$orgvalue=array();
	    					if($billingDataFrom[$formElement]['type'] == 'dropdown' || $billingDataFrom[$formElement]['type'] == 'radio' || $billingDataFrom[$formElement]['type'] == 'checkbox') {
    							if(!empty($billingDataFrom[$formElement]['values'])) {
    								$values = explode(";",$billingDataFrom[$formElement]['values']);
    								/*$values_option_ids =explode(";",$billingDataFrom[$formElement]['values_option_ids']);
	    								if($billingDataFrom[$formElement]['type'] == 'dropdown'){
	    									$values = array_combine(range(1, count($values)), $values);
	    									$values_option_ids = array_combine(range(1, count($values_option_ids)), $values_option_ids);
	    								}
	    							*/
    								if (is_array($formData['value'])) {
	    								foreach ($formData['value'] as $index) {
	    									$orgvalue[$index] = $index;
	    								}
    								} else {
    									$orgvalue[$formData['value']] = $formData['value'];
    								}
    							}
							}
							if(is_array($orgvalue) && !empty($orgvalue)){
	    						foreach ($orgvalue as $option_ids){
	    							if(isset($option_ids) && $option_ids!=0 && $option_ids!=""){
										$eleselval=$element_value_origin="";
                                        if(is_numeric($option_ids) && ($billingDataFrom[$formElement]['type'] == 'dropdown' || $billingDataFrom[$formElement]['type'] == 'radio' || $billingDataFrom[$formElement]['type'] == 'checkbox')) {
											$eleselval=(new FormBuilder())->getSelectedElementOption($option_ids);
										}
										if(isset($eleselval) && $eleselval!=""){
											$element_value_origin=$eleselval;
										}else{
											$element_value_origin=$option_ids;
										}

		    							$taskUnitsData = new TasksUnitsData();
		    							//$taskUnitsData->task_id            	 		= $task_id;
		    							$taskUnitsData->tasks_unit_id       		= $taskunit_id;
		    							//$taskUnitsData->task_instruct_servicetask_id= $taskInstructServicetask->id;
		    							$taskUnitsData->form_builder_id				= $form_builder_id;
		    							$taskUnitsData->evid_num_id         		= $media_id;
		    							$taskUnitsData->element_value             	= $option_ids;
										$taskUnitsData->element_value_origin        = $element_value_origin;
		    							if(isset($post_data['properties'][$formElement]['unit_id'][$media_id]) && $post_data['properties'][$formElement]['unit_id'][$media_id] != ""){
		    								$taskUnitsData->element_unit    = $post_data['properties'][$formElement]['unit_id'][$media_id];
		    							}
		    							$taskUnitsData->save(false);
	    							}
	    						}
	    					}else{
	    						if(isset($formData['value']) && $formData['value']!=""){
									$eleselval=$element_value_origin="";
                                    if(is_numeric($formData['value']) && ($billingDataFrom[$formElement]['type'] == 'dropdown' || $billingDataFrom[$formElement]['type'] == 'radio' || $billingDataFrom[$formElement]['type'] == 'checkbox')) {
										$eleselval=(new FormBuilder())->getSelectedElementOption($formData['value']);
									}
									if(isset($eleselval) && $eleselval!=""){
										$element_value_origin=$eleselval;
									}else{
										$element_value_origin=$formData['value'];
									}
			    					$taskUnitsData = new TasksUnitsData();
			    					//$taskUnitsData->task_id            	 		= $task_id;
			    					$taskUnitsData->tasks_unit_id       		= $taskunit_id;
			    					//$taskUnitsData->task_instruct_servicetask_id= $taskInstructServicetask->id;
			    					$taskUnitsData->form_builder_id				= $form_builder_id;
			    					$taskUnitsData->evid_num_id         		= $media_id;
			    					$taskUnitsData->element_value             	= htmlentities($formData['value']);
									$taskUnitsData->element_value_origin        = htmlentities($element_value_origin);
			    					if(isset($post_data['properties'][$formElement]['unit_id'][$media_id]) && $post_data['properties'][$formElement]['unit_id'][$media_id] != ""){
			    						$taskUnitsData->element_unit    = $post_data['properties'][$formElement]['unit_id'][$media_id];
			    					}
			    					$taskUnitsData->save(false);
	    						}
	    					}
	    				}
	    			}
	    		}
    		}

    		return 'OK';
    	}
    	if(isset($taskunit->unit_assigned_to) && $taskunit->unit_assigned_to==0){
    		$error= 'Task Statistics cannot be entered unless the Task is assigned';
    		return json_encode(array('error'=>$error,'data'=>$mydata));exit;
    	}

    	$team_id=$service_info->teamservice->teamid;
    	$pricepoints = array();
    	if($service_info->billable_item != 0){
    		$pricepoints = (new Pricing)->chkPricePointExistByClientCaseTeam($clientId,$caseId,$team_id,$servicetask_id,$team_loc);
    	}
    	//echo "<pre>",print_r($pricepoints),"</pre>";die;
    	$medias = (new TaskInstructServicetask)->getProjectMedias($task_id);
    	$listunitType=ArrayHelper::map(Unit::find()->where(['remove'=>0])->orderBy('unit_name ASC')->all(), 'id','unit_name');
    	$is_allow_security_adddata=false;

    	if($case_id!="" && $case_id!=0){
    		if((new User)->checkAccess(4.071)){/*158,84*/
    			$is_allow_security_adddata=true;
    		}
    		if((new User)->checkAccess(4.07)){
				$is_allow_security_adddata_bill = true;
			}
    	}else{
    		if((new User)->checkAccess(5.061)!== false){ /*86,163*/
    			$is_allow_security_adddata=true;
    		}
    		if((new User)->checkAccess(5.06)!== false){
				$is_allow_security_adddata_bill = true;
			}
    	}
    	$is_allow_security_data_stat=false;
    	if($case_id!="" && $case_id!=0){
    		if((new User)->checkAccess(4.071)!== false){/*158*/
    			$is_allow_security_data_stat=true;
    		}
    	}else{
    		if((new User)->checkAccess(5.061)!== false){/*163*/
    			$is_allow_security_data_stat=true;
    		}
    	}
    	//die('123');
    	//echo "<pre>"; print_r($billingDataFrom); exit;
    	//echo (new User)->checkAccess(4.071)." - ".$is_allow_security_data_stat;die;
    	$mydata =  $this->renderAjax('AddBillingItems',[
    			'is_allow_security_adddata'=>$is_allow_security_adddata,
    			'is_allow_security_data_stat'=>$is_allow_security_data_stat,
    			'task_id'=>$task_id,
    			'case_id'=>$case_id,
    			'teamId'=>$teamId,
    			'team_loc'=>$team_loc,
    			'servicetask_id'=>$servicetask_id,
    			'taskunit_id'=>$taskunit_id,
    			'pricepoints'=>$pricepoints,
    			'service_info'=>$service_info,
    			'medias'=>$medias,
    			'formbuilder_data'=>$billingDataFrom,
    			'listunitType'=>$listunitType,
    			'is_allow_security_adddata_bill'=>$is_allow_security_adddata_bill
    	]);
    	echo json_encode(array('error'=>$error,'data'=>$mydata));exit;
    }
    /**
     * Delete Billing Item Track Section
     * */
    public function actionDeletebillable($id){
    	$model_billable=TasksUnitsBilling::findOne($id);
    	$model_billable->delete();
    	return 'OK';
    }
    /**
     * Delete Unit Data Item Track Section
     * */
    public function actionDeletedataitem($id){
		$model_billable=TasksUnitsData::findOne($id);
		$element_type = $model_billable->formBuilder->element_type;
		if(strtolower($element_type) == 'checkbox') {
			$allValues=ArrayHelper::map(TasksUnitsData::find()->select(['tbl_tasks_units_data.id','element_value'])->joinWith('tasksUnits')->where('form_builder_id='.$model_billable->form_builder_id.' AND tbl_tasks_units.task_instruct_servicetask_id='.$model_billable->tasksUnits->task_instruct_servicetask_id." AND evid_num_id=".$model_billable->evid_num_id. " AND tbl_tasks_units_data.created = '".$model_billable->created."'")->all(),'id','element_value');
			if(!empty($allValues)){
	    		foreach ($allValues as $unitdata_id=>$eleval){
	    			if(isset($unitdata_id) && $unitdata_id >0){
	    				$modelTasksUnitsData=TasksUnitsData::findOne($unitdata_id);
	    				$modelTasksUnitsData->delete();
	    			}
	    		}
    		}else{
				$model_billable->delete();
			}
		}else{
			$model_billable->delete();
		}
		return 'OK';
    }
    /**
     * Edit Billing Item
     * */
    public function actionEditbilling($id){
    	$model=TasksUnitsBilling::findOne($id);

    	if(Yii::$app->request->post()){
    		$non_billiable=Yii::$app->request->post('TasksUnitsBilling')['nonbillableitem'];
    		$bill_date=Yii::$app->request->post('TasksUnitsBilling')['created'];
			$res = explode("/", $bill_date);
			$changedDate = $res[2]."-".$res[0]."-".$res[1];

			/*Converting User timeZone to UTC*/
			$time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ'],  "HIS");
			if($model->id > 0) {
				$time=(new Options)->ConvertOneTzToAnotherTz($model->created, 'UTC',$_SESSION['usrTZ'], "HIS");
			}
			$bill_date= date('Y-m-d',strtotime($changedDate)).' '.$time;
			$bill_date = (new Options)->ConvertOneTzToAnotherTz($bill_date, $_SESSION['usrTZ'],'UTC', "YMDHIS");
			/*END User Timezone to UTC*/

    		$model->load(Yii::$app->request->post());
    		if(isset($non_billiable) && $non_billiable==1){
                    $model->invoiced = 2;
    		}else{
                    $model->invoiced ='';
            }
    		$model->created=$bill_date;
    		if($model->save()) {
    			return 'OK';
    		} else {
    			return $this->renderAjax('EditBilling',[
    					'model'=>$model,
    			]);
    		}
    	}else{

			if($model->id > 0){
				$model->quantity=round($model->quantity,2);
				$model->created=(new Options)->ConvertOneTzToAnotherTz($model->created, 'UTC',$_SESSION['usrTZ'], "YMDHIS");
			}
		}
    	return $this->renderAjax('EditBilling',[
    			'model'=>$model,
    	]);
    }

    /**
     * IRT 159
     * Edit Data statistics Attachment
     */
    public function actionEditdatafieldattachment()
    {
    	$tasks_unit_id = Yii::$app->request->get('id',0);
    	$post_data = Yii::$app->request->post();
    	$attachment = MyDocument::find()->where(['reference_id' => $tasks_unit_id])->joinWith('user')->joinWith(['mydocumentsBlobs'])->asArray()->all();
    	if($post_data){
    		$remove_attachments = $post_data['remove_attachments'];
    		/* Code for evidence attachment start */
    		$id =Yii::$app->db->getLastInsertId();
    		if(!empty($_FILES['TasksUnitsBilling']['name']['attachment'])){
    			$docmodel = new Mydocument();
    			$doc_arr['p_id']=0;
    			$doc_arr['reference_id']=$tasks_unit_id;
    			$doc_arr['team_loc']=0;
    			$doc_arr['origination']="Data Statistics";
    			$doc_arr['type']='F';
    			$doc_arr['is_private']=0;
    			$docmodel->origination = "Data Statistics";
    			$file_arr=$docmodel->Savemydocs('TasksUnitsBilling', 'attachment', $doc_arr, $remove_attachments);
    		} else {
    			if($remove_attachments!="") {
    				(new Mydocument())->removeAttachments($remove_attachments);
    			}
    		}
    		return 'OK';
    		die();
    	} else {
    		return $this->renderAjax('EditDataFieldAttachment',[
    			'attachment'=>$attachment
    		]);
    	}
    }

    /**
     * Edit Data Field Track Project Section
     * */
    public function actionEditdatafield($id){
    	$model=TasksUnitsData::findOne($id);
    	if(Yii::$app->request->post()){
    		$post_data = Yii::$app->request->post();
    		$element_id = $model->formBuilder->element_id;
			$type= $model->formBuilder->element_type;
    		if(is_array($post_data[$element_id])){ //checkbox Radio Select
    			$allValues=ArrayHelper::map(TasksUnitsData::find()->select(['tbl_tasks_units_data.id','element_value'])->joinWith('tasksUnits')->where('form_builder_id='.$model->form_builder_id.' AND tbl_tasks_units.task_instruct_servicetask_id='.$model->tasksUnits->task_instruct_servicetask_id." AND evid_num_id=".$model->evid_num_id. " AND tbl_tasks_units_data.created = '".$model->created."'")->all(),'id','element_value');
    			$deleted_ids = array_diff(array_values($allValues),array_values($post_data[$element_id]));
    			//echo "<pre>",print_r($post_data),print_r($deleted_ids),print_r($allValues),print_r($post_data[$element_id]),"</pre>";

    			if(!empty($deleted_ids)){
	    			foreach ($deleted_ids as $val){
	    				$unitdata_id = array_search($val, $allValues);
	    				if(isset($unitdata_id) && $unitdata_id >0){
	    					$modelTasksUnitsData=TasksUnitsData::findOne($unitdata_id);
	    					$modelTasksUnitsData->delete();
	    				}
	    			}
    			}
    			foreach ($post_data[$element_id] as $optionvalue){
    				if(!in_array($optionvalue,$allValues)){
						//echo "<pre>",print_r($optionvalue),"</pre>";
						$eleselval=$element_value_origin="";
						if(is_numeric($optionvalue) && ($type == 'dropdown' || $type == 'radio' || $type == 'checkbox')) {
							$eleselval=(new FormBuilder())->getSelectedElementOption($optionvalue);
						}
						if(isset($eleselval) && $eleselval!=""){
							$element_value_origin=$eleselval;
						}else{
							$element_value_origin=$optionvalue;
						}

						//echo "<pre>",print_r($element_value_origin),"</pre>";
						$modelTasksUnitsData=new TasksUnitsData();
    					$modelTasksUnitsData->tasks_unit_id=$model->tasks_unit_id;
    					$modelTasksUnitsData->evid_num_id=$model->evid_num_id;
    					$modelTasksUnitsData->form_builder_id=$model->form_builder_id;
    					$modelTasksUnitsData->element_value=htmlentities($optionvalue);
						$modelTasksUnitsData->element_value_origin=htmlentities($element_value_origin);
    					$modelTasksUnitsData->modified = isset($post_data['TasksUnitsData']['modified'])?$post_data['TasksUnitsData']['modified']:'';
						$modelTasksUnitsData->created = $model->created;
    					$modelTasksUnitsData->save(false);
    				}
    			}

			//	die;
    			return 'OK';
    		}else{
				/*IRT-921*/
					$eleselval=$element_value_origin="";
					$optionValue=$post_data[$element_id];
					if(is_numeric($optionValue) && ($type == 'dropdown' || $type == 'radio' || $type == 'checkbox')) {
							$eleselval=(new FormBuilder())->getSelectedElementOption($optionValue);
					}
					if(isset($eleselval) && $eleselval!=""){
						$element_value_origin=$eleselval;
					}else{
						$element_value_origin=$optionValue;
					}
					$model->element_value_origin=htmlentities($element_value_origin);
				/*IRT-921*/
    			$model->element_value = htmlentities($post_data[$element_id]);
	    		if(isset($post_data['unit_id'])){
	    			$model->element_unit=$post_data['unit_id'];
	    		}
    			$model->save(false);
    		}
    		return 'OK';
    	}
    	return $this->renderAjax('EditDataField',[
    			'model'=>$model,
    	]);
    }


    /**
     * Finds the Todo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CaseCloseType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTodoModel($id)
    {
    	if (($model = TasksUnitsTodos::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

	/**
     * It will Load Est Report by project ID.
     * On Back button User will lend to below pages as per below criteria
     * If User Lend from Case Projects Main Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=index
     * If User Lend from Cancel Projects Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=load-canceled-projects
     * If User Lend from Closed Projects Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=load-closed-projects
     * If User Lend from Track Project Section URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=track/index
     */
    public function actionEstReport()
    {
    	$qryString = Yii::$app->request->get('querystr','');
    	$case_id = Yii::$app->request->get('case_id',0);
    	$team_id = Yii::$app->request->get('team_id',0);
    	$team_loc = Yii::$app->request->get('team_loc',0);
        $task_id = Yii::$app->request->get('task_id',0);

        if($case_id!=0){
                $type = "case";
                $this->layout = "mycase";
                $datamodel = ClientCase::findOne($case_id);
        } else {
                $type = "team";
                $this->layout = "myteam";
                $datamodel = Team::find()->with([
					'teamLocs'=>function(\yii\db\ActiveQuery $query) use($team_loc){
							$query->where(['team_loc'=>$team_loc]);
					}
                ])->where(['id'=>$team_id])->one();
        }

        if ($datamodel !== null && ($taskmodel = Tasks::findOne($task_id)) !== null) {
                $taskinstruct =  TaskInstruct::find()->with([
                        'taskInstructServicetasks' => function(\yii\db\ActiveQuery $query){
                                $query->orderBy('sort_order');
                        }
                ])->where(['task_id'=>$task_id, 'isactive' => 1])->one();
                $submitted_date = $taskmodel->created;
        $duedatetime = $taskinstruct->task_duedate . " " . $taskinstruct->task_timedue;
        $hourdiff = round(abs((strtotime($duedatetime) - strtotime($submitted_date)) / 3600));
        if ($hourdiff > 0)
            $projected_time = round($hourdiff);

        //$submitted_date = $taskmodel->created;
        //$duedatetime = $taskinstruct->task_duedate . " " . $taskinstruct->task_timedue;
//        $hourdiff = round(abs((strtotime($duedatetime) - strtotime($submitted_date)) / 3600));
        $est_times = 0;
        //$est_hours = 0;
        $actual_times = 0;
        $actual_hours = 0;
	        //$projected_time = 0;
			/*
	        if ($hourdiff > 0)
	            $projected_time = round($hourdiff);

	        $actualtimes = array();
	        $servicetaskinfo = $taskinstruct->taskInstructServicetasks;
	        $serviceest_data = array();
	        //echo "<pre>",print_r($servicetaskinfo),"</pre>";    die;
			if (!empty($servicetaskinfo)) {
	            foreach ($servicetaskinfo as $servicetaskdata) {
	                if (isset($servicetaskdata->est_time) && $servicetaskdata->est_time != "") {
	                    $est_hours = $est_hours + $servicetaskdata->est_time;
	                    $stask_id = $servicetaskdata->servicetask_id;
	                    $serviceest_data[$servicetaskdata->servicetask_id] = $servicetaskdata->est_time;
	                }
	                $percomplete = 0;
		            $getallserviceTaskUnits = TasksUnits::find()->where(['task_id' => $task_id,'task_instruct_servicetask_id'=>$servicetaskdata->id])->all();

		            if (!empty($getallserviceTaskUnits)) {
		                foreach ($getallserviceTaskUnits as $unit_data) {
		                    $unit_status = $unit_data->unit_status;
		                    $services[$servicetaskdata->servicetask_id] = $servicetaskdata->servicetask_id;
		                    $tasktranslogArr = TasksUnitsTransactionLog::find()->select(['duration'])->where(['tasks_unit_id' =>$unit_data->id])->all();
		                    $taskactualhours = 0;
		                    $totalactualhours = 0;
		                    if ($unit_status == "4") {

		                        if (count($tasktranslogArr) > 0) {
									foreach ($tasktranslogArr as $tasktranslogdata) {
		                                $duration = $tasktranslogdata->duration;
		                                $durationArr = explode(' ', $duration);
		                                $hours = floor($durationArr[4] / 60);
		                                $hours = $hours + $durationArr[2];
		                                $totalhours = floor(($durationArr[0] * 86400) / 3600) + $hours;
		                                $taskactualhours = $taskactualhours + $totalhours;
		                            }
		                        }
		                    }
		                    $totalactualhours = $taskactualhours;
		                    $actual_hours = $actual_hours + $totalactualhours;
		                }
		            }
	            }
	        }
	        */

//	        if ($est_hours > 0)
//                    $est_times = round($est_hours);

//	        if ($actual_hours > 0)
//	             $actual_times = round($actual_hours);

	        $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->orderBy('team_location_name ASC')->where(['remove'=>0])->all(), 'id', 'team_location_name');
	        //$myfinal_arr = (new TasksUnits)->getTrackTaskProgress($servicetaskinfo, $task_id, $taskmodel->task_status, $est_hours, $serviceest_data, $case_id, $team_id, $team_loc, $type, $teamLocation);
	        $tasksUnitsData = (new TasksUnits)->getTasksUnitsDetails($task_id,$case_id,$team_id,$taskmodel->task_status,$type,'',$team_loc);
			$total_est_time = (new TasksUnits)->getProjectedHrs($task_id);
			//echo "<pre>",print_r($total_est_time),"</pre>";
			//die;
	        $catAr = array('categories'=>['Projected','Actual']);
	        $serAr = array('series'=>array(['name'=>'Hours','colorByPoint'=>true,'data'=>[['Projected',$total_est_time],['Actual',$tasksUnitsData['actual_times']]]]));
			//$serAr = array('series'=>array(['name'=>'Hours','colorByPoint'=>true,'data'=>[['Projected',$tasksUnitsData['total_est_time']],['Actual',$tasksUnitsData['actual_times']]]]));
		if(Yii::$app->db->driverName == 'mysql'){
			$getTaskPercentage = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid($task_id);
		}else{
			$getTaskPercentage = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid($task_id);
		}
    	$perc_complete = \Yii::$app->db->createCommand("SELECT ".$getTaskPercentage."")->queryScalar();

	        $categories = json_encode($catAr);
			$series = json_encode($serAr);
			return $this->render('est-report', [
				'case_id' => $case_id,
				'team_id' => $team_id,
				'team_loc' => $team_loc,
				'type' => $type,
				'task_id' => $task_id,
				'taskmodel' => $taskmodel,
				'taskinstruct' => $taskinstruct,
				'categories' => $categories,
				'series' => $series,
				'myfinal_arr' => $tasksUnitsData['arrResult'],
				'est_hours' => $tasksUnitsData['total_est_time'],
				'qryString' => str_replace("||","&",$qryString),
				'perc_complete' => $perc_complete,
			]);
		} else {
                    throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
     * It will Load Team Est Report by project ID.
     * On Back button User will lend to below pages as per below criteria
     * If User Lend from Case Projects Main Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=index
     * If User Lend from Cancel Projects Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=load-canceled-projects
     * If User Lend from Closed Projects Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=load-closed-projects
     * If User Lend from Track Project Section URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=track/index
     */
    public function actionTeamEstReport()
    {
    	$qryString = Yii::$app->request->get('querystr','');
    	$case_id = Yii::$app->request->get('case_id',0);
    	$team_id = Yii::$app->request->get('team_id',0);
    	$team_loc = Yii::$app->request->get('team_loc',0);
        $task_id = Yii::$app->request->get('task_id',0);


		$type = "team";
		$this->layout = "myteam";
		$datamodel = Team::find()->with([
			'teamLocs'=>function(\yii\db\ActiveQuery $query) use($team_loc){
				$query->where(['team_loc'=>$team_loc]);
			}
		])->where(['id'=>$team_id])->one();


        if ($datamodel !== null && ($taskmodel = Tasks::findOne($task_id)) !== null) {
			/*$taskinstruct->joinWith([
				'tasksUnits' => function(\yii\db\ActiveQuery $query) use($team_id,$team_loc){
					$query->where(['team_loc'=>$team_loc,'team_id'=>$team_id]);
					$query->orderBy('sort_order');
				}
			]);*/
			$taskinstruct = TaskInstruct::find()->where(['tbl_task_instruct.task_id'=>$task_id, 'tbl_task_instruct.isactive' => 1])->one();
			$submitted_date = $taskmodel->created;
			//echo "<pre>",print_r($taskinstruct->attributes),"</pre>";die;
			$duedatetime = $taskinstruct->task_duedate . " " . $taskinstruct->task_timedue;
			$hourdiff = round(abs((strtotime($duedatetime) - strtotime($submitted_date)) / 3600));
			if ($hourdiff > 0)
				$projected_time = round($hourdiff);

			$submitted_date = $taskmodel->created;
			$duedatetime = $taskinstruct->task_duedate . " " . $taskinstruct->task_timedue;

			$est_times = 0;

			$actual_times = 0;
			$actual_hours = 0;

			$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->orderBy('team_location_name ASC')->where(['remove'=>0])->all(), 'id', 'team_location_name');

			$tasksUnitsData = (new TasksUnits)->getTeamTasksUnitsDetails($task_id,$case_id,$team_id,$taskmodel->task_status,$type,'',$team_loc);
			$catAr = array('categories'=>['Projected','Actual']);
			$total_est_time = (new TasksUnits)->getProjectedHrs($task_id);
			//echo "total_est_time=>",$total_est_time;die;
			$serAr = array('series'=>array(['name'=>'Hours','colorByPoint'=>true,'data'=>[['Projected',$total_est_time],['Actual',$tasksUnitsData['actual_times']]]]));

			$getTaskPercentage = (new \app\models\Tasks())->getTeamTaskPercentageCompleteByTaskid($task_id, $team_id, $team_loc);

			$perc_complete = \Yii::$app->db->createCommand("SELECT ".$getTaskPercentage."")->queryScalar();

	        $categories = json_encode($catAr);
			$series = json_encode($serAr);
			return $this->render('team-est-report', [
				'case_id' => $case_id,
				'team_id' => $team_id,
				'team_loc' => $team_loc,
				'type' => $type,
				'task_id' => $task_id,
				'taskmodel' => $taskmodel,
				'taskinstruct' => $taskinstruct,
				'categories' => $categories,
				'series' => $series,
				'myfinal_arr' => $tasksUnitsData['arrResult'],
				'est_hours' => $tasksUnitsData['total_est_time'],
				'qryString' => str_replace("||","&",$qryString),
				'perc_complete' => $perc_complete,
			]);
		} else {
                    throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
