<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use app\models\Settings;
use app\models\FormBuilder;
use app\models\FormElementOptions;
use app\models\EvidenceCustodiansForms;
use app\models\Emailsettings;
use app\models\User;
use app\components\EMailer;
use app\models\PriorityTeam;
use app\models\PriorityProject;
use app\models\SettingsAdFilters;
use app\models\ProductUpdates;
use app\models\SettingsEmailTemplate;
use app\models\SettingsEmailFields;
use app\models\SettingEmailTemplateFields;
use app\models\SettingsEmailRecipients;
use app\models\TeamserviceSlaBusinessHours;
use app\models\TeamserviceSlaHolidays;
use app\models\Options;
use app\models\Teamservice;
use app\models\TasksUnitsData;
use app\models\Role;
use app\models\FormBuilderSystem;
use app\models\EvidenceTransactionTypeFields;
use app\models\PriorityTeamLoc;
use app\models\ReportsTables;
use app\models\ReportsFields;
use app\models\ReportsFieldCalculations;
use app\models\SystemMaintenanceLogs;

class SystemController extends Controller
{
    public function actionIndex()
	{
        return $this->render('index');
    }
    /**
     * Displays a System Form model.
     * @return mixed
     */
    public function actionForm($sysform='media_form') {
		$transtype_in = array();
		$form_lbl=['media_form'=>'Media Form','media_check_in_out_form'=>'Media Check In/Out Form','production_form'=>'Production Form','custodian_form'=>'Custodian Form'];
		$model=FormBuilderSystem::find()->where(['sys_form'=>$sysform])->orderBy('sort_order')->all();
		if($sysform == 'media_check_in_out_form'){
			$type_fields = EvidenceTransactionTypeFields::find()->all();			
			$transType = array('1'=>'Check in','2'=>'Check out','3'=> 'Destroy','4'=>'Move','5'=>'Return');
			foreach($type_fields as $single_field) {			
				if(isset($transtype_in[$single_field->form_builder_system_id]) && $transtype_in[$single_field->form_builder_system_id] != ''){
					$transtype_in[$single_field->form_builder_system_id] .= ', '.$transType[$single_field->transaction_type_id];
				} else {
					$transtype_in[$single_field->form_builder_system_id] = $transType[$single_field->transaction_type_id];
				}			
			}
		}		
		//echo '<pre>';print_r($transtype_in);die;
		if (Yii::$app->request->post()) {
			$post_data=Yii::$app->request->post();
			if(!empty($post_data['SystemFrom'])){
				$i=0;
				foreach($post_data['SystemFrom'] as $id=>$data){
					$model_new=FormBuilderSystem::findOne($id);
					$model_new->grid_type=$data['grid_type'];
					$model_new->required=$data['required'];
					$model_new->sort_order=$i;
					$model_new->save();
					$i++;
				}
			}
			return "OK";
		}
		
		//echo "<pre>",print_r($model),"</pre>"; die();
		return $this->renderAjax('form', [
				'model' => $model,
				'form_lbl'=>$form_lbl,
				'sysform'=>$sysform,
				'transtype_in' => $transtype_in
		]);    	
    }
    /**
     * Displays a single Setting model For Porject Sort.
     * @return mixed
     */
    public function actionProjectsort(){
    	$model = new Settings();
    	$data = Settings::find()->where(['field'=>'project_sort'])->one();
    	if(isset($data->id) && $data->id!== null)
    		$model = Settings::findOne($data->id);
    	
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return 'OK'; //$this->redirect(['view', 'id' => $model->id]);
    	} else {
    		return $this->renderAjax('Projectsort', [
    				'model' => $model,
    		]);
    	}
    }
    /**
     * Displays a single Setting model For Rebrand System.
     * @return mixed
     */
    public function actionRebrandSystem(){
    	$model = new Settings();
    	$request = Yii::$app->request;
    	if (Yii::$app->request->post() ) {
			$post_data=Yii::$app->request->post();
			$flag=true;
    		$submitbtn = $request->post('submitbtn');
    		$custom_version = $request->post('Settings')['custom_version'];
			$custom_logo_name = $request->post('Settings')['custom_logo_name'];
    		if($submitbtn == 'update'){
    			if(isset($_FILES['Settings']['name']['loginpage_logo']) && $_FILES['Settings']['name']['loginpage_logo']!=""){
                            $loginlogo_data = Settings::find()->where(['field'=>'loginpage_logo'])->one();
                            if(isset($loginlogo_data->id) && $loginlogo_data->id!== null)
                                $loginlogo_model = Settings::findOne($loginlogo_data->id);
                            else
                                $loginlogo_model = new Settings();

                            $loginlogo_model->field = 'loginpage_logo';
                            $logo=$loginlogo_model->uploadLoginLogo($_FILES);
                            if($logo === false) {
                                $errores = $loginlogo_model->getErrors();
                                $flag=false;
                            } else {
                                $loginlogo_model->fieldvalue =$logo['name'];
								$loginlogo_model->fieldimage =$logo['data'];
                                $loginlogo_model->save(false);
                            }
    			}
    			if(isset($_FILES['Settings']['name']['modulepage_logo']) && $_FILES['Settings']['name']['modulepage_logo']!="")
                        {
                            $modulelogo_data = Settings::find()->where(['field'=>'modulepage_logo'])->one();
                            if(isset($modulelogo_data->id) && $modulelogo_data->id!== null)
                                $modulelogo_model = Settings::findOne($modulelogo_data->id);
                            else
                                $modulelogo_model = new Settings();

                            $modulelogo_model->field = 'modulepage_logo';
                            $mo_logo=$modulelogo_model->uploadModulLogo($_FILES);
                            if($mo_logo === false) { 
                                $errores = $modulelogo_model->getErrors();
                                $flag=false;
                            } else {
                                $modulelogo_model->fieldvalue = $mo_logo['name'];
								$modulelogo_model->fieldimage = $mo_logo['data'];
                                $modulelogo_model->save(false);
								Settings::setLogo();
                            }
    			}
    			if(isset($custom_logo_name) && $custom_logo_name!="") {
                            $custom_logo_name_data = Settings::find()->where(['field'=>'custom_logo_name'])->one();
                            if(isset($custom_logo_name_data->id) && $custom_logo_name_data->id!== null)
                                $custom_logo_name_model = Settings::findOne($custom_logo_name_data->id);
                            else
                                $custom_logo_name_model = new Settings();

                            $custom_logo_name_model->field = 'custom_logo_name';
                            $custom_logo_name_model->fieldvalue =$custom_logo_name;
                            if($custom_logo_name_model->save()){}else{$flag=false;}
    			}
				if(isset($custom_version) && $custom_version!="") {
                            $custom_version_data = Settings::find()->where(['field'=>'custom_version'])->one();
                            if(isset($custom_version_data->id) && $custom_version_data->id!== null)
                                $custom_version_model = Settings::findOne($custom_version_data->id);
                            else
                                $custom_version_model = new Settings();

                            $custom_version_model->field = 'custom_version';
                            $custom_version_model->fieldvalue =$custom_version;
                            if($custom_version_model->save()){}else{$flag=false;}
    			}
    			if($flag){
    				return 'OK';
    			} else {
    				return 'ERROR';
    			}
    		}
    		if($submitbtn == 'default') {
    			$loginlogo_data = Settings::find()->where(['field'=>'loginpage_logo'])->one();
    			if(isset($loginlogo_data->id) && $loginlogo_data->id!== null) {
					//$path= Yii::$app->basePath.'/images/'.$loginlogo_data->fieldvalue;
					//@unlink($path);
					$loginlogo_data->delete();
    			}	
    			$modulelogo_data = Settings::find()->where(['field'=>'modulepage_logo'])->one();
    			if(isset($modulelogo_data->id) && $modulelogo_data->id!== null) {
					//$path= Yii::$app->basePath.'/images/'.$modulelogo_data->fieldvalue;
					//@unlink($path);
					unset($_SESSION['logo']);
    				$modulelogo_data->delete();
    			}
    			$custom_version_data = Settings::find()->where(['field'=>'custom_version'])->one();
    			if(isset($custom_version_data->id) && $custom_version_data->id!== null) {
    				$custom_version_data->delete();
    			}
				$custom_logo_name_data = Settings::find()->where(['field'=>'custom_logo_name'])->one();
    			if(isset($custom_logo_name_data->id) && $custom_logo_name_data->id!== null) {
    				$custom_logo_name_data->delete();
    			}
    			return 'OK';
    		}
    	} else {
        	$rbs_length = (new User)->getTableFieldLimit($model->tableSchema->name); 		
			$loginlogo_data = Settings::find()->where(['field'=>'loginpage_logo'])->one();	
			$modulelogo_data = Settings::find()->where(['field'=>'modulepage_logo'])->one();
            $custom_version_data = Settings::find()->where(['field'=>'custom_version'])->one();
			$custom_logo_name_data = Settings::find()->where(['field'=>'custom_logo_name'])->one();
	    	return $this->renderAjax('RebrandSystem', [
	    		'model' => $model,
	    		'custom_version_data'=>$custom_version_data,
				'custom_logo_name_data'=>$custom_logo_name_data,
				'loginlogo_data'=>$loginlogo_data,
				'modulelogo_data'=>$modulelogo_data,
	    		'rbs_length' => $rbs_length
	    	]);
    	}
    }
    /**
     * Displays a single EvidenceCustodiansForms model For Custodian Forms
     * @return mixed
     */
    public function actionCustodianforms(){
    	$model = new EvidenceCustodiansForms();
    	if (Yii::$app->request->post()) {
			if(isset($_POST['properties'])){
				foreach($_POST['properties'] as $el=>$val){
					$_POST['properties'][$el]['label']=htmlentities($val['label']);
					$_POST['properties'][$el]['description']=htmlentities($val['description']);
					$_POST['properties'][$el]['default_answer']=htmlentities($val['default_answer']);
					if(isset($_POST['properties'][$el]['text_val'])){
						$_POST['properties'][$el]['text_val']=htmlentities($val['text_val']);
					}
					if(isset($_POST['properties'][$el]['type']) && $_POST['properties'][$el]['type']=='dropdown'){
						if(isset($_POST['properties'][$el]['values'])){
						$_POST['properties'][$el]['values']=html_entity_decode($val['values']);
						}
					}
				}
			}
			$CustForms=Yii::$app->request->post('EvidenceCustodiansForms');
    		$model->Form_name=$CustForms['Form_name'];
    		$model->Form_desc=$CustForms['Form_desc'];
    		$model->Publish=$CustForms['Publish'];
			if($model->save()) {
    			$last_id = $model->Id;
				//Yii::$app->db->getLastInsertId();
    			$fb_model = new FormBuilder();
    			$form_data = $fb_model->ProcessFormData($_POST,'EvidenceCustodiansForms');
				$fb_model->saveFormData($form_data,$last_id,3,'system');
				return 'OK';
    		} else {
    			return 'ERROR';
    		} 
    	} else {			
			$ecf_length = (new User)->getTableFieldLimit($model->tableSchema->name); 			
    		$dataCustodianForms = EvidenceCustodiansForms::find()->select(['Id', 'Form_name','Publish'])->where(['remove'=>0])->asArray()->all();
    		return $this->renderAjax('Custodianforms', [
    			'model' => $model,
    			'dataCustodianForms' => $dataCustodianForms,
    			'ecf_length' => $ecf_length
    		]);
    	}
    }
     /**
     * Updates an existing CustodiansForm model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatecustodianforms($id) {
    	$model = $this->findCustodiansFormModel($id);
    	$formbuilder_data = new FormBuilder();
        $formbuilder_data = $formbuilder_data->getFromData($id,3,'DESC','formbuilder',0,'system');
    	if (Yii::$app->request->post()) {
			if(isset($_POST['properties'])){
				foreach($_POST['properties'] as $el=>$val){
					$_POST['properties'][$el]['label']=htmlentities($val['label']);
					$_POST['properties'][$el]['description']=htmlentities($val['description']);
					$_POST['properties'][$el]['default_answer']=htmlentities($val['default_answer']);
					if(isset($_POST['properties'][$el]['text_val'])){
						$_POST['properties'][$el]['text_val']=htmlentities($val['text_val']);
					}
					if(isset($_POST['properties'][$el]['type']) && $_POST['properties'][$el]['type']=='dropdown'){
						if(isset($_POST['properties'][$el]['values'])){
						$_POST['properties'][$el]['values']=html_entity_decode($val['values']);
						}
					}
				}
			}
    		$CustForms=Yii::$app->request->post('EvidenceCustodiansForms');
    		$model->Form_name=$CustForms['Form_name'];
    		$model->Form_desc=$CustForms['Form_desc'];
    		$model->Publish=$CustForms['Publish'];
    		if($model->save()){
    			$last_id = $id;//Yii::$app->db->getLastInsertId();
    			$fb_model = new FormBuilder();
    			$form_data = $fb_model->ProcessFormData($_POST,'EvidenceCustodiansForms');
    			$fb_model->saveFormData($form_data,$last_id,3,'system');
    			return 'OK';
    		}else{
    			return 'ERROR';
    		}
    	} else {
			$ecf_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		$dataCustodianForms=EvidenceCustodiansForms::find()->select(['Id', 'Form_name','Publish'])->asArray()->all();
    		return $this->renderAjax('Updatecustodianforms', [
				'model' => $model,
				'dataCustodianForms' => $dataCustodianForms,
				'formbuilder_data' => $formbuilder_data,
				'id' => $id,
				'ecf_length' => $ecf_length
    		]);
    	}
    }
    /**
     * Displays a single Setting model For Custom Wording Login Page Top Section
     * @return mixed
     */
    public function actionCustomWordingLogin(){
    	$model = new Settings();
    	$data = Settings::find()->where(['field'=>'loginpage'])->one();
    	if(isset($data->id) && $data->id!== null)
    		$model = Settings::findOne($data->id);
    	 
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return 'OK';//$this->redirect(['view', 'id' => $model->id]);
    	} else {
			$settings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		return $this->renderAjax('Customwordinglogin', [
    			'model' => $model,
    			'settings_length' => $settings_length
    		]);
    	}
    }
    /**
     * Displays a single Setting model For Custom Wording Login Page Bottom Section
     * IRT-15
     * Date Modified : 16-2-2017
     * Modified By   : Nelson Rana
     * @return mixed
     */
    public function actionCustomWordingLoginBottom(){
    	$model = new Settings();
    	$request = Yii::$app->request;
    	if (Yii::$app->request->post() ) {
			$flag=true;
    		$loginpage_bottom = $request->post('Settings')['loginpage_bottom'];
    		if(isset($loginpage_bottom) && $loginpage_bottom!="") {

                            $loginpage_bottom_data = Settings::find()->where(['field'=>'loginpage_bottom'])->one();    				
                            if(isset($loginpage_bottom_data->id) && $loginpage_bottom_data->id!== null){
                                    $loginpage_bottom_model = Settings::findOne($loginpage_bottom_data->id);
                            }else{
                                    $loginpage_bottom_model = new Settings();
                            }		    			
                            $loginpage_bottom_model->field = 'loginpage_bottom';
                            $loginpage_bottom_model->fieldtext =$loginpage_bottom;
                            if($loginpage_bottom_model->save()){}else{$flag=false;}
                    }
                    if($flag){
                            return 'OK';
                    }else{
                            return 'ERROR';
                    }    		
    	} else {
			$settings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		$loginpage_bottom = Settings::find()->where(['field'=>'loginpage_bottom'])->one();    	    		
    		return $this->renderAjax('CustomwordingloginBottom', [
    				'model' => $model,
    				'loginpage_bottom'=>$loginpage_bottom,
    				'settings_length' => $settings_length    	
    		]);
    	}
    
    }
   /* {
    	$model = new Settings();
    	$data = Settings::find()->where(['field'=>'loginpage_bottom'])->one();
    	if(isset($data->id) && $data->id!== null)
    		$model = Settings::findOne($data->id);
    	/*echo '<pre>';
    	print_r(Yii::$app->request->post());
    	print_r($model);
    	echo $data->id;
    	die('actionCustomWordingLoginBottom');*/
    /*	 
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return 'OK';//$this->redirect(['view', 'id' => $model->id]);
    	} else {
			$settings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		return $this->renderAjax('CustomwordingloginBottom', [
    				'model' => $model,
    				'settings_length' => $settings_length
    		]);
    	}
    }*/
    /**
     * Displays a single Setting model For Custom Wording Instruction form
     * @return mixed
     */
    public function actionCustomWordingInstruction(){
    	$model = new Settings();
    	$request = Yii::$app->request;
    	if (Yii::$app->request->post() ) {
    		$flag=true;
    		$instruction_header = $request->post('Settings')['instruction_header'];
    		
    	//	$instruction_footer = $request->post('Settings')['instruction_footer'];
    		if(isset($instruction_header) && $instruction_header!=""){
    				$instruction_header_data = Settings::find()->where(['field'=>'instruction_header'])->one();
	    			if(isset($instruction_header_data->id) && $instruction_header_data->id!== null){
	    				$instruction_header_model = Settings::findOne($instruction_header_data->id);
	    			}else{
	    				$instruction_header_model = new Settings();
	    			}	
	    			$instruction_header_model->field = 'instruction_header';
	    			$instruction_header_model->fieldtext =$instruction_header;
    				if($instruction_header_model->save()){}else{$flag=false;}
	    		}
//     			if(isset($instruction_footer) && $instruction_footer!=""){
//     				$instruction_footer_data = Settings::find()->where(['field'=>'instruction_footer'])->one();
// 	    			if(isset($instruction_footer_data->id) && $instruction_footer_data->id!== null){
// 	    				$instruction_footer_model = Settings::findOne($instruction_footer_data->id);
// 	    			}else{
// 	    				$instruction_footer_model = new Settings();
// 	    			}
// 	    			$instruction_footer_model->field = 'instruction_footer';
// 	    			$instruction_footer_model->fieldtext =$instruction_footer;
// 	    			if($instruction_footer_model->save()){}else{$flag=false;}
//     			}
    			if($flag){
    				return 'OK';
    			}else{
    				return 'ERROR';
    			}
    		
    	} else {
		$settings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		$instruction_header_data = Settings::find()->where(['field'=>'instruction_header'])->one();
    	//	$instruction_footer_data = Settings::find()->where(['field'=>'instruction_footer'])->one();
    		return $this->renderAjax('CustomWordingInstruction', [
                    'model' => $model,
                    'instruction_header_data' => $instruction_header_data,
                    'settings_length' => $settings_length
//			'instruction_footer_data'=>$instruction_footer_data,
    		]);
    	}
    }
    
    /**
     * IRT 27 (Add Header in Report Panel Excel, PDF)
     */
     public function actionCustomWordingReportHeader()
     {
		$model = new Settings();
    	$request = Yii::$app->request;
    	if (Yii::$app->request->post()) {
			$flag=true;
    		$report_header = $request->post('Settings')['report_header'];
    		if(isset($report_header) && $report_header!="") {
				$report_header_data = Settings::find()->where(['field'=>'report_header'])->one();    				
				if(isset($report_header_data->id) && $report_header_data->id!== null){
					$report_header_model = Settings::findOne($report_header_data->id);
				} else {
					$report_header_model = new Settings();
				}		    			
				$report_header_model->field = 'report_header';
				$report_header_model->fieldtext = ($report_header!="")?$report_header:"";
				if($report_header_model->save()){} else {
					$flag=false;
				}
			}
			if($flag){
				return 'OK';
			} else {
				return 'ERROR';
			}    
    	} else {
		$settings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		$report_header_data = Settings::find()->where(['field'=>'report_header'])->one();
    		return $this->renderAjax('CustomWordingReportHeader', [
                    'model' => $model,
                    'report_header_data' => $report_header_data,
                    'settings_length' => $settings_length
    		]);
            }
	}
    
    /**
     * Custom Wording Instruction Footer
     * @return Mixed
     */
    public function actionCustomWordingInstructionFooter()
    {
    	$model = new Settings();
    	$request = Yii::$app->request;
    	if (Yii::$app->request->post()){
    		$flag=true;
    		$instruction_footer = $request->post('Settings')['instruction_footer'];
    		if(isset($instruction_footer) && $instruction_footer!=""){
    			$instruction_footer_data = Settings::find()->where(['field'=>'instruction_footer'])->one();
    			if(isset($instruction_footer_data->id) && $instruction_footer_data->id !== null){
    				$instruction_footer_model = Settings::findOne($instruction_footer_data->id);
    			} else {
    				$instruction_footer_model = new Settings();
    			}
    			$instruction_footer_model->field = 'instruction_footer';
    			$instruction_footer_model->fieldtext = $instruction_footer;
    			if($instruction_footer_model->save()){}else{$flag=false;}
    		}
    		if($flag) {
                    return 'OK';
    		} else {
                    return 'ERROR';
    		}
    	} else {
			$settings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
    		$instruction_footer_data = Settings::find()->where(['field'=>'instruction_footer'])->one();
    		return $this->renderAjax('CustomWordingInstructionFooter', [
                    'model' => $model,
                    'instruction_footer_data'=>$instruction_footer_data,
                    'settings_length' => $settings_length
    		]);
    	}
    }
    /**
     * Displays a single Setting model For Custom Wording Login Page
     * @return mixed
     */
    public function actionEmailsetting()
    {
    	$model = new Emailsettings();
    	$data = Emailsettings::find()->one();
    	if(isset($data->setting_id) && $data->setting_id!== null)
    		$model = Emailsettings::findOne($data->setting_id);
    	
	    if(Yii::$app->request->post()) {
	    	$submitbtn = Yii::$app->request->post('buttonSubmit');
	    	if($submitbtn == 'config') {
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return 'OK'; // $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        return $this->renderAjax('Emailsetting', [
                            'model' => $model,
                        ]);
                    }
	    	}else{
                    if(isset($data->setting_id) && $data->setting_id!== null){
                            $model->delete();
                            return 'OK';
                    }
	    	}
	    }else{
                $esettings_length = (new User)->getTableFieldLimit($model->tableSchema->name);
                return $this->renderAjax('Emailsetting', [
                        'model' => $model,
                        'esettings_length' => $esettings_length
                ]);
	    }
    }
    
    public function actionTestemailalerts()
    {
		$post_data = Yii::$app->request->post();
		$mailer = (new Emailsettings)->testsendemail($post_data['Emailsettings']);
    	$mailer->AddAddress(trim($post_data['toEmail']));
    	$mailer->Subject = "Testing Email DEMO2";
    	$mailer->IsHTML(true);
    	$html = "This is Testing Email";
    	$mailer->Body = $html;
    	if($mailer->Send())
    		echo "Smtp Mail Sent Successfully to ".trim($_REQUEST['toEmail']);
    	else
    		echo $mailer->ErrorInfo;
    	
    	die;
    }
    
    /**
     * Displays a single Setting model For AD Configuration
     * @return mixed
     */
    public function actionLdapconfig() {
    	$model = new Settings();
    	$model->scenario = 'ldapconfig';
    	$data = Settings::find()->where(['field'=>'active_dir'])->one();
    	$json_data = array();
    	if(isset($data->id) && $data->id!== null) {
    		$json_data = json_decode($data->fieldvalue,true);
    	}
    	$user_filter = ArrayHelper::map(SettingsAdFilters::find()->select(['name','filter'])->where(['filter_type'=>2])->all(), 'filter','name');
    	$group_filter = ArrayHelper::map(SettingsAdFilters::find()->select(['name','filter'])->where(['filter_type'=>1])->all(), 'filter','name');
    	
    	if(isset($data->id) && $data->id!== null)
				$model = Settings::findOne($data->id);
    	
    	if(Yii::$app->request->post()) {
			$ldap_update = Yii::$app->request->post();
			$submitbtn = Yii::$app->request->post('buttonSubmit');
    		if($submitbtn == 'config') {
	    		$model->field= 'active_dir';
	    		$settings=Yii::$app->request->post('Settings');
	    		$settings['ldap_user_filter_custom'] = $ldap_update['UserLdapFilterinput'];
				$settings['ldap_group_filter_custom'] = $ldap_update['GroupLdapFilterinput'];
				$model->fieldvalue = json_encode($settings);
	    		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		    		return 'OK'; // $this->redirect(['view', 'id' => $model->id]);
		    	} else {
		    		return $this->renderAjax('Ldapconfig', [
						'model' => $model,
						'user_filter' => $user_filter,
						'group_filter' => $group_filter,
						'json_data' => $json_data
		    		]);
		    	}
    		} else if($submitbtn == 'cancel') {
    			$model->delete();
    			return 'OK';
    		} 
    	} else {	
			return $this->renderAjax('Ldapconfig', [
				'model' => $model,
				'user_filter'=>$user_filter,
				'group_filter'=>$group_filter,
				'json_data'=>$json_data,
				'modelInpLen' =>$modelInpLen
    		]);
    	}
    }
    
    public function actionTestldapconfig() {
		if(isset($_POST))
		{
			$model = new Settings();
			if($model->connect_AD($_POST['Settings']['servers'],$_POST['Settings']['ldap_admin'],$_POST['Settings']['ldap_admin_pass'],$_POST['Settings']['ldap_port'],$_POST['Settings']['search'])){
					echo 1;die;
			}else{
					echo "error";die;
			}
		}
		die;
	}
    /**
    * Lists all TeamlocationMaster models.
    * @return mixed
    */
    public function actionSysupdate(){
		$searchModel = new ProductUpdates();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->renderAjax('Sysupdate', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
		]);
    }
    
    /**
     * Deletes an existing CustodiansForm model.
     * If the $id is not found, a 404 HTTP exception will be thrown.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecustodianforms($id){
    	if(isset($id) && is_numeric($id) && $id>0){
	    	FormBuilder::updateAll(['remove'=>1],'formref_id = '.$id.' AND form_type = 3');
	    	EvidenceCustodiansForms::updateAll(['remove'=>1],'id = '.$id);
	    	//EvidenceCustodiansForms::DeleteAll('id = '.$id);
	    	//FormElementOptions::deleteAll('form_builder_id IN (select id from tbl_form_builder where formref_id ='.$id.' AND form_type =3 )');
	    	return 'OK';
    	}else{
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    } 
    /**
     * List Email Template Configuration
     * @return mixed
     */
     public function actionEmailtempconfig()
     {
		 /*IRT-746*/
     	$templates = SettingsEmailTemplate::find()->select(['id','email_name','email_sort'])->orderBy('email_name')->asArray()->all();
		 //->where('email_sort!=10')
		/*if(Yii::$app->db->driverName == 'mysql'){
			$templates_query = "SELECT id,email_name,email_sort FROM `tbl_settings_email` order by FIND_IN_SET(id,'1,3,2,21,9,19,10,11,12,13,14,15,20,4,7,5,6,8,16,17,18')";
		}else{
			$templates_query = "SELECT id,email_name,email_sort FROM tbl_settings_email ORDER BY CASE WHEN id = '1' THEN '1' WHEN id = '3' THEN '2' WHEN id = '2' THEN '3' WHEN id = '9' THEN '4' WHEN id = '19' THEN '5' WHEN id = '10' THEN '6' WHEN id = '11' THEN '7' WHEN id = '12' THEN '8' WHEN id = '13' THEN '9' WHEN id = '14' THEN '10' WHEN id = '15' THEN '11' WHEN id = '20' THEN '12' WHEN id = '4' THEN '13' WHEN id = '7' THEN '14' WHEN id = '5' THEN '15' WHEN id = '6' THEN '16' WHEN id = '8' THEN '17' WHEN id = '16' THEN '18' WHEN id = '17' THEN '19' WHEN id = '18' THEN '20' ELSE id END ASC";
		}
		$templates = Yii::$app->db->createCommand($templates_query)->queryAll();
		*/
		return $this->renderAjax('Emailtempconfig',['templates'=>$templates]);
     }
     /*
      * Show selected email Template in Edit Mode
      */
     public function actionEmailtemplatedata($id)
     {
     	$template_data=array();
     	if(isset($id) && $id > 0){
     		$template_data = SettingsEmailTemplate::findOne($id);
     	}

     	$getcaseRole = ArrayHelper::map(Role::find()->where("role_type LIKE '%1%' AND id > 0")->all(), 'id', 'role_name');
     	//$getTeamservices=Teamservice::model()->with(array('team'=>array('select'=>'team_name')))->findAll(array('order'=>"team.sort_order ASC,t.sort_order ASC",'select'=>'service_name'));
     	$getTeamservices = Teamservice::find()->select(['tbl_teamservice.id','service_name','teamid'])->joinwith('team')->orderby('tbl_team.sort_order ASC','tbl_teamservice.sor_order ASC')->all();
     	//echo "<pre>"; print_r($getcaseRole); exit;
     	if(Yii::$app->request->post()) {
    		$submitbtn = Yii::$app->request->post('btnSubmit');
    		$post_data = Yii::$app->request->post('SettingsEmailTemplate');
    		$email_caserole = Yii::$app->request->post('email_caserole');
    		$email_teamservice = Yii::$app->request->post('email_teamservice');
    		if($submitbtn=='update') {
                    if($template_data->load(Yii::$app->request->post()) && $template_data->validate()) {
                        $template_data->email_custom_subject = $post_data['email_custom_subject'];
                        $template_data->bcc_email_recipients = $post_data['bcc_email_recipients'];
                        $template_data->email_custom_body = htmlentities($post_data['email_custom_body'], ENT_COMPAT);
                        //$template_data->email_custom_recipients = implode(",",Yii::$app->request->post('emailrecipients'));
						if(Yii::$app->request->post('emailrecipients')!="")
							$template_data->email_custom_recipients = implode(",",Yii::$app->request->post('emailrecipients'));
						else
							$template_data->email_custom_recipients = Yii::$app->request->post('emailrecipients');
                        if(isset($email_caserole)){$template_data->email_caserole = implode(",",$email_caserole);}
                        if(isset($email_teamservice)){$template_data->email_teamservice = implode(",",$email_teamservice);}
                        if($template_data->save(false)) {
                            return 'OK';
                        }
                    } else {                        
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return $template_data->getErrors();                        
                    }   			
    		}
    		if($submitbtn=='restore') {
                    $template_data->email_custom_subject = null;
                    $template_data->email_custom_body = null;
                    $template_data->email_custom_recipients = null;
                    $template_data->email_teamservice = null;
                    $template_data->email_caserole = null;
					$template_data->bcc_email_recipients = null;
                    if($template_data->save(false)){
                        return 'OK';
                    }
    		}
    	} else {
            $Emailtmpl_len = (new User)->getTableFieldLimit($template_data ->tableSchema->name);			
     		$allrecipients = SettingsEmailRecipients::find()->all();
     		return $this->renderAjax('Emailtemplatedata',['id'=>$id,'template'=>$template_data,'email_recipients'=>$allrecipients,'getcaseRole'=>$getcaseRole,'getTeamservices'=>$getTeamservices,'Emailtmpl_len'=>$Emailtmpl_len]);
     	}
     }
    /*
	 * Show Email Template Preview for selected email Template
	 */
     public function actionEmailpreview($id)
     {
		$template_email_data=array();
		$main_body="";
		$main_subject="";
		$main_header="";
		$items = SettingsEmailFields::find()->select(['display_name', 'preview_display'])->all();
		$get_all_fields= ArrayHelper::map($items, 'display_name', 'preview_display');
		
		/** IRT 446 Instruction Form query **/
		/*$instructStatfields = FormBuilder::find()->select(['tbl_form_builder.default_unit','tbl_form_builder.id','tbl_form_builder.element_label as display_name','tbl_form_builder.default_answer','tbl_form_builder.element_type','tbl_form_builder.element_id'])
			->with(['formElementOptions' => function(\yii\db\ActiveQuery $query) {
				$query->select(['tbl_form_element_options.id','tbl_form_element_options.form_builder_id','tbl_form_element_options.element_option','tbl_form_element_options.is_default']);
		}])->where('tbl_form_builder.remove=0 AND tbl_form_builder.form_type=1')->asArray()->all();
		*/
		/** IRT 446 Data Form query **/
		/*$dataStatfields = FormBuilder::find()->select(['tbl_form_builder.default_unit','tbl_form_builder.id','tbl_form_builder.element_label as display_name','tbl_form_builder.default_answer','tbl_form_builder.element_type','tbl_form_builder.element_id'])
			->with(['formElementOptions' => function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_form_element_options.id','tbl_form_element_options.form_builder_id','tbl_form_element_options.element_option','tbl_form_element_options.is_default']);
		}])->where('tbl_form_builder.remove=0 AND tbl_form_builder.form_type=2')->asArray()->all();
		*/
		
		if(isset($id) && $id > 0) {
			$template_email_data=SettingsEmailTemplate::findOne($id);
			$main_header=$template_email_data->email_header;
			if(isset($_REQUEST['SettingsEmailTemplate']['email_custom_body']) && $_REQUEST['SettingsEmailTemplate']['email_custom_body']!="")
			$main_body=html_entity_decode($_REQUEST['SettingsEmailTemplate']['email_custom_body']);
			$main_subject=((isset($template_email_data->email_custom_subject) && $template_email_data->email_custom_subject!="")?$template_email_data->email_custom_subject:$template_email_data->email_subject);
			foreach ($get_all_fields as $field=>$preview) {
                            if(strpos($main_body,$field))
                                $main_body=str_replace($field, nl2br(html_entity_decode($preview)), $main_body);
                            if(strpos($main_subject,$field))
                                $main_subject=str_replace($field, nl2br(html_entity_decode($preview)), $main_subject);
			}
			
			/* IRT 446 Instruction Field */
			/*$option_result = '';
			foreach($instructStatfields as $instField => $value) 
			{
				$required_value = '['.$value['element_id'].']';
				if($value['element_type'] == 'checkbox' || $value['element_type'] == 'radio' || $value['element_type'] == 'dropdown') {
					$option_val = array();
					foreach($value['formElementOptions'] as $key => $element_val) {
						if($element_val['is_default']==1) // selected field 
							$option_val[] = $element_val['element_option'];
					}
					$option_result = implode(" , ",$option_val).' ( '.$value['display_name'].' ) ';	
				} else if($value['element_type'] == 'datetime') {
					$option_result = $value['default_answer'].' ( '.$value['display_name'].' ) ';
				} else if($value['element_type'] == 'number') {
					$option_result = $value['default_answer']. $value['default_unit'] .' ( '.$value['display_name'].' ) ';	
				} else {
					$option_result = $value['default_answer'].' ( '.$value['display_name'].' ) ';	
				}
				if(strpos($main_body,$required_value)){
					$main_body=str_replace($required_value, nl2br(html_entity_decode($option_result)), $main_body);
				}
				if(strpos($main_subject,$value['display_name']))
					$main_subject=str_replace($required_value, nl2br(html_entity_decode($option_result)), $main_subject);
			}*/
			
			/* IRT 446 Data Field */
			/*foreach($dataStatfields as $dataField => $data_value) {
				$required_value = '['.$value['element_id'].']';
				if($data_value['element_type'] == 'checkbox' || $data_value['element_type'] == 'radio' || $data_value['element_type'] == 'dropdown') {
					$option_val = array();
					foreach($data_value['formElementOptions'] as $key => $element_val) {
						if($element_val['is_default']==1) // selected field 
							$option_val[] = $element_val['element_option'];
					}
					$option_result = implode(" , ",$option_val).' ( '.$data_value['display_name'].' ) ';	
				} else if($value['element_type'] == 'datetime') {
					$option_result = $data_value['default_answer'].' ( '.$data_value['display_name'].' ) ';
				} else if($value['element_type'] == 'number') {
					$option_result = $data_value['default_answer']. $data_value['default_unit'] .' ( '.$data_value['display_name'].' ) ';
				} else {
					$option_result = $data_value['default_answer'].' ( '.$data_value['display_name'].' ) ';
				}
				if(strpos($main_body,$required_value)) 
					$main_body=str_replace($required_value, nl2br(html_entity_decode($option_result)), $main_body);
				if(strpos($main_subject,$value['display_name']))
					$main_subject=str_replace($required_value, nl2br(html_entity_decode($option_result)), $main_subject);
			}*/
		}
	
		return $this->renderPartial('emailpreviews',['template_email_data'=>$template_email_data, 'main_body'=>$main_body, 'main_subject'=>$main_subject, "main_header"=>$main_header]);
     }
     /*
      * ADD Email Template Fiedls in Email Subject and Body for selected email Template
      */
     public function actionEmailfields($email_sort){
     	$dataStatfields = array();
     	/*Logic set to show applicable fields only as per Email Alert*/
     	$global_fields=array(5,6,30,22,26);
     	$security_fields=array();
     	/*if(in_array($_REQUEST['email_sort'],array(4,5))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,42,43,44,45,46);}
     	if(in_array($_REQUEST['email_sort'],array(1,6,7,8,9))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28);}
     	if(in_array($_REQUEST['email_sort'],array(11))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,15,16);}
     	if(in_array($_REQUEST['email_sort'],array(12,13,14))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,24);}
     	if(in_array($_REQUEST['email_sort'],array(15,16,17))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,24,18,19,20);}
     	if(in_array($_REQUEST['email_sort'],array(3))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,15,16,27);}
     	if(in_array($_REQUEST['email_sort'],array(19,20))){$security_fields=array(23);}
     	if(in_array($_REQUEST['email_sort'],array(21))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,29,24);}
     	if(in_array($_REQUEST['email_sort'],array(18))){$security_fields=array(30,31);}
     	if(in_array($_REQUEST['email_sort'],array(2))){$security_fields=array(33,34,35,36,37,38,39,40,41,42,1,9,8,7,47);}*/
     	if(in_array($_REQUEST['email_sort'],array(5,6))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,42,43,44,45,46);}
     	if(in_array($_REQUEST['email_sort'],array(1,7,8,9,10))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28);}
     	if(in_array($_REQUEST['email_sort'],array(12))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,15,16);}
     	if(in_array($_REQUEST['email_sort'],array(13,14,15))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,24);}
     	if(in_array($_REQUEST['email_sort'],array(16,17,18))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,24,18,19,20);}
     	if(in_array($_REQUEST['email_sort'],array(4))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,15,16,27);}
     	if(in_array($_REQUEST['email_sort'],array(3))){$security_fields=array(48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71);}
     	if(in_array($_REQUEST['email_sort'],array(20,21))){$security_fields=array(23);}
     	if(in_array($_REQUEST['email_sort'],array(22))){$security_fields=array(1,2,3,4,7,8,9,10,11,12,13,14,25,28,29,24);}
     	if(in_array($_REQUEST['email_sort'],array(19))){$security_fields=array(30,31);}
     	if(in_array($_REQUEST['email_sort'],array(2))){$security_fields=array(33,34,35,36,37,38,39,40,41,42,1,9,8,7,47);}
     	
     	/* Logic set to show applicable fields only as per Email Alert */
        $final_fields = "SELECT id FROM tbl_settings_email where email_sort=".$_REQUEST['email_sort']."";
        // implode(",",array_merge($global_fields,$security_fields));
     	$email_template_fields = SettingEmailTemplateFields::find()->select('tbl_setting_email_template_fields.*,tbl_settings_email_fields.display_name')->where('template_id IN ('.$final_fields.')')->join('INNER JOIN', 
            'tbl_settings_email_fields',
            'tbl_settings_email_fields.id = tbl_setting_email_template_fields.field_id')->orderBy('tbl_setting_email_template_fields.display')->asArray()->all(); //  and field_type=0
        /*$instruction_template_fields = SettingEmailTemplateFields::find()->select('tbl_setting_email_template_fields.*,tbl_settings_email_fields.display_name')->where('template_id IN ('.$final_fields.') and field_type=2')->join('INNER JOIN', 
            'tbl_settings_email_fields',
            'tbl_settings_email_fields.id = tbl_setting_email_template_fields.field_id')->orderBy('tbl_setting_email_template_fields.display')->asArray()->all();
        $data_template_fields = SettingEmailTemplateFields::find()->select('tbl_setting_email_template_fields.*,tbl_settings_email_fields.display_name')->where('template_id IN ('.$final_fields.') and field_type=3')->join('INNER JOIN', 
            'tbl_settings_email_fields',
            'tbl_settings_email_fields.id = tbl_setting_email_template_fields.field_id')->orderBy('tbl_setting_email_template_fields.display')->asArray()->all();*/
        /* $data_cal_field=SettingEmailTemplateFields::find()->select('tbl_setting_email_template_fields.*,tbl_settings_email_fields.display_name')->where('template_id IN ('.$final_fields.') and field_type=1')->join('INNER JOIN', 
           'tbl_settings_email_fields', 
           'tbl_settings_email_fields.id = tbl_setting_email_template_fields.field_id')->asArray()->all(); */
        /* Start : To display statistics data fields in Mail Body */
        
        // echo "<pre>",print_r($email_template_fields),"</pre>"; die();
        /** IRT 446 Dataform fields Number or String **/
     	if($_REQUEST['email_sort'] == 5 || $_REQUEST['email_sort'] == 6 || $_REQUEST['email_sort'] == 9) 
        {
            //  $sql = "SELECT DISTINCT tbl_form_builder.element_label as display_name,tbl_form_builder.id FROM tbl_tasks_units_data INNER JOIN tbl_form_builder ON tbl_tasks_units_data.form_builder_id = tbl_form_builder.id WHERE tbl_form_builder.form_type = 2"; /* AND tbl_form_builder.element_type = 'number' */
            /*  $sql = "SELECT tbl_form_builder.default_unit, tbl_form_builder.id, tbl_form_builder.element_label AS display_name, tbl_form_builder.default_answer, tbl_form_builder.element_type 
                    FROM tbl_form_builder WHERE (tbl_form_builder.remove=0) AND (tbl_form_builder.form_type=2)";
                $dataStatfields = \Yii::$app->db->createCommand($sql)->queryAll(); */
        }
        /** End **/
        
        /** IRT 446 Dataform fields Number or String **/
        if($_REQUEST['email_sort'] == 1 || $_REQUEST['email_sort'] == 2 || $_REQUEST['email_sort'] == 4 ||
         $_REQUEST['email_sort'] == 5 || $_REQUEST['email_sort'] == 6 || $_REQUEST['email_sort'] == 7 ||
         $_REQUEST['email_sort'] == 8 || $_REQUEST['email_sort'] == 9 || $_REQUEST['email_sort'] == 10 ||
         $_REQUEST['email_sort'] == 13 || $_REQUEST['email_sort'] == 15) 
        {
            /*  $inst_sql = "SELECT DISTINCT tbl_form_builder.element_label as display_name,tbl_form_builder.id FROM tbl_task_instruct 
                    INNER JOIN tbl_form_instruction_values ON tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id AND tbl_task_instruct.isactive = 1
                    INNER JOIN tbl_form_builder ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id WHERE tbl_form_builder.form_type = 1 AND tbl_form_builder.remove!=1";	*/
            /*  $inst_sql = "SELECT tbl_form_builder.default_unit, tbl_form_builder.id, tbl_form_builder.element_label AS display_name, tbl_form_builder.default_answer, tbl_form_builder.element_type 
                    FROM tbl_form_builder WHERE (tbl_form_builder.remove=0) AND (tbl_form_builder.form_type=1)";
                $instructStatfields = \Yii::$app->db->createCommand($inst_sql)->queryAll();			
            */
        } 
        /** End **/
        
        /** IRT 446 **/
        $data = SettingsEmailTemplate::find()->where(['email_sort' => $_REQUEST['email_sort']])->one();
        $selectFields = array();	
        $arr_value = array(); 
        $instruction_fields_data = array('display' => "Project Instruction Form Fields",'display_name' => '[project_instruction_form_fields]');
        $data_task_outcome_form = array('display' => "Task Outcome Form Fields",'display_name' => '[task_outcome_form_fields]');
        foreach($email_template_fields as $key => $value) {
            if($data['email_sort']!=2){
                $arr_value[] = $value;
                if($data->is_instruction_form_field==1) {
                    if($value['display_name']=='[project_submitted_by]')
                        $arr_value[] = $instruction_fields_data;
                }
            } else {
                $arr_value[] = $value;
                if($data->is_instruction_form_field==1) {
                    if($value['display_name']=='[project_due_time]')
                        $arr_value[] = $instruction_fields_data;
                }
            }
            if($data->is_data_form_field==1) {
                if($value['display_name']=='[task_assigned_to]')
                    $arr_value[] = $data_task_outcome_form;
            }
            
        }
        $email_template_fields = $arr_value;
        $selectFields['Application Fields'] = $email_template_fields;
        /** End IRT 446 **/
        
        /* End : To display statistics data fields in Mail Body */
     	return $this->renderPartial('Emailfields',['email_template_fields'=>$email_template_fields,'dataStatfields'=>$dataStatfields,'email_sort'=>$email_sort,'instructStatfields'=>$instructStatfields, 'selectFields' => $selectFields]);
     }
     
     public function actionCalculationField() {
        $fieldcalculationList = ReportsFieldCalculations::find()->select(['id','calculation_name'])->orderBy('calculation_name ASC')->all();
        $fieldcalculationselectList = ArrayHelper::map($fieldcalculationList,'id','calculation_name');
        $post_data=Yii::$app->request->post();
        if(isset($post_data['element_pkid'])) {
            $data = SettingsEmailTemplate::find()->where(['email_sort'=>$post_data['email_sort']])->one();
            foreach (explode(',', $post_data['element_pkid']) as $ele_id) {
                $cal_model=ReportsFieldCalculations::findOne($ele_id);
                if(SettingsEmailFields::find()->where(['form_builder_id'=>$ele_id,'field_type'=>2])->count()) {
                    $id=SettingsEmailFields::find()->where(['form_builder_id'=>$ele_id,'field_type'=>2])->one()->id;
                    if(SettingEmailTemplateFields::find()->where(['template_id'=>$data->id,'field_id'=>$id])->count()==0) {
                        $modelEmailTempField = new SettingEmailTemplateFields();
                        $modelEmailTempField->template_id = $data->id;
                        $modelEmailTempField->field_id = $id;
                        $modelEmailTempField->display = $fb_model->element_label;
                        $modelEmailTempField->is_default = 0;
                        $modelEmailTempField->save(false);
                    }
                }else{
                    $model=new SettingsEmailFields();
                    $model->form_builder_id = $ele_id;
                    $model->display_name = $cal_model->calculation_field_name;
                    $model->field_value = '';
                    $model->origination = 'Calculation Data';
                    $model->field_type = 1;
                    $model->save(false);
                    $id=Yii::$app->db->getLastInsertId();
                    $modelEmailTempField = new SettingEmailTemplateFields();
                    $modelEmailTempField->template_id = $data->id;
                    $modelEmailTempField->field_id = $id;
                    $modelEmailTempField->display =  $cal_model->calculation_name;
                    $modelEmailTempField->is_default = 0;
                    $modelEmailTempField->save(false);
                }
            }
           return 'OK';     
        }
        return $this->renderAjax('calculation-field',[
            'fieldcalculationselectList'=>$fieldcalculationselectList
        ]);        
     }
     
     public function actionInstructionField(){
        $post_data=Yii::$app->request->post();
        $data = SettingsEmailTemplate::find()->where(['email_sort'=>$post_data['email_sort']])->one();
        if(isset($post_data['element_pkid']) && $post_data['element_pkid']!="") {
            foreach (explode(',', $post_data['element_pkid']) as $ele_id) {
                $fb_model=FormBuilder::findOne($ele_id);
                 if(SettingsEmailFields::find()->where(['form_builder_id'=>$ele_id,'field_type'=>2])->count()){
                    $id=SettingsEmailFields::find()->where(['form_builder_id'=>$ele_id,'field_type'=>2])->one()->id;
                    if(SettingEmailTemplateFields::find()->where(['template_id'=>$data->id,'field_id'=>$id])->count()==0){
                        $modelEmailTempField = new SettingEmailTemplateFields();
                        $modelEmailTempField->template_id = $data->id;
                        $modelEmailTempField->field_id = $id;
                        $modelEmailTempField->display =  $fb_model->element_label;
                        $modelEmailTempField->is_default = 0;
                        $modelEmailTempField->save(false);
                    }
                } else {
                    $model=new SettingsEmailFields();
                    $model->form_builder_id = $ele_id;
                    $model->display_name = $fb_model->element_id;
                    $model->field_value = '';
                    $model->origination = 'Instruction Data';
                    $model->field_type = 2;
                    $model->save(false);
                    $id=Yii::$app->db->getLastInsertId();
                    $modelEmailTempField = new SettingEmailTemplateFields();
                    $modelEmailTempField->template_id = $data->id;
                    $modelEmailTempField->field_id = $id;
                    $modelEmailTempField->display =  $fb_model->element_label;
                    $modelEmailTempField->is_default = 0;
                    $modelEmailTempField->save(false);
                }
            }
        }
        return 'OK';
     }
     
     public function actionDataField(){
        $post_data=Yii::$app->request->post();
        $data = SettingsEmailTemplate::find()->where(['email_sort'=>$post_data['email_sort']])->one();
        if(isset($post_data['element_pkid']) && $post_data['element_pkid']!=""){
            foreach (explode(',', $post_data['element_pkid']) as $ele_id) {
                $fb_model=FormBuilder::findOne($ele_id);
                 if(SettingsEmailFields::find()->where(['form_builder_id'=>$ele_id,'field_type'=>3])->count()){
                    $id=SettingsEmailFields::find()->where(['form_builder_id'=>$ele_id,'field_type'=>3])->one()->id;
                     if(SettingEmailTemplateFields::find()->where(['template_id'=>$data->id,'field_id'=>$id])->count()==0){
                        $modelEmailTempField = new SettingEmailTemplateFields();
                        $modelEmailTempField->template_id = $data->id;
                        $modelEmailTempField->field_id = $id;
                        $modelEmailTempField->display =  $fb_model->element_label;
                        $modelEmailTempField->is_default = 0;
                        $modelEmailTempField->save(false);
                    }   
                }else{
                    $model=new SettingsEmailFields();
                    $model->form_builder_id = $ele_id;
                    $model->display_name = $fb_model->element_id;
                    $model->field_value = '';
                    $model->origination = 'Data Form';
                    $model->field_type = 3;
                    $model->save(false);
                    $id=Yii::$app->db->getLastInsertId();
                    $modelEmailTempField = new SettingEmailTemplateFields();
                    $modelEmailTempField->template_id = $data->id;
                    $modelEmailTempField->field_id = $id;
                    $modelEmailTempField->display =  $fb_model->element_label;
                    $modelEmailTempField->is_default = 0;
                    $modelEmailTempField->save(false);
                }
            }
        }
        return 'OK';
     }
     public function actionUpdateTempField(){
        $temp_id = Yii::$app->request->get('temp_id',0);
        $field_id = Yii::$app->request->get('field_id',0);
        $model=SettingsEmailFields::findOne($field_id);
        $modelTemp=SettingEmailTemplateFields::find()->where(['id'=>$temp_id,'field_id'=>$field_id])->one();
        $model->display = $modelTemp->display;
        $data = SettingsEmailTemplate::findOne($modelTemp->template_id);
        if(Yii::$app->request->post()){
            $post_data=Yii::$app->request->post();
            $model->load(Yii::$app->request->post());
            $model->save(false);
            $modelTemp->display =$post_data['SettingsEmailFields']['display'];
            $modelTemp->save(false);
            return 'OK';
        }
        return $this->renderPartial('update-temp-field',['temp_id'=>$temp_id,'field_id'=>$field_id,'model'=>$model,'modelTemp'=>$modelTemp,'data'=>$data]);
    }
    public function actionDeleteTempField(){
        $temp_id = Yii::$app->request->get('temp_id',0);
        $field_id = Yii::$app->request->get('field_id',0);
        $modelTemp=SettingEmailTemplateFields::find()->where(['id'=>$temp_id,'field_id'=>$field_id])->one();
        $template_id = $modelTemp->template_id;
        $modelTemp->delete();
        if(SettingEmailTemplateFields::find()->where('template_id NOT IN ('.$template_id.') AND  field_id ='.$field_id)->count() == 0){
            $model=SettingsEmailFields::findOne($field_id);
            $model->delete();
        }
        return 'OK';
    }
    public function actionAddTableField()
    {
        $post_data = Yii::$app->request->post();
        //echo "<pre>",print_r($post_data),"</pre>";die;
        if(isset($post_data['column_field_name'])){
            $data = SettingsEmailTemplate::find()->where(['email_sort'=>$post_data['temp_sort']])->one();
            foreach($post_data['column_field_name'] as $field_id){
                $modelReprtfield=ReportsFields::findOne($field_id);
                $field_value = $modelReprtfield->reportsTables->table_name.'.'.$modelReprtfield->field_name;
                if(SettingsEmailFields::find()->where(['report_field_id'=>$field_id,'field_type'=>0])->count()){
                    $id=SettingsEmailFields::find()->where(['report_field_id'=>$field_id,'field_type'=>0])->one()->id;
                    if(SettingEmailTemplateFields::find()->where(['template_id'=>$data->id,'field_id'=>$id])->count()==0) {
                        $modelEmailTempField = new SettingEmailTemplateFields();
                        $modelEmailTempField->template_id = $data->id;
                        $modelEmailTempField->field_id = $id;
                        $modelEmailTempField->display = $modelReprtfield->field_display_name;
                        $modelEmailTempField->is_default = 0; 
                        $modelEmailTempField->save(false);    
                    }
                } else {
                    $model=new SettingsEmailFields();
                    $model->report_field_id = $field_id;
                    $model->display_name = "[".$modelReprtfield->reportsTables->table_name.'.'.$modelReprtfield->field_name."]";
                    $model->field_value = $modelReprtfield->reportsTables->table_name.'.'.$modelReprtfield->field_name;
                    $model->origination = $modelReprtfield->reportsTables->table_name;
                    $model->field_type = 0;
                    $model->save(false);
                    $id=Yii::$app->db->getLastInsertId();
                    $modelEmailTempField = new SettingEmailTemplateFields();
                    $modelEmailTempField->template_id = $data->id;
                    $modelEmailTempField->field_id = $id;
                    $modelEmailTempField->display =  $modelReprtfield->field_display_name;
                    $modelEmailTempField->is_default = 0;
                    $modelEmailTempField->save(false);
                }
            }
            return 'OK';
        }
        
    }
     
     /**
      * Setting for SLA Business Hours and Holiday
      */
     public function actionSlabusinesshours() {

		if(Yii::$app->request->post()){
     		$teamserviceSlaBusiness = Yii::$app->request->post('TeamserviceSlaBusinessHours');
     		$teamserviceSlaHolidays = Yii::$app->request->post('TeamserviceSlaHolidays');
			
     		//TeamserviceSlaBusinessHours::deleteAll();
     		TeamserviceSlaHolidays::deleteAll();
			$error = 0;
			$workingHours = 0;
			$workingMinutes = 0;
     		$BusinessHours = TeamserviceSlaBusinessHours::find()->one();
			if(empty($BusinessHours)){
				$BusinessHours = new TeamserviceSlaBusinessHours();
			}
     		$start_time = $teamserviceSlaBusiness['start_time'];
     		$end_time =  $teamserviceSlaBusiness['end_time'];
			if($start_time!='' && $end_time!=''){
				$time1 = new \DateTime("$start_time:00");
				$time2 = new \DateTime("$end_time:00");
				$interval = $time1->diff($time2);
				$workingHours = $interval->format('%H');
				$workingMinutes = $interval->format('%i');
			}
			if($workingMinutes == 30){
				$workingHours += 0.5;
			}
     		$BusinessHours->start_time = $start_time;
     		$BusinessHours->end_time = $end_time;
     		$BusinessHours->workinghours = $workingHours;
			 if($start_time=='00:00' && $end_time=='24:00'){
				 $BusinessHours->workinghours = 24;
			 }
     		$BusinessHours->workingdays = "";
     		if($teamserviceSlaBusiness['workingdays'] !== null && $teamserviceSlaBusiness['workingdays'] != ''){
     			$BusinessHours->workingdays = json_encode(explode(',',$teamserviceSlaBusiness['workingdays']));
     		}
     		if(!$BusinessHours->save()){
				 $error = 1;
     			//echo "<prE>",print_r($BusinessHours->getErrors()),"/<pre>";
				//die;
     		}
     		
     		if(isset($teamserviceSlaHolidays['holidaydate']) && !empty($teamserviceSlaHolidays['holidaydate'])){
     			foreach ($teamserviceSlaHolidays['holidaydate'] as $key=>$holidaydate){
     				$BusinessHolidays = new TeamserviceSlaHolidays();
     				$BusinessHolidays->holidaydate = $teamserviceSlaHolidays['holidaydate'][$key];
     				$BusinessHolidays->holiday = $teamserviceSlaHolidays['holiday'][$key];
     				$BusinessHolidays->save();
     			}
     		}
			if($error == 1){
				$SlaHolidaysModel   = TeamserviceSlaHolidays::find()->all();     		
     			$slaHoliday_length 	= (new User)->getTableFieldLimit((new TeamserviceSlaHolidays)->tableSchema->name);
				return $this->renderAjax('businessHours',[
					'BusinessHoursModel'=>$BusinessHours,
					'SlaHolidaysModel'	=>$SlaHolidaysModel,
					'slaHoliday_length' => $slaHoliday_length
				]);
			} else {
				$session = Yii::$app->session;
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

				$businessHolidays =  ArrayHelper::map(TeamserviceSlaHolidays::find()->select(['id','holidaydate'])->all(),'id','holidaydate');
				$session['businessHolidays'] = $businessHolidays;

				if(Yii::$app->db->driverName == 'mysql'){
					$query = "UPDATE tbl_task_instruct 
					INNER JOIN (
						SELECT id, SUBSTRING_INDEX(SUBSTRING_INDEX(updated_duedatetime, '|', 1), '|', -1) as updated_duedate, SUBSTRING_INDEX(SUBSTRING_INDEX(updated_duedatetime, '|', 2), '|', -1) as updated_duetime FROM (
							SELECT tbl_task_instruct.id, task_id, task_duedate, task_timedue, tbl_options.timezone_offset, getUpdatedDueDateTime(timezone_offset, task_duedate, task_timedue) as updated_duedatetime 
							FROM tbl_task_instruct
							INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct.task_id
							INNER JOIN tbl_options ON tbl_options.user_id = tbl_task_instruct.created_by
							WHERE task_status <> 4
						) as A
					) as B ON B.id = tbl_task_instruct.id
					SET task_duedate=B.updated_duedate, task_timedue=B.updated_duetime";
				}else{
					$query = "UPDATE U 
					SET U.task_duedate=B.updated_duedate, U.task_timedue=B.updated_duetime
					FROM tbl_task_instruct as U
					INNER JOIN (
						SELECT id, SUBSTRING(updated_duedatetime, 1,CHARINDEX('|', updated_duedatetime)-1) as updated_duedate, SUBSTRING(updated_duedatetime, CHARINDEX('|', updated_duedatetime)+1,CHARINDEX('|', updated_duedatetime)-1) as updated_duetime FROM (
							SELECT tbl_task_instruct.id, task_id, task_duedate, task_timedue, tbl_options.timezone_offset, dbo.getUpdatedDueDateTime(timezone_offset, task_duedate, task_timedue) as updated_duedatetime 
							FROM tbl_task_instruct
							INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct.task_id
							INNER JOIN tbl_options ON tbl_options.user_id = tbl_task_instruct.created_by
							WHERE task_status <> 4
						) as A
					) as B ON B.id = U.id";
				}
				//Yii::$app->db->createCommand($query)->execute();

     			return "OK";
			}
     	}else{
     		$BusinessHoursModel = new TeamserviceSlaBusinessHours();
     		$datatsbh = TeamserviceSlaBusinessHours::find()->one();
     		if(isset($datatsbh->id) && $datatsbh->id!== null)$BusinessHoursModel = TeamserviceSlaBusinessHours::findOne($datatsbh->id);
     		$SlaHolidaysModel   = TeamserviceSlaHolidays::find()->all();     		
     		$slaHoliday_length 	= (new User)->getTableFieldLimit((new TeamserviceSlaHolidays)->tableSchema->name);
     		return $this->renderAjax('SLABusinessHours',[
				'BusinessHoursModel'=>$BusinessHoursModel,
				'SlaHolidaysModel'	=>$SlaHolidaysModel,
				'slaHoliday_length' => $slaHoliday_length
			]);
     	}
     }
     
     /**
      * Setting for System ideal Session Timeout
      */
     public function actionSessiontimeoutsetting(){
     	$model = new Settings();
    	$data = Settings::find()->where(['field'=>'session_timeout'])->one();
    	$user_id = Yii::$app->user->identity->id;
    	if(isset($data->id) && $data->id!== null)
    		$model = Settings::findOne($data->id);
    	
    	if (Yii::$app->request->post()) {
    		$Settings = Yii::$app->request->post('Settings');
    		$times = Yii::$app->request->post('times');
    		$model->field=$Settings['field'];
    		if($Settings['fieldvalue']==1){
    			$model->fieldvalue=1;
    			Options::updateAll(['session_timeout'=>1200]);
    		}else{
    			Options::updateAll(['session_timeout'=>null]);
    			$model->fieldvalue=$times;
    		}
    		$model->save(false);
    		return 'OK';//$this->redirect(['view', 'id' => $model->id]);
    	} else {
    		return $this->renderAjax('Sessiontimeoutsetting', [
    			'model' => $model,
    		]);
    	}
     }
 	/**
    * System Maintenance
	*/
     public function actionSystemMaintenance() {
		 $data = Settings::find()->where(['field'=>'yii_debug'])->one();
		 $model = new Settings();
		 $sql="SELECT sml1.*,tbl_user.usr_first_name,tbl_user.usr_lastname FROM tbl_system_maintenance_logs sml1 INNER JOIN tbl_user ON tbl_user.id=sml1.created_by LEFT JOIN tbl_system_maintenance_logs sml2 ON (sml1.action = sml2.action AND sml1.id < sml2.id) WHERE sml2.id IS NULL";
		 $system_log = Yii::$app->db->createCommand($sql)->queryAll();
		 $session_size=(new SystemMaintenanceLogs)->folderSize(Yii::$app->basePath.'/runtime/session');
		 $session_sizeformated=(new SystemMaintenanceLogs)->format_size($session_size);
		 $log_size=(new SystemMaintenanceLogs)->folderSize(Yii::$app->basePath.'/runtime/logs');
		 $log_sizeformated=(new SystemMaintenanceLogs)->format_size($log_size);
		 $message="";
		 if(isset($data->id) && $data->id!== null)
    		$model = Settings::findOne($data->id);
		 if (Yii::$app->request->post()) {
			$data=Yii::$app->request->post();
			if(isset($data['clear-assets-directory'])) {
				$dirs = array_filter(glob(Yii::$app->basePath.'/assets/*'), 'is_dir');
				if(!empty($dirs)) {
					foreach($dirs as $k=>$apath) {
						(new SystemMaintenanceLogs)->rrmdir($apath);
					}
				}
				(new SystemMaintenanceLogs())->addLogs('clear-assets-directory');
			}
			if(isset($data['clear-db-cache'])) {
				if(Yii::$app->cache->flush()){
					$message="S";
				}else{
					$message="F";
				}
				
				//yii\caching\Cache::flush();
				(new SystemMaintenanceLogs())->addLogs('clear-db-cache');
			}
			if(isset($data['clear-all-sessions'])) {
				$files = glob(Yii::$app->basePath.'/runtime/session/*');
				if(!empty($files)) {
					foreach($files as $k=>$sfpath) {
						@unlink($sfpath); 
					}
				}
				(new SystemMaintenanceLogs())->addLogs('clear-all-sessions');
			}
			if(isset($data['clear-garbage-sessions'])) {
				$files = glob(Yii::$app->basePath.'/runtime/session/*');
				if(!empty($files)) {
					foreach($files as $k=>$sfpath) {
						if( date ("Y-m-d", filemtime($sfpath)) !== date ("Y-m-d") ) {
							@unlink($sfpath); 
						}
					}
				}
				(new SystemMaintenanceLogs())->addLogs('clear-garbage-sessions');
			}
			if(isset($data['clear-app-logs'])) {
				$files = glob(Yii::$app->basePath.'/runtime/logs/*');
				if(!empty($files)) {
					foreach($files as $k=>$lfpath) {
						@unlink($lfpath); 
					}
				}
				(new SystemMaintenanceLogs())->addLogs('clear-app-logs');
			}
			if(isset($data['enable-debug'])) {
				if($model->fieldvalue!=$data['enable-debug']){
					$model->fieldvalue=$data['enable-debug'];
					$model->field='yii_debug';
					$model->save();
					(new SystemMaintenanceLogs())->addLogs('enable-debug');
				}
			}
			return 'OK'.$message;
		 } else {
			 return $this->renderAjax('system-maintenance', [
    			'model' => $model,
				'system_log'=>$system_log,
				'session_sizeformated'=>$session_sizeformated,
				'log_sizeformated'=>$log_sizeformated

    		]);
		 }
	 }
	  public function actionBackupLogs(){
		  $zip = new \ZipArchive();
		  $test = tempnam(sys_get_temp_dir(), rand(0, 999999999) . '.zip');
		  $res = $zip->open($test, \ZipArchive::CREATE);
		  if ($res) {
				$files = glob(Yii::$app->basePath.'/runtime/logs/*');
				if(!empty($files)) {
					$i=0;
					foreach($files as $k=>$lfpath) {
					$name= pathinfo($lfpath, PATHINFO_FILENAME);
					if($i > 0){$name=$name.'.'.$i;}
					$zip->addFile($lfpath,$name);
					$i++;
					}
				}
				$zip->close();
				(new SystemMaintenanceLogs())->addLogs('backup-logs');
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename=applogs_' . date('Ymd_His'). '.zip');
				readfile($test);
			} else {
				echo 'zip error';
				die;
			}
			return ;
	  }
	 
    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
    	if (($model = Settings::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Finds the EvidenceCustodiansForms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EvidenceCustodiansForms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCustodiansFormModel($id)
    {
    	if (($model = EvidenceCustodiansForms::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Finds the EvidenceCustodiansForms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EvidenceCustodiansForms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCustodiansFormsModel($id)
    {
    	if (($model = EvidenceCustodiansForms::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    /**
     * Finds the Emailsettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Emailsettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findEmailsettingsModel($id)
    {
    	if (($model = Emailsettings::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    /**
     * Priority Team Project
     * @param integer $keylist
     * @return mixed
     */
    public function actionProjectprioriry(){
    	$this->enableCsrfValidation = false;
    	$sort_ids = explode(",",Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
			$sqls = array();
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
    				$model = PriorityProject::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->priority_order = $order + 1;
    				$priority = $model->priority;
    				$model->save(false);
    				$sqls[]= "UPDATE tbl_priority_project SET priority_order=".($order + 1)." WHERE priority ='".$priority."' AND  remove = 1";
    			}
    			$transaction->commit();
    			if(!empty($sqls)){
					foreach($sqls as  $sql){
						Yii::$app->db->createCommand($sql)->execute();
					}
				}
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
    }
    
    /**
     * Priority Team Project
     * @param integer $keylist
     * @return mixed
     */
    public function actionPriorityteamsort()
    {
    	$this->enableCsrfValidation = false;
    	$sort_ids = explode(",",Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
					$sql = "";
    				$model = PriorityTeamLoc::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->priority_order = $order + 1;
    				$model->save(false);
    				//$priority = $model->tasks_priority_name;
    				//$sql = "UPDATE tbl_priority_team SET priority_order = ".($order + 1)." WHERE tasks_priority_name ='".$priority."' AND  remove = 1";
    				//Yii::$app->db->createCommand($sql)->execute();
    			}
    			$transaction->commit();
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
    }
    
    /**
     * Get Existing Form Fields By Types [Types : Custodian / Workflow]
     * @param string $formtype
     * @return mixed
     */    
    public function actionGetFieldsByTypes()
    {
		$type = Yii::$app->request->get('formtype','custodianform');
		$element_list = Yii::$app->request->get('element_list',[]);
        $foremail_teamp=Yii::$app->request->get('email_teamp','');
		$sql_element = '';
		if(!empty($element_list)){
			$element_list_str = implode("','",$element_list);
			$sql_element = " AND element_id NOT IN ('".$element_list_str."')";
		}
		$data = array();
		if($type == 'custodianform'){
			//$data['custodian_form'] = ArrayHelper::map(Formbuilder::find()->select(['id', 'element_label'])->where(['form_type' => 3])->andWhere("element_label IS NOT NULL AND element_label != '' AND remove=0")->andWhere(['not in','element_id',$element_list])->orderBy('element_label ASC')->asArray()->all(),'id','element_label');
			$query = "SELECT tbl_form_builder.id, CONCAT(tbl_evidence_custodians_forms.Form_name,' - ',element_label) as element_label FROM tbl_form_builder INNER JOIN tbl_evidence_custodians_forms ON tbl_evidence_custodians_forms.id=tbl_form_builder.formref_id 
			WHERE form_type=3 AND element_label IS NOT NULL AND element_label != '' AND tbl_form_builder.remove=0 $sql_element ORDER BY element_label";
			$result_data1 = Yii::$app->db->createCommand($query)->queryAll();
			$data['custodian_form'] = array();
			if(!empty($result_data1)){
				foreach($result_data1 as $data1){
					$data['custodian_form'][$data1['id']] = html_entity_decode($data1['element_label']);
				}
			}
		} else {
			//$data['instruction_form'] = ArrayHelper::map(Formbuilder::find()->select(['id', 'element_label'])->where(['form_type' => 1])->andWhere("element_label IS NOT NULL AND element_label != '' AND remove=0")->andWhere(['not in','element_id',$element_list])->orderBy('element_label ASC')->asArray()->all(),'id','element_label');
			//$data['data_form'] = ArrayHelper::map(Formbuilder::find()->select(['id', 'element_label'])->where(['form_type' => 2])->andWhere("element_label IS NOT NULL AND element_label != '' AND remove=0")->andWhere(['not in','element_id',$element_list])->orderBy('element_label ASC')->asArray()->all(),'id','element_label');
		
			$query = "SELECT tbl_form_builder.id, CONCAT(tbl_teamservice.service_name, ' - ',tbl_servicetask.service_task,' - ',element_label) as element_label FROM tbl_form_builder INNER JOIN tbl_servicetask ON tbl_servicetask.id=tbl_form_builder.formref_id 
			INNER JOIN tbl_teamservice ON tbl_teamservice.id=tbl_servicetask.teamservice_id
			WHERE form_type=1 AND element_label IS NOT NULL AND element_label != '' AND tbl_form_builder.remove=0 $sql_element ORDER BY element_label";
			$result_data1 = Yii::$app->db->createCommand($query)->queryAll();
			$data['instruction_form'] = array();
			if(!empty($result_data1)){
				foreach($result_data1 as $data1){
					$data['instruction_form'][$data1['id']] = html_entity_decode($data1['element_label']);
				}
			}
			
			$query = "SELECT tbl_form_builder.id, CONCAT(tbl_teamservice.service_name, ' - ',tbl_servicetask.service_task,' - ',element_label) as element_label FROM tbl_form_builder INNER JOIN tbl_servicetask ON tbl_servicetask.id=tbl_form_builder.formref_id 
			INNER JOIN tbl_teamservice ON tbl_teamservice.id=tbl_servicetask.teamservice_id 
			WHERE form_type=2 AND element_label IS NOT NULL AND element_label != '' AND tbl_form_builder.remove=0 $sql_element ORDER BY element_label";
			$result_data2 = Yii::$app->db->createCommand($query)->queryAll();
			$data['data_form'] = array();
			if(!empty($result_data1)){
				foreach($result_data2 as $data2){
					$data['data_form'][$data2['id']] = html_entity_decode($data2['element_label']);
				}
			}
            if(isset($foremail_teamp) && $foremail_teamp=='instruction'){
                unset($data['data_form']);
            }
            if(isset($foremail_teamp) && $foremail_teamp=='data'){
                unset($data['instruction_form']);
            }
		} 
		
		return $this->renderAjax('getFieldsByTypes', [
			'data' => $data,
			'formtype' => $formtype
		]);
	}
	
	/**
     * Get Element By Field Id
     * @param string $element_id
     * @return mixed
     */    
    public function actionGetElementByFieldId()
    {
		$id = Yii::$app->request->get('element_pkid',0);
		$formbuilder_data = new Formbuilder;
		$formbuilder_data = $formbuilder_data->getFromData($id,0,'DESC','','','copyelement');
		//echo "<pre>",print_r($formbuilder_data),"</pre>";
		return $this->renderAjax('getElementByFieldId', [
    		'formbuilder_data' => $formbuilder_data,
    	]);
    }
}
