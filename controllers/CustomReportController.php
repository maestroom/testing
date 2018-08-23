<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;

use app\models\ReportsReportType;
use app\models\ReportsReportFormat;
use app\models\ReportsChartFormat;
use app\models\ReportsReportTypeFields;
use app\models\ReportsReportTypeFieldCalculation;
use app\models\ReportsFieldCalculations;
use app\models\ReportsFieldOperators;
use app\models\ReportsChartFormatDisplayBy;
use app\models\ReportsChartFormatDisplayLogic;
use app\models\ReportsReportTypeSql;
use app\models\ReportsLookups;
use app\models\ReportsFieldsRelationships;
use app\models\Options;
use app\models\Teamservice;
use app\models\TeamserviceLocs;
use app\models\User;
use app\models\Client;
use app\models\Settings;
use app\models\ClientCase;
use app\models\ProjectSecurity;
use app\models\ReportsUserSaved;
use app\models\ReportsUserSavedFields;
use app\models\ReportsUserSavedFieldsLogic;
use app\models\ReportsUserSavedSharedWith;
use app\models\ReportsUserSavedFilterClientCase;
use app\models\ReportsUserSavedFilterTeamserviceLoc;
use app\models\Role;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Shared_Font;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Helper_HTML;

class CustomReportController extends \yii\web\Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			if (!(new User)->checkAccess(11)) {
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
			}

		return parent::beforeAction($action);
	}
    public function actionIndex()
    {
    	$this->layout = 'report';
    	$id = Yii::$app->request->get('id',0);
    	$flag = Yii::$app->request->get('flag','');
    	$model = new ReportsUserSaved();
    	$fields = array();
    	if($id !=0){
			$model  = ReportsUserSaved::findOne($id);
			$fields = ReportsUserSavedFields::find()->where('saved_report_id='.$id.' AND is_deleted=0')->all();
			$userId=Yii::$app->user->identity->id;
			$roleId=Yii::$app->user->identity->role_id;
			$query = ReportsUserSaved::find();
			if($roleId!=0){
				$rpaccess="SELECT DISTINCT saved_report_id FROM (
				(
                                    SELECT saved_report_id FROM tbl_reports_user_saved_shared_with
                                    INNER JOIN tbl_project_security on tbl_project_security.client_case_id=tbl_reports_user_saved_shared_with.client_case_id
                                    AND tbl_project_security.user_id=$userId
				)
				UNION ALL
				(
                                    SELECT saved_report_id FROM tbl_reports_user_saved_shared_with WHERE user_id IN ($userId)
				)
				UNION ALL
				(
                                    SELECT saved_report_id FROM tbl_reports_user_saved_shared_with WHERE role_id IN ({$roleId})
				)
				UNION ALL
				(
                                    SELECT saved_report_id FROM tbl_reports_user_saved_shared_with
                                    INNER JOIN tbl_project_security on tbl_project_security.team_id=tbl_reports_user_saved_shared_with.team_id
                                    AND tbl_project_security.team_loc = tbl_reports_user_saved_shared_with.team_loc
                                    AND tbl_project_security.user_id=$userId where tbl_reports_user_saved_shared_with.team_id !=0
				)
				) as RPACCESS";
				$where=" ((created_by=$userId) OR id IN ($rpaccess)) ";
				$query->where($where);
			}
			$query->andWhere('id = '.$id);
			if($query->count() == 0){
				throw new NotFoundHttpException("You don't have access to this report.");
			}

		}
    	$modeReportType = ArrayHelper::map(ReportsReportType::find()->orderBy('report_type')->all(),'id','report_type');

        $modeReportFormat = array();
    	$modeReportFormat = ArrayHelper::map(ReportsReportFormat::find()->all(),'id','report_format');
    	$order = "CASE WHEN chart_format='Bar' END";
		$modeReportsChartFormat = ArrayHelper::map(ReportsChartFormat::find()->orderBy('format_order')->all(),'id','chart_format');
		$modelReportFields = new ReportsReportTypeFields;
		$userId=Yii::$app->user->identity->id;
		$roleId=Yii::$app->user->identity->role_id;
		$condition = "client_id!=0 AND team_id=0";
		if ($roleId != 0) {
			$condition = "user_id=$userId AND team_id=0";
		}
		/*$client_data = ArrayHelper::map(Client::find()->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) { $query->select(['client_id']); }])->select(['tbl_client.id', 'client_name'])->where($condition)->all(),'id', 'client_name');
		foreach($client_data as $client_id => $client_name){
			if ($roleId != 0) {
				$case_data = ProjectSecurity::find()->select(['client_case_id'])->where('client_id='.$client_id.' AND user_id='.$userId.' AND team_id=0');
				$caseList = ArrayHelper::map(ClientCase::find()->select(['id', 'case_name'])->where(['in', 'id', $case_data])->andWhere('is_close=0')->orderBy('case_name')->asArray()->all(),'id', 'case_name');
			} else {
				$caseList = ArrayHelper::map(ClientCase::find()->select(['id', 'case_name'])->where(['client_id' => $client_id,'is_close'=>0])->orderBy('case_name')->asArray()->all(),'id', 'case_name');
			}
			$client_case_data[$client_id] = $caseList;
		}
		$teamserviceList = array();
		if ($roleId != 0) {
			$team_data = ProjectSecurity::find()->where('user_id='.$userId.' AND team_id!=0 AND team_loc!=0');
			$team_locs_data = $team_data->select(['team_id','team_loc'])->all();
			$team_loc_data = array();
			if(!empty($team_locs_data)){
				foreach($team_locs_data as $teamlocdata){
					$team_loc_data[$teamlocdata->team_id][] = $teamlocdata->team_loc;
				}
			}

			$team_data->select(['team_id']);
			$teamserviceList = Teamservice::find()->innerJoinWith([
				'team'=>function(\yii\db\ActiveQuery $query){
					$query->select(['tbl_team.id','tbl_team.team_name']);
				},
				'teamserviceLocs'
			])->where(['in','teamid',$team_data])
			->select(['tbl_teamservice.id','tbl_teamservice.service_name','tbl_teamservice.teamid'])->all();
		} else {
			$teamserviceList = Teamservice::find()->innerJoinWith([
				'team'=>function(\yii\db\ActiveQuery $query){
					$query->select(['tbl_team.id','tbl_team.team_name']);
				},
				'teamserviceLocs'
			])->select(['tbl_teamservice.id','tbl_teamservice.service_name','tbl_teamservice.teamid'])->all();
		}*/
		return $this->render('index',[
			/*'teamserviceList'=>$teamserviceList,
			'team_loc_data'=>$team_loc_data,
			'client_data'=>$client_data,
			'client_case_data'=>$client_case_data, */
			'model'=>$model,
			'modeReportType'=>$modeReportType,
			'modeReportFormat'=>$modeReportFormat,
			'modeReportsChartFormat'=>$modeReportsChartFormat,
			'modelReportFields' => $modelReportFields,
			'id'=>$id,
			'flag'=>$flag,
			'fields'=>$fields
		]);
    }
    public function actionSummaryReport()
    {
		$post_data = Yii::$app->request->post();
		$modeReportsChartFormat = ReportsChartFormat::find()->orderBy('format_order')->where(['id'=>$post_data['chart_format_id']])->one();
		$model = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
		$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']])
			->innerJoinWith([
				'reportsField' => function(\yii\db\ActiveQuery $query2){
					$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
					}]);
				}
			])->all();
		//echo "<pre>",print_r($records),"</pre>";
		$reportTypeFields = ArrayHelper::map($records,'id',function($model, $defaultValue) {
			return $model->reportsField->reportsFieldType->field_type;
		});
		$field_disply_with_table=[];
		foreach($post_data['fielddisp'] as $id=>$display_name){
			$table_name=substr($post_data['fieldval'][$id],0,strpos($post_data['fieldval'][$id],"."));
			$table_name=str_replace("tbl_","",$table_name);
			$field_disply_with_table[$id]=ucwords($table_name)."=>".$display_name;
		}
		//echo "<pre>",print_r($id_with_name);
		//print_r($post_data);
		//echo "</pre>";
		if(isset($model->sp_name) && trim($model->sp_name)=='MediaOut'){
				$userId=Yii::$app->user->identity->id;
				if (Yii::$app->db->driverName == 'mysql') {
					$selectedb=Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();
		   	  	} else {
					$selectedb=Yii::$app->db->createCommand("SELECT db_name()")->queryScalar();
				}
				if (Yii::$app->db->driverName == 'mysql') {
					$sql_columns= "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = '".$selectedb."' and table_name = 'report_view_".$userId."' AND COLUMN_NAME < 'task_unit_id'";
				}else{
					$sql_columns= "SELECT COLUMN_NAME FROM information_schema.columns WHERE  table_name = 'report_view_".$userId."' AND COLUMN_NAME < 'task_unit_id'  ORDER BY ORDINAL_POSITION";
				}

				$fielddisp_column =\Yii::$app->db->createCommand($sql_columns)->queryAll(\PDO::FETCH_NUM);
				if(!empty($fielddisp_column)){
					$post_data['fielddisp']['task_unit_id']='task_unit_id';
					$post_data['fieldval']['task_unit_id']='task_unit_id';
					foreach($fielddisp_column as $nclumn){
						$post_data['fielddisp'][$nclumn[0]]=$nclumn[0];
						$post_data['fieldval'][$nclumn[0]]=$nclumn[0];
						$field_disply_with_table[$nclumn[0]]="TotalMediaOut(".ucwords($nclumn[0]).")";
					}
				}
		}
		if (Yii::$app->db->driverName == 'mysql') {
			$sql="SELECT COLUMN_NAME as name,DATA_TYPE as type FROM information_schema.columns WHERE table_name='report_view_".Yii::$app->user->identity->id."'";
			$fieldNameType=ArrayHelper::map(Yii::$app->db->createCommand($sql)->queryAll(), 'name','type');
		}else{
			$sql="SELECT c.name as name,t.name as type FROM   sys.columns c JOIN sys.types t ON t.user_type_id = c.user_type_id AND t.system_type_id = c.system_type_id WHERE  object_id = OBJECT_ID('report_view_".Yii::$app->user->identity->id."')";
			$fieldNameType=ArrayHelper::map(Yii::$app->db->createCommand($sql)->queryAll(), 'name','type');
		}
		return $this->renderAjax('_step_5',['fieldNameType'=>$fieldNameType,'post_data'=>$post_data,'modeReportsChartFormat'=>$modeReportsChartFormat,'reportTypeFields'=>$reportTypeFields,'field_disply_with_table'=>$field_disply_with_table]);
	}

    public function actionGetdatetype()
    {
    	$params = Yii::$app->request->post('depdrop_parents',0);
		$field_id = $params[0];
		if(isset($field_id) && $field_id!=0 && $field_id!="")
		{
			$fieldList = ReportsReportTypeFields::find()->select(['tbl_reports_report_type_fields.id',"CONCAT(tbl_reports_report_type_fields.table_display_name,' => ',tbl_reports_report_type_fields.field_display_name) as name"])
			->innerJoinWith(['reportsFieldType' => function(yii\db\ActiveQuery $query1){
				$query1->joinWith(['reportsFieldTypeTheme'=>function(\yii\db\ActiveQuery $query){
					$query->where(['like','tbl_reports_field_type_theme.field_type_theme','Date']);
				}]);
			}])
			->where(['tbl_reports_report_type_fields.reporttype_id'=>$field_id])->asArray()->all();
			//echo "<pre>",print_r($fieldList),"</pre>";
			if(!empty($fieldList)){
				array_unshift($fieldList,['id'=>'0','name'=>'','reportsFieldType'=>'']);
			}
			//echo "<pre>",print_r($fieldList),"</pre>";
			echo Json::encode(['output'=>$fieldList, 'selected'=>'']);
	    	return;
		}
    }


    /**
     * Get table & table field details Add Custom Report Filter
     * @return
     */
    public function actionGetTableFieldDetails()
    {
		$flag = Yii::$app->request->post('flag',0);
		$post_data=Yii::$app->request->post();
		if(Yii::$app->request->isAjax && !empty($post_data))
		{
			$report_type = Yii::$app->request->post('ReportsUserSaved');
			$reportTypeFieldsModel = ReportsReportTypeFields::find()
			->select(['tbl_reports_report_type_fields.id','table_name', 'table_display_name','field_name', 'field_display_name'])
			->joinWith(['reportsField'=>function(\yii\db\ActiveQuery $query){
				$query->joinWith(['reportsTables']);
			}])
			->where(['report_type_id' => $report_type['report_type_id']])->orderBy('tbl_reports_report_type_fields.id');
			$table_fields_detail = $reportTypeFieldsModel->asArray()->all();

			$new_val = array();
			$data_type = '';
			$newdisplay_name= array();
			$table_display_names = array();
			if(!empty($table_fields_detail)) {
				foreach($table_fields_detail as $key => $val){
					if($val['id']==$report_type_id['date_type_field_id']){
						$data_type = $val['table_name'].'.'.$val['field_name'];
					}
					$new_val[$val['table_name']][$val['id']] = $val['field_name'];
					$newdisplay_name[$val['table_name']][$val['id']] = $val['field_display_name'];
					$table_display_names[$val['table_name']] = $val['table_display_name'];
				}
			}
			// get calculated field list
			$table_calculation = ReportsFieldCalculations::find()->innerJoinWith([
				'reportsReportTypeFieldCalculation' => function(\yii\db\ActiveQuery $query) use($report_type){
					$query->where(['report_type_id'=>$report_type['report_type_id']]);
				}
			])->select(['tbl_reports_field_calculations.id','tbl_reports_field_calculations.calculation_name'])->all();
			if(!empty($table_calculation)){
				foreach($table_calculation as $key => $val){
					$new_val['Calculation'][$val->id] = $val->calculation_name;
				}
			}
			//echo "<pre>",print_r($new_val),"</pre>";
			$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$report_type['report_type_id']])
			->innerJoinWith([
				'reportsField' => function(\yii\db\ActiveQuery $query2) {
					$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
					}]);
				}
			])->all();
			//echo "<pre>",print_r($records),"</pre>";die;
			$reportTypeFields = ArrayHelper::map($records,'id',function($model, $defaultValue) {
				return $model->reportsField->reportsFieldType->field_type;
			});
			$reportTypeFields_conditions = ArrayHelper::map($records,'id','report_condition');
			//echo "<pre>",print_r($reportTypeFields),print_r($reportTypeFields_conditions),"</pre>";die;

			return $this->renderAjax('_avail_table_field_data', array('avail_table_fields' => $new_val, 'data_type' => $data_type, 'report_type' => $report_type, 'newdisplay_name'=>$newdisplay_name, 'table_display_names'=>$table_display_names,'reportTypeFields'=>$reportTypeFields,'reportTypeFields_conditions'=>$reportTypeFields_conditions));
		}
	}

    public function actionValidateSteps()
    {
    	$flag = Yii::$app->request->post('flag',0);
   		$model = new ReportsUserSaved();
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			if(isset($model->grid_line)){
				$model->grid_line=implode(",", $model->grid_line);
			}
    		return ActiveForm::validate($model);
    	} else {
    		return [];
    	}
	}


	/**
	 * Filter pop up option from custom report selected report field
	 * @return
	 */
	 public function actionSelectloadmore(){
		 $report_type_id = Yii::$app->request->get('report_type_id',0);
		 $field_id = Yii::$app->request->get('field_id',0);
		 $q = Yii::$app->request->get('q',0);
		 $getLookupsql=(new ReportsFieldsRelationships())->getFilterLookup($report_type_id,$field_id);
		 \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	 $out = ['results' => ['id' => '', 'text' => '']];
		 if(isset($getLookupsql['sql']) && $getLookupsql['sql']!="" && isset($getLookupsql['from']) && $getLookupsql['from']!="")
		 {
			/* Permission Related client */
			$sql = "SELECT {$getLookupsql['primary']} as id,{$getLookupsql['sql']} as name FROM {$getLookupsql['from']} where {$getLookupsql['sql']} like '%".$q."%' ORDER BY name";
			$lookupfinal_data = ArrayHelper::map(\Yii::$app->db->createCommand($sql)->queryAll(),"id","name");
			if(!empty($lookupfinal_data)){
				$out['results']=[];
				foreach($lookupfinal_data as $id=>$text){
					$out['results'][]=array('id' => $id, 'text' => $text);
				}
			}
			//$out['results'] = $lookupfinal_data;
		 }
		return $out;
	 }
	 public function actionFilterPopUpOption()
	 {
		 $id = Yii::$app->request->get('id',0);
		 $userId = Yii::$app->user->identity->id;
		 $report_type_id = Yii::$app->request->get('report_type_id',0);
		 $field_detail = ReportsReportTypeFields::findOne($id);
		 //echo "<pre>",print_r($field_detail);
		 $table_name=$field_detail->reportsField->reportsTables->table_name;
		 $field_name=$field_detail->reportsField->field_name;
		 $selectedloadmore="";

		 $lookupfinal_data=array();
		 $has_lookup=false;
		 $has_lookupsql=false;
		 $field_id=ReportsReportTypeFields::find()->where("tbl_reports_report_type_fields.id=$id AND tbl_reports_report_type_fields.report_type_id =$report_type_id")->one()->reports_fields_id;
		 $getLookupsql=(new ReportsFieldsRelationships())->getFilterLookup($report_type_id,$field_id);
		 $filter_data = Yii::$app->request->post('filter_value');
		 $filtervalue_selected=[];
		 if(!empty($filter_data))
		 {
		 	  $filter_value = array(); $result_data = array();
			  foreach($filter_data as $val)
			  {
				$filter_value = json_decode($val, true);
				if($filter_value['id']==$id)
					$result_data = $filter_value;
			  }
		 }
		 if(isset($getLookupsql['data']) && !empty($getLookupsql['data']))
		 {
			//die('here');
			$has_lookup = true;
			$lookupfinal_data=$getLookupsql['data'];
		 }
		 else if(isset($getLookupsql['sql']) && $getLookupsql['sql']!="" && isset($getLookupsql['from']) && $getLookupsql['from']!="")
		 {
			/* Permission Related client */
			//die('here123');
			$has_lookup = true;
			$has_lookupsql=true;
			if(!empty($filter_data)) {
				$selected_ids=0;
				if(count(explode(",",$result_data['operator_value'][$id])) > 1) {
					$selected_ids=explode(",",$result_data['operator_value'][$id]);
				} else {
					if(isset($result_data['operator_value'][$id]))
						$selected_ids=$result_data['operator_value'][$id];
					if(isset($result_data['operator_value'][0]))
						$selected_ids=$result_data['operator_value'][0];
				}
				//print_r($result_data);die;
				$selected_ids = "'".str_replace(",","','",$selected_ids)."'";
				$sql = "SELECT {$getLookupsql['primary']} as id,{$getLookupsql['sql']} as name FROM {$getLookupsql['from']} WHERE {$getLookupsql['primary']} IN ($selected_ids) ORDER BY name";
				$selected_lookupfinal_data = ArrayHelper::map(\Yii::$app->db->createCommand($sql)->queryAll(),"id","name");
				if(!empty($selected_lookupfinal_data)){
					$filtervalue_selected=$selected_lookupfinal_data;
					//echo "<pre>",print_r($filtervalue_selected),die;
				}
			}

			// print_r($selected_ids);die;

			//
			//$sql = "SELECT {$getLookupsql['primary']} as id,{$getLookupsql['sql']} as name FROM {$getLookupsql['from']} ORDER BY name";
			//$lookupfinal_data = ArrayHelper::map(\Yii::$app->db->createCommand($sql)->queryAll(),"id","name");
		 }
		 $check_has_lookup = $field_detail->reportsField->reportsFieldsLookupRelationships;


		 $model = new ReportsFieldOperators();
		 $filter_details = $this->getReportFieldsOperatorDetails($id,$has_lookup);
		 if(!isset($lookupfinal_data[0])) {
			if(Yii::$app->db->driverName == 'mysql') {
				$lookupfinal_data[0]='NULL';
			}
		 }
		 if(!empty($lookupfinal_data)) {
			 foreach($lookupfinal_data as $lookupfinal_data_key=>$lookupfinal_data_value) {
				 if(strtolower($lookupfinal_data_key)==null || strtolower($lookupfinal_data_key)=='null') {
					 if(isset($lookupfinal_data[0]) && $lookupfinal_data[0]=='NULL') {
						 unset($lookupfinal_data[0]);break;
					 }
				 }
			 }
		 }
		 $field_theme_name = $filter_details[0]['field_theme_name']; // field theme value

		 /* IRT 564 */
		 if($field_theme_name=='Date')
		 {
		 	if(!empty($result_data)) {
			 	$operator_value = explode(",",$result_data['operator_value'][0]);
			 	$result_data['operator_value'] = $operator_value;
			 	$filter_new_data = array();
			 	$filter_new_data['id'] = $result_data['id'];
			 	$filter_new_data['count'] = $result_data['count'];
			 	foreach($result_data['operator_field_value'] as $key => $value){
			 		$filter_new_data['operator_field_value'][$key] = $value;
			 	}
			 	foreach($result_data['operator_value'] as $key => $value){
			 		$filter_new_data['operator_value'][$key] = $value .' - '. $result_data['operator_value_new'][$key];
			 	}
			 	$result_data = $filter_new_data;
		 	}
		 }
		 /* End */

		 return $this->renderAjax('_filter_popup_option', array('filtervalue_selected'=>$filtervalue_selected,'report_type_id'=>$report_type_id,'field_id'=>$field_id,'reporttypefield'=>$field_detail,'has_lookupsql'=>$has_lookupsql,'has_lookup'=>$has_lookup, 'lookupfinal_data'=>$lookupfinal_data,'model' => $model,'filterdata' => $filter_details, 'filter_data' => $result_data, 'id' => $id, 'field_theme_name' => $field_theme_name));
	 }

	 /**
	  * Sort pop up option from custom report selected report field
	  * @return
	  */
	  public function actionSortPopUpOption()
	  {
		 $sort_type = array('1' => 'First (primary)','2' => 'Second (secondary)','3' => 'Third (tertiary)');
		 $sort_order = array('1' => "Ascending",'2' => "Descending");
		 $id = Yii::$app->request->get('id',0);

		 // selected data post
		 $data = Yii::$app->request->post();

		 // seelct sort type and sort order
		 $selected_sort_type = ''; $selected_sort_order = '';
		 if(!empty($data)){
			$type_order = array();
			foreach($data as $key => $val)
			{
				$type[$key] = json_decode($val, true);

				// key ,id
				if($key==$id){
					$selected_sort_type = $type[$key]['sort-type'];
					$selected_sort_order = $type[$key]['sort-order'];
				}

				// array key exists
				if(array_key_exists($type[$key]['sort-type'], $sort_type) && $key!=$id){
					unset($sort_type[$type[$key]['sort-type']]);
				}
			}
		 }

		 // return ajax
		 return $this->renderAjax('_sort_popup_option', array('sort_type' => $sort_type, 'sort_order' => $sort_order, 'type' => $selected_sort_type, 'order' => $selected_sort_order, 'id' => $id));
	  }

	  /**
	  * Sort pop up option from custom report selected report field
	  * @return
	  */
	public function actionGroupPopUpOption()
	{
		$group_type = array('1' => 'Group By','2' => 'Sum','3' => 'Count');
		$display_by = array(2=>'Number',3=>'Currency',4=>'Percentage');
		$id = Yii::$app->request->get('id',0);
		$type = Yii::$app->request->get('type','');

		// selected data post
		$data = Yii::$app->request->post();

		if($id !=''){
			$sumexist = ReportsReportTypeFields::find()->innerJoinWith([
				'reportsField' => function(\yii\db\ActiveQuery $query){
					$query->innerJoinWith([
						'reportsFieldType' => function(\yii\db\ActiveQuery $query2){
							$query2->innerJoinWith([
								'reportsFieldTypeTheme' => function(\yii\db\ActiveQuery $query3){
									$query3->where(['field_type_theme'=>'Integer']);
								}
							]);
						}
					]);
				}
			])->where(['tbl_reports_report_type_fields.id'=>$id])->count();
			if($sumexist == 0 && $type=='field')
				unset($group_type['2']);
		}

		 // seelct sort type and sort order
		 $selected_group_type = '';
		 $selected_display_by = '';
		 $selected_display_dp = 2;
		 $selected_display_currency_dp =2;
		 $selected_display_per_dp =2;
		 $selected_display_sp = '';
		 $selected_currency_smb ='$';

		 //echo "<pre>",print_r($type);die;
		 if(!empty($data)){
			$type_order = array();
			foreach($data as $key => $val)
			{
				$type_order[$key] = json_decode($val, true);
				if($key==$id) {
					$selected_group_type = $type_order[$key]['group-type'];
					$selected_display_by = $type_order[$key]['group-display-by'];
					if($selected_display_by == 2) {
						$selected_display_dp = $type_order[$key]['group-display-number-dp'];
						if(isset($type_order[$key]['group-display-number-sp'])){
							$selected_display_sp = $type_order[$key]['group-display-number-sp'];
						}
					}
					else if($selected_display_by == 3) {
						$selected_display_currency_dp = $type_order[$key]['group-display-currency-dp'];
						$selected_currency_smb = $type_order[$key]['display_by_currency_smb'];
					}
					else if($selected_display_by == 4){
						$selected_display_per_dp = $type_order[$key]['group-display-per-dp'];
					}
				}
				// array key exists
				if(array_key_exists($type_order[$key]['group-type'], $group_type) && $key!=$id){
					unset($group_type[$type_order[$key]['group-type']]);
				}
				// if(array_key_exists($type_order[$key]['group-display-by'], $display_by) && $key!=$id){
					//unset($display_by[$type_order[$key]['group-display-by']]);
				// }
			}
		 }

		 // return ajax
		 return $this->renderAjax('_group_popup_option', array('selected_display_per_dp'=>$selected_display_per_dp,'selected_display_currency_dp'=>$selected_display_currency_dp,'selected_display_sp'=>$selected_display_sp,'selected_display_dp'=>$selected_display_dp,'display_by'=>$display_by,'group_type' => $group_type, 'type' => $selected_group_type,'selected_display_by'=>$selected_display_by,  'id' => $id));
	  }

	 /**
	  * Report fields operator details
	  * @return
	  */
	 public function getReportFieldsOperatorDetails($id,$has_lookup=false){
		 $query = "SELECT t4.id, t4.field_operator,t5.field_type_theme as field_theme_name FROM tbl_reports_report_type_fields as t1
			INNER JOIN tbl_reports_fields ON t1.reports_fields_id = tbl_reports_fields.id
			INNER JOIN tbl_reports_field_type as t2 ON  tbl_reports_fields.reports_field_type_id = t2.id
			INNER JOIN tbl_reports_field_type_theme as t5 ON t2.field_type_theme_id = t5.id
			INNER JOIN tbl_reports_field_type_operator_logic as t3 ON t2.id = t3.fieldtype_id
			INNER JOIN tbl_reports_field_operators as t4 ON t3.fieldoperator_id = t4.id
			WHERE t1.id=".$id." ORDER BY field_operator";

		 if($has_lookup){
			$query = "SELECT t4.id, t4.field_operator,t5.field_type_theme as field_theme_name FROM tbl_reports_report_type_fields as t1
			INNER JOIN tbl_reports_fields ON t1.reports_fields_id = tbl_reports_fields.id
			INNER JOIN tbl_reports_field_type as t2 ON  t2.field_type = 'LOOKUP'
			INNER JOIN tbl_reports_field_type_theme as t5 ON t2.field_type_theme_id = t5.id
			INNER JOIN tbl_reports_field_type_operator_logic as t3 ON t2.id = t3.fieldtype_id
			INNER JOIN tbl_reports_field_operators as t4 ON t3.fieldoperator_id = t4.id
			WHERE t1.id=".$id." ORDER BY field_operator";
		}
		//echo $query;die;
		$filterdata = \Yii::$app->db->createCommand($query)->queryAll();
		if(!empty($filterdata)){
			return $filterdata;
		}
	 }

	 /**
	  * Display By chart option popup
	  * @return
	  */
	  public function actionDisplaybyPopUpChart(){
		 $selected_chart_format = Yii::$app->request->post('selected_chart_format','');
		 $id = Yii::$app->request->get('id',0);
		 $chart_id = Yii::$app->request->get('chartId',0);
		 $filter_data = Yii::$app->request->post();
		 //$chck_datatype = ReportsReportTypeFields::find()->where('id = '.$id)->one();

		 $query = 'SELECT t3.*,t1.chart_axis,t1.id as chart_id FROM tbl_reports_chart_format as t1
			INNER JOIN tbl_reports_chart_format_display_logic as t2 ON t1.id = t2.chartformat_id
			INNER JOIN tbl_reports_chart_format_display_by as t3 ON t2.chartformat_displayby_id = t3.id
			WHERE t1.id = '.$chart_id;
		 $display_by = \Yii::$app->db->createCommand($query)->queryAll();

		 // chart axis display
		 $chart_axis = explode(",", strtolower($display_by[0]['chart_axis']));
		 //echo "<pre>",print_r($chart_axis),"</pre>";
		 // To get Field Type
		/*$reportTypeFields = ArrayHelper::map(ReportsReportTypeFields::find()->select(['tbl_reports_report_type_fields.id','tbl_reports_report_type_fields.reports_field_type_id'])->where(['tbl_reports_report_type_fields.id'=>$id])->innerJoinWith([
			'reportsFieldType' => function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
			}
		])->all(),'id',function($model, $defaultValue) {
			return $model->reportsFieldType->field_type;
		});*/
		$records = ReportsReportTypeFields::find()->where(['tbl_reports_report_type_fields.id'=>$id])
		->innerJoinWith([
			'reportsField' => function(\yii\db\ActiveQuery $query2){
				$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
					$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
				}]);
			}
		])->all();

		$reportTypeFields = ArrayHelper::map($records, 'id', function($model, $defaultValue) {
			return $model->reportsField->reportsFieldType->field_type;
		});

		// To filter Axis which is already in use

		if(!empty($filter_data)){
			foreach($filter_data as $key => $value){
				//print_r(json_decode($value));die;
				$key1 = array_search(strtolower(json_decode($value)->axis),$chart_axis);
				if($key1 !== false && json_decode($value)->id != $id){
					unset($chart_axis[$key1]);
				}
			}
		}

		sort($chart_axis);
		//echo "<prE>",print_r($chart_axis),print_r($display_by),"</prE>";die;
		// render ajax to chart
		return $this->renderAjax('_chart_display_popup_option', ['selected_chart_format'=>$selected_chart_format,'reportTypeFields'=> $reportTypeFields,'display_by' => $display_by, 'chart_axis' => $chart_axis, 'id' => $id, 'filter_data' => $filter_data]);
	  }

	  public function actionPopUpOptionSelect()
	  {
		  $id = Yii::$app->request->get('id', 0);
		  $flag = Yii::$app->request->get('flag', '');
		  $filter_data = Yii::$app->request->post();
		 // echo "<pre>",print_r($filter_data),"</pre>";
		  //die;
		  /* IRT 564 */
		  if($flag=='') {
		  	if($filter_data['field_theme_name']=='Date') {
		  		$dates = array();
  				$filter_new_data = array();
  				$filter_new_data['id'] = $filter_data['id'];
	  			$filter_new_data['count'] = $filter_data['count'];
		  		foreach($filter_data['operator_value'] as $k=>$value) {
					if($value=='C'){
		  				$dates = explode(" - ",$filter_data['operator_value_custom'][$k]);
		  				$filter_new_data['operator_field_value'][] = $filter_data['operator_field_value'][0];
		  				$filter_new_data['operator_value'][] = $dates[0];
		  				$filter_new_data['operator_value_new'][] = $dates[1];
					} else {
						$dates = explode(" - ",$value);
		  				$filter_new_data['operator_field_value'][] = $filter_data['operator_field_value'][0];
		  				$filter_new_data['operator_value'][] = $dates[0];
		  				$filter_new_data['operator_value_new'][] = $dates[1];
					}
		  		}
		  		$filter_data = $filter_new_data;
	  		}
		  }
		  /* End */

		  if(isset($flag) && ($flag=='sort' || $flag=='group' || $flag=='chart')){
			  echo json_encode($filter_data); die;
		  }
		  foreach($filter_data['operator_field_value'] as $kopt=>$opt){
			  if(trim($opt)==""){unset($filter_data['operator_field_value'][$kopt]);}
		  }
		  if(!empty($filter_data['operator_field_value'])){
				 if(is_array($filter_data['operator_value'])){
					 $values=$filter_data['operator_value'];
					 unset($filter_data['operator_value']);
					 $filter_data['operator_value'][0]=''.implode(",",$values).'';
				 }
				 echo json_encode($filter_data);
		  }else{
				echo "";
		  }
		  die();
	  }
	  public function actionViewChart()
	  {
		  $post_data = Yii::$app->request->post();
		  $modeReportsChartFormat = ReportsChartFormat::find()->orderBy('format_order')->where(['id'=>$post_data['chart_format_id']])->one();
		  if(Yii::$app->db->driverName == 'mysql'){
			$query = (new ReportsReportType)->prepareCustomChartReport($post_data);
		  } else {
			$query = (new ReportsReportType)->prepareCustomChartReportMsSql($post_data);
		  }
		//  echo "<pre>",print_r($query),"</prE>";die;
		  $reportTypeFieldsModel = ReportsReportTypeFields::find()
			->select(['tbl_reports_report_type_fields.id','table_name', 'table_display_name','field_name', 'field_display_name'])
			->joinWith(['reportsField'=>function(\yii\db\ActiveQuery $query){
				$query->joinWith(['reportsTables']);
			}]);
			$table_fields_detail = $ytable_fields_detail = "";
			if(isset($post_data['ReportsUserSaved']['x_data']) && is_numeric($post_data['ReportsUserSaved']['x_data'])){
				$table_fields_detail = $reportTypeFieldsModel->where(['report_type_id' => $post_data['ReportsUserSaved']['report_type_id'],'tbl_reports_report_type_fields.id'=>$post_data['ReportsUserSaved']['x_data']])->asArray()->one();
			}
			if(isset($post_data['ReportsUserSaved']['y_data']) && is_numeric($post_data['ReportsUserSaved']['y_data'])){
				$ytable_fields_detail = $reportTypeFieldsModel->where(['report_type_id' => $post_data['ReportsUserSaved']['report_type_id'],'tbl_reports_report_type_fields.id'=>$post_data['ReportsUserSaved']['y_data']])->asArray()->one();
			}
			if(isset($post_data['ReportsUserSaved']['y_data_display']) && $post_data['ReportsUserSaved']['y_data_display']=='Weeks'){
					$query['sdate'] = date('Y-m-d',strtotime( 'monday this week',strtotime($query['sdate'])));
					$query['edate'] = date('Y-m-d',strtotime( 'sunday this week',strtotime($query['edate'])));
			}
		    if(isset($post_data['ReportsUserSaved']['series1_display']) && $post_data['ReportsUserSaved']['series1_display']=='Weeks'){
			$query['sdate'] = date('Y-m-d',strtotime( 'monday this week',strtotime($query['sdate'])));
			$query['edate'] = date('Y-m-d',strtotime( 'sunday this week',strtotime($query['edate'])));
		   }
		   $usr_id=Yii::$app->user->identity->id;
		   if(isset($post_data['ReportsUserSaved']['y_data_display']) && in_array($post_data['ReportsUserSaved']['y_data_display'],array('Days','Weeks','Months','Years'))) {
			   $query['x'] = str_replace(".","_",$query['x']);
			   $query['y'] = str_replace(".","_",$query['y']);
			   $query['legend']  = str_replace(".","_",$query['legend']);
			   $query['date_field'] = str_replace(".","_",$query['date_field']);
			   $query['group'] = str_replace(".","_",$query['group']);
			   $query['order'] = str_replace(".","_",$query['order']);
			   $query['display_by'] = str_replace(".","_",$query['display_by']);
			   $query['where'] = '1=1';
			   if (Yii::$app->db->driverName == 'mysql') {
			   		$sql="CALL getChartStats1('".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['vorder']."','".$query['display_by']."',".$usr_id.");";
			   } else{
			   		$sql="EXECUTE dbo.getChartStats1 '".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['vorder']."','".$query['display_by']."',".$usr_id;
			   }
			   $report_data =\Yii::$app->db->createCommand($sql)->queryAll();
		   }
		   else if(isset($post_data['ReportsUserSaved']['series1_display']) && in_array($post_data['ReportsUserSaved']['series1_display'],array('Days','Weeks','Months','Years'))){
			  $query['x'] = str_replace(".","_",$query['x']);
			  $query['y'] = str_replace(".","_",$query['y']);
			  $query['legend']  = str_replace(".","_",$query['legend']);
			  $query['date_field'] = str_replace(".","_",$query['date_field']);
			  $query['group'] = str_replace(".","_",$query['group']);
			  $query['order'] = str_replace(".","_",$query['order']);
			  $query['display_by'] = str_replace(".","_",$query['display_by']);
			  $query['where'] = '1=1';
			  if (Yii::$app->db->driverName == 'mysql') {
			  	$sql="CALL getChartStats1('".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['vorder']."','".$query['display_by']."',".$usr_id.");";
			  }else{
			  	$sql="EXECUTE dbo.getChartStats1 '".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['vorder']."','".$query['display_by']."',".$usr_id;
			  }
			  $report_data =\Yii::$app->db->createCommand($sql)->queryAll();
		   }
		   else if(isset($post_data['ReportsUserSaved']['x_data_display']) && in_array($post_data['ReportsUserSaved']['x_data_display'],array('Days','Weeks','Months','Years'))){
		   	  $query['x'] = str_replace(".","_",$query['x']);
		   	  $query['y'] = str_replace(".","_",$query['y']);
		   	  $query['legend']  = str_replace(".","_",$query['legend']);
		   	  $query['date_field'] = str_replace(".","_",$query['date_field']);
		   	  $query['group'] = str_replace(".","_",$query['group']);
			  $query['order'] = str_replace(".","_",$query['order']);
		      $query['display_by'] = str_replace(".","_",$query['display_by']);
			  $query['where'] = '1=1';
		   	  if (Yii::$app->db->driverName == 'mysql') {
		   	  	$sql="CALL getChartStats1('".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['vorder']."' ,'".$query['display_by']."',".$usr_id.");";
		   	  } else {
		   	  	$sql="EXECUTE dbo.getChartStats1 '".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['vorder']."','".$query['display_by']."',".$usr_id;
		   	  }
			  $report_data =\Yii::$app->db->createCommand($sql)->queryAll();
		   } else {
		   	   $report_data =\Yii::$app->db->createCommand($query['vsql'])->queryAll(\PDO::FETCH_NUM);
		   }
		   //echo "<pre>",print_r($report_data),"</pre>";die;
		   return $this->renderAjax('_step_6', ['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]);
	  }
	  /**
	   * Preview Report
	   **/
	   public function actionPreviewReport()
	   {
		    $create_view =  Yii::$app->request->get('createview','false');
		    $post_data = Yii::$app->request->post();
			$post_data['create_view']=$create_view;
			$modelReportype=ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
			$is_sp=false;


			if(Yii::$app->db->driverName == 'mysql'){
				$query = (new ReportsReportType)->prepareCustomReportQuery($post_data,5);
			}else{
				$query = (new ReportsReportType)->prepareCustomReportMsSqlQuery($post_data,5);
			}
			$new_columns=[];
			if(isset($modelReportype->sp_name) && trim($modelReportype->sp_name)=='MediaOut'){
				$id=Yii::$app->user->identity->id;
				$is_sp=true;
				$sp_sql=$query['sql'];
				$select=$query['view_select'];
				$from=$query['from'];
				$join_string=$query['join_string'];
				$where=$query['where'];
				$id=Yii::$app->user->identity->id;
				if (Yii::$app->db->driverName == 'mysql') {
					$selectedb=Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();
		   	  		$sql='CALL getMediaTimeOut("'.$select.'","'.$from.'","'.$join_string.'","'.$where.'","'.$selectedb.'","'.$id.'","5");';
				} else {
					$selectedb=Yii::$app->db->createCommand("SELECT db_name()")->queryScalar();
					$sql="EXECUTE dbo.getMediaTimeOut '".str_replace("'", "''", $select)."','".$from."','".$join_string."','".str_replace("'", "''", $where)."','".$selectedb."','".$id."',5";
				}
				$report_data =\Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_NUM);
				$query['cnt']=Yii::$app->db->createCommand('SELECT COUNT(*) FROM report_view_'.$id)->queryScalar();
				if (Yii::$app->db->driverName == 'mysql') {
					$sql_columns= "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = '".$selectedb."' and table_name = 'report_view_".$id."' AND COLUMN_NAME < 'task_unit_id'";
				}else{
					$sql_columns= "SELECT COLUMN_NAME FROM information_schema.columns WHERE  table_name = 'report_view_".$id."' AND COLUMN_NAME < 'task_unit_id'  ORDER BY ORDINAL_POSITION";
				}
				$fielddisp_column =\Yii::$app->db->createCommand($sql_columns)->queryAll(\PDO::FETCH_NUM);
				if(!empty($fielddisp_column)){
					$post_data['fielddisp'][]='task_unit_id';
					$post_data['fieldval'][]='task_unit_id';
					foreach($fielddisp_column as $nclumn){
						$post_data['fielddisp'][]='TotalMediaOut('.$nclumn[0].')';
						$post_data['fieldval'][]='TotalMediaOut('.$nclumn[0].')';
					}
				}
			}
			if(isset($modelReportype->sp_name) && trim($modelReportype->sp_name)=='SlaDataByServices'){
				$id=Yii::$app->user->identity->id;
				$is_sp=true;
				$sp_sql=$query['sql'];
				$select=$query['view_select'];
				$from=$query['from'];
				$join_string=$query['join_string'];
				$where=$query['where'];
				$id=Yii::$app->user->identity->id;
				if (Yii::$app->db->driverName == 'mysql') {
					$selectedb=Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();
		   	  		$sql='CALL getSlaDataByServices("'.$select.'","'.$from.'","'.$join_string.'","'.$where.'","'.$selectedb.'","'.$id.'","5");';
				} else {
					$selectedb=Yii::$app->db->createCommand("SELECT db_name()")->queryScalar();
					$sql="EXECUTE dbo.getSlaDataByServices '".str_replace("'", "''", $select)."','".$from."','".$join_string."','".str_replace("'", "''", $where)."','".$selectedb."','".$id."',5";
				}
				//echo $sql;die;
				$report_data =\Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_NUM);
				$query['cnt']=Yii::$app->db->createCommand('SELECT COUNT(*) FROM report_view_'.$id)->queryScalar();
				//echo $query['cnt'];die;
			}
			if(isset($post_data['create_view']) && $post_data['create_view']=='true'){
				echo number_format($query['cnt'],0);die;
			}

		    $post_data['fieldval_alias'] = $query['fieldval_alias'];
		    $selected_field = $query['fieldval_select'];
		    $selected_field_keys = array_keys($query['fieldval_select']);


		    if(!$is_sp){
				$report_data = \Yii::$app->db->createCommand($query['sql'])->queryAll(\PDO::FETCH_NUM);
			}

			$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']])
			->innerJoinWith([
				'reportsField' => function(\yii\db\ActiveQuery $query2) {
					$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
					}]);
				}
			])->all();

			$reportTypeFields = ArrayHelper::map($records,'id',function($model, $defaultValue) {
				return $model->reportsField->reportsFieldType->field_type;
			});


			$change_ids=array();
			if(isset($post_data['grouping_value'])) {
				$group_type = array('1' => 'Group By','2' => 'Sum','3' => 'Count');
				foreach($post_data['grouping_value'] as $gval){
					$g_data=json_decode($gval,true);
					if(isset($g_data['group-type']) && $g_data['group-type']!=''){
						if($g_data['group-type']!=1){
							if(strpos($post_data['fieldval'][$g_data['id']],'Calc') === false){
								$selected_data=explode(" as ",$selected_field[$g_data['id']]);
								$change_ids[$g_data['id']]=$selected_data[1];
								$post_data['fielddisp'][$g_data['id']]=$selected_data[0];
							}else{
								$selected_data=explode(" as ",$selected_field[$g_data['id']]);
								$change_ids[$g_data['id']] =$selected_data[1];
								$post_data['fielddisp'][$g_data['id']]=$group_type[$g_data['group-type']].'('.str_replace('Calc.','',$post_data['fieldval'][$g_data['id']]).')';
							}
						}
					}
				}
			}
//echo "<pre>";print_r($post_data['fielddisp']);die;
		return $this->renderAjax('preview-report', ['selected_field_keys'=>$selected_field_keys,'format'=>$post_data['grouping_value'],'change_ids'=>$change_ids,'report_data'=>$report_data, 'column_data' => $post_data['fieldval'], 'column_data_alias' => $post_data['fieldval_alias'], 'reportTypeFields'=>$reportTypeFields, 'column_display_data' => $post_data['fielddisp']]);
	}
	/**
	 * Preview Report
	 */
	public function actionPreviewChartReport()
	{
		$post_data = Yii::$app->request->post();
		$model = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
		$query = (new ReportsReportType)->prepareCustomChartReportQuery($post_data, 5);
		//echo "<pre>",print_r($query),"</pre>";die;
		$sql='call getChartStats("'.$query['sdate'].'","'.$query['edate'].'","'.$query['x'].'","'.$query['y'].'","'.$query['legend'].'","'.$query['interval_range'].'","'.$query['datefield_xy'].'","'.$query['fmr_with_join'].'","'.$query['whr'].'","'.$query['grp'].'","'.$query['display_by'].'");';
		$report_data = \Yii::$app->db->createCommand($sql)->queryAll();
		//echo "<pre>",print_r($report_data),"</pre>";die;
		$format = ReportsChartFormat::findOne($post_data['ReportsUserSaved']['chart_format_id'])->chart_format;
		switch($format){
			case 'Bar':
				$new_chart_data = (new ReportsReportType())->getBarChartData($report_data,$query);
			break;
			case 'Line':
			case 'Column':
				$new_chart_data = (new ReportsReportType())->getLineColumnChartData($report_data,$query);
				break;
			case 'Pie':
				break;

		}
		return $this->renderAjax('preview-report-chart', ['format'=>$format,'model'=>$model,'chart_data'=>json_encode($new_chart_data['data']), 'categories' => json_encode(array_values($new_chart_data['categories']))]);
	}

	public function actionRunReport()
	{
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		if(isset($post_data['chart_report']) && $post_data['chart_report']=='chart_report'){
			$image_data = $post_data['image_data'];
			$table_data=json_decode($post_data['table_data'],true);
		}
		$is_sp=false;
		$model = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
		if(Yii::$app->db->driverName == 'mysql'){
			$query = (new ReportsReportType)->prepareCustomReportQuery($post_data);
		} else {
			$query = (new ReportsReportType)->prepareCustomReportMsSqlQuery($post_data);
		}
		$post_data['fieldval_alias'] = $query['fieldval_alias'];
		$selected_field = $query['fieldval_select'];
		$selected_field_keys = array_keys($query['fieldval_select']);
		$criteria = (new ReportsReportType)->createCustomReportCriteria($post_data);
		if(isset($model->sp_name) && trim($model->sp_name)=='MediaOut'){
				$id=Yii::$app->user->identity->id;
				$is_sp=true;
				$sp_sql=$query['sql'];
				$select=$query['view_select'];
				$from=$query['from'];
				$join_string=$query['join_string'];
				$where=$query['where'];
				$id=Yii::$app->user->identity->id;
				if (Yii::$app->db->driverName == 'mysql') {
					$selectedb=Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();
		   	  		$sql='CALL getMediaTimeOut("'.$select.'","'.$from.'","'.$join_string.'","'.$where.'","'.$selectedb.'","'.$id.'","0");';
				} else {
					$selectedb=Yii::$app->db->createCommand("SELECT db_name()")->queryScalar();
					$sql="EXECUTE dbo.getMediaTimeOut '".str_replace("'", "''", $select)."','".$from."','".$join_string."','".str_replace("'", "''", $where)."','".$selectedb."','".$id."',0";
				}
				$report_data =\Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_NUM);
				if (Yii::$app->db->driverName == 'mysql') {
					$sql_columns= "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = '".$selectedb."' and table_name = 'report_view_".$id."' AND COLUMN_NAME < 'task_unit_id'";
				}else{
					$sql_columns= "SELECT COLUMN_NAME FROM information_schema.columns WHERE  table_name = 'report_view_".$id."' AND COLUMN_NAME < 'task_unit_id'  ORDER BY ORDINAL_POSITION";
				}
				$fielddisp_column =\Yii::$app->db->createCommand($sql_columns)->queryAll(\PDO::FETCH_NUM);
				if(!empty($fielddisp_column)){
					$post_data['fielddisp'][]='task_unit_id';
					$post_data['fieldval'][]='task_unit_id';
					foreach($fielddisp_column as $nclumn){
						$post_data['fielddisp'][]='TotalMediaOut('.$nclumn[0].')';
						$post_data['fieldval'][]='TotalMediaOut('.$nclumn[0].')';
					}
				}
			}else if(isset($model->sp_name) && trim($model->sp_name)=='SlaDataByServices'){
				$id=Yii::$app->user->identity->id;
				$is_sp=true;
				$sp_sql=$query['sql'];
				$select=$query['view_select'];
				$from=$query['from'];
				$join_string=$query['join_string'];
				$where=$query['where'];
				$id=Yii::$app->user->identity->id;
				if (Yii::$app->db->driverName == 'mysql') {
					$selectedb=Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();
		   	  		$sql='CALL getSlaDataByServices("'.$select.'","'.$from.'","'.$join_string.'","'.$where.'","'.$selectedb.'","'.$id.'","0");';
				} else {
					$selectedb=Yii::$app->db->createCommand("SELECT db_name()")->queryScalar();
					$sql="EXECUTE dbo.getSlaDataByServices '".str_replace("'", "''", $select)."','".$from."','".$join_string."','".str_replace("'", "''", $where)."','".$selectedb."','".$id."',0";
				}
				$report_data =\Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_NUM);
			}else{
				$report_data = \Yii::$app->db->createCommand($query['sql'])->queryAll(\PDO::FETCH_NUM);
			}

		$this->layout = false;

		$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']])
		->innerJoinWith([
			'reportsField' => function(\yii\db\ActiveQuery $query2) {
				$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
					$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
				}]);
			}
		])->all();

		$reportTypeFields = ArrayHelper::map($records,'id',function($model, $defaultValue) {
			return $model->reportsField->reportsFieldType->field_type;
		});
		$filename="custom_report_".date('m_d_Y',time()).".xlsx";
		$lookup_criteria_values=array();
		if(!empty($criteria)){
		/*$criteria_fields="";
		foreach($criteria as $ckey=>$cval){
			$field=$post_data['fieldval'][$ckey];
			$val_table_name_field=explode(".",$post_data['fieldval'][$ckey]);
			$criteria_fields="'".$post_data['fieldval'][$ckey]."'";
			$lookup_sql="SELECT tbl_reports_fields_relationships.* FROM `tbl_reports_fields_relationships`
INNER JOIN tbl_reports_fields ON tbl_reports_fields.id=tbl_reports_fields_relationships.report_fields_id
INNER JOIN tbl_reports_tables ON tbl_reports_tables.id=tbl_reports_fields.report_table_id
WHERE tbl_reports_fields_relationships.rela_type NOT IN (0) and CONCAT(tbl_reports_tables.table_name,'.',tbl_reports_fields.field_name) IN ({$criteria_fields})";
			$look_data = Yii::$app->db->createCommand($lookup_sql)->queryOne();
			if(!empty($look_data)){
				$cdata=array();
				if($look_data['rela_type']==2 || $look_data['rela_type']==4){ //custom
					$modelReportsFieldsRelationships =ReportsFieldsRelationships::findOne($look_data['id']);
					if(!empty($modelReportsFieldsRelationships) && !empty($modelReportsFieldsRelationships->reportsFieldsRelationshipsLookups)){
						foreach($modelReportsFieldsRelationships->reportsFieldsRelationshipsLookups as $rfldata){
							$cdata[$rfldata->field_value]=	$rfldata->lookup_value;
						}
					}
				}
				if($look_data['rela_type']==3 || $look_data['rela_type']==1){ //relation&lookup

					$primary_key=$post_data['fieldval'][$ckey];
					if($val_table_name_field[0]!=$from){
						$tablealias_sql = "SELECT CONCAT(tbl_reports_tables.table_name,'_',tbl_reports_fields.field_name) as tablealias
FROM `tbl_reports_report_type_fields`
INNER JOIN tbl_reports_fields_relationships ON tbl_reports_fields_relationships.id = tbl_reports_report_type_fields.reports_fields_relationships_id
INNER JOIN tbl_reports_fields ON tbl_reports_fields.id = tbl_reports_fields_relationships.report_fields_id
INNER JOIN tbl_reports_tables ON tbl_reports_tables.id = tbl_reports_fields.report_table_id
WHERE tbl_reports_report_type_fields.reports_fields_id IN (SELECT `tbl_reports_fields`.id FROM `tbl_reports_fields` INNER JOIN `tbl_reports_tables` ON `tbl_reports_fields`.`report_table_id` = `tbl_reports_tables`.`id` WHERE CONCAT(table_name,'.',field_name) = '{$field}') AND tbl_reports_report_type_fields.report_type_id = ".$post_data['ReportsUserSaved']['report_type_id'];
						$tablealias=Yii::$app->db->createCommand($tablealias_sql)->queryOne();
						if(isset($tablealias['tablealias']) && $tablealias['tablealias']!=''){
							$primary_key=$tablealias['tablealias'].'.'.$val_table_name_field[1];
						}
					}
					$tableAlias = $val_table_name_field[0].'_'.$val_table_name_field[1];
					$field_lookup=$tableAlias.'.'.$look_data['lookup_fields'];
					$lookupfieldsAr = explode(",",$tableAlias.'.'.$look_data['lookup_fields']);
					if(count($lookupfieldsAr) > 1){
						$lookup_data_lookup_fields = implode(", {$tableAlias}.",$lookupfieldsAr);
						$sep=$look_data['lookup_field_separator'];
						if($sep==NULL || $sep==''){
						$sep=' ';
						}
						$field_lookup="CONCAT(".str_replace(",",",'".$sep."',",$lookup_data_lookup_fields).")";
					}

					if(count($lookupfieldsAr) > 1){
						$field_lookup = $field_lookup ;
					}else{
						$field_lookup = $field_lookup ;
					}
					$resultdata = Yii::$app->db->createCommand('SELECT report_table_id,table_name FROM tbl_reports_fields
					INNER JOIN tbl_reports_report_type_fields ON tbl_reports_report_type_fields.reports_fields_id=tbl_reports_fields.id
					INNER JOIN tbl_reports_tables ON tbl_reports_tables.id=tbl_reports_fields.report_table_id
					WHERE tbl_reports_report_type_fields.report_type_id = '.$post_data['ReportsUserSaved']['report_type_id'].' GROUP BY report_table_id, table_name ORDER BY tbl_reports_report_type_fields.id ASC')->queryAll();
					$resultdata = ArrayHelper::map($resultdata, 'report_table_id', 'table_name');
					$join_string="";
					$from="";
					if(!empty($resultdata)){
						foreach($resultdata as $table_name){
							$from=$table_name;break;
						}

						$rel_order="(CASE tbl_reports_tables.id";
						$i=0;
						foreach($resultdata as $key=>$table_name){
						 $rel_order.=" WHEN  {$key} THEN {$i} ";
						 $i++;
						}
						$rel_order.=" END)";

						$sql = "SELECT tbl_reports_tables.table_name,tbl_reports_fields.field_name,tbl_reports_fields_relationships.rela_type,tbl_reports_fields_relationships.rela_join_string,tbl_reports_fields_relationships.rela_base_table,tbl_reports_fields_relationships.rela_data, tbl_reports_fields_relationships.lookup_table, tbl_reports_fields_relationships.lookup_fields, tbl_reports_fields_relationships.lookup_field_separator,tbl_reports_fields.field_name as basefield, tbl_reports_tables.table_name  as basetable
						FROM tbl_reports_fields_relationships
						Inner Join tbl_reports_fields on tbl_reports_fields.id = tbl_reports_fields_relationships.report_fields_id
						Inner Join tbl_reports_tables on tbl_reports_tables.id = tbl_reports_fields.report_table_id
						WHERE tbl_reports_tables.id IN (".implode(',',array_keys($resultdata)).") and tbl_reports_fields_relationships.rela_type IN (0,3)
						ORDER BY  {$rel_order}, report_fields_id ASC";
						$get_relationship_data=Yii::$app->db->createCommand($sql)->queryAll();
						$alltable_alias=array();
						$alltable_alias_for_field=array();
						if(!empty($get_relationship_data)){
							foreach($get_relationship_data as $relationship_data){
								if(isset($alltable_alias[$relationship_data['table_name']])){
									$join_string.=" {$relationship_data['rela_join_string']} JOIN {$relationship_data['rela_base_table']} as {$relationship_data['table_name']}_{$relationship_data['field_name']} ON {$relationship_data['table_name']}_{$relationship_data['field_name']}.{$relationship_data['rela_data']} = {$alltable_alias[$relationship_data['table_name']]}.{$relationship_data['field_name']} ";
								}else{
									$join_string.=" {$relationship_data['rela_join_string']} JOIN {$relationship_data['rela_base_table']} as {$relationship_data['table_name']}_{$relationship_data['field_name']} ON {$relationship_data['table_name']}_{$relationship_data['field_name']}.{$relationship_data['rela_data']} = {$relationship_data['table_name']}.{$relationship_data['field_name']} ";
								}
								$alltable_alias[$relationship_data['rela_base_table']]=$relationship_data['table_name'].'_'.$relationship_data['field_name'];


								$alltable_alias_for_field[$relationship_data['table_name']][$relationship_data['field_name']]=array('table_name'=>$relationship_data['rela_base_table'],'field_name'=>$relationship_data['rela_data']);
							}
						}
					$sql=" SELECT DISTINCT {$primary_key} as id,({$field_lookup}) as text FROM {$from} {$join_string} WHERE {$primary_key} IN (".implode(",",$cval).")";
					$cdata = ArrayHelper::map(\Yii::$app->db->createCommand($sql)->queryAll(),"id","text");
					}
				}
				if(!empty($cdata)){
					$lookup_criteria_values[$ckey]=$cdata;
				}
			}
		}*/

		}

		$change_ids=array();
		if(isset($post_data['grouping_value'])){
			$group_type = array('1' => 'Group By','2' => 'Sum','3' => 'Count');
			foreach($post_data['grouping_value'] as $gval){
				$g_data=json_decode($gval,true);
				if(isset($g_data['group-type']) && $g_data['group-type']!=''){
					if($g_data['group-type']!=1){
						if(strpos($post_data['fieldval'][$g_data['id']],'Calc') === false){
							$selected_data=explode(" as ", $selected_field[$g_data['id']]);
							$change_ids[$g_data['id']] = $selected_data[1];
							$post_data['fielddisp'][$g_data['id']] = $selected_data[0];
						} else {
							$selected_data=explode(" as ", $selected_field[$g_data['id']]);
							$change_ids[$g_data['id']] = $selected_data[1];
							$post_data['fielddisp'][$g_data['id']] = $group_type[$g_data['group-type']].'('.str_replace('Calc.','',$post_data['fieldval'][$g_data['id']]).')';
						}
					}
				}
			}
		}

		if((new User)->checkAccess(8.021300000))
			$report_header = Settings::find()->where(['field' => 'report_header'])->one()->fieldtext;

		$header = $this->render('preview-report-table-header', ['lookup_criteria_values' => $lookup_criteria_values,'header_data'=>$post_data, 'criteria' => $criteria, 'reportTypeFields' => $reportTypeFields,'column_data_alias' => $post_data['fieldval_alias'],'model'=>$model,'column_display_data' => $post_data['fielddisp'],'image_data'=>$image_data]);
	   	$content = "";
		if(isset($image_data) && $image_data!="") {
		}else{
			    $content = $this->render('preview-report-table', ['flag'=>'pdf','selected_field_keys' => $selected_field_keys,'format'=>$post_data['grouping_value'],'change_ids'=>$change_ids,'report_data'=>$report_data, 'column_data' => $post_data['fieldval'],'column_data_alias' => $post_data['fieldval_alias'], 'reportTypeFields'=>$reportTypeFields,'column_display_data' => $post_data['fielddisp']]);
		}

		$datatable = $this->render('preview-data-table', ['table_data'=>$table_data,'header_data'=>$post_data]);

        $table = '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">'.$header.$content.$datatable;

        $tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, $table);
        $objPHPExcel = new PHPExcel();
      	/*  $final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'),
		array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);*/
		//echo $report_header=$report_header;
		//echo "<hr>";
		//echo htmlentities($report_header);die;
		$wizard = new PHPExcel_Helper_HTML;
		$richText = $wizard->toRichTextObject($report_header);
		//$richText = $wizard->toRichTextObject(mb_convert_encoding(html_entity_decode($report_header), 'HTML-ENTITIES', 'UTF-8'));
		$richText = str_replace('&','&&',$richText);
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&R&B'.$richText);
		//'&R&B'.mb_convert_encoding(html_entity_decode($report_header), 'HTML-ENTITIES', 'UTF-8'));
		//echo "<pre>",print_r($objPHPExcel),"</pre>";die;

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
		$excelHTMLReader->setInputEncoding('UTF-8');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data.$datatable);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B'.(count($table_data)+10));
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
		//echo "<pre>",print_r($objPHPExcel);die;
        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

		//$excelHTMLReader->save('php://output');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
		ob_start();
		$writer->save('php://output','w');
        exit;
	}


	public function strip_selected_tags($text, $tags = array())
    {
        $args = func_get_args();
        $text = array_shift($args);
        $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
        foreach ($tags as $tag){
            if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)){
                $text = str_replace($found[0],$found[1],$text);
          }
        }

        return $text;
    }

	/**
	 * To Save Report & it's Criteria
	 * @param mixed Form serialized data
	 * @param report_saved_id int
	 */
	public function actionSaveReportPopup()
	{
		$report_saved_id = Yii::$app->request->get('report_saved_id',0);
		$step_3 = Yii::$app->request->get('step3','');
		$post_data = Yii::$app->request->post();
		$model = new ReportsUserSaved();
		$model->flag = 'saved';
		if($step_3 == 'none'){
			$model->custom_report_name=$post_data['ReportsUserSaved']['title'];
		}
		if($report_saved_id!=0){
			$model = ReportsUserSaved::findOne($report_saved_id);
		}


		return $this->renderAjax('save-report-popup', ['model' => $model]);
	}
	public function actionGetshowby() {
		$show_by = Yii::$app->request->post('show_by',0);
		$data=array();
		$client_data = array();
		$team_data = array();
		$roleId = Yii::$app->user->identity->role_id;
		$userId = Yii::$app->user->identity->id;
		if($show_by==1){//By Role
			/*$casesql     ="	SELECT tbl_user.id FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE  tbl_role.role_type IN ('1') ORDER BY tbl_user.usr_username";
    		$teamsql     ="	SELECT tbl_user.id FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE  tbl_role.role_type IN ('2') ORDER BY tbl_user.usr_username";
    		$bothSql =" SELECT tbl_user.id FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE tbl_user.id NOT IN($casesql) AND tbl_user.id NOT IN($teamsql) AND (tbl_role.role_type IN ('1,2') OR tbl_role.role_type IN ('2,1'))  ORDER BY tbl_user.usr_username";
    		$data['case_manager']=ArrayHelper::map(User::find()->where("tbl_user.id IN ($casesql)")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
			$data['team_member']=ArrayHelper::map(User::find()->where("tbl_user.id IN ($teamsql)")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
			$data['both_case_team_manager']=ArrayHelper::map(User::find()->where("tbl_user.id IN ($bothSql)")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
			*/
			$casesql     ="	SELECT tbl_role.id FROM tbl_role WHERE  tbl_role.role_type IN ('1') and tbl_role.id NOT IN(0)";
    		$teamsql     ="	SELECT tbl_role.id FROM tbl_role WHERE tbl_role.role_type IN ('2') and tbl_role.id NOT IN(0)";
    		$bothSql =" SELECT tbl_role.id FROM tbl_role  WHERE tbl_role.id NOT IN($casesql) AND tbl_role.id NOT IN($teamsql) AND (tbl_role.role_type IN ('1,2') OR tbl_role.role_type IN ('2,1')) and tbl_role.id NOT IN(0)";
    		$data['case_manager']=ArrayHelper::map(Role::find()->where("tbl_role.id IN ($casesql)")->all(),'id','role_name');
                    $data['team_member']=ArrayHelper::map(Role::find()->where("tbl_role.id IN ($teamsql)")->all(),'id','role_name');
                    $data['both_case_team_manager']=ArrayHelper::map(Role::find()->where("tbl_role.id IN ($bothSql)")->all(),'id','role_name');
		} else if($show_by==2) {//By Client/case
                    $condition = "client_id!=0 AND team_id=0";
		if ($roleId != 0) {
                    $condition = "user_id=$userId AND team_id=0";
		}

		$client_data = ArrayHelper::map(Client::find()->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) { $query->select(['client_id']); }])->select(['tbl_client.id', 'client_name'])->where($condition)->all(),'id', 'client_name');
		foreach($client_data as $client_id => $client_name){
                    if ($roleId != 0) {
                        $case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id='.$client_id.' AND user_id='.$userId.' AND team_id=0');
                        $caseList = ArrayHelper::map(ClientCase::find()->select(['id', 'case_name'])->where(['in', 'id', $case_data])->andWhere('is_close=0')->orderBy('case_name')->asArray()->all(),'id', 'case_name');
                    } else {
                        $caseList = ArrayHelper::map(ClientCase::find()->select(['id', 'case_name'])->where(['client_id' => $client_id, 'is_close'=>0])->orderBy('case_name')->asArray()->all(),'id', 'case_name');
                    }
                    $data[$client_id] = $caseList;
		}
		}else if($show_by==3){//By Team/Location
                    $sql_query = "SELECT team.id as team_id,team.team_name,tbl_team_locs.team_loc,master.team_location_name  FROM tbl_team as team LEFT JOIN tbl_team_locs on tbl_team_locs.team_id=team.id LEFT JOIN tbl_teamlocation_master as master ON master.id = tbl_team_locs.team_loc WHERE  team.id != 1 order by team.team_name,master.team_location_name ";
                    if($roleId!=0){
                        $sql_query = "SELECT security.team_id,security.team_loc,team.team_name,master.team_location_name FROM tbl_project_security security INNER JOIN tbl_team as team ON team.id = security.team_id INNER JOIN tbl_teamlocation_master as master ON master.id = security.team_loc WHERE security.user_id = ".$userId." AND security.team_id != 0 AND security.team_loc != 0 order by team.team_name,master.team_location_name";
                    }
                    $params[':user_id'] = $userId;
                    $dropdown_data = \Yii::$app->db->createCommand($sql_query)->queryAll();
                    if(!empty($dropdown_data)){
                        foreach($dropdown_data as $drop => $value) {
                            $team_data[$value['team_id']]=$value['team_name'];
                            $data[$value['team_id']][$value['team_loc']]=$value['team_location_name'];
                        }
                    }
		}
		else if($show_by==4){//By User
                    $sql_query = "SELECT id FROM tbl_user where status=1 and id != 1";
                    $data['users']=ArrayHelper::map(User::find()->select(['id','usr_first_name','usr_lastname'," CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where("tbl_user.id IN ($sql_query)")->orderBy("full_name ASC")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
		}

		return $this->renderAjax('getshowby', ['data' => $data, 'team_data'=>$team_data, 'client_data'=>$client_data, 'show_by'=>$show_by]);
	}

	public function actionEditSaveReport(){
		$id=Yii::$app->request->get('id',0);
		$post_data = Yii::$app->request->post();
		//echo "<pre>",print_r($post_data),"</pre>";die;
		$model = ReportsUserSaved::findOne($id);
		if(!empty($post_data) && $model->load($post_data)){
			$transaction = Yii::$app->db->beginTransaction();
			try{
				if(isset($post_data['ReportsUserSaved']['grid_line'])){
					$model->grid_line=implode(",",$post_data['ReportsUserSaved']['grid_line']);
				}
				$model->report_format_id=1;
				if(isset($post_data['chart_format_id'])){
					$model->report_format_id=2;
					$model->chart_format_id=$post_data['chart_format_id'];
				}
				if($model->save()){
					$i=1;
					$ReportsUserSavedFields_ids=array(0);
					if(!empty($post_data['fieldval'])){
						foreach($post_data['fieldval'] as $key => $field){
						$modelSavedFields =ReportsUserSavedFields::find()->where("saved_report_id = {$id} AND report_type_field_id={$key}")->one();
						if(strpos($field,'Calc.') !== false){
							$modelSavedFields =ReportsUserSavedFields::find()->where("saved_report_id = {$id} AND field_calculation_id={$key}")->one();
						}
						if(!isset($modelSavedFields->id)){
							$modelSavedFields = new ReportsUserSavedFields();
						}
						$modelSavedFields->column_sort_order=$i;
						$modelSavedFields->saved_report_id = $model->id;
						$modelSavedFields->report_type_field_id = $key;
						//$modelSavedFields->field_origin = 1;// 1 = Database, 2 = Calculation
						$modelSavedFields->field_calculation_id=0;
						if(strpos($field,'Calc.') !== false){
							$modelSavedFields->field_calculation_id=$key;
							$modelSavedFields->report_type_field_id = 0;
						}
						/*$modelSavedFields->chart_display_type_id=0;
						$modelSavedFields->chart_axis=0; //1=X and 2=Y
						if(isset($post_data['chart_values_'.$key]) && $post_data['chart_values_'.$key]!=''){
							$chartvalues = json_decode($post_data['chart_values_'.$key],true);
							$modelSavedFields->chart_display_type_id=(isset($chartvalues['display_by'])) ? $chartvalues['display_by'] : 0;
							if($chartvalues['axis'] == 'x') {
								$modelSavedFields->chart_axis=1; //1=X and 2=Y
							} else if($chartvalues['axis'] == 'y') {
								$modelSavedFields->chart_axis=2; //1=X and 2=Y
							}
						}
						$modelSavedFields->field_sort_type=0;//1=p 2=s and 3=t
						$modelSavedFields->field_sort_order=0;//1=asc 2=desc
						if(!empty($post_data['sorting_value']) ){
							foreach($post_data['sorting_value'] as $sorting_data){
								$sorting=json_decode($sorting_data,true);
								if($key==$sorting['id']){
									$modelSavedFields->field_sort_type=$sorting['sort-type'];//1=p 2=s and 3=t
									$modelSavedFields->field_sort_order=$sorting['sort-order'];//1=asc 2=desc
									break;
								}
							}
						}
						$modelSavedFields->field_group_type=0;
						if(!empty($post_data['grouping_value']) ){
							foreach($post_data['grouping_value'] as $grouping_data){
								$grouping=json_decode($grouping_data,true);
								if($key==$grouping['id']){
									$modelSavedFields->field_group_type=$grouping['group-type'];
									break;
								}
							}
						}
						$modelSavedFields->is_deleted=0;*/
						if(!$modelSavedFields->save()){
							//echo "<pre>",print_r($modelSavedFields->getErrors()),print_r($modelSavedFields->attributes),"</pre>";die;
						}
						$ReportsUserSavedFields_ids[$key]=$modelSavedFields->id;
						$i++;
					}
					ReportsUserSavedFields::deleteAll('id NOT IN ('.implode(",",$ReportsUserSavedFields_ids).') AND saved_report_id = '.$id.'');
					ReportsUserSavedFieldsLogic::deleteAll('saved_report_field_id IN ('.implode(",",$ReportsUserSavedFields_ids).')');

					$logic_ids=array();
					if(!empty($post_data['filter_value'])){
						foreach($post_data['filter_value'] as $filter_data){
							$filters=json_decode($filter_data,true);
							$opreator_field_values=$filters['operator_value'];
							$opreator_field_values2=isset($filters['operator_value_new'][0]) && $filters['operator_value_new'][0]!=''?$filters['operator_value_new']:'';
							$opreators=$filters['operator_field_value'];
							$logic_ids[$filters['id']]=$filters['id'];
							if(!empty($opreators) && isset($ReportsUserSavedFields_ids[$filters['id']])){
								$i = 0;
								$opwhere = '';
								foreach($opreators as $opt_key=>$opt){
									$modelfieldLogic=new ReportsUserSavedFieldsLogic();
									$modelfieldLogic->saved_report_field_id=$ReportsUserSavedFields_ids[$filters['id']];
									$modelfieldLogic->report_field_operator_id=$opt;
									$opt_val=$opreator_field_values[$opt_key];
									$opt_val_new=$opreator_field_values2[$opt_key];
									if(count($opreators) > 1){
										$data_opt_val=explode(",",$opreator_field_values[0]);
										if(count($data_opt_val) > 1){
										$opt_val=$data_opt_val[$i];
										}
										$data_opt_valnew=explode(",",$opreator_field_values2[0]);
										if(count($data_opt_valnew) > 1){
										$opt_val_new=$data_opt_valnew[$i];
										}
										$modelfieldLogic->value1=$opt_val;
										if(isset($opt_val) && is_array($opt_val)){
											$modelfieldLogic->value1=implode("|",$opt_val);
										}else{
											$modelfieldLogic->value1=$opt_val;
										}
										if(isset($opt_val_new) && is_array($opt_val_new)){
											$modelfieldLogic->value2=implode("|",$opt_val_new);
										}else{
											$modelfieldLogic->value2=(isset($opt_val_new)?$opt_val_new:'');
										}
									}else{
										$modelfieldLogic->value1=$opreator_field_values[$opt_key];
										if(isset($opreator_field_values[$opt_key]) && is_array($opreator_field_values[$opt_key])){
											$modelfieldLogic->value1=implode("|",$opreator_field_values[$opt_key]);
										}else{
											$modelfieldLogic->value1=$opreator_field_values[$opt_key];
										}
										if(isset($opreator_field_values2[$opt_key]) && is_array($opreator_field_values[$opt_key])){
											$modelfieldLogic->value2=implode("|",$opreator_field_values2[$opt_key]);
										}else{
											$modelfieldLogic->value2=(isset($opreator_field_values2[$opt_key])?$opreator_field_values2[$opt_key]:'');
										}
									}
									$modelfieldLogic->sort_type=0;//1=p 2=s and 3=t
									$modelfieldLogic->sort_order=0;//1=asc 2=desc
									if(!empty($post_data['sorting_value']) ){
										foreach($post_data['sorting_value'] as $sorting_data){
											$sorting=json_decode($sorting_data,true);
											if($filters['id']==$sorting['id']){
												$modelfieldLogic->sort_type=$sorting['sort-type'];//1=p 2=s and 3=t
												$modelfieldLogic->sort_order=$sorting['sort-order'];//1=asc 2=desc
												break;
											}
										}
									}
									$modelfieldLogic->format_total_type=0;
									$modelfieldLogic->format_display_type=0;
									$modelfieldLogic->format_display_decimal=0;
									$modelfieldLogic->format_display_separator='';
									$modelfieldLogic->format_display_symbol='';
									$modelfieldLogic->format_total_type=0;
									if(!empty($post_data['grouping_value']) ){
										foreach($post_data['grouping_value'] as $grouping_data){
											$grouping=json_decode($grouping_data,true);
											if($filters['id']==$grouping['id']){
												if(isset($grouping['group-type']) && $grouping['group-type']!=""){
												$modelfieldLogic->format_total_type=$grouping['group-type'];
												}
												if(isset($grouping['group-display-by']) && $grouping['group-display-by']!=""){
													$modelfieldLogic->format_display_type=$grouping['group-display-by'];
													if($grouping['group-display-by'] == 2){//NUMBER
														$modelfieldLogic->format_display_decimal=$grouping['group-display-number-dp'];
														if(isset($grouping['group-display-number-sp']) && $grouping['group-display-number-sp']!=""){
															$modelfieldLogic->format_display_separator='1';
														}
													}else if($grouping['group-display-by'] == 3){//CURRENCY
														$modelfieldLogic->format_display_decimal=$grouping['group-display-currency-dp'];
														$modelfieldLogic->format_display_symbol=$grouping['display_by_currency_smb'];
													}else if($grouping['group-display-by'] == 4){//PERCENTAGES
														$modelfieldLogic->format_display_decimal=$grouping['group-display-per-dp'];
													}
												}
												break;
											}
										}
									}
									if(!$modelfieldLogic->save()){
										echo "<pre>",print_r($modelfieldLogic->getErrors()),"</pre>";die;
									}
									$i++;
								}
							}
						}
					}
					if(!empty($post_data['sorting_value'])){
						foreach($post_data['sorting_value'] as $sorting_data){
							$sorting=json_decode($sorting_data,true);
							if(!in_array($sorting['id'],$logic_ids)){
							$logic_ids[$sorting['id']]=$sorting['id'];
							$modelfieldLogic=new ReportsUserSavedFieldsLogic();
							$modelfieldLogic->saved_report_field_id=$ReportsUserSavedFields_ids[$sorting['id']];
							$modelfieldLogic->report_field_operator_id=0;
							$modelfieldLogic->value1='';
							$modelfieldLogic->value2='';
							$modelfieldLogic->sort_type=$sorting['sort-type'];//1=p 2=s and 3=t
							$modelfieldLogic->sort_order=$sorting['sort-order'];//1=asc 2=desc
							$modelfieldLogic->format_total_type=0;
							$modelfieldLogic->format_display_type=0;
							$modelfieldLogic->format_display_decimal=0;
							$modelfieldLogic->format_display_separator='';
							$modelfieldLogic->format_display_symbol='';
							$modelfieldLogic->format_total_type=0;
							if(!empty($post_data['grouping_value']) ){
								foreach($post_data['grouping_value'] as $grouping_data){
									$grouping=json_decode($grouping_data,true);
									if($sorting['id']==$grouping['id']){
										if(isset($grouping['group-type']) && $grouping['group-type']!=""){
										$modelfieldLogic->format_total_type=$grouping['group-type'];
										}
										if(isset($grouping['group-display-by']) && $grouping['group-display-by']!=""){
											$modelfieldLogic->format_display_type=$grouping['group-display-by'];
											if($grouping['group-display-by'] == 2){//NUMBER
												$modelfieldLogic->format_display_decimal=$grouping['group-display-number-dp'];
												if(isset($grouping['group-display-number-sp']) && $grouping['group-display-number-sp']!=""){
													$modelfieldLogic->format_display_separator='1';
												}
											}else if($grouping['group-display-by'] == 3){//CURRENCY
												$modelfieldLogic->format_display_decimal=$grouping['group-display-currency-dp'];
												$modelfieldLogic->format_display_symbol=$grouping['display_by_currency_smb'];
											}else if($grouping['group-display-by'] == 4){//PERCENTAGES
												$modelfieldLogic->format_display_decimal=$grouping['group-display-per-dp'];
											}
										}
										break;
									}
								}
							}
							if(!$modelfieldLogic->save()){
								echo "<pre>",print_r($modelfieldLogic->getErrors()),"</pre>";die;
							}
							}
						}
					}
					if(!empty($post_data['grouping_value'])){
						foreach($post_data['grouping_value'] as $grouping_data){
							$grouping=json_decode($grouping_data,true);
							if(!in_array($grouping['id'],$logic_ids)){
							$logic_ids[$grouping['id']]=$grouping['id'];
							$modelfieldLogic=new ReportsUserSavedFieldsLogic();
							$modelfieldLogic->saved_report_field_id=$ReportsUserSavedFields_ids[$grouping['id']];
							$modelfieldLogic->report_field_operator_id=0;
							$modelfieldLogic->value1='';
							$modelfieldLogic->value2='';
							$modelfieldLogic->sort_type=0;//1=p 2=s and 3=t
							$modelfieldLogic->sort_order=0;//1=asc 2=desc
							$modelfieldLogic->format_total_type=0;
							$modelfieldLogic->format_display_type=0;
							$modelfieldLogic->format_display_decimal=0;
							$modelfieldLogic->format_display_separator='';
							$modelfieldLogic->format_display_symbol='';
							$modelfieldLogic->format_total_type=0;
							if(isset($grouping['group-type']) && $grouping['group-type']!=""){
							$modelfieldLogic->format_total_type=$grouping['group-type'];
							}
							if(isset($grouping['group-display-by']) && $grouping['group-display-by']!=""){
								$modelfieldLogic->format_display_type=$grouping['group-display-by'];
								if($grouping['group-display-by'] == 2){//NUMBER
									$modelfieldLogic->format_display_decimal=$grouping['group-display-number-dp'];
									if(isset($grouping['group-display-number-sp'])  && $grouping['group-display-number-sp']!=""){
										$modelfieldLogic->format_display_separator='1';
									}
								}else if($grouping['group-display-by'] == 3){//CURRENCY
									$modelfieldLogic->format_display_decimal=$grouping['group-display-currency-dp'];
									$modelfieldLogic->format_display_symbol=$grouping['display_by_currency_smb'];
								}else if($grouping['group-display-by'] == 4){//PERCENTAGES
									$modelfieldLogic->format_display_decimal=$grouping['group-display-per-dp'];
								}
							}
							if(!$modelfieldLogic->save()){
								echo "<pre>",print_r($modelfieldLogic->getErrors()),"</pre>";die;
							}
						  }
						}
					}
				}
				}else{
					echo "<pre>",print_r($model->getErrors()),"</pre>";die;
				}
				$transaction->commit();
			} catch(Exception $e){
				$transaction->rollBack();
			}
		}
	}

	/*
	 * To Save Report & it's Criteria
	 * @param mixed Form serialized data
	 * @param report_saved_id int
	 */
	public function actionSaveReport()
	{
		$report_saved_id = Yii::$app->request->get('report_saved_id',0);
		$post_data = Yii::$app->request->post();
		$model = new ReportsUserSaved();
		if($report_saved_id != 0)
			$model = ReportsUserSaved::findByOne($report_saved_id);

		//echo "<pre>",print_r($post_data),"</pre>";
		if(!empty($post_data) && $model->load($post_data)){
			//echo "<pre>",print_r($model->attributes),"</pre>";die;
			$transaction = Yii::$app->db->beginTransaction();
			try{
				if(isset($post_data['ReportsUserSaved']['grid_line'])){
					$model->grid_line=implode(",",$post_data['ReportsUserSaved']['grid_line']);
				}
				$model->report_format_id=1;
				if(isset($post_data['chart_format_id'])){
					$model->report_format_id=2;
					$model->chart_format_id=$post_data['chart_format_id'];
				}
				if($model->save()){
					/*Store Share with by type ByRole, ByUser, ByTeamLocation, ByClientCase*/
					if(!empty($post_data['show_by'])){
						foreach($post_data['show_by'] as $key=>$value){
							if($key==1 || $key==4){//ByRole and ByUser
								if(is_array($value)){
									foreach($value as $k=>$v){
										// 'saved_report_id', 'user_id', 'client_id', 'client_case_id', 'team_id', 'team_loc'
										$modelReportsUserSavedSharedWith=new ReportsUserSavedSharedWith();
										$modelReportsUserSavedSharedWith->saved_report_id=$model->id;
										$modelReportsUserSavedSharedWith->user_id=0;
										$modelReportsUserSavedSharedWith->client_id=0;
										$modelReportsUserSavedSharedWith->client_case_id=0;
										$modelReportsUserSavedSharedWith->team_id=0;
										$modelReportsUserSavedSharedWith->role_id=0;
										if($key==4){//ByUser
											$modelReportsUserSavedSharedWith->user_id=$v;
										}
										if($key==1){//ByRole
											$modelReportsUserSavedSharedWith->role_id=$v;
										}
										//echo "<pre>",print_r($modelReportsUserSavedSharedWith->attributes),"</pre>";die;
										$modelReportsUserSavedSharedWith->save();
									}
								}
							}else{
								if(is_array($value)){
									foreach($value as $k=>$v){
										$team_loc_client_case=explode("_",$v);
										$modelReportsUserSavedSharedWith=new ReportsUserSavedSharedWith();
										$modelReportsUserSavedSharedWith->saved_report_id=$model->id;
										$modelReportsUserSavedSharedWith->user_id=0;
										$modelReportsUserSavedSharedWith->client_id=0;
										$modelReportsUserSavedSharedWith->client_case_id=0;
										$modelReportsUserSavedSharedWith->team_id=0;
										$modelReportsUserSavedSharedWith->team_loc=0;
										$modelReportsUserSavedSharedWith->role_id=0;
										if($key==2){//ByClientCase
											$modelReportsUserSavedSharedWith->client_id=$team_loc_client_case[0];
											$modelReportsUserSavedSharedWith->client_case_id=$team_loc_client_case[1];
										}
										if($key==3){//ByTeamLocation
											$modelReportsUserSavedSharedWith->team_id=$team_loc_client_case[0];
											$modelReportsUserSavedSharedWith->team_loc=$team_loc_client_case[1];
										}
										$modelReportsUserSavedSharedWith->save();
									}
								}
							}
						}
					}
					$i=1;
					$ReportsUserSavedFields_ids=array();
					foreach($post_data['fieldval'] as $key => $field){
						$modelSavedFields = new ReportsUserSavedFields();
						$modelSavedFields->column_sort_order=$i;
						$modelSavedFields->saved_report_id = $model->id;
						$modelSavedFields->report_type_field_id = $key;
						//$modelSavedFields->field_origin = 1;// 1 = Database, 2 = Calculation
						$modelSavedFields->field_calculation_id=0;
						if(strpos($field,'Calc.') !== false){
							$modelSavedFields->field_calculation_id=$key;
							$modelSavedFields->report_type_field_id = 0;
						}
						//$modelSavedFields->chart_display_type_id=0;
						//$modelSavedFields->chart_axis=0; //1=X and 2=Y
						/*if(isset($post_data['chart_values_'.$key]) && $post_data['chart_values_'.$key]!=''){
							$chartvalues = json_decode($post_data['chart_values_'.$key],true);
							$modelSavedFields->chart_display_type_id=(isset($chartvalues['display_by'])) ? $chartvalues['display_by'] : 0;
							if($chartvalues['axis'] == 'x') {
								$modelSavedFields->chart_axis=1; //1=X and 2=Y
							} else if($chartvalues['axis'] == 'y') {
								$modelSavedFields->chart_axis=2; //1=X and 2=Y
							}
						}*/
						/*$modelSavedFields->field_sort_type=0;//1=p 2=s and 3=t
						$modelSavedFields->field_sort_order=0;//1=asc 2=desc
						if(!empty($post_data['sorting_value']) ){
							foreach($post_data['sorting_value'] as $sorting_data){
								$sorting=json_decode($sorting_data,true);
								if($key==$sorting['id']){
									$modelSavedFields->field_sort_type=$sorting['sort-type'];//1=p 2=s and 3=t
									$modelSavedFields->field_sort_order=$sorting['sort-order'];//1=asc 2=desc
									break;
								}
							}
						}
						$modelSavedFields->field_group_type=0;
						if(!empty($post_data['grouping_value']) ){
							foreach($post_data['grouping_value'] as $grouping_data){
								$grouping=json_decode($grouping_data,true);
								if($key==$grouping['id']){
									$modelSavedFields->field_group_type=$grouping['group-type'];
									break;
								}
							}
						}
						$modelSavedFields->is_deleted=0;*/
						if(!$modelSavedFields->save()){
							//echo "<pre>",print_r($modelSavedFields->getErrors()),print_r($modelSavedFields->attributes),"</pre>";die;
						}
						$ReportsUserSavedFields_ids[$key]=$modelSavedFields->id;
						$i++;
					}
					$logic_ids=array();
					if(!empty($post_data['filter_value'])){
						foreach($post_data['filter_value'] as $filter_data){
							$filters=json_decode($filter_data,true);
							$opreator_field_values=$filters['operator_value'];
							$opreator_field_values2=isset($filters['operator_value_new'][0]) && $filters['operator_value_new'][0]!=''?$filters['operator_value_new']:'';
							$opreators=$filters['operator_field_value'];
							$logic_ids[$filters['id']]=$filters['id'];
							if(!empty($opreators) && isset($ReportsUserSavedFields_ids[$filters['id']])){
								$i = 0;
								$opwhere = '';
								foreach($opreators as $opt_key=>$opt){
									$modelfieldLogic=new ReportsUserSavedFieldsLogic();
									$modelfieldLogic->saved_report_field_id=$ReportsUserSavedFields_ids[$filters['id']];
									$modelfieldLogic->report_field_operator_id=$opt;
									$opt_val=$opreator_field_values[$opt_key];
									$opt_val_new=$opreator_field_values2[$opt_key];
									if(count($opreators) > 1){
										$data_opt_val=explode(",",$opreator_field_values[0]);
										if(count($data_opt_val) > 1){
										$opt_val=$data_opt_val[$i];
										}
										$data_opt_valnew=explode(",",$opreator_field_values2[0]);
										if(count($data_opt_valnew) > 1){
										$opt_val_new=$data_opt_valnew[$i];
										}
										$modelfieldLogic->value1=$opt_val;
										if(isset($opt_val) && is_array($opt_val)){
											$modelfieldLogic->value1=implode("|",$opt_val);
										}else{
											$modelfieldLogic->value1=$opt_val;
										}
										if(isset($opt_val_new) && is_array($opt_val_new)){
											$modelfieldLogic->value2=implode("|",$opt_val_new);
										}else{
											$modelfieldLogic->value2=(isset($opt_val_new)?$opt_val_new:'');
										}
									}else{
										$modelfieldLogic->value1=$opreator_field_values[$opt_key];
										if(isset($opreator_field_values[$opt_key]) && is_array($opreator_field_values[$opt_key])){
											$modelfieldLogic->value1=implode("|",$opreator_field_values[$opt_key]);
										}else{
											$modelfieldLogic->value1=$opreator_field_values[$opt_key];
										}
										if(isset($opreator_field_values2[$opt_key]) && is_array($opreator_field_values[$opt_key])){
											$modelfieldLogic->value2=implode("|",$opreator_field_values2[$opt_key]);
										}else{
											$modelfieldLogic->value2=(isset($opreator_field_values2[$opt_key])?$opreator_field_values2[$opt_key]:'');
										}
									}
									$modelfieldLogic->sort_type=0;//1=p 2=s and 3=t
									$modelfieldLogic->sort_order=0;//1=asc 2=desc
									if(!empty($post_data['sorting_value']) ){
										foreach($post_data['sorting_value'] as $sorting_data){
											$sorting=json_decode($sorting_data,true);
											if($filters['id']==$sorting['id']){
												$modelfieldLogic->sort_type=$sorting['sort-type'];//1=p 2=s and 3=t
												$modelfieldLogic->sort_order=$sorting['sort-order'];//1=asc 2=desc
												break;
											}
										}
									}
									$modelfieldLogic->format_total_type=0;
									$modelfieldLogic->format_display_type=0;
									$modelfieldLogic->format_display_decimal=0;
									$modelfieldLogic->format_display_separator='';
									$modelfieldLogic->format_display_symbol='';
									$modelfieldLogic->format_total_type=0;
									if(!empty($post_data['grouping_value']) ){
										foreach($post_data['grouping_value'] as $grouping_data){
											$grouping=json_decode($grouping_data,true);
											if($filters['id']==$grouping['id']){
												if(isset($grouping['group-type']) && $grouping['group-type']!=""){
												$modelfieldLogic->format_total_type=$grouping['group-type'];
												}
												if(isset($grouping['group-display-by']) && $grouping['group-display-by']!=""){
													$modelfieldLogic->format_display_type=$grouping['group-display-by'];
													if($grouping['group-display-by'] == 2){//NUMBER
														$modelfieldLogic->format_display_decimal=$grouping['group-display-number-dp'];
														if(isset($grouping['group-display-number-sp'])){
															$modelfieldLogic->format_display_separator='1';
														}
													}else if($grouping['group-display-by'] == 3){//CURRENCY
														$modelfieldLogic->format_display_decimal=$grouping['group-display-currency-dp'];
														$modelfieldLogic->format_display_symbol=$grouping['display_by_currency_smb'];
													}else if($grouping['group-display-by'] == 4){//PERCENTAGES
														$modelfieldLogic->format_display_decimal=$grouping['group-display-per-dp'];
													}
												}
												break;
											}
										}
									}
									if(!$modelfieldLogic->save()){
											//echo "<pre>",print_r($modelfieldLogic->getErrors()),"</pre>";die;
									}
									$i++;
								}
							}
						}
					}
					if(!empty($post_data['sorting_value'])){
						foreach($post_data['sorting_value'] as $sorting_data){
							$sorting=json_decode($sorting_data,true);
							if(!in_array($sorting['id'],$logic_ids)){
							$logic_ids[$sorting['id']]=$sorting['id'];
							$modelfieldLogic=new ReportsUserSavedFieldsLogic();
							$modelfieldLogic->saved_report_field_id=$ReportsUserSavedFields_ids[$sorting['id']];
							$modelfieldLogic->report_field_operator_id=0;
							$modelfieldLogic->value1='';
							$modelfieldLogic->value2='';
							$modelfieldLogic->sort_type=$sorting['sort-type'];//1=p 2=s and 3=t
							$modelfieldLogic->sort_order=$sorting['sort-order'];//1=asc 2=desc
							$modelfieldLogic->format_total_type=0;
							$modelfieldLogic->format_display_type=0;
							$modelfieldLogic->format_display_decimal=0;
							$modelfieldLogic->format_display_separator='';
							$modelfieldLogic->format_display_symbol='';
							$modelfieldLogic->format_total_type=0;
							if(!empty($post_data['grouping_value']) ){
								foreach($post_data['grouping_value'] as $grouping_data){
									$grouping=json_decode($grouping_data,true);
									if($sorting['id']==$grouping['id']){
										if(isset($grouping['group-type']) && $grouping['group-type']!=""){
										$modelfieldLogic->format_total_type=$grouping['group-type'];
										}
										if(isset($grouping['group-display-by']) && $grouping['group-display-by']!=""){
											$modelfieldLogic->format_display_type=$grouping['group-display-by'];
											if($grouping['group-display-by'] == 2){//NUMBER
												$modelfieldLogic->format_display_decimal=$grouping['group-display-number-dp'];
												if(isset($grouping['group-display-number-sp'])){
													$modelfieldLogic->format_display_separator='1';
												}
											}else if($grouping['group-display-by'] == 3){//CURRENCY
												$modelfieldLogic->format_display_decimal=$grouping['group-display-currency-dp'];
												$modelfieldLogic->format_display_symbol=$grouping['display_by_currency_smb'];
											}else if($grouping['group-display-by'] == 4){//PERCENTAGES
												$modelfieldLogic->format_display_decimal=$grouping['group-display-per-dp'];
											}
										}
										break;
									}
								}
							}
							if(!$modelfieldLogic->save()){
								//echo "<pre>",print_r($modelfieldLogic->getErrors()),"</pre>";die;
							}
							}
						}
					}if(!empty($post_data['grouping_value'])){
						foreach($post_data['grouping_value'] as $grouping_data){
							$grouping=json_decode($grouping_data,true);
							if(!in_array($grouping['id'],$logic_ids)){
							$logic_ids[$grouping['id']]=$grouping['id'];
							$modelfieldLogic=new ReportsUserSavedFieldsLogic();
							$modelfieldLogic->saved_report_field_id=$ReportsUserSavedFields_ids[$grouping['id']];
							$modelfieldLogic->report_field_operator_id=0;
							$modelfieldLogic->value1='';
							$modelfieldLogic->value2='';
							$modelfieldLogic->sort_type=0;//1=p 2=s and 3=t
							$modelfieldLogic->sort_order=0;//1=asc 2=desc
							$modelfieldLogic->format_total_type=0;
							$modelfieldLogic->format_display_type=0;
							$modelfieldLogic->format_display_decimal=0;
							$modelfieldLogic->format_display_separator='';
							$modelfieldLogic->format_display_symbol='';
							$modelfieldLogic->format_total_type=0;
							if(isset($grouping['group-type']) && $grouping['group-type']!=""){
							$modelfieldLogic->format_total_type=$grouping['group-type'];
							}
							if(isset($grouping['group-display-by']) && $grouping['group-display-by']!=""){
								$modelfieldLogic->format_display_type=$grouping['group-display-by'];
								if($grouping['group-display-by'] == 2){//NUMBER
									$modelfieldLogic->format_display_decimal=$grouping['group-display-number-dp'];
									if(isset($grouping['group-display-number-sp'])){
										$modelfieldLogic->format_display_separator='1';
									}
								}else if($grouping['group-display-by'] == 3){//CURRENCY
									$modelfieldLogic->format_display_decimal=$grouping['group-display-currency-dp'];
									$modelfieldLogic->format_display_symbol=$grouping['display_by_currency_smb'];
								}else if($grouping['group-display-by'] == 4){//PERCENTAGES
									$modelfieldLogic->format_display_decimal=$grouping['group-display-per-dp'];
								}
							}
							if(!$modelfieldLogic->save()){
								//echo "<pre>",print_r($modelfieldLogic->getErrors()),"</pre>";die;
							}
						  }
						}
					}
				}else{
					echo "<pre>",print_r($model->getErrors()),"</pre>";die;
				}
				$transaction->commit();
			} catch(Exception $e){
				$transaction->rollBack();
			}

		}
		echo json_encode(['reports_saved_id'=>$model->id]);
	}

	/**
	 * Range Picker
	 * @return
	 */
	public function actionGetrangepicker(){
		$total_count = Yii::$app->request->get('count');
		$datepicker = DateRangePicker::widget([
		    'name'=>'operator_value[]',
		    'id' => 'range_date_'.$total_count,
		    'presetDropdown'=>true,
		    'hideInput'=>true,
		    'options' => ['class' => 'date_pickers start_date form-control operator_value']
		]);
		return $datepicker;
		//return $this->renderAjax('range-picker-popup', ['total_count' => $total_count]);
		die('123');
	}


	/**
	 * To check whether Client/Case AND Teamservice&Location field exist in Report Type or not
	 * @param id int (report_type_id)
	 */
	public function actionChkFilterFieldExist()
	{
		$id = Yii::$app->request->get('id',0);
		if($id != ''){
			$reportfields = ReportsReportTypeFields::find()->where(['report_type_id'=>$id])
			->joinWith(['reportsField'])
			->andWhere(['in','field_name',['client_id', 'client_case_id', 'teamservice_id']]);

			$byFilterField = array('byClientCase'=>0,'byTeamservice' => 0);
			if($reportfields->count() > 0){
				$reportfieldsData = $reportfields->all();
				foreach($reportfieldsData as $fields){
					if($byFilterField['byClientCase']!=1 && ($fields->reportsField->field_name == 'client_id' || $fields->reportsField->field_name == 'client_case_id')){
						$byFilterField['byClientCase']=1;
					}
					if($byFilterField['byTeamservice']!=1 && ($fields->reportsField->field_name == 'teamservice_id')){
						$byFilterField['byTeamservice']=1;
					}
				}
			}
			return json_encode($byFilterField);
		}
		return '';
	}
}
?>
