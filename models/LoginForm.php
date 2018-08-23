<?php

namespace app\models;


use Yii;
use yii\base\Model;
use app\models\Settings;
use app\models\UserLog;
use app\models\User;
use yii\helpers\ArrayHelper;
/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $usr_username;
    public $password;
    public $login_type;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['usr_username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    public function attributeLabels()
    {
    	return [
    		'usr_username' => Yii::t('app', 'Username'),
    		'password' => Yii::t('app', 'Password'),
    	];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    public function connectAd(){
    	$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
    	$data=array();
    	if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!=""){
    		$data=json_decode($settings_info->fieldvalue,true);
    	}

    	if(!empty($data)){
    		$config = [
				'account_suffix'        => '',
				'domain_controllers'    => [$data['servers']],
				'port'                  => $data['ldap_port'],
				'base_dn'               => $data['search'],
				'admin_username'        => $data['ldap_admin'],
				'admin_password'        => $data['ldap_admin_pass'],
				'follow_referrals'      => true,
				'use_ssl'               => false,
				'use_tls'               => false,
				'use_sso'               => false,
    		];
    		try{
    			$ad = new \Adldap\Adldap($config);
    			return $ad;
    		} catch (\Exception $e) {
    			return false;
    		}
    	}
    }
    public function authAdUser()
    {
	
			$authUser=false;
			$ad = $this->connectAd();
			if (isset($ad) && $ad->connect()) {
			
			    if (!empty($ad) && $ad->connect()) {
				
					try {
					
							$user_sql = User::find()
                            ->where(['usr_username'=>$this->usr_username,'usr_type'=>3,'status'=>1]);
							//echo "hetre215346 cnt =".$user_sql->count();die;
							if($user_sql->count() == 0){
								return false;
								/*$userldap_data=(new Settings)->ldapSearchByUser($this->usr_username);
								$search_uid=key($userldap_data);
								if(isset($search_uid) && $search_uid!=""){
								$user_sql = User::find()
								->where('usr_type = 3 AND status =1 AND ad_users like \'%"uidnumber":"'.$search_uid.'"%\' ');
								}
								if($user_sql->count() == 0){
								return false;
								}else{
								$user_data=$user_sql->one();
								$user_data->usr_username=$userldap_data[$search_uid]['samaccountname'];
								$user_data->ad_uid=$search_uid;
								$ldap_data=array(
										'cn'=>(isset($userldap_data[$search_uid]['cn'])?$userldap_data[$search_uid]['cn']:''),
										'sn'=>(isset($userldap_data[$search_uid]['sn'])?$userldap_data[$search_uid]['sn']:''),
										'givenname'=>(isset($userldap_data[$search_uid]['givenname'])?$userldap_data[$search_uid]['givenname']:''),
										'gidnumber'=>(isset($userldap_data[$search_uid]['gidnumber'])?$userldap_data[$search_uid]['gidnumber']:''),
										'uidnumber'=>(isset($userldap_data[$search_uid]['uidnumber'])?$userldap_data[$search_uid]['uidnumber']:''),
										'samaccountname'=>(isset($userldap_data[$search_uid]['samaccountname'])?$userldap_data[$search_uid]['samaccountname']:''),
										'dn'=>(isset($userldap_data[$search_uid]['dn'])?$userldap_data[$search_uid]['dn']:''),
										'uid'=>(isset($userldap_data[$search_uid]['uid'])?$userldap_data[$search_uid]['uid']:''),
										'mail'=>(isset($userldap_data[$search_uid]['mail'])?$userldap_data[$search_uid]['mail']:''),
								);
								$ldapuser_info=json_encode($ldap_data);
								$ldapdataval_arr=array_values($ldap_data);    
								if(!empty($ldapdataval_arr)){
									$user_data->ad_users=$ldapuser_info;
								}
								$user_data->save(false);
								$user_data=$user_sql->one();
								}*/
							} else {
								$user_data=$user_sql->one();
							}
							$Userdn="";
							/** IRT 388 **/
                            $query = Settings::find()->where(['field' => 'active_dir'])->all();
                            $settings = ArrayHelper::map($query, 'field', 'fieldvalue');
                            $servers = json_decode($settings['active_dir'],true);
                            /** End **/
                            $samaccount = "";
                            if(isset($user_data->ad_users) && $user_data->ad_users!="") {
								$ad_user=json_decode($user_data->ad_users, true);
								if(isset($ad_user['samaccountname']) && $ad_user['samaccountname']!="" && $servers['ldap_connection_type'] == 'AD') {
                                    $samaccount=$ad_user['samaccountname'].'@'.$servers['servers'];
                                }
                                if(isset($ad_user['dn']) && $ad_user['dn']!="") {
									$Userdn=$ad_user['dn'];
                                    $ldap_uid=$ad_user['samaccountname'];
                                    $userldap_data=(new Settings)->ldapSearchByUser($ldap_uid);
                                    if(is_array($userldap_data)){
                                        $results=array();
                                        foreach($userldap_data as $ldap_k=>$ldap){
                                            if(trim($ldap['samaccountname']) == $ldap_uid){
                                            $ldap_uid_num=$ldap_k;
                                            $results=$ldap;
                                            break;
                                            }
                                        }
                                        if(!empty($results)){
                                            if(isset($results['dn'])){
                                                if($Userdn!=$results['dn']){
                                                    $Userdn=$results['dn'];
                                                }
                                            }
                                        }
                                    }
								}
				if ((isset($samaccount) && $samaccount!="") && $ad->authenticate($samaccount, $this->password))
                {
				                    /* Status Changed */
                                   /* if($user_data->status == 0)
                                            User::updateAll(array( 'status' => 1 ), 'id = '.$user_data->id );*/

                  /** IRT 562 app.Log **/
                  \Yii::error("LDAP User Login in IS-A-TASK successfully");
                  $authUser = true;
                } elseif ((isset($Userdn) && $Userdn!="") && $ad->authenticate($Userdn, $this->password)) {
				                    /** IRT 562 app.Log **/
                                    \Yii::error("LDAP User Login in IS-A-TASK successfully");
                                	$authUser = true;
                                } else {
									/* Search Record from Active Directory */
                                    if(isset($samaccount) && $samaccount!="") {
                                        $search = $ad->search();
                                        $wheres = ['samaccountname' => $ad_user['samaccountname']];

                                        $results = $search->where($wheres);
                                        //$record = $search->findBy('samaccountname',$ad_user['samaccountname']);
                                        //$results = $search->get();
                                        $samaccount_count = count($results);
                                    }
                                    if(isset($Userdn) && $Userdn!="") {
                                        $search = $ad->search();
                                        $record = $search->findBy('dn',$Userdn);
                                        $results = $search->get();
                                        $dn_count = count($results);
                                    }

                                    //if($samaccount_count == 0 && $dn_count == 0) {
                                       // User::updateAll(array( 'status' => 0 ), 'id = '.$user_data->id );
                                    //}
                                    /* End Active Directory */

                                    /* IRT 562 : Service Side Failed */
                                    $usrLog = new UserLog();
                                    $usrLog->user_id = $user_data->id;
                                    $usrLog->login = date('Y-m-d H:i:s');
                                    $usrLog->logout = "";
                                    $usrLog->login_status = 1;
                                    $usrLog->ses_duration = "";
                                    $usrLog->save();
                                    /* End */

                                    if($ad->isDisabled) {
                                        $user_data->status=0;
                                        $user_data->save(false);
                                        $userid=$user_data->id;
										$username=$user_data->usr_username;
										$dt=date('Y-m-d H:i:s');
										$sql="INSERT INTO tbl_activity_log (origination, activity_type, activity_module_id, activity_name, task_cancel_reason, date_time, user_id, username) VALUES ('User', 'Inactive', ".$userid.", '".$username."', '', '".$dt."', ".$userid.", '".$username."')";
										Yii::$app->db->createCommand($sql)->execute();
                                    }
                                }
							} else {
								/* IRT 562 : Service Side Failed */
                                \Yii::error("LDAP login credentials are incorrect.");
                                $user_data = User::find()->where(['usr_username'=>$this->usr_username])->one();
                                $usrLog = new UserLog();
                                $usrLog->user_id = $user_data->id;
                                $usrLog->login = date('Y-m-d H:i:s');
                                $usrLog->logout = "";
                                $usrLog->login_status = 1;
                                $usrLog->ses_duration = "";
                                $usrLog->save();
                                /* End */
                            }
						} catch (\Adldap\Exceptions\Auth\UsernameRequiredException $e) {
                          	echo "The user didn't supply a username."; die;
						} catch (\Adldap\Exceptions\Auth\PasswordRequiredException $e) {
                            echo "The user didn't supply a password."; die;
						}
			}
			return $authUser;
		}
	}

    public function processAdUser($userDn)
    {
    	$user_data = User::find()->where(['usr_username'=>$this->usr_username,'usr_type'=>3])->one();
        $ad_user = json_decode($user_data->ad_users, true);
		$ldap_uid=$ad_user['samaccountname'];
		$userldap_data=(new Settings)->ldapSearchByUser($ldap_uid);
		$results=array();
		$ldap_uid_num=0;
		if(is_array($userldap_data)){
			foreach($userldap_data as $ldap_k=>$ldap){
				if(trim($ldap['samaccountname']) == $ldap_uid){
				$ldap_uid_num=$ldap_k;
				$results=$ldap;
				break;
				}
			}
		}
		if(!empty($results)){
            $ldap_data=array(
                    'cn'=>(isset($results['cn'])?$results['cn']:''),
                    'sn'=>(isset($results['sn'])?$results['sn']:''),
                    'givenname'=>(isset($results['givenname'])?$results['givenname']:''),
                    'gidnumber'=>(isset($results['gidnumber'])?$results['gidnumber']:''),
                    //'uidnumber'=>(isset($results['uidnumber'])?$results['uidnumber']:''),
                    'samaccountname'=>(isset($results['samaccountname'])?$results['samaccountname']:''),
                    'dn'=>(isset($results['dn'])?$results['dn']:''),
                    //'uid'=>(isset($results['uid'])?$results['uid']:''),
                    'mail'=>(isset($results['mail'])?$results['mail']:''),
            );
            $ldapuser_info=json_encode($ldap_data);
            $user_data->status=1;
			/*IRT-802*/
			/*if(isset($results['uid'])){
				$user_data->usr_username=$results['uid'];
				$user_data->ad_uid=$results['uid'];
			}*/
            if(isset($results['samaccountname']) && $results['samaccountname']!="") {
                $user_data->usr_username=$results['samaccountname'];
            }
			if(isset($results['givenname']))
				$user_data->usr_first_name=$results['givenname'];
            if(isset($results['sn']))
				$user_data->usr_lastname=$results['sn'];
			if(isset($results['mail']))
            $user_data->usr_email=$results['mail'];
			if(isset($results['givenname']) && isset($results['sn']))
				$user_data->usr_mi=substr($results['givenname'], 0, 1).substr($results['sn'], 0, 1);
			
			$ldapdataval_arr=array_values($ldap_data);    
			if(!empty($ldapdataval_arr)){
				$user_data->ad_users=$ldapuser_info;
			}
        }
        $rand = substr(md5(microtime()),rand(0,26),5);
        $pass = (new User)->hashPassword($rand);
        $user_data->usr_pass=$pass;
        $this->password = $rand;
        $user_data->save(false);
        return;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
    	$loginType=$_REQUEST['LoginForm']['login_type'];
    	if(isset($loginType) && $loginType=="AD"){ //LDAP  Login logic go here
    		$auth = $this->authAdUser();
			if($auth !== false){
    			$this->processAdUser($auth);
    			return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
    		}else{
    			$this->addError('password', 'Incorrect username or password.');
    			return false;
    		}
    	}else{
		    if ($this->validate()) {
		      	return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
		    } else {
                /* IRT 562 */
                \Yii::error("Isatask login credentials are incorrect.");
                $userId = $this->getUser();
                $usrLog               = new UserLog();
                $usrLog->user_id      = $userId->id;
                $usrLog->login        = date('Y-m-d H:i:s');
                $usrLog->logout       = "";
                $usrLog->login_status = 1;
                $usrLog->ses_duration = "";
                $usrLog->save();
                /* End */
            }
    	}
    	return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsrUsername($this->usr_username);
        }

        return $this->_user;
    }
}
