<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\db\Query;

use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use app\models\ReportsUserSaved;
use app\models\ReportsUserSavedFields;
use app\models\search\ReportsUserSavedSearch;
use app\models\ReportsReportType;
use app\models\ReportsReportTypeFields;
use app\models\ReportsChartFormat;
use app\models\Client;
use app\models\ProjectSecurity;
use app\models\ClientCase;
use app\models\ReportsUserSavedSharedWith;
use app\models\User;
use app\models\Role;
use app\models\ReportsReportFormat;
use app\models\ReportsFieldType;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Shared_Font;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Shared_Date;


/**
 * SavedReportController implements the CRUD actions for  model.
 */
class SavedReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

	/**
	 * It will display list of all saved Reports from model ReportsUserSaved
	 */
	public function actionIndex()
	{
		$this->layout = 'report';
		$searchModel = new ReportsUserSavedSearch();
		$params['grid_id']='dynagrid-report-user-saved';
        Yii::$app->request->queryParams +=$params;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$reportsReportFormat = ArrayHelper::map(ReportsReportFormat::find()->all(),'id','report_format');
		return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_type' => $filter_type,
            'filterWidgetOption' => $filterWidgetOption,
			'reportsReportFormat'=>$reportsReportFormat
        ]);
	}
	public function actionSummaryReport(){
		$post_data = Yii::$app->request->post();
		$id = Yii::$app->request->get('id',0);
		$model=ReportsUserSaved::findOne($id);
		$modeReportsChartFormat  = ReportsChartFormat::find()->orderBy('format_order')->where(['id'=>$post_data['chart_format_id']])->one();
		$model_reportsreporttype = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
		$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']])
			->innerJoinWith([
				'reportsField' => function(\yii\db\ActiveQuery $query2){
					$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
					}]);
				}
			])->all();
			
		$reportTypeFields = ArrayHelper::map($records,'id',function($model, $defaultValue) {
			return $model->reportsField->reportsFieldType->field_type;
		});
		$field_disply_with_table=[];
		foreach($post_data['fielddisp'] as $id=>$display_name){
			$table_name=substr($post_data['fieldval'][$id],0,strpos($post_data['fieldval'][$id],"."));
			$table_name=str_replace("tbl_","",$table_name);
			$field_disply_with_table[$id]=ucwords($table_name)."=>".$display_name;
		}
		if(isset($model_reportsreporttype->sp_name) && trim($model_reportsreporttype->sp_name)=='MediaOut'){//Client Case Projects Task
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
		return $this->renderAjax('_step_5',['fieldNameType'=>$fieldNameType,'model'=>$model,'post_data'=>$post_data,'modeReportsChartFormat'=>$modeReportsChartFormat,'reportTypeFields'=>$reportTypeFields,'field_disply_with_table'=>$field_disply_with_table]);
	}
	public function actionViewChart(){
		  $post_data = Yii::$app->request->post();
		  $modeReportsChartFormat = ReportsChartFormat::find()->orderBy('format_order')->where(['id'=>$post_data['chart_format_id']])->one();
		  if(Yii::$app->db->driverName == 'mysql'){
			$query = (new ReportsReportType)->prepareCustomChartReport($post_data);
		  }else{
			$query = (new ReportsReportType)->prepareCustomChartReportMsSql($post_data);
		  }
		  //echo "<pre>",print_r($query),"</prE>";die;
		  $reportTypeFieldsModel = ReportsReportTypeFields::find()
			->select(['tbl_reports_report_type_fields.id','table_name', 'table_display_name','field_name', 'field_display_name'])
			->joinWith(['reportsField'=>function(\yii\db\ActiveQuery $query){
				$query->joinWith(['reportsTables']);
			}]);
			//$table_fields_detail = $reportTypeFieldsModel->where(['report_type_id' => $post_data['ReportsUserSaved']['report_type_id'],'tbl_reports_report_type_fields.id'=>$post_data['ReportsUserSaved']['x_data']])->asArray()->one();
			//$ytable_fields_detail = $reportTypeFieldsModel->where(['report_type_id' => $post_data['ReportsUserSaved']['report_type_id'],'tbl_reports_report_type_fields.id'=>$post_data['ReportsUserSaved']['y_data']])->asArray()->one();

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
		    if(isset($post_data['ReportsUserSaved']['y_data_display']) && in_array($post_data['ReportsUserSaved']['y_data_display'],array('Days','Weeks','Months','Years'))){
			   $query['x'] = str_replace(".","_",$query['x']);
			   $query['y'] = str_replace(".","_",$query['y']);
			   $query['legend']  = str_replace(".","_",$query['legend']);
			   $query['date_field'] = str_replace(".","_",$query['date_field']);
			   $query['group'] = str_replace(".","_",$query['group']);
			   $query['order'] = str_replace(".","_",$query['order']);
			   $query['display_by'] = str_replace(".","_",$query['display_by']);
			   $query['where'] = '1=1';
			   if (Yii::$app->db->driverName == 'mysql') {
			   		$sql="CALL getChartStats1('".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['order']."','".$query['display_by']."',".$usr_id.");";
			   }else{
			   		$sql="EXECUTE dbo.getChartStats1 '".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['order']."','".$query['display_by']."',".$usr_id;
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
			  	$sql="CALL getChartStats1('".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['order']."','".$query['display_by']."',".$usr_id.");";
			  }else{
			  	$sql="EXECUTE dbo.getChartStats1 '".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['order']."','".$query['display_by']."',".$usr_id;
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
		   	  	$sql="CALL getChartStats1('".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['order']."','".$query['display_by']."',".$usr_id.");";
		   	  }else{
		   	  	$sql="EXECUTE dbo.getChartStats1 '".$query['sdate']."','".$query['edate']."','".$query['x']."','".$query['y']."','".$query['date_field']."','".$query['legend']."','".$query['frm_join']."','".$query['where']."','','".$query['order']."','".$query['display_by']."',".$usr_id;	
		   	  }
			  $report_data =\Yii::$app->db->createCommand($sql)->queryAll();
		   }else{
			   $report_data =\Yii::$app->db->createCommand($query['vsql'])->queryAll(\PDO::FETCH_NUM);
		   }
		   //echo "<pre>",print_r($report_data),"</pre>";
		   //die;
		   return $this->renderAjax('_step_6', ['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]); 
	  }
	/*
	 * edit-savereport
	 * */
	 public function actionEditSavereport(){
		$this->layout = 'report';
		$id = Yii::$app->request->get('id',0);
		$model  = ReportsUserSaved::findOne($id);
		$fields = ReportsUserSavedFields::find()->where('saved_report_id='.$id.'')->all();
		$userId=Yii::$app->user->identity->id;
		$roleId=Yii::$app->user->identity->role_id;
		$query = ReportsUserSaved::find();
		if($roleId!=0) {
			$rpaccess="SELECT DISTINCT saved_report_id FROM ( 
				(
					SELECT saved_report_id FROM tbl_reports_user_saved_shared_with 
					INNER JOIN tbl_project_security on tbl_project_security.client_case_id=tbl_reports_user_saved_shared_with.client_case_id 
					AND tbl_project_security.user_id=$userId AND tbl_reports_user_saved_shared_with.client_case_id !=0
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
					AND tbl_project_security.user_id=$userId where tbl_reports_user_saved_shared_with.team_id != 0
				)
			) as RPACCESS";
			//$where=" CASE WHEN report_save_to = 1 THEN (created_by=$userId) WHEN report_save_to=3 THEN 1=1  WHEN report_save_to=2 THEN id IN ($rpaccess) OR (created_by=$userId) END ";
			$where="((report_save_to = 1 AND (tbl_reports_user_saved.created_by=$userId)) OR (report_save_to = 3 AND 1=1) OR (report_save_to=2 AND id IN($rpaccess)) OR (tbl_reports_user_saved.created_by=$userId) )";
			$query->where($where);
		}
		$query->andWhere('id = '.$id);
		if($query->count() == 0) {
			throw new NotFoundHttpException("You don't have access to this report.");
		}
		$modeReportType = ArrayHelper::map(ReportsReportType::find()->all(),'id','report_type');
		$modeReportsChartFormat = ArrayHelper::map(ReportsChartFormat::find()->orderBy('format_order')->all(),'id','chart_format');
		
		/* IRT 564 */
		$field_type_name = ArrayHelper::map(ReportsFieldType::find()->asArray()->all(),'id','field_type');

		return $this->render('edit-savereport', [
			'model' => $model, 
			'modeReportType' => $modeReportType, 
			'id' => $id,
			'fields' => $fields,
			'flag' => 'edit',
			'modeReportsChartFormat' => $modeReportsChartFormat,
			'field_type_name' => $field_type_name
		]); 
	 }
	 /**
	  * 
	  * */
	  public function actionRunSavereport() {
		$this->layout = 'report';
		$id = Yii::$app->request->get('id',0);
		$model  = ReportsUserSaved::findOne($id);
		$fields = ReportsUserSavedFields::find()->where('saved_report_id='.$id.'')->all();
		$userId=Yii::$app->user->identity->id;
		$roleId=Yii::$app->user->identity->role_id;
		$query = ReportsUserSaved::find();
		if($roleId!=0){
			$rpaccess="SELECT DISTINCT saved_report_id FROM ( 
			(
			SELECT saved_report_id FROM tbl_reports_user_saved_shared_with 
			INNER JOIN tbl_project_security on tbl_project_security.client_case_id=tbl_reports_user_saved_shared_with.client_case_id 
			AND tbl_project_security.user_id=$userId AND tbl_reports_user_saved_shared_with.client_case_id !=0
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
			
			//$where=" CASE WHEN report_save_to = 1 THEN (created_by=$userId) WHEN report_save_to=3 THEN 1=1  WHEN report_save_to=2 THEN id IN ($rpaccess) OR (created_by=$userId) END ";
			$where="( (report_save_to = 1 AND (created_by=$userId)) OR (report_save_to = 3 AND 1=1) OR (report_save_to=2 AND tbl_reports_user_saved.id IN($rpaccess)) OR (created_by=$userId) )";
			$query->where($where);
		}
		$query->andWhere('id = '.$id);
		if($query->count() == 0){
			throw new NotFoundHttpException("You don't have access to this report.");
		}
		$modeReportType = ArrayHelper::map(ReportsReportType::find()->all(),'id','report_type');
		$modeReportsChartFormat = ArrayHelper::map(ReportsChartFormat::find()->orderBy('format_order')->all(),'id','chart_format');
		return $this->render('edit-savereport', [
			'model'=>$model, 
			'modeReportType'=>$modeReportType, 
			'modeReportsChartFormat'=>$modeReportsChartFormat,
			'id'=>$id,
			'fields'=>$fields,
			'flag'=>'run',
		]);
	  }
	/**
     * Filter GridView with Ajax
     * @return
     */
    public function actionAjaxFilter()
    {
    	$searchModel = new ReportsUserSavedSearch();
    	$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
    	$out['results']=array();
    	$val_array=array();
    	foreach ($dataProvider as $key=>$val){
			$val=Html::decode($val);
    		$out['results'][] = ['id' => $val, 'text' => $val,'label' => $val];
    	}
    	return json_encode($out);
    }
    
    /**
     * Grid expand report details in Display Report Grid
     * @return 
     */
     public function actionGetReportDetails()
     {
		$report_details = Yii::$app->request->post();
		 
		/* get report details of final result (final result) */
		$final_result = '';
		$result = ReportsUserSaved::find()->select(['tbl_reports_user_saved.id','tbl_reports_user_saved.created', 'tbl_reports_user_saved.modified','tbl_reports_user_saved.modified_by', 'report_save_to', 'share_report_by', 'report_format_id', 'chart_format_id'])->joinWith(['modifiedUser'=>function ($query) { $query->select(['usr_first_name','usr_lastname']);}])->where(['tbl_reports_user_saved.id' => $report_details['expandRowKey']])->asArray()->one();
		//echo "<pre>",print_r($result),"</pre>";die;
		$shareBy='';
		if($result['report_save_to']==2){
			 $select = ''; $join = '';
			 if($result['share_report_by']==4){ // By Role
				$select = "CONCAT(t3.usr_first_name,' ',t3.usr_lastname) as name";
				$join = "INNER JOIN tbl_user t3 ON t2.user_id = t3.id";	
			 }
			 else if($result['share_report_by']==2){ // By Client/Case
				$select = "CONCAT(t4.client_name,' - ',t3.case_name) as name";
				$join = "INNER JOIN tbl_client_case t3 ON t2.client_case_id = t3.id INNER JOIN tbl_client t4 ON t2.client_id = t4.id";	
			 }
			 else if($result['share_report_by']==3){ // By Team/Location 
				$select = "CONCAT(t3.team_name,' - ',t4.team_location_name) as name";
				$join = "INNER JOIN tbl_team t3 ON t2.team_id = t3.id INNER JOIN tbl_teamlocation_master t4 ON t2.team_loc = t4.id";	
			 }
			 else if($result['share_report_by']==1){ // By User
				$select = "t4.role_name as name";
				$join = "INNER JOIN tbl_role t4 ON t2.role_id = t4.id";	
			 }
			 $query = 'SELECT '.$select.' FROM tbl_reports_user_saved t1 INNER JOIN tbl_reports_user_saved_shared_with t2 ON t1.id = t2.saved_report_id '.$join.' WHERE t1.id = '.$report_details['expandRowKey'];
			 $final = \Yii::$app->db->createCommand($query)->queryAll();
			 $final_result = ArrayHelper::map($final,'name','name');
		} 
		/* End report details */
		 //echo "<pre>",print_r($result),"</pre>";
		 //die;
		return $this->renderAjax('get-report-details', [
           'report_details' => $result,
           'final_result' => $final_result
        ]);
	 }
	 
	 /**
	  * Delete Reports from Display Report Grid
	  * @return 
	  */
	  public function actionDeletesavedreports()
	  {
		  $task_list = Yii::$app->request->post();
		  $userId = Yii::$app->user->identity->id;
		  $roleId = Yii::$app->user->identity->role_id;
		  if(!empty($task_list))
		  {
			  $cnt_arr1 = count($task_list['task_list']);
			  $reportIds = implode(",", $task_list['task_list']); // task list
			  $query_report = "SELECT * FROM tbl_reports_user_saved WHERE created_by = ".$userId." AND id IN (".$reportIds.")";
			  $creator_report = \Yii::$app->db->createCommand($query_report)->queryAll();
			  $cnt_arr2 = count($creator_report);
			  /** Delete Saved Reports **/
			  if($roleId==0){
				  $delete_shared_with = "DELETE FROM tbl_reports_user_saved_shared_with WHERE saved_report_id IN (".$reportIds.")";
				  \Yii::$app->db->createCommand($delete_shared_with)->execute();
				  $delete_saved_field_logic = "DELETE FROM tbl_reports_user_saved_fields_logic WHERE saved_report_field_id IN (SELECT id FROM tbl_reports_user_saved_fields WHERE saved_report_id IN (".$reportIds."))";
				  \Yii::$app->db->createCommand($delete_saved_field_logic)->execute();
				  $delete_saved_field = "DELETE FROM tbl_reports_user_saved_fields WHERE saved_report_id IN (".$reportIds.")";
				  \Yii::$app->db->createCommand($delete_saved_field)->execute();
				  $delete_saved = "DELETE FROM tbl_reports_user_saved WHERE id IN (".$reportIds.")";
				  \Yii::$app->db->createCommand($delete_saved)->execute(); echo "OK";
			  }
			  else if($cnt_arr1 == $cnt_arr2)
			  {
				  $delete_shared_with = "DELETE FROM tbl_reports_user_saved_shared_with WHERE saved_report_id IN (".$reportIds.")";
				  \Yii::$app->db->createCommand($delete_shared_with)->execute();
				  $delete_saved_field_logic = "DELETE FROM tbl_reports_user_saved_fields_logic WHERE saved_report_field_id IN (SELECT id FROM tbl_reports_user_saved_fields WHERE saved_report_id IN (".$reportIds."))";
				  \Yii::$app->db->createCommand($delete_saved_field_logic)->execute();
				  $delete_saved_field = "DELETE FROM tbl_reports_user_saved_fields WHERE saved_report_id IN (".$reportIds.")";
				  \Yii::$app->db->createCommand($delete_saved_field)->execute();
				  $delete_saved = "DELETE FROM tbl_reports_user_saved WHERE id IN (".$reportIds.")";
				  \Yii::$app->db->createCommand($delete_saved)->execute(); echo "OK";
			  } else {
				  echo "Fail";
			  }
		  }
		  die();
	  }
	  
	  /*
	   * Run Reports tabular formate
	   * @return 
	   */
	   public function actionRunReport()
	   {
			$this->layout = 'report';
			$report_user_saved_id = Yii::$app->request->post('keys',0);
			$query = "SELECT t1.report_type_id, t1.report_format_id, t1.chart_format_id, t3.id, CONCAT(t5.table_name,'.',t4.field_name) as tbname, t4.field_display_name as name 
				FROM tbl_reports_user_saved as t1 
				INNER JOIN tbl_reports_user_saved_fields as t2 ON t1.id = t2.saved_report_id
				INNER JOIN tbl_reports_report_type_fields as t3 ON t2.report_type_field_id = t3.id
				INNER JOIN tbl_reports_fields as t4 ON t3.reports_fields_id = t4.id
				INNER JOIN tbl_reports_tables as t5 ON t4.report_table_id = t5.id WHERE t1.id = ".$report_user_saved_id;
			$columns_lists = \Yii::$app->db->createCommand($query)->queryAll();
			$user_lists =  array();
			foreach($columns_lists as $val){
				$user_lists['ReportsUserSaved']['report_type_id'] = $val['report_type_id'];
				$user_lists['ReportsUserSaved']['report_format_id'] = $val['report_format_id'];
				$user_lists['ReportsUserSaved']['chart_format_id'] = $val['chart_format_id'];
			}
			// tabular report	
			$fieldval = array_merge(array('fieldval' => ArrayHelper::map($columns_lists,'id','tbname')),$user_lists);
			$post_data = array_merge(array('fielddisp' => ArrayHelper::map($columns_lists,'id','name')),$fieldval);
			
			// user lists	
			if($user_lists['ReportsUserSaved']['chart_format_id']==0)
			{
				$query = (new ReportsReportType)->prepareCustomReportQuery($post_data, 5);
				$post_data['fieldval_alias'] = $query['fieldval_alias'];
				$report_data = \Yii::$app->db->createCommand($query['sql'])->queryAll();
				
				$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']])
					->innerJoinWith([
						'reportsField' => function(\yii\db\ActiveQuery $query2){
							$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
								$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
							}]);
						}
					])->all();
			
				// preview report 
				return $this->renderAjax('preview-report', ['report_data' => $report_data, 'column_data' => $post_data['fieldval'], 'column_data_alias' => $post_data['fieldval_alias'], 'reportTypeFields' => $reportTypeFields, 'column_display_data' => $post_data['fielddisp']]);  
			} else {
				// chart format
				$chart_format = 'SELECT t3.id, t4.field_name, t4.field_display_name, t2.chart_axis, t2.manipulation_by, t2.chart_display_type_id FROM tbl_reports_user_saved as t1 
					INNER JOIN tbl_reports_user_saved_fields as t2 ON t1.id = t2.saved_report_id 
					INNER JOIN tbl_reports_report_type_fields as t3 ON t2.report_type_field_id = t3.id
					INNER JOIN tbl_reports_fields as t4 ON t3.reports_fields_id = t4.id 
					WHERE t1.id = '.$report_user_saved_id;
				$result = \Yii::$app->db->createCommand($chart_format)->queryAll();
				
				$chart_result_format = array();
				foreach($result as $value){
					if($value['chart_axis']!=0){
						$chart_result_format["chart_value_".$value['id']]['id'] = $value['id'];
						$chart_result_format["chart_value_".$value['id']]['chart_axis'] = $value['chart_axis']==2?"y":"x";
						$chart_result_format["chart_value_".$value['id']]['display_by'] = $value['chart_display_type_id']!=0?$value['chart_display_type_id']:"";
						$chart_result_format["chart_value_".$value['id']]['manipulation_by'] = $value['manipulation_by'];
					}
				}
				
				$result = array();
				foreach($chart_result_format as $key => $cas){
					$result[$key] = json_encode($cas);
				}
				
				$post_data = array_merge($post_data,$result);
				$model = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
				$query = (new ReportsReportType)->prepareCustomChartReportQuery($post_data, 5);
				$sql = 'call getChartStats("'.$query['sdate'].'","'.$query['edate'].'","'.$query['x'].'","'.$query['y'].'","'.$query['legend'].'","'.$query['display_by'].'","'.$query['datefield_xy'].'","'.$query['fmr_with_join'].'","'.$query['whr'].'","'.$query['grp'].'");';
				$report_data = \Yii::$app->db->createCommand($sql)->queryAll();
				$format = ReportsChartFormat::findOne($post_data['ReportsUserSaved']['chart_format_id'])->chart_format;
				$chart_data = array();
				$categories = array();
				$new_chart_data = array();
				$dispay_by = '';
				foreach($post_data['fieldval'] as $keyt=>$field) {
					if(isset($post_data['chart_values_'.$keyt]) && $post_data['chart_values_'.$keyt]!='') {
						$chartvalues = json_decode($post_data['chart_values_'.$keyt],true);
						if($display_by=='' && isset($chartvalues['display_by'])) {
							$display_by = ReportsChartFormatDisplayBy::findOne($chartvalues['display_by'])->chart_display_by;
						}
					}
				}
				
				switch($format){
					case 'Line':
					case 'Bar':
					case 'Column':
						foreach($report_data as $rp_data){
							if(strtolower($display_by) == 'weeks' || strtolower($display_by) == 'week')
								$categories[date('Y-m-d',strtotime($rp_data['start_date']))]=date('Y-m-d',strtotime($rp_data['start_date']));
							else
								$categories[date('Y-m',strtotime($rp_data['start_date']))]=date('Y-m',strtotime($rp_data['start_date']));
						}
						asort($categories); 
						
						foreach($report_data as $rp_data) {
							foreach($categories as $cat) {
								if(strtolower($display_by) == 'weeks' || strtolower($display_by) == 'week') {
									if($cat==date('Y-m-d',strtotime($rp_data['start_date']))) {
										$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
									}
								} else {
									if($cat==date('Y-m',strtotime($rp_data['start_date']))) {
										$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
									}
								}
							}
						}
						
						foreach($chart_data as $k=>$data){
							foreach($categories as $cat){
								if(!isset($chart_data[$k][$cat])){
									$chart_data[$k][$cat]=0;
								}
							}
						}
						
						foreach($chart_data as $k=>$data){
							$mydata=$data;
							ksort($mydata);
							$new_chart_data[]=array('name'=>$k,'data'=>array_values($mydata));
						}
						break;
						
					case 'Pie':
						break;
				}
				
				//echo "<prE>",print_r($categories),"</prE>";
				return $this->renderAjax('preview-report-chart', ['format'=>$format, 'model'=>$model, 'chart_data'=>json_encode($new_chart_data), 'categories' => json_encode(array_values($categories))]); 
			}
	   }
	   
	   /**
	    * Action Index Criteria Display By
	    * @return
	    */
	   public function actionIndexCriteriaDisplayBy() 
	   {
		   $reportId = Yii::$app->request->post('reportId');
		   if($reportId){
			 $query = "SELECT t1.report_type_id, t1.report_format_id, t1.chart_format_id, t3.id, CONCAT(t5.table_name,'.',t4.field_name) as tbname, t4.field_display_name as name 
				FROM tbl_reports_user_saved as t1 
				INNER JOIN tbl_reports_user_saved_fields as t2 ON t1.id = t2.saved_report_id
				INNER JOIN tbl_reports_report_type_fields as t3 ON t2.report_type_field_id = t3.id
				INNER JOIN tbl_reports_fields as t4 ON t3.reports_fields_id = t4.id
				INNER JOIN tbl_reports_tables as t5 ON t4.report_table_id = t5.id WHERE t1.id=".$reportId;
			   $result = \Yii::$app->db->createCommand($query)->queryAll();	
			   die;
		   }
	   }
	   
	   /**
	    * get excel and/or csv file
	    * @return excel file
	    */
	    public function actionRunFileReport()
	    {
			$post_data = json_decode(Yii::$app->request->post('post_data'),true);
			$model = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
			$query = (new ReportsReportType)->prepareCustomReportQuery($post_data);
			$criteria = (new ReportsReportType)->createCustomReportCriteria($post_data);
			$report_data = \Yii::$app->db->createCommand($query)->queryAll();
			$this->layout = false;
			$reportTypeFields = ArrayHelper::map(ReportsReportTypeFields::find()->select(['tbl_reports_report_type_fields.id','tbl_reports_report_type_fields.reports_field_type_id'])->where(['reporttype_id'=>$post_data['ReportsUserSaved']['report_type_id']])->innerJoinWith([
				'reportsFieldType' => function(\yii\db\ActiveQuery $query){
					$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
				}
			])->all(),'id',function($model, $defaultValue) {
				return $model->reportsFieldType->field_type;
			});
			
			$filename = "custom_report_".date('m_d_Y',time()).".xlsx";
			$header = $this->render('preview-report-table-header', ['header_data' => $post_data, 'criteria' => $criteria, 'reportTypeFields' => $reportTypeFields,'model'=>$model,'column_display_data' => $post_data['fielddisp']]); 
			$content = $this->render('saved-report-view', ['report_data' => $report_data, 'column_data' => $post_data['fieldval'],'reportTypeFields'=>$reportTypeFields,'column_display_data' => $post_data['fielddisp']]);
			$table = $header.$content;
			
			// Save $table inside temporary file that will be deleted later
			$tmpfile = tempnam(sys_get_temp_dir(), 'html');
			file_put_contents($tmpfile, $table);
			$objPHPExcel = new PHPExcel();
			$final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'), 
			array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
			$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);
			
			/** HTML **/
			$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
			$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
			unlink($tmpfile); // Delete temporary file because it isn't needed anymore
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
			header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
			header('Cache-Control: max-age=0');
			
			// Creates a writer to output the $objPHPExcel's content
			$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$writer->save('php://output','w');
			exit;
	   }
	   
	   /**
		* To Save Report & it's Criteria
		* @param mixed Form serialized data
		* @param report_saved_id int
	    */
	   public function actionSaveReportPopup()
	   {
		   	$report_saved_id = Yii::$app->request->post('report_saved_id');
			$userId = Yii::$app->user->identity->id;
			$post_data = Yii::$app->request->post();
			$model = new ReportsUserSaved();
			$allowPrivate = false;
			if($report_saved_id!=0){
				$model = ReportsUserSaved::findOne($report_saved_id);
				if($model->created_by == $userId){
					$allowPrivate = true;
				}
			}
			$model->flag = 'saved';
			
			/** Save Report Popup **/	
			$query = "SELECT t1.report_type_id, t1.report_format_id, t1.chart_format_id, t3.id, CONCAT(t5.table_name,'.',t4.field_name) as tbname, t4.field_display_name as name 
				FROM tbl_reports_user_saved as t1 
				INNER JOIN tbl_reports_user_saved_fields as t2 ON t1.id = t2.saved_report_id
				INNER JOIN tbl_reports_report_type_fields as t3 ON t2.report_type_field_id = t3.id
				INNER JOIN tbl_reports_fields as t4 ON t3.reports_fields_id = t4.id
				INNER JOIN tbl_reports_tables as t5 ON t4.report_table_id = t5.id WHERE t1.id = ".$report_saved_id;
			$columns_lists = \Yii::$app->db->createCommand($query)->queryAll();
			
			$user_lists =  array();
			foreach($columns_lists as $val){
				$user_lists['ReportsUserSaved']['report_type_id'] = $val['report_type_id'];
				$user_lists['ReportsUserSaved']['report_format_id'] = $val['report_format_id'];
				$user_lists['ReportsUserSaved']['chart_format_id'] = $val['chart_format_id'];
			}
			
			// tabular report
			$fieldval = array_merge(array('fieldval' => ArrayHelper::map($columns_lists,'id','tbname')),$user_lists);
			$post_data = array_merge(array('fielddisp' => ArrayHelper::map($columns_lists,'id','name')),$fieldval);
			
			return $this->renderAjax('save-report-popup', ['allowPrivate'=>$allowPrivate,'model' => $model, 'userId' => $userId, 'post_data' => $post_data,'report_saved_id'=>$report_saved_id]);  
	   }
	   
	   /**
	    * Get Show By
	    * @return
	    */ 
	   public function actionGetshowby()
	   {
			$show_by = Yii::$app->request->post('show_by',0);
			$reportUId = Yii::$app->request->post('reportId');
			
			
			$data=array();
			$client_data = array();
			$team_data = array();
			$roleId = Yii::$app->user->identity->role_id;
			$userId = Yii::$app->user->identity->id; 
			
			$select = '';
			// sharedWith
			if($show_by==1)
			{ // By Role
				/*$casesql ="	SELECT tbl_user.id FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE  tbl_role.role_type IN ('1') ORDER BY tbl_user.usr_username";
				$teamsql ="	SELECT tbl_user.id FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE  tbl_role.role_type IN ('2') ORDER BY tbl_user.usr_username";
				$bothSql =" SELECT tbl_user.id FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE tbl_user.id NOT IN($casesql) AND tbl_user.id NOT IN($teamsql) AND (tbl_role.role_type IN ('1,2') OR tbl_role.role_type IN ('2,1'))  ORDER BY tbl_user.usr_username";
				$data['case_manager']=ArrayHelper::map(User::find()->where("tbl_user.id IN ($casesql)")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
				$data['team_member']=ArrayHelper::map(User::find()->where("tbl_user.id IN ($teamsql)")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
				$data['both_case_team_manager']=ArrayHelper::map(User::find()->where("tbl_user.id IN ($bothSql)")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
				*/
				$sharedWith = ReportsUserSavedSharedWith::find()->select(['role_id'])->where(['saved_report_id' => $reportUId])->asArray()->all();
				$casesql     ="	SELECT tbl_role.id FROM tbl_role  WHERE  tbl_role.role_type IN ('1') and tbl_role.id NOT IN(0) ";
				$teamsql     ="	SELECT tbl_role.id FROM tbl_role WHERE  tbl_role.role_type IN ('2') and tbl_role.id NOT IN(0) ";
				$bothSql =" SELECT tbl_role.id FROM tbl_role  WHERE tbl_role.id NOT IN($casesql) AND tbl_role.id NOT IN($teamsql) AND (tbl_role.role_type IN ('1,2') OR tbl_role.role_type IN ('2,1')) and tbl_role.id NOT IN(0)";
				$data['case_manager']=ArrayHelper::map(Role::find()->where("tbl_role.id IN ($casesql)")->all(),'id','role_name');
				$data['team_member']=ArrayHelper::map(Role::find()->where("tbl_role.id IN ($teamsql)")->all(),'id','role_name');
				$data['both_case_team_manager']=ArrayHelper::map(Role::find()->where("tbl_role.id IN ($bothSql)")->all(),'id','role_name');
			
				$filter_data = array();
				foreach($sharedWith as $key => $val){
					$filter_data[$val['role_id']][$val['role_id']] = $val['role_id'];
				}
			} else if($show_by==2) { // By Client/case
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
							$caseList = ArrayHelper::map(ClientCase::find()->select(['id', 'case_name'])->where([ 'client_id' => $client_id, 'is_close'=>0])->orderBy('case_name')->asArray()->all(),'id', 'case_name');
						}
						$data[$client_id] = $caseList;
					}
					$sharedWith = ReportsUserSavedSharedWith::find()->select(['client_id','client_case_id'])->where(['saved_report_id' => $reportUId])->asArray()->all();
					$filter_data = array();
					foreach($sharedWith as $key => $val){
						$filter_data[$val['client_id']][$val['client_case_id']] = $val['client_case_id'];
					}
				} else if ($show_by==3){ // By Team/Location
					$sql_query = "SELECT team.id as team_id,team.team_name,tbl_team_locs.team_loc,master.team_location_name  FROM tbl_team as team LEFT JOIN tbl_team_locs on tbl_team_locs.team_id=team.id LEFT JOIN tbl_teamlocation_master as master ON master.id = tbl_team_locs.team_loc WHERE  team.id != 1 order by team.team_name,master.team_location_name ";
					if($roleId!=0){
						$sql_query = "SELECT security.team_id,security.team_loc,team.team_name,master.team_location_name FROM tbl_project_security security INNER JOIN tbl_team as team ON team.id = security.team_id INNER JOIN tbl_teamlocation_master as master ON master.id = security.team_loc WHERE security.user_id = :user_id AND security.team_id != 0 AND security.team_loc != 0 order by team.team_name,master.team_location_name";
					}
					$params[':user_id'] = $userId;
					$dropdown_data = \Yii::$app->db->createCommand($sql_query,$params)->queryAll();
					if(!empty($dropdown_data)){
						foreach($dropdown_data as $drop => $value){
							$team_data[$value['team_id']]=$value['team_name'];
							$data[$value['team_id']][$value['team_loc']]=$value['team_location_name'];
						}
					}
					$sharedWith = ReportsUserSavedSharedWith::find()->select(['team_id','team_loc'])->where(['saved_report_id' => $reportUId])->asArray()->all();
					$filter_data = array();
					foreach($sharedWith as $key => $val){
						$filter_data[$val['team_id']][$val['team_loc']] = $val['team_loc'];
					}
			}else if($show_by==4){//By User
				$sql_query = "SELECT id FROM tbl_user";
				$data['users']=ArrayHelper::map(User::find()->select(['id','usr_first_name','usr_lastname'," CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where("tbl_user.id IN ($sql_query)")->orderBy("full_name ASC")->all(),'id',function($model){ return $model->usr_first_name.' '.$model->usr_lastname;});
				$sharedWith = ReportsUserSavedSharedWith::find()->select(['user_id'])->where(['saved_report_id' => $reportUId])->asArray()->all();
				$filter_data = array();
				foreach($sharedWith as $key => $val){
					$filter_data[$val['user_id']][$val['user_id']] = $val['user_id'];
				}
			}
			
			return $this->renderAjax('getshowby', ['data' => $data, 'team_data' => $team_data, 'client_data' => $client_data, 'show_by' => $show_by, 'filter_data' => $filter_data]);  
		}
	   
	   /*
		* To Save Report & it's Criteria
		* @param mixed Form serialized data
		* @param report_saved_id int
		*/
		public function actionSaveReport()
		{
			$report_saved_id = Yii::$app->request->post('ReportsUserSaved');
			//$post_data = Yii::$app->request->post();
			//echo "<pre>",print_r($post_data),"</prE>";die;
			$data = json_decode(Yii::$app->request->post('post_data'),true);
			$report_details = array("ReportsUserSaved" => Yii::$app->request->post('ReportsUserSaved'));
			//$post_data = array_merge($data, $report_details);
			$userId = Yii::$app->user->identity->id; // current login userId
			
			//$model = new ReportsUserSaved();
			
			if(isset($report_saved_id) && $report_saved_id['id'] != 0) //!empty($post_data) && $model->load($post_data)
			{
				$post_data = Yii::$app->request->post();
				$modified = date('Y-m-d H:i:s');
    			$modified_by = Yii::$app->user->identity->id;
    			$share_report_by=(isset($post_data['ReportsUserSaved']['share_report_by']) && $post_data['ReportsUserSaved']['share_report_by']!="")?$post_data['ReportsUserSaved']['share_report_by']:0;
				$update_sql="UPDATE tbl_reports_user_saved SET custom_report_name = '".Html::encode($post_data['ReportsUserSaved']['custom_report_name'])."',custom_report_description='".Html::encode($post_data['ReportsUserSaved']['custom_report_description'])."',report_save_to=".Html::encode($post_data['ReportsUserSaved']['report_save_to']).",share_report_by=".Html::encode($share_report_by).",modified='".Html::encode($modified)."',modified_by=".Html::encode($modified_by)." WHERE id={$report_saved_id['id']}";
				\Yii::$app->db->createCommand($update_sql)->execute();
				$transaction = Yii::$app->db->beginTransaction();
				// try catch block 
				try{
					
					//echo "<pre>",print_r($post_data),"</pre>";
					\Yii::$app->db->createCommand("DELETE FROM tbl_reports_user_saved_shared_with WHERE saved_report_id=".$report_saved_id['id'])->execute();
					if(!empty($post_data['show_by'])){
						foreach($post_data['show_by'] as $key=>$value){
							if($key==1 || $key==4){//ByRole and ByUser
								if(is_array($value)){
									foreach($value as $k=>$v){
										// 'saved_report_id', 'user_id', 'client_id', 'client_case_id', 'team_id', 'team_loc'
										$modelReportsUserSavedSharedWith=new ReportsUserSavedSharedWith();
										$modelReportsUserSavedSharedWith->saved_report_id=$report_saved_id['id'];
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
										$modelReportsUserSavedSharedWith->saved_report_id=$report_saved_id['id'];
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
					//die('sgdfgd;fh');
					$transaction->commit();
				} catch(Exception $e){
					$transaction->rollBack();	
				}
			}
			echo json_encode(['reports_saved_id'=>$model->id]);
		}
		public function actionCheckReportAccess(){
			$report_saved_id = Yii::$app->request->get('id');
			$reportData = ReportsUserSaved::findOne($report_saved_id);
			if(Yii::$app->user->identity->id == $reportData->created_by){
				echo "OK";
			}
			die;
		}
}
