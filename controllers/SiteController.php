<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Options;
use app\models\User;
use app\models\CaseType;
use app\models\TasksUnits;
use app\models\search\CaseTypeSearch;
use app\models\ContactForm;
use app\models\Settings;
use yii\data\ActiveDataProvider;
use yii\web\Session;
use app\models\ActivityLog;
use app\models\Role;
use app\models\UserLog;
use app\models\Tasks;
use app\models\search\UnitMasterSearch;
use app\models\search\TasksUnitsSearch;
use app\models\UnitMaster;
use app\models\Unit;
use app\models\TeamserviceSlaBusinessHours;
use app\models\TeamserviceSlaHolidays;
use app\models\EmailCron;
use app\models\Mydocument;
use app\models\Servicetask;
use Edvlerblog\Ldap;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;



use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Shared_Font;
use PHPExcel_Worksheet_Drawing;

$action=Yii::$app->controller->action->id;

class SiteController extends Controller
{

	public function beforeAction($action) {
		//Yii::$app->user->login(User::findByUsrUsername('sysadmin'), 3600*24*30);
		if (!Yii::$app->user->isGuest && ((!(new User)->checkAccess(1) && $action->id == 'index') || (!(new User)->checkAccess(8) && $action->id == 'administration'))) {/* 38 */
		
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
			if($redirect_method[0]!="")
				return $this->redirect($redirect_method);
			else
				return true;	

			//throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
		}

		return parent::beforeAction($action);
	}


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout','login'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }



    public function actions()
    {
    	return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionOmtest()
    {

    }

	public function actionDatatables(){
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$draw = Yii::$app->request->getQueryParam('draw');//$_REQUEST['page']=2;
 		$query = CaseType::find()->where(['remove'=>0])->orderBy(['case_type_name'=>SORT_ASC]);
 		$query->offset(Yii::$app->request->getQueryParam('start', 0))
 		->limit(Yii::$app->request->getQueryParam('length', -1))
 		->all();
		$dataProvider = new ActiveDataProvider(['query' => $query,'pagination'=>['pageSize'=>10]]);
 		try {
			$response = [
					'draw' => (int)$draw,
					'recordsTotal' => (int)CaseType::find()->where(['remove'=>0])->orderBy(['case_type_name'=>SORT_ASC])->count(),
					'recordsFiltered' => (int)$dataProvider->getTotalCount(),
					'data' => $dataProvider->getModels(),
			];
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
		return $response;
	}
    public function actionIndex(){
		//echo (new User())->decryptPassword("ZLv5AY+xaObf8yH7vw2RWSQ5ZX0Y1NsjBMRfE49VkRo=");die;
		$user_id = Yii::$app->user->identity->id;

		$roleId = Yii::$app->user->identity->role_id;

    	$this->layout = 'main';

    	$stats=array(array('id'=>2,'name'=>'Pending Tasks'),array('id'=>3,'name'=>'Working Tasks'),array('id'=>4,'name'=>'Working ToDos'),array('id'=>1,'name'=>'Not Started Tasks'));
    	$casearrayDataProvider =new ArrayDataProvider([
			'allModels' => $stats,
		]);

    	$stats=array(array('id'=>'teamassignment_2','name'=>'Pending Tasks'),array('id'=>'teamassignment_3','name'=>'Working Tasks'),array('id'=>'teamassignment_4','name'=>'Working ToDos'),array('id'=>'teamassignment_1','name'=>'Not Started Tasks'));

		$TeamarrayDataProvider = new ArrayDataProvider(['allModels' => $stats]);
		// Start: My Case / My Team Assignments By Status 
		$userId = $user_id;
		$sqlclients = "SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= :userId and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id";
    	$sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=".$userId." and tbl_project_security.team_id=1 group by tbl_project_security.team_id";
		$where = ""; $select = ""; $join = "";$order="";
		$settings_info = Settings::find()->where("field = 'project_sort'")->one();
		if ($settings_info->fieldvalue == '0') {
			$order.= "project.priority_order ASC,tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
		} else if ($settings_info->fieldvalue == '1') {
			$order.= "tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
		} else if ($settings_info->fieldvalue == '2') {
			$order.= "tunits.task_id DESC";
		} else if($settings_info->fieldvalue == '3'){
			$order.= "pteam.id ASC, project.priority_order ASC,tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
		}
    	// Pending tasks 
		$Pendingservice = (new TasksUnits)->getTaskPendingServiceTaskCount();
		$taskstatus['pending'] = $Pendingservice[0]['cntpendingtasks'];
		// working tasks 
			$selectabc = '';
			$taskduedatejoin = '';
				$selectabc = ', A.task_date_time';
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				if (Yii::$app->db->driverName == 'mysql') {
					$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
				} else {
					//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
					$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
				}
				$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tinstruct.id = A.id ";
			
				$sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=$userId and tbl_project_security.team_id!=0 group by tbl_project_security.team_id";

				$where.= " AND tunits.unit_status NOT IN (0,4)";
				$where.= " AND tunits.unit_assigned_to = ".$userId." AND tunits.unit_status !=4 AND (tunits.unit_assigned_to IS NOT NULL OR tunits.unit_assigned_to != '' OR tunits.unit_assigned_to != '[]')";
				Yii::$app->user->identity->id;
				$sql = "SELECT COUNT(*) AS cntworkingtasks  FROM tbl_tasks_units as tunits
				INNER JOIN tbl_task_instruct as tinstruct ON tunits.task_instruct_id = tinstruct.id
				$taskduedatejoin
				INNER JOIN tbl_tasks as task ON tinstruct.task_id = task.id
				INNER JOIN tbl_client_case as ccase ON task.client_case_id = ccase.id
				INNER JOIN tbl_client as client ON ccase.client_id = client.id
				LEFT JOIN tbl_priority_project as project ON tinstruct.task_priority = project.id
				INNER JOIN tbl_teamlocation_master as location ON tunits.team_loc = location.id
				INNER JOIN tbl_servicetask as service ON tunits.servicetask_id = service.id
				INNER JOIN tbl_team as team ON tunits.team_id = team.id
				$join where task.task_status !=4 AND task.task_closed != 1 AND task.task_cancel != 1 AND ccase.is_close != 1 AND (
				(task.client_case_id IN (SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= $userId and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id) AND tunits.team_id IN (1)) OR (tunits.servicetask_id IN (SELECT t.id FROM tbl_servicetask t
				INNER JOIN tbl_teamservice ON t.teamservice_id = tbl_teamservice.id
		WHERE tbl_teamservice.teamid IN ($sqlteams) AND tbl_teamservice.teamid NOT IN (1)))) $where
				";
				/*$sql = "SELECT COUNT(*) AS cntworkingtasks  FROM tbl_tasks_units as tunits
				INNER JOIN tbl_task_instruct as tinstruct ON tunits.task_instruct_id = tinstruct.id
				$taskduedatejoin
				INNER JOIN tbl_tasks as task ON tinstruct.task_id = task.id
				INNER JOIN tbl_client_case as ccase ON task.client_case_id = ccase.id
				INNER JOIN tbl_client as client ON ccase.client_id = client.id
				LEFT JOIN tbl_priority_project as project ON tinstruct.task_priority = project.id
				INNER JOIN tbl_teamlocation_master as location ON tunits.team_loc = location.id
				INNER JOIN tbl_servicetask as service ON tunits.servicetask_id = service.id
				INNER JOIN tbl_team as team ON tunits.team_id = team.id
				LEFT JOIN tbl_priority_team as pteam ON task.team_priority = pteam.id
				".$join." where task.task_status !=4 AND task.task_closed != 1 AND task.task_cancel != 1 AND ccase.is_close != 1 AND ((task.client_case_id IN (SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= $userId and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id) AND tunits.team_id IN (1)) OR (tunits.servicetask_id IN (SELECT t.id FROM tbl_servicetask t
		INNER JOIN tbl_teamservice ON t.teamservice_id = tbl_teamservice.id
		WHERE tbl_teamservice.teamid IN (".$sqlteams.") AND tbl_teamservice.teamid NOT IN (1)))) ".$where;*/
	
				$params = [':userId'=>$userId];
	
				$teamtaskunitdata2 = \Yii::$app->db->createCommand($sql)->queryAll();
				Yii::$app->user->identity->id;
				//echo "<pre>",print_r($teamtaskunitdata2),"</pre>";die;
	
				$is_accessible_submodule_tracktask = 1;
				$taskstatus['workingtasks'] = $teamtaskunitdata2[0]['cntworkingtasks'];
		
		
			// Working Todos 


				$join.= "LEFT JOIN tbl_tasks_units_todos as todos ON todos.tasks_unit_id = tunits.id LEFT JOIN tbl_todo_cats as cats ON todos.todo_cat_id = cats.id";
				$select.= ",todos.todo,todos.modified,todos.complete,cats.todo_desc,cats.todo_cat";
				///$where = " AND tunits.unit_status NOT IN (0,4)";
				$where = " AND tunits.unit_status !=4 AND (tunits.unit_assigned_to IS NOT NULL OR tunits.unit_assigned_to != '' OR tunits.unit_assigned_to != '[]')";
				$where.= " AND todos.complete !=1 AND todos.assigned =".$userId;
			
			$sql = "SELECT COUNT(*) as cntworkingtodos FROM tbl_tasks_units as tunits
			INNER JOIN tbl_task_instruct as tinstruct ON tunits.task_instruct_id = tinstruct.id
			$taskduedatejoin
			INNER JOIN tbl_tasks as task ON tinstruct.task_id = task.id
			INNER JOIN tbl_client_case as ccase ON task.client_case_id = ccase.id
			INNER JOIN tbl_client as client ON ccase.client_id = client.id
			LEFT JOIN tbl_priority_project as project ON tinstruct.task_priority = project.id
			INNER JOIN tbl_teamlocation_master as location ON tunits.team_loc = location.id
			INNER JOIN tbl_servicetask as service ON tunits.servicetask_id = service.id
			INNER JOIN tbl_team as team ON tunits.team_id = team.id
			LEFT JOIN tbl_priority_team as pteam ON task.team_priority = pteam.id
			$join where task.task_status !=4 AND task.task_closed != 1 AND task.task_cancel != 1 AND ccase.is_close != 1 AND ((task.client_case_id IN (SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= $userId and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id) AND tunits.team_id IN (1)) OR tunits.servicetask_id IN (SELECT t.id FROM tbl_servicetask t
			INNER JOIN tbl_teamservice ON t.teamservice_id = tbl_teamservice.id
			WHERE tbl_teamservice.teamid IN ($sqlteams) AND tbl_teamservice.teamid NOT IN (1))) $where";
			//echo $sql;die;

			$params = [':userId'=>$userId];

			$teamtaskunitdata2 = \Yii::$app->db->createCommand($sql)->queryAll();

			$is_accessible_submodule_tracktask = 1;
			$taskstatus['workingtodos'] = $teamtaskunitdata2[0]['cntworkingtodos'];
		

			// Not started tasks 
			$Notstartedtask = (new TasksUnits)->getTaskNotstartedTaskCount();
			$taskstatus['notstarted'] = $Notstartedtask[0]['cntnotstarted'];

		// End: My Case / My Team  Assignments By Status 

		// Assigned tasks by Client
		$sql = "SELECT tbl_client.client_name, COUNT(*) as cnttasksbyclient FROM tbl_client LEFT JOIN tbl_client_case ON tbl_client_case.client_id = tbl_client.id LEFT JOIN tbl_tasks ON tbl_tasks.client_case_id = tbl_client_case.id LEFT JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id LEFT JOIN tbl_tasks_units ON tbl_tasks_units.task_id = tbl_tasks.id WHERE 1=1 AND (tbl_tasks.task_closed = 0) AND (tbl_tasks.task_cancel = 0) AND (tbl_task_instruct.isactive = 1) AND (tbl_tasks_units.unit_assigned_to = :userId) GROUP BY tbl_client.id, tbl_client.client_name"; 
		$params = [':userId'=>$userId];
		$assignedtasksbyclient = \Yii::$app->db->createCommand($sql, $params)->queryAll();

		/*$sql = "SELECT ( SELECT priority FROM tbl_priority_project WHERE tbl_task_instruct.task_priority=tbl_priority_project.id AND tbl_priority_project.remove=0) as task_priority, COUNT(*) as cnttasksbypriority FROM tbl_tasks LEFT JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id LEFT JOIN tbl_tasks_units ON tbl_tasks_units.task_id = tbl_tasks.id WHERE 1=1 AND (tbl_tasks.task_closed = 0) AND (tbl_tasks.task_cancel = 0) AND (tbl_task_instruct.isactive = 1) AND (tbl_tasks_units.unit_assigned_to = :userId) GROUP BY task_priority";
		$params = [':userId'=>$userId];
		$assignedtasksbypriority = \Yii::$app->db->createCommand($sql, $params)->queryAll();*/

		$query = TasksUnits::find();
		$query->select(['tbl_priority_project.priority','COUNT(tbl_tasks_units.id) AS cnttasksbypriority']);
		$query->joinWith(['tasks'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $query){
				$query->joinWith(['client'],false);
			}],false);
		}],false);
		$query->joinWith(['taskInstruct'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['taskPriority','taskPriority'],false);
		}],false);
		$query->joinWith(['servicetask']);
		
		$query->where([
			'tbl_tasks_units.unit_assigned_to' => $userId,
			'isactive'=>1,
			'tbl_tasks.task_closed' => 0,
			'tbl_tasks.task_cancel' => 0,
			'tbl_client_case.is_close' => 0
			]);
		$query->andWhere('tbl_tasks_units.unit_status!=4');
		$query->groupBy('tbl_priority_project.priority');
		
		$assignedtasksbypriority = $query->all();
		//echo "<pre>";print_r($assignedtasksbypriority);die;

		$sql = "SELECT tbl_servicetask.service_task, COUNT(*) as cnttasksbyworkflow FROM tbl_tasks LEFT JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id LEFT JOIN tbl_tasks_units ON tbl_tasks_units.task_id = tbl_tasks.id INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id WHERE 1=1 AND (tbl_tasks_units.unit_assigned_to = :userId) AND (tbl_tasks.task_closed = 0) AND (tbl_tasks.task_cancel = 0) AND (tbl_task_instruct.isactive = 1) GROUP BY tbl_servicetask.service_task";
		$params = [':userId'=>$userId];
		$assignedtasksbyworkflow = \Yii::$app->db->createCommand($sql, $params)->queryAll();


		/*All Active Task Grid*/
		$unit_assigned_user_name = $workflow_selected = $client_case_selected = $todo_client_case_selected = array();
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
                $workflow_selected = ArrayHelper::htmlDecode(ArrayHelper::map(Servicetask::find()->select(['tbl_servicetask.id','service_task'])->where(['tbl_servicetask.id' => $service_ids])->all(), function($model){ return $model->service_task;},function($model){return $model->service_task;}));
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
		$searchModel = new TasksUnitsSearch();
		$params['grid_id']='dynagrid-myactive_tasks';
		Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->searchMyActiveTasks(Yii::$app->request->queryParams);
		$filter_type = \app\models\User::getFilterType(['tbl_tasks_units.task_id', 'tbl_tasks_units.id', 'tbl_tasks_units.servicetask_id', 'tbl_tasks_units.unit_status', 'tbl_tasks_units.unit_assigned_to','tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_priority','tbl_task_instruct.project_name','tbl_tasks.client_case_id', 'tbl_client_case.client_id'], ['tbl_tasks_units','tbl_task_instruct','tbl_tasks','tbl_client_case']);
		$config = ['unit_status' => ['All' => 'All', '0' => 'Not Started', '7'=>'Pending', '1' => 'Started', '2' => 'Paused', '3' => 'On Hold']];
		$config_widget_options = [
            'servicetask_id' => [
                'initValueText' => $workflow_selected,
                'pluginEvents' => ["select2:select" => 'function(evt) {var abc = evt.params.data.label;$(document).on("pjax:end",   function(xhr, textStatus, options) {$("#select2-tasksunitssearch-workflow_task-container").html(abc); });}', "select2:open" => 'function(evt) {$("#select2-tasksunitssearch-workflow_task-container").remove(); }'],
                'field_alais' => 'workflow_task',
            ],
            'client_case_id' => [
                'initValueText' => $client_case_selected
            ],
            'client_id' => [
                'initValueText' => $clients_selected
            ]
        ];
		$filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['site/ajax-filter']), $config, $config_widget_options,Yii::$app->request->queryParams, 'myactive_tasks');
			/*All Working Todos Grid*/
			if (isset($params['TasksUnitsSearch']['todo_client_case_id']) && !empty($params['TasksUnitsSearch']['todo_client_case_id'])) {
				$todo_client_case_selected = (new User)->getSelectedGridCases($params['TasksUnitsSearch']['todo_client_case_id'], 'All');
				if ($todo_client_case_selected == 'ALL') {
					unset($params['TasksUnitsSearch']['todo_client_case_id']);
					$todo_client_case_selected = array();
				}
			}
			if (isset($params['TasksUnitsSearch']['todo_client_id']) && !empty($params['TasksUnitsSearch']['todo_client_id'])) {
				$todo_clients_selected = (new User)->getSelectedGridClients($params['TasksUnitsSearch']['todo_client_id'], 'All');
				if ($todo_clients_selected == 'ALL') {
					unset($params['TasksUnitsSearch']['todo_client_id']);
					$todo_clients_selected = array();
				}
			}
				$params['grid_id']='dynagrid-myworking_todos';
				Yii::$app->request->queryParams +=$params;
				$todoDataProvider = $searchModel->searchMyWorkingTodos(Yii::$app->request->queryParams);
				$todofilter_type = \app\models\User::getFilterType(
					[
					'tbl_tasks_units.id',
					'tbl_task_instruct.project_name',
					'tbl_tasks.client_case_id',
					'tbl_client_case.client_id',
					'tbl_tasks_units_todos.todo',
					'tbl_tasks_units_todos.modified',
					'tbl_todo_cats.todo_cat'
					], 
					[
						'tbl_tasks_units_todos',
						'tbl_todo_cats','tbl_tasks_units',
						'tbl_task_instruct',
						'tbl_tasks',
						'tbl_client_case'
					]
				);
				$config=[];
				$config_widget_options = [
					'servicetask_id' => [
						'initValueText' => $workflow_selected,
						'pluginEvents' => ["select2:select" => 'function(evt) {var abc = evt.params.data.label;$(document).on("pjax:end",   function(xhr, textStatus, options) {$("#select2-tasksunitssearch-workflow_task-container").html(abc); });}', "select2:open" => 'function(evt) {$("#select2-tasksunitssearch-workflow_task-container").remove(); }'],
						'field_alais' => 'workflow_task',
					],
					'client_case_id' => [
						'initValueText' => $todo_client_case_selected
					],
					'client_id' => [
						'initValueText' => $todo_clients_selected
					]
				];
				$todofilterWidgetOption = \app\models\User::getFilterWidgetOption($todofilter_type, Url::toRoute(['site/ajax-todofilter']), $config, $config_widget_options,Yii::$app->request->queryParams, 'myworking_todos');
			/*All Working Todos Grid*/

		/*All Active Task Grid*/


    	return $this->render('index',['params'=>Yii::$app->request->queryParams,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'searchModel'=>$searchModel,'dataProvider'=>$dataProvider,'todofilterWidgetOption'=>$todofilterWidgetOption,'todofilter_type'=>$todofilter_type,'todoDataProvider'=>$todoDataProvider,'casearrayDataProvider'=>$casearrayDataProvider,'TeamarrayDataProvider'=>$TeamarrayDataProvider, 'taskstatus' => $taskstatus, 'assignedtasksbyclient' => $assignedtasksbyclient, 'assignedtasksbypriority' => $assignedtasksbypriority, 'assignedtasksbyworkflow' => $assignedtasksbyworkflow]);
	}

	/*
     * For Ajax Filter of TeamTasks Grid
     * */
    public function actionAjaxTodofilter(){
		$bodyparams=Yii::$app->request->bodyParams;
        Yii::$app->request->queryParams +=$bodyparams;
        //echo "<pre>",print_r($bodyparams),"</prE>";
        //die;
		$searchModel = new TasksUnitsSearch();
		$qparams = Yii::$app->request->queryParams;
		$params = array();
		//$dataProvider = $searchModel->searchFilter($qparams,$params);
		$params = array_merge($qparams, Yii::$app->request->bodyParams,$params);
	    $dataProvider = $searchModel->searchMyWorkingTodosFilter($params);
	    if($params['field'] == 'client_id' || $params['field'] == 'client_case_id') {
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
     * For Ajax Filter of TeamTasks Grid
     * */
    public function actionAjaxFilter(){
		$bodyparams=Yii::$app->request->bodyParams;
        Yii::$app->request->queryParams +=$bodyparams;
        //echo "<pre>",print_r($bodyparams),"</prE>";
        //die;
		$searchModel = new TasksUnitsSearch();
		$qparams = Yii::$app->request->queryParams;
		$params = array();
		//$dataProvider = $searchModel->searchFilter($qparams,$params);
		$params = array_merge($qparams, Yii::$app->request->bodyParams,$params);
	    $dataProvider = $searchModel->searchMyActiveTasksFilter($params);
	    if($params['field'] == 'workflow_task' || $params['field'] == 'client_id' || $params['field'] == 'client_case_id' || $params['field'] == 'task_id') {
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
     * Pass Ajax For Activity Log
     * */
    public function actionAjaxprocessactivity()
    {
    	$offset = Yii::$app->request->post('offset',0);

		$userId = Yii::$app->user->identity->id;

		$roleId = Yii::$app->user->identity->role_id;

		$all_usre_access_info = array();

		$role_ids = array($roleId => $roleId);

		$role_info = Role::find()->select(['role_type'])->where("id = ".$roleId)->one();

		$all_usre_access_info[$userId] = array();
		//(new User)->getSecurityListuserwise($userId);

		$dateTz =  (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), "UTC", $_SESSION["usrTZ"], "YMD");

		$dateTzS  = $dateTz . " 00:00:01";
        $dateTzE  = $dateTz . " 23:59:59";
    	$posts = ActivityLog::find()->with('user')->select(['id','date_time','activity_name','user_id','username','origination','activity_type','activity_module_id','task_cancel_reason'])->where("date_time >='" . $dateTzS . "' and date_time<='" . $dateTzE . "'")->orderBy('tbl_activity_log.id DESC')->offset($offset)->limit(100)->all();

		//echo "<pre>"; print_r($posts); exit;
    	$process_activity = (new ActivityLog)->processActivity($posts, $role_info, $all_usre_access_info, $userId,$offset);
		$total_post  = count($posts);
		//echo "<pre>"; print_r($process_activity);exit;
        $this->renderPartial('process-activity',['total_post'=>$total_post,'process_activity'=>$process_activity,'posts'=>$posts], false, true);
    }
    public function actionChangeForcePassword($id){

    	if (!\Yii::$app->user->isGuest) {
    		return $this->goHome();
    	}
    	$this->layout = 'login';
    	$id = base64_decode($id);
    	$model = User::findOne($id);

    	$Settingdata = Settings::find()->select(['fieldtext'])->where(['field'=>'loginpage'])->one();
        $Settingdatabottom = Settings::find()->select(['fieldtext'])->where(['field'=>'loginpage_bottom'])->one();
    	if ($model->load(Yii::$app->request->post())){
    		$model->usr_pass = (new User())->hashPassword($model->usr_pass);
    		$model->confirm_password = $model->usr_pass;
    		$model->last_pass_change = date('Y-m-d H:i:s');
    		$model->modified_by=$id;
    		if($model->save()){
	    		return $this->redirect(array(
	    				'site/login',

	    		));
    		}else{
    			return $this->render('change-force-password', [
    					'model' => $model,
    					'id'   => base64_encode($id),
    					'Settingdata'=>$Settingdata,
                                        'Settingdatabottom' =>  $Settingdatabottom
    			]);
    		}
    	}
    	return $this->render('change-force-password', [
    			'model' => $model,
    			'id'   => base64_encode($id),
    			'Settingdata'=>$Settingdata,
                        'Settingdatabottom' =>  $Settingdatabottom
    	]);
    }
    public function actionLogin(){
		//echo (new User())->decryptPassword("ZDVAzXaaF8/jp75CSXTSlj0QXmawCG4X17l4WEFNUNo=");die;
    	$this->layout = 'login';
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()){
        	// echo "<pre>",print_r(Yii::$app->request->post()); die();
        	$data = Yii::$app->user->identity;
		if($data->usr_type==3) {
		    User::updateAll(['usr_pass'=>NULL],['id'=>Yii::$app->user->identity->id]);
		}
        	if($data->usr_type!=3) {
	        	$user_force_change_pass=$data->change_pass_after;
	        	$user_last_change_pass=$data->last_pass_change;
	        	$user_last_change_pass=date('d-m-Y',strtotime($user_last_change_pass));
	        	$curdate=date('d-m-Y');
	        	$days = (strtotime($curdate)-strtotime($user_last_change_pass)) / (60 * 60 * 24);
        	 	if(($days)>=$user_force_change_pass) {
	        		$user_id=base64_encode(Yii::$app->user->identity->id);
	        		Yii::$app->user->logout();
	        		$session = Yii::$app->session;
	        		$session->destroy();
	        		return $this->redirect(array('site/change-force-password','id'=>$user_id));
	        	}
        	}
        	$user_model = new User();
        	$session = new Session;
			$session->open();
			$session['myaccess'] = $user_model->getAllSecurityAccess(Yii::$app->user->identity->id);
			$session['role'] 	 = Yii::$app->user->identity->role;
			$session['options']  = Yii::$app->user->identity->options;
			$tz                  = "UTC";
			$options_data        = Options::find()->select('timezone_id')->where('user_id = '.Yii::$app->user->identity->id)->one();
			if (isset($options_data->timezone_id) && $options_data->timezone_id != "") {
				$tz = $options_data->timezone_id;
			}
			$_SESSION['usrTZ'] = $tz;

			/* IRT 562 Changes : Add Field(login_status,fail_reason) */
			$usrLog               = new UserLog();
			$usrLog->user_id      = Yii::$app->user->identity->id;
			$usrLog->login        = date('Y-m-d H:i:s');
			$usrLog->logout       = "";
			$usrLog->login_status = 0;
          	$usrLog->ses_duration = "";
			$usrLog->save();

		    //echo "<pre>",print_r($session['myaccess']); die();
		    /* IRT 31 Default landing page */
			$def_land_page = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->default_landing_page;

			$businessHours =  TeamserviceSlaBusinessHours::find()->one();
			$workingDays = [1,1,1,1,1,1,1];
			if($businessHours->workingdays!== null){
				$workingdaysAr = json_decode($businessHours->workingdays,true);
				if(in_array(1,$workingdaysAr))
					$workingDays[0] = 0;
				else
					$workingDays[0] = 1;
				if(in_array(2,$workingdaysAr))
					$workingDays[1] = 0;
				else
					$workingDays[1] = 1;
				if(in_array(3,$workingdaysAr))
					$workingDays[2] = 0;
				else
					$workingDays[2] = 1;
				if(in_array(4,$workingdaysAr))
					$workingDays[3] = 0;
				else
					$workingDays[3] = 1;
				if(in_array(5,$workingdaysAr))
					$workingDays[4] = 0;
				else
					$workingDays[4] = 1;
				if(in_array(6,$workingdaysAr))
					$workingDays[5] = 0;
				else
					$workingDays[5] = 1;
				if(in_array(7,$workingdaysAr))
					$workingDays[6] = 0;
				else
					$workingDays[6] = 1;
			}

			$session['businessStartTime'] = $businessHours->start_time;
			$session['businessEndTime'] = $businessHours->end_time;
			$session['businessWorkinghours'] = $businessHours->workinghours;
			$session['businessDays'] = $workingDays;

			$businessHolidays = ArrayHelper::map(TeamserviceSlaHolidays::find()->select(['id','holidaydate'])->all(),'id','holidaydate');
			$session['businessHolidays'] = $businessHolidays;

			// echo "<pre>",print_r($session); die();
			$res = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
			if($def_land_page=='') $def_land_page = '';
			$redirect_method = array();
			if($def_land_page==1){ // Show My Assignments
				if((new User)->checkAccess(1)) {
					$redirect_method[] = 'site/index';
				}
			} else if ($def_land_page==2){ // Show Media
				if((new User)->checkAccess(3)) {
					$redirect_method[] = 'media/index';
				}
			} else if ($def_land_page==3){ // Show My Teams
				if((new User)->checkAccess(4)) {
					$redirect_method[] = 'mycase/index';
				}
			} else if ($def_land_page==4){ // Show My Cases
				if((new User)->checkAccess(5)) {
					$redirect_method[] = 'team/index';
				}
			} else if ($def_land_page==5){ // Show Global Projects
				if((new User)->checkAccess(2)) {
					$redirect_method[] = 'global-projects/index';
				}
			} else if ($def_land_page==6){ // Show Billing
				if((new User)->checkAccess(7)) {
					$redirect_method[] = 'billing-pricelist/internal-team-pricing';
				}
			} else if ($def_land_page==7){ // Show Report
				if((new User)->checkAccess(11)) {
					$redirect_method[] = 'custom-report/index';
				}
			} else if ($def_land_page==8){ // Show Administrator
				if((new User)->checkAccess(8)) {
					$redirect_method[] =  'site/administration';
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
        }

        $Settingdata = Settings::find()->select(['fieldtext'])->where(['field'=>'loginpage'])->one();
        $Settingdatabottom = Settings::find()->select(['fieldtext'])->where(['field'=>'loginpage_bottom'])->one();
        $SettingLdap = Settings::find()->select(['id'])->where(['field'=>'active_dir'])->one();

        $users_length = (new User)->getTableFieldLimit('tbl_user');
        return $this->render('login', [
            'model' => $model,
        	'Settingdata'		=>	$Settingdata,
        	'SettingLdap'		=>	$SettingLdap,
        	'users_length'		=>	$users_length,
        	'Settingdatabottom' =>  $Settingdatabottom
        ]);
    }
    public function actionLogout(){
		$log_data = UserLog::find()->where('user_id = '.Yii::$app->user->identity->id)->limit(1)->orderBy('id DESC')->all();
		$log_id = $log_data[0]['id'];
		$model = $this->findUserLogModel($log_id);
		$model->ses_duration = (new UserLog)->dateDiff($model->login,date('Y-m-d H:i:s'));
		$model->logout = date('Y-m-d H:i:s');
		$model->save();
        Yii::$app->user->logout();
        $session = Yii::$app->session;
        $session->destroy();
		// Finally, destroy the session.
		//@session_destroy();
        return $this->redirect(['site/login']);
        //return $this->goHome();
    }
    public function actionAdministration(){
    	$this->layout = 'admin';
    	return $this->render('administration');
    }
    public function actionCasetype(){
    	if (Yii::$app->request->isAjax) {
	    	$searchModel = new CaseTypeSearch();
	    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			/*IRT 67,68,86,87,258*/
			/*IRT 96,398 */
			$filter_type=\app\models\User::getFilterType(['tbl_case_type.id','tbl_case_type.case_type_name'],['tbl_case_type']);

			$config = [];
			$config_widget_options = [];
			$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['site/ajax-case-type-filter']),$config,$config_widget_options);
			/*IRT 67,68,86,87,258*/
	    	return $this->renderAjax('casetype', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'filter_type'=>$filter_type,
				'filterWidgetOption'=>$filterWidgetOption
	    	]);
    	}else{
    		return $this->redirect(['/site/administration'],302);
    	}
    }

    public function actionSizeconversions(){
		if (Yii::$app->request->isAjax) {
	    	$unitMaster = UnitMaster::find()->joinWith('unit')->where(['tbl_unit.remove'=>0, 'tbl_unit_master.unit_type'=>1])->orderBy('unit_size')->all();
			return $this->renderAjax('sizeconversions', [
				'type' => 'Size',
				'unitMaster' => $unitMaster
	    	]);
    	}else{
    		return $this->redirect(['/site/administration'],302);
    	}
	}

	public function actionUnitsView()
	{
		$type = Yii::$app->request->get('typeconversion','');
		$unitConversionType = Yii::$app->params['unit_conversion_type'];
		if($type != ''){
			$unitType = $unitConversionType[$type];
			$unitMaster = UnitMaster::find()->joinWith('unit')->where(['tbl_unit.remove'=>0, 'tbl_unit_master.unit_type'=>$unitType])->orderBy('unit_size')->all();
			//echo "<pre>",print_r($unitMaster),"</pre>";
			return $this->renderAjax('units', [
				'type' => $type,
				'unitMaster' => $unitMaster
			]);
		}
		return false;
	}

	public function actionAddMoreUnitsView()
	{
		$type = Yii::$app->request->get('typeconversion','');
		$unitConversionType = Yii::$app->params['unit_conversion_type'];
		if($type != '') {
			$remainingUnits = ArrayHelper::map(Unit::find()->select(['id','unit_name'])->where(['remove'=>0,'default_unit'=>0])->andWhere('id NOT IN (SELECT unit_id FROM tbl_unit_master WHERE unit_type = '.$unitConversionType[$type].')')->all(), 'id', 'unit_name');
			return $this->renderPartial('addMoreUnitsView', [
				'type' => $type,
				'remainingUnits' => $remainingUnits
			]);
		}
		return false;
	}

    public function actionUpadteUnitsMaster()
    {
		$type = Yii::$app->request->get('typeconversion','100');
		$post_data = Yii::$app->request->post();
		//echo "<pre>",print_r($post_data),"</pre>";die;
		$unitConversionType = Yii::$app->params['unit_conversion_type'];
		if($type != ''){
			if(!empty($post_data)){
				foreach($post_data['UnitMaster']['unit_id'] as $key => $data){
					if($data!=''){
						$unitMaster = UnitMaster::find()->where(['unit_id'=>$data])->one();
						if(!empty($unitMaster)) {
							$unitMaster->unit_id = $data;
							$unitMaster->unit_size = $post_data['UnitMaster']['unit_size'][$key];
							$unitMaster->unit_type = $unitConversionType[$type];
							$unitMaster->unit_convert_report = 0;
							if(isset($post_data['unit_convert_report']) && $post_data['unit_convert_report']==$data){
								$unitMaster->unit_convert_report = 1;
							}
						}else{
							$unitMaster = new UnitMaster;
							$unitMaster->unit_id = $data;
							$unitMaster->unit_size = $post_data['UnitMaster']['unit_size'][$key];
							$unitMaster->unit_type = $unitConversionType[$type];
							$unitMaster->unit_convert_report = 0;
							if(isset($post_data['unit_convert_report']) && $post_data['unit_convert_report']==$data){
								$unitMaster->unit_convert_report = 1;
							}
						}
						if(!$unitMaster->save()){
							echo "<pre>",print_r($unitMaster->getErrors()),"</pre>";
							die;
						}
					}
				}
			}
			echo 'OK';die;
		}
		die;
	}

	/**
	* Delete Unit Master record
	**/
    public function actionDeleteUnitsMaster() {
		$id=Yii::$app->request->post('id',0);
		$unitMaster = UnitMaster::findOne($id);
		$unitMaster->delete();
		echo 'OK';die;
	}
    /**
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxCaseTypeFilter(){
		$params = Yii::$app->request->queryParams;
		$modelseach = CaseType::find()->select(['id','case_type_name'])->where(['remove'=>0]);
		if(isset($params['q']) && $params['q']!='')
			$modelseach->andWhere("case_type_name like '%".$params['q']."%'");

		$dataProvider = ArrayHelper::map($modelseach->all(),'id','case_type_name');
		$dataProvider = array_merge([''=>'All'],$dataProvider);
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
		//print_r($out);die;
		return json_encode($out);
	}

    /**
     * Creates a new CaseType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionAddcasetype(){
     	$model = new CaseType();
     	if ($model->load(Yii::$app->request->post()) && $model->save()) {
     		return 'OK';
     	} else {
			$case_type_length = (new User)->getTableFieldLimit('tbl_case_type');
     		return $this->renderAjax('createcasetype', [
     				'model' => $model,
     				'case_type_length' =>$case_type_length
     		]);
     	}
     }
    /**
     * Updates an existing CaseType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatecasetype($id){
    	$model = $this->findCaseTypeModel($id);
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return 'OK';
    	} else {
			$case_type_length = (new User)->getTableFieldLimit('tbl_case_type');
    		return $this->renderAjax('updatecasetype', [
    				'model' => $model,
    				'case_type_length' =>$case_type_length
    		]);
    	}
    }
    /**
     * Deletes an existing CaseType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecasetype($id){
     	$model = $this->findCaseTypeModel($id);
     	$model->remove = 1;
     	if ($model->save()) {
     		return 'OK';
     	}
     	exit;
    }
    /**
     * Deletes an selected existing CaseType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionDeleteselectedcasetype() {
    	if (isset($_POST['keylist'])) {
    		$keys = implode(",",$_POST['keylist']);
    		CaseType::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }
    /**
     * Finds the CaseType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CaseType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCaseTypeModel($id)
    {
    	if (($model = CaseType::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    public function actionOptions(){
    	$id = Yii::$app->user->identity->id;
    	$model = new Options();
    	$user_model = $this->findUserModel($id);
    	$timezones = $model->getTimezone();
    	$options_data = Options::find()->where('user_id ='.Yii::$app->user->identity->id)->one();
    	$settings_info=Settings::find()->where(['field' =>'session_timeout'])->one();


		if(isset($options_data->id) && $options_data->id!== null)
    		$model = Options::findOne($options_data->id);

    	if ($model->load(Yii::$app->request->post())){
			if(!empty(Yii::$app->request->post('User')['confirm_password'])){
				$user_model->usr_pass = $user_model->hashPassword(Yii::$app->request->post('User')['usr_pass']);
				$user_model->save(false);
		    }
			$model->save(false);
		}
    	return $this->render('/user/Options',['timezones'=>$timezones,'user_model'=>$user_model,'model'=>$model,'options_data'=>$options_data,'settings_info'=>$settings_info]);
    }

    public function actionPasswords(){
		$oldpassword = Yii::$app->request->post('old_password','');
		$user_password = Yii::$app->user->identity->usr_pass;
		$user = new User();
		$real_password = $user->decryptPassword($user_password);
		if(trim($oldpassword) == trim($real_password)){
			$output = 1;
		}else{
			$output = 0;
		}
		return $output;
		exit;
	}
	public function actionChkPasswords(){
		$id = Yii::$app->request->post('id','');
		$id = base64_decode($id);
		$user_model = $this->findUserModel($id);
		$oldpassword = Yii::$app->request->post('old_password','');
		$user_password = $user_model->usr_pass;
		$user = new User();
		$real_password = $user->decryptPassword($user_password);
		if(trim($oldpassword) == trim($real_password)){
			$output = 1;
		}else{
			$output = 0;
		}
		return $output;
		exit;
	}

	protected function findUserModel($id){
    	if (($model1 = User::findOne($id)) !== null) {
    		return $model1;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    public function actionReports(){
		$this->layout = 'report';
		return $this->render('report');
	}

	protected function findUserLogModel($id){
        if (($model = UserLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /*
     * Return the Mycases Assignments data.
     * */
    public function actionGetmycaseassignmentnew(){
		 $id = Yii::$app->request->post('expandRowKey');
		 $userId = Yii::$app->user->identity->id;

		$sqlclients = "SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= :userId and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id";
    	$sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=".$userId." and tbl_project_security.team_id=1 group by tbl_project_security.team_id";
    	$where = ""; $select = ""; $join = "";$order="";

    	if($id == 0){
		    $Pendingservice = (new TasksUnits)->getTaskPendingServiceTaskCase();
			$status = '<em class="fa fa-clock-o text-primary" title="Task Not Started"></em>';
			foreach($Pendingservice as $service => $value){
				$is_pastdue = (new Tasks)->ispastduetask($value['task_id']);
				if ($is_pastdue)
					$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
				else
					$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';

				$myteamtaskdata[]=array(
					'client_case'=>$value['client_name']." - ".$value['case_name'],
					'task_id'=>$value['task_unit_id'],
					'project_id'=>$value['task_id'],
					'workflow_task'=>$value['service_task'],
					'todo_icon' => $status.''.$imghtml1,
					'project_priority'=>$value['priority'],
					'project_due_date'=>$value['task_date_time'], //(new Options)->ConvertOneTzToAnotherTz($value['task_duedate']." ".$value['task_timedue'] , 'UTC', $_SESSION['usrTZ']),
					'team_id'=>$value['team_id'],
					'client_case_id'=>$value['client_case_id'],
					'team_loc'=>$value['team_location'],
				);
			}

		}else if($id == 3){
			$Notstartedtask = (new TasksUnits)->getTaskNotstartedTaskCase();
			$status = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';
			foreach($Notstartedtask as $service => $value){
				$is_pastdue = (new Tasks)->ispastduetask($value['task_id']);
					if ($is_pastdue)
						$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
					else
						$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';

			$myteamtaskdata[]=array(
    						'client_case'=>$value['client_name']." - ".$value['case_name'],
    						'task_id'=>$value['task_unit_id'],
    						'project_id'=>$value['task_id'],
    						'workflow_task'=>$value['service_task'],
    						'todo_icon' => $status.''.$imghtml1,
    						'project_priority'=>$value['priority'],
    						'project_due_date'=>(new Options)->ConvertOneTzToAnotherTz($value['task_duedate']." ".$value['task_timedue'] , 'UTC', $_SESSION['usrTZ']),
    						'team_id'=>$value['team_id'],
    						'client_case_id'=>$value['client_case_id'],
    						'team_loc'=>$value['team_loc'],
    				);
			}
		}
		else{
			$selectabc = '';
			$taskduedatejoin = '';
			if($id != 2){
				$selectabc = ', A.task_date_time';
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				if (Yii::$app->db->driverName == 'mysql') {
					$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
				} else {
					//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
					$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
				}
				$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tinstruct.id = A.id ";
			}
			if($id == 1){
				$where.= " AND tunits.unit_status NOT IN (0,4)";
			}
			if($id == 2){ // ACTIVE TODO
				$join.= "LEFT JOIN tbl_tasks_units_todos as todos ON todos.tasks_unit_id = tunits.id LEFT JOIN tbl_todo_cats as cats ON todos.todo_cat_id = cats.id";
				$select.= ",todos.todo,todos.modified,todos.complete,cats.todo_desc,cats.todo_cat";
				$where.= " AND todos.complete !=1 AND todos.assigned =".$userId;
			}else{
				$where.= " AND tunits.unit_assigned_to = ".$userId." AND tunits.unit_status !=4 AND (tunits.unit_assigned_to IS NOT NULL OR tunits.unit_assigned_to != '' OR tunits.unit_assigned_to != '[]')";
			}
			$settings_info = Settings::find()->where("field = 'project_sort'")->one();
			if ($settings_info->fieldvalue == '0') {
				$order.= "project.priority_order ASC,tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
			} else if ($settings_info->fieldvalue == '1') {
				$order.= "tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
			} else if ($settings_info->fieldvalue == '2') {
				$order.= "tunits.task_id DESC";
			} else if($settings_info->fieldvalue == '3'){
				$order.= "pteam.id ASC, project.priority_order ASC,tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
			}
			$sql = "SELECT tunits.id,tinstruct.task_id,tunits.unit_status,tunits.servicetask_id,tunits.team_loc,tunits.team_id,task.client_case_id,ccase.case_name,client.client_name,tinstruct.task_duedate,tinstruct.task_timedue,project.priority,project.priority_order,location.team_location_name, service.service_task,team.team_name ".$select."$selectabc  FROM tbl_tasks_units as tunits
			INNER JOIN tbl_task_instruct as tinstruct ON tunits.task_instruct_id = tinstruct.id
			$taskduedatejoin
			INNER JOIN tbl_tasks as task ON tinstruct.task_id = task.id
			INNER JOIN tbl_client_case as ccase ON task.client_case_id = ccase.id
			INNER JOIN tbl_client as client ON ccase.client_id = client.id
			LEFT JOIN tbl_priority_project as project ON tinstruct.task_priority = project.id
			INNER JOIN tbl_teamlocation_master as location ON tunits.team_loc = location.id
			INNER JOIN tbl_servicetask as service ON tunits.servicetask_id = service.id
			INNER JOIN tbl_team as team ON tunits.team_id = team.id
			LEFT JOIN tbl_priority_team as pteam ON task.team_priority = pteam.id
			".$join." where task.task_status !=4 AND task.task_closed != 1 AND task.task_cancel != 1 AND ccase.is_close != 1 AND task.client_case_id IN (SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= :userId and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id) AND tunits.team_id IN (1) ".$where." order by ".$order;

			$params = [':userId'=>$userId];

			$teamtaskunitdata2 = \Yii::$app->db->createCommand($sql, $params)->queryAll();

			$is_accessible_submodule_tracktask = 1;

			if(!empty($teamtaskunitdata2)){
				foreach ($teamtaskunitdata2 as $taskunit){
					if($id==2){
						if($taskunit['complete'] == 1){
							$icon_value = '<span tabindex="0" class="fa fa-bell text-dark" title="Completed todos"></span>';
						}else{
							$icon_value = '<span tabindex="0" class="fa fa-bell text-danger" title="Incomplete ToDo"></span>';
						}
						$myteamtaskdata[]=array(
							'client_case'=>$taskunit['client_name']." - ".$taskunit['case_name'],
							'task_id'=>$taskunit['id'],
							'project_id'=>$taskunit['task_id'],
							'team_id'=>$taskunit['team_id'],
							'client_case_id'=>$taskunit['client_case_id'],
							'team_loc'=>$taskunit['team_loc'],
							'todo_icon'=>$icon_value,
							'todo_item'=>$taskunit['todo'],
							'todo_status'=>$taskunit['complete'],
							'followup_category'=>$taskunit['todo_cat'].' - '.$taskunit['todo_desc'],
							'todo_assigned'=> (new Options)->ConvertOneTzToAnotherTz($taskunit['modified'], 'UTC', $_SESSION['usrTZ']),
						);
					}else{
						$is_pastdue = (new Tasks)->ispastduetask($taskunit['task_id']);
						if ($is_pastdue)
							$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
						else
							$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';
						if($taskunit['unit_status'] == 0){
							$status = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';
						}else if($taskunit['unit_status'] == 1){
							$status = '<span tabindex="0" class="fa fa-clock-o text-success" title="Task Started"></span>';
						}elseif($taskunit['unit_status'] == 2){
							$status = '<span tabindex="0" class="fa fa-clock-o text-info" title="Task On Pause"></span>';
						}elseif($taskunit['unit_status'] == 3){
							$status = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Task On Hold"></span>';
						}elseif($taskunit['unit_status'] == 4){
							$status = '<span tabindex="0" class="fa fa-clock-o text-dark" title="Task Completed"></span>';
						}
						$myteamtaskdata[]=array(
							'client_case'=>$taskunit['client_name']." - ".$taskunit['case_name'],
							'task_id'=>$taskunit['id'],
							'project_id'=>$taskunit['task_id'],
							'workflow_task'=>$taskunit['service_task'],
							'todo_icon' => $status.''.$imghtml1,
							'project_priority'=>$taskunit['priority'],
							'project_due_date'=>$taskunit['task_date_time'],//(new Options)->ConvertOneTzToAnotherTz($taskunit['task_duedate']." ".$taskunit['task_timedue'], 'UTC', $_SESSION['usrTZ']),
							'team_id'=>$taskunit['team_id'],
							'client_case_id'=>$taskunit['client_case_id'],
							'team_loc'=>$taskunit['team_loc'],
						);

					}
				}
			}
	}
    	return $this->renderAjax('getcaseassignmentdetailnew', ['myteamtaskdata' => $myteamtaskdata,'id'=>$id]);

	}

	public function actionGetmyteamassignmentnew(){
		$id = Yii::$app->request->post('expandRowKey');
		$userId = Yii::$app->user->identity->id;
		$sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=:userId and tbl_project_security.team_id!=0 group by tbl_project_security.team_id";
		$select = ""; $join = ""; $where = ""; $order = "";
		//echo $id;die;
		if($id == 0){
			$Pendingservice = (new TasksUnits)->getTaskPendingServiceTaskTeam();
			$status = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';
			foreach($Pendingservice as $service => $value){
				$is_pastdue = (new Tasks)->ispastduetask($value['task_id']);
					if ($is_pastdue)
						$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
					else
						$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';

				$myteamtaskdata[]=array(
    						'team_location'=>$value['team_name']." - ".$value['team_location_name'],
    			 			'task_id'=>$value['task_unit_id'],
    			 			'project_id'=>$value['task_id'],
    			 			'workflow_task'=>$value['service_task'],
    			 			'project_priority'=>$value['priority'],
    			 			'todo_icon'=>$status.$imghtml1,
    			 			//'project_due_date'=>(new Options)->ConvertOneTzToAnotherTz($value['task_duedate']." ".$value['task_timedue'], 'UTC', $_SESSION['usrTZ']),
    			 			'team_id'=>$value['team_id'],
    			 			'team_loc'=>$value['team_location'],
    			 			'project_due_date'=>$value['task_date_time'],
    				);
			}
		}else if($id == 3){
			$Notstartedtask = (new TasksUnits)->getTaskNotstartedTaskTeam();
			$status = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';
			foreach($Notstartedtask as $service => $value){
				$is_pastdue = (new Tasks)->ispastduetask($value['task_id']);
					if ($is_pastdue)
						$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
					else
						$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';

				$myteamtaskdata[]=array(
    						'team_location'=>$value['team_name']." - ".$value['team_location_name'],
    			 			'task_id'=>$value['task_unit_id'],
    			 			'project_id'=>$value['task_id'],
    			 			'workflow_task'=>$value['service_task'],
    			 			'project_priority'=>$value['priority'],
    			 			'todo_icon'=>$status.$imghtml1,
    			 			//'project_due_date'=>(new Options)->ConvertOneTzToAnotherTz($value['task_duedate']." ".$value['task_timedue'], 'UTC', $_SESSION['usrTZ']),
							'project_due_date' => $value['task_date_time'],
    			 			'team_id'=>$value['team_id'],
    			 			'team_loc'=>$value['team_loc'],
    				);
			}
		}
		else{
			$selectabc = '';
			$taskduedatejoin = '';
			if($id != 2){
				$selectabc = ', A.task_date_time';
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				if (Yii::$app->db->driverName == 'mysql') {
					$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
				} else {
					//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
					$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
				}
				$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tinstruct.id = A.id ";
			}

			if($id == 1){
				$where.= " AND tunits.unit_status NOT IN (0,4)";
			}
			if($id == 2) // ACTIVE TODO
			{
				$join.= "LEFT JOIN tbl_tasks_units_todos as todos ON todos.tasks_unit_id = tunits.id LEFT JOIN tbl_todo_cats as cats ON todos.todo_cat_id = cats.id";
				$select.= ",todos.todo,todos.modified,todos.complete,cats.todo_desc,cats.todo_cat";
				$where.= " AND todos.complete !=1 AND todos.assigned =".$userId;

			}else{
				$where.= " AND tunits.unit_assigned_to = ".$userId." AND tunits.unit_status !=4 AND (tunits.unit_assigned_to IS NOT NULL OR tunits.unit_assigned_to != '' OR tunits.unit_assigned_to != '[]')";
			}
			$settings_info = Settings::find()->where("field = 'project_sort'")->one();

			if ($settings_info->fieldvalue == '0') {
				$order.= "project.priority_order ASC,tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
			} else if ($settings_info->fieldvalue == '1') {
				$order.= "tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
			} else if ($settings_info->fieldvalue == '2') {
				$order.= "tunits.task_id DESC";
			} else if($settings_info->fieldvalue == '3'){
				$order.= "pteam.id ASC, project.priority_order ASC,tinstruct.task_duedate DESC,tinstruct.task_timedue DESC";
			}else{
				$order.= "tunits.id DESC";
			}

			$sql = "SELECT tunits.id,tinstruct.task_id,tunits.unit_status,tunits.servicetask_id,tunits.team_loc,tunits.team_id,task.client_case_id,tinstruct.task_duedate,tinstruct.task_timedue,project.priority,project.priority_order,location.team_location_name, service.service_task,team.team_name ".$select."$selectabc FROM tbl_tasks_units as tunits
			INNER JOIN tbl_task_instruct as tinstruct ON tunits.task_instruct_id = tinstruct.id
			$taskduedatejoin
			INNER JOIN tbl_tasks as task ON tinstruct.task_id = task.id
			INNER JOIN tbl_client_case as ccase ON task.client_case_id = ccase.id
			LEFT JOIN tbl_priority_project as project ON tinstruct.task_priority = project.id
			INNER JOIN tbl_teamlocation_master as location ON tunits.team_loc = location.id
			INNER JOIN tbl_servicetask as service ON tunits.servicetask_id = service.id
			INNER JOIN tbl_team as team ON tunits.team_id = team.id
			LEFT JOIN tbl_priority_team as pteam ON task.team_priority = pteam.id
			".$join." where task.task_status !=4 AND task.task_closed != 1 AND task.task_cancel != 1 AND ccase.is_close != 1 AND tunits.servicetask_id IN (SELECT t.id FROM tbl_servicetask t
			INNER JOIN tbl_teamservice ON t.teamservice_id = tbl_teamservice.id
			WHERE tbl_teamservice.teamid IN (".$sqlteams.") AND tbl_teamservice.teamid NOT IN (1))".$where." order by ".$order;
			$params = [':userId'=>$userId];
			$teamtaskunitdata2 = \Yii::$app->db->createCommand($sql,$params)->queryAll();
			if(!empty($teamtaskunitdata2)){
				foreach ($teamtaskunitdata2 as $taskunit){
					if($id==2){
						if($taskunit['complete'] == 1){
							$icon_value = '<span tabindex="0" class="fa fa-bell text-dark" title="Completed todos"></span>';
						}else{
							$icon_value = '<span tabindex="0" class="fa fa-bell text-danger" title="Incomplete ToDo"></span>';
						}
						$myteamtaskdata[]=array(
									'team_location'=>$taskunit['team_name']." - ".$taskunit['team_location_name'],
									'task_id'=>$taskunit['id'],
									'project_id'=>$taskunit['task_id'],
									'team_id'=>$taskunit['team_id'],
									'team_loc'=>$taskunit['team_loc'],
									'todo_icon'=>$icon_value,
									'todo_item'=>$taskunit['todo'],
									'todo_status'=>$taskunit['complete'],
									'followup_category'=>$taskunit['todo_cat'].' - '.$taskunit['todo_desc'],
									'todo_assigned'=>(new Options)->ConvertOneTzToAnotherTz($taskunit->modified, 'UTC', $_SESSION['usrTZ']),
								);
					}else{
						$is_pastdue = (new Tasks)->ispastduetask($taskunit['task_id']);
						if ($is_pastdue)
							$imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Task"></span>';
						else
							$imghtml1 = '&nbsp;<em class="fa" style="width: 6px;"></em>';
						//echo $imghtml1; exit;
						if($taskunit['unit_status'] == 0){
							$status = '<span tabindex="0" class="fa fa-clock-o text-primary" title="Task Not Started"></span>';
						}else if($taskunit['unit_status'] == 1){
							$status = '<span tabindex="0" class="fa fa-clock-o text-success" title="Task Started"></span>';
						}elseif($taskunit['unit_status'] == 2){
							$status = '<span tabindex="0" class="fa fa-clock-o text-info" title="Task On Pause"></span>';
						}elseif($taskunit['unit_status'] == 3){
							$status = '<span tabindex="0" class="fa fa-clock-o text-gray" title="Task On Hold"></span>';
						}elseif($taskunit['unit_status'] == 4){
							$status = '<span tabindex="0" class="fa fa-clock-o text-dark" title="Task Completed"></span>';
						}
						$myteamtaskdata[]=array(
								'team_location'=>$taskunit['team_name']." - ".$taskunit['team_location_name'],
								'task_id'=>$taskunit['id'],
								'project_id'=>$taskunit['task_id'],
								'workflow_task'=>$taskunit['service_task'],
								'project_priority'=>$taskunit['priority'],
								'todo_icon'=>$status.$imghtml1,
								'project_due_date'=>$taskunit['task_date_time'],//(new Options)->ConvertOneTzToAnotherTz($taskunit['task_duedate']." ".$taskunit['task_timedue'], 'UTC', $_SESSION['usrTZ']),
								'team_id'=>$taskunit['team_id'],
								'team_loc'=>$taskunit['team_loc'],
						);
					}
				}
			}
		}
		return $this->renderPartial('getteamassignmentdetailnew', array('myteamtaskdata' => $myteamtaskdata,'id'=>$id), false, true);
	}
	public function actionKeepalive()
    {
    	Yii::$app->user->setState('authTimeout',time());
        echo 'OK';
        exit;
        Yii::app()->end();
    }
	public function actionCronemail(){
		EmailCron::sendQueueEmail();
		return;
	}
	public function actionCronpastdueemail(){
		EmailCron::sendApproachingPastDueEmail();		
		return;
	}
	public function actionCronpastdue(){
		EmailCron::sendPastDueEmail();		
		return;
	}
	/**
     * Today's activity
     * */
    public function actionTodaysactivity()
    {
		return $this->render('todaysactivity');
    }
}
