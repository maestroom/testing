<?php
namespace app\controllers;

use Yii;
use yii\web\Session;
use app\models\Team;
use app\models\User;
use app\models\Tasks;
use app\models\TasksTeams;
use app\models\SummaryComment;
use app\models\search\TeamSearch;
use app\models\TeamlocationMaster;
use app\models\CommentsRead;
use app\models\Options;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends Controller
{
    /**
     * @inheritdoc
     */
     
    public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(5)){
			/* IRT 31 Default landing page */
			$def_land_page = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->default_landing_page;
            if($def_land_page=='') $def_land_page = '';
            if($def_land_page==1){ // Show My Assignments
				if((new User)->checkAccess(1)) {
					$redirect_method[] = 'site/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(1);
				}
			} else if ($def_land_page==2){ // Show Media
				if((new User)->checkAccess(3)) {
					$redirect_method[] = 'media/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(2);
				}
			} else if ($def_land_page==3){ // Show My Teams
				if((new User)->checkAccess(4)) {	
					$redirect_method[] = 'mycase/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(3);
				}
			} else if ($def_land_page==4){ // Show My Cases
				if((new User)->checkAccess(5)) {
					$redirect_method[] = 'team/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(4);
				}
			} else if ($def_land_page==5){ // Show Global Projects
				if((new User)->checkAccess(2)) {
					$redirect_method[] = 'global-projects/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(5);
				}
			} else if ($def_land_page==6){ // Show Billing
				if((new User)->checkAccess(7)) {
					$redirect_method[] = 'billing-pricelist/internal-team-pricing';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(6);
				}
			} else if ($def_land_page==7){ // Show Report 
				if((new User)->checkAccess(11)) {
					$redirect_method[] = 'custom-report/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(7);
				}
			} else if ($def_land_page==8){ // Show Administrator
				if((new User)->checkAccess(8)) {
					$redirect_method[] =  'site/administration';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(8);
				}
			} else {
				if((new User)->checkAccess(1)) {
					return $this->redirect(array(
						'site/index'
					));	
				} elseif((new User)->checkAccess(3)) {
					return $this->redirect(array(
						'media/index'
					));	
				} elseif((new User)->checkAccess(4)) {
					return $this->redirect(array(
						'mycase/index'
					));	
				} elseif((new User)->checkAccess(5)) {
					return $this->redirect(array(
						'team/index'
					));	
				} elseif((new User)->checkAccess(2)) {
					return $this->redirect(array(
						'global-projects/index'
					));	
				} elseif((new User)->checkAccess(7)) {
					return $this->redirect(array(
						'billing-pricelist/internal-team-pricing'
					));	
				} elseif((new User)->checkAccess(75)) {
					return $this->redirect(array(
						'site/reports'
					));	
				} elseif((new User)->checkAccess(8)) {
					return $this->redirect(array(
						'site/administration'
					));	
				} else{
					return $this->goBack();
				}
			}
			return $this->redirect($redirect_method);
			//throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
		}
			
	
		return parent::beforeAction($action);
	} 
     
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Team models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$params);
		$userId = Yii::$app->user->identity->id; 
		$roleId = Yii::$app->user->identity->role_id;
		$sql_query = "SELECT team.id as team_id,team.team_name,tbl_team_locs.team_loc,master.team_location_name  FROM tbl_team as team LEFT JOIN tbl_team_locs on tbl_team_locs.team_id=team.id LEFT JOIN tbl_teamlocation_master as master ON master.id = tbl_team_locs.team_loc AND master.remove=0 WHERE  team.id != 1 AND tbl_team_locs.team_loc IS NOT NULL order by team.team_name,master.team_location_name ";
		if($roleId!=0){
			$sql_query = "SELECT security.team_id,security.team_loc,team.team_name,master.team_location_name  FROM tbl_project_security security INNER JOIN tbl_team as team ON team.id = security.team_id INNER JOIN tbl_teamlocation_master as master ON master.id = security.team_loc AND master.remove=0 WHERE security.user_id = ".$userId." AND security.team_id != 0 AND security.team_loc != 0 order by team.team_name,master.team_location_name";
		}
		$params[':user_id'] = $userId;
		$dropdown_data = \Yii::$app->db->createCommand($sql_query)->queryAll();
		if(!empty($dropdown_data)) {
			foreach($dropdown_data as $drop => $value) {
				if(isset($value['team_loc'])  && $value['team_loc']!="") {
					$dropdown_widget[$drop]['id'] = $value['team_id'].'_'.$value['team_loc'];
					$dropdown_widget[$drop]['team_name'] = $value['team_name'].' - '.$value['team_location_name'];
				}
			}
		} else {
			$dropdown_widget['id'] = 'No Records Found';
		}
		
		// echo "<pre>",print_r($dataProvider); die;
		// echo "<pre>"; print_r($dropdown_widget); exit;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dropdown_data' => $dropdown_widget,
        ]);
    }
    
    /*
     * Get the Team Details page On Team
     * */
    public function actionGetteamdetails()
    {
		$firstload=Yii::$app->request->post('firstload'); 
        $team_id = Yii::$app->request->post('expandRowKey');
        $userId = Yii::$app->user->identity->id; 
        $roleId = Yii::$app->user->identity->role_id;
        
        $where = "select team_loc from tbl_team_locs where team_id=".$team_id;
        if($roleId!=0){
			$where = "select team_loc from tbl_project_security where user_id = ".$userId." AND team_id = ".$team_id;
		}
		
		/*$data_sql = "SELECT DISTINCT master.id as team_loc,team.team_id,master.team_location_name,
			(
			SELECT COUNT(DISTINCT t.id) FROM tbl_tasks t  
			INNER JOIN tbl_task_instruct as t1 ON t.id =  t1.task_id 
			INNER JOIN tbl_tasks_teams as service ON t.id = service.task_id
			INNER JOIN tbl_client_case as t2 ON t.client_case_id =  t2.id and t2.is_close=0   
			WHERE service.team_id = $team_id AND service.team_loc = team.team_loc AND task_status IN (0,1,3) AND t.task_closed = 0 AND task_cancel = 0 AND t1.isactive='1' AND task_status != 4
			) as task_count,
			(SELECT COUNT(DISTINCT t.id) FROM tbl_tasks_units as t 
			INNER JOIN tbl_task_instruct_servicetask as service ON t.task_instruct_servicetask_id = service.id
            INNER JOIN tbl_task_instruct Taskindividual ON service.task_instruct_id=Taskindividual.id
            INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
			INNER JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
			WHERE task.task_closed=0 AND task.task_cancel=0 AND service.team_id= $team_id AND service.team_loc= team.team_loc AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND (t.unit_status!=4)
			) as incmpt_task,
			(SELECT count( DISTINCT TasksUnitsTodos.id ) FROM tbl_tasks_units AS t 
			INNER JOIN tbl_task_instruct_servicetask AS service ON t.task_instruct_servicetask_id = service.id 
			INNER JOIN tbl_task_instruct Taskindividual ON ( Taskindividual.id = service.task_instruct_id ) 
			INNER JOIN tbl_tasks task ON ( Taskindividual.task_id = task.id ) 
			INNER JOIN tbl_client_case clientCase ON ( task.client_case_id = clientCase.id ) 
			LEFT JOIN tbl_tasks_units_todos TasksUnitsTodos ON ( TasksUnitsTodos.tasks_unit_id = t.id )
			WHERE task.task_closed =0 AND task.task_cancel =0 AND service.team_id = $team_id AND service.team_loc = team.team_loc AND Taskindividual.isactive =1 AND clientCase.is_close =0 AND task.task_closed =0 AND TasksUnitsTodos.complete =0
			) as incmpt_todos 
			FROM  tbl_teamlocation_master as master 
			INNER JOIN tbl_tasks_teams as team ON master.id = team.team_loc 
			INNER JOIN tbl_tasks as task ON task.id = team.task_id 
			INNER JOIN tbl_task_instruct as instruct ON instruct.task_id = team.task_id AND instruct.isactive = 1
			WHERE team.team_id = $team_id AND team.team_loc IN ($where) AND task.task_closed =0 AND task.task_cancel =0  
			";*/
			
		$data_sql = "SELECT DISTINCT master.id as team_loc, team.team_id, master.team_location_name FROM tbl_teamlocation_master as master INNER JOIN tbl_tasks_teams as team ON master.id = team.team_loc INNER JOIN tbl_tasks as task ON task.id = team.task_id WHERE team.team_id = $team_id AND team.team_loc IN ($where) AND task.task_closed = 0 AND task.task_cancel = 0;";	
		// echo $data_sql;
		$data = \Yii::$app->db->createCommand($data_sql)->queryAll();	
		// echo "<pre>"; print_r($data); exit;	
		if(!empty($data)){
			foreach ($data as $da) {
				$team_loc = $da['team_loc'];
				
				// To get Incomplete Project's count by team and loc
				$task_count_sql = "SELECT COUNT(DISTINCT t.id) FROM tbl_tasks t INNER JOIN tbl_tasks_teams as service ON t.id = service.task_id INNER JOIN tbl_client_case as t2 ON t.client_case_id = t2.id and t2.is_close = 0 WHERE service.team_id = $team_id AND service.team_loc = $team_loc AND task_status IN (0, 1, 3) AND t.task_closed = 0 AND task_cancel = 0 AND task_status != 4;";
				
				// To get Incomplete Task's count by team and loc
				$incmpt_task_sql = "SELECT COUNT(DISTINCT t.id) FROM tbl_tasks_units as t INNER JOIN tbl_tasks task ON (t.task_id = task.id) INNER JOIN tbl_client_case clientCase ON (task.client_case_id = clientCase.id) WHERE task.task_closed = 0 AND task.task_cancel = 0 AND t.team_id = $team_id AND t.team_loc = $team_loc AND clientCase.is_close = 0 AND (t.unit_status != 4);";
				
				// To get Incomplete Todo's count by team and loc
				$incmpt_todos_sql = "SELECT count(DISTINCT TasksUnitsTodos.id) FROM tbl_tasks_units AS t INNER JOIN tbl_tasks task ON (t.task_id = task.id) INNER JOIN tbl_client_case clientCase ON (task.client_case_id = clientCase.id) LEFT JOIN tbl_tasks_units_todos TasksUnitsTodos ON (TasksUnitsTodos.tasks_unit_id = t.id) WHERE task.task_closed = 0 AND task.task_cancel = 0 AND t.team_id = $team_id AND t.team_loc = $team_loc AND clientCase.is_close = 0 AND task.task_closed = 0 AND TasksUnitsTodos.complete = 0;";
				
				$da['task_count'] = \Yii::$app->db->createCommand($task_count_sql)->queryScalar();
				$da['incmpt_task'] = \Yii::$app->db->createCommand($incmpt_task_sql)->queryScalar();
				$da['incmpt_todos'] = \Yii::$app->db->createCommand($incmpt_todos_sql)->queryScalar();
				$unread_case_comments = (new SummaryComment)->getUnreadComments(0,$team_id,$team_loc, "comment");
				$unread_comment_count = $da['team_id'].'_'.$da['team_loc']; // unread comment
				$team_data[$da['team_loc']] = array(
					'team_loc_name' => $da['team_location_name'],
					'task_count' => (new User)->checkAccess(5.01)?Html::a($da['task_count'],null,['href'=>Url::toRoute(['team-projects/index','team_id'=>$team_id,'team_loc'=>$da['team_loc'],'active'=>'active']),"data-pjax" => 0,'title'=>$da['task_count'].' Active Projects']) : $da['task_count'],
					'incmpt_task' => (new User)->checkAccess(5.014)?Html::a($da['incmpt_task'],'@web/index.php?r=team-tasks/index&team_id='.$team_id.'&team_loc='.$da['team_loc'],["data-pjax" => 0,'title'=>$da['incmpt_task']. ' Incomplete Tasks']) : $da['incmpt_task'],
					'incmpt_todos' => (new User)->checkAccess(5.014)?Html::a($da['incmpt_todos'],'@web/index.php?r=team-tasks/index&team_id='.$team_id.'&team_loc='.$da['team_loc'].'&statusFilter=8',["data-pjax" => 0,'title'=>$da['incmpt_todos'].' Incomplete Todos']) : $da['incmpt_todos'],
					'team_id' => $da['team_id'],
					'team_loc' => $da['team_loc'],
					'comment' => (new Tasks)->getUnreadCommentsTeam($team_id,$da['team_loc']),
					'unread_case_comments'=>$unread_case_comments
				);
			}
		}
		// echo "<pre>",print_r($team_data);die;	
		return $this->renderPartial('_getteamdetails', ['team_data'=>$team_data, 'team_id' => $team_id,'firstload'=>$firstload]);    
	 }
	 /*
	  * Get the Unread Comments
	  **/
	 public function actionShowserachmyteam(){
		$term = Yii::$app->request->post('term');
		$team_id = Yii::$app->request->post('team_id');
		$term = str_replace('"', "", $term);
		//echo $team_id; die;
		$search_results = (new Team)->getTeamSerachGoogled($term, $team_id);
		return $this->renderPartial('teamsearchresults', ['search_results' => $search_results, 'term' => $term]);
	 }
	/*
	 * Get the Project Status Graph of Team 
	 * */
	 public function actionGetprojectstatuschartdata()
	 {
		$team_loc = Yii::$app->request->post('team_loc',0);
		$team_id = Yii::$app->request->post('team_id',0);
		$type = Yii::$app->request->post('type','team_loc');
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		$status = array(0 => 'Not Started', 1 => 'Started', 3 => 'On Hold');
	
		if($type == 'team'){
			if($roleId!=0){
				$data_sql = "SELECT distinct tbl_project_security.team_loc FROM tbl_tasks_teams INNER JOIN tbl_project_security ON tbl_tasks_teams.team_id = tbl_project_security.team_id INNER JOIN tbl_tasks ON tbl_tasks_teams.task_id = tbl_tasks.id INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id WHERE tbl_project_security.user_id= :user_id AND tbl_project_security.team_id= :team_id AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close = 0 AND tbl_task_instruct.isactive=1";
				$params = array(':team_id'=>$team_id,':user_id'=>$userId);
			}else{
				$data_sql = "SELECT distinct tbl_team_locs.team_loc FROM tbl_tasks_teams INNER JOIN tbl_team_locs ON tbl_tasks_teams.team_id = tbl_team_locs.team_id INNER JOIN tbl_tasks ON tbl_tasks_teams.task_id = tbl_tasks.id INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id WHERE tbl_team_locs.team_id= :team_id AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close = 0 AND tbl_task_instruct.isactive=1";
				$params = array(':team_id'=>$team_id);
			}
			$data = \Yii::$app->db->createCommand($data_sql,$params)->queryAll();	
			$team_loc_ids=array();
			$casechart = 0;
			// echo "<pre>"; print_r($data); exit;
			if(!empty($data)){
				foreach ($data as $val){
					$team_loc_ids[$val['team_loc']]=$val['team_loc'];
					if(isset($_REQUEST['firstload']) && $_REQUEST['firstload'] == 1){
						$team_loc_ids = $val['team_loc']; break;
					}
				}
			}
		}
		if(is_array($team_loc_ids)){
			if(!empty($team_loc_ids)){
				$team_loc_ids=implode(",",$team_loc_ids);
			}else{
				$team_loc_ids=NULL;
			}
		}else{
			if(!isset($team_loc_ids)){
				$team_loc_ids=$team_loc_ids;
			}else{
				$team_loc_ids=NULL;
			}
		}
		//echo $team_loc_ids;die;
		$past_due_data = array();
        $data = array();
        $line1 = array();
        $line2 = array();
        $yaxislbl = array();
		$status_val_pd=array();
		$status_val_active=array();
		$past_due_all=$active_all=array();
        if($team_id != 0 && $team_id != ''){
			
			if($type=='team') {
				$past_due_all=(new Tasks)->getPastDueTeamTasksByStatusGroup($team_id, "", $team_loc_ids);
				$active_all=(new Tasks)->getPastDueTeamTasksByStatusGroup($team_id, "", $team_loc_ids, "active");
			}else{
				$past_due_all=(new Tasks)->getPastDueTeamLocTasksByStatusGroup($team_id,$team_loc, "");
				$active_all=(new Tasks)->getPastDueTeamLocTasksByStatusGroup($team_id,$team_loc, "", "active");
			}
			if(!empty($past_due_all)) {
				foreach($past_due_all as $pddata) {
					if(isset($status_val_pd[$pddata->task_status]))
						$status_val_pd[$pddata->task_status]+=$pddata->cnttasks;
					else
						$status_val_pd[$pddata->task_status]=$pddata->cnttasks;
				}
			}
			if(!empty($active_all)) {
				foreach($active_all as $adata) {
					if(isset($status_val_active[$adata->task_status]))
						$status_val_active[$adata->task_status]+=$adata->cnttasks;
					else
						$status_val_active[$adata->task_status]=$adata->cnttasks;
				}
			}

			foreach ($status as $k => $v) {
				/*if($type=='team_loc')
				{
					$past_due_count = (new Tasks)->getPastDueTeamLocTasksByStatus($team_id,$team_loc, $k);
					$active_count = (new Tasks)->getPastDueTeamLocTasksByStatus($team_id,$team_loc, $k, "active");
				}
				else
				{
					$past_due_count = (new Tasks)->getPastDueTeamTasksByStatus($team_id, $k, $team_loc_ids);
					$active_count = (new Tasks)->getPastDueTeamTasksByStatus($team_id, $k, $team_loc_ids, "active");
				}
				*/
				$past_due_count = 0;
				$active_count = 0;
				if(isset($status_val_pd[$k]))
					$past_due_count = $status_val_pd[$k];

				if(isset($status_val_active[$k]))
					$active_count = $status_val_active[$k];	

				$line1[] = intval($past_due_count);
				$line2[] = intval($active_count);
				$total[] = intval($active_count) +  intval($past_due_count);
				$yaxislbl[] = $v;
				$data['past_due'][$k]['y'] = intval($past_due_count);
				$data['active'][$k]['y'] = intval($active_count);
			}
	    }
	    
	    echo json_encode($data);
        die;
	 }
	 
	 /*
	  * Get the Project Priority Graph of Team 
	  * */
	  public function actionGetprojectprioritychartdata()
	  {
			$team_loc = Yii::$app->request->post('team_loc',0);
			$team_id = Yii::$app->request->post('team_id',0);
			$type = Yii::$app->request->post('type','team_loc');
			$userId = Yii::$app->user->identity->id;
			$roleId = Yii::$app->user->identity->role_id;
			$priority_data = array();	
			if($type == 'team'){
				/*$limit_sql="";
				if(isset($_REQUEST['firstload'])  && $_REQUEST['firstload'] == 1){
					$mssql="OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY;";
        			$mysql="LIMIT $limit OFFSET 0";
					if(Yii::$app->db->driverName == 'mysql') {
						$limit_sql=$mysql;
					} else {
						$limit_sql=$mssql;
					}
				}
				if($roleId!=0){
					$data_sql = "SELECT distinct tbl_project_security.team_loc FROM tbl_tasks_teams INNER JOIN tbl_project_security ON tbl_tasks_teams.team_id = tbl_project_security.team_id INNER JOIN tbl_tasks ON tbl_tasks_teams.task_id = tbl_tasks.id INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_project_security.team_loc WHERE tbl_project_security.user_id= :user_id AND tbl_project_security.team_id= :team_id AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close = 0 AND tbl_task_instruct.isactive=1 AND tbl_teamlocation_master.remove=0 order by tbl_project_security.team_loc $limit_sql";
					$params = array(':team_id'=>$team_id,':user_id'=>$userId);
				}else{
					$data_sql = "SELECT distinct tbl_team_locs.team_loc FROM tbl_tasks_teams INNER JOIN tbl_team_locs ON tbl_tasks_teams.team_id = tbl_team_locs.team_id INNER JOIN tbl_tasks ON tbl_tasks_teams.task_id = tbl_tasks.id INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team_locs.team_id= :team_id AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close = 0 AND tbl_task_instruct.isactive=1 AND tbl_teamlocation_master.remove=0 order by tbl_team_locs.team_loc $limit_sql";
					$params = array(':team_id'=>$team_id);
				}*/
				$where = "select team_loc from tbl_team_locs where team_id=".$team_id;
				if($roleId!=0){
					$where = "select team_loc from tbl_project_security where user_id = ".$userId." AND team_id = ".$team_id;
				}
				$data_sql = "SELECT DISTINCT master.id as team_loc FROM tbl_teamlocation_master as master INNER JOIN tbl_tasks_teams as team ON master.id = team.team_loc INNER JOIN tbl_tasks as task ON task.id = team.task_id WHERE team.team_id = $team_id AND team.team_loc IN ($where) AND task.task_closed = 0 AND task.task_cancel = 0;";	
				$data = \Yii::$app->db->createCommand($data_sql,$params)->queryAll();	
				$team_loc_ids="";
				$casechart = 0;
				if(!empty($data)) {
					foreach ($data as $val) {
						if($team_loc_ids==""){$team_loc_ids=$val['team_loc'];}else{$team_loc_ids.=",".$val['team_loc'];}
						if(isset($_REQUEST['firstload'])  && $_REQUEST['firstload'] == 1){
							$team_loc_ids = $val['team_loc'];
							break;
						}
					}
					if($team_loc_ids==""){
						$team_loc_ids=0;
					}
					$priority_data = (new Tasks)->getTeamProjectPriority($team_id,$team_loc_ids,$userId);
				} else {
					$data_sql = "SELECT id, priority, priority_order FROM tbl_priority_project WHERE remove=0";
					$data = \Yii::$app->db->createCommand($data_sql)->queryAll();
					if(!empty($data)){
						foreach ($data as $val){
							$priority_data[] = array(
								'task_priority' => 0,
								'cnttasks' => 0,
								'id' => $val['id'],
								'priority' => $val['priority'],
								'priority_order' => $val['priority_order'],
							);
						}
					}
				}
			}else{
				$priority_data = (new Tasks)->getTeamLocProjectPriority($team_id,$team_loc,$userId);
			}

			$sort_data = array();
			$priority_name = array();
			foreach($priority_data as $key => $val) {
				$count = round($val['cnttasks']);
				$porder = round($val['priority_order']);
				$priority = $val['priority'];
				$id = round($val['id']);
				
				if(in_array($priority,$priority_name)) {
					$key1=array_search($priority,$priority_name);
					$sort_data[$key1][0]= $sort_data[$key1][0] + $count;
				} else {
					$sort_data[$porder] = array(
						$count,
						$priority,
						$id,
					);
				}
				$priority_name[$porder]=$priority;
			}
			
			$finalarr = array_reverse($sort_data);
			echo json_encode($finalarr);
			die;
	  }
    /**
     * Displays a single Team model.
     * @param integer $id
     * @return mixed
     */
   

    /**
     * Creates a new Team model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Team();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Team Uncomment Clear functionality
     * @param teamId
     * @return
     */
     public function actionUpdateCommentStatus()
     {
		$teamId = Yii::$app->request->post();
		$term = '';
		$comment_data = (new Team)->getTeamSerachGoogled($term, $teamId['teamId']);
		
		// Comment Data
		if(!empty($comment_data)){
    		$comments_rows=array();
    		foreach ($comment_data as $comment){
				$commentsAttr = array();
				$commentsAttr['comment_id']=$comment['id'];
				$commentsAttr['user_id']=Yii::$app->user->identity->id;
				$comments_rows[] = $commentsAttr; 
    		}
    		
    		if(!empty($comments_rows)){
    			$columns = (new CommentsRead)->attributes();
    			unset($columns[array_search('Id',$columns)]);
    			Yii::$app->db->createCommand()->batchInsert(CommentsRead::tableName(), $columns, $comments_rows)->execute();
    		}
    	}
    	
    	// End Comment Data
		// die();
	 }

    /**
     * Updates an existing Team model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Team model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Team the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Team::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
