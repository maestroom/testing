<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Security;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use yii\web\Session;
use yii\web\JsExpression;

use app\models\RoleSecurity;
use app\models\ReportsReportType;
use app\models\ProjectSecurity;
use app\models\Settings;
use app\models\Options;
use app\models\Tasks;
use app\models\TasksTeams;
use app\models\Teamservice;
use app\models\ClientCase;
use kartik\grid\GridView;
/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property integer $location_id
 * @property string $usr_type
 * @property string $usr_email
 * @property string $usr_pass
 * @property string $usr_first_name
 * @property string $usr_lastname
 * @property string $usr_username
 * @property string $usr_mi
 * @property integer $role_id
 * @property integer $usr_inherent_cases
 * @property integer $usr_inherent_teams
 * @property integer $status
 * @property integer $change_pass_after
 * @property string $last_pass_change
 * @property string $ad_users
 * @property string $ad_uid
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property ActivityLog[] $activityLogs
 * @property CommentsRead[] $commentsReads
 * @property EvidenceTransactions[] $evidenceTransactions
 * @property EvidenceTransactions[] $evidenceTransactions0
 * @property Options[] $options
 * @property ProjectSecurity[] $projectSecurities
 * @property SavedFilters[] $savedFilters
 * @property Role $role
 * @property UserLog[] $userLogs
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
	public $key="password encryption/decryption";
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 10;
	public $role_name;
	public $old_password;
	public $confirm_password;
	public $role_type;
	public $role_info;
	public $ad_group;
	public $full_name;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           // [['location_id', 'usr_type', 'usr_email', 'usr_username','usr_pass', 'confirm_password', 'usr_mi', 'role_id', 'usr_inherent_cases', 'usr_inherent_teams', 'status', 'last_pass_change', 'ad_users', 'created', 'modified', 'modified_by'], 'required'],
        	[['usr_type', 'usr_username','role_id','usr_pass'], 'required'],
            [['location_id', 'role_id', 'usr_inherent_cases', 'usr_inherent_teams', 'status', 'change_pass_after', 'modified_by'], 'integer'],
        	[['usr_username'], 'unique', 'filter' => function($value) {
        		if(isset($this->id) && $this->id > 0) {
        			$value->where("LOWER(usr_username) ='".strtolower($this->usr_username)."' AND id NOT IN (".$this->id.")");
        		} else {
        			$value->where("LOWER(usr_username) ='".strtolower($this->usr_username)."'");
        		}
        	}],
        	[['change_pass_after'],'required','when'=>function($model){ return $model->usr_type != '3';}],
        	[['usr_first_name'],'required','when'=>function($model){ return $model->usr_type != '3';}],
			[['usr_lastname'],'required','when'=>function($model){ return $model->usr_type != '3';}],
			[['usr_email'],'required','when'=>function($model){ return $model->usr_type != '3';}],
        	[['usr_pass'],'required','when'=>function($model){ return $model->usr_type != '3';}],
        	[['confirm_password'],'required','when'=>function($model){ return $model->usr_type != '3';}],
            [['last_pass_change', 'created', 'modified','role_name'], 'safe'],
            //[['usr_type'], 'string', 'max' => 1],
            [['usr_email', 'usr_username', 'usr_mi'], 'string'],
            [['usr_email'], 'email'],
            [['usr_pass'], 'string'],
            [['usr_first_name', 'usr_lastname'], 'string'],
            [['ad_users'], 'string'],
            [['confirm_password'], 'compare', 'compareAttribute'=>'usr_pass'],
            [['usr_pass'], 'compare', 'compareAttribute' => 'old_password', 'operator' => '!='],
            [['ad_uid'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'location_id' => Yii::t('app', 'Location'),
            'usr_type' => Yii::t('app', 'User Type'),
            'usr_email' => Yii::t('app', 'User Email'),
            'usr_pass' => Yii::t('app', 'Password'),
            'usr_first_name' => Yii::t('app', 'First Name'),
            'usr_lastname' => Yii::t('app', 'Last Name'),
            'usr_username' => Yii::t('app', 'User Name'),
            'usr_mi' => Yii::t('app', 'User Mi'),
            'role_id' => Yii::t('app', 'Role'),
            'usr_inherent_cases' => Yii::t('app', 'Inherent Cases'),
            'usr_inherent_teams' => Yii::t('app', 'Inherent Teams'),
            'status' => Yii::t('app', 'Status'),
            'change_pass_after' => Yii::t('app', 'Force Password Change'),
            'last_pass_change' => Yii::t('app', 'Last Pass Change'),
            'ad_users' => Yii::t('app', 'Ad Users'),
            'ad_uid' => Yii::t('app', 'Ad Uid'),
            'created' => Yii::t('app', 'Created'),
          //  'created_by' => Yii::t('app', 'Created By'),
            'modified' => Yii::t('app', 'Modified'),
            'modified_by' => Yii::t('app', 'Modified By'),
        	'role_name'=> Yii::t('app', 'Role Name'),
        	'old_password'=> Yii::t('app', 'Current Password'),
        ];
    }
    /* Getter for role name */
    public function getRoleName() {
    	return $this->role->role_name;
    }

    /**
     * Get All Security Acces by user_id
     * */
    public function getAllSecurityAccess($user_id){
    	$Rolesecurity=array();
    	$roleId     =  Yii::$app->user->identity->role_id;
    	$userId		=  Yii::$app->user->identity->id;

     	if($roleId=='0'){ //if super user all access
    		return array('all'=>1);
    	}
    	$sql = "SELECT tbl_security_feature.feature_sort,tbl_role_security.security_force FROM tbl_role_security  inner join tbl_security_feature on tbl_security_feature.id=tbl_role_security.security_feature_id WHERE ((role_id=$roleId and user_id=0) OR (role_id=0 and user_id=$userId)) order by user_id desc";

    	$Rolesecurity_data=\Yii::$app->db->createCommand($sql)->queryAll();
    	if(!empty($Rolesecurity_data)){
    		foreach($Rolesecurity_data as $rolesec_data){
    			$feature_sort=(float)$rolesec_data['feature_sort'];
    			$Rolesecurity['feature_sort'][]=$feature_sort;
    			$Rolesecurity['security_force'][]=$rolesec_data['security_force'];
    		}
    	}
    	return $Rolesecurity;
    }

     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilterTeamAssigned($params, $team_id)
    {
		if ($team_id != 1) {
    		$sqlselect = "tbl_user.id IN (SELECT tbl_project_security.user_id FROM tbl_project_security WHERE (tbl_project_security.team_id=".$team_id.") AND (tbl_project_security.team_id!=0)) AND tbl_user.usr_type NOT IN (0)";
    	} else {
    		$sqlselect = "tbl_user.id IN (SELECT tbl_user.id FROM tbl_user LEFT OUTER
			 JOIN tbl_role ON (tbl_user.role_id=tbl_role.id)  WHERE (tbl_user.id!=0 And tbl_user.role_id!=0 and
			 tbl_role.role_type like '%1%') GROUP BY tbl_user.id) AND tbl_user.usr_type NOT IN (0)";
    	}
    	$query = $this::find()->joinWith('role')->where($sqlselect)->orderBy(['usr_username'=>SORT_ASC]);

    	if($params['field']=='usr_username'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andFilterWhere(['like', "CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $params['q']]);
			}
			$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue){
				return $model->usr_first_name." ".$model->usr_lastname;
			},function($model, $defaultValue){
				return $model->usr_first_name." ".$model->usr_lastname;
			});
		}

		if($params['field']=='role_name'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andFilterWhere(['like', 'role_name', $params['q']]);
			}
			$dataProvider = ArrayHelper::map($query->all(), 'role.role_name', 'role.role_name');
		}

		return array('All'=>'All')+$dataProvider;

    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchTeamAssigned($params, $team_id)
    {
		if ($team_id != 1) {
    		$sqlselect = "tbl_user.id IN (SELECT tbl_project_security.user_id FROM tbl_project_security WHERE (tbl_project_security.team_id=".$team_id.") AND (tbl_project_security.team_id!=0)) AND tbl_user.usr_type NOT IN (0)";
    	} else {
    		$sqlselect = "tbl_user.id IN (SELECT tbl_user.id FROM tbl_user LEFT OUTER
				 JOIN tbl_role ON (tbl_user.role_id=tbl_role.id)  WHERE (tbl_user.id!=0 And tbl_user.role_id!=0 and
				 tbl_role.role_type like '%1%') GROUP BY tbl_user.id) AND tbl_user.usr_type NOT IN (0)";
    		// $criteria->condition="user.id!=0 And user.role_id!=-1 AND user.id IN (".implode(',',$user_ids).")";
    	}
    	$query = $this::find()->joinWith('role')->where($sqlselect)->orderBy(['role_name' => SORT_ASC, 'usr_lastname'=>SORT_ASC]); // ->orderBy(['usr_username'=>SORT_ASC]);

    	$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    		'pagination'=>['pageSize'=>8]
    	]);

    	if ($params['User']['usr_username'] != null && is_array($params['User']['usr_username'])) {
			if(!empty($params['User']['usr_username'])) {
				foreach($params['User']['usr_username'] as $k=>$v) {
					if($v=='All') { //  || strpos($v,",") !== false
						unset($params['User']['usr_username']); break;
					}
				}
			}
			$query->andFilterWhere(['or like', "CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $params['User']['usr_username']]);
		}

		if ($params['User']['role_name'] != null && is_array($params['User']['role_name'])) {
			if(!empty($params['User']['role_name'])) {
				foreach($params['User']['role_name'] as $k=>$v) {
					if($v=='All') { //  || strpos($v,",") !== false
						unset($params['User']['role_name']); break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'tbl_role.role_name', $params['User']['role_name']]);
		}

    	$this->load($params);
    	$dataProvider->setSort([
			'attributes' => [
				'usr_lastname',
				'role_name' => [
					'asc' => ['tbl_role.role_name' => SORT_ASC],
					'desc' => ['tbl_role.role_name' => SORT_DESC],
					'label' => 'Role Name'
				]
			]
    	]);
    	//$query->andFilterWhere(['like',"CONCAT(usr_first_name,' ',usr_lastname)" ,$this->usr_username]);
    	//$query->andFilterWhere(['like', 'tbl_role.role_name', $this->role_name]);
    	return $dataProvider;
    }

    /**
     * Creates data provider client assigned Filter query
     * @params
     * @return
     */
     public function searchFilterClientAssigned($params)
     {
		$sqlselect = "tbl_user.id IN (
    		SELECT tbl_project_security.user_id
    		FROM tbl_project_security
    		WHERE (tbl_project_security.client_id=".$params['client_id'].") AND (tbl_project_security.user_id!=0) AND (tbl_project_security.team_id=0)
    	) AND tbl_user.role_id!=0";
    	$query = $this::find()->joinWith('role')->where($sqlselect)->orderBy(['role_name' => SORT_ASC,'usr_username'=>SORT_ASC]);

    	if($params['field']=='usr_username'){
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'usr_username', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue){
				return $model->usr_username;
			},function($model, $defaultValue){
				return $model->usr_username;
			});
    	}
    	if($params['field']=='role_name'){
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'role_name', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
				return $model->role->role_name;
			},function($model, $defaultValue){
				return $model->role->role_name;
			});
    	}
    	return $dataProvider;
	 }

	/**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchClientAssigned($params){

    	$sqlselect = "tbl_user.id IN (
    		SELECT tbl_project_security.user_id
    		FROM tbl_project_security
    		WHERE (tbl_project_security.client_id=".$params['client_id'].") AND (tbl_project_security.user_id!=0) AND (tbl_project_security.team_id=0)
    	) AND tbl_user.role_id!=0";

    	$query = $this::find()->joinWith('role')->where($sqlselect)->orderBy(['role_name' => SORT_ASC, 'usr_username' => SORT_ASC]); //->orderBy(['usr_username'=>SORT_ASC]);

    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination'=>['pageSize'=>8]
    	]);

    	if ($params['User']['usr_username'] != null && is_array($params['User']['usr_username'])) {
			if(!empty($params['User']['usr_username'])){
				foreach($params['User']['usr_username'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['User']['usr_username']);break;
					}
				}
			}
			$query->andFilterWhere(['usr_username' => $params['User']['usr_username']]);
		}

		if ($params['User']['role_name'] != null && is_array($params['User']['role_name'])) {
			if(!empty($params['User']['role_name'])){
				foreach($params['User']['role_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['User']['role_name']);break;
					}
				}
			}
			$query->andFilterWhere(['role_name' => $params['User']['role_name']]);
		}

    	$this->load($params);
    	$dataProvider->setSort([
    			'attributes' => [
    					'usr_username',
    					'role_name' => [
    							'asc' => ['tbl_role.role_name' => SORT_ASC],
    							'desc' => ['tbl_role.role_name' => SORT_DESC],
    							'label' => 'Role Name'
    					]
    			]
    	]);

    	//$query->andFilterWhere(['usr_username' => $this->usr_username]);
    	//$query->andFilterWhere(['like', 'tbl_role.role_name', $this->role_name]);
    	return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilterClientCaseAssigned($params){

    	$sqlselect = "tbl_user.id IN (
    		SELECT tbl_project_security.user_id
    		FROM tbl_project_security
    		WHERE (tbl_project_security.client_id=".$params['client_id']." AND tbl_project_security.client_case_id=".$params['client_case_id'].") AND (tbl_project_security.user_id!=0) AND (tbl_project_security.team_id=0)
    	) AND tbl_user.role_id!=0";

    	$query = $this::find()->joinWith('role')->where($sqlselect)->orderBy(['role_name' => SORT_ASC, 'usr_username' => SORT_ASC]); //->orderBy(['usr_username'=>SORT_ASC]);

    	if($params['field']=='usr_username'){
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'usr_username', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue){
				return $model->usr_username;
			},function($model, $defaultValue){
				return $model->usr_username;
			});
    	}
    	if($params['field']=='role_name'){
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'role_name', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
				return $model->role->role_name;
			},function($model, $defaultValue){
				return $model->role->role_name;
			});
    	}

    	return $dataProvider;
    }

	/**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchClientCaseAssigned($params){

    	$sqlselect = "tbl_user.id IN (
    		SELECT tbl_project_security.user_id
    		FROM tbl_project_security
    		WHERE (tbl_project_security.client_id=".$params['client_id']." AND tbl_project_security.client_case_id=".$params['client_case_id'].") AND (tbl_project_security.user_id!=0) AND (tbl_project_security.team_id=0)
    	) AND tbl_user.role_id!=0";

    	$query = $this::find()->joinWith('role')->where($sqlselect)->orderBy(['role_name' => SORT_ASC,'usr_username'=>SORT_ASC]);

    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination'=>['pageSize'=>8]
    	]);

    	$this->load($params);
    	$dataProvider->setSort([
    			'attributes' => [
    					'usr_username',
    					'role_name' => [
    							'asc' => ['tbl_role.role_name' => SORT_ASC],
    							'desc' => ['tbl_role.role_name' => SORT_DESC],
    							'label' => 'Role Name'
    					]
    			]
    	]);
    	if ($params['User']['usr_username'] != null && is_array($params['User']['usr_username'])) {
			if(!empty($params['User']['usr_username'])){
				foreach($params['User']['usr_username'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['User']['usr_username']);break;
					}
				}
			}
			$query->andFilterWhere(['usr_username' => $params['User']['usr_username']]);
		}

		if ($params['User']['role_name'] != null && is_array($params['User']['role_name'])) {
			if(!empty($params['User']['role_name'])){
				foreach($params['User']['role_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['User']['role_name']);break;
					}
				}
			}
			$query->andFilterWhere(['role_name' => $params['User']['role_name']]);
		}
    	// $query->andFilterWhere(['usr_username' => $this->usr_username]);
    	// $query->andFilterWhere(['like', 'tbl_role.role_name', $this->role_name]);
    	return $dataProvider;
    }

    /**
     * Add AD Users
     * */
    public function addApUser($post_data,$model){
    	$res = false;
    	/*Code writen make assign AD user Attributes to DB table attributes*/
    	if(isset($post_data['User']['ad_users']) && $post_data['User']['ad_users']!="")
    	{
    		$directadd=true;
    		$ldap_uid=$post_data['User']['ad_users'];
    		$ldap_data=(new Settings)->ldapSearchByUser($ldap_uid);
			//echo "<pre>",print_r($ldap_data),"</pre>";die;
			//$ldap_data=json_encode($ldap_data);
			$ldap_uid_num=0;
			if(is_array($ldap_data)){
				foreach($ldap_data as $ldap_k=>$ldap){
					if(trim($ldap['samaccountname']) == $ldap_uid){
					$ldap_uid_num=$ldap_k;
					$ldapuser_info=$ldap;
					break;
					}
				}
			}
            //$ldapuser_info=$ldap_data;
    		//echo "<pre>",print_r($ldapuser_info),"</pre>";die;
    		$model->usr_type='3';
    		$model->ad_uid='0';//$ldap_uid_num;
    		$model->usr_username=$ldapuser_info['dn'];
    		if(isset($ldapuser_info['samaccountname']) && $ldapuser_info['samaccountname']!=""){
    			$model->usr_username=$ldapuser_info['samaccountname'];
    		}
    		$model->usr_first_name=$ldapuser_info['givenname'];
    		$model->usr_lastname=$ldapuser_info['sn'];
    		$model->usr_email=((isset($ldapuser_info['mail']) && $ldapuser_info['mail']!="")?$ldapuser_info['mail']:'');
    		$model->usr_mi=substr($ldapuser_info['givenname'], 0, 1).substr($ldapuser_info['sn'], 0, 1);
    		if(is_array($ldapuser_info)){
				$model->ad_users=json_encode($ldapuser_info);
			}else{
				$model->ad_users=$ldapuser_info;
			}
    		$model->role_id=$post_data['User']['role_id'];
    		$model->location_id=$post_data['User']['location_id'];
    		$model->last_pass_change =date('Y-m-d H:i:s');
    		$model->change_pass_after=2147483647;
    		$rand = substr(md5(microtime()),rand(0,26),5);
    		$model->usr_pass = $rand;
    		$model->confirm_password =$rand;
    		$model->status = 1;

			//echo "<pre>",print_r($model->getAttributes()),"</prE>";die;
    		if($model->save(false)){
    				$user_id = Yii::$app->db->getLastInsertId();
    				$my_teams = $post_data['my_teams'];
	    			$my_cases = $post_data['my_cases'];
	    			$clients  = $post_data['clients'];
	    			if(!empty($my_teams)){
	    				(new ProjectSecurity)->updateTeamSecuritywithLoc($my_teams, $user_id);
	    			}
	    			if(!empty($my_cases)){
	    				(new ProjectSecurity)->updateCaseSecurity($my_cases, $user_id, $clients);
	    			}
    			/* Active log entry */
    			$activityLog = new ActivityLog();
    			$activityLog->generateLog('User','Added', $user_id, $model->usr_username);
    			$res=true;

				// model options
				$modelSettings = Settings::find()->where(['field'=>'session_timeout'])->one();
				$modelOptions = new Options();
				$modelOptions->user_id = $user_id;
				$modelOptions->session_timeout = NULL;
				$modelOptions->timezone_id = 'America/New_York';
				$modelOptions->default_landing_page = '';
				if($modelSettings->fieldvalue == 1 || strtolower(trim($modelSettings->fieldvalue)) == 'default') {
	    			$modelOptions->session_timeout = '1200';
	    		}
				$modelOptions->save(false);
    		}else{
				echo "<pre>",print_r($model->getErrors()),"</pre>";
			}
    	}
    	return $res;
    }

    /**
     * add Ad users
     * */
    public function addGroupApUser($post_data,$model)
    {
		$res = false;
		if(!is_array($post_data['grp_users'])){
			$post_data['grp_users']=json_decode($post_data['grp_users'],true);
		}
		if(!empty($post_data['grp_users'])){
	    		foreach($post_data['grp_users'] as $ad_users){
	    			$directadd=true;
	    			$ldap_uid=$ad_users;
	    			//$ldap_data=(new Settings)->ldapsearch();
	    			//$ldapuser_info=json_encode($ldap_data[$ldap_uid]);
					$ldap_data=(new Settings)->ldapSearchByUser($ldap_uid);
					//$ldap_data=json_encode($ldap_data);
					$ldap_uid_num=0;
					if(is_array($ldap_data)){
						foreach($ldap_data as $ldap_k=>$ldap){
							if(trim($ldap['samaccountname']) == $ldap_uid){
							$ldap_uid_num=$ldap_k;
							$ldapuser_info=$ldap;
							break;
							}
						}
					}
					//$ldapuser_info=$ldap_data;
					//echo "<pre>",print_r($ldapuser_info),"</pre>";die;
					$model = new User();
					$model->usr_type='3';
					$model->ad_uid='0';//$ldap_uid_num;
					$model->usr_username=$ldapuser_info['dn'];
					if(isset($ldapuser_info['samaccountname']) && $ldapuser_info['samaccountname']!=""){
						$model->usr_username=$ldapuser_info['samaccountname'];
					}
					$model->usr_first_name=$ldapuser_info['givenname'];
					$model->usr_lastname=$ldapuser_info['sn'];
					$model->usr_email=((isset($ldapuser_info['mail']) && $ldapuser_info['mail']!="")?$ldapuser_info['mail']:'');
					$model->usr_mi=substr($ldapuser_info['givenname'], 0, 1).substr($ldapuser_info['sn'], 0, 1);
					if(is_array($ldapuser_info)){
						$model->ad_users=json_encode($ldapuser_info);
					}else{
						$model->ad_users=$ldapuser_info;
					}
					$model->role_id=$post_data['User']['role_id'];
					$model->location_id=$post_data['User']['location_id'];
					$model->last_pass_change =date('Y-m-d H:i:s');
					$model->change_pass_after=2147483647;
					$rand = substr(md5(microtime()),rand(0,26),5);
					$model->usr_pass = $rand;
					$model->confirm_password =$rand;
					$model->status = 1;
					/*if(is_array($ldap_data)){
						foreach($ldap_data as $ldap_k=>$ldap){
							if(trim($ldap['cn']) == $ldap_uid){
							$ldapuser_info=$ldap;
							break;
							}
						}
					}
	    			$model = new User();
	    			$model->usr_type='3';
	    			$model->ad_uid=$ldap_uid;
	    			$model->usr_username=$ldap_data[$ldap_uid]['uid'];
	    			if(isset($ldap_data[$ldap_uid]['samaccountname']) && $ldap_data[$ldap_uid]['samaccountname']!=""){
	    				$model->usr_username=$ldap_data[$ldap_uid]['samaccountname'];
	    			}
	    			$model->usr_first_name=$ldap_data[$ldap_uid]['givenname'];
	    			$model->usr_lastname=$ldap_data[$ldap_uid]['sn'];
	    			$model->usr_email=((isset($ldap_data[$ldap_uid]['mail']) && $ldap_data[$ldap_uid]['mail']!="")?$ldap_data[$ldap_uid]['mail']:'');
	    			$model->usr_mi=substr($ldap_data[$ldap_uid]['givenname'], 0, 1).substr($ldap_data[$ldap_uid]['sn'], 0, 1);
	    			$model->ad_users=$ldapuser_info;
	    			$model->role_id=$post_data['User']['role_id'];
	    			$model->location_id=$post_data['User']['location_id'];
	    			$model->last_pass_change =date('Y-m-d H:i:s');
	    			$model->change_pass_after=2147483647;
	    			$rand = substr(md5(microtime()),rand(0,26),5);
	    			$model->usr_pass = $rand;
	    			$model->confirm_password =$rand;
	    			$model->status = 1;*/
	    			$model->created = date('Y-m-d H:i:s');
	    			$model->modified = date('Y-m-d H:i:s');
	    			$model->created_by=0;
	    			$model->modified_by=0;
	    			$usr_inhrent_cases = Yii::$app->request->post('usr_inherent_cases');
					$usr_inhrent_teams = Yii::$app->request->post('usr_inherent_teams');
					$model->usr_inherent_cases = isset($usr_inhrent_cases)?$usr_inhrent_cases:0;
					$model->usr_inherent_teams = isset($usr_inhrent_teams)?$usr_inhrent_teams:0;
					if($model->save()){
						$user_id = Yii::$app->db->getLastInsertId();
						$my_teams = $post_data['my_teams'];
						$my_cases = $post_data['my_cases'];
						$clients  = $post_data['clients'];
						if(!empty($my_teams)){
							(new ProjectSecurity)->updateTeamSecuritywithLoc($my_teams, $user_id);
						}
						if(!empty($my_cases)){
							(new ProjectSecurity)->updateCaseSecurity($my_cases, $user_id, $clients);
						}
						/* Active log entry */
						$activityLog = new ActivityLog();
						$activityLog->generateLog('User','Added', $user_id, $model->usr_username);
						$res=true;
						// model options
						$modelSettings = Settings::find()->where(['field'=>'session_timeout'])->one();
						$modelOptions = new Options();
						$modelOptions->user_id = $user_id;
						$modelOptions->session_timeout = NULL;
						$modelOptions->timezone_id = 'America/New_York';
						$modelOptions->default_landing_page = '';
						if($modelSettings->fieldvalue == 1 || strtolower(trim($modelSettings->fieldvalue)) == 'default') {
							$modelOptions->session_timeout = '1200';
						}
						$modelOptions->save(false);
					} else {
						echo "<pre>",print_r($model->getErrors());die;
					}
	    	}
    	}
    	return $res;
    }
    /**
     * Function For Get User Name From Id
     * */
	public function	getusernamefromid($user_id){
		$user_fullname = User::find()->select(["CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where('tbl_user.id = '.$user_id)->one();
		return $user_fullname->full_name;
	}

	/**
	 * Function For Get User Name From Id
	 * */
	public function	getusernamefromunitid($unit_id){
	/*	$user_fullname = User::find()->select(["CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where('tbl_user.id = '.$user_id)->one();
		return $user_fullname->full_name; */

	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivityLogs()
    {
        return $this->hasMany(ActivityLog::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsReads()
    {
        return $this->hasMany(CommentsRead::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceTransactions()
    {
        return $this->hasMany(EvidenceTransactions::className(), ['trans_requested_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceTransactions0()
    {
        return $this->hasMany(EvidenceTransactions::className(), ['trans_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(Options::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSecurities()
    {
        return $this->hasMany(ProjectSecurity::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSavedFilters()
    {
        return $this->hasMany(SavedFilters::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLogs()
    {
        return $this->hasMany(UserLog::className(), ['user_id' => 'id']);
    }


    /** INCLUDE USER LOGIN VALIDATION FUNCTIONS**/
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
		if(!isset($_SESSION['identity_data']))
			$_SESSION['identity_data']=static::findOne($id);

    	return $_SESSION['identity_data'];
		//static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    /* modified */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    	return static::findOne(['access_token' => $token]);
    }

    /* removed
     public static function findIdentityByAccessToken($token)
     {
     throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
     }
     */
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsrUsername($username)
    {
    	return static::findOne(['usr_username' => $username,'status'=>1]);
    }


    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
    	$expire = \Yii::$app->params['user.passwordResetTokenExpire'];
    	$parts = explode('_', $token);
    	$timestamp = (int) end($parts);
    	if ($timestamp + $expire < time()) {
    		// token expired
    		return null;
    	}

    	return static::findOne([
    			'password_reset_token' => $token
    	]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
    	return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
    	return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
    	return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	//return $this->password === sha1($password);
    	return $this->hashPassword($password)===$this->usr_pass;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
    	$this->password_hash = Security::generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
    	$this->auth_key = Security::generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
    	$this->password_reset_token = Security::generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
    	$this->password_reset_token = null;
    }
    /** EXTENSION MOVIE **/

    /**
     *
     * @abstract This function is define for Encryption of password
     * @param string $password
     * @access Public
     */
    public function hashPassword($password)
    {
    	$key=$this->key;
    	$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $password, MCRYPT_MODE_CBC, md5(md5($key))));
    	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");

    	return $encrypted;
    }
    /**
     *
     * @abstract This function is define for a Decryption of password
     * @param string $encryptedPassword
     * @access Public
     */
    public function decryptPassword($encryptedPassword){
    	$key=$this->key;
    	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encryptedPassword), MCRYPT_MODE_CBC, md5(md5($key))), "\0");

    	return $decrypted;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if ($this->isNewRecord){
    			$this->last_pass_change=date('Y-m-d H:i:s');
    			$this->created=date('Y-m-d H:i:s');
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified=date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;

    			if(!isset($this->usr_inherent_cases))
    				$this->usr_inherent_cases=0;
	    		if(!isset($this->usr_inherent_teams))
	    			$this->usr_inherent_teams=0;
	    		if(!isset($this->ad_users))
	    			$this->ad_users='';
	    		if(!isset($this->ad_uid))
	    			$this->ad_uid=0;
	    		if(!isset($this->usr_mi))
	    			$this->usr_mi='';
	    		if(!isset($this->usr_email))
	    			$this->usr_email="";
	    		if(!isset($this->usr_pass))
	    			$this->usr_pass="";
	    		if(!isset($this->usr_first_name))
	    			$this->usr_first_name="";
	    		if(!isset($this->usr_lastname))
	    			$this->usr_lastname="";
    		} else {
    			$this->modified=date('Y-m-d H:i:s');
    			if(isset($this->modified_by) && $this->modified_by!=0){}else{
    			$this->modified_by=Yii::$app->user->identity->id;
				}
    		}
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * Add security features
     */
    public function updateUserSecurityFeatures($userId,$roleId,$secuirtyFeatures){
    	foreach ($secuirtyFeatures as $feature){
      		$userSecurityFeature = new RoleSecurity(); // Rolesecurity insated of Usersecurity due to combination of role and user in role security table
    		$userSecurityFeature->role_id = $roleId;
    		$userSecurityFeature->user_id = $userId;
			if(isset($_POST['security_force'][$feature]))
		    	$security_force = 1;
			else
		    	$security_force = 0;

    		$userSecurityFeature->security_force = $security_force;
    		$userSecurityFeature->security_feature_id = $feature;
    		$userSecurityFeature->isNewRecord = true;
    		$userSecurityFeature->save();
    	}
    	return true;
    }

    public function getSecurityListuserwise($uid)
	{
		$userId=$uid;

		$roleId               = Yii::$app->user->identity->role_id;

		$clientAddSecurities= ArrayHelper::map(
		ProjectSecurity::find()->select(['client_id'])->where("user_id = ".$userId)->groupBy('client_id')->all(),
		'client_id',
		'client_id');

		$caseAddSecurities= ArrayHelper::map(
		ProjectSecurity::find()->select(['client_case_id'])->where("user_id = ".$userId)->groupBy('client_case_id')->all(),
		'client_case_id',
		'client_case_id');


		$teamSecurities= ArrayHelper::map(
		ProjectSecurity::find()->select(['team_id'])->where("user_id = ".$userId)->groupBy(['team_id'])->all(),
		'team_id',
		'team_id');


		$userAddSecurities=array();

		$userRoleSecurities= ArrayHelper::map(
		Rolesecurity::find()->select(['id','security_feature_id'])->where("(role_id = ".$roleId." AND user_id = 0) OR (role_id = 0 AND user_id = ".$userId.")")->orderBy('security_feature_id ASC')->all(),
		'id',
		'security_feature_id');

		$combineArray2=array_merge($userAddSecurities,$userRoleSecurities);
		$combineArray2=array_unique($combineArray2);


		$combineArray['client']=array_unique($clientAddSecurities);
		$combineArray['case']=array_unique($caseAddSecurities);
		$combineArray['team']=array_unique($teamSecurities);
		$combineArray['user_access']=$combineArray2;

		$str_condition="";
		$team_task_id=array();
		$client_case_task_id=array();
		$myteamdataall=array();
		$getallservices=array();
		if(!empty($combineArray['team']))
		{
			$teams_ids=array();

			$implode_data = implode(',',$combineArray['team']);

				$myteamdata = ArrayHelper::map(TasksTeams::find()->joinWith('tasks', false, 'INNER JOIN')->select(['tbl_tasks_teams.task_id'])->where('tbl_tasks_teams.team_id IN ('.$implode_data.') AND tbl_tasks.task_status IN(0,1,3,4)')->all(),'task_id','task_id');
				/*echo $implode_data;
				echo "<pre>"; print_r($myteamdata); exit;*/

				if(!empty($myteamdata))
				$myteamdataall=array_merge($myteamdataall,$myteamdata);

			$teams_ids = array_values($combineArray['team']);
			$implode_team_ids = implode(',',$teams_ids);
			if(!empty($teams_ids)){

			$team_services = Teamservice::find()->select(['id'])->where('teamid IN('.$implode_team_ids.')')->all();
				foreach ($team_services as $teamser)
					array_push($getallservices,$teamser->id);
			}
		}
		$combineArray['team_allservices']=$getallservices;
		$combineArray['team_task']=array_unique(array_values($myteamdataall));
		$mycasedataall=array();
		$getallcase_services=array();
		$getallcasetask_services=array();
		if(!empty($combineArray['client'])){
				$implode_client = implode(',',$combineArray['case']);
				$mycasedata = ArrayHelper::map(Tasks::find()->where('tbl_tasks.task_status IN(0,1,3,4) AND client_case_id IN ('.$implode_client.')')->all(),'id','id');
				if(!empty($mycasedata))
				$mycasedataall=array_merge($mycasedataall,$mycasedata);

		}
		$combineArray['client_case_task_allservices']=$getallcasetask_services;
		$combineArray['client_case_task']=$mycasedataall;
		$combineArray['all_task']=array_merge($mycasedataall,$myteamdataall);
		return $combineArray;
	}

	/*This function is used to check access based on security feature sort*/
	public function checkAccess($id)
	{
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		if($roleId=='0'){ //if super user all access
			return true;
		}
		$session = new Session;
		$session->open();
		$myaccess = $session['myaccess'];
		if($id=='4.082'){
		//echo "$id : <pre>",print_r($myaccess['feature_sort']),"</pre>";
		//print_r($session['myaccess']);
		//die;
		}
		if(!empty($myaccess['feature_sort'])){
			if(array_search($id,$myaccess['feature_sort']) !== false)
			    return true;
			else
			    return false;
		}else{
		    return false;
		}
	}



	/* This function is used to check access based on Client Case id */
	public function checkCaseAccess($case_id)
	{
		if($case_id==null)$case_id=0;
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		if($roleId=='0') //if super user all access
			return  true;

		$sql = "SELECT count(*) as cnt FROM tbl_project_security WHERE user_id=".$userId." AND client_case_id =".$case_id;
		$Rolesecurity = \Yii::$app->db->createCommand($sql)->queryAll();

		if($Rolesecurity[0]['cnt'] > 0)
			return true;
		else
			return false;

		return false;

	}
	public function checkTeamAccess($team_id,$team_location)
	{
		if($team_id==null)$team_id=0;
		if($team_location==null)$team_location=0;

		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		if($roleId=='0') //if super user all access
			return  true;

		$sql = "SELECT count(*) as cnt FROM tbl_project_security WHERE user_id=".$userId." AND team_id =".$team_id;
                if(isset($team_location) && $team_location!="" && $team_location!=0)
                {
                    $sql .=" AND team_loc=".$team_location;
                }

        $Rolesecurity = \Yii::$app->db->createCommand($sql)->queryOne();

		if($Rolesecurity['cnt'] > 0)
			return true;


		return false;

	}
	/** IRT 823
	If Form field data entry is a dropdown, radio or checkbox selection, then the filter type should be "Dropdown"
	If Form field data entry is a Text or MultiText field then the filter type should be "Enter Text" and this includes any text in the field not just the first word in the field
	If Form field data entry is a date picker then the filter type should be "Date Picker"
	If Form field data entry is a auto increment unique number (project or media #, etc) then the filter type should be "Enter one or more"
	If field is a calculated number field (% complete), then the filter type should be "Number Range" (Actually, not sure what this is doing as this field is inconsistent in my cases and my teams. Seems like just a Enter Text)
	*/
	public static function getFilterTypeByData($field,$table) {


	}
	/** IRT 67,68,86,87,258_v2
	 * make filter type dynamic based on table and field.
	 * @params Field MIX [Field can be array of names of fields or single field name]
	 * @params Table varchar [Table will be table name for which we run grid]
	 **/
	public static function getFilterType($field,$table) {
		$range_filter=array('pricing_rate'=>'pricing_rate', 'per_complete'=>'per_complete', 'team_per_complete'=>'team_per_complete', 'totalinvoiceamt'=>'totalinvoiceamt');
		$alas_filter = array('custodians_media'=>'custodians_media', 'custodians_project'=>'custodians_project', 'custodians_form'=>'custodians_form');
		/*823*/
		$enter_text_field= array(
			'tbl_evidence.quantity',
			'tbl_evidence.contents_total_size',
			'tbl_evidence.contents_total_size_comp'
		);
		$select_field=array(
		'tbl_invoice_batch.display_invoice'=>'tbl_invoice_batch.display_invoice',
		'tbl_teamservice.hastasks'=>'tbl_teamservice.hastasks',
		"tbl_user.usr_username"=>"tbl_user.usr_username",
		"tbl_role.role_name"=>"tbl_role.role_name",
		"tbl_case_type.case_type_name"=>"tbl_case_type.case_type_name",
		"tbl_teamservice.service_name"=>"tbl_teamservice.service_name",
		"tbl_servicetask.service_task"=>"tbl_servicetask.service_task",
		"tbl_unit.default_unit"=>"tbl_unit.default_unit",
		"tbl_client_contacts.contact_type"=>"tbl_client_contacts.contact_type",
		'tbl_tasks_units_todos.todo'=>'tbl_tasks_units_todos.todo',
		'tbl_todo_cats.todo_cat'=>'tbl_todo_cats.todo_cat'
		);
		if(is_array($table)){
			if(in_array('tbl_tax_class',$table)){
				$select_field['tbl_pricing.price_point']='tbl_pricing.price_point';
			}
			if(in_array('tbl_evidence_type',$table)){
				$select_field['tbl_unit.unit_name']='tbl_unit.unit_name';
			}
		}else{
			if('tbl_tax_class'==$table){
				$select_field['tbl_pricing.price_point']='tbl_pricing.price_point';
			}
			if('tbl_evidence_type'==$table){
				$select_field['tbl_unit.unit_name']='tbl_unit.unit_name';
			}
		}
		/*823*/
		$filter_datas=array();
		if(is_array($table)) {
			$field_condition = " CONCAT(tbl_reports_tables.table_name,'.',tbl_reports_fields.field_name) IN ('" . implode("','", $field) . "')";
			$table = "'" . implode("','", $table) . "'";
			$table_condition="table_name IN ($table)";
			$sql="SELECT field_name, length_value, reports_field_type_id, field_type FROM tbl_reports_fields
			LEFT JOIN tbl_reports_field_type ON tbl_reports_fields.reports_field_type_id = tbl_reports_field_type.id
			LEFT JOIN tbl_reports_tables ON tbl_reports_fields.report_table_id = tbl_reports_tables.id
			WHERE report_table_id IN(select id from tbl_reports_tables where ".$table_condition.") AND ".$field_condition."";
			$field_type_data=Yii::$app->db->createCommand($sql)->queryAll();
			$field_type_list=array();
			if(!empty($field_type_data)) {
				foreach($field_type_data as $ftdata) {
					if(isset($ftdata['length_value']) && $ftdata['length_value']!="") {
						$field_type_list[$ftdata['field_name']]=$ftdata['field_type']."(".$ftdata['length_value'].")";
					} else {
						$field_type_list[$ftdata['field_name']]=$ftdata['field_type'];
					}
				}
			}
			//echo "<pre>",print_r($field_type_list);die;
			//$field_type_list = ArrayHelper::map(ReportsFields::find()->select(['field_nam','length_value','reports_field_type_id'])->joinWith(['reportsFieldType','reportsTables'])->where('report_table_id IN(select id from tbl_reports_tables where '.$table_condition.') AND '.$field_condition.'' )->all(),'field_name',function($model){if(isset($model->length_value) && $model->length_value!=""){return $model->reportsFieldType->field_type."(".$model->length_value.")";}else{return $model->reportsFieldType->field_type;}});

		}else{
			$table_condition="table_name ='".$table."'";
			$sql="SELECT field_name, length_value, reports_field_type_id, field_type FROM tbl_reports_fields
			LEFT JOIN tbl_reports_field_type ON tbl_reports_fields.reports_field_type_id = tbl_reports_field_type.id
			WHERE report_table_id IN(select id from tbl_reports_tables where ".$table_condition.")
			";
			$field_type_data=Yii::$app->db->createCommand($sql)->queryAll();
			$field_type_list=array();
			if(!empty($field_type_data)) {
				foreach($field_type_data as $ftdata) {
					if(isset($ftdata['length_value']) && $ftdata['length_value']!="") {
						$field_type_list[$ftdata['field_name']]=$ftdata['field_type']."(".$ftdata['length_value'].")";
					} else {
						$field_type_list[$ftdata['field_name']]=$ftdata['field_type'];
					}
				}
			}

			//$field_type_list = ArrayHelper::map(ReportsFields::find()->select(['field_name','length_value','reports_field_type_id'])->joinWith('reportsFieldType')->where('report_table_id IN(select id from tbl_reports_tables where '.$table_condition.')' )->all(),'field_name',function($model){if(isset($model->length_value) && $model->length_value!=""){return $model->reportsFieldType->field_type."(".$model->length_value.")";}else{return $model->reportsFieldType->field_type;}});
		}
		if(is_array($field)){
			if(!empty($field)){
				foreach($field as $field_val){
					$org_field_val=$field_val;
					if(strpos($field_val,".") !== false){
						$field_val=substr($field_val,(strpos($field_val,'.')+1),strlen($field_val));
					}
					if(isset($field_type_list[$field_val])) {

						if(strpos($field_type_list[$field_val],"(")!==false) {
							$data_type = substr($field_type_list[$field_val],0,strpos($field_type_list[$field_val],"("));
							$length    = preg_replace("/[^0-9]/","",substr($field_type_list[$field_val],strpos($field_type_list[$field_val],"("),strlen($field_type_list[$field_val])));
						}else{
							$data_type =$field_type_list[$field_val];
						}
						if(strtolower($data_type)=='datetime' || strtolower($data_type)=='date') {
							$filter_datas[$field_val]= GridView::FILTER_DATE_RANGE;
						}
						//else if((strtolower($data_type)=='int' || strtolower($data_type)=='bigint' || strtolower($data_type)=='tinyint' || strtolower($data_type)=='varchar') && $length <= 150  || strtolower($data_type)=='char') {
						else if((strtolower($data_type)=='int' || strtolower($data_type)=='bigint' || strtolower($data_type)=='tinyint')) {
							if(!in_array($org_field_val,$enter_text_field))
								$filter_datas[$field_val]=GridView::FILTER_SELECT2;
						}
						if(in_array($org_field_val,$select_field)){
							$filter_datas[$field_val]=GridView::FILTER_SELECT2;
						}
						/*else if(strtolower($data_type)=='text') {
							$filter_datas[$field_val]=GridView::FILTER_SELECT2;
						}else if(strtolower($data_type)=='varchar' && $length > 150) {
							$filter_datas[$field_val]=GridView::FILTER_SELECT2;
						}*/
					}
					if(isset($range_filter[$field_val])){
						$filter_datas[$field_val]=GridView::FILTER_RANGE;
					}
					if(isset($alas_filter[$field_val])){
						$filter_datas[$field_val]=GridView::FILTER_SELECT2;
					}
				}
			}
			//echo "<pre>",print_r($filter_datas),print_r($field),"</pre>";die;
			return $filter_datas;
		}else{
			if(isset($field_type_list[$field])){
				if(strpos($field_val,".") !== false){
					$field=substr($field,(strpos($field,'.')+1),strlen($field));
				}
				if(strpos($field_type_list[$field],"(")!==false){
					$data_type = substr($field_type_list[$field],0,strpos($field_type_list[$field],"("));
					$length    = preg_replace("/[^0-9]/","",substr($field_type_list[$field],strpos($field_type_list[$field],"("),strlen($field_type_list[$field])));
				} else {
					$data_type =$field_type_list[$field];
				}
				if(strtolower($data_type)=='datetime' || strtolower($data_type)=='date') {
					return GridView::FILTER_DATE_RANGE;
				} else if((strtolower($data_type)=='int' || strtolower($data_type)=='bigint' || strtolower($data_type)=='varchar') && $length <= 150) {
					return GridView::FILTER_SELECT2;
				} else if(strtolower($data_type)=='text') {
					return GridView::FILTER_TYPEAHEAD;
				} else if(strtolower($data_type)=='varchar' && $length > 150) {
					return GridView::FILTER_TYPEAHEAD;
				}
			}
			if(in_array($field,$range_filter)){
				return GridView::FILTER_RANGE;
			}
			if(in_array($field,$alas_filter)){
				return GridView::FILTER_SELECT2;
			}
		}
	}
	/** IRT 67,68,86,87,258_v2
	 * make filter Widget Option dynamic based Filertypes array
	 * @params fields Array [Fields array]
	 * @params ajaxurl varchar [ajaxurl will use to get data using ajax call on grid filter]
	 **/
	public static function getFilterWidgetOption($fields,$ajaxurl,$config=[],$config_widget_options=[],$params=[],$module=''){
		//echo "<pre>",print_r($fields);print_r($config_widget_options);
		$range_filter=array('pricing_rate'=>'pricing_rate', 'per_complete'=>'per_complete', 'team_per_complete'=>'team_per_complete', 'totalinvoiceamt'=>'totalinvoiceamt');
		$alas_filter = array('custodians_media'=>'custodians_media', 'custodians_project'=>'custodians_project', 'custodians_form'=>'custodians_form');
		$virtual_filter = array('team_status'=>'team_status');
		$filterWidgetOption=array();
		$types=['\kartik\select2\Select2'=>1,'\kartik\typeahead\Typeahead'=>2,'\kartik\daterange\DateRangePicker'=>3];
		if(!empty($fields)){
			foreach($fields as $field=>$type){
				if(isset($config_widget_options[$field]['url'])){
					$ajaxurl=$config_widget_options[$field]['url'];
				}
				if($types[$type]==1){ /* Select2 */
					if(isset($config[$field])){
						$filterWidgetOption[$field]=[

							 'initValueText'=>(isset($config_widget_options[$field]['initValueText']))?$config_widget_options[$field]['initValueText']:NULL,
							 'options'=>['multiple'=>true,'nolabel'=>true],
							 'showToggleAll' => false,
							 'data'=>$config[$field],
							 'pluginEvents'=>(isset($config_widget_options[$field]['pluginEvents']))?$config_widget_options[$field]['pluginEvents']:[],
							 'pluginOptions'=>[
									'allowClear' => true
							 ]
							];
					}else{
						//print_r($params);
						$filed_name=(isset($config_widget_options[$field]['field_alais']))?$config_widget_options[$field]['field_alais']:$field;
						$ajax_da_url=[
		  							'url' => $ajaxurl,
									'type' => 'GET',
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,field:"'.$filed_name.'"}; }')
		  						];
						if($module=='global_project' || $module=='team_project' || $module=='media'){
							$ajax_da_url=[
		  							'url' => $ajaxurl,
									'type' => 'POST',
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,params:'.stripslashes(json_encode($params)).',field:"'.$filed_name.'"}; }')
		  						];
						}
						if($field=='id' || $field=='task_id' || $field=='project_name'){ //|| $field=='servicetask_id'
							$filterWidgetOption[$field]=[
								'id'=>$field,
								'initValueText'=>(isset($config_widget_options[$field]['initValueText']))?$config_widget_options[$field]['initValueText']:NULL,
								'options'=>['multiple'=>true,'nolabel'=>true],
								'showToggleAll' => false,
								'pluginEvents'=>(isset($config_widget_options[$field]['pluginEvents']))?$config_widget_options[$field]['pluginEvents']:[],
								'pluginOptions'=>[
									'minimumInputLength' => 1,
									'allowClear'=>false,
                					'ajax' => $ajax_da_url,


								]
							];
						}else{
							$filterWidgetOption[$field]=[
								'initValueText'=>(isset($config_widget_options[$field]['initValueText']))?$config_widget_options[$field]['initValueText']:NULL,
								'options'=>['multiple'=>true,'nolabel'=>true],
								'showToggleAll' => false,
								'pluginEvents'=>(isset($config_widget_options[$field]['pluginEvents']))?$config_widget_options[$field]['pluginEvents']:[],
								'pluginOptions'=>[
									'ajax' => $ajax_da_url,
									'allowClear' => true
								]
							];
						}
						  /*'ajax' => [
		  							'url' =>$ajaxurl.'&field='.$filed_name,
									'type'=>'post',
		  							'dataType' => 'json',
		  							'data' => new JsExpression("function(params) { return $('.filters :input').serialize()+'&q='+params.term; }")

		  						]*/
				    }
				}else if($types[$type]==2){ /*Typeahead*/
					$filed_name=(isset($config_widget_options[$field]['field_alais']))?$config_widget_options[$field]['field_alais']:$field;
					$filterWidgetOption[$field]=[
					  'scrollable' => true,
					  'pluginOptions' =>['highlight' =>true],
					  'dataset'=>[
								[
								'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
								'display' => 'value',
								'remote' => [
									'url' => $ajaxurl. '&q=%QUERY&field='.$field.'&type=typehead',
									'wildcard' => '%QUERY',
								],
							]
						]
					];
				}else if($types[$type]==3){ /*DateRangePicker*/
					$filterWidgetOption[$field]=[
						'pluginOptions'=>[
							'showDropdowns'=> true,
							'autoUpdateInput' => true,
							'locale'=>[ 'format' => 'MM/DD/YYYY' ],
						],
						'options' => [
							'readonly' => 'readonly'
						],
						'presetDropdown' => true,
					];
				}

				/*Range Input Filter for calculation fields*/
				if(isset($config_widget_options[$field]) && in_array($field,$range_filter)){
					$filterWidgetOption[$field]=[
						'options' => $config_widget_options[$field]['options'],
						'html5Options' => $config_widget_options[$field]['html5Options'],
						'addon' => $config_widget_options[$field]['addon']
					];
				}
				if(in_array($field,$virtual_filter)){
						if(isset($config[$field])){
							$filterWidgetOption[$field]=[
							 'initValueText'=>(isset($config_widget_options[$field]['initValueText']))?$config_widget_options[$field]['initValueText']:NULL,
							 'options'=>['multiple'=>true,'nolabel'=>true],
							 'showToggleAll' => false,
							 'data'=>$config[$field],
							 'pluginEvents'=>(isset($config_widget_options[$field]['pluginEvents']))?$config_widget_options[$field]['pluginEvents']:[],
							 'pluginOptions'=>[
									'allowClear' => true
							 ]
							];
						}
				}
			}
		}
		//echo "<pre>",print_r($field),print_r($filterWidgetOption);die;
		return $filterWidgetOption;
	}
    /*
     * IRT : 206, 261, 378, 396
     * Get field char limit
     * */
    public function getTableFieldLimit($table){
		//$friendsArray2 = '"' . join('","',$select_fields) . '"';
		$field_type_list = ArrayHelper::map(ReportsFields::find()->joinWith('reportsFieldType')
			->where("report_table_id IN(select id from tbl_reports_tables where table_name ='".$table."')")->all(), 'field_name', function($model){
		  if(isset($model->length_value) && $model->length_value!="")
			   return $model->length_value;
		  else
			   return '';
		});
		if(!empty($field_type_list))
			return $field_type_list;
		else
			return array();
	 }
         /*
     * IRT : 206, 261, 378, 396
     * Get field char limit direct from db
     * */

    public function getTableFieldsDB($tbl_name) {
        $reports_db_array = Yii::$app->params['reports_db_tbl_fields'];
        if(isset($reports_db_array[$tbl_name]) && !empty($reports_db_array[$tbl_name])){
            return $reports_db_array[$tbl_name];
        }else{
            return array();
        }
    }

    /*
          * IRT 96,398
          * Author : Nelson Rana
          * Get Cases selected array
          */
         public function getSelectedGridCases($params_cases,$allTxt = 'ALL'){
            $client_case_ids=array();
            if(!empty($params_cases)){
                    foreach($params_cases as $k=>$v){
                            if($v==$allTxt || $v==''){ //  || strpos($v,",") !== false
                                return 'ALL';
                                $client_case_selected=$client_case_ids=array();
                                break;
                            }else if(strtolower($v)==strtolower($allTxt) || $v==''){
								return 'ALL';
								$client_case_selected=$client_case_ids=array();
								break;
							}else{
                                $client_case_ids[$v]= $client_case_selected[$v] = $v;
                            }
                    }
            }
            if(!empty($client_case_ids)) {
                $result_cases = ClientCase::find()->joinWith(['client'])->select(["case_name",'tbl_client_case.id'])->where(['tbl_client_case.id'=> $client_case_ids])->all();
                if(!empty($result_cases)){
                    foreach ($result_cases as $single_ca){
                        if(isset($client_case_selected[$single_ca->id]) && $client_case_selected[$single_ca->id] != '')
                            $client_case_selected[$single_ca->id] = html_entity_decode($single_ca->case_name);
                    }
                }
            }
            return $client_case_selected;
         }
         /*
          * IRT 96,398
          * Author : Nelson Rana
          * Get Cleints selected array
          */
         public function getSelectedGridClients($params_clients,$allTxt = 'ALL'){
            $client_ids= $clients_selected = array();
            if(!empty($params_clients)){
                //echo '<pre>';print_r($params_clients);die;
                foreach($params_clients as $k=>$v){
                    if($v==$allTxt || $v==''){	//|| strpos($v,",") !== false
                        return 'ALL';
						$clients_selected=$client_ids=array();
						break;
                    }else if(strtolower($v)==strtolower($allTxt) || $v==''){
						return 'ALL';
						$clients_selected=$client_ids=array();
						break;
					}else{
                        $client_ids[$v] = $clients_selected[$v] = $v;
                    }
                }
            }
            if(!empty($client_ids)) {
                $result_clients = ClientCase::find()->joinWith(['client'])->select(["client_name",'client_id'])->where(['tbl_client.id'=> $client_ids])->all();
                if(!empty($result_clients)){
                    foreach ($result_clients as $single_cl){
                        if(isset($clients_selected[$single_cl->client_id]) && $clients_selected[$single_cl->client_id] != '')
                            $clients_selected[$single_cl->client_id] = html_entity_decode ($single_cl->client_name);
                    }
                }
            }
            return $clients_selected;
         }

public function getSelectedGridClosedby($params_closedby,$allTxt = 'ALL'){
	 $closedby_ids= $closedby_selected = array();
	 if(!empty($params_closedby)){
			 //echo '<pre>';print_r($params_clients);die;
			 foreach($params_closedby as $k=>$v){
					 if($v==$allTxt || $v==''){	//|| strpos($v,",") !== false
							 return 'ALL';
	 $closedby_selected=$closedby_ids=array();
	 break;
					 }else if(strtolower($v)==strtolower($allTxt) || $v==''){
	 return 'ALL';
	 $closedby_selected=$closedby_ids=array();
	 break;
 }else{
							 $closedby_ids[$v] = $closedby_selected[$v] = $v;
					 }
			 }
	 }
	 if(!empty($closedby_ids)) {
			 $result_closedby = User::find()->select(["usr_first_name",'id', "usr_lastname"])->where(['tbl_user.id'=> $closedby_ids])->all();
			 if(!empty($result_closedby)){
					 foreach ($result_closedby as $single_cl){
							 if(isset($closedby_selected[$single_cl->id]) && $closedby_selected[$single_cl->id] != '')
									 $closedby_selected[$single_cl->id] = html_entity_decode ($single_cl->usr_first_name)." ".html_entity_decode ($single_cl->usr_lastname);
					 }
			 }
	 }
	 return $closedby_selected;
}
		public function checkOtherModuleAccess($notcheckaccess){
			$action_redirect="";
			if((new User)->checkAccess(1) && $notcheckaccess!=1) {
				return $action_redirect = 'site/index';
			}
			if((new User)->checkAccess(3) && $notcheckaccess!=2) {
				return $action_redirect = 'media/index';
			}
			if((new User)->checkAccess(4) && $notcheckaccess!=3) {
				return $action_redirect = 'mycase/index';
			}
			if((new User)->checkAccess(5) && $notcheckaccess!=4) {
				return $action_redirect = 'team/index';
			}
			if((new User)->checkAccess(2) && $notcheckaccess!=5) {
				return $action_redirect = 'global-projects/index';
			}
			if((new User)->checkAccess(7) && $notcheckaccess!=6) {
				return $action_redirect = 'billing-pricelist/internal-team-pricing';
			}
			if((new User)->checkAccess(11) && $notcheckaccess!=7) {
				return $action_redirect = 'custom-report/index';
			}
			if((new User)->checkAccess(8) && $notcheckaccess!=8) {
				return $action_redirect =  'site/administration';
			}
			return $action_redirect;
		}
}
