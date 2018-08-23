<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

use app\models\Client;
use app\models\ClientCase;
use app\models\CommentsRead;
use app\models\search\ClientSearch;
use app\models\Tasks;
use app\models\SummaryComment;
use app\models\TaskInstruct;
use app\models\User;
use app\models\Options;
use app\models\ProjectSecurity;
use app\models\PriorityProject;

class MycaseController extends \yii\web\Controller
{
	
	public function beforeAction($action) {
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
	
	
		if (!(new User)->checkAccess(4))/* 38 */{
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
  
    /* load my cases */
    public function actionIndex() {
        $time_start = microtime(true);
        
        $this->layout = "main";
        
        $roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
        
        $clientDropdownData = $this->getClientDropdownData($userId, $roleId);
		//echo "<pre>",print_r($clientDropdownData),"</pre>";
		//die;
		
        $searchModel = new ClientSearch();
		       
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$params); 
        
        //echo "<pre>"; print_r($dataProvider->getModels()); exit;
       
        return $this->render('index', [
            'searchModel' => $searchModel,
        	'dataProvider' => $dataProvider,
			'userId' => $userId,
			'clientDropdownData' => $clientDropdownData,
	    ]);			    
    }
	public function actionClientcasejsonlist(){
		$params = Yii::$app->request->queryParams;
		$page=isset($params['page'])?$params['page']:1;
		$limit=50;
		$mssql="OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY;";
        $mysql="LIMIT $limit OFFSET 0";
		$offset=( ( $page - 1 ) * $limit );
        if(Yii::$app->db->driverName == 'mysql') {
        	$mysql="LIMIT $limit OFFSET $offset";
            $limit_sql=$mysql;
        } else {
        	$mssql="OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY;";
            $limit_sql=$mssql;
        }
		$roleId = Yii::$app->user->identity->role_id;
		$userId = Yii::$app->user->identity->id; 
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out['items'] = array();
		$out['total_count']=0;
		$out['pagination']['more']=false;
		$q=$params['q'];
		$filterWhere="";
		if(trim($q)!=""){
			$filterWhere=" AND (CONCAT(case_name,' - ', tbl_client.client_name) LIKE '%$q%') ";
		}
		if ($roleId != 0) {
			$caseList_sql = "SELECT tbl_client_case.id, case_name AS name, tbl_client.client_name 
 FROM tbl_client_case 
 INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id
 WHERE
 (tbl_client_case.id IN (SELECT DISTINCT client_case_id FROM tbl_project_security WHERE client_id!=0 AND  client_case_id !=0  AND user_id=$userId AND team_id=0)) 
 AND (is_close=0) $filterWhere
 ORDER BY case_name $limit_sql";
 $caseList_countsql = "SELECT count(*) 
 FROM tbl_client_case 
 INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id
 WHERE (tbl_client_case.id IN (SELECT DISTINCT client_case_id FROM tbl_project_security WHERE client_id!=0 AND  client_case_id !=0  AND user_id=$userId AND team_id=0)) AND (is_close=0) $filterWhere ";
                $caseList = Yii::$app->db->createCommand($caseList_sql)->queryAll();
				$out['total_count'] = Yii::$app->db->createCommand($caseList_countsql)->queryScalar();
                if(!empty($caseList)){
                    foreach($caseList as $case){
                       $val=Html::decode($case['client_name']." - ".$case['name']);
					   $out['items'][] = ['id' => $case['id'], 'text' => $val];
                    }
                }
        } else {
				$caseList_sql = "SELECT tbl_client_case.id, case_name AS name, client_name FROM tbl_client_case 
INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id
WHERE (is_close=0) $filterWhere ORDER BY case_name $limit_sql";
                $caseList_countsql="SELECT count(*) FROM tbl_client_case 
INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id
WHERE (is_close=0) $filterWhere ";
				$caseList = Yii::$app->db->createCommand($caseList_sql)->queryAll();

				$out['total_count'] = Yii::$app->db->createCommand($caseList_countsql)->queryScalar();
                if(!empty($caseList)){
                    foreach($caseList as $case){
                        //$data[$case['id']]=Html::decode($case['client_name']." - ".$case['name']);
						$val=Html::decode($case['client_name']." - ".$case['name']);
					    $out['items'][] = ['id' => $case['id'], 'text' => $val];
                    }
                }
            }
		if($out['total_count'] > 0 && ($page * $limit) < $out['total_count']){
			$out['pagination']['more']=true;
		}
		return $out;
	}
    /* Get client dropdown data */
    public function getClientDropdownData($userId, $roleId=0) {
       
	$condition = "client_id!=0 AND team_id=0";
        if ($roleId != 0) {
        	$condition = "user_id=$userId AND team_id=0";
        }
	if ($roleId != 0) {
		$sql="SELECT tbl_client.id, client_name FROM tbl_client 
		INNER JOIN tbl_project_security ON tbl_client.id = tbl_project_security.client_id 
		WHERE client_id!=0 AND client_case_id !=0 AND team_id=0 AND user_id = ".$userId." group by tbl_client.id,tbl_client.client_name order by client_name";
	}else{
		/*$sql="SELECT tbl_client.id, client_name FROM tbl_client 
		INNER JOIN tbl_project_security ON tbl_client.id = tbl_project_security.client_id 
		WHERE client_id!=0 AND client_case_id !=0 AND team_id=0 group by tbl_client.id,tbl_client.client_name order by client_name";*/
		$sql="SELECT tbl_client.id, client_name FROM tbl_client group by tbl_client.id,tbl_client.client_name order by client_name";
	}
	//echo $sql;die;
	$client_data =Yii::$app->db->createCommand($sql)->queryAll();
	//	$client_data = Client::find()->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) { $query->select(['client_id']); }])->select(['tbl_client.id', 'client_nam'])->where($condition)->asArray()->all();
		return $client_data;
    }
    /* Get clientwise case data */
    public function actionGetcasesbyclient() 
    {
		$params = Yii::$app->request->post('depdrop_parents', 0);
		$client_id = $params[0];
		if(isset($client_id) && $client_id!=0 && $client_id!="")
		{
			$roleId = Yii::$app->user->identity->role_id;
			$userId = Yii::$app->user->identity->id; 
			if ($roleId != 0) {
				$case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id='.$client_id.' AND user_id='.$userId.' AND team_id=0');
				$caseList = ClientCase::find()->select(['id', 'case_name as name'])->where(['in', 'id', $case_data])->andWhere(['is_close'=>0])->orderBy('case_name')->asArray()->all();
			} else {
				$caseList = ClientCase::find()->select(['id', 'case_name as name'])->where([ 'client_id' => $client_id])->andWhere(['is_close'=>0])->orderBy('case_name')->asArray()->all();
			}
	 
			echo Json::encode(['output'=>ArrayHelper::htmlDecode($caseList), 'selected'=>'']);
			return;    	
		}
		echo Json::encode(['output'=>'', 'selected'=>'']);
    }	
    /* Get case details by client */
    public function actionGetcasedetailsbyclient() 
    {
        $firstload = Yii::$app->request->post('firstload');
        $client_id = Yii::$app->request->post('expandRowKey');
        $client_case_data = array();
        $userId  = Yii::$app->user->identity->id; 
        $roleId  = Yii::$app->user->identity->role_id;
        $session = new Session;
        $session->open();
        if(!isset($session['is_accessible_submodul'])) {
        	$session['is_accessible_submodul']=(new User)->checkAccess(4.02);
        }
        $is_accessible_submodule = $session['is_accessible_submodul'];
        if (isset($client_id)) {
          
	    $securitycase_ids = array(0);
	    
	    $casedetail_query = ClientCase::find()->select(['tbl_client_case.id', 'tbl_client_case.case_name', 'COUNT(tbl_tasks.id) as task_count']);
			if($roleId != 0){
				$casedetail_query
				->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) use ($userId, $client_id){ 
						$query->select('tbl_project_security.client_case_id')->where(['tbl_project_security.user_id' => $userId, 'tbl_project_security.client_id' => $client_id]); 
				}],false);
			}else{
				$casedetail_query->where('tbl_client_case.client_id = '.$client_id);
			}
			$casedetail_query
			->innerJoinWith(['tasks' => function (\yii\db\ActiveQuery $query) {
						 $query->select(['tbl_tasks.client_case_id'])
						 ->where('tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0')
						 ->innerJoinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) { 
						 	 	$query->select(['tbl_task_instruct.task_id'])->where(['tbl_task_instruct.isactive' => 1]); 
						 }],false); 
			}],false)
			->where(['tbl_client_case.is_close' => 0, 'tbl_client_case.client_id' => $client_id])
			->groupBy(['tbl_client_case.id', 'tbl_client_case.case_name'])
			->orderBy('tbl_client_case.case_name');
		$data=$casedetail_query->all();			        
		 //echo "<pre>",print_r($data),"</pre>";die;
           if(!empty($data)){
			   foreach ($data as $da) {
					$active_tasks = $da->task_count;
					if (strip_tags($active_tasks) > 0) {
						if ($is_accessible_submodule == 0) {
							$header_assigned = (new Tasks)->getCaseProjectsUnassign($da->id, $is_accessible_submodule);
						} else {
							$header_assigned = (new Tasks)->getCaseProjectsUnassign($da->id);
						}
						$todos = (new Tasks)->getCaseTodos($da->id);
						$unread_comments = (new Tasks)->getUnreadComments($da->id, "comment");
						$unread_case_comments = (new SummaryComment)->getUnreadComments($da->id,0,0, "comment");
						$client_case_data[$da->id] = array(
							'case_name' => $da->case_name,
							'active_projects' => (new User)->checkAccess(4.01)?Html::a($active_tasks, "javascript:void(0);", ["data-pjax"=>"0",'onclick'=>'window.location.href="index.php?r=case-projects/index&case_id=' . $da->id . '&active=active";', "title" => $active_tasks." Active Projects"]) : $active_tasks,
							'active_todos' => (new User)->checkAccess(4.01)?$todos:strip_tags($todos),
							'unread_comments' => $unread_comments,
							'unread_case_comments'=> $unread_case_comments,
							'unassigned_projects' => $header_assigned
						);
					}
				}
          }
		  //echo "<pre>",print_r($client_case_data),"</pre>";die;
        }
		return $this->renderPartial('_getclientwisecasedetail', ['client_case_data'=>$client_case_data, 'firstload' => $firstload, 'client_id' => $client_id]);
    }
    /* Get project status chart data for selected client / case on My Cases landing page */
    public function actionGetprojectstatuschartdata() {
        
        $caseId = Yii::$app->request->post('caseId',0);
		$type = Yii::$app->request->post('type','case');
        $userId = Yii::$app->user->identity->id; 
		$roleId = Yii::$app->user->identity->role_id;
        if ($caseId == '' || $caseId == 0) {
            throw new yii\web\HttpException(404, 'The specified post cannot be found.');
        }
        $status = array(0 => 'Not Started', 1 => 'Started', 3 => 'On Hold');
	
		if($type=='client'){
	    	$projectstatus_query = ClientCase::find()->select('tbl_client_case.id');
	    if($roleId!=0){
			$projectstatus_query->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) use ($userId, $caseId){ $query->select('DISTINCT(tbl_project_security.client_case_id)')->where(['tbl_project_security.user_id' => $userId, 'tbl_project_security.client_id' => $caseId]); }],false);
		}
	    
	    $projectstatus_query->where(['tbl_client_case.is_close'=>0, 'tbl_client_case.client_id'=>$caseId]);
	    $projectstatus_query->innerJoinWith(['tasks' => function (\yii\db\ActiveQuery $query) { 
					$query->select('tbl_tasks.id')->where('tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0')->innerJoinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) { $query->where('tbl_task_instruct.isactive=1'); 
				} 
			],false);
		}],false);
	    $projectstatus_query->asArray();
	    //echo $projectstatus_query;
	    $data = $projectstatus_query->all();
	    
	    //echo "<pre>"; print_r($data); exit;
	    
	    $case_ids=0;
	    $casechart = 0;
	    if(!empty($data)){
		    foreach ($data as $val){
				    $case_ids.=",".$val['id'];
				    if(isset($_REQUEST['firstload'])  && $_REQUEST['firstload'] == 1){
					    $case_ids = $val['id'];break;
				    }
			    
		    }
	    }
	}
	
        $past_due_data = array();
        $data = array();
        $line1 = array();
        $line2 = array();
        $yaxislbl = array();
		$status_val_pd=array();
		$status_val_active=array();
		if($type=='case')
	    {
			$past_due_all=(new Tasks)->getPastDueCaseTasksByStatusGroup($caseId, $k);
			$active_all=(new Tasks)->getPastDueCaseTasksByStatusGroup($caseId, $k, "active");
		}else{
			$past_due_all=(new Tasks)->getPastDueClientTasksByStatusGroup($caseId, '', $case_ids);
			$active_all=(new Tasks)->getPastDueClientTasksByStatusGroup($caseId, '', $case_ids, "active");
		}
		if(!empty($past_due_all)) {
			foreach($past_due_all as $pddata) {
				$status_val_pd[$pddata->task_status]=$pddata->cnttasks;
			}
		}
		if(!empty($active_all)) {
			foreach($active_all as $adata) {
				$status_val_active[$adata->task_status]=$adata->cnttasks;
			}
		}
		//echo "<pre>",print_r($status_val_pd),print_r($status_val_active),print_r($past_due_all),"</pre>";die;
        foreach ($status as $k => $v) {
	    $past_due_count = 0;
		$active_count = 0;
		if(isset($status_val_pd[$k]))
			$past_due_count = $status_val_pd[$k];

		if(isset($status_val_active[$k]))
			$active_count = $status_val_active[$k];	
		/*if($type=='case') {
		//$past_due_count = (new Tasks)->getPastDueCaseTasksByStatus($caseId, $k);
		//$active_count = (new Tasks)->getPastDueCaseTasksByStatus($caseId, $k, "active");
	    } else {
		//$past_due_count =(new Tasks)->getPastDueClientTasksByStatus($caseId, $k, $case_ids);
		//$active_count = (new Tasks)->getPastDueClientTasksByStatus($caseId, $k, $case_ids, "active");
	    }*/
           
		$line1[] = intval($past_due_count);
		$line2[] = intval($active_count);
		$total[] = intval($active_count) +  intval($past_due_count);
		$yaxislbl[] = $v;
	    
	    $data['past_due'][$k]['y'] = intval($past_due_count);
	    $data['active'][$k]['y'] = intval($active_count);
	    			
        }
        //echo "<pre>"; print_r($data); 
        echo json_encode($data);
        die;
    }
	/* Get project priority chart data for selected client / case on My Cases landing page */
    public function actionGetprojectprioritychartdata() {
       
        $caseId = Yii::$app->request->post('caseId',0);
        $type = Yii::$app->request->post('type','case');
		$userId = Yii::$app->user->identity->id; 
		$roleId = Yii::$app->user->identity->role_id;
        if ($caseId == '' || $caseId == 0) {
            throw new yii\web\HttpException(404, 'The specified post cannot be found.');
        }
		$where = "";
		$params = [];
		$query = "SELECT tbl_task_instruct.task_priority, count(tbl_tasks.id) as cnttasks, tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order FROM tbl_tasks INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id AND (isactive=1) INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id";
					  
		if($type=='client')
		{
			$query .= " LEFT JOIN tbl_project_security ON tbl_tasks.client_case_id = tbl_project_security.client_case_id AND ((tbl_project_security.user_id=$userId) AND (tbl_project_security.client_id=$caseId))";	
			$where .= " WHERE ((tbl_tasks.client_id = $caseId))"; 
			$params[':user_id'] = $userId;
			$params[':client_id'] = $caseId;
		}
		if($type=='case')
		{
			$where .= " WHERE tbl_tasks.client_case_id = $caseId";
			$params[':case_id'] = $caseId;
		}
		$query .= $where." AND (((task_status IN (0,1,3) and task_closed=0 and task_cancel=0)))  GROUP BY tbl_task_instruct.task_priority,tbl_priority_project.id, tbl_priority_project.priority, tbl_priority_project.priority_order";
		if($type=='client'){
			if($roleId!=0){
				$query = "SELECT tbl_task_instruct.task_priority, tbl_priority_project.id,tbl_priority_project.priority,tbl_priority_project.priority_order,COUNT(tbl_tasks.id) as cnttasks FROM tbl_client_case 
				  INNER JOIN tbl_project_security ON tbl_client_case.id = tbl_project_security.client_case_id 
				  INNER JOIN tbl_tasks ON tbl_client_case.id = tbl_tasks.client_case_id 
				  INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id 
				  INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id 
				  WHERE (
				  ((tbl_client_case.is_close=0) AND (tbl_client_case.client_id=$caseId)) 
				  AND ((tbl_project_security.user_id=$userId) AND (tbl_project_security.client_id=$caseId))) 
				  AND ((tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0) AND (tbl_task_instruct.isactive=1)) 
				  GROUP BY tbl_priority_project.id,tbl_task_instruct.task_priority, tbl_priority_project.priority, tbl_priority_project.priority_order";
			  }else{
				$query = "SELECT tbl_task_instruct.task_priority, tbl_priority_project.id,tbl_priority_project.priority,tbl_priority_project.priority_order,COUNT(tbl_tasks.id) as cnttasks FROM tbl_client_case 
				  INNER JOIN tbl_tasks ON tbl_client_case.id = tbl_tasks.client_case_id 
				  INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id 
				  INNER JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id 
				  WHERE (
				  ((tbl_client_case.is_close=0) AND (tbl_client_case.client_id=$caseId)) 
				  ) 
				  AND ((tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0) AND (tbl_task_instruct.isactive=1)) 
				  GROUP BY tbl_priority_project.id,tbl_task_instruct.task_priority, tbl_priority_project.priority, tbl_priority_project.priority_order";		  
			  }
		}
		
		$priority_data = Yii::$app->db->createCommand($query)->queryAll();
		//echo $query;
		//echo "<pre>"; print_r($priority_data); exit;
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
		    
		    
	 /*   $sort_data[$porder] = array(
		                $count,
		                $priority,
		                $id,
		        ); */
	}
        $finalarr = array_reverse($sort_data);
        echo json_encode($finalarr);
        die;
    }
    /* show clients in ajax action for select cases dialog box in my case landing page */
    public function actionViewclients()
    {
	$roleId = Yii::$app->user->identity->role_id;
    $userId = Yii::$app->user->identity->id;
	$case_ids = Yii::$app->request->post('case_ids',0);
	$client_id = Yii::$app->request->post('client_id',0);
	$client_data = $this->getClientDropdownData($userId, $roleId);
        return $this->renderPartial('_selectcases', ['client_data'=>$client_data, 'case_ids' => $case_ids, 'client_id' => $client_id]);
    }
    /* get cases list for selected client checkbox in select cases dialog box in my case landing page */
    public function actionGetcaselistbyclient()
    {
	$client_id = Yii::$app->request->post('client_id',0);
	$case_ids = explode(",",Yii::$app->request->post('case_ids',0));
	
	if(isset($client_id) && $client_id!=0 && $client_id!="")
	{
	    $roleId = Yii::$app->user->identity->role_id;
	    $userId = Yii::$app->user->identity->id; 
	    
	    if ($roleId != 0) {
		    $case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id='.$client_id.' AND user_id='.$userId.' AND team_id=0');
		    $caseList = ClientCase::find()->select(['id', 'case_name as name'])->where(['in', 'id', $case_data])->andWhere(['is_close'=>0])->orderBy('case_name')->asArray()->all();
	    } else {
		    $caseList = ClientCase::find()->select(['id', 'case_name as name'])->where([ 'client_id' => $client_id,'is_close'=>0])->orderBy('case_name')->asArray()->all();
	    }
 	
		$html='<ul>';
		foreach($caseList as $list){
		    
		    $html.='<li><span>'.$list['name'].'</span>';
		    if(in_array($list['id'], $case_ids))
				$html.='<div class="pull-right "> 
    				<input rel="'.$list['id'].'" onclick="hideotherclientcheckboxes(\''.$client_id.'\', \''.$list['id'].'\', this.checked);"  id="chk_'.$list['id'].'" type="checkbox" class="case_checkbox chk_'.$client_id.'"  name="cases['.$client_id.']" value="'.$list['id'].'" title="'.$list['name'].'" checked="checked" aria-label="Hide other clients">
    				<label for="chk_'.$list['id'].'"><span class="sr-only">Hide other clients</span></label>
    			</div>';
		    else
				$html.='<div class="pull-right "> 
    				<input rel="'.$list['id'].'" onclick="hideotherclientcheckboxes(\''.$client_id.'\', \''.$list['id'].'\', this.checked);" id="chk_'.$list['id'].'" type="checkbox" class="case_checkbox chk_'.$client_id.'"  name="cases['.$list['id'].']" value="'.$list['id'].'" title="'.$list['name'].'" aria-label="Hide other clients">
    				<label for="chk_'.$list['id'].'"><span class="sr-only">Hide other clients</span></label>
    			</div>';
		}
		$html.='</ul>';
		if($html != "")
			echo $html;
		else 
			echo '<ul><li>No Cases Found</li></ul>';
	}
	else
	{
	    echo '<ul><li>No Cases Found</li></ul>';
	}		
    }
    /* get search results for selected cases when clicking on search button in my case landing page */
    public function actionSearchcases()
    {
		$term = Yii::$app->request->post('term');
        $caseId = Yii::$app->request->post('caseId');
        $ismagnified = Yii::$app->request->post('ismagnified');
        
        $term = str_replace('"', "", $term);
	
        $ismagnified = isset($ismagnified) && $ismagnified != "" ? $ismagnified : "";
        
        $search_results = (new ClientCase)->getCaseSearchResults($term, $caseId, $ismagnified); 
		
	    return $this->renderPartial('casesearchresults', ['search_results' => $search_results, 'term' => $term]);
    }
    /* update comment status */
    public function actionUpdatecommentstatus() {
        
        $caseId = Yii::$app->request->post('caseId');
        $comment_arr = (new Tasks)->getnewcommentsByTeamOrCase($caseId, 'case');
	    // echo "<pre>";print_r($comment_arr); die;
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
}
