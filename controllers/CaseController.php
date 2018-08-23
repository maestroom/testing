<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

use app\models\Client;
use app\models\ClientCase;
use app\models\Tasks;
use app\models\TasksUnitsBilling;
use app\models\CaseXteam;
use app\models\CaseContacts;
use app\models\search\CaseContactsSearch;
use app\models\User;
use app\models\ProjectSecurity;
use app\models\ActivityLog;
use app\models\CaseType;
use app\models\CaseCloseType;
use app\models\SettingsEmail;
use app\models\ClientContacts;
use app\models\ClientCaseSummary;
use app\models\EmailCron;
use app\models\ClientCaseEvidence;
use app\models\EvidenceProduction;

use yii\helpers\Url;
use yii\db\Query;

class CaseController extends \yii\web\Controller
{
	/**
    * Index action will be used to load main container view to manage Cases on the bases of client Selection.
    * 2nd section will display Cases List based on the selection of Client.
    * @return mixed
    */
    public function actionIndex()
    {
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	
    	if ($roleId != 0) {
    		$client_data = ProjectSecurity::find()->select('client_id')->where('client_id!=0 AND user_id='.$userId);
    		$clientList = Client::find()->select(['id', 'client_name'])->where(['in', 'id', $client_data])->orderBy('client_name')->all();
    	} else {
    		$clientList = Client::find()->select(['id', 'client_name'])->orderBy('client_name')->all();
    	}
    	
    	return $this->renderAjax('index', [
    		'clientList' => $clientList,
    	]);
    }

    /**
    * 2nd section will display Cases List based on the selection of Client.
    * 3rd section will load Case Area on the bases of Case selection.
    * OR
    * 3rd section will load Add Case Form to add new Case. 
    * @return mixed
    * @param integer $client_id
    */
	public function actionLoadCasesByClient()
    {
		$client_id = Yii::$app->request->get('client_id',0);
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	
    	if ($roleId != 0) {
    		$case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id='.$client_id.' AND user_id='.$userId.' AND team_id=0');
    		$caseList = ClientCase::find()->select(['id', 'case_name', 'is_close'])->where(['in', 'id', $case_data])->orderBy('case_name')->all();
    	} else {
    		$caseList = ClientCase::find()->select(['id', 'case_name', 'is_close'])->where([ 'client_id' => $client_id])->orderBy('case_name')->all();
    	}
    	
    	return $this->renderAjax('_clientbasecaseslist', [
    		'caseList' => $caseList,
    		'client_id' => $client_id
    	]);
    }
    
	/**
     * Creates a new Case model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$model = new ClientCase();
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
		$client_id = Yii::$app->request->get('client_id',0);
		
        $listCaseType = ArrayHelper::map(CaseType::find()->where(['remove'=>0])->orderBy(['case_type_name' => 'ASC'])->asArray()->all(), 'id', 'case_type_name');
        $listCaseCloseType = ArrayHelper::map(CaseCloseType::find()->where(['remove'=>0])->orderBy(['close_type' => 'ASC'])->asArray()->all(), 'id', 'close_type');
        $listSalesRepo = ArrayHelper::map(User::find()->select(['id',"CONCAT(usr_first_name,' ',usr_lastname) as fullname"])->orderBy(['id' => 'ASC'])->asArray()->all(), 'id', 'fullname');
        
        if ($model->load(Yii::$app->request->post())) {
			if($model->description!='') {
	        	$user_input=HtmlPurifier::process($model->description);
	        	$model->description = html_entity_decode(strip_tags($user_input));
        	}
        	$model->client_id = $client_id; 
        	if(!$model->save()) {
				$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        		return $this->renderAjax('_form', [
	                'model' => $model,
				    'client_id' => $client_id,
				    'listCaseType' => $listCaseType,
					'listCaseCloseType' => $listCaseCloseType,
				    'listSalesRepo' => $listSalesRepo,
				    'model_field_length' => $model_field_length
	            ]);	            
        	}     
        	$case_id = Yii::$app->db->getLastInsertId();
        	/**
        	 * When new Case added into the system, below code will store new entries into the tbl_project_security table
        	 * for currrent user and all other users who have usr_inherent_cases = 1  Auto-Inherit All New Cases within Client(s)
        	 */
        	ProjectSecurity::addUserSecurityCaseswithinClient($userId,$client_id,$case_id,0,0);

			/**
        	 * When new Case added into the system, below code will store new entries into the tbl_project_security table
        	 * for currrent user and all other users who have usr_inherent_cases = 2  Auto-Inherit All Client Cases
        	 */
        	ProjectSecurity::addUserSecurityAllClientCase($userId,$client_id,$case_id,0,0);
        	/**
        	 * When new Case added into the system, below code will generate new log entry.  
        	 */
        	(new ActivityLog())->generateLog('Case','Added', $case_id, $model->case_name);
        	
            return 'OK';
            
        } else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
            return $this->renderAjax('create', [
                'model' => $model,
            	'client_id' => $client_id,
            	'listCaseType' => $listCaseType,
            	'listCaseCloseType' => $listCaseCloseType,
            	'listSalesRepo' => $listSalesRepo,
            	'model_field_length' => $model_field_length
            ]);
            
        }
    }
    
	/**
     * Updates an existing Case model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldmodel = $this->findModel($id);
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
        
        $listCaseType = ArrayHelper::map(CaseType::find()->where(['remove'=>0])->orderBy(['case_type_name' => 'ASC'])->asArray()->all(), 'id', 'case_type_name');
        //echo "<pre>"; print_r($listCaseType); exit;
		/*if($model->case_close_id!="")
			$listCaseCloseType = ArrayHelper::map(CaseCloseType::find()->where('remove=0 OR (remove=1 AND id = '.$model->case_close_id.')')->orderBy(['close_type' => 'ASC'])->asArray()->all(), 'id', 'close_type');
		else*/
	    $listCaseCloseType = ArrayHelper::map(CaseCloseType::find()->where('remove=0')->orderBy(['close_type' => 'ASC'])->asArray()->all(), 'id', 'close_type');
        $listSalesRepo = ArrayHelper::map(User::find()->select(['id',"CONCAT(usr_first_name,' ',usr_lastname) as fullname"])->orderBy(['id' => 'ASC'])->asArray()->all(), 'id', 'fullname');
    	
        
        if ($model->load(Yii::$app->request->post())) {

        	if($model->description!='') {
	        	$user_input = HtmlPurifier::process($model->description);
	        	$model->description = html_entity_decode(strip_tags($user_input));
        	}

        	if(!$model->save()) {
        		return $this->renderAjax('_form', [
	                'model' => $model,
	            	'listCaseType' => $listCaseType,
	            	'listCaseCloseType' => $listCaseCloseType,
	            	'listSalesRepo' => $listSalesRepo
	            ]);
	        }
        	
        	/**
	         *  Send Case details change mail
	         */
            $change_arr = array();
            if ($oldmodel->case_name != $model->case_name)
                $change_arr['name'] = $model->case_name;
            if ($oldmodel->case_manager != $model->case_manager )
                $change_arr['case_manager'] = $model->case_manager;
            if ($oldmodel->description != $model->description)
                $change_arr['description'] = $model->description;
            if ($oldmodel->case_type_id != $model->case_type_id) {
                if (isset($listCaseType[$model->case_type_id]) && $listCaseType[$model->case_type_id] != '')
                    $change_arr['type'] = $listCaseType[$model->case_type_id];
            }
            if ($oldmodel->case_matter_no != $model->case_matter_no)
                $change_arr['matter_no'] = $model->case_matter_no;
            if ($oldmodel->counsel_name != $model->counsel_name)
                $change_arr['counsel_name'] = $model->counsel_name;
            if ($oldmodel->internal_ref_no != $model->internal_ref_no)
                $change_arr['internal_ref_no'] = $model->internal_ref_no;
            if ($oldmodel->sales_user_id != $model->sales_user_id) {
            	if (isset($listSalesRepo[$model->case_type_id]) && $listCaseType[$model->case_type_id] != '')
                	$change_arr['sales_repo'] = $listSalesRepo[$model->case_type_id];
            }
            
            if (!empty($change_arr)) {
				//SettingsEmail::sendEmail
				EmailCron::saveBackgroundEmail(19,'changed_casedetail', $data = array('case_id' => $model->id, 'case_changes' => $change_arr));
            }
        	
        	/**
        	 * When case updated into the system, below code will generate new log entry.  
        	 */
        	$activityLog = new ActivityLog();
        	$activityLog->generateLog('Case','Updated', $id, $model->case_name);

        	/**
        	 * When case closed / re-opened into the system, below code will generate new log entry.  
        	 */
        	if ($oldmodel->is_close != $model->is_close) {
	        	$activityLog = new ActivityLog();
	        	if($model->is_close == 1){
					$activityLog->generateLog('Case','Closed', $id, $model->case_name.'|closeType#:'.$model->case_close_id);
				} else {
	        		$activityLog->generateLog('Case','Reopened', $id, $model->case_name);
	        	}
        	}            
            
            // return 
        	return 'OK';
            
        } else {
			$actLog_case = ActivityLog::find()->where(['and',"origination='Case'", "activity_module_id=$id", ['in','activity_type', ['Closed','Reopened']]])->orderBy('id DESC')->all();
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
			return $this->renderAjax('update', [
                'model' => $model,
            	'listCaseType' => $listCaseType,
            	'listCaseCloseType' => $listCaseCloseType,
            	'listSalesRepo' => $listSalesRepo,
            	'actLog_case' => $actLog_case,
            	'model_field_length' => $model_field_length
            ]);
        }
    }
    
    /**
     * check any cases associated with projects model.
     * If OK, the browser will be prompt alert message.
     * @param integer $id
     * @return mixed
     */
    public function actionCaseHasProjects($id)
	{
		$task = Tasks::find()->where(['client_case_id'=>$id])->count();
		if($task == 0){
			return 'OK';
		}
		return;
	}
	
	/**
     * check any cases associated with projects model.
     * If OK, the browser will be prompt alert message.
     * @param integer $id
     * @return mixed
     */
	public function actionChkCanCloseCase($id){
    
        $invoiceleft = 0;
        $accumalatedleft = 0;
        	
        $caseid = $_POST['ClientCase']['id'];
        /*$invoiceleft = TasksUnitsBilling::find()->with(array('TasksUnits'=>array('joinType'=>'INNER JOIN'),'Tasks'=>array('condition'=>"Tasks.client_case_id={$caseid}",'joinType'=>'INNER JOIN','with'=>array('ClientCase'=>array('condition'=>'ClientCase.is_close=0','joinType'=>'INNER JOIN')))))->count(array("condition"=>"(t.invoiced IS NULL OR t.invoiced='' OR t.invoiced=0)"));
        $accumalatedleft = TasksUnitsBilling::find()->with(array('TasksUnits'=>array('joinType'=>'INNER JOIN'),'Tasks'=>array('condition'=>"Tasks.client_case_id={$caseid}",'joinType'=>'INNER JOIN','with'=>array('ClientCase'=>array('condition'=>'ClientCase.is_close=0','joinType'=>'INNER JOIN'))),'pricing'=>array('condition'=>'pricing.accum_cost=1','joinType'=>'INNER JOIN')))->count(array("condition"=>"(t.invoiced IS NULL OR t.invoiced='' OR t.invoiced=0)"));*/
        
        $sql1 = "SELECT COUNT(DISTINCT t.id) FROM tbl_tasks_units_billing t INNER JOIN tbl_tasks_units taskunits
         ON (t.tasks_unit_id=taskunits.id) 
         INNER JOIN tbl_task_instruct_servicetask as servicetask ON servicetask.id = taskunits.task_instruct_servicetask_id
         INNER JOIN tbl_tasks tasks ON (servicetask.task_id=tasks.id) INNER JOIN tbl_client_case clientCase ON (tasks.client_case_id=clientCase.id) WHERE ((t.invoiced IS NULL OR t.invoiced='' OR t.invoiced=0)) AND (tasks.client_case_id=:case_id) AND (clientCase.is_close=0)";
		$invoiceleft = \Yii::$app->db->createCommand($sql1,[ ':case_id' => $case_id ] )->execute();
		
		$sql2 = "SELECT COUNT(DISTINCT t.id) FROM tbl_tasks_units_billing t INNER JOIN tbl_tasks_units taskunits ON (t.tasks_unit_id=taskunits.id)
		 INNER JOIN tbl_task_instruct_servicetask as servicetask ON servicetask.id = taskunits.task_instruct_servicetask_id
		 INNER JOIN tbl_tasks tasks ON (servicetask.task_id=tasks.id) INNER JOIN tbl_client_case clientCase ON (tasks.client_case_id=clientCase.id) INNER JOIN tbl_pricing pricing ON (t.pricing_id=pricing.id) WHERE ((t.invoiced IS NULL OR t.invoiced='' OR t.invoiced=0)) AND (tasks.client_case_id=:case_id) AND (clientCase.is_close=0) AND (pricing.accum_cost=1)";
		$accumalatedleft = \Yii::$app->db->createCommand($sql2,[ ':case_id' => $case_id ] )->execute();
		
        if($invoiceleft > 0 && $invoiceleft == $accumalatedleft) {
			return "accumalateditemsleft";
        } else if ($invoiceleft > 0) {
        	return "billableitemsleft";
        }

		return;
    }
    
	/**
     * Deletes an existing Case model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id){
     	
		
		$has_media=ClientCaseEvidence::find()->where(['client_case_id'=>$id])->count();
		if($has_media){
			return 'media';
		}
		$has_production=EvidenceProduction::find()->where(['client_case_id'=>$id])->count();
		if($has_production){
			return 'production';
		}
		
     	/** Start : To remove Case related securtiry from tbl_project_security */
     	ProjectSecurity::deleteAll(['client_case_id' => $id]);
     	/** End : To remove Case related securtiry from tbl_project_security */
     	
     	/**
     	 * Remove from case contact 
     	 */
     	CaseContacts::deleteAll(['client_case_id' => $id]);
     	
     	/** Start : To remove Case Excluded Teamservices */
     	CaseXteam::deleteAll(['client_case_id' => $id]);
		/** End : To remove Case Excluded Teamservices */
		
		ClientCaseSummary::deleteAll(['client_case_id'=>$id]);
		
     	/** Start : To remove Case */
     	$modelCase=ClientCase::findOne($id);
		$casename=$modelCase->case_name;
		$modelCase->delete();
		/** End : To remove Case */
     	
		/**
         * When case deleted into the system, below code will generate new log entry.  
         */
        $activityLog = new ActivityLog();
        $activityLog->generateLog('Case','Deleted', $id, $casename);
        
     	return 'OK';
     
    }
	/**
     * Case Summary Node
     * Show the Case Summary Node case id wise.
     * @param integer $case_id
     * @return mixed
     */
	public function actionCaseSummary(){
		$this->layout = "mycase";
		$case_id = Yii::$app->request->get('case_id');
		$model = new ClientCaseSummary();
    	$data = ClientCaseSummary::find()->where(['client_case_id'=>$case_id])->one();
    	if(isset($data->id) && $data->id!== null)
    		$model = ClientCaseSummary::findOne($data->id);
    	
    	$model->client_case_id=$case_id;
		return $this->render('case-summary', [
			'model'=>$model,
			'case_id'=>$case_id
		]);
	}
    
	/**
     * Finds the Case model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Case the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientCase::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
	/**
     * Finds the Case Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCaseContactsModel($id)
    {
        if (($model = CaseContacts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
     * @abstract This action is define for a List of Case's Contact List
     * @access Public
     * @since 1.0.0
     * @param integer $client_id
     * @param integer $client_case_id
     */
    public function actionContactList() {
    
    	$client_id = Yii::$app->request->get('client_id',0);
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	
    	$searchModel = new CaseContactsSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	/*IRT 67,68,86,87,258*/
    	/*IRT 96,398 */
        $filter_type=\app\models\User::getFilterType(['CONCAT(lname, ", ", fname, " ", mi) as fullname','tbl_client_contacts.contact_type','tbl_client_contacts.fname','tbl_client_contacts.lname','tbl_client_contacts.mi','tbl_client_contacts.add_1','tbl_client_contacts.notes'],['tbl_case_contacts','tbl_client_contacts']);
        $config = [];
		$config_widget_options = [];		
        $filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case/ajax-filter']).'&client_id='.$client_id.'&case_id='.$client_case_id, $config, $config_widget_options);
        /* IRT 67,68,86,87,258 */
        
    	//echo "<pre>"; print_r($dataProvider->getModels()); exit;
    	return $this->renderAjax('_contactList', [
    		'client_id' => $client_id,
    		'case_id' => $client_case_id,
    		'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_type' => $filter_type,
            'filterWidgetOption' => $filterWidgetOption
    	]);
    }
    
    /**
     * Filter GridView with Ajax
     * */
    public function actionAjaxFilter(){
    	$searchModel = new CaseContactsSearch();
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
     * Load Client Contact's List by client_id
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $client_id
     * @param integer $client_case_id
     * @return mixed
     */
    public function actionAddCaseContacts()
    {	
    	$client_id = Yii::$app->request->get('client_id',0);
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	
    	$model = new CaseContacts();
    	
    	$query = ClientContacts::find()->select(['tbl_client_contacts.id',"CONCAT(tbl_client_contacts.fname,' ',tbl_client_contacts.lname) as fullname","(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM tbl_case_contacts WHERE tbl_case_contacts.client_contacts_id=tbl_client_contacts.id AND tbl_case_contacts.client_case_id=$client_case_id) AS iscontactexist"])->where(['tbl_client_contacts.client_id'=>$client_id])->orderBy(['tbl_client_contacts.id'=>SORT_ASC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
    	
        if (Yii::$app->request->post()) {
        	
        	/**
        	 * Update Client Contacts will be adjusted in tbl_case_contacts 
        	 */
        	$clientcontactlist = Yii::$app->request->post('clientcontactslist');
        	
        	$client_contact_id = 0;
        	if(!empty($clientcontactlist)){
        		$client_contact_id = implode(",",$clientcontactlist);
        	} 
        	CaseContacts::adjustCaseContactsByCase($client_id,$client_contact_id,$client_case_id);
        	
            return 'OK';
            
        } else {
        	return $this->renderAjax('_clientContactList', [
	    		'model' => $model,
	    		'client_id' => $client_id,
	    		'case_id' => $client_case_id,
	    		'dataProvider' => $dataProvider
	    	]);
        }		
    }
    
	/**
     * Deletes a Case Contacts.
     * If deletion is successful, the browser will be redirected to the 'CaseContacts' page.
     * @param integer $case_id
     * @param integer $client_id
     * @param integer $contact_id
     * @return mixed
     */
	public function actionDeleteCaseContact(){
		/*$case_id = Yii::$app->request->post('case_id',0);
		$client_id = Yii::$app->request->post('client_id',0);*/
		$contact_id = Yii::$app->request->post('contact_id',0);
		//echo $case_id.'-'.$client_id.'-'.$contact_id; exit;
     	if ($contact_id != 0) {
			CaseContacts::deleteAll(['id'=>$contact_id]);
     		return 'OK';
     	}
     	exit;
    }
    
	/**
     * Deletes selected Contacts.
     * If deletion is successful, the browser will be redirected to the 'CaseContacts' page.
     * @param integer $case_id
     * @param integer $client_id
     * @param integer $contact_id
     * @return mixed
     */
	public function actionDeleteSelectedCaseContacts(){
		/*$case_id = Yii::$app->request->post('case_id',0);
		$client_id = Yii::$app->request->post('client_id',0);*/
		$contact_id_array = Yii::$app->request->post('contact_id',array());
     	if (!empty($contact_id_array)) {
			CaseContacts::deleteAll(['in','id',$contact_id_array]);
     		return 'OK';
     	}
     	exit;
    }    
    
    /**
     * Update Case Summary.
     * If update is successful, the browser will be redirected to the 'Edit Case' page.
     * @param integer $client_case_id
     * @return mixed
     */
    public function actionClientcaseSummary(){
    	$case_id = Yii::$app->request->get('client_case_id',0);
    	$model = new ClientCaseSummary();
    	$data = ClientCaseSummary::find()->where(['client_case_id'=>$case_id])->one();
    	if(isset($data->id) && $data->id!== null)
    		$model = ClientCaseSummary::findOne($data->id);
    	
    	$model->client_case_id=$case_id;
    	if (Yii::$app->request->post() && $model->load(Yii::$app->request->post()) && $model->save()) {
    		return 'OK';
    	}else{
	    	return $this->renderAjax('ClientcaseSummary', [
	    		'model' => $model,
	    	]);
    	}
    }
    
	/**
     * Load Case's Excluded Service List
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $client_case_id
     * @return mixed
     */
    public function actionExcludedServiceList()
    {	
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	
		//$model = new CaseXTeam();
    	
		$sqlscalar = 'SELECT count(*) FROM tbl_teamservice LEFT JOIN tbl_teamservice_locs ON tbl_teamservice_locs.teamservice_id = tbl_teamservice.id INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = CASE WHEN tbl_teamservice_locs.team_loc IS NOT NULL THEN tbl_teamservice_locs.team_loc ELSE 0 END';
		$sqlcount = Yii::$app->db->createCommand($sqlscalar)->queryScalar();
		
		$sql = "SELECT *, (
				SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END 
				FROM tbl_case_xteam 
				WHERE tbl_case_xteam.teamservice_id=tbl_teamservice_detail.id AND tbl_case_xteam.team_loc = tbl_teamservice_detail.teamservice_loc AND tbl_case_xteam.client_case_id=$client_case_id
			) AS isserviceexcluded, $client_case_id as client_case_id 
			FROM (
				SELECT tbl_teamservice.id, tbl_teamservice.teamid, tbl_teamservice.sort_order, tbl_teamlocation_master.team_location_name,tbl_teamservice.service_name,CONCAT(tbl_teamservice.service_name,' - ',tbl_teamlocation_master.team_location_name) as teamservices, (CASE WHEN tbl_teamservice_locs.team_loc IS NOT NULL THEN tbl_teamservice_locs.team_loc ELSE 0 END) as teamservice_loc
				FROM tbl_teamservice 
				LEFT JOIN tbl_teamservice_locs ON tbl_teamservice_locs.teamservice_id = tbl_teamservice.id
				INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = CASE WHEN tbl_teamservice_locs.team_loc IS NOT NULL THEN tbl_teamservice_locs.team_loc ELSE 0 END
			) AS tbl_teamservice_detail
			ORDER BY tbl_teamservice_detail.teamid, tbl_teamservice_detail.sort_order ASC";
		
        /*$dataProvider = new SqlDataProvider([
            'sql' => $sql,
		    'params' => [':client_case_id' => $client_case_id],
		    'totalCount' => $sqlcount,
        ]);
        
        $model = $dataProvider->getModels();*/
		$serviceListAr = Yii::$app->db->createCommand($sql)->queryAll();
		$serviceList = [];
		$teamservice_ids=array();
		$selected = array();
		if(!empty($serviceListAr)){
			foreach($serviceListAr as $tsdata){
				if(!in_array($tsdata['id'],$teamservice_ids)) {
				$teamser = [];				
				$teamser['title'] = $tsdata['service_name'];
				$teamser['isFolder'] = true;
				$teamser['key'] = $tsdata['id'];	
				
				$servicetask = [];
				foreach($serviceListAr as $inner_tsdata){
					if($inner_tsdata['id']!=$tsdata['id']){
						continue;
					}
					$servicetask['title'] = $inner_tsdata['team_location_name'];
					$servicetask['key'] = $inner_tsdata['id'].','.$inner_tsdata['teamservice_loc'];
					$servicetask['teamservice_id']=$inner_tsdata['id'];
					$servicetask['team_loc']=$inner_tsdata['teamservice_loc'];
					$servicetask['client_case_id']=$inner_tsdata['client_case_id'];
					$servicetask['select'] = false;
					if($inner_tsdata['isserviceexcluded'] == 1) {
						$servicetask['select'] = true;
						$selected[]=$inner_tsdata['id'].','.$inner_tsdata['teamservice_loc'];
					} 
					$teamser['children'][] = $servicetask;
	    			}
	   			if(!empty($teamser['children']))
	     			$serviceList[] = $teamser;
        
	   			$teamservice_ids[$tsdata['id']]=$tsdata['id'];
	   	 		}
	   	 }
	   }
		//echo "<pre>",print_r($serviceList);
//die;
        if (Yii::$app->request->post()) {
        	/**
        	 * Excluded Services will be adjusted in tbl_case_xteam 
        	 */
        	$rows = array();
        	/*$excludedservicesArray = Yii::$app->request->post('excludedservicelist');
			if(!empty($excludedservicesArray)){
	        	foreach($excludedservicesArray as $excludedservices){
		        	$excludedserviceArray = json_decode($excludedservices,true); 
		        	if(!empty($excludedserviceArray)){
		        		$rows[] = $excludedserviceArray;
		        	}
	        	}
        	}*/

			$excludedservicelist=Yii::$app->request->post('excludedservicelist');
			$excludedservicesArray = json_decode(str_replace("'", '"',$excludedservicelist),true);
			
			if(!empty($excludedservicesArray)) {
	        	foreach($excludedservicesArray as $excludedservices) {
		        	$excludedserviceArray = explode(",",$excludedservices);
					//json_decode($excludedservices,true); 
			    	if(!empty($excludedserviceArray)) {
		        		$rows[] = array($client_case_id,$excludedserviceArray[1],$excludedserviceArray[0]);
		        	}
	        	}
        	}
        	
        	CaseXTeam::deleteAll(['client_case_id'=>$client_case_id]);
        	
        	if(!empty($rows)){
        		$columns = (new CaseXTeam)->attributes();
        		unset($columns[array_search('id',$columns)]);
        		Yii::$app->db->createCommand()->batchInsert(CaseXTeam::tableName(), $columns, $rows)->execute();
        	}
        	
            return 'OK';
            
        } else {
	    	return $this->renderAjax('_excludedServiceList', [
	    		'model' => $model,
	    		'case_id' => $client_case_id,
				'serviceList' => $serviceList,
				'selected'=>$selected,
	    		'dataProvider' => $dataProvider
	    	]);
        }		
    }
    
	/**
     * @abstract This action is define for a List of Case's Contact List
     * @access Public
     * @param integer $client_id
     * @param integer $client_case_id
     * @since 1.0.0
     */
    public function actionAssignedUserList() {
		$client_id = Yii::$app->request->get('client_id',0);
		$client_case_id = Yii::$app->request->get('client_case_id',0);
		
		$sqlselect = "tbl_user.id IN (
    		SELECT tbl_project_security.user_id
    		FROM tbl_project_security
    		WHERE (tbl_project_security.client_id=".$client_id." AND tbl_project_security.client_case_id=".$client_case_id.") AND (tbl_project_security.user_id!=0) AND (tbl_project_security.team_id=0)
    	) AND tbl_user.role_id!=0";

		$role_sql="SELECT DISTINCT tbl_role.id,tbl_role.role_name FROM tbl_role INNER JOIN tbl_user ON tbl_user.role_id = tbl_role.id  WHERE ".$sqlselect." ORDER BY role_name";
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

    	/*$searchModel = new User();
    	$dataProvider = $searchModel->searchClientCaseAssigned(Yii::$app->request->queryParams);
    	
    	/*IRT 67,68,86,87,258*/
    	/*IRT 96,398 
        $filter_type=\app\models\User::getFilterType(['tbl_user.usr_username','tbl_role.role_name'],['tbl_user','tbl_role']);
        $config = [];
		$config_widget_options = [];		
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case/ajax-assign-user-filter']).'&client_id='.$client_id.'&client_case_id='.$client_case_id, $config, $config_widget_options);
        /*IRT 67,68,86,87,258*/
        
    	return $this->renderAjax('caseAssignedUser', [
			'userList'=>$userList
    		/*'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    		'filter_type' => $filter_type,
    		'filterWidgetOption' => $filterWidgetOption*/
    	]);
    }
    
    /**
     * @abstract This action is define for a List of Case's Contact List AjaxFilter
     * @access Public
     * @param integer $client_id
     * @param integer $client_case_id
     * @since 1.0.0
     */
     public function actionAjaxAssignUserFilter(){
		$searchModel = new User();
		$dataProvider = $searchModel->searchFilterClientCaseAssigned(Yii::$app->request->queryParams);
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
    * 2nd section will display Cases List based on the selection of Client.
    * 3rd section will load Case Area on the bases of Case selection.
    * OR
    * 3rd section will load Add Case Form to add new Case. 
    * @return mixed
    * @param integer $client_id
    */
    public function actionLoadCaselistByClient()
    {
		$client_id = Yii::$app->request->get('client_id',0); 
    	$roleId = Yii::$app->user->identity->role_id; 
    	$userId = Yii::$app->user->identity->id; 
    	if ($roleId != 0) {
    		$case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id IN ('.$client_id.') AND user_id='.$userId.' AND team_id=0');
    		$caseList = ClientCase::find()->select(['id', 'case_name','client_id'])->where(['in', 'id', $case_data])->orderBy('case_name')->all();
    	} else {
    		$caseList = ClientCase::find()->select(['id', 'case_name','client_id'])->where(['in', 'client_id', $client_id])->orderBy('case_name')->all();
    	}
    	
    	return $this->renderAjax('_clientbasecaseslistoption', [
    		'caseList' => $caseList,
    		'client_id' => $client_id
    	]);
    }
    
   /**
    * 2nd section will display Cases List based on the selection of Client.
    * 3rd section will load Case Area on the bases of Case selection.
    * OR
    * 3rd section will load Add Case Form to add new Case. 
    * @return mixed
    * @param integer $client_id
    */
    public function actionLoadCaseautocompleteByClient()
    {
		$client_id = Yii::$app->request->get('client_id',0);
		$term = Yii::$app->request->get('term',0);
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	
    	if ($roleId != 0) {
    		$case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id IN ('.$client_id.') AND user_id='.$userId.' AND team_id=0');
    		$caseList = ClientCase::find()->select(['id', 'case_name','client_id'])->where(['in', 'id', $case_data])->andWhere(['like', 'case_name', $term])->orderBy('case_name')->all();
    	} else {
    	   	$caseList = ClientCase::find()->select(['id', 'case_name','client_id'])->where(['in', 'client_id', $client_id])->where(['like', 'case_name', $term])
            ->orderBy('case_name')->all();
    	}
    	if (!empty($caseList)) {
			foreach ($caseList as $case) {
				$suggest[$case->id.'|'.$case->client_id] = $case->case_name;
			}
        }
    	echo json_encode($suggest);
        exit;
    }

    /**
     * Update Excluded Service List 
     **/
     public function actionUpdateExcludedServiceList()
     {
		 if (Yii::$app->request->post()) 
         {
			 $client_case_id = Yii::$app->request->get('client_case_id',0);
        	/**
        	 * Excluded Services will be adjusted in tbl_case_xteam 
        	 */
        	$rows = array();
			$excludedservicelist=Yii::$app->request->post('excludedservicelist');
			$excludedservicesArray = json_decode(str_replace("'", '"',$excludedservicelist),true);
			CaseXTeam::deleteAll(['client_case_id'=>$client_case_id]);
			if(!empty($excludedservicesArray)) {
	        	foreach($excludedservicesArray as $excludedservices) {
		        	$excludedserviceArray = explode(",",$excludedservices);
					//json_decode($excludedservices,true); 
			    	if(!empty($excludedserviceArray)) {
		        		$rows[] = array($client_case_id,$excludedserviceArray[1],$excludedserviceArray[0]);
		        	}
	        	}
        	}
			//echo "<pre>",print_r($row),"</pre>";die;

        	/*Delete Process starts
        	 $postData = Yii::$app->request->post();			 			
			 $idsToRemove = $postData['idsToRemove'];
			 $deleteString = '';			 		 			
			 if(!empty($idsToRemove)){
				 foreach($idsToRemove as $teamservice_id => $team_loc_arr){
					if($deleteString == '')
						$deleteString = "(client_case_id = $client_case_id AND teamservice_id = $teamservice_id AND team_loc IN (".implode(',',$team_loc_arr).")) ";
					else
						$deleteString .= "OR (client_case_id = $client_case_id AND teamservice_id = $teamservice_id AND team_loc IN (".implode(',',$team_loc_arr)."))  ";					
				}
			 }
			 if($deleteString != ''){
				CaseXTeam::deleteAll($deleteString);
			 }*/        	
        	
        	if(!empty($rows)){
        		$columns = (new CaseXTeam)->attributes();
        		unset($columns[array_search('id',$columns)]);
        		Yii::$app->db->createCommand()->batchInsert(CaseXTeam::tableName(), $columns, $rows)->execute();
        	}
        	
            return 'OK';
        }		 
	 }
}
