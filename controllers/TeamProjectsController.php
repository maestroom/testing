<?php
namespace app\controllers;

use Yii;
use app\models\search\TaskInstructSearch;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use app\models\User;
use app\models\Tasks;
use app\models\TasksTeams;
use app\models\TaskInstruct;
use app\models\TaskInstructServicetask;
use app\models\search\TeamSearch;
use app\models\Servicetask;
use app\models\Settings;
use app\models\ProjectSecurity;
use app\models\Role;
use app\models\Options;
use app\models\Client;
use app\models\ClientCase;
use app\models\Comments;
use app\models\CommentRoles;
use app\models\CommentTeams;
use app\models\CommentsRead;
use app\models\Team;
use app\models\PriorityTeam;
use app\models\PriorityProject;
use app\models\Mydocument;
use app\models\ProjectRequestType;
use kartik\grid\GridView;

class TeamProjectsController extends Controller
{
	public function beforeAction($action)
	{
		if(Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));

		if((!(new User)->checkAccess(5.01)) || (!(new User)->checkAccess(5.07) && $action->id == 'post-comment')){
			if(!in_array($action->id,array('view-instructions'))) {
				throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
			}
		}

		return parent::beforeAction($action);
	}
    public function actionIndex()
    {
        $this->layout = "myteam";
        $team_id = Yii::$app->request->get('team_id',0);
        $team_loc = Yii::$app->request->get('team_loc',0);
        $active = Yii::$app->request->get('active',"active");
        $val = Yii::$app->request->get('val',"");
        $teamall = Yii::$app->request->get('teamall',"");
		$session = new Session;
		$session->open();
		if (isset($team_id) && $team_id != '')
			$session['teamId'] = $team_id;
		if (isset($val) && $val != '')
			$session['val'] = $val;
		if (isset($teamall) && $teamall != '')
			$session['teamall'] = $teamall;

		$is_accessible_submodul=0;


		if(!isset($session['is_accessible_submodul'])) {
        	$session['is_accessible_submodul']=(new User)->checkAccess(5.02);
        }
        $is_accessible_submodul = $session['is_accessible_submodul'];
        if(!isset($session['is_accessible_submodul']) || $session['is_accessible_submodul'] == '') {
        	$is_accessible_submodul = 0;
        }
        $searchModel = new TeamSearch();

        // echo "<pre>",print_r(Yii::$app->request->queryParams);
		$params['grid_id']='dynagrid-teamprojects';
		Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->loadproject(Yii::$app->request->queryParams);
        $models=$dataProvider->getModels();
		//echo "<pre>",print_r(Yii::$app->request->queryParams),"</pre>";die;
        //$sdata = $dataProvider->getSort();
        $params = Yii::$app->request->queryParams;
        /*$project_display = array();*/

        /* IRT 67,68,86,87,258 */
        $filter_type=\app\models\User::getFilterType(['tbl_tasks.id','tbl_tasks.client_case_id','tbl_tasks.task_status','tbl_tasks.team_priority','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_task_instruct.project_name','tbl_client_case.client_id','tbl_tasks.client_case_id','per_complete', 'team_per_complete'],['tbl_tasks','tbl_task_instruct','tbl_client_case']);

		//$filter_type['team_status']='team_status';
		$config = ['task_status'=>['All' => 'All', '0'=>'Not Started', '1'=>'Started','3'=>'On Hold','4'=>'Completed','5'=>'Active','6'=>'Active Past Due','7' => 'All Unread Comments'],'team_status'=>['All' => 'All', '0'=>'Not Started', '1'=>'Started','3'=>'On Hold','2'=>'Paused','4'=>'Completed']];


        /* IRT 96,398 Code Starts */
        if (isset($params['TeamSearch']['client_case_id']) && !empty($params['TeamSearch']['client_case_id'])) {
                $client_case_selected = (new User)->getSelectedGridCases($params['TeamSearch']['client_case_id'], 'All');
            if ($client_case_selected == 'ALL') {
                unset($params['TeamSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        if (isset($params['TeamSearch']['client_id']) && !empty($params['TeamSearch']['client_id'])) {
            $clients_selected = (new User)->getSelectedGridClients($params['TeamSearch']['client_id'], 'All');
            if ($clients_selected == 'ALL') {
                unset($params['TeamSearch']['client_id']);
                $clients_selected = array();
            }
        }
		/* IRT 96,398 Code Code Ends */
        $config_widget_options = [
            'client_id' => ['initValueText' => $clients_selected],
            'client_case_id' => ['initValueText' => $client_case_selected],
            'per_complete' => [
                'filter_type' => 'range',
				'options' => ['placeholder' => 'Rate (0 - 100)'],
                'html5Options' => ['min' => 0, 'max' => 100],
            ],
			'team_per_complete' => [
                'filter_type' => 'range',
				'options' => ['placeholder' => 'Rate (0 - 100)'],
                'html5Options' => ['min' => 0, 'max' => 100],
            ]
        ];
				if (Yii::$app->request->isAjax) {
						$this->layout = '';
						Yii::$app->request->queryParams += Yii::$app->request->post();
				}
        /*IRT 96,398*/
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['team-projects/ajax-filter','team_id' => $team_id,'team_loc'=>$team_loc]),$config,$config_widget_options, Yii::$app->request->queryParams, 'team_project');
        /*IRT 67,68,86,87,258*/
        $pporder = PriorityProject::find()->select(['priority_order'])->where('remove = 0')->orderBy('priority_order asc')->one()->priority_order;

        //$filter_type['team_status'] = GridView::FILTER_SELECT2;

		/* End IRT 374 */
        //echo "<pre>",print_r($filter_type),print_r($filterWidgetOption),"</pre>";die;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'team_id' => $team_id,
			'team_loc' => $team_loc,
			'is_accessible_submodule_tracktask' => $is_accessible_submodul,
			'pporder'=>$pporder,
			'project_display' => $project_display,
			'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption,
			'params'=>$params
		]);
    }
    /*
     *  Get the Filter value On Team Projects Data.
     * */
     public function actionAjaxFilter()
     {

		$team_id = Yii::$app->request->get('team_id');
		$team_loc = Yii::$app->request->get('team_loc');
		$searchModel = new TeamSearch();
		$qparams = Yii::$app->request->queryParams;
		$params = array();
		//$dataProvider = $searchModel->searchFilter($qparams,$params);
		$params = array_merge($qparams, Yii::$app->request->bodyParams,$params);
		//echo "<pre>";print_r($params);die;
		$dataProvider = $searchModel->searchLoadProjectFilter($params);
	    foreach ($dataProvider as $key=>$val)
	    {
			$val1 = $val;
			$val2 = '';
			if($val == '')
			{
				$val1 = '(not set)';
				$val='(not set)';
			}
			if( $params['field'] == 'client_id' || $params['field'] == 'client_case_id' )
			{
				$val2 = $key;
			} else {
				$val2 = $val1;
			}
		    $out['results'][] = ['id' => $val2,  'text' => $val, 'label' => $val1];
	    }
	    return json_encode($out);
	 }

     /*
      * Return the detail of Task based on Team.
      **/
      public function actionGetTaskDetails()
      {
		$task_id = Yii::$app->request->post("expandRowKey");
		$team_id = Yii::$app->request->get('team_id');
		$team_loc = Yii::$app->request->get('team_loc');

		$task_data = Tasks::find()->select(['client_case_id', 'tbl_tasks.created', 'tbl_tasks.created_by', 'createdUser.usr_first_name', 'createdUser.usr_lastname', 'createdUser.usr_username','tbl_tasks.task_complete_date','tbl_tasks.task_status'])->joinWith('createdUser')->where(['tbl_tasks.id'=>$task_id])->one();

		$teamservice_data = TaskInstructServicetask::find()->select(['tbl_task_instruct_servicetask.teamservice_id','tbl_teamservice.service_name'])->joinWith('teamservice')->where(['tbl_task_instruct_servicetask.task_id'=>$task_id])->groupBy(['tbl_task_instruct_servicetask.teamservice_id', 'tbl_teamservice.service_name'])->all();

		$servicetask_names = "";
		foreach($teamservice_data as $teamservice)
		{
			  if($servicetask_names != "")
				$servicetask_names.='; ' . $teamservice->teamservice->service_name;
			  else
				$servicetask_names = $teamservice->teamservice->service_name;
		}
		$services=$servicetask_names;
        $unread_comments = (new Tasks)->findReadUnreadCommentTeam($task_id,$team_id,$team_loc);

		if($task_data->createdUser->usr_first_name!="" && $task_data->createdUser->usr_lastname!="") {
			$submitted_by = $task_data->createdUser->usr_first_name." ".$task_data->createdUser->usr_lastname;
		} else {
        	$submitted_by = $task_data->createdUser->usr_username;
        }

        if($task_data->task_complete_date != '0000-00-00 00:00:00' && $task_data->task_status == 4) { $completed_date = (new Options)->ConvertOneTzToAnotherTz($task_data->task_complete_date,"UTC",$_SESSION["usrTZ"],"MDYHIS"); } else{
			$completed_date = '';
		}

		//$completed_date = (new Options)->ConvertOneTzToAnotherTz($task_data->task_complete_date, "UTC", $_SESSION["usrTZ"]);
		return $this->renderPartial('_loadtaskdetails', ['teamservice_data' => $teamservice_data, 'services' => $services, 'comment' => $unread_comments,'completed_date' => $completed_date, 'flag'=>$flag]);
	  }

	/*
     * Get the Popup For Apply Team Priority
     **/
     public function actionLoadteampriority()
     {
		$task_id = Yii::$app->request->get('task_id');
		$team_id  = Yii::$app->request->get('team_id');
		$team_loc = Yii::$app->request->get('team_loc');
		$searchModel = new TaskInstruct();
		$tpriority_data = PriorityTeam::find()->where('remove = 0')->
			joinWith(['priorityTeamLoc'=>function(\yii\db\ActiveQuery $query)use($team_id, $team_loc){
				$query->where(['team_id'=>$team_id,'team_loc_id'=>$team_loc]);
				$query->orderby('priority_order ASC');
			}])->asArray()->all();

		//echo "<pre>",print_r($tpriority_data); die;
		foreach($tpriority_data as $data => $value) {
			$dropdown_data[$data]['id'] = $value['id'];
			$dropdown_data[$data]['priority_name'] = $value['tasks_priority_name'];
		}
        return $this->renderAjax('loadteamPriority', [
			'team_loc'=>$team_loc,
			'team_id'=>$team_id,
			'task_id'=>$task_id,
			'dropdown_data' => $dropdown_data,
			'searchmodel'=>$searchModel
        ]);
	 }
	 /*
	  * Update the Team Priority
	  * */
	 public function actionUpdateteampriority()
	 {
		$task_ids = Yii::$app->request->post('task_id');
		$team_id  = Yii::$app->request->post('team_id');
		$team_loc = Yii::$app->request->post('team_loc');
		$priority = Yii::$app->request->post('team_prioriy');
		$remove = Yii::$app->request->post('remove_team_priority');

		if (isset($task_ids) && !empty($task_ids)) {
			if (isset($remove) && $remove != "") {
                TasksTeams::updateAll(['team_loc_prority' => 0],'team_id='.$team_id.' AND team_loc='.$team_loc.' AND task_id IN ('.implode(',',$task_ids).')');
            } else {
				TasksTeams::updateAll(['team_loc_prority' => $priority], 'team_id='.$team_id.' AND team_loc='.$team_loc.' AND task_id IN ('.implode(',',$task_ids).')');
            }
		}
	    exit;
	 }

    /**
     * Show Project Instrcution with versions
     **/
    public function actionInstrution($task_id,$team_id,$team_loc)
    {
    	$this->layout="myteam";
        /*IRT 67,68,86,87,258 code Starts */
        $filter_type = \app\models\User::getFilterType(['tbl_task_instruct.instruct_version','tbl_task_instruct.created_by','tbl_task_instruct.created'],['tbl_task_instruct']);
        $config = [];
        $config_widget_options = [];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-projects/ajax-change-filter']).'&case_id='.$case_id.'&task_id='.$task_id,$config,$config_widget_options);
    	/* IRT 67,68,86,87,258 code Ends */
    	$searchModel = new TaskInstructSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams,$task_id);
    	return $this->render('Instrution',[
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    		'team_id'=>$team_id,
    		'team_loc'=>$team_loc,
    		'task_id'=>$task_id,
            'filter_type'=>$filter_type,
            'filterWidgetOption' => $filterWidgetOption
    	]);
    }
    /*
     * Instruction version Ajax Filter
     * */
     public function actionInstructionFilter(){
		$team_id = Yii::$app->request->get('team_id');
		$team_loc = Yii::$app->request->get('team_loc');
		$task_id = Yii::$app->request->get('task_id');
		$searchModel = new TaskInstructSearch();
	    $dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams,$task_id);
	    foreach ($dataProvider as $key => $val){
		    $out['results'][] = ['id' => $val, 'text' => $val,'label' => $val];
	    }
	    return json_encode($out);
	 }

    /**
     *  view task instruction details
     * */
    public function actionViewInstructions($taskinstruction_id)
    {
    	$model = TaskInstruct::findOne($taskinstruction_id);
    	$old_instruction = TaskInstruct::find()->where('task_id = '.$model->task_id.' AND instruct_version = '.($model->instruct_version-1))->one();
    	$old_instruction_id = $old_instruction->id;
    	$oldversion = $old_instruction->instruct_version;
    	$changeFBIds=array();
		$changeServiceAttchmentIds=array();
    	if(isset($old_instruction_id)){
    		$changeFBIds = (new Tasks)->getChangedFBID($model->task_id,$oldversion);
			$changeServiceAttchmentIds = (new TaskInstructServicetask)->changeAttchment($model->task_id,$taskinstruction_id);
			//echo "<pre>",print_r($changeServiceAttchmentIds),"</pre>";die;
		}


    	$project_track_data = (new TaskInstructServicetask)->getTrackProjectDataByInstructionId($taskinstruction_id);


    	$task_instructions_data = $project_track_data->getModels();

		$project_request_type = ArrayHelper::map(ProjectRequestType::find()->orderBy('request_type ASC')->all(),'id','request_type');

    	$task_data=Tasks::find()->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($taskinstruction_id){ $query->where(['tbl_task_instruct.id' => $taskinstruction_id])->joinWith('taskPriority')->joinWith(['taskInstructEvidences'=> function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_task_instruct_evidence.id','tbl_task_instruct_evidence.prod_id','tbl_task_instruct_evidence.task_instruct_id'])->joinWith('evidenceProduction');
			}]);}, 'createdUser', 'clientCase' => function (\yii\db\ActiveQuery $query) { $query->joinWith('salesRepo'); }])->where(['tbl_task_instruct.id' => $taskinstruction_id])->one();

    	$task_id = $model->task_id;
    	$settings_data = Settings::find()->where("field IN ('instruction_footer','instruction_header')")->all();
    	foreach($task_instructions_data as $key => $val)
    	{
    		$servicetask_id = $val['servicetask_id'];
    		$taskunit_id   = $val['taskunit_id'];
    		$sort_order    = $val['sort_order'];
    		$teamId        = $val['teamId'];
    		$team_loc      = $val['team_loc'];
    	 	$processTrackData[$key]['media'] = (new TaskInstructServicetask)->processTrackMedia($servicetask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$taskinstruction_id,$options);
    	 	$processTrackData[$key]['task_instructions'] = (new TaskInstructServicetask)->processTrackInstruction($servicetask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$taskinstruction_id,$options);
    	}

    	$acces_team_arr = (new ProjectSecurity)->getUserTeamsArr(Yii::$app->user->identity->id);
    	$acces_team_loc_arr = (new ProjectSecurity)->getUserTeamsLocArr(Yii::$app->user->identity->id);
    	$roleId = Yii::$app->user->identity->role_id;
    	$roleInfo=Role::findOne($roleId);
    	$User_Role=explode(',',$roleInfo->role_type);
    	if($roleId=='0'){ // if super user all access
    		$acces_team_arr[1] = 1;
    	}if(in_array(1,$User_Role)){
    		$acces_team_arr[1] = 1;
    	}

    	$belongtocurr_team_serarr = (new Servicetask)->getBelongto(Yii::$app->user->identity->id);
    	$stlocaccess = (new Servicetask)->getBelongtoLoc(Yii::$app->user->identity->id);

    	return $this->renderPartial('view-task-instructions',[
			'task_instructions_data'=>$task_instructions_data,
			'task_id' => $task_id,
			'settings_data' => $settings_data,
			'task_data' => $task_data,
			'stlocaccess'=>$stlocaccess,
			'processTrackData'=>$processTrackData,
			'servicetask_id'=>$servicetask_id,
			'teamId'=>$teamId,
			'instruct_id'=>$taskinstruction_id,
			'team_loc'=>$team_loc,
			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
			'model'=>$model,
			'project_request_type' => $project_request_type,
			'old_instruction_id'=>$old_instruction_id,
			'changeFBIds'=>$changeFBIds,
			'changeServiceAttchmentIds'=>$changeServiceAttchmentIds
    	]);
    }
    /**
     * Post Project Comment Case wise
     * */
    public function actionPostComment($task_id,$team_id,$team_loc){
    	$refere = Yii::$app->request->referrer;
    	if(!strpos($refere,'case-projects/post-comment')){
    		Yii::$app->getUser()->setReturnUrl(Yii::$app->request->referrer);
    	}
    	$this->layout = "myteam";
    	$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
    	if(!empty($comment_data)){
    		$comments_rows=array();
    		foreach ($comment_data as $comment){
    			$commentsAttr = array();
    			$commentsAttr['comment_id']=$comment->Id;
    			$commentsAttr['user_id']=Yii::$app->user->identity->id;
    			$comments_rows[] = $commentsAttr;
    		}
    		if(!empty($comments_rows)){
    			$columns = (new CommentsRead)->attributes();
    			unset($columns[array_search('Id',$columns)]);
    			Yii::$app->db->createCommand()->batchInsert(CommentsRead::tableName(), $columns, $comments_rows)->execute();
    		}
    	}
    	$model = new Comments();
    	if(Yii::$app->request->post()) {
    		$post_data = Yii::$app->request->post();
            if($model->postComment($post_data,$task_id,$case_id)) {
				$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
				return $this->renderAjax('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'team_id'=>$team_id,'model'=>$model]);
    		} else {
    			return $this->render('PostComment',['comment_data'=>$comment_data,'task_id'=>$task_id,'case_id'=>$case_id,'model'=>$model]);
    		}
    	}
    	return $this->render('PostComment',['comment_data'=>$comment_data,'task_id'=>$task_id,'team_id'=>$team_id,'team_loc'=>$team_loc,'model'=>$model]);
    }
    /**
     * Case comment Receipents
     * */
    public function actionNewrecipients() {
    	$task_id=Yii::$app->request->post('task_id');
    	$team_id=Yii::$app->request->post('team_id');
    	$team_loc=Yii::$app->request->post('team_loc');

		$fixed_emailsend_user_ids=explode(",",Yii::$app->request->post('fixed_emailsend_user_ids',0));
    	$case_ids=explode(", ",Yii::$app->request->post('case_ids',0));
    	//$team_ids=explode(",",Yii::$app->request->post('team_ids',0));
		$team_ids=explode(", ",Yii::$app->request->post('team_ids',0));

    	$cases_ids=explode(", ",Yii::$app->request->post('cases_ids',0));
    	$teams_ids=explode(", ",Yii::$app->request->post('teams_ids',0));

    	//$role_data=ArrayHelper::map(Role::find()->select(['id','role_name'])->where("role_type like '%1%' AND id > 0")->orderBy('role_name')->all(),'id','role_name');
    	// $team_data=ArrayHelper::map(Team::find()->select(['id','team_name'])->where('id IN ('.$team_id.')')->orderBy('team_name')->all(),'id','team_name');
		/*case role users*/			
		$case_sql="SELECT client_case_id FROM tbl_tasks WHERE id=$task_id";
		$case_roles="SELECT id FROM tbl_role WHERE role_type like '%1%' AND id > 0";
		$casemain_sql="SELECT tbl_user.id, role_id,role_name, CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname FROM tbl_user 
		INNER join tbl_project_security on tbl_project_security.user_id= tbl_user.id 
		INNER JOIN tbl_role on tbl_role.id=role_id 
		WHERE role_id IN ($case_roles) AND tbl_project_security.client_case_id IN ($case_sql) ORDER BY  role_name,fullname";
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

    	//$sql = 'SELECT id,team_name FROM tbl_team WHERE id IN (SELECT team_id FROM tbl_tasks_teams WHERE task_id = '.$task_id.')';
    	//$res_teamdata = \Yii::$app->db->createCommand($sql)->queryAll();
    	//$team_data = ArrayHelper::map($res_teamdata,'id','team_name');
		/*Team users*/
		$sql = "SELECT team_id FROM tbl_tasks_units WHERE task_id = ".$task_id." group by team_id";		
		$main_sql="SELECT DISTINCT team_id,tbl_user.id,team_name,CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname FROM tbl_user
		INNER join tbl_project_security on tbl_project_security.user_id= tbl_user.id 
		INNER join tbl_team on tbl_team.id=tbl_project_security.team_id
		WHERE team_id IN ($sql) order by team_name,fullname";
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
		// echo "<pre>",print_r($team_data); die;
    	return $this->renderAjax('Recipientsnew',['teamUserList'=>$teamUserList,'caseUserList'=>$caseUserList,'role_data'=>$role_data,'team_data'=>$team_data,'case_ids'=>$case_ids,'team_ids'=>$team_ids,'teamid'=>$team_id, 'cases_ids' => $cases_ids, 'teams_ids' => $teams_ids]);
    }
	/**
     * Case comment Receipents
     * */
    public function actionRecipients(){
    	$task_id=Yii::$app->request->post('task_id');
    	$team_id=Yii::$app->request->post('team_id');
    	$team_loc=Yii::$app->request->post('team_loc');
    	$case_ids=explode(",",Yii::$app->request->post('case_ids',0));
    	$team_ids=explode(",",Yii::$app->request->post('team_ids',0));

    	$cases_ids=explode(", ",Yii::$app->request->post('cases_ids',0));
    	$teams_ids=explode(", ",Yii::$app->request->post('teams_ids',0));

    	$role_data=ArrayHelper::map(Role::find()->select(['id','role_name'])->where("role_type like '%1%' AND id > 0")->orderBy('role_name')->all(),'id','role_name');
    	// $team_data=ArrayHelper::map(Team::find()->select(['id','team_name'])->where('id IN ('.$team_id.')')->orderBy('team_name')->all(),'id','team_name');
    	$sql = 'SELECT id,team_name FROM tbl_team WHERE id IN (SELECT team_id FROM tbl_tasks_teams WHERE task_id = '.$task_id.')';
    	$res_teamdata = \Yii::$app->db->createCommand($sql)->queryAll();
    	$team_data = ArrayHelper::map($res_teamdata,'id','team_name');
		// echo "<pre>",print_r($team_data); die;
    	return $this->renderAjax('Recipients',['role_data'=>$role_data,'team_data'=>$team_data,'case_ids'=>$case_ids,'team_ids'=>$team_ids,'teamid'=>$team_id, 'cases_ids' => $cases_ids, 'teams_ids' => $teams_ids]);
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
    		/*$team_users = Projectsecurity::find()->select(['tbl_project_security.user_id','tbl_project_security.team_id'])
        		->joinWith(['user' => function(\yii\db\ActiveQuery $query) {
        			$query->select(['tbl_user.id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname"])->orderBY('tbl_user.usr_first_name ASC');
        		}])->joinWith('team')->where('tbl_project_security.team_id IN ('.$team_roles.')')
			->orderBy("tbl_team.team_name ASC")
			->groupBy(['tbl_project_security.user_id','tbl_project_security.team_id'])->asArray()->all();
			*/
			$team_users = Projectsecurity::find()->select(['tbl_project_security.user_id','tbl_project_security.team_id'])
        		->joinWith(['user' => function(\yii\db\ActiveQuery $query) {
        			$query->select(['tbl_user.id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) fullname"])->orderBy('tbl_user.usr_first_name ASC');
        		}])->joinWith('team')->where('tbl_project_security.team_id IN ('.$team_roles.')')
			->orderBy('tbl_team.team_name ASC')
    		->groupBy(['tbl_project_security.user_id','tbl_project_security.team_id','tbl_team.team_name','tbl_user.usr_first_name'])->asArray()->all();
    	}
    	return $this->renderAjax('RecipientsUsers',['case_users' => $case_users, 'team_users' => $team_users, 'case_ids' => $case_ids, 'team_ids' => $team_ids]);
    }

    /**
     * Case Edit comment
     * */
    public function actionEditComment($id,$task_id,$case_team_id){
    	$model = Comments::findOne($id);
    	if(Yii::$app->request->post()){
    		$post_data = Yii::$app->request->post();
    		$model->editComment($post_data,$id,$task_id);
    		$comment_data=Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
    		return $this->renderAjax('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'team_id'=>$case_team_id,'model'=>$model]);
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
    		return $this->renderAjax('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'team_id'=>$case_team_id,'model'=>$model]);
    	}
    	return $this->renderAjax('ReplyComment',['model'=>$model,'id'=>$id,'task_id'=>$task_id,'case_team_id'=>$case_team_id]);
    }
    /**
     * Case Comment Delete
     * */
    public function actionDeleteComment($id,$msg){
    	return (new Comments())->deleteComment($id,$msg);
    }


}
