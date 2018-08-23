<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

use app\models\Client;
use app\models\ClientContacts;
use app\models\search\ClientContactsSearch;
use app\models\ClientCase;
use app\models\ClientCaseSearch;
use app\models\CaseContacts;
use app\models\User;
use app\models\ProjectSecurity;
use app\models\ActivityLog;
use app\models\Industry;
use app\models\Country;
use app\models\ClientXteam;

class ClientController extends \yii\web\Controller
{
	/**
    * Index action will be used to load main container view to manage Client.
    * 2nd section will be Clients List .
    * 3rd section will load Client Area on the bases of Client selection.
    * OR
    * 3rd section will load Add Client Form to add new Client. 
    * @return mixed
    */
    public function actionIndex()
    {
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	
    	if ($roleId != 0) {
    		$client_data = ProjectSecurity::find()->select('client_id')->where('client_id!=0 AND user_id='.$userId)->groupBy('client_id');
    		$clientList = Client::find()->select(['id', 'client_name'])->where(['in', 'id', $client_data])->orderBy('client_name')->all();
    	} else {
    		$clientList = Client::find()->select(['id', 'client_name'])->orderBy('client_name')->all();
    	}
    	
    	return $this->renderAjax('index', [
    		'clientList'  =>  $clientList,
    	]);
    }

	/**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$model = new Client();
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
		$industryList = ArrayHelper::map(Industry::find()->where(['remove'=>0])->orderBy('industry_name ASC')->asArray()->all(), 'id', 'industry_name');       
               
        $countryList = ArrayHelper::map(Country::find()->where(['remove'=>0])->asArray()->all(), 'id', 'country_name');
        if ($model->load(Yii::$app->request->post())) {
        	if($model->description!='') {
	        	$user_input=HtmlPurifier::process($model->description);
	        	$model->description = html_entity_decode(strip_tags($user_input));
        	}
        	if($model->save())
        	{
	        	$client_id = Yii::$app->db->getLastInsertId();
	        	
	        	/**
	        	 * When new client added into the system, below code will store new entries into the tbl_project_security table
	        	 * for currrent user and all other users who have usr_inherent_cases = 2  
	        	 */
	        	
				ProjectSecurity::addUserSecurityAllClientCase($userId,$client_id,0,0,0);
	        	
	        	/**
	        	 * When new client added into the system, below code will generate new log entry.  
	        	 */
	        	$activityLog = new ActivityLog();
	        	$activityLog->generateLog('Client','Added', $client_id, $model->client_name);
	        	
	            return 'OK';
        	} else {
				$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        		return $this->renderAjax('_form', [
	                'model' => $model,
	            	'industryList' => $industryList,
	            	'countryList' => $countryList,
	            	'model_field_length' => $model_field_length
	            ]);
        	}
        } else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
            return $this->renderAjax('create', [
                'model' => $model,
            	'industryList' => $industryList,
            	'countryList' => $countryList,
            	'model_field_length' => $model_field_length
            ]);
        }
    }
    
	/**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
        $industryList = ArrayHelper::map(Industry::find()->where(['remove'=>0])->orderBy('industry_name ASC')->asArray()->all(), 'id', 'industry_name');       
        $countryList = ArrayHelper::map(Country::find()->where(['remove'=>0])->asArray()->all(), 'id', 'country_name');
        if ($model->load(Yii::$app->request->post())) {
        	if($model->description!='') {
	        	$user_input=HtmlPurifier::process($model->description);
	        	$model->description = html_entity_decode(strip_tags($user_input));
        	}
        	if($model->save()){
        	
	        	/**
	        	 * When client updated into the system, below code will generate new log entry.  
	        	 */
	        	$activityLog = new ActivityLog();
	        	$activityLog->generateLog('Client','Updated', $id, $model->client_name);
            
            	return 'OK';
        	} else {
				$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        		return $this->renderAjax('_form', [
	                'model' => $model,
	            	'industryList' => $industryList,
	            	'countryList' => $countryList,
	            	'model_field_length' => $model_field_length
	            ]);
        	}
        } else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);			
            return $this->renderAjax('update', [
                'model' => $model,
            	'industryList' => $industryList,
            	'countryList' => $countryList,
            	'model_field_length' => $model_field_length
            ]);
        }
    }
    
    /**
     * check any cases associated with Client model.
     * If OK, the browser will be prompt alert message.
     * @param integer $id
     * @return mixed
     */
    public function actionClientHasCase($id)
	{
		$clientCase = ClientCase::find()->where(['client_id'=>$id])->count();
		if($clientCase == 0){
			return 'OK';
		}
		return;
	}
    
	/**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id) {
     	/** Start : To remove Client related securtiry from tbl_project_security */
     	ProjectSecurity::deleteAll(['client_id' => $id]);
		$sql2 = "DELETE FROM tbl_client_contacts WHERE client_id IN ($id)";
		\Yii::$app->db->createCommand($sql2)->execute(); 
     	/** End : To remove Client related securtiry from tbl_project_security */
     	
     	/** Start : To remove Client */
     	$modelClient=Client::findOne($id);
		$clientname=$modelClient->client_name;
		$modelClient->delete();
		/** End : To remove Client */
		
		/**
         * When client deleted into the system, below code will generate new log entry.  
         */
        $activityLog = new ActivityLog();
        $activityLog->generateLog('Client','Deleted', $id, $clientname);
        
     	return 'OK';
    }
    
	/**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
	/**
     * Finds the Client Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findClientContactsModel($id)
    {
        if (($model = ClientContacts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
     * @abstract This action is define for a List of Client's Contact List
     * @access Public
     * @since 1.0.0
     */
    public function actionContactList() {
    	$client_id = Yii::$app->request->get('client_id',0);
        $params=Yii::$app->request->get();
		$contactTypeList = Yii::$app->params['contact_type'];
    	 /*IRT 67,68,86,87,258 code Starts */
        $filter_type = \app\models\User::getFilterType(['tbl_client_contacts.contact_type','tbl_client_contacts.fname','tbl_client_contacts.add_1','tbl_client_contacts.notes'],['tbl_client_contacts']);        
		//echo '<pre>';
        //print_r($filter_type);die;
        if (isset($params['ClientContactsSearch']['contact_type']) && is_array($params['ClientContactsSearch']['contact_type'])) {
            if(!empty($params['ClientContactsSearch']['contact_type'])){                            
                foreach($params['ClientContactsSearch']['contact_type'] as $k=>$v){                                   
                    if($v=='All' || strpos($v,",") !== false){                                           
                            unset($params['ClientContactsSearch']['contact_type']);
                    }
                }                                
            }                    
        }
        if (isset($params['ClientContactsSearch']['fullname']) && is_array($params['ClientContactsSearch']['fullname'])) {
            if(!empty($params['ClientContactsSearch']['fullname'])){                            
                foreach($params['ClientContactsSearch']['fullname'] as $k=>$v){                                   
                    if($v=='All'){                                           
                        unset($params['ClientContactsSearch']['fullname']);
                    }
                }                                
            }                    
        }
        if (isset($params['ClientContactsSearch']['add_1']) && is_array($params['ClientContactsSearch']['add_1'])) {
            if(!empty($params['ClientContactsSearch']['add_1'])){                            
                foreach($params['ClientContactsSearch']['add_1'] as $k=>$v){                                   
                    if($v=='All') {                                           
                        unset($params['ClientContactsSearch']['add_1']);
                    }
                }                                
            }                    
        }
        $config = [];
		//['contact_type'=>$contactTypeList];
        $config_widget_options = ['fname'=>['field_alais'=>'fullname']];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['client/ajax-filter']).'&client_id='.$client_id,$config,$config_widget_options);  
                
    	 /*IRT 67,68,86,87,258 code Ends */
    	$searchModel = new ClientContactsSearch();
    	$dataProvider = $searchModel->search($params);
    	
    	return $this->renderAjax('_contactList', [
                    'client_id' => $client_id,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'filter_type'=>$filter_type,
                    'filterWidgetOption' => $filterWidgetOption
        ]);
    }
    
    /**
     * Filter GridView with Ajax
     * */
    public function actionAjaxFilter(){
    	$searchModel = new ClientContactsSearch();
    	$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
    	$out['results']=array();
    	foreach ($dataProvider as $key=>$val){
    		$out['results'][] = ['id' => $val, 'text' => $val,'label' => $val];
    	}
    	return json_encode($out);
    }
    
	/**
     * Creates a new Client Contact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAddClientContacts()
    {
    	$client_id = Yii::$app->request->get('client_id',0);
    	$model = new ClientContacts();
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
		$contactTypeList = Yii::$app->params['contact_type'];    
        $countryList = ArrayHelper::map(Country::find()->where(['remove'=>0])->asArray()->all(), 'id', 'country_name');
        
        $query = ClientCase::find()->select(['id','case_name',"(0) AS iscontactexist"])->where(['client_id'=>$client_id])->orderBy(['id'=>SORT_ASC]);
        
        $caseDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if ($model->load(Yii::$app->request->post())) {
        	if($model->notes!='') {
	        	$user_input=HtmlPurifier::process($model->notes);
	        	$model->notes = html_entity_decode(strip_tags($user_input));
        	}
        	if($model->save()){
	        	$clientcontact_id = Yii::$app->db->getLastInsertId();
	        	/**
	        	 * Selected Cases will be adjusted for newly created Client Contact in tbl_case_contacts 
	        	 */
	        	$caselist = Yii::$app->request->post('caselist');
	        	$cases = 0;
	        	if(!empty($caselist)){
	        		$cases = implode(",",$caselist);
	        	} 
	        	CaseContacts::adjustCaseContacts($client_id,$clientcontact_id,$cases);
	        	
	            return 'OK';
        	} else {
        		//echo "<pre>",print_r($model->getErrors());
        		$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        		return $this->renderAjax('_formClientContact', [
	                'model' => $model,
	            	'client_id' => $client_id,
	            	'contactTypeList' => $contactTypeList,
	            	'countryList' => $countryList,
        			'caseDataProvider' => $caseDataProvider,
        			'model_field_length' => $model_field_length
	            ]);
        	}
        } else {			
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
            return $this->renderAjax('createClientContact', [
                'model' => $model,
            	'client_id' => $client_id,
            	'contactTypeList' => $contactTypeList,
            	'countryList' => $countryList,
            	'caseDataProvider' => $caseDataProvider,
            	'model_field_length' => $model_field_length
            ]);
        }
    }
    
	/**
     * Updates an existing Client Contact model.
     * If update is successful, the browser will be redirected to the Grid page of Contact.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateClientContacts($id,$client_id)
    {
        $model = $this->findClientContactsModel($id);
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$contactTypeList = Yii::$app->params['contact_type'];
        $countryList = ArrayHelper::map(Country::find()->where(['remove'=>0])->asArray()->all(), 'id', 'country_name');
        
        $query = ClientCase::find()->select(['id','case_name',"(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM tbl_case_contacts WHERE tbl_case_contacts.client_case_id=tbl_client_case.id AND tbl_case_contacts.client_contacts_id=$id) AS iscontactexist"])->where(['client_id'=>$client_id])->orderBy(['id'=>SORT_ASC]);
        
        $caseDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if ($model->load(Yii::$app->request->post())) {
        	
        	if($model->notes!='') {
	        	$user_input=HtmlPurifier::process($model->notes);
	        	$model->notes = html_entity_decode(strip_tags($user_input));
        	}
        	if($model->save()){
	        	/**
	        	 * Update Client Contacts will be adjusted in tbl_case_contacts 
	        	 */
	        	$caselist = Yii::$app->request->post('caselist');
	        	$cases = 0;
	        	if(!empty($caselist)){
	        		$cases = implode(",",$caselist);
	        	} 
	        	CaseContacts::adjustCaseContacts($client_id,$id,$cases);
	        	
	            return 'OK';
        	} else {
				$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        		return $this->renderAjax('_formClientContact', [
	                'model' => $model,
	            	'client_id' => $client_id,
	            	'contactTypeList' => $contactTypeList,
	            	'countryList' => $countryList,
	            	'caseDataProvider' => $caseDataProvider,
	            	'model_field_length' => $model_field_length
	            ]);
        	}
        } else {
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);			
            return $this->renderAjax('updateClientContacts', [
                'model' => $model,
            	'client_id' => $client_id,
            	'contactTypeList' => $contactTypeList,
            	'countryList' => $countryList,
            	'caseDataProvider' => $caseDataProvider,
            	'model_field_length' => $model_field_length
            ]);
        }
    }
    
	/**
     * Deletes a Client Contact and Case Contacts model.
     * If deletion is successful, the browser will be redirected to the 'ClientContacts' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDeleteClientContact(){
		$client_id = Yii::$app->request->post('client_id',0);
		$contact_id = Yii::$app->request->post('contact_id',0);
     	if ($client_id != 0 && $contact_id != 0) {
			ClientContacts::removeClientCaseContacts($client_id,$contact_id,0,'Client');
     		return 'OK';
     	}
     	exit;
    }    
    
    /**
     * Deletes selected Client Contacts and Case Contacts model.
     * If deletion is successful, the browser will be redirected to the 'ClientContacts' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDeleteSelectedClientContacts(){
		$client_id = Yii::$app->request->post('client_id',0);
		$contact_id_array = Yii::$app->request->post('contact_id',0);
     	if ($client_id != 0 && !empty($contact_id_array)) {
     		$contact_id = implode(',',$contact_id_array);
			ClientContacts::removeClientCaseContacts($client_id,$contact_id,0,'Client');
     		return 'OK';
     	}
     	exit;
    }
    
	/**
     * @abstract This action is define for a List of Client's Contact List
     * @access Public
     * @since 1.0.0
     */
    public function actionAssignedUserList() {
    	$client_id = Yii::$app->request->get('client_id',0);


		$sqlselect = "tbl_user.id IN (
    		SELECT tbl_project_security.user_id
    		FROM tbl_project_security
    		WHERE (tbl_project_security.client_id=".$client_id.") AND (tbl_project_security.user_id!=0) AND (tbl_project_security.team_id=0)
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
    	$dataProvider = $searchModel->searchClientAssigned(Yii::$app->request->queryParams, $client_id);*/
    	
    	/*IRT 67,68,86,87,258*/
    	/*IRT 96,398 
        $filter_type=\app\models\User::getFilterType(['tbl_user.usr_username','tbl_role.role_name'],['tbl_user','tbl_role']);
        $config = [];
		$config_widget_options = [];		
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['client/ajax-assign-user-filter']).'&client_id='.$client_id, $config, $config_widget_options);
        /*IRT 67,68,86,87,258*/
        
    	return $this->renderAjax('clientAssignedUser', [
			'userList'=>$userList
    		/*'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    		'filter_type' => $filter_type,
    		'filterWidgetOption' => $filterWidgetOption*/
    	]);
    }
    
    /**
     * Assigned User Filter
     * @return
     */
     public function actionAjaxAssignUserFilter(){
		$searchModel = new User();
		$dataProvider = $searchModel->searchFilterClientAssigned(Yii::$app->request->queryParams);
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
     * Load Client's Excluded Service List
     * IRT-5
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $client_case_id
     * @return mixed
     */
    public function actionExcludedServiceList()
    {	
    	$client_id = Yii::$app->request->get('client_id',0);
    	
		$sqlscalar = 'SELECT count(*) FROM tbl_teamservice LEFT JOIN tbl_teamservice_locs ON tbl_teamservice_locs.teamservice_id = tbl_teamservice.id INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = CASE WHEN tbl_teamservice_locs.team_loc IS NOT NULL THEN tbl_teamservice_locs.team_loc ELSE 0 END';
		$sqlcount = Yii::$app->db->createCommand($sqlscalar)->queryScalar();
		
		$sql = "SELECT *, (
				SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END 
				FROM tbl_client_xteam 
				WHERE tbl_client_xteam.teamservice_id=tbl_teamservice_detail.id AND tbl_client_xteam.team_loc = tbl_teamservice_detail.teamservice_loc AND tbl_client_xteam.client_id=$client_id
			) AS isserviceexcluded, $client_id as client_id 
			FROM (
				SELECT tbl_teamservice.id, tbl_teamservice.teamid, tbl_teamservice.sort_order, tbl_teamservice.service_name, tbl_teamlocation_master.team_location_name, CONCAT(tbl_teamservice.service_name,' - ',tbl_teamlocation_master.team_location_name) as teamservices, (CASE WHEN tbl_teamservice_locs.team_loc IS NOT NULL THEN tbl_teamservice_locs.team_loc ELSE 0 END) as teamservice_loc
				FROM tbl_teamservice 
				LEFT JOIN tbl_teamservice_locs ON tbl_teamservice_locs.teamservice_id = tbl_teamservice.id
				INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = CASE WHEN tbl_teamservice_locs.team_loc IS NOT NULL THEN tbl_teamservice_locs.team_loc ELSE 0 END
			) AS tbl_teamservice_detail
			ORDER BY tbl_teamservice_detail.teamid, tbl_teamservice_detail.sort_order ASC";
		//echo $sql;die;
		$serviceListAr = Yii::$app->db->createCommand($sql)->queryAll();
		$serviceList = [];
		$teamservice_ids=array();
		$exculded=array();
		$selected=array();
		if(!empty($serviceListAr)){
			foreach($serviceListAr as $tsdata){
				if(!in_array($tsdata['id'],$teamservice_ids)) {
				$teamser = [];				
				$teamser['title'] = $tsdata['service_name'];
				$teamser['isFolder'] = true;
				$teamser['key'] = $tsdata['id'];	
				
				$servicetask = [];
				foreach($serviceListAr as $inner_tsdata) {
					if($inner_tsdata['id']!=$tsdata['id']) {
						continue;
					}
					$servicetask['title'] = $inner_tsdata['team_location_name'];
					$servicetask['key'] = $inner_tsdata['id'].','.$inner_tsdata['teamservice_loc'];
					$servicetask['teamservice_id']=$inner_tsdata['id'];
					$servicetask['team_loc']=$inner_tsdata['teamservice_loc'];
					$servicetask['client_id']=$inner_tsdata['client_id'];
					$servicetask['select'] = false;
					if($inner_tsdata['isserviceexcluded'] == 1) {
						$servicetask['select'] = true;
						$selected[]=$inner_tsdata['id'].','.$inner_tsdata['teamservice_loc'];
						$exculded[$inner_tsdata['id']][$inner_tsdata['teamservice_loc']]=$inner_tsdata['teamservice_loc'];
					} 
					$teamser['children'][] = $servicetask;
	    		}
	   			if(!empty($teamser['children']))
	     			$serviceList[] = $teamser;
        
	   			$teamservice_ids[$tsdata['id']]=$tsdata['id'];
	   	 		}
	   	 }
	   }
/*$dataProvider = new SqlDataProvider([
'sql' => $sql,
'params' => [':client_id' => $client_id],
'totalCount' => $sqlcount,
]);
$model = $dataProvider->getModels();*/
      // echo "<prE>",print_R($serviceList),"</pre>";die;
		return $this->renderAjax('_excludedServiceList', [
			'model' => $model,
			'client_id' => $client_id,
			'serviceList' => $serviceList,
			'exculded'=>$exculded,
			'selected'=>$selected,
			'dataProvider' => $dataProvider
		]);
        		
    }
    /*
     * Update Excluded Service List
     * */
     public function actionUpdateExcludedServiceList(){
		 if (Yii::$app->request->post()) {
			 $client_id = Yii::$app->request->get('client_id',0);
			 /**
        	 * Excluded Services will be adjusted in tbl_case_xteam 
        	 */
        	$rows = array();
        	/*if(!empty($excludedservicesArray)){
	        	foreach($excludedservicesArray as $excludedservices){
		        	$excludedserviceArray = json_decode($excludedservices,true); 
		        	if(!empty($excludedserviceArray)){
		        		$rows[] = $excludedserviceArray;
		        	}
	        	}
        	}*/
			$excludedservicelist=Yii::$app->request->post('excludedservicelist');
			$excludedservicesArray = json_decode(str_replace("'", '"',$excludedservicelist),true);
			$exculded=array();
			$deleteString = '';
			//if($deleteString != ''){
			ClientXteam::deleteAll(['client_id'=>$client_id]);
			//}
			if(!empty($excludedservicesArray)) {
	        	foreach($excludedservicesArray as $excludedservices) {
		        	$excludedserviceArray = explode(",",$excludedservices);
					//json_decode($excludedservices,true); 
			    	if(!empty($excludedserviceArray)) {
		        		$rows[] = array($client_id,$excludedserviceArray[1],$excludedserviceArray[0]);

						/*if($deleteString == '')
							$deleteString = "CONCAT(client_id,'|',teamservice_id,'|',team_loc) NOT IN (('".$client_id."|".$excludedserviceArray[0]."|".$excludedserviceArray[1]."') ";
						else
							$deleteString .= ",('".$client_id."|".$excludedserviceArray[0]."|".$excludedserviceArray[1]."')  ";*/					
						
					}
	        	}
        	}
			//echo "<pre>",print_r($rows),"</pre>";
			//echo $deleteString.")";
			//die;
        	/*
        	 * Delete Process starts
        	 * */
			 
			 /*
			  * Delete Process Ends*/
			  /* Start:Add Data to table*/
			if(!empty($rows)){
        		$columns = (new ClientXteam)->attributes();
        		unset($columns[array_search('id',$columns)]);
        		Yii::$app->db->createCommand()->batchInsert(ClientXteam::tableName(), $columns, $rows)->execute();
			}
			/* End: Add Data to table*/
			return 'OK';
		 }
	 }
}
