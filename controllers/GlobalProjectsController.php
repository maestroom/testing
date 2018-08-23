<?php

namespace app\controllers;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Client;
use app\models\ClientCase;
use app\models\Team;
use app\models\PriorityProject;
use app\models\ProjectSecurity;
use app\models\TaskInstructServicetask;
use app\models\Tasks;
use app\models\Options;
use app\models\search\TaskSearch;
use app\models\SavedFilters;
use app\models\SettingsEmail;
use app\models\ActivityLog;
use app\models\EmailCron;

class GlobalProjectsController extends Controller {

    /**
     * (non-PHPdoc)
     * @see CController::filters()
     */
    public function beforeAction($action) {
        if (Yii::$app->user->isGuest)
            $this->redirect(array('site/login'));


        if (!(new User)->checkAccess(2)){/* 38 */
          
            /* IRT 31 Default landing page */
			$def_land_page = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->default_landing_page;
            if($def_land_page=='') $def_land_page = '';
            if($def_land_page==1) { // Show My Assignments
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
          //  throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
        }

        return parent::beforeAction($action);
    }

    /**
     * Load Dynamic Filter
     * */
    public function actionIndex() {
        $this->layout = 'global';
        $project_name = '';
        $params = Yii::$app->request->queryParams;
        $userId = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        if (Yii::$app->request->isAjax) {
            $this->layout = '';
            Yii::$app->request->queryParams += Yii::$app->request->post();
        }
        $searchModel = new TaskSearch();
        $params['grid_id']='dynagrid-globalproject';
		Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->searchGlobalProject(Yii::$app->request->queryParams);
        //$models=$dataProvider->getModels();
        //echo "<pre>",print_r($models),"</prE>";die;

        $pporder = PriorityProject::find()->select('priority_order')->where('remove=0')->orderBy('priority_order asc')->one()->priority_order;
        /* IRT 67,68,86,87,258 */
        /* IRT 96,398 */
        $filter_type = \app\models\User::getFilterType(['tbl_tasks.id', 'tbl_tasks.task_status', 'tbl_tasks.client_case_id', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_priority', 'tbl_task_instruct.project_name', 'tbl_client_case.client_id'], ['tbl_tasks', 'tbl_task_instruct', 'tbl_client_case']);
        $config = ['task_status' => ['All' => 'All', '0' => 'Not Started', '1' => 'Started', '3' => 'On Hold', '4' => 'Complete']];
        if (isset($params['TaskSearch']['client_id']) && !empty($params['TaskSearch']['client_id'])) {
            $clients_selected = (new User)->getSelectedGridClients($params['TaskSearch']['client_id']);
            if ($clients_selected == 'ALL') {
                unset($params['TaskSearch']['client_id']);
                $clients_selected = array();
            }
        }
        if (isset($params['TaskSearch']['client_case_id']) && !empty($params['TaskSearch']['client_case_id'])) {
            $client_case_selected = (new User)->getSelectedGridCases($params['TaskSearch']['client_case_id']);
            if ($client_case_selected == 'ALL') {
                unset($params['TaskSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        $config_widget_options = ['task_priority' => ['field_alais' => 'priority'], 'client_id' => ['initValueText' => $clients_selected], 'client_case_id' => ['initValueText' => $client_case_selected]];
        $filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['global-projects/ajax-filter']), $config, $config_widget_options,Yii::$app->request->queryParams,'global_project');
        /* IRT 67,68,86,87,258 */
        if (Yii::$app->request->isAjax) {
            $params = Yii::$app->request->queryParams;
            $project_name = $params['TaskSearch']['project_name'];
            if ($params['TaskSearch']['project_name'] == 'blank')
                $project_name = '';

            return $this->renderAjax('index', ['params'=>$params,'filterWidgetOption' => $filterWidgetOption, 'filter_type' => $filter_type, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'pporder' => $pporder, 'project_name' => $project_name]);
        } else {
            return $this->render('index', ['params'=>$params,'filterWidgetOption' => $filterWidgetOption, 'filter_type' => $filter_type, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'pporder' => $pporder, 'project_name' => $project_name]);
        }
    }

    public function actionFilterOption() {
        $this->layout = 'global';
        $params = Yii::$app->request->queryParams;
        $filter_id = Yii::$app->request->get('filter_id');
        $filter_data = SavedFilters::findOne($filter_id);
        $filterParams = json_decode($filter_data->filter_attributes, true);
        
        if(!empty($filterParams))
            Yii::$app->request->queryParams=array_merge(Yii::$app->request->queryParams,$filterParams);

        $searchModel = new TaskSearch();
        $params1['grid_id']='dynagrid-globalproject-saved-filter';
		Yii::$app->request->queryParams +=$params1;
        $dataProvider = $searchModel->searchGlobalProject(Yii::$app->request->queryParams);
        $pporder = PriorityProject::find()->select('priority_order')->where('remove=0')->orderBy('priority_order asc')->one()->priority_order;
        /* IRT 67,68,86,87,258 */
        //$filter_type = \app\models\User::getFilterType(['tbl_tasks.id', 'tbl_tasks.task_status', 'tbl_tasks.client_case_id', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_priority', 'tbl_task_instruct.project_name'], ['tbl_tasks', 'tbl_task_instruct']);
        //$config = ['task_status' => ['All' => 'All', '0' => 'Not Started', '1' => 'Started', '3' => 'On Hold', '4' => 'Complete']];
        //$config_widget_options = ['task_priority' => ['field_alais' => 'priority']];
        //$filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['global-projects/ajax-filter']), $config, $config_widget_options,Yii::$app->request->queryParams,'global_project');
        $filter_type = \app\models\User::getFilterType(['tbl_tasks.id', 'tbl_tasks.task_status', 'tbl_tasks.client_case_id', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_priority', 'tbl_task_instruct.project_name', 'tbl_client_case.client_id'], ['tbl_tasks', 'tbl_task_instruct', 'tbl_client_case']);
        $config = ['task_status' => ['All' => 'All', '0' => 'Not Started', '1' => 'Started', '3' => 'On Hold', '4' => 'Complete']];
        if (isset($params['TaskSearch']['client_id']) && !empty($params['TaskSearch']['client_id'])) {
            $clients_selected = (new User)->getSelectedGridClients($params['TaskSearch']['client_id']);
            if ($clients_selected == 'ALL') {
                unset($params['TaskSearch']['client_id']);
                $clients_selected = array();
            }
        }
        if (isset($params['TaskSearch']['client_case_id']) && !empty($params['TaskSearch']['client_case_id'])) {
            $client_case_selected = (new User)->getSelectedGridCases($params['TaskSearch']['client_case_id']);
            if ($client_case_selected == 'ALL') {
                unset($params['TaskSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        $config_widget_options = ['task_priority' => ['field_alais' => 'priority'], 'client_id' => ['initValueText' => $clients_selected], 'client_case_id' => ['initValueText' => $client_case_selected]];
        $filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['global-projects/ajax-filter']), $config, $config_widget_options,Yii::$app->request->queryParams,'global_project');
        
        /* IRT 67,68,86,87,258 */
        return $this->render('FilterOptionGrid', ['params'=>$params,'filterWidgetOption' => $filterWidgetOption, 'filter_type' => $filter_type, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'pporder' => $pporder, 'filter_id' => $filter_id]);
    }

    /**
     * Load Save Filter Grid
     * */
    public function actionSaveFilterGrid() {
        $this->layout = 'global';
        $params = Yii::$app->request->queryParams;
        $filter_id = Yii::$app->request->get('filter_id');
        $filter_data = SavedFilters::findOne($filter_id);
        $filterParams = json_decode($filter_data->filter_attributes, true);
        if(!empty($filterParams))
            Yii::$app->request->queryParams=array_merge(Yii::$app->request->queryParams,$filterParams);

        $searchModel = new TaskSearch();
        $params1['grid_id']='dynagrid-globalproject-saved';
		Yii::$app->request->queryParams +=$params1;
        $dataProvider = $searchModel->searchGlobalProject(Yii::$app->request->queryParams);
        $pporder = PriorityProject::find()->select('priority_order')->where('remove=0')->orderBy('priority_order asc')->one()->priority_order;
        /* IRT 67,68,86,87,258 */
        $filter_type = \app\models\User::getFilterType(['tbl_tasks.id', 'tbl_tasks.task_status', 'tbl_tasks.client_case_id', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_priority', 'tbl_task_instruct.project_name', 'tbl_client_case.client_id'], ['tbl_tasks', 'tbl_task_instruct', 'tbl_client_case']);
        $config = ['task_status' => ['All' => 'All', '0' => 'Not Started', '1' => 'Started', '3' => 'On Hold', '4' => 'Complete']];
        if (isset($params['TaskSearch']['client_id']) && !empty($params['TaskSearch']['client_id'])) {
            $clients_selected = (new User)->getSelectedGridClients($params['TaskSearch']['client_id']);
            if ($clients_selected == 'ALL') {
                unset($params['TaskSearch']['client_id']);
                $clients_selected = array();
            }
        }
        if (isset($params['TaskSearch']['client_case_id']) && !empty($params['TaskSearch']['client_case_id'])) {
            $client_case_selected = (new User)->getSelectedGridCases($params['TaskSearch']['client_case_id']);
            if ($client_case_selected == 'ALL') {
                unset($params['TaskSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        $config_widget_options = ['task_priority' => ['field_alais' => 'priority'], 'client_id' => ['initValueText' => $clients_selected], 'client_case_id' => ['initValueText' => $client_case_selected]];
        $filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['global-projects/ajax-filter']), $config, $config_widget_options,Yii::$app->request->queryParams,'global_project');
        
        /* IRT 67,68,86,87,258 */
        /* echo "<pre>";
          print_r($filter_type);
          echo "</pre>";
          die; */
        /* if(Yii::$app->request->isAjax){
          return $this->renderAjax('SaveFilterGrid',['searchModel' => $searchModel,'dataProvider' => $dataProvider,'pporder' => $pporder,'filter_id'=>$filter_id]);
          } else { */
        return $this->render('SaveFilterGrid', ['params'=>$params,'filterWidgetOption' => $filterWidgetOption, 'filter_type' => $filter_type, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'pporder' => $pporder, 'filter_id' => $filter_id]);
        /* } */
    }

    /**
     * Get detail of global project on expand .
     * @return mixed
     */
    public function actionGetDetails() {
        $task_id = Yii::$app->request->post("expandRowKey");
        $task_info = Tasks::findOne($task_id);
        $teamservice_data = TaskInstructServicetask::find()->select(['tbl_task_instruct_servicetask.teamservice_id', 'tbl_teamservice.service_name'])->joinWith('teamservice')->where('tbl_task_instruct_servicetask.task_id='.$task_id.' AND task_instruct_id IN (select id from tbl_task_instruct where tbl_task_instruct.isactive=1 and tbl_task_instruct.task_id='.$task_id.')')->groupBy(['tbl_task_instruct_servicetask.teamservice_id', 'tbl_teamservice.service_name'])->all();
        $servicetask_names = "";
        foreach ($teamservice_data as $teamservice) {
            if ($servicetask_names != "")
                $servicetask_names .= '; ' . $teamservice->teamservice->service_name;
            else
                $servicetask_names = $teamservice->teamservice->service_name;
        }
        $sql = "SELECT unit_assigned_to FROM tbl_tasks_units WHERE unit_assigned_to  !=0 AND task_id =" . $task_id;
        $assigedUser = ArrayHelper::map(User::find()->select([" CONCAT(usr_first_name,' ', usr_lastname) AS full_name "])->where('id IN (' . $sql . ')')->all(), 'full_name', 'full_name');
        // if($task_info->createdUser->usr_first_name!="" && $task_info->createdUser->usr_lastname!=""){
        $submitted_by = $task_info->createdUser->usr_first_name . " " . $task_info->createdUser->usr_lastname;
        /* }else{  
          $submitted_by = $task_info->createdUser->usr_username;
          } */

        $submitted_date = (new Options)->ConvertOneTzToAnotherTz($task_info->created, "UTC", $_SESSION["usrTZ"]);
        $completed_date = "";
        if ($task_info->task_status == 4 && isset($task_info->task_complete_date) && !in_array(date('Y', strtotime($task_info->task_complete_date)), array('1970', '-0001'))) {
            $completed_date = (new Options)->ConvertOneTzToAnotherTz($task_info->task_complete_date, "UTC", $_SESSION["usrTZ"]);
        }
        /*pastdue*/
        $duedate=(new Tasks)->getTaskDuedateByTaskid($task_id);
        $ispastduetask=(new Tasks)->ispastduetask($task_id);


        return $this->renderPartial('getgriddetails', ['ispastduetask'=>$ispastduetask,'duedate'=>$duedate,'services' => $servicetask_names, 'assigedUser' => implode(", ", $assigedUser), 'task_info' => $task_info, 'submitted_date' => $submitted_date, 'submitted_by' => $submitted_by, 'completed_date' => $completed_date]);
    }

    /**
     * Filter GridView with Ajax
     * */
    public function actionAjaxFilter() {
        $filter_id = Yii::$app->request->post('filter_id', 0);
        $field_id = Yii::$app->request->post('field', 0);
        if (isset($filter_id) && $filter_id > 0) {
            $filter_data = SavedFilters::findOne($filter_id);
            $filterParams = json_decode($filter_data->filter_attributes, true);
            if(!empty($filterParams))
            Yii::$app->request->bodyParams=array_merge(Yii::$app->request->bodyParams,$filterParams);
            //Yii::$app->request->bodyParams += $filterParams;
        }
       // echo "<pre>",print_r(Yii::$app->request->bodyParams),"</prE>";die;
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->searchFilterGlobalProject(Yii::$app->request->bodyParams);
        $out['results'] = array();
        $val_array = array();
        foreach ($dataProvider as $key => $val) {
            if (!in_array($val, $val_array)) {
                $val1 = $val;
                if ($val == '' && !in_array('(not set)', $val_array)) {
                    $val1 = '(not set)';
                    $val = '(not set)';
                }
                if ($field_id == 'client_id' || $field_id == 'client_case_id') {
                    $val_id1 = $key;
                } else {
                    $val_id1 = $val1;
                }
                if (trim($val1) != "" && trim($val) != "") {
                    $out['results'][] = ['id' => $val_id1, 'text' => $val, 'label' => $val1];
                    $val_array[$val] = $val;
                }
            }
        }
        return json_encode($out);
    }

    /**
     * Get Client case Data for filter 
     * */
    public function actionGetClientCase() {
        $uid = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;

        $list_cases = array();
        /*$sql = "tbl_client.id IN (SELECT t1.client_id FROM tbl_project_security t1 WHERE t1.user_id=" . $uid . " AND client_case_id !=0 and  t1.team_id=0 GROUP BY t1.client_id,t1.id)";
        $query = Client::find()
                ->JoinWith(['clientCases' => function (\yii\db\ActiveQuery $query) {
                        $query->where("is_close=0");
                    }])
                ->select(['tbl_client.id', 'client_name'])
                ->orderBy('client_nam ASC');

        if ($roleId != 0) {
            $query->where($sql);
        }
        $list_clients = ArrayHelper::map($query->all(), 'id', 'client_name');
        if (!empty($list_clients)) {
            foreach ($list_clients as $client => $client_name) {
                $sql = "SELECT tbl_project_security.client_case_id FROM tbl_project_security inner join tbl_client_case on tbl_client_case.id=tbl_project_security.client_case_id and tbl_client_case.is_close=0 WHERE tbl_project_security.client_id=" . $client . " and user_id=" . $uid;
                $list_cases[$client] = ArrayHelper::map(ClientCase::find()->select(['id', 'case_name'])->where('id IN (' . $sql . ')')->all(), 'id', 'case_name');
            }
        }*/
        $clientListAr = (new Client)->getClientCasesWithPermissiondetailsArray();
        
        $clientList = [];
		$selectedCases = [];
		foreach($clientListAr as $client_name => $clientCases){
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
		}

        return $this->renderAjax('getclientcase', ['clientList' =>$clientList,'list_clients' => $list_clients, 'list_cases' => $list_cases]);
    }

    /**
     * Get Team Data for filter
     * Changed for (IRT 75) 
     * */
    public function actionGetTeams() {
        /*$uid = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        $list_teams = '';
        if ($roleId != 0) {
            $query = 'SELECT tbl_team.id, tbl_team.team_name, tbl_project_security.team_loc, tbl_teamlocation_master.team_location_name 
				FROM tbl_team 
				INNER JOIN tbl_project_security on tbl_project_security.team_id = tbl_team.id 
				INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id = tbl_project_security.team_loc 
				WHERE tbl_team.id != 1 AND tbl_project_security.user_id = ' . $uid . ' 
				GROUP BY tbl_team.id, tbl_team.team_name, tbl_teamlocation_master.team_location_name,tbl_project_security.team_loc';
            $result = \Yii::$app->db->createCommand($query)->queryAll();
            $list_teams = ArrayHelper::index($result, null, [function ($element) {
                            return $element['id'];
                        }, 'team_name']);
        } else {
            $query = 'SELECT tbl_team.id, tbl_team.team_name, tbl_project_security.team_loc, tbl_teamlocation_master.team_location_name 
				FROM tbl_team 
				INNER JOIN tbl_project_security on tbl_project_security.team_id = tbl_team.id 
				INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id = tbl_project_security.team_loc 
				GROUP BY tbl_team.id, tbl_team.team_name, tbl_teamlocation_master.team_location_name, tbl_project_security.team_loc';
            $result = \Yii::$app->db->createCommand($query)->queryAll();
            $list_teams = ArrayHelper::index($result, null, [function ($element) {
                            return $element['id'];
                        }, 'team_name']);
        }
        return $this->renderPartial('getteam', ['list_teams' => $list_teams]);*/
        $user_id = Yii::$app->request->post('user_id');        
        $projectsecurity = '';
        $teamListAr = (new Team)->getTeamLocWithPermissiondetailsArray();
        //getTeamLocdetailsArray();
        $teamList = [];
		$selectedteamloc = [];
		foreach($teamListAr as $team_name => $teamLocs) {
			$team = [];
			foreach($teamLocs as $team_id => $teamloc) {
				$team['title'] = $team_name;
				$team['isFolder'] = true;
				$team['key'] = $team_id;
				$locs = [];
				foreach($teamloc as $loc_id => $loc_name){
					$locs['title'] = $loc_name;
					$locs['key'] = $team_id.','.$loc_id;
					if($projectsecurity[$team_id.','.$loc_id] == $locs['key']){
						$locs['select'] = true;
						$selectedteamloc[] = $locs['key'];
					} else {
						$locs['select'] = false;
					}

					$team['children'][] = $locs;
				}
				if(!empty($team['children']))
					$teamList[] = $team;
			}
		}
        return $this->renderAjax('getteam', ['teamList' =>$teamList,'list_teams' => $list_teams]);
    }

    /**
     * Get Team Members Data for filter
     * */
    public function actionGetTeamembers() {
        $post_data = Yii::$app->request->post();
        $list_teammanager = array();
        $uid = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        $usersql = "SELECT user_id FROM tbl_project_security INNER JOIN tbl_user ON tbl_project_security.user_id = tbl_user.id INNER JOIN tbl_role ON tbl_user.role_id = tbl_role.id WHERE (team_id IN (SELECT team_id FROM tbl_project_security WHERE team_id!=0 and user_id=" . $uid . ") AND team_id!=0) AND (role_type LIKE '%2%') GROUP BY user_id";
        if (!empty($post_data['team_loc_arr'])) {
            $sqlselect = "";
            foreach ($post_data['team_loc_arr'] as $team_loc_arr) {
                if ($sqlselect == "") {
                    if (isset($team_loc_arr['loc'])) {
                        $sqlselect = " ((tbl_project_security.team_id=" . $team_loc_arr['team'] . ") AND (tbl_project_security.team_loc=" . $team_loc_arr['loc'] . "))";
                    } else {
                        $sqlselect = " (tbl_project_security.team_id=" . $team_loc_arr['team'] . ")";
                    }
                } else {
                    if (isset($team_loc_arr['loc'])) {
                        $sqlselect .= " OR ((tbl_project_security.team_id=" . $team_loc_arr['team'] . ") AND (tbl_project_security.team_loc=" . $team_loc_arr['loc'] . "))";
                    } else {
                        $sqlselect .= " OR (tbl_project_security.team_id=" . $team_loc_arr['team'] . ")";
                    }
                }
            }
            $usersql = "SELECT user_id FROM tbl_project_security INNER JOIN tbl_user ON tbl_project_security.user_id = tbl_user.id INNER JOIN tbl_role ON tbl_user.role_id = tbl_role.id WHERE (team_id IN (SELECT team_id FROM tbl_project_security WHERE team_id!=0 and user_id=" . $uid . ") AND team_id!=0) AND (role_type LIKE '%2%') AND (" . $sqlselect . ") GROUP BY user_id";
        }
        if ($roleId != 0) {
            $list_teammanager = ArrayHelper::map(User::find()->select(['id', 'usr_first_name', 'usr_lastname', 'usr_username', "CONCAT(usr_first_name,' ',usr_lastname ) as full_name"])->where('id IN (' . $usersql . ') AND status=1 AND tbl_user.id != 1')->orderBy('usr_lastname')->all(), 'id', function($model) {
                        if ($model->usr_first_name == '' && $model->usr_lastname == '') {
                            $cUserName = $model->usr_username;
                        } else {
                            $cUserName = ucwords($model->full_name);
                        } return $cUserName;
                    });
        } else {
            if ($sqlselect != "") {
                $list_teammanager = ArrayHelper::map(User::find()->select(['id', 'usr_first_name', 'usr_lastname', 'usr_username', "CONCAT(usr_first_name,' ',usr_lastname ) as full_name"])->where('id IN (' . $usersql . ') AND status=1')->andWhere('tbl_user.id != 1')->orderBy('usr_lastname')->all(), 'id', function($model) {
                            if ($model->usr_first_name == '' && $model->usr_lastname == '') {
                                $cUserName = $model->usr_username;
                            } else {
                                $cUserName = ucwords($model->full_name);
                            } return $cUserName;
                        });
            } else {
                $list_teammanager = ArrayHelper::map(User::find()->select(['id', 'usr_first_name', 'usr_lastname', 'usr_username', "CONCAT(usr_first_name,' ',usr_lastname ) as full_name"])->where('status=1')->andWhere('tbl_user.id != 1')->orderBy('usr_lastname')->all(), 'id', function($model) {
                            if ($model->usr_first_name == '' && $model->usr_lastname == '') {
                                $cUserName = $model->usr_username;
                            } else {
                                $cUserName = ucwords($model->full_name);
                            } return $cUserName;
                        });
            }
        }
        $teammemList = [];
		foreach($list_teammanager as $userid => $name) {
			    $member = [];
			
				$member['title'] = $name;
				$member['isFolder'] = true;
				$member['key'] = $userid;
				
				$teammemList[] = $member;
		}    


        return $this->renderAjax('getteamembers', array('teammemList'=>$teammemList,'list_teammanager' => $list_teammanager));
    }

    /**
     * Get Case Created users Data for filter
     * */
    public function actionGetCaseCreated() {
        $uid = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        $securitycase_ids = "SELECT client_case_id FROM tbl_project_security WHERE user_id=" . $uid . " and team_id=0";
        if ($roleId != 0) {
            $createdBy_sql = "SELECT created_by FROM tbl_client_case WHERE id IN (" . $securitycase_ids . ") GROUP BY created_by";
        } else {
            $createdBy_sql = "SELECT created_by FROM tbl_client_case GROUP BY created_by";
        }
        $list_casecreatedbyuser = ArrayHelper::map(User::find()->select(['id', "CONCAT(usr_first_name,' ',usr_lastname ) as full_name"])->where('id IN (' . $createdBy_sql . ') AND status=1 AND id != 1')->orderBy('usr_lastname')->all(), 'id', 'full_name');
        $casecreatedList = [];
		foreach($list_casecreatedbyuser as $userid => $name) {
			    $casecreatedmember = [];
			
				$casecreatedmember['title'] = $name;
				$casecreatedmember['isFolder'] = true;
				$casecreatedmember['key'] = $userid;
				
				$casecreatedList[] = $casecreatedmember;
		}
        return $this->renderAjax('getcasecreated', array('casecreatedList'=>$casecreatedList,'list_casecreatedbyuser' => $list_casecreatedbyuser));
    }

    /**
     * Get project Submitted users Data for filter
     * */
    public function actionProjectsubmitted() {
        $tasksubmitted_usersql = "SELECT created_by FROM tbl_tasks group by created_by";
        $added = array();
        $list_casemanager = ArrayHelper::map(User::find()->select(['id', "CONCAT(usr_first_name,' ',usr_lastname ) as full_name"])->where('id IN (' . $tasksubmitted_usersql . ') AND status=1 AND id != 1')->orderBy('usr_lastname')->all(), 'id', 'full_name');
        $casemanagerList = [];
		foreach($list_casemanager as $userid => $name) {
			    $casemanager = [];
			
				$casemanager['title'] = $name;
				$casemanager['isFolder'] = true;
				$casemanager['key'] = $userid;
				
				$casemanagerList[] = $casemanager;
		}
        return $this->renderAjax('getcasemembers', array('casemanagerList'=>$casemanagerList,'list_casemanager' => $list_casemanager));
    }

    /**
     * Get project requested users Data for filter
     * */
    public function actionProjectrequested() {
        $list_casemanager = ArrayHelper::map(\app\models\TaskInstruct::find()->select('requestor')->where("RTRIM(LTRIM(requestor)) !='' AND isactive=1")->groupBy('requestor')->orderBy('requestor')->all(), 'requestor', 'requestor');
        $requestedList = [];
		foreach($list_casemanager as $requestor) {
			    $requ = [];
			    $requ['title'] = Html::encode($requestor);
				$requ['isFolder'] = true;
				$requ['key'] = Html::encode($requestor);
				$requestedList[] = $requ;
		}
        return $this->renderAjax('getrequesteds', array('requestedList'=>$requestedList,'list_casemanager' => $list_casemanager));
    }

    /**
     * Get project priority Data for filter
     * */
    public function actionProjectpriority() {
        $priority_data = ArrayHelper::map(PriorityProject::find()->select(["id", "priority"])->where("remove=0")->orderBy("priority_order ASC")->all(), "id", "priority");
        $priorityList = [];
		foreach($priority_data as $p_id => $priority) {
			    $priorities = [];
			    $priorities['title'] = Html::encode($priority);
				$priorities['isFolder'] = true;
				$priorities['key'] = Html::encode($priority);
				$priorityList[] = $priorities;
		}
        /* 
         return $this->renderPartial('projectpriority', array('priority_data' => $priority_data));
        $priority_data_arr = PriorityProject::find()->select(["id","priority"])->orderBy("priority_order ASC")->asArray()->all();
          $priority_data = array();
          if($priority_data_arr){
          $isExist = array();
          foreach($priority_data_arr as $priority){
          $priority_datas[$priority['priority']][$priority['id']] = $priority['id'];
          }
          }
          if(!empty($priority_datas)) {
          foreach($priority_datas as $key => $prioritydata) {
          $priority_data[$key] = implode(",",$prioritydata);
          }
          } */

        //echo "<pre>",print_r($priority_data),"</pre>";die;

        return $this->renderAjax('projectpriority', array('priorityList'=>$priorityList,'priority_data' => $priority_data));
    }

    /* check filter name exist or not */

    public function actionCheckfilterExist() {
        $filter_name = Yii::$app->request->post('filter_name');
        return SavedFilters::find()->where(['user_id'=>Yii::$app->user->identity->id,'filter_type' => 1, 'LOWER(filter_name)' => strtolower($filter_name)])->count();
    }

    /**
     * Saved applied Filter Data 
     * */
    /* public function actionSavefilter()
      {
      $post_data = Yii::$app->request->post();
      $filter_name = Yii::$app->request->post('filter_name');

      unset($post_data['filter_name']);
      if($post_data['previous_submitted_date']=="")unset($post_data['previous_submitted_date']);
      if($post_data['submitted_start_date']=="")unset($post_data['submitted_start_date']);
      if($post_data['submitted_end_date']=="")unset($post_data['submitted_end_date']);
      if($post_data['previous_due_date']=="")unset($post_data['previous_due_date']);
      if($post_data['due_start_date']=="")unset($post_data['due_start_date']);
      if($post_data['due_end_date']=="")unset($post_data['due_end_date']);
      if($post_data['previous_completed_date']=="")unset($post_data['previous_completed_date']);
      if($post_data['completed_start_date']=="")unset($post_data['completed_start_date']);
      if($post_data['completed_end_date']=="")unset($post_data['completed_end_date']);

      $model=new SavedFilters();
      $model->filter_type = 1;
      $model->filter_name = $filter_name;
      $model->user_id = Yii::$app->user->identity->id;
      $model->filter_attributes = json_encode($post_data);
      if($model->save())
      return 'OK';
      else
      return 'Opps Something goes Wrong...';
      } */
    public function actionSavefilter() {
        $filter_name = Yii::$app->request->post('filter_name');
        $post_data = Yii::$app->request->post('post_data');
        //echo "<pre>",print_r($post_data),"</pre>";die;
        $post_data_val = array();
        foreach ($post_data as $k=>$val) {
            if ($val['value'] == 'on')
                $post_data_val[$val['name']] = $val['value'];
            else {
                $val['name'] = rtrim($val['name'], '[]'); // remove name character
                if($val['name'] != 'todoStat' && $val['name'] != 'taskStat' && $val['name'] != 'taskpriority' && $val['name'] != 'clientCases' && $val['name'] != 'teamLocs' && $val['name'] != 'teammanagers' && $val['name'] != 'casemanagers' && $val['name'] != 'casecreatedmanagers' && $val['name'] != 'requestor'){
                    if ($val['name'] != 'previous_submitted_date' && $val['name'] != 'submitted_start_date' && $val['name'] != 'submitted_end_date' && $val['name'] != 'previous_due_date' && $val['name'] != 'due_start_date' && $val['name'] != 'due_end_date' && $val['name'] != 'previous_completed_date' && $val['name'] != 'completed_start_date' && $val['name'] != 'completed_end_date')
                        $post_data_val[$val['name']][] = $val['value'];
                    else
                        $post_data_val[$val['name']] = $val['value'];
                } else {
                    if($val['name'] == 'todoStat'){
                        $todostats=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($todostats)){
                            $todostat=array();
                            foreach($todostats as $todos){
                                array_push($todostat,$todos);
                            }
                            $post_data_val['todotatus']=array_unique($todostat);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'taskStat'){
                        $taskstats=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($taskstats)){
                            $taskstat=array();
                            foreach($taskstats as $ts){
                                array_push($taskstat,$ts);
                            }
                            $post_data_val['taskstatuss']=array_unique($taskstat);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'taskpriority'){
                         $taskpriority=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($taskpriority)){
                            $taskp=array();
                            foreach($taskpriority as $priority){
                                array_push($taskp,$priority);
                            }
                            $post_data_val['taskpriority']=array_unique($taskp);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'requestor'){
                        $requestors=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($requestors)){
                            $requestor=array();
                            foreach($requestors as $requ){
                                array_push($requestor,$requ);
                            }
                            $post_data_val['requestor']=array_unique($requestor);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'casecreatedmanagers'){
                        $casecreatedmanagers=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($casecreatedmanagers)){
                            $casecreatedmanager=array();
                            foreach($casecreatedmanagers as $casemval){
                                array_push($casecreatedmanager,$casemval);
                            }
                            $post_data_val['casecreatedmanagers']=array_unique($casecreatedmanager);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'casemanagers'){
                        $casemanagers=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($casemanagers)){
                            $casemanager=array();
                            foreach($casemanagers as $cmval){
                                array_push($casemanager,$cmval);
                            }
                            $post_data_val['casemanagers']=array_unique($casemanager);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'teammanagers'){
                        $teammanagers=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($teammanagers)){
                            $temmem=array();
                            foreach($teammanagers as $tmval){
                            
                                array_push($temmem,$tmval);
                            }
                            $post_data_val['teammanagers']=array_unique($temmem);
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'clientCases'){
                        $clientCases=json_decode(str_replace("'", '"',$val['value']),true);
                        if(!empty($clientCases)){
                            $cleints=array();
                            $cleintscase=array();
                            foreach($clientCases as $ccval){
                                $implode_cleintcases = explode(",",$ccval);
                                array_push($cleints,$implode_cleintcases[0]);
                                array_push($cleintscase,$implode_cleintcases[1]);	
                            }
                            $post_data_val['cleints']=array_unique($cleints);
                            $post_data_val['cleintscase']=$cleintscase;
                            unset($post_data[$k]);
                        }
                    }
                    if($val['name'] == 'teamLocs'){
                        $teamlocs=json_decode(str_replace("'", '"',$val['value']),true);
                        $teams=array();
                        $teamloc=array();
                        if(!empty($teamlocs)) {
                            foreach($teamlocs as $tlval) { 
                                $implode_teanloc = explode(",",$tlval);
                                array_push($teams,$implode_teanloc[0]);
                                array_push($teamloc,$implode_teanloc[1]);	
                            }
                            $post_data_val['teams']=array_unique($teams);
                            $post_data_val['teamloc']=$teamloc;
                            unset($post_data[$k]);
                        }
                    }
                }
            }
        }
        unset($post_data['filter_name']);
        if ($post_data_val['previous_submitted_date'] == "")
            unset($post_data_val['previous_submitted_date']);
        if ($post_data_val['submitted_start_date'] == "")
            unset($post_data_val['submitted_start_date']);
        if ($post_data_val['submitted_end_date'] == "")
            unset($post_data_val['submitted_end_date']);
        if ($post_data_val['previous_due_date'] == "")
            unset($post_data_val['previous_due_date']);
        if ($post_data_val['due_start_date'] == "")
            unset($post_data_val['due_start_date']);
        if ($post_data_val['due_end_date'] == "")
            unset($post_data_val['due_end_date']);
        if ($post_data_val['previous_completed_date'] == "")
            unset($post_data_val['previous_completed_date']);
        if ($post_data_val['completed_start_date'] == "")
            unset($post_data_val['completed_start_date']);
        if ($post_data_val['completed_end_date'] == "")
            unset($post_data_val['completed_end_date']);
        //echo "<pre>",print_r($post_data_val);die;
        $model = new SavedFilters();
        $model->filter_type = 1;
        $model->filter_name = $filter_name;
        $model->user_id = Yii::$app->user->identity->id;
        $model->filter_attributes = json_encode($post_data_val);
        if ($model->save())
            return 'OK';
        else
            return 'Opps Something goes Wrong...';
    }

    /**
     * get Saved Filter List
     * */
    public function actionGetsavedfilters() {
        $filter_da = SavedFilters::find()->select(['id', 'filter_name', 'filter_attributes'])->where(['filter_type' => 1, 'user_id' => Yii::$app->user->identity->id])->orderBy('filter_name')->asArray()->all();
        $list_status = array(0 => 'Not Started', 1 => 'Started', 3 => 'On Hold', 4 => 'Completed', 6 => 'Past Due', 7 => 'Due Today', 8 => 'Closed', '9' => 'Cancel');
        $list_todostatus = array(7 => 'ToDo Assigned', 8 => 'ToDo Transitioned', 9 => 'ToDo Completed', 11 => 'ToDo Transferred', 13 => 'ToDo Started', 14 => 'ToDo Not Started');
        if (!empty($filter_da)) {
            foreach ($filter_da as $id => $name) {
                $data = json_decode($name['filter_attributes']);
                $title = "";
                if (!empty($data)) {
                    $flag = false;
                    foreach ($data as $key => $value) {
                        if(is_object($data->$key)){
                        	$data->$key = json_decode(json_encode($data->$key), true);
                        }
                        if ($key == 'cleints') {
                            $client_ids = ((count($data->$key) > 1) ? implode(',', $data->$key) : $value[0]);
                            if (Yii::$app->db->driverName == 'mysql') {
                                $client_name = ArrayHelper::map(Client::find()->select('client_name')->where('tbl_client.id IN (' . $client_ids . ')')->asArray()->all(), 'client_name', 'client_name');
                            } else {
                                $arrays = explode(",",$client_ids);
                                array_walk($arrays, function (&$value, $key) {
                                    $value=" SELECT $value as id ";
                                });
                                $invaluetable = implode(' UNION ALL ',$arrays);
                                $client_name = ArrayHelper::map(Client::find()->select('client_name')->innerJoin("($invaluetable) as A", 'tbl_client.id = A.id')->asArray()->all(), 'client_name', 'client_name');
                            }
                            $title .= "Clients: ";
                            $title .= implode(', ', $client_name);
                            $flag = true;
                        }
                        if ($key == 'cleintscase') {
                            $client_caseids = ((count($data->$key) > 1) ? implode(',', $data->$key) : $value[0]);
                            if (Yii::$app->db->driverName == 'mysql') {
                                $client_casename = ArrayHelper::map(ClientCase::find()->select('case_name')->where('tbl_client_case.id IN (' . $client_caseids . ')')->asArray()->all(), 'case_name', 'case_name');
                            } else {
                                $arrays = explode(",",$client_caseids);
                                array_walk($arrays, function (&$value, $key) {
                                    $value=" SELECT $value as id ";
                                });
                                $invaluetable = implode(' UNION ALL ',$arrays);
                                $client_casename = ArrayHelper::map(ClientCase::find()->select('case_name')->innerJoin("($invaluetable) as A", 'tbl_client_case.id = A.id')->asArray()->all(), 'case_name', 'case_name');
                            }
                            $title .= (($flag == true) ? "; ClientCases: " : "ClientCases: ");
                            $title .= implode(', ', $client_casename);
                            $flag = true;
                        }
                        if ($key == 'teams') {
                            $team_ids = ((count($data->$key) > 1) ? implode(',', $data->$key) : $value[0]);
                            if (Yii::$app->db->driverName == 'mysql') {
                                $team_name = ArrayHelper::map(Team::find()->select('team_name')->where('tbl_team.id IN (' . $team_ids . ')')->asArray()->all(), 'team_name', 'team_name');
                            } else {
                                $arrays = explode(",",$team_ids);
                                array_walk($arrays, function (&$value, $key) {
                                    $value=" SELECT $value as id ";
                                });
                                $invaluetable = implode(' UNION ALL ',$arrays);
                                $team_name = ArrayHelper::map(Team::find()->select('team_name')->innerJoin("($invaluetable) as A", 'tbl_team.id = A.id')->asArray()->all(), 'team_name', 'team_name');
                            }
                            $title .= (($flag == true) ? "; Teams: " : "Teams: ");
                            $title .= implode(', ', $team_name);
                            $flag = true;
                        }
                        if ($key == 'casecreatedmanagers') {
                            $user_ids = ((count($data->$key) > 1) ? implode(',', $data->$key) : $value[0]);
                            if (Yii::$app->db->driverName == 'mysql') {
                                $user_name = ArrayHelper::map(User::find()->select(["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as value"])->where('tbl_user.id IN (' . $user_ids . ')')->asArray()->all(), 'value', 'value');
                            } else {
                                $arrays = explode(",",$user_ids);
                                array_walk($arrays, function (&$value, $key) {
                                    $value=" SELECT $value as id ";
                                });
                                $invaluetable = implode(' UNION ALL ',$arrays);
                                $user_name = ArrayHelper::map(User::find()->select(["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as value"])->innerJoin("($invaluetable) as A", 'tbl_user.id = A.id')->asArray()->all(), 'value', 'value');
                            }
                            $title .= (($flag == true) ? "; By Case Created: " : "By Case Created: ");
                            $title .= implode(', ', $user_name);
                            $flag = true;
                        }
                        if ($key == 'casemanagers') {
                            $user_ids_manage = ((count($data->$key) > 1) ? implode(',', $data->$key) : $value[0]);
                            if (Yii::$app->db->driverName == 'mysql') {
                                $user_name_manage = ArrayHelper::map(User::find()->select(["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as value"])->where('tbl_user.id IN (' . $user_ids_manage . ')')->asArray()->all(), 'value', 'value');
                            } else {
                                $arrays = explode(",",$user_ids_manage);
                                array_walk($arrays, function (&$value, $key) {
                                    $value=" SELECT $value as id ";
                                });
                                $invaluetable = implode(' UNION ALL ',$arrays);
                                $user_name_manage = ArrayHelper::map(User::find()->select(["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as value"])->innerJoin("($invaluetable) as A", 'tbl_user.id = A.id')->asArray()->all(), 'value', 'value');
                            }
                            $title .= (($flag == true) ? "; By Project Submitted: " : "By Project Submitted: ");
                            $title .= implode(', ', $user_name_manage);
                            $flag = true;
                        }
                        if ($key == 'taskpriority') {
                            $priority_ids = ((count($data->$key) > 1) ? implode("','", $data->$key) : $value[0]);
                            $priority_ids = "'" . $priority_ids . "'";
                            $project_priority = ArrayHelper::map(PriorityProject::find()->select("tbl_priority_project.priority")->where('tbl_priority_project.priority IN (' . $priority_ids . ')')->orderby('tbl_priority_project.priority_order')->asArray()->all(), 'priority', 'priority');
                            $title .= (($flag == true) ? "; Project Priority: " : "Project Priority: ");
                            $title .= implode(', ', $project_priority);
                            $flag = true;
                        }
                        if ($key == 'taskstatuss') {
                            $status = [];
                            if (!is_array($data->$key)) {
                                $data->$key = explode(',', $data->$key);
                            }
                            foreach ($data->$key as $tasklist) {
                                $status[] = $list_status[$tasklist];
                            }
                            $title .= (($flag == true) ? "; Project Status: " : "Project Status: ");
                            $title .= implode(', ', $status);
                            $flag = true;
                        }
                        if ($key == 'previous_submitted_date') {
                            $title .= (($flag == true) ? "; Project Submitted Date: " : "Project Submitted Date: ");
                            if ($value == 'T')
                                $title .= 'Today';
                            elseif ($value == 'Y')
                                $title .= 'Yesterday';
                            elseif ($value == 'W')
                                $title .= 'Last Week';
                            elseif ($value == 'M')
                                $title .= 'Last Month';
                            else    
                                $title .= $value;    
                            $flag = true;
                        }
                        if ($key == 'submitted_start_date') {
                            $title .= (($flag == true) ? "; Project Submitted Start Date: " : "Project Submitted Start Date: ");
                            $title .= $value;
                            $flag = true;
                        }
                        if ($key == 'submitted_end_date') {
                            $title .= (($flag == true) ? "; Project Submitted End Date: " : "Project Submitted End Date: ");
                            $title .= $value;
                            $flag = true;
                        }
                        if ($key == 'previous_due_date') {
                            $title .= (($flag == true) ? "; Project Due Date: " : "Project Due Date: ");
                            if ($value == 'T')
                                $title .= 'Today';
                            elseif ($value == 'Y')
                                $title .= 'Yesterday';
                            elseif ($value == 'W')
                                $title .= 'Last Week';
                            elseif ($value == 'M')
                                $title .= 'Last Month';
                            else    
                                $title .= $value;    
                            $flag = true;
                        }
                        if ($key == 'due_start_date') {
                            $title .= (($flag == true) ? "; Project Due Start Date: " : "Project Due Start Date: ");
                            $title .= $value;
                            $flag = true;
                        }
                        if ($key == 'due_end_date') {
                            $title .= (($flag == true) ? "; Project Due End Date: " : "Project Due End Date: ");
                            $title .= $value;
                            $flag = true;
                        }
                        if ($key == 'previous_completed_date') {
                            $title .= (($flag == true) ? "; Project Completed Date: " : "Project Completed Date: ");
                            if ($value == 'T')
                                $title .= 'Today';
                            elseif ($value == 'Y')
                                $title .= 'Yesterday';
                            elseif ($value == 'W')
                                $title .= 'Last Week';
                            elseif ($value == 'M')
                                $title .= 'Last Month';
                            else    
                                $title .= $value;

                            $flag = true;
                        }
                        if ($key == 'completed_start_date') {
                            $title .= (($flag == true) ? "; Project Completed Start Date: " : "Project Completed Start Date: ");
                            $title .= $value;
                            $flag = true;
                        }
                        if ($key == 'completed_end_date') {
                            $title .= (($flag == true) ? "; Project Completed End Date: " : "Project Completed End Date: ");
                            $title .= $value;
                            $flag = true;
                        }
                        if ($key == 'requestor') {
                            $status = [];
                            if (!is_array($data->$key)) {
                                $data->$key = explode(',', $data->$key);
                            }
                            foreach ($data->$key as $reqlist) {
                                $status[] = $reqlist;
                            }
                            $title .= (($flag == true) ? "; Project Requester: " : " Project Requester: ");
                            $title .= implode(', ', $status);
                            $flag = true;
                        }
                        if ($key == 'todotatus') {
                            $status = [];
                            if (!is_array($data->$key)) {
                                $data->$key = explode(',', $data->$key);
                            }
                            if (!empty($data->$key)) {
                                foreach ($data->$key as $todolist) {
                                    if (isset($list_todostatus[$todolist]) && $list_todostatus[$todolist] != '')
                                        $status[] = $list_todostatus[$todolist];
                                }
                                if (!empty($status)) {
                                    //echo '<pre>',print_r($data->$key);echo '</pre>';
                                    $title .= (($flag == true) ? "; Todo Status: " : "Todo Status: ");
                                    $title .= implode(', ', $status);
                                    $flag = true;
                                }
                            }
                        }
                        if ($key == 'teammanagers') {
                            $status = [];
                            if (!is_array($data->$key)) {
                                $data->$key = explode(',', $data->$key);
                            }
                            $list_teammanager = ArrayHelper::map(User::find()->select(['id', 'usr_first_name', 'usr_lastname', 'usr_username', "CONCAT(usr_first_name,' ',usr_lastname ) as full_name"])->where('status=1')->andWhere('tbl_user.id != 1')->andWhere('id IN (' . implode(',', $data->$key) . ')')->orderBy('usr_lastname')->all(), 'id', function($model) {
                                        if ($model->usr_first_name == '' && $model->usr_lastname == '') {
                                            $cUserName = $model->usr_username;
                                        } else {
                                            $cUserName = ucwords($model->full_name);
                                        } return $cUserName;
                                    });
                            if (!empty($list_teammanager)) {
                                $title .= (($flag == true) ? "; Team Members: " : "Team Members: ");
                                $title .= implode(', ', $list_teammanager);
                                $flag = true;
                            }
                        }
                    }
                }
//				if($title == '') echo $id.'<br>';
                $filter_da[$id]['title'] = $title;
            }
        }
//                die;
//        echo '<pre>';print_r($filter_da);die;
        return $this->renderPartial('savedfilters', array('filter_data' => $filter_da));
    }

    /**
     * Delete Save Filter
     * */
    public function actionDeletesaveFilter() {
        $filter_id = Yii::$app->request->post('filter_id', 0);
        $model = SavedFilters::findOne($filter_id);
        $model->delete();
        return;
    }
    /**
     * Check Save Filter Exist
     * */
    public function actionCheckSavedfilter(){
        $filter_id = Yii::$app->request->post('filter_id', 0);
        $filter_data = SavedFilters::findOne($filter_id);
        if(isset($filter_data->id)){
            return "OK";
        }
        return "ERROR";
    }

    /**
     * ReOpen closed projects for selected Filter id.
     * @return mixed
     * @param integer $filter_id
     * @param integer $task_list
     */
    public function actionReopenProjects() {
        $filter_id = HtmlPurifier::process(Yii::$app->request->get('filter_id', 0));
        $flag = HtmlPurifier::process(Yii::$app->request->post('flag', 'all'));
        if ($flag == 'all') {
            $filter_data = SavedFilters::findOne($filter_id);
            $filterParams = json_decode($filter_data->filter_attributes, true);
            if(!empty($filterParams))
                Yii::$app->request->queryParams=array_merge(Yii::$app->request->queryParams,$filterParams);
            //Yii::$app->request->queryParams += $filterParams;
            Yii::$app->request->queryParams += Yii::$app->request->post();
            $searchModel = new TaskSearch();
            $dataProvider = $searchModel->searchGlobalProject(Yii::$app->request->queryParams);
            $task_list = ArrayHelper::map($dataProvider->getModels(), "id", "id");
        } else {
            $task_list = Yii::$app->request->post('task_list', array());
        }
        $hasclosedcompleted = "Y";
        foreach ($task_list as $task_id) {
            $task_info = Tasks::findOne($task_id);
            if ($task_info->task_status == 4 && $task_info->task_closed == 1) {
                $hasclosedcompleted = "Y";
            } else {
                $hasclosedcompleted = "N";
                break;
            }
        }
        if ($hasclosedcompleted == "Y") {
            Tasks::updateAll(['task_closed' => 0], ['in', 'id', $task_list]);
            foreach ($task_list as $task_id) {
                (new ActivityLog())->generateLog('Project', 'Reopen', $task_id, 'project#:' . $task_id);
                /* Start : Sending ReOpen Project Subscription Alert Email */
                $task_info = Tasks::find()->select('client_case_id')->where('id=' . $task_id)->one();
                //SettingsEmail::sendEmail
                EmailCron::saveBackgroundEmail(8, 'is_reopen_project', $data = array('case_id' => $task_info->client_case_id, 'project_id' => $task_id));
                /* End : Sending ReOpen Project Subscription Alert Email */
            }
            return 'OK';
        }
        return $hasclosedcompleted;
    }

    /**
     * close Completed projects for selected Filter id.
     * @return mixed
     * @param integer $filter_id
     * @param integer $task_list
     */
    public function actionCloseProjects() {        
        $filter_id = HtmlPurifier::process(Yii::$app->request->get('filter_id', 0));
        $flag = HtmlPurifier::process(Yii::$app->request->post('flag', 'all'));
        if ($flag == 'all') {
            $filter_data = SavedFilters::findOne($filter_id);
            $filterParams = json_decode($filter_data->filter_attributes, true);            
            if(!empty($filterParams))
                Yii::$app->request->queryParams=array_merge(Yii::$app->request->queryParams,$filterParams);
            //Yii::$app->request->queryParams += $filterParams;
            Yii::$app->request->queryParams += Yii::$app->request->post();
            $searchModel = new TaskSearch();
            $dataProvider = $searchModel->searchGlobalProject(Yii::$app->request->queryParams);            
            $task_list = ArrayHelper::map($dataProvider->getModels(), "id", "id");
        } else {
            $task_list = Yii::$app->request->post('task_list', array());
        }
        $hasclosedcompleted = "Y";
        foreach ($task_list as $task_id) {
            $task_info = Tasks::findOne($task_id);
            if ($task_info->task_status == 4 && $task_info->task_closed == 0) {
                $hasclosedcompleted = "Y";
            } else {
                $hasclosedcompleted = "N";
                break;
            }
        }
        if ($hasclosedcompleted == "Y") {
            Tasks::updateAll(['task_closed' => 1], ['in', 'id', $task_list]);
            $actLog = new ActivityLog();
            $log = array();
            foreach ($task_list as $task_id) {
                $log[] = array('date_time' => date('Y-m-d H:i:s'), 'user_id' => Yii::$app->user->identity->id, 'username' => Yii::$app->user->identity->usr_username, 'origination' => 'Project', 'activity_type' => 'Closed', 'activity_module_id' => $task_id, 'activity_name' => 'project#:' . $task_id, 'task_cancel_reason' => '');
            }
            $actLog->generateBulkLog($log);

            return 'OK';
        }
        return $hasclosedcompleted;
    }

}
