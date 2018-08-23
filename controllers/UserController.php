<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use app\models\User;
use app\models\Role;
use app\models\Client;

use app\models\SecurityFeature;
use app\models\RoleSecurity;
use app\models\ActivityLog;
use app\models\TeamlocationMaster;
use app\models\Options;
use app\models\Settings;
use app\models\ClientCase;
use app\models\Team;
use app\models\TeamLocs;
use app\models\Tasks;
use app\models\TasksUnits;
use app\models\TasksUnitsTodos;
use app\models\ProjectSecurity;
use app\models\UserLog;
use app\models\ProjectRequestTypeRoles;
use yii\widgets\ActiveForm;
//use yii\widgets\CommentRoles;
use app\models\CommentRoles;
use app\models\CommentRolesUsers;
use app\models\CommentTeamsUsers;
use app\models\EvidenceTransaction;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Session;
class UserController extends Controller
{
	public function actionIndex(){}


	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));

		/*if (!(new User)->checkAccess(10) && $action->id == 'options')
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	*/


		return parent::beforeAction($action);
	}

	/**
     * Displays a manage users for users management.
     * @return mixed
     */
    public function actionManageRole()
    {
            $role_details = Role::find()->where('id!=0')->select(['id','role_name'])->all();
            $childs = (new SecurityFeature)->getSecurityFeatures();
            //echo "<pre>"; print_r($childs); exit;
            $model = new Role();
            $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
            return $this->renderAjax('ManageRole', [
					'role_details' => $role_details,
                    'security_features' => $childs,
                    'model' => $model,
                    'model_field_length' => $model_field_length
            ]);
    }

	/**
	 * Role Security Selected weather My Cases Or My Team or Both Appear
	 * @return
	 */
	public function GetRoleTypeSecurity($role, $childs){
		$cnt = count($role);
		if($cnt < 2){
			$rs = $role[0]==1?'Team':'Case';
			if($rs == 'Case'){
				unset($childs['My Cases']);
			}
			if($rs == 'Team'){
				unset($childs['My Teams']);
			}
		}
		return $childs;
	}

	/**
	 * Role Security Update
	 */
	public function actionRoleSecurityUpdate(){
		$role_security = RoleSecurity::find()->where('role_id='.$_REQUEST['role_id'])->asArray()->all();
		$childs = (new SecurityFeature)->getSecurityFeatures();
	//	$model = new Role();
		$model = $this->findRoleModel($_REQUEST['role_id']);
		if(!empty($_REQUEST['Role']['role_type'])){
			$childs = $this->GetRoleTypeSecurity($_REQUEST['Role']['role_type'], $childs);
		}
		//echo "<pre>"; print_r($childs); exit;
		return $this->renderAjax('RoleSecurityUpdate', [
			'model' => $model,
			'security_features' => $childs,
			'role_security'=>$role_security
		]);
	}

	/**
	 * Role Security Add
	 */
	public function actionRoleSecurityAdd() {
		$childs = (new SecurityFeature)->getSecurityFeatures();
		if(!empty($_REQUEST['Role']['role_type'])){
			$childs = $this->GetRoleTypeSecurity($_REQUEST['Role']['role_type'], $childs);
		}
		$role_security = $_REQUEST['security_feature'];
		$model = new Role();
		return $this->renderAjax('RoleSecurityAdd', [
			'model' => $model,
			'security_features' => $childs,
			 'role_security'=>$role_security
		]);
	}

	/**
     * Displays a manage users for users management.
     * @return mixed
     */
    public function actionManageUser() {
    	// $role_details = ArrayHelper::map(Role::find()->where('id!=0')->select(['id','role_name'])->all(),'id','role_name');
       	// $user_details = ArrayHelper::map(User::find()->select(['id',"(CASE WHEN CONCAT(usr_first_name,'',usr_lastname) <> '' THEN CONCAT(usr_first_name,' ',usr_lastname) ELSE usr_username END) as usr_first_name"])->where('role_id!=0')->orderBy(['usr_first_name'=>SORT_ASC])->all(), 'id', 'usr_first_name');
       	$model = new User();
       	$dataProvider = new ActiveDataProvider([
            'query' => User::find()->select(['id','usr_first_name','usr_lastname','usr_email','usr_type','usr_username','status'])->where('role_id!=0')->orderBy(['usr_lastname'=>SORT_ASC]), /*->orderBy(['usr_first_name'=>SORT_ASC])*/
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
       // echo "<pre>",print_r($dataProvider->getModels()); die;
        return $this->renderAjax('ManageUser', [
	   		'model' => $model,
     		//'user_details' => $user_details,
     		'dataProvider'=>$dataProvider,
    		//'role_details' => $role_details
    	]);
    }

	/**
     * Filter User for users management.
     * @return mixed
     */
	public function actionAjaxFilterUser(){
		$filterText = Yii::$app->request->get('filteruser','');
		$model = User::find()->select(['id','usr_first_name','usr_lastname','usr_email','usr_type','usr_username','status'])
		->where('role_id<>0')
		->orderBy(['usr_lastname'=>SORT_ASC]);
    	if($filterText != ''){
    		$model->andWhere("(CASE WHEN CONCAT(usr_first_name,usr_lastname) <> '' THEN CONCAT(usr_first_name,' ',usr_lastname) ELSE usr_username END) like '%".$filterText."%'");
    	}
    	$dataProvider = new ActiveDataProvider([
            'query' => $model,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
		/*$dataProvider = new ActiveDataProvider([
            'query' => User::find()->select(['id','usr_first_name','usr_lastname','usr_email','usr_type','usr_username','status'])->where('role_id!=0')->orderBy(['usr_first_name'=>SORT_ASC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);*/
		return $this->renderAjax('_ajaxroleuser', [
	   		'model' => $model,
     		//'user_details' => $user_details,
     		'dataProvider'=>$dataProvider,
    		//'role_details' => $role_details
    	]);
	}

	/**
     * Displays a manage users Access for users management.
     * @return mixed
     */
    public function actionManageUserAccess(){
    	$role_details = ArrayHelper::map(Role::find()->where('id!=0')->select(['id','role_name'])->all(),'id','role_name');
       	//$user_details = User::find()->select(['id','usr_first_name','usr_lastname','usr_email','usr_type','usr_username','status'])->where('role_id!=0')->orderBy(['usr_first_name'=>SORT_ASC])->all();
       	$model = new User();
       	$dataProvider = new ActiveDataProvider([
            'query' => User::find()->select(['tbl_user.id','usr_first_name','usr_lastname','usr_username','usr_email','usr_type','usr_username','status','role_id'])->joinWith(['role'])->where('role_id!=0')->orderBy(['usr_lastname'=>SORT_ASC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
       // echo "<pre>",print_r($dataProvider->getModels()); die;
        return $this->renderAjax('ManageUserAccess', [
	   		'model' => $model,
     		'user_details' => $user_details,
     		'dataProvider'=>$dataProvider,
    		'role_details' => $role_details
    	]);
    }


    /**
     * Role & Role Security Add method
     * @return true
     */
    public function actionAddrole(){
    	$model = new Role();
     	if ($model->load(Yii::$app->request->post())){
   	 		$role_type = implode(',',$model->role_type);
     		$model->role_type = $role_type;
     		$model->save();
     		$id = Yii::$app->db->getLastInsertId();
     		if(!empty($_REQUEST['security_feature'])){
	     		$rows =array();
	     		$permissions= [
		            'is_sub_new_task' =>54,
		            'is_sub_com_task' =>55,
		            'is_sub_new_production' =>113,
		            'is_sub_past_due' =>56,
		            'opt_posted_comment' =>57,
		            'is_sub_self_assign' =>58,
		            'is_new_todo_post' =>59,
		            'is_completed_todos' =>60,
		            'is_todos_assign_to_me' =>61,
		            'is_servicetask_transists' =>75,
		            'is_cancel' =>76,
		            'is_uncanceled' =>77,
		            'is_unassign' =>78,
		            'changed_instructions' =>62,
		            'pending_tasks' =>63,
		            'approaching_case_budget_spend' =>64,
		            'reached_case_budget_spend' =>65,
		            'changed_casedetail' =>67,
		            'is_sub_com_service' =>120,
		            'is_sub_new_media' => 131
		        ];

	     		$force_alert = array();
	     		foreach($_REQUEST['security_feature'] as $security_feature){
	     			$role_security = array();
	     			$role_security['role_id']=$id;//$_REQUEST['Role']['id'];
	     			$role_security['security_feature_id']=$security_feature;
	     			$role_security['security_force']=0;
	     			if(isset($_REQUEST['security_force'][$security_feature])){
	     				$role_security['security_force'] = 1;
	     				if(in_array($security_feature,$permissions)){
	     					$force_alert[]=array_search($security_feature,$permissions);
	     				}

	     			}
	     			$role_security['user_id']=0;
	     			$rows[] = $role_security;
	     			/*$role_security = new RoleSecurity();
	     			$role_security->role_id = $_REQUEST['Role']['id'];
	     			$role_security->security_feature_id = $security_feature;
	     			if(isset($_REQUEST['security_force'][$security_feature])){
	     				$role_security->security_force = 1;
	     			}
	     			$role_security->save();*/
	     		}

	     		if(!empty($rows)){
	     			$columns = (new RoleSecurity)->attributes();
	     			unset($columns[array_search('id',$columns)]);
	     			Yii::$app->db->createCommand()->batchInsert(RoleSecurity::tableName(), $columns, $rows)->execute();
	     		}

	     	}
     		$activityLog = new ActivityLog();
        	$activityLog->generateLog('User Role','Added', $id, $model->role_name);
        	return 'OK';
     	} else {
			return "Fail";
     	}
     	die();
   }

   /**
    * Role & Role Security Edit method
    * @return true
    */
   public function actionEditrole(){
   		$model = Role::findOne($_REQUEST['Role']['id']);
   	   	if ($model->load(Yii::$app->request->post())){
			//echo "<pre>"; print_r(Yii::$app->request->post()); exit;

	   		$role_type = implode(',',$model->role_type);
	   		$model->role_type = $role_type;
	     	$model->update();
	     	(new RoleSecurity())->deleteSecurityRole($_REQUEST['Role']['id']);
	     	if(!empty($_REQUEST['security_feature'])){
	     		$rows =array();
				$permissions_securityfeature_no= [
	     				'is_sub_new_task' =>10.01,
						'is_sub_production_posted'=>10.0101,
						'is_cancel' =>10.011,
						'is_uncanceled' =>10.012,
						'changed_instructions' =>10.0121,
						'is_sub_com_task' =>10.020,
						'is_sub_new_production' =>10.021,
						'is_sub_new_media' =>10.0211,
						'is_sub_com_service' =>10.022,
						'is_sub_past_due' =>10.03,
						'opt_posted_comment' =>10.04,
						'opt_posted_summary_comment'=>10.05,
						'is_sub_self_assign' =>10.06,
						'is_unassign' =>10.061,
						'is_new_todo_post' =>10.07,
						'is_completed_todos' =>10.08,
						'is_todos_assign_to_me' =>10.09,
						'pending_tasks' =>10.11,
						'changed_casedetail' =>10.12,
						'approaching_case_budget_spend' =>10.13,
						'approaching_project_due_date'=>10.131,
						'reached_case_budget_spend' =>10.14,
						'is_servicetask_transists' =>10.15,
						'is_reopen_project'=>10.16,
				];
				$featuresort= "'" . implode( "','", array_values($permissions_securityfeature_no)) . "'";
				$security_feature_data = ArrayHelper::map(SecurityFeature::find()->select(['id','feature_sort'])->where("feature_sort IN ($featuresort) ")->all(),'id','feature_sort');
				$permissions=[];
				if(!empty($security_feature_data )) {
					foreach ($security_feature_data  as $id => $feature_sort) {
						$permissions[array_search($feature_sort,$permissions_securityfeature_no)]=$id;
					}
			 	}
				//echo "<pre>",print_r($security_feature_data),print_r($permissions),"</pre>";die;
				// security feature

	     		$force_alert = array();
				$option_alert = array();
	     		foreach($_REQUEST['security_feature'] as $security_feature){
	     			$role_security = array();
	     			$role_security['role_id']=$_REQUEST['Role']['id'];
	     			$role_security['security_feature_id']=$security_feature;
	     			$role_security['security_force']=0;
	     			if(isset($_REQUEST['security_force'][$security_feature])){
	     				$role_security['security_force'] = 1;
	     				if(in_array($security_feature,$permissions)){
	     					$force_alert[]=array_search($security_feature,$permissions);
	     				}
	     			}
					if(in_array($security_feature,$permissions)){
					 	$option_alert[] = array_search($security_feature,$permissions);
					}
	     			$role_security['user_id']=0;
	     			$rows[] = $role_security;
	     			/*$role_security = new RoleSecurity();
	     			$role_security->role_id = $_REQUEST['Role']['id'];
	     			$role_security->security_feature_id = $security_feature;
	     			if(isset($_REQUEST['security_force'][$security_feature])){
	     				$role_security->security_force = 1;
	     			}
	     			$role_security->save();*/
	     		}

	     		if(!empty($force_alert)){
	     			$fields = "";
	     			for($i=1;$i<=count($force_alert);$i++){
	     				if($fields == ""){ $fields = "1";}else{$fields .= ",1";}
	     			}
	     			$sql = " UPDATE tbl_options SET ".implode("=1,",$force_alert)."=1 WHERE user_id in (SELECT id FROM tbl_user WHERE role_id=".$model->id.")";
	     			Yii::$app->db->createCommand($sql)->execute();
	     			//$optioninsersql = " INSERT INTO tbl_options (user_id,".implode(",",$force_alert).") SELECT id,".$fields." FROM tbl_user WHERE role_id={$model->id} and id not IN (SELECT user_id FROM tbl_options where user_id IN (SELECT id FROM tbl_user WHERE role_id={$model->id}))";
					 $options_fields=array_keys($permissions);
					if(!empty($options_fields)){
						foreach($options_fields as $f){
							if(!in_array($f,$force_alert)){
								if($fields == ""){ $fields = "0";}else{$fields .= ",0";}
								$force_alert[$f]=$f;
							}
						}
					}
	     			$optioninsersql = " INSERT INTO tbl_options (user_id,timezone_id,".implode(",",$force_alert).") SELECT id,'America/New_York',".$fields." FROM tbl_user WHERE role_id={$model->id} and id not IN (SELECT user_id FROM tbl_options where user_id IN (SELECT id FROM tbl_user WHERE role_id={$model->id}))";
	     			Yii::$app->db->createCommand($optioninsersql)->execute();
	     			// ADD me Baki hai
	     		}
				$sql_updateoption="";
				$sets="";
				if(!empty($option_alert)) {
					foreach($permissions as $fie=>$fid){
						if(!in_array($fie,$option_alert)){
							if($sets == "")
								$sets=$fie."=0";
							else
								$sets.=", ".$fie."=0";
						}
					}
					if($sets !="") {
						$sql_updateoption = " UPDATE tbl_options SET $sets WHERE user_id in (SELECT id FROM tbl_user WHERE role_id=".$model->id.")";
						Yii::$app->db->createCommand($sql_updateoption)->execute();
					}
				} else {
					foreach($permissions as $fie=>$fid){
							if($sets == "")
								$sets=$fie."=0";
							else
								$sets.=", ".$fie."=0";
					}
					if($sets !="") {
						$sql_updateoption = " UPDATE tbl_options SET $sets WHERE user_id in (SELECT id FROM tbl_user WHERE role_id=".$model->id.")";
						Yii::$app->db->createCommand($sql_updateoption)->execute();
					}
				}
	     		if(!empty($rows)) {
	     			$columns = (new RoleSecurity)->attributes();
	     			unset($columns[array_search('id',$columns)]);
	     			Yii::$app->db->createCommand()->batchInsert(RoleSecurity::tableName(), $columns, $rows)->execute();
	     		}
	     	}
	     	$activityLog = new ActivityLog();
	     	$activityLog->generateLog('User Role','Updated', $model->id, $model->role_name);
			$user_model = new User();
			$session = new Session;
			$session->open();
			$session['myaccess'] = $user_model->getAllSecurityAccess(Yii::$app->user->identity->id);
			$session['role'] 	 = Yii::$app->user->identity->role;
			$session['options']  = Yii::$app->user->identity->options;
			//echo $sql_updateoption;die;
	     	return 'OK';
	   	} else {
			return "Fail";
     	}
   		die();
   }


   /**
    * Update Bulk User Role (BulkRole)
    * @return
    */
   public function actionUpdatebulkuserrole()
   {
   	   	if((isset($_REQUEST['userId']) && $_REQUEST['userId']!="") && (isset($_REQUEST['role_id']) && $_REQUEST['role_id']!=""))
   	   	{
	   		$has_both=false;
	   		$role_type = Role::find()->select('role_type')->where(['id' => $_REQUEST['role_id']])->asArray()->all();
	   		$roleTypes=explode(",",$role_type[0]['role_type']);
	   	 	if(in_array(1,$roleTypes) && in_array(2,$roleTypes)){
		  		$has_both=true;
		  	}
		 	Rolesecurity::deleteAll('user_id = :user_id', [':user_id' => $_REQUEST['user_ids']]);
			User::updateAll(['role_id'=>$_REQUEST['role_id']],'id IN ('.$_REQUEST['userId'].')');
			if(!$has_both){
	   			if(!in_array(1,$roleTypes)){
	   				/*remove case client security*/
	   				ProjectSecurity::deleteAll('client_id != :client_id AND user_id = :user_id', [':client_id' => 0, ':user_id' => $_REQUEST['user_ids']]);
	   			}
	   			if(!in_array(2,$roleTypes)){
	   				/*remove team location security*/
	   				ProjectSecurity::deleteAll('team_id != :team_id AND user_id = :user_id', [':team_id' => 0, ':user_id' => $_REQUEST['user_ids']]);
	   			}
			}
			return 'OK';
   		}
	   	die;
   }

   /**
    * Bulk Edit User
    * @return
    */
   public function actionBulkedituser(){
   		$role_details = Role::find()->where('id!=0')->select(['id','role_name'])->all();
   		return $this->renderAjax('_bulkedituser', [
   			'role_details' => $role_details
   		]);
   }

    /**
     * Displays a manage users for users management.
     * @return mixed
     */
    public function actionUserAdd()
    {
		$role_details = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_name');
		$role_types = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_type');
    	$changePassAfter = array('30' => 'After 30 days', '60' => 'After 60 days', '90' => 'After 90 days');
    	$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->asArray()->where('remove=0')->orderBy('team_location_name ASC')->all(),'id','team_location_name');
    	$childs = (new SecurityFeature)->getSecurityFeatures();
    	/* $mycases = (new Client)->getClientCasesdetails();
    	$myteams = (new Team)->getTeamLocationdetails(); */

    	$is_ad=false;
    	$SettingLdap = Settings::find()->select(['id'])->where(['field'=>'active_dir'])->one();
    	if(!empty($SettingLdap))
    		$is_ad=true;

    	$model = new User();
    	if ($model->load(Yii::$app->request->post())) {
    		if(Yii::$app->request->post('usertypes') == 'ADI') {
    			if($model->addApUser(Yii::$app->request->post(),$model)) {
    				return 'OK';
    			} else {
    				return 'Fail';
    			}
    		}else if(Yii::$app->request->post('usertypes') == 'ADG') {
    			if($model->addGroupApUser(Yii::$app->request->post(),$model)){
    				return 'OK';
    			}else{
    				return 'Fail';
    			}
    		}else if(Yii::$app->request->post('usertypes') == 'IAT') {
    			$model->usr_pass = (new User())->hashPassword($model->usr_pass);
	    		$model->confirm_password = $model->usr_pass;
	    		// checkbox
	    		$usr_inhrent_cases = Yii::$app->request->post('usr_inherent_cases');
				$usr_inhrent_teams = Yii::$app->request->post('usr_inherent_teams');
				$model->usr_inherent_cases = isset($usr_inhrent_cases)?$usr_inhrent_cases:0;
				$model->usr_inherent_teams = isset($usr_inhrent_teams)?$usr_inhrent_teams:0;
				if($model->save()){
					//  permission data
					$permissions= [
	     				'is_sub_new_task' =>54,
	     				'is_sub_com_task' =>55,
	     				'is_sub_new_production' =>113,
	     				'is_sub_past_due' =>56,
	     				'opt_posted_comment' =>57,
	     				'is_sub_self_assign' =>58,
	     				'is_new_todo_post' =>59,
	     				'is_completed_todos' =>60,
	     				'is_todos_assign_to_me' =>61,
	     				'is_servicetask_transists' =>75,
	     				'is_cancel' => 76,
	     				'is_uncanceled' => 77,
	     				'is_unassign' => 78,
	     				'changed_instructions' => 62,
	     				'pending_tasks' => 63,
	     				'approaching_case_budget_spend' =>64,
	     				'reached_case_budget_spend' =>65,
	     				'changed_casedetail' => 67,
	     				'is_sub_com_service' => 120,
	     				'is_sub_new_media' => 131
					];

					// role security features
					$role_security = RoleSecurity::find()->where(['role_id'=>$model->role_id, 'security_force'=>1])->select(['security_feature_id'])->asArray()->all();

					// end role security
					$user_id  = (Yii::$app->db->getLastInsertId()!=0?Yii::$app->db->getLastInsertId():$model->id);
					$my_teams = Yii::$app->request->post('my_teams');
	    			$my_cases = Yii::$app->request->post('my_cases');
	    			$clients  = Yii::$app->request->post('clients');

	    			// model settings
	    			$modelSettings = Settings::find()->where(['field'=>'session_timeout'])->one();
	    			$modelOptions = new Options();
	    			$modelOptions->user_id = $user_id;
	    			$modelOptions->session_timeout = NULL;
    				$modelOptions->timezone_id = 'America/New_York';
    				$modelOptions->default_landing_page = '';
    				// End model settings

    				// model options
					foreach($permissions as $key => $val) {
						foreach($role_security as $value) {
							if($val==$value['security_feature_id']) {
								$modelOptions->$key=1; break;
							} else {
								$modelOptions->$key=0;
							}
						}
					}
					// model
					if($modelSettings->fieldvalue == 1 || strtolower(trim($modelSettings->fieldvalue)) == 'default') {
	    				$modelOptions->session_timeout = '1200';
	    			}
	    			$modelOptions->save(false);

	    			if(!empty($my_teams)){
                        (new ProjectSecurity)->updateTeamSecuritywithLoc($my_teams, $user_id);
	    			}
	    			if(!empty($my_cases)){
                        (new ProjectSecurity)->updateCaseSecurity($my_cases, $user_id, $clients);
	    			}
	    			/* Active log entry */
	    			$activityLog = new ActivityLog();
	    			$activityLog->generateLog('User','Added', $user_id, $model->usr_username);
	    			return "OK";
	    		}else{
	    			//echo "<pre>",print_r($model->getErrors()),"</prE>";
	    			return "Fail";
	    		}
    		}
	    }else{
			$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
	    	return $this->renderAjax('UserAdd',[
	    			'model' => $model,
	    			'user_details' => $user_details,
	    			'is_ad'=>$is_ad,
	    			'role_details' => $role_details,
	    			'role_types'=>$role_types,
	    			'changePassAfter' => $changePassAfter,
	    			'teamLocation' => $teamLocation,
	    		//	'mycases' => $mycases,
	    		//	'myteams' => $myteams,
	    			'security_features' => $childs,
	    			'model_field_length' => $model_field_length
	    	]);
	    }
    }

    /**
     * UserName Check exist or not
     * @return
     */
    public function actionCheckUsernameExist(){
    	$model = User::find()->where(['usr_username'=>$_REQUEST['usr_username']])->all();
    	if(!empty($model)){
    		echo "Exist";
    	}
    	die();
    }

    /**
     * UserSettings Add & update Action
     * @return
     */
    public function actionUpdateuserright()
    {
		if(isset($_POST['User'])) {
			$user_id = $_POST['User']['id'];
//    		$role_id  = $_POST['User']['role_id'];
    		$role_security = (new Rolesecurity)->DeleteRolesecurityAll($user_id);
    		if(!empty($_POST['security_feature']))
    		{
				$user_role_security = (new User)->updateUserSecurityFeatures($user_id, 0, $_POST['security_feature']);
				if(Options::find()->where(['user_id' => $user_id])->count() == 0)
					$options_data = new Options();
				else
					$options_data = Options::find()->where(['user_id' => $user_id])->one();

				$options_data->user_id = $user_id;
				$role_option_arr = array(
					54=>'is_sub_new_task',
					55=>'is_sub_com_task',
					56=>'is_sub_past_due',
					57=>'opt_posted_comment',
					58=>'is_sub_self_assign',
					59=>'is_new_todo_post',
					60=>'is_completed_todos',
					61=>'is_todos_assign_to_me',
					62=>'changed_instructions',
					63=>'pending_tasks',
					64=>'approaching_case_budget_spend',
					65=>'reached_case_budget_spend',
					67=>'changed_casedetail',
					75=>'is_servicetask_transists',
					76=>'is_cancel',
					77=>'is_uncanceled',
					78=>'is_unassign',
					113=>'is_sub_new_production',
					120=>'is_sub_com_service',
					131=>'is_sub_new_media'
				);
				$option_new_array = array();
				foreach($_POST['security_feature'] as $key => $value)
				{
					foreach($role_option_arr as $access_code => $access_email_alert){
						if($access_code == $value)
						{
							$option_new_array[$access_email_alert] = $access_email_alert;
						}
					}
				}
				foreach($role_option_arr as $access_code => $access_email_alert){
					if(isset($option_new_array[$access_email_alert])){
						$options_data->$access_email_alert = 1;
					}else{
						$options_data->$access_email_alert = 0;
					}
				}
				$options_data->save(false);
			}
    	}
    	return 'OK';
    }

    /**
     * Displays a manage users for users access of Cases / Teams.
     * @return mixed
     */

    public function actionUserAccessUpdate($id=0)
    {
		//echo $id;
		//echo "<pre>",print_r($_POST),"</pre>";
		//die;
        $model = $this->findUserModel($id);
        $role_types = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_type');
        $usr_inhrent_cases = Yii::$app->request->post('usr_inherent_cases');
        $usr_inhrent_teams = Yii::$app->request->post('usr_inherent_teams');
        $usr_inherent_cases = isset($usr_inhrent_cases) ? $usr_inhrent_cases : 0;
        $usr_inherent_teams = isset($usr_inhrent_teams) ? $usr_inhrent_teams : 0;
        User::updateAll(['usr_inherent_cases' => $usr_inherent_cases, 'usr_inherent_teams' => $usr_inherent_teams], "id = {$id}");

        //$my_teams = Yii::$app->request->post('teamLocations');
		$teamLocs=Yii::$app->request->post('teamLocs',NULL);
		$my_teams = json_decode(Yii::$app->request->post('teamLocs'),true);
		//echo "<pre> my team",print_r($my_teams);
        //$my_cases = Yii::$app->request->post('clientCasesWithCleint');
		$clientCases = Yii::$app->request->post('clientCases',NULL);
		$my_cases = json_decode(Yii::$app->request->post('clientCases'),true);
        $clients = Yii::$app->request->post('clients');
        $teamLocationDbData = Yii::$app->request->post('db_data');
        //$clientCasesDbData = Yii::$app->request->post('clienCasesDbData');
//        echo "<pre>",print_r(Yii::$app->request->post()),"</pre>";die;
        /* Delete before update */
        //(new ProjectSecurity)->deleteuserprojectsecurity($id);
        $selected_role_types = explode(",", $role_types[$model->role_id]);
//        echo "<pre>",print_r($my_teams),print_r($teamLocationDbData),"</pre>";die;
		$projectsecurityteam = (new ProjectSecurity)->getProjectSecurityWithDetailsTeams($id);
        if (in_array(2, $selected_role_types)) {
            if (!empty($my_teams)) {
				$teamsToinsert = array_diff($my_teams,$projectsecurityteam);
                $teamsTodelete = array_diff($projectsecurityteam,$my_teams);
                /*$teamsToinsert = $my_teams;
                $teamsTodelete = $teamLocationDbData;
                foreach($my_teams as $key => $singleLocation){
                        $dbKey = str_replace(',','',$singleLocation);
                        if(isset($teamLocationDbData[$dbKey]) && $teamLocationDbData[$dbKey] != ''){
                                //Remove key to delete amd keep only those entries which has to insert
                                unset($teamsTodelete[$dbKey]);
                                unset($teamsToinsert[$key]);
                        }
                }*/
                (new ProjectSecurity)->deleteTeamSecuritywithLocations($teamsTodelete, $id);
                (new ProjectSecurity)->updateTeamSecuritywithLocations($teamsToinsert, $id);
            }else{
				$teamsTodelete = $my_teams;
                (new ProjectSecurity)->deleteTeamSecuritywithLocations($teamsTodelete, $id);
            }
			if(empty($my_teams) && isset($teamLocs)) {
				ProjectSecurity::deleteAll("team_id!=0 and user_id=".$id);
			}
        }
		//if()
		//
      	 // die('nelson');

		$projectsecurity = (new ProjectSecurity)->getProjectSecurityWithDetailsClients($id);
		//echo "<pre>",print_r($projectsecurity),"</prE>";//die;
		if (in_array(1, $selected_role_types)) {
            if (!empty($my_cases)) {
				$casesToinsert = array_diff($my_cases,$projectsecurity);
                $casesTodelete = array_diff($projectsecurity,$my_cases);
				//echo "<pre>",print_r($casesToinsert),print_r($casesTodelete),"</pre>";die;
                /*foreach($my_cases as $key => $clientcasedata){
					$dbKey = str_replace(',','',$singleLocation);
					if(isset($clientCasesDbData[$dbKey]) && $clientCasesDbData[$dbKey] != ''){
							//Remove key to delete amd keep only those entries which has to insert
							unset($casesTodelete[$dbKey]);
							unset($casesToinsert[$key]);
					}
                }*/

                (new ProjectSecurity)->deleteCaseSecuritywithClients($casesTodelete, $id);
			    (new ProjectSecurity)->updateCaseSecuritywithClients($casesToinsert, $id);
                /* Remove for update */
            }else{
				$casesTodelete = $my_cases;
                (new ProjectSecurity)->deleteCaseSecuritywithClients($casesTodelete, $id);
            }
			if(empty($my_cases) && isset($clientCases)) {
				ProjectSecurity::deleteAll("client_id!=0 and user_id=".$id);
			}
        }
        /* End */
        return "OK";
    }

    /**
     * Displays a manage users for users management.
     * Changed in IRT-434
     * @return mixed
     */
    public function actionBulkUserAccessUpdate()
    {
        $posted_data = Yii::$app->request->post();
        //$my_teams = Yii::$app->request->post('teamLocations', []);
        //$my_cases = Yii::$app->request->post('clientCasesWithCleint', []);
		$my_teams = json_decode(Yii::$app->request->post('teamLocs'),true);
        $my_cases = json_decode(Yii::$app->request->post('clientCases'),true);
        $users = Yii::$app->request->post('txtusers', '');
        $btn = Yii::$app->request->post('btn', '');
        $limitin = 1000;

        $usr_inhrent_cases = Yii::$app->request->post('bulk_inherent_cases');
        $usr_inhrent_teams = Yii::$app->request->post('bulk_usr_inherent_teams');
//        $usr_inherent_cases = isset($usr_inhrent_cases) ? $usr_inhrent_cases : 0;
//        $usr_inherent_teams = isset($usr_inhrent_teams) ? $usr_inhrent_teams : 0;
        $usersAr = $userAr = explode(",", $users);
        $userArray = $allProjectSecurity = [];
        if (count($userAr) > $limitin) {
            $userArray = array_chunk($userAr, $limitin);
        } else {
            $userArray[0] = $userAr;
        }
		//echo "<pre>",print_r($userArray),"</pre>";die;
        // Break Below query in part of 1000
        // Step 1 Get all selected user data
        // Preppare two arrays to handle clients and teams with users
        if(!empty($userArray)){
            foreach($userArray as $singleUserArr){
                $allProjectSecurity = array_merge($allProjectSecurity,(new ProjectSecurity)->find()->where(['user_id'=>$singleUserArr])->asArray()->all());
                $userlist = implode(',', $singleUserArr);
                // Remove This comment after finish
                $updateCondition = [];
                if(isset($usr_inhrent_cases) && $usr_inhrent_cases != '' && isset($usr_inhrent_teams) && $usr_inhrent_teams != '' )
                    $updateCondition = ['usr_inherent_cases' => $usr_inhrent_cases, 'usr_inherent_teams' => $usr_inhrent_teams];
                else if(isset($usr_inhrent_cases) && $usr_inhrent_cases != '')
                    $updateCondition = ['usr_inherent_cases' => $usr_inhrent_cases];
                else if(isset($usr_inhrent_teams) && $usr_inhrent_teams != '')
                    $updateCondition = ['usr_inherent_teams' => $usr_inhrent_teams];
                if(!empty($updateCondition))
                    User::updateAll($updateCondition, "id IN ({$userlist})");
            }
        }
        //echo "<pre>",print_r($allProjectSecurity),"</pre>";die;
        if(!empty($allProjectSecurity)){
            $dbDataClientCases = $dbDataTeamLocations = [];
            foreach($allProjectSecurity as $single){
                if($single['team_id'] == 0 && $single['team_loc'] == 0){
                    $dbDataClientCases['USER'.$single['user_id'].'CLIENT'.$single['client_id'].'CASE'.$single['client_case_id']] = ['client_id'=>$single['client_id'],'client_case_id'=>$single['client_case_id'],'psid'=>$single['id']];
                }else if($single['client_id'] == 0 && $single['client_case_id'] == 0){
                    $dbDataTeamLocations[$single['user_id'].$single['team_id'].$single['team_loc']] = ['team_id'=>$single['team_id'],'team_loc'=>$single['team_loc'],'psid'=>$single['id']];
                }
            }
        }
        //echo "<pre>",print_r($dbDataClientCases),count($dbDataClientCases),"</pre>";die;
        $dataToInsertCleintCase = $dataToDeleteTeams = $dataToDeleteClientCases = $dataToInsertTeams = [];
        //$dataToDeleteClientCases = $dbDataClientCases;
        if(!empty($usersAr)){
            foreach($usersAr as $singleUser){
                // Preprare array to insert in DB & array to delete from db for Client Cases
                if(!empty($my_cases)){
                    foreach($my_cases as $singleCase){
                    	 $exp_client_case=explode(",", $singleCase);
                    	 $client = $exp_client_case[0];
                    	 $case = $exp_client_case[1];
                         $dbKey = str_replace(',','',$singleCase);
                        if(isset($dbDataClientCases['USER'.$singleUser.'CLIENT'.$client.'CASE'.$case]) && !empty($dbDataClientCases['USER'.$singleUser.'CLIENT'.$client.'CASE'.$case])){
                            //unset($dataToDeleteClientCases[$singleUser.$dbKey]);
                            $dataToDeleteClientCases[] = $dbDataClientCases['USER'.$singleUser.'CLIENT'.$client.'CASE'.$case]['psid'];
                        }else{
                            // Insert in db array for clients
                            $postClientCases = explode(',',$singleCase);
                            $dataToInsertCleintCase[] = [   'user_id'=>$singleUser,
                                                            'client_id'=>$postClientCases[0],
                                                            'client_case_id'=>$postClientCases[1],
                                                            'team_id' => 0,
                                                            'team_loc'=> 0,
                                                        ];
                        }
                    }
                }
                if(!empty($my_teams)){
                    foreach($my_teams as $singleTeam){
                         $dbKey1 = str_replace(',','',$singleTeam);
                        if(isset($dbDataTeamLocations[$singleUser.$dbKey1]) && !empty($dbDataTeamLocations[$singleUser.$dbKey1])){
                           //unset($dataToDeleteClientCases[$singleUser.$dbKey]);
                            $dataToDeleteTeams[] = $dbDataTeamLocations[$singleUser.$dbKey1]['psid'];
                        }else{
                            // Insert in db array for Teams
                            $postTeams = explode(',',$singleTeam);
                            $dataToInsertTeams[] = [   'user_id'=>$singleUser,
                                                        'client_id'=>0,
                                                        'client_case_id'=>0,
                                                        'team_id' => $postTeams[0],
                                                        'team_loc'=> $postTeams[1],
                                                    ];
                        }
                    }
                }
            }
        }
		//echo "<pre>",print_r($dataToDeleteClientCases),count($dataToDeleteClientCases),"</pre>";die;
//      $role_types = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_type');
        $role_types = Yii::$app->request->post('txt_role_type', []);
        $selected_role_types = explode(",", $role_types);
        if (in_array(2, $selected_role_types)) {
            if (!empty($my_teams)) {
                if($btn == 'assign')
                    (new ProjectSecurity)->insertBulkCommon($dataToInsertTeams);
                else if($btn == 'unassign')
                    (new ProjectSecurity)->deleteBulkCommon($dataToDeleteTeams);
            }
        }
        if (in_array(1, $selected_role_types)) {
            if (!empty($my_cases)) {
                if($btn == 'assign')
                    (new ProjectSecurity)->insertBulkCommon($dataToInsertCleintCase);
                else if($btn == 'unassign')
                    (new ProjectSecurity)->deleteBulkCommon($dataToDeleteClientCases);
            }
        }
        /* End */
        return "OK";
    }

    /**
     * Displays a manage users for users management.
     * @return mixed
     */
    public function actionUserUpdate($id)
    {
    	$model = $this->findUserModel($id);
    	$old_status = $model->status;
    	$model->usr_pass = (new User())->decryptPassword($model->usr_pass);
    	$model->confirm_password = $model->usr_pass;

    	$role_details = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_name');
    	$role_types = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_type');
    	$changePassAfter = array('0' => 'Select Force Password Change','1' => 'After 1 days', '30' => 'After 30 days', '60' => 'After 60 days', '90' => 'After 90 days');
    	$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->asArray()->where('remove=0')->orderBy('team_location_name ASC')->all(),'id','team_location_name');

    	/* $mycases = (new Client)->getClientCasesdetails();
    	$myteams = (new Team)->getTeamLocationdetails();
    	$projectsecurity = (new ProjectSecurity)->getProjectSecurity($id); */
    	$childs = (new SecurityFeature)->getSecurityFeatures();
    	$role_security = RoleSecurity::find()->where('user_id='.$id)->asArray()->all();
    	$actLog_user = ActivityLog::find()->select(['activity_type','date_time'])->where("origination='User' AND activity_module_id=".$id." AND activity_type IN ('Inactive','Active')")->all();

    	if ($model->load(Yii::$app->request->post())) {
    		if($model->usr_type!=3){
    			$model->usr_pass = (new User())->hashPassword($model->usr_pass);
    			$model->confirm_password = $model->usr_pass;
    		}
    		$usr_inhrent_cases = Yii::$app->request->post('usr_inherent_cases');
    		$usr_inhrent_teams = Yii::$app->request->post('usr_inherent_teams');
    		$model->usr_inherent_cases = isset($usr_inhrent_cases)?$usr_inhrent_cases:0;
    		$model->usr_inherent_teams = isset($usr_inhrent_teams)?$usr_inhrent_teams:0;
    		if($model->save()){
    			$selected_role_types=explode(",",$role_types[$model->role_id]);
				if(!in_array(2,$selected_role_types)){
					//(new ProjectSecurity)->deleteTeamSecurity($id);
					Yii::$app->db->createCommand("DELETE FROM tbl_project_security where client_id=0 AND client_case_id=0 and team_id!=0 and user_id=".$id)->execute();
				}
				if(!in_array(1,$selected_role_types)){
					//(new ProjectSecurity)->deleteCaseSecurity($id);
					Yii::$app->db->createCommand("DELETE FROM tbl_project_security where client_id!=0 AND client_case_id!=0 and team_id=0 and user_id=".$id)->execute();
				}
				/*$my_teams = Yii::$app->request->post('my_teams');
    			$my_cases = Yii::$app->request->post('my_cases');
    			$clients  = Yii::$app->request->post('clients');
    			//echo "<pre>",print_r($_POST),"</pre>";die;
    			// Delete before update
     			(new ProjectSecurity)->deleteuserprojectsecurity($id);
     			$selected_role_types=explode(",",$role_types[$model->role_id]);
     			if(in_array(2,$selected_role_types)){
	     			if(!empty($my_teams)){
	    				(new ProjectSecurity)->updateTeamSecuritywithLoc($my_teams, $id);
	    			}
     			}
     			if(in_array(1,$selected_role_types)){
	    			if(!empty($my_cases)){
	    				(new ProjectSecurity)->updateCaseSecurity($my_cases, $id, $clients);
	    			}
     			}*/
    			/* Active log Update */
    			$activityLog = new ActivityLog();
    			$activityLog->generateLog('User','Update', $id, $model->usr_username);

    			/* active/unactive user status */
    			$user_postdata = Yii::$app->request->post('User');
    			if(isset($user_postdata['status']) && ($old_status != $user_postdata['status']))
    			{
    				$actLog= new ActivityLog();
    				if($user_postdata['status'] == 1){
    					$type='Active';
    				}else{
    					/*remove all security when user is inactive*/
    					$Optionsdata=Options::find()->where(['user_id'=>$id])->one();
    					if(isset($Optionsdata->id) && $Optionsdata->id!=0){
    						$Optionsdata->delete();
    					}
    					/*remove all security when user is inactive*/
    					$type='Inactive';
    				}
    				$actLog->generateLog("User", $type, $id, $model->usr_username);
    			}
    			/*End*/
    			return "OK";
    		} else {
    			return "Fail";
    		}
    	}else{
		$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		return $this->renderAjax('UserUpdate',[
	    		'role_security' => $role_security,
    			'model' => $model,
    			'security_features' => $childs,
    		//	'projectsecurity' => $projectsecurity,
    			'role_details' => $role_details,
    			'role_types'=>$role_types,
    			'changePassAfter' => $changePassAfter,
    			'teamLocation' => $teamLocation,
    			'password' => $password,
    		//	'mycases' => $mycases,
    		//	'myteams' => $myteams,
    			'actLog_user'=>$actLog_user,
    			'model_field_length' => $model_field_length
	    	]);
    	}
    }

    /**
     * Get All User by Role Dropdown in Manage User
     * @return mixed
     */
    public function actionAjaxRoleUser($role_id,$filteruser='',$from='useraccess',$access=0){

    	$model = User::find()->where('role_id !=0')->joinWith('role');
    	if($role_id != ''){
    		$model = User::find()->where('role_id='.$role_id)->joinWith('role');
    	}

		if($filteruser != ''){
    		$model->andWhere("(CASE WHEN CONCAT(usr_first_name,usr_lastname) <> '' THEN CONCAT(usr_first_name,' ',usr_lastname) ELSE usr_username END) like '%".$filteruser."%'");
    	}

    	if($access == 1){
			$model->distinct = true;
			$model->joinWith('projectSecurities');
			$model->andWhere('tbl_project_security.user_id IS NULL');
		}

    	$dataProvider = new ActiveDataProvider([
            'query' => $model->select(['tbl_user.id','usr_first_name','usr_lastname','usr_email','usr_type','usr_username','status','role_id'])->orderBy(['usr_lastname'=>SORT_ASC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

    	return $this->renderAjax('_ajaxroleuser_access',['role_id' => $role_id, 'user_details'=>$model, 'model'=>$model,'dataProvider'=>$dataProvider,'from'=>$from]);
    }

    public function actionRenderTeamLoc()
    {
		$teams = Yii::$app->request->post('teams',0);
		$teamsModel = TeamLocs::find()->joinWith(['team','teamlocationMaster'])->select(['team_id','team_loc','tbl_team.team_name','tbl_teamlocation_master.team_location_name'])->where("team_id IN ($teams)")->all();
		//echo "<pre>",print_r($teamsModel),"</pre>";die;
		/*$dataProvider = new ActiveDataProvider([
            'query' => $teamsModel,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);*/
		$response['flag'] = 'error';
		if(!empty($teamsModel)){
			$response['html'] = $this->renderAjax('renderTeamLoc',['model'=>$teamsModel]);
			$response['flag'] = 'ok';
		}
		echo json_encode($response);
	}

    /**
     * Displays a Manage Role form in users management.
     * @return mixed
     */
    public function actionRoleUpdate($role_id){
    	$model = $this->findRoleModel($role_id);
		//echo "<prE>",print_r($model),"</pre>";
    	$childs = (new SecurityFeature)->getSecurityFeatures();
    	$model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    	return $this->renderAjax('RoleUpdate',[
            'model' 		=> $model,
            'security_features' 	=> $childs,
            'model_field_length' 	=> $model_field_length
    	]);
    }

 	/**
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRoleModel($id){
    	if (($model = Role::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

	/**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUserModel($id) {
    	if (($model = User::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    /**
     * Role Delete
     */
    public function actionRoleDelete(){
    	$role_id = $_REQUEST['role_id'];
    	$used_role = (new Role)->checkRoleUsed($role_id);
    	if($used_role>0) {
    		return "Fail";
    	} else {
			ProjectRequestTypeRoles::deleteAll(['role_id'=>$role_id]);
			CommentRoles::deleteAll(['role_id'=>$role_id]);
    		(new RoleSecurity())->deleteSecurityRole($role_id); // Role Security Delete
    		$model = $this->findRoleModel($role_id);
    		if($model->delete()) {
    			return "OK";
    		}
    	}
    }

    /**
     * User Options to saved Email Alert Option, Change Password and  Session Timeout
     * */
    public function actionOptions()
    {
    	$id = Yii::$app->user->identity->id;
    	$model = new Options();
    	$user_model = $this->findUserModel($id);
    	$timezones = $model->getTimezone();
    	$options_data = Options::find()->where('user_id ='.Yii::$app->user->identity->id)->one();
        $settings_info = Settings::find()->where(['field' =>'session_timeout'])->one();
//        echo '<pre>';print_r($options_data);die;
		if(isset($options_data->id) && $options_data->id!== null){
    		$model = Options::findOne($options_data->id);
		}else{
			$model->user_id = Yii::$app->user->identity->id;
		}
//      echo '<pre>';
//      $user_model1 = new User();
//      $all_sec = $user_model1->getAllSecurityAccess(2);
//      print_r($all_sec);
//      die('nelson');
		if ($model->load(Yii::$app->request->post())){
			$post_data=Yii::$app->request->post();
			$attributes = $model->getAttributes();
//			echo "<pre>",print_r($post_data),"</pre>";
			if(isset($options_data->id) && $options_data->id!== null){
				foreach($attributes as $key=>$attr){
					if(!in_array($key,array('old_password','user_id','id','session_timeout','usr_pass','confirm_password','timezone_id','select_all')))
					{
						if(!isset($post_data['Options'][$key])){
							$model->$key = 0;
						}
					}
				}
			}
//                        echo '<pre>',print_r($model);die;
    		if(isset(Yii::$app->request->post('Options')['confirm_password']) && Yii::$app->request->post('Options')['confirm_password']!=""){
                            $user_model->usr_pass = (new User())->hashPassword(Yii::$app->request->post('Options')['usr_pass']);
                            $user_model->save(false);
		    }
		    if(isset(Yii::$app->request->post('Options')['timezone_id'])){
		    	$_SESSION['usrTZ']=Yii::$app->request->post('Options')['timezone_id'];
		    }
		    $model->save(false);
			$this->redirect(array('user/options','saved'=>1));
		}

		/* IRT 31 Default landing page array */
		$roleId = Yii::$app->user->identity->role_id;
		$roleInfo = Role::find()->select(['role_type'])->where('id = '.$roleId)->one();
		$User_Role = explode(',', $roleInfo->role_type);
		/*$default_landing_page = array();
		if((new User)->checkAccess(1)){
			$default_landing_page[0] = 'My Assignments';
		} if((new User)->checkAccess(3)){
			$default_landing_page[1] = 'Media';
		} if((new User)->checkAccess(4)){
			if ($roleId == '0' || in_array(1,$User_Role)) {
				$default_landing_page[2] = 'My Cases';
			}
		} if((new User)->checkAccess(5)){
			if ($roleId == '0' || in_array(2,$User_Role)) {
				$default_landing_page[3] = 'My Teams';
			}
		} if((new User)->checkAccess(2)){
			$default_landing_page[4] = 'Global Projects';
		} if((new User)->checkAccess(7)){
			$default_landing_page[5] = 'Billing';
		} if((new User)->checkAccess(11)){
			$default_landing_page[6] = 'Reports';
		} if((new User)->checkAccess(8)){
			$default_landing_page[7] = 'Administration';
		}*/
		$default_landing_page = array();
		if((new User)->checkAccess(1)){
			$default_landing_page[1] = 'My Assignments';
		} if((new User)->checkAccess(3)){
			$default_landing_page[2] = 'Sources';
		} if((new User)->checkAccess(4)){
			if ($roleId == '0' || in_array(1,$User_Role)) {
				$default_landing_page[3] = 'My Cases';
			}
		} if((new User)->checkAccess(5)){
			if ($roleId == '0' || in_array(2,$User_Role)) {
				$default_landing_page[4] = 'My Teams';
			}
		} if((new User)->checkAccess(2)){
			$default_landing_page[5] = 'Global Projects';
		} if((new User)->checkAccess(7)){
			$default_landing_page[6] = 'Billing';
		} if((new User)->checkAccess(11)){
			$default_landing_page[7] = 'Reports';
		} if((new User)->checkAccess(8)){
			$default_landing_page[8] = 'Administration';
		}
		/* End */

		//echo "<pre>",print_r($default_landing_page);die;

		//echo "<pre>",print_R($model); die;
		$saved = yii::$app->request->get('saved',0);
		$model_field_length = (new User)->getTableFieldLimit($user_model->tableSchema->name);
		return $this->render('Options',['saved'=>$saved,'timezones'=>$timezones,'user_model'=>$user_model,'model'=>$model,'options_data'=>$options_data,'settings_info'=>$settings_info,'model_field_length' => $model_field_length, 'default_landing_page' => $default_landing_page]);
    }

    /**
     * Delete User Details
     */
    public function actionUserDelete()
    {
    	/* User assigned to Tasks */
    	if(TasksUnits::find()->where('unit_assigned_to='.$_REQUEST['user_id'])->count() > 0){
    		return "ERROR";
    	}

    	/* User assigned to Tasks Units Todos */
    	if(TasksUnitsTodos::find()->where('assigned='.$_REQUEST['user_id'])->count() > 0){
    		return "ERROR";
    	}

    	/* User assigned to Evidence Transaction */
		if(EvidenceTransaction::find()->where('trans_requested_by='.$_REQUEST['user_id'])->count() > 0){
			return "ERROR";
		}

    	(new ProjectSecurity)->deleteuserprojectsecurity($_REQUEST['user_id']); // Delete All project security by user_id
    	Options::deleteAll(['user_id' => $_REQUEST['user_id']]);
    	UserLog::deleteAll(['user_id' => $_REQUEST['user_id']]);
		ActivityLog::deleteAll(['user_id' => $_REQUEST['user_id']]);
		CommentRolesUsers::deleteAll(['user_id' => $_REQUEST['user_id']]);
		CommentTeamsUsers::deleteAll(['user_id' => $_REQUEST['user_id']]);
    	$model = $this->findUserModel($_REQUEST['user_id']);
		if($model->delete()){
    		return "OK";
    	}
    	die();
    }

    /**
     * Events action
     * @return
     */
    public function actionEvents(){
		$current_tabs = yii::$app->request->get('current_tabs');
		$assigned = Yii::$app->request->get('assigned');
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		// echo "<pre>"; print_r($current_tabs); exit;
		return $this->render('Events',['current_tabs'=>$current_tabs,'assigned'=>$assigned]);
    }
    /*
     * Ajax Calender
     * */
    public function actionJsoncalendar($current_tabs,$assigned){
			$userId = Yii::$app->user->identity->id;
			$roleId = Yii::$app->user->identity->role_id;
			$joinwhere = "";
			$join = "";
			if($assigned == 'assigned'){
                            $where = " AND t.id IN (SELECT instruct.task_id FROM tbl_tasks_units INNER JOIN tbl_task_instruct as instruct ON tbl_tasks_units.task_id = instruct.task_id WHERE tbl_tasks_units.unit_assigned_to = ".$userId." AND instruct.task_id = t.id group by instruct.task_id)";
			}

			if($roleId != 0){
				$joinwhere.= " AND (security.user_id=".$userId.")";
			}else{
				$joinwhere.= " AND (security.client_case_id!=0)";
			}

			if($current_tabs == 'case'){
				$taskdata_sql = "select distinct t.id, t.client_case_id, t.task_status, t.created, instruct.task_duedate,instruct.task_timedue,instruct.task_id,instruct.task_priority,client.client_name,ccase.case_name, users.usr_first_name,users.usr_lastname from tbl_tasks as t
				INNER JOIN tbl_task_instruct as instruct ON t.id = instruct.task_id
				INNER JOIN tbl_client_case as ccase ON t.client_case_id = ccase.id
				INNER JOIN tbl_client as client ON ccase.client_id = client.id
				INNER JOIN tbl_user as users ON t.created_by = users.id
				INNER JOIN tbl_project_security as security ON security.client_case_id = t.client_case_id  ".$joinwhere."
				where t.task_status IN (0,1,2,3) AND t.task_closed=0 AND t.task_cancel=0 AND ccase.is_close = 0 AND instruct.isactive = 1 ".$where;

				//$params = array(':userId'=>$userId);
				$taskdata = \Yii::$app->db->createCommand($taskdata_sql)->queryAll();
//				echo "<pre>"; print_r($taskdata_sql); exit;
			}else if($current_tabs == 'team'){
				$taskdata_sql = "select distinct t.id,t.client_case_id,t.task_status,t.created,instruct.task_duedate,instruct.task_timedue,instruct.task_id,instruct.task_priority,client.client_name,ccase.case_name,users.usr_first_name,users.usr_lastname from tbl_tasks as t
				INNER JOIN tbl_task_instruct as instruct ON t.id = instruct.task_id
				INNER JOIN tbl_client_case as ccase ON t.client_case_id = ccase.id
				INNER JOIN tbl_client as client ON ccase.client_id = client.id
				INNER JOIN tbl_user as users ON t.created_by = users.id
				INNER JOIN tbl_tasks_teams as team ON t.id = team.task_id
				where t.task_status IN (0,1,2,3) AND t.task_closed=0 AND t.task_cancel=0 AND instruct.isactive = 1 AND team.team_loc IN (SELECT team_loc FROM tbl_project_security WHERE team_id!=0  AND user_id=".$userId.") AND team.team_id IN (SELECT team_id FROM tbl_project_security WHERE team_id !=0  AND user_id=".$userId.")".$where;
				//$params = array(":userId"=>$userId);
				$taskdata = \Yii::$app->db->createCommand($taskdata_sql,$params)->queryAll();
				//echo "<pre>"; print_r($taskdata); exit;

			}
			if(!empty($taskdata)) {
				$role_info = Role::find()->where('id ='.$roleId)->one();
				$role_type = explode(',', $role_info->role_type);
				$Event = array();
				$i = 0;
				$curUTCTime = date('Y-m-d H:i:s', time());
				$curUTCTime = (new Options)->ConvertOneTzToAnotherTz($curUTCTime, 'UTC', $_SESSION['usrTZ'], "YMDHIS");
				foreach($taskdata as $data) {
					 $Event[$i]['title'] = '  #'.$data['id'];
					if ((isset($data['task_duedate']) && $data['task_duedate'] != "0000:00:00") && (isset($data['task_timedue']) && $data['task_timedue'] != "0000:00:00")) {
						$task_duedate = (new Options)->ConvertOneTzToAnotherTz($data['task_duedate'] . " " . $data['task_timedue'], 'UTC', $_SESSION['usrTZ'], "date");
                        $task_timedue = (new Options)->ConvertOneTzToAnotherTz($data['task_duedate'] . " " . $data['task_timedue'], 'UTC', $_SESSION['usrTZ'], "time");

                        $dbdatetime = date("Y-m-d H:i:s", strtotime($data['task_duedate'] . " " . $task['task_timedue']));
                        $dbdatetimeUTC = (new Options)->ConvertOneTzToAnotherTz($dbdatetime, 'UTC', $_SESSION['usrTZ'], "YMDHIS");

                        if ($data['task_status'] == 0 || $data['task_status'] == 1 || $data['task_status'] == 3) {
                            $task_time = strtotime($task_duedate . " " . date("H:i", strtotime($task_timedue)));
                            if (strtotime($dbdatetimeUTC) < strtotime($curUTCTime))
                            {
								//pastdue condition
                                $Event[$i]['title'] = "   #" . $data['id'] . " ";
                                $Event[$i]['icon'] = "exclamation";
                                $str.='<span style=color:red;font-weight:bold;>Past Due</span><br>';
                            }
                        }
					}
					if ($data['task_status'] == 4)
                        $Event[$i]['backgroundColor'] = 'Black!important';
                    else if ($data['task_status'] == 3)
                        $Event[$i]['backgroundColor'] = 'Gray!important';
                    else if ($data['task_status'] == 0)
                        $Event[$i]['backgroundColor'] = 'Blue!important';
                    else if ($data['task_status'] == 1)
                       $Event[$i]['backgroundColor'] = 'Green !important';
					 $Event[$i]['id'] = $data['id'];

					 $Event[$i]['start'] = $task_duedate . ' ' . date('H:i', strtotime($task_timedue));
                     $Event[$i]['allDay'] = false;
                     $Event[$i]['end'] = $task_duedate . " " . date('H:i', strtotime('+20 minutes' . $task_timedue));
                     $i++;
				}
			}
			 $data = json_encode($Event);
			  return $data;
	}

	public function actionGetcalenderdetails(){
		$task_id = Yii::$app->request->get('id');
		$taskdata = array();
		$task_status = Yii::$app->params['task_status'];
		if(isset($task_id) && !empty($task_id)){
			$taskdata=Tasks::findOne($task_id);
		}
		return $this->renderpartial('calenderdetails',['taskdata'=>$taskdata,'task_status'=>$task_status]);

	}
	/**
	 * Double Click Event Of My Event Module
	 * */
    public function actionGetdoubleclickurl(){
		$task_id = Yii::$app->request->get('id');
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		$role_info = Role::find()->where('id = '.$roleId)->one();
		$role_type = explode(',', $role_info->role_type);
		$url = "";
		$taskdata = Tasks::findOne($task_id);

		if (in_array(1, $role_type) && in_array(2, $role_type))
		{
			if((new User)->checkAccess(4.01)){
				$url = 'case-projects/index&case_id=' . $taskdata->client_case_id . '&task_id=' . $taskdata->id;
			}
		} else if (in_array(1, $role_type)) { /// client/Case Manager
			if((new User)->checkAccess(4.01)){
				$url = 'case-projects/index&case_id=' . $taskdata->client_case_id . '&task_id=' . $taskdata->id;
			}
		} else if (in_array(2, $role_type)) {

			if((new User)->checkAccess(5.01)){
				//echo "<pre>"; print_r($role_type); exit;
				$taskdata_sql = "select t.id,team.team_id,team.team_loc from tbl_tasks as t INNER JOIN tbl_tasks_teams as team ON team.task_id = t.id where t.id= $task_id AND team.team_id IN (select team_id from tbl_project_security where user_id = $userId) AND team.team_loc IN (select team_loc from tbl_project_security where user_id = $userId)";
				$params = array(":task_id"=>$task_id,":user_id"=>$userId);
				$taskdata1 = \Yii::$app->db->createCommand($taskdata_sql)->queryAll();
				$url = 'team-projects/index&team_id=' . $taskdata1[0]['team_id'] . '&team_loc='.$taskdata1[0]['team_loc'].'&task_id=' . $taskdata1[0]['id'];
			}
		}
		return $url; //exit;
	}
    /**
     * Validate User using ajax
     * @return
     */
    public function actionUservalidate(){
    	$model = new User();
    	if(isset(Yii::$app->request->post('User')['id'])){
    		$model->id=Yii::$app->request->post('User')['id'];
    	}
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($model);
    	}
    }

    /**
     * validate User using ajax & get mycase , myteams
     * @return
     */
    public function actionNextUserAddAccess()
    {
        $id = Yii::$app->request->post('User')['id'];
//        $mycases = (new Client)->getClientCasesdetailsArray();
//        echo "<pre>",print_r($clientList),"</pre>";die;

//        $projectsecurity = '';
//
//        if ($id != '')
//            $projectsecurity = (new ProjectSecurity)->getProjectSecurityWithDetails($id);
//
//        if (!empty($projectsecurity)) {
//            $client_sec = $teams_sec = $clients_sec_arr = $teams_sec_arr = [];
//            foreach ($projectsecurity as $single_sec){
//                if ($single_sec['team_id'] == 0 && $single_sec['team_loc'] == 0) {
//                   $client_sec[] =  $single_sec['client_id'].'|'.$single_sec['client_case_id'];
//                   $clients_sec_arr[] = $single_sec;
//                }else if ($single_sec['client_id'] == 0 && $single_sec['client_case_id'] == 0) {
//                   $teams_sec[] =  $single_sec['team_id'].'|'.$single_sec['team_loc'];
//                   $teams_sec_arr[] = $single_sec;
//                }
//            }
//            $clientList = (new ClientCase)->clientsFilterdProjectSecurity($id);
//            $teamNames = (new Team)->getTeamsWithPrjectSec($id);
//        } else {
//            $clientList = (new Client)->getCleintsArray();
//            $teamNames = (new Team)->getTeamnames();
//        }
//        $clientList = ['All' => 'All'] + $clientList;
//        $teamNames = ['All' => 'All'] + $teamNames;

        $model = new User();
        if ($id != '')
            $model = $this->findUserModel($id);
        //$role_types = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_type');
        //$changePassAfter = array('0' => 'Select Force Password Change','1' => 'After 1 days', '30' => 'After 30 days', '60' => 'After 60 days', '90' => 'After 90 days');
        //$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->asArray()->where('remove=0')->orderBy('team_location_name ASC')->all(),'id','team_location_name');
        return $this->renderAjax('_next_useracess', ['model' => $model]);
    }
    /*
     * IRT-434
     * Get only user settings by user id
     * Added date:18-4-2017
     */
    public function actionGetSingleUserSettings(){
        $post_data = Yii::$app->request->post();
        $user_id = Yii::$app->request->post('user_id');
        $model = new User();
        if ($user_id != '')
            $model = $this->findUserModel($user_id);
        $role_details = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_name');
        $role_security = RoleSecurity::find()->where('user_id=' . $user_id)->asArray()->all();
        $security_features = (new SecurityFeature)->getSecurityFeatures();
        return $this->renderAjax('_next_useraccess_settings',['security_features' => $security_features,'model' => $model, 'role_details' => $role_details,'role_security' => $role_security,]);
    }
    /*
     * IRT-434
     * Get only client cases for permission
     * Added date:18-4-2017
     */
    public function actionGetOnlyClientCasesPermissions(){
        $user_id = Yii::$app->request->post('user_id');
        $projectsecurity = '';
        /*if ($user_id != '')
            $projectsecurity = (new ProjectSecurity)->getProjectSecurityWithDetailsClients($user_id);
        if (!empty($projectsecurity)) {
            $clientList = (new ClientCase)->clientsFilterdProjectSecurity($user_id);
        } else {
            $clientList = (new Client)->getCleintsArray();
        }
        $clientList = ['All' => 'All'] + $clientList;*/
		$clientListAr = (new Client)->getClientCasesdetailsArray();
		$projectsecurity = (new ProjectSecurity)->getProjectSecurityWithDetailsClients($user_id);
		//echo "<pre>",print_r($clientListAr);die;

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

        $model = new User();
        if ($user_id != '')
            $model = $this->findUserModel($user_id);

		//echo "<pre>",print_r($clientList),print_r($projectsecurity),"</pre>";die;

        return $this->renderAjax('_user_access_client_cases', [ 'selectedCases' => $selectedCases,'model' => $model,'clientList' =>$clientList,'projectsecurity' => $projectsecurity]);
    }
    /*
     * IRT-434
     * Get only client cases for permission
     * Added date:18-4-2017
     */
     public function actionGetOnlyTeamsPermissions(){
        $user_id = Yii::$app->request->post('user_id');
        $projectsecurity = '';
        /*if ($user_id != '')
            $projectsecurity = (new ProjectSecurity)->getProjectSecurityWithDetailsTeams($user_id);
        if (!empty($projectsecurity)) {
            $teamNames = (new Team)->getTeamsWithPrjectSec($user_id);
        } else {
            $teamNames = (new Team)->getTeamnames();
        }
        $teamNames = ['All' => 'All'] + $teamNames;
		*/
		$teamListAr = (new Team)->getTeamLocdetailsArray();
		$projectsecurity = (new ProjectSecurity)->getProjectSecurityWithDetailsTeams($user_id);

		//echo "<pre>",print_r($projectsecurity),"</pre>";die;

		$teamList = [];
		$selectedteamloc = [];
		if(!empty($teamListAr)) {
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
		}

        $model = new User();
        if ($user_id != '')
            $model = $this->findUserModel($user_id);
        return $this->renderAjax('_user_access_team', [ 'selectedteamloc' => $selectedteamloc,'model' => $model,'teamList' =>$teamList,'teamNames' => $teamNames,'projectsecurity' => $projectsecurity]);
    }
     /*
      * IRT-434
      * Get aLl list of the client
      *
      *
      */
    public function actionGetTeamLocationList(){
        $post_data = Yii::$app->request->post();
//        echo '<pre>';print_r($post_data);die;
        $allTeamsList = (new Team)->getTeamLocationdetailsByIds($post_data['useraccessTeams'],$post_data['teamLocations']);
        return $this->renderAjax('_ajax_team_locations',['myteams'=>$allTeamsList]);
    }
    /* IRT - 434
     * Get All Cleint List by text
     * return JSON array
     * */
    public function actionGetClientList($q = null, $id = null){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];
            if (!is_null($q)) {
                $query = new Query;
                $query->select('id, client_name AS text')
                        ->from('tbl_client')
                        ->where(['like', 'client_name', $q])
                        ->limit(1000);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_merge(array('0'=>['id'=>'All','text'=>'All']), array_values($data));
//                $out['results'] = array_values($data);
            }
            elseif ($id > 0) {
                    $out['results'] = ['id' => $id, 'text' => Client::find($id)->name];
            }
            return $out;
    }
    /* IRT - 434
     * Get All Cleint List by text
     * return JSON array
     * */
    public function actionGetTeamList($q = null, $id = null){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!is_null($q)) {
			$query = new Query;
			$query->select('id, 	team_name AS text')
				->from('tbl_team')
				->where(['like', 'team_name', $q])
				->limit(1000);
			$command = $query->createCommand();
			$data = $command->queryAll();
                        $out['results'] = array_merge([['id'=>'All','text'=>'All']], array_values($data));
//			$out['results'] = array_values($data);
		}
		elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'text' => Team::find($id)->name];
		}
		return $out;
	}
     /*
      * IRT-434
      * Get aLl list of the client
      *
      *
      */
    public function actionGetCleintCaseList(){
        $post_data = Yii::$app->request->post();
        $allCaseList = (new ClientCase)->getCleintsDetaills($post_data['useraccessclients'],$post_data['clientCasesWithCleint']);
//        print_r($allCaseList);die;
        return $this->renderAjax('_ajax_client_cases',['mycases'=>$allCaseList]);
    }
	/**
     * validate User using ajax & get mycase , myteams
     * @return
     */
    public function actionBulkUserAccess()
    {
        $ids = Yii::$app->request->post('ids',[]);
        $role_id = Yii::$app->request->post('role_id',0);
//        $mycases = (new Client)->getClientCasesdetailsArray();
//        $myteams = (new Team)->getTeamLocationdetailsArray();
        //$clientList = (new Client)->getCleintsArray();
		$clientListAr = (new Client)->getClientCasesdetailsArray();
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
					$case['select'] = false;
					$client['children'][] = $case;
				}
				if(!empty($client['children']))
					$clientList[] = $client;
			}
		}
        //$teamNames = (new Team)->getTeamnames();
        //$clientList = ['All'=>'All'] + $clientList;
    	//$teamNames = ['All'=>'All'] + $teamNames;
		$teamListAr = (new Team)->getTeamLocdetailsArray();
		//echo "<pre>",print_r($projectsecurity),"</pre>";die;
		$teamList = [];
		$selectedteamloc = [];
		foreach($teamListAr as $team_name => $teamLocs) {
			$team = [];
			foreach($teamLocs as $team_id => $teamloc) {
				$team['title'] = $team_name;
				$team['isFolder'] = true;
				$team['key'] = $team_id;
				$locs = [];
				foreach($teamloc as $loc_id => $loc_name) {
					$locs['title'] = $loc_name;
					$locs['key'] = $team_id.','.$loc_id;
					$locs['select'] = false;
					$team['children'][] = $locs;
				}
				if(!empty($team['children']))
					$teamList[] = $team;
			}
		}

        $model = new User();
        $role_details = ArrayHelper::map(Role::find()->where('id!=0')->asArray()->all(), 'id', 'role_name');
        $selected_role_data = Role::findOne($role_id);
        return $this->renderAjax('_bulk_useraccess',[
                'role_details'=>$role_details,
                'clientList'=>$clientList,
                'teamNames'=>$teamList,
                'model' => $model,
                'selected_role_data' => $selected_role_data,
                'ids' => $ids
        ]);
    }

     /**
     * Get Ad Users data
     * */
    public function actionGetapusers()
    {
		$ldap_users = array();
		echo json_encode($ldap_users);
    	die;
    	$Adusers = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid','usr_username'])->all(), 'ad_uid', 'usr_username');
		$AduserList = array_keys($Adusers);
		$AduserNameList = array_values($Adusers);
		//echo "<pre>",print_r($Adusers),print_r($AduserList),print_r($AduserNameList),"</prE>";die;
		//$AduserList = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid'])->all(), 'usr_username', 'usr_username');


    	$ldap_data=(new Settings)->ldapsearch();
    	//echo "<pre>",print_r($ldap_data),"</pre>";die;
    	if(!empty($ldap_data))
    	{
    		foreach ($ldap_data as $key=>$data)
    		{
    			if(!in_array($key,$AduserList)){
					if(isset($data['samaccountname']) && $data['samaccountname']!=""){
						if(!in_array($data['samaccountname'],$AduserNameList)){
							$ldap_users[$key]=$data['cn'];
						}
					}else{
						$ldap_users[$key]=$data['cn'];
					}
				}
			}
    	}
    	/*$AduserList = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid'])->all(), 'ad_uid', 'ad_uid');
    	$ldap_users = array();

    	$ldap_data=(new Settings)->ldapsearch();
    	//echo "<pre>",print_r($ldap_data),"</pre>";die;
    	if(!empty($ldap_data))
    	{
    		foreach ($ldap_data as $key=>$data)
    		{
    			if(!in_array($key,$AduserList))
    				$ldap_users[$key]=$data['cn'];
    		}
    	}*/

    	echo json_encode($ldap_users);
    	die;
    }

	/**
     * Get Ad Users data
     * */
    public function actionGetadusers()
    {
		static $ldap_cookie='';
		$params = Yii::$app->request->queryParams;
		$total_cnt=0;
		$page=isset($params['page'])?$params['page']:1;
		$limit=50;
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out['items'] = array();
		$out['total_count']=0;
		$out['pagination']['more']=false;
		$q=$params['q'];
		$offset=( ( $page - 1 ) * $limit );
		$Adusers = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid','usr_username'])->all(), 'ad_uid', 'usr_username');
		$AduserList = array_keys($Adusers);
		$AduserNameList = array_values($Adusers);
		//echo "<pre>",print_r($Adusers),print_r($AduserList),print_r($AduserNameList),"</prE>";die;
		//$AduserList = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid'])->all(), 'usr_username', 'usr_username');
    	$ldap_users = array();

    	$ldap_data=(new Settings)->ldapsearchWithPagination($ldap_cookie,$q);
		//echo "<pre>",print_r($ldap_data),"</pre>";die;
    	if(!empty($ldap_data))
    	{
			$info=$ldap_data['info'];
			$ldap_cookie=$info['cookies'];
			$out['total_count']=$info['count'];
			unset($ldap_data['info']);
    		foreach ($ldap_data as $key=>$data)
    		{
    			//if(!in_array($key,$AduserList))
				{
					if(isset($data['samaccountname']) && $data['samaccountname']!=""){
						if(!in_array($data['samaccountname'],$AduserNameList)){
							if(!in_array($key,$ldap_users)){
								$ldap_users[$key]=$key;
								$val=Html::decode($data['cn']);
								$out['items'][] = ['id' => $data['samaccountname'], 'text' => $val];
							}
						}
					}else{
						if(!in_array($key,$ldap_users)){
							$ldap_users[$key]=$key;
							$val=Html::decode($data['cn']);
							$out['items'][] = ['id' => $key, 'text' => $val];
						}
					}
				}
			}
    	}
    	/*$AduserList = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid'])->all(), 'ad_uid', 'ad_uid');
    	$ldap_users = array();

    	$ldap_data=(new Settings)->ldapsearch();
    	//echo "<pre>",print_r($ldap_data),"</pre>";die;
    	if(!empty($ldap_data))
    	{
    		foreach ($ldap_data as $key=>$data)
    		{
    			if(!in_array($key,$AduserList))
    				$ldap_users[$key]=$data['cn'];
    		}
    	}*/
    	if($out['total_count'] > 0 && ($page * $limit) < $out['total_count']){
			$out['pagination']['more']=true;
		}
		return $out;
    	//echo json_encode($ldap_users);
    	//die;
    }
    /**
     * Get Ad Groups data
     * */
    public function actionGetAdgroups(){
    	$ldapgroup=array();
    	$ldapgroup_data=(new Settings)->ldapgroupsearch();
    	if(!empty($ldapgroup_data)){
    		foreach ($ldapgroup_data as $key=>$val){
    			$ldapgroup[$key]=$val;
    		}
    	}
    	echo json_encode($ldapgroup);
    	die;
    }
    /**
     * Get Ad Group members data
     * */
    public function actionGetAdGroupMembers()
    {
    	$ldap_result=array();
    	$group=Yii::$app->request->get('adg');
		$userList = [];
    	if(isset($group))
    	{
    		//$AduserList= ArrayHelper :: map(User::find()->where(["usr_type"=>'3'])->all(), 'ad_uid', 'ad_uid');
			$Adusers = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid','usr_username'])->all(), 'ad_uid', 'usr_username');
			$AduserList = array_keys($Adusers);
			$AduserNameList = array_values($Adusers);
    		$ldapgrp_data=(new Settings)->ldapSearchByDN(Yii::$app->request->get('adg'));
			//echo "<pre>",print_r($ldapgrp_data),"</pre>";die;
    		if(is_array($ldapgrp_data) && !empty($ldapgrp_data)){
    			foreach ($ldapgrp_data as $uid=>$data){
					$aduser = [];
    				//if(!in_array($uid,$AduserList))
					{
						if(isset($data['samaccountname']) && $data['samaccountname']!=""){
							if(!in_array($data['samaccountname'],$AduserNameList)){
								//$ldap_result[$uid]=$data['cn'];
								$aduser['title'] = $data['cn'];
								$aduser['isFolder'] = false;
								$aduser['key'] = $data['samaccountname'];
								$userList[] = $aduser;
							}
						}else{
							//$ldap_result[$uid]=$data['cn'];
							$aduser['title'] = $data['cn'];
							$aduser['isFolder'] = false;
							$aduser['key'] = $uid;
							$userList[] = $aduser;
						}
    					//$ldap_result[$uid]=$data['cn'];
    				}
    			}
    		}
    	}
		//echo "<pre>",print_r($userList);die;
		/*if(isset($group))
    	{
    		$AduserList= ArrayHelper :: map(User::find()->where(["usr_type"=>'3'])->all(), 'ad_uid', 'ad_uid');
    		$ldapgrp_data=(new Settings)->ldapSearchByDN(Yii::$app->request->get('adg'));
    		if(is_array($ldapgrp_data) && !empty($ldapgrp_data)){
    			foreach ($ldapgrp_data as $uid=>$data){
    				if(!in_array($uid,$AduserList)){
    					$ldap_result[$uid]=$data['cn'];
    				}
    			}
    		}
    	}*/
    	return $this->renderAjax("grpmemberuser",['ldap_result'=>$ldap_result,'userList'=>$userList]);
    }


}

?>
