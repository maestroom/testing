<?php

namespace app\models;

use Yii;
use yii\log\Logger;
use app\models\User;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%settings}}".
 *
 * @property integer $id
 * @property string $field
 * @property string $fieldvalue
 * @property string $fieldtext
 * @property string $fieldimage
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Settings extends \yii\db\ActiveRecord
{
	/*Rebrand System variables*/
	public $loginpage_logo;
	public $modulepage_logo;
	public $custom_version;
	public $custom_logo_name;
	public $instruction_header;
	public $instruction_footer;
	public $report_header;
	public $loginpage_bottom;
	private $logo;
	/*Rebrand System variables*/
	/*Ldap Config Vars*/
	public $servers,$ldap_port,$defaultDomain,$search,$ldap_admin,$ldap_admin_pass,$ldap_connection_type,$ldap_user_filter,$ldap_group_filter,$ldap_user_filter_custom,$ldap_group_filter_custom;
	/*Ldap Config Vars*/
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field'], 'required'],
            [['field', 'fieldvalue', 'fieldtext','fieldimage'], 'string'],
            [['created', 'modified'], 'safe'],
            [['created_by', 'modified_by'], 'integer'],
        	[['loginpage_logo'], 'file', 'skipOnEmpty' => true,'extensions'=>['png'],'maxSize'=>1048576],
        	[['modulepage_logo'], 'file', 'skipOnEmpty' => true,'extensions'=>['png'],'maxSize'=>1048576],
        	[['servers','ldap_port','defaultDomain','search','ldap_admin','ldap_admin_pass','ldap_connection_type','ldap_user_filter','ldap_group_filter'],'required','on'=>'ldapconfig']	
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field' => 'Field',
            'fieldvalue' => 'Fieldvalue',
            'fieldtext' => 'Fieldtext',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        	'servers'=>'LDAP Servers',
        	'ldap_port'=>"LDAP Servers's Port#",
        	'defaultDomain'=>'Domain Name',
        	'search'=>'LDAP Search/Base Dn',
        	'ldap_admin'=>'LDAP Admin Dn or Rdn',
        	'ldap_admin_pass'=>'LDAP Admin Password',
        	'ldap_connection_type'=>'Connection Type',
        	'ldap_user_filter'=>'User LDAP Filter',
        	'ldap_group_filter'=>'Group LDAP Filter',
			'ldap_user_filter_custom'=>'User LDAP Filter',
        	'ldap_group_filter_custom'=>'Group LDAP Filter',
        ];
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    	
    		return true;
    	} else {
    		return false;
    	}
    }
    public function uploadLoginLogo($files)
    {
    	$random_digit=uniqid();
//    	$name = 'loginpagelogo_'.preg_replace('/[^A-Za-z0-9_]/', '-',basename($files['Settings']['name']['loginpage_logo'],".png")) .'_'.$random_digit. '.png';
    	$name = preg_replace('/[^A-Za-z0-9_]/', '-',basename($files['Settings']['name']['loginpage_logo'],".png")).'.png';
    	$path= Yii::$app->basePath.'/images/'.$name;
		$data = utf8_encode(file_get_contents($files['Settings']['tmp_name']['loginpage_logo']));
    	return array('name'=>$name,'data'=>$data);
		/*if(copy($files['Settings']['tmp_name']['loginpage_logo'], $path)){	
    		return $name;
    	} else {
    		return false;
    	}*/
    }
    public function uploadModulLogo($files)
    {
    	$random_digit=uniqid();
//    	$name = 'modulepagelogo_'.preg_replace('/[^A-Za-z0-9_]/', '-',basename($files['Settings']['name']['modulepage_logo'],".png")) .'_'.$random_digit. '.png';
    	$name = preg_replace('/[^A-Za-z0-9_]/', '-',basename($files['Settings']['name']['modulepage_logo'],".png")).'.png';
    	$path= Yii::$app->basePath.'/images/'.$name;
		$data = utf8_encode(file_get_contents($files['Settings']['tmp_name']['modulepage_logo']));
    	return array('name'=>$name,'data'=>$data);
    	/*if(copy($files['Settings']['tmp_name']['modulepage_logo'], $path)){
    		return $name;
	    } else {
	    	return false;
	    }*/
    }
	public static function setLogo() {
        $logo_data = Settings::find()->select(['fieldimage'])->where("field = 'modulepage_logo'")->one();
		if(isset($logo_data->fieldimage)){
			$_SESSION['logo'] = utf8_decode($logo_data->fieldimage);
		}
    }
	public static function setLogoName() {
        $_SESSION['logoname'] = Settings::find()->select(['fieldvalue'])->where("field = 'custom_logo_name'")->one()->fieldvalue;
		if($_SESSION['logoname'] == ""){
			$_SESSION['logoname']=Yii::$app->name;
		}
    }

    public static function getLogo() {
        if (!isset($_SESSION['logo'])) {
            self::setLogo();
        }
        return $_SESSION['logo'];
    }
	public static function getLogoName() {
        if (!isset($_SESSION['logoname'])) {
            self::setLogoName();
        }
        return $_SESSION['logoname'];
    }
	public static function setSessionTimeout() {
        $_SESSION['setting_session_timeout'] = Settings::find()->select(['fieldvalue'])->where(['field' =>'session_timeout'])->one();
		//Settings::find()->select(['fieldvalue'])->where("field = 'modulepage_logo'")->one()->fieldvalue;
    }
	public static function getSessionTimeout() {
        if (!isset($_SESSION['setting_session_timeout'])) {
            self::setSessionTimeout();
        }
        return $_SESSION['setting_session_timeout'];
    }

	public static function setCustomversion() {
        $_SESSION['custom_version'] = Settings::find()->select(['fieldvalue'])->where(['field' =>'custom_version'])->one();
		//Settings::find()->select(['fieldvalue'])->where("field = 'modulepage_logo'")->one()->fieldvalue;
    }
	public static function getCustomversion() {
        if (!isset($_SESSION['custom_version'])) {
            self::setCustomversion();
        }
        return $_SESSION['custom_version'];
    }
	
	/*ldap Utility Functions*/
    public function connect_AD($ldap_server,$ldap_user,$ldap_pass,$port,$ldap_search)
    {
       
        $config = [
                    'account_suffix'        => '',
                    'domain_controllers'    => [$ldap_server],
                    'port'                  => $port,
                    'base_dn'               => $ldap_search,
                    'admin_username'        => $ldap_user,
                    'admin_password'        => $ldap_pass,
                    'follow_referrals'      => true,
                    'use_ssl'               => false,
                    'use_tls'               => false,
                    'use_sso'               => false,
                ]; 
            try{
               $ad = new \Adldap\Adldap($config);
                if ($ad->connect()) {
                        return true ;
                } else {
                        return false;
                }
           } catch (\Exception $e) {
                return false;
           }
    }
	public function searchLinuxWithPagination($data,$inculdeexisting,$ldap_cookie,$q) {
		$err="";
    	$ldap_result=array();
    	$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
    	$ldappass = $data->ldap_admin_pass;  // associated password
    
    	$base_dn = trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
    	$cn_arr=array();

    	$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
        
    	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	if ($ldapconn) {
    		Yii::info("LDAP Server connected successfully");
    
            // binding to ldap server
    		$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    		// verify binding
    		if ($ldapbind) {
    			Yii::info("LDAP Server Binding successfully");
    			$filter = html_entity_decode(($data->ldap_user_filter_custom));//'(cn=*)';
				//echo $filter;die;
				if(isset($q) && $q!="") {
					$filter="(&(objectClass=inetOrgPerson)(objectClass=organizationalPerson)(objectClass=person)(givenname=*".$q."*)(cn=*".$q."*))";
				}
                //echo trim($filter);die;
                //$filter="(&(objectCategory=person)(objectClass=user) (|(proxyAddresses=*:koch@inovitech.com) (mail=lkoch@inovitech.com)))";
    			//$filter ="(objectClass=inetOrgPerson)";
    			//$attr = array('*');
    			//$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
				$attr = array('*');
				$result  = ldap_search($ldapconn, $base_dn, $filter, $attr);
				$total_count=ldap_count_entries($ldapconn, $result);
				//$ldap_result['count']=$total_count;
				$pageSize = 50;
				$cookie = $ldap_cookie;
				//do {
					 ldap_control_paged_result($ldapconn, $pageSize, true, $cookie);
					 $result  = @ldap_search($ldapconn, $base_dn, $filter, $attr);
					 
				//} while($cookie !== null && $cookie != '');
				 
    			$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
				
    			if ( $result ) {
    				Yii::info("Fetching/Searching Data From Server successfully");
    				$entries = @ldap_get_entries( $ldapconn, $result );
    			
    				$AduserList= ArrayHelper::map(User:: find()->where(['usr_type'=>3])->all(), 'ad_uid', 'ad_uid');
    				foreach ($entries as $entriesdata)
    				{
    					if(((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!="")) && (isset($entriesdata['cn'][0]) && $entriesdata['cn'][0]!="") )
    					{
    						$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						if($inculdeexisting){
    							$ldap_result[$uidnumber]=array(
    									'cn'=>$entriesdata['cn'][0],
    									'sn'=>$entriesdata['sn'][0],
    									'givenname'=>$entriesdata['givenname'][0],
    									'gidnumber'=>$entriesdata['gidnumber'][0],
    									'uidnumber'=>$uidnumber,
    									'dn'=>$entriesdata['dn'],
    									'uid'=>$uid,
    									'mail'=>$entriesdata['mail'][0],
    							);
    							$cn_arr['cn']=$entriesdata['cn'][0];
    						}else{
    							if(!in_array($uidnumber, $AduserList)){
    								$ldap_result[$uidnumber]=array(
    										'cn'=>$entriesdata['cn'][0],
    										'sn'=>$entriesdata['sn'][0],
    										'givenname'=>$entriesdata['givenname'][0],
    										'gidnumber'=>$entriesdata['gidnumber'][0],
    										'uidnumber'=>$uidnumber,
    										'dn'=>$entriesdata['dn'],
    										'uid'=>$uid,
    										'mail'=>$entriesdata['mail'][0],
    								);
    								$cn_arr['cn']=$entriesdata['cn'][0];
    							}
    						}
    
    					}
    				}
    				//echo "<prE>",print_r($ldap_result),"</pre>";die;
    				//print_r($ldap_result);
					ldap_control_paged_result_response($ldapconn, $result, $cookie);
					$ldap_result['info']=array('cookies'=>$cookie,'count'=>$total_count);
    			}
    		} else {
    			$err="LDAP bind failed...";
    			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    				
    		}
    
    	}
    	else
    	{
    		$err="Connection Fail";
    		Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    	}
    	asort($ldap_result);
    	if($err=="")
    		return $ldap_result;
    	else
    		return $err;
	}
    public function searchLinux($data,$inculdeexisting){
    
    	$err="";
    	$ldap_result=array();
    	$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
    	$ldappass = $data->ldap_admin_pass;  // associated password
    
    	$base_dn = trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
    	$cn_arr=array();

    	$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
        
    	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	if ($ldapconn) {
    		Yii::info("LDAP Server connected successfully");
    
            // binding to ldap server
    		$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    		// verify binding
    		if ($ldapbind) {
    			Yii::info("LDAP Server Binding successfully");
    			$filter = html_entity_decode(($data->ldap_user_filter_custom));//'(cn=*)';
                $attr = array('*');
    			$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
				
    			if ( $result ) {
    				Yii::info("Fetching/Searching Data From Server successfully");
    				$entries = @ldap_get_entries( $ldapconn, $result );
    			
    				$AduserList= ArrayHelper::map(User:: find()->where(['usr_type'=>3])->all(), 'ad_uid', 'ad_uid');
    				foreach ($entries as $entriesdata)
    				{
    					if(((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!="")) && (isset($entriesdata['cn'][0]) && $entriesdata['cn'][0]!="") )
    					{
    						$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						if($inculdeexisting){
    							$ldap_result[$uidnumber]=array(
    									'cn'=>$entriesdata['cn'][0],
    									'sn'=>$entriesdata['sn'][0],
    									'givenname'=>$entriesdata['givenname'][0],
    									'gidnumber'=>$entriesdata['gidnumber'][0],
    									'uidnumber'=>$uidnumber,
    									'dn'=>$entriesdata['dn'],
    									'uid'=>$uid,
    									'mail'=>$entriesdata['mail'][0],
    							);
    							$cn_arr['cn']=$entriesdata['cn'][0];
    						}else{
    							if(!in_array($uidnumber, $AduserList)){
    								$ldap_result[$uidnumber]=array(
    										'cn'=>$entriesdata['cn'][0],
    										'sn'=>$entriesdata['sn'][0],
    										'givenname'=>$entriesdata['givenname'][0],
    										'gidnumber'=>$entriesdata['gidnumber'][0],
    										'uidnumber'=>$uidnumber,
    										'dn'=>$entriesdata['dn'],
    										'uid'=>$uid,
    										'mail'=>$entriesdata['mail'][0],
    								);
    								$cn_arr['cn']=$entriesdata['cn'][0];
    							}
    						}
    
    					}
    				}
    				
    			}
    		} else {
    			$err="LDAP bind failed...";
    			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    				
    		}
    
    	}
    	else
    	{
    		$err="Connection Fail";
    		Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    	}
    	asort($ldap_result);
    	if($err=="")
    		return $ldap_result;
    	else
    		return $err;
    
    }
	public function searchMSADWithPagination($data,$inculdeexisting,$ldap_cookie,$q){
	$err="";
    	$ldap_result=array();
    	$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
    	$ldappass = $data->ldap_admin_pass;  // associated password
    
    	$base_dn = trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
    	$cn_arr=array();
    	$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
    	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	if ($ldapconn) {
    		Yii::info("LDAP Server connected successfully");
    
    
    		// binding to ldap server
    		$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    		// verify binding
    		if ($ldapbind) {
    			Yii::info("LDAP Server Binding successfully");
    			$filter = html_entity_decode(($data->ldap_user_filter_custom));//'(objectCategory=Person)';//'(&(objectClass=user)(objectCategory=Person))';
    			//echo $filter;
				//echo "<br>";
				//$filter="(|(&(objectCategory=person)(objectClass=user))(&(sAMAccountName=*t*)))";
				//echo $filter="(& (objectCategory=person) (objectClass=user) (sAMAccountName=*t*))";
				//echo "<br>";
				if(isset($q) && $q!=""){
					//$filter="(&(objectClass=user)(objectCategory=Person)(sAMAccountName=*".$q."*)(cn=*".$q."*))";
					$filter="(&(objectClass=user)(objectCategory=Person)(|(sAMAccountName=*".$q."*)(cn=*".$q."*)))";
				}
				//echo $filter;die;
    			$attr = array('*');
				$result  = ldap_search($ldapconn, $base_dn, $filter, $attr);
				$total_count=ldap_count_entries($ldapconn, $result);
				//$ldap_result['count']=$total_count;
				$pageSize = 50;
				$cookie = $ldap_cookie;
				//do {
					 ldap_control_paged_result($ldapconn, $pageSize, true, $cookie);
					 $result  = @ldap_search($ldapconn, $base_dn, $filter, $attr);
					 
				//} while($cookie !== null && $cookie != '');
				 
    			$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
    			if ( $result ) {
    				Yii::info("Fetching/Searching Data From Server successfully");
    				$entries = @ldap_get_entries( $ldapconn, $result );
    				//$AduserList= ArrayHelper :: map(User::find()->where(["usr_type"=>3])->all(), 'ad_uid', 'ad_uid');
					$Adusers = ArrayHelper::map(User::find()->where(["usr_type"=>'3'])->select(['ad_uid','usr_username'])->all(), 'ad_uid', 'usr_username');
					$AduserList = array_keys($Adusers);
					$AduserNameList = array_map('strtolower', array_values($Adusers));
    				//echo "<prE>",print_r($AduserNameList),print_r($entries),"</pre>";die;
    				foreach ($entries as $entriesdata)
    				{
						$samaccountname=$entriesdata['samaccountname']['0'];
						//if(((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!="")) && (isset($entriesdata['cn'][0]) && $entriesdata['cn'][0]!="") )
    					{
    						//$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						//$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						if(isset($inculdeexisting)  && $inculdeexisting==1){
								$ldap_result[$entriesdata['dn']]=array(
    									'cn'=>$entriesdata['cn'][0],
    									'sn'=>$entriesdata['sn'][0],
    									'givenname'=>$entriesdata['givenname'][0],
    									'gidnumber'=>$entriesdata['gidnumber'][0],
    									//'uidnumber'=>$uidnumber,
    									'samaccountname'=>$samaccountname,
    									'dn'=>$entriesdata['dn'],
    									//'uid'=>$uid,
    									'mail'=>$entriesdata['mail'][0],
    							);
    							$cn_arr['cn']=$entriesdata['cn'][0];
    						}
    						else{
    							//if(!in_array($uidnumber, $AduserList))
								if(!in_array(strtolower($samaccountname),$AduserNameList))
								{
									if(isset($samaccountname) && $samaccountname!=""){
									$ldap_result[$entriesdata['dn']]=array(
    										'cn'=>$entriesdata['cn'][0],
    										'sn'=>$entriesdata['sn'][0],
    										'givenname'=>$entriesdata['givenname'][0],
    										'gidnumber'=>$entriesdata['gidnumber'][0],
    										//'uidnumber'=>$uidnumber,
    										'samaccountname'=>$samaccountname,
    										'dn'=>$entriesdata['dn'],
    										//'uid'=>$uid,
    										'mail'=>$entriesdata['mail'][0],
    								);
    								$cn_arr['cn']=$entriesdata['cn'][0];
									}
    							}
    						}
    					}
    				}
    				//echo "<prE>",print_r($ldap_result),"</pre>";die;
    				//print_r($ldap_result);
					ldap_control_paged_result_response($ldapconn, $result, $cookie);
					$ldap_result['info']=array('cookies'=>$cookie,'count'=>$total_count);
    			}
    		} else {
    			$err="LDAP bind failed...";
    			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    		}
    	}else{
    		$err="Connection Fail";
    		Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    	}
    	asort($ldap_result);
    	if($err=="")
    		return $ldap_result;
    	else
    		return $err;
	}
    public function searchMSAD($data,$inculdeexisting){
    
    	$err="";
    	$ldap_result=array();
    	$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
    	$ldappass = $data->ldap_admin_pass;  // associated password
    
    	$base_dn = trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
    	$cn_arr=array();
    	$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
    	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	if ($ldapconn) {
    		Yii::info("LDAP Server connected successfully");
    
    
    		// binding to ldap server
    		$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    		// verify binding
    		if ($ldapbind) {
    			Yii::info("LDAP Server Binding successfully");
    			$filter = html_entity_decode(($data->ldap_user_filter_custom));//'(objectCategory=Person)';//'(&(objectClass=user)(objectCategory=Person))';
    			//echo $filter;die;
    			$attr = array('*');
    			$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
    			if ( $result ) {
    				Yii::info("Fetching/Searching Data From Server successfully");
    				$entries = @ldap_get_entries( $ldapconn, $result );
    				$AduserList= ArrayHelper :: map(User::find()->where(["usr_type"=>3])->all(), 'ad_uid', 'ad_uid');
    				//echo "<prE>",print_r($entries),"</pre>";die;
    				foreach ($entries as $entriesdata)
    				{
    					if(((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!="")) && (isset($entriesdata['cn'][0]) && $entriesdata['cn'][0]!="") )
    					{
    						$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
    						if($inculdeexisting){
    							$ldap_result[$uidnumber]=array(
    									'cn'=>$entriesdata['cn'][0],
    									'sn'=>$entriesdata['sn'][0],
    									'givenname'=>$entriesdata['givenname'][0],
    									'gidnumber'=>$entriesdata['gidnumber'][0],
    									'uidnumber'=>$uidnumber,
    									'samaccountname'=>$entriesdata['samaccountname'][0],
    									'dn'=>$entriesdata['dn'],
    									'uid'=>$uid,
    									'mail'=>$entriesdata['mail'][0],
    							);
    							$cn_arr['cn']=$entriesdata['cn'][0];
    						}
    						else{
    							if(!in_array($uidnumber, $AduserList)){
    								$ldap_result[$uidnumber]=array(
    										'cn'=>$entriesdata['cn'][0],
    										'sn'=>$entriesdata['sn'][0],
    										'givenname'=>$entriesdata['givenname'][0],
    										'gidnumber'=>$entriesdata['gidnumber'][0],
    										'uidnumber'=>$uidnumber,
    										'samaccountname'=>$entriesdata['samaccountname'][0],
    										'dn'=>$entriesdata['dn'],
    										'uid'=>$uid,
    										'mail'=>$entriesdata['mail'][0],
    								);
    								$cn_arr['cn']=$entriesdata['cn'][0];
    							}
    						}
    					}
    				}
    				//echo "<prE>",print_r($ldap_result),"</pre>";die;
    				//print_r($ldap_result);
    			}
    		} else {
    			$err="LDAP bind failed...";
    			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    		}
    	}else{
    		$err="Connection Fail";
    		Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    	}
    	asort($ldap_result);
    	if($err=="")
    		return $ldap_result;
    	else
    		return $err;
    }
	public function ldapSearchByUser($accountname){
	
		$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
		$data=array();
		if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!="")
			$data=json_decode($settings_info->fieldvalue);
		

		if(isset($data->ldap_connection_type) && $data->ldap_connection_type=="AD"){
			return $this->searchMSADUser($data,$accountname);
		} else {
            return $this->searchLinuxUser($data,$accountname);
		}
		return ;
	
	}
	public function searchMSADUser($data,$accountname){
		$err="";
		$ldap_result=array();
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
		$base_dn = $dn;//'OU=group,DC=perceptionldap,DC=com';
		$config_dn=$data->search;
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
			// verify binding
			if ($ldapbind) {
				Yii::info("LDAP Server Binding successfully");
				//$filter ="(&(objectClass=user)(memberOf={$dn}))";
				$filter = "(&(sAMAccountName={$accountname}))";
	
				//html_entity_decode(($data->ldap_user_filter));//'(&(objectClass=user)(objectCategory=Person))';//'(cn=*)';
	
				$attr = array('*');
				$result = @ldap_search($ldapconn, $config_dn, "$filter", $attr);
				$result = @ldap_search($ldapconn,$config_dn, "$filter", $attr);
				$alreadyaddeduid=array();
				$cn_arr=array();
				if ( $result ) {
					$entries = @ldap_get_entries( $ldapconn, $result );
					Yii::info("Fetching/Searching Data From Server successfully");
					foreach ($entries as $entriesdata)
					{
						//if((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!=""))
						{
							//$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
							//$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
							//$user_info=User::find()->select(['id','ad_users','ad_uid'])->where(["usr_type"=>'3','ad_uid'=>$uidnumber])->one();
							//if(!in_array($uidnumber, $alreadyaddeduid))
							{
								$ldap_result[$entriesdata['dn']]=array(
										'cn'=>$entriesdata['cn'][0],
										'sn'=>$entriesdata['sn'][0],
										'givenname'=>$entriesdata['givenname'][0],
										'gidnumber'=>$entriesdata['gidnumber'][0],
										//'uidnumber'=>$uidnumber,
										'samaccountname'=>$entriesdata['samaccountname'][0],
										'dn'=>$entriesdata['dn'],
										//'uid'=>$uid,
										'mail'=>$entriesdata['mail'][0],
								);
								$cn_arr['cn']=$entriesdata['cn'][0];
							}
								
	
						}
					}
				}
			} else {
				$err="LDAP bind failed...";
				Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
			}
	
		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if(!empty($ldap_result))
		{
			return $ldap_result;
		}
		else
		{
			return $err;
		}
	}
	public function ldapsearchWithPagination($ldap_cookie,$q){
		$err="";
		$ldap_result=array();
		$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
		$data=array();
		if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!="")
			$data=json_decode($settings_info->fieldvalue);
		

		if(isset($data->ldap_connection_type) && $data->ldap_connection_type=="AD"){
			return $this->searchMSADWithPagination($data,$inculdeexisting,$ldap_cookie,$q);
		}
		else if (isset($data->ldap_connection_type) && $data->ldap_connection_type=="LDAP"){
            return $this->searchLinuxWithPagination($data,$inculdeexisting,$ldap_cookie,$q);
		}
		else{
           return $ldap_result;
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
		
        $base_dn = trim($data->search); //'OU=group,DC=perceptionldap,DC=com';
		$cn_arr=array();	
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			
	    
    	// binding to ldap server
    	$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
		// verify binding
    	if ($ldapbind) {
    		Yii::info("LDAP Server Binding successfully");
        $filter ='(cn=*)';
		$attr = array('*');
		$result = @ldap_search($ldapconn, $base_dn, $filter, $attr);
		if ( $result ) {
			Yii::info("Fetching/Searching Data From Server successfully");
		  	$entries = @ldap_get_entries( $ldapconn, $result );
		  	//echo "<prE>",print_r($entries),"</pre>";die;
		  	foreach ($entries as $data)
		  	{
		  		if(((isset($data['uid'][0]) && $data['uid'][0]!="") || (isset($data['objectguid'][0]) && $data['objectguid'][0]!="")) && (isset($data['cn'][0]) && $data['cn'][0]!="") )
		  		{
		  			$uidnumber=(isset($data['uidnumber'][0])?$data['uidnumber'][0]:$this->GUIDtoStr($data['objectguid'][0]));
		  			$uid=(isset($data['uid'][0])?$data['uid'][0]:$this->GUIDtoStr($data['objectguid'][0]));
		  			$ldap_result[$uidnumber]=array(
														'cn'=>$data['cn'][0],
														'sn'=>$data['sn'][0],
														'givenname'=>$data['givenname'][0],
														'gidnumber'=>$data['gidnumber'][0],
														'uidnumber'=>$uidnumber,
														'dn'=>$data['dn'],
														'uid'=>$uid,
														'mail'=>$data['mail'][0],
												  );
												  $cn_arr['cn']=$data['cn'][0];
		  			
		  		}
		  	}
		  	//echo "<prE>",print_r($ldap_result),"</pre>";die;
		  	//print_r($ldap_result);
		 }
    	} else {
	    		$err="LDAP bind failed...";
       			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
	    		
    	}

		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if($err=="")
			return $ldap_result;
		else 
			return $err;	
		}
	}
    public function ldapsearch(){
		$err="";
		$ldap_result=array();
		$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
		$data=array();
		if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!="")
			$data=json_decode($settings_info->fieldvalue);
		

		if(isset($data->ldap_connection_type) && $data->ldap_connection_type=="AD"){
			return $this->searchMSAD($data,$inculdeexisting);
		}
		else if (isset($data->ldap_connection_type) && $data->ldap_connection_type=="LDAP"){
            return $this->searchLinux($data,$inculdeexisting);
		}
		else{
           return $ldap_result;
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
		
        $base_dn = trim($data->search); //'OU=group,DC=perceptionldap,DC=com';
		$cn_arr=array();	
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			
	    
    	// binding to ldap server
    	$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
		// verify binding
    	if ($ldapbind) {
    		Yii::info("LDAP Server Binding successfully");
        $filter ='(cn=*)';
		$attr = array('*');
		$result = @ldap_search($ldapconn, $base_dn, $filter, $attr);
		if ( $result ) {
			Yii::info("Fetching/Searching Data From Server successfully");
		  	$entries = @ldap_get_entries( $ldapconn, $result );
		  	//echo "<prE>",print_r($entries),"</pre>";die;
		  	foreach ($entries as $data)
		  	{
		  		if(((isset($data['uid'][0]) && $data['uid'][0]!="") || (isset($data['objectguid'][0]) && $data['objectguid'][0]!="")) && (isset($data['cn'][0]) && $data['cn'][0]!="") )
		  		{
		  			$uidnumber=(isset($data['uidnumber'][0])?$data['uidnumber'][0]:$this->GUIDtoStr($data['objectguid'][0]));
		  			$uid=(isset($data['uid'][0])?$data['uid'][0]:$this->GUIDtoStr($data['objectguid'][0]));
		  			$ldap_result[$uidnumber]=array(
														'cn'=>$data['cn'][0],
														'sn'=>$data['sn'][0],
														'givenname'=>$data['givenname'][0],
														'gidnumber'=>$data['gidnumber'][0],
														'uidnumber'=>$uidnumber,
														'dn'=>$data['dn'],
														'uid'=>$uid,
														'mail'=>$data['mail'][0],
												  );
												  $cn_arr['cn']=$data['cn'][0];
		  			
		  		}
		  	}
		  	//echo "<prE>",print_r($ldap_result),"</pre>";die;
		  	//print_r($ldap_result);
		 }
    	} else {
	    		$err="LDAP bind failed...";
       			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
	    		
    	}

		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if($err=="")
			return $ldap_result;
		else 
			return $err;	
		}
	}
	public function GUIDtoStr($binary_guid) {
		$hex_guid = unpack("H*hex", $binary_guid);
		$hex = $hex_guid["hex"];
	
		$hex1 = substr($hex, -26, 2) . substr($hex, -28, 2) . substr($hex, -30, 2) . substr($hex, -32, 2);
		$hex2 = substr($hex, -22, 2) . substr($hex, -24, 2);
		$hex3 = substr($hex, -18, 2) . substr($hex, -20, 2);
		$hex4 = substr($hex, -16, 4);
		$hex5 = substr($hex, -12, 12);
	
		$guid_str = $hex1 . "-" . $hex2 . "-" . $hex3 . "-" . $hex4 . "-" . $hex5;
	
		return $guid_str;
	}
	public function searchMSADGroup($data){
		$err="";
		$ldap_result=array();
		$cn_arr=array();
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
	
		$dcs="";
		/*search on DC only to get all group*/
		$search_component=explode("dc=",strtolower(trim($data->search)));
	
		foreach ($search_component as $k=>$v)
		{
			if(!strrpos($v, "="))
				$dcs.="dc=".$v;
		}
		$base_dn = $data->search;//$dcs;//trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
			// verify binding
			if ($ldapbind) {
				Yii::info("LDAP Server Binding successfully");
				$filter =html_entity_decode(trim($data->ldap_group_filter_custom));//'(objectClass=group)';/*(objectClass=container))';*/
				$attr = array('*');
				$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
	
				if ( $result ) {
					$entries = @ldap_get_entries( $ldapconn, $result );
					Yii::info("Fetching/Searching Data From Server successfully");
					foreach ($entries as $entriesdata)
					{
						if(isset($entriesdata['objectclass'][0]) && ($entriesdata['objectclass'][0]=="group" || $entriesdata['objectclass'][1]=="group" || $entriesdata['objectclass'][0]=="container" || $entriesdata['objectclass'][1]=="container"))
						{
							$ldap_result[$entriesdata['dn']]=strtoupper($entriesdata['cn'][0]);$cn_arr[$entriesdata['cn'][0]]=$entriesdata['cn'][0];
						}
					}
				}
			} else {
				$err="LDAP bind failed...";
				Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
			}
	
		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if($err=="")
			return $ldap_result;
		else
			return $err;
	
	
	}
	public function searchLinuxGroup($data){
		$err="";
		$ldap_result=array();
		$cn_arr=array();
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
	
		$dcs="";
		/*search on DC only to get all group*/
		$search_component=explode("dc=",strtolower(trim($data->search)));
	
		foreach ($search_component as $k=>$v)
		{
			if(!strrpos($v, "="))
				$dcs.="dc=".$v;
		}
		$base_dn = $data->search;//$dcs;//trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
			// verify binding
			if ($ldapbind) {
				Yii::info("LDAP Server Binding successfully");
				$filter =html_entity_decode(trim($data->ldap_group_filter_custom));//
				$filter ='(|(ou=*)(objectClass=posixGroup)(objectClass=groupOfNames))'; //needToChange
				$attr = array('*');
				$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
				if ( $result ) {
					$entries = @ldap_get_entries( $ldapconn, $result );
					Yii::info("Fetching/Searching Data From Server successfully");
					foreach ($entries as $data)
					{
						if((isset($data['ou'][0]) && $data['ou'][0]!=""))
						{	$ldap_result[$data['dn']]=strtoupper($data['ou'][0]);$cn_arr[$data['ou'][0]]=$data['ou'][0];}
						if(isset($data['objectclass'][0]) && $data['objectclass'][0]=="posixGroup")
						{		$ldap_result[$data['dn']]=strtoupper($data['cn'][0]);$cn_arr[$data['cn'][0]]=$data['cn'][0];}
						if(isset($data['objectclass'][0]) && $data['objectclass'][0]=="groupOfNames")
						{		$ldap_result[$data['dn']]=strtoupper($data['cn'][0]);$cn_arr[$data['cn'][0]]=$data['cn'][0];}
					}
				}
			} else {
				$err="LDAP bind failed...";
				Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
			}
	
		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if($err=="")
			return $ldap_result;
		else
			return $err;
	}
	public function ldapgroupsearch(){
		$err="";
		$ldap_result=array();
		$cn_arr=array();
		$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
		$data=array();
		if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!="")
			$data=json_decode($settings_info->fieldvalue);
			
		if(isset($data->ldap_connection_type) && $data->ldap_connection_type=="AD"){
			
			return $this->searchMSADGroup($data);
		}
		else if (isset($data->ldap_connection_type) && $data->ldap_connection_type=="LDAP"){
			return $this->searchLinuxGroup($data);
		}
		else{
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
		
		$dcs="";
		/*search on DC only to get all group*/
		$search_component=explode("dc=",strtolower(trim($data->search)));
		
		foreach ($search_component as $k=>$v)
		{
			if(!strrpos($v, "="))
					$dcs.="dc=".$v;
		}
		$base_dn = $data->search;//$dcs;//trim($data->search);//'OU=group,DC=perceptionldap,DC=com';
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		if ($ldapconn) {
		Yii::info("LDAP Server connected successfully");
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    	// binding to ldap server
    	$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
		// verify binding
    	if ($ldapbind) {
    	Yii::info("LDAP Server Binding successfully");
    	$filter ='(|(ou=*)(objectClass=posixGroup)(objectClass=groupOfNames))';	
		$attr = array('*');
		$result = @ldap_search($ldapconn, $base_dn, $filter, $attr);
		if ( $result ) {
		  	$entries = @ldap_get_entries( $ldapconn, $result );
		  	Yii::info("Fetching/Searching Data From Server successfully");
		    foreach ($entries as $data)
		  	{
		  		if((isset($data['ou'][0]) && $data['ou'][0]!=""))
		  		{	$ldap_result[$data['dn']]=strtoupper($data['ou'][0]);$cn_arr[$data['ou'][0]]=$data['ou'][0];}
		  		if(isset($data['objectclass'][0]) && $data['objectclass'][0]=="posixGroup")
		  		{		$ldap_result[$data['dn']]=strtoupper($data['cn'][0]);$cn_arr[$data['cn'][0]]=$data['cn'][0];}	
		  		if(isset($data['objectclass'][0]) && $data['objectclass'][0]=="groupOfNames")
		  		{		$ldap_result[$data['dn']]=strtoupper($data['cn'][0]);$cn_arr[$data['cn'][0]]=$data['cn'][0];}	
		  	}
		 }
    	} else {
       			 $err="LDAP bind failed...";
       			 Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
    	}

		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if($err=="")
			return $ldap_result;
		else 
			return $err;	
		}
	}
	public function searchMSADByDn($data,$dn){
		$err="";
		$ldap_result=array();
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
		$base_dn = $dn;//'OU=group,DC=perceptionldap,DC=com';
		$config_dn=$data->search;
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
			// verify binding
			if ($ldapbind) {
				Yii::info("LDAP Server Binding successfully");
				$filter ="(&(objectClass=user)(memberOf={$dn}))";
	
				//html_entity_decode(($data->ldap_user_filter));//'(&(objectClass=user)(objectCategory=Person))';//'(cn=*)';
	
				$attr = array('*');
				$result = @ldap_search($ldapconn, $config_dn, "$filter", $attr);
				$result = @ldap_search($ldapconn,$config_dn, "$filter", $attr);
				$alreadyaddeduid=array();
				$cn_arr=array();
				if ( $result ) {
					$entries = @ldap_get_entries( $ldapconn, $result );
					Yii::info("Fetching/Searching Data From Server successfully");
					//echo "<pre>",print_r($entries),"</pre>";die;
					if(is_array($entries) && !empty($entries)) {
					foreach ($entries as $entriesdata)
					{
					
						//if((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!=""))
						{
							//$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
							//$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
							//$user_info=User::find()->select(['id','ad_users','ad_uid'])->where(["usr_type"=>'3','ad_uid'=>$uidnumber])->one();
							$dn=$entriesdata['dn'];
							$user_sql = User::find()->where('usr_type = 3 AND ad_users like \'%"dn":"'.$dn.'"%\' ');
							$user_info=	$user_sql->one();
							if(isset($user_info->id))
							{
	
								$udata=json_decode($user_info->ad_users);
								if(isset($udata->dn) && $udata->dn!="")
								{
									if(trim($udata->dn)!=trim($entriesdata['dn']))
									{
										//$assigntask=TasksUnits::model()->count(array('select'=>'unit_assigned_to','condition'=>'unit_assigned_to = '.$user_info->id.' AND unit_status!=4'));
										//$assigntodo=TasksUnitsTodos::model()->count(array('select'=>'assigned','condition'=>'complete=0 AND assigned = '.$user_info->id.''));
										//if($assigntask > 0 || $assigntodo > 0)
										{
											$user_info->status=0;
											$user_info->save(false);
											Yii::info("LDAP User(".$user_info->ad_uid.") Inactive in IS-A-TASK");
										}
									}
								}
								$alreadyaddeduid[$entriesdata['dn']]=$entriesdata['dn'];
							}
							if(!in_array($entriesdata['dn'], $alreadyaddeduid))
							{
								if(isset($entriesdata['dn']) && $entriesdata['dn']!=""){
								$ldap_result[$entriesdata['dn']]=array(
										'cn'=>$entriesdata['cn'][0],
										'sn'=>$entriesdata['sn'][0],
										'givenname'=>$entriesdata['givenname'][0],
										'gidnumber'=>$entriesdata['gidnumber'][0],
										//'uidnumber'=>$uidnumber,
										'samaccountname'=>$entriesdata['samaccountname'][0],
										'dn'=>$entriesdata['dn'],
										//'uid'=>$uid,
										'mail'=>$entriesdata['mail'][0],
								);
								$cn_arr['cn']=$entriesdata['cn'][0];
								}
							}
								
	
						}
					}
					}
				}
			} else {
				$err="LDAP bind failed...";
				Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
			}
	
		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if(!empty($ldap_result))
		{
			return $ldap_result;
		}
		else
		{
			if($err!="")
			return $err;
			else
			return array();
		}
	}
	public function searchLinuxByDn($data,$dn){
		$err="";
		$ldap_result=array();
		$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
		$ldappass = $data->ldap_admin_pass;  // associated password
		$base_dn = $dn;//'OU=group,DC=perceptionldap,DC=com';
		$config_dn=$data->search;
		$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
		if ($ldapconn) {
			Yii::info("LDAP Server connected successfully");
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
			// verify binding
			if ($ldapbind) {
				Yii::info("LDAP Server Binding successfully");
				$filter =html_entity_decode(($data->ldap_user_filter_custom));//'(objectclass=*)';//'(cn=*)';
				$filter ="(objectClass=inetOrgPerson)";
				$attr = array('*');
				$result = @ldap_search($ldapconn, $base_dn, "$filter", $attr);
				$alreadyaddeduid=array();
				$cn_arr=array();
				if ( $result ) {
					$entries = @ldap_get_entries( $ldapconn, $result );
					Yii::info("Fetching/Searching Data From Server successfully");
					foreach ($entries as $entriesdata)
					{
						if((isset($entriesdata['uid'][0]) && $entriesdata['uid'][0]!="") || (isset($entriesdata['objectguid'][0]) && $entriesdata['objectguid'][0]!=""))
						{
							$uidnumber=(isset($entriesdata['uidnumber'][0])?$entriesdata['uidnumber'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
							$uid=(isset($entriesdata['uid'][0])?$entriesdata['uid'][0]:$this->GUIDtoStr($entriesdata['objectguid'][0]));
							$user_info=User::find()->select(['id','ad_users','ad_uid'])->where(["usr_type"=>'3','ad_uid'=>$uidnumber])->one();
							if(isset($user_info->ad_uid))
							{
	
								$udata=json_decode($user_info->ad_users);
								if(isset($udata->dn) && $udata->dn!="")
								{
									if(trim($udata->dn)!=trim($entriesdata['dn']))
									{
										$user_info->status=0;
										$user_info->save(false);
										Yii::info("LDAP User(".$user_info->ad_uid.") Inactive in IS-A-TASK");
										
									}
								}
							}
	
							$ldap_result[$uidnumber]=array(
									'cn'=>$entriesdata['cn'][0],
									'sn'=>$entriesdata['sn'][0],
									'givenname'=>$entriesdata['givenname'][0],
									'gidnumber'=>$entriesdata['gidnumber'][0],
									'uidnumber'=>$uidnumber,
									'dn'=>$entriesdata['dn'],
									'uid'=>$uid,
									'mail'=>$entriesdata['mail'][0],
							);
							$cn_arr['cn']=$entriesdata['cn'][0];
							$alreadyaddeduid[$uidnumber]=$uidnumber;
	
						}
						/*getting member info*/
						if(isset($entriesdata['memberuid']) && !empty($entriesdata['memberuid']))
						{
							foreach ($entriesdata['memberuid'] as $memkey=>$memuid)
							{
								if(!is_numeric($memuid))
								{
									$memfilter ='(uid='.$memuid.')';//'(cn=*)';
									$memattr = array('*');
									$memresult = @ldap_search($ldapconn, $config_dn, $memfilter, $memattr);
									if ( $memresult ) {
										$mementries = @ldap_get_entries( $ldapconn, $memresult );
										foreach ($mementries as $memdata)
										{
											if((isset($memdata['uid'][0]) && $memdata['uid'][0]!=""))
											{
												if(!in_array($memdata['uidnumber'][0],$alreadyaddeduid)){
													$ldap_result[$memdata['uidnumber'][0]]=array(
															'cn'=>$memdata['cn'][0],
															'sn'=>$memdata['sn'][0],
															'givenname'=>$memdata['givenname'][0],
															'gidnumber'=>$memdata['gidnumber'][0],
															'uidnumber'=>$memdata['uidnumber'][0],
															'dn'=>$memdata['dn'],
															'uid'=>$memdata['uid'][0],
															'mail'=>$memdata['mail'][0],
													);
													$cn_arr['cn']=$data['cn'][0];
												}
											}
										}
	
											
									}
								}
							}
						}
						/*getting member info*/
					}
	
				}
			} else {
				$err="LDAP bind failed...";
				Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
			}
	
		}
		else
		{
			$err="Connection Fail";
			Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
		}
		asort($ldap_result);
		if(!empty($ldap_result))
		{
			return $ldap_result;
		}
		else
		{
			return $err;
		}
	}
	public function ldapSearchByDN($dn)
	{
		$err="";
		$ldap_result=array();
		$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
		$data=array();
		if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!="")
			$data=json_decode($settings_info->fieldvalue);
	
		if(isset($data->ldap_connection_type) && $data->ldap_connection_type=="AD"){
	
			return $this->searchMSADByDn($data,$dn);
		}
		else if (isset($data->ldap_connection_type) && $data->ldap_connection_type=="LDAP"){
			return $this->searchLinuxByDn($data,$dn);
		}
		else{
			return $ldap_result;
			$ldaprdn  = $data->ldap_admin;     // ldap rdn or dn
			$ldappass = $data->ldap_admin_pass;  // associated password
	
			$base_dn = $dn;//'OU=group,DC=perceptionldap,DC=com';
			$config_dn=$data->search;
			$ldapconn = ldap_connect($data->servers,$data->ldap_port) or $err="Could not connect to LDAP server.";
			if ($ldapconn) {
				Yii::info("LDAP Server connected successfully");
				ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
				// binding to ldap server
				$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
				// verify binding
				if ($ldapbind) {
					Yii::info("LDAP Server Binding successfully");
					$filter ='(objectclass=*)';//'(cn=*)';
	
					$attr = array('*');
					$result = @ldap_search($ldapconn, $base_dn, $filter, $attr);
					$alreadyaddeduid=array();
					$cn_arr=array();
					if ( $result ) {
			  	$entries = @ldap_get_entries( $ldapconn, $result );
			  	Yii::info("Fetching/Searching Data From Server successfully");
			  	foreach ($entries as $data)
			  	{
			  		if((isset($data['uid'][0]) && $data['uid'][0]!="") || (isset($data['objectguid'][0]) && $data['objectguid'][0]!=""))
			  		{
			  			$uidnumber=(isset($data['uidnumber'][0])?$data['uidnumber'][0]:$this->GUIDtoStr($data['objectguid'][0]));
			  			$uid=(isset($data['uid'][0])?$data['uid'][0]:$this->GUIDtoStr($data['objectguid'][0]));
			  			$user_info=User::model()->find(array('select'=>'id,ad_users, ad_uid','condition'=>"usr_type='3' AND ad_uid='".$uidnumber."'"));
			  			if(isset($user_info->ad_uid))
			  			{
	
			  				$udata=json_decode($user_info->ad_users);
			  				if(isset($udata->dn) && $udata->dn!="")
			  				{
			  					if(trim($udata->dn)!=trim($data['dn']))
			  					{
			  						//$assigntask=TasksUnits::model()->count(array('select'=>'unit_assigned_to','condition'=>'unit_assigned_to = '.$user_info->id.' AND unit_status!=4'));
			  						//$assigntodo=TasksUnitsTodos::model()->count(array('select'=>'assigned','condition'=>'complete=0 AND assigned = '.$user_info->id.''));
			  						//if($assigntask > 0 || $assigntodo > 0)
			  						{
				  						$user_info->status=0;
				  						$user_info->save(false);
				  						Yii::info("LDAP User(".$user_info->ad_uid.") Inactive in IS-A-TASK");
			  						}
			  					}
			  				}
			  			}
			  			 
			  			$ldap_result[$uidnumber]=array(
			  					'cn'=>$data['cn'][0],
			  					'sn'=>$data['sn'][0],
			  					'givenname'=>$data['givenname'][0],
			  					'gidnumber'=>$data['gidnumber'][0],
			  					'uidnumber'=>$uidnumber,
			  					'dn'=>$data['dn'],
			  					'uid'=>$uid,
			  					'mail'=>$data['mail'][0],
			  			);
			  			$cn_arr['cn']=$data['cn'][0];
			  			$alreadyaddeduid[$uidnumber]=$uidnumber;
			  			 
			  		}
			  		/*getting member info*/
			  		if(isset($data['memberuid']) && !empty($data['memberuid']))
			  		{
			  			foreach ($data['memberuid'] as $memkey=>$memuid)
			  			{
			  				if(!is_numeric($memuid))
			  				{
			  					$memfilter ='(uid='.$memuid.')';//'(cn=*)';
			  					$memattr = array('*');
			  					$memresult = @ldap_search($ldapconn, $config_dn, $memfilter, $memattr);
			  					if ( $memresult ) {
			  						$mementries = @ldap_get_entries( $ldapconn, $memresult );
			  						foreach ($mementries as $memdata)
								  	{
								  		if((isset($memdata['uid'][0]) && $memdata['uid'][0]!=""))
								  		{
								  			if(!in_array($memdata['uidnumber'][0],$alreadyaddeduid)){
								  				$ldap_result[$memdata['uidnumber'][0]]=array(
								  						'cn'=>$memdata['cn'][0],
								  						'sn'=>$memdata['sn'][0],
								  						'givenname'=>$memdata['givenname'][0],
								  						'gidnumber'=>$memdata['gidnumber'][0],
								  						'uidnumber'=>$memdata['uidnumber'][0],
								  						'dn'=>$memdata['dn'],
								  						'uid'=>$memdata['uid'][0],
								  						'mail'=>$memdata['mail'][0],
								  				);
								  				$cn_arr['cn']=$data['cn'][0];
								  			}
								  		}
								  	}
	
								  		
			  					}
			  				}
			  			}
			  		}
			  		if(isset($data['member']) && !empty($data['member']))
			  		{
			  			foreach ($data['member'] as $memkey=>$memuid)
			  			{
			  				if(!is_numeric($memuid))
			  				{
			  					$memfilter ='(cn=*)';//'(cn=*)';
			  					$memattr = array('*');
			  					$memresult = @ldap_search($ldapconn, $config_dn, $memfilter, $memattr);
			  					if ( $memresult ) {
			  						$mementries = @ldap_get_entries( $ldapconn, $memresult );
			  						//echo "<pre>",print_r($mementries),"</pre>";
			  						foreach ($mementries as $memdata)
								  	{
								  		if((isset($memdata['uid'][0]) && $memdata['uid'][0]!="") || (isset($memdata['objectguid'][0]) && $memdata['objectguid'][0]!=""))
								  		{
								  			if($memdata['dn']==$memuid)
								  			{
								  				$memuidnumber=(isset($memdata['uidnumber'][0])?$memdata['uidnumber'][0]:$this->GUIDtoStr($memdata['objectguid'][0]));
								  				$memuid=(isset($memdata['uid'][0])?$memdata['uid'][0]:$this->GUIDtoStr($memdata['objectguid'][0]));
								  				if(!in_array($memuidnumber,$alreadyaddeduid)){
								  					$ldap_result[$memuidnumber]=array(
								  							'cn'=>$memdata['cn'][0],
								  							'sn'=>$memdata['sn'][0],
								  							'givenname'=>$memdata['givenname'][0],
								  							'gidnumber'=>$memdata['gidnumber'][0],
								  							'uidnumber'=>$memuidnumber,
								  							'dn'=>$memdata['dn'],
								  							'uid'=>$memuid,
								  							'mail'=>$memdata['mail'][0],
													  );
													  $cn_arr['cn']=$data['cn'][0];
								  				}
								  			}
								  		}
								  	}
	
								  		
			  					}
			  				}
			  			}
			  		}
			  		/*getting member info*/
			  	}
			  	 
					}
				} else {
					$err="LDAP bind failed...";
					Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
				}
	
			}
			else
			{
				$err="Connection Fail";
				Yii::error($err." Error no=>".@ldap_errno($ldapconn)." Error MSg =>".@ldap_err2str(@ldap_errno($ldapconn)));
			}
			asort($ldap_result);
			if(!empty($ldap_result))
			{
				return $ldap_result;
			}
			else
			{
				return $err;
			}
		}
	}
	
    public function ldapsearch123(){

    	$err="";
    	$ldap_result=array();
    	$settings_info=Settings::find()->where(['field'=>'active_dir'])->one();
    	$data=array();
    	if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!="")
    		$data=json_decode($settings_info->fieldvalue,true);

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
    			if ($ad->connect()) {
    				$filter= $data['ldap_user_filter'];
    				//echo $filter;
    				$filter ="(objectClass=inetOrgPerson)";
    				$results = $ad->search()->rawFilter($filter)->get();
    				if($results!="[]"){
    					foreach ($results as $data)
    					{
    						echo "<pre>",print_r($data->attributes),"</pre>";
    					}
    				}
				} else {
    				return array();
    			}
    		} catch (\Exception $e) {
    			return array();
    		}
    	}
    	print_r($data);
    }
}
